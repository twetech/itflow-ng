<?php

/*
 * ajax/ajax.php
 * Similar to post.php, but for requests using Asynchronous JavaScript
 * Always returns data in JSON format, unless otherwise specified
 */

require_once "/var/www/nestogy/includes/tenant_db.php";

require_once "/var/www/nestogy/includes/config/config.php";

require_once "/var/www/nestogy/includes/functions/functions.php";

require_once "/var/www/nestogy/includes/check_login.php";

require_once "/var/www/nestogy/includes/rfc6238.php";




/*
 * Fetches SSL certificates from remote hosts & returns the relevant info (issuer, expiry, public key)
 */
if (isset($_GET['certificate_fetch_parse_json_details'])) {
    // PHP doesn't appreciate attempting SSL sockets to non-existent domains
    if (empty($_GET['domain'])) {
        exit();
    }
    $domain = $_GET['domain'];

    // FQDNs in database shouldn't have a URL scheme, adding one
    $domain = "https://".$domain;

    // Parse host and port
    $url = parse_url($domain, PHP_URL_HOST);
    $port = parse_url($domain, PHP_URL_PORT);
    // Default port
    if (!$port) {
        $port = "443";
    }

    // Get certificate (using verify peer false to allow for self-signed certs)
    $socket = "ssl://$url:$port";
    $get = stream_context_create(array("ssl" => array("capture_peer_cert" => true, "verify_peer" => false,)));
    $read = stream_socket_client($socket, $errno, $errstr, 30, STREAM_CLIENT_CONNECT, $get);
    $cert = stream_context_get_params($read);
    $cert_public_key_obj = openssl_x509_parse($cert['options']['ssl']['peer_certificate']);
    openssl_x509_export($cert['options']['ssl']['peer_certificate'], $export);

    // Process data
    if ($cert_public_key_obj) {
        $response['success'] = "TRUE";
        $response['expire'] = date('Y-m-d', $cert_public_key_obj['validTo_time_t']);
        $response['issued_by'] = strip_tags($cert_public_key_obj['issuer']['O']);
        $response['public_key'] = $export; //nl2br
    } else {
        $response['success'] = "FALSE";
    }

    echo json_encode($response);

}

/*
 * Looks up info for a given certificate ID from the database, used to dynamically populate modal fields
 */
