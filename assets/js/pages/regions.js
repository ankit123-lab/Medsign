function resetList() {
    state_selector.html('').selectpicker('refresh');
    city_selector.html('').selectpicker('refresh');
}

// generate countries field options
function generateCountryList(id) {
    var country_option = '';
    resetList();
    country_selector.find("option").eq(0).remove();
    jQuery.ajax({
        type: 'POST',
        async: false,
        cache: false,
        url: common_url + "/get_all_countries",
        data: {"id": id},
        success: function (data) {
            if (data.success) {
                $.each(data.result_list, function (key, value) {
                    country_option += '<option value="' + value.id + '">' + value.name + ' (' + value.iso + ')</option>';
                });
                country_selector.html(country_option);
                country_selector.selectpicker({title: data.message}).selectpicker('render');
                country_selector.selectpicker('refresh');
            } else {
                country_selector.selectpicker({title: data.message}).selectpicker('render');
            }
        }, error: function (jqXHR, textStatus, errorThrown) {
            country_selector.selectpicker({title: common_no_country_found}).selectpicker('render');
            toastr.error(errorThrown);
        }

    });
}

// generate state field options
function generateStateList(id) {
    var state_option = '';
    resetList();
    state_selector.find("option").eq(0).remove();
    jQuery.ajax({
        type: 'POST',
        async: false,
        cache: false,
        url: common_url + "/get_all_states",
        data: {"id": id},
        success: function (data) {
            if (data.success) {
                $.each(data.result_list, function (key, value) {
                    state_option += '<option value="' + value.id + '">' + value.name + '</option>';
                });
                state_selector.html(state_option);
                state_selector.selectpicker({title: data.message}).selectpicker('render');
                state_selector.selectpicker('refresh');
            } else {
                state_selector.selectpicker({title: data.message}).selectpicker('render');
            }
        }, error: function (jqXHR, textStatus, errorThrown) {
            state_selector.selectpicker({title: common_no_state_found}).selectpicker('render');
            toastr.error(errorThrown);
        }
    });
}

// generate state field options
function generateCityList(id) {
    var city_option = '';
    city_selector.find("option").eq(0).remove();
    city_selector.html('');
    jQuery.ajax({
        type: 'POST',
        async: false,
        cache: false,
        url: common_url + "/get_all_cities",
        data: {"id": id},
        success: function (data) {
            if (data.success) {
                $.each(data.result_list, function (key, value) {
                    city_option += '<option value="' + value.id + '">' + value.name + '</option>';
                });
                city_selector.html(city_option);
                city_selector.selectpicker({title: data.message}).selectpicker('render');
                city_selector.selectpicker('refresh');
            } else {
                city_selector.selectpicker({title: data.message}).selectpicker('render');
            }
        }, error: function (jqXHR, textStatus, errorThrown) {
            city_selector.selectpicker({title: common_no_city_found}).selectpicker('render');
            toastr.error(errorThrown);
        }
    });
}

jQuery(document).ready(function () {

    if (typeof (get_default_country) !== 'undefined' && get_default_country) {
        generateCountryList(0);
    }

    country_selector.change(function () {
        generateStateList(country_selector.find('option:selected').val());
    });

    state_selector.change(function () {
        generateCityList(state_selector.find('option:selected').val());
    });

    city_selector.change(function () {

    });
});