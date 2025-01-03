<?php
function view_friend_requests($data)
{
    check_session();
    global $pdo;
    session_start();
    $user_id = $_SESSION['user_id'];
    
    $sql = "SELECT users.login FROM friends JOIN users ON friends.user_id = users.id WHERE friends.friend_id = :current_id AND friends.status = 'pending'";

    // Подготовка и выполнение запроса
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':current_id', $user_id, PDO::PARAM_INT);
    $stmt->execute();

    $decrypted_logins = [];

    // Обработка результатов запроса
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $decrypted_logins[] = viginerDecipher($row['login']);
    }

    return json_encode(["message" => "success", "data" => $decrypted_logins]);
}
?>