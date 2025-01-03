<?php
include '../secure/protect.php';
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Решение теоремой Безу</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../styles.css">
</head>
<body>
    <div class="container">
        <h2>Введите многочлен с целыми коэфициентами. Пример x4+4x3-9x2+3x+1.</h2>
        <textarea class="key" placeholder="Многочлен" id="input"></textarea>
        <button class="bezu" id="button">Решить</button>
        <h2>Корни:</h2>
        <textarea class="key" readonly id="output"></textarea>
        <h2>О неверных ответах и примерах, когда не работает, говорить мне.</h2>
    </div>
    <script src="script.js"></script>
</body>
</html>