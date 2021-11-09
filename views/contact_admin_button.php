<?php


namespace Stanford\ProjectPortal;

/** @var \Stanford\ProjectPortal\ProjectPortal $this */
?>
<script>
    var dashboardUrl = "<?php echo $this->getURL('views/index.php', false, false) ?>"

    window.onload = function () {
        $(".btn-contact-admin").attr('href', dashboardUrl).html('<i style="color: white" class="fas fa-desktop"></i> REDCap R2P2 Dashboard')
    };
</script>