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
                    <h1 class="page-title text-center"><?php echo $breadcrumbs; ?></h1>
                </div>
                <div class="col-lg-12 btn-m-0">
                    <div class="col-lg-12 text-center">
                        <span class="success-icon"><i class="fa fa-check" aria-hidden="true"></i></span>
                    </div>
                    <div class="col-lg-12 text-center">
                        <h4 class="success-txt">Success</h4>
                        <span>Transactions Complete</span>
                    </div>
                    <div class="col-lg-12 text-center transactions-details">
                        <span>You have successfully payment Rs. <?= $paid_amount; ?> to MedSign. Click to below Download button for download your invoice.</span>
                    </div>
                    <div class="col-lg-12 success-btns">
                        <a href="<?= $download_url; ?>" target="_blank" class="btns active">Download</a>
                        <?php if(!empty($payment_row['detail_type']) && $payment_row['detail_type'] == 'diary') { ?>
                            <a href="<?= site_url('patient/uas7diary'); ?>" class="btns active">Start UAS7</a>
                        <?php } ?>
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
