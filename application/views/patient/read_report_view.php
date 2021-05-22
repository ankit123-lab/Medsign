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
            <h1 class="page-title">
                <?php echo $breadcrumbs; ?>
                <a href="<?= site_url('patient/report'); ?>" class="btns back-btn">Back</a>
            </h1>
            <div class="row">
                <div class="col-lg-12">
                    <div class="contact-form">
                        <div class="row">
                            <div class="col-lg-4">
                                <label><b>Report Name:</b> <?= $report->file_report_name; ?></label>
                            </div>
                            <div class="col-lg-4">
                                <label><b>Type Of Report:</b> <?= $report->report_type_name; ?></label>
                            </div>
                            <div class="col-lg-4">
                                <label><b>Date Of Report:</b> <?= date("d/m/Y", strtotime($report->file_report_date)); ?></label>
                            </div>
                            <?php foreach ($report_image as $key => $value) {
                                $arr = explode('.', $value['file_report_image_url']);
                            ?>
                                <div class="col-lg-12 text-center" style="margin-top: 15px;">
                                    <?php if (strtolower(end($arr)) != 'pdf') { ?>
                                        <img src="<?= $value['file_report_image_url']; ?>">
                                    <?php } else {
                                        $pdf_url = site_url() . 'pdf_preview/web/view_pdf.php?file_url=' . base64_encode(urlencode($value['file_report_image_url']));
                                    ?>
                                        <iframe frameborder="0" width="100%" height="600px" src="<?= site_url('pdf_preview/web/pdf_preview.php?charting_url=' . base64_encode(urlencode($pdf_url))) ?>"></iframe>
                                    <?php } ?>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <?php $this->load->view('patient/footer'); ?>
    <?php $this->load->view('patient/footer_layout'); ?>
