ProjectSetup = {
    init: function () {
        ProjectSetup.getProjectPortalLinkageSection();
    },
    getProjectPortalLinkageSection: function () {
        jQuery.ajax({
            url: jQuery("#project-setup-url").val(),
            type: 'POST',
            success: function (data) {
                jQuery('#setupChklist-modules').after(data);
            },
            error: function (request, error) {
                alert("Request: " + JSON.stringify(request));
            }
        });
    }
}