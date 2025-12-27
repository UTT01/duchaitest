<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Chat Realtime</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="/baitaplon/public/css/chat.css">
    <style>
        .btn-back-home { margin: 15px 15px 0 15px; border-radius: 20px; font-weight: 600; }
    </style>
</head>
<body>

<div class="chat-container">

    <div class="chat-list">

        <a href="/baitaplon/Home/index/<?= htmlspecialchars($my_id) ?>" class="btn btn-outline-secondary btn-back-home">
            <i class="bi bi-arrow-left-circle-fill"></i> Quay lại Trang chủ
        </a>

        <form method="post" action="/baitaplon/Chat/search">
            <div class="chat-search">
                <input type="text" name="keyword" autocomplete="off" placeholder="🔍 Tìm kiếm..." value="<?= htmlspecialchars($_POST['keyword'] ?? '') ?>">
            </div>
        </form>

        <div class="chat-users">
            <?php if (!empty($conversations)): ?>
                <?php foreach ($conversations as $c): ?>
                    
                    <div class="chat-user <?= ($c['id_conversation'] == ($active_conversation_id ?? 0)) ? 'active' : '' ?>"
                        onclick="window.location.href='/baitaplon/Chat/index/<?= $c['id_user'] ?>/<?= $my_id ?>'">

                        <div class="avatar"><?= strtoupper(substr($c['username'], 0, 1)) ?></div>

                        <div class="chat-user-info">
                            <div class="username"><?= htmlspecialchars($c['username']) ?></div>
                            <div class="last-message"><?= htmlspecialchars($c['last_message'] ?? 'Chưa có tin nhắn') ?></div>
                        </div>

                        <div class="chat-time">
                            <?= isset($c['last_message_at']) ? formatChatTime($c['last_message_at']) : '' ?>
                        </div>
                    </div>

                <?php endforeach; ?>
            <?php else: ?>
                <div class="chat-empty" style="text-align:center; padding:20px; color:#888;">Chưa có cuộc trò chuyện nào</div>
            <?php endif; ?>
        </div>
    </div>

    <div class="chat-main">
        <?php if($active_conversation_id > 0): ?>
            <div class="chat-header">
                <div class="chat-header-left">
                    <div class="chat-header-avatar"><?= strtoupper(substr($sender_name, 0, 1)) ?></div>
                    <div class="chat-title">
                        <?= htmlspecialchars($sender_name) ?>
                        <span class="chat-status-sub">● Đang hoạt động</span>
                    </div>
                </div>
            </div>

            <div class="chat-messages">
                <?php if (!empty($messages)): ?>
                    <?php $prevTime = null; ?>
                    <?php foreach ($messages as $msg): ?>
                        <?php
                            $currentTime = strtotime($msg['created_at']);
                            $showTime = ($prevTime === null || ($currentTime - $prevTime) >= 300);
                            $prevTime = $currentTime;
                            $isMine = ($msg['sender_id'] == $my_id);
                        ?>
                        <div class="message <?= $isMine ? 'message-right' : 'message-left' ?>">
                            <?php if (!$isMine): ?><div class="message-avatar"><?= strtoupper(substr($sender_name, 0, 1)) ?></div><?php endif; ?>
                            <div class="message-body">
                                <?php if ($isMine): ?>
                                    <div class="message-actions">⋯
                                        <ul class="message-menu">
                                            <li onclick="editMessage(<?= $msg['id_message'] ?>)">Sửa</li>
                                            <li onclick="deleteMessage(<?= $msg['id_message'] ?>)">Xóa</li>
                                        </ul>
                                    </div>
                                <?php endif; ?>
                                <div class="message-content" data-id="<?= $msg['id_message'] ?>"><?= htmlspecialchars($msg['content']) ?></div>
                                <?php if ($showTime): ?><div class="message-time"><?= date('H:i', $currentTime) ?></div><?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="message-empty" style="text-align: center; margin-top: 20px;">Hãy gửi lời chào tới <?= htmlspecialchars($sender_name) ?></div>
                <?php endif; ?>
            </div>

            <form class="chat-input" method="post" action="/baitaplon/Chat/send" id="chatForm">
                <input type="hidden" name="to_user_id" value="<?= isset($sender_id) ? $sender_id : '' ?>"> 
                <input type="hidden" name="message_id" id="editMessageId">
                <input type="text" id="chatInput" name="message" placeholder="Nhập tin nhắn..." autocomplete="off" required>
                <button type="submit">➤</button>
            </form>

        <?php else: ?>
            <div class="d-flex flex-column align-items-center justify-content-center h-100 bg-white">
                <h4 class="mt-3 text-muted">Chọn một cuộc hội thoại để bắt đầu</h4>
            </div>
        <?php endif; ?>
    </div>

    <div class="chat-info">
        <?php if($active_conversation_id > 0): ?>
            <div class="avatar-large"><?= strtoupper(substr($sender_name, 0, 1)) ?></div>
            <h4><?= htmlspecialchars($sender_name) ?></h4>
            <ul class="chat-info-list">
                <li onclick="window.location.href='/baitaplon/User/Profile/<?= $sender_id ?>/<?= $my_id ?>'">Xem trang cá nhân</li>
                <li style="color: red;">Chặn người dùng</li>
            </ul>
        <?php endif; ?>
    </div>

</div>
<script src="/baitaplon/public/js/openConversation.js"></script>
</body>
</html>