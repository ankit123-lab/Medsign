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
                        <?php if (!empty($errors)) : ?>
                            <div class="alert alert-danger" id="message">
                                <strong>Error!</strong> <?php echo strip_tags($errors); ?>
                            </div>
                        <?php endif; ?>
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
                        <center>
                            <h1 class="page-title">Login</h1>
                        </center>
                        <p class="form-message"></p>
                        <form id="login-form" action="<?= site_url('patient/login') ?>" method="post" novalidate="novalidate">
                            <input type="hidden" name="redirect_page" value="<?= $redirect_page; ?>">
                            <div class="form-group">
                                <input type="text" class="onlyNumbers name form-control" name="phone_number" placeholder="Phone" maxlength="10" aria-required="true" aria-invalid="true">
                            </div>
                            <div class="form-group">
                                <input type="password" class="form-control" name="user_password" id="user_password" placeholder="Password">
                            </div>
                            <div class="form-group mb0 text-right">
                                <a href="<?= site_url('patient/forgot') ?>">Forgot Password</a>
                            </div>
                            <center>
                                <button type="submit" name="login">Login</button>
                            </center>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <?php $this->load->view('patient/footer'); ?>
    <?php $this->load->view('patient/footer_layout'); ?>
