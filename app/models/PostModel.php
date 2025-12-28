<?php
class PostModel
{
    private $con; // Đặt tên biến là $con cho giống convention của duchaitest

    public function __construct($conn)
    {
        $this->con = $conn;
    }

    // Insert sản phẩm
    public function insertProduct($ten_sanpham, $id_danhmuc, $id_user, $gia, $mota, $anh_dai_dien, $khu_vuc_ban)
    {
        // Escape dữ liệu đầu vào
        $ten_sanpham = mysqli_real_escape_string($this->con, $ten_sanpham);
        $id_danhmuc = mysqli_real_escape_string($this->con, $id_danhmuc);
        $id_user = mysqli_real_escape_string($this->con, $id_user);
        $gia = mysqli_real_escape_string($this->con, $gia);
        $mota = mysqli_real_escape_string($this->con, $mota);
        $anh_dai_dien = mysqli_real_escape_string($this->con, $anh_dai_dien);
        // Escape địa chỉ
        $khu_vuc_ban = mysqli_real_escape_string($this->con, $khu_vuc_ban);

        // SỬA: Thêm cột khu_vuc_ban vào câu lệnh SQL
        $sql = "INSERT INTO sanpham (ten_sanpham, id_danhmuc, id_user, gia, mota, avatar, khu_vuc_ban, ngaydang, trangthai) 
                VALUES ('$ten_sanpham', '$id_danhmuc', '$id_user', '$gia', '$mota', '$anh_dai_dien', '$khu_vuc_ban', NOW(), N'Đã duyệt')";

        $result = mysqli_query($this->con, $sql);

        if ($result) {
            return mysqli_insert_id($this->con);// Trả về ID vừa insert
        } else {
            return false;
        }
    }

    // Insert ảnh chi tiết sản phẩm
    public function insertProductImage($id_sanpham, $url_anh)
    {
        $id_sanpham = mysqli_real_escape_string($this->con, $id_sanpham);
        $url_anh = mysqli_real_escape_string($this->con, $url_anh);

        $sql = "INSERT INTO sanpham_anh (id_sanpham, url_anh) VALUES ('$id_sanpham', '$url_anh')";
        return mysqli_query($this->con, $sql);
    }

    // Insert giá trị thuộc tính (Nếu DB duchaitest có bảng này)
    public function insertAttributeValue($id_sanpham, $id_thuoctinh, $id_option)
    {
        $id_sanpham = mysqli_real_escape_string($this->con, $id_sanpham);
        $id_thuoctinh = mysqli_real_escape_string($this->con, $id_thuoctinh);
        $id_option = mysqli_real_escape_string($this->con, $id_option);

        $sql = "INSERT INTO gia_tri_thuoc_tinh (id_sanpham, id_thuoctinh, id_option) 
                VALUES ('$id_sanpham', '$id_thuoctinh', '$id_option')";
        return mysqli_query($this->con, $sql);
    }
}
?>