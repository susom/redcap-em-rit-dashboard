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
    detachRedCapProject: function (projectPortalID, redcapProjectID) {
        jQuery.ajax({
            url: ProjectSetup.detachREDCapURL,
            type: 'POST',
            data: {
                project_portal_id: projectPortalID,
                redcap_project_id: redcapProjectID,
            },
            success: function (data) {
                $('#linked-project').text('')
                $('#linked-project').data('project-id', '')
                ProjectSetup.getProjectPortalLinkageSection()
            },
            error: function (request, error) {
                $("#portal-errors").removeClass('hidden').text("Request: " + JSON.stringify(request));
            }
        });
    }, attachRedCapProject: function (projectPortalID, projectPortalName, projectPortalDescription) {
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
                ProjectSetup.getProjectPortalLinkageSection()
            },
            error: function (request, error) {
                $("#portal-errors").removeClass('hidden').text("Request: " + JSON.stringify(request));
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
            jQuery('#setupChklist-modify_project').before(data);
        }, 10)

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
                    jQuery('#setupChklist-modify_project').before(data);
                }

            },
            error: function (request, error) {
                alert("Request: " + JSON.stringify(request));
            }
        });
    }
}