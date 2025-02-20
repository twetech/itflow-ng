<?php 

// Payment Related Functions


function createPayment(
    $payment
) {

    // Access global variables
    global $mysqli, $user_id, $ip, $user_agent, $config_base_url, $config_invoice_from_name, $config_invoice_from_email, $config_invoice_receipt_email, $config_invoice_receipt_email_subject, $config_invoice_receipt_email_body, $currency_format;

    $date = $payment['date'];
    $amount = floatval($payment['amount']);
    $currency_code = sanitizeInput($payment['currency_code']) ?? getSettingValue('company_currency');
    $account = intval($payment['account']);
    $payment_method = sanitizeInput($payment['method']);
    $reference = sanitizeInput($payment['reference']);
    $invoice_id = intval($payment['invoice_id']);
    $email_receipt = intval($payment['email_receipt']) ?? 1;
    $balance = floatval($payment['balance']);
    $link_payment_to_transaction = $payment['link_payment_to_transaction'] ?? null;

     //Check to see if amount entered is greater than the balance of the invoice
    if ($amount > $balance) {
        $payment_is_credit = true;

        // Calculate the overpayment amount
        $credit_amount = $amount - $balance;

        // Set the payment amount to the invoice balance
        $amount = $balance;
    } else {
        $payment_is_credit = false;
    }


    if ($link_payment_to_transaction) {
        // Set bank transaction to reconciled
        mysqli_query($mysqli, "UPDATE bank_transactions SET reconciled = 1 WHERE transaction_id = $link_payment_to_transaction");
        
        $sql = "INSERT INTO payments SET payment_date = '$date', payment_amount = $amount, payment_currency_code = '$currency_code', payment_account_id = $account, payment_method = '$payment_method', payment_reference = '$reference', payment_invoice_id = $invoice_id, plaid_transaction_id = $link_payment_to_transaction";
    } else {
        $sql = "INSERT INTO payments SET payment_date = '$date', payment_amount = $amount, payment_currency_code = '$currency_code', payment_account_id = $account, payment_method = '$payment_method', payment_reference = '$reference', payment_invoice_id = $invoice_id";
    }

    mysqli_query($mysqli, $sql);


    // Get payment ID for reference
    $payment_id = mysqli_insert_id($mysqli);

    if($payment_is_credit) {
        createCredit($credit_amount, $payment_id);
        //Create a credit for the overpayment
        mysqli_query($mysqli,"INSERT INTO credits SET credit_amount = $credit_amount, credit_currency_code = '$currency_code', credit_date = '$date', credit_reference = 'Overpayment: $reference', credit_client_id = (SELECT invoice_client_id FROM invoices WHERE invoice_id = $invoice_id), credit_payment_id = $payment_id, credit_account_id = $account");
        // Get credit ID for reference
        $credit_id = mysqli_insert_id($mysqli);
        
        //Logging
        mysqli_query($mysqli,"INSERT INTO logs SET log_type = 'Credit', log_action = 'Create', log_description = 'Credit for Overpayment', log_ip = '$ip', log_user_agent = '$user_agent', log_user_id = $user_id");
    }

    //Add up all the payments for the invoice and get the total amount paid to the invoice
    $sql_total_payments_amount = mysqli_query($mysqli,"SELECT SUM(payment_amount) AS payments_amount FROM payments WHERE payment_invoice_id = $invoice_id");
    $row = mysqli_fetch_array($sql_total_payments_amount);
    $total_payments_amount = floatval($row['payments_amount']);

    //Get the invoice total
    $sql = mysqli_query($mysqli,"SELECT * FROM invoices
        LEFT JOIN clients ON invoice_client_id = client_id
        LEFT JOIN contacts ON clients.client_id = contacts.contact_client_id AND contact_primary = 1
        WHERE invoice_id = $invoice_id"
    );

    $row = mysqli_fetch_array($sql);
    $invoice_prefix = sanitizeInput($row['invoice_prefix']);
    $invoice_number = intval($row['invoice_number']);
    $invoice_url_key = sanitizeInput($row['invoice_url_key']);
    $invoice_currency_code = sanitizeInput($row['invoice_currency_code']);
    $client_id = intval($row['client_id']);
    $client_name = sanitizeInput($row['client_name']);
    $contact_name = sanitizeInput($row['contact_name']);
    $contact_email = sanitizeInput($row['contact_email']);
    $contact_phone = sanitizeInput(formatPhoneNumber($row['contact_phone']));
    $contact_extension = preg_replace("/[^0-9]/", '',$row['contact_extension']);
    $contact_mobile = sanitizeInput(formatPhoneNumber($row['contact_mobile']));

    $invoice_amount = getInvoiceAmount($invoice_id);
    $invoice_balance = getInvoiceBalance($invoice_id);

    $sql = mysqli_query($mysqli,"SELECT * FROM companies WHERE company_id = 1");
    $row = mysqli_fetch_array($sql);

    $company_name = sanitizeInput($row['company_name']);
    $company_country = sanitizeInput($row['company_country']);
    $company_address = sanitizeInput($row['company_address']);
    $company_city = sanitizeInput($row['company_city']);
    $company_state = sanitizeInput($row['company_state']);
    $company_zip = sanitizeInput($row['company_zip']);
    $company_phone = sanitizeInput(formatPhoneNumber($row['company_phone']));
    $company_email = sanitizeInput($row['company_email']);
    $company_website = sanitizeInput($row['company_website']);
    $company_logo = sanitizeInput($row['company_logo']);

    // Sanitize Config vars from get_settings.php
    $config_invoice_from_name = sanitizeInput($config_invoice_from_name);
    $config_invoice_from_email = sanitizeInput($config_invoice_from_email);

    //Calculate the Invoice balance
    $invoice_balance = $invoice_amount - $total_payments_amount;

    $email_data = [];

    //Determine if invoice has been paid then set the status accordingly
    if ($invoice_balance == 0) {
        $invoice_status = "Paid";
        if ($email_receipt == 1) {
            $subject = "$company_name Payment Received - Invoice $invoice_prefix$invoice_number";
            $body = "Hello $contact_name,<br><br>We have received your payment in the amount of " . numfmt_format_currency($currency_format, $amount, $invoice_currency_code) . " for invoice <a href=\'https://$config_base_url/portal/guest_view_invoice.php?invoice_id=$invoice_id&url_key=$invoice_url_key\'>$invoice_prefix$invoice_number</a>. Please keep this email as a receipt for your records.<br><br>Amount: " . numfmt_format_currency($currency_format, $amount, $invoice_currency_code) . "<br>Balance: " . numfmt_format_currency($currency_format, $invoice_balance, $invoice_currency_code) . "<br><br>Thank you for your business!<br><br><br>--<br>$company_name - Billing Department<br>$config_invoice_from_email<br>$company_phone";
            // Queue Mail
            $email = [
                'from' => $config_invoice_from_email,
                'from_name' => $config_invoice_from_name,
                'recipient' => $contact_email,
                'recipient_name' => $contact_name,
                'subject' => $subject,
                'body' => $body
            ];

            $email_data[] = $email;

            // Get Email ID for reference
            $email_id = mysqli_insert_id($mysqli);

            // Email Logging

            $_SESSION['alert_message'] = "Email receipt sent ";

            mysqli_query($mysqli,"INSERT INTO history SET history_status = 'Sent', history_description = 'Emailed Receipt!', history_invoice_id = $invoice_id");

        }

    } else {


        $invoice_status = "Partial";

        if ($email_receipt == 1) {

                $subject = "$company_name Partial Payment Received - Invoice $invoice_prefix$invoice_number";
                $body = "Hello $contact_name,<br><br>We have received partial payment in the amount of " . numfmt_format_currency($currency_format, $amount, $invoice_currency_code) . " and it has been applied to invoice <a href=\'https://$config_base_url/portal/guest_view_invoice.php?invoice_id=$invoice_id&url_key=$invoice_url_key\'>$invoice_prefix$invoice_number</a>. Please keep this email as a receipt for your records.<br><br>Amount: " . numfmt_format_currency($currency_format, $amount, $invoice_currency_code) . "<br>Balance: " . numfmt_format_currency($currency_format, $invoice_balance, $invoice_currency_code) . "<br><br>Thank you for your business!<br><br><br>~<br>$company_name - Billing<br>$config_invoice_from_email<br>$company_phone";


            // Queue MailL
            $email = [
                'from' => $config_invoice_from_email,
                'from_name' => $config_invoice_from_name,
                'recipient' => $contact_email,
                'recipient_name' => $contact_name,
                'subject' => $subject,
                'body' => $body
            ];

            $email_data[] = $email;

            // Get Email ID for reference
            $email_id = mysqli_insert_id($mysqli);

            // Email Logging

            $_SESSION['alert_message'] .= "Email receipt sent ";

            mysqli_query($mysqli,"INSERT INTO history SET history_status = 'Sent', history_description = 'Payment Receipt sent to mail queue ID: $email_id!', history_invoice_id = $invoice_id");

        }

    }

    // Add emails to queue
    if (!empty($email)) {
        addToMailQueue($mysqli, $email_data);
    }

    //Update Invoice Status
    mysqli_query($mysqli,"UPDATE invoices SET invoice_status = '$invoice_status' WHERE invoice_id = $invoice_id");

    //Add Payment to History
    mysqli_query($mysqli,"INSERT INTO history SET history_status = '$invoice_status', history_description = 'Payment added', history_invoice_id = $invoice_id");

    //Logging
    mysqli_query($mysqli,"INSERT INTO logs SET log_type = 'Payment', log_action = 'Create', log_description = 'Payment created for $amount', log_ip = '$ip', log_user_agent = '$user_agent', log_client_id = $client_id, log_user_id = $user_id, log_entity_id = $payment_id");

    if ($email_receipt == 1) {
        mysqli_query($mysqli,"INSERT INTO logs SET log_type = 'Payment', log_action = 'Email', log_description = 'Payment receipt for invoice $invoice_prefix$invoice_number queued to $contact_email Email ID: $email_id', log_ip = '$ip', log_user_agent = '$user_agent', log_client_id = $client_id, log_user_id = $user_id, log_entity_id = $payment_id");
    }
}

