<?php
class User
{
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
    
    // Hiển thị trang Profile
    // URL: index.php?controller=user&action=profile&id=US001
    public function Profile($profileId, $loggedInId = '')
    {
        $userModel = $this->model('UserModel');
        $sanphamModel = $this->model('SanphamModel');

        // 1. XỬ LÝ ID ĐĂNG NHẬP (Để giữ trạng thái Navbar)
        // Nếu không truyền tham số thứ 2 ($loggedInId), 
        // thì mặc định coi như đang xem profile của chính mình ($loggedInId = $profileId)
        if (empty($loggedInId)) {
            $loggedInId = $profileId;
        }

        // 2. Lấy thông tin người dùng CẦN XEM (Profile)
        $userProfile = $userModel->getUserById($profileId);

        // 3. Lấy sản phẩm của người đó (Sửa lỗi hiển thị tất cả sản phẩm)
        // Tham số thứ 6 là $profileId để lọc theo User
        $products = $sanphamModel->getProducts('', '', '', 0, 100, $profileId);

        // 4. Kiểm tra quyền sở hữu (Để hiện nút Sửa)
        $isOwner = ($loggedInId === $profileId);

        $data = [
            'page'        => 'profile',
            'profile'     => $userProfile,
            'products'    => $products,
            'isOwner'     => $isOwner,
            // Quan trọng: Truyền id_user để Navbar file home.php nhận diện đã đăng nhập
            'id_user'     => $loggedInId, 
            'isLoggedIn'  => !empty($loggedInId)
        ];

        $this->view('home', $data);
    }

    // Xử lý cập nhật thông tin
    public function Update()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $id_user = $_POST['id_user'];
            $hoten = $_POST['hoten'];
            $sdt = $_POST['sdt'];
            $diachi = $_POST['diachi'];
            $gioithieu = $_POST['gioithieu'];
            
            // Xử lý upload ảnh
            $avatarUrl = null;
            if (isset($_FILES['avatar_file']) && $_FILES['avatar_file']['error'] == 0) {
                $target_dir = "baitaplon/public/images/";
                if (!file_exists($target_dir)) mkdir($target_dir, 0777, true);
                
                $fileName = time() . "_" . basename($_FILES["avatar_file"]["name"]);
                $target_file = $target_dir . $fileName;
                
                if (move_uploaded_file($_FILES["avatar_file"]["tmp_name"], $target_file)) {
                    $avatarUrl = $target_file;
                }
            }

            // Gọi Model cập nhật
            $userModel = $this->model('UserModel');
            $userModel->updateUser($id_user, $hoten, $sdt, $diachi, $gioithieu, $avatarUrl);

            // Quay lại trang profile của chính mình
            // Dùng urlencode để đảm bảo link đúng
            header("Location: /baitaplon/User/Profile/" . urlencode($id_user));
            exit();
        }
    }
}
?>