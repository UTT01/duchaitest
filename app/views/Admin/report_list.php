<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý Báo Cáo Vi Phạm</title>
    
    <link rel="stylesheet" href="/baitaplon/public/css/AdminReport.css">
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
</head>
<body>

<div class="admin-wrapper">
    
    <div class="card">
        <div class="card-header">
            <div class="header-title">
                <i class="fa-solid fa-triangle-exclamation text-danger"></i>
                <h2>Danh Sách Báo Cáo Vi Phạm</h2>
            </div>
            
            <div class="header-actions">
                <button onclick="exportToExcel()" class="btn-excel">
                    <i class="fa-solid fa-file-csv"></i> Xuất Excel
                </button>

                <span class="badge-count"><?= !empty($reports) ? count($reports) : 0 ?> đơn</span>
            </div>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                
                <table class="table" id="reportTable">
                    <thead>
                        <tr>
                            <th width="5%">ID</th>
                            <th width="20%">Người Tố Cáo</th>
                            <th width="20%">Người Bị Tố Cáo</th>
                            <th width="25%">Lý Do & Mô Tả</th>
                            <th width="10%">Bằng Chứng</th>
                            <th width="10%">Trạng Thái</th>
                            <th width="10%" class="text-right">Hành Động</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($reports)): ?>
                            <?php foreach ($reports as $r): ?>
                                <tr>
                                    <td><span class="id-hash">#<?= $r['id_report'] ?></span></td>
                                    
                                    <td>
                                        <div class="user-cell">
                                            <div class="avatar-circle bg-blue">
                                                <?= strtoupper(substr($r['reporter_name'] ?? 'U', 0, 1)) ?>
                                            </div>
                                            <div class="user-info">
                                                <div class="name"><?= htmlspecialchars($r['reporter_name'] ?? 'Unknown') ?></div>
                                                <div class="sub-id">ID: <?= $r['reporter_id'] ?></div>
                                            </div>
                                        </div>
                                    </td>

                                    <td>
                                        <div class="user-cell">
                                            <div class="avatar-circle bg-red">
                                                <?= strtoupper(substr($r['reported_name'] ?? 'U', 0, 1)) ?>
                                            </div>
                                            <div class="user-info">
                                                <div class="name text-danger"><?= htmlspecialchars($r['reported_name'] ?? 'Unknown') ?></div>
                                                <div class="sub-id">ID: <?= $r['reported_id'] ?></div>
                                            </div>
                                        </div>
                                    </td>

                                    <td>
                                        <div class="reason-cell">
                                            <span class="reason-tag"><?= htmlspecialchars($r['reason']) ?></span>
                                            <p class="description" title="<?= htmlspecialchars($r['description']) ?>">
                                                <?= htmlspecialchars(mb_strimwidth($r['description'], 0, 60, "...")) ?>
                                            </p>
                                        </div>
                                    </td>

                                    <td>
                                        <?php if (!empty($r['evidence_image'])): ?>
                                            <a href="/baitaplon/<?= $r['evidence_image'] ?>" target="_blank" class="evidence-link">
                                                <img src="/baitaplon/<?= $r['evidence_image'] ?>" alt="Evidence">
                                                <span class="zoom-icon"><i class="fa-solid fa-magnifying-glass"></i></span>
                                            </a>
                                        <?php else: ?>
                                            <span class="no-evidence">Không có</span>
                                        <?php endif; ?>
                                    </td>

                                    <td>
                                        <?php 
                                            $statusClass = '';
                                            $statusLabel = '';
                                            switch($r['status']) {
                                                case 'PENDING': $statusClass = 'pending'; $statusLabel = 'Chờ xử lý'; break;
                                                case 'PROCESSED': $statusClass = 'processed'; $statusLabel = 'Đã xử lý'; break;
                                                default: $statusClass = 'rejected'; $statusLabel = 'Đã hủy'; break;
                                            }
                                        ?>
                                        <span class="status-badge status-<?= $statusClass ?>">
                                            <?= $statusLabel ?>
                                        </span>
                                    </td>

                                    <td class="text-right">
                                        <?php if ($r['status'] == 'PENDING'): ?>
                                            <form method="POST" action="/baitaplon/AdminReport/process" class="action-buttons">
                                                <input type="hidden" name="report_id" value="<?= $r['id_report'] ?>">
                                                <input type="hidden" name="reported_id" value="<?= $r['reported_id'] ?>">
                                                
                                                <button type="submit" name="action" value="BAN_USER" class="btn-icon btn-ban" title="Khóa tài khoản" onclick="return confirm('⚠️ CẢNH BÁO: Bạn có chắc chắn muốn KHÓA vĩnh viễn tài khoản này?')">
                                                    <i class="fa-solid fa-gavel"></i>
                                                </button>
                                                
                                                <button type="submit" name="action" value="IGNORE" class="btn-icon btn-ignore" title="Bỏ qua báo cáo">
                                                    <i class="fa-solid fa-xmark"></i>
                                                </button>
                                            </form>
                                        <?php else: ?>
                                            <i class="fa-solid fa-check-circle text-success" title="Hoàn tất"></i>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="empty-state">
                                    <img src="https://cdn-icons-png.flaticon.com/512/7486/7486754.png" width="60" alt="Empty">
                                    <p>Hiện tại không có báo cáo nào cần xử lý.</p>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script src="/baitaplon/public/js/exportExcel.js"></script>

</body>
</html>