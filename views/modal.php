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
                require_once("project_creation/project_search.php");
                ?>
            </b-card-text>


        </b-card>
        <b-card v-if="current_step==2" class="card-style" title="Project Wizard">
            <b-card-text>
                <?php
                require_once("project_creation/start.php");
                ?>
            </b-card-text>
            <b-row>
                <b-col class="d-flex justify-content-center">
                    <b-button class="float-right" variant="primary" @click="onClickNext(3)">Yes - I know my IRB #
                    </b-button>
                </b-col>
                <b-col class="d-flex justify-content-center">
                    <b-button class="float-right" variant="primary" @click="onClickNext(4)">No</b-button>
                </b-col>
            </b-row>

        </b-card>
        <b-card v-if="current_step==3" class="card-style" title="Project Wizard">
            <b-card-text>
                <?php
                require_once("project_creation/irb_form.php");
                ?>
            </b-card-text>
            <b-button class="float-left" variant="danger" @click="onClickBack">Back</b-button>
            <b-button class="float-right" variant="primary" @click="onClickNext(5);irb={}">Skip</b-button>
        </b-card>
        <b-card v-if="current_step==4" class="card-style" title="Project Wizard">
            <b-card-text>
                <?php
                require_once("project_creation/user_form.php");
                ?>
            </b-card-text>
            <b-button class="float-left" variant="danger" @click="onClickBack">Back to IRB</b-button>
            <b-button class="float-right" variant="primary" @click="onClickNext(5)">Skip</b-button>
        </b-card>
        <b-card v-if="current_step==5" class="card-style" title="Project Wizard">
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
            Close
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

<b-modal ref="approve-sow-modal" size="lg" id="approve-sow-modal" :title="sow_approval.sow_title">
    <b-overlay :show="isLoading" variant="light" opacity="0.80" rounded="sm">

        <b-alert
            :variant="variant"
            dismissible
            fade
            :show="showDismissibleAlert">
            <b class="row" v-html="alertMessage"></b>
        </b-alert>
        <b-row>
            <b-col>
                {{notifications.approve_sow_instructions}}
            </b-col>
        </b-row>
        <b-row>
            <b-col lg="12">
                <label for="sow_approval.pta_number">Registered PTAs <span style="color: red">*</span></label>
            </b-col>
        </b-row>
        <b-row>
            <b-col lg="12">
                <v-select class="col-8 nopadding"
                          v-model="selected_pta_number"
                          :options="registered_pta"
                          label="pta_charge_number" @input="selectPTA">
                </v-select>
            </b-col>
        </b-row>
        <b-form-group
            class="mb-3"
        >
            <label for="sow_approval.reviewer_name">Please enter your full name here for verification <span
                    style="color: red">*</span></label>
            <b-input-group class="mb-2">
                <b-form-input id="sow_approval.reviewer_name" v-model="sow_approval.reviewer_name"
                              type="text"></b-form-input>
            </b-input-group>
        </b-form-group>

        <b-form-group
            label="Comment (optional)"
            label-for="project_description"
            class="mb-3"
        >
            <b-input-group class="mb-2">
                <b-form-textarea id="sow_approval.comment" v-model="sow_approval.comment"
                                 type="text"></b-form-textarea>
            </b-input-group>
        </b-form-group>
    </b-overlay>
    <template #modal-footer="{ ok, cancel, hide }">
        <b-button :disabled='isDisabled' class="float-left" variant="danger"
                  @click="approveLater()">Later
        </b-button>
        <b-button :disabled='isDisabled' class="float-right" variant="success" @click="approveSOW()">
            Approve
        </b-button>
    </template>

</b-modal>

<b-modal ref="create-pta-modal" size="lg" id="create-pta-modal" title="Create New PTA">
    <b-alert
        :variant="variant"
        dismissible
        fade
        :show="showDismissibleAlert">
        <b class="row" v-html="alertMessage"></b>
    </b-alert>
    <b-overlay :show="isLoading" variant="light" opacity="0.80" rounded="sm">
        <b-form-group class="mb-3">
            <label for="pta_number">PTA Number <span style="color: red">*</span></label>
            <b-input-group class="mb-2">
                <b-form-input id="pta_number"
                              v-model="pta.pta_number"
                              type="text"></b-form-input>
            </b-input-group>
        </b-form-group>

    </b-overlay>
    <template #modal-footer="{ ok, cancel, hide }">
        <b-button :disabled='isDisabled' variant="success" @click="createNewPTA()">
            Submit
        </b-button>
    </template>
</b-modal>

<b-modal ref="sync-users" size="lg" id="sync-users" title="Sync Users">
    <b-alert
        :variant="variant"
        dismissible
        fade
        :show="showDismissibleAlert">
        <b class="row" v-html="alertMessage"></b>
    </b-alert>
    <b-overlay :show="isLoading" variant="light" opacity="0.80" rounded="sm">
        <div class="d-block text-center">
            <b-table striped hover bordered :items="users_list" :fields="users_fields_list">

                <template #cell(group)="row">
                    <b-form-select :disabled="!user_has_admin_permissions" v-model="row.item.group_id"
                                   :options="r2p2_groups"
                                   value-field="id"
                                   text-field="name"
                    ></b-form-select>
                </template>
                <template #cell(action)="row">
                    <div v-if="user_has_admin_permissions">
                        <b-row v-if="row.item.r2p2 != 'N/A'">

                            <b-col>
                                <b-button size="sm" :disabled='isDisabled'
                                          variant="success"
                                          @click="syncREDCapUserToR2P2(row.item.group_id, row.item.redcap_username, row.item.r2p2_user_id)">
                                    Update
                                </b-button>

                                <b-button size="sm" :disabled='isDisabled'
                                          variant="danger"
                                          @click="removeREDCapUser(row.item.r2p2_user_id)">
                                    Delete
                                </b-button>
                            </b-col>

                        </b-row>

                        <b-row v-if="row.item.r2p2 == 'N/A'">
                            <b-col>
                                <b-button size="sm" :disabled='isDisabled' variant="primary"
                                          @click="syncREDCapUserToR2P2(row.item.group_id, row.item.redcap_username, row.item.r2p2_user_id)">
                                    Add
                                </b-button>
                            </b-col>
                        </b-row>
                    </div>
                </template>
            </b-table>
        </div>

    </b-overlay>
    <template #modal-footer="{ ok, cancel, hide }">
        <b-button :disabled='isDisabled' variant="secondary" @click="cancel()">
            Close
        </b-button>
    </template>
</b-modal>
<!-- END Time Slots Modal -->
