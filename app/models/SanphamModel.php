<?php
class SanphamModel
{
    private $con;

    // Nhận biến kết nối từ Controller truyền sang
    public function __construct($conn)
    {
        $this->con = $conn;
    }

    public function getAllCategories()
    {
        $sql = "SELECT id_danhmuc, ten_danhmuc FROM danhmuc ORDER BY ten_danhmuc ASC";
        $result = mysqli_query($this->con, $sql);
        $data = [];
        if ($result) {
            while ($row = mysqli_fetch_assoc($result)) {
                $data[] = $row;
            }
        }
        return $data;
    }

    // Đếm tổng số sản phẩm (phục vụ phân trang)
    public function countProducts($keyword = '', $category = '', $address = '', $userId = '')
    {
        $keyword = mysqli_real_escape_string($this->con, trim($keyword));
        $category = mysqli_real_escape_string($this->con, trim($category));
        $address = mysqli_real_escape_string($this->con, trim($address));
        $userId = mysqli_real_escape_string($this->con, trim($userId)); // Thêm escape cho an toàn

        $where = " WHERE 1 AND sp.trangthai = N'Đã duyệt'";
        if ($keyword !== '') $where .= " AND sp.ten_sanpham LIKE '%$keyword%'";
        if ($category !== '') $where .= " AND sp.id_danhmuc = '$category'";
        if ($address !== '')  $where .= " AND sp.khu_vuc_ban LIKE '%$address%'";
        if ($userId !== '')   $where .= " AND sp.id_user = '$userId' ";
        
        $sql = "SELECT COUNT(*) AS total FROM sanpham sp" . $where;
        $result = mysqli_query($this->con, $sql);
        if ($result) {
            $row = mysqli_fetch_assoc($result);
            return (int)$row['total'];
        }
        return 0;
    }

    public function getProducts($keyword = '', $category = '', $address = '', $offset = 0, $limit = 12, $userId = '')
    {
        $keyword = mysqli_real_escape_string($this->con, trim($keyword));
        $category = mysqli_real_escape_string($this->con, trim($category));
        $address = mysqli_real_escape_string($this->con, trim($address));
        $userId = mysqli_real_escape_string($this->con, trim($userId));

        $where = " WHERE 1 AND sp.trangthai = N'Đã duyệt'";
        if ($keyword !== '') $where .= " AND sp.ten_sanpham LIKE '%$keyword%'";
        if ($category !== '') {
            $where .= " AND (sp.id_danhmuc = '$category' OR sp.id_danhmuc IN (SELECT id_danhmuc FROM danhmuc WHERE id_parent = '$category'))";
        }
        if ($address !== '') $where .= " AND sp.khu_vuc_ban LIKE '%$address%'";
        if ($userId !== '')  $where .= " AND sp.id_user = '$userId' ";

        $sql = "SELECT sp.id_sanpham, sp.ten_sanpham, sp.gia, sp.mota, sp.avatar, sp.khu_vuc_ban, sp.ngaydang, sp.trangthai, dm.ten_danhmuc, 
                COALESCE(MIN(spa.url_anh), sp.avatar) AS anh_hienthi
                FROM sanpham sp
                LEFT JOIN danhmuc dm ON sp.id_danhmuc = dm.id_danhmuc
                LEFT JOIN sanpham_anh spa ON sp.id_sanpham = spa.id_sanpham
                " . $where . "
                GROUP BY sp.id_sanpham
                ORDER BY sp.ngaydang DESC
                LIMIT $offset, $limit";

        $result = mysqli_query($this->con, $sql);
        $data = [];
        if ($result) {
            while ($row = mysqli_fetch_assoc($result)) {
                $data[] = $row;
            }
        }
        return $data;
    }
    public function getProductById($id)
    {
        $id = mysqli_real_escape_string($this->con, $id);
        $sql = "SELECT s.*, u.hoten, u.sdt, u.avatar AS avatar_user, d.ten_danhmuc 
                FROM sanpham s
                JOIN users u ON s.id_user = u.id_user
                JOIN danhmuc d ON s.id_danhmuc = d.id_danhmuc
                WHERE s.id_sanpham = '$id'"; 
        $result = mysqli_query($this->con, $sql);
        return mysqli_fetch_assoc($result);
    }
    
    public function getProductImages($id)
    {
        $id = mysqli_real_escape_string($this->con, $id);
        $sql = "SELECT * FROM sanpham_anh WHERE id_sanpham = '$id'";
        $result = mysqli_query($this->con, $sql);
        $rows = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $rows[] = $row;
        }
        return $rows; 
    }
}
?>

