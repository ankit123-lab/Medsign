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
                    <form name="analyticsFrom" id="analyticsFrom">
                        <input type="hidden" name="temperature_type" id="temperature_type" value="1">
                        <input type="hidden" name="temperature_taken" id="temperature_taken" value="6">
                        <input type="hidden" name="bloodpressure_type" id="bloodpressure_type" value="1">
                        <input type="hidden" name="vital_report_id" id="vital_report_id" value="">
                        <input type="hidden" name="health_analytics_report_id" id="health_analytics_report_id" value="">
                        <div class="contact-form">
                            <center>
                                <div id="analytic_error"></div>
                            </center>
                            <div class="row">
                                <div class="col-lg-3">
                                    <div class="form-group">
                                        <label>Date</label>
                                        <input type="text" class="name form-control" name="analytic_date" value="<?= get_display_date_time("d/m/Y"); ?>" id="analytic_date" placeholder="Date" aria-required="true" aria-invalid="true" autocomplete="off">
                                        <label id="analytic_date-error" class="error hide-element">The date is required.</label>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-3">
                                    <div class="form-group">
                                        <label>Weight(kg)</label>
                                        <input type="text" minVal="1" maxVal="200" class="name form-control vital-val" name="weight" id="weight" value="" placeholder="" aria-required="true" aria-invalid="true">
                                        <label id="weight-error" class="error hide-element"></label>
                                    </div>
                                </div>
                                <div class="col-lg-3">
                                    <div class="form-group">
                                        <label>Pulse Rate/Min</label>
                                        <input type="text" minVal="10" maxVal="500" class="name form-control vital-val" name="pulse_rate" id="pulse_rate" value="" placeholder="" aria-required="true" aria-invalid="true">
                                        <label id="pulse_rate-error" class="error hide-element"></label>
                                    </div>
                                </div>
                                <div class="col-lg-3">
                                    <div class="form-group">
                                        <label>Resp.Rate/Min</label>
                                        <input type="text" minVal="10" maxVal="70" class="name form-control vital-val" name="resp_rate" id="resp_rate" value="" placeholder="" aria-required="true" aria-invalid="true">
                                        <label id="resp_rate-error" class="error hide-element"></label>
                                    </div>
                                </div>
                                <div class="col-lg-3">
                                    <div class="form-group">
                                        <label>SpO<sub>2</sub>(%)</label>
                                        <input type="text" minVal="1" maxVal="100" class="name form-control vital-val" name="spo2" id="spo2" value="" placeholder="" aria-required="true" aria-invalid="true">
                                        <label id="spo2-error" class="error hide-element"></label>
                                    </div>
                                </div>
                                <div class="col-lg-3">
                                    <div class="form-group">
                                        <label>BP Systolic(mm Hg)</label>
                                        <input type="text" minVal="50" maxVal="300" class="name form-control vital-val" name="systolic" id="systolic" value="" placeholder="" aria-required="true" aria-invalid="true">
                                        <label id="systolic-error" class="error hide-element"></label>
                                    </div>
                                </div>
                                <div class="col-lg-3">
                                    <div class="form-group">
                                        <label>BP Diastolic(mm Hg)</label>
                                        <input type="text" minVal="25" maxVal="200" class="name form-control vital-val" name="diastolic" id="diastolic" value="" placeholder="" aria-required="true" aria-invalid="true">
                                        <div class="btn-group" style="float: right;">
                                            <a href="javascript:void(0);" class="dropdown-toggle" data-toggle="dropdown"><span class="bloodpressure_type_txt"><?= !empty($vitals['vital_report_bloodpressure_type']) ?  bloodpressure_type($vitals['vital_report_bloodpressure_type']) :  bloodpressure_type(1); ?></span><span class="caret"></span></a>
                                            <ul class="dropdown-menu" style="left: -104px;">
                                                <?php 
                                                foreach (bloodpressure_type() as $key => $value) { ?>
                                                    <li><a href="javascript:void(0);" class="bloodpressure_type" rel="<?= $key; ?>"><?= $value; ?></a></li>
                                                    <li class="divider"></li>
                                                <?php } ?>
                                            </ul>
                                        </div>
                                        <label id="diastolic-error" class="error hide-element"></label>
                                    </div>
                                </div>
                                <div class="col-lg-3">
                                    <div class="form-group">
                                        <?php
                                            $t_type = 1;
                                        ?>
                                        <label>Temperature <span class="temperature_types" t_type="2" style="display: <?= ($t_type==1) ? 'inline;' : 'none;'?>">(℉)</span><span class="temperature_types" t_type="1" style="display: <?= ($t_type==2) ? 'inline;' : 'none;'?>">(℃)</span></label>
                                        <input type="text" minCVal="24" maxCVal="43" minVal="75.2" maxVal="109.4" class="name form-control vital-val" name="temperature" id="temperature" value="" placeholder="" aria-required="true" aria-invalid="true">
                                        <div class="btn-group" style="float: right;">
                                            <a href="javascript:void(0);" class="dropdown-toggle" data-toggle="dropdown"><span class="temperature_taken_txt"><?= temperature_taken(6); ?></span><span class="caret"></span></a>
                                            <ul class="dropdown-menu" style="left: -151px;">
                                                <?php 
                                                foreach (temperature_taken() as $key => $value) { ?>
                                                    <li><a href="javascript:void(0);" class="temperature_taken" rel="<?= $key; ?>"><?= $value; ?></a></li>
                                                    <li class="divider"></li>
                                                <?php } ?>
                                            </ul>
                                        </div>
                                        <label id="temperature-error" class="error hide-element"></label>
                                    </div>
                                </div>
                            </div>
                            <div class="row analytics_params">
                                <?php foreach ($patient_analytics as $key => $value) { ?>
                                    <div class="col-lg-3">
                                        <div class="form-group">
                                            <label><?= $value['name']; ?></label>
                                            <input type="text" minVal="<?= $value['min']; ?>" maxVal="<?= $value['max']; ?>" class="name form-control analytics-txt analytics_id_<?= $value['id']; ?>" name="analytics[<?= $value['id']; ?>][value]" value="" placeholder="" aria-required="true" aria-invalid="true">
                                            <label id="analytic_<?= $value['id']; ?>-error" class="error hide-element"></label>
                                            <input type="hidden" name="analytics[<?= $value['id']; ?>][name]" value="<?= $value['name']; ?>">
                                            <input type="hidden" name="analytics[<?= $value['id']; ?>][precise_name]" value="<?= $value['precise_name']; ?>">
                                        </div>
                                    </div>
                                <?php } ?>
                            </div>
                            <div class="row">
                                <div class="col-lg-3 text-center text-lg-left">
                                    <a href="javascript:void(0);" class="analytics_popup">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
                                            <path d="M12 0c-6.627 0-12 5.373-12 12s5.373 12 12 12 12-5.373 12-12-5.373-12-12-12zm6 13h-5v5h-2v-5h-5v-2h5v-5h2v5h5v2z" fill="currentColor"></path>
                                        </svg>
                                    </a>
                                </div>
                            </div>
                            <textarea name="patient_analytics" id="patient_analytics" style="display: none;"></textarea>
                            <div class="update-btn">
                                <button type="button" class="<?= (empty($patient_analytics)) ? 'hide-element' : ''; ?>" id="save_analytics" name="submit">Save</button>
                                <img class="loader-img" style="display: none;" src="<?= site_url(); ?>assets/admin/images/ajax-loader.gif">
                                <a href="<?= site_url('patient/analytics_list'); ?>" class="btns">Cancel</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
    <!-- Patient List Modal -->
    <div class="modal fade" id="analyticsListModal" tabindex="-1" role="dialog" aria-labelledby="analyticsListModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">

        </div>
    </div>
    <!-- End Patient List Modal -->

    <?php $this->load->view('patient/footer'); ?>
    <?php $this->load->view('patient/footer_layout'); ?>
