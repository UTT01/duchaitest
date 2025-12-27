<?php 
class Login {
    protected $conn;
    
    public function __construct($conn)
    {
        $this->conn = $conn;
    }
    
    /**
     * Load model
     */
    protected function model($modelName)
    {
        $modelFile = __DIR__ . '/../models/' . $modelName . '.php';
        if (file_exists($modelFile)) {
            require_once $modelFile;
            $model = new $modelName($this->conn);
            return $model;
        } else {
            die("Model $modelName không tồn tại!");
        }
    }
    
    /**
     * Load view
     */
    protected function view($viewName, $data = [])
    {
        $viewFile = __DIR__ . '/../views/' . $viewName . '.php';
        if (file_exists($viewFile)) {
            // Extract data array thành các biến
            extract($data);
            require_once $viewFile;
        } else {
            die("View $viewName không tồn tại!");
        }
    }
    
    public function index(){
        $data = [];
        $this->view('login_view', $data);
    }

    /**
     * Xử lý đăng nhập
     */
    public function processLogin()
    {
        $error = '';
        
        // Kiểm tra nếu có POST request
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = isset($_POST['username']) ? trim($_POST['username']) : '';
            $password = isset($_POST['password']) ? trim($_POST['password']) : '';
            
            // Validate input
            if (empty($username) || empty($password)) {
                $error = 'Vui lòng nhập đầy đủ tên đăng nhập và mật khẩu!';
            } else {
                // Gọi model để xác thực
                $userModel = $this->model('UserModel');
                $user = $userModel->authenticate($username, $password);
                
                if ($user) {
                    // 1. Lưu ID vào Session (QUAN TRỌNG NHẤT)
                    $_SESSION['user_id'] = $user['id_user'];
                    $_SESSION['username'] = $user['username']; // Lưu thêm tên nếu cần
                
                    // 2. Redirect về Home (Giữ nguyên user_id trên URL để code cũ vẫn chạy)
                    $id_user = $user['id_user'];
                    header("Location: /baitaplon/Home?user_id=" . urlencode($id_user));
                    exit();
                } else {
                    $error = 'Tên đăng nhập hoặc mật khẩu không đúng!';
                }
            }
        } else {
            // Nếu không phải POST request, redirect về trang đăng nhập
            header("Location: /baitaplon/Login");
            exit();
        }
        
        // Nếu có lỗi, hiển thị lại form với thông báo lỗi
        $data = [
            'error' => $error
        ];
        $this->view('login_view', $data);
    }
}
?>