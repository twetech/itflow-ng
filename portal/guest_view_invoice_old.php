<?php

require_once "/var/www/nestogy/portal/guest_header.php";

if (!isset($_GET['invoice_id'], $_GET['url_key'])) {
    echo "<br><h2>Oops, something went wrong! Please raise a ticket if you believe this is an error.</h2>";
    require_once "portal/guest_footer.php";

    exit();
}

$url_key = sanitizeInput($_GET['url_key']);
$invoice_id = intval($_GET['invoice_id']);

$sql = mysqli_query(
    $mysqli,
    "SELECT * FROM invoices
    LEFT JOIN clients ON invoice_client_id = client_id
    LEFT JOIN locations ON clients.client_id = locations.location_client_id AND location_primary = 1
    LEFT JOIN contacts ON clients.client_id = contacts.contact_client_id AND contact_primary = 1
    WHERE invoice_id = $invoice_id
    AND invoice_url_key = '$url_key'"
);

if (mysqli_num_rows($sql) !== 1) {
    // Invalid invoice/key
    echo "<br><h2>Oops, something went wrong! Please raise a ticket if you believe this is an error.</h2>";
    require_once "portal/guest_footer.php";

    exit();
}

$row = mysqli_fetch_array($sql);

$invoice_id = intval($row['invoice_id']);
$invoice_prefix = nullable_htmlentities($row['invoice_prefix']);
$invoice_number = intval($row['invoice_number']);
$invoice_status = nullable_htmlentities($row['invoice_status']);
$invoice_date = nullable_htmlentities($row['invoice_date']);
$invoice_due = nullable_htmlentities($row['invoice_due']);
$invoice_discount = floatval($row['invoice_discount_amount']);
$invoice_amount = floatval($row['invoice_amount']);
$invoice_currency_code = nullable_htmlentities($row['invoice_currency_code']);
$invoice_note = nullable_htmlentities($row['invoice_note']);
$invoice_category_id = intval($row['invoice_category_id']);
$client_id = intval($row['client_id']);
$client_name = nullable_htmlentities($row['client_name']);
$client_name_escaped = sanitizeInput($row['client_name']);
$location_address = nullable_htmlentities($row['location_address']);
$location_city = nullable_htmlentities($row['location_city']);
$location_state = nullable_htmlentities($row['location_state']);
$location_zip = nullable_htmlentities($row['location_zip']);
$contact_email = nullable_htmlentities($row['contact_email']);
$contact_phone = formatPhoneNumber($row['contact_phone']);
$contact_extension = nullable_htmlentities($row['contact_extension']);
$contact_mobile = formatPhoneNumber($row['contact_mobile']);
$client_website = nullable_htmlentities($row['client_website']);
$client_currency_code = nullable_htmlentities($row['client_currency_code']);
$client_net_terms = intval($row['client_net_terms']);
if ($client_net_terms == 0) {
    $client_net_terms = intval($row['config_default_net_terms']);
}

$sql = mysqli_query($mysqli, "SELECT * FROM companies, settings WHERE companies.company_id = settings.company_id AND companies.company_id = 1");
$row = mysqli_fetch_array($sql);

$company_name = nullable_htmlentities($row['company_name']);
$company_address = nullable_htmlentities($row['company_address']);
$company_city = nullable_htmlentities($row['company_city']);
$company_state = nullable_htmlentities($row['company_state']);
$company_zip = nullable_htmlentities($row['company_zip']);
$company_phone = formatPhoneNumber($row['company_phone']);
$company_email = nullable_htmlentities($row['company_email']);
$company_website = nullable_htmlentities($row['company_website']);
$company_logo = nullable_htmlentities($row['company_logo']);
if (!empty($company_logo)) {
    $company_logo_base64 = base64_encode(file_get_contents("/uploads/settings/$company_logo"));
}
$company_locale = nullable_htmlentities($row['company_locale']);
$config_invoice_footer = nullable_htmlentities($row['config_invoice_footer']);
$config_stripe_enable = intval($row['config_stripe_enable']);
$config_stripe_percentage_fee = floatval($row['config_stripe_percentage_fee']);
$config_stripe_flat_fee = floatval($row['config_stripe_flat_fee']);
$config_stripe_client_pays_fees = intval($row['config_stripe_client_pays_fees']);

//Set Currency Format
$currency_format = numfmt_create($company_locale, NumberFormatter::CURRENCY);

$invoice_tally_total = 0; // Default

