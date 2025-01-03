<?php
include_once "server/router.php";
//include 'secure/protect.php';
//header("Cache-Control: no-cache");
if ($_SERVER["REQUEST_METHOD"] === "GET") {
    header("Location: https://1rpo.ru/pages/main/index.html");
    exit();
}
//include 'secure/viginer.php';

//include 'chat/include.php';

/*$allowed_origin = 'https://1rpo.ru';
if (isset($_SERVER['HTTP_ORIGIN']) && $_SERVER['HTTP_ORIGIN'] !== $allowed_origin) 
{
    die('Invalid Origin');
}*/ // Надо сделать, чтобы ТГ бот не блокировался
//echo json_encode(['message' => 'test test test test']);
//exit();
/*include_once 'secure/viginer.php';
include_once 'chat/php_code/auth_check.php';
include_once 'chat/php_code/register.php';
include_once 'chat/php_code/send_message.php';
include_once 'chat/php_code/get_messages.php';*/

if ($_SERVER["REQUEST_METHOD"] === "POST") 
{
    $data = json_decode(file_get_contents("php://input"), true);
    if ($data)
    {
        echo router($data);
    }
    exit();
}
?>