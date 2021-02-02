<?php


namespace Stanford\ProjectPortal;

/** @var \Stanford\ProjectPortal\ProjectPortal $this */
?>
<script src="<?php echo $this->getUrl('assets/js/contact_admin_button.js') ?>"></script>
<script>
    ContactButton.linkedProjectURL = "<?php echo $this->getURL('ajax/linked_project.php', false, true) . '&pid=' . $this->getProjectId() ?>"

    window.onload = function () {
        ContactButton.init();
    };
</script>
<div id="warning-dialog" style="top: 10% !important; display: none"
     title="Link Your project to the new Research Portal">To contact REDCap support team you need to link your REDCap
    project with Research IT portal project. If you already created Portal Project please click Link below and select
    your Portal project then click 'Attach Selected Project'. Otherwise please go to <a
            href="https://rit-portal.med.stanford.edu" target="_blank">https://rit-portal.med.stanford.edu</a> and
    create your research project.</d></div>
<div class="loader"><!-- Place at bottom of page --></div>