//Set Badge color based off of invoice status
$invoice_badge_color = getInvoiceBadgeColor($invoice_status);

//Update status to Viewed only if invoice_status = "Sent"
if ($invoice_status == 'Sent') {
    mysqli_query($mysqli, "UPDATE invoices SET invoice_status = 'Viewed' WHERE invoice_id = $invoice_id");
}

//Mark viewed in history
mysqli_query($mysqli, "INSERT INTO history SET history_status = '$invoice_status', history_description = 'Invoice viewed - $ip - $os - $browser', history_invoice_id = $invoice_id");

$sql_payments = mysqli_query($mysqli, "SELECT * FROM payments, accounts WHERE payment_account_id = account_id AND payment_invoice_id = $invoice_id ORDER BY payments.payment_id DESC");

//Add up all the payments for the invoice and get the total amount paid to the invoice
$sql_amount_paid = mysqli_query($mysqli, "SELECT SUM(payment_amount) AS amount_paid FROM payments WHERE payment_invoice_id = $invoice_id");
$row = mysqli_fetch_array($sql_amount_paid);
$amount_paid = floatval($row['amount_paid']);

// Calculate the balance owed
$balance = $invoice_amount - $amount_paid;

// Calculate Gateway Fee
if ($config_stripe_client_pays_fees == 1) {
    $balance_before_fees = $balance;
    // See here for passing costs on to client https://support.stripe.com/questions/passing-the-stripe-fee-on-to-customers
    // Calculate the amount to charge the client
    $balance_to_pay = ($balance + $config_stripe_flat_fee) / (1 - $config_stripe_percentage_fee);
    // Calculate the fee amount
    $gateway_fee = round($balance_to_pay - $balance_before_fees, 2);
}

//check to see if overdue
$invoice_color = $invoice_badge_color; // Default
if ($invoice_status !== "Paid" && $invoice_status !== "Draft" && $invoice_status !== "Cancelled") {
    $unixtime_invoice_due = strtotime($invoice_due) + 86400;
    if ($unixtime_invoice_due < time()) {
        $invoice_color = "text-danger";
    }
}

// Invoice individual items
$sql_invoice_items = mysqli_query($mysqli, "SELECT * FROM invoice_items WHERE item_invoice_id = $invoice_id ORDER BY item_order ASC");

