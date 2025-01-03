checkAuth();
updateCountOnline();
getFriendRequests();
updateFriendList();
updateActivity();

const loginName = document.getElementById('loginName');
const countOnline = document.getElementById('countOnline');

const inpRequestFriend = document.getElementById('inpRequestFriend');
const requestFriend = document.getElementById('requestFriend');
const errorRequestFriend = document.getElementById('errorRequestFriend');

const enableTelegramNotifyBtn = document.getElementById('enableTelegramNotifyBtn');
const logoutBtn = document.getElementById('logoutBtn');
const changePasswordBtn = document.getElementById('changePasswordBtn');
const deleteAccountBtn = document.getElementById('deleteAccountBtn');

let currentLogin = '';

function checkAuth() 
{
    fetch('../../index.php', {
        method: 'POST',
        credentials: 'include',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({type: 'checkAuth'})
    })
    .then(response => response.json())
    .then(data => {
        if (data.isLogin) {
            console.log("checkAuth true");
            // Пользователь авторизован
            loginName.textContent = data['login'];
            currentLogin = data['login'];
        } else {
            console.log("checkAuth false");
            // Пользователь не авторизован
            window.location.replace('https://1rpo.ru/pages/chat/index.html');
        }
    })
    .catch(error => {
        console.error('Error:', error);
    });
}

logoutBtn.addEventListener('click', function() {
    fetch('../../index.php', {
        method: 'POST',
        credentials: 'include',
        body: JSON.stringify({type: 'logged_out'})
    })
    .then(response => response.json())
    .then(data => {
        if (data.message === 'logged_out')
            checkAuth();
    })
    .catch(error => {
        console.error('Error:', error);
    });
});

enableTelegramNotifyBtn.addEventListener('click', function() {
    window.location.href = 'https://t.me/rporu_notifications_bot?start=' + currentLogin;
});

function updateCountOnline()
{
    fetch('../../index.php', {
        method: 'POST',
        credentials: 'include',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({type: 'getCountOnline'})
    })
    .then(response => response.json())
    .then(data => {
        countOnline.textContent = 'В сети: ' + String(data.online_count);
    })
    .catch(error => {
        console.error('Ошибка при получении данных онлайн:', error);
        countOnline.textContent = 'В сети: -1';
    });
}

function updateActivity() 
{
    console.log('updateActivity');
    const data = {
        type: 'updateActivity'
    };

    fetch('../../index.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(result => {
    })
    .catch(error => {
        console.error('Error:', error);
    });
}

requestFriend.addEventListener('click', function() 
{
    const friendLogin = inpRequestFriend.value.trim();

    if (friendLogin === '')
    {
        alert('Введите логин');
        return;
    }

    const data = {
        type: 'sendFriendRequest',
        friend_login: friendLogin
    };
    fetch('../../index.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(result => {
        if (result.message === 'friend_not_found')
            errorRequestFriend.textContent = 'Логин не найден';
        else if (result.message === 'request_already_sent')
            errorRequestFriend.textContent = 'Запрос уже отправлен';
        else if (result.message === 'database_error')
            errorRequestFriend.textContent = 'Ошибка';
        else if (result.message === 'success')
            errorRequestFriend.textContent = '';
    })
    .catch(error => {
        console.error('Error:', error);
    });
});

function getFriendRequests()
{
    const data = {
        type: 'viewFriendRequests'
    };
    fetch('../../index.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(result => {
        displayFriendRequests(result.data);
    })
    .catch(error => {
        console.error('Error:', error);
    });
}

function displayFriendRequests(requests) {
    const requestsContainer = document.getElementById('requestsToFriend');
    requestsContainer.innerHTML = '';

    let flag = true;
    requests.forEach(request => {
        flag = false;
        const requestElement = document.createElement('div');
        requestElement.className = 'message';
        requestElement.style.margin = '5px';

        const loginText = document.createElement('strong');
        loginText.style.fontSize = '16px';
        loginText.textContent = request;

        const acceptButton = document.createElement('button');
        acceptButton.className = 'next-button';
        acceptButton.textContent = 'Принять';
        acceptButton.onclick = () => handleFriendRequest(request, 'accept');

        const declineButton = document.createElement('button');
        declineButton.className = 'next-button';
        declineButton.textContent = 'Отказать';
        declineButton.onclick = () => handleFriendRequest(request, 'decline');

        requestElement.appendChild(loginText);
        requestElement.appendChild(acceptButton);
        requestElement.appendChild(declineButton);
        
        requestsContainer.appendChild(requestElement);
    });

    if (flag)
    {
        const emptyElement = document.createElement('p');
        emptyElement.className = "empty-element";
        emptyElement.textContent = 'Пусто';
        requestsContainer.appendChild(emptyElement);
    }
}

function handleFriendRequest(login, action) {
    const data = {
        type: action === 'accept' ? 'acceptFriendRequest' : 'declineFriendRequest',
        login: login
    };

    fetch('../../index.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(result => {
        console.log(result.message); // Сообщение об успехе или ошибке
        getFriendRequests(); // Обновляем список запросов после действия
        updateFriendList();
    })
    .catch(error => {
        console.error('Error:', error);
    });
}

