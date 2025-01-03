<?php
function f4($data)
{
    $currentUser = $data['recipient'];

    if (empty($currentUser))
        return json_encode(["message" => "invalid_input"]);

    $csvFilePath = 'chat/messages.csv';

    if (!file_exists($csvFilePath))
        return json_encode(["message" => "no_messages"]);

    $messages = [];
    $rows = array_map('str_getcsv', file($csvFilePath));
    $header = array_shift($rows); // Убираем заголовок

    foreach ($rows as $row) {
        list($sender, $recipientRow, $message, $timestamp) = $row;
        if ($currentUser === $currentUser)
        {
            $messages[] = [
                "sender" => "test",
                "recipient" => $recipientRow,
                "message" => $message,
                "timestamp" => $timestamp
            ];
        }
    }

    return json_encode(["message" => "success", "data" => $messages]);
}
?>