<?php

/**
 * 
 * This controller use for user related activity
 * 
 * 
 * Modified Data :- 2018-03-29
 */
class Patient extends MY_Controller {

    public function __construct() {
        parent::__construct();
        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', 0);

        ignore_user_abort(true);
        set_time_limit(0);
        $this->load->model("User_model");
    }

    public function add_patient_post() {

        $first_name = !empty($this->post_data['first_name']) ? trim($this->Common_model->escape_data($this->post_data['first_name'])) : "";
        $last_name = !empty($this->post_data['last_name']) ? trim($this->Common_model->escape_data($this->post_data['last_name'])) : "";
        $email = !empty($this->post_data['email']) ? trim($this->Common_model->escape_data($this->post_data['email'])) : "";
        $gender = !empty($this->post_data['gender']) ? trim($this->Common_model->escape_data($this->post_data['gender'])) : "";
        $user_type = !empty($this->post_data['user_type']) ? trim($this->Common_model->escape_data($this->post_data['user_type'])) : "";
        $dob = !empty($this->post_data['dob']) ? trim(($this->post_data['dob'])) : "";
        $weight = !empty($this->post_data['weight']) ? trim(($this->post_data['weight'])) : "";
        $height = !empty($this->post_data['height']) ? trim(($this->post_data['height'])) : "";
        $blood_group = !empty($this->post_data['blood_group']) ? trim(($this->post_data['blood_group'])) : "";
        $occupation = !empty($this->post_data['occupation']) ? trim(($this->post_data['occupation'])) : "";
        $address = !empty($this->post_data['address']) ? trim(($this->post_data['address'])) : "";
        $address1 = !empty($this->post_data['address1']) ? trim(($this->post_data['address1'])) : "";
        $city_id = !empty($this->post_data['city_id']) ? trim($this->Common_model->escape_data($this->post_data['city_id'])) : "";
        $state_id = !empty($this->post_data['state_id']) ? trim($this->Common_model->escape_data($this->post_data['state_id'])) : "";
        $country_id = !empty($this->post_data['country_id']) ? trim($this->Common_model->escape_data($this->post_data['country_id'])) : "";
        $pincode = !empty($this->post_data['pincode']) ? trim(($this->post_data['pincode'])) : "";
        $latitude = !empty($this->post_data['latitude']) ? trim(($this->post_data['latitude'])) : "";
        $longitude = !empty($this->post_data['longitude']) ? trim(($this->post_data['longitude'])) : "";
        $phone_number = !empty($this->post_data['phone_number']) ? trim(($this->post_data['phone_number'])) : "";
        $languages = !empty($this->post_data['languages']) ? trim(($this->post_data['languages'])) : "";
        $share_status = !empty($this->post_data['share_status']) ? trim(($this->post_data['share_status'])) : "";
        $referred_by = !empty($this->post_data['referred_by']) ? trim(($this->post_data['referred_by'])) : "";


        try {

            if (empty($first_name) ||
                    empty($last_name) ||
                    empty($phone_number) ||
                    empty($email) ||
                    empty($dob) ||
                    empty($blood_group) ||
                    empty($languages) ||
                    empty($height) ||
                    empty($weight) ||
                    empty($occupation) ||
                    empty($address) ||
                    empty($latitude) ||
                    empty($longitude) ||
                    empty($country_id) ||
                    empty($state_id) ||
                    empty($city_id) ||
                    empty($pincode)
            ) {
                $this->bad_request();
            }

            $get_role_details = $this->Common_model->get_the_role($this->user_id);
            if (!empty($get_role_details['user_role_data'])) {
                $permission_data = array(
                    'role_data' => $get_role_details['user_role_data'],
                    'module' => 1,
                    'key' => 1
                );
                $check_module_permission = $this->check_module_permission($permission_data);
                if ($check_module_permission == 2) {
                    $this->my_response['status'] = false;
                    $this->my_response['message'] = lang('permission_error');
                    $this->send_response();
                }
            }

            if (validate_phone_number($phone_number)) {
                $this->my_response['status'] = false;
                $this->my_response['message'] = lang("invalid_phone_number");
                $this->send_response();
            }

            if (validate_email($email)) {
                $this->my_response['status'] = false;
                $this->my_response['message'] = lang("invalid_email");
                $this->send_response();
            }

            if (validate_pincode($pincode)) {
                $this->my_response['status'] = false;
                $this->my_response['message'] = lang("invalid_pincode");
                $this->send_response();
            }

            if (
                    (!empty($first_name) && validate_characters($first_name)) ||
                    (!empty($last_name) && validate_characters($last_name))
            ) {
                $this->my_response['status'] = false;
                $this->my_response['message'] = lang("mycontroller_invalid_request");
                $this->send_response();
            }

            //check email id exists or not
            if (!empty($email)) {
                $check_email_sql = "SELECT 
                                        user_email, user_email_verified
                                 FROM 
                                    " . TBL_USERS . " 
                                 WHERE 
                                      user_email = '" . $email . "' 
                                 AND         
                                      user_status != 9
                                 
                                 AND 
                                      user_type = '" . $user_type . "' ";

                $check_email = $this->Common_model->get_single_row_by_query($check_email_sql);

                if (!empty($check_email)) {
                    $this->my_response['status'] = false;
                    $this->my_response['message'] = lang("user_register_email_exist");
                    $this->send_response();
                }
            }
            $check_number = $this->User_model->get_details_by_number($phone_number, $user_type);

            if (!empty($check_number) && count($check_number) > 0) {
                if (isset($check_number['user_phone_verified']) && $check_number['user_phone_verified'] == 1) {
                    $this->my_response['status'] = false;
                    $this->my_response['user_phone_is_verified'] = '1';
                    $this->my_response['message'] = lang("user_register_phone_number_exist");
                    $this->send_response();
                } else if (isset($check_number['user_phone_verified']) && $check_number['user_phone_verified'] == 2) {
                    $this->my_response['status'] = true;
                    $this->my_response['user_phone_is_verified'] = '2';
                    $this->my_response['message'] = lang("user_register_phone_number_exist_but_not_verified");
                    $this->send_response();
                }
            }

            if ($share_status == true) {
                $share_status = 1;
            } else {
                $share_status = 2;
            }


            $insert_data = array();

            $password = $this->Common_model->generate_random_string(4, 1, 1, 1);
            $unique_id = strtoupper($this->Common_model->escape_data(str_rand_access_token(8)));

            if (!empty($first_name)) {
                $insert_data['user_first_name'] = ucfirst($first_name);
            }

            if (!empty($last_name)) {
                $insert_data['user_last_name'] = ucfirst($last_name);
            }

            if (!empty($email)) {
                $insert_data['user_email'] = $email;
            }

            if (!empty($gender)) {
                $insert_data['user_gender'] = $gender;
            }

            $insert_data['user_password'] = password_hash(sha1($password), PASSWORD_BCRYPT);
            $insert_data['user_referred_by'] = $referred_by;
            $insert_data['user_unique_id'] = $unique_id;
            $insert_data['user_caregiver_id'] = '';
            $insert_data['user_created_at'] = $this->utc_time_formated;
            $insert_data['user_phone_number'] = $phone_number;
            $insert_data['user_plan_id'] = DEFAULT_USER_PLAN_ID;

            $user_id = $this->User_model->insert(TBL_USERS, $insert_data);

            //update the addrress
            $update_address_data = array();
            if (!empty($address)) {
                $update_address_data['address_name'] = $address;
            }
            if (!empty($address1)) {
                $update_address_data['address_name_one'] = $address1;
            }

            if (!empty($city_id)) {
                $update_address_data['address_city_id'] = $city_id;
            }

            if (!empty($state_id)) {
                $update_address_data['address_state_id'] = $state_id;
            }

            if (!empty($country_id)) {
                $update_address_data['address_country_id'] = $country_id;
            }

            if (!empty($pincode)) {
                $update_address_data['address_pincode'] = $pincode;
            }

            if (!empty($latitude)) {
                $update_address_data['address_latitude'] = $latitude;
            }

            if (!empty($longitude)) {
                $update_address_data['address_longitude'] = $longitude;
            }

            $address_is_update = $this->User_model->update_address($user_id, $update_address_data);

            //update the user details
            $update_user_details = array();
            if (!empty($dob)) {
                $update_user_details['user_details_dob'] = $dob;
            }

            if (!empty($height)) {
                $update_user_details['user_details_height'] = $height;
            }

            if (!empty($weight)) {
                $update_user_details['user_details_weight'] = $weight;
            }

            if (!empty($blood_group)) {
                $update_user_details['user_details_blood_group'] = $blood_group;
            }

            if (!empty($languages)) {
                $update_user_details['user_details_languages_known'] = $languages;
            }

            if (!empty($occupation)) {
                $update_user_details['user_details_occupation'] = $occupation;
            }
            $update_user_details['user_details_agree_medical_share'] = $share_status;

            $user_details_is_update = $this->User_model->update_user_details($user_id, $update_user_details);

            if ($user_id > 0 || $address_is_update > 0 || $user_details_is_update > 0) {

                //by default 2 way authentication on for the patient
                $setting_array = array();
                $setting_array[] = array(
                    'id' => "1",
                    'name' => "data security",
                    'status' => "1"
                );
                $insert_setting_array = array(
                    'setting_user_id' => $user_id,
                    'setting_clinic_id' => '',
                    'setting_data' => json_encode($setting_array),
                    'setting_type' => 2,
                    'setting_data_type' => 1,
                    'setting_created_at' => $this->utc_time_formated
                );
                $this->Common_model->insert(TBL_SETTING, $insert_setting_array);

                //store the user authentication details
                $user_auth_insert_array = array(
                    'auth_user_id' => $user_id,
                    'auth_type' => 2,
                    'auth_code' => '',
                    'auth_otp_expiry_time' => '',
                    'auth_created_at' => $this->utc_time_formated
                );
                $this->User_model->store_auth_detail($user_auth_insert_array);

                $reset_token = str_rand_access_token(20);

                $user_auth_email_insert_array = array(
                    'auth_user_id' => $user_id,
                    'auth_type' => 1,
                    'auth_code' => $reset_token,
                    'auth_otp_expiry_time' => date('Y-m-d H:i:s', strtotime(OTP_EXPIRE_TOKEN_TIME, strtotime($this->utc_time_formated))),
                    'auth_created_at' => $this->utc_time_formated
                );
                $this->User_model->store_auth_detail($user_auth_email_insert_array);

                $verify_link = DOMAIN_URL . "verifyaccount/" . $reset_token;

                //this is use for get view and store data in variable
                //EMAIL TEMPLATE START BY PRAGNESH
                $this->load->model('Emailsetting_model');
                $email_template_data = $this->Emailsetting_model->get_emailtemplate_by_id(14);
                $parse_arr = array(
                    '{Email}' => $email,
                    '{Password}' => $password,
                    '{EmailPasswordImage}' => 1,
                    '{UniqueId}' => $unique_id,
                    '{VerificationLink}' => $verify_link,
                    '{WebUrl}' => DOMAIN_URL,
                    '{AppName}' => APP_NAME,
                    '{MailContactNumber}' => $email_template_data['email_static_data']['contact_number'],
                    '{MailEmailAddress}' => $email_template_data['email_static_data']['email_id'],
                    '{MailCompanyName}' => $email_template_data['email_static_data']['company_name'],
                    '{CopyRightsYear}' => date('Y')
                );
                $message = replace_values_in_string($parse_arr, $email_template_data['email_template_message']);
                $subject = $email_template_data['email_template_subject'];
                //EMAIL TEMPLATE END BY PRAGNESH
                //this function help you to send mail to single ot multiple users
                $this->send_email(array($email => $email), $subject, $message);

                $this->my_response['status'] = true;
                $this->my_response['message'] = lang('patient_added');
                $this->my_response['patient_id'] = $user_id;
            } else {
                $this->my_response['status'] = false;
                $this->my_response['message'] = lang('failure');
            }
            $this->send_response();
        } catch (Exception $e) {
            $this->error = $e->getMessage();
            $this->store_error();
        }
    }

    public function search_patient_post() {

        try {
            $search_text = !empty($this->post_data['search_text']) ? trim($this->Common_model->escape_data($this->post_data['search_text'])) : "";
            $doctor_id = !empty($this->post_data['doctor_id']) ? trim($this->Common_model->escape_data($this->post_data['doctor_id'])) : "";

            if (empty($search_text)) {
                $this->bad_request();
            }

            $get_search_patient_data = $this->User_model->search_patient($search_text, $doctor_id);

            if (!empty($get_search_patient_data)) {
                $this->my_response['status'] = true;
                $this->my_response['data'] = $get_search_patient_data;
            } else {
                $this->my_response['status'] = false;
                $this->my_response['data'] = array();
            }
            $this->send_response();
        } catch (ErrorException $ex) {
            $this->error = $ex->getMessage();
            $this->store_error();
        }
    }

    public function add_patient_diseases_post() {
        try {

            $doctor_id = !empty($this->post_data['doctor_id']) ? trim($this->Common_model->escape_data($this->post_data['doctor_id'])) : '';
            $patient_id = !empty($this->post_data['patient_id']) ? trim($this->Common_model->escape_data($this->post_data['patient_id'])) : '';
            $disease_id = !empty($this->post_data['disease_id']) ? trim($this->Common_model->escape_data($this->post_data['disease_id'])) : '';

            if (empty($doctor_id) ||
                    empty($patient_id) ||
                    empty($disease_id)
            ) {
                $this->bad_request();
            } else {
                
            }
        } catch (ErrorException $ex) {
            $this->error = $ex->getMessage();
            $this->store_error();
        }
    }

    /**
     * Description :- This function is used to get the primary doctor of the user
     */
    public function my_primary_doctor_post() {
        try {
            $patient_id = !empty($this->post_data['patient_id']) ? trim($this->Common_model->escape_data($this->post_data['patient_id'])) : '';
            if (empty($patient_id)) {
                $this->bad_request();
            }
            $this->load->model('Doctor_model', 'doctor');
            $primary_doctor_data = $this->doctor->get_primary_doctor($patient_id, 1, 1, true);
            if (!empty($primary_doctor_data)) {
                $this->my_response['status'] = true;
                $this->my_response['message'] = lang('common_detail_found');
                $this->my_response['primary_doctor_data'] = array(
					"doctor_color_code" => PRIMARY_DOCTOR_COLOR_CODE,
                    "doctor_user_id" => $primary_doctor_data['user_id'],
                    "doctor_first_name" => $primary_doctor_data['user_first_name'],
                    "doctor_last_name" => $primary_doctor_data['user_last_name'],
                    "doctor_photo" => $primary_doctor_data['user_photo_filepath'],
                    "doctor_experience" => $primary_doctor_data['doctor_detail_year_of_experience'],
                    "doctor_specialisation" => implode(',', array_unique(explode(',', $primary_doctor_data['specialization']))),
                    "doctor_fees" => $primary_doctor_data['doctor_clinic_mapping_fees'],
                    "doctor_qualification" => $primary_doctor_data['doctor_qualification_degree'],
                    "doctor_clinic_id" => $primary_doctor_data['clinic_id'],
                    "doctor_timing_start" => $primary_doctor_data['doctor_clinic_doctor_session_1_start_time'],
                    "doctor_timing_end" => $primary_doctor_data['doctor_clinic_doctor_session_1_end_time'],
                    "doctor_desc" => $primary_doctor_data['doctor_detail_desc'],
                    "doctor_known_languages" => $primary_doctor_data['language'],
                    "doctor_clinic_service" => $primary_doctor_data['clinic_services'],
                    "doctor_verification_status" => $primary_doctor_data['user_status'],
                    "doctor_phone_number" => $primary_doctor_data['user_phone_number'],
                    "doctor_speciality" => implode(',', array_unique(explode(',', $primary_doctor_data['speciality']))),
                );
            } else {
                $this->my_response['status'] = false;
                $this->my_response['message'] = lang('common_detail_not_found');
                $this->my_response['primary_doctor_data'] = array();
            }
            $this->send_response();
        } catch (ErrorException $ex) {
            $this->error = $ex->getMessage();
            $this->store_error();
        }
    }

    /**
     * Description :- This function is used to add the vital for the patient by doctor
     * 
     * 
     * 
     * 
     */
    public function add_vital_for_patient_post() {

        try {

            $clinic_id = !empty($this->Common_model->escape_data($this->post_data['clinic_id'])) ? trim($this->Common_model->escape_data($this->post_data['clinic_id'])) : '';
            $doctor_id = !empty($this->Common_model->escape_data($this->post_data['doctor_id'])) ? trim($this->Common_model->escape_data($this->post_data['doctor_id'])) : '';
            $patient_id = !empty($this->Common_model->escape_data($this->post_data['patient_id'])) ? trim($this->Common_model->escape_data($this->post_data['patient_id'])) : '';
            $appointment_id = !empty($this->Common_model->escape_data($this->post_data['appointment_id'])) ? trim($this->Common_model->escape_data($this->post_data['appointment_id'])) : '';
            $date = !empty($this->Common_model->escape_data($this->post_data['date'])) ? trim($this->Common_model->escape_data($this->post_data['date'])) : '';
            $sp2o = !empty($this->Common_model->escape_data($this->post_data['sp2o'])) ? trim($this->Common_model->escape_data($this->post_data['sp2o'])) : '';
            $weight = !empty($this->Common_model->escape_data($this->post_data['weight'])) ? trim($this->Common_model->escape_data($this->post_data['weight'])) : '';
            $blood_pressure_systolic = !empty($this->Common_model->escape_data($this->post_data['blood_pressure_systolic'])) ? trim($this->Common_model->escape_data($this->post_data['blood_pressure_systolic'])) : '';
            $blood_pressure_diastolic = !empty($this->Common_model->escape_data($this->post_data['blood_pressure_diastolic'])) ? trim($this->Common_model->escape_data($this->post_data['blood_pressure_diastolic'])) : '';
            $blood_pressure_type = !empty($this->Common_model->escape_data($this->post_data['blood_pressure_type'])) ? trim($this->Common_model->escape_data($this->post_data['blood_pressure_type'])) : '';
            $pulse = !empty($this->Common_model->escape_data($this->post_data['pulse'])) ? trim($this->Common_model->escape_data($this->post_data['pulse'])) : '';
            $temperature = !empty($this->Common_model->escape_data($this->post_data['temperature'])) ? trim($this->Common_model->escape_data($this->post_data['temperature'])) : '';
            $temperature_type = !empty($this->Common_model->escape_data($this->post_data['temperature_type'])) ? trim($this->Common_model->escape_data($this->post_data['temperature_type'])) : '';
            $temperature_taken = !empty($this->Common_model->escape_data($this->post_data['temperature_taken'])) ? trim($this->Common_model->escape_data($this->post_data['temperature_taken'])) : '';
            $resp = !empty($this->Common_model->escape_data($this->post_data['resp'])) ? trim($this->Common_model->escape_data($this->post_data['resp'])) : '';

            if (empty($clinic_id) ||
                    empty($doctor_id) ||
                    empty($patient_id) ||
                    empty($appointment_id) ||
                    empty($date)
            ) {
                $this->bad_request();
            }

            $get_role_details = $this->Common_model->get_the_role($this->user_id);
            if (!empty($get_role_details['user_role_data'])) {
                $permission_data = array(
                    'role_data' => $get_role_details['user_role_data'],
                    'module' => 3,
                    'key' => 1
                );
                $check_module_permission = $this->check_module_permission($permission_data);
                if ($check_module_permission == 2) {
                    $this->my_response['status'] = false;
                    $this->my_response['message'] = lang('permission_error');
                    $this->send_response();
                }
            }

            if (validate_date_only($date)) {
                $this->my_response['status'] = false;
                $this->my_response['message'] = lang("mycontroller_invalid_request");
                $this->send_response();
            }

            if ((!empty($blood_pressure_type) && !in_array($blood_pressure_type, $this->blood_pressure_type)) ||
                    (!empty($temperature_type) && !in_array($temperature_type, $this->temperature_type)) ||
                    (!empty($temperature_taken) && !in_array($temperature_taken, $this->temperature_taken)) ||
                    (!empty($weight) && !is_numeric($weight))
            ) {

                $this->my_response['status'] = false;
                $this->my_response['message'] = lang("mycontroller_invalid_request");
                $this->send_response();
            }

            //check data belongs to the doctor or not
            $requested_data = array(
                'appointment_id' => $appointment_id,
                'doctor_id' => $doctor_id,
                'clinic_id' => $clinic_id,
                'patient_id' => $patient_id
            );
            $this->check_data_belongs_doctor($requested_data);

            //check vital already exists
            $data_exists_where = array(
                'vital_report_appointment_id' => $appointment_id,
                'vital_report_date' => $date,
                'vital_report_status' => 1
            );
            $this->check_data_exists(TBL_VITAL_REPORTS, 'vital_report_id', $data_exists_where);

            //get the share settings for the vital
            $setting_where = array(
                'setting_type' => 1,
                'setting_user_id' => $doctor_id,
                'setting_clinic_id' => $clinic_id
            );
            $get_setting = $this->Common_model->get_setting($setting_where);

            $vital_share_status = 2;

            if (!empty($get_setting)) {
                $setting_array = json_decode($get_setting['setting_data'], true);
                if (!empty($setting_array) && is_array($setting_array)) {
                    foreach ($setting_array as $setting) {
                        if ($setting['id'] == 1) {
                            $vital_share_status = $setting['status'];
                            break;
                        }
                    }
                }
            }

            $vital_array = array(
                'vital_report_user_id' => $patient_id,
                'vital_report_doctor_id' => $doctor_id,
                'vital_report_appointment_id' => $appointment_id,
                'vital_report_clinic_id' => $clinic_id,
                'vital_report_date' => $date,
                'vital_report_spo2' => $sp2o,
                'vital_report_weight' => $weight,
                'vital_report_bloodpressure_systolic' => $blood_pressure_systolic,
                'vital_report_bloodpressure_diastolic' => $blood_pressure_diastolic,
                'vital_report_bloodpressure_type' => $blood_pressure_type,
                'vital_report_pulse' => $pulse,
                'vital_report_temperature' => $temperature,
                'vital_report_temperature_type' => $temperature_type,
                'vital_report_temperature_taken' => $temperature_taken,
                'vital_report_resp_rate' => $resp,
                'vital_report_share_status' => $vital_share_status,
                'vital_report_created_at' => $this->utc_time_formated,
                'vital_report_updated_at' => $this->utc_time_formated
            );

            $inserted_id = $this->Common_model->insert(TBL_VITAL_REPORTS, $vital_array);

            if ($inserted_id > 0) {
                $this->my_response['status'] = true;
                $this->my_response['message'] = lang('vital_added');
            } else {
                $this->my_response['status'] = false;
                $this->my_response['message'] = lang('failure');
            }

            $this->send_response();
        } catch (ErrorException $ex) {
            $this->error = $ex->getMessage();
            $this->store_error();
        }
    }

    /**
     * Description :- This function is used to add the vital for the patient by doctor
     * 
     * 
     * 
     * 
     */
    public function edit_vital_for_patient_post() {

        try {

            $vital_id = !empty($this->Common_model->escape_data($this->post_data['vital_id'])) ? trim($this->Common_model->escape_data($this->post_data['vital_id'])) : '';
            $clinic_id = !empty($this->Common_model->escape_data($this->post_data['clinic_id'])) ? trim($this->Common_model->escape_data($this->post_data['clinic_id'])) : '';
            $doctor_id = !empty($this->Common_model->escape_data($this->post_data['doctor_id'])) ? trim($this->Common_model->escape_data($this->post_data['doctor_id'])) : '';
            $patient_id = !empty($this->Common_model->escape_data($this->post_data['patient_id'])) ? trim($this->Common_model->escape_data($this->post_data['patient_id'])) : '';
            $appointment_id = !empty($this->Common_model->escape_data($this->post_data['appointment_id'])) ? trim($this->Common_model->escape_data($this->post_data['appointment_id'])) : '';
            $date = !empty($this->Common_model->escape_data($this->post_data['date'])) ? trim($this->Common_model->escape_data($this->post_data['date'])) : '';
            $sp2o = !empty($this->Common_model->escape_data($this->post_data['sp2o'])) ? trim($this->Common_model->escape_data($this->post_data['sp2o'])) : '';
            $weight = !empty($this->Common_model->escape_data($this->post_data['weight'])) ? trim($this->Common_model->escape_data($this->post_data['weight'])) : '';
            $blood_pressure_systolic = !empty($this->Common_model->escape_data($this->post_data['blood_pressure_systolic'])) ? trim($this->Common_model->escape_data($this->post_data['blood_pressure_systolic'])) : '';
            $blood_pressure_diastolic = !empty($this->Common_model->escape_data($this->post_data['blood_pressure_diastolic'])) ? trim($this->Common_model->escape_data($this->post_data['blood_pressure_diastolic'])) : '';
            $blood_pressure_type = !empty($this->Common_model->escape_data($this->post_data['blood_pressure_type'])) ? trim($this->Common_model->escape_data($this->post_data['blood_pressure_type'])) : '';
            $pulse = !empty($this->Common_model->escape_data($this->post_data['pulse'])) ? trim($this->Common_model->escape_data($this->post_data['pulse'])) : '';
            $temperature = !empty($this->Common_model->escape_data($this->post_data['temperature'])) ? trim($this->Common_model->escape_data($this->post_data['temperature'])) : '';
            $temperature_type = !empty($this->Common_model->escape_data($this->post_data['temperature_type'])) ? trim($this->Common_model->escape_data($this->post_data['temperature_type'])) : '';
            $temperature_taken = !empty($this->Common_model->escape_data($this->post_data['temperature_taken'])) ? trim($this->Common_model->escape_data($this->post_data['temperature_taken'])) : '';
            $resp = !empty($this->Common_model->escape_data($this->post_data['resp'])) ? trim($this->Common_model->escape_data($this->post_data['resp'])) : '';
            $user_type = !empty($this->Common_model->escape_data($this->post_data['user_type'])) ? trim($this->Common_model->escape_data($this->post_data['user_type'])) : '';
            $vital_share_status = 1;

            if ($user_type == 1) {

                if (empty($vital_id) ||
                        empty($patient_id)
                ) {
                    $this->bad_request();
                }
            } else {
                if (empty($clinic_id) ||
                        empty($doctor_id) ||
                        empty($patient_id) ||
                        empty($appointment_id) ||
                        empty($date) ||
                        empty($vital_id)
                ) {
                    $this->bad_request();
                }

                $get_role_details = $this->Common_model->get_the_role($this->user_id);
                if (!empty($get_role_details['user_role_data'])) {
                    $permission_data = array(
                        'role_data' => $get_role_details['user_role_data'],
                        'module' => 3,
                        'key' => 2
                    );
                    $check_module_permission = $this->check_module_permission($permission_data);
                    if ($check_module_permission == 2) {
                        $this->my_response['status'] = false;
                        $this->my_response['message'] = lang('permission_error');
                        $this->send_response();
                    }
                }

                //check data belongs to the doctor or not
                $requested_data = array(
                    'appointment_id' => $appointment_id,
                    'doctor_id' => $doctor_id,
                    'clinic_id' => $clinic_id,
                    'patient_id' => $patient_id
                );
                $this->check_data_belongs_doctor($requested_data);

                //get the share settings for the vital
                $setting_where = array(
                    'setting_type' => 1,
                    'setting_user_id' => $doctor_id,
                    'setting_clinic_id' => $clinic_id
                );
                $get_setting = $this->Common_model->get_setting($setting_where);

                $vital_share_status = 2;

                if (!empty($get_setting)) {
                    $setting_array = json_decode($get_setting['setting_data'], true);
                    if (!empty($setting_array) && is_array($setting_array)) {
                        foreach ($setting_array as $setting) {
                            if ($setting['id'] == 1) {
                                $vital_share_status = $setting['status'];
                                break;
                            }
                        }
                    }
                }

                //check vital data belongs to the doctor, clinic based or not
                $vital_belongs_array = array(
                    'vital_report_doctor_id' => $doctor_id,
                    'vital_report_clinic_id' => $clinic_id,
                    'vital_report_id' => $vital_id,
                    'vital_report_status !=' => 9
                );
                $is_valid_data = $this->Common_model->validate_data(TBL_VITAL_REPORTS, 'vital_report_id', $vital_belongs_array);

                if ($is_valid_data == 2) {
                    $this->my_response['status'] = false;
                    $this->my_response['message'] = lang("mycontroller_invalid_request");
                    $this->send_response();
                }
            }

            if (validate_date_only($date)) {
                $this->my_response['status'] = false;
                $this->my_response['message'] = lang("mycontroller_invalid_request");
                $this->send_response();
            }

            if ((!empty($blood_pressure_type) && !in_array($blood_pressure_type, $this->blood_pressure_type)) ||
                    (!empty($temperature_type) && !in_array($temperature_type, $this->temperature_type)) ||
                    (!empty($temperature_taken) && !in_array($temperature_taken, $this->temperature_taken)) ||
                    (!empty($weight) && !is_numeric($weight))
            ) {

                $this->my_response['status'] = false;
                $this->my_response['message'] = lang("mycontroller_invalid_request");
                $this->send_response();
            }


            $update_vital_data = array(
                'vital_report_user_id' => $patient_id,
                'vital_report_appointment_id' => $appointment_id,
                'vital_report_clinic_id' => $clinic_id,
                'vital_report_date' => $date,
                'vital_report_spo2' => $sp2o,
                'vital_report_weight' => $weight,
                'vital_report_bloodpressure_systolic' => $blood_pressure_systolic,
                'vital_report_bloodpressure_diastolic' => $blood_pressure_diastolic,
                'vital_report_bloodpressure_type' => $blood_pressure_type,
                'vital_report_pulse' => $pulse,
                'vital_report_temperature' => $temperature,
                'vital_report_temperature_type' => $temperature_type,
                'vital_report_temperature_taken' => $temperature_taken,
                'vital_report_resp_rate' => $resp,
                'vital_report_share_status' => $vital_share_status,
                'vital_report_updated_at' => $this->utc_time_formated
            );

            if ($user_type == 1) {
                $update_vital_data['vital_report_doctor_id'] = $this->user_id;
            } else {
                $update_vital_data['vital_report_doctor_id'] = $doctor_id;
            }

            $update_vital_where = array(
                'vital_report_id' => $vital_id
            );

            $is_update = $this->Common_model->update(TBL_VITAL_REPORTS, $update_vital_data, $update_vital_where);

            if ($is_update > 0) {
                $get_vital_data = $this->Common_model->get_single_row(TBL_VITAL_REPORTS, '*', array('vital_report_id' => $vital_id));
                $this->my_response['status'] = true;
                $this->my_response['message'] = lang('vital_updated');
                $this->my_response['data'] = $get_vital_data;
            } else {
                $this->my_response['status'] = false;
                $this->my_response['message'] = lang('failure');
            }

            $this->send_response();
        } catch (ErrorException $ex) {
            $this->error = $ex->getMessage();
            $this->store_error();
        }
    }

