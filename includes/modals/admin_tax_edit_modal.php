<?php require_once "/var/www/nestogy/includes/inc_all_modal.php"; ?>

<?php
$tax_id = intval($_GET['tax_id']);

$sql_tax = mysqli_query($mysqli, "SELECT * FROM taxes WHERE tax_id = $tax_id");
$row = mysqli_fetch_array($sql_tax);

$tax_name = nullable_htmlentities($row['tax_name']);
$tax_percent = nullable_htmlentities($row['tax_percent']);
?>


<div class="modal" id="editTaxModal<?= $tax_id; ?>" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content bg-dark">
      <div class="modal-header">
        <h5 class="modal-title"><i class="fas fa-fw fa-balance-scale mr-2"></i>Editing tax: <strong><?= $tax_name; ?></strong></h5>
        <button type="button" class="close text-white" data-bs-dismiss="modal">
          <span>&times;</span>
        </button>
      </div>
      <form action="/post.php" method="post" autocomplete="off">
        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
        <input type="hidden" name="tax_id" value="<?= $tax_id; ?>">

        <div class="modal-body bg-white">

          <div class="form-group">
            <label>Name <strong class="text-danger">*</strong></label>
            <input type="text" class="form-control" name="name" value="<?= $tax_name; ?>" required>
          </div>

          <div class="form-group">
            <label>Percent <strong class="text-danger">*</strong></label>
            <input type="number" min="0" step="any" class="form-control col-md-4" name="percent" value="<?= $tax_percent; ?>">

          </div>

        </div>
        <div class="modal-footer bg-white">
          <button type="submit" name="edit_tax" class="btn btn-label-primary text-bold"><i class="fas fa-check mr-2"></i>Save</button>
          <button type="button" class="btn btn-light" data-bs-dismiss="modal"><i class="fas fa-times mr-2"></i>Cancel</button>
        </div>
      </form>
    </div>
  </div>
</div>
