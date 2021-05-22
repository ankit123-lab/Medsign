var graph_data = [];
var weekly_graph_data = [];
var daily_graph_data = [];
var uas7_graph_range = [];
$(document).ready(function () {
	$('#diary_start_date').mask("00/00/0000", {placeholder: "DD/MM/YYYY"});
	$('#diary_start_date').datepicker({
    	autoclose: true,
        todayHighlight: true,
        startDate: '-6d',
        endDate: '+0d',
        format: 'dd/mm/yyyy'
    });
    $("#start-uas7-form").validate({
        rules: {
            diary_start_date: {
                required: true
            },
            doctor_name: {
                required: true
            }
        },
        messages: {
            diary_start_date: "The start date field is required.",
            doctor_name: "The doctor name field is required."
        },
        submitHandler: function(form) { 
            $("#start_btn").attr('disabled', true);
            form.submit();
        },
        highlight: function (element) {
            
        },
        unhighlight: function (element) {
            
        }
    });
    
    function loadPagination(pagno) {
        $.ajax({
            url: site_url + "patient/uas7_para_list/" + pagno,
            type: 'post',
            // data: {},
            dataType: 'json',
            beforeSend: function (result) {
                
            },
            success: function (result) {
                $("tbody.uas7_para_list").html(result.html_data);
                if(result.uas7_score != '') {
                    $(".uas7-total-score").html(result.uas7_score);
                    $(".uas7-total-score").parent().show();
                } else {
                    $(".uas7-total-score").parent().hide();
                }
                $("#pagination").html(result.links);

            }
        });
    }
    $("body").on('click','.page-link',function(e){
        e.preventDefault(); 
        var pageno = $(this).attr('data-ci-pagination-page');
        if(pageno != undefined){
            loadPagination(pageno);
        }
    });
    if(contro_name == "uas7diary") {
        graph_data_ajax();
        // loadPagination(0);
    }
    function graph_data_ajax() {
        $.ajax({
            url: site_url + "patient/uas7_para_graph_data",
            type: 'post',
            // data: {},
            dataType: 'json',
            beforeSend: function (result) {
                
            },
            success: function (result) {
                graph_data = result.uas7_daily_data;
                weekly_graph_data = result.weekly_graph_data;
                daily_graph_data = result.uas7_daily_data;
                uas7_graph_range = result.uas7_range;
                if(graph_data.length > 0){
                    google.charts.load('current', {'packages':['corechart']});
                    google.charts.setOnLoadCallback(drawChart);
                    $(".no-graph-data").addClass("element-hide");
                    $(".graph-data-show").removeClass("element-hide");
                } else {
                    $(".no-graph-data").removeClass("element-hide");
                    $(".graph-data-show").addClass("element-hide");
                    $(".graph-container").addClass("element-hide");
                }
            }
        });
    }
    function drawChart() {
        var field_name = $(".graph_tab.active").attr("field_name");
        if(field_name == 'daily') {
            var vAxisLabel = "UAS";
        } else {
            var vAxisLabel = "UAS7";
        }
        var graph_values = [['Date', vAxisLabel]];
        $.each(graph_data, function(k,val) {
            graph_values.push([val.patient_diary_date,val.uas7_score]);
            // graph_values.push(val.uas7_score);
        });
        // console.log(graph_values);
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

        var chart = new google.visualization.LineChart(document.getElementById('uas7DiaryGraph'));

        chart.draw(data, options);
        // var image_data = chart.getImageURI();
        // $("#chart_image_data").val(image_data.replace("data:", ""));
        // saveChartImage();
    }
    $(window).resize(function(){
        drawChart();
    });
    // $(".uas7_report_download").click(function() {
    //     saveChartImage(1);
    // });

    // function saveChartImage(action_type) {
    //     $.ajax({
    //         type: 'POST',
    //         dataType: 'json',
    //         data: $("#chart_image_data_form").serialize(),
    //         url: site_url + "patient/save_uas7_chart_image",
    //         success: function (data) {
    //             if (data.status == true) {
    //                 if(action_type == 1) {
    //                     window.open(site_url + "patient/uas7_download", '_blank');
    //                 }
    //             }
    //         }
    //     });
    // }
    $(".uas7_tab").click(function(){
        $(".uas7_tab").removeClass("active");
        $(this).addClass("active");
        if($(this).attr("field_name") == "graph") {
            graph_data_ajax();
            $(".tabuler-data").addClass("element-hide");
            $(".graph-data-show").removeClass("element-hide");
            $("#pagination").addClass("element-hide");
        } else {
            $(".tabuler-data").removeClass("element-hide");
            $(".graph-data-show").addClass("element-hide");
            $("#pagination").removeClass("element-hide");
        }
    });
    $(".graph_tab").click(function(){
        $(".graph_tab").removeClass("active");
        $(this).addClass("active");
        if($(this).attr("field_name") == "daily") {
            graph_data = daily_graph_data;
            $(".uas7_range").addClass("element-hide");
        } else {
            graph_data = weekly_graph_data; 
            $(".uas7_range").removeClass("element-hide");
        }
        if(graph_data.length > 0) {
            $(".graph-container").removeClass("element-hide");
            $(".no-graph-data").addClass("element-hide");
        } else {
            $(".no-graph-data").removeClass("element-hide");
            $(".graph-container").addClass("element-hide");
        }
        drawChart();
    });
    $(".wheal-body").click(function(){
        $(this).closest(".row").find(".wheal-body").removeClass("uas7-form-sm-active");
        $(this).addClass("uas7-form-sm-active");
        var row_no = $(this).closest(".row").attr("row");
        $("#wheal_count_" + row_no).val($(this).attr("wheal_count"));
    });
    $(".pruritus-body").click(function(){
        $(this).closest(".row").find(".pruritus-body").removeClass("uas7-form-sm-active");
        $(this).addClass("uas7-form-sm-active");
        var row_no = $(this).closest(".row").attr("row");
        $("#pruritus_count_" + row_no).val($(this).attr("pruritus_val"));
    });

    $(".wheal_count").click(function(){
        $(this).closest("tr").find(".wheal_count>.human-body").removeClass("active");
        $(this).find(".human-body").addClass("active");
        var row_no = $(this).closest("tr").attr("row");
        $("#wheal_count_" + row_no).val($(this).attr("wheal_count"));
        $(this).closest("tr").find(".total_count").html(parseInt($("#pruritus_count_" + row_no).val()) + parseInt($("#wheal_count_" + row_no).val()));
    });
    $(".pruritus_val").click(function(){
        $(this).closest("tr").find(".pruritus_val>.human-body").removeClass("active");
        $(this).find(".human-body").addClass("active");
        var row_no = $(this).closest("tr").attr("row");
        $("#pruritus_count_" + row_no).val($(this).attr("pruritus_val"));
        $(this).closest("tr").find(".total_count").html(parseInt($("#pruritus_count_" + row_no).val()) + parseInt($("#wheal_count_" + row_no).val()));

    });
    $("#save-uas7-form").validate({
        rules: {
            
        },
        messages: {
            
        },
        submitHandler: function(form) { 
            $(".btns-hide").hide();
            $(".loader-img").show();
            $.ajax({
                type: 'POST',
                dataType: 'json',
                data: $("#save-uas7-form").serialize(),
                url: site_url + "patient/save_uas7_para",
                success: function (data) {
                    if (data.error) {
                        
                    }
                    $("#doctor_id").val(data.doctor_id);
                    if(data.status == true && data.is_redirect == "1") {
                        window.location.href = site_url + "patient/uas7diary";
                    }
                }
            });
        },
        highlight: function (element) {
            
        },
        unhighlight: function (element) {
            
        }
    });
    $(".last-tab-btn").click(function() {
        if($(this).closest(".card-body").find(".pruritus-body").hasClass("uas7-form-sm-active")){
            $(this).closest(".card-body").find(".uas7-errors").addClass("element-hide");
        } else {
            $(this).closest(".card-body").find(".uas7-errors").removeClass("element-hide");
            return false;
        }
        $("#is_redirect").val(1);
        $("#single_add_date").val($(this).attr("date"));
        $("#save-uas7-form").submit();
        $(this).attr("disabled", true);
    });
    $(".wheal-next-btn").click(function(){
        if($(this).closest(".card-body").find(".wheal-body").hasClass("uas7-form-sm-active")){
            $(this).closest(".card-body").find(".uas7-errors").addClass("element-hide");
        } else {
            $(this).closest(".card-body").find(".uas7-errors").removeClass("element-hide");
            return false;
        }
        $(this).parent().find(".element-hide").click();
    });
    $(".next-tab").click(function(){
        if($(this).closest(".card-body").find(".pruritus-body").hasClass("uas7-form-sm-active")){
            $(this).closest(".card-body").find(".uas7-errors").addClass("element-hide");
        } else {
            $(this).closest(".card-body").find(".uas7-errors").removeClass("element-hide");
            return false;
        }
        $("#single_add_date").val($(this).attr("date"));
        $("#is_redirect").val(0);
        $("#save-uas7-form").submit();
        var tab_no = $(this).attr("next_tab");
        $(".date-tab-"+tab_no).click();
    });
    $(".save-uas7-btn").click(function() {
        var is_error = false;
        $(".desktop_version tr").each(function(){
            if(!$(this).find(".wheal_count>.human-body").hasClass("active") && !$(this).find(".pruritus_val>.human-body").hasClass("active")){
                $(this).find(".total_count").html('<span class="uas7-errors">Select image</span>');
                is_error = true;
            } else if(!$(this).find(".wheal_count>.human-body").hasClass("active")) {
                $(this).find(".total_count").html('<span class="uas7-errors">Select wheal image</span>');
                is_error = true;
            } else if(!$(this).find(".pruritus_val>.human-body").hasClass("active")) {
                $(this).find(".total_count").html('<span class="uas7-errors">Select pruritus image</span>');
                is_error = true;
            }
        });
        if(is_error)
            return false;
        $("#is_redirect").val(1);
        $("#save-uas7-form").submit();
        $(this).attr("disabled", true);
    });
    var is_blur_doctor_txt = false;
    $(".search-doctors").click(function(){
        $.ajax({
            url: site_url + "patient/search_doctors",
            type: 'post',
            data: { query: $("#doctor_name").val() },
            dataType: 'json',
            beforeSend: function (result) {
                $("#doctor_id").val("");
                $(".hide_other_data").show();
                $(".doctor-search-list").remove();
                is_blur_doctor_txt = false;
            },
            success: function (result) {
                var docto_list = '<ul class="doctor-search-list typeahead dropdown-menu" role="listbox" style="top: 67px; left: 15px; display: block;">';
                $.each(result, function(k, val){
                    docto_list += '<li class=""><a class="dropdown-item" href="javascript:void(0);" doctor_id="'+val.Id+'" doctor_name="'+val.Name+'" role="option"><strong>'+val.Name+'</strong></a></li>';
                });
                docto_list += "</ul>"; 
                if(result.length > 0) {
                    $("#doctor_name").after(docto_list);
                    $("#doctor_name").focus();
                    setTimeout(function() {
                        is_blur_doctor_txt = true;
                        $(".doctor-search-list").show();
                    }, 600);
                }
            }
        });
    });
    $("body").on("click", ".doctor-search-list li a", function(){
        $("#doctor_id").val($(this).attr("doctor_id"));
        $("#doctor_name").val("Dr. " + $(this).attr("doctor_name"));
        $(".hide_other_data").hide();
        $(".doctor-search-list").hide();
    });
    $("#doctor_name").blur(function(){
        setTimeout(function() {
            if(is_blur_doctor_txt){
                $(".doctor-search-list").hide();
            }
        }, 500);
    });

    $(".share_report_view").click(function(){
        $.ajax({
            url: site_url + "patient/share_uas7_report_view",
            type: 'post',
            // data: { query: $("#doctor_name").val() },
            dataType: 'json',
            beforeSend: function (result) {
                $("#shareUAS7ReportModal > .modal-dialog").html("");
                $("#shareUAS7ReportModal").modal("show");
            },
            success: function (result) {
                $("#shareUAS7ReportModal > .modal-dialog").html(result.html);  
                init_share_uas7_report_frm();
            }
        });
    });
    function init_share_uas7_report_frm() {
        $("#share_uas7_report_frm").validate({
            rules: {
                share_doctor_name: {
                    required: true
                },
                share_doctor_email: {
                    required: true,
                    email: true
                }
            },
            messages: {
                share_doctor_name: "The name field is required.",
                share_doctor_email: {required: "The email field is required.",email: "Your email address is invalid."}
            },
            submitHandler: function(form) { 
                $.ajax({
                    url: site_url + "patient/share_uas7_report",
                    type: 'post',
                    data: $("#share_uas7_report_frm").serialize(),
                    dataType: 'json',
                    beforeSend: function (result) {
                        $("#share_uas7_report_btn").attr("disabled", true);
                        $(".share-error").parent().addClass("element-hide");
                    },
                    success: function (result) {
                        if(result.status == true){
                            $("#shareUAS7ReportModal").modal("hide");
                            $(".success-msg").html('<strong>Success!</strong> Report shared successfully.').show();
                            $(".success-msg").delay(5000).slideUp(300);
                        } else {
                            $("#share_uas7_report_btn").attr("disabled", false);
                            $(".share-error").html(result.errors);
                            $(".share-error").parent().removeClass("element-hide");
                        }
                    }
                });
            },
            highlight: function (element) {
                
            },
            unhighlight: function (element) {
                
            }
        });
    }
    $("body").on("click", "#share_uas7_report_btn", function() {
        $("#share_uas7_report_frm").submit();
    });
    $(".save_as_report").click(function(){
        $.ajax({
            url: site_url + "patient/save_as_report",
            type: 'post',
            // data: {},
            dataType: 'json',
            beforeSend: function (result) {
                $(".save_as_report").hide();
            },
            success: function (result) {
                $(".save_as_report").show();
                $(".success-msg").html('<strong>Success!</strong> Report saved successfully.').show();
                $(".success-msg").delay(5000).slideUp(300);
            }
        });
    });

});