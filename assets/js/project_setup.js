ProjectSetup = {
    attachREDCapURL: '',
    projectPortalSectionURL: '',
    isLinked: false,
    init: function () {
        ProjectSetup.addProjectSetupLoad();

        jQuery(document).on("click", "#attach-redcap-project", function () {
            var projectPortalID = jQuery('#project-portal-list').find(":selected").val();
            var projectPortalName = jQuery('#project-portal-list').find(":selected").data('name');
            var projectPortalDescription = jQuery('#project-portal-list').find(":selected").data('description');
            ProjectSetup.attachRedCapProject(projectPortalID, projectPortalName, projectPortalDescription)
        });


        jQuery(document).on("click", "#detach-project", function () {
            if (confirm('Are you sure you want to detach This REDCap project from Portal?')) {
                var projectPortalID = jQuery(this).data("portal-project-id");
                var redcapProjectID = jQuery(this).data("redcap-id");
                ProjectSetup.detachRedCapProject(projectPortalID, redcapProjectID)
            }
        });
    },
    addProjectSetupLoad: function () {
        // setting a timeout
        var data = '<div id="portal-linkage-container">        <div class="alert alert-secondary d-flex justify-content-center">\n' +
            '  <div class="spinner-border" role="status">\n' +
            '    <span class="sr-only">Loading...</span>\n' +
            '  </div>\n' +
            '</div></div>'

        setTimeout(function () {
            jQuery("div.clearfix.mb-3:contains('The tables below provide general dashboard')").before(data);
            jQuery('#setupChklist-modify_project').before(data);
        }, 100)

        setTimeout(function () {
            ProjectSetup.getProjectPortalLinkageSection()
        }, 500)
    },
    getProjectPortalLinkageSection: function () {
        jQuery.ajax({
            url: ProjectSetup.projectPortalSectionURL,
            type: 'POST',
            success: function (data) {
                if ($("#portal-linkage-container").length != 0) {
                    $("#portal-linkage-container").replaceWith(data)
                } else {
                    jQuery("div.clearfix.mb-3:contains('The tables below provide general dashboard')").before(data);
                    jQuery('#setupChklist-modify_project').before(data);
                }

            },
            error: function (request, error) {
                console.log("Request: " + JSON.stringify(request));
            }
        });
    }
}