    /**
     * Description :- This function is used to data belongs to the particular data or not
     * 
     * 
     * 
     * @param type $requested_data
     */
    public function check_data_belongs_doctor($requested_data, $flag = 1) {

        $where_data = array(
            'appointment_doctor_user_id' => $requested_data['doctor_id'],
            'appointment_clinic_id' => $requested_data['clinic_id'],
            'appointment_user_id' => $requested_data['patient_id']
        );

        if ($flag == 1) {
            $where_data['appointment_id'] = $requested_data['appointment_id'];
        }

        $result = $this->Common_model->get_single_row(TBL_APPOINTMENTS, 'appointment_id', $where_data);

        //echo $this->Common_model->get_last_query();exit;

        if (empty($result)) {
            $this->my_response['status'] = false;
            $this->my_response['message'] = lang('mycontroller_invalid_request');
            $this->send_response();
        }
    }

    /**
     * Description :- This function is used to check the data alreay exists like
     * vital, clinicalnotes ...
     * 
     * 
     * 
     * @param type $table_name
     * @param type $column
     * @param type $where_data
     */
    public function check_data_exists($table_name, $column, $where_data) {

        $get_data = $this->Common_model->get_single_row($table_name, $column, $where_data);

        if (!empty($get_data)) {
            $this->my_response['status'] = false;
            $this->my_response['message'] = lang('data_exists');
            $this->send_response();
        }
    }

    /**
     * Description :- This function is used to add the vital by the patient
     * 
     * 
     * 
     * 
     */
    public function add_vital_post() {

        try {

            $patient_id = !empty($this->Common_model->escape_data($this->post_data['patient_id'])) ? trim($this->Common_model->escape_data($this->post_data['patient_id'])) : '';
            $date = !empty($this->Common_model->escape_data($this->post_data['date'])) ? trim($this->Common_model->escape_data($this->post_data['date'])) : '';
            $sp2o = !empty($this->Common_model->escape_data($this->post_data['sp2o'])) ? trim($this->Common_model->escape_data($this->post_data['sp2o'])) : '';
            $weight = !empty($this->Common_model->escape_data($this->post_data['weight'])) ? trim($this->Common_model->escape_data($this->post_data['weight'])) : '';
            $blood_pressure_systolic = !empty($this->Common_model->escape_data($this->post_data['blood_pressure_systolic'])) ? trim($this->Common_model->escape_data($this->post_data['blood_pressure_systolic'])) : '';
            $blood_pressure_diastolic = !empty($this->Common_model->escape_data($this->post_data['blood_pressure_diastolic'])) ? trim($this->Common_model->escape_data($this->post_data['blood_pressure_diastolic'])) : '';
            $blood_pressure_type = !empty($this->Common_model->escape_data($this->post_data['blood_pressure_type'])) ? trim($this->Common_model->escape_data($this->post_data['blood_pressure_type'])) : '';
            $pulse = !empty($this->Common_model->escape_data($this->post_data['pulse'])) ? trim($this->Common_model->escape_data($this->post_data['pulse'])) : '';
            $temperature = !empty($this->Common_model->escape_data($this->post_data['temperature'])) ? trim($this->Common_model->escape_data($this->post_data['temperature'])) : '';
            $temperature_type = !empty($this->Common_model->escape_data($this->post_data['temperature_type'])) ? trim($this->Common_model->escape_data($this->post_data['temperature_type'])) : '';
            $temperature_taken = !empty($this->Common_model->escape_data($this->post_data['temperature_taken'])) ? trim($this->Common_model->escape_data($this->post_data['temperature_taken'])) : '';
            $resp = !empty($this->Common_model->escape_data($this->post_data['resp'])) ? trim($this->Common_model->escape_data($this->post_data['resp'])) : '';

            if (
                    empty($date)
            ) {
                $this->bad_request();
            }

            if (validate_date_only($date)) {
                $this->my_response['status'] = false;
                $this->my_response['message'] = lang("mycontroller_invalid_request");
                $this->send_response();
            }

            if ((!empty($blood_pressure_type) && !in_array($blood_pressure_type, $this->blood_pressure_type)) ||
                    (!empty($temperature_type) && !in_array($temperature_type, $this->temperature_type)) ||
                    (!empty($temperature_taken) && !in_array($temperature_taken, $this->temperature_taken)) ||
                    (!empty($weight) && !is_numeric($weight))
            ) {

                $this->my_response['status'] = false;
                $this->my_response['message'] = lang("mycontroller_invalid_request");
                $this->send_response();
            }

            $vital_share_status = 1;

            $vital_array = array(
                'vital_report_user_id' => $patient_id,
                'vital_report_doctor_id' => $this->user_id,
                'vital_report_date' => $date,
                'vital_report_spo2' => $sp2o,
                'vital_report_weight' => $weight,
                'vital_report_bloodpressure_systolic' => $blood_pressure_systolic,
                'vital_report_bloodpressure_diastolic' => $blood_pressure_diastolic,
                'vital_report_bloodpressure_type' => $blood_pressure_type,
                'vital_report_pulse' => $pulse,
                'vital_report_temperature' => $temperature,
                'vital_report_temperature_type' => $temperature_type,
                'vital_report_temperature_taken' => $temperature_taken,
                'vital_report_resp_rate' => $resp,
                'vital_report_share_status' => $vital_share_status,
                'vital_report_created_at' => $this->utc_time_formated,
                'vital_report_updated_at' => $this->utc_time_formated
            );

            $inserted_id = $this->Common_model->insert(TBL_VITAL_REPORTS, $vital_array);

            if ($inserted_id > 0) {
                $vital_array['vital_report_id'] = $inserted_id;
                $this->my_response['status'] = true;
                $this->my_response['message'] = lang('vital_added');
                $this->my_response['data'] = $vital_array;
            } else {
                $this->my_response['status'] = false;
                $this->my_response['message'] = lang('failure');
            }

            $this->send_response();
        } catch (ErrorException $ex) {
            $this->error = $ex->getMessage();
            $this->store_error();
        }
    }

    /**
     * Description :- This function is used to get the vital of the patient
     * 
     * 
     * 
     */
    public function get_vital_post() {

        try {

            $patient_id = !empty($this->Common_model->escape_data($this->post_data['patient_id'])) ? trim($this->Common_model->escape_data($this->post_data['patient_id'])) : '';
            $user_type = !empty($this->Common_model->escape_data($this->post_data['user_type'])) ? trim($this->Common_model->escape_data($this->post_data['user_type'])) : '';

            $clinic_id = !empty($this->Common_model->escape_data($this->post_data['clinic_id'])) ? trim($this->Common_model->escape_data($this->post_data['clinic_id'])) : '';
            $doctor_id = !empty($this->Common_model->escape_data($this->post_data['doctor_id'])) ? trim($this->Common_model->escape_data($this->post_data['doctor_id'])) : '';
            $flag = !empty($this->Common_model->escape_data($this->post_data['flag'])) ? trim($this->Common_model->escape_data($this->post_data['flag'])) : '';

            $page = !empty($this->post_data['page']) ? trim($this->Common_model->escape_data($this->post_data['page'])) : "1";
            $per_page = !empty($this->post_data['per_page']) ? trim($this->Common_model->escape_data($this->post_data['per_page'])) : "10";

            if (empty($patient_id) ||
                    empty($user_type)
            ) {
                $this->bad_request();
            }

            if ($user_type == 2) {

                if (empty($flag) ||
                        empty($clinic_id) ||
                        empty($doctor_id)
                ) {
                    $this->bad_request();
                }
            }

            $vital_data = array(
                'user_id' => $patient_id,
                'page' => $page,
                'per_page' => $per_page,
                'user_type' => $user_type
            );

            if ($user_type == 2) {

                //check data belongs to the doctor or not
                $requested_data = array(
                    'doctor_id' => $doctor_id,
                    'clinic_id' => $clinic_id,
                    'patient_id' => $patient_id
                );
                $this->check_data_belongs_doctor($requested_data, 2);

                $vital_data['flag'] = $flag;
                $vital_data['logged_in'] = $this->user_id;

                $get_vital_data = $this->User_model->get_patient_vital($vital_data);
            } else {
                $vital_data['flag'] = '';
                $vital_data['logged_in'] = $this->user_id;
                $get_vital_data = $this->User_model->get_patient_vital($vital_data);
            }
            
            /*strart*/
            $vital_array = array();
            if (!empty($get_vital_data)) {
                foreach ($get_vital_data['data'] as $vital) {
                    if($vital['user_type'] == 2){
                        $dr_prefix = DOCTOR;
                    }else{
                        $dr_prefix = '';
                    }
                    $vital_array[] = array(
                        "vital_report_date" => $vital['vital_report_date'],
                        "vital_report_spo2" => $vital['vital_report_spo2'],
                        "vital_report_weight" => $vital['vital_report_weight'],
                        "vital_report_bloodpressure_systolic" => $vital['vital_report_bloodpressure_systolic'],
                        "vital_report_bloodpressure_diastolic" => $vital['vital_report_bloodpressure_diastolic'],
                        "vital_report_bloodpressure_type" => $vital['vital_report_bloodpressure_type'],
                        "vital_report_pulse" => $vital['vital_report_pulse'],
                        "vital_report_temperature" => $vital['vital_report_temperature'],
                        "vital_report_temperature_type" => $vital['vital_report_temperature_type'],
                        "vital_report_temperature_taken" => $vital['vital_report_temperature_taken'],
                        "vital_report_resp_rate" => $vital['vital_report_resp_rate'],
                        "vital_report_doctor_id" => $vital['vital_report_doctor_id'],
                        "user_first_name" => $vital['user_first_name'],
                        "user_last_name" => $vital['user_last_name'],
                        "user_type" => $vital['user_type'],
                        "user_name" => $dr_prefix. '' .$vital['user_name'],
                        "vital_report_user_id" => $vital['vital_report_user_id'],
                        "created_by" => $vital['user_first_name'],
                        "vital_report_id" => $vital['vital_report_id']
                    );
                }
            }
            /*end*/
            
            if (!empty($get_vital_data['data'])) {
                $this->my_response['status'] = true;
                $this->my_response['message'] = lang('common_detail_found');
                $this->my_response['data'] = $vital_array;
                $this->my_response['total_count'] = (string) $get_vital_data['total_records'];
                $this->my_response['per_page'] = $per_page;
                $this->my_response['current_page'] = $page;
            } else {
                $this->my_response['status'] = true;
                $this->my_response['message'] = lang('common_detail_not_found');
                $this->my_response['data'] = array();
            }
            $this->send_response();
        } catch (ErrorException $ex) {
            $this->error = $ex->getMessage();
            $this->store_error();
        }
    }

    /**
     * Description :- This function is used to delete the vital of the patient
     * 
     * 
     * 
     */
    public function delete_vital_post() {

        try {

            $patient_id = !empty($this->Common_model->escape_data($this->post_data['patient_id'])) ? trim($this->Common_model->escape_data($this->post_data['patient_id'])) : '';
            $clinic_id = !empty($this->Common_model->escape_data($this->post_data['clinic_id'])) ? trim($this->Common_model->escape_data($this->post_data['clinic_id'])) : '';
            $doctor_id = !empty($this->Common_model->escape_data($this->post_data['doctor_id'])) ? trim($this->Common_model->escape_data($this->post_data['doctor_id'])) : '';
            $appointment_id = !empty($this->Common_model->escape_data($this->post_data['appointment_id'])) ? trim($this->Common_model->escape_data($this->post_data['appointment_id'])) : '';

            if (empty($patient_id) ||
                    empty($clinic_id) ||
                    empty($doctor_id)
            ) {
                $this->bad_request();
            }

            //check data belongs to the doctor or not
            $requested_data = array(
                'doctor_id' => $doctor_id,
                'clinic_id' => $clinic_id,
                'patient_id' => $patient_id,
                'appointment_id' => $appointment_id
            );
            $this->check_data_belongs_doctor($requested_data, 2);

            $update_data = array(
                'vital_report_status' => 9,
                'vital_report_updated_at' => $this->utc_time_formated
            );

            $update_where = array(
                'vital_report_appointment_id' => $appointment_id
            );

            $is_update = $this->Common_model->update(TBL_VITAL_REPORTS, $update_data, $update_where);

            if ($is_update > 0) {
                $this->my_response['status'] = true;
                $this->my_response['message'] = "Vital deleted succesfully";
            } else {
                $this->my_response['status'] = false;
                $this->my_response['message'] = lang("failure");
            }

            $this->send_response();
        } catch (ErrorException $ex) {
            $this->error = $ex->getMessage();
            $this->store_error();
        }
    }

    /**
     * Description :- This function is used to add the report for 
     * the patient by doctor or the patient itself
     * 
     * 
     * 
     * 
     */
    public function add_report_post() {

        try {

            $clinic_id = !empty($this->Common_model->escape_data($this->post_data['clinic_id'])) ? trim($this->Common_model->escape_data($this->post_data['clinic_id'])) : '';
            $doctor_id = !empty($this->Common_model->escape_data($this->post_data['doctor_id'])) ? trim($this->Common_model->escape_data($this->post_data['doctor_id'])) : '';
            $patient_id = !empty($this->Common_model->escape_data($this->post_data['patient_id'])) ? trim($this->Common_model->escape_data($this->post_data['patient_id'])) : '';
            $appointment_id = !empty($this->Common_model->escape_data($this->post_data['appointment_id'])) ? trim($this->Common_model->escape_data($this->post_data['appointment_id'])) : '';
            $report_type_id = !empty($this->Common_model->escape_data($this->post_data['report_type_id'])) ? trim($this->Common_model->escape_data($this->post_data['report_type_id'])) : '';
            $report_name = !empty($this->Common_model->escape_data($this->post_data['report_name'])) ? trim($this->Common_model->escape_data($this->post_data['report_name'])) : '';
            $user_type = !empty($this->Common_model->escape_data($this->post_data['user_type'])) ? trim($this->Common_model->escape_data($this->post_data['user_type'])) : '';
            $date = !empty($this->Common_model->escape_data($this->post_data['date'])) ? trim($this->Common_model->escape_data($this->post_data['date'])) : '';

            if (empty($user_type) ||
                    empty($patient_id) ||
                    empty($date) ||
                    empty($report_name) ||
                    empty($report_type_id)
            ) {
                $this->bad_request();
            }

            if (!in_array($user_type, $this->user_type)) {
                $this->my_response['status'] = false;
                $this->my_response['message'] = lang("mycontroller_invalid_request");
                $this->send_response();
            }

            if ($user_type == 2 && (
                    empty($clinic_id) ||
                    empty($doctor_id) ||
                    empty($appointment_id)
                    )
            ) {
                $this->bad_request();
            }

            //check the user permission
            if ($user_type == 2) {
                $get_role_details = $this->Common_model->get_the_role($this->user_id);
                if (!empty($get_role_details['user_role_data'])) {
                    $permission_data = array(
                        'role_data' => $get_role_details['user_role_data'],
                        'module' => 8,
                        'key' => 1
                    );
                    $check_module_permission = $this->check_module_permission($permission_data);
                    if ($check_module_permission == 2) {
                        $this->my_response['status'] = false;
                        $this->my_response['message'] = lang('permission_error');
                        $this->send_response();
                    }
                }
            }

            if (validate_report_date($date)) {
                $this->my_response['status'] = false;
                $this->my_response['message'] = lang("mycontroller_invalid_request");
                $this->send_response();
            }

            $report_id_exists = array(
                'report_type_id' => $report_type_id,
                'report_type_status' => 1
            );
            $is_valid_data = $this->Common_model->validate_data(TBL_REPORT_TYPES, 'report_type_id', $report_id_exists);

            if ($is_valid_data == 2) {
                $this->my_response['status'] = false;
                $this->my_response['message'] = lang("mycontroller_invalid_request");
                $this->send_response();
            }

            $report_share_status = '';

            if ($user_type == 2) {
                //check data belongs to the doctor or not
                $requested_data = array(
                    'appointment_id' => $appointment_id,
                    'doctor_id' => $doctor_id,
                    'clinic_id' => $clinic_id,
                    'patient_id' => $patient_id
                );
                $this->check_data_belongs_doctor($requested_data);

                //get the share settings for the vital
                $setting_where = array(
                    'setting_type' => 1,
                    'setting_user_id' => $doctor_id,
                    'setting_clinic_id' => $clinic_id
                );
                $get_setting = $this->Common_model->get_setting($setting_where);

                $report_share_status = 2;

                if (!empty($get_setting)) {
                    $setting_array = json_decode($get_setting['setting_data'], true);
                    if (!empty($setting_array) && is_array($setting_array)) {
                        foreach ($setting_array as $setting) {
                            if ($setting['id'] == 7) {
                                $report_share_status = $setting['status'];
                                break;
                            }
                        }
                    }
                }
            } else {
                $report_share_status = 1;
            }

            $report_array = array(
                'file_report_user_id' => $patient_id,
                'file_report_report_type_id' => $report_type_id,
                'file_report_name' => $report_name,
                'file_report_date' => $date,
                'file_report_created_at' => $this->utc_time_formated,
                'file_report_share_status' => $report_share_status
            );

            if (!empty($doctor_id)) {
                $report_array['file_report_doctor_user_id'] = $doctor_id;
                $report_array['file_report_appointment_id'] = $appointment_id;
                $report_array['file_report_clinic_id'] = $clinic_id;

                if ($user_type == 1) {

                    $patient_user_detail = $this->User_model->get_details_by_id($patient_id);
                    $patient_user_name = '';

                    if (!empty($patient_user_detail) && !empty($patient_user_detail['user_first_name'])) {
                        $patient_user_name = $patient_user_detail['user_first_name'] . ' ' . $patient_user_detail['user_last_name'];
                    }

                    $doctor_user_detail = $this->User_model->get_details_by_id($doctor_id);
                    $doctor_user_name = '';

                    if (!empty($doctor_user_detail) && !empty($doctor_user_detail['user_first_name'])) {
                        $doctor_name = $doctor_user_detail['user_first_name'] . ' ' . $doctor_user_detail['user_last_name'];
                    }

                    //send notification to the doctor i.e patient added the report
                    $notification_array[] = array(
                        'notification_list_user_id' => $doctor_id,
                        'notification_list_user_type' => 2,
                        'notification_list_type' => 4,
                        'notification_list_message' => sprintf(lang('patient_add_report'), $patient_user_name, $doctor_name),
                        'notification_list_created_at' => $this->utc_time_formated
                    );

                    $this->Common_model->insert_multiple(TBL_NOTIFICATION, $notification_array);

                    $send_notification_data = array(
                        'notification_list_type' => 4,
                        'action' => 'prescription_add'
                    );

                    $cron_job_path = CRON_PATH . " notification/send_notification/" . base64_encode(json_encode($send_notification_data));
                    exec(PHP_PATH . " " . $cron_job_path . " > /dev/null &");
                }
            } else {
                $report_array['file_report_doctor_user_id'] = $this->user_id;
                $report_array['file_report_appointment_id'] = NULL;
                $report_array['file_report_clinic_id'] = NULL;
            }

            $this->db->trans_start();

            $inserted_id = $this->Common_model->insert(TBL_FILE_REPORTS, $report_array);

            $image_name = array();

            if ($inserted_id > 0) {
                if ($user_type == 2) {
                    if (!empty($_FILES['images']['name']) && $_FILES['images']['error'] == 0) {
                        // in doctor web/application single image comes
                        $new_profile_img = '';
                        $upload_path = UPLOAD_REL_PATH . "/" . REPORT_FOLDER . "/" . $inserted_id;
                        $upload_folder = REPORT_FOLDER . "/" . $inserted_id;
                        $profile_image_name = do_upload_multiple($upload_path, $_FILES, $upload_folder);
                        $new_profile_img = $profile_image_name['images'];

                        if (!empty($new_profile_img)) {
                            $insert_image_array = array(
                                'file_report_image_file_report_id' => $inserted_id,
                                'file_report_image_url' => IMAGE_MANIPULATION_URL . REPORT_FOLDER . "/" . $inserted_id . "/" . $new_profile_img,
                                'file_report_image_created_at' => $this->utc_time_formated
                            );
                            $this->Common_model->insert(TBL_FILE_REPORTS_IMAGES, $insert_image_array);
                        }
                    }
                } else {
                    
                    // in application patient side images comes in the zip 
                    if (!empty($_FILES['images']['name']) && $_FILES['images']['error'] == 0) {

                        $upload_path = UPLOAD_REL_PATH . "/" . REPORT_FOLDER . "/" . $inserted_id;
                        $upload_folder = REPORT_FOLDER . "/" . $inserted_id;
                        $new_file = do_upload($upload_path, $_FILES, $upload_folder);

                        if (!empty($new_file[0])) {
                            $send_data = array(
                                'file_name' => $new_file[0],
                                'upload_path' => $upload_path,
                                'upload_folder' => $upload_folder,
                                'id' => $inserted_id
                            );
                            $image_name = upload_zip_data($send_data);
                        }

                        $insert_image_array = array();
                        if (!empty($image_name) && count($image_name) > 0) {
                            foreach ($image_name as $image) {
                                if (!empty($image)) {
                                    $insert_image_array[] = array(
                                        'file_report_image_file_report_id' => $inserted_id,
                                        'file_report_image_url' => IMAGE_MANIPULATION_URL . REPORT_FOLDER . "/" . $inserted_id . "/" . $image,
                                        'report_file_size' => get_file_size(IMAGE_MANIPULATION_URL . REPORT_FOLDER . "/" . $inserted_id . "/" . $image),
                                        'file_report_image_created_at' => $this->utc_time_formated
                                    );
                                }
                            }
                            $this->Common_model->insert_multiple(TBL_FILE_REPORTS_IMAGES, $insert_image_array);
                        }
                    }
                }
            }

            $report_data = array(
                'report_id' => $inserted_id
            );

            $get_report_detail = $this->User_model->get_report_detail($report_data);

            if ($this->db->trans_status() !== FALSE) {
                $this->db->trans_commit();

                $this->my_response['status'] = true;
                $this->my_response['message'] = lang('report_added');
                $this->my_response['data'] = $get_report_detail;
            } else {
                $this->db->trans_rollback();
                $this->my_response['status'] = false;
                $this->my_response['message'] = lang('failure');
            }

            $this->send_response();
        } catch (ErrorException $ex) {
            $this->error = $ex->getMessage();
            $this->store_error();
        }
    }

