<?php
header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['number'])) {
    $number = intval($_POST['number']);
    
    // Увеличиваем число на 2
    $result = 2 * $number;
    
    // Возвращаем результат в формате JSON
    echo json_encode(['result' => $result]);
    exit;
}

echo json_encode(['error' => 'Invalid request']);
exit;
?>