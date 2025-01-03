<?php
function viginerCipher($text, $key = '12KEYqwertyPEY89') 
{
    $allowedChars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyzАБВГДЕЁЖЗИЙКЛМНОПРСТУФХЦЧШЩЪЫЬЭЮЯабвгдеёжзийклмнопрстуфхцчшщъыьэюя0123456789.,!?;:-()[]{} _=+*';
    
    $outputText = "";
    $keyLength = mb_strlen($key, 'UTF-8');
    $allowedCharsLength = mb_strlen($allowedChars, 'UTF-8');

    for ($i = 0; $i < mb_strlen($text, 'UTF-8'); $i++) {
        $textChar = mb_substr($text, $i, 1, 'UTF-8');
        $keyChar = mb_substr($key, $i % $keyLength, 1, 'UTF-8');
        
        $textIndex = mb_strpos($allowedChars, $textChar, 0, 'UTF-8');
        $keyIndex = mb_strpos($allowedChars, $keyChar, 0, 'UTF-8');

        if ($textIndex === false || $keyIndex === false) {
            $outputText .= $textChar;
        } else {
            $cipherIndex = ($textIndex + $keyIndex) % $allowedCharsLength;
            $outputText .= mb_substr($allowedChars, $cipherIndex, 1, 'UTF-8');
        }
    }
    
    return $outputText;
}

function viginerDecipher($cipherText, $key = '12KEYqwertyPEY89') {
    $allowedChars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyzАБВГДЕЁЖЗИЙКЛМНОПРСТУФХЦЧШЩЪЫЬЭЮЯабвгдеёжзийклмнопрстуфхцчшщъыьэюя0123456789.,!?;:-()[]{} _=+*';
    
    $outputText = '';
    $keyLength = mb_strlen($key, 'UTF-8');
    $allowedCharsLength = mb_strlen($allowedChars, 'UTF-8');
    $cipherLength = mb_strlen($cipherText, 'UTF-8');

    for ($i = 0; $i < $cipherLength; $i++) {
        $currentChar = mb_substr($cipherText, $i, 1, 'UTF-8');
        $currentKeyChar = mb_substr($key, $i % $keyLength, 1, 'UTF-8');
        
        $cipherIndex = mb_strpos($allowedChars, $currentChar, 0, 'UTF-8');
        $keyIndex = mb_strpos($allowedChars, $currentKeyChar, 0, 'UTF-8');

        if ($cipherIndex === false || $keyIndex === false) {
            $outputText .= $currentChar;
        } else {
            $textIndex = ($cipherIndex - $keyIndex + $allowedCharsLength) % $allowedCharsLength;
            $outputText .= mb_substr($allowedChars, $textIndex, 1, 'UTF-8');
        }
    }
    
    return $outputText;
}
?>