function createBulkPayment(
    $bulk_payment
){

    // Access global variables
    global $mysqli, $user_id, $ip, $user_agent, $config_base_url, $config_invoice_from_name, $config_invoice_from_email, $currency_format;

    $date = $bulk_payment['date'];
    $bulk_payment_amount = floatval($bulk_payment['amount']);
    $bulk_payment_amount_static = $bulk_payment_amount;
    $account = intval($bulk_payment['account']);
    $payment_method = sanitizeInput($bulk_payment['method']);
    $reference = sanitizeInput($bulk_payment['reference']);
    $client_id = intval($bulk_payment['client_id']);
    $email_receipt = intval($bulk_payment['email_receipt']);
    $total_client_balance = getClientBalance($client_id);
    $currency_code = getSettingValue('company_currency');

    $email_body_invoices = "";

    $_SESSION['alert_message'] = "";

    // Check if bulk_payment_amount exceeds total_account_balance
    if ($bulk_payment_amount > $total_client_balance) {
        // Create new credit for the overpayment
        $credit_amount = $bulk_payment_amount - $total_client_balance;
        $bulk_payment_amount = $total_client_balance;

        // Add Credit
        $credit_query = "INSERT INTO credits SET credit_amount = $credit_amount, credit_currency_code = '$currency_code', credit_date = '$date', credit_reference = 'Overpayment: $reference', credit_client_id = $client_id, credit_account_id = $account, credit_payment_id = 0";
        mysqli_query($mysqli, $credit_query);
        $credit_id = mysqli_insert_id($mysqli);
    }

    // Get Invoices
    $sql_invoices = "SELECT * FROM invoices
        WHERE invoice_status != 'Draft'
        AND invoice_status != 'Paid'
        AND invoice_status != 'Cancelled'
        AND invoice_client_id = $client_id
        ORDER BY invoice_id ASC";
    $result_invoices = mysqli_query($mysqli, $sql_invoices);

    // Loop Through Each Invoice and create payment
    while ($row = mysqli_fetch_array($result_invoices)) {
        $invoice_id = intval($row['invoice_id']);
        $invoice_prefix = sanitizeInput($row['invoice_prefix']);
        $invoice_number = intval($row['invoice_number']);
        $invoice_amount = floatval($row['invoice_amount']);
        $invoice_url_key = sanitizeInput($row['invoice_url_key']);
        $invoice_balance_query = "SELECT SUM(payment_amount) AS amount_paid FROM payments WHERE payment_invoice_id = $invoice_id";
        $result_amount_paid = mysqli_query($mysqli, $invoice_balance_query);
        $row_amount_paid = mysqli_fetch_array($result_amount_paid);
        $amount_paid = floatval($row_amount_paid['amount_paid']);
        $invoice_balance = $invoice_amount - $amount_paid;

        if ($bulk_payment_amount <= 0) {
            break; // Stop the loop if the bulk payment amount is 0
        }

        if ($invoice_balance <= 0) {
            continue; // Skip the invoice if it has been paid
        }

        if ($bulk_payment_amount >= $invoice_balance) {
            $payment_amount = $invoice_balance;
            $invoice_status = "Paid";
        } else {
            $payment_amount = $bulk_payment_amount;
            $invoice_status = "Partial";
        }

        // Create Payment
        $payment = [
            'date' => $date,
            'amount' => $payment_amount,
            'currency_code' => $currency_code,
            'account' => $account,
            'method' => $payment_method,
            'reference' => $reference,
            'invoice_id' => $invoice_id,
            'email_receipt' => 0,
            'balance' => $invoice_balance
        ];
        createPayment($payment);

        // Update Bulk Payment Amount
        $bulk_payment_amount -= $payment_amount;

    } // End Invoice Loop

    // Logging
    mysqli_query($mysqli,"INSERT INTO logs SET log_type = 'Payment', log_action = 'Create', log_description = 'Bulk Payment of $bulk_payment_amount_static', log_ip = '$ip', log_user_agent = '$user_agent', log_client_id = $client_id, log_user_id = $user_id");

}

