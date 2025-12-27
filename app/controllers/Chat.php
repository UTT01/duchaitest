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
    public function index($partner_id = 0, $url_user_id = '') {
        // 1. Kiểm tra đăng nhập (Ưu tiên Session, nếu không có thì lấy trên URL)
        if (!isset($_SESSION['user_id']) && empty($url_user_id)) {
             header("Location: /baitaplon/Login");
             exit();
        }
        
        $my_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : $url_user_id;
        
        // 2. Load danh sách các cuộc trò chuyện bên trái
        $conversations = $this->chatModel->loadConversations($my_id);
        
        // 3. Xử lý logic chọn cuộc hội thoại active
        $active_conversation_id = 0;
        
        // Nếu trên URL chưa có ID đối phương (vào trang chat lần đầu)
        if ($partner_id == 0 || $partner_id === '0') {
            // Lấy cuộc trò chuyện gần nhất
            $latest = $this->chatModel->getLatestConversation($my_id);
            if ($latest) {
                $active_conversation_id = $latest['id_conversation'];
                // Tìm ID đối phương để cập nhật lại biến $partner_id (cho view hiển thị đúng)
                $partner_id = $this->chatModel->getOtherUserId($active_conversation_id, $my_id);
            }
        } else {
            // Nếu đã có ID đối phương trên URL -> Tìm ID hội thoại
            $active_conversation_id = $this->chatModel->findConversation($my_id, $partner_id);
            
            // Nếu chưa có hội thoại thì tạo mới luôn (để tránh lỗi)
            if ($active_conversation_id == 0) {
                $active_conversation_id = $this->chatModel->createConversation($my_id, $partner_id);
            }
        }

        // 4. Lấy dữ liệu tin nhắn
        $sender_id = 0; // Đây chính là partner_id (người gửi tin cho mình xem)
        $sender_name = '';
        $messages = [];

        if ($active_conversation_id > 0) {
            // Đảm bảo partner_id đúng với conversation đang mở
            $sender_id = $this->chatModel->getOtherUserId($active_conversation_id, $my_id);
            $sender_name = $this->chatModel->getNameSenderByID($sender_id);
            $messages = $this->chatModel->loadMessage($my_id, $sender_id);
        }

        // 5. Truyền dữ liệu sang View
        $data = [
            'conversations' => $conversations,
            'active_conversation_id' => $active_conversation_id,
            'messages' => $messages,
            'sender_name' => $sender_name,
            'sender_id' => $sender_id, // ID người mình đang chat cùng
            'my_id' => $my_id          // ID của mình
        ];
        
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

        // Thay vì chuyển hướng đến ID hội thoại, ta chuyển hướng đến format URL mới:
        // /Chat/index/NGUOI_BAN/TOI
        header("Location: /baitaplon/Chat/index/" . $seller_id . "/" . $my_id);
        exit();
    }
    // Gửi tin nhắn
    public function send() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // 1. Lấy ID người gửi (mình)
            $my_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : '';
            
            // Nếu session bị mất hoặc không có, thử lấy từ post (nếu bạn có input hidden my_id) 
            // hoặc redirect về login
            if(empty($my_id)) {
                header("Location: /baitaplon/Login");
                exit();
            }

            // 2. Lấy dữ liệu từ Form
            $to_user_id = isset($_POST['to_user_id']) ? $_POST['to_user_id'] : '';
            $content = isset($_POST['message']) ? trim($_POST['message']) : '';
            
            if (!empty($content) && !empty($to_user_id)) {
                // 3. Gọi Model để lưu tin nhắn
                // Hàm này trả về conversation_id nhưng ta không dùng để redirect nữa
                $this->chatModel->insertMessage($my_id, $to_user_id, $content);

                // 4. [QUAN TRỌNG] Redirect về đúng URL định dạng mới:
                // /Chat/index/NGUOI_KIA/MINH
                header("Location: /baitaplon/Chat/index/" . $to_user_id . "/" . $my_id);
                exit();
            }
        }
        
        // Trường hợp lỗi hoặc không post, về trang chủ chat
        header("Location: /baitaplon/Chat");
    }
    public function search() {
        // Kiểm tra đăng nhập
        if (!isset($_SESSION['user_id'])) {
             header("Location: /baitaplon/Login");
             exit();
        }

        $my_id = $_SESSION['user_id'];
        $keyword = isset($_POST['keyword']) ? trim($_POST['keyword']) : '';

        // 1. Gọi Model để tìm kiếm
        if ($keyword !== '') {
            // Tìm theo tên người gửi
            $conversations = $this->chatModel->searchConversationBySenderName($my_id, $keyword);
        } else {
            // Nếu không nhập gì thì load danh sách mặc định
            $conversations = $this->chatModel->loadConversations($my_id);
        }

        // 2. Các logic hiển thị giao diện (giống hệt hàm index)
        $active_conversation_id = isset($_GET['conversation_id']) ? (int)$_GET['conversation_id'] : 0;
        if ($active_conversation_id == 0) {
            $latest = $this->chatModel->getLatestConversation($my_id);
            $active_conversation_id = $latest['id_conversation'] ?? 0;
        }

        $sender_id = 0; 
        $sender_name = ''; 
        $messages = [];

        if ($active_conversation_id > 0) {
            $sender_id = $this->chatModel->getOtherUserId($active_conversation_id, $my_id);
            $sender_name = $this->chatModel->getNameSenderByID($sender_id);
            $messages = $this->chatModel->loadMessage($my_id, $sender_id);
        }

        // 3. Truyền dữ liệu sang View
        $data = [
            'conversations' => $conversations,
            'active_conversation_id' => $active_conversation_id,
            'messages' => $messages,
            'sender_name' => $sender_name,
            'sender_id' => $sender_id,
            'my_id' => $my_id,
            // Truyền lại từ khóa để giữ trên ô input sau khi tìm
            'keyword' => $keyword 
        ];
        
        require_once __DIR__ . '/../views/chat_view.php';
    }

    public function edit_msg() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $my_id = $_SESSION['user_id'];
            $message_id = isset($_POST['message_id']) ? (int)$_POST['message_id'] : 0;
            $content = isset($_POST['message']) ? trim($_POST['message']) : '';

            if ($message_id > 0 && !empty($content)) {
                $this->chatModel->updateMessage($message_id, $my_id, $content);
            }
            // Quay lại trang trước (hoặc trang index chat)
            header("Location: " . $_SERVER['HTTP_REFERER']);
        }
    }

    public function delete_msg() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $my_id = $_SESSION['user_id'];
            $message_id = isset($_POST['message_id']) ? (int)$_POST['message_id'] : 0;

            if ($message_id > 0) {
                $this->chatModel->deleteMessage($message_id, $my_id);
            }
            header("Location: " . $_SERVER['HTTP_REFERER']);
        }
    }
}
?>