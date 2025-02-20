<?php require_once "/var/www/nestogy/includes/inc_all_modal.php"; ?>

<?php
$user_id = intval($_GET['user_id']);
$sql_user = mysqli_query($mysqli, "SELECT * FROM users WHERE user_id = $user_id");
$row = mysqli_fetch_array($sql_user);

$user_name = nullable_htmlentities($row['user_name']);
$user_email = nullable_htmlentities($row['user_email']);
$user_role = nullable_htmlentities($row['user_role']);
$user_status = nullable_htmlentities($row['user_status']);
?>


<div class="modal" id="archiveUserModal<?= $user_id; ?>" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-body">
        <div class="mb-4" style="text-align: center;">
          <i class="far fa-10x fa-times-circle text-danger mb-3 mt-3"></i>
          <h2>Are you sure?</h2>
          <h6 class="mb-4 text-secondary">Do you really want to <b>archive <?= $user_name; ?></b>? This process cannot be undone.</h6>
          <h6 class="mb-4 text-secondary"><?= $user_name ?> will no longer be able to log in or use ITFlow, but all associated content will remain accessible.</h6>
          <button type="button" class="btn btn-outline-secondary btn-lg px-5 mr-4" data-bs-dismiss="modal">Cancel</button>
          <a class="btn btn-danger btn-lg px-5" href="/post.php?archive_user=<?= $user_id; ?>&csrf_token=<?= $_SESSION['csrf_token'] ?>">Yes, archive!</a>
        </div>
      </div>
    </div>
  </div>
</div>
