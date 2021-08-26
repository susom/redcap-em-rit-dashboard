<?php
namespace Stanford\ProjectPortal;

/** @var \Stanford\ProjectPortal\ProjectPortal $module */
?>

<div class="container-fluid">
    <div class="alert hidden messages"></div>
    <form id="jira-ticket">

        <div class="form-group">
            <label for="exampleInputEmail1">Issue Summary</label>
            <!--            <input type="text" class="form-control" id="summary" name="summary" aria-describedby="emailHelp"-->
            <!--                   placeholder="Question Summary" required>-->
            <b-form-input v-model="ticket.summary" placeholder="Question Summary" required></b-form-input>
        </div>
        <div class="form-group">
            <label for="portal-projects">RIT Portal Project</label>
            <b-form-select v-model="ticket.project_portal_id" :options="portal_projects_list" class="mb-3"
                           value-field="id"
                           text-field="project_name">
            </b-form-select>
        </div>
        <div class="form-group">
            <label for="issue-types">Issue Type</label>
            <b-form-select v-model="ticket.type" :options="ticket_types" class="mb-3">
            </b-form-select>
        </div>

        <div class="form-group">
            <label for="description">Description of Issue</label>
            <b-form-textarea
                    id="textarea"
                    v-model="ticket.description"
                    placeholder="Description of Issue"
                    rows="3"
                    max-rows="6"
            ></b-form-textarea>
        </div>
    </form>
</div>