    /**
     * Description :- This function is used to get the report of the patient
     * 
     * 
     * 
     */
    public function get_report_post() {

        try {

            $patient_id = !empty($this->Common_model->escape_data($this->post_data['patient_id'])) ? trim($this->Common_model->escape_data($this->post_data['patient_id'])) : '';
            $user_type = !empty($this->Common_model->escape_data($this->post_data['user_type'])) ? trim($this->Common_model->escape_data($this->post_data['user_type'])) : '';

            $clinic_id = !empty($this->Common_model->escape_data($this->post_data['clinic_id'])) ? trim($this->Common_model->escape_data($this->post_data['clinic_id'])) : '';
            $doctor_id = !empty($this->Common_model->escape_data($this->post_data['doctor_id'])) ? trim($this->Common_model->escape_data($this->post_data['doctor_id'])) : 1;
            $flag = !empty($this->Common_model->escape_data($this->post_data['flag'])) ? trim($this->Common_model->escape_data($this->post_data['flag'])) : 1;

            $page = !empty($this->post_data['page']) ? trim($this->Common_model->escape_data($this->post_data['page'])) : "1";
            $per_page = !empty($this->post_data['per_page']) ? trim($this->Common_model->escape_data($this->post_data['per_page'])) : "10";

            if (empty($patient_id) ||
                    empty($user_type)
            ) {
                $this->bad_request();
            }

            if ($user_type == 2) {

                if (empty($flag) ||
                        empty($clinic_id) ||
                        empty($doctor_id)
                ) {
                    $this->bad_request();
                }
            }

            $report_data = array(
                'user_id' => $patient_id,
                'page' => $page,
                'per_page' => $per_page,
                'user_type' => $user_type
            );


            if ($user_type == 2) {

                //check data belongs to the doctor or not
                $requested_data = array(
                    'doctor_id' => $doctor_id,
                    'clinic_id' => $clinic_id,
                    'patient_id' => $patient_id
                );
                $this->check_data_belongs_doctor($requested_data, 2);

                $report_data['flag'] = $flag;
                $report_data['logged_in'] = $this->user_id;
                $get_report_data = $this->User_model->get_patient_report($report_data);
            } else {

                $report_data['flag'] = '';
                $report_data['logged_in'] = $this->user_id;
                $get_report_data = $this->User_model->get_patient_report($report_data);
            }
            
            /*strart*/
            $final_array = array();
            if (!empty($get_report_data)) {
                foreach ($get_report_data['data'] as $report) {
                    if($report['user_type'] == 2){
                        $dr_prefix = DOCTOR;
                    }else{
                        $dr_prefix = '';
                    }
                    $final_array[] = array(
                        "file_report_id" => $report['file_report_id'],
                        "file_report_name" => $report['file_report_name'],
                        "file_report_date" => $report['file_report_date'],
                        "report_type_name" => $report['report_type_name'],
                        "patient_first_name" => $report['patient_first_name'],
                        "patient_last_name" => $report['patient_last_name'],
                        "user_patient_name" => $report['user_patient_name'],
                        "user_first_name" => $report['user_first_name'],
                        "user_last_name" => $report['user_last_name'],
                        "user_type" => $report['user_type'],
                        "user_name" => $dr_prefix.' '.$report['user_name'],
                        "file_report_share_status" => $report['file_report_share_status'],
                        "created_by" => $report['created_by']
                    );
                }
            }
            /*end*/
            
            if (!empty($get_report_data['data'])) {
                $this->my_response['status'] = true;
                $this->my_response['message'] = lang('common_detail_found');
                $this->my_response['data'] = $final_array;
                $this->my_response['total_count'] = (string) $get_report_data['total_records'];
                $this->my_response['per_page'] = $per_page;
                $this->my_response['current_page'] = $page;
            } else {
                $this->my_response['status'] = true;
                $this->my_response['message'] = lang('common_detail_not_found');
                $this->my_response['data'] = array();
            }
            $this->send_response();
        } catch (ErrorException $ex) {
            $this->error = $ex->getMessage();
            $this->store_error();
        }
    }

    /**
     * Description :- This function is used to get the detail of the report
     * 
     * 
     * 
     */
    public function get_report_detail_post() {

        try {
            $patient_id = !empty($this->Common_model->escape_data($this->post_data['patient_id'])) ? trim($this->Common_model->escape_data($this->post_data['patient_id'])) : '';
            $report_id = !empty($this->Common_model->escape_data($this->post_data['report_id'])) ? trim($this->Common_model->escape_data($this->post_data['report_id'])) : '';

            if (empty($patient_id) ||
                    empty($report_id)
            ) {
                $this->bad_request();
            }

            $report_id_exists = array(
                'file_report_id' => $report_id,
                'file_report_user_id' => $patient_id
            );
            $is_valid_data = $this->Common_model->validate_data(TBL_FILE_REPORTS, 'file_report_id', $report_id_exists);

            if ($is_valid_data == 2) {
                $this->my_response['status'] = false;
                $this->my_response['message'] = lang("mycontroller_invalid_request");
                $this->send_response();
            }

            $report_data = array(
                'report_id' => $report_id
            );

            $get_report_detail = $this->User_model->get_report_detail($report_data);

            if (!empty($get_report_detail)) {
                $this->my_response['status'] = true;
                $this->my_response['message'] = lang('common_detail_found');
                $this->my_response['data'] = $get_report_detail;
            } else {
                $this->my_response['status'] = true;
                $this->my_response['message'] = lang('common_detail_not_found');
                $this->my_response['data'] = array();
            }
            $this->send_response();
        } catch (ErrorException $ex) {
            $this->error = $ex->getMessage();
            $this->store_error();
        }
    }

    /**
     * Description :- This function is used to delete the report of the patient
     * 
     * 
     * 
     */
    public function delete_report_post() {

        try {

            $clinic_id = !empty($this->Common_model->escape_data($this->post_data['clinic_id'])) ? trim($this->Common_model->escape_data($this->post_data['clinic_id'])) : '';
            $report_id = !empty($this->Common_model->escape_data($this->post_data['report_id'])) ? trim($this->Common_model->escape_data($this->post_data['report_id'])) : '';
            $doctor_id = !empty($this->Common_model->escape_data($this->post_data['doctor_id'])) ? trim($this->Common_model->escape_data($this->post_data['doctor_id'])) : '';

            if (empty($doctor_id) ||
                    empty($clinic_id) ||
                    empty($report_id)
            ) {
                $this->bad_request();
            }

            //check the user permission
            $get_role_details = $this->Common_model->get_the_role($this->user_id);
            if (!empty($get_role_details['user_role_data'])) {
                $permission_data = array(
                    'role_data' => $get_role_details['user_role_data'],
                    'module' => 8,
                    'key' => 4
                );
                $check_module_permission = $this->check_module_permission($permission_data);
                if ($check_module_permission == 2) {
                    $this->my_response['status'] = false;
                    $this->my_response['message'] = lang('permission_error');
                    $this->send_response();
                }
            }

            //check report data belongs to the doctor, clinic based or not
            $report_belongs_array = array(
                'file_report_doctor_user_id' => $doctor_id,
                'file_report_clinic_id' => $clinic_id,
                'file_report_status !=' => 9,
                'file_report_id' => $report_id
            );
            $is_valid_data = $this->Common_model->validate_data(TBL_FILE_REPORTS, 'file_report_id', $report_belongs_array);

            if ($is_valid_data == 2) {
                $this->my_response['status'] = false;
                $this->my_response['message'] = lang("mycontroller_invalid_request");
                $this->send_response();
            }


            $update_report_data = array(
                'file_report_status' => 9,
                'file_report_updated_at' => $this->utc_time_formated,
            );

            $update_report_where = array(
                'file_report_id' => $report_id
            );

            $is_update = $this->Common_model->update(TBL_FILE_REPORTS, $update_report_data, $update_report_where);

            if ($is_update > 0) {

                $update_report_images_data = array(
                    'file_report_image_updated_at' => $this->utc_time_formated,
                    'file_report_image_status' => 9
                );

                $update_report_images_where = array(
                    'file_report_image_file_report_id' => $report_id
                );

                $this->Common_model->update(TBL_FILE_REPORTS_IMAGES, $update_report_images_data, $update_report_images_where);

                $this->my_response['status'] = true;
                $this->my_response['message'] = lang('report_deleted');
            } else {
                $this->my_response['status'] = false;
                $this->my_response['message'] = lang('failure');
            }

            $this->send_response();
        } catch (ErrorException $ex) {
            $this->error = $ex->getMessage();
            $this->store_error();
        }
    }

    /**
     * Description :- This function is used to add the vital for the patient by doctor
     * 
     * 
     * 
     * 
     */
    public function add_clinic_notes_post() {

        try {

            $clinic_id = !empty($this->Common_model->escape_data($this->post_data['clinic_id'])) ? trim($this->Common_model->escape_data($this->post_data['clinic_id'])) : '';
            $doctor_id = !empty($this->Common_model->escape_data($this->post_data['doctor_id'])) ? trim($this->Common_model->escape_data($this->post_data['doctor_id'])) : '';
            $patient_id = !empty($this->Common_model->escape_data($this->post_data['patient_id'])) ? trim($this->Common_model->escape_data($this->post_data['patient_id'])) : '';
            $appointment_id = !empty($this->Common_model->escape_data($this->post_data['appointment_id'])) ? trim($this->Common_model->escape_data($this->post_data['appointment_id'])) : '';
            $date = !empty($this->Common_model->escape_data($this->post_data['date'])) ? trim($this->Common_model->escape_data($this->post_data['date'])) : '';
            $user_type = !empty($this->Common_model->escape_data($this->post_data['user_type'])) ? trim($this->Common_model->escape_data($this->post_data['user_type'])) : '';
            $kco = !empty($this->post_data['kco']) ? $this->post_data['kco'] : '';
            $complaints = !empty($this->post_data['complaints']) ? $this->post_data['complaints'] : '';
            $observation = !empty($this->post_data['observation']) ? $this->post_data['observation'] : '';
            $diagnose = !empty($this->post_data['diagnose']) ? $this->post_data['diagnose'] : '';
            $notes = !empty($this->post_data['notes']) ? $this->post_data['notes'] : '';
            $image_type = !empty($this->Common_model->escape_data($this->post_data['image_type'])) ? trim($this->Common_model->escape_data($this->post_data['image_type'])) : '';

            if (empty($user_type) ||
                    empty($patient_id) ||
                    empty($date)
            ) {
                $this->bad_request();
            }

            if (!in_array($user_type, $this->user_type)) {
                $this->my_response['status'] = false;
                $this->my_response['message'] = lang("mycontroller_invalid_request");
                $this->send_response();
            }

            if ($user_type == 2 && (
                    empty($clinic_id) ||
                    empty($doctor_id) ||
                    empty($appointment_id)
                    )
            ) {
                $this->bad_request();
            }

            if (validate_date_only($date)) {
                $this->my_response['status'] = false;
                $this->my_response['message'] = lang("mycontroller_invalid_request");
                $this->send_response();
            }

            $report_share_status = '';

            if ($user_type == 2) {
                //check data belongs to the doctor or not
                $requested_data = array(
                    'appointment_id' => $appointment_id,
                    'doctor_id' => $doctor_id,
                    'clinic_id' => $clinic_id,
                    'patient_id' => $patient_id
                );
                $this->check_data_belongs_doctor($requested_data);

                //get the share settings for the vital
                $setting_where = array(
                    'setting_type' => 1,
                    'setting_user_id' => $doctor_id,
                    'setting_clinic_id' => $clinic_id
                );
                $get_setting = $this->Common_model->get_setting($setting_where);

                $report_share_status = 2;

                if (!empty($get_setting)) {
                    $setting_array = json_decode($get_setting['setting_data'], true);
                    if (!empty($setting_array) && is_array($setting_array)) {
                        foreach ($setting_array as $setting) {
                            if ($setting['id'] == 2) {
                                $report_share_status = $setting['status'];
                                break;
                            }
                        }
                    }
                }
            } else {
                $report_share_status = 1;
            }

            //check already kco exists or not
            $decode_kco = json_decode($kco, true);
            $total_kco = count($decode_kco);

            if (!empty($decode_kco)) {

                $get_kco_sql = "SELECT 
                                clinical_notes_reports_id
                            FROM 
                                " . TBL_CLINICAL_REPORTS . " 
                            WHERE 
                                clinical_notes_reports_user_id = '" . $patient_id . "' AND 
                                clinical_notes_reports_appointment_id != '" . $appointment_id . "' AND ";
                $get_kco_sql .= " ( ";
                foreach ($decode_kco as $key => $data) {

                    if ($key == $total_kco - 1) {
                        $get_kco_sql .= " clinical_notes_reports_kco LIKE '%\"" . $data . "\"%' ";
                    } else {
                        $get_kco_sql .= " clinical_notes_reports_kco LIKE '%\"" . $data . "\"%' OR ";
                    }
                }
                $get_kco_sql .= " ) ";

                $get_kco = $this->Common_model->get_single_row_by_query($get_kco_sql);

                if (!empty($get_kco['clinical_notes_reports_id'])) {
                    $this->my_response['status'] = false;
                    $this->my_response['message'] = lang("kco_exists");
                    $this->send_response();
                }
            }

            $clinic_notes_array = array(
                'clinical_notes_reports_user_id' => $patient_id,
                'clinical_notes_reports_date' => $date,
                'clinical_notes_reports_share_status' => $report_share_status,
                'clinical_notes_reports_kco' => json_encode(array_values(array_unique(json_decode($kco, true)))),
                'clinical_notes_reports_complaints' => $complaints,
                'clinical_notes_reports_observation' => $observation,
                'clinical_notes_reports_diagnoses' => $diagnose,
                'clinical_notes_reports_add_notes' => $notes
            );

            if ($user_type == 2) {
                $clinic_notes_array['clinical_notes_reports_doctor_user_id'] = $doctor_id;
                $clinic_notes_array['clinical_notes_reports_appointment_id'] = $appointment_id;
                $clinic_notes_array['clinical_notes_reports_clinic_id'] = $clinic_id;
            } else {
                $clinic_notes_array['clinical_notes_reports_doctor_user_id'] = $this->user_id;
            }

            $this->db->trans_start();

            //check report already exists
            $data_exists_where = array(
                'clinical_notes_reports_appointment_id' => $appointment_id,
                'clinical_notes_reports_status' => 1
            );
            $check_clinicnotes_exists = $this->Common_model->get_single_row(TBL_CLINICAL_NOTES_REPORT, 'clinical_notes_reports_id', $data_exists_where);

            $clinic_notes_report_id = '';
            if (!empty($check_clinicnotes_exists)) {

                $clinic_notes_array['clinical_notes_reports_updated_at'] = $this->utc_time_formated;

                if ($user_type == 2) {

                    $get_role_details = $this->Common_model->get_the_role($this->user_id);
                    if (!empty($get_role_details['user_role_data'])) {
                        $permission_data = array(
                            'role_data' => $get_role_details['user_role_data'],
                            'module' => 4,
                            'key' => 2
                        );
                        $check_module_permission = $this->check_module_permission($permission_data);
                        if ($check_module_permission == 2) {
                            $this->my_response['status'] = false;
                            $this->my_response['message'] = lang('permission_error');
                            $this->send_response();
                        }
                    }
                }

                $this->Common_model->update(TBL_CLINICAL_NOTES_REPORT, $clinic_notes_array, array(
                    'clinical_notes_reports_id' => $check_clinicnotes_exists['clinical_notes_reports_id']
                        )
                );

                $clinic_notes_report_id = $check_clinicnotes_exists['clinical_notes_reports_id'];

                $clinic_data = array(
                    'clinical_notes_reports_id' => $clinic_notes_report_id
                );
            } else {

                $clinic_notes_array['clinical_notes_reports_created_at'] = $this->utc_time_formated;

                if ($user_type == 2) {
                    $get_role_details = $this->Common_model->get_the_role($this->user_id);
                    if (!empty($get_role_details['user_role_data'])) {
                        $permission_data = array(
                            'role_data' => $get_role_details['user_role_data'],
                            'module' => 4,
                            'key' => 1
                        );
                        $check_module_permission = $this->check_module_permission($permission_data);
                        if ($check_module_permission == 2) {
                            $this->my_response['status'] = false;
                            $this->my_response['message'] = lang('permission_error');
                            $this->send_response();
                        }
                    }
                }

                $inserted_id = $this->Common_model->insert(TBL_CLINICAL_NOTES_REPORT, $clinic_notes_array);

                $clinic_notes_report_id = $inserted_id;

                $clinic_data = array(
                    'clinical_notes_reports_id' => $inserted_id
                );
            }

            if (!empty($_FILES['image']['name']) && $_FILES['image']['error'] == 0) {

                if (empty($image_type)) {
                    $this->bad_request();
                }

                $image_name = '';
                $upload_path = UPLOAD_REL_PATH . "/" . CILINICAL_REPORT . "/" . $clinic_notes_report_id;
                $upload_folder = CILINICAL_REPORT . "/" . $clinic_notes_report_id;
                $profile_image_name = do_upload_multiple($upload_path, $_FILES, $upload_folder);
                $image_name = $profile_image_name['image'];

                if (!empty($image_name)) {
                    $insert_image_array = array(
                        'clinic_notes_reports_images_reports_id' => $clinic_notes_report_id,
                        'clinic_notes_reports_images_url' => IMAGE_MANIPULATION_URL . CILINICAL_REPORT . "/" . $clinic_notes_report_id . "/" . $image_name,
                        'clinic_notes_reports_images_type' => $image_type,
                        'clinic_notes_reports_images_created_at' => $this->utc_time_formated
                    );
                    $this->Common_model->insert(TBL_CLINICAL_NOTES_REPORT_IMAGE, $insert_image_array);
                }
            }


            $get_clinic_report_detail = $this->User_model->get_clinic_report_detail($clinic_data);

            if ($this->db->trans_status() !== FALSE) {
                $this->db->trans_commit();
                $this->my_response['status'] = true;
                $this->my_response['message'] = lang('clinical_notes_added');
                $this->my_response['data'] = $get_clinic_report_detail;
            } else {
                $this->db->trans_rollback();
                $this->my_response['status'] = false;
                $this->my_response['message'] = lang('failure');
            }

            $this->send_response();
        } catch (ErrorException $ex) {
            $this->error = $ex->getMessage();
            $this->store_error();
        }
    }

    /**
     * Description :- This function is used to add the vital for the patient by doctor
     * 
     * 
     * 
     * 
     */
    public function delete_clinic_notes_image_post() {

        try {

            $clinic_id = !empty($this->Common_model->escape_data($this->post_data['clinic_id'])) ? trim($this->Common_model->escape_data($this->post_data['clinic_id'])) : '';
            $clinic_notes_image_id = !empty($this->Common_model->escape_data($this->post_data['clinic_notes_image_id'])) ? trim($this->Common_model->escape_data($this->post_data['clinic_notes_image_id'])) : '';
            $doctor_id = !empty($this->Common_model->escape_data($this->post_data['doctor_id'])) ? trim($this->Common_model->escape_data($this->post_data['doctor_id'])) : '';

            if (empty($doctor_id) ||
                    empty($clinic_id) ||
                    empty($clinic_notes_image_id)
            ) {
                $this->bad_request();
            }

            //check clinic notes data belongs to the doctor, clinic based or not
            $clinic_report_belongs_array = array(
                'clinical_notes_reports_doctor_user_id' => $doctor_id,
                'clinical_notes_reports_clinic_id' => $clinic_id,
                'clinical_notes_reports_status !=' => 9,
                'clinic_notes_reports_images_id' => $clinic_notes_image_id,
                'clinic_notes_reports_images_status !=' => 9
            );

            $left_join = array(
                TBL_CLINICAL_NOTES_REPORT_IMAGE => 'clinical_notes_reports_id = clinic_notes_reports_images_reports_id'
            );

            $is_valid_data = $this->Common_model->get_single_row(TBL_CLINICAL_NOTES_REPORT, 'clinic_notes_reports_images_id', $clinic_report_belongs_array, $left_join);

            if (empty($is_valid_data)) {
                $this->my_response['status'] = false;
                $this->my_response['message'] = lang("mycontroller_invalid_request");
                $this->send_response();
            }


            $update_data = array(
                'clinic_notes_reports_images_status' => 9,
                'clinic_notes_reports_images_updated_at' => $this->utc_time_formated,
            );

            $update_where = array(
                'clinic_notes_reports_images_id' => $clinic_notes_image_id
            );

            $is_update = $this->Common_model->update(TBL_CLINICAL_NOTES_REPORT_IMAGE, $update_data, $update_where);

            if ($is_update > 0) {
                $this->my_response['status'] = true;
                $this->my_response['message'] = lang('image_deleted');
            } else {
                $this->my_response['status'] = false;
                $this->my_response['message'] = lang('failure');
            }

            $this->send_response();
        } catch (ErrorException $ex) {
            $this->error = $ex->getMessage();
            $this->store_error();
        }
    }

