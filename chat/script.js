let isInitialLoad = true;
let currentLogin = '';
let lastMessageCount = 0;
let arrayMassages = [];


const loginName = document.getElementById('loginName');
const buttonLogin = document.getElementById('login');
const buttonRegister = document.getElementById('registration');

const windowRegistration = document.getElementById('windowRegistration');
const buttonCloseReg = document.getElementById('closeReg');
const buttonSubmitReg = document.getElementById('submitReg');
const h2ErrorReg = document.getElementById('errorReg');

const windowLogin = document.getElementById('windowLogin');
const buttonCloseLogin = document.getElementById('closeLogin');
const buttonSubmitLogin = document.getElementById('submitLogin');
const h2ErrorLogin = document.getElementById('errorLogin');

const chatContainer = document.getElementById('chatContainer');
const messagesDiv = document.getElementById('messages');
const sendMessageButton = document.getElementById('sendMessage');
const messageInput = document.getElementById('messageInput');

//const recipientSelect = document.getElementById('recipientSelect');
const friendsList = document.getElementById('friendsList');
const toggleFriendsListBtn = document.getElementById('toggleFriendsList');
const recipientInput = document.getElementById('recipient');
const clearRecipientBtn = document.getElementById('clearRecipient');
const searchFriendsInput = document.getElementById('searchFriends');
const friendsItemsContainer = document.getElementById('friendsItems');

const messagesTab = document.getElementById('messagesTab');
const groupsTab = document.getElementById('groupsTab');
const mainDivMessages = document.getElementById('main-div-messages');
const mainDivGroups = document.getElementById('main-div-groups');


function init() {
    checkAuth();
  
    loginName.addEventListener('click', onProfileClick);
    buttonRegister.addEventListener('click', onRegisterClick);
    buttonCloseReg.addEventListener('click', onCloseRegistrationClick);
    buttonLogin.addEventListener('click', onLoginClick);
    buttonCloseLogin.addEventListener('click', onCloseLoginClick);
  
    buttonSubmitReg.addEventListener('click', onSubmitRegistrationClick);
    buttonSubmitLogin.addEventListener('click', onSubmitLoginClick);
  
    sendMessageButton.addEventListener('click', sendMessage);
  
    toggleFriendsListBtn.addEventListener('click', onToggleFriendsListClick);
    document.addEventListener('click', onDocumentClick);
    clearRecipientBtn.addEventListener('click', onClearRecipientClick);
    recipientInput.addEventListener('input', updateClearButtonVisibility);
    searchFriendsInput.addEventListener('input', filterFriends);
  
    messagesTab.addEventListener('click', onMessagesTabClick);
    groupsTab.addEventListener('click', onGroupsTabClick);
  
    setInterval(loadMessages, 5000);

    updateClearButtonVisibility();
}


function onProfileClick() {
    window.location.href = 'profile/index.php';
}

function onRegisterClick() {
    windowRegistration.style.display = 'flex';
    windowLogin.style.display = 'none';
    chatContainer.style.display = 'none';
}

function onCloseRegistrationClick() {
    windowRegistration.style.display = 'none';
}

function onLoginClick() {
    windowLogin.style.display = 'flex';
    windowRegistration.style.display = 'none';
    chatContainer.style.display = 'none';
}
  
function onCloseLoginClick() {
    windowLogin.style.display = 'none';
}

async function onSubmitRegistrationClick() {
    const inpLogin = document.getElementById('inputLoginReg');
    const inpPassword = document.getElementById('inputPasswordReg');
    const inpRepeatPassword = document.getElementById('inputRepeatPasswordReg');
  
    const login = inpLogin.value.trim();
    const password = inpPassword.value.trim();
    const repeatPassword = inpRepeatPassword.value.trim();
  
    if (!login) {
        h2ErrorReg.textContent = 'Введите логин';
        return;
    }
    if (!password) {
        h2ErrorReg.textContent = 'Введите пароль';
        return;
    }
    if (password !== repeatPassword) {
        h2ErrorReg.textContent = 'Пароли не совпадают';
        return;
    }
  
    const data = {
        type: 'reg',
        log: login,
        pass: password
    };
  
    try {
        const result = await postData('../index.php', data);
        const message = result.message;
    
        switch (message) {
            case 'have':
                h2ErrorReg.textContent = 'Логин уже занят';
                break;
            case 'limit_exceeded':
                h2ErrorReg.textContent = 'Максимум аккаунтов достигнут';
                break;
            case 'true':
                checkAuth();
                break;
            default:
                console.log(result);
        }
    } catch (error) {
        console.error('Error onSubmitRegistrationClick:', error);
    }
}

