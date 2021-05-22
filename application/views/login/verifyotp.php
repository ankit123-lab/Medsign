<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class="register common_padding">
    <div class="container">
        <div class="row m_top_70 m_bottom_40">
            <div class="col-xs-12">
                <h4 class="clv_text_bold no_margin">Hello,</h4>
                <h4 class="clv_text_light no_mar_top">Please enter your verification code, which you have received. &nbsp;&nbsp;<span class="color_orange">Resend again?</span></h4>
                <h3 class="clv_text_book "><i class="fa fa-credit-card"></i> Verification Code</h3>
                <hr  class="common_hr"/>

                <?php if (!empty($this->session->flashdata('failure'))) { ?>
                    <script>toastr.error('<?= $this->session->flashdata('failure') ?>');</script>
                <?php } ?>

                <?php if (!empty($this->session->flashdata('success'))) { ?>
                    <script>toastr.success('<?= $this->session->flashdata('success') ?>');</script>
                <?php } ?>

                <div class="col-lg-7 col-md-12 col-sm-12 col-xs-12 no_pad">

                    <?php echo form_open(HOME_PATH . '/verifyotp', 'id="verifyotp" class="user_register text-uppercase clv_text_light"'); ?>

                    <div class="row m_top_30">
                        <div class="form-group col-md-6">
                            <img src="<?= ASSETS_PATH ?>images/mobile_otp.png" class="img-responsive" style="margin: 0 auto;" />
                        </div>
                    </div>
                    <div class="row m_top_30">  
                        <div class="form-group col-md-6">
                            <label class="required">Verification Code</label>
                            <?php
                            $otp = array(
                                'name' => 'verify_otp',
                                'id' => 'verify_otp',
                                'class' => 'form-control form_input',
                                'maxlength' => '50',
                                'value' => set_value('otp')
                            );
                            echo form_input($otp);
                            ?>

                            <?php if (!empty(form_error('otp'))) { ?>
                                <div class="help-block">
                                    <?= form_error('otp'); ?>
                                </div>
                            <?php } ?>
                        </div>
                    </div>

                    <div class="text-center clv_text_bold col-xs-6 no_pad">
                        <input type="submit" value="Done" class="common_button register_button no_pad_left" style="margin-top: 20px;" />
                    </div>

                    <?php form_close(); ?>
                </div>                
            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function() {
        $("#verifyotp").validate({
            errorElement: 'div',
            errorClass: 'help-block',
            focusInvalid: false,
            rules: {
                verify_otp: {
                    required: true
                }
            },
            messages: {
                verify_otp: {
                    required: "Please enter verify code."
                }
            },
            errorPlacement: function(error, element) {
                element.after(error);
            }
        });
    });
</script>
