<?php
// app/models/ReportModel.php

class ReportModel {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function createReport($reporter_id, $target_id, $reason, $description, $evidence_image) {
        $sql = "INSERT INTO reports (reporter_id, reported_id, reason, description, evidence_image, status, created_at) 
                VALUES (?, ?, ?, ?, ?, 'PENDING', NOW())";
        
        $stmt = $this->conn->prepare($sql);
        
        // sssss: 5 chuỗi (reporter, reported, reason, description, image)
        $stmt->bind_param("sssss", $reporter_id, $target_id, $reason, $description, $evidence_image);
        
        return $stmt->execute();
    }
    // app/models/ReportModel.php

    //Check trùng report
    public function checkPendingReport($reporter_id, $target_id) {
        // Logic: Tìm xem có đơn nào của Reporter gửi cho Target mà trạng thái đang là 'PENDING' không?
        $sql = "SELECT id_report 
                FROM reports 
                WHERE reporter_id = ? 
                AND reported_id = ? 
                AND status = 'PENDING'";
                
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ss", $reporter_id, $target_id);
        $stmt->execute();
        $stmt->store_result();
        
        // Trả về TRUE nếu tìm thấy (tức là ĐANG CÓ đơn chưa xử lý -> bị trùng)
        return $stmt->num_rows > 0;
    }
}
?>