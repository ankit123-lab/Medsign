
jQuery(document).ready(function () {

    jQuery.validator.addMethod("verifyEmail", function (value, element) {
        var result = false;
        $.ajax({
            type: "POST",
            async: false,
            url: controller_url + "/check_data", // script to validate in server side
            data: {"data": value, "type": "email"},
            success: function (data) {
                result = data.success;
            }
        });
        // return true if username is exist in database
        return result;
    },
            "This email already register with us."
            );

    jQuery.validator.addMethod("verifyPhoneNumber", function (value, element) {
        var result = false;
        $.ajax({
            type: "POST",
            async: false,
            url: controller_url + "/check_data", // script to validate in server side
            data: {"data": value, "type": "number"},
            success: function (data) {
                result = data.success;
            }
        });
        // return true if username is exist in database
        return result;
    },
            "This phone number already register with us."
            );

    $.validator.addMethod("lettersonly", function (value, element) {
        return this.optional(element) || value == value.match(/^[a-zA-Z\s]+$/);
    }, 'Please enter only alphabetical letters.');

    jQuery("#custom_form_add").validate({
        rules: {
        },
        messages: {
        },
        errorPlacement: function (error, element) {
            var next = element;
            if (element.hasClass('file'))
                jQuery(error).insertAfter(jQuery(element).parents(".file-input"));
            else if (element.hasClass('chosen-select'))
                jQuery(error).insertAfter(jQuery(element).siblings(".chosen-container"));
            else
                jQuery(error).insertAfter(jQuery(element).parent());
        },
    });
});

