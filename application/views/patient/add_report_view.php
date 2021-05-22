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
                        <form id="patient-report-form" action="<?= site_url("patient/add_report"); ?>" enctype="multipart/form-data" method="post" novalidate="novalidate">
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="form-group">
                                        <label>Report Name <span class="error">*</span></label>
                                        <input type="text" class="name form-control" name="report_name" value="<?= $report_name; ?>" placeholder="Report Name" aria-required="true" aria-invalid="true">
                                        <?php if (form_error('report_name')) { ?>
                                            <label class="error"><?= form_error('report_name') ?></label>
                                        <?php } ?>
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="form-group">
                                        <label>Type Of Report <span class="error">*</span></label>
                                        <select class="select2 md-form form-control" name="type_of_report">
                                            <option value="">Select Report Type</option>
                                            <?php
                                            foreach ($report_types as $key => $value) { ?>
                                                <option <?= ($type_of_report == $value->report_type_id) ? 'selected' : ''; ?> value="<?= $value->report_type_id; ?>"><?= $value->report_type_name; ?></option>
                                            <?php } ?>
                                        </select>
                                        <?php if (form_error('type_of_report')) { ?>
                                            <label class="error"><?= form_error('type_of_report') ?></label>
                                        <?php } ?>
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="form-group">
                                        <label>Date Of Report <span class="error">*</span></label>
                                        <input type="text" class="name form-control" name="date_of_report" value="<?= $date_of_report; ?>" id="date_of_report" placeholder="Date Of Report" aria-required="true" aria-invalid="true" autocomplete="off">
                                        <?php if (form_error('date_of_report')) { ?>
                                            <label class="error"><?= form_error('date_of_report') ?></label>
                                        <?php } ?>
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="form-group">
                                        <label>Select Report (image,pdf) <span class="error">*</span></label>
                                        <input type="file" name="report_file[]" multiple="multiple" class="form-control">
                                        <?php if (!empty($report_file_error)) { ?>
                                            <label class="error"><?= $report_file_error; ?></label>
                                        <?php } ?>
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <button type="submit" id="add_report_btn" value="Save" name="Save Report">Save</button>
                                    <a href="<?= site_url('patient/report'); ?>" class="btns">Cancel</a>
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
