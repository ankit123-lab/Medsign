$(document).ready(function () {
	$('.dateOfBirth').mask("00/00/0000", {placeholder: "DD/MM/YYYY"});
	$('.dateOfBirth').datepicker({
        autoclose: true,
        todayHighlight: true,
        endDate: '+0d',
        format: 'dd/mm/yyyy'
    });

    $("body").on("keyup","#mobile_no", function() {
    	var mobile_no = $("#mobile_no").val();
    	$("#user_id").val("");
    	$("#first_name").attr('readonly', false);
	    $("#last_name").attr('readonly', false);
    	$('.member_other_details').show();
    	if(mobile_no.length == 10) {
			$.ajax({
	            type: 'POST',
	            dataType: 'json',
	            data: {'mobile_no': mobile_no},
	            url: site_url + "patient/get_member",
	            beforeSend: function() {
					
	            },
	            success: function(data) {
	                if(data.status == true) {
	                	$('.member_other_details').hide();
	                	$("#user_id").val(data.user.user_id);
	                	$("#first_name").val(data.user.user_first_name);
	                	$("#last_name").val(data.user.user_last_name);
	                	$("#first_name").attr('readonly', true);
	                	$("#last_name").attr('readonly', true);
	                }
	            }
	        });
		}
	});

	$("#patient-family-form").validate({
        rules: {
            first_name: {
                required: true
            },
            last_name: {
                required: true
            },
            date_of_birth: {
                required: true
            },
            relation: {
                required: true
            },
            gender: {
                required: true
            },
            user_phone_number: {
                digits: true,
                minlength: 10,
                required: false
            }
        },
        messages: {
            user_first_name: "Please enter first name",
            user_last_name: "Please enter last name",
            date_of_birth: "Please enter date of birth",
            relation: "Please select relation",
            gender: "Please select gender",
            user_phone_number: {required: "Please enter phone number", digits: "Your phone number is invalid", minlength: "Your phone number is invalid"}
        },
        submitHandler: function(form) { 
            $("#add_member_btn").attr('disabled', true);
            form.submit();
        },
        highlight: function (element) {
            
        },
        unhighlight: function (element) {
            
        }
    }); 

});