function validate_id_proof() {
	var id_proof_type = $("#id_proof_type").val();
	var id_proof_detail = $("#id_proof_detail").val();
	var patt = '';
	if(id_proof_type == 'Aadhar card') {
		patt = /^[0-9]{12}$/;
	} else if(id_proof_type == 'Passport') {
		patt = /^([a-zA-Z]){1}([0-9]){7}?$/;
	} else if(id_proof_type == 'Driving License') {
		patt = /^([a-zA-Z]){2}([0-9]){13}?$/;
	} else if(id_proof_type == 'Pan Card') {
		patt = /^([a-zA-Z]){5}([0-9]){4}([a-zA-Z]){1}?$/;
	}
	var is_valid = true;
	$("#id_proof_type-error").hide();
	$("#id_proof_detail-error").hide();
	if(id_proof_type == '' && id_proof_detail != '') {
		$("#id_proof_type-error").html("Please select ID proof type").show();
		console.log("asdas");
		is_valid = false;
	}
	if(patt != '' && id_proof_detail != '') {
		if(patt.test(id_proof_detail) == false) {
			$("#id_proof_detail-error").html("Invalid ID proof detail").show();
			console.log("zzz");
			is_valid = false;
		}
	}
	return is_valid;
}
function family_health_history(){
	$(".family-health-history").append('<div class="row family-health-history-row">'+$(".family-health-history-html").html()+'</div>');
	var count = $(".family-health-history-row").length;
	$(".family-health-history-row:last").find(".family_medical_conditions").attr("name", "family_medical_conditions[" + (count-1) + "][]");
	$(".family-health-history-row:last").find(".family_medical_conditions").select2();
	$(".family-health-history-row:last").find(".familySinceWhen").datepicker({
    	autoclose: true,
        todayHighlight: true,
        endDate: '+0d',
        format: 'dd/mm/yyyy'
    });
}
function chronic_diseases_selected(val) {
	var text = my_decrypt(val);
	$.each(text.split(","), function(i,e){
	    $("#chronic_diseases option[value='" + e + "']").prop("selected", true);
	});
	// $("#chronic_diseases").val(text);
	$("#chronic_diseases").select2();
}
function activity_levels() {
	$(".activity-level").append('<div class="row activity-level-row">'+$(".activity-level-html").html()+'</div>');
}
function injuries_selected(val){
	if(val != undefined && val != '') {
		var injuries = my_decrypt(val);
		$.each(injuries.split(","), function(k, value){
			$(".patient-injuries").append('<div class="row patient-injuries-row">'+$(".patient-injuries-html").html()+'</div>');
			$(".patient-injuries-row:last").find("input").val(value);
		});
		$(".patient-injuries-row").find(".add-patient-injuries").hide();
		$(".patient-injuries-row").find(".delete-patient-injuries").show();
		$(".patient-injuries-row:last").find(".delete-patient-injuries").hide();
		$(".patient-injuries-row:last").find(".add-patient-injuries").show();
	} else {
		$(".patient-injuries").append('<div class="row patient-injuries-row">'+$(".patient-injuries-html").html()+'</div>');
	}
}
function surgeries_selected(val){
	if(val != undefined && val != '') {
		var surgeries = my_decrypt(val);
		$.each(surgeries.split(","), function(k, value){
			$(".patient-surgeries").append('<div class="row patient-surgeries-row">'+$(".patient-surgeries-html").html()+'</div>');
			$(".patient-surgeries-row:last").find("input").val(value);
		});
		$(".patient-surgeries-row").find(".add-patient-surgeries").hide();
		$(".patient-surgeries-row").find(".delete-patient-surgeries").show();
		$(".patient-surgeries-row:last").find(".delete-patient-surgeries").hide();
		$(".patient-surgeries-row:last").find(".add-patient-surgeries").show();
	} else {
		$(".patient-surgeries").append('<div class="row patient-surgeries-row">'+$(".patient-surgeries-html").html()+'</div>');
	}
}
function food_allergies_selected(val){
	if(val != undefined && val != '') {
		var food_allergies = my_decrypt(val);
		$.each(food_allergies.split(","), function(k, value){
			$(".patient-food-allergies").append('<div class="row patient-food-allergies-row">'+$(".patient-food-allergies-html").html()+'</div>');
			$(".patient-food-allergies-row:last").find("input").val(value);
		});
		$(".patient-food-allergies-row").find(".add-patient-food-allergies").hide();
		$(".patient-food-allergies-row").find(".delete-patient-food-allergies").show();
		$(".patient-food-allergies-row:last").find(".delete-patient-food-allergies").hide();
		$(".patient-food-allergies-row:last").find(".add-patient-food-allergies").show();
	} else {
		$(".patient-food-allergies").append('<div class="row patient-food-allergies-row">'+$(".patient-food-allergies-html").html()+'</div>');
	}
}
function medicine_allergies_selected(val){
	if(val != undefined && val != '') {
		var medicine_allergies = my_decrypt(val);
		$.each(medicine_allergies.split(","), function(k, value){
			$(".patient-medicine-allergies").append('<div class="row patient-medicine-allergies-row">'+$(".patient-medicine-allergies-html").html()+'</div>');
			$(".patient-medicine-allergies-row:last").find("input").val(value);
		});
		$(".patient-medicine-allergies-row").find(".add-patient-medicine-allergies").hide();
		$(".patient-medicine-allergies-row").find(".delete-patient-medicine-allergies").show();
		$(".patient-medicine-allergies-row:last").find(".delete-patient-medicine-allergies").hide();
		$(".patient-medicine-allergies-row:last").find(".add-patient-medicine-allergies").show();
	} else {
		$(".patient-medicine-allergies").append('<div class="row patient-medicine-allergies-row">'+$(".patient-medicine-allergies-html").html()+'</div>');
	}
}
function other_allergies_selected(val){
	if(val != undefined && val != '') {
		var other_allergies = my_decrypt(val);
		$.each(other_allergies.split(","), function(k, value){
			$(".patient-others-allergies").append('<div class="row patient-others-allergies-row">'+$(".patient-others-allergies-html").html()+'</div>');
			$(".patient-others-allergies-row:last").find("input").val(value);
		});
		$(".patient-others-allergies-row").find(".add-patient-others-allergies").hide();
		$(".patient-others-allergies-row").find(".delete-patient-others-allergies").show();
		$(".patient-others-allergies-row:last").find(".delete-patient-others-allergies").hide();
		$(".patient-others-allergies-row:last").find(".add-patient-others-allergies").show();
	} else {
		$(".patient-others-allergies").append('<div class="row patient-others-allergies-row">'+$(".patient-others-allergies-html").html()+'</div>');
	}
}
$(function () {
    $('#dateOfBirth').datepicker({
    	autoclose: true,
        todayHighlight: true,
        endDate: '+0d',
        format: 'dd/mm/yyyy'
    });
    $('.familySinceWhen').datepicker({
    	autoclose: true,
        todayHighlight: true,
        endDate: '+0d',
        format: 'dd/mm/yyyy'
    });
});
$(document).ready(function () {
	$('.select2').select2();
	$('.dateOfBirth').mask("00/00/0000", {placeholder: "DD/MM/YYYY"});
	$('.familySinceWhen').mask("00/00/0000", {placeholder: "DD/MM/YYYY"});
	
	$("body").on("click", ".add-activity-level", function(){
		$(".activity-level").append('<div class="row activity-level-row">'+$(".activity-level-html").html()+'</div>');
		$(".activity-level-row").find(".add-activity-level").hide();
		$(".activity-level-row").find(".delete-activity-level").show();

		$(".activity-level-row:last").find(".delete-activity-level").hide();
		$(".activity-level-row:last").find(".add-activity-level").show();
	});
	$("body").on("click", ".delete-activity-level", function(){
		$(this).closest(".activity-level-row").remove();
	});
	
	$("body").on("click", ".add-family-history", function(){
		$(".family-health-history").append('<div class="row family-health-history-row">'+$(".family-health-history-html").html()+'</div>');
		$(".family-health-history-row").find(".add-family-history").hide();
		$(".family-health-history-row").find(".delete-family-history").show();
		$(".family-health-history-row:last").find(".delete-family-history").hide();
		$(".family-health-history-row:last").find(".add-family-history").show();
		var count = $(".family-health-history-row").length;
		$(".family-health-history-row:last").find(".family_medical_conditions").attr("name", "family_medical_conditions[" + (count-1) + "][]");
		$(".family-health-history-row:last").find(".family_medical_conditions").select2();
		$(".family-health-history-row:last").find(".familySinceWhen").datepicker({
	    	autoclose: true,
	        todayHighlight: true,
	        endDate: '+0d',
	        format: 'dd/mm/yyyy'
	    });
	});
	$("body").on("click", ".delete-family-history", function(){
		$(this).closest(".family-health-history-row").remove();
	});
	
	$("body").on("click", ".add-patient-injuries", function(){
		$(".patient-injuries").append('<div class="row patient-injuries-row">'+$(".patient-injuries-html").html()+'</div>');
		$(".patient-injuries-row").find(".add-patient-injuries").hide();
		$(".patient-injuries-row").find(".delete-patient-injuries").show();
		$(".patient-injuries-row:last").find(".delete-patient-injuries").hide();
		$(".patient-injuries-row:last").find(".add-patient-injuries").show();
	});
	$("body").on("click", ".delete-patient-injuries", function(){
		$(this).closest(".patient-injuries-row").remove();
	});

	$("body").on("click", ".add-patient-surgeries", function(){
		$(".patient-surgeries").append('<div class="row patient-surgeries-row">'+$(".patient-surgeries-html").html()+'</div>');
		$(".patient-surgeries-row").find(".add-patient-surgeries").hide();
		$(".patient-surgeries-row").find(".delete-patient-surgeries").show();
		$(".patient-surgeries-row:last").find(".delete-patient-surgeries").hide();
		$(".patient-surgeries-row:last").find(".add-patient-surgeries").show();
	});
	$("body").on("click", ".delete-patient-surgeries", function(){
		$(this).closest(".patient-surgeries-row").remove();
	});

	$("body").on("click", ".add-patient-food-allergies", function(){
		$(".patient-food-allergies").append('<div class="row patient-food-allergies-row">'+$(".patient-food-allergies-html").html()+'</div>');
		$(".patient-food-allergies-row").find(".add-patient-food-allergies").hide();
		$(".patient-food-allergies-row").find(".delete-patient-food-allergies").show();
		$(".patient-food-allergies-row:last").find(".delete-patient-food-allergies").hide();
		$(".patient-food-allergies-row:last").find(".add-patient-food-allergies").show();
	});
	$("body").on("click", ".delete-patient-food-allergies", function(){
		$(this).closest(".patient-food-allergies-row").remove();
	});

	$("body").on("click", ".add-patient-medicine-allergies", function(){
		$(".patient-medicine-allergies").append('<div class="row patient-medicine-allergies-row">'+$(".patient-medicine-allergies-html").html()+'</div>');
		$(".patient-medicine-allergies-row").find(".add-patient-medicine-allergies").hide();
		$(".patient-medicine-allergies-row").find(".delete-patient-medicine-allergies").show();
		$(".patient-medicine-allergies-row:last").find(".delete-patient-medicine-allergies").hide();
		$(".patient-medicine-allergies-row:last").find(".add-patient-medicine-allergies").show();
	});
	$("body").on("click", ".delete-patient-medicine-allergies", function(){
		$(this).closest(".patient-medicine-allergies-row").remove();
	});

	$("body").on("click", ".add-patient-others-allergies", function(){
		$(".patient-others-allergies").append('<div class="row patient-others-allergies-row">'+$(".patient-others-allergies-html").html()+'</div>');
		$(".patient-others-allergies-row").find(".add-patient-others-allergies").hide();
		$(".patient-others-allergies-row").find(".delete-patient-others-allergies").show();
		$(".patient-others-allergies-row:last").find(".delete-patient-others-allergies").hide();
		$(".patient-others-allergies-row:last").find(".add-patient-others-allergies").show();
	});
	$("body").on("click", ".delete-patient-others-allergies", function(){
		$(this).closest(".patient-others-allergies-row").remove();
	});

	$("#user_state_id").change(function(){
		$.ajax({
            type: 'POST',
            data: {'state_id': $(this).val()},
            dataType: 'json',
            url: site_url + "patient/profile/get_city",
            success: function (data) {
                var options = '<option value="">Select City</option>';
                if(data.status == true) {
                    $.each(data.data, function(k,value){
                    	options += '<option value="'+value.city_id+'">'+value.city_name+'</option>';
                    });
                }
                $("#user_city_id").html(options);
            }
        });
	});
	$("#patient-update-form").validate({
        rules: {
            user_first_name: {
                required: true
            },
            user_last_name: {
                required: true
            },
            date_of_birth: {
                required: true
            },
            gender: {
                required: true
            },
            user_phone_number: {
                digits: true,
                minlength: 10,
                required: false
            },
            emergency_contact_number: {
                digits: true,
                minlength: 10,
                required: false
            },
            user_email: {
                email: true
            },
            user_height: {
                range: [1, 333]
            },
            user_weight: {
                range: [1, 200]
            }
        },
        messages: {
            user_first_name: "Please enter first name",
            user_last_name: "Please enter last name",
            date_of_birth: "Please enter date of birth",
            gender: "Please select gender",
            user_email: {email: "Your email address is invalid"},
            user_height: {range: "Height can not be less then 1 nor greater than 333 CM."},
            user_weight: {range: "Weight can not be less then 1 nor greater than 200 KG."},
            user_phone_number: {required: "Please enter phone number", digits: "Your phone number is invalid", minlength: "Your phone number is invalid"},
            emergency_contact_number: {digits: "Number is invalid", minlength: "Number is invalid"}
        },
        submitHandler: function() { 
            $("#id_proof_file-error").hide();
            $("#update_server_side_error").hide();
        	if(!validate_id_proof()) {
        		$("#patient-personal-tab").click();
        		return false;
        	}
            $("#reg-btn").hide();
            $(".loader-img").show();
            $(".update-success").hide();
            var values = $("#patient-update-form").serializeArray();
            var patient_injuries_text = '';
            var patient_surgeries_text = '';
            var patient_food_sllergies_text = '';
            var patient_medicine_sllergies_text = '';
            var patient_other_sllergies_text = '';
            var chronic_diseases_text = '';
            for (index = 0; index < values.length; ++index) {
			    if (values[index].name == "chronic_diseases[]") {
			        if(chronic_diseases_text != '')
			    		chronic_diseases_text += ',';
			        chronic_diseases_text += values[index].value;
			    }
			    if (values[index].name == "patient_injuries[]") {
			    	if(patient_injuries_text != '')
			    		patient_injuries_text += ',';
			        patient_injuries_text += values[index].value;
			    }
			    if (values[index].name == "patient_surgeries[]") {
			    	if(patient_surgeries_text != '')
			    		patient_surgeries_text += ',';
			        patient_surgeries_text += values[index].value;
			    }
			    if (values[index].name == "patient_food_allergies[]") {
			    	if(patient_food_sllergies_text != '')
			    		patient_food_sllergies_text += ',';
			        patient_food_sllergies_text += values[index].value;
			    }
			    if (values[index].name == "patient_medicine_allergies[]") {
			    	if(patient_medicine_sllergies_text != '')
			    		patient_medicine_sllergies_text += ',';
			        patient_medicine_sllergies_text += values[index].value;
			    }
			    if (values[index].name == "patient_others_allergies[]") {
			    	if(patient_other_sllergies_text != '')
			    		patient_other_sllergies_text += ',';
			        patient_other_sllergies_text += values[index].value;
			    }
			}
			values.push({'name': 'patient_injuries_text', 'value': my_encrypt(patient_injuries_text)})
			values.push({'name': 'patient_surgeries_text', 'value': my_encrypt(patient_surgeries_text)})
			values.push({'name': 'patient_food_sllergies_text', 'value': my_encrypt(patient_food_sllergies_text)})
			values.push({'name': 'patient_medicine_sllergies_text', 'value': my_encrypt(patient_medicine_sllergies_text)})
			values.push({'name': 'patient_other_sllergies_text', 'value': my_encrypt(patient_other_sllergies_text)})
			values.push({'name': 'chronic_diseases_text', 'value': my_encrypt(chronic_diseases_text)})
            $.ajax({
                type: 'POST',
                dataType: 'json',
                data: jQuery.param(values),
                url: site_url + "patient/profile/update_data",
                success: function (data) {
                    if (data.error) {
                        $("#update_server_side_error").html(data.error);
                        $("#update_server_side_error").addClass("alert alert-danger");
                        $("#update_server_side_error").show();
                        $(".loader-img").hide();
                        $("#reg-btn").show();
                        $('html, body').animate({
                            scrollTop: $("#update_server_side_error").offset().top - 100
                        }, 1000);
                    }
                    if(data.status == true) {
                    	if(files != undefined && files != '') {
                    		var formData = new FormData();
					        $.each(files, function(key, value){
					        	formData.append('id_proof_file', value);
					        });
					        $.ajax({
								url: site_url + "patient/profile/document_upload",  
								type: 'POST',
								data: formData,
								dataType: 'json',
								success: function(r){
									if(r.status == true) {
										$(".loader-img").hide();
				                        $("#reg-btn").show();
				                        $(".update-success").html(data.msg);
				                        $(".update-success").show();
				                        $("#message").delay(5000).slideUp(300);
				                        if(r.img_path != undefined && r.img_path != '') {
					                        $(".view_id_proof").attr("src", r.img_path_thumb);
					                        $(".view_id_proof").attr("img_path", r.img_path).show();
					                    }
					                    $('#id_proof_file').val('');
									} else {
										$("#id_proof_file-error").html(r.msg).show();
										$("#patient-personal-tab").click();
										$(".loader-img").hide();
                        				$("#reg-btn").show();
									}
									files = '';
								}, 
								cache: false,
								contentType: false,
								processData: false
							});
                    	} else {
                    		$(".loader-img").hide();
	                        $("#reg-btn").show();
	                        $(".update-success").html(data.msg);
	                        $(".update-success").show();
	                        $("#message").delay(5000).slideUp(300);
                    	}
                    	
                    }
                }
            });
        },
        highlight: function (element) {
            
        },
        unhighlight: function (element) {
            
        }
    }); 
	$("body").on("click", ".view_id_proof", function(){
		$("#idProofViewModal").modal("show");
		$(".id_proof_img_view").attr("src",$(this).attr("img_path"));
	});
	var files;
    $('#id_proof_file').on('change', prepareUpload);
    function prepareUpload(event) {
    	files = event.target.files;
    };
	$("body").on("click", ".remove_member", function (){
		$("#memberRemoveModal").modal("show");
		$("#member_name").html($(this).attr("user_name"));
		$("#member_email").val($(this).attr("user_email"));
		$("#member_remove_error").hide();
		$("#member_mobile_no").val("");
		if($(this).attr("user_email") == "")
			$("#member_email").attr("readonly", false);
		else
			$("#member_email").attr("readonly", true);
		$("#member_id").val($(this).attr("user_id"));
	});
	$("#member_remove_frm").validate({
        rules: {
            member_email: {
                email: true
            },
            member_mobile_no: {
                digits: true,
                minlength: 10,
                required: true
            }
        },
        messages: {
            member_email: {email: "Your email address is invalid"},
            member_mobile_no: {required: "Please enter phone number", digits: "Your phone number is invalid", minlength: "Your phone number is invalid"}
        },
        submitHandler: function(form) { 
            $.ajax({
	            type: 'POST',
	            dataType: 'json',
	            data: $("#member_remove_frm").serialize(),
	            url: site_url + "patient/remove_member_with_save_detail",
	            beforeSend: function() {
					$("#member_remove_btn").attr('disabled', true);
					$("#member_remove_error").hide();
	            },
	            success: function(data) {
	                if(data.status == true) {
	                	$("#" + $("#member_id").val()).remove();
	                	$("#memberRemoveModal").modal("hide");
                        $("#message2").html("<strong>Success!</strong> " + data.msg);
                        $("#message2").show();
                        $("#message2").delay(5000).slideUp(300);
                    } else {
                    	$("#member_remove_error").html(data.error);
                        $("#member_remove_error").addClass("alert alert-danger");
                        $("#member_remove_error").show();
                    }
	                $("#member_remove_btn").attr('disabled', false);
	            }
	        });
        },
        highlight: function (element) {
            
        },
        unhighlight: function (element) {
            
        }
    }); 
});

