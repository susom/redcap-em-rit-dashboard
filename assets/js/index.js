Main = {
    ajaxUserTicketURL: '',
    ajaxCreateJiraTicketURL: '',
    init: function () {
        Main.getUserTickets()

        $(".add-ticket").click(function () {
            $('#generic-modal').modal('show');
        });

        $(".save-ticket").on('click', function () {
            var data = $('#jira-ticket').serialize()
            Main.createTicket(data)
        })
    },
    getUserTickets: function () {
        jQuery.ajax({
            url: Main.ajaxUserTicketURL,
            type: 'POST',
            'success': function (data) {
                $("#user-tickets").dataTable({
                    dom: '<"previous-filter"><lf<t>ip>',
                    data: data.data,
                    pageLength: 50,
                    "bDestroy": true,
                    "aaSorting": [[0, "asc"]],
                });
            },
            'error': function (request, error) {
                var data = JSON.parse(request.responseText)
                alert(data.message);
            },
        });
    },
    createTicket(data) {
        jQuery.ajax({
            url: Main.ajaxCreateJiraTicketURL,
            type: 'POST',
            data: data,
            success: function (data) {
                var re = JSON.parse(data)
                Main.getUserTickets()
            },
            error: function (request, error) {
                var re = JSON.parse(request.responseText)
                $(".messages").addClass("alert-danger").removeClass('hidden').text(re.message);
            }
        });
    }
}