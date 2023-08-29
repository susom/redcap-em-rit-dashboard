<?php
namespace Stanford\ProjectPortal;

/** @var \Stanford\ProjectPortal\ProjectPortal $module */
?>

<div class="container-fluid">
    <b-row class="mb-3">
        <b-col>
            <strong>
                <i v-if="linked() == true">If you are requesting <a target="_blank"
                                                                    href="https://medwiki.stanford.edu/x/qafZB">professional
                        services</a> please create a consultation request in <a target="_blank"
                                                                                :href="ticket.project_portal_consultation_url">R2P2</a>
                    instead of a support ticket.</i>
                <i v-else>If you are requesting <a target="_blank" href="https://medwiki.stanford.edu/x/qafZB">professional
                        services</a> please create a consultation request in <a target="_blank" :href="base_portal_url">R2P2</a>
                    instead of a support ticket.</i>
            </strong>
        </b-col>
    </b-row>
    <b-alert :variant="variant"
             dismissible
             fade
             :show="showDismissibleAlert"
    >
        <b class="row" v-html="alertMessage"></b>
    </b-alert>

    <form id="jira-ticket">

        <div class="form-group">
            <label for="exampleInputEmail1"><strong>Title</strong></label>
            <!--            <input type="text" class="form-control" id="summary" name="summary" aria-describedby="emailHelp"-->
            <!--                   placeholder="Question Summary" required>-->
            <b-form-input v-model="ticket.summary" max="250" placeholder="Ticket Title" required></b-form-input>
        </div>
        <div class="form-group">
            <label for="portal-projects"><strong>R2P2 Project</strong> (<span>To create new R2P2 project click <a
                            target="_blank" :href="base_portal_url">here</a></span>)</label>
            <v-select class="mb-3 nopadding" v-model="ticket.project_portal_id" :options="portal_projects_list"
                      value="id"
                      label="project_name" @input="supportTicketOpenR2P2CreationWizard">
            </v-select>

        </div>
        <!--        <div class="form-group">-->
        <!--            <label for="issue-types">Issue Type</label>-->
        <!--            <b-form-select v-model="ticket.type" :options="ticket_types" class="mb-3">-->
        <!--            </b-form-select>-->
        <!--        </div>-->

        <div class="form-group">
            <label for="description"><strong>Detailed Description</strong></label>
            <b-form-textarea
                    id="textarea"
                    v-model="ticket.description"
                    placeholder="Screenshots and/or Attachments can be added as comment after creating the ticket"
                    rows="6"
                    max-rows="10"
            ></b-form-textarea>
        </div>
        <div v-if="isSuperUser == 1" class="form-group">
            <label for="portal-projects">Submit on behalf of:</label>
            <v-select class="mb-3 nopadding" v-model="ticket.on_behalf_of" :options="redcapUsers"
                      :reduce="user => user.username"
                      label="full_name">
            </v-select>

        </div>
    </form>
</div>