?>

    <div class="card">
        <div class="card-header bg-light d-print-none">
            <div class="float-right">
                <a class="btn btn-light" data-bs-toggle="collapse" href="#collapsePreviousInvoices"><i class="fas fa-fw fa-history mr-2"></i>Invoice History</a>
                <a class="btn btn-label-primary" href="#" onclick="window.print();"><i class="fas fa-fw fa-print mr-2"></i>Print</a>
                <a class="btn btn-label-primary" href="#" onclick="pdfMake.createPdf(docDefinition).download('<?= strtoAZaz09(html_entity_decode("$invoice_date-$company_name-Invoice-$invoice_prefix$invoice_number")); ?>');"><i class="fa fa-fw fa-download mr-2"></i>Download</a>
                <?php
                if ($invoice_status !== "Paid" && $invoice_status  !== "Cancelled" && $invoice_status !== "Draft" && $config_stripe_enable == 1) { ?>
                    <a class="btn btn-success" href="portal/guest_pay_invoice_stripe.php?invoice_id=<?= $invoice_id; ?>&url_key=<?= $url_key; ?>"><i class="fa fa-fw fa-credit-card mr-2"></i>Pay Online <?php if($config_stripe_client_pays_fees == 1) { echo "(Gateway Fee: " .  numfmt_format_currency($currency_format, $gateway_fee, $invoice_currency_code) . ")"; } ?></a>
                <?php } ?>
            </div>
        </div>
        <div class="card-body">
            <div class="row mb-4">
                <div class="col-2">
                    <img class="img-fluid" src="<?= "/uploads/settings/$company_logo"; ?>">
                </div>
                <div class="col-10">
                    <?php if ($invoice_status == "Paid") { ?>
                        <div class="ribbon-wrapper">
                            <div class="ribbon bg-success">
                                <?= $invoice_status; ?>
                            </div>
                        </div>
                    <?php } ?>
                    <h3 class="text-right mt-5"><strong>Invoice</strong><br><small class="text-secondary"><?= "$invoice_prefix$invoice_number"; ?></small></h3>
                </div>
            </div>
            <div class="row mb-4">
                <div class="col">
                    <ul class="list-unstyled">
                        <li><h4><strong><?= $company_name; ?></strong></h4></li>
                        <li><?= $company_address; ?></li>
                        <li><?= "$company_city $company_state $company_zip"; ?></li>
                        <li><?= $company_phone; ?></li>
                        <li><?= $company_email; ?></li>
                    </ul>

                </div>
                <div class="col">

                    <ul class="list-unstyled text-right">
                        <li><h4><strong><?= $client_name; ?></strong></h4></li>
                        <li><?= $location_address; ?></li>
                        <li><?= "$location_city $location_state $location_zip"; ?></li>
                        <li><?= "$contact_phone $contact_extension"; ?></li>
                        <li><?= $contact_mobile; ?></li>
                        <li><?= $contact_email; ?></li>
                    </ul>

                </div>
            </div>
            <div class="row mb-4">
                <div class="col-sm-8">
                </div>
                <div class="col-sm-4">
                    <table id=responsive class="responsive table">
                        <tr>
                            <td>Date</td>
                            <td class="text-right"><?= $invoice_date; ?></td>
                        </tr>
                        <tr class="text-bold">
                            <td>Due</td>
                            <td class="text-right"><div class="<?= $invoice_color; ?>"><?= $invoice_due; ?></div></td>
                        </tr>
                    </table>
                </div>
            </div>

            <div class="row mb-4">
                <div class="col-md-12">
                    <div class="card">
                                <div class="card-datatable table-responsive container-fluid  pt-0">               
                            <table id=responsive class="responsive table">
                                <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Description</th>
                                    <th class="text-center">Qty</th>
                                    <th class="text-right">Price</th>
                                    <th class="text-right">Tax</th>
                                    <th class="text-right">Total</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php

                                $total_tax = 0.00;
                                $sub_total = 0.00 - $invoice_discount;

                                while ($row = mysqli_fetch_array($sql_invoice_items)) {
                                    $item_id = intval($row['item_id']);
                                    $item_name = nullable_htmlentities($row['item_name']);
                                    $item_description = nullable_htmlentities($row['item_description']);
                                    $item_quantity = floatval($row['item_quantity']);
                                    $item_price = floatval($row['item_price']);
                                    $item_tax = floatval($row['item_tax']);
                                    $item_total = floatval($row['item_total']);
                                    $total_tax = $item_tax + $total_tax;
                                    $sub_total = $item_price * $item_quantity + $sub_total;
                                    ?>

                                    <tr>
                                        <td><?= $item_name; ?></td>
                                        <td><?= nl2br($item_description); ?></td>
                                        <td class="text-center"><?= $item_quantity; ?></td>
                                        <td class="text-right"><?= numfmt_format_currency($currency_format, $item_price, $invoice_currency_code); ?></td>
                                        <td class="text-right"><?= numfmt_format_currency($currency_format, $item_tax, $invoice_currency_code); ?></td>
                                        <td class="text-right"><?= numfmt_format_currency($currency_format, $item_total, $invoice_currency_code); ?></td>
                                    </tr>

                                <?php } ?>

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mb-4">
                <div class="col-sm-7">
                    <?php if (!empty($invoice_note)) { ?>
                        <div class="card">
                            <div class="card-body">
                                <?= nl2br($invoice_note); ?>
                            </div>
                        </div>
                    <?php } ?>
                </div>
                <div class="col-sm-3 offset-sm-2">
                    <table id=responsive class="responsive table table-borderless">
                        <tbody>
                        <tr class="border-bottom">
                            <td>Subtotal</td>
                            <td class="text-right"><?= numfmt_format_currency($currency_format, $sub_total, $invoice_currency_code); ?></td>
                        </tr>
                        <?php
                        if ($invoice_discount > 0) {
                            ?>
                            <tr class="border-bottom">
                                <td>Discount</td>
                                <td class="text-right">-<?= numfmt_format_currency($currency_format, $invoice_discount, $invoice_currency_code); ?></td>
                            </tr>
                        <?php
                        }
                        ?>
                        <?php if ($total_tax > 0) { ?>
                            <tr class="border-bottom">
                                <td>Tax</td>
                                <td class="text-right"><?= numfmt_format_currency($currency_format, $total_tax, $invoice_currency_code); ?></td>
                            </tr>
                        <?php } ?>
                        <tr class="border-bottom">
                            <td>Total</td>
                            <td class="text-right"><?= numfmt_format_currency($currency_format, $invoice_amount, $invoice_currency_code); ?></td>
                        </tr>
                        <?php if ($amount_paid > 0) { ?>
                            <tr class="border-bottom">
                                <td><div class="text-success">Paid</div></td>
                                <td class="text-right text-success"><?= numfmt_format_currency($currency_format, $amount_paid, $invoice_currency_code); ?></td>
                            </tr>
                        <?php
                        } 
                        ?>
                        <tr class="border-bottom">
                            <td><strong>Balance</strong></td>
                            <td class="text-right"><strong><?= numfmt_format_currency($currency_format, $balance, $invoice_currency_code); ?></strong></td>
                        </tr>

                        </tbody>
                    </table>
                </div>
            </div>

            <hr class="mt-5">

            <div class="text-center"><?= nl2br($config_invoice_footer); ?></div>
        </div>
    </div>

    <script src='plugins/pdfmake/pdfmake.min.js'></script>
    <script src='plugins/pdfmake/vfs_fonts.js'></script>
    <script>

        var docDefinition = {
            info: {
                title: <?= json_encode(html_entity_decode($company_name) . "- Invoice") ?>,
                author: <?= json_encode(html_entity_decode($company_name)) ?>
            },

            //watermark: {text: '<?= $invoice_status; ?>', color: 'lightgrey', opacity: 0.3, bold: true, italics: false},

            content: [
                // Header
                {
                    columns: [
                        <?php if (!empty($company_logo_base64)) { ?>
                        {
                            image: <?= json_encode("data:image;base64,$company_logo_base64") ?>,
                            width: 120
                        },
                        <?php } ?>

                        [
                            {
                                text: 'Invoice',
                                style: 'invoiceTitle',
                                width: '*'
                            },
                            {
                                text: <?= json_encode(html_entity_decode("$invoice_prefix$invoice_number")) ?>,
                                style: 'invoiceNumber',
                                width: '*'
                            },
                        ],
                    ],
                },
                // Billing Headers
                {
                    columns: [
                        {
                            text: <?= json_encode(html_entity_decode($company_name)) ?>,
                            style: 'invoiceBillingTitle',
                        },
                        {
                            text: <?= json_encode(html_entity_decode($client_name)) ?>,
                            style: 'invoiceBillingTitleClient',
                        },
                    ]
                },
                // Billing Address
                {
                    columns: [
                        {
                            text: <?= json_encode(html_entity_decode("$company_address \n $company_city $company_state $company_zip \n $company_phone \n $company_website")) ?>,
                            style: 'invoiceBillingAddress'
                        },
                        {
                            text: <?= json_encode(html_entity_decode("$location_address \n $location_city $location_state $location_zip \n $contact_email \n $contact_phone")) ?>,
                            style: 'invoiceBillingAddressClient'
                        },
                    ]
                },
                //Invoice Dates Table
                {
                    table: {
                        // headers are automatically repeated if the table spans over multiple pages
                        // you can declare how many rows should be treated as headers
                        headerRows: 0,
                        widths: [ '*',80, 80 ],

                        body: [
                            // Total
                            [
                                {
                                    text: '',
                                    rowSpan: 3
                                },
                                {},
                                {},
                            ],
                            [
                                {},
                                {
                                    text: 'Date',
                                    style: 'invoiceDateTitle'
                                },
                                {
                                    text: <?= json_encode(html_entity_decode($invoice_date)) ?>,
                                    style: 'invoiceDateValue'
                                },
                            ],
                            [
                                {},
                                {
                                    text: 'Due',
                                    style: 'invoiceDueDateTitle'
                                },
                                {
                                    text: <?= json_encode(html_entity_decode($invoice_due)) ?>,
                                    style: 'invoiceDueDateValue'
                                },
                            ],
                        ]
                    }, // table
                    layout: 'lightHorizontalLines'
                },
                // Line breaks
                '\n\n',
                // Items
                {
                    table: {
                        // headers are automatically repeated if the table spans over multiple pages
                        // you can declare how many rows should be treated as headers
                        headerRows: 1,
                        widths: [ '*', 40, 'auto', 'auto', 80 ],

                        body: [
                            // Table Header
                            [
                                {
                                    text: 'Product',
                                    style: [ 'itemsHeader', 'left']
                                },
                                {
                                    text: 'Qty',
                                    style: [ 'itemsHeader', 'center']
                                },
                                {
                                    text: 'Price',
                                    style: [ 'itemsHeader', 'right']
                                },
                                {
                                    text: 'Tax',
                                    style: [ 'itemsHeader', 'right']
                                },
                                {
                                    text: 'Total',
                                    style: [ 'itemsHeader', 'right']
                                }
                            ],
                            // Items
                            <?php
                            $total_tax = 0;
                            $sub_total = 0;

                            $sql_invoice_items = mysqli_query($mysqli, "SELECT * FROM invoice_items WHERE item_invoice_id = $invoice_id ORDER BY item_order ASC");

                            while ($row = mysqli_fetch_array($sql_invoice_items)) {
                            $item_name = $row['item_name'];
                            $item_description = $row['item_description'];
                            $item_quantity = $row['item_quantity'];
                            $item_price = $row['item_price'];
                            $item_subtotal = $row['item_price'];
                            $item_tax = $row['item_tax'];
                            $item_total = $row['item_total'];
                            $tax_id = $row['item_tax_id'];
                            $total_tax = $item_tax + $total_tax;
                            $sub_total = $item_price * $item_quantity + $sub_total;
                            ?>

                            // Item
                            [
                                [
                                    {
                                        text: <?= json_encode($item_name) ?>,
                                        style:'itemTitle'
                                    },
                                    {
                                        text: <?= json_encode($item_description) ?>,
                                        style:'itemDescription'
                                    }
                                ],
                                {
                                    text: <?= json_encode($item_quantity) ?>,
                                    style: 'itemQty'
                                },
                                {
                                    text: <?= json_encode(numfmt_format_currency($currency_format, $item_price, $invoice_currency_code)) ?>,
                                    style: 'itemNumber'
                                },
                                {
                                    text: <?= json_encode(numfmt_format_currency($currency_format, $item_tax, $invoice_currency_code)) ?>,
                                    style: 'itemNumber'
                                },
                                {
                                    text:  <?= json_encode(numfmt_format_currency($currency_format, $item_total, $invoice_currency_code)) ?>,
                                    style: 'itemNumber'
                                }
                            ],

                            <?php
                            }
                            ?>
                            // END Items
                        ]
                    }, // table
                    layout: 'lightHorizontalLines'
                },
                // TOTAL
                {
                    table: {
                        // headers are automatically repeated if the table spans over multiple pages
                        // you can declare how many rows should be treated as headers
                        headerRows: 0,
                        widths: [ '*','auto', 80 ],

                        body: [
                            // Total
                            [
                                {
                                    text: 'Notes',
                                    style: 'notesTitle'
                                },
                                {},
                                {}
                            ],
                            [
                                {
                                    rowSpan: '*',
                                    text: <?= json_encode(html_entity_decode($invoice_note)) ?>,
                                    style: 'notesText'
                                },
                                {
                                    text: 'Subtotal',
                                    style: 'itemsFooterSubTitle'
                                },
                                {
                                    text: <?= json_encode(numfmt_format_currency($currency_format, $sub_total, $invoice_currency_code)) ?>,
                                    style: 'itemsFooterSubValue'
                                }
                            ],
                            <?php if ($invoice_discount > 0) { ?>
                            [
                                {},
                                {
                                    text: 'Discount',
                                    style: 'itemsFooterSubTitle'
                                },
                                {
                                    text: <?= json_encode(numfmt_format_currency($currency_format, -$invoice_discount, $invoice_currency_code)) ?>,
                                    style: 'itemsFooterSubValue'
                                }
                            ],
                            <?php } ?>
                            <?php if ($total_tax > 0) { ?>
                            [
                                {},
                                {
                                    text: 'Tax',
                                    style: 'itemsFooterSubTitle'
                                },
                                {
                                    text: <?= json_encode(numfmt_format_currency($currency_format, $total_tax, $invoice_currency_code)) ?>,
                                    style: 'itemsFooterSubValue'
                                }
                            ],
                            <?php } ?>
                            [
                                {},
                                {
                                    text: 'Total',
                                    style: 'itemsFooterSubTitle'
                                },
                                {
                                    text: <?= json_encode(numfmt_format_currency($currency_format, $invoice_amount, $invoice_currency_code)) ?>,
                                    style: 'itemsFooterSubValue'
                                }
                            ],
                            <?php if ($amount_paid > 0) { ?>
                            [
                                {},
                                {
                                    text: 'Paid',
                                    style: 'itemsFooterSubTitle'
                                },
                                {
                                    text: <?= json_encode(numfmt_format_currency($currency_format, $amount_paid, $invoice_currency_code)) ?>,
                                    style: 'itemsFooterSubValue'
                                }
                            ],
                            <?php } ?>
                            [
                                {},
                                {
                                    text: 'Balance',
                                    style: 'itemsFooterTotalTitle'
                                },
                                {
                                    text: <?= json_encode(numfmt_format_currency($currency_format, $balance, $invoice_currency_code)) ?>,

                                    style: 'itemsFooterTotalTitle'
                                }
                            ],
                        ]
                    }, // table
                    layout: 'lightHorizontalLines'
                },
                // TERMS / FOOTER
                {
                    text: <?= json_encode(html_entity_decode($config_invoice_footer)) ?>,
                    style: 'documentFooterCenter'
                }
            ], //End Content,
            styles: {
                // Document Footer
                documentFooterCenter: {
                    fontSize: 9,
                    margin: [10,50,10,10],
                    alignment: 'center'
                },
                // Invoice Title
                invoiceTitle: {
                    fontSize: 18,
                    bold: true,
                    alignment: 'right',
                    margin: [0,0,0,3]
                },
                // Invoice Number
                invoiceNumber: {
                    fontSize: 14,
                    alignment: 'right'
                },
                // Billing Headers
                invoiceBillingTitle: {
                    fontSize: 14,
                    bold: true,
                    alignment: 'left',
                    margin: [0,20,0,5]
                },
                invoiceBillingTitleClient: {
                    fontSize: 14,
                    bold: true,
                    alignment: 'right',
                    margin: [0,20,0,5]
                },
                // Billing Details
                invoiceBillingAddress: {
                    fontSize: 10,
                    lineHeight: 1.2
                },
                invoiceBillingAddressClient: {
                    fontSize: 10,
                    lineHeight: 1.2,
                    alignment: 'right',
                    margin: [0,0,0,30]
                },
                // Invoice Date
                invoiceDateTitle: {
                    fontSize: 10,
                    alignment: 'left',
                    margin: [0,5,0,5]
                },
                invoiceDateValue: {
                    fontSize: 10,
                    alignment: 'right',
                    margin: [0,5,0,5]
                },
                // Invoice Due Date
                invoiceDueDateTitle: {
                    fontSize: 10,
                    bold: true,
                    alignment: 'left',
                    margin: [0,5,0,5]
                },
                invoiceDueDateValue: {
                    fontSize: 10,
                    bold: true,
                    alignment: 'right',
                    margin: [0,5,0,5]
                },
                // Items Header
                itemsHeader: {
                    fontSize: 10,
                    margin: [0,5,0,5],
                    bold: true,
                    alignment: 'right'
                },
                // Item Title
                itemTitle: {
                    fontSize: 10,
                    bold: true,
                    margin: [0,5,0,3]
                },
                itemDescription: {
                    italics: true,
                    fontSize: 9,
                    lineHeight: 1.1,
                    margin: [0,3,0,5]
                },
                itemQty: {
                    fontSize: 10,
                    margin: [0,5,0,5],
                    alignment: 'center'
                },
                itemNumber: {
                    fontSize: 10,
                    margin: [0,5,0,5],
                    alignment: 'right'
                },
                itemTotal: {
                    fontSize: 10,
                    margin: [0,5,0,5],
                    bold: true,
                    alignment: 'right'
                },
                // Items Footer (Subtotal, Total, Tax, etc)
                itemsFooterSubTitle: {
                    fontSize: 10,
                    margin: [0,5,0,5],
                    alignment: 'right'
                },
                itemsFooterSubValue: {
                    fontSize: 10,
                    margin: [0,5,0,5],
                    bold: false,
                    alignment: 'right'
                },
                itemsFooterTotalTitle: {
                    fontSize: 10,
                    margin: [0,5,0,5],
                    bold: true,
                    alignment: 'right'
                },
                itemsFooterTotalValue: {
                    fontSize: 10,
                    margin: [0,5,0,5],
                    bold: true,
                    alignment: 'right'
                },
                notesTitle: {
                    fontSize: 10,
                    bold: true,
                    margin: [0,5,0,5]
                },
                notesText: {
                    fontSize: 9,
                    margin: [0,5,50,5]
                },
                left: {
                    alignment: 'left'
                },
                center: {
                    alignment: 'center'
                },
            },
            defaultStyle: {
                columnGap: 20
            }
        }
    </script>

