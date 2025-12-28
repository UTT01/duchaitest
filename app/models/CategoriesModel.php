<?php
class CategoriesModel {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    // Lấy danh mục cha
    public function getParentCategories() {
        $sql = "SELECT id_danhmuc, ten_danhmuc FROM danhmuc WHERE id_parent IS NULL";
        $result = mysqli_query($this->conn, $sql);
        
        $data = [];
        if ($result) {
            while ($row = mysqli_fetch_assoc($result)) {
                $data[] = $row;
            }
        }
        return $data;
    }

    // Lấy danh mục con theo id_parent
    public function getSubCategories($id_parent) {
        $id_parent = mysqli_real_escape_string($this->conn, $id_parent);
        $sql = "SELECT id_danhmuc, ten_danhmuc FROM danhmuc WHERE id_parent = '$id_parent'";
        $result = mysqli_query($this->conn, $sql);

        $data = [];
        if ($result) {
            while ($row = mysqli_fetch_assoc($result)) {
                $data[] = $row;
            }
        }
        return $data;
    }

    // Lấy thuộc tính theo danh mục
    public function getAttributesByCategory($id_danhmuc) {
        $id_danhmuc = mysqli_real_escape_string($this->conn, $id_danhmuc);
        $id_parent = 'NULL';

        // 1. Lấy ID cha của danh mục để lấy thuộc tính kế thừa
        $sqlParent = "SELECT id_parent FROM danhmuc WHERE id_danhmuc = '$id_danhmuc'";
        $resParent = mysqli_query($this->conn, $sqlParent);
        if ($resParent && mysqli_num_rows($resParent) > 0) {
            $row = mysqli_fetch_assoc($resParent);
            if (!empty($row['id_parent'])) {
                $id_parent = "'" . $row['id_parent'] . "'";
            }
        }

        // 2. Lấy thuộc tính của chính nó HOẶC của cha nó
        $sql = "SELECT * FROM thuoc_tinh WHERE id_danhmuc = '$id_danhmuc' OR (id_danhmuc IS NOT NULL AND id_danhmuc = $id_parent)";
        $result = mysqli_query($this->conn, $sql);
        
        $attributes = [];
        if ($result) {
            while ($attr = mysqli_fetch_assoc($result)) {
                // 3. Lấy Options cho từng thuộc tính
                $id_thuoctinh = $attr['id_thuoctinh'];
                $sqlOpt = "SELECT id_option, gia_tri_option FROM thuoc_tinh_options WHERE id_thuoctinh = '$id_thuoctinh'";
                $resOpt = mysqli_query($this->conn, $sqlOpt);
                
                $options = [];
                if ($resOpt) {
                    while ($opt = mysqli_fetch_assoc($resOpt)) {
                        $options[] = $opt;
                    }
                }
                $attr['options'] = $options;
                $attributes[] = $attr;
            }
        }

        return $attributes;
    }
}
?>