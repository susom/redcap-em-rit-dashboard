<?php


namespace Stanford\ProjectPortal;

/** @var \Stanford\ProjectPortal\ProjectPortal $this */
?>
<script>
    var dashboardUrl = "<?php echo $this->getURL('views/index.php', false, false) ?>"

    var supportTab = '<br><div style="padding-top:15px;"><a href="' + dashboardUrl + '&open-support-modal=true#support" class="btn-contact-admin btn btn-danger btn-xs fs13" style="color:#fff;"><i style="color: white" class="fas fa-question-circle"></i>    Contact REDCap Support</a> </div>';
    window.onload = function () {
        $(".btn-contact-admin").attr('href', dashboardUrl).html('<i style="color: white" class="fas fa-desktop"></i> REDCap R2P2 Dashboard').after(supportTab)
    };
</script>