<?php
function send_friend_request($data)
{
    check_session();
    global $pdo;
    session_start();
    
    $user_id = $_SESSION['user_id'];
    $friend_login = viginerCipher($data['friend_login']);

    // Получаем ID друга по логину
    $stmt = $pdo->prepare("SELECT id FROM users WHERE login = :login");
    $stmt->bindParam(':login', $friend_login);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$result)
        return json_encode(["message" => "friend_not_found"]);

    $friend_id = $result['id'];

    // Проверяем, существует ли уже запрос в друзья
    $stmt = $pdo->prepare("SELECT * FROM friends WHERE (user_id = :user_id AND friend_id = :friend_id) OR (user_id = :friend_id AND friend_id = :user_id)");
    $stmt->bindParam(':user_id', $user_id);
    $stmt->bindParam(':friend_id', $friend_id);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        return json_encode(["message" => "request_already_sent"]);
    }

    // Если запрос не существует, добавляем новый
    try {
        $stmt = $pdo->prepare("INSERT INTO friends (user_id, friend_id, status) VALUES (?, ?, 'pending')");
        $stmt->execute([$user_id, $friend_id]);
        
        sendTelegramNotification($data['friend_login'], "Вас приглашают в друзья!");

        return json_encode(["message" => "success"]);
    } catch (PDOException $e) {
        return json_encode(["message" => "database_error", "error" => $e->getMessage()]);
    }
}   
?>