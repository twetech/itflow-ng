<?php require_once "/var/www/nestogy/includes/inc_all_modal.php"; ?>

<?php 

$client_id = intval($_GET['client_id']);

?>

<div class="modal" id="addLoginModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content bg-dark">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fa fa-fw fa-key mr-2"></i>New Login</h5>
                <button type="button" class="close text-white" data-bs-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form action="/post.php" method="post" autocomplete="off">

                <div class="modal-body">
                <input type="hidden" name="client_id" value="<?= $client_id; ?>">
                    <ul class="nav nav-pills  mb-3">
                        <li class="nav-item">
                            <a class="nav-link active" role="tab" data-bs-toggle="tab" href="#pills-details">Details</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" role="tab" data-bs-toggle="tab" href="#pills-relation">Relation</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" role="tab" data-bs-toggle="tab" href="#pills-notes">Notes</a>
                        </li>
                    </ul>

                    <hr>

                    <div class="tab-content">

                        <div class="tab-pane fade show active" id="pills-details">

                            <div class="form-group">
                                <label>Name <strong class="text-danger">*</strong> / <span class="text-secondary">Important?</span></label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fa fa-fw fa-key"></i></span>
                                    </div>
                                    <input type="text" class="form-control" name="name" placeholder="Name of Login" required autofocus>
                                    <div class="input-group-append">
                                        <div class="input-group-text">
                                            <input type="checkbox" name="important" value="1">
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
                                    <input type="text" class="form-control" name="description" placeholder="Description">
                                </div>
                            </div>

                            <div class="form-group">
                                <label>Username</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fa fa-fw fa-user"></i></span>
                                    </div>
                                    <input type="text" class="form-control" name="username" placeholder="Username">
                                </div>
                            </div>

                            <div class="form-group">
                                <label>Password <strong class="text-danger">*</strong></label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fa fa-fw fa-lock"></i></span>
                                    </div>
                                    <input type="password" class="form-control" data-bs-toggle="password" id="password" name="password" placeholder="Password" required autocomplete="new-password">
                                    <div class="input-group-append">
                                        <span class="input-group-text"><i class="fa fa-fw fa-eye"></i></span>
                                    </div>
                                    <div class="input-group-append">
                                        <span class="btn btn-default"><i class="fa fa-fw fa-question" onclick="generatePassword()"></i></span>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label>OTP</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fa fa-fw fa-key"></i></span>
                                    </div>
                                    <input type="password" class="form-control" data-bs-toggle="password" name="otp_secret" placeholder="Insert secret key">
                                    <div class="input-group-append">
                                        <span class="input-group-text"><i class="fa fa-fw fa-eye"></i></span>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label>URI</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fa fa-fw fa-link"></i></span>
                                    </div>
                                    <input type="text" class="form-control" name="uri" placeholder=" http://192.168.1.1">
                                </div>
                            </div>

                            <div class="form-group">
                                <label>URI 2</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fa fa-fw fa-link"></i></span>
                                    </div>
                                    <input type="text" class="form-control" name="uri_2" placeholder="https://server.company.com:5001">
                                </div>
                            </div>

                        </div>

                        <div class="tab-pane fade" role="tabpanel" id="pills-relation">

                            <div class="form-group">
                                <label>Contact</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fa fa-fw fa-user"></i></span>
                                    </div>
                                    <select class="form-control select2"  name="contact">
                                        <option value="">- Contact -</option>
                                        <?php

                                        $sql = mysqli_query($mysqli, "SELECT * FROM contacts WHERE contact_client_id = $client_id ORDER BY contact_name ASC");
                                        while ($row = mysqli_fetch_array($sql)) {
                                            $contact_id = intval($row['contact_id']);
                                            $contact_name = nullable_htmlentities($row['contact_name']);
                                            ?>
                                            <option value="<?= $contact_id; ?>"><?= $contact_name; ?></option>

                                            <?php
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group">
                                <label>Vendor</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fa fa-fw fa-building"></i></span>
                                    </div>
                                    <select class="form-control select2"  name="vendor">
                                        <option value="">- Vendor -</option>
                                        <?php

                                        $sql_vendors = mysqli_query($mysqli, "SELECT * FROM vendors WHERE vendor_client_id = $client_id ORDER BY vendor_name ASC");
                                        while ($row = mysqli_fetch_array($sql_vendors)) {
                                            $vendor_id = intval($row['vendor_id']);
                                            $vendor_name = nullable_htmlentities($row['vendor_name']);
                                            ?>
                                            <option value="<?= $vendor_id; ?>"><?= $vendor_name; ?></option>

                                            <?php
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group">
                                <label>Asset</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fa fa-fw fa-tag"></i></span>
                                    </div>
                                    <select class="form-control select2"  name="asset">
                                        <option value="">- Asset -</option>
                                        <?php

                                        $sql = mysqli_query($mysqli, "SELECT * FROM assets LEFT JOIN locations on asset_location_id = location_id WHERE asset_client_id = $client_id AND asset_archived_at IS NULL ORDER BY asset_name ASC");
                                        while ($row = mysqli_fetch_array($sql)) {
                                            $asset_id = intval($row['asset_id']);
                                            $asset_name = nullable_htmlentities($row['asset_name']);
                                            $asset_location = nullable_htmlentities($row['location_name']);

                                            $asset_display_string = $asset_name;
                                            if (!empty($asset_location)) {
                                                $asset_display_string = "$asset_name ($asset_location)";
                                            }

                                            ?>
                                            <option value="<?= $asset_id; ?>"><?= $asset_display_string; ?></option>

                                            <?php
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group">
                                <label>Software</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fa fa-fw fa-box"></i></span>
                                    </div>
                                    <select class="form-control select2"  name="software">
                                        <option value="">- Software -</option>
                                        <?php

                                        $sql = mysqli_query($mysqli, "SELECT * FROM software WHERE software_client_id = $client_id ORDER BY software_name ASC");
                                        while ($row = mysqli_fetch_array($sql)) {
                                            $software_id = intval($row['software_id']);
                                            $software_name = nullable_htmlentities($row['software_name']);
                                            ?>
                                            <option value="<?= $software_id; ?>"><?= $software_name; ?></option>

                                            <?php
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>

                        </div>

                        <div class="tab-pane fade" role="tabpanel" id="pills-notes">

                            <div class="form-group">
                                <textarea class="form-control" rows="12" placeholder="Enter some notes" name="note"></textarea>
                            </div>

                        </div>

                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" name="add_login" class="btn btn-label-primary">Create</button>
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>
