<?php require_once "/var/www/nestogy/includes/inc_all_modal.php"; ?>
<div class="modal" id="addSoftwareModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content bg-dark">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fa fa-fw fa-cube mr-2"></i>New License</h5>
                <button type="button" class="close text-white" data-bs-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form action="/post.php" method="post" autocomplete="off">
                <div class="modal-body bg-white">
                <input type="hidden" name="client_id" value="<?= $client_id; ?>">

                    <ul class="nav nav-pills  mb-3">
                        <li class="nav-item">
                            <a class="nav-link active" role="tab" data-bs-toggle="tab" href="#pills-details">Details</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" role="tab" data-bs-toggle="tab" href="#pills-licensing">Licensing</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" role="tab" data-bs-toggle="tab" href="#pills-notes">Notes</a>
                        </li>
                    </ul>

                    <hr>

                    <div class="tab-content">

                        <div class="tab-pane fade show active" id="pills-details">

                            <div class="form-group">
                                <label>Software Name <strong class="text-danger">*</strong></label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fa fa-fw fa-cube"></i></span>
                                    </div>
                                    <input type="text" class="form-control" name="name" placeholder="Software name" required>
                                </div>
                            </div>

                            <div class="form-group">
                                <label>Version</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fa fa-fw fa-cube"></i></span>
                                    </div>
                                    <input type="text" class="form-control" name="version" placeholder="Software version">
                                </div>
                            </div>

                            <div class="form-group">
                                <label>Description</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fa fa-fw fa-angle-right"></i></span>
                                    </div>
                                    <input type="text" class="form-control" name="description" placeholder="Short description">
                                </div>
                            </div>

                            <div class="form-group">
                                <label>Type <strong class="text-danger">*</strong></label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fa fa-fw fa-tag"></i></span>
                                    </div>
                                    <select class="form-control select2"  name="type" required>
                                        <option value="">- Type -</option>
                                        <?php foreach ($software_types_array as $software_type) { ?>
                                            <option><?= $software_type; ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>

                        </div>

                        <div class="tab-pane fade" role="tabpanel" id="pills-licensing">

                            <div class="form-group">
                                <label>License Type</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fa fa-fw fa-cube"></i></span>
                                    </div>
                                    <select class="form-control select2"  name="license_type">
                                        <option value="">- Select a License Type -</option>
                                        <?php foreach ($license_types_array as $license_type) { ?>
                                            <option><?= $license_type; ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group">
                                <label>Seats</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fa fa-fw fa-chair"></i></span>
                                    </div>
                                    <input type="text" inputmode="numeric" pattern="[0-9]*" class="form-control" name="seats" placeholder="Number of seats">
                                </div>
                            </div>

                            <div class="form-group">
                                <label>License Key</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fa fa-fw fa-key"></i></span>
                                    </div>
                                    <input type="text" class="form-control" name="key" placeholder="License key">
                                </div>
                            </div>

                            <div class="form-group">
                                <label>Purchase Date</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fa fa-fw fa-calendar-check"></i></span>
                                    </div>
                                    <input type="date" class="form-control" name="purchase" max="2999-12-31">
                                </div>
                            </div>

                            <div class="form-group">
                                <label>Expire</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fa fa-fw fa-calendar-times"></i></span>
                                    </div>
                                    <input type="date" class="form-control" name="expire" max="2999-12-31">
                                </div>
                            </div>

                            <div class="form-group">
                                <label>Devices</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fa fa-fw fa-desktop"></i></span>
                                    </div>
                                    <select class="form-control select2"  name="assets[]" data-placeholder="Select licensed Assets" multiple>
                                        <?php

                                       $sql = mysqli_query($mysqli, "SELECT * FROM assets LEFT JOIN contacts ON asset_contact_id = contact_id WHERE asset_archived_at IS NULL AND asset_client_id = $client_id ORDER BY asset_name ASC");

                                        while ($row = mysqli_fetch_array($sql)) {
                                            $asset_id = intval($row['asset_id']);
                                            $asset_name = nullable_htmlentities($row['asset_name']);
                                            $asset_type = nullable_htmlentities($row['asset_type']);
                                            $contact_name = nullable_htmlentities($row['contact_name']);
                                        ?>
                                            <option value="<?= $asset_id; ?>"><?= "$asset_name - $contact_name"; ?></option>
                                        <?php } ?>

                                    </select>
                                </div>
                            </div>

                            <div class="form-group">
                                <label>Users</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fa fa-fw fa-users"></i></span>
                                    </div>
                                    <select class="form-control select2"  name="contacts[]" data-placeholder="Select licensed Users" multiple>
                                        <?php

                                       $sql = mysqli_query($mysqli, "SELECT * FROM contacts WHERE contact_archived_at IS NULL AND contact_client_id = $client_id ORDER BY contact_name ASC");

                                        while ($row = mysqli_fetch_array($sql)) {
                                            $contact_id = intval($row['contact_id']);
                                            $contact_name = nullable_htmlentities($row['contact_name']);
                                            $contact_email = nullable_htmlentities($row['contact_email']);
                                            
                                            ?>
                                            <option value="<?= $contact_id; ?>"><?= "$contact_name - $contact_email"; ?></option>
                                        <?php } ?>

                                    </select>
                                </div>
                            </div>

                        </div>

                        <div class="tab-pane fade" role="tabpanel" id="pills-notes">

                            <textarea class="form-control" rows="12" placeholder="Enter some notes" name="notes"></textarea>

                        </div>

                    </div>

                </div>
                <div class="modal-footer bg-white">
                    <button type="submit" name="add_software" class="btn btn-label-primary text-bold"></i>Create</button>
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal"></i>Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>
