<?php
function accept_friend_request($data)
{
    check_session();
    global $pdo;
    session_start();
    $user_id = $_SESSION['user_id'];
    $sender = viginerCipher($data['login']);

    $stmt = $pdo->prepare("SELECT id FROM users WHERE login = :login");
    $stmt->bindParam(':login', $sender);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$result)
        return json_encode(["message" => "friend_not_found"]);

    $sender_id = $result['id'];

    // Подготовка SQL-запроса для обновления статуса
    $sql = "UPDATE friends SET status = :status WHERE user_id = :user_id AND friend_id = :friend_id";
    // Подготовка запроса
    $stmt = $pdo->prepare($sql);
    // Привязка параметров
    $status = 'accepted';
    $stmt->bindParam(':status', $status);
    $stmt->bindParam(':user_id', $sender_id);
    $stmt->bindParam(':friend_id', $user_id);
    // Выполнение запроса
    $stmt->execute();

    // Проверка количества затронутых строк
    if ($stmt->rowCount() > 0) {
        return json_encode(["message" => "success"]);
    } else {
        return json_encode(["message" => "error"]);
    }
}

function decline_friend_request($data)
{
    check_session();
    global $pdo;
    session_start();
    $user_id = $_SESSION['user_id'];
    $sender = viginerCipher($data['login']);

    $stmt = $pdo->prepare("SELECT id FROM users WHERE login = :login");
    $stmt->bindParam(':login', $sender);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$result)
        return json_encode(["message" => "friend_not_found"]);

    $sender_id = $result['id'];

    // Подготовка SQL-запроса для удаления записи
    $sql = "DELETE FROM friends WHERE user_id = :user_id AND friend_id = :friend_id";
    // Подготовка запроса
    $stmt = $pdo->prepare($sql);
    // Привязка параметров
    $stmt->bindParam(':user_id', $sender_id, PDO::PARAM_INT);
    $stmt->bindParam(':friend_id', $user_id, PDO::PARAM_INT);
    
    // Выполнение запроса
    if ($stmt->execute()) {
        return json_encode(["message" => "success"]);
    } else {
        return json_encode(["message" => "error"]);
    }
}

function get_friends($data)
{
    check_session();
    global $pdo;
    
    // Проверка на существование пользовательской сессии
    session_start();
    if (!isset($_SESSION['user_id'])) {
        return json_encode(["message" => "error", "data" => "User not logged in"]);
    }
    
    $current_id = $_SESSION['user_id'];
    $friendsLogins = [];

    // SQL-запрос для получения логинов друзей
    $sql = "
        SELECT u.login 
        FROM friends f
        JOIN users u ON f.friend_id = u.id 
        WHERE f.user_id = :current_id AND f.status = 'accepted' AND f.user_id != f.friend_id
        
        UNION ALL
        
        SELECT u.login 
        FROM friends f
        JOIN users u ON f.user_id = u.id 
        WHERE f.friend_id = :current_id AND f.status = 'accepted' AND f.user_id != f.friend_id";
    
    try {
        // Подготовка и выполнение запроса
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':current_id' => $current_id]);

        // Получение результатов и заполнение массива логинов
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $friendsLogins[] = viginerDecipher($row['login']);
        }

        return json_encode(["message" => "success", "data" => $friendsLogins]);
        
    } catch (PDOException $e) {
        return json_encode(["message" => "error", "data" => $e->getMessage()]);
    }
}

function delete_friend($data)
{
    check_session();
    global $pdo;
    session_start();
    $user_id = $_SESSION['user_id'];

    $friend_login = viginerCipher($data["friend"]);

    $stmt = $pdo->prepare("SELECT id FROM users WHERE login = :login");
    $stmt->bindParam(':login', $friend_login);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$result)
        return json_encode(["message" => "friend_not_found"]);

    $friend_id = $result['id'];

    $sql = "DELETE FROM friends WHERE (user_id = :user_id AND friend_id = :friend_id) OR (user_id = :friend_id AND friend_id = :user_id)";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->bindParam(':friend_id', $friend_id, PDO::PARAM_INT);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        return json_encode(["message" => "success"]);
    } else {
        return json_encode(["message" => "bad", "a" => $user_id, "b" => $friend_id]);
    }
}
?>