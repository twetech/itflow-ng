<?php require_once "/var/www/nestogy/includes/inc_all_modal.php"; ?>

<?php
$budget_id = intval($_GET['budget_id']);
$sql_budget = mysqli_query($mysqli, "SELECT * FROM budgets WHERE budget_id = $budget_id");
$row = mysqli_fetch_array($sql_budget);
$budget_month = intval($row['budget_month']);
$budget_year = intval($row['budget_year']);
$budget_description = nullable_htmlentities($row['budget_description']);
$budget_amount = floatval($row['budget_amount']);
$budget_category_id = intval($row['budget_category_id']);
?>


<div class="modal" id="editBudgetModal<?= $budget_id; ?>" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content bg-dark">
            <div class="modal-header">
                <h5 class="modal-title text-white"><i class="fa fa-fw fa-balance-scale mr-2"></i>Editing Budget</h5>
                <button type="button" class="close text-white" data-bs-dismiss="modal">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="/post.php" method="post" autocomplete="off">
                <div class="modal-body bg-white">
                    <input type="hidden" name="budget_id" value="<?= $budget_id; ?>">

                    <div class="form-row">

                        <div class="form-group col-md">
                            <label>Month <strong class="text-danger">*</strong></label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fa fa-fw fa-calendar"></i></span>
                                </div>
                                <select class="form-control select2"  name="month" required>
                                    <option value="">- Select a Month -</option>
                                    <option value="1" <?php if($budget_month == 1) { echo "selected"; } ?>>01 - January</option>
                                    <option value="2" <?php if($budget_month == 2) { echo "selected"; } ?>>02 - February</option>
                                    <option value="3" <?php if($budget_month == 3) { echo "selected"; } ?>>03 - March</option>
                                    <option value="4" <?php if($budget_month == 4) { echo "selected"; } ?>>04 - April</option>
                                    <option value="5" <?php if($budget_month == 5) { echo "selected"; } ?>>05 - May</option>
                                    <option value="6" <?php if($budget_month == 6) { echo "selected"; } ?>>06 - June</option>
                                    <option value="7" <?php if($budget_month == 7) { echo "selected"; } ?>>07 - July</option>
                                    <option value="8" <?php if($budget_month == 8) { echo "selected"; } ?>>08 - August</option>
                                    <option value="9" <?php if($budget_month == 9) { echo "selected"; } ?>>09 - September</option>
                                    <option value="10" <?php if($budget_month == 10) { echo "selected"; } ?>>10 - October</option>
                                    <option value="11" <?php if($budget_month == 11) { echo "selected"; } ?>>11 - November</option>
                                    <option value="12" <?php if($budget_month == 12) { echo "selected"; } ?>>12 - December</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group col-md">
                            <label>Year <strong class="text-danger">*</strong></label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fa fa-fw fa-calendar"></i></span>
                                </div>
                                <input type="text" class="form-control" inputmode="numeric" pattern="^[0-9]{4}$" name="year" placeholder="2024" value="<?= $budget_year; ?>" required>
                            </div>
                        </div>

                    </div>

                    <div class="form-group">
                        <label>Description <strong class="text-danger">*</strong></label>
                        <textarea class="form-control" rows="6" name="description" placeholder="Enter a description" required><?= $budget_description; ?></textarea>
                    </div>

                    <div class="form-row">

                        <div class="form-group col-md">
                            <label>Amount <strong class="text-danger">*</strong></label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fa fa-fw fa-dollar-sign"></i></span>
                                </div>
                                <input type="text" class="form-control" inputmode="numeric" pattern="[0-9]*\.?[0-9]{0,2}" name="amount" value="<?= number_format($budget_amount, 2, '.', ''); ?>" placeholder="0.00" required>
                            </div>
                        </div>

                        <div class="form-group col-md">
                            <label>Category <strong class="text-danger">*</strong></label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fa fa-fw fa-list"></i></span>
                                </div>
                                <select class="form-control select2"  name="category" required>
                                    <?php

                                    $sql_select = mysqli_query($mysqli, "SELECT category_id, category_name FROM categories WHERE category_type = 'Expense' AND (category_archived_at > '$expense_created_at' OR category_archived_at IS NULL) ORDER BY category_name ASC");
                                    while ($row = mysqli_fetch_array($sql_select)) {
                                        $category_id_select = intval($row['category_id']);
                                        $category_name_select = nullable_htmlentities($row['category_name']);
                                        ?>
                                        <option <?php if ($budget_category_id == $category_id_select) { ?> selected <?php } ?> value="<?= $category_id_select; ?>"><?= $category_name_select; ?></option>
                                        <?php
                                    }

                                    ?>
                                </select>
                            </div>
                        </div>

                    </div>

                </div>
                <div class="modal-footer bg-white">
                    <button type="submit" name="edit_budget" class="btn btn-label-primary text-bold"><i class="fas fa-check mr-2"></i>Save</button>
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal"><i class="fas fa-times mr-2"></i>Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>
