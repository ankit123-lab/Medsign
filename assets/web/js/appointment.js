$(document).ready(function () {
	$('.appointment_date').mask("00/00/0000", {placeholder: "DD/MM/YYYY"});
	$('.appointment_date').datepicker({
        autoclose: true,
        todayHighlight: true,
        format: 'dd/mm/yyyy'
    });
    $('#appointment_date').mask("00/00/0000", {placeholder: "DD/MM/YYYY"});
    var date = new Date();
    // date.setDate(date.getDate()-1);
	$('#appointment_date').datepicker({
        autoclose: true,
        startDate: date,
        todayHighlight: true,
        format: 'dd/mm/yyyy'
    });
    $('#appointment_from_time').change(function() {
        $("#doctor_availability_id").val($('#appointment_from_time option:selected').attr('doctor_availability_id'));
        $("#appointment_to_time").val($('#appointment_from_time option:selected').attr('end_time'));
    });
    $('#appointment_date, #appointment_type').change(function() {
        get_availability();
    });
    $("#speciality").select2();
    $("#book-now-form").validate({
        rules: {
            appointment_date: {
                required: true
            },
            appointment_type: {
                required: true
            },
            appointment_from_time: {
                required: true
            }
        },
        messages: {
            appointment_date: "The appointment date field is required.",
            appointment_type: "The appointment type field is required.",
            appointment_from_time: "The appointment time field is required."
        },
        submitHandler: function(form) { 
            $("#book_appointment_btn").attr('disabled', true);
            form.submit();
        },
        highlight: function (element) {
            
        },
        unhighlight: function (element) {
            
        }
    }); 
});

function get_availability() {
	var reuqest = {
		'doctor_id': $("#doctor_id").val(),
		'clinic_id': $("#clinic_id").val(),
		'date': $("#appointment_date").val(),
        'appointment_type': $("#appointment_type").val()
	};
    var options = '<option value="">Select Time</option>';
    if(reuqest.date.length != 10){
        $("#appointment_from_time").html(options);
        return false;
    }
    if(reuqest.date == '' || reuqest.appointment_type == '')
        return false;
	$.ajax({
        type: 'POST',
        data: reuqest,
        dataType: 'json',
        url: site_url + "patient/get_availability",
        success: function (data) {
            if(data.status == true) {
                $.each(data.data, function(k,value) {
                	options += '<option ' + ((!value.is_available) ? 'disabled' : '') + ' value="'+value.start_time+'" end_time="'+value.end_time+'" doctor_availability_id="'+value.doctor_availability_id+'">'+value.slot_time+'</option>';
                });
            }
            $("#appointment_from_time").html(options);
        }
    });
}