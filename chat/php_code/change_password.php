<?php
function change_password($data)
{
    check_session();
    global $pdo;
    session_start();

    $currentPassword = $data["currentPassword"];
    $newPassword = $data["newPassword"];
    $user_id = $_SESSION["user_id"];

    $stmt = $pdo->prepare("SELECT password FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($result)
    {
        if (password_verify($currentPassword, $result["password"]))
        {
            $hashedNewPassword = password_hash($newPassword, PASSWORD_DEFAULT);

            // Обновляем пароль в базе данных
            $updateStmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
            if ($updateStmt->execute([$hashedNewPassword, $user_id])) {
                $stmt = $pdo->prepare("DELETE FROM sessions WHERE user_id = ?");
                $stmt->execute([$user_id]);

                session_unset();
                session_destroy();

                return json_encode(["message" => "password_changed_successfully"]);
            } else {
                return json_encode(["message" => "password_change_failed"]);
            }
        }
        else
        {
            return json_encode(["message" => "wrong_password"]);
        }
    }
    else
        return json_encode(["message" => "user_not_found"]);
}
?>