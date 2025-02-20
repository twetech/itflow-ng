<?php

require_once "/var/www/nestogy/includes/tenant_db.php";

require_once "/var/www/nestogy/includes/config/config.php";

require_once "/var/www/nestogy/includes/functions/functions.php";


session_start();

if (isset($_GET['accept_quote'], $_GET['url_key'])) {

    $quote_id = intval($_GET['accept_quote']);
    $url_key = sanitizeInput($_GET['url_key']);

    $sql = mysqli_query($mysqli, "SELECT * FROM quotes WHERE quote_id = $quote_id AND quote_url_key = '$url_key'");

    if (mysqli_num_rows($sql) == 1) {

        mysqli_query($mysqli, "UPDATE quotes SET quote_status = 'Accepted' WHERE quote_id = $quote_id");

        mysqli_query($mysqli, "INSERT INTO history SET history_status = 'Accepted', history_description = 'Client accepted Quote!', history_quote_id = $quote_id");

        $_SESSION['alert_message'] = "Quote Accepted";

        header("Location: " . $_SERVER["HTTP_REFERER"]);
    } else {
        echo "Invalid!!";
    }

}

if (isset($_GET['decline_quote'], $_GET['url_key'])) {

    $quote_id = intval($_GET['decline_quote']);
    $url_key = sanitizeInput($_GET['url_key']);

    $sql = mysqli_query($mysqli, "SELECT * FROM quotes WHERE quote_id = $quote_id AND quote_url_key = '$url_key'");

    if (mysqli_num_rows($sql) == 1) {

        mysqli_query($mysqli, "UPDATE quotes SET quote_status = 'Declined' WHERE quote_id = $quote_id");

        mysqli_query($mysqli, "INSERT INTO history SET history_status = 'Declined', history_description = 'Client declined Quote!', history_quote_id = $quote_id");

        $_SESSION['alert_type'] = "danger";
        $_SESSION['alert_message'] = "Quote Declined";

        header("Location: " . $_SERVER["HTTP_REFERER"]);
    } else {
        echo "Invalid!!";
    }

}

