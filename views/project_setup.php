<?php


namespace Stanford\ProjectPortal;

/** @var \Stanford\ProjectPortal\ProjectPortal $this */
?>
<style>
    .select2-container *:focus {
        outline: none;
    }
</style>
<script src="<?php echo $this->getUrl('assets/js/project_setup.js') ?>"></script>
<script>

    ProjectSetup.attachREDCapURL = "<?php echo $this->getURL('ajax/portal/project_attach.php', false, true) . '&pid=' . $this->getProjectId() ?>"
    ProjectSetup.detachREDCapURL = "<?php echo $this->getURL('ajax/portal/project_detach.php', false, true) . '&pid=' . $this->getProjectId() ?>"
    ProjectSetup.projectPortalSectionURL = "<?php echo $this->getURL('ajax/portal/project_setup.php', false, true) . '&pid=' . $this->getProjectId() ?>"

    window.onload = ProjectSetup.init();
</script>