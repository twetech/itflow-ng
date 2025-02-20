<?php require_once "/var/www/nestogy/includes/inc_all_modal.php"; ?>
<div class="modal" id="exportExpensesModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content bg-dark">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fa fa-fw fa-download mr-2"></i>Export Expenses to CSV</h5>
                <button type="button" class="close text-white" data-bs-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form action="/post.php" method="post" autocomplete="off">
                <div class="modal-body bg-white">

                    <?php require_once "/var/www/nestogy/includes/inc_export_warning.php";
 ?>

                    <div class="form-group">
                        <label>Date From</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="fa fa-fw fa-calendar"></i></span>
                            </div>
                            <input type="date" class="form-control" name="date_from" max="2999-12-31" value="<?= nullable_htmlentities($dtf); ?>">
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Date To</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="fa fa-fw fa-calendar"></i></span>
                            </div>
                            <input type="date" class="form-control" name="date_to" max="2999-12-31" value="<?= nullable_htmlentities($dtt); ?>">
                        </div>
                    </div>

                </div>
                <div class="modal-footer bg-white">
                    <button type="submit" name="export_expenses_csv" class="btn btn-label-primary text-bold"><i class="fas fa-fw fa-download mr-2"></i>Download CSV</button>
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal"></i>Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>