jQuery(document).on("change", "#countries,#clinic_countries", function () {
    var obj = jQuery(this).parents("fieldset").find("select.state");
    var country_id = jQuery(this).val();
    if (country_id != '') {
        jQuery.ajax({
            type: 'POST',
            url: controller_url + "/get_states",
            data: {"country_id": country_id},
            dataType: 'json',
            async: false,
            success: function (response) {
                if (response.success) {
                    var html = '<option value="">Select State</option>';
                    jQuery(response.data).each(function (key, states) {
                        html += '<option value="' + states.state_id + '">' + states.state_name + '</option>'
                    });
                    obj.html(html);
                    obj.selectpicker('refresh');
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
        obj.html(html);
        obj.selectpicker('refresh');
    }
});

jQuery("#state,#clinic_state").change(function () {
    var obj = jQuery(this).parents("fieldset").find("select.city");
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

                    obj.html(html);
                    obj.selectpicker('refresh');
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
        obj.html(html);
        obj.selectpicker('refresh');
    }
});

var html = '<div class="added_content"><div class="col-sm-6 col-xs-12">' +
        '<div class="form-group form-float">' +
        '<div class="form-line">' +
        '<input type="text" class="form-control required" name="clinic_service[]">' +
        '<label class="form-label">Write clinic services *</label>' +
        '</div>' +
        '</div>' +
        '</div>' +
        '<div class="col-sm-6 col-xs-12">' +
        '<div class="form-group form-float">' +
        '<a href="javascript:void(0)" class="btn btn-danger remove_service">' +
        '<i class="material-icons">remove</i>' +
        '</a>' +
        '</div>' +
        '</div></div>';
jQuery("#add_service").click(function () {
    jQuery("#div_clinic_services").append(html);
});
jQuery(document).on("click", ".remove_service", function () {
    jQuery(this).parents(".added_content").remove();
});

initAutocomplete();
function initAutocomplete() {
    var doctor_autocomplete = new google.maps.places.Autocomplete(
            (document.getElementById('dr_google_address')));

    google.maps.event.addListener(doctor_autocomplete, 'place_changed', function () {
        var place = doctor_autocomplete.getPlace();
        console.log(place.geometry.location.lat());
        if (place.geometry !== 'undefined') {
            jQuery("#dr_google_address_lat").val(place.geometry.location.lat());
            jQuery("#dr_google_address_long").val(place.geometry.location.lng());
        }
        var componentForm = {
            street_number: 'short_name',
            route: 'long_name',
            locality: 'long_name',
            administrative_area_level_1: 'short_name',
            country: 'long_name',
            postal_code: 'short_name',
        };

        if (typeof place !== 'undefined') {
            for (var i = 0; i < place.address_components.length; i++) {
                var addressType = place.address_components[i].types[0];
                if (componentForm[addressType]) {
                    var search_address_value = place.address_components[i][componentForm[addressType]];
                    if (addressType == 'country') {
                        var country_name = search_address_value.toLowerCase().trim();
                        jQuery("#countries option").each(function () {
                            if (jQuery(this).text().trim().toLowerCase() == country_name) {
                                jQuery("#countries").val(jQuery(this).val());
                                jQuery("#countries").trigger("change");
                                jQuery("#countries").selectpicker('refresh');
                            }
                        });
                    }
                }
            }
        }
    });

    var clinic_google_address = new google.maps.places.Autocomplete(
            (document.getElementById('clinic_google_address')));

    google.maps.event.addListener(clinic_google_address, 'place_changed', function () {
        var place = clinic_google_address.getPlace();
        for (var d = 0; d <= 3; d++) {
            if (place.geometry !== 'undefined') {
                jQuery("#clinic_google_address_lat").val(place.geometry.location.lat());
                jQuery("#clinic_google_address_lat").val(place.geometry.location.lng());
            }
            if (place.geometry.location.lat() != '' && place.geometry.location.lng() != '') {
                break;
            }
        }
        var componentForm = {
            street_number: 'short_name',
            route: 'long_name',
            locality: 'long_name',
            administrative_area_level_1: 'short_name',
            country: 'long_name',
            postal_code: 'short_name',
        };

        if (typeof place !== 'undefined') {
            for (var i = 0; i < place.address_components.length; i++) {
                var addressType = place.address_components[i].types[0];
                if (componentForm[addressType]) {
                    var search_address_value = place.address_components[i][componentForm[addressType]];
                    if (addressType == 'country') {
                        var country_name = search_address_value.toLowerCase().trim();
                        jQuery("#clinic_countries option").each(function () {
                            if (jQuery(this).text().trim().toLowerCase() == country_name) {
                                jQuery("#clinic_countries").val(jQuery(this).val());
                                jQuery("#clinic_countries").trigger("change");
                                jQuery("#clinic_countries").selectpicker('refresh');
                            }
                        });
                    }
                }
            }
        }
    });
}

jQuery("#copy_doctor_address").click(function () {
    var dr_address = jQuery("#dr_google_address").val();
    var dr_address_lat = jQuery("#dr_google_address_lat").val();
    var dr_address_long = jQuery("#dr_google_address_long").val();
    var dr_countries = jQuery("#countries").val();
    var dr_state = jQuery("#state").val();
    var dr_city = jQuery("#city").val();
    var dr_localoty = jQuery("#dr_locality").val();
    var dr_pincode = jQuery("#dr_pincode").val();

    jQuery("#clinic_google_address").val(dr_address);
    jQuery("#clinic_google_address_lat").val(dr_address_lat);
    jQuery("#clinic_google_address_long").val(dr_address_long);
    jQuery("#clinic_countries").val(dr_countries);
    jQuery("#clinic_countries").trigger("change");
    jQuery("#clinic_state").val(dr_state);
    jQuery("#clinic_state").trigger("change");
    jQuery("#clinic_city").val(dr_city);
    jQuery("#clinic_locality").val(dr_localoty);
    jQuery("#clinic_pincode").val(dr_pincode);

    jQuery("#clinic_google_address,#clinic_locality,#clinic_pincode").click();

    jQuery("#clinic_countries,#clinic_state,#clinic_city").selectpicker('refresh');
});