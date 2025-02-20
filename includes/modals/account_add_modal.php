
<?php require_once "/var/www/nestogy/includes/inc_all_modal.php"; ?>

<div class="modal" id="addAccountModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content bg-dark">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fa fa-fw fa-piggy-bank mr-2"></i>New Account</h5>
                <button type="button" class="close text-white" data-bs-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form action="/post.php" method="post" autocomplete="off">
                <div class="modal-body bg-white">

                    <div class="form-group">
                        <label>Account Name <strong class="text-danger">*</strong></label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="fa fa-fw fa-piggy-bank"></i></span>
                            </div>
                            <input type="text" class="form-control" name="name" placeholder="Account name" required autofocus>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Account Type <strong class="text-danger">*</strong></label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="fa fa-fw fa-list"></i></span>
                            </div>
                            <select class="form-control select" name="type" required>
                            <option value="">- Select -</option>
                            <?php
                            $sql_account_types = mysqli_query($mysqli, "SELECT * FROM account_types ORDER BY account_type_name ASC");
                            while ($row = mysqli_fetch_array($sql_account_types)) {
                                $account_type_id = intval($row['account_type_id']);
                                $account_type_name = nullable_htmlentities($row['account_type_name']);

                                echo "<option value='$account_type_id'>$account_type_name</option>";
                                
                            }
                            ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Opening Balance <strong class="text-danger">*</strong></label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="fa fa-fw fa-dollar-sign"></i></span>
                            </div>
                            <input type="text" class="form-control" inputmode="numeric" pattern="-?[0-9]*\.?[0-9]{0,2}" name="opening_balance" placeholder="0.00" required>
                    </div>

                    <div class="form-group">
                        <label>Currency <strong class="text-danger">*</strong></label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="fa fa-fw fa-money-bill"></i></span>
                            </div>
                            <select class="form-control select2"  name="currency_code" required>
                                <option value="">- Currency -</option>
                                <?php foreach ($currencies_array as $currency_code => $currency_name) { ?>
                                    <option <?php if ($company_currency == $currency_code) { echo "selected"; } ?> value="<?= $currency_code; ?>"><?= "$currency_code - $currency_name"; ?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Notes</label>
                        <textarea class="form-control" rows="5" placeholder="Enter some notes" name="notes"></textarea>
                    </div>

                </div>
                <div class="modal-footer bg-white">
                    <button type="submit" name="add_account" class="btn btn-label-primary text-bold">Create</button>
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>
