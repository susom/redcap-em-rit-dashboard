<!-- Generic Modal -->
<b-modal ref="generic-modal" size="lg" id="generic-modal" title="Create New Ticket">
    <b-overlay :show="isLoading" variant="light" opacity="0.80" rounded="sm">
        <?php
        require_once("create_jira_ticket.php");
        ?>
    </b-overlay>
    <template #modal-footer="{ ok, cancel, hide }">
        <!-- Emulate built in modal footer ok and cancel button actions -->
        <b-button :disabled='isDisabled' variant="primary" @click="submitTicket()">
            Submit
        </b-button>
        <b-button :disabled='isDisabled' variant="danger" @click="cancel()">
            Cancel
        </b-button>
    </template>
</b-modal>
<b-modal ref="ticket-modal" size="lg" id="ticket-modal" title="Support Ticket">
    <div class="d-block text-center">
        <span class="row ml-2" v-html="bodyMessage"></span>
    </div>
    <template #modal-footer="{ ok, cancel, hide }">
        <b-button :disabled='isDisabled' variant="secondary" @click="cancel()">
            Close
        </b-button>
    </template>
</b-modal>
<!-- END Time Slots Modal -->
