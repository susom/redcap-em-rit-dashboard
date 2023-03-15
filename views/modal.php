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
            <label for="description"><strong>Detailed Description: </strong></label>
            <b-form-textarea
                    id="textarea"
                    v-model="selectedServiceBlock.description"
                    placeholder="Please describe what you need help with."
                    rows="6"
                    max-rows="10"
            ></b-form-textarea>
        </div>
        <div class="form-group">
            <label for="portal-projects"><strong>Service Block Size <a
                            href="https://medwiki.stanford.edu/display/redcap/The+Statement+of+Work%3A+How+we+price+professional+services"
                            target="_blank" class="ml-1"><i
                                class="fas fa-external-link-alt"></i><span>more info</span></a>: </strong></label>

            <v-select class="mb-3 nopadding" v-model="selectedServiceBlock.id" :options="sprintBlocks"
                      label="title" required>
            </v-select>

        </div>
        <div class="form-group">
            <label for="exampleInputEmail1">Have you met with someone from REDCap team? If so, please describe.</label>
            <b-form-input v-model="selectedServiceBlock.redcap_admin" max="250"
                          placeholder="Whom you met with?"></b-form-input>
        </div>
        <b-form-checkbox
                id="checkbox-1"
                v-model="isAccepted"
                name="checkbox-1"
                value="accepted"
                unchecked-value="not_accepted"
        >
            I understand this is a request for a fixed amount of project assistance and depending on the scope and time
            of work additional blocks may be necessary.
        </b-form-checkbox>

    </b-overlay>
    <template #modal-footer="{ ok, cancel, hide }">
        <!-- Emulate built in modal footer ok and cancel button actions -->
        <b-button :disabled='isDisabled || isAccepted != "accepted"' variant="primary" @click="submitServiceBlock()">
            Submit
        </b-button>
        <b-button :disabled='isDisabled' variant="danger" @click="cancel()">
            Cancel
        </b-button>
    </template>
</b-modal>

<b-modal ref="project-creation-modal" size="xl" id="project-creation-modal" title="Create New R2P2 Project">
    <b-overlay :show="isLoading" variant="light" opacity="0.80" rounded="sm">
        <div class="card-style">
            <b-progress :value="progress" variant="success"></b-progress>
        </div>
        <b-card v-if="current_step==1" class="card-style" title="Project Wizard">
            <b-card-text>
                <?php
                require_once("project_creation/start.php");
                ?>
            </b-card-text>
            <b-row>
                <b-col class="d-flex justify-content-center">
                    <b-button class="float-right" variant="primary" @click="onClickNext(2)">Yes - I know my IRB #
                    </b-button>
                </b-col>
                <b-col class="d-flex justify-content-center">
                    <b-button class="float-right" variant="primary" @click="onClickNext(3)">No</b-button>
                </b-col>
            </b-row>

        </b-card>
        <b-card v-if="current_step==2" class="card-style" title="Project Wizard">
            <b-card-text>
                <?php
                require_once("project_creation/irb_form.php");
                ?>
            </b-card-text>
            <b-button class="float-left" variant="danger" @click="onClickBack">Back</b-button>
            <b-button class="float-right" variant="primary" @click="onClickNext(3)">Skip</b-button>
        </b-card>
        <b-card v-if="current_step==3" class="card-style" title="Project Wizard">
            <b-card-text>
                <?php
                require_once("project_creation/user_form.php");
                ?>
            </b-card-text>
            <b-button class="float-left" variant="danger" @click="onClickBack">Back to IRB</b-button>
            <b-button class="float-right" variant="primary" @click="onClickNext(4)">Skip</b-button>
        </b-card>
        <b-card v-if="current_step==4" class="card-style" title="Project Wizard">
            <b-card-text>
                <?php
                require_once("project_creation/project_form.php");
                ?>
            </b-card-text>
            <b-button class="float-left" variant="danger" @click="onClickBack">Back</b-button>
            <b-button class="float-right" variant="primary" @click="createR2P2Project">Create</b-button>
        </b-card>
    </b-overlay>
    <template #modal-footer="{ ok, cancel, hide }">

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
<!-- END Time Slots Modal -->