! function($) {
    "use strict";
    var a = {
        accordionOn: ["xs"]
    };
    $.fn.responsiveTabs = function(e) {
        var t = $.extend({}, a, e),
            s = "";
        return $.each(t.accordionOn, function(a, e) {
            s += " accordion-" + e
        }), this.each(function() {
            var a = $(this),
                e = a.find("> li > a"),
                t = $(e.first().attr("href")).parent(".tab-content"),
                i = t.children(".tab-pane");
            a.add(t).wrapAll('<div class="responsive-tabs-container" />');
            var n = a.parent(".responsive-tabs-container");
            n.addClass(s), e.each(function(a) {
                var t = $(this),
                    s = t.attr("href"),
                    i = "",
                    n = "",
                    r = "";
                t.parent("li").hasClass("active") && (i = " active"), 0 === a && (n = " first"), a === e.length - 1 && (r = " last"), t.clone(!1).addClass("accordion-link" + i + n + r).insertBefore(s)
            });
            var r = t.children(".accordion-link");
            e.on("click", function(a) {
                a.preventDefault();
                var e = $(this),
                    s = e.parent("li"),
                    n = s.siblings("li"),
                    c = e.attr("href"),
                    l = t.children('a[href="' + c + '"]');
                s.hasClass("active") || (s.addClass("active"), n.removeClass("active"), i.removeClass("active"), $(c).addClass("active"), r.removeClass("active"), l.addClass("active"))
            }), r.on("click", function(t) {
                t.preventDefault();
                var s = $(this),
                    n = s.attr("href"),
                    c = a.find('li > a[href="' + n + '"]').parent("li");
                s.hasClass("active") || (r.removeClass("active"), s.addClass("active"), i.removeClass("active"), $(n).addClass("active"), e.parent("li").removeClass("active"), c.addClass("active"))
            })
        })
    }
}(jQuery);
$('.responsive-tabs').responsiveTabs({
     accordionOn: ['xs', 'sm']
});
