<link rel="stylesheet" href="/baitaplon/Public/css/DialogVote.css">
<script src="/baitaplon/public/js/openDialogVote.js"></script>
<div class="modal-overlay" id="reviewModal">
    <div class="modal">

        <div class="modal-header">
            <h2>Đánh giá người dùng</h2>
            <button class="btn-close" onclick="closeReview()">×</button>
        </div>

        <div class="modal-body">
            
            <input type="hidden" id="voteTargetId" value="<?= htmlspecialchars($target_id ?? '') ?>">
            
            <input type="hidden" id="voteRating" value="0">

            <div class="row">
                <div class="form-group">
                    <label>Người dùng</label>
                    <input type="text" value="<?= htmlspecialchars($target_name ?? 'Người dùng') ?>" disabled>
                </div>
            </div>

            <div class="form-group">
                <label>Đánh giá chất lượng *</label>
                <div class="star-rating">
                    <span data-star="1">★</span>
                    <span data-star="2">★</span>
                    <span data-star="3">★</span>
                    <span data-star="4">★</span>
                    <span data-star="5">★</span>
                </div>
                <small id="starError" style="color: red; display: none; margin-top: 5px;">Vui lòng chọn số sao</small>
            </div>

            <div class="form-group">
                <label>Nhận xét</label>
                <textarea id="voteComment" placeholder="Nhận xét về thái độ, độ uy tín của người này..."></textarea>
            </div>

        </div>

        <div class="modal-footer">
            <button class="btn-secondary" onclick="closeReview()">Hủy</button>
            <button class="btn-primary" onclick="submitVote()">Gửi đánh giá →</button>
        </div>

    </div>
</div>