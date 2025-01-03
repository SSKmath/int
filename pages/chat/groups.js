let currentGroupId = null;
let isInitialLoadGroup = true;
let lastMessageCountGroup = 0;
let groups = [];
let lastGroupCount = 0;

const page1 = document.getElementById('page1');
const page2 = document.getElementById('page2');
const page3 = document.getElementById('page3');

const groupList = document.getElementById('groupList');
const groupTitle = document.getElementById('groupTitle');

const inviteToGroupBtn = document.getElementById('inviteToGroup');
const loginForInviteInput = document.getElementById('loginForInvite');

const messageDivGroup = document.getElementById('messagesGroup');
const sendMessageGroupBtn = document.getElementById('sendMessageGroup');
const messageInputG = document.getElementById('messageInputGroup');

const forOwner = document.getElementById('forOwner');
const successInviteToGroup = document.getElementById('successInviteToGroup');

initGroups();

function initGroups() {
    document.getElementById('btnCreateGroup').addEventListener('click', () => {
        page1.style.display = 'none';
        page2.style.display = 'block';
    });

    document.getElementById('saveGroup').addEventListener('click', createGroup);
    document.getElementById('backToPage1').addEventListener('click', () => switchPage(page2, page1));
    document.getElementById('backToPage1FromChat').addEventListener('click', () => {switchPage(page3, page1); currentGroupId = null});

    sendMessageGroupBtn.addEventListener('click', sendMessageGroup);

    setInterval(loadMessagesGroup, 5000);
}

function switchPage(fromPage, toPage) {
    fromPage.style.display = 'none';
    toPage.style.display = 'block';
}

function isUserAtBottom() {
    const threshold = 5;
    return (messageDivGroup.scrollHeight - messageDivGroup.scrollTop - messageDivGroup.clientHeight) < threshold;
}

async function createGroup() {
    const groupName = document.getElementById('groupName').value.trim();
    const groupDescription = document.getElementById('groupDescription').value.trim();

    if (!groupName || !groupDescription) {
        alert('Заполните все поля для создания группы.');
        return;
    }

    const data = {
        type: 'createGroup',
        groupName,
        groupDescription
    };

    try {
        const result = await postData(data);

        // TODO: Дописать нормальную обработку (result.success / ошибка и т.д.)

        document.getElementById('groupName').value = '';
        document.getElementById('groupDescription').value = '';

        switchPage(page2, page1);
        getMyGroups();
    } catch (error) {
        console.error('Error createGroup:', error);
    }
}

async function getMyGroups(anyway = false) {
    if (openPage !== 'groups' && !anyway)
        return;

    const data = {
        type: 'getMyGroups'
    };

    try {
        const result = await postData(data);
        //groupList.innerHTML = '';

        if (result.success) {
            groups = result.groups;

            if (groups.length > lastGroupCount) {
                displayMyGroups();

                lastGroupCount = groups.length;
            }
        } else {
            groupList.innerHTML = '<p>Ошибка загрузки групп</p>';
        }
    } catch (error) {
        console.error('Error getMyGroups:', error);
    }
    console.log(groups);
}

function displayMyGroups() {
    for (let i = lastGroupCount; i < groups.length; i++) {
        groups[i].lastMessageCount = 0;
        groups[i].messages = [];

        const group = groups[i];

        const listItem = createElementWithClass('div', 'element-group-list');

        const nameGroup = createElementWithClass('strong');
        nameGroup.textContent = group.group_name;

        listItem.addEventListener('click', () => {
            groupTitle.textContent = group.group_name;
            currentGroupId = group.group_id;

            // Проверяем, если пользователь владелец группы
            if (group.is_owner) {
                forOwner.style.display = 'flex';
                // Чтобы не навешивать много раз, сначала уберём старый обработчик
                inviteToGroupBtn.removeEventListener('click', handleInviteClick);
                inviteToGroupBtn.addEventListener('click', handleInviteClick);

                function handleInviteClick() {
                    const loginForInvite = loginForInviteInput.value.trim();
                    if (!loginForInvite) {
                        alert('Введите логин, чтобы пригласить пользователя');
                        return;
                    }
                    inviteUserToGroup(currentGroupId, loginForInvite);
                    loginForInviteInput.value = '';
                }
            } else {
                forOwner.style.display = 'none';
            }

            // Изначально при входе в группу устанавливаем флаг на прокрутку в самый низ
            isInitialLoadGroup = true;
            lastMessageCountGroup = 0; // Обнуляем подсчёт сообщений в текущей группе

            console.log('click');
            displayMessagesGroup();
            loadMessagesGroup();
            switchPage(page1, page3);
        });
        
        listItem.appendChild(nameGroup);
        groupList.appendChild(listItem);
    }
}

