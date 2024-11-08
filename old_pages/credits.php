<?php

// Default Column Sortby/Order Filter
$sort = "client_id";
$order = "DESC";

require_once "/var/www/portal.twe.tech/includes/inc_all.php";


//Rebuild URL

$sql = mysqli_query(
    $mysqli,
    "SELECT * FROM credits
    LEFT JOIN clients ON credit_client_id = client_id
    LEFT JOIN accounts ON credit_account_id = account_id
    WHERE credit_archived_at IS NULL
    ORDER BY $sort $order"
);

$num_rows = mysqli_num_rows($sql);

?>

<div class="card">
    <div class="card-header py-3">
        <h3 class="card-title"><i class="fas fa-fw fa-credit-card mr-2"></i>Credits</h3>
    </div>

    <div class="card-body">
        <div class="card-datatable table-responsive container-fluid  pt-0">               
            <table class="datatables-basic table border-top">
                <thead class="text-dark <?php if ($num_rows == 0) { echo "d-none"; } ?>">
                    <tr>
                        <th><a class="text-dark"
                                href="?<?= $url_query_strings_sort; ?>&sort=credit_client&order=<?= $disp; ?>">Client Name</a></th>
                        <th><a class="text-dark"
                                href="?<?= $url_query_strings_sort; ?>&sort=credit_account&order=<?= $disp; ?>">Account
                                Name</a></th>
                        <th class="text-right
                            <?php if ($sort == "credit_amount") { echo "sorting-$order"; } ?>">
                            <a class="text-dark"
                                href="?<?= $url_query_strings_sort; ?>&sort=credit_amount&order=<?= $disp; ?>">Amount</a>
                        </th>
                        <th><a class="text-dark"
                                href="?<?= $url_query_strings_sort; ?>&sort=credit_date&order=<?= $disp; ?>">Date</a>
                        </th>
                        <th><a class="text-dark"
                                href="?<?= $url_query_strings_sort; ?>&sort=credit_reference&order=<?= $disp; ?>">Reference</a>
                        </th>
                        <th><a class="text-dark"
                                href="?<?= $url_query_strings_sort; ?>&sort=credit_payment&order=<?= $disp; ?>">Origin</a>
                        </th>

                        <th>Actions</th>

                    </tr>
                </thead>
                <tbody>
                <?php
                    while ($row = mysqli_fetch_array($sql)) {
                        $credit_id = intval($row['credit_id']);
                        $credit_amount = floatval($row['credit_amount']);

                        // if amount is 0, delete credit
                        if($credit_amount == 0) {
                            mysqli_query($mysqli, "DELETE FROM credits WHERE credit_id = $credit_id");
                            continue;
                        }

                        $credit_currency_code = sanitizeInput($row['credit_currency_code']);
                        $credit_date = $row['credit_date'];
                        $credit_reference = intval($row['credit_reference']);
                        $credit_client_id = intval($row['credit_client_id']);
                        $credit_payment_id = intval($row['credit_payment_id']);
                        $credit_account_id = intval($row['credit_account_id']);
                        $client_name = sanitizeInput($row['client_name']);
                        $client_balance = getClientBalance($credit_client_id);

                        // Get account name from DB
                        if($credit_account_id != null) {
                            $accountQuery = mysqli_query($mysqli, "SELECT * FROM accounts WHERE account_id = $credit_account_id");
                            $account = mysqli_fetch_array($accountQuery);
                            $account_name = sanitizeinput($account['account_name']);
                        } else {
                            $account_name = "Unassigned";
                        }

                        // Get payment invoice and reference from DB
                        if($credit_payment_id != null) {
                            $paymentQuery = mysqli_query($mysqli, "SELECT * FROM payments WHERE payment_id = $credit_payment_id");
                            $payment = mysqli_fetch_array($paymentQuery);
                            $payment_invoice = intval($payment['payment_invoice_id']);
                            $payment_reference = intval($payment['payment_reference']);
                        } else {
                            $payment_invoice = "Unassigned";
                            $payment_reference = "Unassigned";
                        }

                        // Get invoice prefix and number from DB
                        if($payment_invoice != "Unassigned") {
                            $invoiceQuery = mysqli_query($mysqli, "SELECT * FROM invoices WHERE invoice_id = $payment_invoice");
                            $invoice = mysqli_fetch_array($invoiceQuery);
                            $invoice_prefix = sanitizeInput($invoice['invoice_prefix']);
                            $invoice_number = intval($invoice['invoice_number']);
                            $payment_invoice_display = "Payment for: " . $invoice_prefix . $invoice_number;
                        } else {
                            $invoice_prefix = "Unassigned";
                            $invoice_number = "Unassigned";
                        }

                        $credit_display_amount = numfmt_format_currency($currency_format, $credit_amount, $credit_currency_code);
                        ?>

                        <tr>
                            <td><a href="client_overview.php?client_id=<?= $credit_client_id; ?>"><?= $client_name; ?> (Balance: <?= $client_balance; ?>)</a>
                            <td><?= $account_name; ?></td>
                            <td class="text-right
                                <?php if ($sort == "credit_amount") { echo "sorting-$order"; } ?>">
                                <?= $credit_display_amount; ?>
                            </td>
                            <td><?= $credit_date; ?></td>
                            <td><?= $credit_reference; ?></td>
                            <td><a href="client_payments.php?client_id=<?= $credit_client_id; ?>"><?= $payment_invoice_display; ?></a></td>
                            <td>
                                <?php if($client_balance > 0) { ?>
                                    <a href="/post.php?apply_credit=<?= $credit_id; ?>" class="btn btn-sm btn-soft-primary"
                                    title="Apply"><i class="fas fa-credit-card"></i></a>
                                <?php } ?>
                                <a href="/post.php?delete_credit=<?= $credit_id; ?>" class="btn btn-sm btn-danger"
                                title="Delete"><i class="fas fa-trash"></i></a>
                            </td>
                        </tr>
                    <?php } ?>

                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once '/var/www/portal.twe.tech/includes/footer.php';
 ?>
