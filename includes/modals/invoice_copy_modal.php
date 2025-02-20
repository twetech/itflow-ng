<?php require_once "/var/www/nestogy/includes/inc_all_modal.php"; ?>
<div class="modal" id="addInvoiceCopyModal<?= $invoice_id; ?>" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content bg-dark">
      <div class="modal-header">
        <h5 class="modal-title"><i class="fas fa-fw fa-copy mr-2"></i>Copying invoice: <strong><?= "$invoice_prefix$invoice_number"; ?></strong> - <?= $client_name; ?></h5>
        <button type="button" class="close text-white" data-bs-dismiss="modal">
          <span>&times;</span>
        </button>
      </div>
      <form action="/post.php" method="post" autocomplete="off">
        <input type="hidden" name="invoice_id" value="<?= $invoice_id; ?>">
        
        <div class="modal-body bg-white">

          <div class="form-group">
            <label>Invoice Date <strong class="text-danger">*</strong></label>
            <div class="input-group">
              <div class="input-group-prepend">
                <span class="input-group-text"><i class="fa fa-fw fa-calendar"></i></span>
              </div>
              <input type="date" class="form-control" name="date" max="2999-12-31" value="<?= date("Y-m-d"); ?>" required>
            </div>
          </div>
          
        </div>
        <div class="modal-footer bg-white">
          <button type="submit" name="add_invoice_copy" class="btn btn-label-primary text-bold"></i>Copy</button>
          <button type="button" class="btn btn-light" data-bs-dismiss="modal"></i>Cancel</button>
        </div>
      </form>
    </div>
  </div>
</div>