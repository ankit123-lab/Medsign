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
                <div class="col-lg-6 offset-lg-3">
                    <div class="contact-form">
                        <center>
                            <div id="server_side_error"></div>
                        </center>
                        <form id="share-data-form" action="<?= site_url("patient/share_data_save"); ?>" method="post" novalidate="novalidate">
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="form-group">
                                        <label>Doctor Name <span class="error">*</span></label>
                                        <input type="text" class="name form-control" name="doctor_name" id="doctor_name" value="" placeholder="Name" aria-required="true" aria-invalid="true">
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="form-group">
                                        <label>WhatsApp/Mobile No <span class="error">*</span></label>
                                        <input type="text" class="name form-control" name="mobile_no" id="mobile_no" value="" maxlength="10" placeholder="Mobile No" aria-required="true" aria-invalid="true">
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="form-group">
                                        <label>Email Id</label>
                                        <input type="text" class="name form-control" name="email_id" id="email_id" value="" placeholder="Email" aria-required="true" aria-invalid="true">
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="form-group">
                                        <label>Access Data Duration <span class="error">*</span></label>
                                        <select class="select2 md-form form-control" name="month_duration" id="month_duration">
                                            <option value="">Select Duration</option>
                                            <option value="1">One Month</option>
                                            <option value="6">6 Month</option>
                                            <option value="12">1 Year</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-12 share-success alert alert-success" id="message" style="display: none;"></div>
                                <div class="col-lg-12">
                                    <button type="submit" id="share_btn" value="Save" name="Share">Share</button>
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
    <script type="text/javascript">
        $("#message").delay(5000).slideUp(300);
    </script>
