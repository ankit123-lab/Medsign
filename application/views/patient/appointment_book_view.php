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
                    <div class="contact-form">
                        <?php
                            $this->load->view('patient/page_title',['title' => 'Book Appointment']);
                        ?>
                        <?php if ($this->session->userdata('message') != '') : ?>
                            <div class="alert alert-success">
                                <strong>Success!</strong> <?php echo $this->session->userdata('message'); ?>
                            </div>
                        <?php endif; ?>
                        <center>
                            <div id="alert alert-danger">
                                <?php
                                if (!empty($errors)) {
                                    echo $errors;
                                }
                                ?>
                            </div>
                        </center>
                        <form method="get" action="<?= site_url('patient/appointment_book'); ?>">
                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <input type="text" value="<?= ($this->input->get('search_txt')) ? $this->input->get('search_txt') : ''; ?>" placeholder="Search Doctor, Clinic, Address, City" name="search_txt" autocomplete="off" class="form-control">
                                    </div>
                                </div>
                                <div class="col-lg-2">
                                    <div class="form-check available-box">
                                        <input <?= ($this->input->get('available_today')) ? 'checked' : ''; ?> name="available_today" type="checkbox" value="1" class="form-check-input" id="available_today">
                                        <label class="form-check-label" for="available_today">Available Today</label>
                                    </div>
                                </div>
                                <div class="col-lg-2">
                                    <div class="form-group">
                                        <input type="number" value="<?= ($this->input->get('year_of_experience')) ? $this->input->get('year_of_experience') : ''; ?>" placeholder="Year of experience" name="year_of_experience" autocomplete="off" class="form-control">
                                    </div>
                                </div>
                                <div class="col-lg-2">
                                    <div class="form-group">
                                        <select class="mdb-select md-form form-control" name="sex">
                                            <option value="">Select Gender</option>
                                            <?php
                                            foreach ($sex_array as $key => $sex) { ?>
                                                <option <?= ($this->input->get('sex') && $this->input->get('sex') == $key) ? 'selected' : ''; ?> value="<?= $key; ?>"><?= $sex; ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <select class="md-form form-control" multiple id="speciality" name="speciality[]" data-placeholder="Speciality">
                                            <?php
                                            foreach ($speciality as $key => $value) { ?>
                                                <option <?= ($this->input->get('speciality') && in_array($value->speciality_title, $this->input->get('speciality'))) ? 'selected' : ''; ?> value="<?= $value->speciality_title; ?>">
                                                    <?= $value->speciality_title; ?>
                                                </option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-2">
                                    <div class="form-group">
                                        <select class="mdb-select md-form form-control" name="fees">
                                            <option value="">Select Fees</option>
                                            <?php
                                            foreach ($fees_array as $key => $fee) { ?>
                                                <option <?= ($this->input->get('fees') && $this->input->get('fees') == $key) ? 'selected' : ''; ?> value="<?= $key; ?>"><?= $fee; ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-4 text-left text-lg-right">
                                    <button type="submit" name="submit">Search</button>
                                    <a href="<?= site_url('patient/appointment_book'); ?>" class="btns">Clear</a>
                                </div>

                            </div>
                        </form>

                        <div class="mt30">
                            <table class="table table-striped table-responsive-grid">
                                <thead>
                                    <tr>
                                        <th>Doctor</th>
                                        <th>Qualification</th>
                                        <th>Speciality</th>
                                        <th>Clinic Name</th>
                                        <th>Address</th>
                                        <th>Fees</th>
                                        <th>Tele Consult</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    if (count($doctors) > 0) {
                                        foreach ($doctors as $key => $value) {

                                    ?>
                                            <tr>
                                                <td data-title="Doctor"><?= DOCTOR . ' ' . $value->doctor_name; ?></td>
                                                <td data-title="Qualification"><?= str_replace(',', ', ', $value->doctor_qualification_degree); ?></td>
                                                <td data-title="Speciality"><?= str_replace(',', ', ', $value->doctor_detail_speciality); ?></td>
                                                <td data-title="Clinic Name"><?= $value->clinic_name; ?></td>
                                                <td data-title="Address">
                                                    <?php 
                                                    $address_data = [
                                                        'address_name' => $value->address_name,
                                                        'address_name_one' => $value->address_name_one,
                                                        'address_locality' => $value->address_locality,
                                                        'city_name' => $value->city_name,
                                                        'state_name' => $value->state_name,
                                                        'address_pincode' => $value->address_pincode
                                                    ];
                                                    echo clinic_address($address_data);
                                                    ?>
                                                </td>
                                                <td data-title="Fees"><?= round($value->doctor_clinic_mapping_fees, 0); ?></td>
                                                <td data-title="Tele Consult">
                                                    <?php if (!empty($value->doctor_payment_mode_id) && !empty($value->doctor_clinic_mapping_tele_fees) && $value->doctor_clinic_mapping_tele_fees > 0) {
                                                        echo "Yes";
                                                    } else {
                                                        echo "No";
                                                    }
                                                    ?>
                                                </td>
                                                <td class="td-btn">
                                                    <a href="<?= site_url('patient/book_now/' . encrypt_decrypt($value->doctor_id.'_'.$value->clinic_id, 'encrypt')); ?>" class="btns">Book Now</a>
                                                </td>
                                            </tr>
                                        <?php }
                                    } else { ?>
                                        <td colspan="9" class="text-center no-record">No record found</td>
                                    <?php } ?>
                                </tbody>
                            </table>
                            <p class="pagination"><?php echo $links; ?></p>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </section>
    <?php $this->load->view('patient/footer'); ?>
    <?php $this->load->view('patient/footer_layout'); ?>