<?php

// PREVIOUS UNPAID INVOICES

$sql = mysqli_query($mysqli, "SELECT * FROM invoices WHERE invoice_client_id = $client_id AND invoice_due < CURDATE() AND(invoice_status = 'Sent' OR invoice_status = 'Viewed' OR invoice_status = 'Partial') ORDER BY invoice_date DESC");

if (mysqli_num_rows($sql) > 1) { ?>

    <div class="card d-print-none card-danger">
        <div class="card-header">
            <strong><i class="fa fa-fw fa-exclamation-triangle mr-2"></i>Previous Unpaid Invoices</strong>
        </div>
        <div card="card-body">
            <table id=responsive class="responsive table">
                <thead>
                <tr>
                    <th class="text-center">Invoice #</th>
                    <th>Date</th>
                    <th>Due Date</th>
                    <th class="text-right">Amount</th>
                </tr>
                </thead>
                <tbody>
                <?php

                while ($row = mysqli_fetch_array($sql)) {
                    $invoice_id = intval($row['invoice_id']);
                    $invoice_prefix = nullable_htmlentities($row['invoice_prefix']);
                    $invoice_number = intval($row['invoice_number']);
                    $invoice_date = nullable_htmlentities($row['invoice_date']);
                    $invoice_due = nullable_htmlentities($row['invoice_due']);
                    $invoice_amount = floatval($row['invoice_amount']);
                    $invoice_currency_code = nullable_htmlentities($row['invoice_currency_code']);
                    $invoice_url_key = nullable_htmlentities($row['invoice_url_key']);
                    $invoice_tally_total = $invoice_amount + $invoice_tally_total;
                    $difference = time() - strtotime($invoice_due);
                    $days = floor($difference / (60*60*24));

                    ?>

                    <tr <?php if ($_GET['invoice_id'] == $invoice_id) { echo "class='table-active'"; } ?>>
                        <th class="text-center"><a href="portal/guest_view_invoice.php?invoice_id=<?= $invoice_id; ?>&url_key=<?= $invoice_url_key; ?>"><?= "$invoice_prefix$invoice_number"; ?></a></th>
                        <td><?= $invoice_date; ?></td>
                        <td class="text-danger text-bold"><?= $invoice_due; ?> (<?= $days; ?> Days Late)</td>
                        <td class="text-right"><?= numfmt_format_currency($currency_format, $invoice_amount, $invoice_currency_code); ?></td>
                    </tr>

                    <?php
                }
                ?>

                </tbody>
            </table>
        </div>
    </div>

<?php } // End previous unpaid invoices