function updateFriendList()
{
    const data = {
        type: 'getFriends'
    };

    fetch('../../index.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(result => {
        const friendContainer = document.getElementById('listFriend');
        friendContainer.innerHTML = '';
        flag = true;
        result['data'].forEach(friend => {
            flag = false;
            const friendElement = document.createElement('div');
            friendElement.className = 'message';
            friendElement.style.margin = '5px';

            const loginText = document.createElement('strong');
            loginText.style.fontSize = '16px';
            loginText.textContent = friend;

            const deleteButton = document.createElement('button');
            deleteButton.className = 'next-button';
            deleteButton.textContent = 'Удалить';
            deleteButton.onclick = () => deleteFriend(friend);

            friendElement.appendChild(loginText);
            friendElement.appendChild(deleteButton);
            
            friendContainer.appendChild(friendElement);
        });
        if (flag)
        {
            const emptyElement = document.createElement('p');
            emptyElement.style.alignSelf = 'center';
            emptyElement.textContent = 'Пусто';
            friendContainer.appendChild(emptyElement);
        }
    })
    .catch(error => {
        console.error('Error:', error);
    });
}

function deleteFriend(friend)
{
    const data = {
        type: 'deleteFriend',
        friend: friend
    };

    fetch('../../index.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(result => {
        console.log(result.message);
        updateFriendList();
    })
    .catch(error => {
        console.error('Error:', error);
    });
}

document.addEventListener('DOMContentLoaded', () => {
    const modal = document.getElementById('modal');
    const closeButton = document.querySelector('.close-button');

    function openModal(content) {
        const modalBody = document.getElementById('modalBody');
        modalBody.innerHTML = content;
        modal.style.display = 'block';
    }

    function closeModal() {
        modal.style.display = 'none';
    }

    changePasswordBtn.addEventListener('click', () => {
        const content = `
            <h2>Изменить пароль</h2>
            <form id="changePasswordForm">
                <label for="currentPassword">Текущий пароль:</label>
                <input type="password" id="currentPassword" name="currentPassword" required placeholder="Текущий пароль">
                
                <label for="newPassword">Новый пароль:</label>
                <input type="password" id="newPassword" name="newPassword" required placeholder="Новый пароль">

                <button type="submit">Сохранить</button>

                <p id="errorMessage"></p>
                <p>После сохранения вас выкинет из аккаунта.</p>
            </form>
        `;
        openModal(content);
    });

    deleteAccountBtn.addEventListener('click', () => {
        const content = `
            <h2>Удалить аккаунт</h2>
            <p>Вы уверены, что хотите удалить свой аккаунт? Это действие необратимо.</p>
            <input type="password" id="currentPasswordForDelete" name="currentPassword" required placeholder="Введите пароль">
            <button id="confirmDelete" class="danger">Удалить</button>
            <button id="cancelDelete">Отмена</button>
        `;
        openModal(content);
    });

    closeButton.addEventListener('click', closeModal);

    window.addEventListener('click', (event) => {
        if (event.target == modal) {
            closeModal();
        }
    });

    // обработка формы изменения пароля
    document.body.addEventListener('submit', (event) => {
        if (event.target.id === 'changePasswordForm') {
            event.preventDefault();
            const currentPassword = document.getElementById('currentPassword').value;
            const newPassword = document.getElementById('newPassword').value;
            const pErrorMessage = document.getElementById('errorMessage');

            const data = {
                type: 'changePassword',
                currentPassword: currentPassword,
                newPassword: newPassword
            };

            fetch('../../index.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(data)
            })
            .then(response => response.json())
            .then(result => {
                console.log(result.message);
                if (result["message"] === "user_not_found")
                    pErrorMessage.textContent = "Ошибка";
                else if (result["message"] === "wrong_password")
                    pErrorMessage.textContent = "Неверный текущий пароль";
                else if (result["message"] === "password_change_failed")
                    pErrorMessage.textContent = "Ошибка";
                else if (result["message"] === "password_changed_successfully")
                    window.location.href = '../index.php';
                else
                    pErrorMessage.textContent = "фатальная ошибка";
            })
            .catch(error => {
                console.error('Error:', error);
            });
        }
    });

    // Обработка удаления аккаунта
    document.body.addEventListener('click', (event) => {
        if (event.target.id === 'confirmDelete') {
            console.log('deleteAccount');
            const currentPassword = document.getElementById('currentPasswordForDelete').value;
            const data = {
                type: 'deleteAccount',
                currentPassword
            };

            fetch('../../index.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(data)
            })
            .then(response => response.json())
            .then(result => {
                console.log(result.message);
                if (result.message === 'good')
                    window.location.href = '../index.php';
                else if (result.message === 'wrong password')
                    alert('Неверный пароль');
                else if (result.message === 'owner of group')
                    alert('Вы не можите удалить аккаунт, так как являетесь владельцем группы');
            })
            .catch(error => {
                console.error('Error:', error);
            });
        } else if (event.target.id === 'cancelDelete') {
            closeModal();
        }
    });
});

setInterval(getFriendRequests, 5000);
setInterval(updateFriendList, 5000);
setInterval(updateCountOnline, 10000);
setInterval(updateActivity, 59000);