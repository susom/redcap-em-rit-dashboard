ProjectSetup = {
    init: function () {
        ProjectSetup.getProjectPortalLinkageSection();

        jQuery(document).on("click", "#attach-redcap-project", function () {
            var projectPortalID = jQuery('#project-portal-list').find(":selected").val();
            ProjectSetup.attacheRedCapProject(projectPortalID)
        });
    },
    attacheRedCapProject: function (projectPortalID) {
        jQuery.ajax({
            url: jQuery("#attach-project-url").val(),
            type: 'POST',
            data: {project_portal_id: projectPortalID},
            success: function (data) {
                alert('link worked!!!')
            },
            error: function (request, error) {
                alert("Request: " + JSON.stringify(request));
            }
        });
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