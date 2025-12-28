<?php
// app/models/AdminReportModel.php

class AdminReportModel {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    // Lấy tất cả báo cáo (Mới nhất lên đầu)

        public function getAllReports() {
            // 🔥 ĐÃ SỬA: Xóa u1.email và u2.email đi để tránh lỗi
            $sql = "
                SELECT 
                    r.*, 
                    u1.hoten AS reporter_name, 
                    u2.hoten AS reported_name
                FROM reports r
                JOIN users u1 ON r.reporter_id = u1.id_user  
                JOIN users u2 ON r.reported_id = u2.id_user  
                ORDER BY r.created_at DESC
            ";
            
            $result = $this->conn->query($sql);
            
            $data = [];
            if ($result && $result->num_rows > 0) { // Thêm check $result tồn tại cho chắc
                while ($row = $result->fetch_assoc()) {
                    $data[] = $row;
                }
            }
            return $data;
        }

    // Xử lý báo cáo (Duyệt/Bỏ qua)
    public function updateStatus($report_id, $status, $admin_note = '') {
        $sql = "UPDATE reports SET status = ?, admin_note = ?, updated_at = NOW() WHERE id_report = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ssi", $status, $admin_note, $report_id);
        return $stmt->execute();
    }
}
?>