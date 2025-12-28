<?php
class ChatModel {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

  // Lấy tên người gửi dựa trên ID (Varchar)
    public function getNameSenderByID($sender_id) {
        $sql = "SELECT hoten FROM users WHERE id_user = ? LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $sender_id); // "s" vì id_user là varchar(20)
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        return $result['hoten'] ?? '';
    }

    // Tìm hội thoại giữa 2 user (Cả 2 đều là Varchar)
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
        $stmt->bind_param("ss", $user1, $user2); // Đổi thành "ss"
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        return $result['id_conversation'] ?? 0;
    }

    // Tạo hội thoại mới
    public function createConversation($user1, $user2) {
        // Tạo bản ghi trong bảng conversations (Giả định bảng này có auto_increment id_conversation)
        $this->conn->query("INSERT INTO conversations () VALUES ()");
        $conversation_id = $this->conn->insert_id;

        $stmt = $this->conn->prepare(
            "INSERT INTO conversation_users (id_conversation, id_user)
             VALUES (?, ?), (?, ?)"
        );
        // id_conversation là Int (i), id_user là Varchar (s) -> thứ tự: i, s, i, s
        $stmt->bind_param("isis", $conversation_id, $user1, $conversation_id, $user2);
        $stmt->execute();

        return $conversation_id;
    }

        public function getOrCreateConversation($user1, $user2)
        {
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
            $stmt->bind_param("ss", $user1, $user2);
            $stmt->execute();
            $res = $stmt->get_result();

            if ($row = $res->fetch_assoc()) {
                return (int)$row['id_conversation']; // ✅ INT
            }

            return (int)$this->createConversation($user1, $user2); // ✅ INT
        }

    // Thêm tin nhắn mới
    public function insertMessage($from_user, $to_user, $content) {
        $conversation_id = $this->findConversation($from_user, $to_user);
        if ($conversation_id == 0) {
            $conversation_id = $this->createConversation($from_user, $to_user);
        }

        $sql = "INSERT INTO messages (id_conversation, sender_id, content) VALUES (?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        // id_conversation (i), sender_id (s), content (s)
        $stmt->bind_param("iss", $conversation_id, $from_user, $content);
        $stmt->execute();

        return $conversation_id;
    }

    // Load tin nhắn theo ID cuộc hội thoại
    public function loadMessageByConversation($conversation_id) {
        $sql = "
            SELECT 
                m.id_message,
                m.sender_id,
                u.hoten   AS sender_name,
                u.avatar  AS sender_avatar,
                m.content,
                m.created_at,
                m.updated_at
            FROM messages m
            JOIN users u ON m.sender_id = u.id_user
            WHERE m.id_conversation = ?
            ORDER BY m.created_at ASC
        ";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $conversation_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    // Lấy danh sách các cuộc hội thoại của 1 user
public function loadConversations($user_id) {
    // Sử dụng LEFT JOIN hoặc Subquery ở phần tin nhắn để đảm bảo không bị mất dòng nếu chưa có tin nhắn
    $sql = "
        SELECT 
            cu_me.id_conversation,
            c.last_message_at,
            u.id_user,
            u.hoten,
            u.avatar,
            -- Lấy tin nhắn cuối cùng (nếu chưa có thì trả về NULL)
            (
                SELECT m.content 
                FROM messages m 
                WHERE m.id_conversation = cu_me.id_conversation 
                ORDER BY m.created_at DESC 
                LIMIT 1
            ) as last_message
        FROM conversation_users cu_me
        -- 1. JOIN để tìm người kia (đối phương) trong cùng hội thoại
        JOIN conversation_users cu_other 
            ON cu_me.id_conversation = cu_other.id_conversation 
            AND cu_other.id_user != cu_me.id_user
        -- 2. JOIN bảng users để lấy tên/avatar đối phương
        JOIN users u 
            ON cu_other.id_user = u.id_user
        -- 3. JOIN bảng conversations để lấy thời gian (Nếu dữ liệu bị thiếu ở bảng này thì sẽ mất dòng)
        JOIN conversations c 
            ON cu_me.id_conversation = c.id_conversation
        WHERE cu_me.id_user = ?
        -- 4. Sắp xếp: Ưu tiên cái chưa có thời gian (mới tạo) hoặc mới nhắn tin lên đầu
        ORDER BY 
            (c.last_message_at IS NULL) DESC, -- Đưa hội thoại mới (chưa có time) lên đầu
            c.last_message_at DESC,           -- Đưa hội thoại mới nhắn gần đây lên tiếp theo
            c.id_conversation DESC            -- Nếu cùng null thì ID lớn lên đầu
    ";

    $stmt = $this->conn->prepare($sql);
    $stmt->bind_param("s", $user_id);
    $stmt->execute();
    return $stmt->get_result();
}
    public function getOtherUserId($conversation_id, $my_id)
        {
            $sql = "
                SELECT id_user
                FROM conversation_users
                WHERE id_conversation = ?
                AND id_user != ?
                LIMIT 1
            ";

            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("is", $conversation_id, $my_id);
            $stmt->execute();

            $row = $stmt->get_result()->fetch_assoc();
            return $row['id_user'] ?? '';
        }

    // Tìm kiếm hội thoại theo tên người nhận
    public function searchConversationBySenderName($my_id, $keyword){
        $sql = "
            SELECT 
                c.id_conversation,
                c.last_message_at,
                u.id_user,
                u.hoten,
                (SELECT m.content FROM messages m WHERE m.id_conversation = c.id_conversation ORDER BY m.created_at DESC LIMIT 1) AS last_message
            FROM conversations c
            JOIN conversation_users cu1 ON c.id_conversation = cu1.id_conversation
            JOIN conversation_users cu2 ON c.id_conversation = cu2.id_conversation
            JOIN users u ON cu2.id_user = u.id_user
            WHERE cu1.id_user = ?
            AND cu2.id_user != ?
            AND u.hoten LIKE ?
            ORDER BY c.last_message_at DESC
        ";
        $stmt = $this->conn->prepare($sql);
        $like = '%' . $keyword . '%';
        $stmt->bind_param("sss", $my_id, $my_id, $like); // "sss"
        $stmt->execute();
        return $stmt->get_result();
    }

    public function updateMessage($message_id, $user_id, $content){
        $sql = "UPDATE messages SET content = ?, updated_at = NOW() WHERE id_message = ? AND sender_id = ?";
        $stmt = $this->conn->prepare($sql);
        // content (s), id_message (i), sender_id (s)
        $stmt->bind_param("sis", $content, $message_id, $user_id);
        $stmt->execute();
    }

    public function deleteMessage($message_id, $user_id){
        $sql = "DELETE FROM messages WHERE id_message = ? AND sender_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("is", $message_id, $user_id); // id (i), user (s)
        $stmt->execute();
    }

    public function isConversationOfUser($conversation_id, $user_id) {
        $sql = "SELECT 1 FROM conversation_users WHERE id_conversation = ? AND id_user = ? LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("is", $conversation_id, $user_id); 
        $stmt->execute();
        $stmt->store_result();
        return $stmt->num_rows > 0;
    }
    public function searchMessageByContent($conversation_id, $keyword)
    {
        $sql = "
            SELECT 
                m.id_message,
                m.sender_id,
                u.hoten AS sender_name,
                u.avatar AS sender_avatar,
                m.content,
                m.created_at,
                m.updated_at
            FROM messages m
            JOIN users u ON m.sender_id = u.id_user
            WHERE m.id_conversation = ?
            AND m.content LIKE ?
            ORDER BY m.created_at ASC
        ";

        $stmt = $this->conn->prepare($sql);
        $likeKeyword = '%' . $keyword . '%';
        $stmt->bind_param("is", $conversation_id, $likeKeyword);
        $stmt->execute();

        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }


}
?>