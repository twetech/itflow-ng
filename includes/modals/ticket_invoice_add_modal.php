<?php require_once "/var/www/nestogy/includes/inc_all_modal.php"; ?>
<?php

$ticket_id = isset($_GET['ticket_id']) ? intval($_GET['ticket_id']) : 0;
$invoice_id = isset($_GET['invoice_id']) ? intval($_GET['invoice_id']) : 0;


$ticket_sql = mysqli_query($mysqli, "SELECT * FROM tickets WHERE ticket_id = $ticket_id");
$ticket_row = mysqli_fetch_array($ticket_sql);

$ticket_prefix = nullable_htmlentities($ticket_row['ticket_prefix']);
$ticket_number = intval($ticket_row['ticket_number']);
$ticket_subject = nullable_htmlentities($ticket_row['ticket_subject']);
$ticket_date = nullable_htmlentities($ticket_row['ticket_date']);
$ticket_asset_id = intval($ticket_row['ticket_asset_id']);
$ticket_contact_id = intval($ticket_row['ticket_contact_id']);
$ticket_status = nullable_htmlentities($ticket_row['ticket_status']);
$ticket_priority = nullable_htmlentities($ticket_row['ticket_priority']);
$ticket_category_id = intval($ticket_row['ticket_category_id']);

$ticket_total_reply_time = mysqli_query($mysqli, "SELECT SEC_TO_TIME(SUM(TIME_TO_SEC(ticket_reply_time_worked))) AS ticket_total_reply_time FROM ticket_replies WHERE ticket_reply_archived_at IS NULL AND ticket_reply_ticket_id = $ticket_id");
$row = mysqli_fetch_array($ticket_total_reply_time);
$ticket_total_reply_time = nullable_htmlentities($row['ticket_total_reply_time']);

$client_id = intval($ticket_row['ticket_client_id']);
$client_sql = mysqli_query($mysqli, "SELECT * FROM clients WHERE client_id = $client_id");
$client_row = mysqli_fetch_array($client_sql);
$client_rate = floatval($client_row['client_rate']);


// Check if ticket_id and invoice_id are set in the URL
$addToExistingInvoice = isset($_GET['ticket_id']) && isset($_GET['invoice_id']);
$sql_invoices = mysqli_query($mysqli, "SELECT * FROM invoices WHERE invoice_status LIKE 'Draft' AND invoice_client_id = $client_id ORDER BY invoice_number ASC");

?>

