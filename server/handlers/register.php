<?php
function register_user($data)
{
    global $pdo;
    session_start();

    $log0 = $data['log'];

    $log0 = trim($log0);

    $log = viginerCipher($log0);
    $pass = $data['pass'];

    $user_ip = $_SERVER['REMOTE_ADDR'];

    // Проверяем, существует ли пользователь
    $stmt = $pdo->prepare("SELECT login FROM users WHERE login = ?");
    $stmt->execute([$log]);
    
    if ($stmt->fetch()) {
        return json_encode(["message" => "have"]);
    }

    // Проверяем количество аккаунтов с этого IP
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE ip_address = ?");
    $stmt->execute([$user_ip]);
    $account_count = $stmt->fetchColumn();

    // Устанавливаем лимит на количество аккаунтов с одного IP (например, 3)
    $account_limit = 5;
    
    if ($account_count >= $account_limit) {
        return json_encode(["message" => "limit_exceeded"]);
    }

    // Хешируем пароль и сохраняем пользователя
    $hashedPassword = password_hash($pass, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("INSERT INTO users (login, password, ip_address) VALUES (?, ?, ?)");
    
    if ($stmt->execute([$log, $hashedPassword, $user_ip])) {
        $_SESSION['user_id'] = $pdo->lastInsertId();
        $_SESSION['isLogin'] = true;
        $_SESSION['login'] = $log0;

        // Добавляем сессию
        $stmt = $pdo->prepare("INSERT INTO sessions (user_id) VALUES (?)");
        $stmt->execute([$_SESSION['user_id']]);
        return json_encode(["message" => "true"]);
    }

    return json_encode(["message" => "error"]);
}

/*function generateRandomString($length = 10) {
    // Генерируем случайные байты
    $bytes = random_bytes($length);
    
    // Кодируем в Base64
    $randomString = base64_encode($bytes);
    
    // Убираем символы, которые могут быть нежелательны
    // Например, убираем символы '=', '+' и '/' и обрезаем до нужной длины
    $randomString = str_replace(['+', '/', '='], '', $randomString);
    
    // Возвращаем строку нужной длины
    return substr($randomString, 0, $length);
}*/

function cutFirstHalf($string) {
    // Получаем длину строки
    $length = strlen($string);
    
    // Находим середину строки
    $halfLength = ceil($length / 2);
    
    // Возвращаем вторую половину строки
    return substr($string, $halfLength);
}

function login_user($data)
{
    global $pdo;
    session_start();

    $log0 = $data['log'];
    $log = viginerCipher($log0);
    $pass = $data['pass'];

    // Проверяем пользователя в базе данных
    $stmt = $pdo->prepare("SELECT id, password FROM users WHERE login = ?");
    $stmt->execute([$log]);
    
    if ($user = $stmt->fetch(PDO::FETCH_ASSOC)) {
        if (password_verify($pass, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['isLogin'] = true;
            $_SESSION['login'] = $log0;

            $token = rand(-PHP_INT_MAX, PHP_INT_MAX - 1000) + 1000; // PHP_INT_MAX

            $token = password_hash($token, PASSWORD_DEFAULT);;
            $token = cutFirstHalf($token);

            setcookie('auth_token', $token, time() + 86400 * 7, "/", "", true, true);
            
            $stmt = $pdo->prepare("UPDATE users SET auth_token = :token WHERE login = :login");
            $stmt->execute(['token' => $token, 'login' => $log]);

            // Добавляем сессию
            $userId = $_SESSION['user_id'];
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM sessions WHERE user_id = ?");
            $stmt->execute([$userId]);
            $count = $stmt->fetchColumn();
            if ($count == 0) {
                $stmt = $pdo->prepare("INSERT INTO sessions (user_id) VALUES (?)");
                $stmt->execute([$userId]);
            }
            return json_encode(["message" => "true"]);
        } else {
            return json_encode(["message" => "wrong_password"]);
        }
    }

    return json_encode(["message" => "user_not_found"]);
}

function logout_user($data)
{
    global $pdo;
    session_start();
    
    if (!empty($_SESSION['user_id'])) {
        $userId = $_SESSION['user_id'];

        // Удаляем auth_token из БД
        $stmt = $pdo->prepare("UPDATE users SET auth_token = NULL WHERE id = ?");
        $stmt->execute([$userId]);
    }

    $stmt = $pdo->prepare("DELETE FROM sessions WHERE user_id = ?");
    $stmt->execute([$_SESSION['user_id']]);

    setcookie("auth_token", "", time() - 3600, "/", "", true, true);
    setcookie('login', "", time() - 3600, "/", "", true, true);
    setcookie('user_id', "", time() - 3600, "/", "", true, true);

    $stmt = $pdo->prepare("UPDATE users SET auth_token = NULL WHERE login = :login");
    $stmt->execute(['login' => viginerCipher($_SESSION['login'])]);

    session_regenerate_id(true);
    session_unset();
    session_destroy();
    return json_encode(["message" => "logged_out"]);
}
?>