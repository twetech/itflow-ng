<?php
require_once "/var/www/nestogy/includes/tenant_db.php";

require_once "/var/www/nestogy/includes/config/config.php";

include_once "/var/www/nestogy/includes/functions/functions.php";

require_once "check_login.php";

require_once "header.php";

?>

<div class="card">
    <div class="card-header py-3">
        <h3 class="card-title"><i class="fas fa-fw fa-sheild mr-2"></i>2FA Setup</h3>
    </div>
    <div class="card-body">

        <form action="/post.php" method="post" autocomplete="off">
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">

            <?php if (empty($token)) { ?>
                <button type="submit" name="enable_2fa" class="btn btn-success btn-block mt-3"><i class="fa fa-fw fa-lock"></i><br> Enable 2FA</button>
            <?php } else { ?>
                <p>You have set up 2FA. Your QR code is below.</p>
                <button type="submit" name="disable_2fa" class="btn btn-danger btn-block mt-3"><i class="fa fa-fw fa-unlock"></i><br>Disable 2FA</button>
            <?php } ?>

            <center>
                <?php

                require_once 'rfc6238.php';


                //Generate a base32 Key
                $secretkey = key32gen();

                if (!empty($token)) {

                    //Generate QR Code based off the generated key
                    print sprintf('<img src="%s"/>', TokenAuth6238::getBarCodeUrl($name, ' ', $token, $_SERVER['SERVER_NAME']));

                    echo "<p class='text-secondary'>$token</p>";
                }

                ?>
            </center>

            <input type="hidden" name="token" value="<?= $secretkey; ?>">

        </form>

        <?php if (!empty($token)) { ?>
            <form action="/post.php" method="post" autocomplete="off">
                <div class="form-group">
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fa fa-fw fa-key"></i></span>
                        </div>
                        <input type="text" class="form-control" name="code" placeholder="Verify 2FA Code" required>
                        <div class="input-group-append">
                            <button type="submit" name="verify" class="btn btn-success">Verify</button>
                        </div>
                    </div>
                </div>

            </form>
        <?php } ?>
    </div>
</div>

<?php
require_once '/var/www/nestogy/includes/footer.php';

