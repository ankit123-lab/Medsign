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
                <div class="col-lg-12">
                    <div class="contact-form">
                        <center>
                            <div id="register_server_side_error"></div>
                        </center>
                        <?php if (!empty($doctor_details)) { ?>
                            <div class="doctor-detail__box">
                                <div class="row">
                                    <div class="col-5 text-center">
                                        <?php if(!empty($doctor_details->user_photo_filepath)) { ?>
                                        <img src="<?= get_image_thumb($doctor_details->user_photo_filepath); ?>" alt="Profile Photo" width="100">
                                        <?php } else { ?>
                                            <img src="<?= ASSETS_PATH . 'images/placeholder_user.png'; ?>" alt="Profile Photo">
                                        <?php } ?>
                                    </div>
                                    <div class="col-7 pl-0">
                                        <h5><?= DOCTOR . ' ' . $doctor_details->doctor_name; ?></h5>
                                        <?= str_replace(',', ', ', $doctor_details->doctor_qualification_degree) ?>
                                        <br>
                                        <?= str_replace(',', ', ', $doctor_details->doctor_detail_speciality) ?>
                                        
                                    </div>
                                    <div class="col-lg-12">
                                        <div class="clinic_details text-center">
                                            <h5><?= $doctor_details->clinic_name; ?></h5>
                                            <?php
                                            $address_data = [
                                                'address_name' => $doctor_details->address_name,
                                                'address_name_one' => $doctor_details->address_name_one,
                                                'address_locality' => $doctor_details->address_locality,
                                                'city_name' => $doctor_details->city_name,
                                                'state_name' => $doctor_details->state_name,
                                                'address_pincode' => $doctor_details->address_pincode
                                            ];
                                            echo clinic_address($address_data);
                                            ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php } ?>

                        <h1 class="page-title">Patient Register</h1>
                        <p class="form-message"></p>
                        <form id="patient-register-form" method="post" novalidate="novalidate">
                            <input type="hidden" name="redirect_page" id="redirect_page" value="<?= $redirect_page; ?>">
                            <input type="hidden" name="user_source_id" value="<?= !empty($doctor_details->doctor_id) ? $doctor_details->doctor_id : '' ?>">
                            <input type="hidden" name="patient_id" value="<?= !empty($patient_details->user_id) ? $patient_details->user_id : '' ?>">
                            <input type="hidden" name="share_link_id" value="<?= !empty($share_link_id) ? $share_link_id : '' ?>">
                            <input type="hidden" name="reg_share_id" value="<?= !empty($share_row['registration_share_id']) ? $share_row['registration_share_id'] : '' ?>">
                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label>First Name <span class="error">*</span></label>
                                        <input type="text" class="name form-control" name="user_first_name" value="<?= !empty($patient_details->user_first_name) ? $patient_details->user_first_name : ''; ?>" placeholder="First Name" aria-required="true" aria-invalid="true">
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label>Last Name <span class="error">*</span></label>
                                        <input type="text" class="name form-control" name="user_last_name" value="<?= !empty($patient_details->user_last_name) ? $patient_details->user_last_name : ''; ?>" placeholder="Last Name" aria-required="true" aria-invalid="true">
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label>Mobile Number <span class="error">*</span></label>
                                        <input type="text" class="name form-control" name="user_phone_number" value="<?= !empty($patient_details->user_phone_number) ? $patient_details->user_phone_number : ''; ?>" <?= !empty($patient_details->user_phone_number) ? 'readonly' : ''; ?> placeholder="Mobile Number" maxlength="10" aria-required="true" aria-invalid="true">
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label>Email</label>
                                        <input type="text" class="name form-control" name="user_email" value="<?= !empty($patient_details->user_email) ? $patient_details->user_email : ''; ?>" <?= !empty($patient_details->user_email) ? 'readonly' : ''; ?> placeholder="Email" aria-required="true" aria-invalid="true">
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label>Password <span class="error">*</span></label>
                                        <input type="password" class="name form-control" name="user_password" id="user_password" placeholder="Password" aria-required="true" aria-invalid="true">
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label>Confirm Password <span class="error">*</span></label>
                                        <input type="password" class="name form-control" name="c_user_password" id="c_user_password" placeholder="Confirm Password" aria-required="true" aria-invalid="true">
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label>Date Of Birth <span class="error">*</span></label>
                                        <input type="text" class="name form-control dateOfBirth" name="date_of_birth" value="<?= !empty($patient_details->user_details_dob) ? date('d/m/Y', strtotime($patient_details->user_details_dob)) : ''; ?>" placeholder="Date Of Birth" aria-required="true" aria-invalid="true" autocomplete="off">
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group relative">
                                        <label class="dblock">Gender <span class="error">*</span></label>
                                        <?php
                                        $user_gender = '';
                                        if (!empty($patient_details->user_gender)) {
                                            $user_gender = $patient_details->user_gender;
                                        }
                                        ?>
                                        <div class="form-check form-check-inline static">
                                            <span class="radio-inline">
                                                <input type="radio" <?= ($user_gender == 'male') ? 'checked' : ''; ?> id="gender_male" name="gender" value="male" class="form-check-input">
                                                <label class="form-check-label" for="gender_male">Male</label>
                                            </span>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <span class="radio-inline">
                                                <input type="radio" <?= ($user_gender == 'female') ? 'checked' : ''; ?> id="gender_female" name="gender" value="female" class="form-check-input">
                                                <label class="form-check-label" for="gender_female">Female</label>
                                            </span>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <span class="radio-inline">
                                                <input type="radio" <?= ($user_gender == 'undisclosed') ? 'checked' : ''; ?> id="gender_undisclosed" name="gender" value="undisclosed" class="form-check-input">
                                                <label class="form-check-label" for="gender_undisclosed">Undisclosed</label>
                                            </span>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <span class="radio-inline">
                                                <input type="radio" <?= ($user_gender == 'other') ? 'checked' : ''; ?> id="gender_other" name="gender" value="other" class="form-check-input">
                                                <label class="form-check-label" for="gender_other">Other</label>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="form-check terms_conditions">
                                        <input name="terms_conditions" type="checkbox" value="1" class="form-check-input" id="terms_conditions">
                                        <label class="form-check-label" for="terms_conditions">I agree to the <a href="<?= site_url('terms-conditions') ?>" class="text-decoration-underline" target="_blank">Terms & Conditions</a> and <a href="<?= site_url('privacy-policy') ?>" class="text-decoration-underline" target="_blank">Privacy Policy</a></label>
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <button type="submit" id="reg-btn" name="submit"><?= !empty($patient_details->user_id) ? 'Continue' : 'Register' ?></button>
                                    <img class="loader-img" style="display: none;" src="<?= site_url(); ?>assets/admin/images/ajax-loader.gif">
                                </div>
                            </div>
                        </form>
                        <center>
                            <div id="result"></div>
                        </center>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <?php $this->load->view('patient/footer'); ?>
    <?php $this->load->view('patient/footer_layout'); ?>
