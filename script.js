const move = 4;

document.getElementById('convertButton1').addEventListener('click', function() {
    let inputText = document.getElementById('textarea1').value;
    let outputText = "";
    
    for (let i = 0; i < inputText.length; i++)
    {
        let code = inputText.charCodeAt(i);
        outputText += String.fromCharCode(code + move);
    }

    document.getElementById('textarea3').innerText = outputText;
});

document.getElementById('convertButton2').addEventListener('click', function() {
    const inputText = document.getElementById('textarea2').value;
    let outputText = "";
    
    for (let i = 0; i < inputText.length; i++)
    {
        let code = inputText.charCodeAt(i);
        outputText += String.fromCharCode(code - move);
    }

    document.getElementById('textarea4').innerText = outputText;
});