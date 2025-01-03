<?php
function get_messages($data)
{
    session_start();

    if (!isset($_SESSION['isLogin']) || $_SESSION['isLogin'] !== true) {
        return json_encode(["message" => "unauthorized"]);
    }

    $currentUser = $_SESSION['login'];

    if (empty($currentUser)) {
        return json_encode(["message" => "invalid_input"]);
    }

    // Подключение к базе данных
    $dsn = 'mysql:host=localhost;dbname=db10879p;charset=utf8mb4';
    $username = 'us10879i';
    $password = '*Siv;dp6-MZL-mvH-M6g!';

    try {
        $pdo = new PDO($dsn, $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
        return json_encode(["message" => "database_connection_error"]);
    }

    // Получение сообщений из базы данных
    $stmt = $pdo->prepare("SELECT sender, recipient, message, timestamp FROM messages WHERE sender = :currentUser OR recipient = :currentUser");
    $stmt->execute(['currentUser' => $currentUser]);
    
    $messages = [];
    
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $sender = $row['sender'];
        $recipientRow = $row['recipient'];
        $message = viginerDecipher($row['message']);
        
        $message = str_replace("__NEWLINE__", "\n", $message);
        
        $messages[] = [
            "sender" => $sender,
            "recipient" => $recipientRow,
            "message" => $message,
            "timestamp" => $row['timestamp']
        ];
    }

    return json_encode(["message" => "success", "data" => $messages]);
}
?>