    /**
     * Description :- This function is used to add the prescription of the patient
     * 
     * 
     * 
     */
    public function add_prescription_post() {

        try {

            $clinic_id = !empty($this->Common_model->escape_data($this->post_data['clinic_id'])) ? trim($this->Common_model->escape_data($this->post_data['clinic_id'])) : '';
            $doctor_id = !empty($this->Common_model->escape_data($this->post_data['doctor_id'])) ? trim($this->Common_model->escape_data($this->post_data['doctor_id'])) : '';
            $patient_id = !empty($this->Common_model->escape_data($this->post_data['patient_id'])) ? trim($this->Common_model->escape_data($this->post_data['patient_id'])) : '';
            $appointment_id = !empty($this->Common_model->escape_data($this->post_data['appointment_id'])) ? trim($this->Common_model->escape_data($this->post_data['appointment_id'])) : '';
            $date = !empty($this->Common_model->escape_data($this->post_data['date'])) ? trim($this->Common_model->escape_data($this->post_data['date'])) : '';
            $user_type = !empty($this->Common_model->escape_data($this->post_data['user_type'])) ? trim($this->Common_model->escape_data($this->post_data['user_type'])) : '';
            $diet_instruction = !empty($this->Common_model->escape_data($this->post_data['diet_instruction'])) ? trim($this->Common_model->escape_data($this->post_data['diet_instruction'])) : '';
            $next_follow_up = !empty($this->Common_model->escape_data($this->post_data['next_follow_up'])) ? trim($this->Common_model->escape_data($this->post_data['next_follow_up'])) : '';
            $drug_request_json = $this->post_data['drug_request_json'];
            $drug_request_json = json_decode($drug_request_json, true);

            if (empty($user_type) ||
                    empty($patient_id) ||
                    empty($date) ||
                    empty($drug_request_json)
            ) {
                $this->bad_request();
            }


            if ($user_type == 2 && (
                    empty($clinic_id) ||
                    empty($doctor_id) ||
                    empty($appointment_id)
                    )
            ) {
                $this->bad_request();
            }

            if (validate_date_only($date) ||
                    (!empty($next_follow_up) && validate_date($next_follow_up) ) ||
                    !in_array($user_type, $this->user_type)
            ) {
                $this->my_response['status'] = false;
                $this->my_response['message'] = lang("mycontroller_invalid_request");
                $this->send_response();
            }

            $add_permission = 1;
            $edit_permission = 1;
            $delete_permission = 1;

            if ($user_type == 2) {
                $get_role_details = $this->Common_model->get_the_role($this->user_id);
                if (!empty($get_role_details['user_role_data'])) {

                    $permission_data = array(
                        'role_data' => $get_role_details['user_role_data'],
                        'module' => 5
                    );
                    $permission_data['key'] = 1;
                    $check_add_permission = $this->check_module_permission($permission_data);
                    if ($check_add_permission == 2) {
                        $edit_permission = 2;
                    }

                    $permission_data['key'] = 2;
                    $check_edit_permission = $this->check_module_permission($permission_data);
                    if ($check_edit_permission == 2) {
                        $add_permission = 2;
                    }

                    $permission_data['key'] = 3;
                    $check_delete_permission = $this->check_module_permission($permission_data);
                    if ($check_delete_permission == 2) {
                        $delete_permission = 2;
                    }
                }
            }

            $report_share_status = '';

            if ($user_type == 2) {
                //check data belongs to the doctor or not
                $requested_data = array(
                    'appointment_id' => $appointment_id,
                    'doctor_id' => $doctor_id,
                    'clinic_id' => $clinic_id,
                    'patient_id' => $patient_id
                );
                $this->check_data_belongs_doctor($requested_data);


                //get the share settings for the vital
                $setting_where = array(
                    'setting_type' => 1,
                    'setting_user_id' => $doctor_id,
                    'setting_clinic_id' => $clinic_id
                );
                $get_setting = $this->Common_model->get_setting($setting_where);

                $report_share_status = 2;

                if (!empty($get_setting)) {
                    $setting_array = json_decode($get_setting['setting_data'], true);
                    if (!empty($setting_array) && is_array($setting_array)) {
                        foreach ($setting_array as $setting) {
                            if ($setting['id'] == 3) {
                                $report_share_status = $setting['status'];
                                break;
                            }
                        }
                    }
                }
            } else {
                $report_share_status = 1;
            }

            $this->db->trans_start();

            foreach ($drug_request_json as $drug) {

                if (!empty($drug) &&
                        !empty($drug['id']) &&
                        $drug['is_delete'] == 2 &&
                        $edit_permission == 1
                ) {

                    $frequency_id = $drug['frequency_id'];

                    $update_drug_data = array(
                        'prescription_user_id' => $patient_id,
                        'prescription_drug_id' => $drug['drug_id'],
                        'prescription_date' => $date,
                        'prescription_drug_name' => $drug['drug_name'],
                        'prescription_generic_id' => $drug['generic_id'],
                        'prescription_unit_id' => $drug['unit_id'],
                        'prescription_frequency_id' => $frequency_id,
                        'prescription_frequency_value' => $drug['frequency_value'],
                        'prescription_frequency_instruction' => $drug['frequency_instruction'],
                        'prescription_intake' => $drug['intake'],
                        'prescription_intake_instruction' => $drug['intake_instruction'],
                        'prescription_duration' => $drug['duration'],
                        'prescription_duration_value' => $drug['duration_value'],
                        'prescription_updated_at' => $this->utc_time_formated,
                        'prescription_share_status' => $report_share_status,
                        'prescription_dosage' => $drug['dosage']
                    );

                    $update_drug_where = array(
                        'prescription_id' => $drug['id']
                    );

                    $is_update = $this->Common_model->update(TBL_PRESCRIPTION, $update_drug_data, $update_drug_where);

                    if ($is_update > 0) {
                        $frequency = '';
                        if ($frequency_id == 1) {
                            $frequency = json_decode(FREQUENCY_TIMING, true)[1];
                        } else if ($frequency_id == 2) {
                            $frequency = json_decode(FREQUENCY_TIMING, true)[2];
                        } else if ($frequency_id == 3) {
                            $frequency = json_decode(FREQUENCY_TIMING, true)[3];
                        } else if ($frequency_id == 4) {
                            $frequency = json_decode(FREQUENCY_TIMING, true)[4];
                        } else if ($frequency_id == 5) {
                            $frequency = json_decode(FREQUENCY_TIMING, true)[5];
                        } else if ($frequency_id == 6) {
                            $frequency = json_decode(FREQUENCY_TIMING, true)[6];
                        } else if ($frequency_id == 7) {
                            $frequency = json_decode(FREQUENCY_TIMING, true)[1];
                        } else if ($frequency_id == 8) {
                            $frequency = json_decode(FREQUENCY_TIMING, true)[1];
                        }

                        $reminder_where = array(
                            'reminder_prescription_report_id' => $drug['id']
                        );

                        //check if reminder exist or not
                        $get_reminder = $this->Common_model->get_single_row(TBL_REMINDERS, 'reminder_id', $reminder_where);
                        if (!empty($get_reminder)) {

                            $reminder_array = array(
                                'reminder_drug_id' => $drug['drug_id'],
                                'reminder_drug_name' => $drug['drug_name'],
                                'reminder_duration' => $drug['duration_value'],
                                'reminder_day' => $drug['duration'],
                                'reminder_modified_at' => $this->utc_time_formated,
                                'reminder_timing' => $frequency
                            );

                            $this->Common_model->update(TBL_REMINDERS, $reminder_array, $reminder_where);
                        } else {
                            if ($frequency_id != 9 && $frequency_id != 10) {

                                $reminder_array = array(
                                    'reminder_appointment_id' => $appointment_id,
                                    'reminder_prescription_report_id' => $drug['id'],
                                    'reminder_type' => 1,
                                    'reminder_user_id' => $patient_id,
                                    'reminder_created_by' => $doctor_id,
                                    'reminder_drug_id' => $drug['drug_id'],
                                    'reminder_drug_name' => $drug['drug_name'],
                                    'reminder_duration' => $drug['duration_value'],
                                    'reminder_day' => $drug['duration'],
                                    'reminder_start_date' => $date,
                                    'reminder_created_at' => $this->utc_time_formated,
                                    'reminder_timing' => $frequency
                                );

                                $this->Common_model->insert(TBL_REMINDERS, $reminder_array);
                            }
                        }
                    }
                } else if (!empty($drug) &&
                        !empty($drug['id']) &&
                        $drug['is_delete'] == 1 &&
                        $delete_permission == 1
                ) {

                    $update_drug_data = array(
                        'prescription_status' => 9,
                        'prescription_updated_at' => $this->utc_time_formated,
                    );

                    $update_drug_where = array(
                        'prescription_id' => $drug['id']
                    );

                    $is_update = $this->Common_model->update(TBL_PRESCRIPTION, $update_drug_data, $update_drug_where);

                    if ($is_update > 0) {
                        // also delete the reminder for the same
                        $reminder_where = array(
                            'reminder_prescription_report_id' => $drug['id']
                        );

                        $reminder_update = array(
                            'reminder_modified_at' => $this->utc_time_formated,
                            'reminder_status' => 9
                        );

                        $this->Common_model->update(TBL_REMINDERS, $reminder_update, $reminder_where);
                    }
                } else if (!empty($drug) &&
                        empty($drug['id']) &&
                        $add_permission == 1
                ) {

                    $frequency_id = $drug['frequency_id'];

                    $insert_drug_array = array(
                        'prescription_user_id' => $patient_id,
                        'prescription_drug_id' => $drug['drug_id'],
                        'prescription_date' => $date,
                        'prescription_drug_name' => $drug['drug_name'],
                        'prescription_generic_id' => $drug['generic_id'],
                        'prescription_unit_id' => $drug['unit_id'],
                        'prescription_frequency_id' => $frequency_id,
                        'prescription_frequency_value' => $drug['frequency_value'],
                        'prescription_frequency_instruction' => $drug['frequency_instruction'],
                        'prescription_intake' => $drug['intake'],
                        'prescription_intake_instruction' => $drug['intake_instruction'],
                        'prescription_duration' => $drug['duration'],
                        'prescription_duration_value' => $drug['duration_value'],
                        'prescription_created_at' => $this->utc_time_formated,
                        'prescription_share_status' => $report_share_status,
                        'prescription_dosage' => $drug['dosage']
                    );

                    if ($user_type == 2) {
                        $insert_drug_array['prescription_appointment_id'] = $appointment_id;
                        $insert_drug_array['prescription_doctor_user_id'] = $doctor_id;
                        $insert_drug_array['prescription_clinic_id'] = $clinic_id;
                    } else {
                        $insert_drug_array['prescription_doctor_user_id'] = $this->user_id;
                    }


                    $inserted_id = $this->Common_model->insert(TBL_PRESCRIPTION, $insert_drug_array);


                    if ($frequency_id != 9 && $frequency_id != 10) {

                        $reminder_array = array(
                            'reminder_appointment_id' => $appointment_id,
                            'reminder_prescription_report_id' => $inserted_id,
                            'reminder_type' => 1,
                            'reminder_user_id' => $patient_id,
                            'reminder_created_by' => $doctor_id,
                            'reminder_drug_id' => $drug['drug_id'],
                            'reminder_drug_name' => $drug['drug_name'],
                            'reminder_duration' => $drug['duration_value'],
                            'reminder_day' => $drug['duration'],
                            'reminder_start_date' => $date,
                            'reminder_created_at' => $this->utc_time_formated,
                        );

                        if ($frequency_id == 1) {
                            $reminder_array['reminder_timing'] = json_decode(FREQUENCY_TIMING, true)[1];
                        } else if ($frequency_id == 2) {
                            $reminder_array['reminder_timing'] = json_decode(FREQUENCY_TIMING, true)[2];
                        } else if ($frequency_id == 3) {
                            $reminder_array['reminder_timing'] = json_decode(FREQUENCY_TIMING, true)[3];
                        } else if ($frequency_id == 4) {
                            $reminder_array['reminder_timing'] = json_decode(FREQUENCY_TIMING, true)[4];
                        } else if ($frequency_id == 5) {
                            $reminder_array['reminder_timing'] = json_decode(FREQUENCY_TIMING, true)[5];
                        } else if ($frequency_id == 6) {
                            $reminder_array['reminder_timing'] = json_decode(FREQUENCY_TIMING, true)[6];
                        } else if ($frequency_id == 7) {
                            $reminder_array['reminder_timing'] = json_decode(FREQUENCY_TIMING, true)[1];
                        } else if ($frequency_id == 8) {
                            $reminder_array['reminder_timing'] = json_decode(FREQUENCY_TIMING, true)[1];
                        }

                        $this->Common_model->insert(TBL_REMINDERS, $reminder_array);
                    }
                }
            }


            //check mail send to the patient for the prescription
            $track_report = array(
                'patient_report_track_appointment_id' => $appointment_id,
                'patient_report_track_rx_mail' => 1,
                'patient_report_track_status' => 1
            );
            $get_track_report = $this->Common_model->get_single_row(TBL_PATIENT_REPORT_TRACK, 'patient_report_track_id', $track_report);

            if (empty($get_track_report)) {

                $get_user_name = $this->User_model->get_details_by_id($patient_id);
                $send_user_name = 'User';
                if (!empty($get_user_name['user_first_name'])) {
                    $send_user_name = $get_user_name['user_first_name'] . ' ' . $get_user_name['user_last_name'];
                }

                $get_doctor_name = $this->User_model->get_details_by_id($doctor_id);
                $send_doctor_name = 'Doctor';
                if (!empty($get_doctor_name['user_first_name'])) {
                    $send_doctor_name = $get_doctor_name['user_first_name'] . ' ' . $get_doctor_name['user_last_name'];
                }
                $send_mail = array(
                    'user_name' => $send_user_name,
                    'user_email' => $get_user_name['user_email'],
                    'doctor_name' => $send_doctor_name,
                    'template_id' => 27
                );

                $cron_job_path = CRON_PATH . " notification/send_mail_background/" . base64_encode(json_encode($send_mail));
                exec(PHP_PATH . " " . $cron_job_path . " > /dev/null &");

                $update_track_where = array(
                    'patient_report_track_appointment_id' => $appointment_id,
                );

                $update_track_data = array(
                    'patient_report_track_rx_mail' => 1,
                    'patient_report_track_updated_at' => $this->utc_time_formated
                );
                $this->Common_model->update(TBL_PATIENT_REPORT_TRACK, $update_track_data, $update_track_where);
            }

            if (!empty($next_follow_up)) {
                //add follow up and instruction
                $where = array(
                    "follow_up_appointment_id" => $appointment_id,
                    "follow_up_status" => 1
                );
                $is_follow_exist = $this->Common_model->get_single_row(TBL_PRESCRIPTION_FOLLOUP, "follow_up_id", $where);

                $insert_followup_array = array(
                    "follow_up_user_id" => $patient_id,
                    "follow_up_doctor_id" => $doctor_id,
                    "follow_up_followup_date" => $next_follow_up,
                    "follow_up_appointment_id" => $appointment_id,
                    "follow_up_status" => 1,
                    'follow_up_instruction' => $diet_instruction
                );

                if (empty($is_follow_exist)) {
                    $insert_followup_array['follow_up_created_at'] = $this->utc_time_formated;
                    $this->Common_model->insert(TBL_PRESCRIPTION_FOLLOUP, $insert_followup_array);
                } else {
                    $insert_followup_array['follow_up_modified_at'] = $this->utc_time_formated;
                    $this->Common_model->update(TBL_PRESCRIPTION_FOLLOUP, $insert_followup_array, $where);
                }
            }

            if ($this->db->trans_status() !== FALSE) {

                $this->db->trans_commit();

                //send notification for the reminder
                $notification_array = array(
                    'notification_list_user_id' => $patient_id,
                    'notification_list_user_type' => 1,
                    'notification_list_type' => 3,
                    'notification_list_message' => sprintf(lang('reminder_notification')),
                    'notification_list_created_at' => $this->utc_time_formated
                );

                $this->Common_model->insert(TBL_NOTIFICATION, $notification_array);

                $send_notification_data = array(
                    'notification_list_type' => 3
                );
                $cron_job_path = CRON_PATH . " notification/send_notification/" . base64_encode(json_encode($send_notification_data));
                exec(PHP_PATH . " " . $cron_job_path . " > /dev/null &");

                $this->my_response['status'] = true;
                $this->my_response['message'] = lang('prescription_added');
            } else {
                $this->db->trans_rollback();
                $this->my_response['status'] = false;
                $this->my_response['message'] = lang('failure');
            }

            $this->send_response();
        } catch (ErrorException $ex) {
            $this->error = $ex->getMessage();
            $this->store_error();
        }
    }

    /**
     * Description :- This function is used to add the prescription of the patient
     * 
     * 
     * 
     */
    public function clone_prescription_post() {

        try {

            $clinic_id = !empty($this->Common_model->escape_data($this->post_data['clinic_id'])) ? trim($this->Common_model->escape_data($this->post_data['clinic_id'])) : '';
            $doctor_id = !empty($this->Common_model->escape_data($this->post_data['doctor_id'])) ? trim($this->Common_model->escape_data($this->post_data['doctor_id'])) : '';
            $patient_id = !empty($this->Common_model->escape_data($this->post_data['patient_id'])) ? trim($this->Common_model->escape_data($this->post_data['patient_id'])) : '';
            $appointment_id = !empty($this->Common_model->escape_data($this->post_data['appointment_id'])) ? trim($this->Common_model->escape_data($this->post_data['appointment_id'])) : '';
            $date = !empty($this->Common_model->escape_data($this->post_data['date'])) ? trim($this->Common_model->escape_data($this->post_data['date'])) : '';
            $user_type = !empty($this->Common_model->escape_data($this->post_data['user_type'])) ? trim($this->Common_model->escape_data($this->post_data['user_type'])) : '';
            $prescription_id = !empty($this->Common_model->escape_data($this->post_data['prescription_id'])) ? trim($this->Common_model->escape_data($this->post_data['prescription_id'])) : '';

            if (empty($user_type) ||
                    empty($patient_id) ||
                    empty($date) ||
                    empty($prescription_id)
            ) {
                $this->bad_request();
            }


            if ($user_type == 2 && (
                    empty($clinic_id) ||
                    empty($doctor_id) ||
                    empty($appointment_id)
                    )
            ) {
                $this->bad_request();
            }

            if (validate_date_only($date) ||
                    !in_array($user_type, $this->user_type)
            ) {
                $this->my_response['status'] = false;
                $this->my_response['message'] = lang("mycontroller_invalid_request");
                $this->send_response();
            }

            $get_role_details = $this->Common_model->get_the_role($this->user_id);
            if (!empty($get_role_details['user_role_data'])) {
                $permission_data = array(
                    'role_data' => $get_role_details['user_role_data'],
                    'module' => 5,
                    'key' => 1
                );
                $check_module_permission = $this->check_module_permission($permission_data);
                if ($check_module_permission == 2) {
                    $this->my_response['status'] = false;
                    $this->my_response['message'] = lang('permission_error');
                    $this->send_response();
                }
            }

            $report_share_status = '';

            if ($user_type == 2) {
                //check data belongs to the doctor or not
                $requested_data = array(
                    'appointment_id' => $appointment_id,
                    'doctor_id' => $doctor_id,
                    'clinic_id' => $clinic_id,
                    'patient_id' => $patient_id
                );
                $this->check_data_belongs_doctor($requested_data);


                //get the share settings for the vital
                $setting_where = array(
                    'setting_type' => 1,
                    'setting_user_id' => $doctor_id,
                    'setting_clinic_id' => $clinic_id
                );
                $get_setting = $this->Common_model->get_setting($setting_where);

                $report_share_status = 2;

                if (!empty($get_setting)) {
                    $setting_array = json_decode($get_setting['setting_data'], true);
                    if (!empty($setting_array) && is_array($setting_array)) {
                        foreach ($setting_array as $setting) {
                            if ($setting['id'] == 3) {
                                $report_share_status = $setting['status'];
                                break;
                            }
                        }
                    }
                }
            } else {
                $report_share_status = 1;
            }

            //get the prescription detail
            $prescription_data = array(
                'prescription_id' => $prescription_id
            );
            $get_prescription_detail = $this->User_model->get_prescription_detail($prescription_data);

            if (!empty($get_prescription_detail)) {

                $frequency_id = !empty($get_prescription_detail['prescription_frequency_id']) ? $get_prescription_detail['prescription_frequency_id'] : '';
                $drug_id = !empty($get_prescription_detail['prescription_drug_id']) ? $get_prescription_detail['prescription_drug_id'] : '';
                $drug_name = !empty($get_prescription_detail['prescription_drug_name']) ? $get_prescription_detail['prescription_drug_name'] : '';
                $duration_value = !empty($get_prescription_detail['prescription_duration_value']) ? $get_prescription_detail['prescription_duration_value'] : '';
                $duration = !empty($get_prescription_detail['prescription_duration']) ? $get_prescription_detail['prescription_duration'] : '';

                $insert_drug_array = array(
                    'prescription_user_id' => $patient_id,
                    'prescription_drug_id' => $drug_id,
                    'prescription_date' => $date,
                    'prescription_drug_name' => $drug_name,
                    'prescription_generic_id' => $get_prescription_detail['prescription_generic_id'],
                    'prescription_unit_id' => $get_prescription_detail['prescription_unit_id'],
                    'prescription_frequency_id' => $get_prescription_detail['prescription_frequency_id'],
                    'prescription_frequency_value' => $get_prescription_detail['prescription_frequency_value'],
                    'prescription_frequency_instruction' => $get_prescription_detail['prescription_frequency_instruction'],
                    'prescription_intake' => $get_prescription_detail['prescription_intake'],
                    'prescription_intake_instruction' => $get_prescription_detail['prescription_intake_instruction'],
                    'prescription_duration' => $duration,
                    'prescription_duration_value' => $duration_value,
                    'prescription_diet_instruction' => $get_prescription_detail['prescription_diet_instruction'],
                    'prescription_created_at' => $this->utc_time_formated,
                    'prescription_share_status' => $report_share_status,
                    'prescription_dosage' => $get_prescription_detail['prescription_dosage']
                );

                if ($user_type == 2) {
                    $insert_drug_array['prescription_appointment_id'] = $appointment_id;
                    $insert_drug_array['prescription_doctor_user_id'] = $doctor_id;
                    $insert_drug_array['prescription_clinic_id'] = $clinic_id;
                } else {
                    $insert_drug_array['prescription_doctor_user_id'] = $this->user_id;
                }

                $inserted_id = $this->Common_model->insert(TBL_PRESCRIPTION, $insert_drug_array);

                if ($inserted_id > 0) {

                    if ($frequency_id != 9 && $frequency_id != 10) {

                        if ($duration == 3) {
                            $duration = 4;
                        }

                        $reminder_array = array(
                            'reminder_appointment_id' => $appointment_id,
                            'reminder_prescription_report_id' => $inserted_id,
                            'reminder_type' => 1,
                            'reminder_user_id' => $patient_id,
                            'reminder_created_by' => $doctor_id,
                            'reminder_drug_id' => $drug_id,
                            'reminder_drug_name' => $drug_name,
                            'reminder_duration' => $duration_value,
                            'reminder_day' => $duration,
                            'reminder_start_date' => $date,
                            'reminder_created_at' => $this->utc_time_formated,
                        );

                        if ($frequency_id == 1) {
                            $reminder_array['reminder_timing'] = json_decode(FREQUENCY_TIMING, true)[1];
                        } else if ($frequency_id == 2) {
                            $reminder_array['reminder_timing'] = json_decode(FREQUENCY_TIMING, true)[2];
                        } else if ($frequency_id == 3) {
                            $reminder_array['reminder_timing'] = json_decode(FREQUENCY_TIMING, true)[3];
                        } else if ($frequency_id == 4) {
                            $reminder_array['reminder_timing'] = json_decode(FREQUENCY_TIMING, true)[4];
                        } else if ($frequency_id == 5) {
                            $reminder_array['reminder_timing'] = json_decode(FREQUENCY_TIMING, true)[5];
                        } else if ($frequency_id == 6) {
                            $reminder_array['reminder_timing'] = json_decode(FREQUENCY_TIMING, true)[6];
                        } else if ($frequency_id == 7) {
                            $reminder_array['reminder_timing'] = json_decode(FREQUENCY_TIMING, true)[1];
                        } else if ($frequency_id == 8) {
                            $reminder_array['reminder_timing'] = json_decode(FREQUENCY_TIMING, true)[1];
                        }

                        $reminder_inserted_id = $this->Common_model->insert(TBL_REMINDERS, $reminder_array);

                        if ($reminder_inserted_id > 0) {

                            //send notification for the reminder
                            $notification_array = array(
                                'notification_list_user_id' => $patient_id,
                                'notification_list_user_type' => 1,
                                'notification_list_type' => 3,
                                'notification_list_message' => sprintf(lang('reminder_notification')),
                                'notification_list_created_at' => $this->utc_time_formated
                            );

                            $this->Common_model->insert(TBL_NOTIFICATION, $notification_array);

                            $send_notification_data = array(
                                'notification_list_type' => 3,
                                'reminder_inserted_id' => $reminder_inserted_id
                            );
                            $cron_job_path = CRON_PATH . " notification/send_notification/" . base64_encode(json_encode($send_notification_data));
                            exec(PHP_PATH . " " . $cron_job_path . " > /dev/null &");
                        }
                    }

                    //get prescription detail
                    $requested_data = array(
                        'prescription_id' => $inserted_id
                    );
                    $get_prescription_detail = $this->User_model->get_prescription_detail($requested_data);

                    $this->my_response['status'] = true;
                    $this->my_response['message'] = lang('prescription_added');
                    $this->my_response['data'] = $get_prescription_detail;
                } else {
                    $this->my_response['status'] = false;
                    $this->my_response['message'] = lang('failure');
                }
            } else {
                $this->my_response['status'] = false;
                $this->my_response['message'] = lang('failure');
            }

            $this->send_response();
        } catch (ErrorException $ex) {
            $this->error = $ex->getMessage();
            $this->store_error();
        }
    }

    /**
     * Description :- This function is used to add the prescription of the patient
     * 
     * 
     * 
     */
    public function edit_prescription_post() {

        try {

            $clinic_id = !empty($this->Common_model->escape_data($this->post_data['clinic_id'])) ? trim($this->Common_model->escape_data($this->post_data['clinic_id'])) : '';
            $prescription_id = !empty($this->Common_model->escape_data($this->post_data['prescription_id'])) ? trim($this->Common_model->escape_data($this->post_data['prescription_id'])) : '';
            $doctor_id = !empty($this->Common_model->escape_data($this->post_data['doctor_id'])) ? trim($this->Common_model->escape_data($this->post_data['doctor_id'])) : '';
            $patient_id = !empty($this->Common_model->escape_data($this->post_data['patient_id'])) ? trim($this->Common_model->escape_data($this->post_data['patient_id'])) : '';
            $appointment_id = !empty($this->Common_model->escape_data($this->post_data['appointment_id'])) ? trim($this->Common_model->escape_data($this->post_data['appointment_id'])) : '';
            $date = !empty($this->Common_model->escape_data($this->post_data['date'])) ? trim($this->Common_model->escape_data($this->post_data['date'])) : '';
            $user_type = !empty($this->Common_model->escape_data($this->post_data['user_type'])) ? trim($this->Common_model->escape_data($this->post_data['user_type'])) : '';
            $drug_id = !empty($this->Common_model->escape_data($this->post_data['drug_id'])) ? trim($this->Common_model->escape_data($this->post_data['drug_id'])) : '';
            $drug_name = !empty($this->Common_model->escape_data($this->post_data['drug_name'])) ? trim($this->Common_model->escape_data($this->post_data['drug_name'])) : '';
            $generic_id = !empty($this->Common_model->escape_data($this->post_data['generic_id'])) ? trim($this->Common_model->escape_data($this->post_data['generic_id'])) : '';
            $unit_id = !empty($this->Common_model->escape_data($this->post_data['unit_id'])) ? trim($this->Common_model->escape_data($this->post_data['unit_id'])) : '';
            $frequency_id = !empty($this->Common_model->escape_data($this->post_data['frequency_id'])) ? trim($this->Common_model->escape_data($this->post_data['frequency_id'])) : '';
            $frequency_value = !empty($this->Common_model->escape_data($this->post_data['frequency_value'])) ? trim($this->Common_model->escape_data($this->post_data['frequency_value'])) : '';
            $frequency_instruction = !empty($this->Common_model->escape_data($this->post_data['frequency_instruction'])) ? trim($this->Common_model->escape_data($this->post_data['frequency_instruction'])) : '';
            $intake = !empty($this->Common_model->escape_data($this->post_data['intake'])) ? trim($this->Common_model->escape_data($this->post_data['intake'])) : '';
            $intake_instruction = !empty($this->Common_model->escape_data($this->post_data['intake_instruction'])) ? trim($this->Common_model->escape_data($this->post_data['intake_instruction'])) : '';
            $duration = !empty($this->Common_model->escape_data($this->post_data['duration'])) ? trim($this->Common_model->escape_data($this->post_data['duration'])) : '';
            $duration_value = !empty($this->Common_model->escape_data($this->post_data['duration_value'])) ? trim($this->Common_model->escape_data($this->post_data['duration_value'])) : '';
            $diet_instruction = !empty($this->Common_model->escape_data($this->post_data['diet_instruction'])) ? trim($this->Common_model->escape_data($this->post_data['diet_instruction'])) : '';
            $next_follow_up = !empty($this->Common_model->escape_data($this->post_data['next_follow_up'])) ? trim($this->Common_model->escape_data($this->post_data['next_follow_up'])) : '';
            $dosage = !empty($this->Common_model->escape_data($this->post_data['dosage'])) ? trim($this->Common_model->escape_data($this->post_data['dosage'])) : '';


            if (empty($user_type) ||
                    empty($patient_id) ||
                    empty($date) ||
                    empty($prescription_id)
            ) {
                $this->bad_request();
            }


            if ($user_type == 2 && (
                    empty($clinic_id) ||
                    empty($doctor_id) ||
                    empty($appointment_id)
                    )
            ) {
                $this->bad_request();
            }

            if (validate_date_only($date) ||
                    (!empty($next_follow_up) && validate_date_only($next_follow_up) ) ||
                    (!empty($intake) && !in_array($intake, $this->drug_intake)) ||
                    (!empty($duration) && !in_array($duration, $this->duration)) ||
                    !in_array($user_type, $this->user_type)
            ) {
                $this->my_response['status'] = false;
                $this->my_response['message'] = lang("mycontroller_invalid_request");
                $this->send_response();
            }


            $report_share_status = '';

            if ($user_type == 2) {
                //check data belongs to the doctor or not
                $requested_data = array(
                    'appointment_id' => $appointment_id,
                    'doctor_id' => $doctor_id,
                    'clinic_id' => $clinic_id,
                    'patient_id' => $patient_id
                );
                $this->check_data_belongs_doctor($requested_data);

                //check prescription data belongs to the doctor, clinic based or not
                $prescription_belongs_array = array(
                    'prescription_doctor_user_id' => $doctor_id,
                    'prescription_clinic_id' => $clinic_id,
                    'prescription_status !=' => 9,
                    'prescription_id' => $prescription_id
                );
                $is_valid_data = $this->Common_model->validate_data(TBL_PRESCRIPTION_REPORTS, 'prescription_id', $prescription_belongs_array);

                if ($is_valid_data == 2) {
                    $this->my_response['status'] = false;
                    $this->my_response['message'] = lang("mycontroller_invalid_request");
                    $this->send_response();
                }

                //get the share settings for the vital
                $setting_where = array(
                    'setting_type' => 1,
                    'setting_user_id' => $doctor_id,
                    'setting_clinic_id' => $clinic_id
                );
                $get_setting = $this->Common_model->get_setting($setting_where);

                $report_share_status = 2;

                if (!empty($get_setting)) {
                    $setting_array = json_decode($get_setting['setting_data'], true);
                    if (!empty($setting_array) && is_array($setting_array)) {
                        foreach ($setting_array as $setting) {
                            if ($setting['id'] == 3) {
                                $report_share_status = $setting['status'];
                                break;
                            }
                        }
                    }
                }
            } else {
                $report_share_status = 1;
            }

            $update_drug_data = array(
                'prescription_user_id' => $patient_id,
                'prescription_drug_id' => $drug_id,
                'prescription_date' => $date,
                'prescription_drug_name' => $drug_name,
                'prescription_generic_id' => $generic_id,
                'prescription_unit_id' => $unit_id,
                'prescription_frequency_id' => $frequency_id,
                'prescription_frequency_value' => $frequency_value,
                'prescription_frequency_instruction' => $frequency_instruction,
                'prescription_intake' => $intake,
                'prescription_intake_instruction' => $intake_instruction,
                'prescription_duration' => $duration,
                'prescription_duration_value' => $duration_value,
                'prescription_diet_instruction' => $diet_instruction,
                'prescription_updated_at' => $this->utc_time_formated,
                'prescription_share_status' => $report_share_status,
                'prescription_dosage' => $dosage
            );

            $update_drug_where = array(
                'prescription_id' => $prescription_id
            );

            $is_update = $this->Common_model->update(TBL_PRESCRIPTION, $update_drug_data, $update_drug_where);

            if ($is_update > 0) {
                if (!empty($next_follow_up)) {
                    //add follow up 
                    $where = array(
                        "follow_up_appointment_id" => $appointment_id,
                        "follow_up_status" => 1
                    );
                    $is_follow_exist = $this->Common_model->get_single_row(TBL_PRESCRIPTION_FOLLOUP, "follow_up_id", $where);
                    $insert_followup_array = array(
                        "follow_up_user_id" => $patient_id,
                        "follow_up_doctor_id" => $doctor_id,
                        "follow_up_followup_date" => $next_follow_up,
                        "follow_up_appointment_id" => $appointment_id,
                        "follow_up_status" => 1
                    );

                    if (empty($is_follow_exist)) {
                        $insert_followup_array['follow_up_created_at'] = $this->utc_time_formated;
                        $this->Common_model->insert(TBL_PRESCRIPTION_FOLLOUP, $insert_followup_array);
                    } else {
                        $insert_followup_array['follow_up_modified_at'] = $this->utc_time_formated;
                        $this->Common_model->update(TBL_PRESCRIPTION_FOLLOUP, $insert_followup_array, $where);
                    }
                }

                $reminder_array = array(
                    'reminder_appointment_id' => $appointment_id,
                    'reminder_type' => 1,
                    'reminder_user_id' => $patient_id,
                    'reminder_created_by' => $doctor_id,
                    'reminder_drug_id' => $drug_id,
                    'reminder_drug_name' => $drug_name,
                    'reminder_duration' => $duration_value,
                    'reminder_day' => $duration,
                    'reminder_start_date' => $date,
                    'reminder_modified_at' => $this->utc_time_formated,
                );

                $need_to_insert = 1;

                if ($frequency_id != 9 && $frequency_id != 10) {
                    $need_to_insert = 2;
                }

                if ($frequency_id == 1) {
                    $reminder_array['reminder_timing'] = json_decode(FREQUENCY_TIMING, true)[1];
                } else if ($frequency_id == 2) {
                    $reminder_array['reminder_timing'] = json_decode(FREQUENCY_TIMING, true)[2];
                } else if ($frequency_id == 3) {
                    $reminder_array['reminder_timing'] = json_decode(FREQUENCY_TIMING, true)[3];
                } else if ($frequency_id == 4) {
                    $reminder_array['reminder_timing'] = json_decode(FREQUENCY_TIMING, true)[4];
                } else if ($frequency_id == 5) {
                    $reminder_array['reminder_timing'] = json_decode(FREQUENCY_TIMING, true)[5];
                } else if ($frequency_id == 6) {
                    $reminder_array['reminder_timing'] = json_decode(FREQUENCY_TIMING, true)[6];
                } else if ($frequency_id == 7) {
                    $reminder_array['reminder_timing'] = json_decode(FREQUENCY_TIMING, true)[1];
                } else if ($frequency_id == 8) {
                    $reminder_array['reminder_timing'] = json_decode(FREQUENCY_TIMING, true)[1];
                }

                //check reminder exists or not
                $reminder_where = array(
                    'reminder_prescription_report_id' => $prescription_id,
                    'reminder_status' => 1
                );
                $get_reminder = $this->Common_model->get_single_row(TBL_REMINDERS, 'reminder_id', $reminder_where);

                if (!empty($get_reminder)) {
                    $reminder_is_update = $this->Common_model->update(TBL_REMINDERS, $reminder_array, $reminder_where);
                } else {
                    if ($need_to_insert == 1) {
                        $reminder_is_update = $this->Common_model->insert(TBL_REMINDERS, $reminder_array);
                    }
                }

                if ($reminder_is_update > 0) {

                    //send notification for the reminder
                    $notification_array = array(
                        'notification_list_user_id' => $patient_id,
                        'notification_list_user_type' => 1,
                        'notification_list_type' => 3,
                        'notification_list_message' => sprintf(lang('reminder_notification')),
                        'notification_list_created_at' => $this->utc_time_formated
                    );

                    $this->Common_model->insert(TBL_NOTIFICATION, $notification_array);

                    $send_notification_data = array(
                        'notification_list_type' => 3
                    );
                    $cron_job_path = CRON_PATH . " notification/send_notification/" . base64_encode(json_encode($send_notification_data));
                    exec(PHP_PATH . " " . $cron_job_path . " > /dev/null &");
                }

                //get prescription detail
                $requested_data = array(
                    'prescription_id' => $prescription_id
                );
                $get_prescription_detail = $this->User_model->get_prescription_detail($requested_data);

                $this->my_response['status'] = true;
                $this->my_response['message'] = lang('prescription_updated');
                $this->my_response['data'] = $get_prescription_detail;
            } else {
                $this->my_response['status'] = false;
                $this->my_response['message'] = lang('failure');
            }

            $this->send_response();
        } catch (ErrorException $ex) {
            $this->error = $ex->getMessage();
            $this->store_error();
        }
    }

