<?php
function update_activity($data)
{
    //check_session();
    session_start();

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

    if (isset($_SESSION['login']))
    {
        $user_id = $_SESSION['user_id'];
        $stmt = $pdo->prepare("UPDATE users SET last_activity = NOW() WHERE id = ?");
        $stmt->execute([$user_id]);
    }
    
    if ($data["type"] === "updateActivity")
    return json_encode(["message" => "good"]);
}
?>