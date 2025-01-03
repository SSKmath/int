<?php
function send_message($data)
{
    session_start();

    if (!isset($_SESSION['isLogin']) || $_SESSION['isLogin'] !== true)
        return json_encode(["message" => "unauthorized"]);

    $sender = $_SESSION['login'];
    $recipient = $data['recipient'];
    $message = $data['message'];

    if (empty($sender) || empty($recipient) || empty($message))
        return json_encode(["message" => "invalid_input"]);
    
    $conn = new mysqli("localhost", "us10879i", "*Siv;dp6-MZL-mvH-M6g!", "db10879p");

    if ($conn->connect_error)
        return json_encode(["message" => "database_error"]);

    $escapedMessage = str_replace('"', '""', $message); // Экранирование специальных символов в сообщении
    $escapedMessage = str_replace("\n", "__NEWLINE__", $message); // Заменяем переносы строк на специальную последовательность
    $timestamp = date('Y-m-d H:i:s');

    $goodMessage = viginerCipher($escapedMessage);
    
    $stmt = $conn->prepare("INSERT INTO messages (sender, recipient, message, timestamp) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $sender, $recipient, $goodMessage, $timestamp);

    if ($stmt->execute()) {
        sendTelegramNotification($recipient, "Уведомление! на " . $recipient . " от " . $sender);
        $stmt->close();
        $conn->close();
        return json_encode(["message" => "success"]);
    } else {
        $stmt->close();
        $conn->close();
        return json_encode(["message" => "error"]);
    }
}
?>