function readPayment(
    $payment_id
) {
    // Access global variables
    global $mysqli;

    $sql = mysqli_query($mysqli, "SELECT * FROM payments WHERE payment_id = $payment_id");
    $row = mysqli_fetch_array($sql);

    return $row;
}

function deletePayment(
    $payment_id
){
    // Access global variables
    global $mysqli, $user_id, $ip, $user_agent;

    $sql = mysqli_query($mysqli,"SELECT * FROM payments WHERE payment_id = $payment_id");
    $row = mysqli_fetch_array($sql);
    $invoice_id = intval($row['payment_invoice_id']);
    $deleted_payment_amount = floatval($row['payment_amount']);

    //Add up all the payments for the invoice and get the total amount paid to the invoice
    $sql_total_payments_amount = mysqli_query($mysqli,"SELECT SUM(payment_amount) AS total_payments_amount FROM payments WHERE payment_invoice_id = $invoice_id");
    $row = mysqli_fetch_array($sql_total_payments_amount);
    $total_payments_amount = floatval($row['total_payments_amount']);

    //Get the invoice total
    $sql = mysqli_query($mysqli,"SELECT * FROM invoices WHERE invoice_id = $invoice_id");
    $row = mysqli_fetch_array($sql);
    $invoice_amount = floatval($row['invoice_amount']);

    //Calculate the Invoice balance
    $invoice_balance = $invoice_amount - $total_payments_amount + $deleted_payment_amount;

    //Determine if invoice has been paid
    if ($invoice_balance == 0) {
        $invoice_status = "Paid";
    } else {
        $invoice_status = "Partial";
    }

    //Update Invoice Status
    mysqli_query($mysqli,"UPDATE invoices SET invoice_status = '$invoice_status' WHERE invoice_id = $invoice_id");

    //Add Payment to History
    mysqli_query($mysqli,"INSERT INTO history SET history_status = '$invoice_status', history_description = 'Payment deleted', history_invoice_id = $invoice_id");

    mysqli_query($mysqli,"DELETE FROM payments WHERE payment_id = $payment_id");

    //Logging
    mysqli_query($mysqli,"INSERT INTO logs SET log_type = 'Payment', log_action = 'Delete', log_description = '$payment_id', log_ip = '$ip', log_user_agent = '$user_agent', log_user_id = $user_id");
}

function getPaymentsForInvoice(
    $invoice_id
) {
    // Access global variables
    global $mysqli;

    $sql = mysqli_query($mysqli,"SELECT * FROM payments WHERE payment_invoice_id = $invoice_id ORDER BY payment_date ASC");
    $payments = [];
    while ($row = mysqli_fetch_array($sql)) {
        $payments[] = $row;
    }

    return $payments;
}
