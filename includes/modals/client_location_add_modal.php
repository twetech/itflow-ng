<?php require_once "/var/www/nestogy/includes/inc_all_modal.php"; ?>

<?php 
$client_id = intval($_GET['client_id']);
?>


<div class="modal" id="addLocationModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content bg-dark">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fa fa-fw fa-map-marker-alt mr-2"></i>Creating location</h5>
                <button type="button" class="close text-white" data-bs-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form action="/post.php" method="post" enctype="multipart/form-data" autocomplete="off">

                <div class="modal-body bg-white">
                    <input type="hidden" name="client_id" value="<?= $client_id; ?>">

                    <ul class="nav nav-pills  mb-3">
                        <li class="nav-item">
                            <a class="nav-link active" role="tab" data-bs-toggle="tab" href="#pills-details">Details</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" role="tab" data-bs-toggle="tab" href="#pills-address">Address</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" role="tab" data-bs-toggle="tab" href="#pills-contact">Contact</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" role="tab" data-bs-toggle="tab" href="#pills-notes">Notes</a>
                        </li>
                        
                    </ul>

                    <hr>

                    <div class="tab-content">

                        <div class="tab-pane fade show active" id="pills-details">

                            <div class="form-group">
                                <label>Location Name <strong class="text-danger">*</strong> / <span class="text-secondary">Primary</span></label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fa fa-fw fa-map-marker"></i></span>
                                    </div>
                                    <input type="text" class="form-control" name="name" placeholder="Name of location" required autofocus>
                                    <div class="input-group-append">
                                        <div class="input-group-text">
                                            <input type="checkbox" name="location_primary" value="1">
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
                                    <input type="text" class="form-control" name="description" placeholder="Short Description">
                                </div>
                            </div>

                            <div class="form-group">
                                <label>Photo</label>
                                <input type="file" class="form-control-file" name="file">
                            </div>

                        </div>

                        <div class="tab-pane fade" role="tabpanel" id="pills-address">

                            <div class="form-group">
                                <label>Address</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fa fa-fw fa-map-marker-alt"></i></span>
                                    </div>
                                    <input type="text" class="form-control" name="address" placeholder="Street Address">
                                </div>
                            </div>

                            <div class="form-group">
                                <label>City</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fa fa-fw fa-city"></i></span>
                                    </div>
                                    <input type="text" class="form-control" name="city" placeholder="City">
                                </div>
                            </div>

                            <div class="form-group">
                                <label>State / Province</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fa fa-fw fa-flag"></i></span>
                                    </div>
                                    <input type="text" class="form-control" name="state" placeholder="State or Province">
                                </div>
                            </div>

                            <div class="form-group">
                                <label>Postal Code</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fab fa-fw fa-usps"></i></span>
                                    </div>
                                    <input type="text" class="form-control" name="zip" placeholder="Zip or Postal Code">
                                </div>
                            </div>

                            <div class="form-group">
                                <label>Country</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fa fa-fw fa-globe-americas"></i></span>
                                    </div>
                                    <select class="form-control select2"  name="country">
                                        <option value="">- Country -</option>
                                        <?php foreach($countries_array as $country_name) { ?>
                                            <option <?php if ($company_country == $country_name) { echo "selected"; } ?> ><?= $country_name; ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>

                        </div>

                        <div class="tab-pane fade" role="tabpanel" id="pills-contact">

                            <div class="form-group">
                                <label>Contact</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fa fa-fw fa-user"></i></span>
                                    </div>
                                    <select class="form-control select2"  name="contact">
                                        <option value="">- Contact -</option>
                                        <?php

                                        $sql_contacts = mysqli_query($mysqli, "SELECT * FROM contacts WHERE contact_archived_at IS NULL AND contact_client_id = $client_id ORDER BY contact_name ASC");
                                        while ($row = mysqli_fetch_array($sql_contacts)) {
                                            $contact_id = $row['contact_id'];
                                            $contact_name = nullable_htmlentities($row['contact_name']);
                                            ?>
                                            <option value="<?= $contact_id; ?>"><?= $contact_name; ?></option>
                                        <?php } ?>

                                    </select>
                                </div>
                            </div>

                            <div class="form-group">
                                <label>Phone</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fa fa-fw fa-phone"></i></span>
                                    </div>
                                    <input type="text" class="form-control" name="phone" placeholder="Phone Number">
                                </div>
                            </div>

                            <div class="form-group">
                                <label>Hours</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fa fa-fw fa-clock"></i></span>
                                    </div>
                                    <input type="text" class="form-control" name="hours" placeholder="Hours of operation">
                                </div>
                            </div>

                            <div class="form-group">
                                <textarea class="form-control" rows="6" name="notes" placeholder="Notes, eg Parking Info, Building Access etc"></textarea>
                            </div>

                        </div>

                        <div class="tab-pane fade" role="tabpanel" id="pills-notes">

                            <div class="form-group">
                                <textarea class="form-control" rows="12" name="notes" placeholder="Notes, eg Parking Info, Building Access etc"></textarea>
                            </div>

                        </div>

                    </div>

                </div>
                <div class="modal-footer bg-white">
                    <button type="submit" name="add_location" class="btn btn-label-primary text-bold"></i>Create</button>
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal"></i>Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>
