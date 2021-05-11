JiraTicket = {
    ajaxCreateJiraTicketURL: '',
    init: function () {

        $(".save-ticket").on('click', function () {
            var data = $('#jira-ticket').serialize()
            JiraTicket.createTicket(data)
        })
    },
    createTicket(data) {
        jQuery.ajax({
            url: JiraTicket.ajaxCreateJiraTicketURL,
            type: 'POST',
            data: data,
            success: function (data) {
                var re = JSON.parse(data)
                console.log(data)
                console.log(re)
            },
            error: function (request, error) {
                var re = JSON.parse(request.responseText)
                console.log(data)
                console.log(re)
                $(".messages").addClass("alert-danger").removeClass('hidden').text(re.message);
            }
        });
    }
}