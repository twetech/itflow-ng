<?php require_once "/var/www/nestogy/includes/inc_all_modal.php"; ?>

<?php
$user_id = intval($_GET['user_id']);
$sql_user = mysqli_query($mysqli, "SELECT * FROM users LEFT JOIN user_settings ON user_settings.user_id = users.user_id WHERE users.user_id = $user_id");
$row = mysqli_fetch_array($sql_user);

$user_name = nullable_htmlentities($row['user_name']);
$user_email = nullable_htmlentities($row['user_email']);
$user_role = nullable_htmlentities($row['user_role']);  
$user_avatar = nullable_htmlentities($row['user_avatar']);
$user_initials = strtoupper($user_name[0].$user_name[1]);
$user_token = nullable_htmlentities($row['user_token']);
$user_config_force_mfa = nullable_htmlentities($row['user_config_force_mfa']);
?>

<div class="modal" id="editUserModal<?= $user_id; ?>" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content bg-dark">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-fw fa-user-edit mr-2"></i>Editing user:
                    <strong><?= $user_name; ?></strong></h5>
                <button type="button" class="close text-white" data-bs-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form action="/post.php" method="post" enctype="multipart/form-data" autocomplete="off">

                <div class="modal-body bg-white">
                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                <input type="hidden" name="user_id" value="<?= $user_id; ?>">
                    <center class="mb-3">
                        <?php if (!empty($user_avatar)) { ?>
                            <img class="img-fluid" src="<?= "/uploads/users/$user_id/$user_avatar"; ?>">
                        <?php } else { ?>
                            <span class="fa-stack fa-4x">
                            <i class="fa fa-circle fa-stack-2x text-secondary"></i>
                            <span class="fa fa-stack-1x text-white"><?= $user_initials; ?></span>
                            </span>
                        <?php } ?>
                    </center>

                    <div class="form-group">
                        <label>Name <strong class="text-danger">*</strong></label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="fa fa-fw fa-user"></i></span>
                            </div>
                            <input type="text" class="form-control" name="name" placeholder="Full Name"
                                   value="<?= $user_name; ?>" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Email <strong class="text-danger">*</strong></label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="fa fa-fw fa-envelope"></i></span>
                            </div>
                            <input type="email" class="form-control" name="email" placeholder="Email Address"
                                   value="<?= $user_email; ?>" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>New Password</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="fa fa-fw fa-lock"></i></span>
                            </div>
                            <input type="password" class="form-control" data-bs-toggle="password" name="new_password"
                                   placeholder="Leave Blank For No Password Change" autocomplete="new-password">
                            <div class="input-group-append">
                                <span class="input-group-text"><i class="fa fa-fw fa-eye"></i></span>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Role <strong class="text-danger">*</strong></label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="fa fa-fw fa-user-shield"></i></span>
                            </div>
                            <select class="form-control select2"  name="role" required>
                                <option value="">- Role -</option>
                                <option <?php if ($user_role == 3) {
                                    echo "selected";
                                } ?> value="3">Administrator
                                </option>
                                <option <?php if ($user_role == 2) {
                                    echo "selected";
                                } ?> value="2">Technician
                                </option>
                                <option <?php if ($user_role == 1) {
                                    echo "selected";
                                } ?> value="1">Accountant
                                </option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Avatar</label>
                        <input type="file" class="form-control-file" accept="image/*;capture=camera" name="file">
                    </div>

                    <div class="form-group">
                        <div class="custom-control custom-checkbox">
                            <input class="custom-control-input" type="checkbox" id="forceMFACheckBox<?= $user_id; ?>" name="force_mfa" value="1" <?php if($user_config_force_mfa == 1){ echo "checked"; } ?>>
                            <label for="forceMFACheckBox<?= $user_id; ?>" class="custom-control-label">
                                Force MFA
                            </label>
                        </div>
                    </div>

                    <?php if (!empty($user_token)) { ?>

                        <div class="form-group">
                            <label>2FA</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fa fa-fw fa-id-card"></i></span>
                                </div>
                                <select class="form-control" name="2fa">
                                    <option value="">Keep enabled</option>
                                    <option value="disable">Disable</option>
                                </select>
                            </div>
                        </div>

                    <?php } ?>

                </div>
                <div class="modal-footer bg-white">
                    <button type="submit" name="edit_user" class="btn btn-label-primary text-bold"><i class="fas fa-check mr-2"></i>Save</button>
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal"><i class="fas fa-times mr-2"></i>Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>
