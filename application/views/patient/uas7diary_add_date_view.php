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
                    <a href="<?= site_url('patient/uas7diary'); ?>" class="btns btns-small" style="float:right;"><i class="fa fa-angle-double-left" aria-hidden="true"></i> Back</a>
                    <p style="width:80%;" class="page-title"><?php echo $breadcrumbs; ?></p>
                </div>
                <div class="col-lg-12 btn-m-0">
                    <div class="contact-form">
                        <ul class="list-group">
                            <?php foreach ($missing_date as $key => $value) { ?>
                                <li class="list-group-item">
                                    <?php if(!empty($value['patient_diary_id'])) { ?>
                                        <a class="add_uas7_date_list" href="<?= site_url('patient/edit_uas7_para/'.encrypt_decrypt($value['patient_diary_id'],'encrypt')); ?>"><i class="fa fa-arrow-circle-right" aria-hidden="true"></i> <?= $value['date']; ?></a>
                                    <?php } else { ?>
                                        <a class="add_uas7_date_list" href="<?= site_url('patient/add_uas7_para').'/'.encrypt_decrypt($value['date'], 'encrypt'); ?>"><i class="fa fa-arrow-circle-right" aria-hidden="true"></i> <?= $value['date']; ?></a>
                                    <?php } ?>
                                </li>
                            <?php } ?>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12 m_top_25">
                    <nav aria-label="Page navigation">
                        <?php echo $links; ?>
                    </nav>
                </div>
            </div>
        </div>
    </section>
    <?php $this->load->view('patient/footer'); ?>
    <?php $this->load->view('patient/footer_layout'); ?>
<script type="text/javascript">
    $("#message").delay(5000).slideUp(300);
</script>
