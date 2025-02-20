<?php require_once "/var/www/nestogy/includes/inc_all_modal.php"; ?>
<?php
include '/var/www/nestogy/bootstrap.php';

$login_id = $_GET['login_id'];

use Twetech\Nestogy\Model\Documentation;

$documentation = new Documentation($pdo);
$login = $documentation->getLogin($login_id);

$login_name = $login['login_name'];
$login_description = $login['login_description'];
$login_username = $documentation->decryptLoginPassword($login['login_username']);
$login_password = $documentation->decryptLoginPassword($login['login_password']);
$login_otp_secret = $login['login_otp_secret'];
$login_uri = $login['login_uri'];
$login_uri_2 = $login['login_uri_2'];
$login_contact_id = $login['login_contact_id'];
$login_vendor_id = $login['login_vendor_id'];
$login_asset_id = $login['login_asset_id'];
$client_id = $login['login_client_id'];
?>

<div class="modal" id="editLoginModal<?= $login_id; ?>" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content bg-dark">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fa fa-fw fa-key mr-2"></i>Editing login: <strong><?= $login_name; ?></strong></h5>
                <button type="button" class="close text-white" data-bs-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form action="/post.php" method="post" autocomplete="off">

                <div class="modal-body bg-white">
                <input type="hidden" name="login_id" value="<?= $login_id; ?>">
                <input type="hidden" name="client_id" value="<?= $client_id; ?>">
                    <ul class="nav nav-pills  mb-3">
                        <li class="nav-item">
                            <a class="nav-link active" role="tab" data-bs-toggle="tab" href="#pills-details<?= $login_id; ?>">Details</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" role="tab" data-bs-toggle="tab" href="#pills-relation<?= $login_id; ?>">Relation</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" role="tab" data-bs-toggle="tab" href="#pills-notes<?= $login_id; ?>">Notes</a>
                        </li>
                    </ul>

                    <hr>

                    <div class="tab-content">

                        <div class="tab-pane fade show active" id="pills-details<?= $login_id; ?>">

                            <div class="form-group">
                                <label>Name <strong class="text-danger">*</strong> / <span class="text-secondary">Important?</span></label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fa fa-fw fa-key"></i></span>
                                    </div>
                                    <input type="text" class="form-control" name="name" placeholder="Name of Login" value="<?= $login_name; ?>" required>
                                    <div class="input-group-append">
                                        <div class="input-group-text">
                                            <input type="checkbox" name="important" value="1" <?php if ($login_important == 1) { echo "checked"; } ?>>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label>Description</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fa fa-fw fa-angle-right"></i></span>
                                    </div>
                                    <input type="text" class="form-control" name="description" placeholder="Description" value="<?= $login_description; ?>">
                                </div>
                            </div>

                            <div class="form-group">
                                <label>Username</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fa fa-fw fa-user"></i></span>
                                    </div>
                                    <input type="text" class="form-control" name="username" placeholder="Username" value="<?= $login_username; ?>">
                                </div>
                            </div>

                            <div class="form-group">
                                <label>Password <strong class="text-danger">*</strong></label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fa fa-fw fa-lock"></i></span>
                                    </div>
                                    <input class="form-control" name="password" placeholder="Password" value="<?= $login_password; ?>" required autocomplete="new-password">
                                    <div class="input-group-append">
                                        <button class="btn btn-default clipboardjs" type="button" data-clipboard-text="<?= $login_password; ?>"><i class="fa fa-fw fa-copy"></i></button>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label>OTP</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fa fa-fw fa-key"></i></span>
                                    </div>
                                    <input class="form-control" data-bs-toggle="password" name="otp_secret" value="<?= $login_otp_secret; ?>" placeholder="Insert secret key">
                                    <div class="input-group-append">
                                        <span class="input-group-text"><i class="fa fa-fw fa-eye"></i></span>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label>URI</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fa fa-fw fa-link"></i></span>
                                    </div>
                                    <input type="text" class="form-control" name="uri" placeholder="ex. http://192.168.1.1" value="<?= $login_uri; ?>">
                                    <div class="input-group-append">

                                        <a href="<?= $login_uri; ?>" class="input-group-text"><i class="fa fa-fw fa-link"></i></a>
                                    </div>
                                    <div class="input-group-append">
                                        <button class="input-group-text clipboardjs" type="button" data-clipboard-text="<?= $login_uri; ?>"><i class="fa fa-fw fa-copy"></i></button>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label>URI 2</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fa fa-fw fa-link"></i></span>
                                    </div>
                                    <input type="text" class="form-control" name="uri_2" placeholder="ex. https://server.company.com:5001" value="<?= $login_uri_2; ?>">
                                    <div class="input-group-append">
                                        <a href="<?= $login_uri_2; ?>" class="input-group-text"><i class="fa fa-fw fa-link"></i></a>
                                    </div>
                                    <div class="input-group-append">
                                        <button class="input-group-text clipboardjs" type="button" data-clipboard-text="<?= $login_uri_2; ?>"><i class="fa fa-fw fa-copy"></i></button>
                                    </div>
                                </div>
                            </div>

                        </div>

                        <div class="tab-pane fade" role="tabpanel" id="pills-relation<?= $login_id; ?>">

                            <div class="form-group">
                                <label>Contact</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fa fa-fw fa-user"></i></span>
                                    </div>
                                    <select class="form-control select2"  name="contact">
                                        <option value="">- Contact -</option>
                                        <?php

                                        $sql_contacts = $pdo->query("SELECT * FROM contacts WHERE contact_client_id = $client_id ORDER BY contact_name ASC");
                                        while ($row = $sql_contacts->fetch()) {
                                            $contact_id_select = intval($row['contact_id']);
                                            $contact_name_select = nullable_htmlentities($row['contact_name']);
                                            ?>
                                            <option <?php if ($login_contact_id == $contact_id_select) { echo "selected"; } ?> value="<?= $contact_id_select; ?>"><?= $contact_name_select; ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group">
                                <label>Vendor</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fa fa-fw fa-building"></i></span>
                                    </div>
                                    <select class="form-control select2"  name="vendor">
                                        <option value="0">- None -</option>
                                        <?php

                                        $sql_vendors = $pdo->query("SELECT * FROM vendors WHERE vendor_client_id = $client_id ORDER BY vendor_name ASC");
                                        while ($row = $sql_vendors->fetch()) {
                                            $vendor_id_select = intval($row['vendor_id']);
                                            $vendor_name_select = nullable_htmlentities($row['vendor_name']);
                                            ?>
                                            <option <?php if ($login_vendor_id == $vendor_id_select) { echo "selected"; } ?> value="<?= $vendor_id_select; ?>"><?= $vendor_name_select; ?></option>
                                        <?php } ?>

                                    </select>
                                </div>
                            </div>

                            <div class="form-group">
                                <label>Asset</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fa fa-fw fa-tag"></i></span>
                                    </div>
                                    <select class="form-control select2"  name="asset">
                                        <option value="0">- None -</option>
                                        <?php

                                        $sql_assets = $pdo->query("SELECT * FROM assets LEFT JOIN locations on asset_location_id = location_id WHERE asset_client_id = $client_id AND asset_archived_at IS NULL ORDER BY asset_name ASC");
                                        while ($row = $sql_assets->fetch()) {
                                            $asset_id_select = intval($row['asset_id']);
                                            $asset_name_select = nullable_htmlentities($row['asset_name']);
                                            $asset_location_select = nullable_htmlentities($row['location_name']);

                                            $asset_select_display_string = $asset_name_select;
                                            if (!empty($asset_location_select)) {
                                                $asset_select_display_string = "$asset_name_select ($asset_location_select)";
                                            }

                                            ?>
                                            <option <?php if ($login_asset_id == $asset_id_select) { echo "selected"; } ?> value="<?= $asset_id_select; ?>"><?= $asset_select_display_string; ?></option>

                                        <?php } ?>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group">
                                <label>Software</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fa fa-fw fa-box"></i></span>
                                    </div>
                                    <select class="form-control select2"  name="software">
                                        <option value="0">- None -</option>
                                        <?php

                                        $sql_software = $pdo->query("SELECT * FROM software WHERE software_client_id = $client_id ORDER BY software_name ASC");
                                        while ($row = $sql_software->fetch()) {
                                            $software_id_select = intval($row['software_id']);
                                            $software_name_select = nullable_htmlentities($row['software_name']);
                                            ?>
                                            <option <?php if ($login_software_id == $software_id_select) { echo "selected"; } ?> value="<?= $software_id_select; ?>"><?= $software_name_select; ?></option>

                                        <?php } ?>
                                    </select>
                                </div>
                            </div>

                        </div>

                        <div class="tab-pane fade" role="tabpanel" id="pills-notes<?= $login_id; ?>">

                            <div class="form-group">
                                <textarea class="form-control" rows="12" placeholder="Enter some notes" name="note"><?= $login_note; ?></textarea>
                            </div>

                        </div>

                    </div>
                </div>
                <div class="modal-footer bg-white">
                    <button type="submit" name="edit_login" class="btn btn-label-primary text-bold"></i>Save</button>
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal"></i>Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>
