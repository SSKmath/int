<?php
header("Content-Security-Policy: default-src 'self'; script-src 'self';");
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
    <title>Шифрование текста</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../styles.css">
    <link rel="stylesheet" href="modes.css">
</head>
<body>
    <div class="container" id="oldMod" name="oldMod">
        <h2>Введите текст, который хотите зашифровать, и ключ для шифрования</h2>
        <textarea placeholder="Текст" id="textarea1"></textarea>
        <textarea placeholder="Ключ" class="key" id="key1"></textarea>
        <button class="cypher" id="convertButton1">Зашифровать</button>
        <h2>Результат:</h2>
        <textarea readonly id="textarea3"></textarea>
    </div>
    <div class="container" id="newMod">
        <h2>Введите текст, зашифруйте или расшифруйте его ключём</h2>
        <textarea placeholder="Текст" id="textarea5"></textarea>
        <textarea placeholder="Ключ" class="key" id="key3"></textarea>
        <div id="newButtonsContainer">
            <button class="cypher" id="convertButton3">Зашифровать</button>
            <button class="cypher" id="convertButton4">Расшифровать</button>
        </div>
        <h2>Результат:</h2>
        <textarea readonly id="textarea6"></textarea>
    </div>
    <div class="container" id="oldMod" name="oldMod">
        <h2>Введите зашифрованный текст и ключ для расшифрования</h2>
        <textarea placeholder="Текст" id="textarea2"></textarea>
        <textarea placeholder="Ключ" class="key" id="key2"></textarea>
        <button class="cypher" id="convertButton2">Расшифровать</button>
        <h2>Результат:</h2>
        <textarea readonly id="textarea4"></textarea>
    </div>
    <div class="changeButtonDiv">
        <button id ="changeButton" class="changeButton">о</button>
    </div>
    <script src="script.js"></script>
</body>
</html>