<div class="modal" id="addInvoiceFromTicketModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content bg-dark">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fa fa-fw fa-file-invoice-dollar mr-2"></i>Invoice ticket</h5>
                <button type="button" class="close text-white" data-bs-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form action="/post.php" method="post" autocomplete="off">

                <div class="modal-body bg-white">
                        <input type="hidden" name="ticket_id" value="<?= $ticket_id; ?>">
                        <ul class="nav nav-pills  mb-3">
                            <?php if (mysqli_num_rows($sql_invoices) > 0) { ?>
                                <li class="nav-item">
                                    <?php if (!$addToExistingInvoice): ?>
                                        <a class="nav-link active" role="tab" data-bs-toggle="tab" href="#pills-create-invoice"><i class="fa fa-fw fa-check mr-2"></i>Create New Invoice</a>
                                    <?php else: ?>
                                        <a class="nav-link" role="tab" data-bs-toggle="tab" href="#pills-create-invoice"><i class="fa fa-fw fa-check mr-2"></i>Create New Invoice</a>
                                    <?php endif; ?>
                                </li>
                                <li class="nav-item">
                                    <?php if ($addToExistingInvoice): ?>
                                        <a class="nav-link active" role="tab" data-bs-toggle="tab" href="#pills-add-to-invoice"><i class="fa fa-fw fa-plus mr-2"></i>Add to Existing Invoice</a>
                                    <?php else: ?>
                                        <a class="nav-link" role="tab" data-bs-toggle="tab" href="#pills-add-to-invoice"><i class="fa fa-fw fa-plus mr-2"></i>Add to Existing Invoice</a>
                                    <?php endif; ?>
                                </li>
                            <?php } else { ?>
                                <div class="alert alert-warning" role="alert">
                                    <i class="fa fa-fw fa-exclamation-triangle mr-2"></i>No draft invoices found. Please create a new invoice first.
                                </div> 
                            <?php } ?>
                        </ul>

                    <hr>

                    <div class="tab-content">

                        <?php if (!$addToExistingInvoice): ?>
                            <div class="tab-pane fade show active" id="pills-create-invoice">
                        <?php else: ?>
                            <div class="tab-pane fade" role="tabpanel" id="pills-create-invoice">
                        <?php endif; ?>

                            <div class="form-group">
                                <label>Invoice Date <strong class="text-danger">*</strong></label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fa fa-fw fa-calendar"></i></span>
                                    </div>
                                    <input type="date" class="form-control" name="date" max="2999-12-31" value="<?= date("Y-m-d"); ?>">
                                </div>
                            </div>

                            <div class="form-group">
                                <label>Invoice Category <strong class="text-danger">*</strong></label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fa fa-fw fa-list"></i></span>
                                    </div>
                                    <select class="form-control select2"  name="category">
                                        <option value="">- Category -</option>
                                        <?php

                                        $sql = mysqli_query($mysqli, "SELECT * FROM categories WHERE category_type = 'Income' AND category_archived_at IS NULL ORDER BY category_name ASC");
                                        while ($row = mysqli_fetch_array($sql)) {
                                            $category_id = intval($row['category_id']);
                                            $category_name = nullable_htmlentities($row['category_name']);
                                            ?>
                                            <option value="<?= $category_id; ?>"><?= $category_name; ?></option>

                                            <?php
                                        }
                                        ?>
                                    </select>
                                    <div class="input-group-append">
                                        <button type="button" class="btn btn-light" data-bs-toggle="modal" data-bs-target="#addQuickCategoryIncomeModal"><i class="fas fa-fw fa-plus"></i></button>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label>Scope</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fa fa-fw fa-comment"></i></span>
                                    </div>
                                    <input type="text" class="form-control" name="scope" placeholder="Quick description" value="Ticket <?= "$ticket_prefix$ticket_number - $ticket_subject"; ?>">
                                </div>
                            </div>


                        </div>

                        <?php
                        
                        if (mysqli_num_rows($sql_invoices) > 0) {
                            if ($addToExistingInvoice): ?>
                            <div class="tab-pane fade show active" id="pills-add-to-invoice">
                        <?php else: ?>
                            <div class="tab-pane fade" role="tabpanel" id="pills-add-to-invoice">
                            <?php endif;?>
                            <div class="form-group">
                                <label>Invoice</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fa fa-fw fa-file-invoice-dollar"></i></span>
                                    </div>
                                    <select class="form-control" name="invoice_id">
                                        <option value="0">- Invoice -</option>
                                        <?php

                                        while ($row = mysqli_fetch_array($sql_invoices)) {
                                            $invoice_id = intval($row['invoice_id']);
                                            $invoice_prefix = nullable_htmlentities($row['invoice_prefix']);
                                            $invoice_number = intval($row['invoice_number']);
                                            $invoice_scope = nullable_htmlentities($row['invoice_scope']);
                                            $invoice_status = nullable_htmlentities($row['invoice_status']);
                                            $invoice_date = nullable_htmlentities($row['invoice_date']);
                                            $invoice_due = nullable_htmlentities($row['invoice_due']);
                                            $invoice_amount = floatval($row['invoice_amount']);


                                            if ($invoice_status == "Draft") {

                                            ?>
                                            <option value="<?= $invoice_id; ?>" <?php if ($invoice_id == $_GET['invoice_id']) { 
                                                echo "selected";
                                                }?>><?= "$invoice_prefix$invoice_number $invoice_scope"; ?></option>
                                            <?php
                                            }
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <?php
                        }
                        ?>
                    </div>
                    <hr>
                    <div class="form-group">
                        <label>Item <strong class="text-danger">*</strong></label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="fa fa-fw fa-box"></i></span>
                            </div>
                            <input type="text" class="form-control" name="item_name" placeholder="Item" value="Support [Hourly]" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Item Description</label>
                        <div class="input-group">
                            <textarea class="form-control" rows="5" name="item_description">
                                <?= "# $contact_name - $asset_name - $ticket_date\nTicket $ticket_prefix$ticket_number\n$ticket_subject\nTT: $ticket_total_reply_time"; ?>
                            </textarea>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="col">

                            <div class="form-group">
                                <label>QTY <strong class="text-danger">*</strong></label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fa fa-fw fa-balance-scale"></i></span>
                                    </div>
                                    <input type="text" class="form-control" inputmode="numeric" pattern="-?[0-9]*\.?[0-9]{0,2}" name="qty" value="<?= roundToNearest15($ticket_total_reply_time); ?>" required>
                                </div>
                            </div>

                        </div>

                        <div class="col">

                            <div class="form-group">
                                <label>Price <strong class="text-danger">*</strong></label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fa fa-fw fa-dollar-sign"></i></span>
                                    </div>
                                    <input type="text" class="form-control" inputmode="numeric" pattern="-?[0-9]*\.?[0-9]{0,2}" name="price" value="<?= number_format($client_rate, 2, '.', ''); ?>" required>
                                </div>
                            </div>

                        </div>

                    </div>

                    <div class="form-group">
                        <label>Tax <strong class="text-danger">*</strong></label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="fa fa-fw fa-piggy-bank"></i></span>
                            </div>
                            <select class="form-control select2"  name="tax_id" required>
                                <?php

                                $taxes_sql = mysqli_query($mysqli, "SELECT * FROM taxes WHERE tax_archived_at IS NULL ORDER BY tax_name ASC");
                                while ($row = mysqli_fetch_array($taxes_sql)) {
                                    $tax_id_select = intval($row['tax_id']);
                                    $tax_name = nullable_htmlentities($row['tax_name']);
                                    $tax_percent = floatval($row['tax_percent']);
                                    ?>
                                    <option <?= $tax_id_select == 0 ? 'selected' : '' ?> value="<?= $tax_id_select; ?>"><?= "$tax_name $tax_percent%"; ?></option>

                                    <?php
                                }
                                ?>
                                <option value="0">None</option>

                            </select>

                        </div>
                    </div>

                </div>
                <div class="modal-footer bg-white">
                    <button type="submit" name="add_invoice_from_ticket" class="btn btn-label-primary text-bold"></i>Invoice</button>
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal"></i>Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>
