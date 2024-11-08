<?php
require_once "/var/www/portal.twe.tech/includes/inc_all.php";

?>

<div class="card">
    <div class="card-header py-3">
        <h3 class="card-title"><i class="fas fa-fw fa-user mr-2"></i>Your User Details</h3>
    </div>
    <div class="card-body">
        <a type="button" href="/public/subscribe.php" class="btn btn-label-primary btn-block mt-3"><i class="fas fa-check mr-2"></i>Subscribe to notifications on this device</a>

        <form action="/post.php" method="post" enctype="multipart/form-data" autocomplete="off">
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">

            <center class="mb-3 px-5">
                <?php if (empty($avatar)) { ?>
                    <i class="fas fa-user-circle fa-8x text-secondary"></i>
                <?php } else { ?>
                    <img alt="User avatar" src="<?= "/uploads/users/$user_id/" . nullable_htmlentities($avatar); ?>" class="img-fluid">
                <?php } ?>
                <h4 class="text-secondary mt-2"><?= nullable_htmlentities($user_role_display); ?></h4>
            </center>

            <hr>

            <div class="form-group">
                <label>Your Name <strong class="text-danger">*</strong></label>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text"><i class="fa fa-fw fa-user"></i></span>
                    </div>
                    <input type="text" class="form-control" name="name" placeholder="Full Name" value="<?= stripslashes(nullable_htmlentities($name)); ?>" required>
                </div>
            </div>

            <div class="form-group">
                <label>Your Email <strong class="text-danger">*</strong></label>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text"><i class="fa fa-fw fa-envelope"></i></span>
                    </div>
                    <input type="email" class="form-control" name="email" placeholder="Email Address" value="<?= nullable_htmlentities($email); ?>" required>
                </div>
            </div>

            <div class="form-group">
                <label>Your Avatar</label>
                <input type="file" class="form-control-file" accept="image/*;capture=camera" name="file">
            </div>

            <button type="submit" name="edit_your_user_details" class="btn btn-label-primary btn-block mt-3"><i class="fas fa-check mr-2"></i>Save</button>


        </form>
                
    </div>

</div>

<?php
require_once '/var/www/portal.twe.tech/includes/footer.php';
