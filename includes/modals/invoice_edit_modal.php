<?php require_once "/var/www/nestogy/includes/inc_all_modal.php"; 

$invoice_id = intval($_GET['invoice_id']);

$sql = mysqli_query($mysqli, "SELECT * FROM invoices WHERE invoice_id = $invoice_id");
$row = mysqli_fetch_array($sql);

$invoice_number = intval($row['invoice_number']);
$invoice_prefix = nullable_htmlentities($row['invoice_prefix']);
$invoice_date = nullable_htmlentities($row['invoice_date']);
$invoice_due = nullable_htmlentities($row['invoice_due']);
$invoice_scope = nullable_htmlentities($row['invoice_scope']);
$invoice_discount = floatval($row['invoice_discount']);
$invoice_created_at = nullable_htmlentities($row['invoice_created_at']);
$invoice_deposit_amount = floatval($row['invoice_deposit_amount']);


?>

<div class="modal" id="editInvoiceModal<?= $invoice_id; ?>" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content bg-dark">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-fw fa-file-invoice mr-2"></i>Editing invoice: <strong><?= "$invoice_prefix$invoice_number"; ?></strong> - <?= $client_name; ?></h5>
                <button type="button" class="close text-white" data-bs-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form action="/post.php" method="post" autocomplete="off">

                <div class="modal-body bg-white">
                <input type="hidden" name="invoice_id" value="<?= $invoice_id; ?>">

                    <div class="form-group">
                        <label>Invoice Date <strong class="text-danger">*</strong></label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="fa fa-fw fa-calendar"></i></span>
                            </div>
                            <input type="date" class="form-control" name="date" max="2999-12-31" value="<?= $invoice_date; ?>" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Invoice Due <strong class="text-danger">*</strong></label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="fa fa-fw fa-calendar-alt"></i></span>
                            </div>
                            <input type="date" class="form-control" name="due" max="2999-12-31" value="<?= $invoice_due; ?>" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Income Category <strong class="text-danger">*</strong></label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="fa fa-fw fa-tag"></i></span>
                            </div>
                            <select class="form-control select2"  name="category" required>
                                <option value="">- Category -</option>
                                <?php

                                $sql_income_category = mysqli_query($mysqli, "SELECT * FROM categories WHERE category_type = 'Income' AND (category_archived_at > '$invoice_created_at' OR category_archived_at IS NULL) ORDER BY category_name ASC");
                                while ($row = mysqli_fetch_array($sql_income_category)) {
                                    $category_id_select = intval($row['category_id']);
                                    $category_name_select = nullable_htmlentities($row['category_name']);
                                ?>
                                    <option <?php if ($category_id == $category_id_select) {
                                                echo "selected";
                                            } ?> value="<?= $category_id_select; ?>"><?= $category_name_select; ?></option>

                                <?php
                                }
                                ?>
                            </select>
                            <div class="input-group-append">
                                <a class="btn btn-light" href="admin_categories.php?category=Income" target="_blank"><i class="fas fa-fw fa-plus"></i></a>
                            </div>
                        </div>
                    </div>

                    <div class='form-group'>
                        <label>Discount Amount</label>
                        <div class='input-group'>
                            <div class='input-group-prepend'>
                                <span class='input-group-text'><i class='fa fa-fw fa-dollar-sign'></i></span>
                            </div>
                            <input type='text' class='form-control' inputmode="numeric" pattern="-?[0-9]*\.?[0-9]{0,2}" name='invoice_discount' placeholder='0.00' value="<?= number_format($invoice_discount, 2, '.', ''); ?>">
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Deposit Amount</label>
                        <div class='input-group'>
                            <div class='input-group-prepend'>
                                <span class='input-group-text'><i class='fa fa-fw fa-dollar-sign'></i></span>
                            </div>
                            <input type='text' class='form-control' inputmode="numeric" pattern="-?[0-9]*\.?[0-9]{0,2}" name='invoice_deposit_amount' placeholder='0.00' value="<?= number_format($invoice_deposit_amount, 2, '.', ''); ?>">
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Scope</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="fa fa-fw fa-comment"></i></span>
                            </div>
                            <input type="text" class="form-control" name="scope" placeholder="Quick description" value="<?= $invoice_scope; ?>">
                        </div>
                    </div>

                </div>
                <div class="modal-footer bg-white">
                    <button type="submit" name="edit_invoice" class="btn btn-label-primary text-bold"><i class="fas fa-check mr-2"></i>Save</button>
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal"></i>Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>
