<?php
function check_session()
{
    global $pdo;
    session_start();
    
    if (!isset($_SESSION['user_id']))
    {
        session_unset();
        session_destroy();
        header("Location: https://1rpo.ru");
        exit;
    }

    $userId = $_SESSION['user_id'];

    // Проверьте, существует ли активная сессия для пользователя
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM sessions WHERE user_id = ?");
    $stmt->execute([$userId]);
    $isValidSession = $stmt->fetchColumn() > 0;

    if (!$isValidSession) {
        // Если сессия недействительна, перенаправьте на страницу входа
        session_unset();
        session_destroy();
        header("Location: https://1rpo.ru");
        exit;
    }
}
?>