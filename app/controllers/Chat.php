<?php

require_once __DIR__ . '/../models/ChatModel.php';
require_once __DIR__ . '/../helpers/time_helper.php';

class Chat {
    private $model;
    private $chatModel;

    public function __construct($conn) {
         $this->model = new ChatModel($conn);
         $this->chatModel = new ChatModel($conn);
    }

    public function index() {
        // redirect logic hoặc gọi trực tiếp
        $this->start();
    }

    // ===== DANH SÁCH =====
    public function start($param = null)
    {
        // 1️⃣ Kiểm tra đăng nhập trước khi lấy ID
        if (!isset($_SESSION['user_id'])) {
            header("Location: /baitaplon/Login");
            exit;
        }

        // SỬA: Dùng 'user_id' thay vì 'id_user'
        $my_id = $_SESSION['user_id']; 

        if (isset($_GET['product_id'])) {
            $_SESSION['chat_product_id'] = (int)$_GET['product_id'];
        }

        // 2️⃣ Load sidebar
        $conversations = $this->chatModel->loadConversations($my_id);

        $active_conversation_id = 0;

        if ($param !== null) {
            // 🔥 CASE A: param là conversation_id (chỉ khi toàn số)
            if (ctype_digit((string)$param) 
                && $this->chatModel->isConversationOfUser((int)$param, $my_id)) {

                $active_conversation_id = (int)$param;

            } 
            // 🔥 CASE B: param là seller_id (USxxx hoặc ID khác)
            else {
                
                $seller_id = $param; // ✅ GIỮ NGUYÊN STRING

                if ($seller_id != $my_id) { // So sánh lỏng lẻo để tránh lỗi type string/int
                    $active_conversation_id =
                        $this->chatModel
                            ->getOrCreateConversation($my_id, $seller_id);
                }
            }

            $_SESSION['active_conversation_id'] = $active_conversation_id;
            $_SESSION['sender_id'] = $seller_id ?? null;

        } else {
            // Không có param → conversation gần nhất
            $active_conversation_id =
                $_SESSION['active_conversation_id']
                ?? ($this->chatModel->getLatestConversation($my_id)['id_conversation'] ?? 0);
        }

        // 3️⃣ Load messages
        $sender_id = '';
        $sender_name = '';
        $messages = [];

        if ($active_conversation_id > 0) {
            $sender_id = $this->chatModel
                ->getOtherUserId($active_conversation_id, $my_id);

            $sender_name = $this->chatModel
                ->getNameSenderByID($sender_id);

            $messages = $this->chatModel
                ->loadMessageByConversation($active_conversation_id);
        }

        require __DIR__ . '/../views/GiaoDien_Chat.php';
    }

    public function send()
    {
        if (!isset($_SESSION['user_id'])) {
            header("Location: /baitaplon/Login");
            exit;
        }

        // SỬA: Dùng 'user_id'
        $my_id = $_SESSION['user_id'];
        $content = trim($_POST['message'] ?? '');
        $message_id = (int)($_POST['message_id'] ?? 0);

        // ✏️ ĐANG SỬA TIN NHẮN
        if ($message_id > 0 && $content !== '') {
            $this->chatModel->updateMessage($message_id, $my_id, $content);
        }
        // ➕ GỬI TIN MỚI
        else if ($content !== '') {
            $to_user = $_SESSION['sender_id'] ?? ''; // Thêm check null
            if (!empty($to_user)) {
                $conversation_id = $this->chatModel->insertMessage($my_id, $to_user, $content);
                $_SESSION['active_conversation_id'] = $conversation_id;
            }
        }

        header("Location: /baitaplon/chat");
        exit;
    }

    public function search()
    {
        if (!isset($_SESSION['user_id'])) {
            header("Location: /baitaplon/Login");
            exit;
        }

        // SỬA: Dùng 'user_id'
        $my_id   = $_SESSION['user_id'];
        $keyword = trim($_POST['keyword'] ?? '');

        // 1️⃣ Load danh sách conversation (theo keyword)
        if ($keyword !== '') {
            $conversations = $this->chatModel
                ->searchConversationBySenderName($my_id, $keyword);
        } else {
            $conversations = $this->chatModel
                ->loadConversations($my_id);
        }

        // 2️⃣ GIỮ NGUYÊN conversation đang active (KHÔNG DÙNG $_GET)
        $active_conversation_id = $_SESSION['active_conversation_id']
            ?? ($this->chatModel->getLatestConversation($my_id)['id_conversation'] ?? 0);

        // 3️⃣ Load sender + messages theo conversation_id
        $sender_id   = 0;
        $sender_name = '';
        $messages    = [];

        if ($active_conversation_id > 0) {
            $sender_id = $this->chatModel
                ->getOtherUserId($active_conversation_id, $my_id);

            $sender_name = $this->chatModel
                ->getNameSenderByID($sender_id);

            // 🔥 ĐÚNG KIẾN TRÚC
            $messages = $this->chatModel
                ->loadMessageByConversation($active_conversation_id);
        }

        require __DIR__ . '/../views/GiaoDien_Chat.php';
    }

    public function deleteMessage()
    {
        if (!isset($_SESSION['user_id'])) return;

        // SỬA: Dùng 'user_id'
        $my_id = $_SESSION['user_id'];
        $message_id = (int)($_POST['message_id'] ?? 0);

        if ($message_id > 0) {
            $this->chatModel->deleteMessage($message_id, $my_id);
        }

        header("Location: /baitaplon/chat");
        exit;
    }

    public function searchMessage()
    {
        if (!isset($_SESSION['user_id'])) {
            header("Location: /baitaplon/Login");
            exit;
        }

        // SỬA: Dùng 'user_id'
        $my_id = $_SESSION['user_id'];
        $keyword = trim($_POST['message_keyword'] ?? '');

        // 1️⃣ Load danh sách conversation (KHÔNG lọc)
        $conversations = $this->chatModel
            ->loadConversations($my_id);

        // 2️⃣ Lấy conversation đang active
        $active_conversation_id = $_SESSION['active_conversation_id']
            ?? ($this->chatModel->getLatestConversation($my_id)['id_conversation'] ?? 0);

        $sender_id = 0;
        $sender_name = '';
        $messages = [];

        if ($active_conversation_id > 0) {

            // 3️⃣ Lấy thông tin người chat
            $sender_id = $this->chatModel
                ->getOtherUserId($active_conversation_id, $my_id);

            $sender_name = $this->chatModel
                ->getNameSenderByID($sender_id);

            // 4️⃣ Tìm message theo nội dung
            if ($keyword !== '') {
                $messages = $this->chatModel
                    ->searchMessageByContent(
                        $active_conversation_id,
                        $keyword
                    );
            } else {
                // fallback: load toàn bộ
                $messages = $this->chatModel
                    ->loadMessageByConversation($active_conversation_id);
            }
        }

        require __DIR__ . '/../views/GiaoDien_Chat.php';
    }
}
?>