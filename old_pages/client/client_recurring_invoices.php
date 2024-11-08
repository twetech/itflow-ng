<?php

// Default Column Sortby Filter
$sort = "recurring_last_sent";
$order = "DESC";

require_once "/var/www/portal.twe.tech/includes/inc_all.php";


//Rebuild URL

$sql = mysqli_query(
    $mysqli,
    "SELECT * FROM recurring
    LEFT JOIN categories ON recurring_category_id = category_id
    WHERE recurring_client_id = $client_id
    AND (CONCAT(recurring_prefix,recurring_number) LIKE '%$q%' OR recurring_frequency LIKE '%$q%' OR recurring_scope LIKE '%$q%' OR category_name LIKE '%$q%') 
    ORDER BY $sort $order");

$num_rows = mysqli_fetch_row(mysqli_query($mysqli, "SELECT FOUND_ROWS()"));

?>

<div class="card">
    <div class="card-header py-2">
        <h3 class="card-title mt-2"><i class="fas fa-fw fa-redo-alt mr-2"></i>Recurring Invoices</h3>
        <div class="card-tools">
            <div class="btn-group">
                <button type="button" class="btn btn-label-primary loadModalContentBtn" data-bs-toggle="modal" data-bs-target="#dynamicModal" data-modal-file="recurring_invoice_add_modal.php?client_id=<?= $client_id; ?>"><i class="fas fa-plus mr-2"></i>New Recurring</button>
                <button type="button" class="btn btn-label-primary dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown"></button>
                <div class="dropdown-menu">
                    <a class="dropdown-item text-dark" href="#" data-bs-toggle="modal" data-bs-target="#exportRecurringModal">
                        <i class="fa fa-fw fa-download mr-2"></i>Export
                    </a>
                </div>
            </div>
        </div>
    </div>
    <div class="card-body">
        <form autocomplete="off">
            <input type="hidden" name="client_id" value="<?= $client_id; ?>">
            <div class="row">

                <div class="col-md-4">
                    <div class="input-group mb-3 mb-md-0">
                        <input type="search" class="form-control" name="q" value="<?php if (isset($q)) { echo stripslashes(nullable_htmlentities($q)); } ?>" placeholder="Search Recurring Invoices">
                        <div class="input-group-append">
                            <button class="btn btn-dark"><i class="fa fa-search"></i></button>
                        </div>
                    </div>
                </div>

                <div class="col-md-8">
                    <div class="float-right">
                        <div class="btn-group float-right">
                            <a href="client_invoices.php?client_id=<?= $client_id; ?>" class="btn btn-label-primary"><i class="fa fa-fw fa-file-invoice mr-2"></i>Back to Invoices</a>
                        </div>
                    </div>
                </div>

            </div>
        </form>
        <hr>
        <div class="card-datatable table-responsive container-fluid  pt-0">               
<table class="datatables-basic table border-top">
                <thead class="text-dark <?php if ($num_rows[0] == 0) { echo "d-none"; } ?>">
                <tr>
                    <th>Number</a></th>
                    <th>Scope</a></th>
                    <th class="text-right">Amount</a></th>
                    <th>Frequency</a></th>
                    <th>Last Sent</a></th>
                    <th>Next Date</a></th>
                    <th>Category</a></th>
                    <th>Status</a></th>
                    <th class="text-center">Action</th>
                </tr>
                </thead>
                <tbody>
                <?php

                while ($row = mysqli_fetch_array($sql)) {
                    $recurring_id = intval($row['recurring_id']);
                    $recurring_prefix = nullable_htmlentities($row['recurring_prefix']);
                    $recurring_number = intval($row['recurring_number']);
                    $recurring_scope = nullable_htmlentities($row['recurring_scope']);
                    $recurring_frequency = nullable_htmlentities($row['recurring_frequency']);
                    $recurring_status = nullable_htmlentities($row['recurring_status']);
                    $recurring_last_sent = nullable_htmlentities($row['recurring_last_sent']);
                    if ($recurring_last_sent == 0) {
                        $recurring_last_sent = "-";
                    }
                    $recurring_next_date = nullable_htmlentities($row['recurring_next_date']);
                    $recurring_amount = floatval($row['recurring_amount']);
                    $recurring_discount = floatval($row['recurring_discount_amount']);
                    $recurring_currency_code = nullable_htmlentities($row['recurring_currency_code']);
                    $recurring_created_at = nullable_htmlentities($row['recurring_created_at']);
                    $category_id = intval($row['category_id']);
                    $category_name = nullable_htmlentities($row['category_name']);
                    if ($recurring_status == 1) {
                        $status = "Active";
                        $status_badge_color = "success";
                    } else {
                        $status = "Inactive";
                        $status_badge_color = "secondary";
                    }

                    ?>

                    <tr>
                        <td class="text-bold"><a href="/old_pages/recurring_invoice.php?recurring_id=<?= $recurring_id; ?>"><?=$recurring_prefix.$recurring_number?></a></td>
                        <td><?= $recurring_scope; ?></td>
                        <td class="text-bold text-right"><?= numfmt_format_currency($currency_format, $recurring_amount, $recurring_currency_code); ?></td>
                        <td><?= ucwords($recurring_frequency); ?>ly</td>
                        <td><?= $recurring_last_sent; ?></td>
                        <td><?= $recurring_next_date; ?></td>
                        <td><?= $category_name; ?></td>
                        <td>
                        <span class="p-2 badge badge-<?= $status_badge_color; ?>">
                        <?= $status; ?>
                        </span>
                        </td>
                        <td>
                            <div class="dropdown dropleft text-center">
                                <button class="btn btn-light btn-sm" type="button" data-bs-toggle="dropdown">
                                    <i class="fas fa-ellipsis-h"></i>
                                </button>
                                <div class="dropdown-menu">
                                    <a class="dropdown-item" href="recurring_invoice.php?recurring_id=<?= $recurring_id; ?>">
                                        <i class="fas fa-fw fa-edit mr-2"></i>Edit
                                    </a>
                                    <div class="dropdown-divider"></div>
                                    <a class="dropdown-item text-danger text-bold confirm-link" href="/post.php?delete_recurring=<?= $recurring_id; ?>">
                                        <i class="fas fa-fw fa-trash mr-2"></i>Delete
                                    </a>
                                </div>
                            </div>
                        </td>
                    </tr>

                    <?php

                }

                ?>

                </tbody>
            </table>
        </div>

    </div>
</div>

<?php

require_once '/var/www/portal.twe.tech/includes/footer.php';

