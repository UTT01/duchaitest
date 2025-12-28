<?php
// app/controllers/Report.php

require_once __DIR__ . '/../models/ReportModel.php';
require_once __DIR__ . '/../models/VoteModel.php'; // Tận dụng VoteModel để lấy thông tin user

class Report {
    private $reportModel;
    private $voteModel;
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
        $this->reportModel = new ReportModel($conn);
        $this->voteModel = new VoteModel($conn);
    }

    // ==================================================
    // 1. HIỂN THỊ FORM BÁO CÁO (GET)
    // ==================================================
    public function create() {
        // 1. Check đăng nhập
        if (!isset($_SESSION['user_id'])) {
            // Chuyển hướng về login hoặc báo lỗi
            die("Vui lòng đăng nhập để thực hiện chức năng này.");
        }

        // 2. Lấy ID người bị báo cáo từ URL
        $target_id = $_GET['target_id'] ?? '';

        if (empty($target_id)) {
            die("Lỗi: Không xác định được người cần báo cáo.");
        }

        // 3. Lấy thông tin người bị báo cáo để hiện tên cho chắc chắn
        $targetUser = $this->voteModel->getUserInfo($target_id);
        
        if (!$targetUser) {
            die("Người dùng không tồn tại.");
        }

        // Truyền dữ liệu sang View
        $target_name = $targetUser['hoten'];
        $target_avatar = $targetUser['avatar'];

        require __DIR__ . '/../views/Report/create.php';
    }

    // ==================================================
    // 2. XỬ LÝ GỬI BÁO CÁO (POST)
    // ==================================================
public function submit() {
        // 1. Kiểm tra phương thức POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: /"); exit;
        }

        // 2. Lấy dữ liệu đầu vào
        // Kiểm tra session để tránh lỗi nếu chưa đăng nhập
        if (!isset($_SESSION['user_id'])) {
            echo "<script>alert('Vui lòng đăng nhập!'); window.location.href='/LapTrinhWeb/baitaplon/Home';</script>";
            return;
        }

        $reporter_id = $_SESSION['user_id'];
        $target_id   = $_POST['target_id'] ?? '';
        $reason      = $_POST['reason'] ?? '';
        $description = $_POST['description'] ?? '';

        // --- 🔥 3. CODE CHỐNG SPAM (KIỂM TRA TRƯỚC) ---
        // Phải kiểm tra ngay đoạn này. Nếu đã báo cáo rồi thì dừng luôn, không tốn công upload ảnh.
        if ($this->reportModel->checkPendingReport($reporter_id, $target_id)) {
            echo "<script>
                alert('❌ BẠN ĐÃ BÁO CÁO NGƯỜI NÀY RỒI!\\n\\nĐơn báo cáo trước đó đang chờ Admin xử lý. Vui lòng không gửi lại nhiều lần.');
                
                // SỬA: Chuyển hướng thẳng về trang Chat thay vì quay lại
                window.location.href = '/baitaplon/Chat/'; 
            </script>";
            return; 
        }
        
        // --- 4. XỬ LÝ UPLOAD ẢNH BẰNG CHỨNG ---
        $imagePath = null;
        if (isset($_FILES['evidence']) && $_FILES['evidence']['error'] == 0) {
            $uploadDir = __DIR__ . '/../../public/uploads/reports/';
            
            // Tạo thư mục nếu chưa có
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            // Đổi tên file để tránh trùng: report_TIMESTAMP_random.jpg
            $extension = pathinfo($_FILES['evidence']['name'], PATHINFO_EXTENSION);
            $fileName = 'report_' . time() . '_' . rand(100,999) . '.' . $extension;
            $targetFile = $uploadDir . $fileName;

            // Di chuyển file
            if (move_uploaded_file($_FILES['evidence']['tmp_name'], $targetFile)) {
                // Lưu đường dẫn tương đối vào DB
                $imagePath = 'public/uploads/reports/' . $fileName;
            }
        }

        // --- 5. GỌI MODEL LƯU DB ---
        $result = $this->reportModel->createReport($reporter_id, $target_id, $reason, $description, $imagePath);

        if ($result) {
            // Báo thành công và quay về trang Chat
            echo "<script>alert('Đã gửi báo cáo thành công! Admin sẽ xem xét.'); window.location.href='/baitaplon/Chat';</script>";
        } else {
            echo "<script>alert('Lỗi hệ thống. Vui lòng thử lại.'); window.history.back();</script>";
        }
    }
}
?>