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
<b-modal ref="result-modal" size="lg" id="result-modal" :title="resultModalTitle">
    <div class="d-block text-center">
        <span class="row ml-2" v-html="bodyMessage"></span>
    </div>
    <template #modal-footer="{ ok, cancel, hide }">
        <b-button :disabled='isDisabled' variant="secondary" @click="cancel()">
            Close
        </b-button>
    </template>
</b-modal>
<b-modal ref="service-block-modal" size="lg" id="service-block-modal" title="Generate Sprint Block">
    <b-alert :variant="variant"
             dismissible
             fade
             :show="showDismissibleAlert"
    >
        <b class="row" v-html="alertMessage"></b>
    </b-alert>
    <b-overlay :show="isLoading" variant="light" opacity="0.80" rounded="sm">
        <div class="form-group">
            <label for="portal-projects">Service Block Size</label>

            <v-select class="mb-3 nopadding" v-model="selectedServiceBlock.id" :options="sprintBlocks"
                      label="title" required>
            </v-select>

        </div>
        <div class="form-group">
            <label for="description"><strong>Detailed Description</strong></label>
            <b-form-textarea
                    id="textarea"
                    v-model="selectedServiceBlock.description"
                    placeholder="Please describe what you need help with."
                    rows="6"
                    max-rows="10"
            ></b-form-textarea>
        </div>
    </b-overlay>
    <template #modal-footer="{ ok, cancel, hide }">
        <!-- Emulate built in modal footer ok and cancel button actions -->
        <b-button :disabled='isDisabled' variant="primary" @click="submitServiceBlock()">
            Submit
        </b-button>
        <b-button :disabled='isDisabled' variant="danger" @click="cancel()">
            Cancel
        </b-button>
    </template>
</b-modal>
<!-- END Time Slots Modal -->
