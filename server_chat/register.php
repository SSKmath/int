<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(array("status" => "error", "message" => "Method not allowed"));
    exit;
}

$data = json_decode(file_get_contents("php://input"), true);

// Проверка на наличие данных
if (!isset($data['name']) || !isset($data['age'])) {
    echo json_encode(array("status" => "error", "message" => "Invalid input"));
    exit;
}

// Обработка данных
$name = $data['name'];
$age = $data['age'];

// Ответ клиенту
$response = array("status" => "success", "name" => $name, "age" => $age);
echo json_encode($response);
exit;
?>