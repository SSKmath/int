<?php
function dbConnect() {
    $host = 'localhost';
    $db = 'db10879p';
    $user = 'us10879i';
    $pass = '*Siv;dp6-MZL-mvH-M6g!';

    try {
        $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $pdo;
    } catch (PDOException $e) {
        die("Connection failed: " . $e->getMessage());
    }
}

/*function updateLogins($pdo) {
    // Получаем все логины из базы данных
    $query = "SELECT id, login FROM users"; // Замените 'users' на имя вашей таблицы
    $stmt = $pdo->prepare($query);
    $stmt->execute();
    
    // Обновляем каждый логин
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $id = $row['id'];
        $login = $row['login'];
        
        // Шифруем логин
        $encryptedLogin = viginerCipher($login);
        
        // Обновляем логин в базе данных
        $updateQuery = "UPDATE users SET login = :login WHERE id = :id"; // Замените 'users' на имя вашей таблицы
        $updateStmt = $pdo->prepare($updateQuery);
        $updateStmt->execute([':login' => $encryptedLogin, ':id' => $id]);
    }
}

function replFunc()
{
    try {
        $pdo = new PDO('mysql:host=localhost;dbname=db10879p', 'us10879i', '*Siv;dp6-MZL-mvH-M6g!');
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        updateLogins($pdo);
        
        echo "Логины успешно обновлены!";
    } catch (PDOException $e) {
        echo "Ошибка: " . $e->getMessage();
    }
}*/

function register_user($data)
{
    session_start();

    $log0 = $data['log'];
    $log = viginerCipher($log0);
    $pass = $data['pass'];

    $pdo = dbConnect();

    // Проверяем, существует ли пользователь
    $stmt = $pdo->prepare("SELECT login FROM users WHERE login = ?");
    $stmt->execute([$log]);
    
    if ($stmt->fetch()) {
        return json_encode(["message" => "have"]);
    }

    // Хешируем пароль и сохраняем пользователя
    $hashedPassword = password_hash($pass, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("INSERT INTO users (login, password) VALUES (?, ?)");
    
    if ($stmt->execute([$log, $hashedPassword])) {
        $_SESSION['isLogin'] = true;
        $_SESSION['login'] = $log0;
        return json_encode(["message" => "true"]);
    }

    return json_encode(["message" => "error"]);
}

function login_user($data)
{
    session_start();

    $log0 = $data['log'];
    $log = viginerCipher($log0);
    $pass = $data['pass'];

    $pdo = dbConnect();

    // Проверяем пользователя в базе данных
    $stmt = $pdo->prepare("SELECT password FROM users WHERE login = ?");
    $stmt->execute([$log]);
    
    if ($user = $stmt->fetch(PDO::FETCH_ASSOC)) {
        if (password_verify($pass, $user['password'])) {
            $_SESSION['isLogin'] = true;
            $_SESSION['login'] = $log0;
            return json_encode(["message" => "true"]);
        } else {
            return json_encode(["message" => "wrong_password"]);
        }
    }

    return json_encode(["message" => "user_not_found"]);
}

function logout_user($data)
{
    session_start();
    session_unset();
    session_destroy();
    return json_encode(["message" => "logged_out"]);
}
?>