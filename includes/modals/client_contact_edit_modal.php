<?php require_once "/var/www/nestogy/includes/inc_all_modal.php"; ?>

<?php
$contact_id = intval($_GET['contact_id']);

$sql_contact = mysqli_query($mysqli,
    "SELECT * FROM contacts
    LEFT JOIN clients ON contacts.contact_client_id = clients.client_id
    WHERE contact_id = $contact_id");
$row = mysqli_fetch_array($sql_contact);

$contact_name = nullable_htmlentities($row['contact_name']);
$contact_title = nullable_htmlentities($row['contact_title']);
$contact_department = nullable_htmlentities($row['contact_department']);
$contact_phone = nullable_htmlentities($row['contact_phone']);
$contact_extension = nullable_htmlentities($row['contact_extension']);
$contact_mobile = nullable_htmlentities($row['contact_mobile']);
$contact_email = nullable_htmlentities($row['contact_email']);
$contact_location_id = intval($row['contact_location_id']);
$contact_primary = intval($row['contact_primary']);
$contact_important = intval($row['contact_important']);
$contact_billing = intval($row['contact_billing']);
$contact_technical = intval($row['contact_technical']);
$contact_pin = nullable_htmlentities($row['contact_pin']);
$contact_photo = nullable_htmlentities($row['contact_photo']);
$contact_notes = nullable_htmlentities($row['contact_notes']);
$auth_method = nullable_htmlentities($row['contact_auth_method']);
$client_id = intval($row['client_id']);

$contact_initials = initials($contact_name);

?>

<div class="modal" id="editContactModal<?= $contact_id; ?>" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content bg-dark">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fa fa-fw fa-user-edit mr-2"></i>Editing: <strong><?= $contact_name; ?></strong></h5>
                <button type="button" class="close text-white" data-bs-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form action="/post.php" method="post" enctype="multipart/form-data" autocomplete="off">

                <div class="modal-body bg-white">

                    <!-- Prevent undefined checkbox errors on submit -->
                    <input type="hidden" name="contact_primary" value="0">
                    <input type="hidden" name="contact_important" value="0">
                    <input type="hidden" name="contact_billing" value="0">
                    <input type="hidden" name="contact_technical" value="0">
                    <input type="hidden" name="send_email" value="0">
                    <input type="hidden" name="contact_id" value="<?= $contact_id; ?>">
                    <input type="hidden" name="client_id" value="<?= $client_id; ?>">

                    <div class="nav-align-top">
                        <ul class="nav nav-pills  mb-3">
                            <li class="nav-item">
                                <a class="nav-link active" role="tab" data-bs-toggle="tab" href="#pills-details"><i class="fa fa-fw fa-user mr-2"></i>Details</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" role="tab" data-bs-toggle="tab" href="#pills-photo"><i class="fa fa-fw fa-image mr-2"></i>Photo</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" role="tab" data-bs-toggle="tab" href="#pills-secure"><i class="fa fa-fw fa-lock mr-2"></i>Secure</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" role="tab" data-bs-toggle="tab" href="#pills-notes"><i class="fa fa-fw fa-edit mr-2"></i>Notes</a>
                            </li>
                        </ul>

                        <hr>

                        <div class="tab-content">

                            <div class="tab-pane fade show active" id="pills-details" role="tabpanel">

                                <div class="form-group">
                                    <label>Name <strong class="text-danger">*</strong> / <span class="text-secondary">Primary Contact</span></label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fa fa-fw fa-user"></i></span>
                                        </div>
                                        <input type="text" class="form-control" name="name" placeholder="Full Name" value="<?= $contact_name; ?>" required>
                                        <div class="input-group-append">
                                            <div class="input-group-text">
                                                <input type="checkbox" name="contact_primary" value="1" <?php if ($contact_primary == 1) { echo "checked"; } ?>>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label>Title</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fa fa-fw fa-id-badge"></i></span>
                                        </div>
                                        <input type="text" class="form-control" name="title" placeholder="Title" value="<?= $contact_title; ?>">
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label>Department / Group</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fa fa-fw fa-users"></i></span>
                                        </div>
                                        <input type="text" class="form-control" name="department" placeholder="Department or group" value="<?= $contact_department; ?>">
                                    </div>
                                </div>

                                <div class="form-row">
                                    <label>Phone</label>
                                    <div class="col-8">
                                        <div class="form-group">
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text"><i class="fa fa-fw fa-phone"></i></span>
                                                </div>
                                                <input type="text" class="form-control" name="phone" placeholder="Phone Number" value="<?= $contact_phone; ?>">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <input type="text" class="form-control" name="extension" placeholder="Extension" value="<?= $contact_extension; ?>">
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label>Mobile</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fa fa-fw fa-mobile-alt"></i></span>
                                        </div>
                                        <input type="text" class="form-control" name="mobile" placeholder="Mobile Phone Number" value="<?= $contact_mobile; ?>">
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label>Email</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fa fa-fw fa-envelope"></i></span>
                                        </div>
                                        <input type="email" class="form-control" name="email" placeholder="Email Address" value="<?= $contact_email; ?>">
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label>Location</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fa fa-fw fa-map-marker-alt"></i></span>
                                        </div>
                                        <select class="form-control select2"  name="location">
                                            <option value="">- Location -</option>
                                            <?php

                                            $sql_locations = mysqli_query($mysqli, "SELECT * FROM locations WHERE location_id = $contact_location_id OR location_archived_at IS NULL AND location_client_id = $client_id ORDER BY location_name ASC");
                                            while ($row = mysqli_fetch_array($sql_locations)) {
                                                $location_id_select = intval($row['location_id']);
                                                $location_name_select = nullable_htmlentities($row['location_name']);
                                                $location_archived_at = nullable_htmlentities($row['location_archived_at']);
                                                if ($location_archived_at) {
                                                    $location_name_select_display = "($location_name_select) - ARCHIVED";
                                                } else {
                                                    $location_name_select_display = $location_name_select;
                                                }
                                            ?>
                                                <option <?php if ($contact_location_id == $location_id_select) {
                                                            echo "selected";
                                                        } ?> value="<?= $location_id_select; ?>"><?= $location_name_select_display; ?></option>
                                            <?php } ?>

                                        </select>
                                    </div>
                                </div>

                                <div class="form-row">

                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <div class="custom-control custom-checkbox">
                                                <input type="checkbox" class="custom-control-input" id="contactImportantCheckbox<?= $contact_id; ?>" name="contact_important" value="1" <?php if ($contact_important == 1) { echo "checked"; } ?>>
                                                <label class="custom-control-label" for="contactImportantCheckbox<?= $contact_id; ?>">Important</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <div class="custom-control custom-checkbox">
                                                <input type="checkbox" class="custom-control-input" id="contactBillingCheckbox<?= $contact_id; ?>" name="contact_billing" value="1" <?php if ($contact_billing == 1) { echo "checked"; } ?>>
                                                <label class="custom-control-label" for="contactBillingCheckbox<?= $contact_id; ?>">Billing</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <div class="custom-control custom-checkbox">
                                                <input type="checkbox" class="custom-control-input" id="contactTechnicalCheckbox<?= $contact_id; ?>" name="contact_technical" value="1" <?php if ($contact_technical == 1) { echo "checked"; } ?>>
                                                <label class="custom-control-label" for="contactTechnicalCheckbox<?= $contact_id; ?>">Technical</label>
                                            </div>
                                        </div>
                                    </div>

                                </div>

                            </div>

                            <div class="tab-pane fade" role="tabpanel" id="pills-secure" role="tabpanel">

                                <div class="form-group">
                                    <label>Pin</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fa fa-fw fa-key"></i></span>
                                        </div>
                                        <input type="text" class="form-control" name="pin" placeholder="Security code or pin" value="<?= $contact_pin; ?>">
                                    </div>
                                </div>

                                <?php if ($config_client_portal_enable == 1) { ?>
                                    <div class="authForm">
                                        <div class="form-group">
                                            <label>Login</label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text"><i class="fa fa-fw fa-user-circle"></i></span>
                                                </div>
                                                <select class="form-control select2 authMethod" name="auth_method">
                                                    <option value="">- None -</option>
                                                    <option value="local" <?php if ($auth_method == "local") { echo "selected"; } ?>>Local</option>
                                                    <option value="azure" <?php if ($auth_method == "azure") { echo "selected"; } ?>>Azure</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group passwordGroup" style="display: none;">
                                            <label>Password <strong class="text-danger">*</strong></label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text"><i class="fa fa-fw fa-lock"></i></span>
                                                </div>
                                                <input type="password" class="form-control" data-bs-toggle="password" id="password-edit-<?= $contact_id; ?>" name="contact_password" placeholder="Password" autocomplete="new-password">
                                                <div class="input-group-append">
                                                    <span class="input-group-text"><i class="fa fa-fw fa-eye"></i></span>
                                                </div>
                                                <div class="input-group-append">
                                                    <button type="button" class="btn btn-default" onclick="generatePassword('edit', <?= $contact_id; ?>)">
                                                        <i class="fa fa-fw fa-question"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input" name="send_email" value="1" />
                                        <label class="form-check-label">Send user e-mail with login details?</label>
                                    </div>

                                <?php } ?>

                            </div>

                            <div class="tab-pane fade" role="tabpanel" id="pills-photo" role="tabpanel">

                                <div class="mb-3 text-center">
                                    <?php if (!empty($contact_photo)) { ?>
                                        <img class="img-fluid" alt="contact_photo" src="<?= "/uploads/clients/$client_id/$contact_photo"; ?>">
                                    <?php } else { ?>
                                        <span class="fa-stack fa-4x">
                                            <i class="fa fa-circle fa-stack-2x text-secondary"></i>
                                            <span class="fa fa-stack-1x text-white"><?= $contact_initials; ?></span>
                                        </span>
                                    <?php } ?>
                                </div>

                                <div class="form-group">
                                    <input type="file" class="form-control-file" name="file">
                                </div>

                            </div>

                            <div class="tab-pane fade" role="tabpanel" id="pills-notes" role="tabpanel">

                                <div class="form-group">
                                    <textarea class="form-control" rows="8" name="notes" placeholder="Notes, eg Personal tidbits to spark convo, temperment, etc"><?= $contact_notes; ?></textarea>
                                </div>

                            </div>

                        </div>                        
                    </div>


                </div>

                <div class="modal-footer bg-white">
                    <button type="submit" name="edit_contact" class="btn btn-label-primary text-bold"><i class="fas fa-check mr-2"></i>Save</button>
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal"></i>Cancel</button>

                    <script>
                        function generatePassword(type, id) {
                            // Send a GET request to ajax.php as ajax.php?get_readable_pass=true
                            jQuery.get(
                                "/ajax/ajax.php", {
                                    get_readable_pass: 'true'
                                },
                                function(data) {
                                    //If we get a response from post.php, parse it as JSON
                                    const password = JSON.parse(data);

                                    // Set the password value to the correct modal, based on the type
                                    if (type == "add") {
                                        document.getElementById("password-add").value = password;
                                    } else if (type == "edit") {
                                        document.getElementById("password-edit-"+id.toString()).value = password;
                                    }
                                }
                            );
                        }

                        $(document).ready(function() {
                            $('.authMethod').on('change', function() {
                                var $form = $(this).closest('.authForm');
                                if ($(this).val() === 'local') {
                                    $form.find('.passwordGroup').show();
                                } else {
                                    $form.find('.passwordGroup').hide();
                                }
                            });
                            $('.authMethod').trigger('change');

                        });
                    </script>
                </div>
        </div>
    </div>
</div>