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
        Main.getPortalProjectsList()

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
    },
    getPortalProjectsList: function () {
        // move loader in correct position.
        jQuery.ajax({
            url: Main.ajaxPortalProjectsListURL,
            type: 'POST',
            success: function (data) {
                var re = data.data;
                var sel = $('<select id="portal-project-list">').appendTo('#portal-project');
                $(re).each(function () {
                    if (this.url == '') {
                        if ((this.linked == undefined || this.linked == false)) {
                            sel.append($("<option>").attr('value', this.id).attr('data-name', this.name).attr('data-description', this.description).text(this.name));
                        } else {
                            sel.append($("<option>").attr('value', this.id).attr('selected', 'selected').attr('data-name', this.name).attr('data-description', this.description).text(this.name));
                        }
                    } else {
                        sel.append($("<option>").attr('data-url', this.url).text(this.name));
                    }

                });
            },
            error: function (request, error) {
                var re = JSON.parse(request.responseText)
                $(".messages").addClass("alert-danger").removeClass('hidden').text(re.message);
            },
            complete: function () {
                $('#portal-project-list').select2();
                $(document).on('select2:select', '#portal-project-list', function (e) {
                    // if user selects create new project open new tab with to the portal.
                    if (e.params.data.element.dataset.url !== undefined) {
                        var url = e.params.data.element.dataset.url
                        window.open(url, '_blank');
                    }
                });
            }
        });
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