async function onSubmitLoginClick() {
    const inpLogin = document.getElementById('inputLoginLogin');
    const inpPassword = document.getElementById('inputPasswordLogin');
  
    const login = inpLogin.value.trim();
    const password = inpPassword.value.trim();
  
    if (!login) {
        h2ErrorLogin.textContent = 'Введите логин';
        return;
    }
    if (!password) {
        h2ErrorLogin.textContent = 'Введите пароль';
        return;
    }
  
    const data = {
        type: 'log',
        log: login,
        pass: password
    };
  
    try {
        const result = await postData('../index.php', data);
        const message = result.message;
    
        switch (message) {
            case 'true':
                checkAuth();
                break;
            case 'wrong_password':
                h2ErrorLogin.textContent = 'Неверный пароль';
                break;
            case 'user_not_found':
                h2ErrorLogin.textContent = 'Логин не найден';
                break;
            default:
                console.log(result);
        }
    } catch (error) {
        console.error('Error onSubmitLoginClick:', error);
    }
}

function onToggleFriendsListClick(event) {
    event.stopPropagation();
    if (friendsList.style.display === 'none' || !friendsList.style.display) {
        friendsList.style.display = 'block';
        toggleFriendsListBtn.style.transform = 'rotate(180deg)';
        getRecipientSelect(); // Обновить список друзей
        searchFriendsInput.value = '';
    } else {
        friendsList.style.display = 'none';
        toggleFriendsListBtn.style.transform = 'rotate(0deg)';
    }
}

function onDocumentClick(event) {
    // Если кликнули не по списку друзей и не по кнопке открытия, то закрываем список
    if (!friendsList.contains(event.target) && event.target !== toggleFriendsListBtn) {
        friendsList.style.display = 'none';
        toggleFriendsListBtn.style.transform = 'rotate(0deg)';
    }
}

function onClearRecipientClick(event) {
    event.stopPropagation();
    recipientInput.value = '';
    updateClearButtonVisibility();
}

function onMessagesTabClick() {
    messagesTab.classList.add('active');
    groupsTab.classList.remove('active');
  
    mainDivMessages.style.display = 'inline';
    mainDivGroups.style.display = 'none';
}

function onGroupsTabClick() {
    groupsTab.classList.add('active');
    messagesTab.classList.remove('active');
  
    mainDivMessages.style.display = 'none';
    mainDivGroups.style.display = 'inline';
}


async function checkAuth() {
    const data = { type: 'check' };
    try {
        const result = await postData('../index.php', data);
        
        if (result.isLogin) {
            console.log('checkAuth: user is logged in');
            currentLogin = result.login;
            loginName.textContent = result.login;
    
            loginName.style.display = 'inline';
            buttonLogin.style.display = 'none';
            buttonRegister.style.display = 'none';
            chatContainer.style.display = 'flex';
            windowLogin.style.display = 'none';
            windowRegistration.style.display = 'none';
  
            loadMessages();
        } else {
            console.log('checkAuth: user is NOT logged in');
            loginName.style.display = 'none';
            buttonLogin.style.display = 'inline';
            buttonRegister.style.display = 'inline';
            chatContainer.style.display = 'none';
        }
    } catch (error) {
        console.error('Error checkAuth:', error);
    }
}

async function sendMessage() {
    const recipient = recipientInput.value.trim();
    const message = messageInput.value.trim();
  
    if (!recipient || !message) {
        alert('Пожалуйста, заполните все поля.');
        return;
    }
  
    const data = {
        type: 'send',
        recipient: recipient,
        message: message
    };
  
    try {
        const result = await postData('../index.php', data);
        if (result.message === 'success') {
            messageInput.value = '';
            await loadMessages(); // Обновляем список сообщений после отправки
        } else {
            alert('Ошибка при отправке сообщения.');
        }
    } catch (error) {
        console.error('Error sendMessage:', error);
    }
}

