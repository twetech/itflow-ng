<?php require_once "/var/www/nestogy/includes/inc_all_modal.php"; ?>

<?php
$account_id = intval($_GET['account_id']);
$sql_account = mysqli_query($mysqli, "SELECT * FROM accounts WHERE account_id = $account_id");
$row = mysqli_fetch_array($sql_account);
$account_name = nullable_htmlentities($row['account_name']);
$account_type = intval($row['account_type']);
$account_notes = nullable_htmlentities($row['account_notes']);
?>

<div class="modal" id="editAccountModal<?= $account_id; ?>" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content bg-dark">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fa fa-fw fa-piggy-bank mr-2"></i>Editing account: <strong><?= $account_name; ?></strong></h5>
                <button type="button" class="close text-white" data-bs-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form action="/post.php" method="post" autocomplete="off">
                <div class="modal-body bg-white">
                                    <input type="hidden" name="account_id" value="<?= $account_id; ?>">
<div class="form-group">
                        <label>Account Name <strong class="text-danger">*</strong></label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="fa fa-fw fa-piggy-bank"></i></span>
                            </div>
                            <input type="text" class="form-control" name="name" value="<?= $account_name; ?>" placeholder="Account name" required>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label>Account Type <strong class="text-danger">*</strong></label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="fa fa-fw fa-list"></i></span>
                            </div>
                            <select class="form-control select" name="type" required>
                            <?php
                            $sql_account_types_select = mysqli_query($mysqli, "SELECT * FROM account_types ORDER BY account_type_name ASC");
                            while ($row = mysqli_fetch_array($sql_account_types_select)) {
                                $account_type_id_select = intval($row['account_type_id']);
                                $account_type_name_select = nullable_htmlentities($row['account_type_name']);
                                ?>
                                <option value="<?= $account_type_id_select; ?>" <?php if($account_type == $account_type_id_select){ echo "selected"; } ?>><?= $account_type_name_select; ?></option>
                            <?php
                            }
                            ?>
                        </select>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label>Notes</label>
                        <textarea class="form-control" rows="5" placeholder="Enter some notes" name="notes"><?= $account_notes; ?></textarea>
                    </div>
                
                </div>
                <div class="modal-footer bg-white">
                    <button type="submit" name="edit_account" class="btn btn-label-primary text-bold"></i>Save</button>
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal"></i>Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>
