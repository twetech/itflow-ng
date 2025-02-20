<?php require_once "/var/www/nestogy/includes/inc_all_modal.php"; ?>

<div class="modal" id="linkFileToDocumentModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content bg-dark">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fa fa-fw fa-paperclip mr-2"></i>Link File to <strong><?= $document_name; ?></strong></h5>
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
                                <span class="input-group-text"><i class="fa fa-fw fa-paperclip"></i></span>
                            </div>
                            <select class="form-control select2"  name="file_id">
                                <option value="">- Select a File -</option>
                                <?php
                                $sql_files_select = mysqli_query($mysqli, "SELECT * FROM files 
                                    LEFT JOIN folders ON folder_id = file_folder_id
                                    WHERE file_client_id = $client_id ORDER BY folder_name ASC, file_name ASC");
                                while ($row = mysqli_fetch_array($sql_files_select)) {
                                    $file_id = intval($row['file_id']);
                                    $file_name = nullable_htmlentities($row['file_name']);
                                    $folder_name = nullable_htmlentities($row['folder_name']);

                                    ?>
                                    <option value="<?= $file_id ?>"><?= "$folder_name/$file_name"; ?></option>
                                    <?php
                                }
                                ?>

                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-white">
                    <button type="submit" name="link_file_to_document" class="btn btn-label-primary text-bold"></i>Link</button>
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal"></i>Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>
