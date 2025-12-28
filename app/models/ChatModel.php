<?php
class ChatModel {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function getNameSenderByID($sender_id) {
        // SỬA: username -> hoten
        $sql = "SELECT hoten FROM users WHERE id_user = ? LIMIT 1";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $sender_id);
        $stmt->execute();

        $result = $stmt->get_result()->fetch_assoc();

        return $result['hoten'] ?? ''; // SỬA: username -> hoten
    }

     public function findConversation($user1, $user2) {
        $sql = "
            SELECT cu1.id_conversation
            FROM conversation_users cu1
            JOIN conversation_users cu2
              ON cu1.id_conversation = cu2.id_conversation
            WHERE cu1.id_user = ?
              AND cu2.id_user = ?
            LIMIT 1
        ";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ss", $user1, $user2); // Đã đúng
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc()['id_conversation'] ?? 0;
    }

    // ✅ Tạo conversation mới
    public function createConversation($user1, $user2) {
        $this->conn->query("INSERT INTO conversations () VALUES ()");
        $conversation_id = $this->conn->insert_id;

        $stmt = $this->conn->prepare(
            "INSERT INTO conversation_users (id_conversation, id_user)
             VALUES (?, ?), (?, ?)"
        );
        // Đã đúng: isis
        $stmt->bind_param(
            "isis",
            $conversation_id, $user1,
            $conversation_id, $user2
        );
        $stmt->execute();

        return $conversation_id;
    }

    public function insertMessage($from_user, $to_user, $content) {
        // 1️⃣ Tìm conversation cũ
        $conversation_id = $this->findConversation($from_user, $to_user);

        // 2️⃣ Nếu chưa có thì tạo mới
        if ($conversation_id == 0) {
            $conversation_id = $this->createConversation($from_user, $to_user);
        }

        // 3️⃣ Insert tin nhắn
        $sql = "
            INSERT INTO messages (id_conversation, id_user, noidung) 
            VALUES (?, ?, ?);
        ";

        $stmt = $this->conn->prepare($sql);
        // Đã đúng: iss (int, string, string)
        $stmt->bind_param("iss", $conversation_id, $from_user, $content);
        $stmt->execute();

        // 4️⃣ Trả về conversation_id (để load lại chat)
        return $conversation_id;
    }

    public function loadMessage($user1, $user2) {
        // 1️⃣ Tìm conversation
        $conversation_id = $this->findConversation($user1, $user2);

        // 2️⃣ Nếu chưa có chat thì trả về mảng rỗng
        if ($conversation_id == 0) {
            return [];
        }

        // 3️⃣ Load danh sách tin nhắn
        // SỬA: u.username -> u.hoten
        $sql = "
            SELECT 
                m.id_message,
                m.id_user AS sender_id,  -- Đổi tên alias cho code PHP đỡ phải sửa
                u.hoten AS sender_name, 
                m.noidung AS content,    -- Alias lại thành content
                m.thoigian AS created_at -- Alias lại thành created_at
            FROM messages m
            JOIN users u ON m.id_user = u.id_user -- Sửa sender_id thành id_user
            WHERE m.id_conversation = ?
            ORDER BY m.thoigian ASC;
        ";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $conversation_id);
        $stmt->execute();

        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function getLatestConversation($user_id) {
        // SỬA: u.username -> u.hoten
        $sql = "
            SELECT 
                c.id_conversation,
                u.hoten as username
            FROM conversations c
            JOIN conversation_users cu1 
                ON c.id_conversation = cu1.id_conversation
            JOIN conversation_users cu2 
                ON c.id_conversation = cu2.id_conversation
            JOIN users u 
                ON u.id_user = cu2.id_user
            WHERE cu1.id_user = ?
            AND cu2.id_user != ?
            ORDER BY c.last_message_at DESC
            LIMIT 1
        ";

        $stmt = $this->conn->prepare($sql);
        // SỬA QUAN TRỌNG: ii -> ss (vì user_id là chuỗi)
        $stmt->bind_param("ss", $user_id, $user_id); 
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }
    
    public function getLastMessageByConversation($conversation_id) {
        // SỬA: u.username -> u.hoten
        $sql = "
        SELECT 
            m.id_message,
            m.sender_id,
            u.hoten AS sender_name,
            m.content,
            m.created_at
        FROM messages m
        JOIN users u ON m.sender_id = u.id_user
        WHERE m.id_conversation = ?
        ORDER BY m.thoigian DESC
        LIMIT 1
    ";

    $stmt = $this->conn->prepare($sql);
    $stmt->bind_param("i", $conversation_id);
    $stmt->execute();

    $row = $stmt->get_result()->fetch_assoc();
    return $row;
    }
    
    public function searchConversationBySenderName($my_id, $keyword){
        // SỬA: u.username -> u.hoten
        $sql = "
            SELECT 
                c.id_conversation,
                c.last_message_at,
                u.id_user,
                u.hoten as username,
                (
                    SELECT m.noidung        -- Sửa content thành noidung
                    FROM messages m
                    WHERE m.id_conversation = c.id_conversation
                    ORDER BY m.thoigian DESC -- Sửa created_at thành thoigian
                    LIMIT 1
                ) AS last_message
            FROM conversations c
            JOIN conversation_users cu1 ON c.id_conversation = cu1.id_conversation
            JOIN conversation_users cu2 ON c.id_conversation = cu2.id_conversation
            JOIN users u ON cu2.id_user = u.id_user
            WHERE cu1.id_user = ?
            AND cu2.id_user != ?
            AND u.hoten LIKE ?
            ORDER BY c.last_message_at DESC;
        ";

        $stmt = $this->conn->prepare($sql);

        $like = '%' . $keyword . '%';
        // SỬA QUAN TRỌNG: iis -> sss (3 chuỗi)
        $stmt->bind_param("sss", $my_id, $my_id, $like);

        $stmt->execute();
        return $stmt->get_result();
    }
        
    public function loadConversations($user_id){
        // SỬA: u.username -> u.hoten
        $sql = "
           SELECT 
            c.id_conversation,
            c.last_message_at,
            u.id_user,
            u.hoten,          -- Lấy tên người chat cùng
            u.avatar,         -- Nên lấy thêm avatar để hiển thị cho đẹp
            (
                SELECT m.noidung
                FROM messages m
                WHERE m.id_conversation = c.id_conversation
                ORDER BY m.thoigian DESC
                LIMIT 1
            ) AS last_message
        FROM conversations c
        JOIN conversation_users cu1 
            ON c.id_conversation = cu1.id_conversation
        JOIN conversation_users cu2 
            ON c.id_conversation = cu2.id_conversation
        JOIN users u 
            ON u.id_user = cu2.id_user
        WHERE cu1.id_user = ?     -- ID của người đang đăng nhập (Bạn)
        AND cu2.id_user != ?    -- ID của người kia (Để không lấy chính mình)
        ORDER BY c.last_message_at DESC
        ";

        $stmt = $this->conn->prepare($sql);

        $stmt->bind_param("ss", $user_id, $user_id);
        $stmt->execute();

        return $stmt->get_result();
    }

    public function getOtherUserId($conversation_id, $my_id){
        $sql = "
            SELECT id_user
            FROM conversation_users
            WHERE id_conversation = ?
            AND id_user != ?
            LIMIT 1
        ";

        $stmt = $this->conn->prepare($sql);
        // SỬA QUAN TRỌNG: ii -> is (conversation_id là int, my_id là string)
        $stmt->bind_param("is", $conversation_id, $my_id);
        $stmt->execute();

        $row = $stmt->get_result()->fetch_assoc();
        return $row['id_user'] ?? 0;
    }

    public function updateMessage($message_id, $user_id, $content){
        $sql = "
           UPDATE messages
            SET noidung = ?,     
                thoigian = NOW()  
            WHERE id_message = ? 
            AND id_user = ?;
        ";

        $stmt = $this->conn->prepare($sql);
        // SỬA QUAN TRỌNG: sii -> sis (content string, message_id int, user_id string)
        $stmt->bind_param("sis", $content, $message_id, $user_id);
        $stmt->execute();
    }

    public function deleteMessage($message_id, $user_id){
        $sql = "
           DELETE FROM messages
            WHERE id_message = ?
            AND id_user = ?;
        ";

        $stmt = $this->conn->prepare($sql);
        // SỬA QUAN TRỌNG: ii -> is (message_id int, user_id string)
        $stmt->bind_param("is", $message_id, $user_id);
        $stmt->execute();
    }
}
?>