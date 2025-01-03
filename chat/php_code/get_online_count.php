<?php
function get_online_count($data)
{
    check_session();
    global $pdo;

    // Определяем время, считаем активными пользователей, которые были активны в последние 1 минут
    $time_threshold = date("Y-m-d H:i:s", strtotime("-1 minutes"));

    $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE last_activity >= ?");
    $stmt->execute([$time_threshold]);
    $count = $stmt->fetchColumn();

    return json_encode(['online_count' => $count]);
}
?>