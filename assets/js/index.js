Main = {
    ajaxUserTicketURL: '',
    ajaxCreateJiraTicketURL: '',
    init: function () {
        // initiate the datatable.
        // $("#user-tickets").dataTable({
        //     processing: true,
        //     "language": {
        //         processing: '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Loading..n.</span> '
        //     },
        // });

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

        // move loader in correct position.
        $("#user-tickets").dataTable({
            "ajax": {
                "url": Main.ajaxUserTicketURL,
                "type": "POST"
            },
            // dom: '<"previous-filter"><lf<t>ip>',
            // data: data.data,
            "processing": true,
            'language': {
                'loadingRecords': '&nbsp;',
                'processing': '<i class="mt-1 fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Loading..n.</span>'
            }
        });
        // jQuery.ajax({
        //     url: Main.ajaxUserTicketURL,
        //     type: 'POST',
        //     'success': function (data) {
        //         $("#user-tickets").dataTable({
        //             dom: '<"previous-filter"><lf<t>ip>',
        //             data: data.data,
        //             pageLength: 50,
        //             "bDestroy": true,
        //             "aaSorting": [[0, "asc"]],
        //             'processing': true,
        //             'language': {
        //                 'loadingRecords': '&nbsp;',
        //                 'processing': 'Loading...'
        //             }
        //         });
        //     },
        //     'error': function (request, error) {
        //         var data = JSON.parse(request.responseText)
        //         alert(data.message);
        //     },
        // });
    },
    createTicket(data) {
        jQuery.ajax({
            url: Main.ajaxCreateJiraTicketURL,
            type: 'POST',
            data: data,
            success: function (data) {
                var re = JSON.parse(data)
                Main.getUserTickets();
                $('#generic-modal').modal('hide');
            },
            error: function (request, error) {
                var re = JSON.parse(request.responseText)
                $(".messages").addClass("alert-danger").removeClass('hidden').text(re.message);
            }
        });
    }
}