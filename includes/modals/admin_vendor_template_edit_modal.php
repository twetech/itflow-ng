<?php require_once "/var/www/nestogy/includes/inc_all_modal.php"; ?>

<?php
$vendor_id = intval($_GET['vendor_id']);
$sql_vendor = mysqli_query($mysqli, "SELECT * FROM vendor_templates WHERE vendor_id = $vendor_id");
$row = mysqli_fetch_array($sql_vendor);

$vendor_name = nullable_htmlentities($row['vendor_name']);
$vendor_description = nullable_htmlentities($row['vendor_description']);
$vendor_account_number = nullable_htmlentities($row['vendor_account_number']);
$vendor_contact_name = nullable_htmlentities($row['vendor_contact_name']);
$vendor_phone = nullable_htmlentities($row['vendor_phone']);
$vendor_extension = nullable_htmlentities($row['vendor_extension']);
$vendor_hours = nullable_htmlentities($row['vendor_hours']);
$vendor_email = nullable_htmlentities($row['vendor_email']);
$vendor_website = nullable_htmlentities($row['vendor_website']);
$vendor_sla = nullable_htmlentities($row['vendor_sla']);
$vendor_code = nullable_htmlentities($row['vendor_code']);
$vendor_notes = nullable_htmlentities($row['vendor_notes']);
?>


<div class="modal" id="editVendorTemplateModal<?= $vendor_id; ?>" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content bg-dark">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-fw fa-building mr-2"></i>Editing vendor template: <strong><?= $vendor_name; ?></strong></h5>
                <button type="button" class="close text-white" data-bs-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form action="/post.php" method="post" autocomplete="off">
                <div class="modal-body bg-white">
                <input type="hidden" name="vendor_id" value="<?= $vendor_id; ?>">

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

                    <div class="alert alert-info">Check the fields you would like to update globally</div>

                    <div class="tab-content">

                        <div class="tab-pane fade show active" id="pills-details<?= $vendor_id; ?>">


                            <div class="form-group">
                                <label>Vendor Name <strong class="text-danger">*</strong></label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fa fa-fw fa-building"></i></span>
                                    </div>
                                    <input type="text" class="form-control" name="name" placeholder="Vendor Name" value="<?= "$vendor_name"; ?>" required>
                                    <div class="input-group-append">
                                        <div class="input-group-text">
                                            <input type="checkbox" name="global_update_vendor_name" value="1">
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
                                    <input type="text" class="form-control" name="description" placeholder="Description" value="<?= $vendor_description; ?>">
                                    <div class="input-group-append">
                                        <div class="input-group-text">
                                            <input type="checkbox" name="global_update_vendor_description" value="1">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label>Account Number</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fa fa-fw fa-fingerprint"></i></span>
                                    </div>
                                    <input type="text" class="form-control" name="account_number" placeholder="Account number" value="<?= $vendor_account_number; ?>">
                                    <div class="input-group-append">
                                        <div class="input-group-text">
                                            <input type="checkbox" name="global_update_vendor_account_number" value="1">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label>Account Manager</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fa fa-fw fa-user"></i></span>
                                    </div>
                                    <input type="text" class="form-control" name="contact_name" value="<?= $vendor_contact_name; ?>" placeholder="Vendor contact name">
                                    <div class="input-group-append">
                                        <div class="input-group-text">
                                            <input type="checkbox" name="global_update_vendor_contact_name" value="1">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" id="updateVendorsCheckbox<?= $vendor_id; ?>" name="update_base_vendors" value="1" >
                                    <label class="custom-control-label" for="updateVendorsCheckbox<?= $vendor_id; ?>">Update All Base Vendors</label>
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
                                            <div class="input-group-append">
                                                <div class="input-group-text">
                                                    <input type="checkbox" name="global_update_vendor_phone" value="1">
                                                </div>
                                            </div>
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
                                    <div class="input-group-append">
                                        <div class="input-group-text">
                                            <input type="checkbox" name="global_update_vendor_hours" value="1">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label>Support Email</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fa fa-fw fa-envelope"></i></span>
                                    </div>
                                    <input type="email" class="form-control" name="email" placeholder="Support Email" value="<?= $vendor_email; ?>">
                                    <div class="input-group-append">
                                        <div class="input-group-text">
                                            <input type="checkbox" name="global_update_vendor_email" value="1">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label>Support Website URL</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fa fa-fw fa-globe"></i></span>
                                    </div>
                                    <input type="text" class="form-control" name="website" placeholder="Do not include http(s)://" value="<?= $vendor_website; ?>">
                                    <div class="input-group-append">
                                        <div class="input-group-text">
                                            <input type="checkbox" name="global_update_vendor_website" value="1">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label>SLA</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fa fa-fw fa-handshake"></i></span>
                                    </div>
                                    <input type="text" class="form-control" name="sla" placeholder="SLA Response Time" value="<?= $vendor_sla; ?>">
                                    <div class="input-group-append">
                                        <div class="input-group-text">
                                            <input type="checkbox" name="global_update_vendor_sla" value="1">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label>Pin/Code</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fa fa-fw fa-key"></i></span>
                                    </div>
                                    <input type="text" class="form-control" name="code" placeholder="Access Code or Pin" value="<?= $vendor_code; ?>">
                                    <div class="input-group-append">
                                        <div class="input-group-text">
                                            <input type="checkbox" name="global_update_vendor_code" value="1">
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>

                        <div class="tab-pane fade" role="tabpanel" id="pills-notes<?= $vendor_id; ?>">

                            <div class="form-group">
                                <textarea class="form-control" rows="8" placeholder="Enter some notes" name="notes"><?= $vendor_notes; ?></textarea>
                            </div>
                            
                            <div class="form-group">
                                <label>Update Notes Globally?</label>
                                <input type="checkbox" name="global_update_vendor_notes" value="1">
                            </div>

                        </div>

                    </div>

                </div>
                <div class="modal-footer bg-white">
                    <button type="submit" class="btn btn-label-primary text-bold" name="edit_vendor_template"></i>Update Template</button>
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal"></i>Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>
