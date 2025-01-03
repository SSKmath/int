<?php
// Подключаем assistants
include_once "assistants/db.php";
include_once "assistants/viginer.php";
include_once "assistants/protect.php";

// Подключаем handlers
include_once "handlers/check_session.php";
include_once "handlers/telegram_bot.php";
include_once "handlers/update_activity.php";
include_once "handlers/auth_check.php";
include_once "handlers/register.php";
include_once "handlers/send_message.php";
include_once "handlers/get_messages.php";
include_once "handlers/get_online_count.php";
include_once "handlers/send_friend_request.php";
include_once "handlers/view_friend_requests.php";
include_once "handlers/friend_request.php";
include_once "handlers/delete_account.php";
include_once "handlers/change_password.php";
include_once "handlers/groups.php";

function router($data)
{
    if (isset($data["update_id"]))
        return telegram_bot($data);
    
    if (!isset($data["type"]))
        return json_encode(["success" => false, "message" => "not found data['type]"]);

    switch ($data["type"])
    {
        case "checkAuth":
            return auth_check($data);
        case "reg":
            return register_user($data);
        case "log":
            return login_user($data);
        case "send":
            return send_message($data);
        case "getPersonalMessages":
            return get_messages($data);
        case "logged_out":
            return logout_user($data);
        case "getCountOnline":
            return get_online_count($data);
        case "updateActivity":
            return update_activity($data);
        case "sendFriendRequest":
            return send_friend_request($data);
        case "viewFriendRequests":
            return view_friend_requests($data);
        case "acceptFriendRequest":
            return accept_friend_request($data);
        case "declineFriendRequest":
            return decline_friend_request($data);
        case "getFriends":
            return get_friends($data);
        case "deleteFriend":
            return delete_friend($data);
        case "deleteAccount":
            return delete_account($data);
        case "changePassword":
            return change_password($data);
        case "createGroup":
            return create_group($data);
        case "getMyGroups":
            return get_my_groups($data);
        case "addToGroup":
            return add_member_to_group($data);
        case "sendToGroup":
            return send_to_group($data);
        case "sendNotification":
            return send_notifications_to_group($data);
        case "getGroupMessages":
            return get_group_messages($data);
        default:
            return json_encode(["success" => false, "message" => "wrong data['type']"]);
    }
}
?>