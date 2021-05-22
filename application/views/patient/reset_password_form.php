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
                        <?php if (!empty($token_error)) : ?>
                            <div class="alert alert-danger" id="message">
                                <strong>Error!</strong> <?php echo strip_tags($token_error); ?>
                            </div>
                        <?php endif; ?>
                        <?php if(empty($token_error)) { ?>
                        <center>
                            <h1 class="page-title">Reset Password</h1>
                        </center>
                        <p class="form-message"></p>
                        <form id="reset-password-form" method="post" novalidate="novalidate">
                            <input type="hidden" name="userId" value="<?php echo isset($userId) ? $userId : "" ?>" />
                            <div class="form-group">
                                <input type="password" class="name form-control" name="user_password" id="user_password" placeholder="New Password" aria-required="true" aria-invalid="true">
                                <?php if (form_error('user_password')) { ?>
                                    <label class="error"><?= form_error('user_password') ?></label>
                                <?php } ?>
                                <?php if (form_error('userId')) { ?>
                                    <label class="error"><?= form_error('userId') ?></label>
                                <?php } ?>
                            </div>
                            <div class="form-group">
                                <input type="password" class="name form-control" name="c_user_password" id="c_user_password" placeholder="New Confirm Password" aria-required="true" aria-invalid="true">
                                <?php if (form_error('c_user_password')) { ?>
                                    <label class="error"><?= form_error('c_user_password') ?></label>
                                <?php } ?>
                            </div>
                            <center>
                                <button type="submit" name="Reset Password">Reset Password</button>
                            </center>
                        </form>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <?php $this->load->view('patient/footer'); ?>
    <?php $this->load->view('patient/footer_layout'); ?>
