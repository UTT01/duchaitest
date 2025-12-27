<?php
// Hứng dữ liệu từ Controller
$p = isset($data['product']) ? $data['product'] : null;
$imgs = isset($data['productImages']) ? $data['productImages'] : [];


// Kiểm tra nếu không tìm thấy sản phẩm
if (!$p) {
    echo '<div class="alert alert-danger container mt-5">
            Sản phẩm không tồn tại! (Mã nhận được: <strong>' . htmlspecialchars($id_sanpham) . '</strong>) 
            <a href="./">Quay lại</a>
          </div>';
    return; // Dừng chạy file
}

// Xử lý ảnh đại diện mặc định nếu null
$mainAvatar = !empty($p['avatar']) ? $p['avatar'] : 'https://via.placeholder.com/150';
$mainImg = !empty($p['anh_dai_dien']) ? $p['anh_dai_dien'] : 'https://via.placeholder.com/500';
?>

<div class="container mt-5 mb-5">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="http://localhost/baitaplon/Home">Trang chủ</a></li>
            <li class="breadcrumb-item">
                <a href="/baitaplon/?danhmuc=<?php echo $p['id_danhmuc']; ?>">
                    <?php echo htmlspecialchars($p['ten_danhmuc']); ?>
                </a>
            </li>
            <li class="breadcrumb-item active" aria-current="page"><?php echo htmlspecialchars($p['ten_sanpham']); ?></li>
        </ol>
    </nav>

    <div class="row">
        <div class="col-md-6 mb-4">
            <div class="card border-0">
                <div class="main-image-box mb-3 text-center border rounded p-2" style="background: #f8f9fa;">
                   <img id="mainImage" src="/baitaplon/<?php echo htmlspecialchars($mainImg); ?>"
                         class="img-fluid" style="max-height: 400px; object-fit: contain;" alt="Ảnh sản phẩm">
                </div>

                <div class="d-flex overflow-auto gap-2">
                    <img src="<?php echo htmlspecialchars($mainImg); ?>" 
                         class="img-thumbnail thumb-img active" 
                         style="width: 80px; height: 80px; object-fit: cover; cursor: pointer;" 
                         onclick="changeImage(this)">
                    
                    <?php foreach ($imgs as $img): ?>
                       <img src="/baitaplon/<?php echo htmlspecialchars($img['url_anh']); ?>"
                             class="img-thumbnail thumb-img" 
                             style="width: 80px; height: 80px; object-fit: cover; cursor: pointer;" 
                             onclick="changeImage(this)">
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <h2 class="fw-bold mb-3"><?php echo htmlspecialchars($p['ten_sanpham']); ?></h2>
            
            <p class="text-muted">
                <i class="bi bi-clock"></i> Ngày đăng: <?php echo date('d/m/Y', strtotime($p['ngaydang'])); ?>
                <span class="mx-2">|</span>
                <i class="bi bi-eye"></i> Lượt xem: <?php echo $p['luot_xem']; ?>
            </p>

            <h1 class="text-danger fw-bold mb-4">
                <?php echo number_format($p['gia'], 0, ',', '.'); ?> đ
            </h1>

            <div class="card bg-light mb-4 border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <img src="<?php echo htmlspecialchars($mainAvatar); ?>" 
                             class="rounded-circle me-3" width="60" height="60" alt="Avatar người bán">
                        <div>
                        <?php
    // 1. Tạo link đến trang cá nhân
                            $viewerId = isset($data['user_id']) ? $data['user_id'] : '';
                            $sellerId = $p['id_user']; 
                            $profileLink = "/baitaplon/User/Profile/" . $sellerId;
                            
                            // Nếu đang đăng nhập, nối thêm ID người xem để giữ trạng thái đăng nhập
                            if (!empty($viewerId)) {
                                $profileLink .= "/" . $viewerId;
                            }
                        ?>

                        <h6 class="mb-0 fw-bold text-uppercase">
                            Người bán: 
                            <a href="<?php echo htmlspecialchars($profileLink); ?>" class="text-decoration-none text-dark hover-name">
                                <?php echo htmlspecialchars($p['hoten']); ?>
                            </a>
                        </h6>
                            <small class="text-muted"><i class="bi bi-geo-alt-fill"></i> <?php echo htmlspecialchars($p['khu_vuc_ban']); ?></small>
                        </div>
                    </div>
                    
                    <div class="d-grid gap-2">
                        <a href="tel:<?php echo htmlspecialchars($p['sdt']); ?>" class="btn btn-success fw-bold">
                            <i class="bi bi-telephone-fill"></i> GỌI NGAY: <?php echo htmlspecialchars($p['sdt']); ?>
                        </a>
                        <button class="btn btn-outline-primary">
                            <i class="bi bi-chat-dots-fill"></i> Chat với người bán
                        </button>
                    </div>
                </div>
            </div>

            <div class="alert alert-info">
                <strong><i class="bi bi-shield-check"></i> Lưu ý:</strong> Hãy kiểm tra kỹ sản phẩm trước khi giao dịch. Không chuyển khoản trước khi nhận hàng.
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-white fw-bold text-uppercase">
                    Mô tả chi tiết sản phẩm
                </div>
                <div class="card-body">
                    <div class="content-desc">
                        <?php 
                            // nl2br giúp chuyển dấu xuống dòng trong database thành thẻ <br>
                            echo nl2br(htmlspecialchars($p['mota'])); 
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function changeImage(element) {
        // 1. Đổi ảnh lớn thành đường dẫn của ảnh nhỏ vừa click
        document.getElementById('mainImage').src = element.src;

        // 2. Xóa class active ở tất cả ảnh nhỏ
        let thumbs = document.querySelectorAll('.thumb-img');
        thumbs.forEach(img => {
            img.classList.remove('active', 'border-primary');
        });

        // 3. Thêm class active cho ảnh vừa click
        element.classList.add('active', 'border-primary');
    }
</script>

<style>
    /* CSS bổ sung để làm đẹp */
    .thumb-img.active {
        border: 2px solid #0d6efd !important; /* Viền xanh khi được chọn */
        opacity: 0.7;
    }
    .thumb-img:hover {
        transform: scale(1.05);
        transition: 0.3s;
    }
    .hover-name:hover {
    color: #f59e0b !important; /* Màu cam Chợ Tốt */
    text-decoration: underline !important;
    cursor: pointer;
}
</style>