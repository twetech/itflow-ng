<?php require_once "/var/www/nestogy/includes/inc_all_modal.php"; ?>
<div class="modal" id="exportPaymentModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content bg-dark">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-fw fa-download mr-2"></i>Export Payments to CSV</h5>
                <button type="button" class="close text-white" data-bs-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form action="/post.php" method="post" autocomplete="off">
                <div class="modal-body bg-white">
                <input type="hidden" name="client_id" value="<?= $client_id; ?>">

                    <?php require_once "/var/www/nestogy/includes/inc_export_warning.php";
 ?>

                </div>
                <div class="modal-footer bg-white">
                    <button type="submit" name="export_client_payments_csv" class="btn btn-label-primary text-bold"><i class="fas fa-fw fa-download mr-2"></i>Download CSV</button>
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal"><i class="fas fa-times mr-2"></i>Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>
