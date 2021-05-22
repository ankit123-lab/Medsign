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
                    <div class="alert alert-success success-msg" style="display: none;">
                        <strong>Success!</strong>
                    </div>
                    <?php if (!empty($this->session->userdata('message'))) : ?>
                        <div class="alert alert-success">
                            <strong>Success!</strong> <?php echo $this->session->userdata('message'); ?>
                        </div>
                    <?php endif; ?>
                    <?php if (!empty($this->session->userdata('error'))) : ?>
                        <div class="alert alert-danger">
                            <strong>Error!</strong> <?php echo $this->session->userdata('error'); ?>
                        </div>
                    <?php endif; ?>
                    <?php if(!$is_plan_active) : ?>
                        <div class="alert alert-danger">
                            <strong>Payment!</strong> Your plan is expired. Click <a class="payment_popup payment_popup_link" href="javascript:void(0);" utility_name="diary">here</a> for renew.
                        </div>
                    <?php endif; ?>
                </div>
                <?php if(empty($last_para)) { ?>
                <div class="col-lg-6 offset-lg-3">
                    <div class="contact-form">
                        <form name="start-uas7-form" id="start-uas7-form" action="<?= site_url("patient/add_uas7_para"); ?>" method="post">
                            <input type="hidden" name="doctor_id" id="doctor_id" value="<?= $patient_uas7_detail->doctor_id; ?>">
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="form-group">
                                        <label>Start date <span class="error">*</span></label>
                                        <input type="text" class="name form-control" name="diary_start_date" value="" id="diary_start_date" placeholder="Start date" aria-required="true" aria-invalid="true" autocomplete="off">
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="form-group">
                                        <label>Doctor name <span class="error">*</span></label>
                                        <input type="text" class="name form-control" name="doctor_name" value="<?= DOCTOR . ' ' .$patient_uas7_detail->doctor_name; ?>" id="doctor_name" placeholder="Doctor name" aria-required="true" aria-invalid="true" autocomplete="off">
                                        <span class="fa-icon-search-input search-doctors">
                                            <i class="fa fa-search" aria-hidden="true"></i>
                                        </span>
                                    </div>
                                </div>
                                <div class="col-lg-12 hide_other_data" style="display: none;">
                                    <div class="form-group">
                                        <label>Doctor email <span class="error"></span></label>
                                        <input type="text" class="name form-control" name="doctor_email" value="" id="doctor_email" placeholder="Doctor email" aria-required="true" aria-invalid="true" autocomplete="off">
                                    </div>
                                </div>
                                <div class="col-lg-12 hide_other_data" style="display: none;">
                                    <div class="form-group">
                                        <label>Doctor phone number <span class="error"></span></label>
                                        <input type="text" class="name form-control" name="doctor_phone_number" value="" id="doctor_phone_number" placeholder="Doctor phone number" aria-required="true" aria-invalid="true" autocomplete="off">
                                    </div>
                                </div>
                                <div class="col-lg-12 hide_other_data" style="display: none;">
                                    <div class="form-group">
                                        <label>Doctor Address <span class="error"></span></label>
                                        <textarea name="doctor_address"></textarea>
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <button type="submit" value="Save" id="start_btn" name="Start">Add Parameter</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <?php } else { ?>
                <div class="col-lg-12">
                    <div class="contact-form">
                        <div class="row btns-row m_bottom_15">
                            <div class="col-8 col-lg-8 padding_right_0">
                                <!-- <a href="javascript:void(0);" field_name="table" class="uas7_tab btns active">Tabuler</a>
                                <a href="javascript:void(0);" field_name="graph" class="uas7_tab btns">Graph</a> -->
                                <a href="javascript:void(0);" field_name="daily" class="graph_tab btns btns-small active"><i class="fa fa-sun-o" aria-hidden="true"></i> Daily</a>
                                <a href="javascript:void(0);" field_name="weekly" class="graph_tab btns btns-small"><i class="fa fa-calendar" aria-hidden="true"></i> Weekly</a>
                                <!-- <a href="javascript:void(0);" title="Save as Report" class="uas7-download-icon save_as_report">
                                    <i class="fa fa-floppy-o"></i>
                                </a> -->
                            </div>
                        <?php if($is_add_params && $is_plan_active) { ?> 
                            <div class="col-4 col-lg-4 text-right padding_left_0">
                                <a title="Add Parameter" href="<?= site_url('patient/add_date_list'); ?>" class="btns btns-small"><i class="fa fa-calendar-plus-o" aria-hidden="true"></i> Add</a>
                            </div>
                        <?php } else { ?>
                            <div class="col-4 col-lg-4 text-right padding_left_0">
                                <a title="Edit Parameter" href="<?= site_url('patient/add_date_list'); ?>" class="btns btns-small"><i class="fa fa-calendar-plus-o" aria-hidden="true"></i> Edit</a>
                            </div>
                        <?php } ?>
                            <!-- <div class="col-6 col-lg-6">
                                <a href="javascript:void(0);" id="download_report" class="btns">Download Report</a>
                            </div> -->
                        </div>
                        <div class="row graph-data-show element-hide">
                            <div class="col-lg-12 graph-container">
                                <div style="width: 100%;" id="uas7DiaryGraph"></div>
                            </div>
                            <!-- <div class="col-lg-12 graph-container text-center">
                                <div>Date</div>
                            </div> -->
                            <div class="col-lg-12 col-12 text-center element-hide no-graph-data">
                                <h4 style="margin-top: 15px;">No graph data found</h4>
                            </div>
                        </div>
                        <div class="col-12 col-lg-12 padding_right_0 text-center">
                            <a href="<?= site_url('patient/uas7_download'); ?>" target="_blank" title="Download" class="uas7-download-icon btns btns-small"><i class="fa fa-download"></i> Download</a>
                            <a href="javascript:void(0);" title="Share" class="btns btns-small uas7-download-icon share_report_view"><i class="fa fa-share"></i> Share</a>
                        </div>
                        <div class="row tabuler-data">
                            <div class="col-lg-12 table-responsive element-hide">
                                <div class="uas7-form d-lg-block">
                                    <table id="uas7_table" class="table table-bordered table-uas7">
                                        <thead>
                                            <tr>
                                                <th width="10%" scope="col" class="field-white">
                                                    DATE
                                                </th>
                                                <th width="10%" scope="col" class="bg1">
                                                    WHEAL COUNT
                                                    <span>
                                                        NONE
                                                    </span>
                                                </th>
                                                <th width="10%" scope="col" class="bg2">
                                                    WHEAL COUNT
                                                    <span>
                                                        MILD - &lt;20 Wheals/24h
                                                    </span>
                                                </th>
                                                <th width="10%" scope="col" class="bg3">
                                                    WHEAL COUNT
                                                    <span>
                                                        MODERATE - 20-50 Wheals/24h
                                                    </span>
                                                </th>
                                                <th width="10%" scope="col" class="bg4">
                                                    WHEAL COUNT
                                                    <span>
                                                        INTENSE - &gt;50 Wheals/24h
                                                    </span>
                                                </th>
                                                <th width="2%" scope="col" class="sep">
                                                    &nbsp;
                                                </th>
                                                <th width="10%" scope="col" class="bg1">
                                                    PRURITUS
                                                    <span>
                                                        No prutitus
                                                    </span>
                                                </th>
                                                <th width="10%" scope="col" class="bg2">
                                                    PRURITUS
                                                    <span>
                                                        MILD - Present but not annoying or troublesome
                                                    </span>
                                                </th>
                                                <th width="10%" scope="col" class="bg3">
                                                    PRURITUS
                                                    <span>
                                                        MODERATE - Troublesome but does not interfere with normal daily activity or sleep
                                                    </span>
                                                </th>
                                                <th width="10%" scope="col" class="bg4">
                                                    PRURITUS
                                                    <span>
                                                        SEVERE - Troublesome to interfere with normal daily activity or sleep
                                                    </span>
                                                </th>
                                                <th width="10%" scope="col" class="field-white field-total">
                                                    TOTAL
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody class="uas7_para_list">
                                            
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <td colspan="10" class="total-label">UAS7 = </td>
                                                <td class="total-value uas7-total-score"></td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                            <div class="col-lg-12 table-responsive m_top_15 uas7_range element-hide">
                                <?php /*
                                <table class="table table-bordered">
                                    <tbody>
                                        <?php foreach ($uas7_range['all_range'] as $range) { ?>
                                        <tr>
                                            <td>
                                                <?= $range['dataLabel']?>
                                            </td>
                                        </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                                */ ?>
                                <ul class="text-left" style="list-style:disc; padding-inline-start:40px;"><li>The UAS consisted of the sum of the wheal (hive) number score and the itch (Pruritus) severity score.</li>  
                                <li>The wheal numbers are graded from 0 to 3 as follows: 0 - less than 10 small wheals (diameter, < 3 cm); 1- 10 to 50 small wheals or less than 10 large wheals (diameter, > 3 cm); 2 - greater than 50 small wheals or 10 to 50 large wheals; and 3 - almost the whole body is covered.</li>
                                <li>The severity of the itching is graded from 0 to 3 (0, none; 1, mild; 2, moderate; and 3, severe).</li><li>The UAS7 score is obtained as the sum of the daily average itch (pruritus)  and hive (Wheals)  scores over 7 days.</li> <li>Please consult your doctor for interpretation and further actions.</li> <li><small>Adapted from  Reference : Indian J Dermatol Venereol Leprol|July-August 2006|Vol 72|Issue 4</small></li></ul>
                            </div>
                        </div>
                        <div class="row element-hide">
                            <div class="col-lg-12">
                                <div class="pagination" id="pagination"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php } ?>
            </div>
        </div>
    </section>
    <!-- Payment Modal -->
    <div class="modal fade" id="paymentModal" tabindex="-1" role="dialog" aria-labelledby="paymentModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">

        </div>
    </div>
    <!-- End Payment Modal -->
    <!-- Payment Modal -->
    <div class="modal fade" id="shareUAS7ReportModal" tabindex="-1" role="dialog" aria-labelledby="shareUAS7ReportModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
        </div>
    </div>
    <!-- End Payment Modal -->
    <?php $this->load->view('patient/footer'); ?>
    <?php $this->load->view('patient/footer_layout'); ?>
<script type="text/javascript">
    $("#message").delay(5000).slideUp(300);
</script>
