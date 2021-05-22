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
            <div class="row">
                <div class="col-lg-6 offset-lg-3 col-md-8 offset-md-2">
                    <div class="contact-form">
                        <?php if ($this->session->userdata('message') != '') : ?>
                            <div class="alert alert-success">
                                <strong>Success!</strong> <?php echo $this->session->userdata('message'); ?>
                            </div>
                        <?php endif; ?>
                        <?php if (!empty($errors)) : ?>
                            <div class="alert alert-danger" id="message">
                                <strong>Error!</strong> <?php echo strip_tags($errors); ?>
                            </div>
                        <?php endif; ?>
                        <center>
                            <h1 class="page-title">Forgot Password</h1>
                        </center>
                        <p class="form-message"></p>
                        <form id="forgot-password-form" action="<?= site_url('patient/forgot') ?>" method="post" novalidate="novalidate">
                            <div class="form-group">
                                <input type="text" class="onlyNumbers name" name="phone_number" placeholder="Phone" aria-required="true" aria-invalid="true" maxlength="10">
                                <?php if (form_error('phone_number')) { ?>
                                    <label class="error"><?= form_error('phone_number') ?></label>
                                <?php } ?>
                            </div>
                            <div class="form-group mb0 text-right">
                                <a href="<?= site_url('patient/login') ?>">Back To Login</a>
                            </div>
                            <center>
                                <button type="submit" name="submit">Send</button>
                            </center>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <?php $this->load->view('patient/footer'); ?>
    <?php $this->load->view('patient/footer_layout'); ?>
