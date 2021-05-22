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
            <?php
                $this->load->view('patient/page_title',['title' => $breadcrumbs]);
            ?>
            <div class="row">
                <div class="col-lg-12">
                    <div class="contact-form">
                        <?php if (!empty($errors)) : ?>
                            <div class="alert alert-danger" id="message">
                                <strong>Error!</strong> <?php echo $errors; ?>
                            </div>
                        <?php endif; ?>
                        <?php if (!empty($this->session->userdata('error'))) : ?>
                            <div class="alert alert-danger" id="message">
                                <strong>Error!</strong> <?php echo $this->session->userdata('error'); ?>
                            </div>
                        <?php endif; ?>
                        <?php if ($this->session->userdata('message') != '') : ?>
                            <div class="alert alert-success" id="message">
                                <strong>Success!</strong> <?php echo $this->session->userdata('message'); ?>
                            </div>
                        <?php endif; ?>
                        <form id="book-now-form" method="post" novalidate="novalidate">
                            <input type="hidden" name="doctor_id" id="doctor_id" value="<?= $doctor_details->doctor_id; ?>">
                            <input type="hidden" name="clinic_id" id="clinic_id" value="<?= $doctor_details->clinic_id; ?>">
                            <input type="hidden" name="appointment_to_time" id="appointment_to_time" value="">
                            <input type="hidden" name="doctor_availability_id" id="doctor_availability_id" value="">

                            <div class="doctor-detail__box">
                                <div class="row">
                                    <div class="col-5 text-center">
                                        <img src="<?= !empty($doctor_details->user_photo_filepath) ? get_image_thumb($doctor_details->user_photo_filepath) : ASSETS_PATH . 'images/placeholder_user.png'; ?>">
                                    </div>
                                    <div class="col-7 pl-0">
                                        <h5><?= DOCTOR . ' ' . $doctor_details->doctor_name; ?></h5>
                                        <?= str_replace(',', ', ', $doctor_details->doctor_qualification_degree); ?>
                                        <br>
                                        <?= str_replace(',', ', ', $doctor_details->doctor_detail_speciality); ?>
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
                                            <br>
                                            <b>Fees:</b> <?= $doctor_details->doctor_clinic_mapping_fees; ?> INR
                                            <br>
                                            <b>Tele Consult:</b> <?= (!empty($doctor_details->doctor_clinic_mapping_tele_fees) && $doctor_details->doctor_clinic_mapping_tele_fees > 0 && !empty($doctor_details->doctor_payment_mode_id)) ? 'Yes' : 'No'; ?>
                                        </div>
                                    </div>

                                    <div class="col-lg-12">
                                        <div class="form-group">
                                            <label>Appointment Date <span class="error">*</span></label>
                                            <input type="text" class="name form-control" name="appointment_date" value="<?= (set_value('appointment_date') !== NULL) ? set_value('appointment_date') : get_display_date_time('d/m/Y'); ?>" id="appointment_date" placeholder="Appointment Date" aria-required="true" aria-invalid="true" autocomplete="off">
                                            <?php if (form_error('appointment_date')) { ?>
                                                <label class="error"><?= form_error('appointment_date') ?></label>
                                            <?php } ?>
                                        </div>
                                    </div>
                                    <div class="col-lg-12">
                                        <div class="form-group">
                                            <label>Appointment Type <span class="error">*</span></label>
                                            <?php
                                            $appointment_type = 1;
                                            if (set_value('appointment_type') !== NULL)
                                                $appointment_type = set_value('appointment_type');
                                            ?>
                                            <select class="md-form form-control" name="appointment_type" id="appointment_type">
                                                <option value="">Select Type</option>
                                                <option <?= ($appointment_type == 1) ? 'selected' : ''; ?> value="1">Doctor Visit</option>
                                                <?php
                                                if (!empty($doctor_details->doctor_clinic_mapping_tele_fees) && $doctor_details->doctor_clinic_mapping_tele_fees > 0 && !empty($doctor_details->doctor_payment_mode_id)) {
                                                ?>
                                                    
                                                    <?php if($is_booked_video_appointment){ ?>
                                                    <option <?= ($appointment_type == 5) ? 'selected' : ''; ?> value="5">Tele Consultation</option>
                                                <?php } 
                                                } ?>
                                            </select>
                                            <?php if (form_error('appointment_type')) { ?>
                                                <label class="error"><?= form_error('appointment_type') ?></label>
                                            <?php } ?>
                                        </div>
                                    </div>
                                    <div class="col-lg-12">
                                        <div class="form-group">
                                            <label>Appointment Time <span class="error">*</span></label>
                                            <select class="md-form form-control" name="appointment_from_time" id="appointment_from_time">
                                                <option value="">Select Time</option>
                                            </select>
                                            <?php if (form_error('appointment_from_time')) { ?>
                                                <label class="error"><?= form_error('appointment_from_time') ?></label>
                                            <?php } ?>
                                        </div>
                                    </div>
                                    <div class="col-lg-12 text-center">
                                        <button type="submit" id="book_appointment_btn" value="Confirm" name="Confirm">Confirm</button>
                                        <a href="<?= site_url('patient/appointment_book'); ?>" class="btns">Cancel</a>
                                    </div>
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
        get_availability();
    </script>
