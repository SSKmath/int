<?php
function removeTelegramChat($chat_id) 
{
    $botToken = '7602926953:AAG5HWnMfBQj04Rvsrq2WU5dRGmkJh8m8JU';
    $url = "https://api.telegram.org/bot$botToken/sendMessage";

    $data = [
        'chat_id' => $chat_id,
        'text' => "Уведомления отключены."
    ];

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    
    $response = curl_exec($ch);
    curl_close($ch);

    return $response;
}

function delete_account($data)
{
    check_session();
    global $pdo;
    session_start();

    try {
        // Проверяем является ли владельцем группы
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM groups WHERE owner_id = ?");
        $stmt->execute([$_SESSION["user_id"]]);
        $count = $stmt->fetchColumn();

        if ($count > 0) {
            return json_encode(["message" => "owner of group"]);
        }

        // Проверяем и удаляем уведомления
        $stmt = $pdo->prepare("SELECT telegram_chat_id FROM user_notifications WHERE site_login = ?");
        $stmt->execute([$_SESSION["login"]]);
        $notifications = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if ($notifications)
        {
            $stmt = $pdo->prepare("DELETE FROM user_notifications WHERE site_login = ?");
            $stmt->execute([$_SESSION["login"]]);

            foreach ($notifications as $notification) {
                $telegram_chat_id = $notification['telegram_chat_id'];
                removeTelegramChat($telegram_chat_id);
            }
        }

        // Удаляем из messages
        $stmt = $pdo->prepare("DELETE FROM messages WHERE sender = ? OR recipient = ?");
        $stmt->execute([$_SESSION["login"], $_SESSION["login"]]);

        // Удаляем из друзей
        $stmt = $pdo->prepare("DELETE FROM friends WHERE user_id = ? OR friend_id = ?");
        $stmt->execute([$_SESSION["user_id"], $_SESSION["user_id"]]);
        
        // Удаляем из sessions
        $stmt = $pdo->prepare("DELETE FROM sessions WHERE user_id = ?");
        $stmt->execute([$_SESSION["user_id"]]);

        // Удаляем из group_messages
        $stmt = $pdo->prepare("DELETE FROM group_messages WHERE user_id = ?");
        $stmt->execute([$_SESSION["user_id"]]);

        // Удаляем из group_members
        $stmt = $pdo->prepare("DELETE FROM group_members WHERE user_id = ?");
        $stmt->execute([$_SESSION["user_id"]]);

        // Удаляем из users
        $stmt = $pdo->prepare("DELETE FROM users WHERE login = ?");
        $loginCiphered = viginerCipher($_SESSION["login"]);
        $stmt->execute([$loginCiphered]);

        session_unset();
        session_destroy();
        return json_encode(["message" => "good"]);
    } catch (PDOException $e) {
        return json_encode(["message" => $e->getMessage()]);
    }
}
?>