const allowedChars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyzАБВГДЕЁЖЗИЙКЛМНОПРСТУФХЦЧШЩЪЫЬЭЮЯабвгдеёжзийклмнопрстуфхцчшщъыьэюя0123456789.,!?;:"\'-()[]{} _=+*';

document.getElementById('convertButton1').addEventListener('click', function() {
    let inputText = document.getElementById('textarea1').value;
    let key = document.getElementById('key1').value;
    let outputText = "";
    
    if (inputText.length == 0)
    {
        alert("Введите текст");
        return;
    }

    if (key.length == 0)
    {
        alert("Введите ключ");
        return;
    }

    for (let i = 0; i < inputText.length; i++)
    {
        let textIndex = allowedChars.indexOf(inputText[i]);
        let keyIndex = allowedChars.indexOf(key[i % key.length]);
        if (textIndex === -1 || keyIndex === -1)
            outputText += inputText[i];
        else 
        {
            let cipherIndex = (textIndex + keyIndex) % allowedChars.length;
            outputText += allowedChars[cipherIndex];
        }
    }
    
    document.getElementById('textarea3').innerText = outputText;
});

document.getElementById('convertButton2').addEventListener('click', function() {
    let inputText = document.getElementById('textarea2').value;
    let key = document.getElementById('key2').value;
    let outputText = "";
    
    if (inputText.length == 0)
    {
        alert("Введите текст");
        return;
    }

    if (key.length == 0)
    {
        alert("Введите ключ");
        return;
    }

    for (let i = 0; i < inputText.length; i++)
    {
        let textIndex = allowedChars.indexOf(inputText[i]);
        let keyIndex = allowedChars.indexOf(key[i % key.length]);
        if (textIndex === -1 || keyIndex === -1)
            outputText += inputText[i];
        else 
        {
            let decipherIndex = (textIndex - keyIndex + allowedChars.length) % allowedChars.length;
            outputText += allowedChars[decipherIndex];
        }
    }

    document.getElementById('textarea4').innerText = outputText;
});

document.getElementById('convertButton3').addEventListener('click', function() {
    let inputText = document.getElementById('textarea5').value;
    let key = document.getElementById('key3').value;
    let outputText = "";
    
    if (inputText.length == 0)
    {
        alert("Введите текст");
        return;
    }

    if (key.length == 0)
    {
        alert("Введите ключ");
        return;
    }

    for (let i = 0; i < inputText.length; i++)
    {
        let textIndex = allowedChars.indexOf(inputText[i]);
        let keyIndex = allowedChars.indexOf(key[i % key.length]);
        if (textIndex === -1 || keyIndex === -1)
            outputText += inputText[i];
        else 
        {
            let cipherIndex = (textIndex + keyIndex) % allowedChars.length;
            outputText += allowedChars[cipherIndex];
        }
    }
    
    document.getElementById('textarea6').innerText = outputText;
});

document.getElementById('convertButton4').addEventListener('click', function() {
    let inputText = document.getElementById('textarea5').value;
    let key = document.getElementById('key3').value;
    let outputText = "";
    
    if (inputText.length == 0)
    {
        alert("Введите текст");
        return;
    }

    if (key.length == 0)
    {
        alert("Введите ключ");
        return;
    }

    for (let i = 0; i < inputText.length; i++)
    {
        let textIndex = allowedChars.indexOf(inputText[i]);
        let keyIndex = allowedChars.indexOf(key[i % key.length]);
        if (textIndex === -1 || keyIndex === -1)
            outputText += inputText[i];
        else 
        {
            let decipherIndex = (textIndex - keyIndex + allowedChars.length) % allowedChars.length;
            outputText += allowedChars[decipherIndex];
        }
    }

    document.getElementById('textarea6').innerText = outputText;
});


let changeMode = localStorage.getItem('changeMode') === 'true';

const oldContainers = document.getElementsByName('oldMod');
const newContainer = document.getElementById('newMod');

if (changeMode) {
    oldContainers[0].style.display = 'flex';
    oldContainers[1].style.display = 'flex';
    newContainer.style.display = 'none';
} 
else 
{
    oldContainers[0].style.display = 'none';
    oldContainers[1].style.display = 'none';
    newContainer.style.display = 'flex';
}

document.getElementById('changeButton').addEventListener('click', function() {
    changeMode = !changeMode;

    localStorage.setItem('changeMode', changeMode);
    
    if (changeMode) 
    {
        oldContainers[0].style.display = 'flex';
        oldContainers[1].style.display = 'flex';
        newContainer.style.display = 'none';
    } 
    else 
    {
        oldContainers[0].style.display = 'none';
        oldContainers[1].style.display = 'none';
        newContainer.style.display = 'flex';
    }
});