<?php

class DuyetSPModel {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    // Lấy tất cả sản phẩm chờ duyệt (trạng thái = 'Chờ duyệt')
    public function getPendingProducts() {
        try {
            $sql = "SELECT sp.id_sanpham, sp.ten_sanpham, sp.gia, sp.mota, sp.avatar, sp.ngaydang, 
                           dm.ten_danhmuc, u.hoten, u.sdt, u.diachi
                    FROM sanpham sp
                    JOIN danhmuc dm ON sp.id_danhmuc = dm.id_danhmuc
                    JOIN users u ON sp.id_user = u.id_user
                    WHERE sp.trangthai = 'Chờ duyệt'
                    ORDER BY sp.ngaydang DESC";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Lỗi lấy danh sách sản phẩm: " . $e->getMessage());
        }
    }

    // Lấy chi tiết sản phẩm
    public function getProductDetail($id_sanpham) {
        try {
            $sql = "SELECT sp.*, dm.ten_danhmuc, u.hoten, u.sdt, u.diachi
                    FROM sanpham sp
                    JOIN danhmuc dm ON sp.id_danhmuc = dm.id_danhmuc
                    JOIN users u ON sp.id_user = u.id_user
                    WHERE sp.id_sanpham = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([$id_sanpham]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Lỗi lấy chi tiết sản phẩm: " . $e->getMessage());
        }
    }

    // Lấy ảnh sản phẩm
    public function getProductImages($id_sanpham) {
        try {
            $sql = "SELECT id_anh, url_anh FROM sanpham_anh WHERE id_sanpham = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([$id_sanpham]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Lỗi lấy ảnh sản phẩm: " . $e->getMessage());
        }
    }

    public function getProductAttributes($id_sanpham) {
        try {
            $sql = "SELECT tt.ten_thuoctinh, 
                    COALESCE(opt.gia_tri_option, gvt.id_option) as giatri
                    FROM gia_tri_thuoc_tinh gvt
                    JOIN thuoc_tinh tt ON gvt.id_thuoctinh = tt.id_thuoctinh
                    LEFT JOIN thuoc_tinh_options opt ON gvt.id_option = opt.id_option
                    WHERE gvt.id_sanpham = ?
                    ORDER BY tt.id_thuoctinh";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([$id_sanpham]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Lỗi lấy thuộc tính sản phẩm: " . $e->getMessage());
        }
    }

    // Duyệt sản phẩm (cập nhật trạng thái)
    public function approveProduct($id_sanpham) {
        try {
            $sql = "UPDATE sanpham SET trangthai = 'Đã duyệt' WHERE id_sanpham = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([$id_sanpham]);
            return true;
        } catch (PDOException $e) {
            throw new Exception("Lỗi duyệt sản phẩm: " . $e->getMessage());
        }
    }

    // Duyệt tất cả sản phẩm chờ duyệt
    public function approveAllProducts() {
        try {
            $sql = "UPDATE sanpham SET trangthai = 'Đã duyệt' WHERE trangthai = 'Chờ duyệt'";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
            return true;
        } catch (PDOException $e) {
            throw new Exception("Lỗi duyệt tất cả sản phẩm: " . $e->getMessage());
        }
    }

    // Từ chối sản phẩm (cập nhật trạng thái)
    public function rejectProduct($id_sanpham) {
        try {
            $sql = "UPDATE sanpham SET trangthai = 'Từ chối' WHERE id_sanpham = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([$id_sanpham]);
            return true;
        } catch (PDOException $e) {
            throw new Exception("Lỗi từ chối sản phẩm: " . $e->getMessage());
        }
    }
}
?>
