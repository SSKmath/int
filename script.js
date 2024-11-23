document.getElementById('convertButton').addEventListener('click', function() {
    // Получаем текст из поля ввода
    const inputText = document.getElementById('textInput').value;

    // Преобразуем текст в верхний регистр
    const upperCaseText = inputText.toUpperCase();

    // Выводим результат на экран
    document.getElementById('outputText').innerText = upperCaseText;
});