if (isset($_GET['certificate_get_json_details'])) {
    validateTechRole();

    $certificate_id = intval($_GET['certificate_id']);
    $client_id = intval($_GET['client_id']);

    // Individual certificate lookup
    $cert_sql = mysqli_query($mysqli,
    "SELECT * FROM certificates
    WHERE certificate_id = $certificate_id AND certificate_client_id = $client_id");
    while ($row = mysqli_fetch_array($cert_sql)) {
        $response['certificate'][] = $row;
    }

    // Get all domains for this client that could be linked to this certificate
    $domains_sql = mysqli_query($mysqli,
        "SELECT domain_id, domain_name FROM domains
        WHERE domain_client_id = $client_id"
    );
    while ($row = mysqli_fetch_array($domains_sql)) {
        $response['domains'][] = $row;
    }

    echo json_encode($response);
}

/*
 * Looks up info for a given domain ID from the database, used to dynamically populate modal fields
 */
if (isset($_GET['domain_get_json_details'])) {
    validateTechRole();

    $domain_id = intval($_GET['domain_id']);
    $client_id = intval($_GET['client_id']);

    // Individual domain lookup
    $cert_sql = mysqli_query($mysqli,
        "SELECT * FROM domains
        WHERE domain_id = $domain_id
        AND domain_client_id = $client_id
    ");
    while ($row = mysqli_fetch_array($cert_sql)) {
        $response['domain'][] = $row;
    }

    // Get all registrars/webhosts (vendors) for this client that could be linked to this domain
    $vendor_sql = mysqli_query($mysqli,
        "SELECT vendor_id, vendor_name FROM vendors
        WHERE vendor_client_id = $client_id
    ");
    while ($row = mysqli_fetch_array($vendor_sql)) {
        $response['vendors'][] = $row;
    }

    echo json_encode($response);
}

/*
 * Looks up info on the ticket number provided, used to populate the ticket merge modal
 */
if (isset($_GET['merge_ticket_get_json_details'])) {
    validateTechRole();

    $merge_into_ticket_number = intval($_GET['merge_into_ticket_number']);

    $sql = mysqli_query($mysqli,
        "SELECT
            ticket_id,
            ticket_number,
            ticket_prefix,
            ticket_subject,
            ticket_priority,
            ticket_status,
            client_name,
            contact_name
        FROM tickets
        LEFT JOIN clients ON ticket_client_id = client_id
        LEFT JOIN contacts ON ticket_contact_id = contact_id
        WHERE ticket_number = $merge_into_ticket_number
    ");

    if (mysqli_num_rows($sql) == 0) {
        //Do nothing.
    } else {
        //Return ticket, client and contact details for the given ticket number
        $response = mysqli_fetch_array($sql);

        echo json_encode($response);
    }
}

/*
 * Looks up info for a given network ID from the database, used to dynamically populate modal fields
 */
if (isset($_GET['network_get_json_details'])) {
    validateTechRole();

    $network_id = intval($_GET['network_id']);
    $client_id = intval($_GET['client_id']);

    // Individual network lookup
    $network_sql = mysqli_query($mysqli,
        "SELECT * FROM networks
        WHERE network_id = $network_id
        AND network_client_id = $client_id
    ");
    while ($row = mysqli_fetch_array($network_sql)) {
        $response['network'][] = $row;
    }

    // Lookup all client locations, as networks can be associated with any client location
    $locations_sql = mysqli_query(
        $mysqli,
        "SELECT location_id, location_name FROM locations
        WHERE location_client_id = '$client_id'"
    );
    while ($row = mysqli_fetch_array($locations_sql)) {
        $response['locations'][] = $row;
    }

    echo json_encode($response);
}

if (isset($_POST['client_set_notes'])) {
    $client_id = intval($_POST['client_id']);
    $notes = sanitizeInput($_POST['notes']);

    // Update notes
    mysqli_query($mysqli, "UPDATE clients SET client_notes = '$notes' WHERE client_id = $client_id");

    // Logging
    mysqli_query($mysqli,
        "INSERT INTO logs SET
            log_type = 'Client',
            log_action = 'Modify',
            log_description = '$name modified client notes',
            log_ip = '$ip',
            log_user_agent = '$user_agent',
            log_client_id = $client_id,
            log_user_id = $user_id
        ");

}

if (isset($_POST['contact_set_notes'])) {
    $contact_id = intval($_POST['contact_id']);
    $notes = sanitizeInput($_POST['notes']);

    // Update notes
    mysqli_query($mysqli, "UPDATE contacts SET contact_notes = '$notes' WHERE contact_id = $contact_id");

    // Logging
    mysqli_query($mysqli,
        "INSERT INTO logs SET
            log_type = 'Contact',
            log_action = 'Modify',
            log_description = '$name modified contact notes',
            log_ip = '$ip',
            log_user_agent = '$user_agent',
            log_user_id = $user_id
        ");

}

if (isset($_POST['asset_set_notes'])) {
    $asset_id = intval($_POST['asset_id']);
    $notes = sanitizeInput($_POST['notes']);

    // Update notes
    mysqli_query($mysqli, "UPDATE assets SET asset_notes = '$notes' WHERE asset_id = $asset_id");

    // Logging
    mysqli_query($mysqli,
        "INSERT INTO logs SET
            log_type = 'Assets',
            log_action = 'Modify',
            log_description = '$name modified asset notes',
            log_ip = '$ip',
            log_user_agent = '$user_agent',
            log_user_id = $user_id
        ");

}

/*
 * Collision Detection/Avoidance
 * Called upon loading a ticket, and every 2 mins thereafter
 * Is used in conjunction with ticket_query_views to show who is currently viewing a ticket
 */
if (isset($_GET['ticket_add_view'])) {
    $ticket_id = intval($_GET['ticket_id']);

    mysqli_query($mysqli,
        "INSERT INTO ticket_views SET
            view_ticket_id = $ticket_id,
            view_user_id = $user_id,
            view_timestamp = NOW()
        ");
}

/*
 * Collision Detection/Avoidance
 * Returns formatted text of the agents currently viewing a ticket
 * Called upon loading a ticket, and every 2 mins thereafter
 */
if (isset($_GET['ticket_query_views'])) {
    $ticket_id = intval($_GET['ticket_id']);

    $query = mysqli_query($mysqli,
        "SELECT user_name FROM ticket_views
        LEFT JOIN users ON view_user_id = user_id
        WHERE view_ticket_id = $ticket_id
        AND view_user_id != $user_id
        AND view_timestamp > DATE_SUB(NOW(), INTERVAL 2 MINUTE)
    ");
    while ($row = mysqli_fetch_array($query)) {
        $users[] = $row['user_name'];
    }

    if (!empty($users)) {
        $users = array_unique($users);
        if (count($users) > 1) {
            // Multiple viewers
            $response['message'] = "<i class='fas fa-fw fa-eye mr-2'></i>" . nullable_htmlentities(implode(", ", $users) . " are viewing this ticket.");
        } else {
            // Single viewer
            $response['message'] = "<i class='fas fa-fw fa-eye mr-2'></i>" . nullable_htmlentities(implode("", $users) . " is viewing this ticket.");
        }
    } else {
        // No viewers
        $response['message'] = "";
    }

    echo json_encode($response);
}

/*
 * Generates public/guest links for sharing logins/docs
 */
if (isset($_GET['share_generate_link'])) {
    validateTechRole();

    $item_encrypted_username = '';  // Default empty
    $item_encrypted_credential = '';  // Default empty

    $client_id = intval($_GET['client_id']);
    $item_type = sanitizeInput($_GET['type']);
    $item_id = intval($_GET['id']);
    $item_email = sanitizeInput($_GET['contact_email']);
    $item_note = sanitizeInput($_GET['note']);
    $item_view_limit = intval($_GET['views']);
    $item_expires = sanitizeInput($_GET['expires']);
    $item_expires_friendly = "never"; // default never
    if ($item_expires == "30 MINUTE") {
        $item_expires_friendly = "30 minutes";
    } elseif ($item_expires == "24 HOUR") {
        $item_expires_friendly = "24 hours";
    } elseif ($item_expires == "72 HOUR") {
        $item_expires_friendly = "72 hours (3 days)";
    }

    $item_key = randomString(156);

    if ($item_type == "Document") {
        $row = mysqli_fetch_array(mysqli_query($mysqli,
            "SELECT document_name FROM documents
            WHERE document_id = $item_id AND document_client_id = $client_id LIMIT 1
        "));
        $item_name = sanitizeInput($row['document_name']);
    }

    if ($item_type == "File") {
        $row = mysqli_fetch_array(mysqli_query($mysqli,
            "SELECT file_name FROM files WHERE file_id = $item_id AND file_client_id = $client_id LIMIT 1"
        ));
        $item_name = sanitizeInput($row['file_name']);
    }

    if ($item_type == "Login") {
        $login = mysqli_query($mysqli,
            "SELECT login_name, login_username, login_password FROM logins
            WHERE login_id = $item_id AND login_client_id = $client_id LIMIT 1
        ");
        $row = mysqli_fetch_array($login);

        $item_name = sanitizeInput($row['login_name']);

        // Decrypt & re-encrypt username/password for sharing
        $login_encryption_key = randomString();

        $login_username_cleartext = decryptLoginEntry($row['login_username']);
        $iv = randomString();
        $username_ciphertext = openssl_encrypt($login_username_cleartext, 'aes-128-cbc', $login_encryption_key, 0, $iv);
        $item_encrypted_username = $iv . $username_ciphertext;

        $login_password_cleartext = decryptLoginEntry($row['login_password']);
        $iv = randomString();
        $password_ciphertext = openssl_encrypt($login_password_cleartext, 'aes-128-cbc', $login_encryption_key, 0, $iv);
        $item_encrypted_credential = $iv . $password_ciphertext;
    }

    // Insert entry into DB
    $sql = mysqli_query($mysqli,
        "INSERT INTO shared_items SET
            item_active = 1,
            item_key = '$item_key',
            item_type = '$item_type',
            item_related_id = $item_id,
            item_encrypted_username = '$item_encrypted_username',
            item_encrypted_credential = '$item_encrypted_credential',
            item_note = '$item_note',
            item_views = 0,
            item_view_limit = $item_view_limit,
            item_expire_at = NOW() + INTERVAL + $item_expires,
            item_client_id = $client_id
        ");
    $share_id = $mysqli->insert_id;

    // Return URL
    if ($item_type == "Login") {
        $url = "https://$config_base_url/portal/guest_view_item.php?id=$share_id&key=$item_key&ek=$login_encryption_key";
    }
    else {
        $url = "https://$config_base_url/portal/guest_view_item.php?id=$share_id&key=$item_key";
    }

    $sql = mysqli_query($mysqli,"SELECT * FROM companies WHERE company_id = 1");
    $row = mysqli_fetch_array($sql);
    $company_name = sanitizeInput($row['company_name']);
    $company_phone = sanitizeInput(formatPhoneNumber($row['company_phone']));

    // Sanitize Config vars from get_settings.php
    $config_ticket_from_name = sanitizeInput($config_ticket_from_name);
    $config_ticket_from_email = sanitizeInput($config_ticket_from_email);
    $config_mail_from_name = sanitizeInput($config_mail_from_name);
    $config_mail_from_email = sanitizeInput($config_mail_from_email);

    // Send user e-mail, if specified
    if(!empty($config_smtp_host) && filter_var($item_email, FILTER_VALIDATE_EMAIL)){

        $subject = "Time sensitive - $company_name secure link enclosed";
        if ($item_expires_friendly == "never") {
            $subject = "$company_name secure link enclosed";
        }
        $body = "Hello,
            <br>
            <br>
            $name from $company_name sent you a time sensitive secure link regarding \"$item_name\".
            <br>
            <br>
            The link will expire in <strong>$item_expires_friendly</strong> and may only be viewed <strong>$item_view_limit</strong> times, before the link is destroyed.
            <br>
            <br>
            <strong><a href=\'$url\'>Click here to access your secure content</a></strong>
            <br>
            <br>
            --
            <br>
            $company_name - Support
            <br>
            $config_ticket_from_email
            <br>
            $company_phone
        ";

        $data = [
            [
                'from' => $config_mail_from_email,
                'from_name' => $config_mail_from_name,
                'recipient' => $item_email,
                'recipient_name' => $item_email,
                'subject' => $subject,
                'body' => $body
            ]
        ];

        $mail = addToMailQueue($mysqli, $data);

        if ($mail !== true) {
            mysqli_query($mysqli,
                "INSERT INTO notifications SET
                    notification_type = 'Mail',
                    notification = 'Failed to send email to $item_email'
                ");
            mysqli_query($mysqli,
                "INSERT INTO logs SET
                    log_type = 'Mail',
                    log_action = 'Error',
                    log_description = 'Failed to send email to $item_email regarding $subject. $item_mail',
                    log_ip = '$ip',
                    log_user_agent = '$user_agent',
                    log_user_id = $user_id"
                );
        }

    }

    echo json_encode($url);

    // Logging
    mysqli_query($mysqli,
    "INSERT INTO logs SET
        log_type = 'Sharing',
        log_action = 'Create',
        log_description = '$name created shared link for $item_type - $item_name',
        log_client_id = $client_id,
        log_ip = '$ip',
        log_user_agent = '$user_agent',
        log_user_id = $user_id
    ");

}

/*
 *  Looks up info for a given recurring (was scheduled) ticket ID from the database,
 *  Used to dynamically populate modal edit fields
 */
if (isset($_GET['recurring_ticket_get_json_details'])) {
    validateTechRole();

    $client_id = intval($_GET['client_id']);
    $ticket_id = intval($_GET['ticket_id']);

    // Get all contacts, to allow tickets to be raised under a specific contact
    $contact_sql = mysqli_query($mysqli, "SELECT contact_id, contact_name FROM contacts
    WHERE contact_client_id = $client_id
    AND contact_archived_at IS NULL
    ORDER BY contact_primary DESC, contact_technical DESC, contact_name ASC"
    );
    while ($row = mysqli_fetch_array($contact_sql)) {
        $response['contacts'][] = $row;
    }

    // Get ticket details
    $ticket_sql = mysqli_query($mysqli, "SELECT * FROM scheduled_tickets
    WHERE scheduled_ticket_id = $ticket_id
    AND scheduled_ticket_client_id = $client_id LIMIT 1");
    while ($row = mysqli_fetch_array($ticket_sql)) {
        $response['ticket'][] = $row;
    }

    // Get assets
    $asset_sql = mysqli_query($mysqli,
        "SELECT asset_id, asset_name FROM assets
        WHERE asset_client_id = $client_id AND asset_archived_at IS NULL
    ");
    while ($row = mysqli_fetch_array($asset_sql)) {
        $response['assets'][] = $row;
    }

    // Get technicians to auto assign the ticket to
    $sql_agents = mysqli_query(
        $mysqli,
        "SELECT users.user_id, user_name FROM users
            LEFT JOIN user_settings on users.user_id = user_settings.user_id
            WHERE user_role > 1
            AND user_status = 1
            AND user_archived_at IS NULL
            ORDER BY user_name ASC"
    );
    while ($row = mysqli_fetch_array($sql_agents)) {
        $response['agents'][] = $row;
    }

    echo json_encode($response);

}

/*
 * Looks up info for a given quote ID from the database, used to dynamically populate modal fields
 */
if (isset($_GET['quote_get_json_details'])) {
    $quote_id = intval($_GET['quote_id']);

    // Get quote details
    $quote_sql = mysqli_query(
        $mysqli,
        "SELECT * FROM quotes
        LEFT JOIN clients ON quote_client_id = client_id
        WHERE quote_id = $quote_id LIMIT 1"
    );

    while ($row = mysqli_fetch_array($quote_sql)) {
        $response['quote'][] = $row;
    }


    // Get all income-related categories for quoting
    $quote_created_at = $response['quote'][0]['quote_created_at'];
    $category_sql = mysqli_query(
        $mysqli,
        "SELECT category_id, category_name FROM categories
        WHERE category_type = 'Income' AND (category_archived_at > '$quote_created_at' OR category_archived_at IS NULL)
        ORDER BY category_name"
    );

    while ($row = mysqli_fetch_array($category_sql)) {
        $response['categories'][] = $row;
    }

    echo json_encode($response);

}

/*
 * Returns sorted list of active clients
 */
if (isset($_GET['get_active_clients'])) {

    $client_sql = mysqli_query(
        $mysqli,
        "SELECT client_id, client_name FROM clients
        WHERE client_archived_at IS NULL
        ORDER BY client_accessed_at DESC"
    );

    while ($row = mysqli_fetch_array($client_sql)) {
        $response['clients'][] = $row;
    }

    echo json_encode($response);
}

/*
 * Returns ordered list of active contacts for a specified client
 */
if (isset($_GET['get_client_contacts'])) {
    $client_id = intval($_GET['client_id']);

    $contact_sql = mysqli_query(
        $mysqli,
        "SELECT contact_id, contact_name, contact_primary, contact_important, contact_technical FROM contacts
        WHERE contacts.contact_archived_at IS NULL AND contact_client_id = $client_id
        ORDER BY contact_primary DESC, contact_technical DESC, contact_important DESC, contact_name"
    );

    while ($row = mysqli_fetch_array($contact_sql)) {
        $response['contacts'][] = $row;
    }

    echo json_encode($response);
}

/*
 * Dynamic TOTP "resolver"
 * When provided with a TOTP secret, returns a 6-digit code
 * // TODO: Check if this can now be removed
 */
if (isset($_GET['get_totp_token'])) {
    $otp = TokenAuth6238::getTokenCode(strtoupper($_GET['totp_secret']));

    echo json_encode($otp);
}

/*
 * NEW TOTP getter for client login/passwords page
 * When provided with a login ID, checks permissions and returns the 6-digit code
 */
if (isset($_GET['get_totp_token_via_id'])) {
    validateTechRole();

    $login_id = intval($_GET['login_id']);

    $sql = mysqli_fetch_assoc(mysqli_query($mysqli,
        "SELECT login_name, login_otp_secret, login_client_id FROM logins WHERE login_id = $login_id"
    ));
    $name = sanitizeInput($sql['login_name']);
    $totp_secret = $sql['login_otp_secret'];
    $client_id = intval($sql['login_client_id']);

    $otp = TokenAuth6238::getTokenCode(strtoupper($totp_secret));
    echo json_encode($otp);

    // Logging
    //  Only log the TOTP view if the user hasn't already viewed this specific login entry recently,
    //  This prevents logs filling if a user hovers across an entry a few times
    $check_recent_totp_view_logged_sql = mysqli_fetch_assoc(mysqli_query($mysqli,
        "SELECT COUNT(log_id) AS recent_totp_view FROM logs
        WHERE log_type = 'Login'
        AND log_action = 'View TOTP'
        AND log_user_id = $user_id
        AND log_entity_id = $login_id
        AND log_client_id = $client_id
        AND log_created_at > (NOW() - INTERVAL 5 MINUTE)
    "));
    $recent_totp_view_logged_count = intval($check_recent_totp_view_logged_sql['recent_totp_view']);

    if ($recent_totp_view_logged_count == 0) {
        mysqli_query($mysqli,
        "INSERT INTO logs SET
            log_type = 'Login',
            log_action = 'View TOTP',
            log_description = '$name viewed login TOTP code for $name',
            log_ip = '$ip',
            log_user_agent = '$user_agent',
            log_client_id = $client_id,
            log_user_id = $user_id,
            log_entity_id = $login_id
        ");
    }
}

if (isset($_GET['get_readable_pass'])) {
    echo json_encode(GenerateReadablePassword(4));
}

if (isset($_GET['get_modal'])) {
    //Assume modal is an array of modals
    $modal = $_GET['get_modal'];
    $item_id = $_GET['item_id'];
    foreach ($modal as $modal_name) {

        require_once "/var/www/nestogy/includes/modals/$modal_name.php";
    }
    

}

if (isset($_GET['search'])) {

    $category = isset($_GET['category']) ? sanitizeInput($_GET['category']) : null;
    $search = sanitizeInput($_GET['search']);
    if ($category == 'pages') {
        $pages = ['clients', 'contacts', 'tickets', 'documents', 'logins', 'ticket_replies', 'assets', 'invoices'];
        $response = [];
        foreach ($pages as $page) {
            $response[] = [
                'name' => ucfirst($page),
                'url' => '/public/?page=' . $page
            ];
        }
    } else {

        $sql = "SELECT SQL_NO_CACHE * FROM";
        $client_sql = "clients WHERE (client_name LIKE '%$search%' OR client_type LIKE '%$search%' OR client_notes LIKE '%$search%') AND client_archived_at IS NULL ";
        $contact_sql = "contacts, clients WHERE (contact_name LIKE '%$search%' OR contact_email LIKE '%$search%' OR contact_phone LIKE '%$search%' OR contact_mobile LIKE '%$search%' OR contact_notes LIKE '%$search%') AND contacts.contact_client_id = clients.client_id AND contacts.contact_archived_at IS NULL";
        $ticket_sql = "tickets WHERE ticket_subject LIKE '%$search%' OR ticket_number LIKE '%$search%' OR ticket_details LIKE '%$search%'";
        $document_sql = "documents WHERE document_name LIKE '%$search%'";
        $login_sql = "logins WHERE (login_name LIKE '%$search%' or login_uri LIKE '%$search%') AND login_archived_at IS NULL";
        $ticket_reply_sql = "ticket_replies WHERE reply_content LIKE '%$search%'";
        $asset_sql = "assets WHERE asset_name LIKE '%$search%' or asset_notes LIKE '%$search%' or asset_description LIKE '%$search%' OR asset_serial LIKE '%$search%' OR asset_mac LIKE '%$search%' or asset_make LIKE '%$search%' or asset_model LIKE '%$search%'";
        $invoice_sql = "invoices WHERE invoice_number LIKE '%$search%' OR invoice_description LIKE '%$search%'";
        $location_sql = "locations LEFT JOIN clients ON location_client_id = client_id WHERE location_name LIKE '%$search%' OR location_address LIKE '%$search%' OR location_city LIKE '%$search%' OR location_state LIKE '%$search%' OR location_zip LIKE '%$search%' OR client_name LIKE '%$search%'";

        switch ($category) {
            case "clients":
                $sql = $sql . " $client_sql";
                $url = "/public/?page=client&action=show&client_id=";
                break;
            case "contacts":
                $sql = $sql . " $contact_sql";
                $url = "/public/?page=contact&client_id=";
                break;
            case "tickets":
                $sql = $sql . " $ticket_sql";
                $url = "/public/?page=ticket&action=show&ticket_id=";
                break;
            case "documents":
                $sql = $sql . " $document_sql";
                $url = "/public/?page=documentaion&documentation_type=document&client_id=";
                break;
            case "logins":
                $sql = $sql . " $login_sql";
                $url = "/public/?page=documentation&documentation_type=login&client_id=";
                break;
            case "ticket_replies":
                $sql = $sql . " $ticket_reply_sql";
                $url = "/public/?page=ticket&action=show&ticket_id=";
                break;
            case "assets":
                $sql = $sql . " $asset_sql";
                $url = "/public/?page=documentation&documentation_type=asset&client_id=";
                break;
            case "invoices":
                $sql = $sql . " $invoice_sql";
                $url = "/public/?page=invoice&action=show&invoice_id=";
                break;
            case "locations":
                $sql = $sql . " $location_sql";
                $url = "/public/?page=location&action=show&location_id=";
                break;
            default:
                $sql = $sql . " $client_sql UNION $contact_sql UNION $ticket_sql UNION $document_sql UNION $login_sql UNION $ticket_reply_sql UNION $asset_sql";
        }

        $result = mysqli_query($mysqli, $sql);

        // build an array of onjects, each object is a row from the query
        $response = [];
        while ($row = mysqli_fetch_array($result)) {
            $url_id = $row['client_id'] ?? $row['contact_id'] ?? $row['ticket_id'] ?? $row['document_id'] ?? $row['login_id'] ?? $row['reply_id'] ?? $row['asset_id'];
            error_log($url_id);
            $response[] = [
                'name' => $row['contact_name'] ?? $row['ticket_subject'] ?? $row['document_name'] ?? $row['login_name'] ?? $row['reply_content'] ?? $row['asset_name'] ?? $row['client_name'],
                'id' => $row['client_id'] ?? $row['client_id'] ?? $row['ticket_id'] ?? $row['document_id'] ?? $row['login_id'] ?? $row['reply_id'] ?? $row['asset_id'],
                'url' => $url . $url_id
                
            ];
        }
    }

    echo json_encode($response);
}

if (isset($_GET['plaid_link_token'])) {
// Create a link token
    $create_link_token_url = "https://production.plaid.com/link/token/create";

    $data = [
        'client_id' => "$config_plaid_client_id",
        'secret' => "$config_plaid_secret",
        'client_name' => 'ITFlow',
        'country_codes' => ['US'],
        'language' => 'en',
        'user' => [
            'client_user_id' => '1'
        ],
        'products' => ['transactions'],
        'transactions' => [
            'days_requested' => 730
        ]
    ];

    // send the request using php curl
    $ch = curl_init($create_link_token_url);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
    ]);

    $result = curl_exec($ch);
    curl_close($ch);

    //if account is specified via get, save the link token to the database
    if (isset($_GET['account_id'])) {
        $account_id = $_GET['account_id'];
        $link_token = $result['link_token'];
        $sql = mysqli_query($mysqli, "UPDATE plaid_accounts SET plaid_link_token = '$link_token' 
        LEFT JOIN accounts ON plaid_accounts.plaid_account_id = accounts.plaid_id
        WHERE accounts.account_id = $account_id");
    }
    
    echo $result;
}

if (isset($_GET['client_invoices'])) {
    $client_id = intval($_GET['client_invoices']);
    $response = [];

    $sql = mysqli_query($mysqli,
        "SELECT invoice_id, invoice_date, invoice_due, invoice_status, invoice_number FROM invoices
        WHERE invoice_client_id = $client_id
        ORDER BY invoice_date ASC"
    );

    $client_currency_sql = mysqli_query($mysqli,
        "SELECT client_currency_code FROM clients
        WHERE client_id = $client_id"
    );
    $client_currency = mysqli_fetch_array($client_currency_sql)['client_currency_code'];

    while ($row = mysqli_fetch_array($sql)) {
        // get the invoice balance
        $invoice_id = $row['invoice_id'];
        $invoice_balance = getInvoiceBalance($invoice_id);
        $row['invoice_balance'] = numfmt_format_currency($currency_format, $invoice_balance, $client_currency);
        //format the amount
        $amount = numfmt_format_currency($currency_format, $row['invoice_amount'], $client_currency);
        $row['invoice_amount'] = $amount;

        $response[] = $row;
    }

    echo json_encode($response);
}

if (isset($_GET['apply_payment'])) {
    $data = file_get_contents('php://input');
    $data = json_decode($data, true);

    $invoices_amount = 0;
    
    $payment_amount = $data['payment_amount'];
    $payment_date = $data['payment_date'];
    $payment_method = $data['payment_method'];
    $payment_reference = $data['payment_reference'];
    $payment_account = $data['payment_account'];
    $credit = $data['credit'];
    $link_payment_to_transaction = $data['link_payment_to_transaction'];

    if ($credit) {
        //Create a credit
        
    }

    foreach ($data['invoices'] as $invoice) {
        createPayment([
            'invoice_id' => $invoice['invoice_id'],
            'amount' => $invoice['invoice_payment_amount'],
            'date' => $payment_date,
            'method' => $payment_method,
            'reference' => $payment_reference,
            'account' => $payment_account,
            'balance' => $invoice['invoice_payment_amount'],
            'link_to_transaction' => $link_payment_to_transaction
        ]);
    }
    //success
    echo json_encode(['success' => true]);
}

if (isset($_GET['client_credits'])) {
    $client_id = intval($_GET['client_credits']);
    $response = [];

    $sql = mysqli_query($mysqli, "SELECT * FROM credits WHERE credit_client_id = $client_id");
    while ($row = mysqli_fetch_array($sql)) {
        $response[] = $row;
    }
    echo json_encode($response);
}

if (isset($_GET['create_credit'])) {
    $data = json_decode(file_get_contents('php://input'), true);
    
    error_log("Received data: " . print_r($data, true)); // Log the received data

    if (!is_null($data)) {
        $credit_amount = isset($data['credit_amount']) ? (float)$data['credit_amount'] : 0.00;
        $credit_date = isset($data['credit_date']) ? $data['credit_date'] : '';
        $credit_description = isset($data['credit_description']) ? $data['credit_description'] : '';
        $credit_notes = isset($data['credit_notes']) ? $data['credit_notes'] : '';
        $client_id = isset($data['client_id']) ? (int)$data['client_id'] : 0;
        $invoice_id = isset($data['invoice_id']) ? (int)$data['invoice_id'] : 0;

        error_log("credit_amount: $credit_amount, credit_date: $credit_date, client_id: $client_id"); // Log individual fields

        if ($credit_amount > 0 && $credit_date != '' && $client_id > 0) {
            $sql = mysqli_query($mysqli,
                "INSERT INTO credits SET
                    credit_amount = $credit_amount,
                    credit_currency_code = 'USD',
                    credit_date = '$credit_date',
                    credit_reference = '$credit_description',
                    credit_client_id = $client_id"
            );

            if ($sql) {
                // Assuming these session variables are set properly
                $name = mysqli_real_escape_string($mysqli, $_SESSION['name']);
                $ip = mysqli_real_escape_string($mysqli, $_SERVER['REMOTE_ADDR']);
                $user_agent = mysqli_real_escape_string($mysqli, $_SERVER['HTTP_USER_AGENT']);
                $user_id = (int)$_SESSION['user_id'];

                mysqli_query($mysqli,
                    "INSERT INTO logs SET
                        log_type = 'Credit',
                        log_action = 'Create',
                        log_description = '$name created a credit for invoice $invoice_id',
                        log_ip = '$ip',
                        log_user_agent = '$user_agent',
                        log_client_id = $client_id,
                        log_user_id = $user_id"
                );

                echo json_encode("Credit created successfully");
            } else {
                echo json_encode("Error creating credit: " . mysqli_error($mysqli));
            }
        } else {
            echo json_encode("Invalid input data");
        }
    } else {
        echo json_encode("No input data");
    }
}

if (isset($_GET['save_subscription'])) {
    //get the data from the request
    $data = file_get_contents('php://input');
    $data = json_decode($data, true);
    $endpoint = $data['endpoint'];
    $public_key = $data['keys']['p256dh'];
    $auth_key = $data['keys']['auth'];
    error_log("Received data: " . print_r($data, true)); // Log the received data

    $sql = mysqli_query($mysqli,
        "INSERT INTO notification_subscriptions SET
            notification_subscription_user_id = $user_id,
            notification_subscription_endpoint = '$endpoint',
            notification_subscription_public_key = '$public_key',
            notification_subscription_auth_key = '$auth_key'
        "
    );
    
    //return http status 200
    http_response_code(200);
    echo json_encode(['success' => true]);
}

if (isset($_GET['send_invoice_email'])) {
    $invoice_id = intval($_GET['send_invoice_email']);
    emailInvoice($invoice_id);
    echo json_encode(['success' => true]);
}

if (isset($_GET['cancel_invoice'])) {
    $invoice_id = intval($_GET['cancel_invoice']);
    updateInvoiceStatus($invoice_id, "Cancelled");
    echo json_encode(['success' => true]);
}

if (isset($_GET['save_access_token'])) {
    $account_id = $_GET['account_id'];
    $data = json_decode(file_get_contents('php://input'), true);
    $public_token = $data['public_token'];

    // Exchange the public_token for an access_token
    $exchange_url = "https://production.plaid.com/item/public_token/exchange";
    $post_data = [
        'client_id' => $config_plaid_client_id,
        'secret' => $config_plaid_secret,
        'public_token' => $public_token
    ];

    $ch = curl_init($exchange_url);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($post_data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
    ]);

    $result = curl_exec($ch);
    curl_close($ch);

    $result = json_decode($result, true);

    if (isset($result['access_token'])) {
        $access_token = $result['access_token'];
        $item_id = $result['item_id'];


        // Insert the access token in the database
        mysqli_query($mysqli, "INSERT INTO plaid_accounts (plaid_access_token, plaid_account_id) VALUES ('$access_token', '$item_id')");
        mysqli_query($mysqli, "UPDATE accounts SET plaid_id = '$item_id' WHERE account_id = $account_id");

    } else {
        echo json_encode(['success' => false, 'error' => 'Could not exchange public_token']);
    }
}

if (isset($_GET['sync_plaid_transactions'])) {
    $account_id = $_GET['account_id'];
    //Get the info from the database
    $sql = mysqli_query($mysqli, "SELECT * FROM accounts LEFT JOIN plaid_accounts ON accounts.plaid_id = plaid_accounts.plaid_account_id WHERE account_id = $account_id");
    
    $account = mysqli_fetch_array($sql);
    $next_cursor = $account['plaid_next_cursor'];

    $response = syncPlaidTransactions($account_id, $next_cursor);

    if ($response['success']) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => $response['error']]);
    }

}

if (isset($_GET['send_invoice_email'])) {
    $invoice_id = intval($_GET['send_invoice_email']);
    emailInvoice($invoice_id);
    echo json_encode(['success' => true]);
}