<!-- Generic Modal -->
<b-modal size="lg" id="generic-modal" title="Create New Ticket">

<?php
    require_once("create_jira_ticket.php");
    ?>
    <template #modal-footer="{ ok, cancel, hide }">
        <!-- Emulate built in modal footer ok and cancel button actions -->
        <b-button variant="primary" @click="submitTicket()">
            Submit
        </b-button>
        <b-button variant="danger" @click="cancel()">
            Cancel
        </b-button>
    </template>
</b-modal>
<!-- END Time Slots Modal -->
