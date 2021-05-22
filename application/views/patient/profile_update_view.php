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
                    <form id="patient-update-form" method="post" novalidate="novalidate">
                        <div class="contact-form">
                            <?php if (!empty($this->session->userdata('message'))) : ?>
                                <div class="alert alert-success" id="message1">
                                    <strong>Success!</strong> <?php echo $this->session->userdata('message'); ?>
                                </div>
                            <?php endif; ?>
                            <center>
                                <div id="update_server_side_error"></div>
                            </center>
                            <?php
                                $this->load->view('patient/page_title',['title' => $breadcrumbs]);
                            ?>
                            <ul class="nav nav-tabs responsive-tabs" id="myTab" role="tablist" style="margin-bottom: 20px;">
                                <li class="nav-item">
                                    <a class="nav-link active" id="patient-general-tab" data-toggle="tab" href="#patient-general" role="tab" aria-controls="home" aria-selected="true">General</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="patient-personal-tab" data-toggle="tab" href="#patient-personal" role="tab" aria-controls="patient-personal" aria-selected="false">Personal</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="patient-lifestyle-tab" data-toggle="tab" href="#patient-lifestyle" role="tab" aria-controls="patient-lifestyle" aria-selected="false">Lifestyle</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="patient-family-history-tab" data-toggle="tab" href="#patient-family-history" role="tab" aria-controls="patient-family-history" aria-selected="false">Family History</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="patient-allergies-tab" data-toggle="tab" href="#patient-allergies" role="tab" aria-controls="patient-allergies" aria-selected="false">Allergies</a>
                                </li>
                                <?php if($this->patient_auth->get_logged_user_id() == $this->patient_auth->get_user_id()) { ?>
                                <li class="nav-item">
                                    <a class="nav-link" id="patient-member-tab" data-toggle="tab" href="#patient-member" role="tab" aria-controls="patient-member" aria-selected="false">Caregiver For</a>
                                </li>
                                <?php } ?>
                            </ul>
                            <p class="form-message"></p>
                            <div class="tab-content" id="myTabContent">
                                <div class="tab-pane fade show active" id="patient-general" role="tabpanel" aria-labelledby="patient-general-tab">
                                    <div class="row">
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label>First Name <span class="error">*</span></label>
                                                <input type="text" class="name form-control" name="user_first_name" value="<?= $patient_details->user_first_name ?>" placeholder="First Name" aria-required="true" aria-invalid="true">
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label>Last Name <span class="error">*</span></label>
                                                <input type="text" class="name form-control" name="user_last_name" value="<?= $patient_details->user_last_name ?>" placeholder="Last Name" aria-required="true" aria-invalid="true">
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label>Mobile Number <?= ($this->patient_auth->get_logged_user_id() == $this->patient_auth->get_user_id()) ? '<span class="error">*</span>' : ''; ?></label>
                                                <input type="text" class="onlyNumbers name form-control" name="user_phone_number" value="<?= $patient_details->user_phone_number ?>" placeholder="Mobile Number" <?= !empty($patient_details->user_phone_number) ? 'readonly' : ''; ?> maxlength="10" aria-required="true" aria-invalid="true">
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label>Email</label>
                                                <input type="text" class="name form-control" name="user_email" value="<?= $patient_details->user_email ?>" <?= !empty($patient_details->user_email) ? 'readonly' : ''; ?> placeholder="Email" aria-required="true" aria-invalid="true">
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label>Date Of Birth <span class="error">*</span></label>
                                                <input type="text" class="name dateOfBirth form-control" id="dateOfBirth" name="date_of_birth" value="<?= !empty($patient_details->user_details_dob) ? date('d/m/Y', strtotime($patient_details->user_details_dob)) : '' ?>" placeholder="Date Of Birth" aria-required="true" aria-invalid="true" autocomplete="off">
                                                <span class="input-group-addon">
                                                    <span class="glyphicon glyphicon-calendar"></span>
                                                </span>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="form-group relative">
                                                <label class="dblock">Gender <span class="error">*</span></label>
                                                <div class="form-check form-check-inline static">
                                                    <span class="radio-inline">
                                                        <input type="radio" id="gender_male" name="gender" <?= ($patient_details->user_gender == 'male') ? 'checked' : ''; ?> value="male" class="form-check-input">
                                                        <label class="form-check-label" for="gender_male">Male</label>
                                                    </span>
                                                </div>
                                                <div class="form-check form-check-inline">
                                                    <span class="radio-inline">
                                                        <input type="radio" id="gender_female" name="gender" <?= ($patient_details->user_gender == 'female') ? 'checked' : ''; ?> value="female" class="form-check-input">
                                                        <label class="form-check-label" for="gender_female">Female</label>
                                                    </span>
                                                </div>
                                                <div class="form-check form-check-inline">
                                                    <span class="radio-inline">
                                                        <input type="radio" id="gender_undisclosed" name="gender" <?= ($patient_details->user_gender == 'undisclosed') ? 'checked' : ''; ?> value="undisclosed" class="form-check-input">
                                                        <label class="form-check-label" for="gender_undisclosed">Undisclosed</label>
                                                    </span>
                                                </div>
                                                <div class="form-check form-check-inline">
                                                    <span class="radio-inline">
                                                        <input type="radio" id="gender_other" name="gender" <?= ($patient_details->user_gender == 'other') ? 'checked' : ''; ?> value="other" class="form-check-input">
                                                        <label class="form-check-label" for="gender_other">Other</label>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane fade show" id="patient-personal" role="tabpanel" aria-labelledby="patient-personal-tab">
                                    <div class="row">
                                        <div class="col-lg-3">
                                            <div class="form-group">
                                                <label>Language Preference</label>
                                                <select class="select2 md-form form-control" name="language_id[]" multiple style="width: 100%;">
                                                    <?php
                                                    foreach ($languages as $key => $value) { ?>
                                                        <option <?= in_array($value['language_id'], $patient_details->user_details_languages_known) ? 'selected' : ''; ?> value="<?= $value['language_id']; ?>"><?= $value['language_name']; ?></option>
                                                    <?php } ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-lg-3">
                                            <div class="form-group">
                                                <label>Height (cm)</label>
                                                <input type="text" class="onlyNumbers name form-control" name="user_height" value="<?= $patient_details->user_details_height ?>" placeholder="Height (cm)" maxlength="5" aria-required="true" aria-invalid="true">
                                            </div>
                                        </div>
                                        <div class="col-lg-3">
                                            <div class="form-group">
                                                <label>Weight (kg)</label>
                                                <input type="text" class="onlyNumbers name form-control" name="user_weight" value="<?= PoundToKG($patient_details->user_details_weight); ?>" maxlength="5" placeholder="Weight (kg)" aria-required="true" aria-invalid="true">
                                            </div>
                                        </div>
                                        <div class="col-lg-3">
                                            <div class="form-group">
                                                <label>Blood Group</label>
                                                <select class="mdb-select md-form form-control" name="blood_group">
                                                    <option value="">Select Blood Group</option>
                                                    <?php
                                                    foreach ($blood_groups as $key => $blood_group) { ?>
                                                        <option <?= ($patient_details->user_details_blood_group == $blood_group) ? 'selected' : ''; ?> value="<?= $blood_group; ?>"><?= $blood_group; ?></option>
                                                    <?php } ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label>Address</label>
                                                <textarea name="address_name_one" class="form-control"><?= $patient_details->address_name_one; ?></textarea>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label>Landmark</label>
                                                <textarea name="address_name" class="form-control"><?= $patient_details->address_name; ?></textarea>
                                            </div>
                                        </div>
                                        <div class="col-lg-3">
                                            <div class="form-group">
                                                <label>State</label>
                                                <select class="mdb-select md-form form-control" name="state_id" id="user_state_id">
                                                    <option value="">Select State</option>
                                                    <?php
                                                    foreach ($states as $key => $state) { ?>
                                                        <option <?= ($patient_details->address_state_id == $state['state_id']) ? 'selected' : ''; ?> value="<?= $state['state_id']; ?>"><?= $state['state_name']; ?></option>
                                                    <?php } ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-lg-3">
                                            <div class="form-group">
                                                <label>City</label>
                                                <select class="mdb-select md-form form-control" name="city_id" id="user_city_id">
                                                    <option value="">Select City</option>
                                                    <?php
                                                    foreach ($cities as $key => $city) { ?>
                                                        <option <?= ($patient_details->address_city_id == $city['city_id']) ? 'selected' : ''; ?> value="<?= $city['city_id']; ?>"><?= $city['city_name']; ?></option>
                                                    <?php } ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-lg-3">
                                            <div class="form-group">
                                                <label>Locality</label>
                                                <input type="text" class="name form-control" name="user_locality" value="<?= $patient_details->address_locality ?>" placeholder="Locality" aria-required="true" aria-invalid="true">
                                            </div>
                                        </div>
                                        <div class="col-lg-3">
                                            <div class="form-group">
                                                <label>Pin Code</label>
                                                <input type="text" class="name form-control" name="user_pin_code" value="<?= $patient_details->address_pincode ?>" placeholder="Pin Code" aria-required="true" aria-invalid="true">
                                            </div>
                                        </div>
                                        <div class="col-lg-3">
                                            <div class="form-group">
                                                <label>Emergency Contact Name</label>
                                                <input type="text" class="name form-control" name="emergency_contact_name" value="<?= $patient_details->user_details_emergency_contact_person ?>" placeholder="Emergency Contact Name" aria-required="true" aria-invalid="true">
                                            </div>
                                        </div>
                                        <div class="col-lg-3">
                                            <div class="form-group">
                                                <label>Emergency Contact Number</label>
                                                <input type="text" maxlength="10" class="onlyNumbers name form-control" name="emergency_contact_number" value="<?= $patient_details->user_details_emergency_contact_number ?>" placeholder="Emergency Contact Number" aria-required="true" aria-invalid="true">
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label class="dblock">Marital Status</label>
                                                <div class="form-check form-check-inline">
                                                    <span class="radio-inline">
                                                        <input type="radio" <?= ($patient_details->user_details_marital_status == '2') ? 'checked' : ''; ?> name="marital_status" value="2" class="form-check-input" id="single">
                                                        <label class="form-check-label" for="single">Single</label>
                                                    </span>
                                                </div>
                                                <div class="form-check form-check-inline">
                                                    <span class="radio-inline">
                                                        <input type="radio" <?= ($patient_details->user_details_marital_status == '1') ? 'checked' : ''; ?> name="marital_status" value="1" class="form-check-input" id="married">
                                                        <label class="form-check-label" for="married">Married</label>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-3">
                                            <div class="form-group">
                                                <label>Select ID Proof</label>
                                                <select class="mdb-select md-form form-control" name="id_proof_type" id="id_proof_type">
                                                    <option value="">None</option>
                                                    <option <?= ($patient_details->user_details_id_proof_type=='Aadhar card') ? 'selected' : ''; ?> value="Aadhar card">Aadhar card</option>
                                                    <option <?= ($patient_details->user_details_id_proof_type=='Passport') ? 'selected' : ''; ?> value="Passport">Passport</option>
                                                    <option <?= ($patient_details->user_details_id_proof_type=='Driving License') ? 'selected' : ''; ?> value="Driving License">Driving License</option>
                                                    <option <?= ($patient_details->user_details_id_proof_type=='Pan Card') ? 'selected' : ''; ?> value="Pan Card">Pan Card</option>
                                                </select>
                                                <label style="display: none;" id="id_proof_type-error" class="error" for="id_proof_type">Please select ID proof type</label>
                                            </div>
                                        </div>
                                        <div class="col-lg-3">
                                            <div class="form-group">
                                                <label>ID Proof Detail</label>
                                                <input type="text" maxlength="20" class="name form-control" name="id_proof_detail" id="id_proof_detail" value="<?= $patient_details->user_details_id_proof_detail ?>" placeholder="ID Proof Detail" aria-required="true" aria-invalid="true">
                                                <label style="display: none;" id="id_proof_detail-error" class="error" for="id_proof_detail">Invalid ID proof detail</label>
                                            </div>
                                        </div>
                                        <div class="col-lg-3">
                                            <div class="form-group">
                                                <label>ID Proof Image</label>
                                                <input type="file" name="id_proof_file" id="id_proof_file" class="form-control">
                                                <label style="display: none;" id="id_proof_file-error" class="error"></label>
                                            </div>
                                        </div>
                                        <div class="col-lg-3 id_proof_file">
                                            <img style="cursor: pointer;margin-bottom: 10px;<?= empty($patient_details->user_details_id_proof_image) ? 'display: none;' : '';?>" img_path="<?= $patient_details->user_details_id_proof_image; ?>" class="view_id_proof" src="<?= $patient_details->user_details_id_proof_image_thumb; ?>" />
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane fade show" id="patient-lifestyle" role="tabpanel" aria-labelledby="patient-lifestyle-tab">
                                    <div class="row">
                                        <div class="col-lg-3">
                                            <div class="form-group">
                                                <label>Smoking habits</label>
                                                <select class="mdb-select md-form form-control" name="smoking_habbit_id">
                                                    <option value="">Select option</option>
                                                    <?php
                                                    foreach ($smoking_habbit as $key => $value) { ?>
                                                        <option <?= ($value['smoking_habbit_id'] == $patient_details->user_details_smoking_habbit) ? 'selected' : ''; ?> value="<?= $value['smoking_habbit_id']; ?>"><?= $value['smoking_habbit_name_en']; ?></option>
                                                    <?php } ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-lg-3">
                                            <div class="form-group">
                                                <label>Alcohol consumption</label>
                                                <select class="mdb-select md-form form-control" name="alcohol_id">
                                                    <option value="">Select option</option>
                                                    <?php
                                                    foreach ($alcohol as $key => $value) { ?>
                                                        <option <?= ($value['alcohol_id'] == $patient_details->user_details_alcohol) ? 'selected' : ''; ?> value="<?= $value['alcohol_id']; ?>"><?= $value['alcohol_name_en']; ?></option>
                                                    <?php } ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-lg-3">
                                            <div class="form-group">
                                                <label>Food preference</label>
                                                <select class="mdb-select md-form form-control" name="food_preference">
                                                    <option value="">Select option</option>
                                                    <?php
                                                    foreach ($food_preference as $key => $value) { ?>
                                                        <option <?= ($value['food_preference_id'] == $patient_details->user_details_food_preference) ? 'selected' : ''; ?> value="<?= $value['food_preference_id']; ?>"><?= $value['food_preference_name_en']; ?></option>
                                                    <?php } ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-lg-3">
                                            <div class="form-group">
                                                <label>Occupation</label>
                                                <select class="mdb-select md-form form-control" name="occupation">
                                                    <option value="">Select option</option>
                                                    <?php
                                                    foreach ($occupations as $key => $value) { ?>
                                                        <option <?= ($value['occupation_name_en'] == $patient_details->user_details_occupation) ? 'selected' : ''; ?> value="<?= $value['occupation_name_en']; ?>"><?= $value['occupation_name_en']; ?></option>
                                                    <?php } ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="activity-level__section">
                                        <h4 class="sub-title">Activity Level</h4>
                                        <div class="d-none d-lg-block">
                                            <div class="row">
                                                <div class="col-lg-3">
                                                    <label>Activity</label>
                                                </div>
                                                <div class="col-lg-3">
                                                    <label>Number of days/week</label>
                                                </div>
                                                <div class="col-lg-3">
                                                    <label>Number of minutes</label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="activity-level">
                                            <?php
                                            $total_user_details_activity_level = count($patient_details->user_details_activity_level);
                                            for ($i = 1; $i <= $total_user_details_activity_level; $i++) {
                                                $k = $i - 1;
                                            ?>
                                                <div class="row activity-level-row">
                                                    <div class="col-lg-3">
                                                        <label class="d-lg-none">Activity</label>
                                                        <select class="mdb-select md-form form-control" name="activity_levels[]">
                                                            <option value="">Select option</option>
                                                            <?php
                                                            foreach ($activity_levels as $key => $value) { ?>
                                                                <option <?= (!empty($patient_details->user_details_activity_level[$k]) && $value['activity_level_name_en'] == $patient_details->user_details_activity_level[$k]) ? 'selected' : ''; ?> value="<?= $value['activity_level_name_en']; ?>"><?= $value['activity_level_name_en']; ?></option>
                                                            <?php } ?>
                                                        </select>
                                                    </div>
                                                    <div class="col-lg-3">
                                                        <label class="d-lg-none">Number of days/week</label>
                                                        <select class="mdb-select md-form form-control" name="activity_days[]">
                                                            <option value="">Select option</option>
                                                            <?php
                                                            foreach ($activity_days as $key => $activity_day) { ?>
                                                                <option <?= (!empty($patient_details->user_details_activity_days[$k]) && $activity_day == $patient_details->user_details_activity_days[$k]) ? 'selected' : ''; ?> value="<?= $activity_day; ?>"><?= $activity_day; ?></option>
                                                            <?php } ?>
                                                        </select>
                                                    </div>
                                                    <div class="col-lg-3">
                                                        <label class="d-lg-none">Number of minutes</label>
                                                        <select class="mdb-select md-form form-control" name="activity_hours[]">
                                                            <option value="">Select option</option>
                                                            <?php
                                                            foreach ($activity_hours as $key => $activity_hour) { ?>
                                                                <option <?= (!empty($patient_details->user_details_activity_hours[$k]) && $activity_hour == $patient_details->user_details_activity_hours[$k]) ? 'selected' : ''; ?> value="<?= $activity_hour; ?>"><?= $activity_hour; ?></option>
                                                            <?php } ?>
                                                        </select>
                                                    </div>
                                                    <div class="col-lg-3 text-center text-lg-left">
                                                        <a href="javascript:void(0);" class="add-activity-level" <?= ($i == $total_user_details_activity_level) ? '' : 'style="display: none;"'; ?>>
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
                                                                <path d="M12 0c-6.627 0-12 5.373-12 12s5.373 12 12 12 12-5.373 12-12-5.373-12-12-12zm6 13h-5v5h-2v-5h-5v-2h5v-5h2v5h5v2z" fill="currentColor" />
                                                            </svg>
                                                        </a>
                                                        <a href="javascript:void(0);" <?= ($i != $total_user_details_activity_level) ? '' : 'style="display: none;"'; ?> class="delete-activity-level">
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
                                                                <path d="M12 0c-6.627 0-12 5.373-12 12s5.373 12 12 12 12-5.373 12-12-5.373-12-12-12zm6 13h-12v-2h12v2z" fill="currentColor" />
                                                            </svg>
                                                        </a>
                                                    </div>
                                                </div>
                                            <?php
                                            }
                                            ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane fade show" id="patient-family-history" role="tabpanel" aria-labelledby="patient-family-history-tab">
                                    <h4 class="sub-title">Add family health history</h4>
                                    <div class="family-health-history">
                                        <div class="d-none d-lg-block">
                                            <div class="row">
                                                <div class="col-lg-2">
                                                    <label>Relation</label>
                                                </div>
                                                <div class="col-lg-3">
                                                    <label>Condition</label>
                                                </div>
                                                <div class="col-lg-2">
                                                    <label>Since when</label>
                                                </div>
                                                <div class="col-lg-4">
                                                    <label>Comments</label>
                                                </div>
                                                <div class="col-lg-1"></div>
                                            </div>
                                        </div>

                                        <?php
                                        foreach ($family_medical_history as $key => $history_row) {
                                            $k = $key + 1;
                                        ?>
                                            <div class="row family-health-history-row">
                                                <div class="col-lg-2">
                                                    <label class="d-lg-none">Relation</label>
                                                    <select class="mdb-select md-form form-control" name="family_relation[]">
                                                        <option value="">Select option</option>
                                                        <?php
                                                        foreach ($family_relation as $value) { ?>
                                                            <option <?= ($value['id'] == $history_row['family_medical_history_relation']) ? 'selected' : '' ?> value="<?= $value['id']; ?>"><?= $value['name']; ?></option>
                                                        <?php } ?>
                                                    </select>
                                                </div>
                                                <div class="col-lg-3">
                                                    <label class="d-lg-none">Condition</label>
                                                    <select class="select2 md-form form-control" name="family_medical_conditions[<?= $key; ?>][]" multiple="multiple" style="width: 100%;" data-placeholder="Condition">
                                                        <option value="">Select option</option>
                                                        <?php
                                                        foreach ($medical_conditions as $value) { ?>
                                                            <option <?= in_array($value['medical_condition_name'], explode(',', $history_row['family_medical_history_medical_condition_id'])) ? 'selected' : '' ?> value="<?= $value['medical_condition_name']; ?>"><?= $value['medical_condition_name']; ?></option>
                                                        <?php } ?>
                                                    </select>
                                                </div>
                                                <div class="col-lg-2">
                                                    <label class="d-lg-none">Since when</label>
                                                    <input type="text" class="name familySinceWhen" name="family_since_when[]" placeholder="DD/MM/YYYY" value="<?= date("d/m/Y", strtotime($history_row['family_medical_history_date'])) ?>" aria-required="true" aria-invalid="true" autocomplete="off">
                                                </div>
                                                <div class="col-lg-4">
                                                    <label class="d-lg-none">Comments</label>
                                                    <input type="text" class="name" value="<?= $history_row['family_medical_history_comment'] ?>" name="family_comment[]" placeholder="Comment" aria-required="true" aria-invalid="true">
                                                </div>
                                                <div class="col-lg-1 px-0 text-center text-lg-left">
                                                    <a href="javascript:void(0);" <?= ($k == count($family_medical_history)) ? '' : 'style="display: none;"'; ?> class="add-family-history">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
                                                            <path d="M12 0c-6.627 0-12 5.373-12 12s5.373 12 12 12 12-5.373 12-12-5.373-12-12-12zm6 13h-5v5h-2v-5h-5v-2h5v-5h2v5h5v2z" fill="currentColor" />
                                                        </svg>
                                                    </a>
                                                    <a href="javascript:void(0);" <?= ($k != count($family_medical_history)) ? '' : 'style="display: none;"'; ?> class="delete-family-history">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
                                                            <path d="M12 0c-6.627 0-12 5.373-12 12s5.373 12 12 12 12-5.373 12-12-5.373-12-12-12zm6 13h-12v-2h12v2z" fill="currentColor" />
                                                        </svg>
                                                    </a>
                                                </div>
                                            </div>
                                        <?php } ?>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-11">
                                            <div class="form-group">
                                                <label>Chronic diseases</label>
                                                <select class="md-form form-control" name="chronic_diseases[]" multiple="multiple" id="chronic_diseases" style="width: 100%;" data-placeholder="Conditions">
                                                    <?php
                                                    foreach ($medical_conditions as $key => $value) { ?>
                                                        <option value="<?= $value['medical_condition_name']; ?>"><?= $value['medical_condition_name']; ?></option>
                                                    <?php } ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <div class="patient-injuries">
                                            <label>Injuries</label>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <div class="patient-surgeries">
                                            <label>Surgeries</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane fade show" id="patient-allergies" role="tabpanel" aria-labelledby="patient-allergies-tab">
                                    <div class="form-group">
                                        <div class="patient-food-allergies">
                                            <label>Food Allergies</label>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <div class="patient-medicine-allergies">
                                            <label>Medicine Allergies</label>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <div class="patient-others-allergies">
                                            <label>Others Allergies</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane fade show" id="patient-member" role="tabpanel" aria-labelledby="patient-member-tab">
                                    <div class="row btns-row"> 
                                        <div class="col-lg-12 col-12">
                                            <div class="alert alert-success" id="message2" style="display: none;"></div>
                                        </div>
                                        <div class="col-lg-12 col-12 text-right">
                                            <a href="<?= site_url('patient/add_member'); ?>" class="btns add-report-btn">Add Member</a>
                                        </div>
                                    </div>
                                    <table class="table table-striped table-responsive-grid">
                                        <thead>
                                            <tr>
                                                <th>Name</th>
                                                <th>Relation</th>
                                                <th align="right"></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            if (!empty($family_members) && count($family_members) > 0) {
                                            foreach ($family_members as $key => $value) {
                                            ?>
                                                <tr id="<?= encrypt_decrypt($value->user_id, 'encrypt'); ?>">
                                                    <td data-title="Name"><?= $value->user_first_name . ' ' . $value->user_last_name; ?></td>
                                                    <td data-title="Relation"><?= $value->relation; ?></td>
                                                    <td align="right">
                                                        <span class="icon delete-icon">
                                                            <?php if(!empty($value->user_phone_number)) { ?>
                                                            <a href="<?= site_url("patient/remove_member/" . encrypt_decrypt($value->user_id, 'encrypt')) ?>" title="Remove" onclick="return confirm('Are you sure to remove this member?')">
                                                                <i class="fa fa-trash"></i>
                                                            </a>
                                                        <?php } else { ?>
                                                            <a class="remove_member" user_name="<?= $value->user_first_name . ' ' . $value->user_last_name; ?>" user_email="<?= $value->user_email; ?>" user_id="<?= encrypt_decrypt($value->user_id, 'encrypt'); ?>" href="javascript:void(0);" title="Remove">
                                                                <i class="fa fa-trash"></i>
                                                            </a>
                                                        <?php } ?>
                                                        </span>
                                                    </td>
                                                </tr>
                                            <?php }
                                            } else { ?>
                                                <td colspan="3" class="text-center no-record">No record found</td>
                                            <?php } ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="col-lg-12 update-success alert alert-success" id="message" style="display: none;">

                            </div>
                            <div class="update-btn">
                                <button type="submit" id="reg-btn" name="submit">Update</button>
                                <img class="loader-img" style="display: none;" src="<?= site_url(); ?>assets/admin/images/ajax-loader.gif">
                            </div>

                            <center>
                                <div id="result"></div>
                            </center>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
    <div class="row patient-food-allergies-html" style="display: none;">
        <div class="col-11">
            <input type="text" class="name form-control" name="patient_food_allergies[]" placeholder="Food Allergies" aria-required="true" aria-invalid="true">
        </div>
        <div class="col-1 px-0">
            <a href="javascript:void(0);" class="add-patient-food-allergies">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
                    <path d="M12 0c-6.627 0-12 5.373-12 12s5.373 12 12 12 12-5.373 12-12-5.373-12-12-12zm6 13h-5v5h-2v-5h-5v-2h5v-5h2v5h5v2z" fill="currentColor" />
                </svg>
            </a>
            <a href="javascript:void(0);" style="display: none;" class="delete-patient-food-allergies">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
                    <path d="M12 0c-6.627 0-12 5.373-12 12s5.373 12 12 12 12-5.373 12-12-5.373-12-12-12zm6 13h-12v-2h12v2z" fill="currentColor" />
                </svg>
            </a>
        </div>
    </div>
    <div class="row patient-medicine-allergies-html" style="display: none;">
        <div class="col-11">
            <input type="text" class="name form-control" name="patient_medicine_allergies[]" placeholder="Medicine Allergies" aria-required="true" aria-invalid="true">
        </div>
        <div class="col-1 px-0">
            <a href="javascript:void(0);" class="add-patient-medicine-allergies">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
                    <path d="M12 0c-6.627 0-12 5.373-12 12s5.373 12 12 12 12-5.373 12-12-5.373-12-12-12zm6 13h-5v5h-2v-5h-5v-2h5v-5h2v5h5v2z" fill="currentColor" />
                </svg>
            </a>
            <a href="javascript:void(0);" style="display: none;" class="delete-patient-medicine-allergies">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
                    <path d="M12 0c-6.627 0-12 5.373-12 12s5.373 12 12 12 12-5.373 12-12-5.373-12-12-12zm6 13h-12v-2h12v2z" fill="currentColor" />
                </svg>
            </a>
        </div>
    </div>
    <div class="row patient-others-allergies-html" style="display: none;">
        <div class="col-11">
            <input type="text" class="name form-control" name="patient_others_allergies[]" placeholder="Others Allergies" aria-required="true" aria-invalid="true">
        </div>
        <div class="col-1 px-0">
            <a href="javascript:void(0);" class="add-patient-others-allergies">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
                    <path d="M12 0c-6.627 0-12 5.373-12 12s5.373 12 12 12 12-5.373 12-12-5.373-12-12-12zm6 13h-5v5h-2v-5h-5v-2h5v-5h2v5h5v2z" fill="currentColor" />
                </svg>
            </a>
            <a href="javascript:void(0);" style="display: none;" class="delete-patient-others-allergies">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
                    <path d="M12 0c-6.627 0-12 5.373-12 12s5.373 12 12 12 12-5.373 12-12-5.373-12-12-12zm6 13h-12v-2h12v2z" fill="currentColor" />
                </svg>
            </a>
        </div>
    </div>
    <div class="row patient-injuries-html" style="display: none;">
        <div class="col-11">
            <input type="text" class="name form-control" name="patient_injuries[]" placeholder="Injuries" aria-required="true" aria-invalid="true">
        </div>
        <div class="col-1 px-0">
            <a href="javascript:void(0);" class="add-patient-injuries">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
                    <path d="M12 0c-6.627 0-12 5.373-12 12s5.373 12 12 12 12-5.373 12-12-5.373-12-12-12zm6 13h-5v5h-2v-5h-5v-2h5v-5h2v5h5v2z" fill="currentColor" />
                </svg>
            </a>
            <a href="javascript:void(0);" style="display: none;" class="delete-patient-injuries">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
                    <path d="M12 0c-6.627 0-12 5.373-12 12s5.373 12 12 12 12-5.373 12-12-5.373-12-12-12zm6 13h-12v-2h12v2z" fill="currentColor" />
                </svg>
            </a>
        </div>
    </div>
    <div class="row patient-surgeries-html" style="display: none;">
        <div class="col-11">
            <input type="text" class="name form-control" name="patient_surgeries[]" placeholder="Surgeries" aria-required="true" aria-invalid="true">
        </div>
        <div class="col-1 px-0">
            <a href="javascript:void(0);" class="add-patient-surgeries">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
                    <path d="M12 0c-6.627 0-12 5.373-12 12s5.373 12 12 12 12-5.373 12-12-5.373-12-12-12zm6 13h-5v5h-2v-5h-5v-2h5v-5h2v5h5v2z" fill="currentColor" />
                </svg>
            </a>
            <a href="javascript:void(0);" style="display: none;" class="delete-patient-surgeries">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
                    <path d="M12 0c-6.627 0-12 5.373-12 12s5.373 12 12 12 12-5.373 12-12-5.373-12-12-12zm6 13h-12v-2h12v2z" fill="currentColor" />
                </svg>
            </a>
        </div>
    </div>
    <div class="row family-health-history-html" style="display: none;">
        <div class="col-lg-2">
            <label class="d-lg-none">Relation</label>
            <select class="mdb-select md-form form-control" name="family_relation[]">
                <option value="">Select option</option>
                <?php
                foreach ($family_relation as $key => $value) { ?>
                    <option value="<?= $value['id']; ?>"><?= $value['name']; ?></option>
                <?php } ?>
            </select>
        </div>
        <div class="col-lg-3">
            <label class="d-lg-none">Condition</label>
            <select class="family_medical_conditions md-form form-control" multiple="multiple" style="width: 100%;" name="family_medical_conditions[][]" data-placeholder="Condition">
                <option value="">Select option</option>
                <?php
                foreach ($medical_conditions as $key => $value) { ?>
                    <option value="<?= $value['medical_condition_name']; ?>"><?= $value['medical_condition_name']; ?></option>
                <?php } ?>
            </select>
        </div>
        <div class="col-lg-2">
            <label class="d-lg-none">Since when</label>
            <input type="text" class="name familySinceWhen form-control" name="family_since_when[]" placeholder="DD/MM/YYYY" aria-required="true" aria-invalid="true" autocomplete="off">
        </div>
        <div class="col-lg-4">
            <label class="d-lg-none">Comments</label>
            <input type="text" class="name form-control" name="family_comment[]" placeholder="Comment" aria-required="true" aria-invalid="true">
        </div>
        <div class="col-lg-1 px-0 text-center text-lg-left">
            <a href="javascript:void(0);" class="add-family-history">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
                    <path d="M12 0c-6.627 0-12 5.373-12 12s5.373 12 12 12 12-5.373 12-12-5.373-12-12-12zm6 13h-5v5h-2v-5h-5v-2h5v-5h2v5h5v2z" fill="currentColor" />
                </svg>
            </a>
            <a href="javascript:void(0);" style="display: none;" class="delete-family-history">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
                    <path d="M12 0c-6.627 0-12 5.373-12 12s5.373 12 12 12 12-5.373 12-12-5.373-12-12-12zm6 13h-12v-2h12v2z" fill="currentColor" />
                </svg>
            </a>
        </div>
    </div>
    <div class="row activity-level-html" style="display: none;">
        <div class="col-lg-3">
            <select class="mdb-select md-form form-control" name="activity_levels[]">
                <option value="">Select option</option>
                <?php
                foreach ($activity_levels as $key => $value) { ?>
                    <option value="<?= $value['activity_level_name_en']; ?>"><?= $value['activity_level_name_en']; ?></option>
                <?php } ?>
            </select>
        </div>
        <div class="col-lg-3">
            <select class="mdb-select md-form form-control" name="activity_days[]">
                <option value="">Select option</option>
                <?php
                foreach ($activity_days as $key => $activity_day) { ?>
                    <option value="<?= $activity_day; ?>"><?= $activity_day; ?></option>
                <?php } ?>
            </select>
        </div>
        <div class="col-lg-3">
            <select class="mdb-select md-form form-control" name="activity_hours[]">
                <option value="">Select option</option>
                <?php
                foreach ($activity_hours as $key => $activity_hour) { ?>
                    <option value="<?= $activity_hour; ?>"><?= $activity_hour; ?></option>
                <?php } ?>
            </select>
        </div>
        <div class="col-lg-3 text-center text-lg-left">
            <a href="javascript:void(0);" class="add-activity-level">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
                    <path d="M12 0c-6.627 0-12 5.373-12 12s5.373 12 12 12 12-5.373 12-12-5.373-12-12-12zm6 13h-5v5h-2v-5h-5v-2h5v-5h2v5h5v2z" fill="currentColor" />
                </svg>
            </a>
            <a href="javascript:void(0);" style="display: none;" class="delete-activity-level">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
                    <path d="M12 0c-6.627 0-12 5.373-12 12s5.373 12 12 12 12-5.373 12-12-5.373-12-12-12zm6 13h-12v-2h12v2z" fill="currentColor" />
                </svg>
            </a>
        </div>
    </div>
    <?php $this->load->view('patient/footer'); ?>
    <?php $this->load->view('patient/footer_layout'); ?>
    <script type="text/javascript">
        setTimeout(function() {
            <?php if (count($patient_details->user_details_activity_level) == 0) { ?>
                activity_levels();
            <?php } ?>
            <?php if (count($family_medical_history) == 0) { ?>
                family_health_history();
            <?php } ?>
            chronic_diseases_selected("<?= $patient_details->user_details_chronic_diseases; ?>");
            injuries_selected("<?= $patient_details->user_details_injuries; ?>");
            surgeries_selected("<?= $patient_details->user_details_surgeries; ?>");
            food_allergies_selected("<?= $patient_details->user_details_food_allergies; ?>");
            medicine_allergies_selected("<?= $patient_details->user_details_medicine_allergies; ?>");
            other_allergies_selected("<?= $patient_details->user_details_other_allergies; ?>");
        }, 3000);
    </script>
