<?php


namespace Stanford\ProjectPortal;

/** @var \Stanford\ProjectPortal\ProjectPortal $this */
?>
<script src="<?php echo $this->getUrl('assets/js/project_setup.js') ?>"></script>
<input type="hidden" name="project-setup-url" id="project-setup-url"
       value="<?php echo $this->getUrl("ajax/project_setup.php") . '&pid=' . $this->getProjectId() ?>">
<input type="hidden" name="attach-project-url" id="attach-project-url"
       value="<?php echo PROJECT_PORTAL_URL . 'api/projects/[PID]/attach-redcap/' ?>">
<input type="hidden" name="attach-project-url" id="attach-project-url"
       value="<?php echo $this->getUrl("ajax/project_attach.php") . '&pid=' . $this->getProjectId() ?>">
<script>
    ProjectSetup.init();
</script>
<div class="loader"><!-- Place at bottom of page --></div>