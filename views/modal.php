<!-- Generic Modal -->

<div class="modal " id="generic-modal">
    <div class="modal-dialog modal-lg modal-sm">
        <div class="modal-content">

            <!-- Modal Header -->
            <div class="modal-header">
                <h4 class="modal-title">Create New Ticket</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>

            <!-- Modal body -->
            <div class="modal-body">
                <?php
                require_once("create_jira_ticket.php");
                ?>
            </div>

            <!-- Modal footer -->
            <div class="modal-footer">
                <button type="button" class="save-ticket btn btn-primary">Submit</button>
                <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
            </div>

        </div>
    </div>
</div>
<!-- END Time Slots Modal -->
