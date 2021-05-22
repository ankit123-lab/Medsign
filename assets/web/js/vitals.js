var graph_data = [];
$(document).ready(function () {
    $('.dropdown-toggle').dropdown();
    $('.temperature_taken').click(function(){
        $(".temperature_taken_txt").html($(this).text());
        $("#vital_report_temperature_taken").val($(this).attr('rel'));
    });
    $('.bloodpressure_type').click(function(){
        $(".bloodpressure_type_txt").html($(this).text());
        $("#vital_report_bloodpressure_type").val($(this).attr('rel'));
    });
	$('#vital_report_date').mask("00/00/0000", {placeholder: "DD/MM/YYYY"});
	$('#vital_report_date').datepicker({
    	autoclose: true,
        todayHighlight: true,
        // startDate: '-180d',
        endDate: '+0d',
        format: 'dd/mm/yyyy'
    });
    $('#start_date').mask("00/00/0000", {placeholder: "DD/MM/YYYY"});
    $('#start_date').datepicker({
        autoclose: true,
        todayHighlight: true,
        endDate: '+0d',
        format: 'dd/mm/yyyy'
    });
    $('#end_date').mask("00/00/0000", {placeholder: "DD/MM/YYYY"});
    $('#end_date').datepicker({
        autoclose: true,
        todayHighlight: true,
        endDate: '+0d',
        format: 'dd/mm/yyyy'
    });
    $(".temperature_type").click(function(){
        $(".temperature_type").show();
        $(this).hide();
        $("#vital_report_temperature_type").val($(this).attr("t_type"));
        var temperature = FCConvert($("#vital_report_temperature").val(),$(this).attr("t_type"));
        $("#vital_report_temperature").val(temperature);
    });
    $(".card-header > a > label").click(function(){
        var field_arr = [
            'vital_report_date',
            'vital_report_weight',
            'vital_report_pulse',
            'vital_report_resp_rate',
            'vital_report_spo2',
            'bloodpressure',
            'vital_report_temperature'
        ];
        $.each(field_arr, function(k, field){
            if(field == "bloodpressure"){
                var systolic = $("#vital_report_bloodpressure_systolic").val();
                var diastolic = $("#vital_report_bloodpressure_diastolic").val();
                if(systolic != "" || diastolic != "") {
                    $("#"+field+"_val").html(systolic + "/" + diastolic);
                } else {
                    $("#"+field+"_val").html("");
                }
            } else {
                $("#"+field+"_val").html($("#"+field).val());
            }
        });
    });
    $("#patient-vitals-form").validate({
        ignore: [],
        rules: {
            vital_report_date: {
                required: true
            },
            vital_report_weight: {
                required: false,
                number: true,
                range: [1, 200]
            },
            vital_report_pulse: {
                required: false,
                number: true,
                range: [10, 500]
            },
            vital_report_resp_rate: {
                required: false,
                number: true,
                range: [10, 70]
            },
            vital_report_spo2: {
                required: false,
                number: true,
                range: [1, 100]
            },
            vital_report_bloodpressure_systolic: {
                required: false,
                number: true,
                range: [50, 300]
            },
            vital_report_bloodpressure_diastolic: {
                required: false,
                number: true,
                range: [25, 200]
            },
            vital_report_temperature: {
                required: false,
                number: true
            }
        },
        messages: {
            vital_report_date: "The date of vital field is required.",
            vital_report_weight: {required: "The weight of vital field is required.", number: "Invalid weight", range: "Weight can not be less then 1 nor greater than 200"},
            vital_report_pulse: {required: "The pulse rate of vital field is required.", number: "Invalid pulse rate", range: "Pulse rate cannot be lesser than 10 nor greater than 500"},
            vital_report_resp_rate: {required: "The respiration rate of vital field is required.", number: "Invalid respiration rate", range: "Respiration rate cannot be lesser than 10 nor greater than 70"},
            vital_report_spo2: {required: "The SpO2 of vital field is required.", number: "Invalid SpO2", range: "SpO2 cannot be lesser than 1 nor greater than 100."},
            vital_report_bloodpressure_systolic: {required: "The systolic blood pressure of vital field is required.", number: "Invalid systolic blood pressure", range: "Systolic Blood Pressure cannot be lesser than 50 nor greater than 300"},
            vital_report_bloodpressure_diastolic: {required: "The diastolic blood pressure of vital field is required.", number: "Invalid diastolic blood pressure", range: "Diastolic Blood Pressure cannot be lesser than 25 nor greater than 200"},
            vital_report_temperature: {required: "The temperature of vital field is required.", number: "Invalid temperature"}
        },
        submitHandler: function(form) { 
            $("#vitals_server_side_error").hide();
            $("#temperature_error").hide();
            if($("#vital_report_weight").val() == '' && $("#vital_report_pulse").val() == '' && $("#vital_report_resp_rate").val() == '' && $("#vital_report_spo2").val() == '' && $("#vital_report_bloodpressure_systolic").val() == '' && $("#vital_report_bloodpressure_diastolic").val() == '' && $("#vital_report_temperature").val() == '') {
                $("#vitals_server_side_error").html("Please Enter At Least One Vital Sign");
                $("#vitals_server_side_error").addClass("alert alert-danger");
                $("#vitals_server_side_error").show();
                $('html, body').animate({
                    scrollTop: $("#vitals_server_side_error").offset().top - 100
                }, 1000);
                return false;
            }
            if($("#vital_report_temperature").val() != '') {
                if(($("#vital_report_temperature").val() < 75.2 || $("#vital_report_temperature").val() > 109.4) && $("#vital_report_temperature_type").val() == "1") {
                    $("#temperature_error").html("Temperature cannot be lesser than 75.2 nor greater than 109.4").show();
                    $("#vital_report_temperature").closest(".card").find(".collapse").addClass("show");
                    $("#vital_report_temperature").focus();
                    return false;
                } else if(($("#vital_report_temperature").val() < 24 || $("#vital_report_temperature").val() > 43) && $("#vital_report_temperature_type").val() == "2") {
                    $("#temperature_error").html("Temperature cannot be lesser than 24 nor greater than 43").show();
                    $("#vital_report_temperature").closest(".card").find(".collapse").addClass("show");
                    $("#vital_report_temperature").focus();
                    return false;
                }
            }
            $(".btns-hide").hide();
            $(".loader-img").show();
            $.ajax({
                type: 'POST',
                dataType: 'json',
                data: $("#patient-vitals-form").serialize(),
                url: site_url + "patient/save_vital",
                success: function (data) {
                    if (data.error) {
                        $("#vitals_server_side_error").html(data.error);
                        $("#vitals_server_side_error").addClass("alert alert-danger");
                        $("#vitals_server_side_error").show();
                        $(".loader-img").hide();
                        $(".btns-hide").show();
                        $('html, body').animate({
                            scrollTop: $("#vitals_server_side_error").offset().top - 100
                        }, 1000);
                    }
                    if(data.status == true) {
                        window.location.href = site_url + "patient/vitals";
                    }
                }
            });
        },
        highlight: function (element) {
            
        },
        invalidHandler: function(e,validator) {
            for (var i in validator.errorMap) {
                $("#"+i).closest(".card").find(".collapse").addClass("show");
            }
        },
        unhighlight: function (element) {
            
        }
    }); 
    function ajax_graph_data() {
        $.ajax({
            type: 'POST',
            dataType: 'json',
            data: {"start_date": $("#start_date").val(), "end_date": $("#end_date").val()},
            url: site_url + "patient/vital_graph_data",
            success: function (data) {
                graph_data = data.graph_data;
                if(graph_data.length > 0){
                    google.charts.load('current', {'packages':['corechart']});
                    google.charts.setOnLoadCallback(drawChart);
                    $(".no-graph-data").hide();
                    $(".graph-data-show").show();
                } else {
                    $(".no-graph-data").show();
                    $(".graph-data-show").hide();
                }
            }
        });
    }
    function drawChart() {
        var vAxisLabel = "";
        var field_name = $(".vitals_tab.active").attr("field_name");
        if(field_name == 'weight'){
            vAxisLabel = "Wieght";
        } else if(field_name == 'pulse') {
            vAxisLabel = "Pulse Rate";
        } else if(field_name == 'resp_rate') {
            vAxisLabel = "Resp. Rate";
        } else if(field_name == 'spo2') {
            vAxisLabel = "SpO2";
        } else if(field_name == 'bloodpressure') {
            vAxisLabel = "Blood Pressure";
        } else if(field_name == 'temperature') {
            vAxisLabel = "Temperature";
        }
        if(field_name == 'bloodpressure') {
            var graph_values = [['Date', 'Systolic', 'Diastolic']];
        } else {
            var graph_values = [['Date', vAxisLabel]];
        }
        var labels = [];
        $.each(graph_data, function(k,val) {
            if(field_name == 'weight' && val.vital_report_weight != "" && val.vital_report_weight != null) {
                graph_values.push([val.vital_report_date, parseInt(val.vital_report_weight)]);
            }
            if(field_name == 'pulse' && val.vital_report_pulse != "" && val.vital_report_pulse != null) {
                graph_values.push([val.vital_report_date, parseInt(val.vital_report_pulse)]);
            }
            if(field_name == 'resp_rate' && val.vital_report_resp_rate != "" && val.vital_report_resp_rate != null) {
                graph_values.push([val.vital_report_date, parseInt(val.vital_report_resp_rate)]);
            }
            if(field_name == 'spo2' && val.vital_report_spo2 != "" && val.vital_report_spo2 != null) {
                graph_values.push([val.vital_report_date, parseInt(val.vital_report_spo2)]);
            }
            if(field_name == 'bloodpressure' && val.vital_report_bloodpressure_systolic != "" && val.vital_report_bloodpressure_systolic != null) {
                graph_values.push([val.vital_report_date, parseInt(val.vital_report_bloodpressure_systolic),parseInt(val.vital_report_bloodpressure_diastolic)]);
            }
            if(field_name == 'temperature' && val.vital_report_temperature != "" && val.vital_report_temperature != null) {
                
                graph_values.push([val.vital_report_date, parseInt(val.vital_report_temperature)]);
            }
        });
        if(graph_values.length ==1) {
        $(".no-graph-data").hide();
            $(".no-graph-data").show();
            $(".graph-data-show").hide();
            return false;
        } else {
            $(".graph-data-show").show();
        }
        var data = google.visualization.arrayToDataTable(graph_values);
        var options = {
            title: '',
            curveType: 'function',
            legend: { position: 'none' },
            hAxis: {
              title: 'Date'
            },
            vAxis: {
              title: vAxisLabel
            }, 
            chartArea: {
                left: 40,
                top: 10,
                width: '100%',
                height: '70%'
            },
            pointsVisible: true
        };
        var chart = new google.visualization.LineChart(document.getElementById('vitalsGraph'));
        chart.draw(data, options);
    }
    $(window).resize(function(){
        drawChart();
    });
    $(".vitals_tab").click(function(){
        $(".vitals_tab").removeClass("active");
        $(this).addClass("active");
        drawChart();
    });
    $("#filter_graph_data").click(function() {
        ajax_graph_data();
    });
    if(contro_name == 'vitals') {
        ajax_graph_data();
    }
    $("body").on("click", ".edit_vital", function() {
        if($(this).attr("vital_report_id") != "") {
            window.location.href = site_url + "patient/edit_vital/" + $(this).attr("vital_report_id");
        }
    });
});
function FCConvert(input, unitType) {
    if(input == ''){
        return '';
    }
    var temperature = input;
    if (unitType == "2") {
        //celsius code
        var result = ((temperature - 32) / 1.8).toFixed(2);
        if (!isNaN(result)) {
            return result;
        }
        return '';
    } else {
        //fahrenheit code                 
        var result = ((temperature * 1.8) + 32).toFixed(2);
        if (!isNaN(result)) {
            return result;
        }
        return '';
    }
}
var columns = [];
function getVitalDT() {
    $('#vitals_datatable').DataTable({
        processing: true,
        serverSide: true,
        "bLengthChange": false,
        "order": [[ 0, "desc" ]],
        "pageLength": 6,
        "searching": false,
        "info": false,
        "ajax": {
            "url": site_url + "patient/vital_data",
            "type": "POST",
            'data': function (d) {
            },
            "dataSrc": function (json) {
                var columnNames = json.columns;
                if(columnNames.length == 1){
                    $("#vitals_datatable").hide();
                    $("#vitals_datatable_wrapper").hide();
                    $(".no-vitals-data").show();
                }
                var tcols = '<tr>';
                for (var i in columnNames) {
                    tcols += '<th class="vital-th text-center">'+columnNames[i]+'</th>';
                }
                tcols += '</tr>';
                $(".tcols").html(tcols);
                return json.data;
            }
        }
    });
}
$(document).ready(function() {
    if(contro_name == 'vitals') {
        getVitalDT();
    }
});