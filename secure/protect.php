<?php
session_start();
include 'db.php';

function getUserIP() {
    if (!empty($_SERVER['HTTP_CLIENT_IP']))
        return $_SERVER['HTTP_CLIENT_IP'];
    elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))
        return $_SERVER['HTTP_X_FORWARDED_FOR'];
    else
        return $_SERVER['REMOTE_ADDR'];
}

$ip = getUserIP();

// Проверяем, заблокирован ли IP
$stmt = $pdo->prepare("SELECT * FROM blocked_ips WHERE ip_address = :ip AND blocked_until > NOW()");
$stmt->execute(['ip' => $ip]);
$blocked = $stmt->fetch();

if ($blocked) {
    // IP заблокирован
    header('HTTP/1.0 403 Forbidden');
    echo "Your IP has been temporarily blocked due to suspicious activity. " . $blocked['blocked_until'];
    exit();
}

// Параметры защиты
$time_window = 10; // временное окно в секундах
$max_requests = 50; // максимальное количество запросов за временное окно
$block_duration = 360; // длительность блокировки в секундах, 6 мин

// Регистрируем текущий запрос
$stmt = $pdo->prepare("INSERT INTO request_logs (ip_address, request_time) VALUES (:ip, NOW())");
$stmt->execute(['ip' => $ip]);

// Считаем количество запросов за последнее временное окно
$stmt = $pdo->prepare("SELECT COUNT(*) FROM request_logs WHERE ip_address = :ip AND request_time > (NOW() - INTERVAL :window SECOND)");
$stmt->execute(['ip' => $ip, 'window' => $time_window]);
$request_count = $stmt->fetchColumn();

if ($request_count > $max_requests) {
    // Блокируем IP
    $blocked_until = date('Y-m-d H:i:s', time() + $block_duration);
    try {
        $stmt = $pdo->prepare("INSERT INTO blocked_ips (ip_address, blocked_until) VALUES (:ip, :blocked_until)
                               ON DUPLICATE KEY UPDATE blocked_until = :blocked_until");
        $stmt->execute(['ip' => $ip, 'blocked_until' => $blocked_until]);
    } catch (PDOException $e) {
        // Обработка ошибок, например, если IP уже заблокирован
    }

    $stmt = $pdo->prepare("DELETE FROM request_logs WHERE ip_address = :ip");
    $stmt->execute(['ip' => $ip]);

    header('HTTP/1.0 403 Forbidden');
    echo "Your IP has been temporarily blocked due to suspicious activity.";
    exit();
}

$stmt = $pdo->prepare("DELETE FROM request_logs WHERE request_time < (NOW() - INTERVAL :window SECOND)");
$stmt->execute(['window' => $time_window]);
?>