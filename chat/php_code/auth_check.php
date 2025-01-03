<?php
function auth_check()
{
    global $pdo;
    session_start();
    
    if (!empty($_SESSION['isLogin']) && $_SESSION['isLogin'] === true) {
        return json_encode([
            "isLogin" => true,
            "login"   => $_SESSION['login']
        ]);
    }

    if (!empty($_COOKIE['auth_token'])) {
        $token = $_COOKIE['auth_token'];
        
        // В более защищённом варианте: $tokenHash = hash('sha256', $token);
        $stmt = $pdo->prepare("SELECT id, login FROM users WHERE auth_token = ?");
        $stmt->execute([$token]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($user) {
            $_SESSION['isLogin'] = true;
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['login']   = viginerDecipher($user['login']);

            return json_encode([
                "isLogin" => true,
                "login"   => viginerDecipher($user['login'])
            ]);
        }
    }

    return json_encode([
        "isLogin" => false
    ]);
}
?>