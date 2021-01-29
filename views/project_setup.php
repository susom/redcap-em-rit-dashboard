<?php


namespace Stanford\ProjectPortal;

/** @var \Stanford\ProjectPortal\ProjectPortal $this */
?>
<script src="<?php echo $this->getUrl('assets/js/project_setup.js') ?>"></script>
<script>
    ProjectSetup.attachREDCapURL = "<?php echo $this->getURL('ajax/project_attach.php', false, true) . '&pid=' . $this->getProjectId() ?>"
    ProjectSetup.projectPortalSectionURL = "<?php echo $this->getURL('ajax/project_setup.php', false, true) . '&pid=' . $this->getProjectId() ?>"
    ProjectSetup.init();
</script>
<div class="loader"><!-- Place at bottom of page --></div>