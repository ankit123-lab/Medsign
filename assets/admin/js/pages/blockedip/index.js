var data_table = '#datatable_list';
function get_all_data() {
    var ip_filter = $('.ip_filter').val();

    jQuery(data_table).DataTable({
        responsive: true,
        processing: true,
        serverSide: true,
        pagingType: "full_numbers",
        scrollY: '50vh',
        scrollX: true,
        scrollCollapse: true,
        searching: false,
        order: [1, 'DESC'],
        "columns": [
            {"data": "ip"},
            {"data": "date"},
            {"data": "status"}
        ],
        columnDefs: [
            {
                targets: [0],
                searchable: true,
                sortable: true,
            },
            {
                targets: [1],
                searchable: true,
                sortable: true
            },
            {
                targets: [2],
                searchable: true,
                sortable: false,
            }
        ],
        language: {
            emptyTable: "No data available",
            zeroRecords: "No matching records found...",
            infoEmpty: "No records available"
        },
        oLanguage: {
            "sProcessing": ''
        },
        ajax: {
            "url": controller_url + '/get_all_data',
            "type": "POST",
            "async": false,
            "data": {
                'ip_filter': ip_filter
            },
        },
        drawCallback: function () {
            jQuery('<li><a onclick="refresh_tab()" style="cursor:pointer" title="Refresh"><i class="material-icons">cached</i></a></li>').prependTo('div.dataTables_paginate ul.pagination');
        }
    });
}

function refresh_tab() {
    jQuery(data_table).dataTable().fnDestroy();
    get_all_data();
    jQuery("#datatable_list_filter").css('display', 'none');
}

function filterGlobal() {
    jQuery(data_table).DataTable().search(
            jQuery('#global_filter').val()
            ).draw();
}

function filterColumn(i) {
    jQuery(data_table).DataTable().column(i).search(
            jQuery('#col' + i + '_filter').val()
            ).draw();
}

jQuery(document).ready(function () {
    get_all_data();

    // change active / inactive status
    jQuery(document).on('change', '.status_change', function () {
        var status;
        var id;
        if (jQuery(this).is(':checked')) {
            status = 1;
        } else {
            status = 2;
        }
        id = jQuery(this).attr('data-id');
        if (!isNaN(id)) {
            jQuery.ajax({
                "url": controller_url + '/change_status',
                type: "POST",
                data: {
                    'id': id, 'status': status
                },
                dataType: 'json',
                cache: false,
                success: function (response) {
                    if (response.success == true) {
                        toastr.success(response.message);
                    } else {
                        toastr.error(response.message);
                    }
                },
                error: function () {
                    toastr.error("Problem in performing your action.");
                }
            });
        } else {
            toastr.error("Invalid request...!");
        }
    });

    jQuery('.search_filter').click(function () {
        jQuery(data_table).dataTable().fnDestroy();
        get_all_data();
    });

    jQuery('.reset_filter').click(function () {
        $('.ip_filter').val('');

        jQuery('.smart_search').find('input:text, input:password, input:file, select, textarea').val('');
        jQuery('.smart_search').find('input:radio, input:checkbox').removeAttr('checked').removeAttr('selected');
        setTimeout(function () {
            jQuery(data_table).dataTable().fnDestroy();
            get_all_data();
        }, 100);
    });

    jQuery(document).on("click", ".unblock_ip", function () {
        var ip = jQuery(this).data("ip");
        if (ip != '') {
            jQuery.ajax({
                type: 'POST',
                url: controller_url + "/unblock_ip",
                data: {"ip": ip},
                dataType: 'json',
                success: function (response) {
                    if (response.success) {
                        toastr.success("IP unblocked successfully");
                    } else {
                        toastr.error("Error while fetch states");
                    }
                },
                error: function () {
                    toastr.error("Error while fetch states");
                }
            });
        } else {
            toastr.error("Error while fetch states");
        }

        setTimeout(function () {
            jQuery(data_table).dataTable().fnDestroy();
            get_all_data();
        }, 1000)
    });

});