<!-- Member remove Modal -->
<div class="modal fade" id="memberRemoveModal" tabindex="-1" role="dialog" aria-labelledby="memberRemoveModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="member_name"></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form name="member_remove_frm" id="member_remove_frm" method="post" onsubmit="return false;">
                <input type="hidden" name="member_id" id="member_id" value="">
                <div class="modal-body">
                    <center>
                        <div id="member_remove_error"></div>
                    </center>
                    <div class="row">
                        <div class="col-lg-12 col-12">
                            <div class="alert alert-warning">Enter new details below for continued access to account</div>
                        </div>
                        <div class="col-lg-12">
                            <div class="form-group">
                                <label>Mobile No <span class="error">*</span></label>
                                <input type="text" class="name form-control" name="member_mobile_no" id="member_mobile_no" value="" maxlength="10" placeholder="Mobile No" aria-required="true" aria-invalid="true">
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <div class="form-group">
                                <label>Email</label>
                                <input type="text" class="name form-control" name="member_email" id="member_email" value="" placeholder="Email" aria-required="true" aria-invalid="true">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="contact-form">
                        <a href="javascript:void(0);" class="btns" data-dismiss="modal">Cancel</a>
                        <button type="submit" class="btns-hide" id="member_remove_btn">Remove</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- End Member remove Modal -->
<!-- ID proof document view Modal -->
<div class="modal fade" id="idProofViewModal" tabindex="-1" role="dialog" aria-labelledby="idProofViewModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-center">ID Proof Image</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-lg-12 col-12 text-center">
                        <img class="id_proof_img_view" src="" />
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- End ID proof document view Modal -->
<script type="text/javascript">
    $("#message1").delay(5000).slideUp(300);
</script>