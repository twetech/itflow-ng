<?php require_once "/var/www/nestogy/includes/inc_all_modal.php"; ?>

<?php
$inventory_location_id = intval($_GET['inventory_location_id']);
$sql_inventory_location = mysqli_query($mysqli, "SELECT inventory_locations.*, users.user_name FROM inventory_locations
LEFT JOIN users ON inventory_locations.inventory_location_user_id = users.user_id
WHERE inventory_location_id = $inventory_location_id");
$row = mysqli_fetch_array($sql_inventory_location);
$inventory_location_name = nullable_htmlentities($row['inventory_location_name']);
$inventory_location_description = nullable_htmlentities($row['inventory_location_description']);
$inventory_location_address = nullable_htmlentities($row['inventory_location_address']);
$inventory_location_city = nullable_htmlentities($row['inventory_location_city']);
$inventory_location_state = nullable_htmlentities($row['inventory_location_state']);
$inventory_location_zip = nullable_htmlentities($row['inventory_location_zip']);
$inventory_location_country = nullable_htmlentities($row['inventory_location_country']);
$inventory_location_user_id = intval($row['inventory_location_user_id']);
$inventory_location_user_name = nullable_htmlentities($row['user_name']);
?>


<div class="modal" id="editLocationModal<?= $inventory_location_id; ?>" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content bg-dark">
            <div class="modal-header text-white">
                <h5 class="modal-title"><i class="fas fa-fw fa-map-marker-alt mr-2"></i>Edit Location</h5>
                <button type="button" class="close text-white" data-bs-dismiss="modal">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="/post.php" method="post" autocomplete="off">

                <div class="modal-body bg-white">
                    <div class="form-group">
                        <label>Name <strong class="text-danger">*</strong></label>
                        <input type="text" class="form-control" name="name" value="<?= $inventory_location_name; ?>" placeholder="Location name" required autofocus>
                    </div>
                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                <input type="hidden" name="inventory_location_id" value="<?= $inventory_location_id; ?>">

                    <div class="form-group">
                        <label>Description <strong class="text-danger">*</strong></label>
                        <input type="text" class="form-control" name="description" value="<?= $inventory_location_description; ?>" placeholder="Description" required>
                    </div>

                    <div class="form-group">
                        <label>Address</label>
                        <input type="text" class="form-control" name="address" value="<?= $inventory_location_address; ?>" placeholder="Address (Optional)">
                    </div>

                    <div class="form-group">
                        <label>City</label>
                        <input type="text" class="form-control" name="city" value="<?= $inventory_location_city; ?>" placeholder="City (Optional)">
                    </div>

                    <div class="form-group">
                        <label>State</label>
                        <input type="text" class="form-control" name="state" value="<?= $inventory_location_state; ?>" placeholder="State (Optional)">
                    </div>

                    <div class="form-group">
                        <label>Zip</label>
                        <input type="text" class="form-control" name="zip" value="<?= $inventory_location_zip; ?>" placeholder="Zip (Optional)">
                    </div>

                    <div class="form-group">
                        <label>Country</label>
                        <input type="text" class="form-control" name="country" value="<?= $inventory_location_country; ?>" placeholder="Country (Optional)">
                    </div>

                    <div class="form-group">
                        <label>User Assigned<strong class="text-danger">*</strong></label>
                        <select class="form-control select2"  name="user_id" required>
                            <option value="" selected disabled>Select a user</option>
                            <?php
                            $users = mysqli_query($mysqli, "SELECT users.* FROM users
							LEFT JOIN inventory_locations ON users.user_id = inventory_locations.inventory_location_user_id
							WHERE user_status = 1 AND user_archived_at IS NULL AND inventory_locations.inventory_location_user_id IS NULL AND users.user_id != '$inventory_location_user_id'");
                            while ($user = mysqli_fetch_array($users)) {
                                $user_name = nullable_htmlentities($user['user_name']);
                                $user_id = intval($user['user_id']);
                                echo "<option value=\"$user[user_id]\">$user[user_name]</option>";
                            }
                            // Add the selected attribute to the user that is currently assigned to the location
                            echo "<option value=\"$inventory_location_user_id\" selected disabled>$inventory_location_user_name</option>";
                            ?>
                        </select>
                    </div>
                </div>
                <div class="modal-footer bg-white">
                    <button type="submit" name="edit_inventory_locations" class="btn btn-label-primary text-bold"><i class="fa fa-check mr- 2"></i>Create</button>
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal"><i class="fas fa-times mr-2"></i>Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>