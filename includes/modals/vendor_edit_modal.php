<?php require_once "/var/www/nestogy/includes/inc_all_modal.php"; ?>
<div class="modal" id="editVendorModal<?= $vendor_id; ?>" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content bg-dark">
      <div class="modal-header">
        <h5 class="modal-title"><i class="fas fa-fw fa-building mr-2"></i>Editing vendor: <strong><?= $vendor_name; ?></strong></h5>
        <button type="button" class="close text-white" data-bs-dismiss="modal">
          <span>&times;</span>
        </button>
      </div>
      <form action="/post.php" method="post" autocomplete="off">
        <input type="hidden" name="vendor_id" value="<?= $vendor_id; ?>">
        <div class="modal-body bg-white">

          <ul class="nav nav-pills  mb-3">
            <li class="nav-item">
              <a class="nav-link active" role="tab" data-bs-toggle="tab" href="#pills-details<?= $vendor_id; ?>">Details</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" role="tab" data-bs-toggle="tab" href="#pills-support<?= $vendor_id; ?>">Support</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" role="tab" data-bs-toggle="tab" href="#pills-notes<?= $vendor_id; ?>">Notes</a>
            </li>
          </ul>

          <hr>
          
          <div class="tab-content">

            <div class="tab-pane fade show active" id="pills-details<?= $vendor_id; ?>">

              <div class="form-group">
                <label>Vendor Name <strong class="text-danger">*</strong></label>
                <div class="input-group">
                  <div class="input-group-prepend">
                    <span class="input-group-text"><i class="fa fa-fw fa-building"></i></span>
                  </div>
                  <input type="text" class="form-control" name="name" placeholder="Vendor Name" value="<?= "$vendor_name"; ?>" required>
                </div>
              </div>
              
              <div class="form-group">
                <label>Description</label>
                <div class="input-group">
                  <div class="input-group-prepend">
                    <span class="input-group-text"><i class="fa fa-fw fa-angle-right"></i></span>
                  </div>
                  <input type="text" class="form-control" name="description" placeholder="Description" value="<?= $vendor_description; ?>">
                </div>
              </div>

              <div class="form-group">
                <label>Account Number</label>
                <div class="input-group">
                  <div class="input-group-prepend">
                    <span class="input-group-text"><i class="fa fa-fw fa-fingerprint"></i></span>
                  </div>
                  <input type="text" class="form-control" name="account_number" placeholder="Account number" value="<?= $vendor_account_number; ?>">
                </div>
              </div>

              <div class="form-group">
                <label>Account Manager</label>
                <div class="input-group">
                  <div class="input-group-prepend">
                    <span class="input-group-text"><i class="fa fa-fw fa-user"></i></span>
                  </div>
                  <input type="text" class="form-control" name="contact_name" value="<?= $vendor_contact_name; ?>" placeholder="Vendor contact name">
                </div>
              </div>

              <div class="form-group">
                  <label>Template Base</label>
                  <div class="input-group">
                      <div class="input-group-prepend">
                          <span class="input-group-text"><i class="fa fa-fw fa-puzzle-piece"></i></span>
                      </div>
                      <select class="form-control select2"  name="vendor_template_id">
                          <option value="0">- None -</option>
                          <?php

                          $sql_vendor_templates = mysqli_query($mysqli, "SELECT * FROM vendors WHERE vendor_template = 1 AND vendor_archived_at IS NULL ORDER BY vendor_name ASC");
                          while ($row = mysqli_fetch_array($sql_vendor_templates)) {
                              $vendor_template_id_select = $row['vendor_id'];
                              $vendor_template_name_select = nullable_htmlentities($row['vendor_name']); ?>
                              <option <?php if ($vendor_template_id == $vendor_template_id_select) { echo "selected"; } ?> value="<?= $vendor_template_id_select; ?>"><?= $vendor_template_name_select; ?></option>

                          <?php } ?>
                      </select>
                  </div>
              </div>

            </div>
            
            <div class="tab-pane fade" role="tabpanel" id="pills-support<?= $vendor_id; ?>">

              <label>Support Phone</label>
              <div class="form-row">
                <div class="col-8">
                  <div class="form-group">
                    <div class="input-group">
                      <div class="input-group-prepend">
                        <span class="input-group-text"><i class="fa fa-fw fa-phone"></i></span>
                      </div>
                      <input type="text" class="form-control" name="phone" placeholder="Phone Number" value="<?= $vendor_phone; ?>"> 
                    </div>
                  </div>
                </div>
                <div class="col-4">
                  <input type="text" class="form-control" name="extension" placeholder="Prompts" value="<?= $vendor_extension; ?>">
                </div>
              </div>

              <div class="form-group">
                <label>Support Hours</label>
                <div class="input-group">
                  <div class="input-group-prepend">
                    <span class="input-group-text"><i class="fa fa-fw fa-calendar"></i></span>
                  </div>
                  <input type="text" class="form-control" name="hours" placeholder="Support Hours" value="<?= $vendor_hours; ?>">
                </div>
              </div>
              
              <div class="form-group">
                <label>Support Email</label>
                <div class="input-group">
                  <div class="input-group-prepend">
                    <span class="input-group-text"><i class="fa fa-fw fa-envelope"></i></span>
                  </div>
                  <input type="email" class="form-control" name="email" placeholder="Support Email" value="<?= $vendor_email; ?>">
                </div>
              </div>
              
              <div class="form-group">
                <label>Support Website URL</label>
                <div class="input-group">
                  <div class="input-group-prepend">
                    <span class="input-group-text"><i class="fa fa-fw fa-globe"></i></span>
                  </div>
                  <input type="text" class="form-control" name="website" placeholder="Do not include http(s)://" value="<?= $vendor_website; ?>">
                </div>
              </div>

              <div class="form-group">
                <label>SLA</label>
                <div class="input-group">
                  <div class="input-group-prepend">
                    <span class="input-group-text"><i class="fa fa-fw fa-handshake"></i></span>
                  </div>
                  <input type="text" class="form-control" name="sla" placeholder="SLA Response Time" value="<?= $vendor_sla; ?>">
                </div>
              </div>

              <div class="form-group">
                <label>Pin/Code</label>
                <div class="input-group">
                  <div class="input-group-prepend">
                    <span class="input-group-text"><i class="fa fa-fw fa-key"></i></span>
                  </div>
                  <input type="text" class="form-control" name="code" placeholder="Access Code or Pin" value="<?= $vendor_code; ?>">
                </div>
              </div>
            
            </div>

            <div class="tab-pane fade" role="tabpanel" id="pills-notes<?= $vendor_id; ?>">
              
              <div class="form-group">
                <textarea class="form-control" rows="12" placeholder="Enter some notes" name="notes"><?= $vendor_notes; ?></textarea>
              </div>

            </div>

          </div>
          
        </div>
        <div class="modal-footer bg-white">
          <button type="submit" name="edit_vendor" class="btn btn-label-primary text-bold"><i class="fas fa-check mr-2"></i>Save</button>
          <button type="button" class="btn btn-light" data-bs-dismiss="modal"></i>Cancel</button>
        </div>
      </form>
    </div>
  </div>
</div>
