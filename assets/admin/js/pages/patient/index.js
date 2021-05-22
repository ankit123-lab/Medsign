var data_table = '#datatable_list';
function get_all_data() {
    var email_filter = $('.email_filter').val();
    var name_filter = $('.name_filter').val();
    var countries = jQuery(".countries").selectpicker("val");
    var state = jQuery(".state").selectpicker("val");
    var city = jQuery(".city").selectpicker("val");
    var status_filter = $('.status_filter').selectpicker('val');
    var blocked = $('.block_filter').selectpicker('val');

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
            {"data": "name"},
            {"data": "email"},
            {"data": "created_at"},
            {"data": "status"},
            {"data": "block_status"},
            {"data": "action"}
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
                sortable: true,
            },
            {
                targets: [3],
                searchable: true,
                sortable: false,
            },
            {
                targets: [4],
                searchable: true,
                sortable: false,
            },
            {
                targets: [5],
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
                'name_filter': name_filter,
                'email_filter': email_filter,
                'status_filter': status_filter,
                "countries": countries,
                "state": state,
                "city": city,
                "blocked": blocked
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
        $('.status_filter').selectpicker('deselectAll');
        $('.email_filter').val('');
        $('.name_filter').val('');
        $('.countries').selectpicker('deselectAll');
        $('.state').selectpicker('deselectAll');
        $('.city').selectpicker('deselectAll');
        $('.block_filter').selectpicker('deselectAll');

        jQuery('.smart_search').find('input:text, input:password, input:file, select, textarea').val('');
        jQuery('.smart_search').find('input:radio, input:checkbox').removeAttr('checked').removeAttr('selected');
        setTimeout(function () {
            jQuery(data_table).dataTable().fnDestroy();
            get_all_data();
        }, 100);
    });


    jQuery("#countries").change(function () {
        var country_id = jQuery(this).val();
        if (country_id != '') {
            jQuery.ajax({
                type: 'POST',
                url: controller_url + "/get_states",
                data: {"country_id": country_id},
                dataType: 'json',
                success: function (response) {
                    if (response.success) {
                        var html = '<option value="">Select State</option>';
                        jQuery(response.data).each(function (key, states) {
                            html += '<option value="' + states.state_id + '">' + states.state_name + '</option>'
                        });

                        jQuery("#state").html(html);
                        jQuery("#state").selectpicker('refresh');
                    } else {
                        toastr.error("Error while fetch states");
                    }
                },
                error: function () {
                    toastr.error("Error while fetch states");
                }
            });
        } else {
            var html = '<option value="">Select State</option>';
            jQuery("#state").html(html);
            jQuery("#state").selectpicker('refresh');
        }
    });

    jQuery("#state").change(function () {
        var state_id = jQuery(this).val();
        if (state_id != '') {
            jQuery.ajax({
                type: 'POST',
                url: controller_url + "/get_cities",
                data: {"state_id": state_id},
                dataType: 'json',
                success: function (response) {
                    if (response.success) {
                        var html = '<option value="">Select City</option>';
                        jQuery(response.data).each(function (key, cities) {
                            html += '<option value="' + cities.city_id + '">' + cities.city_name + '</option>'
                        });

                        jQuery("#city").html(html);
                        jQuery("#city").selectpicker('refresh');
                    } else {
                        toastr.error("Error while fetch cities");
                    }
                },
                error: function () {
                    toastr.error("Error while fetch cities");
                }
            });
        } else {
            var html = '<option value="">Select City</option>';
            jQuery("#city").html(html);
            jQuery("#city").selectpicker('refresh');
        }
    });

    jQuery(document).on("click", ".unblock_user", function () {
        var user_id = jQuery(this).data("id");
        if (user_id != '') {
            jQuery.ajax({
                type: 'POST',
                url: controller_url + "/unblock_users",
                data: {"user_id": user_id},
                dataType: 'json',
                success: function (response) {
                    if (response.success) {
                        toastr.success("User unblocked successfully");
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