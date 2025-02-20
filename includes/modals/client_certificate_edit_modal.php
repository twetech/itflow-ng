<?php require_once "/var/www/nestogy/includes/inc_all_modal.php"; ?>

<div class="modal" id="editCertificateModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content bg-dark">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fa fa-fw fa-lock mr-2"></i>Editing certificate: <span class="text-bold" id="editCertificateHeader"></span></h5>
                <button type="button" class="close text-white" data-bs-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form action="/post.php" method="post" autocomplete="off">
                <div class="modal-body bg-white">
                <input type="hidden" name="certificate_id" value="" id="editCertificateId">
                <input type="hidden" name="client_id" value="<?= $client_id; ?>">

                    <div class="form-group">
                        <label>Certificate Name <strong class="text-danger">*</strong></label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="fa fa-fw fa-lock"></i></span>
                            </div>
                            <input type="text" class="form-control" id="editCertificateName" name="name" placeholder="Certificate name" value="" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Description</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="fa fa-fw fa-angle-right"></i></span>
                            </div>
                            <input type="text" class="form-control" id="editCertificateDescription" name="description" placeholder="Short Description">
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Domain <strong class="text-danger">*</strong></label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="fa fa-fw fa-globe"></i>&nbsp;https://</span>
                            </div>
                            <input type="text" class="form-control" id="editCertificateDomain" name="domain" placeholder="Domain" value="" required>
                            <div class="input-group-append">
                                <button type="button" class="btn btn-light" onclick="fetchSSL('edit')"><i class="fas fa-fw fa-sync-alt"></i></button>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Issued By</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="fa fa-fw fa-building"></i></span>
                            </div>
                            <input type="text" class="form-control" id="editCertificateIssuedBy" name="issued_by" placeholder="Issued By" value="">
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Expire Date</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="fa fa-fw fa-calendar-times"></i></span>
                            </div>
                            <input type="date" class="form-control" id="editCertificateExpire" name="expire" max="2999-12-31" value="">
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Public Key </label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="fa fa-fw fa-key"></i></span>
                            </div>
                            <textarea class="form-control" id="editCertificatePublicKey" name="public_key"></textarea>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Notes</label>
                        <textarea class="form-control" id="editCertificateNotes" name="notes" rows="3" placeholder="Enter some notes"></textarea>
                    </div>

                    <div class="form-group">
                        <label>Domain</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="fa fa-fw fa-globe"></i></span>
                            </div>
                            <select class="form-control select2"  id="editDomainId" name="domain_id">
                            </select>
                        </div>
                    </div>

                </div>

                <div class="modal-footer bg-white">
                    <button type="submit" name="edit_certificate" class="btn btn-label-primary text-bold"></i>Save</button>
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal"></i>Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>
