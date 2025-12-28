<?php
class AttributeModel {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }
        
    public function getAttributesByCategory($id_danhmuc) {
        $id_parent = null;

        try {
            // 1. Lấy ID cha của danh mục để lấy thuộc tính kế thừa (ví dụ: Điện thoại kế thừa từ Đồ điện tử)
            $stmtParent = $this->conn->prepare("SELECT id_parent FROM danhmuc WHERE id_danhmuc = ?");
            $stmtParent->execute([$id_danhmuc]);
            $row = $stmtParent->fetch(PDO::FETCH_ASSOC);
            
            if ($row && !empty($row['id_parent'])) {
                $id_parent = $row['id_parent'];
            }

            // 2. Lấy thuộc tính của chính nó HOẶC của cha nó
            // Sử dụng COALESCE hoặc kiểm tra null để câu lệnh SQL không bị lỗi nếu id_parent là null
            $sql = "SELECT * FROM thuoc_tinh WHERE id_danhmuc = ? OR (id_danhmuc IS NOT NULL AND id_danhmuc = ?)";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([$id_danhmuc, $id_parent]);
            $attributes = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // 3. Lấy Options cho từng thuộc tính
            // Dùng dấu & trước $attr để thay đổi trực tiếp giá trị trong mảng $attributes
            foreach ($attributes as &$attr) {
                $stmtOpt = $this->conn->prepare("SELECT id_option, gia_tri_option FROM thuoc_tinh_options WHERE id_thuoctinh = ?");
                $stmtOpt->execute([$attr['id_thuoctinh']]);
                $attr['options'] = $stmtOpt->fetchAll(PDO::FETCH_ASSOC);
            }
            // Hủy tham chiếu sau vòng lặp để tránh lỗi logic ngoài ý muốn
            unset($attr);

            return $attributes;

        } catch (PDOException $e) {
            throw new Exception("Lỗi Database: " . $e->getMessage());
        }
    }
}
?>