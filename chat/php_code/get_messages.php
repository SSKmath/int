<?php
function get_messages($data)
{
    //check_session();
    global $pdo;
    update_activity($data);
    session_start();

    if (!isset($_SESSION['isLogin']) || $_SESSION['isLogin'] !== true) {
        return json_encode(["message" => "unauthorized"]);
    }

    $currentUser = $_SESSION['login'];

    if (empty($currentUser)) {
        return json_encode(["message" => "invalid_input"]);
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