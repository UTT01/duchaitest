<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Chat Realtime</title>
    <link rel="stylesheet" href="public/css/chat.css">
</head>
<body>

<div class="chat-container">

    <!-- ================= LEFT (30%) ================= -->
<div class="chat-list">

    <!-- 🔍 SEARCH -->
        <form method="post" action="index.php?action=search">
            <div class="chat-search">
                <input 
                    type="text" 
                    name="keyword" 
                    autocomplete="off"
                    placeholder="🔍 Tìm kiếm cuộc trò chuyện"
                    value="<?= htmlspecialchars($_POST['keyword'] ?? '') ?>"
                >
            </div>
        </form>

        <!-- 📩 DANH SÁCH CUỘC TRÒ CHUYỆN -->
        <div class="chat-users">

            <?php if (!empty($conversations)): ?>
                <?php foreach ($conversations as $c): ?>

                    <div class="chat-user <?= ($c['id_conversation'] == ($active_conversation_id ?? 0)) ? 'active' : '' ?>"
                        onclick="window.location.href='index.php?action=index&conversation_id=<?= $c['id_conversation'] ?>'">

                        <div class="avatar">
                            <?= strtoupper(substr($c['username'], 0, 1)) ?>
                        </div>

                        <div class="chat-user-info">
                            <div class="username">
                                <?= htmlspecialchars($c['username']) ?>
                            </div>

                            <div class="last-message">
                                <?= htmlspecialchars($c['last_message'] ?? 'Chưa có tin nhắn') ?>
                            </div>
                        </div>

                        <div class="chat-time">
                            <?= isset($c['last_message_at']) 
                                ? formatChatTime($c['last_message_at']) 
                                : '' ?>
                        </div>
                    </div>

                <?php endforeach; ?>

            <?php else: ?>
                <div class="chat-empty">
                    Không tìm thấy cuộc trò chuyện
                </div>
            <?php endif; ?>

        </div>
    </div>


    <!-- ================= CENTER (50%) ================= -->
    <div class="chat-main">
            <div class="chat-header">
        
                <div class="chat-header-left">
                    <div class="chat-header-avatar">
                        <?= strtoupper(substr($sender_name, 0, 1)) ?>
                    </div>

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
                    $showTime = false;

                    if ($prevTime === null || ($currentTime - $prevTime) >= 300) {
                        $showTime = true;
                    }
                    $prevTime = $currentTime;

                    $isMine = ($msg['sender_id'] == $my_id);
                ?>

                <div class="message <?= $isMine ? 'message-right' : 'message-left' ?>">

                    <?php if (!$isMine): ?>
                        <!-- 👤 AVATAR ĐỐI PHƯƠNG -->
                        <div class="message-avatar">
                            <?= strtoupper(substr($sender_name, 0, 1)) ?>
                        </div>
                    <?php endif; ?>

                    <div class="message-body">

                        <?php if ($isMine): ?>
                            <!-- ⋯ NÚT HÀNH ĐỘNG -->
                            <div class="message-actions">
                                ⋯
                                <ul class="message-menu">
                                    <li onclick="editMessage(<?= $msg['id_message'] ?>)">Sửa</li>
                                    <li onclick="deleteMessage(<?= $msg['id_message'] ?>)">Xóa</li>
                                </ul>
                            </div>
                        <?php endif; ?>

                        <div class="message-content"
                            data-id="<?= $msg['id_message'] ?>">
                            <?= htmlspecialchars($msg['content']) ?>
                        </div>


                        <?php if ($showTime): ?>
                            <div class="message-time">
                                <?= date('H:i', $currentTime) ?>
                            </div>
                        <?php endif; ?>

                    </div>


                </div>

            <?php endforeach; ?>

        <?php else: ?>
            <div class="message-empty">Chưa có tin nhắn</div>
        <?php endif; ?>
        </div>


        <!-- ✅ FORM CHỈ NẰM Ở INPUT -->
        <form class="chat-input" method="post" action="/baitaplon/Chat/send">
            <?php 
            // Logic lấy ID người nhận từ active conversation
            $receiver_id = 0;
            if($active_conversation_id > 0) {
                // Sử dụng model hoặc biến đã truyền từ controller
                // Ở đây mình giả định controller đã xử lý hoặc lấy lại từ message cũ
                // Cách nhanh nhất: ChatModel::getOtherUserId cần được gọi ở View hoặc truyền xuống
                // Để đơn giản, Controller nên truyền biến $receiver_id xuống data
            }
            ?>
            <input type="hidden" name="to_user_id" value="<?php echo isset($sender_id) ? $sender_id : ''; ?>"> 

            <input type="text" name="message" placeholder="Nhập tin nhắn..." autocomplete="off">
            <button type="submit">➤</button>
        </form>



    </div>

    <!-- ================= RIGHT (20%) ================= -->
            <div class="chat-info">
                
                <div class="avatar-large">
                    <?= strtoupper(substr($sender_name, 0, 1)) ?>
                </div>

                <h4><?= htmlspecialchars($sender_name) ?></h4>
                
                <div class="user-status">● Đang hoạt động</div>

                <ul class="chat-info-list">
                    <li>Xem trang cá nhân</li>
                    <li>Tìm kiếm trong tin nhắn</li>
                    <li>File phương tiện & file</li>
                    <li style="color: red;">Chặn người dùng</li>
                </ul>

            </div>

</div>
<script src="public/js/openConversation.js"></script>

</body>
</html>
