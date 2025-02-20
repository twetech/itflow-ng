<?php require_once "/var/www/nestogy/includes/inc_all_modal.php"; ?>
<div class="modal" id="editSoftwareModal<?= $software_id; ?>" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content bg-dark">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fa fa-fw fa-cube mr-2"></i>Editing license: <strong><?= $software_name; ?></strong></h5>
                <button type="button" class="close text-white" data-bs-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form action="/post.php" method="post" autocomplete="off">

                <div class="modal-body bg-white">
                <input type="hidden" name="software_id" value="<?= $software_id; ?>">
                <input type="hidden" name="client_id" value="<?= $client_id; ?>">
                    <ul class="nav nav-pills  mb-3">
                        <li class="nav-item">
                            <a class="nav-link active" role="tab" data-bs-toggle="tab" href="#pills-details<?= $software_id; ?>">Details</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" role="tab" data-bs-toggle="tab" href="#pills-license<?= $software_id; ?>">License</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" role="tab" data-bs-toggle="tab" href="#pills-device-licenses<?= $software_id; ?>">Devices</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" role="tab" data-bs-toggle="tab" href="#pills-user-licenses<?= $software_id; ?>">Users</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" role="tab" data-bs-toggle="tab" href="#pills-notes<?= $software_id; ?>">Notes</a>
                        </li>
                    </ul>

                    <hr>

                    <div class="tab-content">

                        <div class="tab-pane fade show active" id="pills-details<?= $software_id; ?>">

                            <div class="form-group">
                                <label>Software Name <strong class="text-danger">*</strong></label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fa fa-fw fa-cube"></i></span>
                                    </div>
                                    <input type="text" class="form-control" name="name" placeholder="Software name" value="<?= $software_name; ?>" required>
                                </div>
                            </div>

                            <div class="form-group">
                                <label>Version</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fa fa-fw fa-cube"></i></span>
                                    </div>
                                    <input type="text" class="form-control" name="version" placeholder="Software version" value="<?= $software_version; ?>">
                                </div>
                            </div>

                            <div class="form-group">
                                <label>Description</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fa fa-fw fa-angle-right"></i></span>
                                    </div>
                                    <input type="text" class="form-control" name="description" placeholder="Short description" value="<?= $software_description; ?>">
                                </div>
                            </div>

                            <div class="form-group">
                                <label>Type <strong class="text-danger">*</strong></label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fa fa-fw fa-tag"></i></span>
                                    </div>
                                    <select class="form-control select2"  name="type" required>
                                        <?php foreach($software_types_array as $software_type_select) { ?>
                                            <option <?php if ($software_type == $software_type_select) { echo "selected"; } ?>><?= $software_type_select; ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>

                        </div>

                         <div class="tab-pane fade" role="tabpanel" id="pills-license<?= $software_id; ?>">

                            <div class="form-group">
                                <label>License Type</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fa fa-fw fa-cube"></i></span>
                                    </div>
                                    <select class="form-control select2"  name="license_type">
                                        <option value="">- Select a License Type -</option>
                                        <?php foreach($license_types_array as $license_type_select) { ?>
                                            <option <?php if ($license_type_select == $software_license_type) { echo "selected"; } ?>><?= $license_type_select; ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group">
                                <label>Seats</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fa fa-fw fa-cube"></i></span>
                                    </div>
                                    <input type="text" class="form-control" inputmode="numeric" pattern="[0-9]*" name="seats" placeholder="Number of seats" value="<?= $software_seats; ?>">
                                </div>
                            </div>

                            <div class="form-group">
                                <label>License Key</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fa fa-fw fa-key"></i></span>
                                    </div>
                                    <input type="text" class="form-control" name="key" placeholder="License key" value="<?= $software_key; ?>">
                                </div>
                            </div>

                            <div class="form-group">
                                <label>Purchase Date</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fa fa-fw fa-calendar-check"></i></span>
                                    </div>
                                    <input type="date" class="form-control" name="purchase" max="2999-12-31" value="<?= $software_purchase; ?>">
                                </div>
                            </div>

                            <div class="form-group">
                                <label>Expire</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fa fa-fw fa-calendar-times"></i></span>
                                    </div>
                                    <input type="date" class="form-control" name="expire" max="2999-12-31" value="<?= $software_expire; ?>">
                                </div>
                            </div>

                        </div>

                        <div class="tab-pane fade" role="tabpanel" id="pills-device-licenses<?= $software_id; ?>">

                            <div class="alert alert-info">
                                Select Assets that are licensed for this software
                            </div>

                            <ul class="list-group">

                                <?php
                                $sql_assets_select = mysqli_query($mysqli, "SELECT * FROM assets LEFT JOIN contacts ON asset_contact_id = contact_id WHERE (asset_archived_at > '$software_created_at' OR asset_archived_at IS NULL) AND asset_client_id = $client_id ORDER BY asset_archived_at ASC, asset_name ASC");

                                while ($row = mysqli_fetch_array($sql_assets_select)) {
                                    $asset_id_select = intval($row['asset_id']);
                                    $asset_name_select = nullable_htmlentities($row['asset_name']);
                                    $asset_type_select = nullable_htmlentities($row['asset_type']);
                                    $asset_archived_at = nullable_htmlentities($row['asset_archived_at']);
                                    if (empty($asset_archived_at)) {
                                        $asset_archived_display = "";
                                    } else {
                                        $asset_archived_display = "Archived - ";
                                    }
                                    $contact_name_select = nullable_htmlentities($row['contact_name']);

                                    ?>
                                    <li class="list-group-item">
                                        <div class="form-check">
                                            <input type="checkbox" class="form-check-input" name="assets[]" value="<?= $asset_id_select; ?>" <?php if (in_array($asset_id_select, $asset_licenses_array)) { echo "checked"; } ?>>
                                            <label class="form-check-label ml-2"><?= "$asset_archived_display$asset_name_select - $contact_name_select"; ?></label>
                                        </div>
                                    </li>

                                <?php } ?>

                            </ul>

                        </div>

                        <div class="tab-pane fade" role="tabpanel" id="pills-user-licenses<?= $software_id; ?>">

                            <div class="alert alert-info">
                                Select Users that are licensed for this software
                            </div>

                            <ul class="list-group">

                                <?php
                                $sql_contacts_select = mysqli_query($mysqli, "SELECT * FROM contacts WHERE (contact_archived_at > '$software_created_at' OR contact_archived_at IS NULL) AND contact_client_id = $client_id ORDER BY contact_archived_at ASC, contact_name ASC");

                                while ($row = mysqli_fetch_array($sql_contacts_select)) {
                                    $contact_id_select = intval($row['contact_id']);
                                    $contact_name_select = nullable_htmlentities($row['contact_name']);
                                    $contact_email_select = nullable_htmlentities($row['contact_email']);
                                    $contact_archived_at = nullable_htmlentities($row['contact_archived_at']);
                                    if (empty($contact_archived_at)) {
                                        $contact_archived_display = "";
                                    } else {
                                        $contact_archived_display = "Archived - ";
                                    }

                                    ?>
                                    <li class="list-group-item">
                                        <div class="form-check">
                                            <input type="checkbox" class="form-check-input" name="contacts[]" value="<?= $contact_id_select; ?>" <?php if (in_array("$contact_id_select", $contact_licenses_array)) { echo "checked"; } ?>>
                                            <label class="form-check-label ml-2"><?= "$contact_archived_display$contact_name_select - $contact_email_select"; ?></label>
                                        </div>
                                    </li>

                                <?php } ?>

                            </ul>

                        </div>

                        <div class="tab-pane fade" role="tabpanel" id="pills-notes<?= $software_id; ?>">

                            <textarea class="form-control" rows="12" placeholder="Enter some notes" name="notes"><?= $software_notes; ?></textarea>

                        </div>

                    </div>

                </div>
                <div class="modal-footer bg-white">
                    <button type="submit" name="edit_software" class="btn btn-label-primary text-bold"></i>Save</button>
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal"></i>Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>
