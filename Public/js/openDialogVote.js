// ==========================================
// 1. HÀM MỞ DIALOG (Đánh giá User)
// ==========================================
function openVoteDialog(el) {
    // SỬA: Lấy ID đối tác (partner) thay vì transaction
    let partnerId = el.getAttribute('data-partner-id'); 
    
    if (partnerId) {
        partnerId = partnerId.trim(); 
    }

    console.log("Partner ID:", partnerId);

    if (!partnerId) {
        alert('Lỗi: Không xác định được người dùng cần đánh giá!');
        return;
    }

    // Xử lý modal cũ
    const oldModal = document.getElementById('reviewModal');
    if (oldModal) oldModal.remove();

    // Gọi Fetch
    const url = '/baitaplon/Vote/dialog/' + encodeURIComponent(partnerId);

    fetch(url)
        .then(response => {
            if (!response.ok) {
                return response.text().then(text => { throw new Error(text || response.statusText) });
            }
            return response.text();
        })
        .then(html => {
            if (html.includes("Lỗi:") || html.includes("Fatal error")) {
                alert("Lỗi từ Server: " + html);
                return;
            }

            document.body.insertAdjacentHTML('beforeend', html);
            document.body.style.overflow = 'hidden'; 
            
            if (typeof initStarRating === "function") {
                initStarRating();
            }
        })
        .catch(err => {
            console.error(err);
            alert("Không thể mở hộp thoại: " + err.message);
        });
}

// ==========================================
// 2. HÀM KHỞI TẠO SAO (Giữ nguyên)
// ==========================================
function initStarRating() {
    const stars = document.querySelectorAll('#reviewModal .star-rating span');
    const ratingInput = document.getElementById('voteRating');

    stars.forEach(star => {
        star.addEventListener('click', () => {
            const rating = star.dataset.star;
            ratingInput.value = rating;

            stars.forEach(s => {
                if (Number(s.dataset.star) <= Number(rating)) {
                    s.classList.add('active');
                } else {
                    s.classList.remove('active');
                }
            });
        });
    });
}

// ==========================================
// 3. HÀM GỬI ĐÁNH GIÁ (Cập nhật key gửi đi)
// ==========================================
function submitVote() {
    // SỬA: Lấy ID từ input ẩn mới (voteTargetId)
    const targetId = document.getElementById('voteTargetId').value;
    const rating = document.getElementById('voteRating').value;
    const comment = document.getElementById('voteComment').value;

    if (rating == 0) {
        document.getElementById('starError').style.display = 'block';
        return;
    }

    const formData = new FormData();
    // SỬA: Key gửi đi là 'target_id' để khớp với $_POST['target_id'] trong Controller
    formData.append('target_id', targetId);
    formData.append('rating', rating);
    formData.append('comment', comment);

    fetch('/baitaplon/Vote/submit', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            
            closeReview(); 

            // SỬA: Truyền targetId cho hàm gợi ý báo cáo
            if (parseInt(rating) <= 2) {
                setTimeout(() => {
                    showReportSuggestion(targetId);
                }, 300);
            } else {
                alert('Cảm ơn bạn đã đánh giá!');
            }

        } else {
            alert('Lỗi: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Có lỗi xảy ra khi kết nối server.');
    });
}

// ==========================================
// 4. HÀM HIỂN THỊ GỢI Ý BÁO CÁO
// ==========================================
function showReportSuggestion(targetId) {
    const choice = confirm(
        "⚠️ CẢNH BÁO TRẢI NGHIỆM KÉM \n\n" +
        "Chúng tôi nhận thấy bạn đánh giá thấp người dùng này.\n" +
        "Nếu bạn nghi ngờ họ LỪA ĐẢO hoặc VI PHẠM chính sách, hãy Báo cáo ngay.\n\n" +
        "Bạn có muốn chuyển sang trang Báo Cáo Vi Phạm không?"
    );

    if (choice) {
        // SỬA: Chuyển hướng kèm target_id (người bị báo cáo)
        window.location.href = '/baitaplon/Report/create?target_id=' + targetId;
    }
}

// ==========================================
// 5. HÀM ĐÓNG DIALOG (Giữ nguyên)
// ==========================================
function closeReview() {
    const modal = document.getElementById('reviewModal');
    if (modal) modal.remove();
    document.body.style.overflow = ''; 
}   