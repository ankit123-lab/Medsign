<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<body class="fp-page">
    <?php if ($this->session->flashdata('success')) { ?>
        <script>toastr.success('<?= $this->session->flashdata('success') ?>')</script>
    <?php } if ($this->session->flashdata('failure')) { ?>
        <script>toastr.error('<?= $this->session->flashdata('failure') ?>')</script>                            
    <?php } ?>
    <div class="forgot_pasword common_padding">
        <div class="container">
            <div class="m_top_20 m_bottom_40">
                <div class="text-center">
                    <a href="javascript:void(0);"><img src="<?= ASSETS_PATH ?>images/small_logo.png" style="margin: 0 auto;" class="img-responsive"></a>
                </div>

                <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 user_register" style="margin: 0 auto;float: none;color: red;">

                    <?php echo form_open("home/verify_token/" . $token . "/" . $id, 'id="reset_password"'); ?>

                    <div class="m_top_20">
                        <div class="form-group">
                            <input type="password" id="password" name="password" class="form-control form_input" placeholder="Password" value="<?php echo empty($email) ? "" : $email; ?>" autofocus="true">
                        </div>
                        <?php if (!empty(form_error('password'))) echo form_error('password'); ?>
                        <?php if (!empty(form_error('not_registered'))) echo form_error('not_registered'); ?>
                    </div>

                    <div class="m_top_20">
                        <div class="form-group">
                            <input type="password" id="cpassword" name="cpassword" class="form-control form_input" placeholder="Confirm Password">
                        </div>
                        <?php if (!empty(form_error('cpassword'))) echo form_error('cpassword'); ?>
                        <?php if (!empty(form_error('not_match'))) echo form_error('not_match'); ?>
                    </div>

                    <div>
                        <button type="submit" class="common_button">Change Password</button>
                    </div>

                    <?php echo form_close(); ?>    
                </div>
            </div>
        </div>
    </div>