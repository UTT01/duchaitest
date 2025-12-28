<?php
// app/controllers/Vote.php

require_once __DIR__ . '/../models/VoteModel.php';

class Vote {
    private $voteModel;
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
        $this->voteModel = new VoteModel($conn);
    }

    // ==================================================
    // 1. HIỆN POPUP ĐÁNH GIÁ NGƯỜI DÙNG
    // URL: /Vote/dialog/{partner_id} (Ví dụ: US002)
    // ==================================================
    public function dialog($partner_id) {
        // 1. Kiểm tra đăng nhập
        if (!isset($_SESSION['user_id'])) {
            echo "Lỗi: Bạn chưa đăng nhập.";
            return;
        }

        $my_id = $_SESSION['user_id'];

        // 2. Không cho tự đánh giá mình
        if ($partner_id === $my_id) {
            echo "Lỗi: Không thể tự đánh giá bản thân.";
            return;
        }
        
        // 3. Lấy thông tin người bị đánh giá (Tên, Avatar...)
        $partnerInfo = $this->voteModel->getUserInfo($partner_id);

        if (!$partnerInfo) {
            echo "Lỗi: Người dùng không tồn tại.";
            return;
        }

        // 4. Chuẩn bị dữ liệu truyền sang View
        // Ở đây ta dùng user_id làm định danh chính thay vì transaction_id
        $target_id    = $partnerInfo['id_user']; 
        $target_name  = $partnerInfo['hoten'];
        
        // Lưu ý: View dialog.php cần sửa nhẹ để hứng biến $target_id
        require __DIR__ . '/../views/Vote/dialog.php';
    }

    // ==================================================
    // 2. XỬ LÝ SUBMIT
    // ==================================================
    public function submit() {
        header('Content-Type: application/json');

        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['status' => 'error', 'message' => 'Vui lòng đăng nhập.']);
            return;
        }

        $reviewer_id = $_SESSION['user_id']; // Tôi (Người đánh giá)
        
        // Lấy dữ liệu từ JS gửi lên
        // Lưu ý: JS cần gửi key là 'target_id' thay vì 'transaction_id'
        $rated_user_id = $_POST['target_id'] ?? ''; 
        $rating        = (int)($_POST['rating'] ?? 0);
        $comment       = trim($_POST['comment'] ?? '');

        // Validate
        if (empty($rated_user_id) || $rating < 1 || $rating > 5) {
            echo json_encode(['status' => 'error', 'message' => 'Dữ liệu không hợp lệ.']);
            return;
        }

        // 🛑 BẢO MẬT: Kiểm tra xem 2 người này có từng chat với nhau không?
        // Nếu chưa chat bao giờ -> Không cho đánh giá (chống spam)
        $hasChatted = $this->voteModel->checkIfChatted($reviewer_id, $rated_user_id);
        
        if (!$hasChatted) {
            echo json_encode(['status' => 'error', 'message' => 'Bạn cần nhắn tin với người này trước khi đánh giá.']);
            return;
        }

        // Lưu đánh giá
        $result = $this->voteModel->addReview($reviewer_id, $rated_user_id, $rating, $comment);

        if ($result) {
            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Lỗi hệ thống hoặc bạn đã đánh giá người này gần đây.']);
        }
    }
}
?>