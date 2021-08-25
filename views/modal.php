<!-- Generic Modal -->
<b-modal id="generic-modal" title="Create New Ticket">
    <b-alert variant="danger"
             dismissible
             fade
             :show="showDismissibleAlert"
    >
        {{alertMessage}}
    </b-alert>
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