// CURRENT INVOICES

$sql = mysqli_query($mysqli, "SELECT * FROM invoices WHERE invoice_client_id = $client_id AND invoice_due > CURDATE() AND(invoice_status = 'Sent' OR invoice_status = 'Viewed' OR invoice_status = 'Partial') ORDER BY invoice_number DESC");

if (mysqli_num_rows($sql) > 1) { ?>


    <div class="card d-print-none card-light">
        <div class="card-header">
            <strong><i class="fas fa-fw fa-clock mr-2"></i>Current Invoices</strong>
        </div>
        <div card="card-body">
            <table id=responsive class="responsive table">
                <thead>
                <tr>
                    <th class="text-center">Invoice #</th>
                    <th>Date</th>
                    <th>Due</th>
                    <th class="text-right">Amount</th>
                </tr>
                </thead>
                <tbody>
                <?php

                while ($row = mysqli_fetch_array($sql)) {
                    $invoice_id = intval($row['invoice_id']);
                    $invoice_prefix = nullable_htmlentities($row['invoice_prefix']);
                    $invoice_number = intval($row['invoice_number']);
                    $invoice_date = nullable_htmlentities($row['invoice_date']);
                    $invoice_due = nullable_htmlentities($row['invoice_due']);
                    $invoice_amount = floatval($row['invoice_amount']);
                    $invoice_currency_code = nullable_htmlentities($row['invoice_currency_code']);
                    $invoice_url_key = nullable_htmlentities($row['invoice_url_key']);
                    $invoice_tally_total = $invoice_amount + $invoice_tally_total;
                    $difference = strtotime($invoice_due) - time();
                    $days = floor($difference / (60*60*24));

                    ?>

                    <tr <?php if ($_GET['invoice_id'] == $invoice_id) { echo "class='table-active'"; } ?>>
                        <th class="text-center"><a href="portal/guest_view_invoice.php?invoice_id=<?= $invoice_id; ?>&url_key=<?= $invoice_url_key; ?>"><?= "$invoice_prefix$invoice_number"; ?></a></th>
                        <td><?= $invoice_date; ?></td>
                        <td><?= $invoice_due; ?> (Due in <?= $days; ?> Days)</td>
                        <td class="text-right"><?= numfmt_format_currency($currency_format, $invoice_amount, $invoice_currency_code); ?></td>
                    </tr>

                <?php } ?>

                </tbody>
            </table>
        </div>
    </div>
    <?php
}
?>