    /**
     * Description :- This function is used to delete the prescription of the patient
     * 
     * 
     * 
     */
    public function delete_prescription_post() {

        try {

            $clinic_id = !empty($this->Common_model->escape_data($this->post_data['clinic_id'])) ? trim($this->Common_model->escape_data($this->post_data['clinic_id'])) : '';
            $prescription_id = !empty($this->Common_model->escape_data($this->post_data['prescription_id'])) ? trim($this->Common_model->escape_data($this->post_data['prescription_id'])) : '';
            $doctor_id = !empty($this->Common_model->escape_data($this->post_data['doctor_id'])) ? trim($this->Common_model->escape_data($this->post_data['doctor_id'])) : '';
            $patient_id = !empty($this->Common_model->escape_data($this->post_data['patient_id'])) ? trim($this->Common_model->escape_data($this->post_data['patient_id'])) : '';

            if (empty($doctor_id) ||
                    empty($clinic_id) ||
                    empty($prescription_id)
            ) {
                $this->bad_request();
            }

            //check prescription data belongs to the doctor, clinic based or not
            $prescription_belongs_array = array(
                'prescription_doctor_user_id' => $doctor_id,
                'prescription_clinic_id' => $clinic_id,
                'prescription_status !=' => 9,
                'prescription_id' => $prescription_id,
                'prescription_user_id' => $patient_id
            );
            $is_valid_data = $this->Common_model->validate_data(TBL_PRESCRIPTION_REPORTS, 'prescription_id', $prescription_belongs_array);

            if ($is_valid_data == 2) {
                $this->my_response['status'] = false;
                $this->my_response['message'] = lang("mycontroller_invalid_request");
                $this->send_response();
            }


            $update_drug_data = array(
                'prescription_status' => 9,
                'prescription_updated_at' => $this->utc_time_formated,
            );

            $update_drug_where = array(
                'prescription_id' => $prescription_id
            );

            $is_update = $this->Common_model->update(TBL_PRESCRIPTION, $update_drug_data, $update_drug_where);

            if ($is_update > 0) {

                // also delete the reminder for the same
                $reminder_where = array(
                    'reminder_prescription_report_id' => $prescription_id
                );

                $reminder_update = array(
                    'reminder_modified_at' => $this->utc_time_formated,
                    'reminder_status' => 9
                );

                $reminder_is_update = $this->Common_model->update(TBL_REMINDERS, $reminder_update, $reminder_where);

                if ($reminder_is_update > 0) {

                    //send notification for the reminder
                    $notification_array = array(
                        'notification_list_user_id' => $patient_id,
                        'notification_list_user_type' => 1,
                        'notification_list_type' => 3,
                        'notification_list_message' => sprintf(lang('reminder_notification')),
                        'notification_list_created_at' => $this->utc_time_formated
                    );

                    $this->Common_model->insert(TBL_NOTIFICATION, $notification_array);

                    $send_notification_data = array(
                        'notification_list_type' => 3
                    );
                    $cron_job_path = CRON_PATH . " notification/send_notification/" . base64_encode(json_encode($send_notification_data));
                    exec(PHP_PATH . " " . $cron_job_path . " > /dev/null &");
                }

                $this->my_response['status'] = true;
                $this->my_response['message'] = lang('prescription_deleted');
            } else {
                $this->my_response['status'] = false;
                $this->my_response['message'] = lang('failure');
            }

            $this->send_response();
        } catch (ErrorException $ex) {
            $this->error = $ex->getMessage();
            $this->store_error();
        }
    }

    /**
     * Description :- This function is used to add the lab reports for the patieng
     * 
     * 
     * 
     */
    public function add_investigation_post() {

        try {

            $clinic_id = !empty($this->Common_model->escape_data($this->post_data['clinic_id'])) ? trim($this->Common_model->escape_data($this->post_data['clinic_id'])) : '';
            $doctor_id = !empty($this->Common_model->escape_data($this->post_data['doctor_id'])) ? trim($this->Common_model->escape_data($this->post_data['doctor_id'])) : '';
            $patient_id = !empty($this->Common_model->escape_data($this->post_data['patient_id'])) ? trim($this->Common_model->escape_data($this->post_data['patient_id'])) : '';
            $appointment_id = !empty($this->Common_model->escape_data($this->post_data['appointment_id'])) ? trim($this->Common_model->escape_data($this->post_data['appointment_id'])) : '';
            $date = !empty($this->Common_model->escape_data($this->post_data['date'])) ? trim($this->Common_model->escape_data($this->post_data['date'])) : '';
            $user_type = !empty($this->Common_model->escape_data($this->post_data['user_type'])) ? trim($this->Common_model->escape_data($this->post_data['user_type'])) : '';
            $lab_test_name = !empty($this->post_data['lab_test_name']) ? $this->post_data['lab_test_name'] : '';
            $instruction = !empty($this->Common_model->escape_data($this->post_data['instruction'])) ? trim($this->Common_model->escape_data($this->post_data['instruction'])) : '';

            if (empty($user_type) ||
                    empty($patient_id) ||
                    empty($date)
            ) {
                $this->bad_request();
            }


            if ($user_type == 2 && (
                    empty($clinic_id) ||
                    empty($doctor_id) ||
                    empty($appointment_id)
                    )
            ) {
                $this->bad_request();
            }

            if (validate_date_only($date) ||
                    !in_array($user_type, $this->user_type)
            ) {
                $this->my_response['status'] = false;
                $this->my_response['message'] = lang("mycontroller_invalid_request");
                $this->send_response();
            }

            //check the permission of the user
            if ($user_type == 2) {
                $get_role_details = $this->Common_model->get_the_role($this->user_id);
                if (!empty($get_role_details['user_role_data'])) {
                    $permission_data = array(
                        'role_data' => $get_role_details['user_role_data'],
                        'module' => 6,
                        'key' => 1
                    );
                    $check_module_permission = $this->check_module_permission($permission_data);
                    if ($check_module_permission == 2) {
                        $this->my_response['status'] = false;
                        $this->my_response['message'] = lang('permission_error');
                        $this->send_response();
                    }
                }
            }

            $report_share_status = '';

            if ($user_type == 2) {
                //check data belongs to the doctor or not
                $requested_data = array(
                    'appointment_id' => $appointment_id,
                    'doctor_id' => $doctor_id,
                    'clinic_id' => $clinic_id,
                    'patient_id' => $patient_id
                );
                $this->check_data_belongs_doctor($requested_data);


                //check ivestigation already exists
                $data_exists_where = array(
                    'lab_report_appointment_id' => $appointment_id,
                    'lab_report_date' => $date,
                    'lab_report_status' => 1
                );
                $this->check_data_exists(TBL_LAB_REPORTS, 'lab_report_id', $data_exists_where);

                //get the share settings for the vital
                $setting_where = array(
                    'setting_type' => 1,
                    'setting_user_id' => $doctor_id,
                    'setting_clinic_id' => $clinic_id
                );
                $get_setting = $this->Common_model->get_setting($setting_where);

                $report_share_status = 2;

                if (!empty($get_setting)) {
                    $setting_array = json_decode($get_setting['setting_data'], true);
                    if (!empty($setting_array) && is_array($setting_array)) {
                        foreach ($setting_array as $setting) {
                            if ($setting['id'] == 4) {
                                $report_share_status = $setting['status'];
                                break;
                            }
                        }
                    }
                }
            } else {
                $report_share_status = 1;
            }

            $insert_investigation = array(
                'lab_report_user_id' => $patient_id,
                'lab_report_date' => $date,
                'lab_report_test_name' => $lab_test_name,
                'lab_report_instruction' => $instruction,
                'lab_report_share_status' => $report_share_status,
                'lab_report_created_at' => $this->utc_time_formated,
            );

            if ($user_type == 2) {
                $insert_investigation['lab_report_appointment_id'] = $appointment_id;
                $insert_investigation['lab_report_doctor_user_id'] = $doctor_id;
                $insert_investigation['lab_report_clinic_id'] = $clinic_id;
            } else {
                $insert_investigation['lab_report_doctor_user_id'] = $this->user_id;
            }

            $inserted_id = $this->Common_model->insert(TBL_LAB_REPORTS, $insert_investigation);

            if ($inserted_id > 0) {


                //check mail send to the patient for the prescription
                $track_report = array(
                    'patient_report_track_appointment_id' => $appointment_id,
                    'patient_report_track_investigation_mail' => 1,
                    'patient_report_track_status' => 1
                );
                $get_track_report = $this->Common_model->get_single_row(TBL_PATIENT_REPORT_TRACK, 'patient_report_track_id', $track_report);

                if (empty($get_track_report)) {

                    $get_user_name = $this->User_model->get_details_by_id($patient_id);
                    $send_user_name = 'User';
                    if (!empty($get_user_name['user_first_name'])) {
                        $send_user_name = $get_user_name['user_first_name'] . ' ' . $get_user_name['user_last_name'];
                    }

                    $get_doctor_name = $this->User_model->get_details_by_id($doctor_id);
                    $send_doctor_name = 'Doctor';
                    if (!empty($get_doctor_name['user_first_name'])) {
                        $send_doctor_name = $get_doctor_name['user_first_name'] . ' ' . $get_doctor_name['user_last_name'];
                    }
                    $send_mail = array(
                        'user_name' => $send_user_name,
                        'user_email' => $get_user_name['user_email'],
                        'doctor_name' => $send_doctor_name,
                        'template_id' => 26
                    );

                    $cron_job_path = CRON_PATH . " notification/send_mail_background/" . base64_encode(json_encode($send_mail));
                    exec(PHP_PATH . " " . $cron_job_path . " > /dev/null &");
                    
                    $update_track_where = array(
                        'patient_report_track_appointment_id' => $appointment_id,
                    );

                    $update_track_data = array(
                        'patient_report_track_investigation_mail' => 1,
                        'patient_report_track_updated_at' => $this->utc_time_formated
                    );
                    $this->Common_model->update(TBL_PATIENT_REPORT_TRACK, $update_track_data, $update_track_where);
                }


                //get prescription detail
                $requested_data = array(
                    'lab_report_id' => $inserted_id
                );
                $get_lab_report_detail = $this->User_model->get_investigation_detail($requested_data);

                $this->my_response['status'] = true;
                $this->my_response['message'] = lang('lab_report_added');
                $this->my_response['data'] = $get_lab_report_detail;
            } else {
                $this->my_response['status'] = false;
                $this->my_response['message'] = lang('failure');
            }

            $this->send_response();
        } catch (ErrorException $ex) {
            $this->error = $ex->getMessage();
            $this->store_error();
        }
    }

    /**
     * Description :- This function is used to eidt the lab reports for the patient
     * 
     * 
     * 
     */
    public function edit_investigation_post() {

        try {

            $investigation_id = !empty($this->Common_model->escape_data($this->post_data['investigation_id'])) ? trim($this->Common_model->escape_data($this->post_data['investigation_id'])) : '';
            $clinic_id = !empty($this->Common_model->escape_data($this->post_data['clinic_id'])) ? trim($this->Common_model->escape_data($this->post_data['clinic_id'])) : '';
            $doctor_id = !empty($this->Common_model->escape_data($this->post_data['doctor_id'])) ? trim($this->Common_model->escape_data($this->post_data['doctor_id'])) : '';
            $patient_id = !empty($this->Common_model->escape_data($this->post_data['patient_id'])) ? trim($this->Common_model->escape_data($this->post_data['patient_id'])) : '';
            $appointment_id = !empty($this->Common_model->escape_data($this->post_data['appointment_id'])) ? trim($this->Common_model->escape_data($this->post_data['appointment_id'])) : '';
            $date = !empty($this->Common_model->escape_data($this->post_data['date'])) ? trim($this->Common_model->escape_data($this->post_data['date'])) : '';
            $user_type = !empty($this->Common_model->escape_data($this->post_data['user_type'])) ? trim($this->Common_model->escape_data($this->post_data['user_type'])) : '';
            $lab_test_name = !empty($this->post_data['lab_test_name']) ? $this->post_data['lab_test_name'] : '';
            $instruction = !empty($this->Common_model->escape_data($this->post_data['instruction'])) ? trim($this->Common_model->escape_data($this->post_data['instruction'])) : '';

            if (empty($user_type) ||
                    empty($patient_id) ||
                    empty($date) ||
                    empty($investigation_id)
            ) {
                $this->bad_request();
            }


            if ($user_type == 2 && (
                    empty($clinic_id) ||
                    empty($doctor_id) ||
                    empty($appointment_id)
                    )
            ) {
                $this->bad_request();
            }

            if (validate_date_only($date) ||
                    !in_array($user_type, $this->user_type)
            ) {
                $this->my_response['status'] = false;
                $this->my_response['message'] = lang("mycontroller_invalid_request");
                $this->send_response();
            }

            //check the permission of the user
            if ($user_type == 2) {
                $get_role_details = $this->Common_model->get_the_role($this->user_id);
                if (!empty($get_role_details['user_role_data'])) {
                    $permission_data = array(
                        'role_data' => $get_role_details['user_role_data'],
                        'module' => 6,
                        'key' => 2
                    );
                    $check_module_permission = $this->check_module_permission($permission_data);
                    if ($check_module_permission == 2) {
                        $this->my_response['status'] = false;
                        $this->my_response['message'] = lang('permission_error');
                        $this->send_response();
                    }
                }
            }

            $report_share_status = '';

            if ($user_type == 2) {
                //check data belongs to the doctor or not
                $requested_data = array(
                    'appointment_id' => $appointment_id,
                    'doctor_id' => $doctor_id,
                    'clinic_id' => $clinic_id,
                    'patient_id' => $patient_id
                );
                $this->check_data_belongs_doctor($requested_data);


                //check prescription data belongs to the doctor, clinic based or not
                $lab_reports_belongs_array = array(
                    'lab_report_doctor_user_id' => $doctor_id,
                    'lab_report_clinic_id' => $clinic_id,
                    'lab_report_id' => $investigation_id,
                    'lab_report_status !=' => 9
                );
                $is_valid_data = $this->Common_model->validate_data(TBL_LAB_REPORTS, 'lab_report_id', $lab_reports_belongs_array);

                if ($is_valid_data == 2) {
                    $this->my_response['status'] = false;
                    $this->my_response['message'] = lang("mycontroller_invalid_request");
                    $this->send_response();
                }

                //get the share settings for the vital
                $setting_where = array(
                    'setting_type' => 1,
                    'setting_user_id' => $doctor_id,
                    'setting_clinic_id' => $clinic_id
                );
                $get_setting = $this->Common_model->get_setting($setting_where);

                $report_share_status = 2;

                if (!empty($get_setting)) {
                    $setting_array = json_decode($get_setting['setting_data'], true);
                    if (!empty($setting_array) && is_array($setting_array)) {
                        foreach ($setting_array as $setting) {
                            if ($setting['id'] == 4) {
                                $report_share_status = $setting['status'];
                                break;
                            }
                        }
                    }
                }
            } else {
                $report_share_status = 1;
            }

            $update_investigation_data = array(
                'lab_report_date' => $date,
                'lab_report_test_name' => $lab_test_name,
                'lab_report_instruction' => $instruction,
                'lab_report_share_status' => $report_share_status,
                'lab_report_updated_at' => $this->utc_time_formated,
            );

            $update_investigation_where = array(
                'lab_report_id' => $investigation_id
            );

            $is_update = $this->Common_model->update(TBL_LAB_REPORTS, $update_investigation_data, $update_investigation_where);

            if ($is_update > 0) {

                //get prescription detail
                $requested_data = array(
                    'lab_report_id' => $investigation_id
                );
                $get_lab_report_detail = $this->User_model->get_investigation_detail($requested_data);

                $this->my_response['status'] = true;
                $this->my_response['message'] = lang('lab_report_updated');
                $this->my_response['data'] = $get_lab_report_detail;
            } else {
                $this->my_response['status'] = false;
                $this->my_response['message'] = lang('failure');
            }

            $this->send_response();
        } catch (ErrorException $ex) {
            $this->error = $ex->getMessage();
            $this->store_error();
        }
    }

    /**
     * Description :- This function is used to add the lab reports for the patieng
     * 
     * 
     * 
     */
    public function add_procedure_post() {

        try {

            $clinic_id = !empty($this->Common_model->escape_data($this->post_data['clinic_id'])) ? trim($this->Common_model->escape_data($this->post_data['clinic_id'])) : '';
            $doctor_id = !empty($this->Common_model->escape_data($this->post_data['doctor_id'])) ? trim($this->Common_model->escape_data($this->post_data['doctor_id'])) : '';
            $patient_id = !empty($this->Common_model->escape_data($this->post_data['patient_id'])) ? trim($this->Common_model->escape_data($this->post_data['patient_id'])) : '';
            $appointment_id = !empty($this->Common_model->escape_data($this->post_data['appointment_id'])) ? trim($this->Common_model->escape_data($this->post_data['appointment_id'])) : '';
            $date = !empty($this->Common_model->escape_data($this->post_data['date'])) ? trim($this->Common_model->escape_data($this->post_data['date'])) : '';
            $user_type = !empty($this->Common_model->escape_data($this->post_data['user_type'])) ? trim($this->Common_model->escape_data($this->post_data['user_type'])) : '';
            $procedure = !empty($this->post_data['procedure']) ? $this->post_data['procedure'] : '';
            $procedure_note = !empty($this->Common_model->escape_data($this->post_data['procedure_note'])) ? trim($this->Common_model->escape_data($this->post_data['procedure_note'])) : '';

            if (empty($user_type) ||
                    empty($patient_id) ||
                    empty($date)
            ) {
                $this->bad_request();
            }


            if ($user_type == 2 && (
                    empty($clinic_id) ||
                    empty($doctor_id) ||
                    empty($appointment_id)
                    )
            ) {
                $this->bad_request();
            }

            if (validate_date_only($date) ||
                    !in_array($user_type, $this->user_type)
            ) {
                $this->my_response['status'] = false;
                $this->my_response['message'] = lang("mycontroller_invalid_request");
                $this->send_response();
            }

            //check the user permisstion
            if ($user_type == 2) {
                $get_role_details = $this->Common_model->get_the_role($this->user_id);
                if (!empty($get_role_details['user_role_data'])) {
                    $permission_data = array(
                        'role_data' => $get_role_details['user_role_data'],
                        'module' => 7,
                        'key' => 1
                    );
                    $check_module_permission = $this->check_module_permission($permission_data);
                    if ($check_module_permission == 2) {
                        $this->my_response['status'] = false;
                        $this->my_response['message'] = lang('permission_error');
                        $this->send_response();
                    }
                }
            }

            $report_share_status = '';

            if ($user_type == 2) {
                //check data belongs to the doctor or not
                $requested_data = array(
                    'appointment_id' => $appointment_id,
                    'doctor_id' => $doctor_id,
                    'clinic_id' => $clinic_id,
                    'patient_id' => $patient_id
                );
                $this->check_data_belongs_doctor($requested_data);

                //check procedure already exists
                $data_exists_where = array(
                    'procedure_report_appointment_id' => $appointment_id,
                    'procedure_report_date' => $date,
                    'procedure_report_status' => 1
                );
                $this->check_data_exists(TBL_PROCEDURE_REPORTS, 'procedure_report_id', $data_exists_where);

                //get the share settings for the vital
                $setting_where = array(
                    'setting_type' => 1,
                    'setting_user_id' => $doctor_id,
                    'setting_clinic_id' => $clinic_id
                );
                $get_setting = $this->Common_model->get_setting($setting_where);

                $report_share_status = 2;

                if (!empty($get_setting)) {
                    $setting_array = json_decode($get_setting['setting_data'], true);
                    if (!empty($setting_array) && is_array($setting_array)) {
                        foreach ($setting_array as $setting) {
                            if ($setting['id'] == 5) {
                                $report_share_status = $setting['status'];
                                break;
                            }
                        }
                    }
                }
            } else {
                $report_share_status = 1;
            }

            $insert_procedure = array(
                'procedure_report_user_id' => $patient_id,
                'procedure_report_date' => $date,
                'procedure_report_procedure_text' => $procedure,
                'procedure_report_note' => $procedure_note,
                'procedure_report_share_status' => $report_share_status,
                'procedure_report_created_at' => $this->utc_time_formated,
            );

            if ($user_type == 2) {
                $insert_procedure['procedure_report_appointment_id'] = $appointment_id;
                $insert_procedure['procedure_report_doctor_user_id'] = $doctor_id;
                $insert_procedure['procedure_report_clinic_id'] = $clinic_id;
            } else {
                $insert_procedure['procedure_report_doctor_user_id'] = $this->user_id;
            }

            $inserted_id = $this->Common_model->insert(TBL_PROCEDURE_REPORTS, $insert_procedure);

            if ($inserted_id > 0) {

                //get prescription detail
                $requested_data = array(
                    'procedure_report_id' => $inserted_id
                );
                $get_procedure_report_detail = $this->User_model->get_procedure_detail($requested_data);

                $this->my_response['status'] = true;
                $this->my_response['message'] = lang('procedure_added');
                $this->my_response['data'] = $get_procedure_report_detail;
            } else {
                $this->my_response['status'] = false;
                $this->my_response['message'] = lang('failure');
            }

            $this->send_response();
        } catch (ErrorException $ex) {
            $this->error = $ex->getMessage();
            $this->store_error();
        }
    }

