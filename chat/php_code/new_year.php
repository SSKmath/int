<?php
function telegram_bot($update)
{
    if(isset($update["message"])) {
        $message = $update["message"];
        $chat_id = $message["chat"]["id"];
        $text = $message["text"];

        if(strpos($text, "/start") === 0) {
            $parts = explode(" ", $text);
            if(count($parts) == 2) {
                $site_login = $parts[1];
                
                

                $mysqli = new mysqli("localhost", "us10879i", "*Siv;dp6-MZL-mvH-M6g!", "db10879p");

                if ($mysqli->connect_error) {
                    error_log("Ошибка подключения к БД: " . $mysqli->connect_error);
                    exit;
                }

                $site_login = $mysqli->real_escape_string($site_login);
                $telegram_chat_id = $mysqli->real_escape_string($chat_id);

                $sql = "INSERT INTO user_notifications (site_login, telegram_chat_id) VALUES ('$site_login', '$telegram_chat_id') 
                        ON DUPLICATE KEY UPDATE telegram_chat_id='$telegram_chat_id'";

                if($mysqli->query($sql)) {
                    // Ответ пользователю
                    sendMessage($chat_id, "Уведомления успешно включены для вашего аккаунта '$site_login'.");
                } else {
                    sendMessage($chat_id, "Произошла ошибка при подключении уведомлений.");
                }

                $mysqli->close();
            } else {
                sendMessage($chat_id, "Некорректный формат команды. Используйте /start site_login");
            }
        }
    }
}

// Функция отправки сообщения
function sendMessage($chat_id, $text) {
    $token = "7602926953:AAG5HWnMfBQj04Rvsrq2WU5dRGmkJh8m8JU";
    $url = "https://api.telegram.org/bot$token/sendMessage";

    $data = array(
        'chat_id' => $chat_id,
        'text' => $text
    );

    // Используем cURL для отправки POST-запроса
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    curl_close($ch);
}

function sendTelegramNotification($site_login, $message) 
{
    $host = 'localhost';
    $db = 'db10879p';
    $user = 'us10879i';
    $password = '*Siv;dp6-MZL-mvH-M6g!';
    $charset = 'utf8mb4';

    try {
        $pdo = new PDO("mysql:host=$host;dbname=$db;charset=$charset", $user, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch(PDOException $e) {
        die("Ошибка подключения: " . $e->getMessage());
    }

    // Экранирование входных данных
    $site_login = $pdo->quote($site_login);

    // Поиск chat_id по логину
    $sql = "SELECT telegram_chat_id FROM user_notifications WHERE site_login=$site_login LIMIT 1";
    $result = $pdo->query($sql);

    if($result && $row = $result->fetch(PDO::FETCH_ASSOC)) 
    {
        $chat_id = $row['telegram_chat_id'];

        // Отправка сообщения через Telegram API
        $token = "7602926953:AAG5HWnMfBQj04Rvsrq2WU5dRGmkJh8m8JU";
        $url = "https://api.telegram.org/bot$token/sendMessage";

        $data = array(
            'chat_id' => $chat_id,
            'text' => $message
        );

        // Используем cURL для отправки POST-запроса
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if($http_code === 200) {
            return true;
        } else {
            error_log("Ошибка отправки сообщения: " . $response);
            return false;
        }
    } else {
        return false;
    }
}

function new_year()
{
    $host = 'localhost';
    $db = 'db10879p';
    $user = 'us10879i';
    $password = '*Siv;dp6-MZL-mvH-M6g!';
    $charset = 'utf8mb4';

    try {
        $pdo = new PDO("mysql:host=$host;dbname=$db;charset=$charset", $user, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch(PDOException $e) {
        die("Ошибка подключения: " . $e->getMessage());
    }

    try {
        $stmt = $pdo->prepare("SELECT * FROM user_notifications");
        $stmt->execute(); // Выполняем запрос
        $records = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($records as $record) {
            sendTelegramNotification($record['site_login'], 'Поздравляю тебя с Новым Годом!');
        }
    } catch (PDOException $e) {
        // Обработка ошибок
        error_log("Ошибка запроса: " . $e->getMessage());
    }
}

//new_year();
?>