<?php

// Default Column Sortby Filter
$sort = "log_id";
$order = "DESC";

require_once "/var/www/portal.twe.tech/includes/inc_all_admin.php";


//Rebuild URL

$sql = mysqli_query(
    $mysqli,
    "SELECT SQL_CALC_FOUND_ROWS * FROM logs
  LEFT JOIN users ON log_user_id = user_id
  LEFT JOIN clients ON log_client_id = client_id
  WHERE (log_type LIKE '%$q%' OR log_action LIKE '%$q%' OR log_description LIKE '%$q%' OR log_ip LIKE '%$q%' OR log_user_agent LIKE '%$q%' OR user_name LIKE '%$q%' OR client_name LIKE '%$q%')
  ORDER BY $sort $order"
);

$num_rows = mysqli_fetch_row(mysqli_query($mysqli, "SELECT FOUND_ROWS()"));

?>

    <div class="card">
        <div class="card-header py-3">
            <h3 class="card-title"><i class="fas fa-fw fa-history mr-2"></i>Audit Logs</h3>
        </div>
        <div class="card-body">
            <form class="mb-4" autocomplete="off">
                <div class="row">
                    <div class="col-sm-4">
                        <div class="input-group">
                            <input type="search" class="form-control" name="q" value="<?php if (isset($q)) { echo stripslashes(nullable_htmlentities($q)); } ?>" placeholder="Search audit logs">
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
                                    <option <?php if ($_GET['canned_date'] == "custom") { echo "selected"; } ?> value="">Custom</option>
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
            <div class="card-datatable table-responsive container-fluid  pt-0">                <table id=responsive class="responsive table table-sm table-striped table-borderless table-hover">
                    <thead class="text-dark <?php if ($num_rows[0] == 0) { echo "d-none"; } ?>">
                    <tr>
                        <th><a class="text-dark" href="?<?= $url_query_strings_sort; ?>&sort=log_created_at&order=<?= $disp; ?>">Timestamp</a></th>
                        <th><a class="text-dark" href="?<?= $url_query_strings_sort; ?>&sort=user_name&order=<?= $disp; ?>">User</a></th>
                        <th><a class="text-dark" href="?<?= $url_query_strings_sort; ?>&sort=client_name&order=<?= $disp; ?>">Client</a></th>
                        <th><a class="text-dark" href="?<?= $url_query_strings_sort; ?>&sort=log_type&order=<?= $disp; ?>">Type</a></th>
                        <th><a class="text-dark" href="?<?= $url_query_strings_sort; ?>&sort=log_action&order=<?= $disp; ?>">Action</a></th>
                        <th><a class="text-dark" href="?<?= $url_query_strings_sort; ?>&sort=log_description&order=<?= $disp; ?>">Description</a></th>
                        <th><a class="text-dark" href="?<?= $url_query_strings_sort; ?>&sort=log_ip&order=<?= $disp; ?>">IP Address</a></th>
                        <th><a class="text-dark" href="?<?= $url_query_strings_sort; ?>&sort=log_user_agent&order=<?= $disp; ?>">User Agent</a></th>
                        <th><a class="text-dark" href="?<?= $url_query_strings_sort; ?>&sort=log_entity_id&order=<?= $disp; ?>">Entity ID</a></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php

                    while ($row = mysqli_fetch_array($sql)) {
                        $log_id = intval($row['log_id']);
                        $log_type = nullable_htmlentities($row['log_type']);
                        $log_action = nullable_htmlentities($row['log_action']);
                        $log_description = nullable_htmlentities($row['log_description']);
                        $log_ip = nullable_htmlentities($row['log_ip']);
                        $log_user_agent = nullable_htmlentities($row['log_user_agent']);
                        $log_user_os = getOS($log_user_agent);
                        $log_user_browser = getWebBrowser($log_user_agent);
                        $log_created_at = nullable_htmlentities($row['log_created_at']);
                        $user_id = intval($row['user_id']);
                        $user_name = nullable_htmlentities($row['user_name']);
                        if (empty($user_name)) {
                            $user_name_display = "-";
                        } else {
                            $user_name_display = $user_name;
                        }
                        $client_name = nullable_htmlentities($row['client_name']);
                        $client_id = intval($row['client_id']);
                        if (empty($client_name)) {
                            $client_name_display = "-";
                        } else {
                            $client_name_display = "<a href='client_logs.php?client_id=$client_id&tab=logs'>$client_name</a>";
                        }
                        $log_entity_id = intval($row['log_entity_id']);

                        ?>

                        <tr>
                            <td><?= $log_created_at; ?></td>
                            <td><?= $user_name_display; ?></td>
                            <td><?= $client_name_display; ?></td>
                            <td><?= $log_type; ?></td>
                            <td><?= $log_action; ?></td>
                            <td><?= $log_description; ?></td>
                            <td><?= $log_ip; ?></td>
                            <td><?= "$log_user_os<br>$log_user_browser"; ?></td>
                            <td><?= $log_entity_id; ?></td>
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

