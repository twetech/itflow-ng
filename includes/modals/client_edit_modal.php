<?php require_once "/var/www/nestogy/includes/inc_all_modal.php";
$client_id = $_GET['client_id'];

$sql = mysqli_query($mysqli, "SELECT * FROM clients LEFT JOIN client_tags ON client_id = client_tag_client_id WHERE client_id = $client_id");
$row = mysqli_fetch_array($sql);

$client_id = intval($row['client_id']);
$client_name = nullable_htmlentities($row['client_name']);
$client_type = nullable_htmlentities($row['client_type']);
$client_website = nullable_htmlentities($row['client_website']);
$client_is_lead = intval($row['client_is_lead']);
$client_referral = nullable_htmlentities($row['client_referral']);
$client_rate = floatval($row['client_rate']);
$client_currency_code = nullable_htmlentities($row['client_currency_code']);
$client_net_terms = intval($row['client_net_terms']);
$client_tax_id_number = nullable_htmlentities($row['client_tax_id_number']);
$client_notes = nullable_htmlentities($row['client_notes']);
$client_tag_id_array = explode(',', $row['client_tag_id']);
$client_created_at = $row['client_created_at'];


?>

<div class="modal" id="editClientModal<?= $client_id; ?>" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content bg-dark">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fa fa-fw fa-user-edit mr-2"></i>Editing: <strong>
                        <?= $client_name; ?>
                    </strong></h5>
                <button type="button" class="close text-white" data-bs-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form action="/post.php" method="post" autocomplete="off">

                <div class="modal-body bg-white">
                <input type="hidden" name="client_id" value="<?= $client_id; ?>">
                <input type="hidden" name="lead" value="0">
                <input type="hidden" name="currency_code" value="<?php if (empty($currency_code)) {
                    echo $company_currency;
                } else {
                    echo $currency_code;
                } ?>">
                <input type="hidden" name="net_terms" value="<?= $client_net_terms; ?>">
                    <ul class="nav nav-pills  mb-3">
                        <li class="nav-item">
                            <a class="nav-link active" role="tab" data-bs-toggle="tab" href="#pills-client-details<?= $client_id; ?>">Details</a>
                        </li>
                        <?php if ($config_module_enable_accounting) { ?>
                        <li class="nav-item">
                            <a class="nav-link" role="tab" data-bs-toggle="tab" href="#pills-client-billing<?= $client_id; ?>">Billing</a>
                        </li>
                        <?php } ?>
                        <li class="nav-item">
                            <a class="nav-link" role="tab" data-bs-toggle="tab" href="#pills-client-more<?= $client_id; ?>">More</a>
                        </li>
                    </ul>

                    <hr>

                    <div class="tab-content">

                        <div class="tab-pane fade show active" id="pills-client-details<?= $client_id; ?>">

                            <div class="form-group">
                                <label>Name <strong class="text-danger">*</strong></label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fa fa-fw fa-user"></i></span>
                                    </div>
                                    <input type="text" class="form-control" name="name" placeholder="Name or Company"
                                        value="<?= $client_name; ?>" required>
                                </div>
                            </div>

                            <div class="form-group">
                                <label>Industry</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fa fa-fw fa-briefcase"></i></span>
                                    </div>
                                    <input type="text" class="form-control" name="type" placeholder="Industry"
                                        value="<?= $client_type; ?>">
                                </div>
                            </div>

                            <div class="form-group">
                                <label>Referral</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fa fa-fw fa-smile-wink"></i></span>
                                    </div>
                                    <select class="form-control select2"  data-tags="true" name="referral">
                                        <option value="">N/A</option>
                                        <?php

                                        $referral_sql = mysqli_query($mysqli, "SELECT * FROM categories WHERE category_type = 'Referral' AND (category_archived_at > '$client_created_at' OR category_archived_at IS NULL) ORDER BY category_name ASC");
                                        while ($row = mysqli_fetch_array($referral_sql)) {
                                            $referral = nullable_htmlentities($row['category_name']);
                                            ?>
                                            <option <?php if ($client_referral == $referral) {
                                                echo "selected";
                                            } ?>>
                                                <?= $referral; ?>
                                            </option>

                                            <?php
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group">
                                <label>Website</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fa fa-fw fa-globe"></i></span>
                                    </div>
                                    <input type="text" class="form-control" name="website" placeholder="ex. google.com"
                                        value="<?= $client_website; ?>">
                                </div>
                            </div>

                            <div class="form-group">
                                <label>Is Lead <strong class="text-danger">*</strong></label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <input type="checkbox" name="lead" value="1"<?php if ($client_is_lead == 1) {
                                            echo "checked";
                                        } ?>>
                                    </div>
                                </div>
                            </div>

                        </div>

                        <?php if ($config_module_enable_accounting) { ?>

                        <div class="tab-pane fade" role="tabpanel" id="pills-client-billing<?= $client_id; ?>">     

                            <div class="form-group">
                                <label>Hourly Rate</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fa fa-fw fa-clock"></i></span>
                                    </div>
                                    <input type="text" class="form-control" inputmode="numeric"
                                        pattern="[0-9]*\.?[0-9]{0,2}" name="rate" placeholder="0.00"
                                        value="<?= number_format($client_rate, 2, '.', ''); ?>">
                                </div>
                            </div>

                            <div class="form-group">
                                <label>Currency <strong class="text-danger">*</strong></label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fa fa-fw fa-money-bill"></i></span>
                                    </div>
                                    <select class="form-control select2"  name="currency_code" required>
                                        <option value="">- Currency -</option>
                                        <?php foreach ($currencies_array as $currency_code => $currency_name) { ?>
                                            <option <?php if ($client_currency_code == $currency_code) {
                                                echo "selected";
                                            } ?> value="<?= $currency_code; ?>">
                                                <?= "$currency_code - $currency_name"; ?>
                                            </option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group">
                                <label>Invoice Net Terms</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fa fa-fw fa-calendar"></i></span>
                                    </div>
                                    <select class="form-control select2"  name="net_terms">
                                        <option value="">- Net Terms -</option>
                                        <?php foreach ($net_terms_array as $net_term_value => $net_term_name) { ?>
                                            <option <?php if ($net_term_value == $client_net_terms) {
                                                echo "selected";
                                            } ?> value="<?= $net_term_value; ?>">
                                                <?= $net_term_name; ?>
                                            </option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group">
                                <label>Tax ID</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fa fa-fw fa-balance-scale"></i></span>
                                    </div>
                                    <input type="text" class="form-control" name="tax_id_number"
                                        placeholder="Tax ID Number" value="<?= $client_tax_id_number; ?>">
                                </div>
                            </div>

                        </div>

                        <?php } ?>

                        <div class="tab-pane fade" role="tabpanel" id="pills-client-more<?= $client_id; ?>">

                            <div class="form-group">
                                <textarea class="form-control" rows="8" placeholder="Enter some notes"
                                    name="notes"><?= $client_notes; ?></textarea>
                            </div>

                            <div class="form-group">
                                <label>Tags</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fa fa-fw fa-tags"></i></span>
                                    </div>
                                    <pre>
                                    <?php print_r($client_tag_id_array); ?>
                                    </pre>
                                    <select class="form-control" name="tags[]" data-placeholder="Add some tags" multiple>
                                        <?php
                                        $sql_tags_select = mysqli_query($mysqli, "SELECT * FROM tags WHERE tag_type = 1 ORDER BY tag_name ASC");
                                        while ($row = mysqli_fetch_array($sql_tags_select)) {
                                            $tag_name = nullable_htmlentities($row['tag_name']);
                                            $tag_id = intval($row['tag_id']);
                                            ?>
                                            <option value="<?= $tag_id; ?>" <?php if (in_array($tag_id, $client_tag_id_array)) { echo "selected"; } ?>><?= $tag_name; ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>

                        </div>

                    </div>
                </div>
                <div class="modal-footer bg-white">
                    <button type="submit" name="edit_client" class="btn btn-label-primary text-bold"></i>Save</button>
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal"></i>Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>