async function loadMessages() {
    // Если пользователь не авторизован — не грузим
    if (!currentLogin) return;
  
    const data = { type: 'get' };
  
    // Проверяем, были ли мы прокручены к нижней части
    const isAtBottom = messagesDiv.scrollHeight - messagesDiv.clientHeight <= messagesDiv.scrollTop + 1;
  
    try {
        const result = await postData('../index.php', data);
        if (result.message === 'success') {
            displayMessages(result.data);
    
            if (result.data.length > lastMessageCount) {
                if (isInitialLoad || isAtBottom) {
                    scrollToBottom(messagesDiv);
                }   
            }
            lastMessageCount = result.data.length;
            isInitialLoad = false;
        } else {
            messagesDiv.innerHTML = '<p>Нет сообщений.</p>';
        }
    } catch (error) {
        console.error('Error loadMessages:', error);
    }
}

function displayMessages(messages) {
    const previousScrollHeight = messagesDiv.scrollHeight;
    const previousScrollTop = messagesDiv.scrollTop;
  
    messagesDiv.innerHTML = '';
  
    // Обеспечиваем, что в arrayMassages есть флаги для каждого сообщения
    while (arrayMassages.length < messages.length) {
        arrayMassages.push(false);
    }
  
    messages.forEach((msg, index) => {
        const messageElement = document.createElement('div');
        messageElement.classList.add('message');
    
        const senderElement = document.createElement('strong');
        if (msg.sender === currentLogin) {
            senderElement.textContent = `я -> ${msg.recipient}: `;
        } else {
            senderElement.textContent = `${msg.sender} -> мне: `;
        }
    
        const textElement = document.createElement('span');
        textElement.textContent = msg.message;
    
        const timestampElement = document.createElement('div');
        timestampElement.style.fontSize = '0.8em';
        timestampElement.style.color = '#666';
        timestampElement.textContent = msg.timestamp;
    
        let needTruncate = false;
        let nextButton = null;
    
        const lines = msg.message.split('\n');
        // Если в сообщении больше 4 строк и оно не развернуто
        if (lines.length > 4 && !arrayMassages[index]) {
            needTruncate = true;
            const truncatedText = lines.slice(0, 4).join('\n') + '...';
            textElement.textContent = truncatedText;
    
            nextButton = document.createElement('button');
            nextButton.textContent = 'Далее';
            nextButton.classList.add('next-button');
            nextButton.onclick = () => {
                arrayMassages[index] = true;
                textElement.textContent = msg.message;
                nextButton.remove();
            };
        }
    
        // Добавляем дочерние элементы
        messageElement.appendChild(senderElement);
        messageElement.appendChild(textElement);
        messageElement.appendChild(timestampElement);
        if (needTruncate && nextButton) {
            messageElement.appendChild(nextButton);
        }
    
        messagesDiv.appendChild(messageElement);
    });
  
    if (!isInitialLoad) {
        messagesDiv.scrollTop = messagesDiv.scrollHeight - previousScrollHeight + previousScrollTop;
    }
}

async function getRecipientSelect() {
    const data = { type: 'getFriends' };
  
    try {
        const result = await postData('../index.php', data);
        friendsItemsContainer.innerHTML = '';
    
        result.data.forEach(friend => {
            const friendDiv = document.createElement('div');
            friendDiv.textContent = friend;
            friendDiv.addEventListener('click', () => {
                recipientInput.value = friend;
                friendsList.style.display = 'none';
                toggleFriendsListBtn.style.transform = 'rotate(0deg)';
                filterFriends();
                updateClearButtonVisibility();
            });
            friendsItemsContainer.appendChild(friendDiv);
        });
    } catch (error) {
        console.error('Error getRecipientSelect:', error);
    }
}

function filterFriends() {
    const filter = searchFriendsInput.value.toLowerCase();
    const friends = friendsItemsContainer.getElementsByTagName('div');
  
    Array.from(friends).forEach(friend => {
        const txtValue = friend.textContent || friend.innerText;
        friend.style.display = txtValue.toLowerCase().includes(filter) ? '' : 'none';
    });
}
  
function updateClearButtonVisibility() {
    clearRecipientBtn.style.display = recipientInput.value ? 'flex' : 'none';
}

document.addEventListener('DOMContentLoaded', init);