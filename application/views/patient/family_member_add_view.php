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
            <h1 class="page-title text-center"><?= $breadcrumbs; ?></h1>
            <div class="row">
                <div class="col-lg-6 offset-lg-3">
                    <div class="contact-form">
                        <?php if (!empty($errors)) : ?>
                            <div class="alert alert-danger" id="message">
                                <strong>Error!</strong> <?php echo $errors; ?>
                            </div>
                        <?php endif; ?>
                        <form id="patient-family-form" action="<?= site_url("patient/add_member"); ?>" method="post" novalidate="novalidate">
                            <input type="hidden" name="user_id" id="user_id" value="<?= $user_id; ?>">
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="form-group">
                                        <label>First Name <span class="error">*</span></label>
                                        <input type="text" class="name form-control" name="first_name" id="first_name" value="<?= $first_name; ?>" placeholder="First Name" aria-required="true" aria-invalid="true">
                                        <?php if (form_error('first_name')) { ?>
                                            <label class="error"><?= form_error('first_name') ?></label>
                                        <?php } ?>
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="form-group">
                                        <label>Last Name <span class="error">*</span></label>
                                        <input type="text" class="name form-control" name="last_name" id="last_name" value="<?= $last_name; ?>" placeholder="Last Name" aria-required="true" aria-invalid="true">
                                        <?php if (form_error('last_name')) { ?>
                                            <label class="error"><?= form_error('last_name') ?></label>
                                        <?php } ?>
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="form-group">
                                        <label>Mobile No</label>
                                        <input type="text" class="name form-control" name="mobile_no" id="mobile_no" value="<?= $mobile_no; ?>" maxlength="10" placeholder="Mobile No" aria-required="true" aria-invalid="true">
                                        <?php if (form_error('mobile_no')) { ?>
                                            <label class="error"><?= form_error('mobile_no') ?></label>
                                        <?php } ?>
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="form-group">
                                        <label>Relation <span class="error">*</span></label>
                                        <select class="select2 md-form form-control" name="relation">
                                            <option value="">Select Relation</option>
                                            <?php
                                            foreach ($family_relation as $key => $value) { ?>
                                                <option <?= ($relation == $value['id']) ? 'selected' : ''; ?> value="<?= $value['id']; ?>"><?= $value['name']; ?></option>
                                            <?php } ?>
                                        </select>
                                        <?php if (form_error('relation')) { ?>
                                            <label class="error"><?= form_error('relation') ?></label>
                                        <?php } ?>
                                    </div>
                                </div>
                                <div class="col-lg-12 member_other_details" <?= !empty($user_id) ? 'style="display:none;"' : ''; ?>>
                                    <div class="form-group">
                                        <label>Date Of Birth <span class="error">*</span></label>
                                        <input type="text" class="name form-control dateOfBirth" name="date_of_birth" value="<?= $date_of_birth; ?>" id="date_of_birth" placeholder="Date Of Birth" aria-required="true" aria-invalid="true" autocomplete="off">
                                        <?php if (form_error('date_of_birth')) { ?>
                                            <label class="error"><?= form_error('date_of_birth') ?></label>
                                        <?php } ?>
                                    </div>
                                </div>
                                <div class="col-lg-12 member_other_details" <?= !empty($user_id) ? 'style="display:none;"' : ''; ?>>
                                    <div class="form-group">
                                        <label>Gender <span class="error">*</span></label><br>
                                        <div class="form-check form-check-inline static">
                                            <span class="radio-inline">
                                                <input type="radio" id="gender_male" name="gender" value="male" class="form-check-input">
                                                <label class="form-check-label" for="gender_male">Male</label>
                                            </span>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <span class="radio-inline">
                                                <input type="radio" id="gender_female" name="gender" value="female" class="form-check-input">
                                                <label class="form-check-label" for="gender_female">Female</label>
                                            </span>
                                        </div>
                                        <?php if (form_error('gender')) { ?>
                                            <br/>
                                            <label class="error"><?= form_error('gender') ?></label>
                                        <?php } ?>
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <button type="submit" id="add_member_btn" value="add_member" name="add_member">Add Member</button>
                                    <a href="<?= site_url('patient/profile/update'); ?>" class="btns">Cancel</a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <style type="text/css">
        #gender-error{bottom: 0px !important;left: 16px !important;}
    </style>
    <?php $this->load->view('patient/footer'); ?>
    <?php $this->load->view('patient/footer_layout'); ?>
