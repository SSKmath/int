<?php
function create_group($data)
{
    global $pdo;
    session_start();

    // Проверяем, что пользователь авторизован
    if (!isset($_SESSION['user_id'])) {
        return json_encode(['success' => false, 'message' => 'User not authenticated']);
    }

    // Получаем данные из запроса
    $groupName = isset($data['groupName']) ? trim($data['groupName']) : '';
    $groupDescription = isset($data['groupDescription']) ? trim($data['groupDescription']) : '';
    $ownerId = $_SESSION['user_id']; // ID владельца группы из сессии

    if (empty($groupName))
        return json_encode(['success' => false, 'message' => 'Group name is required']);

    if (mb_strlen($groupName, 'UTF-8') > 32)
        return json_encode(['success' => false, 'message' => 'Very length']);

    if (mb_strlen($groupDescription, 'UTF-8') > 256)
        return json_encode(['success' => false, 'message' => 'Very length']);

    $groupName = str_replace("\n", "__NEWLINE__", $groupName);
    $groupDescription = str_replace("\n", "__NEWLINE__", $groupDescription);

    // Подготовка SQL-запроса для вставки данных в таблицу groups
    $stmt = $pdo->prepare("INSERT INTO groups (name, description, created_at, owner_id) VALUES (:name, :description, NOW(), :owner_id)");
    $groupName = viginerCipher($groupName);
    $stmt->bindParam(':name', $groupName);
    $groupDescription = viginerCipher($groupDescription);
    $stmt->bindParam(':description', $groupDescription);
    $stmt->bindParam(':owner_id', $ownerId);

    // Выполняем запрос и проверяем результат
    if ($stmt->execute()) {
        // Получаем ID созданной группы
        $groupId = $pdo->lastInsertId();

        // Добавляем создателя в таблицу group_members
        $stmt = $pdo->prepare("INSERT INTO group_members (group_id, user_id, joined_at, role) VALUES (:group_id, :user_id, NOW(), 'admin')");
        $stmt->bindParam(':group_id', $groupId);
        $stmt->bindParam(':user_id', $ownerId);
        
        if ($stmt->execute()) {
            return json_encode(['success' => true, 'message' => 'Group created successfully']);
        } else {
            return json_encode(['success' => false, 'message' => 'Failed to add owner to group']);
        }
    } else {
        return json_encode(['success' => false, 'message' => 'Failed to create group']);
    }
}

function get_my_groups($data)
{
    global $pdo;
    session_start();

    // Проверяем, что пользователь авторизован
    if (!isset($_SESSION['user_id'])) {
        return json_encode(['success' => false, 'message' => 'User not authenticated']);
    }

    $userId = $_SESSION['user_id'];

    // Подготовка SQL-запроса для получения групп, в которых состоит пользователь или является владельцем
    $stmt = $pdo->prepare("
        SELECT g.id AS group_id, g.name AS group_name, g.description AS group_description, g.created_at, g.owner_id
        FROM groups g
        LEFT JOIN group_members gm ON g.id = gm.group_id
        WHERE gm.user_id = :user_id OR g.owner_id = :user_id
        GROUP BY g.id
    ");
    $stmt->bindParam(':user_id', $userId);

    // Выполняем запрос
    if ($stmt->execute()) {
        $groups = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Проверяем, есть ли группы
        if (empty($groups)) {
            return json_encode(['success' => true, 'groups' => []]);
        }
        
        // Добавляем свойство is_owner к каждой группе
        foreach ($groups as &$group) {
            $group['group_name'] = viginerDecipher($group['group_name']);
            $group['group_description'] = viginerDecipher($group['group_description']);
            $group['is_owner'] = ($group['owner_id'] == $userId);
        }

        return json_encode(['success' => true, 'groups' => $groups]);
    } else {
        return json_encode(['success' => false, 'message' => 'Failed to retrieve groups']);
    }
}

function add_member_to_group($data)
{
    global $pdo;
    session_start();

    // Проверяем, что пользователь авторизован
    if (!isset($_SESSION['user_id'])) {
        return json_encode(['success' => false, 'message' => 'User not authenticated']);
    }

    $userId = $_SESSION['user_id'];
    $groupId = $data['groupId'];
    $username = $data['login'];

    // Проверяем, является ли текущий пользователь владельцем группы
    $stmt = $pdo->prepare("SELECT owner_id FROM groups WHERE id = :group_id");
    $stmt->bindParam(':group_id', $groupId);
    $stmt->execute();
    
    $group = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$group || $group['owner_id'] != $userId) {
        return json_encode(['success' => false, 'message' => 'User is not the owner of the group']);
    }

    // Получаем user_id нового участника по имени пользователя
    $chiphedUsername = viginerCipher($username);
    $stmt = $pdo->prepare("SELECT id FROM users WHERE login = :username");
    $stmt->bindParam(':username', $chiphedUsername);
    $stmt->execute();

    $userToAdd = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$userToAdd) {
        return json_encode(['success' => false, 'message' => 'User not found']);
    }

    // Проверяем, состоит ли пользователь уже в группе
    $stmt = $pdo->prepare("SELECT * FROM group_members WHERE group_id = :group_id AND user_id = :user_id");
    $stmt->bindParam(':group_id', $groupId);
    $stmt->bindParam(':user_id', $userToAdd['id']);
    $stmt->execute();
    
    if ($stmt->fetch(PDO::FETCH_ASSOC)) {
        return json_encode(['success' => false, 'message' => 'User is already a member of the group']);
    }

    // Добавляем нового участника в группу
    $stmt = $pdo->prepare("
        INSERT INTO group_members (group_id, user_id, joined_at, role) 
        VALUES (:group_id, :user_id, NOW(), 'member')
    ");
    $stmt->bindParam(':group_id', $groupId);
    $stmt->bindParam(':user_id', $userToAdd['id']);

    if ($stmt->execute()) {
        return json_encode(['success' => true, 'message' => 'User added to group successfully']);
    } else {
        return json_encode(['success' => false, 'message' => 'Failed to add user to group']);
    }
}

