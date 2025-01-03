<?php
function send_message($data)
{
    check_session();
    global $pdo;
    session_start();

    if (!isset($_SESSION['isLogin']) || $_SESSION['isLogin'] !== true)
        return json_encode(["message" => "unauthorized"]);

    $sender = $_SESSION['login'];
    $recipient = $data['recipient'];
    $message = $data['message'];

    if (empty($sender) || empty($recipient) || empty($message))
        return json_encode(["message" => "invalid_input"]);
    
    try {
        // Подготовка сообщения
        //$escapedMessage = str_replace('"', '""', $message); // Экранирование специальных символов в сообщении
        $escapedMessage = str_replace("\n", "__NEWLINE__", $message); // Заменяем переносы строк на специальную последовательность
        $timestamp = date('Y-m-d H:i:s');

        $goodMessage = viginerCipher($escapedMessage);
        
        // Подготовка и выполнение запроса
        $stmt = $pdo->prepare("INSERT INTO messages (sender, recipient, message, timestamp) VALUES (?, ?, ?, ?)");
        $stmt->execute([$sender, $recipient, $goodMessage, $timestamp]);

        sendTelegramNotification($recipient, "Уведомление! на " . $recipient . " от " . $sender);
        
        return json_encode(["message" => "success"]);
    } catch (PDOException $e) {
        return json_encode(["message" => "database_error", "error" => $e->getMessage()]);
    }
}
?>