<?php

// Default Column Sortby/Order Filter
$sort = "revenue_date";
$order = "DESC";

require_once "/var/www/portal.twe.tech/includes/inc_all.php";


//Rebuild URL

$sql = mysqli_query(
    $mysqli,
    "SELECT SQL_CALC_FOUND_ROWS * FROM revenues
    JOIN categories ON revenue_category_id = category_id
    LEFT JOIN accounts ON revenue_account_id = account_id
    WHERE (account_name LIKE '%$q%' OR revenue_payment_method LIKE '%$q%' OR category_name LIKE '%$q%' OR revenue_reference LIKE '%$q%' OR revenue_amount LIKE '%$q%')
    ORDER BY $sort $order");

$num_rows = mysqli_fetch_row(mysqli_query($mysqli, "SELECT FOUND_ROWS()"));

?>

<div class="card">
    <div class="card-header py-2">
        <h3 class="card-title mt-2"><i class="fas fa-fw fa-hand-holding-usd mr-2"></i>Revenues</h3>
        <div class="card-tools">
            <button type="button" class="btn btn-label-primary" data-bs-toggle="modal" data-bs-target="#addRevenueModal"><i class="fas fa-plus mr-2"></i>New Revenue</button>
        </div>
    </div>

    <div class="card-body">
        <form class="mb-4" autocomplete="off">
            <div class="row">
                <div class="col-sm-4">
                    <div class="input-group">
                        <input type="search" class="form-control" name="q" value="<?php if (isset($q)) { echo stripslashes(nullable_htmlentities($q)); } ?>" placeholder="Search Revenues">
                        <div class="input-group-append">
                            <button class="btn btn-light" type="button" data-bs-toggle="collapse" data-bs-target="#advancedFilter"><i class="fas fa-filter"></i></button>
                            <button class="btn btn-label-primary"><i class="fa fa-search"></i></button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="collapse mt-3 <?php if (!empty($_GET['dtf']) || $_GET['canned_date'] !== "custom" ) { echo "show"; } ?>" id="advancedFilter">
                <div class="row">
                    <div class="col-md-2">
                        <div class="form-group">
                            <label>Canned Date</label>
                            <select onchange="this.form.submit()" class="form-control select2"  name="canned_date">
                                <option <?php if ($_GET['canned_date'] == "custom") { echo "selected"; } ?> value="custom">Custom</option>
                                <option <?php if ($_GET['canned_date'] == "today") { echo "selected"; } ?> value="today">Today</option>
                                <option <?php if ($_GET['canned_date'] == "yesterday") { echo "selected"; } ?> value="yesterday">Yesterday</option>
                                <option <?php if ($_GET['canned_date'] == "thisweek") { echo "selected"; } ?> value="thisweek">This Week</option>
                                <option <?php if ($_GET['canned_date'] == "lastweek") { echo "selected"; } ?> value="lastweek">Last Week</option>
                                <option <?php if ($_GET['canned_date'] == "thismonth") { echo "selected"; } ?> value="thismonth">This Month</option>
                                <option <?php if ($_GET['canned_date'] == "lastmonth") { echo "selected"; } ?> value="lastmonth">Last Month</option>
                                <option <?php if ($_GET['canned_date'] == "thisyear") { echo "selected"; } ?> value="thisyear">This Year</option>
                                <option <?php if ($_GET['canned_date'] == "lastyear") { echo "selected"; } ?> value="lastyear">Last Year</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label>Date From</label>
                            <input onchange="this.form.submit()" type="date" class="form-control" name="dtf" max="2999-12-31" value="<?= nullable_htmlentities($dtf); ?>">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label>Date To</label>
                            <input onchange="this.form.submit()" type="date" class="form-control" name="dtt" max="2999-12-31" value="<?= nullable_htmlentities($dtt); ?>">
                        </div>
                    </div>
                </div>
            </div>
        </form>
        <hr>
        <div class="card-datatable table-responsive container-fluid  pt-0">               
<table class="datatables-basic table border-top">
                <thead class="<?php if ($num_rows[0] == 0) { echo "d-none"; } ?>">
                <tr>
                    <th><a class="text-dark" href="?<?= $url_query_strings_sort; ?>&sort=revenue_date&order=<?= $disp; ?>">Date</a></th>
                    <th><a class="text-dark" href="?<?= $url_query_strings_sort; ?>&sort=category_name&order=<?= $disp; ?>">Category</a></th>
                    <th class="text-right"><a class="text-dark" href="?<?= $url_query_strings_sort; ?>&sort=revenue_amount&order=<?= $disp; ?>">Amount</a></th>
                    <th><a class="text-dark" href="?<?= $url_query_strings_sort; ?>&sort=revenue_payment_method&order=<?= $disp; ?>">Method</a></th>
                    <th><a class="text-dark" href="?<?= $url_query_strings_sort; ?>&sort=revenue_reference&order=<?= $disp; ?>">Reference</a></th>
                    <th><a class="text-dark" href="?<?= $url_query_strings_sort; ?>&sort=account_name&order=<?= $disp; ?>">Account</a></th>
                    <th class="text-center">Action</th>
                </tr>
                </thead>
                <tbody>
                <?php

                while ($row = mysqli_fetch_array($sql)) {
                    $revenue_id = intval($row['revenue_id']);
                    $revenue_description = nullable_htmlentities($row['revenue_description']);
                    $revenue_reference = nullable_htmlentities($row['revenue_reference']);
                    if (empty($revenue_reference)) {
                        $revenue_reference_display = "-";
                    } else {
                        $revenue_reference_display = $revenue_reference;
                    }
                    $revenue_date = nullable_htmlentities($row['revenue_date']);
                    $revenue_payment_method = nullable_htmlentities($row['revenue_payment_method']);
                    $revenue_amount = floatval($row['revenue_amount']);
                    $revenue_currency_code = nullable_htmlentities($row['revenue_currency_code']);
                    $revenue_created_at = nullable_htmlentities($row['revenue_created_at']);
                    $account_id = intval($row['account_id']);
                    $account_name = nullable_htmlentities($row['account_name']);
                    $category_id = intval($row['category_id']);
                    $category_name = nullable_htmlentities($row['category_name']);

                    ?>

                    <tr>
                        <td><a href="#" data-bs-toggle="modal" data-bs-target="#editRevenueModal<?= $revenue_id; ?>"><?= $revenue_date; ?></a></td>
                        <td><?= $category_name; ?></td>
                        <td class="text-bold text-right"><?= numfmt_format_currency($currency_format, $revenue_amount, $revenue_currency_code); ?></td>
                        <td><?= $revenue_payment_method; ?></td>
                        <td><?= $revenue_reference_display; ?></td>
                        <td><?= $account_name; ?></td>
                        <td>
                            <div class="dropdown dropleft text-center">
                                <button class="btn btn-light btn-sm" type="button" data-bs-toggle="dropdown">
                                    <i class="fas fa-ellipsis-h"></i>
                                </button>
                                <div class="dropdown-menu">
                                    <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#editRevenueModal<?= $revenue_id; ?>">
                                        <i class="fas fa-fw fa-edit mr-2"></i>Edit
                                    </a>
                                    <div class="dropdown-divider"></div>
                                    <a class="dropdown-item text-danger text-bold confirm-link" href="/post.php?delete_revenue=<?= $revenue_id; ?>">
                                        <i class="fas fa-fw fa-trash mr-2"></i>Delete
                                    </a>
                                </div>
                            </div>
                            <?php

                            ?>
                        </td>
                    </tr>

                <?php } ?>


                </tbody>
            </table>
        </div>

    </div>
</div>

<?php

require_once '/var/www/portal.twe.tech/includes/footer.php';
