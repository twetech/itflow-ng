<?php require_once "/var/www/nestogy/includes/inc_all_modal.php"; ?>

<div class="modal" id="addAssetInterfaceModal<?= $asset_id; ?>" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content bg-dark">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fa fa-fw fa-ethernet"></i> <i class="fa fa-fw fa-<?= $device_icon; ?>"></i> <?= $asset_name; ?></h5>
                <button type="button" class="close text-white" data-bs-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>

            <form action="/post.php" method="post" autocomplete="off">

                <input type="hidden" name="client_id" value="<?= $client_id; ?>">
                <input type="hidden" name="asset_id" value="<?= $asset_id; ?>">

                <div class="modal-body bg-white">

                    <ul class="nav nav-pills  mb-3">
                        <li class="nav-item">
                            <a class="nav-link active" role="tab" data-bs-toggle="tab" href="#pills-interfaces<?= $asset_id; ?>">Interfaces</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" role="tab" data-bs-toggle="tab" href="#pills-create<?= $asset_id; ?>">Create</a>
                        </li>
                    </ul>

                    <hr>

                    <div class="tab-content">

                        <div class="tab-pane fade" role="tabpanel" id="pills-interfaces<?= $asset_id; ?>">


                        </div>

                        <div class="tab-pane fade" role="tabpanel" id="pills-create<?= $asset_id; ?>">

                            <div class="form-group">
                                <label>Interface Number</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fa fa-fw fa-ethernet"></i></span>
                                    </div>
                                    <input type="text" class="form-control" name="interface_number" placeholder="Port number">
                                </div>
                            </div>

                            <div class="form-group">
                                <label>Description</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fa fa-fw fa-tag"></i></span>
                                    </div>
                                    <input type="text" class="form-control" name="interface_description" placeholder="Description">
                                </div>
                            </div>

                            <div class="form-group">
                                <label>Connected Asset</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fa fa-fw fa-desktop"></i></span>
                                    </div>
                                    <input type="text" class="form-control" name="interface_connected_asset" placeholder="Connected Device">
                                </div>
                            </div>

                            <div class="form-group">
                                <label>Network</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fa fa-fw fa-network-wired"></i></span>
                                    </div>
                                    <select class="form-control select2"  name="interface_network">
                                        <option value="">- None -</option>
                                        <?php

                                        $sql_network_select = mysqli_query($mysqli, "SELECT * FROM networks WHERE network_archived_at IS NULL AND network_client_id = $client_id ORDER BY network_name ASC");
                                        while ($row = mysqli_fetch_array($sql_network_select)) {
                                            $network_id = $row['network_id'];
                                            $network_name = nullable_htmlentities($row['network_name']);
                                            $network = nullable_htmlentities($row['network']);

                                            ?>
                                            <option value="<?= $network_id; ?>"><?= $network_name; ?> - <?= $network; ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group">
                                <label>IP</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fa fa-fw fa-ethernet"></i></span>
                                    </div>
                                    <input type="text" class="form-control" name="interface_ip" placeholder="IP Address" data-inputmask="'alias': 'ip'" data-mask>
                                </div>
                            </div>

                            <div class="form-group">
                                <label>MAC Address</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fa fa-fw fa-ethernet"></i></span>
                                    </div>
                                    <input type="text" class="form-control" name="interface_mac" placeholder="MAC Address" data-inputmask="'alias': 'mac'" data-mask>
                                </div>
                            </div>

                        </div>

                    </div>
                </div>
                <div class="modal-footer bg-white">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" name="add_asset_interface" class="btn btn-label-primary"><i class="fa fa-check"></i> Create</button>
                </div>
            </form>
        </div>
    </div>
</div>
