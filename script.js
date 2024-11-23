document.getElementById('convertButton1').addEventListener('click', function() {
    const inputText = document.getElementById('textInput1').value;
    const upperCaseText = inputText.toUpperCase();
    document.getElementById('outputText1').innerText = upperCaseText;
});

document.getElementById('convertButton2').addEventListener('click', function() {
    const inputText = document.getElementById('textInput2').value;
    const upperCaseText = inputText.toUpperCase();
    document.getElementById('outputText2').innerText = upperCaseText;
});