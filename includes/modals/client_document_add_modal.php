<?php require_once "/var/www/nestogy/includes/inc_all_modal.php"; ?>

<?php
$client_id = intval($_GET['client_id']);
?>

<div class="modal" id="addDocumentModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content bg-dark">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fa fa-fw fa-file-alt mr-2"></i>New Document</h5>
                <button type="button" class="close text-white" data-bs-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form action="/post.php" method="post" autocomplete="off">
                <div class="modal-body bg-white">
                <input type="hidden" name="client_id" value="<?= $client_id; ?>">

                    <div class="form-group">
                        <input type="text" class="form-control" name="name" placeholder="Name" required autofocus>
                    </div>

                    <?php if($config_ai_enable) { ?>
                    <div class="form-group">
                        <textarea class="form-control tinymceai" id="textInput" name="content"></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <button id="rewordButton" class="btn btn-label-primary" type="button"><i class="fas fa-fw fa-robot mr-2"></i>Reword</button>
                        <button id="undoButton" class="btn btn-light" type="button" style="display:none;"><i class="fas fa-fw fa-redo-alt mr-2"></i>Undo</button>
                    </div>
                    <?php } else { ?>
                    <div class="form-group">
                        <textarea  class="form-control" name="content"></textarea>
                    </div>
                    <?php } ?>
                    <div class="form-group">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="fa fa-fw fa-folder"></i></span>
                            </div>
                            <select class="form-control" name="folder">
                                <option value="0">/</option>
                                <?php
                                $sql_folders = mysqli_query($mysqli, "SELECT * FROM folders WHERE folder_location = $folder_location AND folder_client_id = $client_id ORDER BY folder_name ASC");
                                while ($row = mysqli_fetch_array($sql_folders)) {
                                    $folder_id = intval($row['folder_id']);
                                    $folder_name = nullable_htmlentities($row['folder_name']);

                                    ?>
                                    <option <?php if (isset($_GET['folder_id']) && $_GET['folder_id'] == $folder_id) echo "selected"; ?> value="<?= $folder_id ?>"><?= $folder_name; ?></option>
                                    <?php
                                }
                                ?>

                            </select>
                        </div>
                    </div>

                    <label>Description</label>
                    <div class="form-group">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="fa fa-fw fa-file"></i></span>
                            </div>
                            <input type="text" class="form-control" name="description" placeholder="Short summary of the document">
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-white">
                    <button type="submit" name="add_document" class="btn btn-label-primary text-bold"></i>Create</button>
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal"></i>Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>