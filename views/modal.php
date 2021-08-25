<!-- Generic Modal -->
<b-modal id="generic-modal" title="Create New Ticket">
    <?php
    require_once("create_jira_ticket.php");
    ?>
    <template #modal-footer="{ ok, cancel, hide }">
        <b>Custom Footer</b>
        <!-- Emulate built in modal footer ok and cancel button actions -->
        <b-button size="sm" variant="primary" @click="ok()">
            Submit
        </b-button>
        <b-button size="sm" variant="danger" @click="cancel()">
            Cancel
        </b-button>
    </template>
</b-modal>
<!-- END Time Slots Modal -->
