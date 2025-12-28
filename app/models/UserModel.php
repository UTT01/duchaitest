<?php
class UserModel
{
    private $con;

    public function __construct($conn)
    {
        $this->con = $conn;
    }

    public function getUserById($id_user)
    {
        $id_user = mysqli_real_escape_string($this->con, $id_user);
        $sql = "SELECT * FROM users WHERE id_user = '$id_user'";
        $result = mysqli_query($this->con, $sql);
        if ($result) {
            return mysqli_fetch_assoc($result);
        }
        return null;
    }

    public function isManager($id_user)
    {
        $user = $this->getUserById($id_user);
        if ($user) {
            if (isset($user['vaitro'])) {
                return $user['vaitro'] == 'quanly' || $user['vaitro'] == 'admin';
            }
            if (isset($user['role'])) {
                return $user['role'] == 'quanly' || $user['role'] == 'admin' || $user['role'] == 'manager';
            }
            // Nếu không có cột vaitro/role, mặc định không phải quản lý
            return false;
        }
        return false;
    }
    public function updateUser($id_user, $hoten, $sdt, $diachi, $gioithieu, $avatarUrl = null)
{
    $id_user = mysqli_real_escape_string($this->con, $id_user);
    $hoten = mysqli_real_escape_string($this->con, $hoten);
    $sdt = mysqli_real_escape_string($this->con, $sdt);
    $diachi = mysqli_real_escape_string($this->con, $diachi);
    $gioithieu = mysqli_real_escape_string($this->con, $gioithieu);

    $sql = "UPDATE users SET 
            hoten = '$hoten', 
            sdt = '$sdt', 
            diachi = '$diachi', 
            gioithieu = '$gioithieu'";

    if ($avatarUrl !== null) {
        $avatarUrl = mysqli_real_escape_string($this->con, $avatarUrl);
        $sql .= ", avatar = '$avatarUrl'";
    }

    $sql .= " WHERE id_user = '$id_user'";

    return mysqli_query($this->con, $sql);
}

    /**
     * Xác thực đăng nhập với username và password
     * @param string $username Tên đăng nhập
     * @param string $password Mật khẩu
     * @return array|null Trả về thông tin user nếu đăng nhập thành công, null nếu thất bại
     */
    public function authenticate($username, $password)
    {
        $username = mysqli_real_escape_string($this->con, trim($username));
        
        // Tìm user theo username trong bảng account
        $sql = "SELECT * FROM account WHERE username = '$username' AND trangthai = 1";
        $result = mysqli_query($this->con, $sql);
        
        if ($result && mysqli_num_rows($result) > 0) {
            $user = mysqli_fetch_assoc($result);
            
            // Kiểm tra mật khẩu
            // Hỗ trợ cả password đã hash (password_hash) và plain text
            $storedPassword = $user['password'];
            
            // Nếu password bắt đầu bằng $2y$ hoặc $2a$ thì là password đã hash
            if (strpos($storedPassword, '$2y$') === 0 || strpos($storedPassword, '$2a$') === 0) {
                // Kiểm tra password đã hash
                if (password_verify($password, $storedPassword)) {
                    return $user;
                }
            } else {
                // So sánh password plain text (để tương thích với dữ liệu cũ)
                if ($password === $storedPassword) {
                    return $user;
                }
            }
        }
        
        return null;
    }
}
?>