async function inviteUserToGroup(groupId, login) {
    const data = {
        type: 'addToGroup',
        groupId,
        login
    };

    try {
        const result = await postData(data);
        
        function processing(text, color) {
            successInviteToGroup.innerText = text;
            successInviteToGroup.style.display = 'block';
            successInviteToGroup.style.color = color;

            setTimeout(() => {
                successInviteToGroup.style.display = 'none';
            }, 1500);
        }

        if (result.success) {
            processing('Успешно добавлен', 'green');
        } else {
            if (result.message === 'Failed to add user to group')
                processing('Ошибка', 'red');
            else if (result.message === 'User is already a member of the group')
                processing('Уже есть', 'red');
            else if (result.message === 'User not found')
                processing('Не найден', 'red');
        }

    } catch (error) {
        console.error('Error inviteUserToGroup:', error);
    }
}

async function sendMessageGroup() {
    const message = messageInputG.value.trim();

    if (!message) {
        alert('Пожалуйста, введите сообщение.');
        return;
    }

    const data = {
        type: 'sendToGroup',
        groupId: currentGroupId,
        message
    };

    try {
        const result = await postData(data);

        if (result.message === 'success') {
            messageInputG.value = '';
            loadMessagesGroup();

            sendNotifications(currentGroupId, message, result.token);
        } else {
            alert('Ошибка при отправке сообщения.');
        }
    } catch (error) {
        console.error('Error sendMessageGroup:', error);
    }
}

async function sendNotifications(groupId, message, token) {
    const data = {
        type: 'sendNotification',
        groupId,
        token,
        message
    };

    try {
        const result = await postData(data);
    } catch (error) {
        console.error('Error sendNotifications:', error);
    }
}

function findGroup(groupId) {
    for (let i = 0; i < groups.length; i++) {
        if (groups[i].group_id == groupId)
            return i;
    }
}

async function loadMessagesGroup() {
    if (!currentGroupId) return;

    const groupId = findGroup(currentGroupId);
    
    const data = {
        type: 'getGroupMessages',
        groupId: currentGroupId,
        startFromIdMessage: groups[findGroup(currentGroupId)].messages.length > 0 ? Number(groups[findGroup(currentGroupId)].messages[groups[findGroup(currentGroupId)].messages.length - 1].id) + 1 : 1
    };

    try {
        const result = await postData(data);

        if (result.message === 'success') {
            groups[findGroup(currentGroupId)].messages = [...groups[findGroup(currentGroupId)].messages, ...result.data];

            if (groups[findGroup(currentGroupId)].messages.length > groups[findGroup(currentGroupId)].lastMessageCount) {
                const userAtBottom = isUserAtBottom();

                displayMessagesGroup();
                
                if (userAtBottom)
                    scrollToBottom(messageDivGroup);

                groups[findGroup(currentGroupId)].lastMessageCount = groups[findGroup(currentGroupId)].messages.length;
            }

            isInitialLoadGroup = false;
        } else {
            messageDivGroup.innerHTML = '<p>Нет сообщений.</p>';
        }
    } catch (error) {
        console.error('Error loadMessagesGroup:', error);
    }
}

function displayMessagesGroup() {
    messageDivGroup.innerHTML = '';

    for (let i = 0; i < groups[findGroup(currentGroupId)].messages.length; i++) {
        const msg = groups[findGroup(currentGroupId)].messages[i];

        const messageElement = createElementWithClass('div', 'message');

        const senderElement = document.createElement('strong');
        senderElement.textContent = msg.login + ': ';

        const textElement = document.createElement('span');
        textElement.textContent = msg.message;

        const timestampElement = document.createElement('div');
        timestampElement.style.fontSize = '0.8em';
        timestampElement.style.color = '#666';
        timestampElement.textContent = msg.created_at;

        messageElement.appendChild(senderElement);
        messageElement.appendChild(textElement);
        messageElement.appendChild(timestampElement);

        messageDivGroup.appendChild(messageElement);
    }

    console.log(isInitialLoadGroup);
    if (isInitialLoadGroup) {
        scrollToBottom(messageDivGroup);
    }
}