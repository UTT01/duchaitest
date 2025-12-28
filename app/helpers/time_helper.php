<?php
function formatChatTime($datetime) {
    if (empty($datetime)) return '';

    $time = strtotime($datetime);
    $today = strtotime(date('Y-m-d'));

    if ($time >= $today) {
        return date('H:i', $time);
    }
    return date('d/m', $time);
}
?>