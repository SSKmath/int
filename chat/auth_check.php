<?php
function auth_check($data)
{
    session_start();

    if (isset($_SESSION['isLogin']) && $_SESSION['isLogin'] === true)
    {
        return json_encode([
            "isLogin" => true,
            "login" => $_SESSION['login']
        ]);
    }
    else
    {
        return json_encode([
            "isLogin" => false
        ]);
    }
}
?>