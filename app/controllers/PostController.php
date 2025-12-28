<?php
// Đảm bảo load Model trước
require_once __DIR__ . '/../models/PostModel.php';

class PostController {
        private $postModel;
        private $conn; // Cần biến này để quản lý Transaction
        private $uploadDir;
        private $dbPublicPath;

        public function __construct($conn) {
            $this->conn = $conn;
            $this->postModel = new PostModel($conn);
            
            // 1. Đường dẫn vật lý trên Server (Dùng để move_uploaded_file)
            // __DIR__ là app/controllers -> ra ngoài 2 cấp là root -> vào public/images
            $this->uploadDir = __DIR__ . '/../../public/images/';
            
            // 2. Đường dẫn lưu vào Database (Dùng để hiển thị thẻ <img src="...">)
            // Giả sử thư mục gốc web trỏ vào folder chứa index.php
            $this->dbPublicPath = 'public/images/'; 
        }

        public function index($id_user = 0) {
            // Truyền biến $id_user_url sang view để dùng
            $id_user_url = $id_user;
            
            // Cập nhật đường dẫn mới (đã chuyển vào thư mục Page)
            require_once __DIR__ . '/../views/Page/View_ThemSP.php';
        }

        public function add() {
            // 1. Đảm bảo không có khoảng trắng thừa trước khi in JSON
            ob_clean(); 
            header('Content-Type: application/json; charset=utf-8');
            
            try {
                // SỬA LỖI 1: Dùng hàm đúng của MySQLi
                $this->conn->begin_transaction();

                if (empty($_POST['title']) || empty($_POST['price']) || empty($_POST['catLevel2'])) {
                    throw new Exception("Vui lòng nhập đầy đủ: Tiêu đề, Giá, Danh mục");
                }

                if (isset($_POST['id_user_posted']) && !empty($_POST['id_user_posted'])) {
                    $id_user = $_POST['id_user_posted'];
                } else {
                    $id_user = $_SESSION['user_id'] ?? 1;
                }

                $ten_sanpham = trim($_POST['title']);
                $id_danhmuc = intval($_POST['catLevel2']);
                $gia = floatval($_POST['price']);
                $mota = trim($_POST['description'] ?? '');
                $address = trim($_POST['address'] ?? '');

                // Xử lý Upload ảnh
                $uploadedImages = [];
                if (isset($_FILES['images']) && !empty($_FILES['images']['name'][0])) {
                    foreach ($_FILES['images']['name'] as $key => $val) {
                        $fileName = $this->uploadImage($_FILES['images'], $key);
                        if ($fileName) {
                            $uploadedImages[] = $this->dbPublicPath . $fileName;
                        }
                    }
                }

                $avatar = count($uploadedImages) > 0 ? $uploadedImages[0] : 'public/images/default.jpg';

                // Insert Sản phẩm
                $id_sanpham = $this->postModel->insertProduct($ten_sanpham, $id_danhmuc, $id_user, $gia, $mota, $avatar, $address);

                if (!$id_sanpham) {
                    // Lấy lỗi trực tiếp từ biến kết nối $this->conn
                    throw new Exception("Chi tiết lỗi DB: " . mysqli_error($this->conn));
                }

                // Insert Album ảnh
                foreach ($uploadedImages as $imgUrl) {
                    $this->postModel->insertProductImage($id_sanpham, $imgUrl);
                }

                // Insert Thuộc tính
                if (isset($_POST['thuoctinh']) && is_array($_POST['thuoctinh'])) {
                    foreach ($_POST['thuoctinh'] as $id_thuoctinh => $giatri) {
                        $val = trim($giatri);
                        if ($val !== '') {
                            $this->postModel->insertAttributeValue($id_sanpham, intval($id_thuoctinh), $val);
                        }
                    }
                }

                // SỬA LỖI: Commit transaction
                $this->conn->commit();

                echo json_encode([
                    'success' => true,
                    'message' => 'Đăng tin thành công!',
                    'id_sanpham' => $id_sanpham,
                    'redirect' => 'index.php?controller=product&action=detail&id=' . $id_sanpham
                ]);

            } catch (Exception $e) {
                // SỬA LỖI 2: Rollback thẳng, không kiểm tra inTransaction() vì MySQLi không có hàm đó
                try {
                    $this->conn->rollback();
                } catch (Exception $ex) {
                    // Bỏ qua lỗi rollback nếu chưa start transaction
                }

                http_response_code(400);
                echo json_encode([
                    'success' => false,
                    'error' => $e->getMessage()
                ]);
            }
            exit; // Dừng code ngay lập tức để tránh in thêm ký tự thừa
        }

    // Hàm chỉ trả về Tên file, việc ghép đường dẫn để controller lo
    private function uploadImage($files, $index) {
        // Kiểm tra lỗi upload từ PHP
        if ($files['error'][$index] !== UPLOAD_ERR_OK) {
            // Nếu không có file (lỗi số 4) thì bỏ qua, lỗi khác thì báo
            if ($files['error'][$index] == UPLOAD_ERR_NO_FILE) return null;
            throw new Exception("Lỗi upload file (Mã lỗi: " . $files['error'][$index] . ")");
        }

        $tmp_file = $files['tmp_name'][$index];
        $filename = basename($files['name'][$index]);
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        
        // Validate file type
        $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        if (!in_array($ext, $allowed)) {
            throw new Exception("File '$filename' không đúng định dạng ảnh.");
        }

        // Validate file size (ví dụ max 5MB)
        if ($files['size'][$index] > 5 * 1024 * 1024) {
             throw new Exception("File '$filename' quá lớn (Max 5MB).");
        }

        // Tạo tên file unique
        $newFilename = 'sp_' . time() . '_' . uniqid() . '.' . $ext;
        $destPath = $this->uploadDir . $newFilename;

        // Tạo thư mục nếu chưa có
        if (!is_dir($this->uploadDir)) {
            if (!mkdir($this->uploadDir, 0777, true)) {
                throw new Exception("Không thể tạo thư mục upload.");
            }
        }

        if (!move_uploaded_file($tmp_file, $destPath)) {
            throw new Exception("Không thể di chuyển file tới: " . $destPath);
        }

        return $newFilename; // Chỉ trả về tên file
    }
}
?>