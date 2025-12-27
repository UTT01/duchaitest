<?php
class CategoriesModel {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

        // Lấy danh mục cha
        public function getParentCategories() {
            $sql = "SELECT id_danhmuc, ten_danhmuc FROM danhmuc WHERE id_parent IS NULL";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        // Lấy danh mục con theo id_parent
        public function getSubCategories($id_parent) {
            $sql = "SELECT id_danhmuc, ten_danhmuc FROM danhmuc WHERE id_parent = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([$id_parent]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        // Lấy thuộc tính theo danh mục
        public function getAttributesByCategory($id_danhmuc) {
            $id_parent = null;

            try {
                // 1. Lấy ID cha của danh mục để lấy thuộc tính kế thừa
                $stmtParent = $this->conn->prepare("SELECT id_parent FROM danhmuc WHERE id_danhmuc = ?");
                $stmtParent->execute([$id_danhmuc]);
                $row = $stmtParent->fetch(PDO::FETCH_ASSOC);
                
                if ($row && !empty($row['id_parent'])) {
                    $id_parent = $row['id_parent'];
                }

                // 2. Lấy thuộc tính của chính nó HOẶC của cha nó
                $sql = "SELECT * FROM thuoc_tinh WHERE id_danhmuc = ? OR (id_danhmuc IS NOT NULL AND id_danhmuc = ?)";
                $stmt = $this->conn->prepare($sql);
                $stmt->execute([$id_danhmuc, $id_parent]);
                $attributes = $stmt->fetchAll(PDO::FETCH_ASSOC);

                // 3. Lấy Options cho từng thuộc tính
                foreach ($attributes as &$attr) {
                    $stmtOpt = $this->conn->prepare("SELECT id_option, gia_tri_option FROM thuoc_tinh_options WHERE id_thuoctinh = ?");
                    $stmtOpt->execute([$attr['id_thuoctinh']]);
                    $attr['options'] = $stmtOpt->fetchAll(PDO::FETCH_ASSOC);
                }

                return $attributes;

            } catch (PDOException $e) {
                throw new Exception($e->getMessage());
            }
        }
    }
?>

        
