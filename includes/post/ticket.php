<?php

global $mysqli, $name, $ip, $user_agent, $user_id;


/*
 * ITFlow - GET/POST request handler for client tickets
 */

if (isset($_POST['add_ticket'])) {

    validateTechRole();

    $parameters = [
        'ticket_client_id' => intval($_POST['client_id']),
        'ticket_assigned_to' => intval($_POST['assigned_to']),
        'ticket_contact' => intval($_POST['contact']),
        'ticket_subject' => sanitizeInput($_POST['subject']),
        'ticket_priority' => sanitizeInput($_POST['priority']),
        'ticket_details' => mysqli_real_escape_string($mysqli, $_POST['details']),
        'ticket_vendor_ticket_number' => sanitizeInput($_POST['vendor_ticket_number']),
        'ticket_vendor' => intval($_POST['vendor_id']),
        'ticket_asset' => intval($_POST['asset_id']),
        'ticket_billable' => intval($_POST['billable']),
        'ticket_status' => 1,
        'ticket_use_primary_contact' => intval($_POST['use_primary_contact']),
    ];

    $return_data = createTicket($parameters);
    $ticket_id = $return_data['ticket_id'];
    referWithAlert($return_data['message'], $return_data['status'], "/public/?page=ticket&ticket_id=$ticket_id");
}

if (isset($_POST['edit_ticket'])) {
    $ticket = mysqli_query($mysqli, "SELECT * FROM tickets WHERE ticket_id = " . intval($_POST['ticket_id']));
    $row = mysqli_fetch_array($ticket);

    validateTechRole();

    $parameters = [];
    $parameters['ticket_id'] = intval($_POST['ticket_id']);

    if (isset($_POST['type']) && $_POST['type'] != $row['ticket_type']) {
        $parameters['ticket_type'] = sanitizeInput($_POST['type']);
    }
    if (isset($_POST['client_id']) && intval($_POST['client_id']) != $row['ticket_client_id']) {
        $parameters['ticket_client_id'] = intval($_POST['client_id']);
    }
    if (isset($_POST['number']) && intval($_POST['number']) != $row['ticket_number']) {
        $parameters['ticket_number'] = intval($_POST['number']);
    }
    if (isset($_POST['contact']) && intval($_POST['contact']) != $row['ticket_contact_id']) {
        $parameters['ticket_contact_id'] = intval($_POST['contact']);
    }
    if (isset($_POST['subject']) && $_POST['subject'] != $row['ticket_subject']) {
        $parameters['ticket_subject'] = sanitizeInput($_POST['subject']);
    }
    if (isset($_POST['billable']) && intval($_POST['billable']) != $row['ticket_billable']) {
        $parameters['ticket_billable'] = intval($_POST['billable']);
    }
    if (isset($_POST['priority']) && $_POST['priority'] != $row['ticket_priority']) {
        $parameters['ticket_priority'] = sanitizeInput($_POST['priority']);
    }
    if (isset($_POST['details']) && $_POST['details'] != $row['ticket_details']) {
        $parameters['ticket_details'] = mysqli_real_escape_string($mysqli, $_POST['details']);
    }
    if (isset($_POST['vendor_ticket_number']) && $_POST['vendor_ticket_number'] != $row['ticket_vendor_ticket_number']) {
        $parameters['ticket_vendor_ticket_number'] = sanitizeInput($_POST['vendor_ticket_number']);
    }
    if (isset($_POST['vendor_id']) && intval($_POST['vendor_id']) != $row['ticket_vendor_id']) {
        $parameters['ticket_vendor_id'] = intval($_POST['vendor_id']);
    }
    if (isset($_POST['asset_id']) && intval($_POST['asset_id']) != $row['ticket_asset_id']) {
        $parameters['ticket_asset_id'] = intval($_POST['asset_id']);
    }

    if ($parameters) {
        $return_data = updateTicket($parameters);
        referWithAlert($return_data['message'], $return_data['status']);
    } else {
        referWithAlert("No changes were made to the ticket.", "error");
    }
}

if (isset($_POST['edit_ticket_priority'])) {

    validateTechRole();

    $parameters = [
        'ticket_id' => intval($_POST['ticket_id']),
        'ticket_priority' => sanitizeInput($_POST['priority'])
    ];

    $return_data = updateTicket($parameters);
    referWithAlert($return_data['message'], $return_data['status']);
}

if (isset($_POST['edit_ticket_contact'])) {

    validateTechRole();

    $parameters = [
        'ticket_id' => intval($_POST['ticket_id']),
        'ticket_contact_id' => intval($_POST['contact'])
    ];

    $return_data = updateTicket($parameters);
    referWithAlert($return_data['message'], $return_data['status']);
}

if (isset($_POST['add_ticket_watcher'])) {

    validateTechRole();

    global $mysqli, $name, $ip, $user_agent, $user_id;

    $ticket_id = intval($_POST['ticket_id']);
    $client_id = intval($_POST['client_id']);
    $ticket_number = sanitizeInput($_POST['ticket_number']);
    $watcher_email = sanitizeInput($_POST['watcher_email']);

    mysqli_query($mysqli, "INSERT INTO ticket_watchers SET watcher_email = '$watcher_email', watcher_ticket_id = $ticket_id");

    //Logging
    mysqli_query($mysqli, "INSERT INTO logs SET log_type = 'Ticket', log_action = 'Edit', log_description = '$name added watcher $watcher_email to ticket $ticket_number', log_ip = '$ip', log_user_agent = '$user_agent', log_client_id = $client_id, log_user_id = $user_id, log_entity_id = $ticket_id");

    $_SESSION['alert_message'] = "You added $watcher_email as a watcher to Ticket <strong>$ticket_number</strong>";

    header("Location: " . $_SERVER["HTTP_REFERER"]);
}

if (isset($_POST['edit_ticket_watchers'])) {

    validateTechRole();

    $ticket_id = intval($_POST['ticket_id']);
    $client_id = intval($_POST['client_id']);
    $ticket_number = sanitizeInput($_POST['ticket_number']);

    // Add Watchers
    if (!empty($_POST['watchers'])) {

        // Remove all watchers first
        mysqli_query($mysqli, "DELETE FROM ticket_watchers WHERE watcher_ticket_id = $ticket_id");

        //Add the Watchers
        foreach ($_POST['watchers'] as $watcher) {
            $watcher_email = sanitizeInput($watcher);
            mysqli_query($mysqli, "INSERT INTO ticket_watchers SET watcher_email = '$watcher_email', watcher_ticket_id = $ticket_id");
        }
    }

    //Logging
    mysqli_query($mysqli, "INSERT INTO logs SET log_type = 'Ticket', log_action = 'Edit', log_description = '$name added watchers to ticket $ticket_number', log_ip = '$ip', log_user_agent = '$user_agent', log_client_id = $client_id, log_user_id = $user_id, log_entity_id = $ticket_id");

    $_SESSION['alert_message'] = "Ticket <strong>$ticket_number</strong> watchers updated";

    header("Location: " . $_SERVER["HTTP_REFERER"]);
}

if (isset($_GET['delete_ticket_watcher'])) {

    validateTechRole();

    $watcher_id = intval($_GET['delete_ticket_watcher']);

    mysqli_query($mysqli, "DELETE FROM ticket_watchers WHERE watcher_id = $watcher_id");


    $_SESSION['alert_message'] = "You <b>removed</b> a ticket watcher";

    header("Location: " . $_SERVER["HTTP_REFERER"]);
}

if (isset($_POST['edit_ticket_asset'])) {

    validateTechRole();

    $parameters = [
        'ticket_id' => intval($_POST['ticket_id']),
        'ticket_asset_id' => intval($_POST['ticket_asset_id'])
    ];

    $return_data = updateTicket($parameters);
    referWithAlert($return_data['message'], $return_data['status']);
}

if (isset($_POST['edit_ticket_vendor'])) {

    validateTechRole();

    $parameters = [
        'ticket_id' => intval($_POST['ticket_id']),
        'ticket_vendor_id' => intval($_POST['ticket_vendor_id'])
    ];

    $return_data = updateTicket($parameters);
    referWithAlert($return_data['message'], $return_data['status']);
}

if (isset($_POST['edit_ticket_priority'])) {

    validateTechRole();

    $parameters = [
        'ticket_id' => intval($_POST['ticket_id']),
        'ticket_priority' => sanitizeInput($_POST['ticket_priority'])
    ];

    $return_data = updateTicket($parameters);
    referWithAlert($return_data['message'], $return_data['status']);
}

if (isset($_POST['assign_ticket'])) {

    global $mysqli, $user_id, $name, $company_name, $ip, $user_agent, $config_smtp_host, $config_ticket_from_name, $config_ticket_from_email, $config_base_url;

    // Role check
    validateTechRole();

    // POST variables
    $ticket_id = intval($_POST['ticket_id']);
    $assigned_to = intval($_POST['assigned_to']);

    // Notify the user
    $data = [
        [//Agent Email Notification
            'from' => $config_ticket_from_email,
            'from_name' => $config_ticket_from_name,
            'recipient' => $agent_email,
            'recipient_name' => $agent_name,
            'subject' => "Ticket $ticket_number assigned to you",
            'body' => "Ticket $ticket_number has been assigned to you. Please login to view the ticket.",
        ],
        [//End user Notification
            'from' => $config_ticket_from_email,
            'from_name' => $config_ticket_from_name,
            'recipient' => $client_email,
            'recipient_name' => $client_name,
            'subject' => $subject,
            'body' => $body,
        ]
    ];

    // Update ticket
    $return_data = updateTicket(['ticket_id' => $ticket_id, 'ticket_assigned_to' => $assigned_to]);
    referWithAlert($return_data['message'], $return_data['status']);


}

if (isset($_GET['delete_ticket'])) {
    validateTechRole();
    $ticket_id = intval($_GET['delete_ticket']);
    $return_data = deleteTicket(['ticket_id' => $ticket_id]);
    referWithAlert($return_data['message'], $return_data['alert_type'], "/public/?page=tickets");
}

