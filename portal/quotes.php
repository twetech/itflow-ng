<?php
/*
 * Client Portal
 * Quotes for PTC / billing contacts
 */



require_once "/var/www/nestogy/includes/inc_portal.php";

if ($contact_primary == 0 && !$contact_is_billing_contact) {
    header("Location: portal_post.php?logout");
    exit();
}

$quotes_sql = mysqli_query($mysqli, "SELECT * FROM quotes WHERE quote_client_id = $client_id AND quote_status != 'Draft' ORDER BY quote_date DESC");
?>

<div class="row">

    <div class="col-md-10">

        <table id=responsive class="responsive table tabled-bordered border border-dark">
            <thead class="thead-dark">
            <tr>
                <th>#</th>
                <th>Scope</th>
                <th>Amount</th>
                <th>Date</th>
                <th>Status</th>
            </tr>
            </thead>
            <tbody>

            <?php
            while ($row = mysqli_fetch_array($quotes_sql)) {
                $quote_id = intval($row['quote_id']);
                $quote_prefix = nullable_htmlentities($row['quote_prefix']);
                $quote_number = intval($row['quote_number']);
                $quote_scope = nullable_htmlentities($row['quote_scope']);
                $quote_status = nullable_htmlentities($row['quote_status']);
                $quote_date = nullable_htmlentities($row['quote_date']);
                $quote_amount = floatval($row['quote_amount']);
                $quote_url_key = nullable_htmlentities($row['quote_url_key']);

                if (empty($quote_scope)) {
                    $quote_scope_display = "-";
                } else {
                    $quote_scope_display = $quote_scope;
                }

                if ($quote_status == "Sent") {
                    $quote_badge_color = "warning text-white";
                } elseif ($quote_status == "Viewed") {
                    $quote_badge_color = "primary";
                } elseif ($quote_status == "Accepted") {
                    $quote_badge_color = "success";
                } elseif ($quote_status == "Declined") {
                    $quote_badge_color = "danger";
                } elseif ($quote_status == "Invoiced") {
                    $quote_badge_color = "info";
                } else {
                    $quote_badge_color = "secondary";
                }

                ?>

                <tr>
                    <td><a target="_blank" href="//<?= $config_base_url ?>/portal/guest_view_quote.php?quote_id=<?= "$quote_id&url_key=$quote_url_key"?>"> <?= "$quote_prefix$quote_number"; ?></a></td>
                    <td><?= $quote_scope_display; ?></td>
                    <td><?= numfmt_format_currency($currency_format, $quote_amount, $company_currency); ?></td>
                    <td><?= $quote_date; ?></td>
                    <td>
                        <span class="p-2 badge badge-<?= $quote_badge_color; ?>">
                            <?= $quote_status; ?>
                        </span>
                    </td>

                </tr>
            <?php } ?>

            </tbody>
        </table>

    </div>

</div>

<?php
require_once "portal_footer.php";
