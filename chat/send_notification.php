<?php
include 'chat/telegram_bot.php';
if (isset($argv[1]) && isset($argv[2])) {
    $recipient = $argv[1];
    $message = $argv[2];
    sendTelegramNotification($recipient, $message);
}
?>