if (isset($_POST['bulk_assign_ticket'])) {

    // Role check
    validateTechRole();

    // POST variables
    $assign_to = intval($_POST['assign_to']);

    // Get a Ticket Count
    $ticket_count = count($_POST['ticket_ids']);

    // Assign Tech to Selected Tickets
    if (!empty($_POST['ticket_ids'])) {
        foreach ($_POST['ticket_ids'] as $ticket_id) {
            $ticket_id = intval($ticket_id);

            $sql = mysqli_query($mysqli, "SELECT * FROM tickets WHERE ticket_id = $ticket_id");
            $row = mysqli_fetch_array($sql);

            $ticket_prefix = sanitizeInput($row['ticket_prefix']);
            $ticket_number = intval($row['ticket_number']);
            $ticket_status = sanitizeInput($row['ticket_status']);
            $ticket_subject = sanitizeInput($row['ticket_subject']);
            $client_id = intval($row['ticket_client_id']);

            if ($ticket_status == 1 && $assigned_to !== 0) {
                $ticket_status = 2;
            }

            // Allow for un-assigning tickets
            if ($assign_to == 0) {
                $ticket_reply = "Ticket unassigned, pending re-assignment.";
                $agent_name = "No One";
            } else {
                // Get & verify assigned agent details
                $agent_details_sql = mysqli_query($mysqli, "SELECT user_name, user_email FROM users LEFT JOIN user_settings ON users.user_id = user_settings.user_id WHERE users.user_id = $assign_to AND user_settings.user_role > 1");
                $agent_details = mysqli_fetch_array($agent_details_sql);

                $agent_name = sanitizeInput($agent_details['user_name']);
                $agent_email = sanitizeInput($agent_details['user_email']);
                $ticket_reply = "Ticket re-assigned to $agent_name.";

                if (!$agent_name) {
                    $_SESSION['alert_type'] = "error";
                    $_SESSION['alert_message'] = "Invalid agent!";
                    header("Location: " . $_SERVER["HTTP_REFERER"]);
                    exit();
                }
            }

            // Update ticket & insert reply
            mysqli_query($mysqli, "UPDATE tickets SET ticket_assigned_to = $assign_to, ticket_status = '$ticket_status' WHERE ticket_id = $ticket_id");

            mysqli_query($mysqli, "INSERT INTO ticket_replies SET ticket_reply = '$ticket_reply', ticket_reply_type = 'Internal', ticket_reply_time_worked = '00:01:00', ticket_reply_by = $user_id, ticket_reply_ticket_id = $ticket_id");

            // Logging
            mysqli_query($mysqli, "INSERT INTO logs SET log_type = 'Ticket', log_action = 'Edit', log_description = '$name reassigned ticket $ticket_prefix$ticket_number - $ticket_subject to $agent_name', log_ip = '$ip', log_user_agent = '$user_agent', log_client_id = $client_id, log_user_id = $user_id, log_entity_id = $ticket_id");

            $tickets_assigned_body .= "$ticket_prefix$ticket_number - $ticket_subject<br>";
        } // End For Each Ticket ID Loop

        // Notification
        if ($user_id != $assign_to && $assign_to != 0) {

            // App Notification
            mysqli_query($mysqli, "INSERT INTO notifications SET notification_type = 'Ticket', notification = '$ticket_count Tickets have been assigned to you by $name', notification_action = 'tickets.php?status=Open&assigned=$assign_to', notification_client_id = $client_id, notification_user_id = $assign_to");

            // Agent Email Notification
            if (!empty($config_smtp_host)) {

                // Sanitize Config vars from get_settings.php
                $config_ticket_from_name = sanitizeInput($config_ticket_from_name);
                $config_ticket_from_email = sanitizeInput($config_ticket_from_email);
                $company_name = sanitizeInput($company_name);

                $subject = "$config_app_name - $ticket_count tickets have been assigned to you";
                $body = "Hi $agent_name, <br><br>$name assigned $ticket_count tickets to you!<br><br>$tickets_assigned_body<br>Thanks, <br>$name<br>$company_name";

                // Email Ticket Agent
                // Queue Mail
                $data = [
                    [
                        'from' => $config_ticket_from_email,
                        'from_name' => $config_ticket_from_name,
                        'recipient' => $agent_email,
                        'recipient_name' => $agent_name,
                        'subject' => $subject,
                        'body' => $body,
                    ]
                ];
                addToMailQueue($mysqli, $data);
            }
        }
    }

    $_SESSION['alert_message'] = "You assigned <b>$ticket_count</b> Tickets to <b>$agent_name</b>";

    header("Location: " . $_SERVER["HTTP_REFERER"]);
}

if (isset($_POST['bulk_edit_ticket_priority'])) {

    // Role check
    validateTechRole();

    // POST variables
    $priority = sanitizeInput($_POST['bulk_priority']);

    // Get a Ticket Count
    $ticket_count = count($_POST['ticket_ids']);

    // Assign Tech to Selected Tickets
    if (!empty($_POST['ticket_ids'])) {
        foreach ($_POST['ticket_ids'] as $ticket_id) {
            $ticket_id = intval($ticket_id);

            $sql = mysqli_query($mysqli, "SELECT * FROM tickets WHERE ticket_id = $ticket_id");
            $row = mysqli_fetch_array($sql);

            $ticket_prefix = sanitizeInput($row['ticket_prefix']);
            $ticket_number = intval($row['ticket_number']);
            $ticket_status = sanitizeInput($row['ticket_status']);
            $ticket_subject = sanitizeInput($row['ticket_subject']);
            $current_ticket_priority = sanitizeInput($row['ticket_priority']);
            $client_id = intval($row['ticket_client_id']);

            // Update ticket & insert reply
            mysqli_query($mysqli, "UPDATE tickets SET ticket_priority = '$priority' WHERE ticket_id = $ticket_id");

            mysqli_query($mysqli, "INSERT INTO ticket_replies SET ticket_reply = '$name updated the priority from $current_ticket_priority to $priority', ticket_reply_type = 'Internal', ticket_reply_time_worked = '00:01:00', ticket_reply_by = $user_id, ticket_reply_ticket_id = $ticket_id");

            // Logging
            mysqli_query($mysqli, "INSERT INTO logs SET log_type = 'Ticket', log_action = 'Edit', log_description = '$name updated the priority on ticket $ticket_prefix$ticket_number - $ticket_subject from $current_ticket_priority to $priority', log_ip = '$ip', log_user_agent = '$user_agent', log_client_id = $client_id, log_user_id = $user_id, log_entity_id = $ticket_id");
        } // End For Each Ticket ID Loop
    }

    $_SESSION['alert_message'] = "You updated the priority for <b>$ticket_count</b> Tickets to <b>$priority</b>";

    header("Location: " . $_SERVER["HTTP_REFERER"]);
}

