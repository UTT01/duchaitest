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

        // 1️⃣ tìm message-content theo id
        const msgEl = document.querySelector(
            '.message-content[data-id="' + id + '"]'
        );

        if (!msgEl) return;

        // 2️⃣ lấy nội dung text
        const content = msgEl.innerText.trim();

        // 3️⃣ đẩy xuống input
        const input = document.getElementById('chatInput');
        input.value = content;
        input.focus();

        // 4️⃣ set action = edit
        document.getElementById('chatAction').value = 'edit_msg';

        // 5️⃣ set message_id
        document.getElementById('editMessageId').value = id;
    }

    function deleteMessage(id) {
        if (!confirm('Xóa tin nhắn này?')) return;

        // tạo form ẩn để submit action delete_msg
        const form = document.createElement('form');
        form.method = 'post';
        form.action = 'index.php';

        form.innerHTML = `
            <input type="hidden" name="action" value="delete_msg">
            <input type="hidden" name="message_id" value="${id}">
        `;

        document.body.appendChild(form);
        form.submit();
    }

