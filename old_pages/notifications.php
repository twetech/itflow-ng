<?php

require_once "/var/www/portal.twe.tech/includes/inc_all.php";


$sql = mysqli_query($mysqli, "SELECT * FROM notifications LEFT JOIN clients ON notification_client_id = client_id WHERE notification_dismissed_at IS NULL AND (notification_user_id = $user_id OR notification_user_id = 0) ORDER BY notification_id DESC");

?>

    <div class="card">
        <div class="card-header py-2">
            <h3 class="card-title mt-2"><i class="fas fa-fw fa-bell mr-2"></i>Notifications</h3>
            <div class="card-tools">

                <?php if (mysqli_num_rows($sql) > 0) { ?><a href="/post.php?dismiss_all_notifications" class="btn btn-label-primary"><i class="fas fa-fw fa-check mr-2"></i>Dismiss All</a><?php } ?>
                <a href="notifications_dismissed.php" class="btn btn-light"><i class="fas fa-fw fa-history mr-2"></i>Dismissed</a>
            </div>
        </div>
        <div class="card-body">
            <?php if (mysqli_num_rows($sql) > 0) { ?>

            <div class="card-datatable table-responsive container-fluid  pt-0">                  
<table class="datatables-basic table border-top">
                    <thead>
                    <tr>
                        <th>Timestamp</th>
                        <th>Type</th>
                        <th>Notification</th>
                        <th>Client</th>
                        <th class="text-center">Dismiss</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php

                    while ($row = mysqli_fetch_array($sql)) {
                        $notification_id = intval($row['notification_id']);
                        $notification_type = nullable_htmlentities($row['notification_type']);
                        $notification = nullable_htmlentities($row['notification']);
                        $notification_timestamp = nullable_htmlentities($row['notification_timestamp']);
                        $client_name = nullable_htmlentities($row['client_name']);
                        $client_id = intval($row['client_id']);
                        if (empty($client_name)) {
                            $client_name_display = "-";
                        } else {
                            $client_name_display = "<a href='/old_pages/client/client_overview.php?client_id=$client_id'>$client_name</a>";
                        }

                        ?>
                        <tr class="row-danger">
                            <td><?= $notification_timestamp; ?></td>
                            <td><?= $notification_type; ?></td>
                            <td><?= $notification; ?></td>
                            <td><?= $client_name_display; ?></td>
                            <td class="text-center"><a class="btn btn-info btn-sm" href="/post.php?dismiss_notification=<?= $notification_id; ?>"><i class="fas fa-check"></a></td>
                        </tr>

                    <?php } ?>


                    </tbody>
                </table>
            </div>
        </div>

        <?php } else { ?>
            <div class="my-5" style="text-align: center">
                <i class='far fa-fw fa-6x fa-bell-slash text-secondary'></i><h3 class='text-secondary mt-3'>No Notifications</h3>
            </div>
        <?php } ?>

    </div>
</div>

<?php
require_once '/var/www/portal.twe.tech/includes/footer.php';

