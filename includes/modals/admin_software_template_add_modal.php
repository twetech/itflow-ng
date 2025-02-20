<?php require_once "/var/www/nestogy/includes/inc_all_modal.php"; ?>

<div class="modal" id="addSoftwareTemplateModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content bg-dark">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fa fa-fw fa-cube mr-2"></i>New License Template</h5>
                <button type="button" class="close text-white" data-bs-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form action="/post.php" method="post" autocomplete="off">
                <div class="modal-body bg-white">

                    <div class="form-group">
                        <label>Template Name <strong class="text-danger">*</strong></label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="fa fa-fw fa-cube"></i></span>
                            </div>
                            <input type="text" class="form-control" name="name" placeholder="Software name" required autofocus>
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
                                <?php foreach($software_types_array as $software_type) { ?>
                                    <option><?= $software_type; ?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>License Type</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="fa fa-fw fa-cube"></i></span>
                            </div>
                            <select class="form-control select2"  name="license_type">
                                <option value="">- Select a License Type -</option>
                                <?php foreach($license_types_array as $license_type) { ?>
                                    <option><?= $license_type; ?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>

                    <textarea class="form-control" rows="8" placeholder="Enter some notes" name="notes"></textarea>

                </div>
                <div class="modal-footer bg-white">
                    <button type="submit" name="add_software_template" class="btn btn-label-primary text-bold"></i>Create</button>
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal"></i>Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>
