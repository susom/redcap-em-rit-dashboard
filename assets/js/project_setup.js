ProjectSetup = {
    attachREDCapURL: '',
    projectPortalSectionURL: '',
    isLinked: false,
    init: function () {
        ProjectSetup.getProjectPortalLinkageSection();

        jQuery(document).on("click", "#attach-redcap-project", function () {
            var projectPortalID = jQuery('#project-portal-list').find(":selected").val();
            var projectPortalName = jQuery('#project-portal-list').find(":selected").data('name');
            var projectPortalDescription = jQuery('#project-portal-list').find(":selected").data('description');
            ProjectSetup.attacheRedCapProject(projectPortalID, projectPortalName, projectPortalDescription)
        });
    },
    attacheRedCapProject: function (projectPortalID, projectPortalName, projectPortalDescription) {
        jQuery.ajax({
            url: ProjectSetup.attachREDCapURL,
            type: 'POST',
            data: {
                project_portal_id: projectPortalID,
                project_portal_name: projectPortalName,
                project_portal_description: projectPortalDescription,
            },
            success: function (data) {
                alert('This REDCap project is linked to ' + projectPortalName)
            },
            complete: function () {
                ProjectSetup.getProjectPortalLinkageSection()
            },
            error: function (request, error) {
                alert("Request: " + JSON.stringify(request));
            }
        });
    },
    getProjectPortalLinkageSection: function () {
        jQuery.ajax({
            url: ProjectSetup.projectPortalSectionURL,
            type: 'POST',
            success: function (data) {
                if ($("#portal-linkage-container").length != 0) {
                    $("#portal-linkage-container").replaceWith(data)
                } else {
                    jQuery('#setupChklist-modify_project').before(data);
                }

            },
            error: function (request, error) {
                alert("Request: " + JSON.stringify(request));
            }
        });
    }
}