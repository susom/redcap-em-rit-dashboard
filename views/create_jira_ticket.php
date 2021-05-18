<?php


namespace Stanford\ProjectPortal;

use GuzzleHttp\Exception\GuzzleException;

/** @var \Stanford\ProjectPortal\ProjectPortal $module */
?>

<div class="container-fluid">
    <div class="alert hidden messages"></div>
    <form id="jira-ticket">
        <input type="hidden" name="redcap-project-id" value="<?php echo $module->getProjectId() ?>">
        <div class="form-group">
            <label for="exampleInputEmail1">Issue Summary</label>
            <input type="text" class="form-control" id="summary" name="summary" aria-describedby="emailHelp"
                   placeholder="Question Summary" required>
        </div>
        <div class="form-group">
            <label for="portal-projects">RIT Portal Project</label>
            <select class="form-control" id="project-portal-id" name="project-portal-id">
                <option value="">SELECT A PROJECT</option>
                <?php
                foreach ($module->getProjectPortalList() as $project) {
                    if ($project['project_deleted_at']) {
                        continue;
                    }
                    ?>
                    <option value="<?php echo $project['id'] ?>"><?php echo $project['project_name'] ?></option>
                    <?php
                }
                ?>
            </select>
        </div>
        <div class="form-group">
            <label for="issue-types">Issue Type</label>
            <select class="form-control" id="issue-types-id" name="issue-types-id" required>
                <option>SELECT ISSUE TYPE</option>
                <?php
                foreach ($module->getJiraIssueTypes() as $id => $issueType) {
                    ?>
                    <option value="<?php echo $id ?>"><?php echo $issueType ?></option>
                    <?php
                }
                ?>
            </select>
        </div>

        <div class="form-group">
            <label for="description">Description of Issue</label>
            <textarea class="form-control" id="description" name="description" rows="3" required></textarea>
        </div>
        <div class="form-group">
            <button type="button" class="save-ticket btn btn-primary">Submit</button>
        </div>

    </form>
</div>
<?php
echo '<pre>';
try {
    print_r($module->getUserJiraTickets());
} catch (GuzzleException $e) {
    echo $e->getMessage();
}
echo '</pre>';
?>
<script src="<?php echo $module->getUrl('assets/js/create_jira_ticket.js') ?>"></script>
<script>
    JiraTicket.ajaxCreateJiraTicketURL = "<?php echo $module->getUrl('ajax/create_jira_ticket.php') ?>"
    JiraTicket.init()
</script>