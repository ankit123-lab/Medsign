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
            <div class="row btn-m-0">
                <div class="col-lg-12">
                    <div class="contact-form">
                        <?php if ($this->session->userdata('message') != '') : ?>
                            <div class="alert alert-success" id="message">
                                <strong>Success!</strong> <?php echo $this->session->userdata('message'); ?>
                            </div>
                        <?php endif; ?>
                        <?php if ($this->session->userdata('errors') != '') : ?>
                            <div class="alert alert-danger" id="message">
                                <strong>Error!</strong> <?php echo $this->session->userdata('errors'); ?>
                            </div>
                        <?php endif; ?>
                        <form method="get" action="<?= site_url('patient/appointment_list'); ?>">
                            <div class="row">
                                <div class="col-lg-3">
                                    <div class="form-group">
                                        <select class="mdb-select md-form form-control" name="doctor_id">
                                            <option value="">Select Doctor</option>
                                            <?php
                                            foreach ($doctors as $key => $doctor) { ?>
                                                <option <?= ($this->input->get('doctor_id') && $this->input->get('doctor_id') == $doctor->user_id) ? 'selected' : ''; ?> value="<?= $doctor->user_id; ?>"><?= $doctor->doctor_name; ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-3">
                                    <div class="form-group">
                                        <select class="mdb-select md-form form-control" name="appointment_type_id">
                                            <option value="">Select Type</option>
                                            <?php
                                            foreach ($appointment_types as $key => $appointment_type) {
                                                if (!in_array($appointment_type['appointment_type_id'], [1, 5]))
                                                    continue;
                                            ?>
                                                <option <?= ($this->input->get('appointment_type_id') && $this->input->get('appointment_type_id') == $appointment_type['appointment_type_id']) ? 'selected' : ''; ?> value="<?= $appointment_type['appointment_type_id']; ?>"><?= $appointment_type['appointment_type_name_en']; ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-3">
                                    <div class="form-group">
                                        <input type="text" class="appointment_date form-control" value="<?= ($this->input->get('appointment_date')) ? $this->input->get('appointment_date') : ''; ?>" placeholder="Date" name="appointment_date" autocomplete="off">
                                    </div>
                                </div>
                                <div class="col-lg-3 form-group">
                                    <button type="submit" name="submit">Search</button>
                                    <a href="<?= site_url('patient/appointment_list'); ?>" class="btns">Clear</a>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-3 form-group">
                                    <button style="width: 100%;" type="button" id="view_past_appointment" name="Past Appointments">Past Appointments</button>
                                </div>
                                <div class="col-lg-3 form-group">
                                    <button style="width: 100%;" type="button" id="book_appointment" name="Book Appointment">Book New Appointment</button>
                                </div>
                            </div>
                        </form>

                        <table class="table table-striped table-responsive-grid appointment-table">
                            <thead>
                                <tr>
                                    <th>Doctor</th>
                                    <th>Speciality</th>
                                    <th>Clinic Name</th>
                                    <th>Address</th>
                                    <th>Date</th>
                                    <th>Time</th>
                                    <th>Type</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                if (count($appointments) > 0) {
                                foreach ($appointments as $key => $value) {
                                ?>
                                    <tr>
                                        <td data-title="Doctor Name"><?= DOCTOR . ' ' . $value->doctor_name; ?></td>
                                        <td data-title="Doctor Speciality"><?= str_replace(',', ', ', $value->doctor_detail_speciality); ?></td>
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
                                        <td data-title="Date"><?= date("d/m/Y", strtotime($value->appointment_date)); ?></td>
                                        <td data-title="Time"><?= date("h:i A", strtotime($value->appointment_from_time)); ?></td>
                                        <td data-title="Type"><?= $value->appointment_type_name_en; ?></td>
                                        <td class="td-btn text-center">
                                            <?php if ((!empty($value->vital_report_id) || !empty($value->clinical_notes_reports_id) || !empty($value->prescription_id) || !empty($value->lab_report_id) || !empty($value->procedure_report_id)) && $value->patient_mail_flag == 1) {
                                                $pdf_url = site_url() . 'pdf_preview/web/pdf_preview.php?charting_url=' . base64_encode(urlencode(site_url('patient/prescription/' . encrypt_decrypt($value->appointment_id, 'encrypt'))));
                                                if($this->patient_auth->get_access('view_rx')) {
                                            ?>
                                                <a href="<?= $pdf_url; ?>" target="_blank" class="btns">View Prescription</a>
                                            <?php 
                                            } else { ?>
                                                <a href="<?= site_url('patient/upgrade'); ?>" target="_blank" class="btns">View Prescription</a>
                                            <?php }
                                        } ?>
                                            <?php if (empty($value->vital_report_id) && empty($value->clinical_notes_reports_id) && empty($value->prescription_id) && empty($value->lab_report_id) && empty($value->procedure_report_id) && strtotime($value->appointment_date . ' ' . $value->appointment_from_time) > strtotime(get_display_date_time("Y-m-d H:i:s"))) {
                                            ?>
                                                <span class="icon delete-icon">
                                                    <a href="<?= site_url("patient/appointment_delete/" . encrypt_decrypt($value->appointment_id, 'encrypt')) ?>" title="Cancel Appointment" onclick="return confirm('Are you sure to cancel this appointment?')">
                                                        <i class="fa fa-trash"></i>
                                                    </a>
                                                </span>
                                            <?php } ?>
                                        </td>
                                    </tr>
                                <?php 
                                    }
                                } else { ?>
                                    <td colspan="8" class="text-center no-record">No record found</td>
                                <?php } ?>
                            </tbody>
                        </table>

                        <nav aria-label="Page navigation">
                            <?php echo $links; ?>
                        </nav>

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