// Mở conversation
function openConversation(conversationId) {
    window.location.href =
        '/baitaplon/chat/start/' + conversationId;
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

       let isEditing = false;

        function editMessage(id) {
            // 1️⃣ Lấy element nội dung tin nhắn
            const msgEl = document.querySelector(
                '.message-content[data-id="' + id + '"]'
            );
            if (!msgEl) return;

            // 2️⃣ Lấy nội dung cũ
            const content = msgEl.innerText.trim();

            // 3️⃣ Đẩy nội dung xuống input chat
            const input = document.getElementById('chatInput');
            input.value = content;
            input.focus();

            // 4️⃣ Đánh dấu đang sửa (QUAN TRỌNG)
            document.getElementById('editMessageId').value = id;
            isEditing = true;

            // 5️⃣ Đổi placeholder cho rõ trạng thái
            input.placeholder = "Đang sửa tin nhắn...";
        }



    function deleteMessage(id) {
        if (!confirm('Xóa tin nhắn này?')) return;

        const form = document.createElement('form');
        form.method = 'post';

        // URL ĐẸP
        form.action = '/baitaplon/chat/deleteMessage';

        form.innerHTML = `
            <input type="hidden" name="message_id" value="${id}">
        `;

        document.body.appendChild(form);
        form.submit();
    }




