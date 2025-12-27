<?php
// app/controllers/Chat.php
require_once __DIR__ . '/../models/ChatModel.php';
require_once __DIR__ . '/../helpers/time_helper.php'; // Nhớ copy file helper

class Chat {
    private $conn;
    private $chatModel;

    public function __construct($conn) {
        $this->conn = $conn;
        $this->chatModel = new ChatModel($conn);
    }

    // URL: /baitaplon/Chat/index
    public function index($conversation_id = 0) {
        // Kiểm tra đăng nhập
        if (!isset($_GET['user_id']) && !isset($_SESSION['user_id'])) {
             // Nếu chưa đăng nhập thì đá về login
             header("Location: /baitaplon/Login");
             exit();
        }
        
        // Lấy ID người đang đăng nhập (Ưu tiên Session)
        $my_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : $_GET['user_id'];
        
        // 1. Load danh sách chat
        $conversations = $this->chatModel->loadConversations($my_id);
        
        // 2. Xác định conversation đang mở
        $active_conversation_id = (int)$conversation_id;
        if ($active_conversation_id == 0) {
            $latest = $this->chatModel->getLatestConversation($my_id);
            $active_conversation_id = $latest['id_conversation'] ?? 0;
        }

        // 3. Lấy dữ liệu tin nhắn
        $sender_id = 0; 
        $sender_name = '';
        $messages = [];

        if ($active_conversation_id > 0) {
            $sender_id = $this->chatModel->getOtherUserId($active_conversation_id, $my_id);
            $sender_name = $this->chatModel->getNameSenderByID($sender_id);
            $messages = $this->chatModel->loadMessage($my_id, $sender_id);
        }

        // Truyền dữ liệu sang View
        $data = [
            'conversations' => $conversations,
            'active_conversation_id' => $active_conversation_id,
            'messages' => $messages,
            'sender_name' => $sender_name,
            'my_id' => $my_id
        ];
        
        // Gọi View (Lưu ý đường dẫn view của dự án chính)
        require_once __DIR__ . '/../views/chat_view.php';
    }

    // Chức năng bắt đầu chat từ trang sản phẩm
    // URL: /baitaplon/Chat/start/ID_NGUOI_BAN
    public function start($seller_id) {
        $my_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : '';
        if(empty($my_id)) {
            header("Location: /baitaplon/Login");
            exit();
        }

        if($my_id == $seller_id) {
            echo "<script>alert('Không thể chat với chính mình'); window.history.back();</script>";
            return;
        }

        // Tìm hoặc tạo cuộc hội thoại
        $conversation_id = $this->chatModel->findConversation($my_id, $seller_id);
        if($conversation_id == 0) {
            $conversation_id = $this->chatModel->createConversation($my_id, $seller_id);
        }

        // Chuyển hướng vào trang chat với ID hội thoại đó
        header("Location: /baitaplon/Chat/index/" . $conversation_id);
        exit();
    }

    // Gửi tin nhắn
    public function send() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $my_id = $_SESSION['user_id'];
            $to_user_id = $_POST['to_user_id']; // Cần thêm input hidden này ở View
            $content = trim($_POST['message']);
            
            if (!empty($content)) {
                $con_id = $this->chatModel->insertMessage($my_id, $to_user_id, $content);
                header("Location: /baitaplon/Chat/index/" . $con_id);
                exit();
            }
        }
        header("Location: /baitaplon/Chat");
    }
}
?>