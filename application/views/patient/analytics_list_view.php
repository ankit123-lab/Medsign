<?php $this->load->view('patient/header_layout'); ?>

<body data-spy="scroll" data-target=".header" data-offset="50">
    <!-- Page loader -->
    <div id="preloader"></div>
    <!-- header section start -->
    <?php
    $this->load->view('patient/header');
    ?>
    <section class="ptb-90 main-section report-page">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <?php
                        $this->load->view('patient/page_title',['title' => $breadcrumbs]);
                    ?>
                </div>
                <div class="col-lg-12 btn-m-0">
                    <div class="contact-form">
                        <?php if ($this->session->userdata('message') != '') : ?>
                            <div class="alert alert-success" id="message">
                                <strong>Success!</strong> <?php echo $this->session->userdata('message'); ?>
                            </div>
                        <?php endif; ?>
                        <div class="row">
                             <div class="col-lg-12">
                                <label>Select Period</label>
                            </div>
                        </div>
                        <div class="row btns-row"> 
                            <div class="col-6 col-lg-3">
                                <div class="form-group">
                                    <label>From</label>
                                    <input type="text" value="<?= $start_date; ?>" placeholder="Start Date" name="start_date" id="start_date" class="form-control">
                                </div>
                            </div>
                            <div class="col-6 col-lg-3">
                                <div class="form-group">
                                    <label>To</label>
                                    <input type="text" value="<?= $end_date ?>" placeholder="End Date" name="end_date" id="end_date" class="form-control">
                                </div>
                            </div>
                            <div class="col-lg-3 col-6 btn-margin-top">
                                <button type="button" id="filter_graph_data" name="Filter">Filter</button>
                                <img class="loader-img" style="display: none;" src="<?= site_url(); ?>assets/admin/images/ajax-loader.gif">
                            </div>
                            <div class="col-lg-3 col-6 text-right btn-margin-top">
                                <a href="<?= site_url('patient/add_analytics'); ?>" class="btns add-report-btn">Add Analytics</a>
                            </div>
                        </div>
                        <div class="row btns-row"> 
                            <div class="col-lg-12 col-12">
                                <a href="javascript:void(0);" analytic_id="vital" field_name="weight" class="analytics-btn btns weight-tab active">Weight</a>
                                <a href="javascript:void(0);" analytic_id="vital" field_name="pulse" class="analytics-btn btns">Pulse Rate</a>
                                <a href="javascript:void(0);" analytic_id="vital" field_name="resp_rate" class="analytics-btn btns">Resp. Rate</a>
                                <a href="javascript:void(0);" analytic_id="vital" field_name="spo2" class="analytics-btn btns">SpO<sub>2</sub></a>
                                <a href="javascript:void(0);" analytic_id="vital" field_name="bloodpressure" class="analytics-btn btns">Blood Pressure</a>
                                <a href="javascript:void(0);" analytic_id="vital" field_name="temperature" class="analytics-btn btns">Temperature</a>
                            </div>
                            <div class="col-lg-12 col-12 m_bottom_15 analytics_tabs">
                                
                            </div>
                        </div>
                        <div class="row btns-row uas7-graph-btn hide-element"> 
                            <div class="col-lg-12 col-12 m_bottom_15">
                                <a href="javascript:void(0);" field_name="daily" class="uas_graph_tab btns btns-small active"><i class="fa fa-sun-o" aria-hidden="true"></i> Daily</a>
                                <a href="javascript:void(0);" field_name="weekly" class="uas_graph_tab btns btns-small"><i class="fa fa-calendar" aria-hidden="true"></i> Weekly</a>
                            </div>
                        </div>
                        <div class="row graph-data-show"> 
                            <!-- <div class="col-lg-12 col-12"> -->
                            <div class="col-lg-12">
                                <div id="analyticsGraph"></div>
                            </div>
                            <!-- </div> -->
                        </div>
                        <div class="row no-graph-data" style="display: none;"> 
                            <div class="col-lg-12 col-12 text-center">
                                <h4 style="margin-top: 15px;">No graph data found</h4>
                            </div>
                        </div>
                        <div class="row"> 
                            <div class="col-lg-12 col-12 table-responsive">
                                <table id="analytics_table" class="table table-striped table-bordered table-responsive-lg" style="width: 100%;">
                                    <thead class="tcols">
                                        
                                    </thead>
                                    <tbody>
                                        
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="row no-analytics-data" style="display: none;"> 
                            <div class="col-lg-12 col-12 text-center">
                                <h4 style="margin-top: 15px;">No table data found</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <style type="text/css">
        .analytics-btn {
            padding: 2px 10px!important;
            margin-top: 5px!important;
        }
    </style>
    <?php $this->load->view('patient/footer'); ?>
    <?php $this->load->view('patient/footer_layout'); ?>
<script type="text/javascript">
    $("#message").delay(5000).slideUp(300);
</script>
