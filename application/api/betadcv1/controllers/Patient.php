<?php
//error_reporting(-1);
//ini_set('display_errors', 'On');

/**
 * This controller use for user related activity
 */
class Patient extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model("User_model");
        $this->load->model("Appointments_model");
        $this->load->model("Clinic_model");
        $this->load->model("Patient_model");
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
        $id_proof_type = !empty($this->post_data['id_proof_type']) ? trim(($this->post_data['id_proof_type'])) : "";
        $id_proof_detail = !empty($this->post_data['id_proof_detail']) ? trim(($this->post_data['id_proof_detail'])) : "";
        $address = !empty($this->post_data['address']) ? trim(($this->post_data['address'])) : "";
        $address1 = !empty($this->post_data['address1']) ? trim(($this->post_data['address1'])) : "";
        $city_id = !empty($this->post_data['city_id']) ? trim($this->Common_model->escape_data($this->post_data['city_id'])) : "";
        $state_id = !empty($this->post_data['state_id']) ? trim($this->Common_model->escape_data($this->post_data['state_id'])) : "";
        $country_id = !empty($this->post_data['country_id']) ? trim($this->Common_model->escape_data($this->post_data['country_id'])) : "";
        $pincode = !empty($this->post_data['pincode']) ? trim(($this->post_data['pincode'])) : "";
        $locality = !empty($this->post_data['locality']) ? trim(($this->post_data['locality'])) : "";
        $latitude = !empty($this->post_data['latitude']) ? trim(($this->post_data['latitude'])) : "";
        $longitude = !empty($this->post_data['longitude']) ? trim(($this->post_data['longitude'])) : "";
        $phone_number = !empty($this->post_data['phone_number']) ? trim(($this->post_data['phone_number'])) : "";
        $languages = !empty($this->post_data['languages']) ? trim(($this->post_data['languages'])) : "";
        $share_status = !empty($this->post_data['share_status']) ? trim(($this->post_data['share_status'])) : "";
        $referred_by = !empty($this->post_data['referred_by']) ? trim(($this->post_data['referred_by'])) : "";
        $refer_other_doctor_id = !empty($this->post_data['refer_other_doctor_id']) ? trim(($this->post_data['refer_other_doctor_id'])) : "";
        $refer_user_id = !empty($this->post_data['refer_user_id']) ? trim(($this->post_data['refer_user_id'])) : "";
        $doctor_id = !empty($this->post_data['doctor_id']) ? trim(($this->post_data['doctor_id'])) : "";
        $emergency_contact_name = !empty($this->post_data['emergency_contact_name']) ? trim(($this->post_data['emergency_contact_name'])) : "";
        $emergency_contact_number = !empty($this->post_data['emergency_contact_number']) ? trim(($this->post_data['emergency_contact_number'])) : "";
        $marital_status = !empty($this->post_data['marital_status']) ? trim(($this->post_data['marital_status'])) : "";
        $food_allergies = !empty($this->post_data['food_allergies']) ? trim(($this->post_data['food_allergies'])) : "";
        $medicine_allergies = !empty($this->post_data['medicine_allergies']) ? trim(($this->post_data['medicine_allergies'])) : "";
        $other_allergies = !empty($this->post_data['other_allergies']) ? trim(($this->post_data['other_allergies'])) : "";
        $chronic_diseases = !empty($this->post_data['chronic_diseases']) ? trim(($this->post_data['chronic_diseases'])) : "";
        $injuries = !empty($this->post_data['injuries']) ? trim(($this->post_data['injuries'])) : "";
        $surgeries = !empty($this->post_data['surgeries']) ? trim(($this->post_data['surgeries'])) : "";
        $smoking_habits = !empty($this->post_data['smoking_habits']) ? trim(($this->post_data['smoking_habits'])) : "";
        $alcohol = !empty($this->post_data['alcohol']) ? trim(($this->post_data['alcohol'])) : "";
        $food_preference = !empty($this->post_data['food_preference']) ? trim(($this->post_data['food_preference'])) : "";
        $patient_id = !empty($this->post_data['patient_id']) ? trim(($this->post_data['patient_id'])) : "";
        $patient_family_history = !empty($this->post_data['patient_family_history']) ? json_decode($this->post_data['patient_family_history']) : array();
        $patient_activity_levels = !empty($this->post_data['patient_activity_levels']) ? json_decode($this->post_data['patient_activity_levels']) : array();
        $city_id_text = !empty($this->post_data['city_id_text']) ? trim($this->Common_model->escape_data($this->post_data['city_id_text'])) : "";
        $state_id_text = !empty($this->post_data['state_id_text']) ? trim($this->Common_model->escape_data($this->post_data['state_id_text'])) : "";
        $country_id_text = !empty($this->post_data['country_id_text']) ? trim($this->Common_model->escape_data($this->post_data['country_id_text'])) : "";
        $caretaker_mobile = !empty($this->post_data['caretaker_mobile']) ? trim($this->Common_model->escape_data($this->post_data['caretaker_mobile'])) : "";
        $patient_type = !empty($this->post_data['patient_type']) ? trim($this->Common_model->escape_data($this->post_data['patient_type'])) : "";
        $caretaker_user_id = !empty($this->post_data['caretaker_user_id']) ? trim($this->Common_model->escape_data($this->post_data['caretaker_user_id'])) : "";
        $caretaker_first_name = !empty($this->post_data['caretaker_first_name']) ? trim($this->Common_model->escape_data($this->post_data['caretaker_first_name'])) : "";
        $caretaker_last_name = !empty($this->post_data['caretaker_last_name']) ? trim($this->Common_model->escape_data($this->post_data['caretaker_last_name'])) : "";
        $caretaker_age = !empty($this->post_data['caretaker_age']) ? trim($this->Common_model->escape_data($this->post_data['caretaker_age'])) : "";
        $caretaker_gender = !empty($this->post_data['caretaker_gender']) ? trim($this->Common_model->escape_data($this->post_data['caretaker_gender'])) : "";
        $caretaker_relation = !empty($this->post_data['caretaker_relation']) ? trim($this->Common_model->escape_data($this->post_data['caretaker_relation'])) : "";
        $caregiver_patient_email = !empty($this->post_data['caregiver_patient_email']) ? trim($this->Common_model->escape_data($this->post_data['caregiver_patient_email'])) : "";
        $user_patient_id = !empty($this->post_data['user_patient_id']) ? trim($this->Common_model->escape_data($this->post_data['user_patient_id'])) : "";
		
        try {
			if (empty($first_name) || empty($dob)) { 
				$this->bad_request(); 
			}
            if(($patient_type == 1 && empty($phone_number)) || ($patient_type == 2 && empty($caretaker_mobile) && empty($patient_id)))
                $this->bad_request();
			
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

            if (!empty($phone_number) && validate_phone_number($phone_number)) {
                $this->my_response['status'] = false;
                $this->my_response['message'] = lang("invalid_phone_number");
                $this->send_response();
            }

            if (!empty($email) && validate_email($email)) {
                $this->my_response['status'] = false;
                $this->my_response['message'] = lang("invalid_email");
                $this->send_response();
            }

            if (!empty($pincode) && validate_pincode($pincode)) {
                $this->my_response['status'] = false;
                $this->my_response['message'] = lang("invalid_pincode");
                $this->send_response();
            }

            if ((!empty($first_name) && validate_characters($first_name)) || (!empty($last_name) && validate_characters($last_name))) {
                $this->my_response['status'] = false;
                $this->my_response['message'] = lang("mycontroller_invalid_request");
                $this->send_response();
            }

            //check email id exists or not
            if(!empty($email)){
                $check_email_sql = "SELECT 
                                        user_email, user_email_verified
                                 FROM 
                                    " . TBL_USERS . " 
                                 WHERE 
                                      LOWER(user_email) = '" . strtolower($email) . "' 
                                 AND         
                                      user_status != 9 
                                 AND 
                                      user_type = '" . $user_type . "' ";
                if(!empty($patient_id))
                    $check_email_sql .= " AND user_id!=".$patient_id;
                $check_email = $this->Common_model->get_single_row_by_query($check_email_sql);
                if (!empty($check_email)) {
                    $this->my_response['status'] = false;
                    $this->my_response['message'] = lang("user_register_email_exist");
                    $this->send_response();
                }
            }
            
			//check phone no exists or not
			$check_number = $this->User_model->get_details_by_number($phone_number, $user_type, $patient_id);
			if (!empty($check_number) && count($check_number) > 0) {
				$this->my_response['status'] = false;
				$this->my_response['message'] = lang("user_register_phone_number_exist");
				$this->send_response();
            }
            if($patient_type == 2 && empty($patient_id)) {
                if(!empty($caregiver_patient_email) && strtolower($caregiver_patient_email) == strtolower($email)) {
                    $this->my_response['status'] = false;
                    $this->my_response['message'] = lang("caregiver_email_same");
                    $this->send_response();
                }
                if(!empty($caretaker_mobile) && strtolower($caretaker_mobile) == strtolower($phone_number)) {
                    $this->my_response['status'] = false;
                    $this->my_response['message'] = lang("caregiver_mobile_same");
                    $this->send_response();
                }
                if(!empty($caregiver_patient_email)) {
                    $check_email_sql = "SELECT 
                                            user_email
                                     FROM 
                                        " . TBL_USERS . " 
                                     WHERE 
                                          LOWER(user_email) = '" . strtolower($caregiver_patient_email) . "' 
                                     AND         
                                          user_status != 9 
                                     AND 
                                          user_type = '" . $user_type . "' ";
                    
                    $check_email = $this->Common_model->get_single_row_by_query($check_email_sql);
                    if (!empty($check_email)) {
                        $this->my_response['status'] = false;
                        $this->my_response['message'] = lang("caregiver_register_email_exist");
                        $this->send_response();
                    }
                }
                $get_caretaker_user = $this->Common_model->get_single_row(TBL_USERS, 'user_id', ['user_phone_number' => $caretaker_mobile, 'user_type' => 1, 'user_status' => 1]);
                if(!empty($get_caretaker_user)) {
                    $parent_patient_id = $get_caretaker_user['user_id'];
                } else {
                    $unique_id = strtoupper($this->Common_model->escape_data(str_rand_access_token(8)));
                    $password = $this->Common_model->generate_random_string(4, 1, 1, 1);
                    $user_caretaker_data = array(
                        'user_unique_id' => $unique_id,
                        'user_first_name' => $caretaker_first_name,
                        'user_last_name' => $caretaker_last_name,
                        'user_phone_number' => $caretaker_mobile,
                        'user_phone_verified' => 1,
                        'user_gender' => $caretaker_gender,
                        'user_source_id' => $doctor_id,
                        'user_plan_id' => DEFAULT_USER_PLAN_ID,
                        'user_password' => password_hash(sha1($password), PASSWORD_BCRYPT),
                        'user_created_at' => $this->utc_time_formated
                    );
                    if(!empty($caregiver_patient_email)) {
                        $user_caretaker_data['user_email'] = $caregiver_patient_email;
                        $user_caretaker_data['user_email_verified'] = 1;
                    }
                    $parent_patient_id = $this->User_model->insert(TBL_USERS, $user_caretaker_data);
                    if(!empty($caretaker_age)) {
                        $insert_user_details = [
                            'user_details_user_id' => $parent_patient_id,
                            'user_details_dob' => date('Y-07-01', strtotime("-" . $caretaker_age . " years")),
                            'user_details_agree_medical_share' => 2,
                            'user_details_created_at' => $this->utc_time_formated
                        ];
                        $this->User_model->insert(TBL_USER_DETAILS, $insert_user_details);
                    }
                    //by default 2 way authentication on for the patient
                    $setting_array = array();
                    $setting_array[] = array(
                        'id' => "1",
                        'name' => "data security",
                        'status' => "1"
                    );
                    $insert_setting_array = array(
                        'setting_user_id' => $parent_patient_id,
                        'setting_clinic_id' => '',
                        'setting_data' => json_encode($setting_array),
                        'setting_type' => 2,
                        'setting_data_type' => 1,
                        'setting_created_at' => $this->utc_time_formated
                    );
                    $this->Common_model->insert(TBL_SETTING, $insert_setting_array);

                    //store the user authentication details
                    $user_auth_insert_array = array(
                        'auth_user_id' => $parent_patient_id,
                        'auth_type' => 2,
                        'auth_code' => '',
                        'auth_otp_expiry_time' => '',
                        'auth_created_at' => $this->utc_time_formated
                    );
                    $this->User_model->store_auth_detail($user_auth_insert_array);
                }
                $insert_caretaker_mapping = array(
                    'parent_patient_id' => $parent_patient_id,
                    'mapping_status' => 1,
                    'mapping_relation' => $caretaker_relation,
                    'created_at' => $this->utc_time_formated,
                    'created_by' => $this->user_id
                );
            }
			if ($share_status == true)
                $share_status = 1;
            else
                $share_status = 2;
            $insert_data = array();
            if (!empty($first_name))
                $insert_data['user_first_name'] = ucfirst($first_name);
            if (!empty($user_patient_id))
                $insert_data['user_patient_id'] = $user_patient_id;
            else
                $insert_data['user_patient_id'] = NULL;

            $insert_data['user_last_name'] = ucfirst($last_name);
            if (!empty($email)){
                $insert_data['user_email'] = $email;
                $insert_data['user_email_verified'] = 1;
            }
            if(!empty($phone_number)) {
                $insert_data['user_phone_number'] = $phone_number;
                $insert_data['user_phone_verified'] = 1;
            }
            if (!empty($gender))
                $insert_data['user_gender'] = $gender;
            $insert_data['user_referred_by']  = 0;
            if(!empty($referred_by) && is_numeric($referred_by) && $referred_by > 0)
                $insert_data['user_referred_by']  = $referred_by;
            if(!empty($patient_id)) {
                $user_id = $patient_id;
                if(IS_AUDIT_LOG_ENABLE) {
                    $new_user_data = $insert_data;
                    $columns = implode(',',array_keys($new_user_data));
                    $user_old_data = $this->User_model->get_details_by_id($patient_id,$columns);
                    $this->load->model("Auditlog_model");
                    $this->Auditlog_model->create_audit_log($this->user_id, 1, AUDIT_SLUG_ARR['USER_PROFILE_ACTION'], $user_old_data, $new_user_data, TBL_USERS, 'user_id', $user_id);
                }
                $insert_data['user_modified_at']   = $this->utc_time_formated;
                $this->User_model->update_profile($user_id, $insert_data);
            } else {
                if(!empty($phone_number)) {
                    $insert_data['user_phone_number'] = $phone_number;
                    $insert_data['user_phone_verified'] = 1;
                }
                if(!empty($doctor_id))
                    $insert_data['user_source_id']= $doctor_id;
                elseif(isset($this->user_id) && !empty($this->user_id))
                    $insert_data['user_source_id']= $this->user_id;
                $password = $this->Common_model->generate_random_string(4, 1, 1, 1);
                $unique_id = strtoupper($this->Common_model->escape_data(str_rand_access_token(8)));
                $insert_data['user_password']     = password_hash(sha1($password), PASSWORD_BCRYPT);
                $insert_data['user_referred_by']  = $referred_by;
                $insert_data['user_unique_id']    = $unique_id;
                $insert_data['user_plan_id'] = DEFAULT_USER_PLAN_ID;
                $insert_data['user_created_at']   = $this->utc_time_formated;
                $user_id = $this->User_model->insert(TBL_USERS, $insert_data);
                if($patient_type == 2 && !empty($parent_patient_id)) {
                    $insert_caretaker_mapping['patient_id'] = $user_id;
                    $this->User_model->insert('me_patient_family_member_mapping', $insert_caretaker_mapping);
                    $caregiver_name = $caretaker_first_name . ' ' . $caretaker_last_name;
                    $pt_name = $insert_data['user_first_name'] . ' ' . $insert_data['user_last_name'];
                    $global_setting_row = $this->Common_model->get_single_row('me_global_settings', 'global_setting_value', array('global_setting_name' => 'email_id'));
                    $support_email = $global_setting_row['global_setting_value'];
                    $message = sprintf(lang('caregiver_notification_sms'), $caregiver_name, $pt_name, trim(DOMAIN_URL, '/'), $support_email);
                    $whatsapp_sms_body = sprintf(lang('wa_template_caregiver_has_shared_contacts_37'), $caregiver_name, $pt_name, trim(DOMAIN_URL, '/'), $support_email);
                    $send_message = array(
                        'phone_number' => $caretaker_mobile,
                        'message' => $message,
                        'whatsapp_sms_body' => $whatsapp_sms_body,
                        'doctor_id' => $doctor_id,
                        'patient_id' => $parent_patient_id,
                        'is_sms_count' => true,
                    );
                    if(!empty($caretaker_mobile))
                        send_communication($send_message);

                    $message = sprintf(lang('patient_has_shared_contacts'), $pt_name, $caregiver_name, trim(DOMAIN_URL, '/'), $support_email);
                    $whatsapp_sms_body = sprintf(lang('wa_template_patient_has_shared_contacts_38'), $pt_name, $caregiver_name, trim(DOMAIN_URL, '/'), $support_email);
                    $send_message = array(
                        'phone_number' => $phone_number,
                        'message' => $message,
                        'whatsapp_sms_body' => $whatsapp_sms_body,
                        'doctor_id' => $doctor_id,
                        'patient_id' => $user_id,
                        'is_sms_count' => true,
                    );
                    if(!empty($phone_number))
                        send_communication($send_message);
                }
			}
            //update the addrress
            $update_address_data = array();
			$update_address_data['address_name'] = $address;
            $update_address_data['address_name_one'] = $address1;
            $update_address_data['address_city_id'] = $city_id;
            $update_address_data['address_state_id'] = $state_id;
            $update_address_data['address_country_id'] = $country_id;
            $update_address_data['address_pincode'] = $pincode;
            $update_address_data['address_locality'] = $locality;
            $update_address_data['address_latitude'] = $latitude;
            $update_address_data['address_longitude'] = $longitude;
            if(IS_AUDIT_LOG_ENABLE && !empty($patient_id)) {
                $log_address_data = $update_address_data;
                if(!empty($log_address_data['address_latitude']))
                    unset($log_address_data['address_latitude']);
                if(!empty($log_address_data['address_longitude']))
                    unset($log_address_data['address_longitude']);
                if(!empty($log_address_data['address_city_id']))
                    unset($log_address_data['address_city_id']);
                if(!empty($log_address_data['address_state_id']))
                    unset($log_address_data['address_state_id']);
                if(!empty($log_address_data['address_country_id']))
                    unset($log_address_data['address_country_id']);
                if(!empty($country_id_text)) {
                    $log_address_data['country_name'] = $country_id_text;
                }
                if(!empty($state_id_text)) {
                    $log_address_data['state_name'] = $state_id_text;
                }
                if(!empty($city_id_text)) {
                    $log_address_data['city_name'] = $city_id_text;
                }
                $columns = implode(',',array_keys($log_address_data));
                $user_address_old_data = $this->User_model->get_user_details_by_id($user_id,1 ,$columns);
                $this->Auditlog_model->create_audit_log($this->user_id, 1, AUDIT_SLUG_ARR['USER_PROFILE_ACTION'], $user_address_old_data, $log_address_data, TBL_ADDRESS, 'address_user_id', $user_id);
            }
 			if(!empty($update_address_data) && count($update_address_data) > 0)
				$address_is_update = $this->User_model->update_address($user_id, $update_address_data);

            //update the user details
            $update_user_details = array();
            if(!empty($dob))
                $update_user_details['user_details_dob'] = $dob;
                $update_user_details['user_details_height'] = $height;
                $update_user_details['user_details_weight'] = $weight;
                $update_user_details['user_details_blood_group'] = $blood_group;
                $update_user_details['user_details_languages_known'] = $languages;
                $update_user_details['user_details_occupation'] = $occupation;
                $update_user_details['user_details_id_proof_type'] = $id_proof_type;
                $update_user_details['user_details_id_proof_detail'] = $id_proof_detail;
                $update_user_details['user_details_emergency_contact_person'] = $emergency_contact_name;
                $update_user_details['user_details_emergency_contact_number'] = $emergency_contact_number;
                $update_user_details['user_details_marital_status'] = $marital_status;
                $update_user_details['user_details_food_allergies'] = $food_allergies;
                $update_user_details['user_details_medicine_allergies'] = $medicine_allergies;
                $update_user_details['user_details_other_allergies'] = $other_allergies;
                $update_user_details['user_details_chronic_diseases'] = $chronic_diseases;
                $update_user_details['user_details_surgeries'] = $surgeries;
                $update_user_details['user_details_injuries'] = $injuries;
                $update_user_details['user_details_smoking_habbit'] = $smoking_habits;
                $update_user_details['user_details_alcohol'] = $alcohol;
                $update_user_details['user_details_food_preference'] = $food_preference;
            if(count($patient_activity_levels) > 0) {
                $activity_levels = '';
                $activity_days = '';
                $activity_hours = '';
                foreach ($patient_activity_levels as $key => $value) {
                    if(!empty($value->activity_levels) && !empty($value->activity_days) && !empty($value->activity_hours)) {
                        if($key > 0) {
                            $activity_levels .= ', ';
                            $activity_days .= ',';
                            $activity_hours .= ',';
                        }
                        $activity_levels .= $value->activity_levels;
                        $activity_days .= $value->activity_days;
                        $activity_hours .= $value->activity_hours;
                    }
                }
                if(!empty($activity_levels) && !empty($activity_days) && !empty($activity_hours)) {
                    $update_user_details['user_details_activity_level'] = $activity_levels;
                    $update_user_details['user_details_activity_days'] = $activity_days;
                    $update_user_details['user_details_activity_hours'] = $activity_hours;
                }
            }
			$update_user_details['user_details_agree_medical_share'] = $share_status;
            if(IS_AUDIT_LOG_ENABLE && !empty($patient_id)) {
                $columns = implode(',',array_keys($update_user_details));
                $user_details_old_data = $this->User_model->get_user_details_by_id($user_id,1,$columns);
                $this->Auditlog_model->create_audit_log($this->user_id, 1, AUDIT_SLUG_ARR['USER_PROFILE_ACTION'], $user_details_old_data, $update_user_details, TBL_DOCTOR_DETAILS, 'address_user_id', $user_id);
            }
			$user_details_is_update = $this->User_model->update_user_details($user_id, $update_user_details);

            $this->Common_model->update(TBL_FAMILY_MEDICAL_HISTORY, ['family_medical_history_status' => 9, 'family_medical_history_updated_at' => $this->utc_time_formated], ['family_medical_history_user_id' => $user_id]);
            if(count($patient_family_history) > 0) {
                $created_time = $this->utc_time_formated;
                $insert_family_history_array = array();
                foreach ($patient_family_history as $key => $value) {
                    if(!empty($value->medical_condition) && !empty($value->relation) && !empty($value->history_date)) {
                        $insert_family_history_array[] = array(
                            "family_medical_history_user_id" => $user_id,
                            "family_medical_history_medical_condition_id" => implode(',', $value->medical_condition),
                            "family_medical_history_relation" => $value->relation,
                            "family_medical_history_date" => $value->history_date,
                            "family_medical_history_comment" => $value->comments,
                            "family_medical_history_created_at" => $created_time
                        );
                    }
                }
                if(count($insert_family_history_array) > 0)
                    $this->Common_model->insert_multiple(TBL_FAMILY_MEDICAL_HISTORY, $insert_family_history_array);
            }
            if(!empty(trim($referred_by))) {
                $refer_insert_array = array(
                      'refer_user_id' => $user_id
                    );
                if(is_numeric($referred_by) && $referred_by > 0) {            
                    $user_data = $this->User_model->get_details_by_id($referred_by);
                    $refer_insert_array['refer_other_doctor_id'] = $referred_by;
                    if (!empty($user_data) && count($user_data) > 0) {
                        $refer_insert_array['refer_doctor_name'] = $user_data['user_first_name'] . ' ' . $user_data['user_last_name'];
                    }
                    $refer_insert_array['refer_other_doctor_id'] = $referred_by;
                } elseif(!empty($referred_by)) {
                    $refer_insert_array['refer_doctor_name'] = $referred_by;
                }
                if(isset($this->user_id) && !empty($this->user_id))
                    $refer_insert_array['refer_doctor_id']= $this->user_id;
                //Save refer data
                if(empty($refer_user_id)) {
                    $refer_insert_array['refer_created_at'] = $this->utc_time_formated;
                    $this->Common_model->insert(TBL_REFER, $refer_insert_array);
                } elseif($refer_other_doctor_id != $referred_by && !empty($patient_id)) {
                    $refer_insert_array['refer_updated_at'] = $this->utc_time_formated;
                    $this->Common_model->update(TBL_REFER, $refer_insert_array, ['refer_user_id' => $patient_id]);
                }
            }

            if(!empty($patient_id)) {
                $this->my_response['status']     = true;
                $this->my_response['message']    = lang('patient_edited');
                $this->my_response['patient_id'] = $user_id;
            } elseif ($user_id > 0 || $address_is_update > 0 || $user_details_is_update > 0) {
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
				if(!empty($email)){
                    $send_notification_data = array(
                        'email' => $email,
                        'first_name' => $first_name,
                        'password' => $password,
                        'unique_id' => $unique_id,
                        'reset_token' => $reset_token
                    );
                    $cron_job_path = CRON_PATH . " notification/add_patient_email/" . base64_encode(json_encode($send_notification_data));
                    exec(PHP_PATH . " " . $cron_job_path . " > /dev/null &");
				}
                $notification_data['user_id'] = $user_id;
                $notification_data['doctor_id'] = $doctor_id;
                $cron_job_path = CRON_PATH . " notification/patient_welcome_email/" . base64_encode(json_encode($notification_data));
                exec(PHP_PATH . " " . $cron_job_path . " > /dev/null &");
                $this->my_response['status']     = true;
                $this->my_response['message']    = lang('patient_added');
                $this->my_response['patient_id'] = $user_id;
            } else {
                $this->my_response['status']  = false;
                $this->my_response['message'] = lang('failure');
            }
            $this->send_response();
        } catch (Exception $e) {
            $this->error = $e->getMessage();
            $this->store_error();
        }
    }

    public function get_patient_user_details_post() {
        try {
            $patient_id = !empty($this->post_data['patient_id']) ? trim($this->Common_model->escape_data($this->post_data['patient_id'])) : "";

            if (empty($patient_id)) {
                $this->bad_request();
            }
            $patient_data = $this->User_model->get_patient_details_by_id($patient_id);
            $patient_data['patient_family_history'] = $this->User_model->get_family_medical_history($patient_id);
            $patient_data['patient_caretaker_data'] = [];
            
            $columns = 'u.user_id,u.user_phone_number,u.user_first_name,u.user_last_name,map.mapping_relation';
            $this->load->model('User_model', 'user');
            $patient_caretaker_data = $this->user->get_linked_family_members($patient_id, $columns);
            if(!empty($patient_caretaker_data))
                $patient_data['patient_caretaker_data'] = $patient_caretaker_data;
            
            $patient_data['user_photo_filepath_thumb'] = '';
            if(!empty($patient_data['user_photo_filepath']))
                $patient_data['user_photo_filepath_thumb'] = get_image_thumb($patient_data['user_photo_filepath']);
            $patient_data['user_photo_filepath'] = get_file_full_path($patient_data['user_photo_filepath']);
            if(!empty($patient_data['user_details_id_proof_image']))
                $patient_data['user_details_id_proof_image_thumb'] = get_image_thumb($patient_data['user_details_id_proof_image']);
            $patient_data['user_details_id_proof_image'] = get_file_full_path($patient_data['user_details_id_proof_image']);
            $this->my_response['status'] = true;
            $this->my_response['data'] = $patient_data;
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
            $is_patient_from_gdb = !empty($this->post_data['is_patient_from_gdb']) ? trim($this->Common_model->escape_data($this->post_data['is_patient_from_gdb'])) : "";

            if (empty($search_text)) {
                $this->bad_request();
            }
            // $rece = $this->User_model->get_doctor_receptionist($doctor_id, 'user_id');
            // $user_source_id_arr = array_column($rece, 'user_id');
            $user_source_id_arr = [$doctor_id];
            $get_search_patient_data = $this->User_model->search_patient($search_text, $doctor_id, $is_patient_from_gdb, $user_source_id_arr);
            foreach ($get_search_patient_data as $key => $value) {
                $get_search_patient_data[$key]['relation'] = "";
                if(!empty($value['mapping_relation']))
                    $get_search_patient_data[$key]['relation'] = relation_map($value['mapping_relation']);
                $get_search_patient_data[$key]['is_new_patient'] = 'Yes';
                if(!empty($value['appointment_id']) || in_array($value['user_source_id'], $user_source_id_arr)) {
                    $get_search_patient_data[$key]['is_new_patient'] = 'No';
                }
                if(empty($value['user_phone_number'])) {
                    $get_search_patient_data[$key]['user_search'] = $value['user_name'] . ' ' . $value['parent_user_phone_number'] . ' ' . $value['user_unique_id'] . ' ' . $value['user_patient_id'];
                } else {
                    $get_search_patient_data[$key]['user_search'] = $value['user_name'] . ' ' . $value['user_phone_number'] . ' ' . $value['user_unique_id'] . ' ' . $value['parent_user_phone_number'] . ' ' . $value['user_patient_id'];
                }
            }
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
     * 
     * 
     * 
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
                    "doctor_user_id" => $primary_doctor_data['user_id'],
                    "doctor_first_name" => $primary_doctor_data['user_first_name'],
                    "doctor_last_name" => $primary_doctor_data['user_last_name'],
                    "doctor_photo" => get_file_full_path($primary_doctor_data['user_photo_filepath']),
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
                'vital_report_created_by' => $this->user_id,
                'vital_report_created_at' => $this->utc_time_formated,
                'vital_report_updated_at' => $this->utc_time_formated
            );

            $inserted_id = $this->Common_model->insert(TBL_VITAL_REPORTS, $vital_array);

            if ($inserted_id > 0) {
                delete_past_prescription($appointment_id);
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
                'vital_report_updated_at' => $this->utc_time_formated,
                'vital_report_updated_by' => $this->user_id
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
                delete_past_prescription($appointment_id);
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
                $vital_data['logged_in'] = $doctor_id;

                $get_vital_data = $this->User_model->get_patient_vital($vital_data);
            } else {
                $vital_data['flag'] = '';
                $vital_data['logged_in'] = $this->user_id;
                $get_vital_data = $this->User_model->get_patient_vital($vital_data);
            }


            if (!empty($get_vital_data['data'])) {
                $this->my_response['status'] = true;
                $this->my_response['message'] = lang('common_detail_found');
                $this->my_response['data'] = $get_vital_data['data'];
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
                'vital_report_updated_by' => $this->user_id,
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
            $file_upload_validate = file_upload_validate($_FILES);
            if(!$file_upload_validate['status']) {
                $this->my_response['status'] = false;
                $this->my_response['message'] = $file_upload_validate['error'];
                $this->send_response();
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
                $report_array['file_report_added_by_user_id'] = $this->user_id;

                if ($user_type == 1) {

                    $patient_user_detail = $this->User_model->get_details_by_id($patient_id);
                    $patient_user_name = '';

                    if (!empty($patient_user_detail) && !empty($patient_user_detail['user_first_name'])) {
                        $patient_user_name = $patient_user_detail['user_first_name'] . ' ' . $patient_user_detail['user_last_name'];
                    }

                    $doctor_user_detail = $this->User_model->get_details_by_id($doctor_id);
                    $doctor_user_name = '';

                    if (!empty($doctor_user_detail) && !empty($doctor_user_detail['user_first_name'])) {
                        $doctor_name = DOCTOR . $doctor_user_detail['user_first_name'] . ' ' . $doctor_user_detail['user_last_name'];
                    }

                }
            } else {
                $report_array['file_report_doctor_user_id'] = $this->user_id;
                $report_array['file_report_added_by_user_id'] = $this->user_id;
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
                            $file_report_image_url = get_file_json_detail(REPORT_FOLDER . "/" . $inserted_id . "/" . $new_profile_img);
                            $insert_image_array = array(
                                'file_report_image_file_report_id' => $inserted_id,
                                'file_report_image_url' => $file_report_image_url,
                                'report_file_size' => get_file_size(get_file_full_path($file_report_image_url)),
                                'file_report_image_created_at' => $this->utc_time_formated
                            );
                            $this->Common_model->insert(TBL_FILE_REPORTS_IMAGES, $insert_image_array);
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
            $search = !empty($this->Common_model->escape_data(trim($this->post_data['search']))) ? trim($this->Common_model->escape_data(trim($this->post_data['search']))) : '';
            $flag = !empty($this->Common_model->escape_data($this->post_data['flag'])) ? trim($this->Common_model->escape_data($this->post_data['flag'])) : 1;
            $page = !empty($this->post_data['page']) ? trim($this->Common_model->escape_data($this->post_data['page'])) : "1";
            $per_page = !empty($this->post_data['per_page']) ? trim($this->Common_model->escape_data($this->post_data['per_page'])) : "10";
			
			$rptTypeId = !empty($this->Common_model->escape_data($this->post_data['rptTypeId'])) ? trim($this->Common_model->escape_data($this->post_data['rptTypeId'])) : '';

            if (empty($patient_id) || empty($user_type)) {
                $this->bad_request();
            }

            if ($user_type == 2) {
                if (empty($flag) || empty($clinic_id) || empty($doctor_id)) {
                    $this->bad_request();
                }
            }

            $report_data = array(
                'user_id' => $patient_id,
                'page' => $page,
                'per_page' => $per_page,
                'user_type' => $user_type
            );
			
			if(!empty($rptTypeId)){
				$report_data['file_report_report_type_id'] = $rptTypeId;
			}

            if ($user_type == 2) {
                //check data belongs to the doctor or not
                $requested_data = array(
                    'doctor_id'  => $doctor_id,
                    'clinic_id'  => $clinic_id,
                    'patient_id' => $patient_id
                );
                $this->check_data_belongs_doctor($requested_data, 2);
				
                $report_data['flag'] = $flag;
                $report_data['logged_in'] = $doctor_id;
                $report_data['search'] = $search;
                $get_report_data = $this->User_model->get_patient_report($report_data);
            } else {
                $report_data['flag'] = '';
                $report_data['logged_in'] = $this->user_id;
                $get_report_data = $this->User_model->get_patient_report($report_data);
            }
            
            if (!empty($get_report_data['data'])) {
                $this->my_response['status'] = true;
                $this->my_response['message'] = lang('common_detail_found');
                $this->my_response['data'] = $get_report_data['data'];
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
            $report_type_id = !empty($this->Common_model->escape_data($this->post_data['report_type_id'])) ? trim($this->Common_model->escape_data($this->post_data['report_type_id'])) : '';
			if(!empty($this->Common_model->escape_data($this->post_data['report_id']))){
				$report_id = is_array($this->post_data['report_id']) && !empty($this->post_data['report_id']) ? $this->post_data['report_id'] : trim($this->Common_model->escape_data($this->post_data['report_id']));
			}else{
				$report_id = '';
			}
			
			if(empty($patient_id) || empty($report_id))
                $this->bad_request();
			
			$whereInArry = [];
			if(is_array($report_id)){
				$whereInArry = ['file_report_id'=>$report_id];
				$report_id_exists = array('file_report_user_id' => $patient_id);
			}else{
				$report_id_exists = array(
					'file_report_id' => $report_id,
					'file_report_user_id' => $patient_id
				);
			}
			
            // $is_valid_data = $this->Common_model->validate_data(TBL_FILE_REPORTS, 'file_report_id', $report_id_exists, $whereInArry);
            // if ($is_valid_data == 2) {
            //     $this->my_response['status'] = false;
            //     $this->my_response['message'] = lang("mycontroller_invalid_request");
            //     $this->send_response();
            // }
			
			$report_data = array('report_id' => $report_id);
			if(is_array($report_id)){
				$get_report_detail = $this->User_model->get_all_report_detail($report_data);
			}else{
                if($report_type_id == 12)
				    $get_report_detail = $this->User_model->get_rx_upload_detail($report_data);
                else
                    $get_report_detail = $this->User_model->get_report_detail($report_data);
			}
			
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
                'file_report_updated_by_user_id' => $this->user_id,
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
					$data = $this->Common_model->escape_data($data);
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
                $clinic_notes_array['clinical_notes_reports_updated_by'] = $this->user_id;

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
                $clinic_notes_array['clinical_notes_reports_created_by'] = $this->user_id;

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
                    $reports_images_url = get_file_json_detail(CILINICAL_REPORT . "/" . $clinic_notes_report_id . "/" . $image_name);
                    $insert_image_array = array(
                        'clinic_notes_reports_images_reports_id' => $clinic_notes_report_id,
                        'clinic_notes_reports_images_url' => $reports_images_url,
                        'clinic_notes_reports_images_size' => get_file_size(get_file_full_path($reports_images_url)),
                        'clinic_notes_reports_images_type' => $image_type,
                        'clinic_notes_reports_images_created_at' => $this->utc_time_formated
                    );
                    $this->Common_model->insert(TBL_CLINICAL_NOTES_REPORT_IMAGE, $insert_image_array);
                }
            }


            $get_clinic_report_detail = $this->User_model->get_clinic_report_detail($clinic_data);
            delete_past_prescription($appointment_id);
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
     */
    public function add_prescription_post() {
        try {
            $clinic_id = !empty($this->Common_model->escape_data($this->post_data['clinic_id'])) ? trim($this->Common_model->escape_data($this->post_data['clinic_id'])) : '';
            $doctor_id = !empty($this->Common_model->escape_data($this->post_data['doctor_id'])) ? trim($this->Common_model->escape_data($this->post_data['doctor_id'])) : '';
            $patient_id = !empty($this->Common_model->escape_data($this->post_data['patient_id'])) ? trim($this->Common_model->escape_data($this->post_data['patient_id'])) : '';
            $appointment_id = !empty($this->Common_model->escape_data($this->post_data['appointment_id'])) ? trim($this->Common_model->escape_data($this->post_data['appointment_id'])) : '';
            $date = !empty($this->Common_model->escape_data($this->post_data['date'])) ? trim($this->Common_model->escape_data($this->post_data['date'])) : '';
            $user_type = !empty($this->Common_model->escape_data($this->post_data['user_type'])) ? trim($this->Common_model->escape_data($this->post_data['user_type'])) : '';
            $diet_instruction = !empty($this->post_data['diet_instruction']) ? trim($this->post_data['diet_instruction']) : '';
            $next_follow_up = !empty($this->Common_model->escape_data($this->post_data['next_follow_up'])) ? trim($this->Common_model->escape_data($this->post_data['next_follow_up'])) : '';
            $is_capture_compliance = !empty($this->Common_model->escape_data($this->post_data['is_capture_compliance'])) ? 1 : 0;
            $diet_instruction_id = !empty($this->Common_model->escape_data($this->post_data['diet_instruction_id'])) ? trim($this->Common_model->escape_data($this->post_data['diet_instruction_id'])) : "";
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
            $global_instruction_data = [];
            foreach ($drug_request_json as $drug) {
                if(!empty($drug['frequency_instruction'])) {
                    $global_instruction_data[strtolower($drug['frequency_instruction'])] = $drug['frequency_instruction'];
                }
                if(!empty($drug['intake_instruction'])) {
                    $global_instruction_data[strtolower($drug['intake_instruction'])] = $drug['intake_instruction'];
                }
            }
            if(!empty($global_instruction_data)) {
                $where = ['doctor_id' => $doctor_id, 'search' => $global_instruction_data, 'type' => 2];
                $instruction_exists = $this->Common_model->get_instruction_exists($where);
                $translate_arr = [];
                foreach ($instruction_exists as $key => $value) {
                    if(!empty($value->translation_lang_id)) {
                        $translate_arr[$value->diet_instruction][] = [
                            'language_id' => $value->translation_lang_id,
                            'translation_text' => $value->translation_text,
                            'note_id' => $value->id
                        ];
                    }
                }
                $instruction_arr_exists = array_column($instruction_exists, 'id','diet_instruction');
                $insert_instruction = [];
                foreach ($global_instruction_data as $key => $value) {
                    if(empty($instruction_arr_exists[strtolower($value)])) {
                        $insert_instruction[] = [
                            "diet_instruction" => $value,
                            "doctor_id" => $doctor_id,
                            "instruction_type" => 2,
                            "created_at" => $this->utc_time_formated
                        ];
                    }
                }
            }
            $this->db->trans_start();
			$flgSendRxEmail = false;
            foreach ($drug_request_json as $drug) {
                if(!empty($drug) && !empty($drug['id']) && $drug['is_delete'] == 2 && $edit_permission == 1){
                    $frequency_id = $drug['frequency_id'];
                    $update_drug_data = array(
                        'prescription_user_id' => $patient_id,
                        'prescription_drug_id' => $drug['drug_id'],
                        'prescription_date' => $date,
                        'prescription_drug_name' => $drug['drug_name'],
						'prescription_drug_name_with_unit' => $drug['drug_name_with_unit'],
                        'prescription_generic_id' => $drug['generic_id'],
                        'prescription_unit_id' => $drug['unit_id'],
                        'prescription_unit_value' => $drug['unit_value'],
                        'prescription_frequency_id' => $frequency_id,
                        'prescription_frequency_value' => $drug['frequency_value'],
                        'prescription_frequency_instruction' => $drug['frequency_instruction'],
                        'prescription_frequency_instruction_json' => !empty($translate_arr[strtolower($drug['frequency_instruction'])]) ? json_encode($translate_arr[strtolower($drug['frequency_instruction'])]) : NULL,
                        'prescription_intake' => $drug['intake'],
                        'prescription_intake_instruction' => $drug['intake_instruction'],
                        'prescription_intake_instruction_json' => !empty($translate_arr[strtolower($drug['intake_instruction'])]) ? json_encode($translate_arr[strtolower($drug['intake_instruction'])]) : NULL,
                        'prescription_duration' => $drug['duration'],
                        'prescription_duration_value' => (int)$drug['duration_value'],
                        'prescription_updated_at' => $this->utc_time_formated,
                        'prescription_updated_by' => $this->user_id,
                        'prescription_share_status' => $report_share_status,
                        'prescription_dosage' => $drug['dosage'],
                        'is_capture_compliance' => $is_capture_compliance
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
                        } else if ($frequency_id == 11) {
                            $frequency = json_decode(FREQUENCY_TIMING, true)[11];
                        }

                        $reminder_where = array(
                            'reminder_prescription_report_id' => $drug['id']
                        );

                        //check if reminder exist or not
                        $get_reminder = $this->Common_model->get_single_row(TBL_REMINDERS, 'reminder_id', $reminder_where);
                        if (!empty($get_reminder)) {

                            $reminder_array = array(
                                'reminder_drug_id' 		=> $drug['drug_id'],
                                'reminder_drug_name' 	=> $drug['drug_name'],
                                'reminder_duration' 	=> $drug['duration_value'],
                                'reminder_day' 			=> $drug['duration'],
                                'reminder_modified_at' 	=> $this->utc_time_formated,
                                'reminder_timing' 		=> $frequency
                            );
                            if(is_patient_reminder_access($patient_id)) {
                                $this->Common_model->update(TBL_REMINDERS, $reminder_array, $reminder_where);
                            } else {
                                $reminder_array = array(
                                    'reminder_modified_at' => $this->utc_time_formated,
                                    'reminder_status' => 9
                                );
                                $this->Common_model->update(TBL_REMINDERS, $reminder_array, $reminder_where);
                            }
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
                                if(is_patient_reminder_access($patient_id))
                                    $this->Common_model->insert(TBL_REMINDERS, $reminder_array);
                            }
                        }
                    }
                } 
				else if (!empty($drug) && !empty($drug['id']) && $drug['is_delete'] == 1 && $delete_permission == 1) 
				{
                    $update_drug_data = array(
                        'prescription_status' => 9,
                        'prescription_updated_at' => $this->utc_time_formated,
                        'prescription_updated_by' => $this->user_id,
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
                }
				else if(!empty($drug) && empty($drug['id']) && $add_permission == 1)
				{
                    $frequency_id = $drug['frequency_id'];
                    if($drug['drug_id'] == "") {
                        $drug['drug_id'] = $this->addLocalBrand($drug, $doctor_id);
                        $drug['drug_name'] = strtoupper($drug['drug_name']);
                        $drug['drug_name_with_unit'] = strtoupper($drug['drug_name_with_unit']);
                        $is_clear_cache = true;
                    }
                    $insert_drug_array = array(
                        'prescription_user_id' => $patient_id,
                        'prescription_drug_id' => $drug['drug_id'],
                        'prescription_date' => $date,
                        'prescription_drug_name' => $drug['drug_name'],
						'prescription_drug_name_with_unit' => $drug['drug_name_with_unit'],
                        'prescription_generic_id' => $drug['generic_id'],
                        'prescription_unit_id' => $drug['unit_id'],
                        'prescription_unit_value' => $drug['unit_value'],
                        'prescription_frequency_id' => $frequency_id,
                        'prescription_frequency_value' => $drug['frequency_value'],
                        'prescription_frequency_instruction' => $drug['frequency_instruction'],
                        'prescription_frequency_instruction_json' => !empty($translate_arr[strtolower($drug['frequency_instruction'])]) ? json_encode($translate_arr[strtolower($drug['frequency_instruction'])]) : NULL,
                        'prescription_intake' => $drug['intake'],
                        'prescription_intake_instruction' => $drug['intake_instruction'],
                        'prescription_intake_instruction_json' => !empty($translate_arr[strtolower($drug['intake_instruction'])]) ? json_encode($translate_arr[strtolower($drug['intake_instruction'])]) : NULL,
                        'prescription_duration' => $drug['duration'],
                        'prescription_duration_value' => (int)$drug['duration_value'],
                        'prescription_created_at' => $this->utc_time_formated,
                        'prescription_created_by' => $this->user_id,
                        'prescription_share_status' => $report_share_status,
                        'prescription_dosage' => $drug['dosage'],
                        'is_capture_compliance' => $is_capture_compliance
                    );

                    if ($user_type == 2) {
                        $insert_drug_array['prescription_appointment_id'] = $appointment_id;
                        $insert_drug_array['prescription_doctor_user_id'] = $doctor_id;
                        $insert_drug_array['prescription_clinic_id'] = $clinic_id;
                    } else {
                        $insert_drug_array['prescription_doctor_user_id'] = $this->user_id;
                    }
                    $inserted_id = $this->Common_model->insert(TBL_PRESCRIPTION, $insert_drug_array);
					if(!empty($inserted_id))
						$flgSendRxEmail = true;
					
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
                        } else if ($frequency_id == 11) {
                            $reminder_array['reminder_timing'] = json_decode(FREQUENCY_TIMING, true)[11];
                        }
                        if(is_patient_reminder_access($patient_id))
                            $this->Common_model->insert(TBL_REMINDERS, $reminder_array);
                    }
                }
            }
            if(!empty($insert_instruction)) {
                $this->Common_model->insert_multiple('me_diet_instruction', $insert_instruction);
            }
			
			/*start Prescription mail send to patient*/
			/*if($flgSendRxEmail && is_email_communication($doctor_id)){
				if($report_share_status == 1){
					$prescription_email_data = array();
					$prescription_email_data['patient_id']     = $patient_id;
					$prescription_email_data['doctor_id']  	   = $doctor_id;
					$prescription_email_data['clinic_id']      = $clinic_id;
					$prescription_email_data['appointment_id'] = $appointment_id;
                    $cron_job_path = CRON_PATH . " cron/send_patient_prescription_mail/" . base64_encode(json_encode($prescription_email_data));
                    exec(PHP_PATH . " " . $cron_job_path . " > /dev/null &");
				}
			}*/
			/*End Prescription mail send to patient*/
	
            //check mail send to the patient for the prescription
            /*
             * code commet 
             * $track_report = array(
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
            */
			
            if (!empty($next_follow_up) || !empty($diet_instruction)) {
                //add follow up and instruction
                if(empty($diet_instruction_id) && !empty($diet_instruction)) {
                    $where = ['doctor_id' => $doctor_id, 'search' => $diet_instruction, 'type' => 1];
                    $is_instruction_exist = $this->Common_model->check_instruction_exists($where);
                    if(empty($is_instruction_exist)) {
                        $insert_instruction_array = array(
                            "diet_instruction" => $diet_instruction,
                            "doctor_id" => $doctor_id,
                            "instruction_type" => 1,
                            "created_at" => $this->utc_time_formated
                        );
                        $this->Common_model->insert('me_diet_instruction', $insert_instruction_array);
                    } else {
                        if(empty($diet_instruction_id) && !empty($is_instruction_exist[0]->id)){
                            $diet_instruction_id = $is_instruction_exist[0]->id;
                        }
                    }
                }
                if(!empty($diet_instruction_id)){
                    $diet_translate = $this->Common_model->get_all_rows('me_translation', "translation_note_id,translation_lang_id,translation_text", ['translation_note_id' => $diet_instruction_id, 'translation_status' => 1]);
                    if(!empty($diet_translate) && count($diet_translate) > 0) {
                        $translate_diet_arr = [];
                        foreach ($diet_translate as $key => $value) {
                            $translate_diet_arr[] = [
                                'language_id' => $value['translation_lang_id'],
                                'translation_text' => $value['translation_text'],
                                'note_id' => $value['translation_note_id']
                            ];
                        }
                    }
                }
                $where = array(
                    "follow_up_appointment_id" => $appointment_id,
                    "follow_up_status" => 1
                );
                $is_follow_exist = $this->Common_model->get_single_row(TBL_PRESCRIPTION_FOLLOUP, "follow_up_id", $where);
                $insert_followup_array = array(
                    "follow_up_user_id" => $patient_id,
                    "follow_up_doctor_id" => $doctor_id,
                    "follow_up_followup_date" => !empty($next_follow_up) ? $next_follow_up : NULL,
                    "follow_up_appointment_id" => $appointment_id,
                    "follow_up_status" => 1,
                    "follow_up_instruction_json" => !empty($translate_diet_arr) ? json_encode($translate_diet_arr) : NULL,
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
                //Clear cache of me_drugs table
                if(!empty($is_clear_cache)) {
                    $this->config->set_item('cache_path', APPPATH. 'cache/me_drugs/'.$doctor_id.'/');
                    $this->load->driver('cache');
                    $this->cache->file->clean();
                }
                delete_past_prescription($appointment_id);
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

    public function addLocalBrand($drug, $doctor_id) {
        $drug_data_insert = [
            'drug_user_id' => $doctor_id,
            'drug_name' => strtoupper($drug['drug_name']),
            'drug_name_with_unit' => strtoupper($drug['drug_name_with_unit']),
            'drug_drug_unit_value' => $drug['dosage'],
            'drug_drug_unit_id' => $drug['unit_id'],
            'drug_intake' => $drug['intake'],
            'drug_duration' => $drug['duration'],
            'drug_drug_frequency_id' => $drug['frequency_id'],
            'drug_frequency_value' => !empty($drug['frequency_value']) ? $drug['frequency_value'] : NULL,
            'drug_created_at' => $this->utc_time_formated,
            'drug_created_by' => $this->user_id
        ];
        $inserted_id = $this->Common_model->insert(TBL_DRUGS, $drug_data_insert);
        return $inserted_id;
    }

    /**
     * Description :- This function is used to add the prescription of the patient
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
				$prescription_drug_name_with_unit = !empty($get_prescription_detail['prescription_drug_name_with_unit']) ? $get_prescription_detail['prescription_drug_name_with_unit'] : '';
                $duration_value = !empty($get_prescription_detail['prescription_duration_value']) ? $get_prescription_detail['prescription_duration_value'] : '';
                $duration = !empty($get_prescription_detail['prescription_duration']) ? $get_prescription_detail['prescription_duration'] : '';

                $insert_drug_array = array(
                    'prescription_user_id' => $patient_id,
                    'prescription_drug_id' => $drug_id,
                    'prescription_date' => $date,
                    'prescription_drug_name' => $drug_name,
					'prescription_drug_name_with_unit' => $prescription_drug_name_with_unit,
                    'prescription_generic_id' => $get_prescription_detail['prescription_generic_id'],
                    'prescription_unit_id' => $get_prescription_detail['prescription_unit_id'],
                    'prescription_frequency_id' => $get_prescription_detail['prescription_frequency_id'],
                    'prescription_frequency_value' => $get_prescription_detail['prescription_frequency_value'],
                    'prescription_frequency_instruction' => $get_prescription_detail['prescription_frequency_instruction'],
                    'prescription_intake' => $get_prescription_detail['prescription_intake'],
                    'prescription_intake_instruction' => $get_prescription_detail['prescription_intake_instruction'],
                    'prescription_duration' => $duration,
                    'prescription_duration_value' => (int)$duration_value,
                    'prescription_diet_instruction' => $get_prescription_detail['prescription_diet_instruction'],
                    'prescription_created_at' => $this->utc_time_formated,
                    'prescription_created_by' => $this->user_id,
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
                        } else if ($frequency_id == 11) {
                            $reminder_array['reminder_timing'] = json_decode(FREQUENCY_TIMING, true)[11];
                        }
                        if(is_patient_reminder_access($patient_id))
                            $reminder_inserted_id = $this->Common_model->insert(TBL_REMINDERS, $reminder_array);
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
			$drug_name_with_unit = !empty($this->Common_model->escape_data($this->post_data['drug_name_with_unit'])) ? trim($this->Common_model->escape_data($this->post_data['drug_name_with_unit'])) : $drug_name;
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
				'prescription_drug_name_with_unit' => $drug_name_with_unit,
                'prescription_generic_id' => $generic_id,
                'prescription_unit_id' => $unit_id,
                'prescription_frequency_id' => $frequency_id,
                'prescription_frequency_value' => $frequency_value,
                'prescription_frequency_instruction' => $frequency_instruction,
                'prescription_intake' => $intake,
                'prescription_intake_instruction' => $intake_instruction,
                'prescription_duration' => $duration,
                'prescription_duration_value' => (int)$duration_value,
                'prescription_diet_instruction' => $diet_instruction,
                'prescription_updated_at' => $this->utc_time_formated,
                'prescription_updated_by' => $this->user_id,
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
                } else if ($frequency_id == 11) {
                    $reminder_array['reminder_timing'] = json_decode(FREQUENCY_TIMING, true)[11];
                }

                //check reminder exists or not
                $reminder_where = array(
                    'reminder_prescription_report_id' => $prescription_id,
                    'reminder_status' => 1
                );
                $get_reminder = $this->Common_model->get_single_row(TBL_REMINDERS, 'reminder_id', $reminder_where);

                if (!empty($get_reminder)) {
                    if(is_patient_reminder_access($patient_id)) {
                        $reminder_is_update = $this->Common_model->update(TBL_REMINDERS, $reminder_array, $reminder_where);
                    } else {
                        $reminder_array = array(
                            'reminder_modified_at' => $this->utc_time_formated,
                            'reminder_status' => 9
                        );
                        $reminder_is_update = $this->Common_model->update(TBL_REMINDERS, $reminder_array, $reminder_where);
                    }
                } else {
                    if ($need_to_insert == 1 && is_patient_reminder_access($patient_id)) {
                        $reminder_is_update = $this->Common_model->insert(TBL_REMINDERS, $reminder_array);
                    }
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
                'prescription_updated_by' => $this->user_id,
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
            $lab_test_name_other = !empty($this->post_data['lab_test_name_other']) ? $this->post_data['lab_test_name_other'] : '';
            $instruction = !empty($this->Common_model->escape_data($this->post_data['instruction'])) ? trim($this->Common_model->escape_data($this->post_data['instruction'])) : '';
            
            $othertestdata = array_keys(json_decode($lab_test_name_other, true));
            if(!empty($othertestdata[0])){
                $lab_test_name = json_encode(array_merge(json_decode($lab_test_name, true),json_decode($lab_test_name_other, true)));
            }
            
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
                'lab_report_created_by' => $this->user_id
            );

            if ($user_type == 2) {
                $insert_investigation['lab_report_appointment_id'] = $appointment_id;
                $insert_investigation['lab_report_doctor_user_id'] = $doctor_id;
                $insert_investigation['lab_report_clinic_id'] = $clinic_id;
            } else {
                $insert_investigation['lab_report_doctor_user_id'] = $this->user_id;
            }

            $inserted_id = $this->Common_model->insert(TBL_LAB_REPORTS, $insert_investigation);
            $this->add_investigation_instruction([
                'lab_test_name' => $lab_test_name,
                'doctor_id' => $doctor_id,
                'user_id' => $this->user_id
            ]);
            if ($inserted_id > 0) {
                /*START - data save in reminder when doctor add investing lab report*/
                $labTestName = json_decode($lab_test_name,true); 
                $allLabTestName = implode(", ", array_keys($labTestName));
                $reminder_start_date = date('Y-m-d', strtotime($date . ' +1 day'));
                $doctor_data = $this->User_model->get_details_by_id($doctor_id);
                $user_first_name = $doctor_data['user_first_name'];
                $user_last_name = $doctor_data['user_last_name'];
                
                $reminder_array = array(
                                'reminder_appointment_id' => $appointment_id,
                                'reminder_lab_report_id' => $inserted_id,
                                'reminder_type' => 3,
                                'reminder_doctor_name'=> DOCTOR . $user_first_name.' '.$user_last_name,
                                'reminder_user_id' => $patient_id,
                                'reminder_created_by' => $doctor_id,
                                'reminder_doctor_id' => $doctor_id,
                                'reminder_lab_report_name' => $allLabTestName,
                                'reminder_start_date' => $reminder_start_date,
                                'reminder_created_at' => $this->utc_time_formated,
                            );
                $reminder_array['reminder_timing'] = json_decode(FREQUENCY_TIMING, true)[1];
                if(is_patient_reminder_access($patient_id))
                    $this->Common_model->insert(TBL_REMINDERS, $reminder_array);
                /*END - data save in reminder when doctor add investing lab report*/
                
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
                    $send_doctor_name = '';
                    if (!empty($get_doctor_name['user_first_name'])) {
                        $send_doctor_name = DOCTOR . $get_doctor_name['user_first_name'] . ' ' . $get_doctor_name['user_last_name'];
                    }
                    $getclinicDetail = $this->Clinic_model->get_clinic_detail($clinic_id);
                    $clinic_name = isset($getclinicDetail['clinic_name']) ? $getclinicDetail['clinic_name'] : '';
        
                    $send_mail = array(
                        'user_name' => $send_user_name,
                        'user_email' => $get_user_name['user_email'],
                        'doctor_name' => $send_doctor_name,
                        'clinic_name' => $clinic_name,
                        'template_id' => 26
                    );
                    /*if(is_email_communication($doctor_id)) {
                        $cron_job_path = CRON_PATH . " notification/send_mail_background/" . base64_encode(json_encode($send_mail));
                        exec(PHP_PATH . " " . $cron_job_path . " > /dev/null &");
                    }*/
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
                delete_past_prescription($appointment_id);
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

    public function add_investigation_instruction($data) {
        $health_analytics_test_name = array_keys(json_decode($data['lab_test_name'], true));
        foreach ($health_analytics_test_name as $key => $value) {
            $health_analytics_test_name[$key] = trim(strtolower($value));
        }
        $data['health_analytics_test_name'] = $health_analytics_test_name;
        $result = $this->User_model->check_investigation_exist($data);
        $diff_arr = array_diff($health_analytics_test_name, array_column($result, 'health_analytics_test_name'));
        $new_investigation = array_values($diff_arr);
        if(!empty($new_investigation)){
            $health_analytics_test_data = [];
            foreach ($new_investigation as $key => $value) {
                $health_analytics_test_data[] = array(
                    'health_analytics_test_doctor_id' => $data['doctor_id'],
                    'health_analytics_test_name' => strtoupper($value),
                    'health_analytics_test_name_precise' => strtoupper($value),
                    'health_analytics_test_parent_id' => 0,
                    'health_analytics_test_created_at' => $this->utc_time_formated,
                    'health_analytics_test_status' => 1,
                    'health_analytics_test_type' => 2,
                    'health_analytics_test_created_by' => $data['user_id']
                );
            }
            $this->Common_model->insert_multiple('me_health_analytics_test', $health_analytics_test_data);
        }
        $lab_test_name = json_decode($data['lab_test_name'], true);
        $instructions = array_filter($lab_test_name);
        if(!empty($instructions)){
            $result = $this->User_model->check_investigation_exist($data);
            $analytics_ids = array_column($result, 'health_analytics_test_id','health_analytics_test_name');
            $instruction_where = [];
            foreach ($instructions as $key => $value) {
                if(!empty($analytics_ids[strtolower($key)])) {
                    $instruction_where[] = ['instruction' => $value, 'health_analytics_test_id' => $analytics_ids[strtolower($key)]];
                }
            }
            if(!empty($instruction_where)){
                $result = $this->User_model->check_investigation_instruction_exist($data,$instruction_where);
                $instruction_diff = check_diff_multi($instruction_where, $result);
                $instruction_diff = array_values(array_filter($instruction_diff));
                if(!empty($instruction_diff)) {
                    $investigation_instructions_data = [];
                    foreach ($instruction_diff as $key => $value) {
                        $investigation_instructions_data[] = [
                            'health_analytics_test_id' => $value['health_analytics_test_id'],
                            'instruction' => $value['instruction'],
                            'doctor_id' => $data['doctor_id'],
                            'created_by' => $data['user_id'],
                            'created_at' => $this->utc_time_formated,
                        ];
                    }
                    $this->Common_model->insert_multiple('me_investigation_instructions', $investigation_instructions_data);
                }
            }
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
            $lab_test_name_other = !empty($this->post_data['lab_test_name_other']) ? $this->post_data['lab_test_name_other'] : '';
            $instruction = !empty($this->Common_model->escape_data($this->post_data['instruction'])) ? trim($this->Common_model->escape_data($this->post_data['instruction'])) : '';
            
            $othertestdata = array_keys(json_decode($lab_test_name_other, true));
            if(!empty($othertestdata[0])){
                $lab_test_name = json_encode(array_merge(json_decode($lab_test_name, true),json_decode($lab_test_name_other, true)));
            }
			
			if(!empty($lab_test_name)){
				$lab_test_name_decoded = json_decode($lab_test_name, true);
				$lab_test_name = json_encode(array_change_key_case($lab_test_name_decoded, CASE_UPPER));
			}
			
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
                'lab_report_updated_by' => $this->user_id
            );

            $update_investigation_where = array(
                'lab_report_id' => $investigation_id
            );

            $is_update = $this->Common_model->update(TBL_LAB_REPORTS, $update_investigation_data, $update_investigation_where);
            $this->add_investigation_instruction([
                'lab_test_name' => $lab_test_name,
                'doctor_id' => $doctor_id,
                'user_id' => $this->user_id
            ]);
            if ($is_update > 0) {
                 //*Start reminder data update/add process (02/12/2018)*/
                $labTestName = json_decode($lab_test_name,true); 
                $allLabTestName = implode(", ", array_keys($labTestName));
                $reminder_start_date = date('Y-m-d', strtotime($date . ' +1 day'));
                $doctor_data = $this->User_model->get_details_by_id($doctor_id);
                $user_first_name = $doctor_data['user_first_name'];
                $user_last_name = $doctor_data['user_last_name'];
                
                $reminder_array = array(
                                'reminder_appointment_id' => $appointment_id,
                                'reminder_lab_report_id' => $investigation_id,
                                'reminder_type' => 3,
                                'reminder_doctor_name'=>DOCTOR . $user_first_name.' '. $user_last_name,
								'reminder_doctor_id' => $doctor_id,
                                'reminder_user_id' => $patient_id,
                                'reminder_created_by' => $doctor_id,
                                'reminder_lab_report_name' => $allLabTestName,
                                'reminder_start_date' => $reminder_start_date,
                                'reminder_created_at' => $this->utc_time_formated,
                            );
                $reminder_array['reminder_timing'] = json_decode(FREQUENCY_TIMING, true)[1];
                
		$reminder_where = array(
                    'reminder_lab_report_id' => $investigation_id,
                    'reminder_status' => 1
                );
                
                $get_reminder = $this->Common_model->get_single_row(TBL_REMINDERS, 'reminder_id', $reminder_where);
                
                if (!empty($get_reminder)) {
                    if(is_patient_reminder_access($patient_id)) {
                        $reminder_is_update = $this->Common_model->update(TBL_REMINDERS, $reminder_array, $reminder_where);
                    } else {
                        $reminder_array = array(
                            'reminder_modified_at' => $this->utc_time_formated,
                            'reminder_status' => 9
                        );
                        $reminder_is_update = $this->Common_model->update(TBL_REMINDERS, $reminder_array, $reminder_where);
                    }
                } else {
                    if(is_patient_reminder_access($patient_id))
                        $reminder_is_update = $this->Common_model->insert(TBL_REMINDERS, $reminder_array);
                }
                /*End reminder data update/add process (02/12/2018)*/
                
                //get prescription detail
                $requested_data = array(
                    'lab_report_id' => $investigation_id
                );
                $get_lab_report_detail = $this->User_model->get_investigation_detail($requested_data);
                delete_past_prescription($appointment_id);
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
                'procedure_report_created_by' => $this->user_id
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
                delete_past_prescription($appointment_id);
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
                'procedure_report_updated_by' => $this->user_id
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
                delete_past_prescription($appointment_id);
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
            $user_id = !empty($this->Common_model->escape_data($this->post_data['user_id'])) ? trim($this->Common_model->escape_data($this->post_data['user_id'])) : '';
            
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
                /*get doctor clinic mapping role id*/
                $clinic_mapping_where = array(
                    'doctor_clinic_mapping_user_id' => $user_id,
                    'doctor_clinic_mapping_status' => 1,
                );
                
                $doctor_clinic_mapping_data = $this->Common_model->get_single_row(TBL_DOCTOR_CLINIC_MAPPING, 'doctor_clinic_mapping_role_id', $clinic_mapping_where);
                $doctor_clinic_mapping_role_id = isset($doctor_clinic_mapping_data['doctor_clinic_mapping_role_id']) ? $doctor_clinic_mapping_data['doctor_clinic_mapping_role_id'] : '';
                
                $patient_detail_params = array(
                    'key' => $key,
                    'appointment_id' => $appointment_id,
                    'clinic_id' => $clinic_id,
                    'doctor_id' => $doctor_id,
                    'date' => $date,
                    'patient_id' => $patient_id,
                    'user_type' => 2,
                    'doctor_clinic_mapping_role_id'=> $doctor_clinic_mapping_role_id,
                    'user_id'=> $user_id
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

            if ($detail_found == 1) {
                //get followup date
                $this->my_response['status'] = true;
                $this->my_response['message'] = lang('common_detail_found');
                $this->my_response['data'] = $get_patient_report_detail;
                $this->my_response['is_capture_compliance'] = !empty($get_patient_report_detail[0]['is_capture_compliance']) ? true : false;
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
    public function add_health_analytics_post(){
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
					'health_analytics_report_date' => $date,
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
                        'patient_analytics_updated_by' => $this->user_id,
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
                            'patient_analytics_created_at' => $this->utc_time_formated,
                            'patient_analytics_created_by' => $this->user_id
                        );
                    }
                }

                if (!empty($store_health_analytics)) {
                    $this->Common_model->insert_multiple(TBL_PATIENT_ANALYTICS, $store_health_analytics);
                }
            }

            //get the user gender
            $gender = $this->Common_model->get_single_row(TBL_USERS, 'user_gender', array('user_id' => $patient_id));

            $is_valid = 1;
            $health_analytics_data = json_decode($health_analytics_data, true);

            foreach ($health_analytics_data as &$health_data) {
                unset($health_data['health_analytics_test_validation']);
                
                if (!empty($health_data['value'])) {
                    
                    $health_data['value'] = number_format((float) $health_data['value'], 2, '.', '');
                    
                    $requested_valid = array(
                        'value' => $health_data['value'],
                        'id' => $health_data['id'],
                        'gender' => $gender['user_gender']
                    );
                    $is_data_valid = $this->is_valid_analytics($requested_valid);
                    if ($is_data_valid == 2) {
                        $is_valid = 2;
                        break;
                    }
                }
            }
			
            if ($is_valid == 2) {
                $this->my_response['status'] = false;
                $this->my_response['message'] = "Invalid analytics value";
                $this->send_response();
            }

            $insert_health_analytics = array(
                'health_analytics_report_user_id' => $patient_id,
                'health_analytics_report_date' => $date,
                'health_analytics_report_data' => json_encode($health_analytics_data),
                'health_analytics_report_created_at' => $this->utc_time_formated,
                'health_analytics_report_created_by' => $this->user_id,
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
                        'patient_analytics_updated_by' => $this->user_id,
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
                        'patient_analytics_created_at' => $this->utc_time_formated,
                        'patient_analytics_created_by' => $this->user_id
                    );
                }
            }

            if (!empty($store_health_analytics)) {
                $this->Common_model->insert_multiple(TBL_PATIENT_ANALYTICS, $store_health_analytics);
            }

            //get the user gender
            $gender = $this->Common_model->get_single_row(TBL_USERS, 'user_gender', array('user_id' => $patient_id));

            $is_valid = 1;
            $health_analytics_data = json_decode($health_analytics_data, true);
            foreach ($health_analytics_data as &$health_data) {
                unset($health_data['health_analytics_test_validation']);
                if (!empty($health_data['value'])) {
                    
                    $health_data['value'] = number_format((float) $health_data['value'], 2, '.', '');
                    
                    $requested_valid = array(
                        'value' => $health_data['value'],
                        'id' => $health_data['id'],
                        'gender' => $gender['user_gender']
                    );
                    $is_data_valid = $this->is_valid_analytics($requested_valid);
                    
                    if ($is_data_valid == 2) {
                        $is_valid = 2;
                        break;
                    }
                }
            }

            if ($is_valid == 2) {
                $this->my_response['status'] = false;
                $this->my_response['message'] = "Invalid analytics value";
                $this->send_response();
            }

            $update_health_analytics_data = array(
                'health_analytics_report_date' => $date,
                'health_analytics_report_data' => json_encode($health_analytics_data),
                'health_analytics_report_updated_at' => $this->utc_time_formated,
                'health_analytics_report_updated_by' => $this->user_id,
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

    public function is_valid_analytics($requested_data) {

        //get health analytics validation
        $get_validation = $this->Common_model->get_single_row(TBL_HEALTH_ANALYTICS, 'health_analytics_test_validation', array('health_analytics_test_id' => $requested_data['id']));
		
        if (!empty($get_validation)) {
            $value = $requested_data['value'];
            $validation = json_decode($get_validation['health_analytics_test_validation'], true);
            $gender = $requested_data['gender'];

            if ($validation['validation']['type'] == 'numeric') {
                if ($gender == '' || empty($validation['validation']['gender'])) {

                    $min = $validation['validation']['min'];
                    $max = $validation['validation']['max'];
                    if ($value < $min || $value > $max) {
                        return 2;
                    } else {
                        return 1;
                    }
                } else {

                    $fmin = $validation['validation']['gender']['female']['min'];
                    $fmax = $validation['validation']['gender']['female']['max'];
					
                    $mmin = $validation['validation']['gender']['male']['min'];
                    $mmax = $validation['validation']['gender']['male']['max'];

                    if ($gender == 'female') {
                        if ($value < $fmin || $value > $fmax) {
                            return 2;
                        } else {
                            return 1;
                        }
                    } else {
                        if ($value < $mmin || $value > $mmax) {
                            return 2;
                        } else {
                            return 1;
                        }
                    }
                }
            } else {
                return 1;
            }
        }
        return 2;
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
			
			$patient_previous_health_analytic = !empty($this->post_data['patient_previous_health_analytic']) ? trim($this->Common_model->escape_data($this->post_data['patient_previous_health_analytic'])) : '';
			$appointment_id = !empty($this->post_data['appointment_id']) ? trim($this->Common_model->escape_data($this->post_data['appointment_id'])) : '';

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
                'user_id'   => $patient_id,
                "page" 		=> $page,
                "per_page"  => $per_page,
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
                $health_data['logged_in'] = $doctor_id; //$this->user_id;
				
				if(!empty($patient_previous_health_analytic)){
					$health_data['isPatientPreviousHealthAnalytic'] = $patient_previous_health_analytic;
					$health_data['health_analytics_report_appointment_id'] = $appointment_id;
					$get_health_analytics_data = $this->User_model->get_health_analytics_detail($health_data);
				}else{
					$get_health_analytics_data = $this->User_model->get_health_analytics_detail_with_group_date($health_data);
                    $health_analytics_report_date_arr = array_column($get_health_analytics_data['data'], "health_analytics_report_date");
                    if(!empty($get_health_analytics_data['data'])) {
                        $last_row_date = date("Y-m-d",strtotime("-1 days", strtotime(end($health_analytics_report_date_arr))));
                        $first_row_date = $get_health_analytics_data['data'][0]['health_analytics_report_date'];
                    }
                    $group_by_data_arr = [];
                    foreach ($get_health_analytics_data['data'] as $key => $value) {
                        $analytics_data = json_decode($value['health_analytics_report_data']);
                        if(!empty($analytics_data) && is_array($analytics_data)) {
                            $analytic_arr_key = array_search(308, array_column($analytics_data, 'id'));
                            if(is_numeric($analytic_arr_key)) {
                                $analytics_data[$analytic_arr_key]->value = '';
                                if(count($analytics_data) > 1) {
                                    $get_health_analytics_data['data'][$key]['health_analytics_report_data'] = json_encode($analytics_data);
                                } else {
                                    unset($get_health_analytics_data['data'][$key]);
                                }
                            }
                        }
                        $group_by_fields = $value['health_analytics_report_user_id'].$value['health_analytics_report_doctor_user_id'].$value['health_analytics_report_appointment_id'].$value['health_analytics_report_date'];
                        if(!empty($group_by_data_arr[$group_by_fields])) {
                            $report_analytic_id = $group_by_data_arr[$group_by_fields];
                            $analytics_key = array_search($report_analytic_id, array_column($get_health_analytics_data['data'], 'health_analytics_report_id'));
                            if(!empty($get_health_analytics_data['data'][$analytics_key])) {
                                $get_health_analytics_data['data'][$analytics_key]['health_analytics_report_data'] = $get_health_analytics_data['data'][$analytics_key]['health_analytics_report_data'].'$@@$'.$value['health_analytics_report_data'];
                                unset($get_health_analytics_data['data'][$key]);
                                continue;
                            }
                        }
                        if(!empty($value['health_analytics_report_id']))
                            $group_by_data_arr[$group_by_fields] = $value['health_analytics_report_id'];
                    }
                    // print_r($get_health_analytics_data['data']);die;
                    $get_health_analytics_data['data'] = array_values($get_health_analytics_data['data']);
                    $where = ['patient_id' => $patient_id];
                    $result = $this->Patient_model->get_uas7_para_data($where);
                    $get_health_analytics_test = $this->Common_model->get_single_row('me_health_analytics_test', 'health_analytics_test_id, health_analytics_test_id id,health_analytics_test_name name,health_analytics_test_name_precise precise_name', array('health_analytics_test_id' => 308));
                    $uas7_weekly_data = [];
                    $uas7_score = 0;
                    foreach ($result as $key => $value) {
                        $value_arr = explode(",", $value->patient_diary_value);
                        $uas7_score += array_sum($value_arr);
                        if(($key+1)%7 == 0) {
                            $uas7_weekly_data[] = [
                                'patient_diary_date' => date("d/m/Y", strtotime($result[($key+1)-7]->patient_diary_date)),
                                'uas7_score' => $uas7_score,
                            ];
                            $arr_key = array_search($result[($key+1)-7]->patient_diary_date, array_column($get_health_analytics_data['data'], 'health_analytics_report_date'));
                            if(is_numeric($arr_key) && !empty($get_health_analytics_data['data'][$arr_key]['health_analytics_report_data'])) {
                                $analytics_data = json_decode($get_health_analytics_data['data'][$arr_key]['health_analytics_report_data']);
                                $analytic_arr_key = array_search(308, array_column($analytics_data, 'id'));
                                if(is_numeric($analytic_arr_key) && !empty($analytics_data[$analytic_arr_key])) {
                                    $analytics_data[$analytic_arr_key]->value = $uas7_score;
                                    // print_r($analytics_data[$analytic_arr_key]);
                                    $get_health_analytics_data['data'][$arr_key]['health_analytics_report_data'] = json_encode($analytics_data);

                                }
                            } else {
                                if(!empty($health_analytics_report_date_arr) && $last_row_date <= $result[($key+1)-7]->patient_diary_date && $first_row_date >= $result[($key+1)-7]->patient_diary_date) {
                                    $get_health_analytics_test['value'] = $uas7_score;
                                    $new_arr = [
                                        'health_analytics_report_id' => '',
                                        'health_analytics_report_user_id' => '',
                                        'health_analytics_report_doctor_user_id' => '',
                                        'health_analytics_report_appointment_id' => '',
                                        'health_analytics_report_clinic_id' => '',
                                        'health_analytics_report_date' => $result[($key+1)-7]->patient_diary_date,
                                        'health_analytics_report_data' => json_encode([$get_health_analytics_test]),
                                        'patient_first_name' => '',
                                        'patient_last_name' => '',
                                        'user_patient_name' => '',
                                        'user_first_name' => '',
                                        'user_last_name' => '',
                                        'user_type' => '',
                                        'user_name' => '',
                                    ];
                                    $get_health_analytics_data['data'][] = $new_arr;
                                }
                            }
                            $uas7_score = 0;
                        }
                    }
                    function sort_by_date($a, $b) {
                        $t1 = strtotime($a['health_analytics_report_date']);
                        $t2 = strtotime($b['health_analytics_report_date']);
                        return $t2 - $t1;
                    }    
                    usort($get_health_analytics_data['data'], 'sort_by_date');
				}
            } else {
                $health_data['flag'] = '';
                $health_data['logged_in'] = $this->user_id;
                $get_health_analytics_data = $this->User_model->get_health_analytics_detail($health_data);
            }
			
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
			
            $where = array('patient_analytics_user_id' => $patient_id);
            if ($user_type == 1) {
                $where['patient_analytics_status'] = 1;
            }

            $columns = 'patient_analytics_analytics_id, 
                        patient_analytics_doctor_id,
                        patient_analytics_name,
                        patient_analytics_name_precise,
                        health_analytics_test_validation';

            $left_join = array(
                TBL_HEALTH_ANALYTICS => "patient_analytics_analytics_id = health_analytics_test_id"
            );

            $get_health_analytics_data = $this->Common_model->get_all_rows(TBL_PATIENT_ANALYTICS, $columns, $where, $left_join, array(), '', '', array(), 'patient_analytics_name');
            foreach ($get_health_analytics_data as $key => $value) {
                $validation_data = json_decode($value['health_analytics_test_validation']);
                $get_health_analytics_data[$key]['min'] = isset($validation_data->normal_range->min) ? (float) $validation_data->normal_range->min : '';
                $get_health_analytics_data[$key]['max'] = isset($validation_data->normal_range->max) ? (float) $validation_data->normal_range->max : '';
                if(!empty($validation_data->all_range)){
                    // print_r($validation_data->all_range);
                    $get_health_analytics_data[$key]['range'] = '';
                    foreach ($validation_data->all_range as $range) {
                        $get_health_analytics_data[$key]['range'] .= $range->dataLabel."\n";
                    }
                    $get_health_analytics_data[$key]['range'] = trim($get_health_analytics_data[$key]['range']);
                }
            }
            // die;
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
                'vital_report_updated_by' => $this->user_id,
                'vital_report_status' => 9
            );
            $this->Common_model->update(TBL_VITAL_REPORTS, $vital_update_data, $vital_where_data);

            $clinical_where_data = array(
                'clinical_notes_reports_appointment_id' => $new_appointment_id
            );
            $clinical_update_data = array(
                'clinical_notes_reports_updated_at' => $this->utc_time_formated,
                'clinical_notes_reports_updated_by' => $this->user_id,
                'clinical_notes_reports_status' => 9
            );
            $this->Common_model->update(TBL_CLINICAL_NOTES_REPORT, $clinical_update_data, $clinical_where_data);

            $prescription_where_data = array(
                'prescription_appointment_id' => $new_appointment_id,
            );
            $prescription_update_data = array(
                'prescription_updated_at' => $this->utc_time_formated,
                'prescription_updated_by' => $this->user_id,
                'prescription_status' => 9
            );
            $this->Common_model->update(TBL_PRESCRIPTION_REPORTS, $prescription_update_data, $prescription_where_data);

            $investigation_where_data = array(
                'lab_report_appointment_id' => $new_appointment_id
            );

            $investigation_update_data = array(
                'lab_report_updated_at' => $this->utc_time_formated,
                'lab_report_updated_by' => $this->user_id,
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
                    'clinical_notes_reports_created_at' => $this->utc_time_formated,
                    'clinical_notes_reports_created_by' => $this->user_id,
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
                    'lab_report_created_at' => $this->utc_time_formated,
                    'lab_report_created_by' => $this->user_id
                );
                $this->Common_model->insert(TBL_LAB_REPORTS, $insert_investigaion);
            }

            $add_prescription = array();
            if (!empty($prescription)) {

                $is_data_contains = 1;
                foreach ($prescription as $single_prescription) {
                    if($single_prescription['drug_status'] == 9)
                        continue;
                    
                    $add_prescription[] = array(
                        'prescription_user_id' => $single_prescription['prescription_user_id'],
                        'prescription_doctor_user_id' => $single_prescription['prescription_doctor_user_id'],
                        'prescription_appointment_id' => $new_appointment_id,
                        'prescription_drug_id' => $single_prescription['prescription_drug_id'],
                        'prescription_drug_name' => $single_prescription['prescription_drug_name'],
						'prescription_drug_name_with_unit' => $single_prescription['prescription_drug_name_with_unit'],
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
                        'prescription_duration_value' => (int)$single_prescription['prescription_duration_value'],
                        'prescription_diet_instruction' => $single_prescription['prescription_diet_instruction'],
                        'prescription_share_status' => $prescription_report_share_status,
                        'prescription_created_at' => $this->utc_time_formated,
                        'prescription_created_by' => $this->user_id
                    );
                }
                $this->Common_model->insert_multiple(TBL_PRESCRIPTION_REPORTS, $add_prescription);
            }
            delete_past_prescription($new_appointment_id);

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
                'clinical_notes_reports_updated_by' => $this->user_id,
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
                'prescription_updated_by' => $this->user_id,
                'prescription_status' => 9
            );
            $this->Common_model->update(TBL_PRESCRIPTION_REPORTS, $prescription_update_data, $prescription_where_data);

            $investigation_where_data = array(
                'lab_report_appointment_id' => $appointment_id
            );

            $investigation_update_data = array(
                'lab_report_updated_at' => $this->utc_time_formated,
                'lab_report_updated_by' => $this->user_id,
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
                $clinical_data['logged_in'] = $doctor_id;

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

    public function get_my_procedure_report_post() {
        try {
            $doctor_id = !empty($this->Common_model->escape_data($this->post_data['doctor_id'])) ? trim($this->Common_model->escape_data($this->post_data['doctor_id'])) : 1;
            if (empty($doctor_id)) {
                $this->bad_request();
            }
            $where_data = array(
                'doctor_id' => $doctor_id,
            );
            $get_my_proc_data = $this->User_model->get_my_procedure_report($where_data);
            if (!empty($get_my_proc_data)) {
                $my_procedure = [];
                foreach ($get_my_proc_data as $key => $value) {
                    $my_procedure = array_merge($my_procedure,json_decode($value->procedure_report_procedure_text, true));
                }
                $my_procedure = array_map('trim', $my_procedure);
                $my_procedure = array_values(array_unique($my_procedure));
                sort($my_procedure, SORT_NATURAL | SORT_FLAG_CASE);
                $this->my_response['status'] = true;
                $this->my_response['message'] = lang('common_detail_found');
                $this->my_response['data'] = $my_procedure;
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
                $procedure_data['logged_in'] = $doctor_id;

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
                $lab_report_data['logged_in'] = $doctor_id;

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
            $is_all_data = !empty($this->Common_model->escape_data($this->post_data['is_all_data'])) ? trim($this->Common_model->escape_data($this->post_data['is_all_data'])) : "";
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
                'is_all_data' => $is_all_data,
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
                $prescription_data['logged_in'] = $doctor_id;

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
                'clinical_notes_reports_updated_at' => $this->utc_time_formated,
                'clinical_notes_reports_updated_by' => $this->user_id
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
                                    CONCAT('".DOCTOR."',' ',user_first_name,' ',user_last_name) AS user_name,
                                    CONCAT(user_first_name,' ',user_last_name) AS user_search,
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
                                    user_type = 2 AND 
                                    doctor_clinic_mapping_role_id = 1";

            if (!empty($search)) {
                $get_doctors_sql .= " AND ( 
                                            CONCAT(user_first_name,' ',user_last_name) LIKE '%" . $search . "%'  OR
                                            user_phone_number LIKE '%" . $search . "%' OR    
                                            LOWER(user_unique_id) LIKE '%" . strtolower($search) . "%'    
                                          ) ";
            }
            $get_doctors_sql .= " GROUP BY user_id ";
            $get_doctors_sql .= " LIMIT 0, 50 ";

            $get_doctors = $this->Common_model->get_all_rows_by_query($get_doctors_sql);

            if (!empty($get_doctors)) {
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
                'vital_report_updated_by' => $this->user_id,
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
                'file_report_updated_by_user_id' => $this->user_id,
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

	public function manage_previous_patient_health_analytics_post(){
        try {
            $clinic_id = !empty($this->Common_model->escape_data($this->post_data['clinic_id'])) ? trim($this->Common_model->escape_data($this->post_data['clinic_id'])) : '';
            $doctor_id = !empty($this->Common_model->escape_data($this->post_data['doctor_id'])) ? trim($this->Common_model->escape_data($this->post_data['doctor_id'])) : '';
            $patient_id = !empty($this->Common_model->escape_data($this->post_data['patient_id'])) ? trim($this->Common_model->escape_data($this->post_data['patient_id'])) : '';
            $appointment_id = !empty($this->Common_model->escape_data($this->post_data['appointment_id'])) ? trim($this->Common_model->escape_data($this->post_data['appointment_id'])) : '';
            $user_type = !empty($this->Common_model->escape_data($this->post_data['user_type'])) ? trim($this->Common_model->escape_data($this->post_data['user_type'])) : '';
            $health_analytics_data = !empty($this->post_data['health_analytics_data']) ? $this->post_data['health_analytics_data'] : '';
           
            if (empty($user_type) || empty($patient_id) || empty($health_analytics_data)) {
                $this->bad_request();
            }
			
            if ($user_type == 2 && (empty($clinic_id) || empty($doctor_id) || empty($appointment_id))) {
                $this->bad_request();
            }

            if (!in_array($user_type, $this->user_type)) {
                $this->my_response['status']  = false;
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
            }
			
			//get the user gender
            $gender = $this->Common_model->get_single_row(TBL_USERS, 'user_gender', array('user_id' => $patient_id));
            $is_valid = 1;
            $health_analytics_data = json_decode($health_analytics_data, true);
            $add_health_analytics = $update_health_analytics = [];
			$store_health_analytics = [];
			$date = '';
			$allAnalyticIds = array_unique(array_column($health_analytics_data,'id'));
			foreach ($health_analytics_data as &$health_data) {
				$date = $health_data['health_analytics_report_date'];
				unset($health_data['health_analytics_report_date']);
				unset($health_data['health_analytics_test_validation']);
				
				if(validate_date_only($date)){
					$is_valid = 2;
                    break;
				}
				
				if (!empty($health_data['value'])) {
                    $health_data['value'] = number_format((float) $health_data['value'], 2, '.', '');
                    $requested_valid = array(
                        'value' => $health_data['value'],
                        'id' => $health_data['id'],
                        'gender' => $gender['user_gender']
                    );
                    $is_data_valid = $this->is_valid_analytics($requested_valid);
                    if ($is_data_valid == 2) {
                        $is_valid = 2;
                        break;
                    }
					$health_analytics_report_id = $health_analytics_report_status = '';
					if(isset($health_data['health_analytics_report_id']) && !empty($health_data['health_analytics_report_id'])){
						$health_analytics_report_id 	= $health_data['health_analytics_report_id'];
						$health_analytics_report_status = $health_data['health_analytics_report_status'];
						unset($health_data['health_analytics_report_id']);
						unset($health_data['health_analytics_report_status']);
						
						$update_health_analytics[] =  array(
														'health_analytics_report_id'			 => $health_analytics_report_id,
														'health_analytics_report_date' 	         => $date,
														'health_analytics_report_data'           => json_encode([$health_data]),
														'health_analytics_report_updated_at'     => $this->utc_time_formated,
                                                        'health_analytics_report_updated_by'     => $this->user_id,
														'health_analytics_report_share_status'   => 1,
														'health_analytics_report_status'         => $health_analytics_report_status
												    );
					}else{
						$add_health_analytics[] = array(
														'health_analytics_report_user_id'        => $patient_id,
														'health_analytics_report_date' 	         => $date,
														'health_analytics_report_data'           => json_encode([$health_data]),
														'health_analytics_report_created_at'     => $this->utc_time_formated,
                                                        'health_analytics_report_created_by'     => $this->user_id,
														'health_analytics_report_share_status'   => 1,
														'health_analytics_report_appointment_id' => $appointment_id,
														'health_analytics_report_doctor_user_id' => $doctor_id,
														'health_analytics_report_clinic_id' 	 => $clinic_id
												    );
					}
					
					/* [START] PREPARING THE HEALTH ANALYTICS */
					$store_health_analytics[$health_data['id']] = array(
						'patient_analytics_user_id' 	 => $patient_id,
						'patient_analytics_name' 	     => $health_data['name'],
						'patient_analytics_name_precise' => $health_data['precise_name'],
						'patient_analytics_analytics_id' => $health_data['id'],
						'patient_analytics_doctor_id'    => $doctor_id,
						'patient_analytics_created_at'   => $this->utc_time_formated,
                        'patient_analytics_created_by'   => $this->user_id,
						'patient_analytics_updated_at'   => $this->utc_time_formated,
						'patient_analytics_status'		 => 9
					);
					/* [END] */
				}
            }
			
            if ($is_valid == 2) {
                $this->my_response['status'] = false;
                $this->my_response['message'] = "Invalid analytics details";
                $this->send_response();
            }
			
			$this->db->trans_start();
			if (!empty($update_health_analytics) && is_array($update_health_analytics) && count($update_health_analytics) > 0) {
                $this->Common_model->update_multiple(TBL_HEALTH_ANALYTICS_REPORT, $update_health_analytics, 'health_analytics_report_id');
            }
			
			if (!empty($add_health_analytics) && is_array($add_health_analytics) && count($add_health_analytics) > 0) {
                $this->Common_model->insert_multiple(TBL_HEALTH_ANALYTICS_REPORT, $add_health_analytics);
            }
			
			//get store analytics id 
			if(isset($allAnalyticIds) && is_array($allAnalyticIds) && count($allAnalyticIds) > 0){
				$selQue = "SELECT patient_analytics_analytics_id FROM ".TBL_PATIENT_ANALYTICS." WHERE patient_analytics_user_id=".$patient_id." AND patient_analytics_doctor_id=".$doctor_id." AND patient_analytics_analytics_id IN (".implode(',',$allAnalyticIds).")";
				$get_anlaytics_ids = $this->Common_model->get_all_rows_by_query($selQue);
				if(!empty($get_anlaytics_ids)){
					$alreadyAssignedHaIds = array_unique(array_column($get_anlaytics_ids,'patient_analytics_analytics_id'));
					$needToAddAnalytics = array_diff($allAnalyticIds,$alreadyAssignedHaIds);
					if(!empty($needToAddAnalytics) && count($needToAddAnalytics) > 0){
						$store_health_analytics_final = [];
						foreach($needToAddAnalytics as $haId){
							if(isset($store_health_analytics[$haId])){
								$store_health_analytics_final[] = $store_health_analytics[$haId];
							}
						}
						if(count($store_health_analytics_final) > 0){
							$this->Common_model->insert_multiple(TBL_PATIENT_ANALYTICS, $store_health_analytics_final);
						}
					}
				}
			}
			
			if ($this->db->trans_status() !== FALSE) {
                $this->db->trans_commit();
				
                $this->my_response['status'] = true;
                $this->my_response['message'] = lang('analytics_added');
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
	
	public function update_patient_dob_post(){
        try {
            $clinic_id = !empty($this->Common_model->escape_data($this->post_data['clinic_id'])) ? trim($this->Common_model->escape_data($this->post_data['clinic_id'])) : '';
            $user_id = !empty($this->Common_model->escape_data($this->post_data['user_id'])) ? trim($this->Common_model->escape_data($this->post_data['user_id'])) : '';
			$doctor_id = !empty($this->Common_model->escape_data($this->post_data['doctor_id'])) ? trim($this->Common_model->escape_data($this->post_data['doctor_id'])) : '';
            $patient_id = !empty($this->Common_model->escape_data($this->post_data['patient_id'])) ? trim($this->Common_model->escape_data($this->post_data['patient_id'])) : '';
            $user_type = !empty($this->Common_model->escape_data($this->post_data['user_type'])) ? trim($this->Common_model->escape_data($this->post_data['user_type'])) : '';
			$old_dob = !empty($this->Common_model->escape_data($this->post_data['old_dob'])) ? trim($this->Common_model->escape_data($this->post_data['old_dob'])) : '';
			$updated_user_dob = !empty($this->Common_model->escape_data($this->post_data['updated_user_dob'])) ? trim($this->Common_model->escape_data($this->post_data['updated_user_dob'])) : '';
			
            if ($user_type == 2 && (empty($clinic_id) || empty($doctor_id) || empty($patient_id) || empty($updated_user_dob))) {
                $this->bad_request();
            }
            if (!in_array($user_type, $this->user_type)) {
                $this->my_response['status']  = false;
                $this->my_response['message'] = lang("mycontroller_invalid_request");
                $this->send_response();
            }
            /* if ($user_type == 2) {
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
            } */
			$this->db->trans_start();
			$update_user_details = array();
            $update_user_details['user_details_dob'] = $updated_user_dob;
			$user_details_is_update = $this->User_model->update_user_details($patient_id, $update_user_details);
			if ($this->db->trans_status() !== FALSE) {
				$this->load->model("Auditlog_model");
				if(IS_AUDIT_LOG_ENABLE && $user_details_is_update && !empty($update_user_details)) {
					$this->Auditlog_model->create_audit_log($user_id, 2, AUDIT_SLUG_ARR['PATIENT_DOB_UPDATE'], ['user_details_dob'=>$old_dob], $update_user_details, TBL_USER_DETAILS, 'user_details_user_id', $patient_id);
				}
                $this->db->trans_commit();
                $this->my_response['status'] = true;
                $this->my_response['message'] = lang('patient_dob_updated');
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

    public function add_previous_vitals_post() {

        try {

            $clinic_id = !empty($this->Common_model->escape_data($this->post_data['clinic_id'])) ? trim($this->Common_model->escape_data($this->post_data['clinic_id'])) : '';
            $doctor_id = !empty($this->Common_model->escape_data($this->post_data['doctor_id'])) ? trim($this->Common_model->escape_data($this->post_data['doctor_id'])) : '';
            $patient_id = !empty($this->Common_model->escape_data($this->post_data['patient_id'])) ? trim($this->Common_model->escape_data($this->post_data['patient_id'])) : '';
            $vitals_data = !empty($this->post_data['vitals_data']) ? $this->post_data['vitals_data'] : '';
            $previous_deleted_vital_ids = !empty($this->post_data['previous_deleted_vital_ids']) ? $this->post_data['previous_deleted_vital_ids'] : '';

            if (empty($clinic_id) ||
                    empty($doctor_id) ||
                    empty($patient_id)
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
            if(!empty($previous_deleted_vital_ids) && count($previous_deleted_vital_ids) > 0) {
                foreach ($previous_deleted_vital_ids as $vital_id) {
                    $vital_delete_array[] = array(
                        'vital_report_id' => $vital_id,
                        'vital_report_status' => 9,
                        'vital_report_updated_by' => $this->user_id,
                        'vital_report_updated_at' => $this->utc_time_formated
                    );
                }
                $this->Common_model->update_multiple(TBL_VITAL_REPORTS, $vital_delete_array, 'vital_report_id');
            }
            
            $vital_array = array();
            $vital_update_array = array();
            if(!empty($vitals_data) && count($vitals_data) > 0) {
                foreach ($vitals_data as $key => $value) {
                    $where_params = array(
                        'patient_id' => $patient_id,
                        'doctor_id' => $doctor_id,
                        'clinic_id' => $clinic_id,
                        'date' => $value['date'],
                    );
                    $appointment = $this->Appointments_model->check_appointment_exist($where_params);
                    if(!empty($value['vital_report_id'])) {
                        $vital_update_array[$key] = array(
                            'vital_report_id' => $value['vital_report_id'],
                            'vital_report_user_id' => $patient_id,
                            'vital_report_doctor_id' => $doctor_id,
                            'vital_report_appointment_id' => NULL,
                            'vital_report_clinic_id' => $clinic_id,
                            'vital_report_date' => $value['date'],
                            'vital_report_spo2' => $value['sp2o'],
                            'vital_report_weight' => $value['weight'],
                            'vital_report_bloodpressure_systolic' => $value['blood_pressure_systolic'],
                            'vital_report_bloodpressure_diastolic' => $value['blood_pressure_diastolic'],
                            'vital_report_bloodpressure_type' => $value['blood_pressure_type'],
                            'vital_report_pulse' => $value['pulse'],
                            'vital_report_temperature' => $value['temperature'],
                            'vital_report_temperature_type' => $value['temperature_type'],
                            'vital_report_temperature_taken' => $value['temperature_taken'],
                            'vital_report_resp_rate' => $value['resp'],
                            'vital_report_share_status' => $vital_share_status,
                            'vital_report_updated_by' => $this->user_id,
                            'vital_report_updated_at' => $this->utc_time_formated
                        );
                        if(!empty($appointment->appointment_id) && $value['vital_report_appointment_id'] == $appointment->appointment_id) {
                            $vital_update_array[$key]['vital_report_appointment_id'] = $value['vital_report_appointment_id'];
                        } else {
                            if((!empty($appointment->appointment_id) && empty($appointment->vital_report_id)))
                                $vital_update_array[$key]['vital_report_appointment_id'] = $appointment->appointment_id;
                        }
                    } else {
                        $vital_array[$key] = array(
                            'vital_report_user_id' => $patient_id,
                            'vital_report_doctor_id' => $doctor_id,
                            'vital_report_appointment_id' => (!empty($appointment->appointment_id) && empty($appointment->vital_report_id)) ? $appointment->appointment_id : NULL,
                            'vital_report_clinic_id' => $clinic_id,
                            'vital_report_date' => $value['date'],
                            'vital_report_spo2' => $value['sp2o'],
                            'vital_report_weight' => $value['weight'],
                            'vital_report_bloodpressure_systolic' => $value['blood_pressure_systolic'],
                            'vital_report_bloodpressure_diastolic' => $value['blood_pressure_diastolic'],
                            'vital_report_bloodpressure_type' => $value['blood_pressure_type'],
                            'vital_report_pulse' => $value['pulse'],
                            'vital_report_temperature' => $value['temperature'],
                            'vital_report_temperature_type' => $value['temperature_type'],
                            'vital_report_temperature_taken' => $value['temperature_taken'],
                            'vital_report_resp_rate' => $value['resp'],
                            'vital_report_share_status' => $vital_share_status,
                            'vital_report_created_by' => $this->user_id,
                            'vital_report_created_at' => $this->utc_time_formated,
                            'vital_report_updated_at' => $this->utc_time_formated
                        );
                    }
                }
                if(count($vital_array) > 0)
                    $this->Common_model->insert_multiple(TBL_VITAL_REPORTS, $vital_array);
                if(count($vital_update_array) > 0)
                    $this->Common_model->update_multiple(TBL_VITAL_REPORTS, $vital_update_array, 'vital_report_id');
            }
            // print_r($vital_array);die;
            $this->my_response['status'] = true;
            $this->my_response['message'] = lang('vital_added');
            $this->send_response();
        } catch (ErrorException $ex) {
            $this->error = $ex->getMessage();
            $this->store_error();
        }
    }

    public function get_previous_vitals_post() {

        try {

            $clinic_id = !empty($this->Common_model->escape_data($this->post_data['clinic_id'])) ? trim($this->Common_model->escape_data($this->post_data['clinic_id'])) : '';
            $doctor_id = !empty($this->Common_model->escape_data($this->post_data['doctor_id'])) ? trim($this->Common_model->escape_data($this->post_data['doctor_id'])) : '';
            $patient_id = !empty($this->Common_model->escape_data($this->post_data['patient_id'])) ? trim($this->Common_model->escape_data($this->post_data['patient_id'])) : '';

            if (empty($clinic_id) ||
                    empty($doctor_id) ||
                    empty($patient_id)
            ) {
                $this->bad_request();
            }
            $where_params = array(
                    'patient_id' => $patient_id,
                    'doctor_id' => $doctor_id,
                    'clinic_id' => $clinic_id,
                );
            $vitals = $this->Appointments_model->get_previous_vitals($where_params);
            // print_r($vital_array);die;
            $this->my_response['status'] = true;
            $this->my_response['data'] = $vitals;
            $this->my_response['message'] = lang('common_detail_found');
            $this->send_response();
        } catch (ErrorException $ex) {
            $this->error = $ex->getMessage();
            $this->store_error();
        }
    }

    public function refer_rx_send_otp_post() {
        try {
            $patient_id = !empty($this->post_data['patient_id']) ? trim($this->Common_model->escape_data($this->post_data['patient_id'])) : "";
            $doctor_id = !empty($this->post_data['doctor_id']) ? trim($this->Common_model->escape_data($this->post_data['doctor_id'])) : "";
            $clinic_id = !empty($this->post_data['clinic_id']) ? trim($this->Common_model->escape_data($this->post_data['clinic_id'])) : "";
            $is_resend_otp = !empty($this->post_data['is_resend_otp']) ? true : false;

            if (empty($patient_id) || empty($doctor_id) || empty($clinic_id)) {
                $this->bad_request();
                exit;
            }
            $credit = check_sms_whatsapp_credit($doctor_id, 'sms');
            if($credit == 0 && !empty($GLOBALS['ENV_VARS']['CHECK_SMS_CREDIT']['REFER_PATIENT_RX_OTP'])) {
                $this->my_response['status'] = false;
                $this->my_response['message'] = lang('no_sms_credit');
                $this->send_response();
            }
            $this->load->model(array('User_model'));

            $auth_resend_count = 0;
            if($is_resend_otp) {
                $auth_update_where = array(
                    'auth_user_id' => $patient_id,
                    "auth_type" => 5 //5=Refer Rx Verification
                );
                $auth_resend_data = $this->User_model->get_single_row(TBL_USER_AUTH, "auth_resend_count,auth_resend_timestamp", $auth_update_where);
                if (!empty($auth_resend_data)) {
                    $auth_resend_count = $auth_resend_data['auth_resend_count'] + 1;
                    if ($auth_resend_data['auth_resend_count'] >= RESEND_OTP_LIMIT) {
                        $this->my_response['status'] = false;
                        $this->my_response['message'] = lang("otp_resend_limit_reach");
                        $this->send_response();
                    }
                }
            }
            
            $otp = getUniqueToken(6, 'numeric');
            $message = sprintf(lang('refer_patient_rx_otp'), $otp);
            $patient_user_detail = $this->User_model->get_details_by_id($patient_id,'user_phone_number');
            if(!empty($patient_user_detail['user_phone_number'])) {
                $send_otp = array(
                    'phone_number' => $patient_user_detail['user_phone_number'],
                    'message' => $message,
                    'whatsapp_sms_body' => $message
                );
                $send_otp['doctor_id'] = $doctor_id;
                $send_otp['patient_id'] = $patient_id;
                $send_otp['is_sms_count'] = true;
                $send_otp['is_check_sms_credit'] = $GLOBALS['ENV_VARS']['CHECK_SMS_CREDIT']['REFER_PATIENT_RX_OTP'];
                send_communication($send_otp);
            } else {
                $columns = 'u.user_id,u.user_phone_number,u.user_phone_verified';
                $parent_members = $this->User_model->get_linked_family_members($patient_id, $columns);
                    foreach ($parent_members as $parent_member) {
                        if($parent_member->user_phone_verified == 1) {
                            $send_otp = array(
                                'phone_number' => $parent_member->user_phone_number,
                                'message' => $message,
                                'whatsapp_sms_body' => $message,
                                'doctor_id' => $doctor_id,
                                'patient_id' => $parent_member->user_id,
                                'is_sms_count' => true,
                                'is_check_sms_credit' => $GLOBALS['ENV_VARS']['CHECK_SMS_CREDIT']['REFER_PATIENT_RX_OTP']
                            );
                            send_communication($send_otp);
                        }
                    }
            }
            $auth_update_data = array(
                'auth_code' => $otp,
                'auth_otp_expiry_time' => date('Y-m-d H:i:s', strtotime(OTP_EXPIRE_TOKEN_TIME, strtotime($this->utc_time_formated))),
                'auth_phone_number' => $patient_phone_number,
                'auth_resend_count' => $auth_resend_count,
                'auth_type' => 5  // 5=Refer Rx Verification
            );
            $auth_update_where = array(
                'auth_user_id' => $patient_id,
                'auth_type' => 5 // 5=Refer Rx Verification
            );
            $this->User_model->update_auth_details($auth_update_data, $auth_update_where);
            $this->my_response['status'] = true;
            $this->my_response['message'] = lang('otp_sent_to_mobile');
            $this->send_response();
        } catch (Exception $ex) {
            $this->error = $ex->getMessage();
            $this->store_error();
        }
    }

    public function refer_rx_verify_otp_post() {
        try {
            $patient_id = !empty($this->post_data['patient_id']) ? trim($this->Common_model->escape_data($this->post_data['patient_id'])) : "";
            $doctor_id = !empty($this->post_data['doctor_id']) ? trim($this->Common_model->escape_data($this->post_data['doctor_id'])) : "";
            $clinic_id = !empty($this->post_data['clinic_id']) ? trim($this->Common_model->escape_data($this->post_data['clinic_id'])) : "";
            $otp = !empty($this->post_data['otp']) ? trim($this->Common_model->escape_data($this->post_data['otp'])) : "";

            if (empty($patient_id) || empty($doctor_id) || empty($clinic_id) || empty($otp)) {
                $this->bad_request();
                exit;
            }
            $where = array(
                'auth_user_id' => $patient_id,
                'auth_type' => 5 // 5=Refer Rx Verification
            );
            $get_auth_details = $this->Common_model->get_single_row(TBL_USER_AUTH, "*", $where);
            if (!empty($get_auth_details)) {
                if (strtotime($get_auth_details['auth_otp_expiry_time']) >= $this->utc_time) {
                    if ($get_auth_details['auth_code'] == $otp) {
                        $this->my_response = array(
                            "status" => true,
                            "message" => lang("otp_verfication_success")
                        );
                    } else {
                        $this->my_response = array(
                            "status" => false,
                            "message" => lang("otp_verfication_fail")
                        );
                    }
                } else {
                    $this->my_response = array(
                        "status" => false,
                        "message" => lang("otp_token_expire")
                    );
                }
            } else {
                $this->my_response = array(
                    "status" => false,
                    "message" => lang("patient_not_found")
                );
            }
            $this->send_response();
        } catch (Exception $ex) {
            $this->error = $ex->getMessage();
            $this->store_error();
        }
    }

    public function get_patient_rx_data_post() {
        try {
            $patient_id = !empty($this->post_data['patient_id']) ? trim($this->Common_model->escape_data($this->post_data['patient_id'])) : "";
            $doctor_id = !empty($this->post_data['doctor_id']) ? trim($this->Common_model->escape_data($this->post_data['doctor_id'])) : "";
            $clinic_id = !empty($this->post_data['clinic_id']) ? trim($this->Common_model->escape_data($this->post_data['clinic_id'])) : "";
            $is_verified_otp = !empty($this->post_data['is_verified_otp']) ? trim($this->Common_model->escape_data($this->post_data['is_verified_otp'])) : false;

            if (empty($patient_id) || empty($doctor_id) || empty($clinic_id)) {
                $this->bad_request();
                exit;
            }
            $this->load->model("Auditlog_model");
            $audit_where = ['action_slug_name' => AUDIT_SLUG_ARR['PATIENT_PAST_RX_REFER'], 'user_id' => $doctor_id, 'user_type' => 2, 'table_primary_key_value' => $patient_id];
            $audit_log = $this->Auditlog_model->get_last_audit_log($audit_where);
            if($is_verified_otp || (!empty($audit_log->audit_created_at) && (strtotime($audit_log->audit_created_at) + 60*60*24) > time())) {
                $where_data = array(
                    'patient_id' => $patient_id,
                    'doctor_id' => $doctor_id,
                );
                $columns = "pr.prescription_id, pr.prescription_user_id, pr.prescription_doctor_user_id, pr.prescription_appointment_id,DATE_FORMAT(a.appointment_date, '%d/%m/%Y') as appointment_date,CONCAT(u.user_first_name, ' ', u.user_last_name) AS doctor_name";
                $result = $this->Patient_model->get_patient_past_rx($where_data, $columns);
                if(IS_AUDIT_LOG_ENABLE && $is_verified_otp) {
                    $this->Auditlog_model->create_audit_log($doctor_id, 2, AUDIT_SLUG_ARR['PATIENT_PAST_RX_REFER'], [], $result, 'me_users', 'user_id', $patient_id);
                }
                $this->my_response['status'] = true;
                $this->my_response['is_otp_required'] = false;
                $this->my_response['message'] = lang('common_detail_found');
                $this->my_response['data'] = $result;
                $this->my_response['audit_log'] = $audit_log;
            } else {
                $this->my_response['status'] = true;
                $this->my_response['is_otp_required'] = true;
                $this->my_response['message'] = lang('common_detail_found');
            }
            $this->send_response();
        } catch (Exception $ex) {
            $this->error = $ex->getMessage();
            $this->store_error();
        }
    }

    public function get_prescription_pdf_post() {
        try {
            $patient_id = !empty($this->post_data['patient_id']) ? trim($this->Common_model->escape_data($this->post_data['patient_id'])) : "";
            $doctor_id = !empty($this->post_data['doctor_id']) ? trim($this->Common_model->escape_data($this->post_data['doctor_id'])) : "";
            $appointment_id = !empty($this->post_data['appointment_id']) ? trim($this->Common_model->escape_data($this->post_data['appointment_id'])) : '';
            if (empty($patient_id) || empty($doctor_id) || empty($appointment_id)) {
                $this->bad_request();
                exit;
            }
            
            if (!file_exists(UPLOAD_REL_PATH . "/" . PAST_PRESCRIPTION . "/")) {
                mkdir(UPLOAD_REL_PATH . "/" . PAST_PRESCRIPTION . "/", 0777, TRUE);
                chmod(UPLOAD_REL_PATH . "/" . PAST_PRESCRIPTION . "/", 0777);
            }
            $upload_path = UPLOAD_REL_PATH . "/" . PAST_PRESCRIPTION . "/" . $appointment_id."_prescription".".pdf";
            $s3_upload_path = PAST_PRESCRIPTION . "/" . $appointment_id."_prescription".".pdf";
            include_once BUCKET_HELPER_PATH;
            $pdf_path = IMAGE_MANIPULATION_URL . $s3_upload_path;
            $result = checkResource($s3_upload_path);
            $appointment_row = $this->Common_model->get_single_row_by_query("SELECT appointment_date FROM " . TBL_APPOINTMENTS . " WHERE appointment_id='" . $appointment_id . "'");
            if(!$result || $appointment_row['appointment_date'] == date('Y-m-d')) {
                $is_new = true;
                $check_patient_appointment_sql = "
                SELECT 
                    appointment_date,
                    user_first_name,
                    user_last_name,
                    user_phone_number,
                    doctor_detail_speciality,
                    clinic_name,
                    clinic_contact_number,
                    clinic_email,
                    address_name,
                    GROUP_CONCAT(DISTINCT(doctor_qualification_degree)) AS doctor_qualification
                FROM 
                    " . TBL_APPOINTMENTS . "
                LEFT JOIN 
                    " . TBL_USERS . " ON appointment_doctor_user_id=user_id
                LEFT JOIN 
                    " . TBL_DOCTOR_DETAILS . " ON  user_id = doctor_detail_doctor_id
                LEFT JOIN 
                    " . TBL_ADDRESS . " ON address_user_id = appointment_clinic_id AND address_type = 2
                LEFT JOIN 
                   " . TBL_CLINICS . " ON appointment_clinic_id = clinic_id
                LEFT JOIN
                    ".TBL_DOCTOR_EDUCATIONS." ON appointment_doctor_user_id = doctor_qualification_user_id AND doctor_qualification_status = 1
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
                    "files_data" => array(),
                    "language_id" => 'en'
                );
                
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
                        prescription_status=1
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
                        lab_report_status=1
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
                                            procedure_report_status = 1    
                    ";
                $get_procedure_report = $this->Common_model->get_single_row_by_query($get_procedure_sql);
                $view_data['procedure_data'] = $get_procedure_report;
                $view_data['billing_data'] = array();
                $view_html = $this->load->view("prints/charting", $view_data, true);
                
                require_once MPDF_PATH;
                $lang_code = 'en-GB';
                $mpdf = new MPDF(
                        $lang_code, // mode - default '' //sd
                        'A4', // format - A4, for example, default ''
                        0, // font size - default 0
                        'arial', // default font family
                        8, // margin_left
                        8, // margin right
                        35, // margin top
                        8, // margin bottom
                        8, // margin header
                        5, // margin footer
                        'P'   // L - landscape, P - portrait
                );
                
                $mpdf->useOnlyCoreFonts = true;
                $mpdf->SetDisplayMode('real');
                $mpdf->list_indent_first_level = 0;
                $mpdf->setAutoBottomMargin = 'stretch';
                $doctor_data = $view_data['doctor_data'];
                $date_and_time = date('m/d/Y h:i:s a', time());
                $patient_name = $view_data['patient_data']['user_first_name'];
                $mpdf->SetTitle('Rx_'.$patient_name.'_'.$date_and_time);
                $mpdf->SetHTMLHeader('
                    <table style="width:100%;border-bottom:1px solid #000">
                        <tr>
                            <td width="50%" style="text-align:left;vertical-align:top">
                             ' . DOCTOR. " ".$doctor_data['user_first_name'] . " " . $doctor_data['user_last_name'] . "<br>" . '
                             ' . $doctor_data['doctor_detail_speciality'] . "<br>" . '
                             ' . $doctor_data['doctor_qualification'] . "<br>" . '    
                            </td>
                            <td width="50%" style="text-align:right;vertical-align:top">
                                ' . $doctor_data['clinic_name'] . "<br>" . '
                                ' . $doctor_data['address_name'] . "<br>" . '
                                ' . $doctor_data['clinic_contact_number'] . ", " . '
                                ' . $doctor_data['clinic_email'] . "<br>" . '
                            </td>
                        </tr>
                    </table>
                ');
                $mpdf->SetHTMLFooter('
                    <table width="100%">
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
                // echo $appointment_id;die;
                $upload_flag = uploadfilesS3($upload_path, $s3_upload_path);
                unlink($upload_path);
            } else {
                $is_new = false;
            }
            // $get_patient_report_detail['pdf_path'] = $pdf_path;
            $this->my_response['status'] = true;
            $this->my_response['message'] = lang('common_detail_found');
            $this->my_response['pdf_url'] = $pdf_path;
            $this->my_response['is_new'] = $is_new;
            
            $this->send_response();
        } catch (Exception $ex) {
            $this->error = $ex->getMessage();
            $this->store_error();
        }
    }

    public function upload_rx_post() {
        try {
            $clinic_id = !empty($this->Common_model->escape_data($this->post_data['clinic_id'])) ? trim($this->Common_model->escape_data($this->post_data['clinic_id'])) : '';
            $doctor_id = !empty($this->Common_model->escape_data($this->post_data['doctor_id'])) ? trim($this->Common_model->escape_data($this->post_data['doctor_id'])) : '';
            $patient_id = !empty($this->Common_model->escape_data($this->post_data['patient_id'])) ? trim($this->Common_model->escape_data($this->post_data['patient_id'])) : '';
            $appointment_id = !empty($this->Common_model->escape_data($this->post_data['appointment_id'])) ? trim($this->Common_model->escape_data($this->post_data['appointment_id'])) : '';
            $rx_upload_name = !empty($this->Common_model->escape_data($this->post_data['rx_upload_name'])) ? trim($this->Common_model->escape_data($this->post_data['rx_upload_name'])) : '';
            $user_type = !empty($this->Common_model->escape_data($this->post_data['user_type'])) ? trim($this->Common_model->escape_data($this->post_data['user_type'])) : '';
            $date = !empty($this->Common_model->escape_data($this->post_data['date'])) ? trim($this->Common_model->escape_data($this->post_data['date'])) : '';
            if (empty($user_type) ||
                    empty($patient_id) ||
                    empty($date) ||
                    empty($rx_upload_name)
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
            $file_upload_validate = file_upload_validate($_FILES);
            if(!$file_upload_validate['status']) {
                $this->my_response['status'] = false;
                $this->my_response['message'] = $file_upload_validate['error'];
                $this->send_response();
            }
            //check the user permission
            if ($user_type == 2) {
                $get_role_details = $this->Common_model->get_the_role($this->user_id);
                if (!empty($get_role_details['user_role_data'])) {
                    $permission_data = array(
                        'role_data' => $get_role_details['user_role_data'],
                        'module' => 42,
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
            $report_share_status = '';
            $requested_data = array(
                'appointment_id' => $appointment_id,
                'doctor_id' => $doctor_id,
                'clinic_id' => $clinic_id,
                'patient_id' => $patient_id
            );
            $this->check_data_belongs_doctor($requested_data);
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
            $report_array = array(
                'rx_upload_user_id' => $patient_id,
                'rx_upload_doctor_user_id' => $doctor_id,
                'rx_upload_appointment_id' => $appointment_id,
                'rx_upload_clinic_id' => $clinic_id,
                'rx_upload_name' => $rx_upload_name,
                'rx_upload_date' => $date,
                'rx_upload_created_at' => $this->utc_time_formated,
                'rx_upload_share_status' => $report_share_status,
                'rx_upload_added_by_user_id' => $this->user_id
            );
            $this->db->trans_start();
            $inserted_id = $this->Common_model->insert(TBL_RX_UPLOAD_REPORTS, $report_array);
            $image_name = array();
            if ($inserted_id > 0) {
                if (!empty($_FILES['images']['name']) && $_FILES['images']['error'] == 0) {
                    $new_profile_img = '';
                    $upload_path = UPLOAD_REL_PATH . "/" . RX_FOLDER . "/" . $inserted_id;
                    $upload_folder = RX_FOLDER . "/" . $inserted_id;
                    $profile_image_name = do_upload_multiple($upload_path, $_FILES, $upload_folder, 290, 210);
                    $new_profile_img = $profile_image_name['images'];

                    if (!empty($new_profile_img)) {
                        $report_image_url = get_file_json_detail(RX_FOLDER . "/" . $inserted_id . "/" . $new_profile_img);
                        $insert_image_array = array(
                            'rx_upload_report_id' => $inserted_id,
                            'rx_upload_report_image_url' => $report_image_url,
                            'rx_upload_file_size' => get_file_size(get_file_full_path($report_image_url)),
                            'rx_upload_report_image_created_at' => $this->utc_time_formated
                        );
                        $this->Common_model->insert(TBL_RX_UPLOAD_REPORTS_IMAGES, $insert_image_array);
                    }
                }
            }
            if ($this->db->trans_status() !== FALSE) {
                $this->db->trans_commit();
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
    public function get_uploaded_rx_post() {
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
            //check the user permission
            $get_role_details = $this->Common_model->get_the_role($this->user_id);
            if (!empty($get_role_details['user_role_data'])) {
                $permission_data = array(
                    'role_data' => $get_role_details['user_role_data'],
                    'module' => 42,
                    'key' => 1
                );
                $check_module_permission = $this->check_module_permission($permission_data);
                if ($check_module_permission == 2) {
                    $this->my_response['status'] = false;
                    $this->my_response['message'] = lang('permission_error');
                    $this->send_response();
                }
            }
            $where_data = [
                'patient_id' => $patient_id,
                'clinic_id' => $clinic_id,
                'doctor_id' => $doctor_id,
                'appointment_id' => $appointment_id,
            ];
            $columns = "rx_upload_id,rx_upload_name,rx_upload_date,rx_upload_report_image_url,rx_upload_created_at";
            $result = $this->Patient_model->get_uploaded_rx($where_data, $columns);
            foreach ($result as $key => $value) {
                $result[$key]->is_edit = false;
                if(date("Y-m-d") == date("Y-m-d", strtotime($value->rx_upload_created_at)))
                    $result[$key]->is_edit = true;
                if (!empty($value->rx_upload_report_image_url)) {
                    $result[$key]->rx_upload_report_image_thumb_url = get_image_thumb($value->rx_upload_report_image_url);
                    $result[$key]->rx_upload_report_image_url = get_file_full_path($value->rx_upload_report_image_url);
                    $extension = pathinfo(get_file_full_path($value->rx_upload_report_image_url), PATHINFO_EXTENSION);
                    if ($extension == 'pdf') {
                        $result[$key]->is_pdf = 1;
                    } else {
                        $result[$key]->is_pdf = 2;
                    }
                }
            }
            $this->my_response['status'] = true;
            $this->my_response['data'] = $result;
            if(!empty($result))
                $this->my_response['message'] = lang('common_detail_found');
            else
                $this->my_response['message'] = lang('common_detail_not_found');
            $this->send_response();
        } catch (ErrorException $ex) {
            $this->error = $ex->getMessage();
            $this->store_error();
        }
    }

    public function delete_rx_uploaded_post() {
        try {
            $rx_upload_id = !empty($this->Common_model->escape_data($this->post_data['rx_upload_id'])) ? trim($this->Common_model->escape_data($this->post_data['rx_upload_id'])) : '';
            $doctor_id = !empty($this->Common_model->escape_data($this->post_data['doctor_id'])) ? trim($this->Common_model->escape_data($this->post_data['doctor_id'])) : '';
            if (empty($doctor_id) ||
                    empty($rx_upload_id)
            ) {
                $this->bad_request();
            }
            //check the user permission
            $get_role_details = $this->Common_model->get_the_role($this->user_id);
            if (!empty($get_role_details['user_role_data'])) {
                $permission_data = array(
                    'role_data' => $get_role_details['user_role_data'],
                    'module' => 42,
                    'key' => 4
                );
                $check_module_permission = $this->check_module_permission($permission_data);
                if ($check_module_permission == 2) {
                    $this->my_response['status'] = false;
                    $this->my_response['message'] = lang('permission_error');
                    $this->send_response();
                }
            }
            $update_report_data = array(
                'rx_upload_status' => 9,
                'rx_upload_updated_at' => $this->utc_time_formated,
                'rx_upload_updated_by_user_id' => $this->user_id,
            );
            $update_report_where = array(
                'rx_upload_id' => $rx_upload_id
            );
            $is_update = $this->Common_model->update(TBL_RX_UPLOAD_REPORTS, $update_report_data, $update_report_where);
            if ($is_update > 0) {
                $update_report_images_data = array(
                    'rx_upload_report_image_updated_at' => $this->utc_time_formated,
                    'rx_upload_report_image_status' => 9
                );
                $update_report_images_where = array(
                    'rx_upload_report_id' => $rx_upload_id
                );
                $this->Common_model->update(TBL_RX_UPLOAD_REPORTS_IMAGES, $update_report_images_data, $update_report_images_where);
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

    public function save_uas7_data_post() {
        try {
            $doctor_id = !empty($this->Common_model->escape_data($this->post_data['doctor_id'])) ? trim($this->Common_model->escape_data($this->post_data['doctor_id'])) : '';
            $patient_id = !empty($this->Common_model->escape_data($this->post_data['patient_id'])) ? trim($this->Common_model->escape_data($this->post_data['patient_id'])) : '';
            $wheal_count = !empty($this->post_data['wheal_count']) ? $this->post_data['wheal_count'] : '';
            $pruritus = !empty($this->post_data['pruritus']) ? $this->post_data['pruritus'] : '';
            $uas7_date = !empty($this->post_data['uas7_date']) ? $this->post_data['uas7_date'] : '';
            if (empty($doctor_id) ||
                    empty($patient_id) ||
                    empty($uas7_date)
            ) {
                $this->bad_request();
            }
            $insert_uas7_data = [];
            $uas7_update_data = [];
            $result = $this->Patient_model->get_uas7_param_by_date($uas7_date, $patient_id, $doctor_id);
            $wheal_data = [];
            $pruritus_data = [];
            foreach ($result as $value) {
                if($value->patient_diary_label == "wheal_count") {
                    $wheal_data[] = $value;
                } else {
                    $pruritus_data[] = $value;
                }
            }
            for($i=0; $i < count($uas7_date); $i++) {
                $key = array_search($uas7_date[$i], array_column($wheal_data, 'patient_diary_date'));
                if(is_numeric($key) && !empty($wheal_data[$key]->patient_diary_id)) {
                    $uas7_update_data[] = [
                        'patient_diary_id' => $wheal_data[$key]->patient_diary_id,
                        'patient_diary_value' => $wheal_count[$i],
                        'patient_diary_added_by' => $doctor_id,
                        'patient_diary_updated_at' => $this->utc_time_formated
                    ];
                } else {
                    $insert_uas7_data[] = [
                        'patient_diary_patient_id' => $patient_id,
                        'patient_diary_doctor_id' => $doctor_id,
                        'patient_diary_date' => $uas7_date[$i],
                        'patient_diary_label' => 'wheal_count',
                        'patient_diary_value' => $wheal_count[$i],
                        'patient_diary_created_at' => $this->utc_time_formated,
                        'patient_diary_type' => 1, //1=UAS7 Para
                        'patient_diary_is_medsign_doctor' => 1,
                        'patient_diary_added_by' => $doctor_id
                    ];
                }
                if(is_numeric($key) && !empty($pruritus_data[$key]->patient_diary_id)) {
                    $uas7_update_data[] = [
                        'patient_diary_id' => $pruritus_data[$key]->patient_diary_id,
                        'patient_diary_value' => $pruritus[$i],
                        'patient_diary_added_by' => $doctor_id,
                        'patient_diary_updated_at' => $this->utc_time_formated
                    ];
                } else {
                    $insert_uas7_data[] = [
                        'patient_diary_patient_id' => $patient_id,
                        'patient_diary_doctor_id' => $doctor_id,
                        'patient_diary_date' => $uas7_date[$i],
                        'patient_diary_label' => 'pruritus_count',
                        'patient_diary_value' => $pruritus[$i],
                        'patient_diary_created_at' => $this->utc_time_formated,
                        'patient_diary_type' => 1, //1=UAS7 Para
                        'patient_diary_is_medsign_doctor' => 1,
                        'patient_diary_added_by' => $doctor_id
                    ];
                }
            }
            if(count($insert_uas7_data) > 0)
                $this->Common_model->insert_multiple('me_patient_diary', $insert_uas7_data);
            if(count($uas7_update_data) > 0)
                $this->Patient_model->uas7_bulk_update($uas7_update_data);
            $date = get_display_date_time('Y_m_d');
            $file_name = "UAS7_".$patient_id."_".$date.".pdf";
            $upload_folder = UAS7_FOLDER . "/" . $patient_id;
            delete_file_from_s3($upload_folder."/".$file_name);
            $this->my_response['status'] = true;
            $this->my_response['message'] = lang('uas7_added');
            $this->send_response();
        } catch (ErrorException $ex) {
            $this->error = $ex->getMessage();
            $this->store_error();
        }
    }

    public function get_uas7_parameters_post() {
        try {
            $patient_id = !empty($this->Common_model->escape_data($this->post_data['patient_id'])) ? trim($this->Common_model->escape_data($this->post_data['patient_id'])) : '';
            $graph_type = !empty($this->Common_model->escape_data($this->post_data['graph_type'])) ? trim($this->Common_model->escape_data($this->post_data['graph_type'])) : '1';
            if (empty($patient_id)) {
                $this->bad_request();
            }
            $where = ['patient_id' => $patient_id];
            $result = $this->Patient_model->get_uas7_para_data($where);
            // print_r($result);die;
            $uas7_weekly_data = [];
            $uas7_daily_data = [];
            $uas7_score = 0;
            foreach ($result as $key => $value) {
                $value_arr = explode(",", $value->patient_diary_value);
                $uas7_score += array_sum($value_arr);
                $uas7_daily_data[] = [
                    'patient_diary_date' => date("d/m/Y", strtotime($value->patient_diary_date)),
                    'uas7_score' => array_sum($value_arr)
                ];
                if(($key+1)%7 == 0){
                    $uas7_weekly_data[] = [
                        'patient_diary_date' => date("d/m/Y", strtotime($result[($key+1)-7]->patient_diary_date)),
                        'uas7_score' => $uas7_score,
                    ];
                    $uas7_score = 0;
                }
            }
            $this->my_response['status'] = true;
            $this->my_response['uas7_daily_data'] = $uas7_daily_data;
            $this->my_response['uas7_weekly_data'] = $uas7_weekly_data;
            $this->send_response();
        } catch (ErrorException $ex) {
            $this->error = $ex->getMessage();
            $this->store_error();
        }
    }

    public function search_caretaker_list_post() {
        try {
            $search = !empty($this->Common_model->escape_data($this->post_data['search'])) ? trim($this->Common_model->escape_data($this->post_data['search'])) : '';

            if (empty($search)) {
                $this->bad_request();
            }
            $where = ['search' => $search];
            $result = $this->Patient_model->search_caretaker($where);
            if (!empty($result)) {
                $this->my_response['status'] = true;
                $this->my_response['message'] = lang('common_detail_found');
                $this->my_response['data'] = $result;
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

    public function send_caretaker_otp_post() {
        try {
            $doctor_id = !empty($this->Common_model->escape_data($this->post_data['doctor_id'])) ? trim($this->Common_model->escape_data($this->post_data['doctor_id'])) : '';
            $mobile_no = !empty($this->Common_model->escape_data($this->post_data['mobile_no'])) ? trim($this->Common_model->escape_data($this->post_data['mobile_no'])) : '';
            $is_resend_otp = !empty($this->Common_model->escape_data($this->post_data['is_resend_otp'])) ? trim($this->Common_model->escape_data($this->post_data['is_resend_otp'])) : '';

            if (empty($doctor_id) || empty($mobile_no)) {
                $this->bad_request();
            }
            $auth_resend_count = 0;
            if($is_resend_otp) {
                $auth_update_where = array(
                    'auth_user_id' => $doctor_id,
                    "auth_type" => 6 // 6=Add Patient Caretaker Verification
                );
                $auth_resend_data = $this->User_model->get_single_row(TBL_USER_AUTH, "auth_resend_count,auth_resend_timestamp", $auth_update_where);
                if (!empty($auth_resend_data)) {
                    $auth_resend_count = $auth_resend_data['auth_resend_count'] + 1;
                    if ($auth_resend_data['auth_resend_count'] >= RESEND_OTP_LIMIT) {
                        $this->my_response['status'] = false;
                        $this->my_response['message'] = lang("otp_resend_limit_reach");
                        $this->send_response();
                    }
                }
            }
            $otp = getUniqueToken(6, 'numeric');
            $message = sprintf(lang('caretaker_varification_otp'), $otp);
            $send_otp = array(
                'phone_number' => $mobile_no,
                'message' => $message,
                'whatsapp_sms_body' => sprintf(lang('caretaker_varification_otp'), $otp)
            );
            $send_otp['doctor_id'] = $doctor_id;
            $send_otp['is_sms_count'] = true;
            $send_otp['is_check_sms_credit'] = $GLOBALS['ENV_VARS']['CHECK_SMS_CREDIT']['OTP_MESSAGE'];
            $sening_sms = send_communication($send_otp);
            $auth_update_data = array(
                'auth_code' => $otp,
                'auth_otp_expiry_time' => date('Y-m-d H:i:s', strtotime(OTP_EXPIRE_TOKEN_TIME, strtotime($this->utc_time_formated))),
                'auth_phone_number' => $mobile_no,
                'auth_resend_count' => $auth_resend_count,
                'auth_type' => 6  // 6=Add Patient Caretaker Verification
            );
            $auth_update_where = array(
                'auth_user_id' => $doctor_id,
                'auth_type' => 6 // 6=Add Patient Caretaker Verification
            );
            $this->User_model->update_auth_details($auth_update_data, $auth_update_where);
            $this->my_response['status'] = true;
            $this->my_response['message'] = lang('otp_sent_to_mobile');
            $this->send_response();
        } catch (ErrorException $ex) {
            $this->error = $ex->getMessage();
            $this->store_error();
        }
    }

}