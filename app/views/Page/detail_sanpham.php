<?php
// app/views/Page/detail_sanpham.php

// Hứng dữ liệu từ Controller
$p = isset($data['product']) ? $data['product'] : null;
$imgs = isset($data['productImages']) ? $data['productImages'] : [];
$baseUrl = "/baitaplon";

// Lấy ID người xem (để xử lý link Home và link Chat)
$viewerId = isset($data['user_id']) ? $data['user_id'] : '';

// === [SỬA LẠI LINK HOME] ===
// Mặc định về trang chủ không đăng nhập
$homeLink = "/baitaplon/Home";
// Nếu đang đăng nhập thì thêm /index/MA_NV
if (!empty($viewerId)) {
    $homeLink .= "/index/" . urlencode($viewerId);
}
// ===========================

// Kiểm tra nếu không tìm thấy sản phẩm
if (!$p) {
    echo '<div class="alert alert-danger container mt-5">
            Sản phẩm không tồn tại! 
            <a href="' . htmlspecialchars($homeLink) . '">Quay lại trang chủ</a>
          </div>';
    return;
}

// Xử lý ảnh đại diện mặc định
$mainAvatar = !empty($p['avatar_user']) ? "/baitaplon/" . $p['avatar_user'] : 'https://via.placeholder.com/150';
$mainImg = !empty($p['anh_dai_dien']) ? "/baitaplon/" . $p['anh_dai_dien'] : 'https://via.placeholder.com/500';
?>

<div class="container mt-5 mb-5">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="<?php echo htmlspecialchars($homeLink); ?>">Trang chủ</a>
            </li>
            
            <li class="breadcrumb-item">
                <a href="/baitaplon/Home?danhmuc=<?php echo $p['id_danhmuc']; ?><?php echo !empty($viewerId) ? '&user_id='.$viewerId : ''; ?>">
                    <?php echo htmlspecialchars($p['ten_danhmuc']); ?>
                </a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">
                <?php echo htmlspecialchars($p['ten_sanpham']); ?>
            </li>
        </ol>
    </nav>

    <div class="row">
        <div class="col-md-6 mb-4">
            <div class="card border-0">
                <div class="main-image-box mb-3 text-center border rounded p-2" style="background: #f8f9fa;">
                   <img id="mainImage" src="<?php echo htmlspecialchars($mainImg); ?>"
                         class="img-fluid" style="max-height: 400px; object-fit: contain;" alt="Ảnh sản phẩm">
                </div>

                <div class="d-flex overflow-auto gap-2">
                    <?php foreach ($imgs as $index => $img): ?>
                        <img src="/baitaplon/<?php echo htmlspecialchars($img['url_anh']); ?>"
                            class="img-thumbnail thumb-img <?php echo ($index === 0) ? 'active' : ''; ?>" 
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
    <img src="<?php echo htmlspecialchars($mainAvatar); ?>" class="rounded-circle me-3" width="60" height="60" alt="Avatar">
    <div>
    <?php
        // === SỬA LOGIC LINK PROFILE ===
        // $data['user_id'] là ID của người đang xem (đang đăng nhập)
        $viewerId = isset($data['user_id']) ? $data['user_id'] : '';
        
        // $p['id_user'] là ID của người bán
        $sellerId = $p['id_user']; 
        
        // Format chuẩn: /baitaplon/User/Profile/ID_NGUOI_BAN/ID_NGUOI_XEM
        $profileLink = "/baitaplon/User/Profile/" . urlencode($sellerId);
        
        if (!empty($viewerId)) {
            $profileLink .= "/" . urlencode($viewerId);
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

                <?php 
                // Kiểm tra đăng nhập từ dữ liệu controller truyền sang
                $isLoggedIn = !empty($data['isLoggedIn']); 
                
                // Nếu đã đăng nhập -> Link vào Controller Chat/start
                // Nếu chưa đăng nhập -> Link vào Controller Login
                $chatLink = $isLoggedIn 
                    ? "/baitaplon/Chat/start/" . $p['id_user'] 
                    : "/baitaplon/Login";
                    
                $onclickInfo = $isLoggedIn ? "" : "return confirm('Bạn cần đăng nhập để bắt đầu trò chuyện. Chuyển đến trang đăng nhập?');";
                ?>

                <a href="<?php echo $chatLink; ?>" 
                class="btn btn-outline-primary"
                onclick="<?php echo $onclickInfo; ?>">
                    <i class="bi bi-chat-dots-fill"></i> Chat với người bán
                </a>
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
                        <?php echo nl2br(htmlspecialchars($p['mota'])); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function changeImage(element) {
        document.getElementById('mainImage').src = element.src;
        let thumbs = document.querySelectorAll('.thumb-img');
        thumbs.forEach(img => {
            img.classList.remove('active', 'border-primary');
        });
        element.classList.add('active', 'border-primary');
    }
</script>

<style>
    .thumb-img.active {
        border: 2px solid #0d6efd !important;
        opacity: 0.7;
    }
    .thumb-img:hover {
        transform: scale(1.05);
        transition: 0.3s;
    }
    .hover-name:hover {
        color: #f59e0b !important;
        text-decoration: underline !important;
        cursor: pointer;
    }
</style>