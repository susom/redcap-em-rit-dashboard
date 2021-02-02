ContactButton = {
    linkedProjectURL: '',
    portalProject: [],
    isLinked: false,
    init: function () {
        ContactButton.getLinkedPortalProject();
    },
    modifyContactAdminButtons: function () {
        var $link = $('a:contains(" Contact REDCap administrator")');

        // remove origin link
        if (ContactButton.portalProject.status == 'success') {
            ContactButton.isLinked = true;
            $link.attr('href', 'https://rit-portal.med.stanford.edu/detail/' + ContactButton.portalProject.id + '/support');
            $link.attr('target', '_blank');
        } else {
            $link.attr('href', '#');
            $link.attr('onclick', 'ContactButton.warningDialog()');
        }
    },
    warningDialog: function () {
        $('#warning-dialog').dialog({
            bgiframe: true, modal: true, width: 400, position: ['center', 20],
            open: function () {
                fitDialog(this);
            },
            buttons: {
                Cancel: function () {
                    $(this).dialog('close');
                },
                Link: function () {
                    $("#project-portal-list").select2('open')
                    $(this).dialog('close');
                }
            }
        });
    },
    getLinkedPortalProject: function () {
        jQuery.ajax({
            url: ContactButton.linkedProjectURL,
            type: 'POST',
            success: function (data) {

                if (data.status == 'success') {
                    ContactButton.portalProject = data
                }
            },
            complete: function () {
                ContactButton.modifyContactAdminButtons();
            },
            error: function (request, error) {
                alert("Request: " + JSON.stringify(request));
            }
        });
    }
}