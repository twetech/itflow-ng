<?php require_once "/var/www/nestogy/includes/inc_all_modal.php"; ?>
<div class="modal" id="addSoftwareFromTemplateModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content bg-dark">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fa fa-fw fa-cube mr-2"></i>New License from Template</h5>
                <button type="button" class="close text-white" data-bs-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form action="/post.php" method="post" autocomplete="off">
                <div class="modal-body bg-white">
                <input type="hidden" name="client_id" value="<?= $client_id; ?>">

                    <label>Template</label>
                    <div class="form-group">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="fa fa-fw fa-puzzle-piece"></i></span>
                            </div>
                            <select class="form-control" name="software_template_id" required>
                                <option value="">- Select Template -</option>
                                <?php
                                $sql_software_templates = mysqli_query($mysqli, "SELECT * FROM software WHERE software_template = 1 AND software_archived_at IS NULL ORDER BY software_name ASC");
                                while ($row = mysqli_fetch_array($sql_software_templates)) {
                                    $software_template_id = intval($row['software_id']);
                                    $software_template_name = nullable_htmlentities($row['software_name']);

                                    ?>
                                    <option value="<?= $software_template_id ?>"><?= $software_template_name; ?></option>
                                <?php } ?>

                            </select>
                        </div>
                    </div>

                </div>

                <div class="modal-footer bg-white">
                    <button type="submit" name="add_software_from_template" class="btn btn-label-primary text-bold"></i>Create</button>
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal"></i>Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>
