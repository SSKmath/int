const cipherText = document.getElementById('cipherText');
const solvingЕquations = document.getElementById('solvingЕquations');
const chat = document.getElementById('chat');

function init() {
    cipherText.addEventListener('click', () => {
        window.location.href = 'https://1rpo.ru/pages/cipherText/index.html';
    });

    solvingЕquations.addEventListener('click', () => {
        window.location.href = 'https://1rpo.ru/pages/solvingEquations/index.html';
    });

    chat.addEventListener('click', () => {
        window.location.href = 'https://1rpo.ru/pages/chat/index.html';
    });
}

init();