<?php
function save_sub($data)
{
    $host = 'localhost';
    $db = 'db10879p';
    $user = 'us10879i';
    $password = '*Siv;dp6-MZL-mvH-M6g!';
    $charset = 'utf8mb4';

    try {
        $pdo = new PDO("mysql:host=$host;dbname=$db;charset=$charset", $user, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch(PDOException $e) {
        die("Ошибка подключения: " . $e->getMessage());
    }

    $subscription = $data['subscription'];

    $endpoint = $subscription['endpoint'];
    $keys_p256dh = $subscription['keys']['p256dh'];
    $keys_auth = $subscription['keys']['auth'];

    $stmt = $pdo->prepare("INSERT INTO subscriptions (endpoint, keys_p256dh, keys_auth) VALUES (?, ?, ?)");
    if ($stmt->execute([$endpoint, $keys_p256dh, $keys_auth]))
        return json_encode(['success' => true]);
    else
        return json_encode(['success' => false]);
}
?>