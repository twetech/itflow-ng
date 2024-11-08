<?php

// Default Column Sortby Filter
$sort = "certificate_name";
$order = "ASC";

require_once "/var/www/portal.twe.tech/includes/inc_all.php";


//Rebuild URL

$sql = mysqli_query($mysqli, "SELECT SQL_CALC_FOUND_ROWS * FROM certificates 
  WHERE certificate_archived_at IS NULL
  AND certificate_client_id = $client_id 
  AND (certificate_name LIKE '%$q%' OR certificate_domain LIKE '%$q%' OR certificate_issued_by LIKE '%$q%') 
  ORDER BY $sort $order");

$num_rows = mysqli_fetch_row(mysqli_query($mysqli, "SELECT FOUND_ROWS()"));

?>

<div class="card">
    <div class="card-header py-2">
        <h3 class="card-title mt-2"><i class="fas fa-fw fa-lock mr-2"></i>Certificates</h3>
        <div class="card-tools">
            <div class="btn-group">
                <button type="button" class="btn btn-label-primary loadModalContentBtn" data-bs-toggle="modal" data-bs-target="#dynamicModal" data-modal-file="client_certificate_add_modal.php?client_id=<?= $client_id; ?>">
                    <i class="fas fa-plus mr-2"></i>New Certificate</button>
                <button type="button" class="btn btn-label-primary dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown"></button>
                <div class="dropdown-menu">
                    <a class="dropdown-item text-dark" href="#" data-bs-toggle="modal" data-bs-target="#exportCertificateModal">
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
                        <input type="search" class="form-control" name="q" value="<?php if (isset($q)) { echo stripslashes(nullable_htmlentities($q)); } ?>" placeholder="Search Certificates">
                        <div class="input-group-append">
                            <button class="btn btn-dark"><i class="fa fa-search"></i></button>
                        </div>
                    </div>
                </div>

                <div class="col-md-8">
                    <div class="btn-group float-right">
                        <div class="dropdown ml-2" id="bulkActionButton" hidden>
                            <button class="btn btn-light dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                <i class="fas fa-fw fa-layer-group mr-2"></i>Bulk Action (<span id="selectedCount">0</span>)
                            </button>
                            <div class="dropdown-menu">
                                <button class="dropdown-item text-danger text-bold"
                                        type="submit" form="bulkActions" name="bulk_delete_certificates">
                                    <i class="fas fa-fw fa-trash mr-2"></i>Delete
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </form>
        <hr>
        <div class="card-datatable table-responsive container-fluid  pt-0">
            <form id="bulkActions" action="/post.php" method="post">
                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">

                   
<table class="datatables-basic table border-top">
                    <thead class="text-dark <?php if ($num_rows[0] == 0) { echo "d-none"; } ?>">
                    <tr>
                        <th><a class="text-secondary" href="?<?= $url_query_strings_sort; ?>&sort=certificate_name&order=<?= $disp; ?>">Name</a></th>
                        <th><a class="text-secondary" href="?<?= $url_query_strings_sort; ?>&sort=certificate_domain&order=<?= $disp; ?>">Domain</a></th>
                        <th><a class="text-secondary" href="?<?= $url_query_strings_sort; ?>&sort=certificate_issued_by&order=<?= $disp; ?>">Issued By</a></th>
                        <th><a class="text-secondary" href="?<?= $url_query_strings_sort; ?>&sort=certificate_expire&order=<?= $disp; ?>">Expire</a></th>
                        <th class="text-center">Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php

                    while ($row = mysqli_fetch_array($sql)) {
                        $certificate_id = intval($row['certificate_id']);
                        $certificate_name = nullable_htmlentities($row['certificate_name']);
                        $certificate_description = nullable_htmlentities($row['certificate_description']);
                        $certificate_domain = nullable_htmlentities($row['certificate_domain']);
                        $certificate_issued_by = nullable_htmlentities($row['certificate_issued_by']);
                        $certificate_expire = nullable_htmlentities($row['certificate_expire']);
                        $certificate_created_at = nullable_htmlentities($row['certificate_created_at']);

                        ?>
                        <tr>
                            <td>
                                <a class="text-dark" href="#" data-bs-toggle="modal" onclick="populateCertificateEditModal(<?= $client_id, ",", $certificate_id ?>)" data-bs-target="#editCertificateModal">
                                    <div class="media">
                                        <i class="fa fa-fw fa-2x fa-lock mr-3"></i>
                                        <div class="media-body">
                                            <div><?= $certificate_name; ?></div>
                                            <div><small class="text-secondary"><?= $certificate_description; ?></small></div>
                                        </div>
                                    </div>
                                </a>
                            </td>
                            <td><?= $certificate_domain; ?></td>

                            <td><?= $certificate_issued_by; ?></td>

                            <td><?= $certificate_expire; ?></td>

                            <td>
                                <div class="dropdown dropleft text-center">
                                    <button class="btn btn-light btn-sm" type="button" data-bs-toggle="dropdown">
                                        <i class="fas fa-ellipsis-h"></i>
                                    </button>
                                    <div class="dropdown-menu">
                                        <a class="dropdown-item" href="#" data-bs-toggle="modal" onclick="populateCertificateEditModal(<?= $client_id, ",", $certificate_id ?>)" data-bs-target="#editCertificateModal">
                                            <i class="fas fa-fw fa-edit mr-2"></i>Edit
                                        </a>
                                        <?php if ($user_role == 3) { ?>
                                            <div class="dropdown-divider"></div>
                                            <a class="dropdown-item text-danger confirm-link" href="/post.php?archive_certificate=<?= $certificate_id; ?>">
                                                <i class="fas fa-fw fa-archive mr-2"></i>Archive
                                            </a>
                                            <div class="dropdown-divider"></div>
                                            <a class="dropdown-item text-danger text-bold confirm-link" href="/post.php?delete_certificate=<?= $certificate_id; ?>">
                                                <i class="fas fa-fw fa-trash mr-2"></i>Delete
                                            </a>
                                        <?php } ?>
                                    </div>
                                </div>
                            </td>
                        </tr>

                        <?php
                    }
                    ?>

                    </tbody>
                </table>

            </form>
        </div>

    </div>
</div>

<?php
require_once "/var/www/portal.twe.tech/includes/modals/client_certificate_edit_modal.php";

require_once "/var/www/portal.twe.tech/includes/modals/client_certificate_add_modal.php";

require_once "/var/www/portal.twe.tech/includes/modals/client_certificate_export_modal.php";

?>

<script src="/includes/js/certificate_edit_modal.js"></script>
<script src="/includes/js/bulk_actions.js"></script>
<script src="/includes/js/certificate_fetch_ssl.js"></script>

<?php require_once '/var/www/portal.twe.tech/includes/footer.php';
 ?>
