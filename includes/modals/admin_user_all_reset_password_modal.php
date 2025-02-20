<?php require_once "/var/www/nestogy/includes/inc_all_modal.php"; ?>

<div class="modal" id="resetAllUserPassModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-header">
            <h5 class="modal-title">Incident Response: Agent Password Reset</h5>
            <button type="button" class="close" data-bs-dismiss="modal">
                <span>&times;</span>
            </button>
            <form action="/post.php" method="POST">
        </div>
        <div class="modal-content">
            <div class="modal-body">
                <div class="mb-4" style="text-align: center;">
                    <i class="far fas fa-10x fa-skull-crossbones text-danger mb-3 mt-3"></i>
                    <h2>Incident Response: Agent Password Reset</h2>
                    <br>
                    <div class="alert alert-danger" role="alert">
                        <b>This is a potentially destructive function.<br>It is intended to be used as part of a potential security incident.</b>
                    </div>
                    <h6 class="mb-4 text-secondary"><b>All ITFlow agent passwords will be reset and shown to you </b><i>(except yours - change yours first!)</i>.<br/><br/>You should communicate temporary passwords to agents out of band (e.g. via a phone call) and require they are changed ASAP.</h6>
                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                        <div class="row col-7 offset-4">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <input type="password" class="form-control" placeholder="Enter your account password to continue" name="admin_password" autocomplete="new-password" required>
                                </div>
                            </div>
                        </div>
                        <br>
                </div>

            </div>
            
            <div class="modal-footer">
            <button type="button" class="btn btn-outline-secondary btn-lg px-5 mr-4" data-bs-dismiss="modal">Cancel</button>
            <button class="btn btn-danger" type="submit" name="ir_reset_user_password"><i class="fas fa-fw fa-key mr-2"></i>Reset passwords</button>
            </form>
            </div>
        </div>
    </div>
</div>