    /**
     * Description :- This function is used to eidt the procedure for the patient
     * 
     * 
     * 
     */
    public function edit_procedure_post() {

        try {

            $procedure_id = !empty($this->Common_model->escape_data($this->post_data['procedure_id'])) ? trim($this->Common_model->escape_data($this->post_data['procedure_id'])) : '';
            $clinic_id = !empty($this->Common_model->escape_data($this->post_data['clinic_id'])) ? trim($this->Common_model->escape_data($this->post_data['clinic_id'])) : '';
            $doctor_id = !empty($this->Common_model->escape_data($this->post_data['doctor_id'])) ? trim($this->Common_model->escape_data($this->post_data['doctor_id'])) : '';
            $patient_id = !empty($this->Common_model->escape_data($this->post_data['patient_id'])) ? trim($this->Common_model->escape_data($this->post_data['patient_id'])) : '';
            $appointment_id = !empty($this->Common_model->escape_data($this->post_data['appointment_id'])) ? trim($this->Common_model->escape_data($this->post_data['appointment_id'])) : '';
            $date = !empty($this->Common_model->escape_data($this->post_data['date'])) ? trim($this->Common_model->escape_data($this->post_data['date'])) : '';
            $user_type = !empty($this->Common_model->escape_data($this->post_data['user_type'])) ? trim($this->Common_model->escape_data($this->post_data['user_type'])) : '';
            $procedure = !empty($this->post_data['procedure']) ? $this->post_data['procedure'] : '';
            $procedure_note = !empty($this->Common_model->escape_data($this->post_data['procedure_note'])) ? trim($this->Common_model->escape_data($this->post_data['procedure_note'])) : '';

            if (empty($user_type) ||
                    empty($patient_id) ||
                    empty($date) ||
                    empty($procedure_id)
            ) {
                $this->bad_request();
            }


            if ($user_type == 2 && (
                    empty($clinic_id) ||
                    empty($doctor_id) ||
                    empty($appointment_id)
                    )
            ) {
                $this->bad_request();
            }

            if (validate_date_only($date) ||
                    !in_array($user_type, $this->user_type)
            ) {
                $this->my_response['status'] = false;
                $this->my_response['message'] = lang("mycontroller_invalid_request");
                $this->send_response();
            }

            //check the user permisstion
            if ($user_type == 2) {
                $get_role_details = $this->Common_model->get_the_role($this->user_id);
                if (!empty($get_role_details['user_role_data'])) {
                    $permission_data = array(
                        'role_data' => $get_role_details['user_role_data'],
                        'module' => 7,
                        'key' => 2
                    );
                    $check_module_permission = $this->check_module_permission($permission_data);
                    if ($check_module_permission == 2) {
                        $this->my_response['status'] = false;
                        $this->my_response['message'] = lang('permission_error');
                        $this->send_response();
                    }
                }
            }


            $report_share_status = '';

            if ($user_type == 2) {
                //check data belongs to the doctor or not
                $requested_data = array(
                    'appointment_id' => $appointment_id,
                    'doctor_id' => $doctor_id,
                    'clinic_id' => $clinic_id,
                    'patient_id' => $patient_id
                );
                $this->check_data_belongs_doctor($requested_data);


                //check prescription data belongs to the doctor, clinic based or not
                $procedure_reports_belongs_array = array(
                    'procedure_report_doctor_user_id' => $doctor_id,
                    'procedure_report_clinic_id' => $clinic_id,
                    'procedure_report_id' => $procedure_id,
                    'procedure_report_status !=' => 9
                );
                $is_valid_data = $this->Common_model->validate_data(TBL_PROCEDURE_REPORTS, 'procedure_report_id', $procedure_reports_belongs_array);

                if ($is_valid_data == 2) {
                    $this->my_response['status'] = false;
                    $this->my_response['message'] = lang("mycontroller_invalid_request");
                    $this->send_response();
                }

                //get the share settings for the vital
                $setting_where = array(
                    'setting_type' => 1,
                    'setting_user_id' => $doctor_id,
                    'setting_clinic_id' => $clinic_id
                );
                $get_setting = $this->Common_model->get_setting($setting_where);

                $report_share_status = 2;

                if (!empty($get_setting)) {
                    $setting_array = json_decode($get_setting['setting_data'], true);
                    if (!empty($setting_array) && is_array($setting_array)) {
                        foreach ($setting_array as $setting) {
                            if ($setting['id'] == 5) {
                                $report_share_status = $setting['status'];
                                break;
                            }
                        }
                    }
                }
            } else {
                $report_share_status = 1;
            }

            $update_procedure_data = array(
                'procedure_report_date' => $date,
                'procedure_report_procedure_text' => $procedure,
                'procedure_report_note' => $procedure_note,
                'procedure_report_share_status' => $report_share_status,
                'procedure_report_updated_at' => $this->utc_time_formated,
            );

            $update_procedure_where = array(
                'procedure_report_id' => $procedure_id
            );

            $is_update = $this->Common_model->update(TBL_PROCEDURE_REPORTS, $update_procedure_data, $update_procedure_where);

            if ($is_update > 0) {

                //get prescription detail
                $requested_data = array(
                    'procedure_report_id' => $procedure_id
                );
                $get_procedure_report_detail = $this->User_model->get_procedure_detail($requested_data);

                $this->my_response['status'] = true;
                $this->my_response['message'] = lang('procedure_updated');
                $this->my_response['data'] = $get_procedure_report_detail;
            } else {
                $this->my_response['status'] = false;
                $this->my_response['message'] = lang('failure');
            }

            $this->send_response();
        } catch (ErrorException $ex) {
            $this->error = $ex->getMessage();
            $this->store_error();
        }
    }