<?php

// PREVIOUS PAID INVOICES

$sql = mysqli_query($mysqli, "SELECT * FROM invoices WHERE invoice_client_id = $client_id AND invoice_status = 'Paid' ORDER BY invoice_date DESC");

if (mysqli_num_rows($sql) > 1) { ?>

    <div class="card d-print-none collapse" id="collapsePreviousInvoices">
        <div class="card-header bg-dark">
            <strong><i class="fas fa-fw fa-history mr-2"></i>Previous Invoices Paid</strong>
        </div>
        <div card="card-body">
            <table id=responsive class="responsive table">
                <thead>
                <tr>
                    <th class="text-center">Invoice #</th>
                    <th>Date</th>
                    <th>Due Date</th>
                    <th class="text-right">Amount</th>
                </tr>
                </thead>
                <tbody>
                <?php

                while ($row = mysqli_fetch_array($sql)) {
                    $invoice_id = intval($row['invoice_id']);
                    $invoice_prefix = nullable_htmlentities($row['invoice_prefix']);
                    $invoice_number = intval($row['invoice_number']);
                    $invoice_date = nullable_htmlentities($row['invoice_date']);
                    $invoice_due = nullable_htmlentities($row['invoice_due']);
                    $invoice_amount = floatval($row['invoice_amount']);
                    $invoice_currency_code = nullable_htmlentities($row['invoice_currency_code']);
                    $invoice_url_key = nullable_htmlentities($row['invoice_url_key']);
                    $invoice_tally_total = $invoice_amount + $invoice_tally_total;

                    ?>

                    <tr <?php if ($_GET['invoice_id'] == $invoice_id) { echo "class='table-active'"; } ?>>
                        <th class="text-center"><a href="portal/guest_view_invoice.php?invoice_id=<?= $invoice_id; ?>&url_key=<?= $invoice_url_key; ?>"><?= "$invoice_prefix$invoice_number"; ?></a></th>
                        <td><?= $invoice_date; ?></td>
                        <td><?= $invoice_due; ?></td>
                        <td class="text-right"><?= numfmt_format_currency($currency_format, $invoice_amount, $invoice_currency_code); ?></td>
                    </tr>

                    <tr>
                        <th colspan="4">Payments</th>
                    </tr>

                    <?php

                    $sql_payments = mysqli_query($mysqli, "SELECT * FROM payments WHERE payment_invoice_id = $invoice_id ORDER BY payment_date DESC");

                    while ($row = mysqli_fetch_array($sql_payments)) {
                        $payment_id = intval($row['payment_id']);
                        $payment_date = nullable_htmlentities($row['payment_date']);
                        $payment_amount = floatval($row['payment_amount']);
                        $payment_currency_code = nullable_htmlentities($row['payment_currency_code']);
                        $payment_method = nullable_htmlentities($row['payment_method']);
                        $payment_reference = nullable_htmlentities($row['payment_reference']);
                        if (strtotime($payment_date) > strtotime($invoice_due)) {
                            $payment_note = "Late";
                            $difference = strtotime($payment_date) - strtotime($invoice_due);
                            $days = floor($difference / (60*60*24)) . " Days";
                        } else {
                            $payment_note = "";
                            $days = "";
                        }


                        $invoice_tally_total = $invoice_amount + $invoice_tally_total;

                        ?>

                        <tr>
                            <td colspan="4"><?= $payment_date; ?> - <?= numfmt_format_currency($currency_format, $payment_amount, $payment_currency_code); ?> - <?= $payment_method; ?> - <?= $payment_reference; ?> - <?= $days; ?> <?= $payment_note; ?></td>
                        </tr>

                    <?php } ?>

                <?php } ?>

                </tbody>
            </table>
        </div>
    </div>

<?php } // End previous paid invoices


require_once "portal/guest_footer.php";

