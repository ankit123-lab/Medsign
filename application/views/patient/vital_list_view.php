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
                            </div>
                            <div class="col-lg-3 col-6 text-right btn-margin-top">
                                <a href="<?= site_url('patient/add_vital'); ?>" class="btns add-report-btn">Add Vitals</a>
                            </div>
                        </div>
                        <div class="row btns-row"> 
                            <div class="col-lg-12 col-12 m_bottom_15">
                                <a href="javascript:void(0);" field_name="weight" class="vitals_tab btns active">Weight</a>
                                <a href="javascript:void(0);" field_name="pulse" class="vitals_tab btns">Pulse Rate</a>
                                <a href="javascript:void(0);" field_name="resp_rate" class="vitals_tab btns">Resp. Rate</a>
                                <a href="javascript:void(0);" field_name="spo2" class="vitals_tab btns">SpO<sub>2</sub></a>
                                <a href="javascript:void(0);" field_name="bloodpressure" class="vitals_tab btns">Blood Pressure</a>
                                <a href="javascript:void(0);" field_name="temperature" class="vitals_tab btns">Temperature</a>
                            </div>
                        </div>
                        <div class="row graph-data-show"> 
                            <!-- <div class="col-lg-12 col-12"> -->
                            <div class="col-lg-12">
                                <div id="vitalsGraph"></div>
                            <!-- <div style="width:100%;"></div> -->
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
                                <table id="vitals_datatable" class="table table-striped table-bordered table-responsive-lg" style="width: 100%;">
                                    <thead class="tcols">
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                    </thead>
                                </table>
                            </div>
                        </div>
                        <div class="row no-vitals-data" style="display: none;"> 
                            <div class="col-lg-12 col-12 text-center">
                                <h4 style="margin-top: 15px;">No vitals data found</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <?php $this->load->view('patient/footer'); ?>
    <?php $this->load->view('patient/footer_layout'); ?>
<script type="text/javascript">
    $("#message").delay(5000).slideUp(300);
</script>