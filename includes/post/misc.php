<?php

global $mysqli, $name, $ip, $user_agent, $user_id;


/*
 * ITFlow - GET/POST request handler for misc (functionality that doesn't quite fit elsewhere)
 */

// Records to show per page

if(isset($_POST['change_records_per_page'])){

    $records_per_page = intval($_POST['change_records_per_page']);

    mysqli_query($mysqli,"UPDATE user_settings SET user_config_records_per_page = $records_per_page WHERE user_id = $user_id");

    header("Location: " . $_SERVER["HTTP_REFERER"]);

}

if(isset($_POST['change_max_rows'])){

    $max_rows = intval($_POST['max_rows']);

    header("Location: /" . $SERVER["HTTP_REFERER"] . "&max_rows=$max_rows");
}

if (isset($_GET['dismiss_notification'])) {

    $notification_id = intval($_GET['dismiss_notification']);

    mysqli_query($mysqli,"UPDATE notifications SET notification_dismissed_at = NOW(), notification_dismissed_by = $user_id WHERE notification_id = $notification_id");

    //Logging
    mysqli_query($mysqli,"INSERT INTO logs SET log_type = 'Notification', log_action = 'Dismiss', log_description = '$name dismissed notification', log_ip = '$ip', log_user_agent = '$user_agent', log_user_id = $user_id");

    $_SESSION['alert_message'] = "Notification Dismissed";

    header("Location: " . $_SERVER["HTTP_REFERER"]);

}

if (isset($_GET['dismiss_all_notifications'])) {

    global $mysqli, $user_id, $name, $ip, $user_agent;

    $sql = mysqli_query($mysqli,"SELECT * FROM notifications WHERE notification_dismissed_at IS NULL");

    $num_notifications = mysqli_num_rows($sql);

    while($row = mysqli_fetch_array($sql)) {
        $notification_id = intval($row['notification_id']);
        $notification_dismissed_at = sanitizeInput($row['notification_dismissed_at']);

        mysqli_query($mysqli,"UPDATE notifications SET notification_dismissed_at = NOW(), notification_dismissed_by = $user_id WHERE notification_id = $notification_id");

    }

    //Logging
    mysqli_query($mysqli,"INSERT INTO logs SET log_type = 'Notification', log_action = 'Dismiss', log_description = '$name dismissed $num_notifications notifications', log_ip = '$ip', log_user_agent = '$user_agent', log_user_id = $user_id");

    $_SESSION['alert_message'] = "$num_notifications Notifications Dismissed";

    header("Location: " . $_SERVER["HTTP_REFERER"]);

}

// Revoke sharing (sharing itself is done via ajax.php)
if (isset($_GET['deactivate_shared_item'])) {

    validateAdminRole();

    $item_id = intval($_GET['deactivate_shared_item']);

    // Get details of the shared link
    $sql = mysqli_query($mysqli, "SELECT item_type, item_related_id, item_client_id FROM shared_items WHERE item_id = $item_id");
    $row = mysqli_fetch_array($sql);
    $item_type = sanitizeInput($row['item_type']);
    $item_related_id = intval($row['item_related_id']);
    $item_client_id = intval($row['item_client_id']);

    // Deactivate item id
    mysqli_query($mysqli, "DELETE FROM shared_items WHERE item_id = $item_id");

    // Logging
    mysqli_query($mysqli,"INSERT INTO logs SET log_type = 'Sharing', log_action = 'Delete', log_description = '$name deactivated shared $item_type link. Item ID: $item_related_id. Share ID $item_id', log_ip = '$ip', log_user_agent = '$user_agent', log_client_id = $item_client_id, log_user_id = $user_id, log_entity_id = $item_id");

    $_SESSION['alert_message'] = "Link deactivated";

    header("Location: " . $_SERVER["HTTP_REFERER"]);
}
