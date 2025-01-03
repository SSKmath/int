<?php
function get_messages($data)
{
    global $pdo;
    update_activity($data);
    session_start();

    if (!isset($_SESSION["isLogin"]) || $_SESSION["isLogin"] !== true) {
        return json_encode(["success" => false, "message" => "unauthorized"]);
    }

    $currentUser = $_SESSION["login"];
    $startFromIdMessage = $data["startFromIdMessage"];

    if (empty($currentUser)) {
        return json_encode(["success" => false, "message" => "empty user login"]);
    }

    // Получение сообщений из базы данных
    $stmt = $pdo->prepare("SELECT id, sender, recipient, message, timestamp FROM messages WHERE (sender = :currentUser OR recipient = :currentUser) AND id >= :startFromIdMessage");
    $stmt->execute(["currentUser" => $currentUser, "startFromIdMessage" => $startFromIdMessage]);
    
    $messages = [];
    
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $sender = $row['sender'];
        $recipientRow = $row['recipient'];
        $message = viginerDecipher($row['message']);
        
        $message = str_replace("__NEWLINE__", "\n", $message);
        
        $messages[] = [
            "id" => $row["id"],
            "sender" => $sender,
            "recipient" => $recipientRow,
            "message" => $message,
            "timestamp" => $row['timestamp']
        ];
    }

    return json_encode(["success" => true, "data" => $messages]);
}
?>