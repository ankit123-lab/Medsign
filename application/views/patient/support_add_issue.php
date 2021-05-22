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
            <div class="col-lg-12 btn-m-0">
                <h1 class="page-title text-center"><?= $page_title; ?></h1>
            </div>
        </div>
            <div class="row">
                <div class="col-lg-6 offset-lg-3">
                    <div class="contact-form">
                        <?php if ($this->session->userdata('message') != '') : ?>
                            <div class="alert alert-success">
                                <strong>Success!</strong> <?php echo $this->session->userdata('message'); ?>
                            </div>
                        <?php endif; ?>
                        <form id="support-report-form" action="<?= site_url("patient/add_issue"); ?>" enctype="multipart/form-data" method="post" novalidate="novalidate">
                            <input type="hidden" name="user_name" value="<?= $user_row['user_first_name'] . ' ' . $user_row['user_last_name'];?>">
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="form-group">
                                        <label>Report an issues <span class="error">*</span></label>
                                        <textarea name="issue_message" id="issue_message" placeholder="Type here" class="form-control" aria-required="true" aria-invalid="true"><?= $issue_message; ?></textarea>
                                        <?php if (form_error('issue_message')) { ?>
                                            <label class="error"><?= form_error('issue_message') ?></label>
                                        <?php } ?>
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="form-group">
                                        <label>Email where we can reach you</label>
                                        <input type="text" class="name form-control" name="issue_email" id="issue_email" value="<?= $issue_email; ?>" placeholder="Email Address" aria-required="true" aria-invalid="true">
                                        <?php if (form_error('issue_email')) { ?>
                                            <label class="error"><?= form_error('issue_email') ?></label>
                                        <?php } ?>
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="form-group">
                                        <label>Select Screenshot (image)</label>
                                        <input type="file" name="screenshot[]" multiple="multiple" class="form-control">
                                        <?php if (!empty($screenshot_error)) { ?>
                                            <label class="error"><?= $screenshot_error; ?></label>
                                        <?php } ?>
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="row">
                                        <div class="col-lg-4 form-group">
                                            <input type="text" name="comment_captcha" maxlength="4" placeholder="Captcha">
                                            <?php if(!empty($invalid_captcha)) { ?>
                                                <label class="error" for="comment_captcha"><?= $invalid_captcha; ?></label>
                                            <?php } ?>
                                        </div>
                                        <div class="captcha-img col-lg-3">
                                            
                                        </div>
                                        <div class="col-lg-1" style="margin-top: 6px;font-size: 23px;">
                                            <a href="javascript:void(0);" title="Refresh Captcha" onclick="support_captcha();"><i class="fa fa-refresh"></i></a>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <button type="submit" id="add_issue_btn" value="submit" name="submit">Submit</button>
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
    <script type="text/javascript">
        support_captcha();
        function support_captcha() {
            $.ajax({
                type: 'POST',
                dataType: 'json',
                url: "<?php echo site_url('patient/support/captcha_code'); ?>",
                success: function (data) {
                    $(".captcha-img").html(data.image);
                }
            });
        }
    </script>