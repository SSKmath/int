<?php
//include '../secure/protect.php';
//header("Cache-Control: no-cache");
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Чат</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../styles.css">
    <link rel="stylesheet" href="chatStyles.css">
</head>
<body>
    <div class="header">
        <div id="goMainPage">
            <a href="../index.php">
                <button>&larr;</button>
            </a>
        </div>
        <div id="mainHeader">
            <h2 id="loginName">Имя</h2>
            <button id="login">Войти</button>
            <button id="registration">Зарегистрироваться</button>
        </div>
    </div>
    <div class="main">
        <div class="container" id="windowRegistration">
            <h2>Регистрация</h2>
            <p>Придумайте логин, пароль и повторите пароль.</p>
            <input type="login" id="inputLoginReg" maxlength="16" placeholder="Логин">
            <input type="password" id="inputPasswordReg" maxlength="16" placeholder="Пароль">
            <input type="password" id="inputRepeatPasswordReg" maxlength="16" placeholder="Повтор пароля">
            <button id="submitReg">Готово</button>
            <button id="closeReg">x</button>
            <h2 id="errorReg"></h2>
        </div>
        <div class="container" id="windowLogin">
            <h2>Вход</h2>
            <p>Введите логин с паролем</p>
            <input type="login" id="inputLoginLogin" maxlength="16" placeholder="Логин">
            <input type="password" id="inputPasswordLogin" maxlength="16" placeholder="Пароль">
            <button id="submitLogin">Готово</button>
            <button id="closeLogin">x</button>
            <h2 id="errorLogin"></h2>
        </div>
        <div class="container" id="chatContainer" style="display: none;">
            <div class="tab-container">
                <button id="messagesTab" class="tab-button active">Сообщения</button>
                <button id="groupsTab" class="tab-button">Группы</button>
            </div>

            <div id="main-div-messages">
                <div class="messages" id="messages">
                    <!-- Сообщения будут отображаться здесь -->
                </div>
                <div class="input-group">
                    <div class="recipient-container">
                        <input type="text" id="recipient" maxlength="16" placeholder="Кому" autocomplete="off">
                        <button id="toggleFriendsList" class="toggle-button">▼</button>
                        <button id="clearRecipient" class="clear-button">&times;</button>
                        <div id="friendsList" class="friends-list">
                            <input type="text" id="searchFriends" placeholder="Поиск друга...">
                            <div id="friendsItems">
                                <!-- Друзья будут добавлены здесь динамически -->
                            </div>
                        </div>
                    </div>
                    <textarea id="messageInput" maxlength="512" placeholder="Ваше сообщение"></textarea>
                    <button id="sendMessage" class="send-button">Отправить</button>
                </div>
            </div>

            <div id="main-div-groups" style="display: none;">
                <div id="page1">
                    <div class="page1-header">
                        <h2>Мои группы:</h2>
                        <button id="btnCreateGroup">Создать</button>
                    </div>
                    <ul id="groupList">
                        <!-- Список групп будет динамически добавлен здесь -->
                    </ul>
                </div>
                <div id="page2" style="display: none;">
                    <button id="backToPage1">Назад</button>
                    <h2>Создать группу:</h2>
                    <input type="text" id="groupName" placeholder="Название группы" required maxlength="32">
                    <textarea id="groupDescription" placeholder="Описание группы" required maxlength="256"></textarea>
                    <button id="saveGroup">Сохранить группу</button>
                </div>
                <div id="page3" style="display: none;">
                    <div class="page3-header">
                        <div>
                            <button id="backToPage1FromChat">Назад</button>
                        </div>
                        <h2><span id="groupTitle"></span></h2>
                    </div>

                    <div id="forOwner" style="display: none; align-items: center;">
                       <input id="loginForInvite" type="text" placeholder="Кого добавить?">
                       <button id="inviteToGroup">Добавить</button>
                        <p id="successInviteToGroup" style="display: none; margin-left: 5px;">Да</p>
                    </div>
                    
                    <div class="messages" id="messagesGroup">
                        <!-- Сообщения будут отображаться здесь -->
                    </div>
                    
                    <div class="input-group">
                        <textarea id="messageInputGroup" maxlength="512" placeholder="Ваше сообщение"></textarea>
                        <button id="sendMessageGroup">Отправить</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="modules.js"></script>
    <script src="script.js"></script>
    <script src="groups.js"></script>
</body>
</html>