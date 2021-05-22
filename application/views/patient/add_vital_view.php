<?php $this->load->view('patient/header_layout'); ?>

<body data-spy="scroll" data-target=".header" data-offset="50">
    <!-- Page loader -->
    <div id="preloader"></div>
    <!-- header section start -->
    <?php
    $this->load->view('patient/header');
    ?>
    <section class="ptb-90 main-section">
        <div class="container">
            <?php
                $this->load->view('patient/page_title',['title' => $breadcrumbs]);
            ?>
            <div class="row">
                <div class="col-lg-6 offset-lg-3">
                    <div class="contact-form">
                        <center>
                            <div id="vitals_server_side_error"></div>
                        </center>
                        <form id="patient-vitals-form" action="<?= site_url("patient/save_vital"); ?>" method="post" novalidate="novalidate">
                            <input type="hidden" name="vital_report_id" id="vital_report_id" value="<?= !empty($vitals['vital_report_id']) ? $vitals['vital_report_id'] : ''?>">
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="accordion md-accordion" id="accordionEx" role="tablist" aria-multiselectable="true">
                                        <div class="card card_m_bottom">
                                            <div class="card-header" role="tab" id="headingOne1">
                                                <a data-toggle="collapse" data-parent="#accordionEx" href="#collapseOne1" aria-expanded="true"
                                                aria-controls="collapseOne1">
                                                    <label>Date <span class="error">*</span><span class="vitals_val" id="vital_report_date_val"><?= !empty($vitals['vital_report_date']) ? date("d/m/Y", strtotime($vitals['vital_report_date'])) : ''?></span></label>
                                                </a>
                                            </div>
                                            <div id="collapseOne1" class="collapse show" role="tabpanel" aria-labelledby="headingOne1"
                                              data-parent="#accordionEx">
                                                <div class="card-body">
                                                    <input type="text" class="name form-control" name="vital_report_date" value="<?= !empty($vitals['vital_report_date']) ? date("d/m/Y", strtotime($vitals['vital_report_date'])) : ''?>" id="vital_report_date" placeholder="Date" aria-required="true" aria-invalid="true" autocomplete="off">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card card_m_bottom">
                                            <div class="card-header" role="tab" id="headingWeight">
                                                <a data-toggle="collapse" data-parent="#accordionEx" href="#collapseWeight" aria-expanded="false"
                                                aria-controls="collapseWeight">
                                                    <label>Weight(kg)<span class="vitals_val" id="vital_report_weight_val"><?= !empty($vitals['vital_report_weight']) ? $vitals['vital_report_weight'] : ''?></span></label>
                                                </a>
                                            </div>
                                            <div id="collapseWeight" class="collapse" role="tabpanel" aria-labelledby="headingWeight"
                                              data-parent="#accordionEx">
                                                <div class="card-body">
                                                    <input type="text" class="name form-control" name="vital_report_weight" id="vital_report_weight" value="<?= !empty($vitals['vital_report_weight']) ? $vitals['vital_report_weight'] : ''?>" placeholder="Weight(kg)" aria-required="true" aria-invalid="true">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card card_m_bottom">
                                            <div class="card-header" role="tab" id="headingPulse">
                                                <a data-toggle="collapse" data-parent="#accordionEx" href="#collapsePulse" aria-expanded="false"
                                                aria-controls="collapsePulse">
                                                    <label>Pulse Rate/Min<span class="vitals_val" id="vital_report_pulse_val"><?= !empty($vitals['vital_report_pulse']) ? $vitals['vital_report_pulse'] : ''?></span></label>
                                                </a>
                                            </div>
                                            <div id="collapsePulse" class="collapse" role="tabpanel" aria-labelledby="headingPulse"
                                              data-parent="#accordionEx">
                                                <div class="card-body">
                                                    <input type="text" class="name form-control" name="vital_report_pulse" id="vital_report_pulse" value="<?= !empty($vitals['vital_report_pulse']) ? $vitals['vital_report_pulse'] : ''?>" placeholder="Pulse Rate/Min" aria-required="true" aria-invalid="true">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card card_m_bottom">
                                            <div class="card-header" role="tab" id="headingRespRate">
                                                <a data-toggle="collapse" data-parent="#accordionEx" href="#collapseRespRate" aria-expanded="false"
                                                aria-controls="collapseRespRate">
                                                    <label>Resp.Rate/Min<span class="vitals_val" id="vital_report_resp_rate_val"><?= !empty($vitals['vital_report_resp_rate']) ? $vitals['vital_report_resp_rate'] : ''?></span></label>
                                                </a>
                                            </div>
                                            <div id="collapseRespRate" class="collapse" role="tabpanel" aria-labelledby="headingRespRate"
                                              data-parent="#accordionEx">
                                                <div class="card-body">
                                                    <input type="text" class="name form-control" name="vital_report_resp_rate" id="vital_report_resp_rate" value="<?= !empty($vitals['vital_report_resp_rate']) ? $vitals['vital_report_resp_rate'] : ''?>" placeholder="Resp.Rate/Min" aria-required="true" aria-invalid="true">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card card_m_bottom">
                                            <div class="card-header" role="tab" id="headingSpo2">
                                                <a data-toggle="collapse" data-parent="#accordionEx" href="#collapseSpo2" aria-expanded="false"
                                                aria-controls="collapseSpo2">
                                                    <label>Sp0<sub>2</sub>(%)<span class="vitals_val" id="vital_report_spo2_val"><?= !empty($vitals['vital_report_spo2']) ? $vitals['vital_report_spo2'] : ''?></span></label>
                                                </a>
                                            </div>
                                            <div id="collapseSpo2" class="collapse" role="tabpanel" aria-labelledby="headingSpo2"
                                              data-parent="#accordionEx">
                                                <div class="card-body">
                                                    <input type="text" class="name form-control" name="vital_report_spo2" id="vital_report_spo2" value="<?= !empty($vitals['vital_report_spo2']) ? $vitals['vital_report_spo2'] : ''?>" placeholder="Sp02(%)" aria-required="true" aria-invalid="true">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card card_m_bottom">
                                            <div class="card-header" role="tab" id="headingBloodPressure">
                                                <a data-toggle="collapse" data-parent="#accordionEx" href="#collapseBloodPressure" aria-expanded="false"
                                                aria-controls="collapseBloodPressure">
                                                    <label>Blood Pressure(mm Hg)<span class="vitals_val" id="bloodpressure_val"><?= !empty($vitals['vital_report_bloodpressure_systolic']) ? $vitals['vital_report_bloodpressure_systolic'] : ''?>
                                                    <?= (!empty($vitals['vital_report_bloodpressure_systolic']) || !empty($vitals['vital_report_bloodpressure_diastolic'])) ? '/' : ''?>
                                                        <?= !empty($vitals['vital_report_bloodpressure_diastolic']) ? $vitals['vital_report_bloodpressure_diastolic'] : ''?>
                                                    </span></label>
                                                </a>
                                            </div>
                                            <div id="collapseBloodPressure" class="collapse" role="tabpanel" aria-labelledby="headingBloodPressure"
                                              data-parent="#accordionEx">
                                                <div class="card-body">
                                                    <div class="row">
                                                        <div class="col-6 col-lg-6">
                                                            <div class="form-group">
                                                                <label>Systolic</label>
                                                                <input type="text" class="name form-control" name="vital_report_bloodpressure_systolic" id="vital_report_bloodpressure_systolic" value="<?= !empty($vitals['vital_report_bloodpressure_systolic']) ? $vitals['vital_report_bloodpressure_systolic'] : ''?>" placeholder="Systolic" aria-required="true" aria-invalid="true">
                                                            </div>
                                                        </div>
                                                        <div class="col-6 col-lg-6">
                                                            <div class="form-group">
                                                            <label>Diastolic</label>
                                                            <input type="hidden" name="vital_report_bloodpressure_type" id="vital_report_bloodpressure_type" value="<?= !empty($vitals['vital_report_bloodpressure_type']) ? $vitals['vital_report_bloodpressure_type'] : '1'?>">
                                                            <input type="text" class="name form-control" name="vital_report_bloodpressure_diastolic" id="vital_report_bloodpressure_diastolic" value="<?= !empty($vitals['vital_report_bloodpressure_diastolic']) ? $vitals['vital_report_bloodpressure_diastolic'] : ''?>" placeholder="Diastolic" aria-required="true" aria-invalid="true">
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
                                                          </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <?php
                                            if(!empty($vitals['vital_report_temperature_type']))
                                                $t_type = $vitals['vital_report_temperature_type'];
                                            else
                                                $t_type = 1;
                                        ?>
                                        <div class="card card_m_bottom">
                                            <div class="card-header" role="tab" id="headingTemperature">
                                                <a data-toggle="collapse" data-parent="#accordionEx" href="#collapseTemperature" aria-expanded="false"
                                                aria-controls="collapseTemperature">
                                                    <label>Temperature<span class="vitals_val" id="vital_report_temperature_val"><?= !empty($vitals['vital_report_temperature']) ? $vitals['vital_report_temperature'] : ''?></span></label>
                                                </a>
                                                <span class="temperature_type" t_type="2" style="display: <?= ($t_type==1) ? 'inline;' : 'none;'?>">(℉)</span><span class="temperature_type" t_type="1" style="display: <?= ($t_type==2) ? 'inline;' : 'none;'?>">(℃)</span>
                                            </div>
                                            <div id="collapseTemperature" class="collapse" role="tabpanel" aria-labelledby="headingTemperature"
                                              data-parent="#accordionEx">
                                                <div class="card-body">
                                                    <div class="form-group">
                                                        <input type="hidden" name="vital_report_temperature_type" id="vital_report_temperature_type" value="<?= !empty($vitals['vital_report_temperature_type']) ? $vitals['vital_report_temperature_type'] : '1'?>">
                                                        <input type="hidden" name="vital_report_temperature_taken" id="vital_report_temperature_taken" value="<?= !empty($vitals['vital_report_temperature_taken']) ? $vitals['vital_report_temperature_taken'] : '6'?>">
                                                        <input type="text" class="name form-control" name="vital_report_temperature" id="vital_report_temperature" value="<?= !empty($vitals['vital_report_temperature']) ? $vitals['vital_report_temperature'] : ''?>" placeholder="Temperature" aria-required="true" aria-invalid="true">
                                                        <label id="temperature_error" style="display: none;" class="error" for="vital_report_temperature"></label>
                                                        <div class="btn-group" style="float: right;">
                                                            <a href="javascript:void(0);" class="dropdown-toggle" data-toggle="dropdown"><span class="temperature_taken_txt"><?= !empty($vitals['vital_report_temperature_taken']) ?  temperature_taken($vitals['vital_report_temperature_taken']) :  temperature_taken(6); ?></span><span class="caret"></span></a>
                                                            <ul class="dropdown-menu" style="left: -151px;">
                                                                <?php 
                                                                foreach (temperature_taken() as $key => $value) { ?>
                                                                    <li><a href="javascript:void(0);" class="temperature_taken" rel="<?= $key; ?>"><?= $value; ?></a></li>
                                                                    <li class="divider"></li>
                                                                <?php } ?>
                                                            </ul>
                                                          </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-12 m_top_15">
                                    <button class="btns-hide" type="submit" id="add_vitals_btn" value="Save" name="Save Vitals"><?= !empty($vitals['vital_report_id']) ? 'Edit' : 'Save'?></button>
                                    <?php if(!empty($vitals['vital_report_id'])) { ?>
                                    <a href="<?= site_url('patient/delete_vital/'.encrypt_decrypt($vitals['vital_report_id'],'encrypt')); ?>" onclick="return confirm('Are you sure to delete this vitals?');" class="btns btns-hide">Delete</a>
                                    <?php } ?>
                                    <a href="<?= site_url('patient/vitals'); ?>" class="btns btns-hide">Cancel</a>
                                    <img class="loader-img" style="display: none;" src="<?= site_url(); ?>assets/admin/images/ajax-loader.gif">
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <?php $this->load->view('patient/footer'); ?>
    <?php $this->load->view('patient/footer_layout'); ?>
