<?php

class PostModel {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    // Insert sản phẩm
    public function insertProduct($ten_sanpham, $id_danhmuc, $id_user, $gia, $mota, $avatar) {
        try {
            $sql = "INSERT INTO sanpham (ten_sanpham, id_danhmuc, id_user, gia, mota, avatar, ngaydang, trangthai) 
                    VALUES (?, ?, ?, ?, ?, ?, NOW(), 'Chờ duyệt')";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([$ten_sanpham, $id_danhmuc, $id_user, $gia, $mota, $avatar]);
            return $this->conn->lastInsertId();
        } catch (PDOException $e) {
            throw new Exception("Lỗi insert sản phẩm: " . $e->getMessage());
        }
    }

    // Insert ảnh sản phẩm
    public function insertProductImage($id_sanpham, $url_anh) {
        try {
            $sql = "INSERT INTO sanpham_anh (id_sanpham, url_anh) VALUES (?, ?)";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([$id_sanpham, $url_anh]);
        } catch (PDOException $e) {
            throw new Exception("Lỗi insert ảnh: " . $e->getMessage());
        }
    }

    // Insert giá trị thuộc tính
    public function insertAttributeValue($id_sanpham, $id_thuoctinh, $id_option) {
        try {
            $sql = "INSERT INTO gia_tri_thuoc_tinh (id_sanpham, id_thuoctinh, id_option) VALUES (?, ?, ?)";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([$id_sanpham, $id_thuoctinh, $id_option]);
        } catch (PDOException $e) {
            throw new Exception("Lỗi insert giá trị thuộc tính: " . $e->getMessage());
        }
    }
}
?>
