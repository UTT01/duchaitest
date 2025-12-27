<?php
// app/views/Page/profile.php

$u = isset($data['profile']) ? $data['profile'] : null;
$products = isset($data['products']) ? $data['products'] : [];
$isOwner = isset($data['isOwner']) ? $data['isOwner'] : false;
$loggedInId = isset($data['user_id']) ? $data['user_id'] : '';

// Xử lý hiển thị Avatar
$avatar = (!empty($u['avatar'])) ? "/baitaplon/" . $u['avatar'] : "https://via.placeholder.com/150";

if (!$u) {
    echo '<div class="alert alert-warning">Người dùng không tồn tại.</div>';
    return;
}
?>

<div class="container py-4">
    <div class="row">
        <div class="col-md-4">
            <div class="card shadow-sm border-0 mb-4 text-center p-3">
                <div class="d-flex justify-content-center">
                    <img src="<?php echo htmlspecialchars($avatar); ?>" 
                         class="rounded-circle img-thumbnail" 
                         style="width: 150px; height: 150px; object-fit: cover;" alt="Avatar">
                </div>
                
                <div class="card-body">
                    <h4 class="fw-bold mb-1"><?php echo htmlspecialchars($u['hoten']); ?></h4>
                    <p class="text-muted mb-2">
                        <i class="bi bi-star-fill text-warning"></i> 
                        <?php echo number_format($u['danhgia'], 1); ?> / 5.0
                    </p>
                    
                    <div class="text-start mt-4">
                        <p><i class="bi bi-telephone me-2 text-primary"></i> <?php echo htmlspecialchars($u['sdt']); ?></p>
                        <p><i class="bi bi-geo-alt me-2 text-danger"></i> <?php echo htmlspecialchars($u['diachi']); ?></p>
                        <hr>
                        <strong>Giới thiệu:</strong>
                        <p class="text-muted small mt-2">
                            <?php echo nl2br(htmlspecialchars($u['gioithieu'])); ?>
                        </p>
                    </div>

                    <?php if ($isOwner): ?>
                        <button type="button" class="btn btn-primary w-100 mt-3" data-bs-toggle="modal" data-bs-target="#editProfileModal">
                            <i class="bi bi-pencil-square"></i> Chỉnh sửa trang cá nhân
                        </button>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <h5 class="mb-3 fw-bold border-bottom pb-2">
                Tin đang rao bán (<?php echo count($products); ?>)
            </h5>

            <?php if (empty($products)): ?>
                <div class="alert alert-info">Người dùng này chưa đăng sản phẩm nào.</div>
            <?php else: ?>
                <div class="row g-3">
                <?php foreach ($products as $p): ?>
    <div class="col-sm-6 col-lg-4">
        <div class="card h-100 product-card shadow-sm">
            <?php
            $img = isset($p['anh_hienthi']) ? "/baitaplon/" . $p['anh_hienthi'] : 'https://via.placeholder.com/300';
            
            // === SỬA LẠI ĐƯỜNG DẪN CHI TIẾT (QUAN TRỌNG) ===
            // Sử dụng format: /baitaplon/Home/detail_Sanpham/ID_SP/ID_NGUOI_XEM
            
            $detailLink = "/baitaplon/Home/detail_Sanpham/" . $p['id_sanpham'];
            
            // Nếu người xem đang đăng nhập ($loggedInId lấy từ đầu file profile.php), nối thêm vào link
            if (!empty($loggedInId)) {
                $detailLink .= "/" . urlencode($loggedInId);
            }
            ?>
            
            <img src="<?php echo htmlspecialchars($img); ?>" class="card-img-top" style="height: 150px; object-fit: cover;">
            <div class="card-body p-2 d-flex flex-column">
                <h6 class="card-title text-truncate"><?php echo htmlspecialchars($p['ten_sanpham']); ?></h6>
                <p class="text-danger fw-bold mb-1"><?php echo number_format($p['gia']); ?> đ</p>
                <p class="text-muted small mb-1"><?php echo htmlspecialchars($p['ngaydang']); ?></p>
                
                <a href="<?php echo htmlspecialchars($detailLink); ?>" class="btn btn-sm btn-outline-primary mt-auto stretched-link">Xem chi tiết</a>
            </div>
        </div>
    </div>
<?php endforeach; ?>    
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php if ($isOwner): ?>
<div class="modal fade" id="editProfileModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form action="/baitaplon/User/Update" method="POST" enctype="multipart/form-data">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Chỉnh sửa thông tin</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id_user" value="<?php echo htmlspecialchars($u['id_user']); ?>">

                    <div class="mb-3 text-center">
                        <img src="<?php echo htmlspecialchars($avatar); ?>" class="rounded-circle border mb-2" width="100" height="100" style="object-fit: cover;">
                        <br>
                        <label for="avatar_file" class="form-label small text-primary" style="cursor: pointer;">Thay đổi ảnh đại diện</label>
                        <input type="file" class="form-control form-control-sm" name="avatar_file" id="avatar_file" accept="image/*">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Họ và tên</label>
                        <input type="text" class="form-control" name="hoten" value="<?php echo htmlspecialchars($u['hoten']); ?>" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Số điện thoại</label>
                        <input type="text" class="form-control" name="sdt" value="<?php echo htmlspecialchars($u['sdt']); ?>">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Địa chỉ</label>
                        <input type="text" class="form-control" name="diachi" value="<?php echo htmlspecialchars($u['diachi']); ?>">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Giới thiệu bản thân</label>
                        <textarea class="form-control" name="gioithieu" rows="3"><?php echo htmlspecialchars($u['gioithieu']); ?></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-primary">Lưu thay đổi</button>
                </div>
            </div>
        </form>
    </div>
</div>
<?php endif; ?>