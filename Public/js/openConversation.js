// Mở conversation
function openConversation(conversationId) {
    window.location.href =
        'index.php?action=index&conversation_id=' + conversationId;
}

// Mở list option
document.addEventListener('click', function (e) {
    // đóng tất cả menu
    document.querySelectorAll('.message-menu').forEach(m => {
        m.style.display = 'none';
    });

    // mở menu khi click ⋯
    const actions = e.target.closest('.message-actions');
    if (actions) {
        const menu = actions.querySelector('.message-menu');
        if (menu) {
            menu.style.display = 'block';
        }
        e.stopPropagation();
    }
});

function editMessage(id) {
    // 1️⃣ Tìm message-content theo id
    const msgEl = document.querySelector(
        '.message-content[data-id="' + id + '"]'
    );

    if (!msgEl) return;

    // 2️⃣ Lấy nội dung text hiện tại
    const content = msgEl.innerText.trim();

    // 3️⃣ Đẩy nội dung xuống ô nhập liệu
    const input = document.getElementById('chatInput');
    input.value = content;
    input.focus();

    // 4️⃣ (QUAN TRỌNG) Đổi đường dẫn form để gửi về hàm edit_msg trong Controller
    const form = document.getElementById('chatForm'); // Đảm bảo form có id="chatForm"
    if (form) {
        form.action = '/baitaplon/Chat/edit_msg';
    }

    // 5️⃣ Gán ID tin nhắn vào input ẩn (nếu chưa có input này thì phải tạo trong View)
    // Bạn cần đảm bảo trong view chat_view.php có dòng: <input type="hidden" name="message_id" id="editMessageId">
    const idInput = document.getElementById('editMessageId');
    if (idInput) {
        idInput.value = id;
    }
    
    // Thay đổi nút gửi thành icon "Sửa" hoặc đổi màu để người dùng biết (Tuỳ chọn)
    const btn = form.querySelector('button');
    if(btn) {
        btn.innerHTML = '✎'; // Biểu tượng bút chì
        btn.style.backgroundColor = '#28a745'; // Màu xanh lá
    }
}

    function deleteMessage(id) {
        if (!confirm('Xóa tin nhắn này?')) return;

        const form = document.createElement('form');
        form.method = 'post';
        // Sửa đường dẫn chuẩn MVC:
        form.action = '/baitaplon/Chat/delete_msg'; 

        form.innerHTML = `
            <input type="hidden" name="message_id" value="${id}">
        `;

        document.body.appendChild(form);
        form.submit();
    }

