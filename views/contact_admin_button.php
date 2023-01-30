<?php


namespace Stanford\ProjectPortal;

/** @var \Stanford\ProjectPortal\ProjectPortal $this */
?>
<script>
    var dashboardUrl = "<?php echo $this->getURL('views/index.php', false, false) ?>"

    var supportTab = '<br><div style="padding-top:15px;"><a target="_self" href="' + dashboardUrl + '&open-support-modal=true#support" class="btn-contact-admin btn btn-danger btn-xs fs13" style="color:#fff;"><i style="color: white" class="fas fa-question-circle"></i>    Contact REDCap Support</a> </div>';
    // window.onload = function () {
    //     console.log(2222)
    //     console.log($(".btn-contact-admin"))
    //     $(".btn-contact-admin").attr('target', '_self').attr('href', dashboardUrl).html('<i style="color: white" class="fas fa-desktop"></i> REDCap R2P2 Dashboard').after(supportTab);
    //
    //
    //     // for redcap 12.3.2 remove top contact admin button
    //     $(document).find("a:contains('Contact REDCap administrator')").parent().remove()
    //     console.log(3333)
    //     console.log($(".btn-contact-admin"))
    //     console.log($(".btn-contact-admin"))
    //     $('.btn-contact-admin').first().parent().remove()
    //
    // };

    window.addEventListener("load", function (event) {

        $(".btn-contact-admin").attr('target', '_self').attr('href', dashboardUrl).html('<i style="color: white" class="fas fa-desktop"></i> REDCap R2P2 Dashboard').after(supportTab);


        // for redcap 12.3.2 remove top contact admin button
        $(document).find("a:contains('Contact REDCap administrator')").parent().remove()

        $('.btn-contact-admin').first().parent().remove()
    }, false);
</script>