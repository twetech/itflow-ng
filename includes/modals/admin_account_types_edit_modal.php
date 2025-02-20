<?php require_once "/var/www/nestogy/includes/inc_all_modal.php"; ?>

<?php
$account_type_id = intval($_GET['account_type_id']);
$sql_account_type = mysqli_query($mysqli, "SELECT * FROM account_types WHERE account_type_id = $account_type_id");
$row = mysqli_fetch_array($sql_account_type);
$account_type_name = nullable_htmlentities($row['account_type_name']);
$account_type_description = nullable_htmlentities($row['account_type_description']);
$account_parent = intval($row['account_parent']);
?>

<div class="modal" id="editAccountTypeModal<?= $account_type_id; ?>" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content bg-dark">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-fw fa-balance-scale mr-2"></i>Editing account type: <strong><?= $account_type_name; ?></strong></h5>
                <button type="button" class="close text-white" data-bs-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form action="/post.php" method="post" autocomplete="off">
                <div class="modal-body bg-white">
                <input type="hidden" name="account_type_id" value="<?= $account_type_id; ?>">

                    <div class="form-group">
                        <label>Name <strong class="text-danger">*</strong></label>
                        <input type="text" class="form-control" name="name" value="<?= $account_type_name; ?>" required>
                    </div>

                    <div class="form-group">
                        <label>Account Type</label>
                        <select class="form-control select2"  name="type" required>
                            <option value="1" <?php if ($account_parent == 1) echo 'selected'; ?>>Assets</option>
                            <option value="2" <?php if ($account_parent == 2) echo 'selected'; ?>>Liabilities</option>
                            <option value="3" <?php if ($account_parent == 3) echo 'selected'; ?>>Equity</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Description</label>
                        <textarea class="form-control" name="description" placeholder="Description"><?= $account_type_description; ?></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-white">
                    <button type="submit" name="edit_account_type" class="btn btn-label-primary text-bold"><i class="fas fa-check mr-2"></i>Save</button>
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal"><i class="fas fa-times mr-2"></i>Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>