    /**
     * Description :- This function is used to get the details of the patient 
     * prescription, reports, tests etc..
     * 
     * 
     * 
     */
    public function get_patient_report_detail_post() {
        try {

            $clinic_id = !empty($this->Common_model->escape_data($this->post_data['clinic_id'])) ? trim($this->Common_model->escape_data($this->post_data['clinic_id'])) : '';
            $doctor_id = !empty($this->Common_model->escape_data($this->post_data['doctor_id'])) ? trim($this->Common_model->escape_data($this->post_data['doctor_id'])) : '';
            $patient_id = !empty($this->Common_model->escape_data($this->post_data['patient_id'])) ? trim($this->Common_model->escape_data($this->post_data['patient_id'])) : '';
            $appointment_id = !empty($this->Common_model->escape_data($this->post_data['appointment_id'])) ? trim($this->Common_model->escape_data($this->post_data['appointment_id'])) : '';
            $date = !empty($this->Common_model->escape_data($this->post_data['date'])) ? trim($this->Common_model->escape_data($this->post_data['date'])) : '';
            $key = !empty($this->Common_model->escape_data($this->post_data['key'])) ? trim($this->Common_model->escape_data($this->post_data['key'])) : '';

            if (empty($patient_id) ||
                    empty($date) ||
                    empty($key) ||
                    empty($clinic_id) ||
                    empty($doctor_id) ||
                    empty($appointment_id)
            ) {
                $this->bad_request();
            }


            if (validate_date_only($date)) {
                $this->my_response['status'] = false;
                $this->my_response['message'] = lang("mycontroller_invalid_request");
                $this->send_response();
            }

            //check data belongs to the doctor or not
            $requested_data = array(
                'appointment_id' => $appointment_id,
                'doctor_id' => $doctor_id,
                'clinic_id' => $clinic_id,
                'patient_id' => $patient_id
            );
            $this->check_data_belongs_doctor($requested_data);

            $detail_found = 1;

            if ($key != 8) {
                $permission_data = array();
                $get_role_details = $this->Common_model->get_the_role($this->user_id);
                if (!empty($get_role_details['user_role_data'])) {
                    $permission_data['role_data'] = $get_role_details['user_role_data'];
                    $permission_data['key'] = 3;
                    if ($key == 1) {
                        $permission_data['module'] = 3;
                    } else if ($key == 2) {
                        $permission_data['module'] = 4;
                    } else if ($key == 3) {
                        $permission_data['module'] = 5;
                    } else if ($key == 4) {
                        $permission_data['module'] = 6;
                    } else if ($key == 5) {
                        $permission_data['module'] = 7;
                    } else if ($key == 6) {
                        $permission_data['module'] = 8;
                    } else if ($key == 7) {
                        $permission_data['module'] = 9;
                    }
                }

                if (!empty($permission_data)) {
                    $check_module_permission = $this->check_module_permission($permission_data);
                    if ($check_module_permission == 2) {
                        $this->my_response['status'] = false;
                        $this->my_response['message'] = lang('permission_error');
                        $this->send_response();
                    }
                }

                $patient_detail_params = array(
                    'key' => $key,
                    'appointment_id' => $appointment_id,
                    'clinic_id' => $clinic_id,
                    'doctor_id' => $doctor_id,
                    'date' => $date,
                    'patient_id' => $patient_id,
                    'user_type' => 2
                );
                $get_patient_report_detail = $this->User_model->get_patient_report_detail($patient_detail_params);

                if (empty($get_patient_report_detail)) {
                    $detail_found = 2;
                }
            } else {

                $patient_detail_params = array(
                    'appointment_id' => $appointment_id,
                    'clinic_id' => $clinic_id,
                    'doctor_id' => $doctor_id,
                    'date' => $date,
                    'patient_id' => $patient_id,
                    'user_type' => 1
                );

                $patient_detail_params['key'] = 1;
                $vital = $this->User_model->get_patient_report_detail($patient_detail_params);

                $patient_detail_params['key'] = 2;
                $clinical_notes = $this->User_model->get_patient_report_detail($patient_detail_params);

                $patient_detail_params['key'] = 3;
                $prescription = $this->User_model->get_patient_report_detail($patient_detail_params);

                $patient_detail_params['key'] = 4;
                $investigation = $this->User_model->get_patient_report_detail($patient_detail_params);

                $patient_detail_params['key'] = 5;
                $procedure = $this->User_model->get_patient_report_detail($patient_detail_params);

                $patient_detail_params['key'] = 6;
                $reports = $this->User_model->get_patient_report_detail($patient_detail_params);

                $get_patient_report_detail = array(
                    'prescription' => $prescription,
                    'reports' => $reports
                );

                if (!empty($clinical_notes) && is_array($clinical_notes) && count($clinical_notes) > 0) {
                    $get_patient_report_detail['clinical_notes'] = (object) $clinical_notes;
                }

                if (!empty($vital) && is_array($vital) && count($vital) > 0) {
                    $get_patient_report_detail['vital'] = (object) $vital;
                }

                if (!empty($procedure) && is_array($procedure) && count($procedure) > 0) {
                    $get_patient_report_detail['procedure'] = (object) $procedure;
                }

                if (!empty($investigation) && is_array($investigation) && count($investigation) > 0) {
                    $get_patient_report_detail['investigation'] = (object) $investigation;
                }

                if (empty($clinical_notes) &&
                        empty($procedure) &&
                        empty($vital) &&
                        empty($prescription) &&
                        empty($reports) &&
                        empty($investigation)
                ) {
                    $detail_found = 2;
                }
            }
           
    /*START - generate pdf file and upload on server and url response give*/
        $upload_path = UPLOAD_REL_PATH . "/" . PRESCRIPTION_PDF_FOLDER . "/" . $appointment_id."_prescription".".pdf";
        $s3_upload_path = PRESCRIPTION_PDF_FOLDER . "/" . $appointment_id."_prescription".".pdf";
        IMAGE_MANIPULATION_URL . $s3_upload_path;
        include_once BUCKET_HELPER_PATH;
        $pdf_path = IMAGE_MANIPULATION_URL . $s3_upload_path;
        $result = checkResource($s3_upload_path);
        $appointment_row = $this->Common_model->get_single_row_by_query("SELECT appointment_date FROM " . TBL_APPOINTMENTS . " WHERE appointment_id='" . $appointment_id . "'");
        if(!$result || $appointment_row['appointment_date'] == date('Y-m-d')) {
            $check_patient_appointment_sql = "
            SELECT 
                appointment_date,
                appointment_type,
                user_first_name,
                user_last_name,
                user_sign_filepath,
                user_phone_number,
                doctor_detail_speciality,
                clinic_name,
                clinic_contact_number,
                clinic_email,
                address_name,
                address_name_one,
                address_locality,
                address_pincode,
                city_name,
                state_name,
                GROUP_CONCAT(DISTINCT(doctor_qualification_degree) ORDER BY doctor_qualification_id ASC) AS doctor_qualification,
                GROUP_CONCAT(DISTINCT(doctor_council_registration_number)) AS doctor_regno
            FROM 
                " . TBL_APPOINTMENTS . "
            LEFT JOIN 
                " . TBL_USERS . " ON appointment_doctor_user_id=user_id
            LEFT JOIN 
                " . TBL_DOCTOR_DETAILS . " ON  user_id = doctor_detail_doctor_id
            LEFT JOIN 
                " . TBL_ADDRESS . " ON address_user_id = appointment_clinic_id AND address_type = 2
            LEFT JOIN 
                " . TBL_CITIES . " ON address_city_id = city_id
            LEFT JOIN 
                " . TBL_STATES . " ON address_state_id = state_id
            LEFT JOIN 
               " . TBL_CLINICS . " ON appointment_clinic_id = clinic_id
            LEFT JOIN
                ".TBL_DOCTOR_EDUCATIONS." ON appointment_doctor_user_id = doctor_qualification_user_id AND doctor_qualification_status = 1
            LEFT JOIN
                ".TBL_DOCTOR_REGISTRATIONS." ON appointment_doctor_user_id = doctor_registration_user_id AND doctor_registration_status = 1 
            WHERE
                appointment_user_id='" . $patient_id . "' AND 
                appointment_doctor_user_id='" . $doctor_id . "' AND 
                appointment_id='" . $appointment_id . "'
        ";
        $check_patient_appointment = $this->Common_model->get_single_row_by_query($check_patient_appointment_sql);
            $get_patient_column = "user_first_name,
                               user_last_name,
                               user_email,
                               user_phone_number, 
                               user_gender,
                               user_unique_id,
                               user_details_dob ";
        $left_join = array(
            TBL_USER_DETAILS => 'user_id = user_details_user_id'
        );
        $get_patient_data = $this->Common_model->get_single_row(TBL_USERS, $get_patient_column, array("user_id" => $patient_id), $left_join);

        $view_data = array(
            "doctor_data" => $check_patient_appointment,
            "patient_data" => $get_patient_data,
            "vitalsign_data" => array(),
            "clinicnote_data" => array(),
            "prescription_data" => array(),
            "patient_lab_orders_data" => array(),
            "files_data" => array()
        );
           $get_vitalsign_data_sql = "
                SELECT 
                    vital_report_spo2,
                    vital_report_weight,
                    vital_report_bloodpressure_systolic,
                    vital_report_bloodpressure_diastolic,
                    vital_report_bloodpressure_type,
                    vital_report_pulse,
                    vital_report_temperature,
                    vital_report_temperature_type,
                    vital_report_temperature_taken,
                    vital_report_resp_rate
                FROM " . TBL_VITAL_REPORTS . "
                WHERE 
                    vital_report_appointment_id='" . $appointment_id . "' AND 
                    vital_report_status=1 AND vital_report_share_status = 1
            ";
            $get_vitalsign_data = $this->Common_model->get_single_row_by_query($get_vitalsign_data_sql);
            $view_data['vitalsign_data'] = $get_vitalsign_data;
			
			$get_clinicnote_data_sql = "
                SELECT 
                    clinical_notes_reports_kco,
                    clinical_notes_reports_complaints,
                    clinical_notes_reports_observation,
                    clinical_notes_reports_diagnoses,
                    clinical_notes_reports_add_notes
                FROM " . TBL_CLINICAL_REPORTS . "
                WHERE 
                    clinical_notes_reports_appointment_id='" . $appointment_id . "' AND 
                    clinical_notes_reports_status=1 AND clinical_notes_reports_share_status = 1
            ";
            $get_clinicnote_data = $this->Common_model->get_single_row_by_query($get_clinicnote_data_sql);
            $view_data['clinicnote_data'] = $get_clinicnote_data;
			
			
			
			$patient_prescription_sql = "
                SELECT 
                    prescription_drug_name, 
                    drug_frequency_name, 
                    prescription_duration, 
                    prescription_duration_value, 
                    prescription_intake, 
                    prescription_dosage,
                    prescription_frequency_id,
                    drug_unit_is_qty_calculate,
                    drug_unit_medicine_type,
                    drug_unit_name,
                    GROUP_CONCAT(drug_generic_title) as drug_generic_title,
                    prescription_intake_instruction,
                    prescription_frequency_instruction,
                    follow_up_followup_date,
                    follow_up_instruction,
                    prescription_frequency_value as freq
                FROM 
                    " . TBL_PRESCRIPTION_REPORTS . " 
                LEFT JOIN
                    ".TBL_PRESCRIPTION_FOLLOUP." ON prescription_appointment_id = follow_up_appointment_id
                LEFT JOIN 
                    " . TBL_DRUG_FREQUENCY . " ON prescription_frequency_id=drug_frequency_id 
                LEFT JOIN 
                    " . TBL_DRUG_UNIT . " ON prescription_unit_id = drug_unit_id
                LEFT JOIN 
                    " . TBL_DRUG_GENERIC . " ON FIND_IN_SET(drug_generic_id, prescription_generic_id)  AND  drug_generic_status = 1
                WHERE 
                    prescription_appointment_id='" . $appointment_id . "' AND 
                    prescription_status=1 AND prescription_share_status = 1
                GROUP BY 
                    prescription_id
            ";
            // echo $patient_prescription_sql;exit;
            $patient_prescription = $this->Common_model->get_all_rows_by_query($patient_prescription_sql);
            $view_data['prescription_data'] = $patient_prescription;
			
			
			$get_lab_orders_sql = "
                SELECT 
                    lab_report_test_name
                FROM " . TBL_LAB_REPORTS . "
                WHERE 
                    lab_report_appointment_id='" . $appointment_id . "' AND 
                    lab_report_status=1 AND lab_report_share_status=1
            ";
            $get_lab_orders = $this->Common_model->get_single_row_by_query($get_lab_orders_sql);
            $view_data['patient_lab_orders_data'] = $get_lab_orders;
			
			$get_procedure_sql = "
                                    SELECT
                                        procedure_report_procedure_text,
                                        procedure_report_note
                                    FROM
                                        " . TBL_PROCEDURE_REPORTS . "
                                    WHERE
                                        procedure_report_appointment_id = '" . $appointment_id . "' AND
                                        procedure_report_status = 1 AND procedure_report_share_status=1 
                ";
            $get_procedure_report = $this->Common_model->get_single_row_by_query($get_procedure_sql);
            $view_data['procedure_data'] = $get_procedure_report;
			
			
			$columns = '
                        billing_grand_total,
                        billing_discount,
                        billing_total_payable,
                        billing_advance_amount,
                        billing_paid_amount,
                        billing_detail_name,
                        billing_detail_unit,
                        billing_detail_basic_cost,
                        billing_detail_discount,
                        billing_detail_discount_type,
                        billing_detail_tax_id,
                        billing_detail_total,
                        billing_detail_id
                        ';

            $get_billing_sql = "    SELECT 
                                        " . $columns . " 
                                    FROM 
                                        " . TBL_BILLING . " 
                                    LEFT JOIN 
                                        " . TBL_BILLING_DETAILS . " 
                                    ON 
                                        billing_id = billing_detail_billing_id AND billing_detail_status = 1
                                    WHERE 
                                        billing_appointment_id = '" . $appointment_id . "'
                                    AND 
                                        billing_status = 1 ";

            $get_billing_data = $this->Common_model->get_all_rows_by_query($get_billing_sql);
            $view_data['billing_data'] = $get_billing_data;
            $view_data['billing_data'] = array();
            $with_teleCunsultation = (!empty($check_patient_appointment['appointment_type']) && in_array($check_patient_appointment['appointment_type'], [4,5])) ? true : false;
            if($with_teleCunsultation) {
                $view_data['teleConsultationMsg'] = 'The prescription is given on telephonic consultation.';
            }
            $patient_link_enable = $this->Common_model->get_single_row('me_global_settings','global_setting_value', ['global_setting_key'=> 'patient_link_enable']);
            $patient_tool_document = $this->Common_model->get_all_rows('me_patient_documents_shared','id', ['appointment_id'=> $appointment_id]);
            $view_data['patient_tool_document'] = $patient_tool_document;
            $view_html = $this->load->view("prints/charting", $view_data, true);
            $doctor_data = $view_data['doctor_data'];
            $address_data = [
                'address_name' => $doctor_data['address_name'],
                'address_name_one' => $doctor_data['address_name_one'],
                'address_locality' => $doctor_data['address_locality'],
                'city_name' => $doctor_data['city_name'],
                'state_name' => $doctor_data['state_name'],
                'address_pincode' => $doctor_data['address_pincode']
            ];
            $header_margin = 22;
            $char_len = strlen($doctor_data['doctor_detail_speciality']);
            $char_len2 = strlen($doctor_data['doctor_qualification']);
            $speciality_margin = (int) (4 * (ceil($char_len/40)));
            $qua_margin = (int) (4 * (ceil($char_len2/40)));
            $left_height = $speciality_margin + $qua_margin;

            $address_char_len = strlen(clinic_address($address_data));
            $right_height = (int) (4 * (ceil($address_char_len/40)));
            if($right_height > $left_height)
                $header_margin = $header_margin + $right_height;
            else
                $header_margin = $header_margin + $left_height;
            require_once MPDF_PATH;
            $lang_code = 'en-GB';
            $mpdf = new MPDF(
                    $lang_code, // mode - default '' //sd
                    'A4', // format - A4, for example, default ''
                    0, // font size - default 0
                    'arial', // default font family
                    8, // margin_left
                    8, // margin right
                    $header_margin, // margin top
                    8, // margin bottom
                    8, // margin header
                    5, // margin footer
                    'P'   // L - landscape, P - portrait
            );
            
            $mpdf->useOnlyCoreFonts = true;
            $mpdf->SetDisplayMode('real');
            $mpdf->list_indent_first_level = 0;
            $mpdf->setAutoBottomMargin = 'stretch';
            $date_and_time = date('m/d/Y h:i:s a', time());
            $patient_name = $view_data['patient_data']['user_first_name'];
            $mpdf->SetTitle('Rx_'.$patient_name.'_'.$date_and_time);
            $mpdf->SetHTMLHeader('
                <table style="width:100%;border-bottom:1px solid #000">
                    <tr>
                        <td width="50%" style="text-align:left;vertical-align:top">
                         ' . DOCTOR. " ".$doctor_data['user_first_name'] . " " . $doctor_data['user_last_name'] . "<br>" . '
                         ' . 'Reg. No. '.$doctor_data['doctor_regno'] . "<br>" . '
                         ' . $doctor_data['doctor_detail_speciality'] . "<br>" . '
                         ' . $doctor_data['doctor_qualification'] . "<br>" . '    
                        </td>
                        <td width="50%" style="text-align:right;vertical-align:top">
                            ' . $doctor_data['clinic_name'] . "<br>" . '
                            ' . clinic_address($address_data) . "<br>" . '
                            ' . $doctor_data['clinic_contact_number'] . ", " . '
                            ' . $doctor_data['clinic_email'] . "<br>" . '
                        </td>
                    </tr>
                </table>
            ');
            $patient_link_data = "";
            if(!empty($patient_link_enable) && $patient_link_enable['global_setting_value'] == "1"){
                $patient_link_data = '<tr>
                    <td align="center" colspan="3" width="100%" style="font-size:10px">
                        <b>Please Visit MedSign Patient: </b> <a target="_blank" href="'.DOMAIN_URL . 'patient">' . DOMAIN_URL . 'patient</a>
                    </td>
                    </tr>';
            }
            $mpdf->SetHTMLFooter('
                <table width="100%">
                    ' . $patient_link_data . '
                    <tr>
                        <td width="33%" style="font-size:10px">
                            Generated On: {DATE d/m/Y}
                        </td>
                        <td width="33%" style="font-size:10px" align="center">Page No. {PAGENO}/{nbpg}</td>
                        <td width="33%" align="right" style="font-size:10px">Powerd by Medsign</td>
                    </tr>
                </table>
            ');
        
            $mpdf->WriteHTML($view_html);
            $mpdf->Output($upload_path,'F');
            $upload_flag = uploadfilesS3($upload_path, $s3_upload_path);
            unlink($upload_path);
        }
        
        $get_patient_report_detail['pdf_path'] = $pdf_path;
        //$get_patient_report_detail['pdf_path'] = UPLOAD_ABS_PATH.''.PRESCRIPTION_PDF_FOLDER . "/" . $appointment_id."_prescription".".pdf";
        
        /*END - generate pdf file and upload on server and url response give*/

            if ($detail_found == 1) {
                //get followup date
                $this->my_response['status'] = true;
                $this->my_response['message'] = lang('common_detail_found');
                $this->my_response['data'] = $get_patient_report_detail;
                if ($key == 3) {
                    $followup_date = $this->User_model->get_followup_data($patient_detail_params);
                    if (!empty($followup_date)) {
                        $this->my_response['next_follow_up'] = $followup_date['follow_up_followup_date'];
                        $this->my_response['follow_up_instruction'] = $followup_date['follow_up_instruction'];
                    }
                }
            } else {
                $this->my_response['status'] = false;
                $this->my_response['message'] = lang('common_detail_not_found');
            }

            $this->send_response();
        } catch (ErrorException $ex) {
            $this->error = $ex->getMessage();
            $this->store_error();
        }
    }

    /**
     * Description :- This function is used to add the health analytics of the patient
     * 
     * 
     * 
     */
    public function add_health_analytics_post() {

        try {

            $clinic_id = !empty($this->Common_model->escape_data($this->post_data['clinic_id'])) ? trim($this->Common_model->escape_data($this->post_data['clinic_id'])) : '';
            $doctor_id = !empty($this->Common_model->escape_data($this->post_data['doctor_id'])) ? trim($this->Common_model->escape_data($this->post_data['doctor_id'])) : '';
            $patient_id = !empty($this->Common_model->escape_data($this->post_data['patient_id'])) ? trim($this->Common_model->escape_data($this->post_data['patient_id'])) : '';
            $appointment_id = !empty($this->Common_model->escape_data($this->post_data['appointment_id'])) ? trim($this->Common_model->escape_data($this->post_data['appointment_id'])) : '';
            $date = !empty($this->Common_model->escape_data($this->post_data['date'])) ? trim($this->Common_model->escape_data($this->post_data['date'])) : '';
            $user_type = !empty($this->Common_model->escape_data($this->post_data['user_type'])) ? trim($this->Common_model->escape_data($this->post_data['user_type'])) : '';
            $health_analytics_data = !empty($this->post_data['health_analytics_data']) ? $this->post_data['health_analytics_data'] : '';

            $health_analytics_test = !empty($this->post_data['health_analytics_test']) ? $this->post_data['health_analytics_test'] : '';

            if (empty($user_type) ||
                    empty($patient_id) ||
                    empty($date) ||
                    empty($health_analytics_data)
            ) {
                $this->bad_request();
            }


            if ($user_type == 2 && (
                    empty($clinic_id) ||
                    empty($doctor_id) ||
                    empty($appointment_id)
                    )
            ) {
                $this->bad_request();
            }

            if (validate_date_only($date) ||
                    !in_array($user_type, $this->user_type)
            ) {
                $this->my_response['status'] = false;
                $this->my_response['message'] = lang("mycontroller_invalid_request");
                $this->send_response();
            }


            if ($user_type == 2) {

                //check the user permission
                $get_role_details = $this->Common_model->get_the_role($this->user_id);
                if (!empty($get_role_details['user_role_data'])) {
                    $permission_data = array(
                        'role_data' => $get_role_details['user_role_data'],
                        'module' => 9,
                        'key' => 1
                    );
                    $check_module_permission = $this->check_module_permission($permission_data);
                    if ($check_module_permission == 2) {
                        $this->my_response['status'] = false;
                        $this->my_response['message'] = lang('permission_error');
                        $this->send_response();
                    }
                }

                //check data belongs to the doctor or not
                $requested_data = array(
                    'appointment_id' => $appointment_id,
                    'doctor_id' => $doctor_id,
                    'clinic_id' => $clinic_id,
                    'patient_id' => $patient_id
                );
                $this->check_data_belongs_doctor($requested_data);

                //check procedure already exists
                $data_exists_where = array(
                    'health_analytics_report_appointment_id' => $appointment_id,
                    'health_analytics_report_status' => 1
                );
                $this->check_data_exists(TBL_HEALTH_ANALYTICS_REPORT, 'health_analytics_report_id', $data_exists_where);
            }

            $health_analytics_test = json_decode($health_analytics_test, true);

            //store the health analytics id for the user
            if (!empty($health_analytics_test)) {

                //get store analytics id 
                $analytics_where = array(
                    'patient_analytics_user_id' => $patient_id,
                    'patient_analytics_doctor_id' => $doctor_id
                );
                $get_anlaytics_id = $this->Common_model->get_all_rows(TBL_PATIENT_ANALYTICS, 'patient_analytics_analytics_id', $analytics_where);


                if (!empty($get_anlaytics_id)) {

                    //delete the old enteries
                    $update_analytics_data = array(
                        'patient_analytics_status' => 9,
                        'patient_analytics_updated_at' => $this->utc_time_formated
                    );

                    $is_update = $this->Common_model->update(TBL_PATIENT_ANALYTICS, $update_analytics_data, $analytics_where);
                }
                $store_health_analytics = array();
                foreach ($health_analytics_test as $health_test) {
                    if (empty($health_test['doctor_id']) || $health_test['doctor_id'] == $doctor_id) {
                        $store_health_analytics[] = array(
                            'patient_analytics_user_id' => $patient_id,
                            'patient_analytics_name' => $health_test['name'],
                            'patient_analytics_name_precise' => $health_test['precise_name'],
                            'patient_analytics_analytics_id' => $health_test['id'],
                            'patient_analytics_doctor_id' => $doctor_id,
                            'patient_analytics_created_at' => $this->utc_time_formated
                        );
                    }
                }

                if (!empty($store_health_analytics)) {
                    $this->Common_model->insert_multiple(TBL_PATIENT_ANALYTICS, $store_health_analytics);
                }
            }

            $insert_health_analytics = array(
                'health_analytics_report_user_id' => $patient_id,
                'health_analytics_report_date' => $date,
                'health_analytics_report_data' => $health_analytics_data,
                'health_analytics_report_created_at' => $this->utc_time_formated,
                'health_analytics_report_share_status' => 1
            );

            if ($user_type == 2) {
                $insert_health_analytics['health_analytics_report_appointment_id'] = $appointment_id;
                $insert_health_analytics['health_analytics_report_doctor_user_id'] = $doctor_id;
                $insert_health_analytics['health_analytics_report_clinic_id'] = $clinic_id;
            } else {
                $insert_health_analytics['health_analytics_report_doctor_user_id'] = $this->user_id;
            }

            $inserted_id = $this->Common_model->insert(TBL_HEALTH_ANALYTICS_REPORT, $insert_health_analytics);

            if ($inserted_id > 0) {

                //get prescription detail
                $requested_data = array(
                    'health_analytics_report_id' => $inserted_id
                );
                $get_health_analytics_detail = $this->User_model->get_health_analytics_detail($requested_data);

                $this->my_response['status'] = true;
                $this->my_response['message'] = lang('analytics_added');
                $this->my_response['data'] = $get_health_analytics_detail;
            } else {
                $this->my_response['status'] = false;
                $this->my_response['message'] = lang('failure');
            }

            $this->send_response();
        } catch (ErrorException $ex) {
            $this->error = $ex->getMessage();
            $this->store_error();
        }
    }

    /**
     * Description :- This function is used to add the health analytics of the patient
     * 
     * 
     * 
     */
    public function edit_health_analytics_post() {
        try {
            $health_analytics_id = !empty($this->Common_model->escape_data($this->post_data['health_analytics_id'])) ? trim($this->Common_model->escape_data($this->post_data['health_analytics_id'])) : '';
            $clinic_id = !empty($this->Common_model->escape_data($this->post_data['clinic_id'])) ? trim($this->Common_model->escape_data($this->post_data['clinic_id'])) : '';
            $doctor_id = !empty($this->Common_model->escape_data($this->post_data['doctor_id'])) ? trim($this->Common_model->escape_data($this->post_data['doctor_id'])) : '';
            $patient_id = !empty($this->Common_model->escape_data($this->post_data['patient_id'])) ? trim($this->Common_model->escape_data($this->post_data['patient_id'])) : '';
            $appointment_id = !empty($this->Common_model->escape_data($this->post_data['appointment_id'])) ? trim($this->Common_model->escape_data($this->post_data['appointment_id'])) : '';
            $date = !empty($this->Common_model->escape_data($this->post_data['date'])) ? trim($this->Common_model->escape_data($this->post_data['date'])) : '';
            $user_type = !empty($this->Common_model->escape_data($this->post_data['user_type'])) ? trim($this->Common_model->escape_data($this->post_data['user_type'])) : '';
            $health_analytics_data = !empty($this->post_data['health_analytics_data']) ? $this->post_data['health_analytics_data'] : '';
			$health_analytics_test = !empty($this->post_data['health_analytics_test']) ? $this->post_data['health_analytics_test'] : '';

            if (empty($user_type) ||
                    empty($patient_id) ||
                    empty($date) ||
                    empty($health_analytics_data)
            ) {
                $this->bad_request();
            }


            if ($user_type == 2 && (
                    empty($clinic_id) ||
                    empty($doctor_id) ||
                    empty($appointment_id)
                    )
            ) {
                $this->bad_request();
            }

            if (validate_date_only($date) || !in_array($user_type, $this->user_type)){
                $this->my_response['status'] = false;
                $this->my_response['message'] = lang("mycontroller_invalid_request");
                $this->send_response();
            }

            if ($user_type == 2) {
               //check the user permission
                $get_role_details = $this->Common_model->get_the_role($this->user_id);
                if (!empty($get_role_details['user_role_data'])) {
                    $permission_data = array(
                        'role_data' => $get_role_details['user_role_data'],
                        'module' => 9,
                        'key' => 2
                    );
                    $check_module_permission = $this->check_module_permission($permission_data);
                    if ($check_module_permission == 2) {
                        $this->my_response['status'] = false;
                        $this->my_response['message'] = lang('permission_error');
                        $this->send_response();
                    }
                }

                //check data belongs to the doctor or not
                $requested_data = array(
                    'appointment_id' => $appointment_id,
                    'doctor_id' => $doctor_id,
                    'clinic_id' => $clinic_id,
                    'patient_id' => $patient_id
                );
                $this->check_data_belongs_doctor($requested_data);

                //check prescription data belongs to the doctor, clinic based or not
                $health_analytics_belongs_array = array(
                    'health_analytics_report_doctor_user_id' => $doctor_id,
                    'health_analytics_report_clinic_id' => $clinic_id,
                    'health_analytics_report_id' => $health_analytics_id,
                    'health_analytics_report_status !=' => 9
                );
                $is_valid_data = $this->Common_model->validate_data(TBL_HEALTH_ANALYTICS_REPORT, 'health_analytics_report_id', $health_analytics_belongs_array);

                if ($is_valid_data == 2) {
                    $this->my_response['status'] = false;
                    $this->my_response['message'] = lang("mycontroller_invalid_request");
                    $this->send_response();
                }
            }
			
            $health_analytics_test = json_decode($health_analytics_test, true);
            //store the health analytics id for the user
            if (!empty($health_analytics_test)) {
                //get store analytics id 
                $analytics_where = array(
                    'patient_analytics_user_id' => $patient_id,
                    'patient_analytics_doctor_id' => $doctor_id
                );
                $get_anlaytics_id = $this->Common_model->get_all_rows(TBL_PATIENT_ANALYTICS, 'patient_analytics_analytics_id', $analytics_where);
                if (!empty($get_anlaytics_id)) {
                    //delete the old enteries
                    $update_analytics_data = array(
                        'patient_analytics_status' => 9,
                        'patient_analytics_updated_at' => $this->utc_time_formated
                    );

                    $is_update = $this->Common_model->update(TBL_PATIENT_ANALYTICS, $update_analytics_data, $analytics_where);
                }
            }
            $store_health_analytics = array();
            foreach ($health_analytics_test as $health_test) {
                if (empty($health_test['doctor_id']) || $health_test['doctor_id'] == $doctor_id) {
                    $store_health_analytics[] = array(
                        'patient_analytics_user_id' => $patient_id,
                        'patient_analytics_name' => $health_test['name'],
                        'patient_analytics_name_precise' => $health_test['precise_name'],
                        'patient_analytics_analytics_id' => $health_test['id'],
                        'patient_analytics_doctor_id' => $doctor_id,
                        'patient_analytics_created_at' => $this->utc_time_formated
                    );
                }
            }
            if (!empty($store_health_analytics)) {
                $this->Common_model->insert_multiple(TBL_PATIENT_ANALYTICS, $store_health_analytics);
            }
            $update_health_analytics_data = array(
                'health_analytics_report_date'         => $date,
                'health_analytics_report_data' 		   => $health_analytics_data,
                'health_analytics_report_updated_at'   => $this->utc_time_formated,
                'health_analytics_report_share_status' => 1
            );
            $update_health_analytics_where = array(
                'health_analytics_report_id' => $health_analytics_id
            );
			$is_update = $this->Common_model->update(TBL_HEALTH_ANALYTICS_REPORT, $update_health_analytics_data, $update_health_analytics_where);
            if ($is_update > 0) {
                //get prescription detail
                $requested_data = array(
                    'health_analytics_report_id' => $health_analytics_id
                );
                $get_health_analytics_detail = $this->User_model->get_health_analytics_detail($requested_data);
                $this->my_response['status'] = true;
                $this->my_response['message'] = lang('analytics_updated');
                $this->my_response['data'] = $get_health_analytics_detail;
            } else {
                $this->my_response['status'] = false;
                $this->my_response['message'] = lang('failure');
            }
            $this->send_response();
        } catch (ErrorException $ex) {
            $this->error = $ex->getMessage();
            $this->store_error();
        }
    }

    public function get_patient_health_analytics_report_post() {

        try {

            $patient_id = !empty($this->Common_model->escape_data($this->post_data['patient_id'])) ? trim($this->Common_model->escape_data($this->post_data['patient_id'])) : '';
            $user_type = !empty($this->Common_model->escape_data($this->post_data['user_type'])) ? trim($this->Common_model->escape_data($this->post_data['user_type'])) : '';

            $clinic_id = !empty($this->Common_model->escape_data($this->post_data['clinic_id'])) ? trim($this->Common_model->escape_data($this->post_data['clinic_id'])) : '';
            $doctor_id = !empty($this->Common_model->escape_data($this->post_data['doctor_id'])) ? trim($this->Common_model->escape_data($this->post_data['doctor_id'])) : 1;
            $flag = !empty($this->Common_model->escape_data($this->post_data['flag'])) ? trim($this->Common_model->escape_data($this->post_data['flag'])) : 1;

            $page = !empty($this->post_data['page']) ? trim($this->Common_model->escape_data($this->post_data['page'])) : "1";
            $per_page = !empty($this->post_data['per_page']) ? trim($this->Common_model->escape_data($this->post_data['per_page'])) : "10";

            if (empty($patient_id) ||
                    empty($user_type)
            ) {
                $this->bad_request();
            }

            if ($user_type == 2) {

                if (empty($flag) ||
                        empty($clinic_id) ||
                        empty($doctor_id)
                ) {
                    $this->bad_request();
                }
            }

            $health_data = array(
                'user_id' => $patient_id,
                "page" => $page,
                "per_page" => $per_page,
                'user_type' => $user_type
            );

            if ($user_type == 2) {

                //check data belongs to the doctor or not
                $requested_data = array(
                    'doctor_id' => $doctor_id,
                    'clinic_id' => $clinic_id,
                    'patient_id' => $patient_id
                );
                $this->check_data_belongs_doctor($requested_data, 2);

                $health_data['flag'] = $flag;
                $health_data['logged_in'] = $this->user_id;

                $get_health_analytics_data = $this->User_model->get_health_analytics_detail($health_data);
            } else {
                $health_data['flag'] = '';
                $health_data['logged_in'] = $this->user_id;
                $get_health_analytics_data = $this->User_model->get_health_analytics_detail($health_data);
            }
            /*start*/
            foreach($get_health_analytics_data['data'] as $key => $value)
            {
            if($value['user_type'] == 2){
                $dr_prefix = DOCTOR;
            }else{
                $dr_prefix = '';
                //$get_health_analytics_data[$key]['patient_prescription_doctor_name'] =  DOCTOR .' '. $value['patient_prescription_doctor_name'];
            }
            
              $get_health_analytics_data['data'][$key]['user_name'] =  $dr_prefix .' '. $value['user_name'];
            }
            /*end*/
            if (!empty($get_health_analytics_data['data'])) {
                $this->my_response['status'] = true;
                $this->my_response['message'] = lang('common_detail_found');
                $this->my_response['data'] = $get_health_analytics_data['data'];
                $this->my_response['total_count'] = $get_health_analytics_data['no_of_records'];
                $this->my_response['per_page'] = $per_page;
                $this->my_response['current_page'] = $page;
            } else {
                $this->my_response['status'] = false;
                $this->my_response['message'] = lang('common_detail_not_found');
            }
            $this->send_response();
        } catch (ErrorException $ex) {
            $this->error = $ex->getMessage();
            $this->store_error();
        }
    }

    public function get_patient_health_analytics_post() {

        try {

            $patient_id = !empty($this->Common_model->escape_data($this->post_data['patient_id'])) ? trim($this->Common_model->escape_data($this->post_data['patient_id'])) : '';
            $user_type = !empty($this->Common_model->escape_data($this->post_data['user_type'])) ? trim($this->Common_model->escape_data($this->post_data['user_type'])) : '';

            if (empty($patient_id)) {
                $this->bad_request();
            }

            $where = array(
                'patient_analytics_user_id' => $patient_id
            );

            if ($user_type == 1) {
                $where['patient_analytics_status'] = 1;
            }

            $columns = 'patient_analytics_analytics_id, 
                        patient_analytics_doctor_id,
                        patient_analytics_name,
                        patient_analytics_name_precise,
						health_analytics_test_validation';

            $get_health_analytics_data = $this->Common_model->get_all_rows(TBL_PATIENT_ANALYTICS, $columns, $where, array(TBL_HEALTH_ANALYTICS=>TBL_PATIENT_ANALYTICS.".patient_analytics_analytics_id=".TBL_HEALTH_ANALYTICS.".health_analytics_test_id"), array(), '', 'left', array(), 'patient_analytics_name');
			
            if (!empty($get_health_analytics_data)) {
                $this->my_response['status'] = true;
                $this->my_response['message'] = lang('common_detail_found');
                $this->my_response['data'] = $get_health_analytics_data;
            } else {
                $this->my_response['status'] = false;
				//$this->my_response['data']['parent_id'] = '';
                $this->my_response['message'] = lang('common_detail_not_found');
            }

            $this->send_response();
        } catch (ErrorException $ex) {
            $this->error = $ex->getMessage();
            $this->store_error();
        }
    }

    public function add_followup_data_post() {

        try {

            $clinic_id = !empty($this->Common_model->escape_data($this->post_data['clinic_id'])) ? trim($this->Common_model->escape_data($this->post_data['clinic_id'])) : '';
            $doctor_id = !empty($this->Common_model->escape_data($this->post_data['doctor_id'])) ? trim($this->Common_model->escape_data($this->post_data['doctor_id'])) : '';
            $patient_id = !empty($this->Common_model->escape_data($this->post_data['patient_id'])) ? trim($this->Common_model->escape_data($this->post_data['patient_id'])) : '';
            $old_appointment_id = !empty($this->Common_model->escape_data($this->post_data['old_appointment_id'])) ? trim($this->Common_model->escape_data($this->post_data['old_appointment_id'])) : '';
            $new_appointment_id = !empty($this->Common_model->escape_data($this->post_data['new_appointment_id'])) ? trim($this->Common_model->escape_data($this->post_data['new_appointment_id'])) : '';
            $old_date = !empty($this->Common_model->escape_data($this->post_data['old_date'])) ? trim($this->Common_model->escape_data($this->post_data['old_date'])) : '';
            $new_date = !empty($this->Common_model->escape_data($this->post_data['new_date'])) ? trim($this->Common_model->escape_data($this->post_data['new_date'])) : '';
            $user_type = !empty($this->Common_model->escape_data($this->post_data['user_type'])) ? trim($this->Common_model->escape_data($this->post_data['user_type'])) : '';

            if (
                    empty($patient_id) ||
                    empty($old_appointment_id) ||
                    empty($new_appointment_id)
            ) {
                $this->bad_request();
            }

            if ($old_appointment_id == $new_appointment_id) {
                $this->bad_request();
            }

            //check data belongs to the doctor or not
            $requested_data = array(
                'appointment_id' => $new_appointment_id,
                'doctor_id' => $doctor_id,
                'clinic_id' => $clinic_id,
                'patient_id' => $patient_id
            );
            $this->check_data_belongs_doctor($requested_data);

            $patient_detail_params = array(
                'appointment_id' => $old_appointment_id,
                'clinic_id' => $clinic_id,
                'doctor_id' => $doctor_id,
                'date' => $old_date,
                'patient_id' => $patient_id,
                'user_type' => $user_type
            );

            $patient_detail_params['key'] = 1;
            $vital = $this->User_model->get_patient_report_detail($patient_detail_params);

            $patient_detail_params['key'] = 2;
            $clinical_notes = $this->User_model->get_patient_report_detail($patient_detail_params);

            $patient_detail_params['key'] = 3;
            $prescription = $this->User_model->get_patient_report_detail($patient_detail_params);

            $patient_detail_params['key'] = 4;
            $investigation = $this->User_model->get_patient_report_detail($patient_detail_params);

            /*
              $patient_detail_params['key'] = 5;
              $procedure = $this->User_model->get_patient_report_detail($patient_detail_params);


              $patient_detail_params['key'] = 7;
              $health_analytics = $this->User_model->get_patient_report_detail($patient_detail_params);
             */

            //get the share settings for the investigation
            $setting_where = array(
                'setting_type' => 1,
                'setting_user_id' => $doctor_id,
                'setting_clinic_id' => $clinic_id
            );
            $get_setting = $this->Common_model->get_setting($setting_where);

            $investigation_report_share_status = 2;
            $clinic_report_share_status = 2;
            $prescription_report_share_status = 2;
            $vital_share_status = 2;
            $procedure_report_share_status = 2;

            if (!empty($get_setting)) {
                $setting_array = json_decode($get_setting['setting_data'], true);
                if (!empty($setting_array) && is_array($setting_array)) {
                    foreach ($setting_array as $setting) {

                        if ($setting['id'] == 1) {
                            $vital_share_status = $setting['status'];
                        }
                        if ($setting['id'] == 2) {
                            $clinic_report_share_status = $setting['status'];
                        }
                        if ($setting['id'] == 3) {
                            $prescription_report_share_status = $setting['status'];
                        }
                        if ($setting['id'] == 4) {
                            $investigation_report_share_status = $setting['status'];
                        }
                        if ($setting['id'] == 5) {
                            $procedure_report_share_status = $setting['status'];
                        }
                    }
                }
            }


            //check if already data exist in all the reports for the same appointment id then delete it first
            $this->db->trans_start();

            $vital_where_data = array(
                'vital_report_appointment_id' => $new_appointment_id
            );
            $vital_update_data = array(
                'vital_report_updated_at' => $this->utc_time_formated,
                'vital_report_status' => 9
            );
            $this->Common_model->update(TBL_VITAL_REPORTS, $vital_update_data, $vital_where_data);

            $clinical_where_data = array(
                'clinical_notes_reports_appointment_id' => $new_appointment_id
            );
            $clinical_update_data = array(
                'clinical_notes_reports_updated_at' => $this->utc_time_formated,
                'clinical_notes_reports_status' => 9
            );
            $this->Common_model->update(TBL_CLINICAL_NOTES_REPORT, $clinical_update_data, $clinical_where_data);

            $prescription_where_data = array(
                'prescription_appointment_id' => $new_appointment_id,
            );
            $prescription_update_data = array(
                'prescription_updated_at' => $this->utc_time_formated,
                'prescription_status' => 9
            );
            $this->Common_model->update(TBL_PRESCRIPTION_REPORTS, $prescription_update_data, $prescription_where_data);

            $investigation_where_data = array(
                'lab_report_appointment_id' => $new_appointment_id
            );

            $investigation_update_data = array(
                'lab_report_updated_at' => $this->utc_time_formated,
                'lab_report_status' => 9
            );

            $this->Common_model->update(TBL_LAB_REPORTS, $investigation_update_data, $investigation_where_data);

            /*

              $procedure_where_data = array(
              'procedure_report_appointment_id' => $new_appointment_id
              );
              $procedure_update_data = array(
              'procedure_report_updated_at' => $this->utc_time_formated,
              'procedure_report_status' => 9
              );

              $this->Common_model->update(TBL_PROCEDURE_REPORTS, $procedure_update_data, $procedure_where_data);


              $file_where_data = array(
              'file_report_appointment_id' => $new_appointment_id
              );

              $file_update_data = array(
              'file_report_updated_at' => $this->utc_time_formated,
              'file_report_status' => 9
              );

              $this->Common_model->update(TBL_FILE_REPORTS, $file_update_data, $file_where_data);

              $health_where_data = array(
              'health_analytics_report_appointment_id' => $new_appointment_id
              );
              $health_update_data = array(
              'health_analytics_report_status' => 9,
              'health_analytics_report_updated_at' => $this->utc_time_formated
              );
              $this->Common_model->update(TBL_HEALTH_ANALYTICS_REPORT, $health_update_data, $health_where_data);
             */
            $is_data_contains = 2;

            if (!empty($vital)) {
                $is_data_contains = 1;

                $vital_data = array(
                    'vital_report_user_id' => $vital['vital_report_user_id'],
                    'vital_report_doctor_id' => $vital['vital_report_doctor_id'],
                    'vital_report_appointment_id' => $new_appointment_id,
                    'vital_report_clinic_id' => $vital['vital_report_clinic_id'],
                    'vital_report_date' => $new_date,
                    'vital_report_spo2' => $vital['vital_report_spo2'],
                    'vital_report_weight' => $vital['vital_report_weight'],
                    'vital_report_bloodpressure_systolic' => $vital['vital_report_bloodpressure_systolic'],
                    'vital_report_bloodpressure_diastolic' => $vital['vital_report_bloodpressure_diastolic'],
                    'vital_report_bloodpressure_type' => $vital['vital_report_bloodpressure_type'],
                    'vital_report_pulse' => $vital['vital_report_pulse'],
                    'vital_report_temperature' => $vital['vital_report_temperature'],
                    'vital_report_temperature_type' => $vital['vital_report_temperature_type'],
                    'vital_report_temperature_type' => $vital['vital_report_temperature_type'],
                    'vital_report_resp_rate' => $vital['vital_report_resp_rate'],
                    'vital_report_created_at' => $this->utc_time_formated,
                    'vital_report_share_status' => $vital_share_status
                );
                $this->Common_model->insert(TBL_VITAL_REPORTS, $vital_data);
            }

            if (!empty($clinical_notes)) {

                $is_data_contains = 1;
                $clinical_notes_data = array(
                    'clinical_notes_reports_user_id' => $clinical_notes['clinical_notes_reports_user_id'],
                    'clinical_notes_reports_doctor_user_id' => $clinical_notes['clinical_notes_reports_doctor_user_id'],
                    'clinical_notes_reports_appointment_id' => $new_appointment_id,
                    'clinical_notes_reports_clinic_id' => $clinical_notes['clinical_notes_reports_clinic_id'],
                    'clinical_notes_reports_date' => $new_date,
                    'clinical_notes_reports_kco' => "[]",
                    'clinical_notes_reports_complaints' => $clinical_notes['clinical_notes_reports_complaints'],
                    'clinical_notes_reports_observation' => $clinical_notes['clinical_notes_reports_observation'],
                    'clinical_notes_reports_diagnoses' => $clinical_notes['clinical_notes_reports_diagnoses'],
                    'clinical_notes_reports_add_notes' => $clinical_notes['clinical_notes_reports_add_notes'],
                    'clinical_notes_reports_share_status' => $clinic_report_share_status,
                    'clinical_notes_reports_created_at' => $this->utc_time_formated
                );
                $clinical_note_inserted = $this->Common_model->insert(TBL_CLINICAL_NOTES_REPORT, $clinical_notes_data);

                if ($clinical_note_inserted > 0) {

                    if (!empty($clinical_notes['images'])) {

                        $requested_data = array(
                            'inserted_id' => $clinical_note_inserted,
                            'existing_id' => $clinical_notes['clinical_notes_reports_id']
                        );

                        $cron_job_path = CRON_PATH . " cron/copy_clinical_report_images/" . base64_encode(json_encode($requested_data));
                        exec(PHP_PATH . " " . $cron_job_path . " > /dev/null &");
                    }
                }
            }

            if (!empty($investigation)) {

                $is_data_contains = 1;
                //insert investigation data
                $insert_investigaion = array(
                    'lab_report_user_id' => $investigation['lab_report_user_id'],
                    'lab_report_doctor_user_id' => $investigation['lab_report_doctor_user_id'],
                    'lab_report_appointment_id' => $new_appointment_id,
                    'lab_report_clinic_id' => $investigation['lab_report_clinic_id'],
                    'lab_report_date' => $new_date,
                    'lab_report_test_name' => $investigation['lab_report_test_name'],
                    'lab_report_instruction' => $investigation['lab_report_instruction'],
                    'lab_report_share_status' => $investigation_report_share_status,
                    'lab_report_created_at' => $this->utc_time_formated
                );
                $this->Common_model->insert(TBL_LAB_REPORTS, $insert_investigaion);
            }

            $add_prescription = array();
            if (!empty($prescription)) {

                $is_data_contains = 1;
                foreach ($prescription as $single_prescription) {
                    $add_prescription[] = array(
                        'prescription_user_id' => $single_prescription['prescription_user_id'],
                        'prescription_doctor_user_id' => $single_prescription['prescription_doctor_user_id'],
                        'prescription_appointment_id' => $new_appointment_id,
                        'prescription_drug_id' => $single_prescription['prescription_drug_id'],
                        'prescription_drug_name' => $single_prescription['prescription_drug_name'],
                        'prescription_clinic_id' => $single_prescription['prescription_clinic_id'],
                        'prescription_date' => $new_date,
                        'prescription_generic_id' => $single_prescription['prescription_generic_id'],
                        'prescription_unit_id' => $single_prescription['prescription_unit_id'],
                        'prescription_unit_value' => $single_prescription['prescription_unit_value'],
                        'prescription_dosage' => $single_prescription['prescription_dosage'],
                        'prescription_frequency_id' => $single_prescription['prescription_frequency_id'],
                        'prescription_frequency_value' => $single_prescription['prescription_frequency_value'],
                        'prescription_frequency_instruction' => $single_prescription['prescription_frequency_instruction'],
                        'prescription_intake' => $single_prescription['prescription_intake'],
                        'prescription_intake_instruction' => $single_prescription['prescription_intake_instruction'],
                        'prescription_duration' => $single_prescription['prescription_duration'],
                        'prescription_duration_value' => $single_prescription['prescription_duration_value'],
                        'prescription_diet_instruction' => $single_prescription['prescription_diet_instruction'],
                        'prescription_share_status' => $prescription_report_share_status,
                        'prescription_created_at' => $this->utc_time_formated
                    );
                    $this->Common_model->insert_multiple(TBL_PRESCRIPTION_REPORTS, $add_prescription);
                }
            }

            /*
              if (!empty($procedure)) {
              $is_data_contains = 1;

              $procedure_data = array(
              'procedure_report_user_id' => $procedure['procedure_report_user_id'],
              'procedure_report_doctor_user_id' => $procedure['procedure_report_doctor_user_id'],
              'procedure_report_appointment_id' => $new_appointment_id,
              'procedure_report_clinic_id' => $procedure['procedure_report_clinic_id'],
              'procedure_report_date' => $new_date,
              'procedure_report_procedure_text' => $procedure['procedure_report_procedure_text'],
              'procedure_report_note' => $procedure['procedure_report_note'],
              'procedure_report_created_at' => $this->utc_time_formated,
              'procedure_report_share_status' => $procedure_report_share_status
              );

              $this->Common_model->insert(TBL_PROCEDURE_REPORTS, $procedure_data);
              }

              if (!empty($health_analytics)) {
              if (isset($health_analytics['health_analytics_report_user_id'])) {
              $health_analytics_data = array(
              'health_analytics_report_user_id' => $health_analytics['health_analytics_report_user_id'],
              'health_analytics_report_doctor_user_id' => $health_analytics['health_analytics_report_doctor_user_id'],
              'health_analytics_report_appointment_id' => $new_appointment_id,
              'health_analytics_report_clinic_id' => $health_analytics['health_analytics_report_clinic_id'],
              'health_analytics_report_date' => $new_date,
              'health_analytics_report_data' => $health_analytics['health_analytics_report_data'],
              'health_analytics_report_created_at' => $this->utc_time_formated
              );

              $this->Common_model->insert(TBL_HEALTH_ANALYTICS_REPORT, $health_analytics_data);
              }
              }
             */
            if ($is_data_contains == 2) {
                $this->my_response['status'] = true;
                $this->my_response['message'] = lang('no_data_contain');
            } else {
                if ($this->db->trans_status() !== FALSE) {
                    $this->db->trans_commit();
                    $this->my_response['status'] = true;
                    $this->my_response['message'] = lang('common_data_added');
                } else {
                    $this->db->trans_rollback();
                    $this->my_response['status'] = false;
                    $this->my_response['message'] = lang('failure');
                }
            }

            $this->send_response();
        } catch (ErrorException $ex) {
            $this->error = $ex->getMessage();
            $this->store_error();
        }
    }

    public function change_template_post() {
        try {

            $clinic_id = !empty($this->Common_model->escape_data($this->post_data['clinic_id'])) ? trim($this->Common_model->escape_data($this->post_data['clinic_id'])) : '';
            $doctor_id = !empty($this->Common_model->escape_data($this->post_data['doctor_id'])) ? trim($this->Common_model->escape_data($this->post_data['doctor_id'])) : '';
            $patient_id = !empty($this->Common_model->escape_data($this->post_data['patient_id'])) ? trim($this->Common_model->escape_data($this->post_data['patient_id'])) : '';
            $appointment_id = !empty($this->Common_model->escape_data($this->post_data['appointment_id'])) ? trim($this->Common_model->escape_data($this->post_data['appointment_id'])) : '';

            if (empty($patient_id) ||
                    empty($clinic_id) ||
                    empty($doctor_id) ||
                    empty($appointment_id)
            ) {
                $this->bad_request();
            }


            //check data belongs to the doctor or not
            $requested_data = array(
                'appointment_id' => $appointment_id,
                'doctor_id' => $doctor_id,
                'clinic_id' => $clinic_id,
                'patient_id' => $patient_id
            );
            $this->check_data_belongs_doctor($requested_data);

            $this->db->trans_start();

            $clinical_where_data = array(
                'clinical_notes_reports_appointment_id' => $appointment_id
            );
            $clinical_update_data = array(
                'clinical_notes_reports_updated_at' => $this->utc_time_formated,
                'clinical_notes_reports_status' => 9
            );
            $this->Common_model->update(TBL_CLINICAL_NOTES_REPORT, $clinical_update_data, $clinical_where_data);

            $get_clinical_report_id = $this->Common_model->get_single_row(TBL_CLINICAL_NOTES_REPORT, 'clinical_notes_reports_id', $clinical_where_data);

            if (!empty($get_clinical_report_id)) {

                //update the image status
                $clinic_report_image_update = array(
                    'clinic_notes_reports_images_status' => 9,
                    'clinic_notes_reports_images_updated_at' => $this->utc_time_formated
                );

                $clinic_report_image_where = array(
                    'clinic_notes_reports_images_reports_id' => $get_clinical_report_id['clinical_notes_reports_id']
                );

                $this->Common_model->update(TBL_CLINICAL_NOTES_REPORT_IMAGE, $clinic_report_image_update, $clinic_report_image_where);
            }

            $prescription_where_data = array(
                'prescription_appointment_id' => $appointment_id,
            );
            $prescription_update_data = array(
                'prescription_updated_at' => $this->utc_time_formated,
                'prescription_status' => 9
            );
            $this->Common_model->update(TBL_PRESCRIPTION_REPORTS, $prescription_update_data, $prescription_where_data);

            $investigation_where_data = array(
                'lab_report_appointment_id' => $appointment_id
            );

            $investigation_update_data = array(
                'lab_report_updated_at' => $this->utc_time_formated,
                'lab_report_status' => 9
            );
            $this->Common_model->update(TBL_LAB_REPORTS, $investigation_update_data, $investigation_where_data);

            if ($this->db->trans_status() !== FALSE) {
                $this->db->trans_commit();
                $this->my_response['status'] = true;
                $this->my_response['message'] = true;
            } else {
                $this->db->trans_rollback();
                $this->my_response['status'] = false;
                $this->my_response['message'] = lang('failure');
            }

            $this->send_response();
        } catch (ErrorException $ex) {
            $this->error = $ex->getMessage();
            $this->store_error();
        }
    }

    public function get_patient_clinical_notes_report_post() {

        try {

            $patient_id = !empty($this->Common_model->escape_data($this->post_data['patient_id'])) ? trim($this->Common_model->escape_data($this->post_data['patient_id'])) : '';
            $user_type = !empty($this->Common_model->escape_data($this->post_data['user_type'])) ? trim($this->Common_model->escape_data($this->post_data['user_type'])) : '';

            $clinical_report_id = !empty($this->Common_model->escape_data($this->post_data['clinical_report_id'])) ? trim($this->Common_model->escape_data($this->post_data['clinical_report_id'])) : '';

            $clinic_id = !empty($this->Common_model->escape_data($this->post_data['clinic_id'])) ? trim($this->Common_model->escape_data($this->post_data['clinic_id'])) : '';
            $doctor_id = !empty($this->Common_model->escape_data($this->post_data['doctor_id'])) ? trim($this->Common_model->escape_data($this->post_data['doctor_id'])) : 1;
            $flag = !empty($this->Common_model->escape_data($this->post_data['flag'])) ? trim($this->Common_model->escape_data($this->post_data['flag'])) : 1;

            $page = !empty($this->post_data['page']) ? trim($this->Common_model->escape_data($this->post_data['page'])) : "1";
            $per_page = !empty($this->post_data['per_page']) ? trim($this->Common_model->escape_data($this->post_data['per_page'])) : "10";

            if (empty($patient_id) ||
                    empty($user_type)
            ) {
                $this->bad_request();
            }

            if ($user_type == 2) {

                if (empty($flag) ||
                        empty($clinic_id) ||
                        empty($doctor_id)
                ) {
                    $this->bad_request();
                }
            }

            $clinical_data = array(
                'user_id' => $patient_id,
                'page' => $page,
                'per_page' => $per_page,
                'user_type' => $user_type,
                'clinical_report_id' => $clinical_report_id
            );

            if ($user_type == 2) {

                //check data belongs to the doctor or not
                $requested_data = array(
                    'doctor_id' => $doctor_id,
                    'clinic_id' => $clinic_id,
                    'patient_id' => $patient_id
                );
                $this->check_data_belongs_doctor($requested_data, 2);

                $clinical_data['flag'] = $flag;
                $clinical_data['logged_in'] = $this->user_id;

                $get_clinic_notes_report_data = $this->User_model->get_patient_clinical_notes_report($clinical_data);
            } else {
                $clinical_data['flag'] = '';
                $clinical_data['logged_in'] = $this->user_id;
                $get_clinic_notes_report_data = $this->User_model->get_patient_clinical_notes_report($clinical_data);
            }


            if (!empty($get_clinic_notes_report_data['data'])) {
                $this->my_response['status'] = true;
                $this->my_response['message'] = lang('common_detail_found');
                $this->my_response['data'] = $get_clinic_notes_report_data['data'];
                $this->my_response['total_count'] = (string) $get_clinic_notes_report_data['total_records'];
                $this->my_response['per_page'] = $per_page;
                $this->my_response['current_page'] = $page;
            } else {
                $this->my_response['status'] = true;
                $this->my_response['message'] = lang('common_detail_not_found');
                $this->my_response['data'] = array();
            }
            $this->send_response();
        } catch (ErrorException $ex) {
            $this->error = $ex->getMessage();
            $this->store_error();
        }
    }

    public function get_patient_procedure_report_post() {

        try {

            $patient_id = !empty($this->Common_model->escape_data($this->post_data['patient_id'])) ? trim($this->Common_model->escape_data($this->post_data['patient_id'])) : '';
            $user_type = !empty($this->Common_model->escape_data($this->post_data['user_type'])) ? trim($this->Common_model->escape_data($this->post_data['user_type'])) : '';

            $procedure_report_id = !empty($this->Common_model->escape_data($this->post_data['procedure_report_id'])) ? trim($this->Common_model->escape_data($this->post_data['procedure_report_id'])) : '';

            $clinic_id = !empty($this->Common_model->escape_data($this->post_data['clinic_id'])) ? trim($this->Common_model->escape_data($this->post_data['clinic_id'])) : '';
            $doctor_id = !empty($this->Common_model->escape_data($this->post_data['doctor_id'])) ? trim($this->Common_model->escape_data($this->post_data['doctor_id'])) : 1;
            $flag = !empty($this->Common_model->escape_data($this->post_data['flag'])) ? trim($this->Common_model->escape_data($this->post_data['flag'])) : 1;

            $page = !empty($this->post_data['page']) ? trim($this->Common_model->escape_data($this->post_data['page'])) : "1";
            $per_page = !empty($this->post_data['per_page']) ? trim($this->Common_model->escape_data($this->post_data['per_page'])) : "10";

            if (empty($patient_id) ||
                    empty($user_type)
            ) {
                $this->bad_request();
            }

            if ($user_type == 2) {

                if (empty($flag) ||
                        empty($clinic_id) ||
                        empty($doctor_id)
                ) {
                    $this->bad_request();
                }
            }

            $procedure_data = array(
                'user_id' => $patient_id,
                'page' => $page,
                'per_page' => $per_page,
                'user_type' => $user_type,
                'procedure_report_id' => $procedure_report_id
            );

            if ($user_type == 2) {

                //check data belongs to the doctor or not
                $requested_data = array(
                    'doctor_id' => $doctor_id,
                    'clinic_id' => $clinic_id,
                    'patient_id' => $patient_id
                );
                $this->check_data_belongs_doctor($requested_data, 2);

                $procedure_data['flag'] = $flag;
                $procedure_data['logged_in'] = $this->user_id;

                $get_procedure_report_data = $this->User_model->get_patient_procedure_report($procedure_data);
            } else {
                $procedure_data['flag'] = '';
                $procedure_data['logged_in'] = $this->user_id;
                $get_procedure_report_data = $this->User_model->get_patient_procedure_report($procedure_data);
            }


            if (!empty($get_procedure_report_data['data'])) {
                $this->my_response['status'] = true;
                $this->my_response['message'] = lang('common_detail_found');
                $this->my_response['data'] = $get_procedure_report_data['data'];
                $this->my_response['total_count'] = (string) $get_procedure_report_data['total_records'];
                $this->my_response['per_page'] = $per_page;
                $this->my_response['current_page'] = $page;
            } else {
                $this->my_response['status'] = true;
                $this->my_response['message'] = lang('common_detail_not_found');
                $this->my_response['data'] = array();
            }
            $this->send_response();
        } catch (ErrorException $ex) {
            $this->error = $ex->getMessage();
            $this->store_error();
        }
    }

    public function get_patient_investigation_report_post() {

        try {

            $patient_id = !empty($this->Common_model->escape_data($this->post_data['patient_id'])) ? trim($this->Common_model->escape_data($this->post_data['patient_id'])) : '';
            $user_type = !empty($this->Common_model->escape_data($this->post_data['user_type'])) ? trim($this->Common_model->escape_data($this->post_data['user_type'])) : '';

            $lab_report_id = !empty($this->Common_model->escape_data($this->post_data['lab_report_id'])) ? trim($this->Common_model->escape_data($this->post_data['lab_report_id'])) : '';

            $clinic_id = !empty($this->Common_model->escape_data($this->post_data['clinic_id'])) ? trim($this->Common_model->escape_data($this->post_data['clinic_id'])) : '';
            $doctor_id = !empty($this->Common_model->escape_data($this->post_data['doctor_id'])) ? trim($this->Common_model->escape_data($this->post_data['doctor_id'])) : 1;
            $flag = !empty($this->Common_model->escape_data($this->post_data['flag'])) ? trim($this->Common_model->escape_data($this->post_data['flag'])) : 1;

            $page = !empty($this->post_data['page']) ? trim($this->Common_model->escape_data($this->post_data['page'])) : "1";
            $per_page = !empty($this->post_data['per_page']) ? trim($this->Common_model->escape_data($this->post_data['per_page'])) : "10";

            if (empty($patient_id) ||
                    empty($user_type)
            ) {
                $this->bad_request();
            }

            if ($user_type == 2) {

                if (empty($flag) ||
                        empty($clinic_id) ||
                        empty($doctor_id)
                ) {
                    $this->bad_request();
                }
            }

            $lab_report_data = array(
                'user_id' => $patient_id,
                'page' => $page,
                'per_page' => $per_page,
                'user_type' => $user_type,
                'lab_report_id' => $lab_report_id
            );

            if ($user_type == 2) {

                //check data belongs to the doctor or not
                $requested_data = array(
                    'doctor_id' => $doctor_id,
                    'clinic_id' => $clinic_id,
                    'patient_id' => $patient_id
                );
                $this->check_data_belongs_doctor($requested_data, 2);

                $lab_report_data['flag'] = $flag;
                $lab_report_data['logged_in'] = $this->user_id;

                $get_lab_report_data = $this->User_model->get_patient_lab_report($lab_report_data);
            } else {
                $lab_report_data['flag'] = '';
                $lab_report_data['logged_in'] = $this->user_id;
                $get_lab_report_data = $this->User_model->get_patient_lab_report($lab_report_data);
            }


            if (!empty($get_lab_report_data['data'])) {
                $this->my_response['status'] = true;
                $this->my_response['message'] = lang('common_detail_found');
                $this->my_response['data'] = $get_lab_report_data['data'];
                $this->my_response['total_count'] = (string) $get_lab_report_data['total_records'];
                $this->my_response['per_page'] = $per_page;
                $this->my_response['current_page'] = $page;
            } else {
                $this->my_response['status'] = true;
                $this->my_response['message'] = lang('common_detail_not_found');
                $this->my_response['data'] = array();
            }
            $this->send_response();
        } catch (ErrorException $ex) {
            $this->error = $ex->getMessage();
            $this->store_error();
        }
    }

    public function get_patient_prescription_post() {

        try {

            $patient_id = !empty($this->Common_model->escape_data($this->post_data['patient_id'])) ? trim($this->Common_model->escape_data($this->post_data['patient_id'])) : '';
            $user_type = !empty($this->Common_model->escape_data($this->post_data['user_type'])) ? trim($this->Common_model->escape_data($this->post_data['user_type'])) : '';

            $clinic_id = !empty($this->Common_model->escape_data($this->post_data['clinic_id'])) ? trim($this->Common_model->escape_data($this->post_data['clinic_id'])) : '';
            $doctor_id = !empty($this->Common_model->escape_data($this->post_data['doctor_id'])) ? trim($this->Common_model->escape_data($this->post_data['doctor_id'])) : 1;
            $flag = !empty($this->Common_model->escape_data($this->post_data['flag'])) ? trim($this->Common_model->escape_data($this->post_data['flag'])) : 1;
            $date = !empty($this->Common_model->escape_data($this->post_data['date'])) ? trim($this->Common_model->escape_data($this->post_data['date'])) : '';

            $page = !empty($this->post_data['page']) ? trim($this->Common_model->escape_data($this->post_data['page'])) : "1";
            $per_page = !empty($this->post_data['per_page']) ? trim($this->Common_model->escape_data($this->post_data['per_page'])) : "10";

            if (empty($patient_id) ||
                    empty($user_type)
            ) {
                $this->bad_request();
            }

            if ($user_type == 2) {

                if (empty($flag) ||
                        empty($clinic_id) ||
                        empty($doctor_id)
                ) {
                    $this->bad_request();
                }
            }

            $prescription_data = array(
                'user_id' => $patient_id,
                'page' => $page,
                'per_page' => $per_page,
                'user_type' => $user_type,
                'flag' => $flag,
                'date' => $date
            );

            if ($user_type == 2) {

                //check data belongs to the doctor or not
                $requested_data = array(
                    'doctor_id' => $doctor_id,
                    'clinic_id' => $clinic_id,
                    'patient_id' => $patient_id
                );
                $this->check_data_belongs_doctor($requested_data, 2);

                $prescription_data['flag'] = $flag;
                $prescription_data['logged_in'] = $this->user_id;

                $get_prescription_data = $this->User_model->get_patient_prescription($prescription_data);
            } else {
                $prescription_data['flag'] = '';
                $prescription_data['logged_in'] = $this->user_id;
                $get_prescription_data = $this->User_model->get_patient_prescription($prescription_data);
            }

            if (!empty($get_prescription_data['data'])) {
                $this->my_response['status'] = true;
                $this->my_response['message'] = lang('common_detail_found');
                $this->my_response['data'] = $get_prescription_data['data'];
                $this->my_response['total_count'] = (string) $get_prescription_data['total_records'];
                $this->my_response['per_page'] = $per_page;
                $this->my_response['current_page'] = $page;
            } else {
                $this->my_response['status'] = true;
                $this->my_response['message'] = lang('common_detail_not_found');
                $this->my_response['data'] = array();
            }
            $this->send_response();
        } catch (ErrorException $ex) {
            $this->error = $ex->getMessage();
            $this->store_error();
        }
    }

    /**
     * Description :- This function is used to add the vital for the patient by doctor
     * 
     * 
     * 
     * 
     */
    public function edit_kco_post() {

        try {

            $clinic_id = !empty($this->Common_model->escape_data($this->post_data['clinic_id'])) ? trim($this->Common_model->escape_data($this->post_data['clinic_id'])) : '';
            $doctor_id = !empty($this->Common_model->escape_data($this->post_data['doctor_id'])) ? trim($this->Common_model->escape_data($this->post_data['doctor_id'])) : '';
            $patient_id = !empty($this->Common_model->escape_data($this->post_data['patient_id'])) ? trim($this->Common_model->escape_data($this->post_data['patient_id'])) : '';
            $appointment_id = !empty($this->Common_model->escape_data($this->post_data['appointment_id'])) ? trim($this->Common_model->escape_data($this->post_data['appointment_id'])) : '';
            $kco = !empty($this->post_data['kco']) ? $this->post_data['kco'] : '';

            if (empty($clinic_id) ||
                    empty($doctor_id) ||
                    empty($patient_id) ||
                    empty($appointment_id)
            ) {
                $this->bad_request();
            }

            //check data belongs to the doctor or not
            $requested_data = array(
                'appointment_id' => $appointment_id,
                'doctor_id' => $doctor_id,
                'clinic_id' => $clinic_id,
                'patient_id' => $patient_id
            );
            $this->check_data_belongs_doctor($requested_data);

            $update_data = array(
                'clinical_notes_reports_kco' => $kco,
                'clinical_notes_reports_updated_at' => $this->utc_time_formated
            );

            $update_where = array(
                'clinical_notes_reports_appointment_id' => $appointment_id,
                'clinical_notes_reports_user_id' => $patient_id,
                'clinical_notes_reports_clinic_id' => $clinic_id
            );

            $is_update = $this->Common_model->update(TBL_CLINICAL_NOTES_REPORT, $update_data, $update_where);

            if ($is_update > 0) {
                $this->my_response['status'] = true;
                $this->my_response['message'] = lang('common_detail_update');
            } else {
                $this->my_response['status'] = false;
                $this->my_response['message'] = lang('failure');
            }

            $this->send_response();
        } catch (ErrorException $ex) {
            $this->error = $ex->getMessage();
            $this->store_error();
        }
    }

    /**
     * Description :- this function is used to search the doctor 
     * based on the phone number, unique id and name
     * 
     * 
     * 
     */
    public function search_doctor_list_post() {

        try {
            $search = !empty($this->Common_model->escape_data($this->post_data['search'])) ? trim($this->Common_model->escape_data($this->post_data['search'])) : '';

            $get_doctors_sql = "SELECT 
                                    user_id,
                                    CONCAT(user_first_name,' ',user_last_name) AS user_name,
                                    user_phone_number,
                                    user_unique_id,
                                    doctor_clinic_mapping_fees AS doctor_fees,
                                    address_name
                                FROM 
                                    " . TBL_USERS . " 
                                JOIN
                                    " . TBL_DOCTOR_CLINIC_MAPPING . " 
                                ON 
                                    doctor_clinic_mapping_user_id = user_id 
                                AND 
                                    doctor_clinic_mapping_status=1
                                AND 
                                    doctor_clinic_mapping_role_id=1    
                                JOIN 
                                    " . TBL_CLINICS . " 
                                ON 
                                    clinic_id=doctor_clinic_mapping_clinic_id AND clinic_status=1   
                                LEFT JOIN 
                                    " . TBL_ADDRESS . " 
                                ON 
                                    address_user_id=clinic_id AND address_status=1 AND address_type=2    
                                WHERE 
                                    user_status = 1 AND
                                    user_type = 2 ";

            if (!empty($search)) {
                $get_doctors_sql .= " AND ( 
                                            CONCAT(user_first_name,' ',user_last_name) LIKE '%" . $search . "%'  OR
                                            user_phone_number LIKE '%" . $search . "%' OR    
                                            LOWER(user_unique_id) LIKE '%" . strtolower($search) . "%'    
                                          ) ";
            }
             $order_by[] = " user_first_name  ASC ";
             $get_doctors_sql.=" ORDER BY  " . implode(',', $order_by);
             $get_doctors_sql .= "LIMIT 0, 50 ";

            $doctor_list = $this->Common_model->get_all_rows_by_query($get_doctors_sql);

            if (!empty($doctor_list)) {
                $final_array = array();
                foreach ($doctor_list as $doctor) {
                    $get_doctors[] = array(
                        "user_id" => $doctor['user_id'],
                        "user_name" => DOCTOR.' '.$doctor['user_name'],
                        "user_phone_number" => $doctor['user_phone_number'],
                        "user_unique_id" => $doctor['user_unique_id'],
                        "doctor_fees" => $doctor['doctor_fees'],
                        "address_name" => $doctor['address_name'],
                    );
                }

                $this->my_response['status'] = true;
                $this->my_response['message'] = lang('common_detail_found');
                $this->my_response['data'] = $get_doctors;
            } else {
                $this->my_response['status'] = false;
                $this->my_response['message'] = lang('common_detail_not_found');
            }

            $this->send_response();
        } catch (ErrorException $ex) {
            $this->error = $ex->getMessage();
            $this->store_error();
        }
    }

    public function delete_vital_by_patient_post() {
        try {

            $patient_id = !empty($this->Common_model->escape_data($this->post_data['patient_id'])) ? trim($this->Common_model->escape_data($this->post_data['patient_id'])) : '';
            $vital_id = !empty($this->Common_model->escape_data($this->post_data['vital_id'])) ? trim($this->Common_model->escape_data($this->post_data['vital_id'])) : '';

            $where = array(
                'vital_report_doctor_id' => $this->user_id,
                'vital_report_user_id' => $patient_id,
                'vital_report_id' => $vital_id
            );

            $update = array(
                'vital_report_status' => 9,
                'vital_report_updated_at' => $this->utc_time_formated
            );

            $is_update = $this->Common_model->update(TBL_VITAL_REPORTS, $update, $where);

            if ($is_update > 0) {
                $this->my_response['status'] = true;
                $this->my_response['message'] = lang('vital_delete');
            } else {
                $this->my_response['status'] = false;
                $this->my_response['message'] = lang('failure');
            }

            $this->send_response();
        } catch (ErrorException $ex) {
            $this->error = $ex->getMessage();
            $this->store_error();
        }
    }

    public function delete_prescription_by_patient_post() {
        try {

            $patient_id = !empty($this->Common_model->escape_data($this->post_data['patient_id'])) ? trim($this->Common_model->escape_data($this->post_data['patient_id'])) : '';
            $prescription_id = !empty($this->Common_model->escape_data($this->post_data['prescription_id'])) ? trim($this->Common_model->escape_data($this->post_data['prescription_id'])) : '';

            $where = array(
                'patient_prescription_user_id' => $patient_id,
                'patient_prescription_id' => $prescription_id
            );

            $update = array(
                'patient_prescription_status' => 9,
                'patient_prescription_modified_at' => $this->utc_time_formated
            );

            $is_update = $this->Common_model->update(TBL_PATIENT_PRESCRIPTION, $update, $where);

            if ($is_update > 0) {
                $this->my_response['status'] = true;
                $this->my_response['message'] = lang('prescription_delete');
            } else {
                $this->my_response['status'] = false;
                $this->my_response['message'] = lang('failure');
            }

            $this->send_response();
        } catch (ErrorException $ex) {
            $this->error = $ex->getMessage();
            $this->store_error();
        }
    }
    public function delete_invoice_by_patient_post() {
        try {

            $patient_id = !empty($this->Common_model->escape_data($this->post_data['patient_id'])) ? trim($this->Common_model->escape_data($this->post_data['patient_id'])) : '';
            $invoice_id = !empty($this->Common_model->escape_data($this->post_data['invoice_id'])) ? trim($this->Common_model->escape_data($this->post_data['invoice_id'])) : '';

            $where = array(
                'patient_invoice_user_id' => $patient_id,
                'patient_invoice_id' => $invoice_id
            );

            $update = array(
                'patient_invoice_status' => 9,
                'patient_invoice_modified_at' => $this->utc_time_formated
            );

            $is_update = $this->Common_model->update(TBL_PATIENT_INVOICE, $update, $where);

            if ($is_update > 0) {
                $this->my_response['status'] = true;
                $this->my_response['message'] = lang('invoice_delete');
            } else {
                $this->my_response['status'] = false;
                $this->my_response['message'] = lang('failure');
            }

            $this->send_response();
        } catch (ErrorException $ex) {
            $this->error = $ex->getMessage();
            $this->store_error();
        }
    }

    public function delete_report_by_patient_post() {
        try {

            $patient_id = !empty($this->Common_model->escape_data($this->post_data['patient_id'])) ? trim($this->Common_model->escape_data($this->post_data['patient_id'])) : '';
            $report_id = !empty($this->Common_model->escape_data($this->post_data['report_id'])) ? trim($this->Common_model->escape_data($this->post_data['report_id'])) : '';

            $where = array(
                'file_report_user_id' => $patient_id,
                'file_report_doctor_user_id' => $this->user_id,
                'file_report_id' => $report_id
            );

            $update = array(
                'file_report_updated_at' => $this->utc_time_formated,
                'file_report_status' => 9
            );

            $is_update = $this->Common_model->update(TBL_FILE_REPORTS, $update, $where);

            if ($is_update > 0) {
                $this->my_response['status'] = true;
                $this->my_response['message'] = lang('report_delete');
            } else {
                $this->my_response['status'] = false;
                $this->my_response['message'] = lang('failure');
            }

            $this->send_response();
        } catch (ErrorException $ex) {
            $this->error = $ex->getMessage();
            $this->store_error();
        }
    }

}
