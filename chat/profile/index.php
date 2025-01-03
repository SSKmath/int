<?php
header("Cache-Control: no-cache");
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Чат -> Профиль</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../../styles.css">
    <link rel="stylesheet" href="../chatStyles.css">
    <link rel="stylesheet" href="profileStyles.css">
</head>
<body>
    <header class="header">
        <div id="goMainPage">
            <a href="../index.php">
                <button>&larr;</button>
            </a>
            <h2 id="countOnline">В сети: 0</h2>
        </div>
        <div id="mainHeader">
            <h2 id="loginName">Имя</h2>
        </div>
    </header>

    <main class="main">
        <div class="profile-container">
            <section class="container" class="friends-section">
                <h2>Друзья</h2>
                <div class="friends-actions">
                    <div class="add-friend">
                        <div>
                            <input type="text" id="inpRequestFriend" maxlength="16" placeholder="Кого пригласить?">
                            <button id="requestFriend">Отправить</button>
                        </div>
                        <p id="errorRequestFriend" class="error-message"></p>
                    </div>
                    <div class="friend-lists">
                        <div class="friend-list">
                            <h3>Приглашения в друзья</h3>
                            <ul id="requestsToFriend">
                                <!-- Запросы будут здесь -->
                            </ul>
                        </div>
                        <div class="friend-list">
                            <h3>Ваши друзья<br><br></h3>
                            <ul id="listFriend">
                                <!-- Друзья будут здесь -->
                            </ul>
                        </div>
                    </div>
                </div>
            </section>

            <section class="container" class="account-management">
                <h2>Управление аккаунтом</h2>
                <div class="account-actions">
                    <div>
                        <button id="changePasswordBtn">Изменить пароль</button>
                        <button id="enableTelegramNotifyBtn">Вкл. увед.</button>
                    </div>
                    <div>
                        <button id="logoutBtn">Выйти</button>
                        <button id="deleteAccountBtn" class="danger">Удалить аккаунт</button>
                    </div>
                </div>
            </section>
        </div>
    </main>

    <div id="modal" class="modal">
        <div class="modal-content">
            <span class="close-button">&times;</span>
            <div id="modalBody">
                <!-- Загрузится из js -->
            </div>
        </div>
    </div>

    <script src="script.js"></script>
</body>
</html>