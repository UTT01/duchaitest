<?php
// app/controllers/AdminReport.php

require_once __DIR__ . '/../models/AdminReportModel.php';
require_once __DIR__ . '/../models/UserModel.php'; // Cần model User để khóa tài khoản

class AdminReport {
    private $reportModel;
    private $userModel;
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
        $this->reportModel = new AdminReportModel($conn);
        // Giả sử bạn đã có UserModel để quản lý user
        // $this->userModel = new UserModel($conn); 
    }

    // URL: /AdminReport/index
    public function index() {
        // 1. Check quyền Admin (Giả sử bạn có session role)
        // if ($_SESSION['role'] !== 'ADMIN') { header('Location: /'); exit; }

        $reports = $this->reportModel->getAllReports();
        
        require __DIR__ . '/../views/Admin/report_list.php';
    }

    // URL: /AdminReport/process (POST)
    public function process() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $report_id = $_POST['report_id'];
            $action    = $_POST['action']; // 'BAN_USER' hoặc 'IGNORE'
            $reported_id = $_POST['reported_id']; // ID người bị tố cáo

            if ($action === 'BAN_USER') {
                // 1. Cập nhật trạng thái báo cáo thành ĐÃ XỬ LÝ (PROCESSED)
                $this->reportModel->updateStatus($report_id, 'PROCESSED', 'Đã khóa tài khoản người dùng.');
                
                // 2. Gọi hàm khóa tài khoản (Bạn cần tự viết hàm này trong UserModel)
                // $this->userModel->banUser($reported_id);
                
                // Demo tạm bằng câu lệnh SQL trực tiếp nếu chưa có UserModel
                $this->conn->query("UPDATE users SET status = 0 WHERE id_user = '$reported_id'");

                echo "<script>alert('Đã khóa tài khoản US$reported_id thành công!'); window.location.href='/baitaplon/AdminReport';</script>";

            } elseif ($action === 'IGNORE') {
                // Từ chối báo cáo
                $this->reportModel->updateStatus($report_id, 'REJECTED', 'Báo cáo không đủ căn cứ.');
                echo "<script>alert('Đã hủy bỏ báo cáo.'); window.location.href='/baitaplon/AdminReport';</script>";
            }
        }
    }
}
?>