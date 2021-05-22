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
                        <?php if(!empty($utilities_list)) { ?>
                        <ul class="list-group">
                            <?php foreach ($utilities_list as $key => $value) { ?>
                                <li class="list-group-item">
                                    <div class="col-lg-12 col-12 text-center">
                                        <a class="<?= $value['is_paid'] ? '' : 'payment_popup'; ?> utility_name" href="<?= $value['utility_url']; ?>" utility_name="<?= $value['utility_name']; ?>"><h4><?= $value['utility_label']; ?></h4>
                                        <?=!empty($value['utility_desc']) ? $value['utility_desc'] : '';?>
                                        </a>
                                    </div>
                                </li>
                            <?php } ?>
                        </ul>
                    <?php } else { ?>
                        <div class="row"> 
                            <div class="col-lg-12 col-12 text-center">
                                <h5 style="margin-top: 15px;">You don't have any health tracker assigned by your doctor.</h5>
                            </div>
                        </div>
                    <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Payment Modal -->
    <div class="modal fade" id="paymentModal" tabindex="-1" role="dialog" aria-labelledby="paymentModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">

        </div>
    </div>
    <!-- End Payment Modal -->
    <?php $this->load->view('patient/footer'); ?>
    <?php $this->load->view('patient/footer_layout'); ?>
<script type="text/javascript">
    $("#message").delay(5000).slideUp(300);
</script>
