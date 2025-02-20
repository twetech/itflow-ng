<?php require_once "/var/www/nestogy/includes/inc_all_modal.php"; ?>

<div class="modal" id="linkAssetToDocumentModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content bg-dark">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fa fa-fw fa-desktop mr-2"></i>Link Asset to <strong><?= $document_name; ?></strong></h5>
                <button type="button" class="close text-white" data-bs-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form action="/post.php" method="post" autocomplete="off">

                <div class="modal-body bg-white">
                <input type="hidden" name="client_id" value="<?= $client_id; ?>">
                <input type="hidden" name="document_id" value="<?= $document_id; ?>">
                    <div class="form-group">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="fa fa-fw fa-desktop"></i></span>
                            </div>
                            <select class="form-control select2"  name="asset_id">
                                <option value="">- Select an Asset -</option>
                                <?php
                                // Check if there are any associated vendors
                                if (!empty($linked_assets)) {
                                    $excluded_asset_ids = implode(",", $linked_assets);
                                    $exclude_condition = "AND asset_id NOT IN ($excluded_asset_ids)";
                                } else {
                                    $exclude_condition = "";  // No condition if there are no displayed vendors
                                }

                                $sql_assets_select = mysqli_query($mysqli, "SELECT * FROM assets
                                    WHERE asset_client_id = $client_id 
                                    AND asset_archived_at IS NULL
                                    $exclude_condition
                                    ORDER BY asset_name ASC"
                                );
                                while ($row = mysqli_fetch_array($sql_assets_select)) {
                                    $asset_id = intval($row['asset_id']);
                                    $asset_name = nullable_htmlentities($row['asset_name']);

                                    ?>
                                    <option value="<?= $asset_id ?>"><?= $asset_name; ?></option>
                                    <?php
                                }
                                ?>

                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-white">
                    <button type="submit" name="link_asset_to_document" class="btn btn-label-primary text-bold"></i>Link</button>
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal"></i>Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>
