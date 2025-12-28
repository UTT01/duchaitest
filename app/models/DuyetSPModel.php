<?php
class DuyetSPModel
{
    private $con;

    public function __construct($conn)
    {
        $this->con = $conn;
    }

    // Lấy tất cả sản phẩm chờ duyệt
    public function getPendingProducts()
    {
        // Lưu ý: Đã đổi sp.avatar thành sp.anh_dai_dien
        $sql = "SELECT sp.id_sanpham, sp.ten_sanpham, sp.gia, sp.mota, sp.anh_dai_dien, sp.ngaydang, 
                       dm.ten_danhmuc, u.hoten, u.sdt
                FROM sanpham sp
                JOIN danhmuc dm ON sp.id_danhmuc = dm.id_danhmuc
                JOIN users u ON sp.id_user = u.id_user
                WHERE sp.trangthai = 'Chờ duyệt'
                ORDER BY sp.ngaydang DESC";

        $result = mysqli_query($this->con, $sql);
        $data = [];
        if ($result) {
            while ($row = mysqli_fetch_assoc($result)) {
                $data[] = $row;
            }
        }
        return $data;
    }

    // Lấy chi tiết sản phẩm để duyệt
    public function getProductDetail($id_sanpham)
    {
        $id_sanpham = mysqli_real_escape_string($this->con, $id_sanpham);
        
        // Lưu ý: Đã kiểm tra users có cột diachi hay không, nếu không có hãy bỏ u.diachi đi
        $sql = "SELECT sp.*, dm.ten_danhmuc, u.hoten, u.sdt, u.diachi
                FROM sanpham sp
                JOIN danhmuc dm ON sp.id_danhmuc = dm.id_danhmuc
                JOIN users u ON sp.id_user = u.id_user
                WHERE sp.id_sanpham = '$id_sanpham'";

        $result = mysqli_query($this->con, $sql);
        if ($result) {
            return mysqli_fetch_assoc($result);
        }
        return null;
    }

    // Lấy ảnh sản phẩm
    public function getProductImages($id_sanpham)
    {
        $id_sanpham = mysqli_real_escape_string($this->con, $id_sanpham);
        $sql = "SELECT id_anh, url_anh FROM sanpham_anh WHERE id_sanpham = '$id_sanpham'";
        
        $result = mysqli_query($this->con, $sql);
        $data = [];
        if ($result) {
            while ($row = mysqli_fetch_assoc($result)) {
                $data[] = $row;
            }
        }
        return $data;
    }

    // Lấy thuộc tính sản phẩm (Lưu ý: Chỉ chạy nếu có bảng gia_tri_thuoc_tinh)
    public function getProductAttributes($id_sanpham)
    {
        $id_sanpham = mysqli_real_escape_string($this->con, $id_sanpham);
        $sql = "SELECT tt.ten_thuoctinh, 
                COALESCE(opt.gia_tri_option, gvt.id_option) as giatri
                FROM gia_tri_thuoc_tinh gvt
                JOIN thuoc_tinh tt ON gvt.id_thuoctinh = tt.id_thuoctinh
                LEFT JOIN thuoc_tinh_options opt ON gvt.id_option = opt.id_option
                WHERE gvt.id_sanpham = '$id_sanpham'
                ORDER BY tt.id_thuoctinh";

        $result = mysqli_query($this->con, $sql);
        $data = [];
        if ($result) {
            while ($row = mysqli_fetch_assoc($result)) {
                $data[] = $row;
            }
        }
        return $data;
    }

    // Duyệt sản phẩm
    public function approveProduct($id_sanpham)
    {
        $id_sanpham = mysqli_real_escape_string($this->con, $id_sanpham);
        $sql = "UPDATE sanpham SET trangthai = 'Đã duyệt' WHERE id_sanpham = '$id_sanpham'";
        return mysqli_query($this->con, $sql);
    }

    // Duyệt tất cả
    public function approveAllProducts()
    {
        $sql = "UPDATE sanpham SET trangthai = 'Đã duyệt' WHERE trangthai = 'Chờ duyệt'";
        return mysqli_query($this->con, $sql);
    }

    // Từ chối sản phẩm
    public function rejectProduct($id_sanpham)
    {
        $id_sanpham = mysqli_real_escape_string($this->con, $id_sanpham);
        $sql = "UPDATE sanpham SET trangthai = 'Từ chối' WHERE id_sanpham = '$id_sanpham'";
        return mysqli_query($this->con, $sql);
    }
}
?>