<?php
namespace Stanford\ProjectPortal;

/** @var \Stanford\ProjectPortal\ProjectPortal $module */
?>

<div class="container-fluid">
    <div class="alert hidden messages"></div>
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
            <!--            <b-form-select v-model="ticket.project_portal_id" :options="portal_projects_list" class="mb-3"-->
            <!--                           value-field="id"-->
            <!--                           text-field="project_name">-->
            <!--            </b-form-select>-->
            <v-select class="mb-3 nopadding" v-model="ticket.project_portal_id" :options="portal_projects_list"
                      :reduce="project_portal_id => project_portal_id.id"
                      label="project_name">
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
    </form>
</div>