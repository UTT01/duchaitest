<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Báo cáo vi phạm</title>
    <link rel="stylesheet" href="/baitaplon/public/css/ReportForm.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>

<div class="report-wrapper">
    <div class="report-card">
        
        <div class="report-header">
            <div class="icon-warning">⚠️</div>
            <h2>Báo Cáo Vi Phạm</h2>
            <p>Vui lòng cung cấp thông tin chính xác để chúng tôi xử lý.</p>
        </div>

        <div class="target-profile">
            <div class="profile-avatar">
                <?= strtoupper(substr($target_name ?? 'U', 0, 1)) ?>
            </div>
            <div class="profile-info">
                <span class="label">Đối tượng báo cáo:</span>
                <h3 class="name"><?= htmlspecialchars($target_name ?? 'Người dùng') ?></h3>
                <span class="id">ID: #<?= htmlspecialchars($target_id ?? '---') ?></span>
            </div>
        </div>

        <form action="/baitaplon/Report/submit" method="POST" enctype="multipart/form-data" class="report-form">
            <input type="hidden" name="target_id" value="<?= htmlspecialchars($target_id ?? '') ?>">

            <div class="form-group">
                <label for="reason">Lý do báo cáo <span class="required">*</span></label>
                <div class="select-wrapper">
                    <select name="reason" id="reason" required>
                        <option value="" disabled selected>-- Chọn lý do vi phạm --</option>
                        <option value="Lừa đảo/Chiếm đoạt tài sản">💸 Lừa đảo / Chiếm đoạt tài sản</option>
                        <option value="Hàng giả/Hàng cấm">🚫 Bán hàng giả / Hàng cấm</option>
                        <option value="Quấy rối/Lời lẽ thô tục">🤬 Quấy rối / Lời lẽ thô tục</option>
                        <option value="Spam/Quảng cáo rác">📢 Spam / Quảng cáo rác</option>
                        <option value="Khác">📝 Lý do khác</option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label for="description">Mô tả chi tiết <span class="required">*</span></label>
                <textarea name="description" id="description" required placeholder="Mô tả rõ sự việc: Thời gian, số tiền đã chuyển, nội dung tin nhắn..."></textarea>
            </div>

            <div class="form-group">
                <label for="evidence">Bằng chứng (Ảnh chụp màn hình)</label>
                <div class="file-upload">
                    <input type="file" name="evidence" id="evidence" accept="image/*">
                    <small class="hint">Hỗ trợ định dạng JPG, PNG. Tối đa 5MB.</small>
                </div>
            </div>

            <div class="form-actions">
                <a href="javascript:history.back()" class="btn-cancel">Hủy bỏ</a>
                <button type="submit" class="btn-submit">Gửi Báo Cáo</button>
            </div>
        </form>

    </div>
</div>

</body>
</html>