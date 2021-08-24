<?php
namespace Stanford\ProjectPortal;

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
                foreach ($module->getUser()->getProjectPortalList() as $project) {
                    if ($project['project_deleted_at']) {
                        continue;
                    }
                    ?>
                    <option value="<?php echo $project['id'] ?>" <?php echo isset($module->getPortal()->projectPortalSavedConfig['portal_project_id']) && $module->getPortal()->projectPortalSavedConfig['portal_project_id'] == $project['id'] ? 'selected' : '' ?>><?php echo $project['project_name'] ?></option>
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
                foreach ($module->getSupport()->getJiraIssueTypes() as $id => $issueType) {
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
    </form>
</div>