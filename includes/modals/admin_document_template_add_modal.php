<?php require_once "/var/www/nestogy/includes/inc_all_modal.php"; ?>

<div class="modal" id="addDocumentTemplateModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content bg-dark">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fa fa-fw fa-file-alt mr-2"></i>Creating Document Template</h5>
                <button type="button" class="close text-white" data-bs-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form action="/post.php" method="post" autocomplete="off">
                <div class="modal-body bg-white">

                    <div class="form-group">
                        <input type="text" class="form-control" name="name" placeholder="Template name">
                    </div>

                    <div class="form-group">
                        <textarea  class="form-control" name="content"></textarea>
                    </div>
                    <div class="form-group">
                        <input type="text" class="form-control" name="description" placeholder="Enter a short summary">
                    </div>
                
                </div>

                <div class="modal-footer bg-white">

                    <button type="submit" name="add_document_template" class="btn btn-label-primary text-bold"></i>Create</button>
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal"></i>Cancel</button>

                </div>
            </form>
        </div>
    </div>
</div>
