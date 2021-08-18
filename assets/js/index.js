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
        Main.getProjectEMs()

        $(".add-ticket").click(function () {
            $('#generic-modal').modal('show');
        });

        $(".save-ticket").on('click', function () {
            var data = $('#jira-ticket').serialize()
            Main.createTicket(data)
        })
    },
    getProjectEMs: function () {
        $("#external-modules-table").dataTable({
            "ajax": {
                "url": Main.ajaxProjectEMstURL,
                "type": "POST"
            },
            // dom: '<"previous-filter"><lf<t>ip>',
            // data: data.data,
            "processing": true,
            // "serverSide": true,
            'language': {
                'loadingRecords': '&nbsp;',
                'processing': '<div class="spinner-border " role="status"><span class="sr-only">Loading...</span></div>'
            },
            "footerCallback": function (row, data, start, end, display) {
                var api = this.api(), data;

                // Remove the formatting to get integer data for summation
                var intVal = function (i) {
                    return typeof i === 'string' ?
                        i.replace(/[\$,]/g, '') * 1 :
                        typeof i === 'number' ?
                            i : 0;
                };
                // Total over all pages
                total = api
                    .column(2)
                    .data()
                    .reduce(function (a, b) {
                        return intVal(a) + intVal(b);
                    }, 0);

                // Total over this page
                pageTotal = api
                    .column(2, {page: 'current'})
                    .data()
                    .reduce(function (a, b) {
                        return intVal(a) + intVal(b);
                    }, 0);

                // Update footer
                $(api.column(2).footer()).html(
                    '$' + pageTotal + ' ( $' + total + ' total)'
                );
            }
        });
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
                'processing': '<div class="spinner-border " role="status"><span class="sr-only">Loading...</span></div>'
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
                var re = JSON.parse(data);
                $('#user-tickets').DataTable().clear();
                $('#user-tickets').DataTable().destroy();
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