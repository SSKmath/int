<?php
/*
Пользователь: us10879i
пароль: *Siv;D>HG!f"W2fj
Сервер: mysql-10879
*/ 

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
?>