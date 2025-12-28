<?php
// app/models/VoteModel.php

class VoteModel {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    // Lấy thông tin cơ bản của user để hiện lên popup
    public function getUserInfo($user_id) {
        $sql = "SELECT id_user, hoten, avatar FROM users WHERE id_user = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $user_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    // 🔥 KIỂM TRA QUAN HỆ: Hai người này có chung cuộc hội thoại nào không?
    public function checkIfChatted($user1, $user2) {
        // Logic: Tìm id_conversation mà CẢ user1 và user2 đều tham gia
        $sql = "
            SELECT c1.id_conversation 
            FROM conversation_users c1
            JOIN conversation_users c2 ON c1.id_conversation = c2.id_conversation
            WHERE c1.id_user = ? 
            AND c2.id_user = ?
            LIMIT 1
        ";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ss", $user1, $user2);
        $stmt->execute();
        $stmt->store_result();
        
        return $stmt->num_rows > 0; // Trả về true nếu tìm thấy
    }

    // Lưu đánh giá User
    public function addReview($reviewer_id, $rated_user_id, $rating, $comment) {
        // (Tùy chọn) Kiểm tra xem đã đánh giá trong vòng 7 ngày qua chưa?
        // Để tránh spam 1 người đánh giá 10 lần liên tục
        
        $sql = "INSERT INTO reviews (user_id, seller_id, rating, comment, created_at) VALUES (?, ?, ?, ?, NOW())";
        
        // Lưu ý: Cột seller_id trong bảng reviews bây giờ đóng vai trò là 'rated_user_id'
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ssis", $reviewer_id, $rated_user_id, $rating, $comment);
        
        return $stmt->execute();
    }
}
?>