function send_to_group($data)
{
    global $pdo;
    session_start();

    if (!isset($_SESSION['isLogin']) || $_SESSION['isLogin'] !== true)
        return json_encode(["message" => "unauthorized"]);

    $senderId = $_SESSION['user_id'];
    $groupId = $data['groupId'];
    $message = $data['message'];

    if (mb_strlen($message, 'UTF-8') > 512)
        return json_encode(["message" => "error"]);

    if (empty($message))
        return json_encode(["message" => "invalid_input"]);

    $escapedMessage = str_replace("\n", "__NEWLINE__", $message); // Заменяем переносы строк на специальную последовательность
    $timestamp = date('Y-m-d H:i:s');

    $goodMessage = viginerCipher($escapedMessage);

    $stmt = $pdo->prepare("INSERT INTO group_messages (group_id, user_id, message, created_at) VALUES (?, ?, ?, ?)");
    $stmt->execute([$groupId, $senderId, $goodMessage, $timestamp]);

    $token = rand(0, 1024);
    $_SESSION['notification_token'] = $token;

    return json_encode(["message" => "success", "token" => $token]);
}

function send_notifications_to_group($data)
{
    global $pdo;
    session_start();

    if (!isset($_SESSION['notification_token']) || $_SESSION['notification_token'] !== $data['token']) {
        return json_encode(["message" => "invalid_token"]);
    }

    unset($_SESSION['notification_token']);

    $senderId = $_SESSION["user_id"];
    $groupId = $data["groupId"];

    $stmt = $pdo->prepare("SELECT user_id FROM group_members WHERE group_id = ? AND user_id != ?");
    $stmt->execute([$groupId, $senderId]);
    $members = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($members as $member) {
        // Получаем логин участника
        $stmt = $pdo->prepare("SELECT login FROM users WHERE id = ?");
        $stmt->execute([$member['user_id']]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user) {
            $login = viginerDecipher($user['login']);
            sendTelegramNotification($login, "Новое сообщение в группе.");
        }
    }

    return json_encode(["message" => "success"]);
}

function get_group_messages($data)
{
    global $pdo;
    session_start();

    if (!isset($_SESSION['isLogin']) || $_SESSION['isLogin'] !== true) {
        return json_encode(["message" => "unauthorized"]);
    }

    $groupId = $data['groupId'];
    $user_id = $_SESSION['user_id'];
    $startFromIdMessage = $data['startFromIdMessage'];

    $stmt = $pdo->prepare("SELECT id FROM group_members WHERE user_id = ? AND group_id = ?");
    $stmt->execute([$user_id, $groupId]);
    $count = $stmt->rowCount();

    if ($count === 0) {
        return json_encode(["message" => "ne poluhitsya"]);
    }

    $stmt = $pdo->prepare("SELECT u.login, gm.message, gm.created_at, gm.id 
        FROM group_messages gm
        JOIN users u ON gm.user_id = u.id 
        WHERE gm.group_id = ? AND gm.id >= ? 
        ORDER BY gm.created_at ASC");
    $stmt->execute([$groupId, $startFromIdMessage]);
    
    // Получаем все сообщения
    $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Если сообщений нет, возвращаем соответствующее сообщение
    if (empty($messages)) {
        return json_encode(["message" => "success", "data" => []]);
    }

    foreach ($messages as &$msg) {
        $msg['message'] = viginerDecipher($msg['message']); // Предполагается, что у вас есть функция для декодирования
        $msg['message'] = str_replace("__NEWLINE__", "\n", $msg['message']);
        $msg['login'] = viginerDecipher($msg['login']);
        $msg['created_at'] = date('Y-m-d H:i:s', strtotime($msg['created_at'])); // Форматируем дату
    }

    return json_encode(["message" => "success", "data" => $messages]);
}
?>