if (isset($_POST['bulk_close_tickets'])) {

    // Role check
    validateTechRole();

    // POST variables
    $details = mysqli_escape_string($mysqli, $_POST['bulk_details']);
    $private_note = intval($_POST['bulk_private_note']);
    if ($private_note == 1) {
        $ticket_reply_type = 'Internal';
    } else {
        $ticket_reply_type = 'Public';
    }

    // Get a Ticket Count
    $ticket_count = count($_POST['ticket_ids']);

    // Assign Tech to Selected Tickets
    if (!empty($_POST['ticket_ids'])) {
        foreach ($_POST['ticket_ids'] as $ticket_id) {
            $ticket_id = intval($ticket_id);

            $sql = mysqli_query($mysqli, "SELECT * FROM tickets WHERE ticket_id = $ticket_id");
            $row = mysqli_fetch_array($sql);

            $ticket_prefix = sanitizeInput($row['ticket_prefix']);
            $ticket_number = intval($row['ticket_number']);
            $ticket_status = sanitizeInput($row['ticket_status']);
            $ticket_subject = sanitizeInput($row['ticket_subject']);
            $current_ticket_priority = sanitizeInput($row['ticket_priority']);
            $client_id = intval($row['ticket_client_id']);

            // Update ticket & insert reply
            mysqli_query($mysqli, "UPDATE tickets SET ticket_status = 'Closed' WHERE ticket_id = $ticket_id");

            mysqli_query($mysqli, "INSERT INTO ticket_replies SET ticket_reply = '$details', ticket_reply_type = '$ticket_reply_type', ticket_reply_time_worked = '00:01:00', ticket_reply_by = $user_id, ticket_reply_ticket_id = $ticket_id");

            // Logging
            mysqli_query($mysqli, "INSERT INTO logs SET log_type = 'Ticket', log_action = 'Close', log_description = '$name closed $ticket_prefix$ticket_number - $ticket_subject in a bulk action', log_ip = '$ip', log_user_agent = '$user_agent', log_client_id = $client_id, log_user_id = $user_id, log_entity_id = $ticket_id");

            // Client notification email
            if (!empty($config_smtp_host) && $config_ticket_client_general_notifications == 1 && $private_note == 0) {

                // Get Contact details
                $ticket_sql = mysqli_query($mysqli, "SELECT contact_name, contact_email FROM tickets 
                    LEFT JOIN contacts ON ticket_contact_id = contact_id
                    WHERE ticket_id = $ticket_id
                ");
                $row = mysqli_fetch_array($ticket_sql);

                $contact_name = sanitizeInput($row['contact_name']);
                $contact_email = sanitizeInput($row['contact_email']);

                // Sanitize Config vars from get_settings.php
                $from_name = sanitizeInput($config_ticket_from_name);
                $from_email = sanitizeInput($config_ticket_from_email);
                $base_url = sanitizeInput($config_base_url);

                // Get Company Info
                $sql = mysqli_query($mysqli, "SELECT company_name, company_phone FROM companies WHERE company_id = 1");
                $row = mysqli_fetch_array($sql);
                $company_name = sanitizeInput($row['company_name']);
                $company_phone = sanitizeInput(formatPhoneNumber($row['company_phone']));

                // Check email valid
                if (filter_var($contact_email, FILTER_VALIDATE_EMAIL)) {

                    $data = [];

                    $subject = "Ticket closed - [$ticket_prefix$ticket_number] - $ticket_subject | (do not reply)";
                    $body = "Hello $contact_name,<br><br>Your ticket regarding \"$ticket_subject\" has been closed.<br><br>$details<br><br> We hope the request/issue was resolved to your satisfaction. If you need further assistance, please raise a new ticket using the below details. Please do not reply to this email. <br><br>Ticket: $ticket_prefix$ticket_number<br>Subject: $ticket_subject<br>Portal: https://$base_url/portal/ticket.php?id=$ticket_id<br><br>--<br>$company_name - Support<br>$config_ticket_from_email<br>$company_phone";

                    // Email Ticket Contact
                    // Queue Mail

                    $data[] = [
                        'from' => $from_email,
                        'from_name' => $from_name,
                        'recipient' => $contact_email,
                        'recipient_name' => $contact_name,
                        'subject' => $subject,
                        'body' => $body
                    ];

                    // Also Email all the watchers
                    $sql_watchers = mysqli_query($mysqli, "SELECT watcher_email FROM ticket_watchers WHERE watcher_ticket_id = $ticket_id");
                    $body .= "<br><br>----------------------------------------<br>DO NOT REPLY - YOU ARE RECEIVING THIS EMAIL BECAUSE YOU ARE A WATCHER";
                    while ($row = mysqli_fetch_array($sql_watchers)) {
                        $watcher_email = sanitizeInput($row['watcher_email']);

                        // Queue Mail
                        $data[] = [
                            'from' => $from_email,
                            'from_name' => $from_name,
                            'recipient' => $watcher_email,
                            'recipient_name' => $watcher_email,
                            'subject' => $subject,
                            'body' => $body
                        ];
                    }
                }
                addToMailQueue($mysqli, $data);
            } // End Mail IF
        } // End Loop
    } // End Array Empty Check

    $_SESSION['alert_message'] = "You closed <b>$ticket_count</b> Tickets";

    header("Location: " . $_SERVER["HTTP_REFERER"]);
}

if (isset($_POST['bulk_ticket_reply'])) {

    // Role check
    validateTechRole();

    // POST variables
    $ticket_reply = mysqli_escape_string($mysqli, $_POST['bulk_reply_details']);
    $ticket_status = sanitizeInput($_POST['bulk_status']);
    $private_note = intval($_POST['bulk_private_reply']);
    if ($private_note == 1) {
        $ticket_reply_type = 'Internal';
    } else {
        $ticket_reply_type = 'Public';
    }

    // Get a Ticket Count
    $ticket_count = count($_POST['ticket_ids']);

    // Loop Through Tickets and Add Reply along with Email notifications
    if (!empty($_POST['ticket_ids'])) {
        foreach ($_POST['ticket_ids'] as $ticket_id) {
            $ticket_id = intval($ticket_id);

            $sql = mysqli_query($mysqli, "SELECT * FROM tickets WHERE ticket_id = $ticket_id");
            $row = mysqli_fetch_array($sql);

            $ticket_prefix = sanitizeInput($row['ticket_prefix']);
            $ticket_number = intval($row['ticket_number']);
            $ticket_subject = sanitizeInput($row['ticket_subject']);
            $current_ticket_priority = sanitizeInput($row['ticket_priority']);
            $client_id = intval($row['ticket_client_id']);

            // Add reply
            mysqli_query($mysqli, "INSERT INTO ticket_replies SET ticket_reply = '$ticket_reply', ticket_reply_time_worked = '00:01:00', ticket_reply_type = '$ticket_reply_type', ticket_reply_by = $user_id, ticket_reply_ticket_id = $ticket_id");

            $ticket_reply_id = mysqli_insert_id($mysqli);

            // Update Ticket Status
            mysqli_query($mysqli, "UPDATE tickets SET ticket_status = '$ticket_status' WHERE ticket_id = $ticket_id");

            // Logging
            mysqli_query($mysqli, "INSERT INTO logs SET log_type = 'Ticket Reply', log_action = 'Create', log_description = '$name replied to ticket $ticket_prefix$ticket_number - $ticket_subject and was a $ticket_reply_type reply', log_ip = '$ip', log_user_agent = '$user_agent', log_client_id = $client_id, log_user_id = $user_id, log_entity_id = $ticket_reply_id");

            // Get Contact Details
            $sql = mysqli_query(
                $mysqli,
                "SELECT contact_name, contact_email, ticket_created_by, ticket_assigned_to
                FROM tickets
                LEFT JOIN contacts ON ticket_contact_id = contact_id
                WHERE ticket_id = $ticket_id"
            );

            $row = mysqli_fetch_array($sql);

            $contact_name = sanitizeInput($row['contact_name']);
            $contact_email = sanitizeInput($row['contact_email']);
            $ticket_created_by = intval($row['ticket_created_by']);
            $ticket_assigned_to = intval($row['ticket_assigned_to']);

            // Sanitize Config vars from get_settings.php
            $from_name = sanitizeInput($config_ticket_from_name);
            $from_email = sanitizeInput($config_ticket_from_email);
            $base_url = sanitizeInput($config_base_url);

            $sql = mysqli_query($mysqli, "SELECT company_name, company_phone FROM companies WHERE company_id = 1");
            $row = mysqli_fetch_array($sql);
            $company_name = sanitizeInput($row['company_name']);
            $company_phone = sanitizeInput(formatPhoneNumber($row['company_phone']));

            // Send e-mail to client if public update & email is set up
            if ($private_note == 0 && !empty($config_smtp_host)) {

                if (filter_var($contact_email, FILTER_VALIDATE_EMAIL)) {

                    $subject = "Ticket update - [$ticket_prefix$ticket_number] - $ticket_subject";
                    $body = "<i style=\'color: #808080\'>##- Please type your reply above this line -##</i><br><br>Hello $contact_name,<br><br>Your ticket regarding $ticket_subject has been updated.<br><br>--------------------------------<br>$ticket_reply<br>--------------------------------<br><br>Ticket: $ticket_prefix$ticket_number<br>Subject: $ticket_subject<br>Status: $ticket_status<br>Portal: https://$base_url/portal/ticket.php?id=$ticket_id<br><br>--<br>$company_name - Support<br>$from_email<br>$company_phone";

                    $data = [];

                    // Email Ticket Contact
                    // Queue Mail
                    $data[] = [
                        'from' => $from_email,
                        'from_name' => $from_name,
                        'recipient' => $contact_email,
                        'recipient_name' => $contact_name,
                        'subject' => $subject,
                        'body' => $body
                    ];

                    // Also Email all the watchers
                    $sql_watchers = mysqli_query($mysqli, "SELECT watcher_email FROM ticket_watchers WHERE watcher_ticket_id = $ticket_id");
                    $body .= "<br><br>----------------------------------------<br>DO NOT REPLY - YOU ARE RECEIVING THIS EMAIL BECAUSE YOU ARE A WATCHER";
                    while ($row = mysqli_fetch_array($sql_watchers)) {
                        $watcher_email = sanitizeInput($row['watcher_email']);

                        // Queue Mail
                        $data[] = [
                            'from' => $from_email,
                            'from_name' => $from_name,
                            'recipient' => $watcher_email,
                            'recipient_name' => $watcher_email,
                            'subject' => $subject,
                            'body' => $body
                        ];
                    }
                }
                addToMailQueue($mysqli, $data);
            } //End Mail IF

            // Notification for assigned ticket user
            if ($user_id != $ticket_assigned_to && $ticket_assigned_to != 0) {

                mysqli_query($mysqli, "INSERT INTO notifications SET notification_type = 'Ticket', notification = '$name updated Ticket $ticket_prefix$ticket_number - Subject: $ticket_subject that is assigned to you', notification_action = 'ticket.php?ticket_id=$ticket_id', notification_client_id = $client_id, notification_user_id = $ticket_assigned_to");
            }

            // Notification for user that opened the ticket
            if ($user_id != $ticket_created_by && $ticket_created_by != 0) {

                mysqli_query($mysqli, "INSERT INTO notifications SET notification_type = 'Ticket', notification = '$name updated Ticket $ticket_prefix$ticket_number - Subject: $ticket_subject that you opened', notification_action = 'ticket.php?ticket_id=$ticket_id', notification_client_id = $client_id, notification_user_id = $ticket_created_by");
            }
        } // End Ticket Lopp

    }

    $_SESSION['alert_message'] = "You updated <b>$ticket_count</b> tickets";

    header("Location: " . $_SERVER["HTTP_REFERER"]);
}

if (isset($_POST['add_ticket_reply'])) {

    global $mysqli, $user_id, $name, $company_name, $ip, $user_agent, $config_smtp_host, $config_ticket_from_name, $config_ticket_from_email, $config_base_url;

    validateTechRole();

    $ticket_id = intval($_POST['ticket_id']);
    $ticket_reply = mysqli_real_escape_string($mysqli, $_POST['ticket_reply']);
    $ticket_status = sanitizeInput($_POST['status']);
    // Handle the time inputs for hours, minutes, and seconds
    $hours = intval($_POST['hours']);
    $minutes = intval($_POST['minutes']);
    $seconds = intval($_POST['seconds']);

    //var_dump($_POST);
    //exit;

    // Combine into a single time string
    $ticket_reply_time_worked = sanitizeInput(sprintf("%02d:%02d:%02d", $hours, $minutes, $seconds));

    $client_id = intval($_POST['client_id']);

    if (isset($_POST['public_reply_type'])) {
        $ticket_reply_type = 'Public';
    } else {
        $ticket_reply_type = 'Internal';
    }

    // Add reply
    mysqli_query($mysqli, "INSERT INTO ticket_replies SET ticket_reply = '$ticket_reply', ticket_reply_time_worked = '$ticket_reply_time_worked', ticket_reply_type = '$ticket_reply_type', ticket_reply_by = $user_id, ticket_reply_ticket_id = $ticket_id");

    $ticket_reply_id = mysqli_insert_id($mysqli);

    // Update Ticket Last Response Field
    mysqli_query($mysqli, "UPDATE tickets SET ticket_status = $ticket_status WHERE ticket_id = $ticket_id");

    if ($ticket_status == 5) {
        mysqli_query($mysqli, "UPDATE tickets SET ticket_closed_at = NOW() WHERE ticket_id = $ticket_id");
    }

    // Get Ticket Details
    $ticket_sql = mysqli_query($mysqli, "SELECT contact_name, contact_email, ticket_prefix, ticket_number, ticket_subject, ticket_client_id, ticket_created_by, ticket_assigned_to 
        FROM tickets 
        LEFT JOIN clients ON ticket_client_id = client_id 
        LEFT JOIN contacts ON ticket_contact_id = contact_id
        WHERE ticket_id = $ticket_id
    ");

    $row = mysqli_fetch_array($ticket_sql);

    $contact_name = sanitizeInput($row['contact_name']);
    $contact_email = sanitizeInput($row['contact_email']);
    $ticket_prefix = sanitizeInput($row['ticket_prefix']);
    $ticket_number = intval($row['ticket_number']);
    $ticket_subject = sanitizeInput($row['ticket_subject']);
    $client_id = intval($row['ticket_client_id']);
    $ticket_created_by = intval($row['ticket_created_by']);
    $ticket_assigned_to = intval($row['ticket_assigned_to']);

    // Sanitize Config vars from get_settings.php
    $config_ticket_from_name = sanitizeInput($config_ticket_from_name);
    $config_ticket_from_email = sanitizeInput($config_ticket_from_email);
    $config_base_url = sanitizeInput($config_base_url);

    $sql = mysqli_query($mysqli, "SELECT company_name, company_phone FROM companies WHERE company_id = 1");
    $row = mysqli_fetch_array($sql);
    $company_name = sanitizeInput($row['company_name']);
    $company_phone = sanitizeInput(formatPhoneNumber($row['company_phone']));

    // Send e-mail to client if public update & email is set up
    if ($ticket_reply_type == 'Public' && !empty($config_smtp_host)) {

        if (filter_var($contact_email, FILTER_VALIDATE_EMAIL)) {

            // Slightly different email subject/text depending on if this update closed the ticket or not

            if ($ticket_status == 5) {
                $subject = "Ticket closed - [$ticket_prefix$ticket_number] - $ticket_subject | (do not reply)";
                $body = "Hello $contact_name,<br><br>Your ticket regarding $ticket_subject has been closed.<br><br>--------------------------------<br>$ticket_reply<br>--------------------------------<br><br>We hope the request/issue was resolved to your satisfaction. If you need further assistance, please raise a new ticket using the below details. Please do not reply to this email. <br><br>Ticket: $ticket_prefix$ticket_number<br>Subject: $ticket_subject<br>Portal: https://$config_base_url/portal/ticket.php?id=$ticket_id<br><br>--<br>$company_name - Support<br>$config_ticket_from_email<br>$company_phone";
            } elseif ($ticket_status == 4) {
                $subject = "Ticket update - [$ticket_prefix$ticket_number] - $ticket_subject | (pending closure)";
                $body = "<i style=\'color: #808080\'>##- Please type your reply above this line -##</i><br><br>Hello $contact_name,<br><br>Your ticket regarding $ticket_subject has been updated and is pending closure.<br><br>--------------------------------<br>$ticket_reply<br>--------------------------------<br><br>If your request/issue is resolved, you can simply ignore this email. If you need further assistance, please respond to let us know!  <br><br>Ticket: $ticket_prefix$ticket_number<br>Subject: $ticket_subject<br>Status: $ticket_status<br>Portal: https://$config_base_url/portal/ticket.php?id=$ticket_id<br><br>--<br>$company_name - Support<br>$config_ticket_from_email<br>$company_phone";
            } else {
                $subject = "Ticket update - [$ticket_prefix$ticket_number] - $ticket_subject";
                $body = "<i style=\'color: #808080\'>##- Please type your reply above this line -##</i><br><br>Hello $contact_name,<br><br>Your ticket regarding $ticket_subject has been updated.<br><br>--------------------------------<br>$ticket_reply<br>--------------------------------<br><br>Ticket: $ticket_prefix$ticket_number<br>Subject: $ticket_subject<br>Status: $ticket_status<br>Portal: https://$config_base_url/portal/ticket.php?id=$ticket_id<br><br>--<br>$company_name - Support<br>$config_ticket_from_email<br>$company_phone";
            }

            $data = [];

            // Email Ticket Contact
            // Queue Mail
            $data[] = [
                'from' => $config_ticket_from_email,
                'from_name' => $config_ticket_from_name,
                'recipient' => $contact_email,
                'recipient_name' => $contact_name,
                'subject' => $subject,
                'body' => $body
            ];

            // Also Email all the watchers
            $sql_watchers = mysqli_query($mysqli, "SELECT watcher_email FROM ticket_watchers WHERE watcher_ticket_id = $ticket_id");
            $body .= "<br><br>----------------------------------------<br>DO NOT REPLY - YOU ARE RECEIVING THIS EMAIL BECAUSE YOU ARE A WATCHER";
            while ($row = mysqli_fetch_array($sql_watchers)) {
                $watcher_email = sanitizeInput($row['watcher_email']);

                // Queue Mail
                $data[] = [
                    'from' => $config_ticket_from_email,
                    'from_name' => $config_ticket_from_name,
                    'recipient' => $watcher_email,
                    'recipient_name' => $watcher_email,
                    'subject' => $subject,
                    'body' => $body
                ];
            }
            addToMailQueue($mysqli, $data);
        }
    }
    //End Mail IF

    // Notification for assigned ticket user
    if ($user_id != $ticket_assigned_to && $ticket_assigned_to != 0) {

        mysqli_query($mysqli, "INSERT INTO notifications SET notification_type = 'Ticket', notification = '$name updated Ticket $ticket_prefix$ticket_number - Subject: $ticket_subject that is assigned to you', notification_action = 'ticket.php?ticket_id=$ticket_id', notification_client_id = $client_id, notification_user_id = $ticket_assigned_to");
    }

    // Notification for user that opened the ticket
    if ($user_id != $ticket_created_by && $ticket_created_by != 0) {

        mysqli_query($mysqli, "INSERT INTO notifications SET notification_type = 'Ticket', notification = '$name updated Ticket $ticket_prefix$ticket_number - Subject: $ticket_subject that you opened', notification_action = 'ticket.php?ticket_id=$ticket_id', notification_client_id = $client_id, notification_user_id = $ticket_created_by");
    }

    // Logging
    mysqli_query($mysqli, "INSERT INTO logs SET log_type = 'Ticket Reply', log_action = 'Create', log_description = '$name replied to ticket $ticket_prefix$ticket_number - $ticket_subject and was a $ticket_reply_type reply', log_ip = '$ip', log_user_agent = '$user_agent', log_client_id = $client_id, log_user_id = $user_id, log_entity_id = $ticket_reply_id");

    $_SESSION['alert_message'] = "Ticket <strong>$ticket_prefix$ticket_number</strong> has been updated with your reply and was <strong>$ticket_reply_type</strong>";

    header("Location: " . $_SERVER["HTTP_REFERER"]);
}

if (isset($_POST['edit_ticket_reply'])) {

    validateTechRole();

    $ticket_reply_id = intval($_POST['ticket_reply_id']);
    $ticket_reply = mysqli_real_escape_string($mysqli, $_POST['ticket_reply']);
    $ticket_reply_time_worked = sanitizeInput($_POST['time']);

    $client_id = intval($_POST['client_id']);

    mysqli_query($mysqli, "UPDATE ticket_replies SET ticket_reply = '$ticket_reply', ticket_reply_time_worked = '$ticket_reply_time_worked' WHERE ticket_reply_id = $ticket_reply_id AND ticket_reply_type != 'Client'") or die(mysqli_error($mysqli));

    //Logging
    mysqli_query($mysqli, "INSERT INTO logs SET log_type = 'Ticket Reply', log_action = 'Modify', log_description = '$name modified ticket reply', log_ip = '$ip', log_user_agent = '$user_agent', log_client_id = $client_id, log_user_id = $user_id, log_entity_id = $ticket_reply_id");

    $_SESSION['alert_message'] = "Ticket reply updated";

    header("Location: " . $_SERVER["HTTP_REFERER"]);
}

if (isset($_GET['archive_ticket_reply'])) {

    validateAdminRole();

    $ticket_reply_id = intval($_GET['archive_ticket_reply']);

    mysqli_query($mysqli, "UPDATE ticket_replies SET ticket_reply_archived_at = NOW() WHERE ticket_reply_id = $ticket_reply_id");

    //Logging
    mysqli_query($mysqli, "INSERT INTO logs SET log_type = 'Ticket Reply', log_action = 'Archive', log_description = '$name arhived ticket reply', log_ip = '$ip', log_user_agent = '$user_agent', log_user_id = $user_id, log_entity_id = $ticket_reply_id");

    $_SESSION['alert_type'] = "error";
    $_SESSION['alert_message'] = "Ticket reply archived";

    header("Location: " . $_SERVER["HTTP_REFERER"]);
}

if (isset($_POST['merge_ticket'])) {

    validateTechRole();

    $ticket_id = intval($_POST['ticket_id']);
    $merge_into_ticket_number = intval($_POST['merge_into_ticket_number']);
    $merge_comment = sanitizeInput($_POST['merge_comment']);
    $ticket_reply_type = 'Internal';

    //Get current ticket details
    $sql = mysqli_query($mysqli, "SELECT ticket_prefix, ticket_number, ticket_subject, ticket_details FROM tickets WHERE ticket_id = $ticket_id");
    if (mysqli_num_rows($sql) == 0) {
        $_SESSION['alert_message'] = "No ticket with that ID found.";
        header("Location: " . $_SERVER["HTTP_REFERER"]);
        exit();
    }
    $row = mysqli_fetch_array($sql);
    $ticket_prefix = sanitizeInput($row['ticket_prefix']);
    $ticket_number = intval($row['ticket_number']);
    $ticket_subject = sanitizeInput($row['ticket_subject']);
    $ticket_details = sanitizeInput($row['ticket_details']);

    //Get merge into ticket id (as it may differ from the number)
    $sql = mysqli_query($mysqli, "SELECT ticket_id FROM tickets WHERE ticket_number = $merge_into_ticket_number");
    if (mysqli_num_rows($sql) == 0) {
        $_SESSION['alert_message'] = "Cannot merge into that ticket.";
        header("Location: " . $_SERVER["HTTP_REFERER"]);
        exit();
    }
    $merge_row = mysqli_fetch_array($sql);
    $merge_into_ticket_id = intval($merge_row['ticket_id']);

    if ($ticket_number == $merge_into_ticket_number) {
        $_SESSION['alert_message'] = "Cannot merge into the same ticket.";
        header("Location: " . $_SERVER["HTTP_REFERER"]);
        exit();
    }

    //Update current ticket
    mysqli_query($mysqli, "INSERT INTO ticket_replies SET ticket_reply = 'Ticket $ticket_prefix$ticket_number merged into $ticket_prefix$merge_into_ticket_number. Comment: $merge_comment', ticket_reply_time_worked = '00:01:00', ticket_reply_type = '$ticket_reply_type', ticket_reply_by = $user_id, ticket_reply_ticket_id = $ticket_id") or die(mysqli_error($mysqli));
    mysqli_query($mysqli, "UPDATE tickets SET ticket_status = 'Closed', ticket_closed_at = NOW() WHERE ticket_id = $ticket_id") or die(mysqli_error($mysqli));

    //Update new ticket
    mysqli_query($mysqli, "INSERT INTO ticket_replies SET ticket_reply = 'Ticket $ticket_prefix$ticket_number was merged into this ticket with comment: $merge_comment.<br><b>$ticket_subject</b><br>$ticket_details', ticket_reply_time_worked = '00:01:00', ticket_reply_type = '$ticket_reply_type', ticket_reply_by = $user_id, ticket_reply_ticket_id = $merge_into_ticket_id") or die(mysqli_error($mysqli));

    //Logging
    mysqli_query($mysqli, "INSERT INTO logs SET log_type = 'Ticket', log_action = 'Merged', log_description = 'Merged ticket $ticket_prefix$ticket_number into $ticket_prefix$merge_into_ticket_number', log_ip = '$ip', log_user_agent = '$user_agent', log_user_id = $user_id");

    $_SESSION['alert_message'] = "Ticket merged into $ticket_prefix$merge_into_ticket_number";

    header("Location: " . $_SERVER["HTTP_REFERER"]);
}

if (isset($_POST['change_client_ticket'])) {

    validateTechRole();

    $ticket_id = intval($_POST['ticket_id']);
    $client_id = intval($_POST['new_client_id']);

    // Set any/all existing replies to internal
    mysqli_query($mysqli, "UPDATE ticket_replies SET ticket_reply_type = 'Internal' WHERE ticket_reply_ticket_id = $ticket_id");

    // Update ticket client
    mysqli_query($mysqli, "UPDATE tickets SET ticket_client_id = $client_id, ticket_contact_id = 0 WHERE ticket_id = $ticket_id LIMIT 1");

    //Logging
    mysqli_query($mysqli, "INSERT INTO logs SET log_type = 'Ticket Reply', log_action = 'Modify', log_description = '$name modified ticket - client changed', log_ip = '$ip', log_user_agent = '$user_agent', log_client_id = $client_id, log_user_id = $user_id, log_entity_id = $ticket_id");

    $_SESSION['alert_message'] = "Ticket client updated";

    header("Location: " . $_SERVER["HTTP_REFERER"]);
}

if (isset($_GET['close_ticket'])) {

    validateTechRole();

    $ticket_id = intval($_GET['close_ticket']);

    mysqli_query($mysqli, "UPDATE tickets SET ticket_status = 5, ticket_closed_at = NOW(), ticket_closed_by = $user_id WHERE ticket_id = $ticket_id") or die(mysqli_error($mysqli));

    mysqli_query($mysqli, "INSERT INTO ticket_replies SET ticket_reply = 'Ticket closed.', ticket_reply_type = 'Internal', ticket_reply_time_worked = '00:01:00', ticket_reply_by = $user_id, ticket_reply_ticket_id = $ticket_id");

    //Logging
    mysqli_query($mysqli, "INSERT INTO logs SET log_type = 'Ticket', log_action = 'Closed', log_description = 'Ticket ID $ticket_id Closed', log_ip = '$ip', log_user_agent = '$user_agent', log_user_id = $user_id, log_entity_id = $ticket_id");

    // Client notification email
    if (!empty($config_smtp_host) && $config_ticket_client_general_notifications == 1) {

        // Get details
        $ticket_sql = mysqli_query($mysqli, "SELECT contact_name, contact_email, ticket_prefix, ticket_number, ticket_subject FROM tickets 
            LEFT JOIN clients ON ticket_client_id = client_id 
            LEFT JOIN contacts ON ticket_contact_id = contact_id
            WHERE ticket_id = $ticket_id
        ");
        $row = mysqli_fetch_array($ticket_sql);

        $contact_name = sanitizeInput($row['contact_name']);
        $contact_email = sanitizeInput($row['contact_email']);
        $ticket_prefix = sanitizeInput($row['ticket_prefix']);
        $ticket_number = intval($row['ticket_number']);
        $ticket_subject = sanitizeInput($row['ticket_subject']);
        $ticket_details = sanitizeInput($row['ticket_details']);
        $client_id = intval($row['ticket_client_id']);
        $ticket_created_by = intval($row['ticket_created_by']);
        $ticket_assigned_to = intval($row['ticket_assigned_to']);

        // Sanitize Config vars from get_settings.php
        $config_ticket_from_name = sanitizeInput($config_ticket_from_name);
        $config_ticket_from_email = sanitizeInput($config_ticket_from_email);
        $config_base_url = sanitizeInput($config_base_url);

        // Get Company Info
        $sql = mysqli_query($mysqli, "SELECT company_name, company_phone FROM companies WHERE company_id = 1");
        $row = mysqli_fetch_array($sql);
        $company_name = sanitizeInput($row['company_name']);
        $company_phone = sanitizeInput(formatPhoneNumber($row['company_phone']));

        // Check email valid
        if (filter_var($contact_email, FILTER_VALIDATE_EMAIL)) {

            $data = [];

            $subject = "Ticket closed - [$ticket_prefix$ticket_number] - $ticket_subject | (do not reply)";
            $body = "Hello $contact_name,<br><br>Your ticket regarding \"$ticket_subject\" has been closed. <br><br> We hope the request/issue was resolved to your satisfaction. If you need further assistance, please raise a new ticket using the below details. Please do not reply to this email. <br><br>Ticket: $ticket_prefix$ticket_number<br>Subject: $ticket_subject<br>Portal: https://$config_base_url/portal/ticket.php?id=$ticket_id<br><br>--<br>$company_name - Support<br>$config_ticket_from_email<br>$company_phone";

            // Email Ticket Contact
            // Queue Mail

            $data[] = [
                'from' => $config_ticket_from_email,
                'from_name' => $config_ticket_from_name,
                'recipient' => $contact_email,
                'recipient_name' => $contact_name,
                'subject' => $subject,
                'body' => $body
            ];

            // Also Email all the watchers
            $sql_watchers = mysqli_query($mysqli, "SELECT watcher_email FROM ticket_watchers WHERE watcher_ticket_id = $ticket_id");
            $body .= "<br><br>----------------------------------------<br>DO NOT REPLY - YOU ARE RECEIVING THIS EMAIL BECAUSE YOU ARE A WATCHER";
            while ($row = mysqli_fetch_array($sql_watchers)) {
                $watcher_email = sanitizeInput($row['watcher_email']);

                // Queue Mail
                $data[] = [
                    'from' => $config_ticket_from_email,
                    'from_name' => $config_ticket_from_name,
                    'recipient' => $watcher_email,
                    'recipient_name' => $watcher_email,
                    'subject' => $subject,
                    'body' => $body
                ];
            }
            addToMailQueue($mysqli, $data);
        }
    }
    //End Mail IF

    $_SESSION['alert_message'] = "Ticket Closed, this cannot not be reopened but you may start another one";
    header("Location: " . $_SERVER["HTTP_REFERER"]);
}

if (isset($_POST['add_invoice_from_ticket'])) {

    global $config_ticket_next_number, $config_default_net_terms, $config_invoice_prefix, $config_invoice_next_number, $company_currency;

    $invoice_id = intval($_POST['invoice_id']);
    $ticket_id = intval($_POST['ticket_id']);
    $date = sanitizeInput($_POST['date']);
    $category = intval($_POST['category']);
    $scope = sanitizeInput($_POST['scope']);

    $sql = mysqli_query(
        $mysqli,
        "SELECT * FROM tickets
        LEFT JOIN clients ON ticket_client_id = client_id
        LEFT JOIN contacts ON ticket_contact_id = contact_id 
        LEFT JOIN assets ON ticket_asset_id = asset_id
        LEFT JOIN locations ON ticket_location_id = location_id
        WHERE ticket_id = $ticket_id"
    );

    $row = mysqli_fetch_array($sql);
    $client_id = intval($row['client_id']);
    $client_net_terms = intval($row['client_net_terms']);
    if ($client_net_terms == 0) {
        $client_net_terms = $config_default_net_terms;
    }

    $ticket_prefix = sanitizeInput($row['ticket_prefix']);
    $ticket_number = intval($row['ticket_number']);
    $ticket_category = sanitizeInput($row['ticket_category']);
    $ticket_subject = sanitizeInput($row['ticket_subject']);
    $ticket_created_at = sanitizeInput($row['ticket_created_at']);
    $ticket_updated_at = sanitizeInput($row['ticket_updated_at']);
    $ticket_closed_at = sanitizeInput($row['ticket_closed_at']);

    $contact_id = intval($row['contact_id']);
    $contact_name = sanitizeInput($row['contact_name']);
    $contact_email = sanitizeInput($row['contact_email']);

    $asset_id = intval($row['asset_id']);

    $location_name = sanitizeInput($row['location_name']);

    if ($invoice_id == 0) {

        //Get the last Invoice Number and add 1 for the new invoice number
        $invoice_number = $config_invoice_next_number;
        $new_config_invoice_next_number = $config_invoice_next_number + 1;
        mysqli_query($mysqli, "UPDATE settings SET config_invoice_next_number = $new_config_invoice_next_number WHERE company_id = 1");

        //Generate a unique URL key for clients to access
        $url_key = randomString(156);

        mysqli_query($mysqli, "INSERT INTO invoices SET invoice_prefix = '$config_invoice_prefix', invoice_number = $invoice_number, invoice_scope = '$scope', invoice_date = '$date', invoice_due = DATE_ADD('$date', INTERVAL $client_net_terms day), invoice_currency_code = '$company_currency', invoice_category_id = $category, invoice_status = 'Draft', invoice_url_key = '$url_key', invoice_client_id = $client_id");
        $invoice_id = mysqli_insert_id($mysqli);
    }

    //Add Item
    $item_name = sanitizeInput($_POST['item_name']);
    $item_description = sanitizeInput($_POST['item_description']);
    $qty = floatval($_POST['qty']);
    $price = floatval($_POST['price']);
    $tax_id = intval($_POST['tax_id']);

    $subtotal = $price * $qty;

    if ($tax_id > 0) {
        $sql = mysqli_query($mysqli, "SELECT * FROM taxes WHERE tax_id = $tax_id");
        $row = mysqli_fetch_array($sql);
        $tax_percent = floatval($row['tax_percent']);
        $tax_amount = $subtotal * $tax_percent / 100;
    } else {
        $tax_amount = 0;
    }

    $total = $subtotal + $tax_amount;

    mysqli_query($mysqli, "INSERT INTO invoice_items SET item_name = '$item_name', item_description = '$item_description', item_quantity = $qty, item_price = $price, item_order = 1, item_tax_id = $tax_id, item_invoice_id = $invoice_id, item_discount = 0, item_product_id = 0, item_category_id = 0");


    // Check for products in db and add to invoice
    $ticket_products_sql = mysqli_query($mysqli, "SELECT * FROM ticket_products WHERE ticket_product_ticket_id = $ticket_id");
    while ($row = mysqli_fetch_array($ticket_products_sql)) {
        $product_id = intval($row['ticket_product_product_id']);
        $product_qty = floatval($row['ticket_product_quantity']);

        $sql = mysqli_query($mysqli, "SELECT * FROM products WHERE product_id = $product_id");
        $row = mysqli_fetch_array($sql);
        $product_name = sanitizeInput($row['product_name']);
        $product_description = sanitizeInput($row['product_description']);
        $product_price = floatval($row['product_price']);
        $product_tax_id = intval($row['product_tax_id']);

        $product_subtotal = $product_price * $product_qty;

        if ($product_tax_id > 0) {
            $sql = mysqli_query($mysqli, "SELECT * FROM taxes WHERE tax_id = $product_tax_id");
            $row = mysqli_fetch_array($sql);
            $product_tax_percent = floatval($row['tax_percent']);
            $product_tax_amount = $product_subtotal * $product_tax_percent / 100;
        } else {
            $product_tax_amount = 0;
        }

        $product_total = $product_subtotal + $product_tax_amount;

        $total = $total + $product_total;

        mysqli_query($mysqli, "INSERT INTO invoice_items SET item_name = '$product_name', item_description = '$product_description', item_quantity = $product_qty, item_price = $product_price, item_subtotal = $product_subtotal, item_tax = $product_tax_amount, item_total = $product_total, item_order = 1, item_tax_id = $product_tax_id, item_invoice_id = $invoice_id");
    
        // Add product to string for internal note
        $product_ticket_note .= ".<br>$product_qty x $product_name added to invoice";
    }

    mysqli_query($mysqli, "INSERT INTO history SET history_status = 'Draft', history_description = 'Invoice created from Ticket $ticket_prefix$ticket_number', history_invoice_id = $invoice_id");

    // Add internal note to ticket, and link to invoice in database
    mysqli_query($mysqli, "INSERT INTO ticket_replies SET ticket_reply = 'Created invoice <a href=\"invoice.php?invoice_id=$invoice_id\">$config_invoice_prefix$invoice_number</a> for this ticket$product_ticket_note.', ticket_reply_type = 'Internal', ticket_reply_time_worked = '00:01:00', ticket_reply_by = $user_id, ticket_reply_ticket_id = $ticket_id");
    mysqli_query($mysqli, "UPDATE tickets SET ticket_invoice_id = $invoice_id WHERE ticket_id = $ticket_id");


    // Logging
    mysqli_query($mysqli, "INSERT INTO logs SET log_type = 'Invoice', log_action = 'Create', log_description = '$config_invoice_prefix$invoice_number created from Ticket $ticket_prefix$ticket_number', log_ip = '$ip', log_user_agent = '$user_agent', log_user_id = $user_id");

    $_SESSION['alert_message'] = "Invoice created from ticket";

    referWithAlert("Invoice created from ticket", "success", '/public/?page=invoice&invoice_id='.$invoice_id);
}

if (isset($_POST['export_client_tickets_csv'])) {

    validateTechRole();

    $client_id = intval($_POST['client_id']);

    //get records from database
    $sql = mysqli_query($mysqli, "SELECT * FROM clients WHERE client_id = $client_id");
    $row = mysqli_fetch_array($sql);

    $client_name = $row['client_name'];

    $sql = mysqli_query($mysqli, "SELECT * FROM tickets WHERE ticket_client_id = $client_id ORDER BY ticket_number ASC");
    if ($sql->num_rows > 0) {
        $delimiter = ",";
        $filename = $client_name . "-Tickets-" . date('Y-m-d') . ".csv";

        //create a file pointer
        $f = fopen('php://memory', 'w');

        //set column headers
        $fields = array('Ticket Number', 'Priority', 'Status', 'Subject', 'Date Opened', 'Date Closed');
        fputcsv($f, $fields, $delimiter);

        //output each row of the data, format line as csv and write to file pointer
        while ($row = $sql->fetch_assoc()) {
            $lineData = array($row['ticket_number'], $row['ticket_priority'], $row['ticket_status'], $row['ticket_subject'], $row['ticket_created_at'], $row['ticket_closed_at']);
            fputcsv($f, $lineData, $delimiter);
        }

        //move back to beginning of file
        fseek($f, 0);

        //set headers to download file rather than displayed
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '";');

        //output all remaining data on a file pointer
        fpassthru($f);
    }
    exit;
}

if (isset($_POST['add_recurring_ticket'])) {

    validateTechRole();

    require_once '/var/www/nestogy/includes/post/models/recurring_ticket_model.php';

    $start_date = sanitizeInput($_POST['start_date']);

    // If no contact is selected automatically choose the primary contact for the client
    if ($client_id > 0 && $contact_id == 0) {
        $sql = mysqli_query($mysqli, "SELECT contact_id FROM contacts WHERE contact_client_id = $client_id AND contact_primary = 1");
        $row = mysqli_fetch_array($sql);
        $contact_id = intval($row['contact_id']);
    }

    // Add scheduled ticket
    mysqli_query($mysqli, "INSERT INTO scheduled_tickets SET scheduled_ticket_subject = '$subject', scheduled_ticket_details = '$details', scheduled_ticket_priority = '$priority', scheduled_ticket_frequency = '$frequency', scheduled_ticket_start_date = '$start_date', scheduled_ticket_next_run = '$start_date', scheduled_ticket_assigned_to = $assigned_to, scheduled_ticket_created_by = $user_id, scheduled_ticket_client_id = $client_id, scheduled_ticket_contact_id = $contact_id, scheduled_ticket_asset_id = $asset_id");

    $scheduled_ticket_id = mysqli_insert_id($mysqli);

    // Logging
    mysqli_query($mysqli, "INSERT INTO logs SET log_type = 'Recurring Ticket', log_action = 'Create', log_description = '$name created recurring ticket for $subject - $frequency', log_ip = '$ip', log_user_agent = '$user_agent', log_client_id = $client_id, log_user_id = $user_id, log_entity_id = $scheduled_ticket_id");

    $_SESSION['alert_message'] = "Recurring ticket <strong>$subject - $frequency</strong> created";

    header("Location: " . $_SERVER["HTTP_REFERER"]);
}

if (isset($_POST['edit_recurring_ticket'])) {

    validateTechRole();

    require_once '/var/www/nestogy/includes/post/models/recurring_ticket_model.php';

    $scheduled_ticket_id = intval($_POST['scheduled_ticket_id']);
    $next_run_date = sanitizeInput($_POST['next_date']);

    // If no contact is selected automatically choose the primary contact for the client
    if ($client_id > 0 && $contact_id == 0) {
        $sql = mysqli_query($mysqli, "SELECT contact_id FROM contacts WHERE contact_client_id = $client_id AND contact_primary = 1");
        $row = mysqli_fetch_array($sql);
        $contact_id = intval($row['contact_id']);
    }

    // Edit scheduled ticket
    mysqli_query($mysqli, "UPDATE scheduled_tickets SET scheduled_ticket_subject = '$subject', scheduled_ticket_details = '$details', scheduled_ticket_priority = '$priority', scheduled_ticket_frequency = '$frequency', scheduled_ticket_next_run = '$next_run_date', scheduled_ticket_assigned_to = $assigned_to, scheduled_ticket_asset_id = $asset_id, scheduled_ticket_contact_id = $contact_id WHERE scheduled_ticket_id = $scheduled_ticket_id");

    // Logging
    mysqli_query($mysqli, "INSERT INTO logs SET log_type = 'Recurring Ticket', log_action = 'Modify', log_description = '$name modified recurring ticket for $subject - $frequency', log_ip = '$ip', log_user_agent = '$user_agent', log_client_id = $client_id, log_user_id = $user_id, log_entity_id = $scheduled_ticket_id");

    $_SESSION['alert_message'] = "Recurring ticket <strong>$subject - $frequency</strong> updated";

    header("Location: " . $_SERVER["HTTP_REFERER"]);
}

if (isset($_GET['delete_recurring_ticket'])) {

    validateAdminRole();

    $scheduled_ticket_id = intval($_GET['delete_recurring_ticket']);

    // Get Scheduled Ticket Subject Ticket Prefix, Number and Client ID for logging and alert message
    $sql = mysqli_query($mysqli, "SELECT * FROM scheduled_tickets WHERE scheduled_ticket_id = $scheduled_ticket_id");
    $row = mysqli_fetch_array($sql);
    $subject = sanitizeInput($row['scheduled_ticket_subject']);
    $frequency = sanitizeInput($row['scheduled_ticket_frequency']);

    $client_id = intval($row['scheduled_ticket_client_id']);

    // Delete
    mysqli_query($mysqli, "DELETE FROM scheduled_tickets WHERE scheduled_ticket_id = $scheduled_ticket_id");

    //Logging
    mysqli_query($mysqli, "INSERT INTO logs SET log_type = 'Scheduled Ticket', log_action = 'Delete', log_description = '$name deleted recurring ticket for $subject - $frequency', log_client_id = $client_id, log_user_id = $user_id, log_entity_id = $scheduled_ticket_id");

    $_SESSION['alert_message'] = "Recurring ticket <strong>$subject - $frequency</strong> deleted";

    header("Location: " . $_SERVER["HTTP_REFERER"]);
}

if (isset($_POST['bulk_delete_scheduled_tickets']) || isset($_POST['bulk_delete_recurring_tickets'])) {
    validateAdminRole();
    validateCSRFToken($_POST['csrf_token']);

    $count = 0; // Default 0
    $scheduled_ticket_ids = $_POST['scheduled_ticket_ids']; // Get array of recurring scheduled tickets IDs to be deleted

    if (!empty($scheduled_ticket_ids)) {

        // Cycle through array and delete each recurring scheduled ticket
        foreach ($scheduled_ticket_ids as $scheduled_ticket_id) {

            $scheduled_ticket_id = intval($scheduled_ticket_id);
            mysqli_query($mysqli, "DELETE FROM scheduled_tickets WHERE scheduled_ticket_id = $scheduled_ticket_id");
            mysqli_query($mysqli, "INSERT INTO logs SET log_type = 'Scheduled Ticket', log_action = 'Delete', log_description = '$name deleted recurring ticket (bulk)', log_ip = '$ip', log_user_agent = '$user_agent', log_user_id = $user_id, log_entity_id = $scheduled_ticket_id");

            $count++;
        }

        // Logging
        mysqli_query($mysqli, "INSERT INTO logs SET log_type = 'Scheduled Ticket', log_action = 'Delete', log_description = '$name bulk deleted $count recurring tickets', log_ip = '$ip', log_user_agent = '$user_agent', log_user_id = $user_id");

        $_SESSION['alert_message'] = "Deleted $count recurring ticket(s)";
    }

    header("Location: " . $_SERVER["HTTP_REFERER"]);
}

if (isset($_GET['set_billable_status'])) {
    validateTechRole();

    $ticket_id = intval($_POST['ticket_id']);
    $billable = intval($_POST['billable']);

    $parameters = [
        'ticket_id' => $ticket_id,
        'ticket_billable' => $billable
    ];

    $return_data = updateTicket($parameters);
    referWithAlert($return_data['type'], $return_data['message']);
}


if (isset($_POST['set_ticket_status'])) {
    validateTechRole();

    $ticket_id = intval($_POST['ticket_id']);
    $status = intval($_POST['status']);

    $parameters = [
        'ticket_id' => $ticket_id,
        'ticket_status' => $status
    ];

    $return_data = updateTicket($parameters);
    referWithAlert($return_data['type'], $return_data['message']);
}

if (isset($_POST['edit_ticket_schedule'])) {

    global $config_base_url, $config_ticket_from_email, $config_ticket_from_name, $mysqli, $company_name, $user_id, $user_name, $user_email, $user_role, $ip, $user_agent;

    validateTechRole();

    $ticket_id = intval($_POST['ticket_id']);
    $onsite = intval($_POST['onsite']);
    $schedule = sanitizeInput($_POST['scheduled_date_time']);
    $ticket_link = "pages/ticket.php?ticket_id=$ticket_id";
    $full_ticket_url = "https://$config_base_url/portal/ticket.php?ticket_id=$ticket_id";
    $ticket_link_html = "<a href=\"$full_ticket_url\">$ticket_link</a>";

    mysqli_query(
        $mysqli,
        "UPDATE tickets SET
        ticket_schedule = '$schedule',
        ticket_onsite = '$onsite',
        ticket_status = 3
        WHERE ticket_id = $ticket_id"
    );


    // Check for other conflicting scheduled items based on 2 hr window
    //TODO make this configurable
    $start = date('Y-m-d H:i:s', strtotime($schedule) - 7200);
    $end = date('Y-m-d H:i:s', strtotime($schedule) + 7200);
    $sql = mysqli_query($mysqli, "SELECT * FROM tickets WHERE ticket_schedule BETWEEN '$start' AND '$end' AND ticket_id != $ticket_id AND ticket_status = 3");
    if (mysqli_num_rows($sql) > 0) {
        $conflicting_tickets = [];
        while ($row = mysqli_fetch_array($sql)) {
            $conflicting_tickets[] = $row['ticket_id'] . " - " . $row['ticket_subject'] . " @ " . $row['ticket_schedule'];
        }
    }
    $sql = mysqli_query($mysqli, "SELECT * FROM tickets 
        LEFT JOIN clients ON ticket_client_id = client_id
        LEFT JOIN contacts ON ticket_contact_id = contact_id
        LEFT JOIN locations on contact_location_id = location_id
        LEFT JOIN users ON ticket_assigned_to = user_id
        WHERE ticket_id = $ticket_id
    ");

    $row = mysqli_fetch_array($sql);

    $client_id = intval($row['ticket_client_id']);
    $client_name = sanitizeInput($row['client_name']);
    $ticket_details = sanitizeInput($row['ticket_details']);
    $contact_name = sanitizeInput($row['contact_name']);
    $contact_email = sanitizeInput($row['contact_email']);
    $ticket_prefix = sanitizeInput($row['ticket_prefix']);
    $ticket_number = intval($row['ticket_number']);
    $ticket_subject = sanitizeInput($row['ticket_subject']);
    $user_name = sanitizeInput($row['user_name']);
    $user_email = sanitizeInput($row['user_email']);
    $cal_subject = $ticket_number . ": " . $client_name . " - " . $ticket_subject;
    $ticket_details_truncated = substr($ticket_details, 0, 100);
    $cal_description = $ticket_details_truncated . " - " . $full_ticket_url;
    $cal_location = sanitizeInput($row["location_address"]);
    $email_datetime = date('l, F j, Y \a\t g:ia', strtotime($schedule));

    /// Create iCal event
    $cal_str = createiCalStr($schedule, $cal_subject, $cal_description, $cal_location);

    $data = [
        [   //Client Contact Email
            'from' => $config_ticket_from_email,
            'from_name' => $config_ticket_from_name,
            'recipient' => $contact_email,
            'recipient_name' => $contact_name,
            'subject' => "Ticket Scheduled - [$ticket_prefix$ticket_number] - $ticket_subject",
            'body' => mysqli_escape_string($mysqli, "<div class='header'>
                                Hello, $contact_name
                            </div>
                            Your ticket regarding $ticket_subject has been scheduled for $email_datetime.
                            <br><br>
                            <a href='https://$config_base_url/portal/ticket.php?id=$ticket_id' class='link-button'>Access your ticket here</a>
                            <br><br>
                            Please do not reply to this email.
                            <br><br>
                            <strong>Ticket:</strong> $ticket_prefix$ticket_number<br>
                            <strong>Subject:</strong> $ticket_subject<br>
                            <br><br>
                            <div class='footer'>
                                ~<br>
                                $company_name<br>
                                Support Department<br>
                                $config_ticket_from_email<br>
                            </div>
                            <div class='no-reply'>
                                This is an automated message. Please do not reply directly to this email.
                            </div>"),
            'cal_str' => $cal_str
        ],
        [
            // User Email
            'from' => $config_ticket_from_email,
            'from_name' => $config_ticket_from_name,
            'recipient' => $user_email,
            'recipient_name' => $user_name,
            'subject' => "Ticket Scheduled - [$ticket_prefix$ticket_number] - $ticket_subject",
            'body' => "Hello, " . $user_name . "<br><br>The ticket regarding $ticket_subject has been scheduled for $email_datetime.<br><br>--------------------------------<br><a href=\"https://$config_base_url/ticket.php?id=$ticket_id\">$ticket_link</a><br>--------------------------------<br><br>Please do not reply to this email. <br><br>Ticket: $ticket_prefix$ticket_number<br>Subject: $ticket_subject<br>Portal: https://$config_base_url/ticket.php?id=$ticket_id<br><br>~<br>$company_name<br>Support Department<br>$config_ticket_from_email",
            'cal_str' => $cal_str
        ]
    ];

    //Send all watchers an email
    $sql_watchers = mysqli_query($mysqli, "SELECT watcher_email FROM ticket_watchers WHERE watcher_ticket_id = $ticket_id");

    while ($row = mysqli_fetch_array($sql_watchers)) {
        $watcher_email = sanitizeInput($row['watcher_email']);
        $data[] = [
            'from' => $config_ticket_from_email,
            'from_name' => $config_ticket_from_name,
            'recipient' => $watcher_email,
            'recipient_name' => $watcher_email,
            'subject' => "Ticket Scheduled - [$ticket_prefix$ticket_number] - $ticket_subject",
            'body' => mysqli_escape_string($mysqli, nullable_htmlentities("<div class='header'>
            Hello,
        </div>
        The ticket regarding $ticket_subject has been scheduled for $email_datetime.
        <br><br>
        <a href='https://$config_base_url/portal/ticket.php?id=$ticket_id' class='link-button'>$ticket_link</a>
        <br><br>
        Please do not reply to this email.
        <br><br>
        <strong>Ticket:</strong> $ticket_prefix$ticket_number<br>
        <strong>Subject:</strong> $ticket_subject<br>
        <strong>Portal:</strong> <a href='https://$config_base_url/portal/ticket.php?id=$ticket_id'>Access the ticket here</a>
        <br><br>
        <div class='footer'>
            ~<br>
            $company_name<br>
            Support Department<br>
            $config_ticket_from_email<br>
        </div>
        <div class='no-reply'>
            This is an automated message. Please do not reply directly to this email.
        </div>")),
            'cal_str' => $cal_str
        ];
    }

    $response = addToMailQueue($mysqli, $data);


    // Update ticket reply
    $ticket_reply_note = "Ticket scheduled for $email_datetime " . (boolval($onsite) ? '(onsite).' : '(remote).');
    mysqli_query($mysqli, "INSERT INTO ticket_replies SET ticket_reply = '$ticket_reply_note', ticket_reply_type = 'Internal', ticket_reply_time_worked = '00:01:00', ticket_reply_by = $user_id, ticket_reply_ticket_id = $ticket_id");

    //Logging
    mysqli_query(
        $mysqli,
        "INSERT INTO logs SET
            log_type = 'Ticket',
            log_action = 'Modify',
            log_description = '$name modified ticket schedule',
            log_ip = '$ip',
            log_user_agent = '$user_agent',
            log_user_id = $user_id,
            log_entity_id = $ticket_id"
    );


    if (empty($conflicting_tickets)) {
        $_SESSION['alert_message'] = "Ticket scheduled for $email_datetime";
        header("Location: " . $_SERVER["HTTP_REFERER"]);
    } else {
        $_SESSION['alert_type'] = "error";
        $_SESSION['alert_message'] = "Ticket scheduled for $email_datetime. Yet there are conflicting tickets scheduled for the same time: <br>" . implode(", <br>", $conflicting_tickets);
        header("Location: calendar_events.php");
    }

}

if (isset($_GET['cancel_ticket_schedule'])) {

    validateTechRole();

    $ticket_id = intval($_GET['cancel_ticket_schedule']);

    $sql = mysqli_query($mysqli, "SELECT * FROM tickets WHERE ticket_id = $ticket_id");
    $row = mysqli_fetch_array($sql);

    $client_id = intval($row['ticket_client_id']);
    $ticket_prefix = sanitizeInput($row['ticket_prefix']);
    $ticket_number = intval($row['ticket_number']);
    $ticket_subject = sanitizeInput($row['ticket_subject']);
    $ticket_schedule = sanitizeInput($row['ticket_schedule']);
    $ticket_cal_str = sanitizeInput($row['ticket_cal_str']);

    mysqli_query($mysqli, "UPDATE tickets SET ticket_schedule = NULL, ticket_status = 2 WHERE ticket_id = $ticket_id");

    //Create iCal event
    $cal_str = createiCalStrCancel($ticket_cal_str);

    //Send emails

    $sql = mysqli_query($mysqli, "SELECT * FROM tickets 
        LEFT JOIN clients ON ticket_client_id = client_id
        LEFT JOIN contacts ON ticket_contact_id = contact_id
        LEFT JOIN locations on contact_location_id = location_id
        LEFT JOIN users ON ticket_assigned_to = user_id
        WHERE ticket_id = $ticket_id
    ");
    $row = mysqli_fetch_array($sql);

    $client_id = intval($row['ticket_client_id']);
    $client_name = sanitizeInput($row['client_name']);
    $ticket_details = sanitizeInput($row['ticket_details']);
    $contact_name = sanitizeInput($row['contact_name']);
    $contact_email = sanitizeInput($row['contact_email']);
    $ticket_prefix = sanitizeInput($row['ticket_prefix']);
    $ticket_number = intval($row['ticket_number']);
    $ticket_subject = sanitizeInput($row['ticket_subject']);
    $user_name = sanitizeInput($row['user_name']);
    $user_email = sanitizeInput($row['user_email']);

    $data = [
        [   //Client Contact Email
            'from' => $config_ticket_from_email,
            'from_name' => $config_ticket_from_name,
            'recipient' => $contact_email,
            'recipient_name' => $contact_name,
            'subject' => "Ticket Schedule Cancelled - [$ticket_prefix$ticket_number] - $ticket_subject",
            'body' => mysqli_escape_string($mysqli, "<div class='header'>
                                Hello, $contact_name
                            </div>
                            Scheduled work for your ticket regarding $ticket_subject has been cancelled.
                            <br><br>
                            <a href='https://$config_base_url/portal/ticket.php?id=$ticket_id' class='link-button'>Access your ticket here</a>
                            <br><br>
                            Please do not reply to this email.
                            <br><br>
                            <strong>Ticket:</strong> $ticket_prefix$ticket_number<br>
                            <strong>Subject:</strong> $ticket_subject<br>
                            <br><br>
                            <div class='footer'>
                                ~<br>
                                $company_name<br>
                                Support Department<br>
                                $config_ticket_from_email<br>
                            </div>
                            <div class='no-reply'>
                                This is an automated message. Please do not reply directly to this email.
                            </div>"),
            'cal_str' => $cal_str
        ],
        [
            // User Email
            'from' => $config_ticket_from_email,
            'from_name' => $config_ticket_from_name,
            'recipient' => $user_email,
            'recipient_name' => $user_name,
            'subject' => "Ticket Schedule Cancelled - [$ticket_prefix$ticket_number] - $ticket_subject",
            'body' => "Hello, " . $user_name . "<br><br>Scheduled work for the ticket regarding $ticket_subject has been cancelled.<br><br>--------------------------------<br><a href=\"https://$config_base_url/ticket.php?id=$ticket_id\">$ticket_link</a><br>--------------------------------<br><br>Please do not reply to this email. <br><br>Ticket: $ticket_prefix$ticket_number<br>Subject: $ticket_subject<br>Portal: https://$config_base_url/ticket.php?id=$ticket_id<br><br>~<br>$company_name<br>Support Department<br>$config_ticket_from_email",
            'cal_str' => $cal_str
        ]
    ];

    //Send all watchers an email
    $sql_watchers = mysqli_query($mysqli, "SELECT watcher_email FROM ticket_watchers WHERE watcher_ticket_id = $ticket_id");
    while ($row = mysqli_fetch_assoc($sql_watchers)) {
        $watcher_email = sanitizeInput($row['watcher_email']);
        $data[] = [
            'from' => $config_ticket_from_email,
            'from_name' => $config_ticket_from_name,
            'recipient' => $watcher_email,
            'recipient_name' => $watcher_email,
            'subject' => "Ticket Schedule Cancelled - [$ticket_prefix$ticket_number] - $ticket_subject",
            'body' => mysqli_escape_string($mysqli, nullable_htmlentities("<div class='header'>
            Hello,
        </div>
        Scheduled work for the ticket regarding $ticket_subject has been cancelled.
        <br><br>
        <a href='https://$config_base_url/portal/ticket.php?id=$ticket_id' class='link-button'>$ticket_link</a>
        <br><br>
        Please do not reply to this email.
        <br><br>
        <strong>Ticket:</strong> $ticket_prefix$ticket_number<br>
        <strong>Subject:</strong> $ticket_subject<br>
        <strong>Portal:</strong> <a href='https://$config_base_url/portal/ticket.php?id=$ticket_id'>Access the ticket here</a>
        <br><br>
        <div class='footer'>
            ~<br>
            $company_name<br>
            Support Department<br>
            $config_ticket_from_email<br>
        </div>
        <div class='no-reply'>
            This is an automated message. Please do not reply directly to this email.
        </div>")),
            'cal_str' => $cal_str
        ];
    }

    $response = addToMailQueue($mysqli, $data);

    // Update ticket reply
    $ticket_reply_note = "Ticket schedule cancelled.";
    mysqli_query($mysqli, "INSERT INTO ticket_replies SET ticket_reply = '$ticket_reply_note', ticket_reply_type = 'Internal', ticket_reply_time_worked = '00:01:00', ticket_reply_by = $user_id, ticket_reply_ticket_id = $ticket_id");

    //Logging
    mysqli_query($mysqli, "INSERT INTO logs SET log_type = 'Ticket', log_action = 'Modify', log_description = '$name cancelled ticket schedule', log_ip = '$ip', log_user_agent = '$user_agent', log_user_id = $user_id, log_entity_id = $ticket_id");

    $_SESSION['alert_message'] = "Ticket schedule cancelled";

    header("Location: " . $_SERVER["HTTP_REFERER"]);
}

if (isset($_POST['add_ticket_products'])) {

    validateTechRole();

    $ticket_id = intval($_POST['ticket_id']);
    $product_id = intval($_POST['product_id']);
    $qty = intval($_POST['quantity']);

    //find user inventory location
    $sql = mysqli_query($mysqli, "SELECT * FROM inventory_locations WHERE inventory_location_user_id = $user_id");
    $num_rows = mysqli_num_rows($sql);

    if ($num_rows == 1) {
        $row = mysqli_fetch_array($sql);
        $location_id = intval($row['inventory_location_id']);
    } elseif ($num_rows > 1) {        
        $_SESSION['alert_type'] = "error";
        $_SESSION['alert_message'] = "You have more than one inventory location set. Please contact your administrator";
        header("Location: " . $_SERVER["HTTP_REFERER"]);
        exit;
    } else {
        $_SESSION['alert_type'] = "error";
        $_SESSION['alert_message'] = "You do not have an inventory location set. Please contact your administrator";
        header("Location: " . $_SERVER["HTTP_REFERER"]);
        exit;
    }

    //check qty against inventory
    $sql = mysqli_query($mysqli, "SELECT SUM(inventory_quantity) as inventory_quantity FROM inventory WHERE inventory_product_id = $product_id AND inventory_location_id = $location_id GROUP BY inventory_product_id, inventory_location_id;");
    $row = mysqli_fetch_array($sql);
    $inventory_qty = intval($row['inventory_quantity']);
    if ($qty > $inventory_qty) {
        $_SESSION['alert_type'] = "error";
        $_SESSION['alert_message'] = "You do not have enough inventory to add that quantity, QTY: $qty, Inventory: $inventory_qty in location $location_id, $num_rows rows found.";
        header("Location: " . $_SERVER["HTTP_REFERER"]); 
        exit;
    }

    

    // Add to DB
    mysqli_query($mysqli, "INSERT INTO ticket_products SET ticket_product_ticket_id = $ticket_id, ticket_product_product_id = $product_id, ticket_product_quantity = $qty");

    // Delete one item per qty
    mysqli_query($mysqli, "UPDATE inventory SET inventory_quantity = inventory_quantity - 1 WHERE inventory_product_id = $product_id AND inventory_location_id = $location_id AND inventory_quantity > 0 LIMIT $qty");

    // Logging
    mysqli_query($mysqli, "INSERT INTO logs SET log_type = 'Ticket', log_action = 'Modify', log_description = '$name added product to ticket', log_ip = '$ip', log_user_agent = '$user_agent', log_user_id = $user_id, log_entity_id = $ticket_id");

    $_SESSION['alert_message'] = "Product added to ticket";
    header("Location: ". $_SERVER["HTTP_REFERER"]);

}

if (isset($_GET['delete_ticket_product'])) {

    validateTechRole();

    $ticket_product_id = intval($_GET['delete_ticket_product']);
    $ticket_id = intval($_GET['ticket_id']);

    // Get product ID
    $sql = mysqli_query($mysqli, "SELECT * FROM ticket_products WHERE ticket_product_id = $ticket_product_id");
    $row = mysqli_fetch_array($sql);
    $product_id = intval($row['ticket_product_product_id']);
    $qty = intval($row['ticket_product_quantity']);

    // Delete
    mysqli_query($mysqli, "DELETE FROM ticket_products WHERE ticket_product_id = $ticket_product_id");

    //find user's inventory location
    $sql = mysqli_query($mysqli, "SELECT * FROM inventory_locations WHERE inventory_location_user_id = $user_id");
    $row = mysqli_fetch_array($sql);
    $location_id = intval($row['inventory_location_id']);

    // Restore inventory quantity
    mysqli_query($mysqli, "UPDATE inventory SET inventory_quantity = inventory_quantity + 1 WHERE inventory_product_id = $product_id AND inventory_location_id = $location_id AND inventory_quantity = 0 LIMIT $qty");

    // Logging
    mysqli_query($mysqli, "INSERT INTO logs SET log_type = 'Ticket', log_action = 'Modify', log_description = '$name deleted product from ticket', log_ip = '$ip', log_user_agent = '$user_agent', log_user_id = $user_id, log_entity_id = $ticket_id");

    $_SESSION['alert_message'] = "Product removed from ticket. Please see administrator to return inventory";
    header("Location: ". $_SERVER["HTTP_REFERER"]);

}

if (isset($_GET['ticket_billable'])) {

    validateTechRole();

    $ticket_id = intval($_GET['ticket_billable']);

    $parameters = [
        'ticket_id' => $ticket_id,
        'ticket_billable' => 1
    ];

    $return_data = updateTicket($parameters);
    referWithAlert($return_data['type'], $return_data['message']);
}

if (isset($_GET['ticket_unbillable'])) {

    validateTechRole();

    $ticket_id = intval($_GET['ticket_unbillable']);

    $parameters = [
        'ticket_id' => $ticket_id,
        'ticket_billable' => 0
    ];

    $return_data = updateTicket($parameters);
    referWithAlert($return_data['type'], $return_data['message']);
}
