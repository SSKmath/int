<?php
$groupId = $argv[1];
$senderId = $argv[2];

// Получение всех участников группы, кроме отправителя
$stmt = $pdo->prepare("SELECT user_id FROM group_members WHERE group_id = ? AND user_id != ?");
$stmt->execute([$groupId, $senderId]);
$members = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($members as $member) {
    // Получаем логин участника
    $stmt = $pdo->prepare("SELECT login FROM users WHERE id = ?");
    $stmt->execute([$member['user_id']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($user) {
        $login = viginerDecipher($user['login']);
        sendTelegramNotification($login, "Новое сообщение в группе.");
    }
}

exit(0);
?>