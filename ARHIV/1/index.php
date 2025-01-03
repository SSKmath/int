<?php
header("Content-Security-Policy: default-src 'self'; script-src 'self';");
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>.....</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <a href="cypher/index.php">
            <button>Шифрование текста</button>
        </a>
        <a href="bezu/index.php">
            <button>Решение теоремой Безу</button>
        </a>
    </div>
</body>
</html>