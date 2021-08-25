<?php
namespace Stanford\ProjectPortal;

/** @var \Stanford\ProjectPortal\ProjectPortal $module */
?>

<div class="container-fluid">
    <div class="alert hidden messages"></div>
    <form id="jira-ticket">
        <!--        <input type="hidden" name="redcap-project-id" value="--><?php //echo $module->getProjectId()
        ?><!--">-->
        <div class="form-group">
            <label for="exampleInputEmail1">Issue Summary</label>
            <!--            <input type="text" class="form-control" id="summary" name="summary" aria-describedby="emailHelp"-->
            <!--                   placeholder="Question Summary" required>-->
            <b-form-input v-model="ticket.summary" placeholder="Question Summary" required></b-form-input>
        </div>
        <div class="form-group">
            <label for="portal-projects">RIT Portal Project</label>
            <b-form-select v-model="ticket.project_portal_id" class="mb-3">
                <?php
                foreach ($module->getUser()->getProjectPortalList() as $project) {
                    if ($project['project_deleted_at']) {
                        continue;
                    }
                    ?>
                    <b-form-select-option
                            value="<?php echo $project['id'] ?>"><?php echo $project['project_name'] ?></b-form-select-option>

                    <?php
                }
                ?>
            </b-form-select>
        </div>
        <div class="form-group">
            <label for="issue-types">Issue Type</label>
            <b-form-select v-model="ticket.type" class="mb-3">
                <?php
                foreach ($module->getSupport()->getJiraIssueTypes() as $id => $issueType) {

                    ?>
                    <b-form-select-option value="<?php echo $id ?>"><?php echo $issueType ?></b-form-select-option>

                    <?php
                }
                ?>
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