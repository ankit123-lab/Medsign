$(document).ready(function () {
	$('#date_of_report').mask("00/00/0000", {placeholder: "DD/MM/YYYY"});
	$('#date_of_report').datepicker({
    	autoclose: true,
        todayHighlight: true,
        endDate: '+0d',
        format: 'dd/mm/yyyy'
    });
    $("#patient-report-form").validate({
        rules: {
            report_name: {
                required: true
            },
            type_of_report: {
                required: true
            },
            date_of_report: {
                required: true
            },
            "report_file[]": {
		      required: true,
		      extension: "jpg|png|gif|pdf"
		    }
        },
        messages: {
            report_name: "The report name field is required.",
            type_of_report: "The type of report field is required.",
            date_of_report: "The date of report field is required.",
            "report_file[]": {required: "The report file field is required.", extension: "Your selected file type is invalid."}
        },
        submitHandler: function(form) { 
            $("#add_report_btn").attr('disabled', true);
            form.submit();
        },
        highlight: function (element) {
            
        },
        unhighlight: function (element) {
            
        }
    }); 
});