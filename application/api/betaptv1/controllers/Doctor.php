<?php

/**
 * 
 * This controller use for user related activity
 * 
 * @author Nitinkumar Vaghani
 * Modified Data :- 2018-01-12
 */
class Doctor extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model("Doctor_model", "doctor");
    }

    /**
     * Description :- This function is used to get the list of the doctor based on the clinic wise
     * 
     * @author Manish Ramnani
     * 
     */
    public function get_doctor_list_post() {

        $clinic_id = !empty($this->post_data['clinic_id']) ? trim($this->Common_model->escape_data($this->post_data['clinic_id'])) : "";

        if (empty($clinic_id)) {
            $this->bad_request();
        }

        $requested_data = array(
            'clinic_id' => $clinic_id,
            'doctor_clinic_mapping_role_id' => 1
        );
        $get_doctor_list = $this->doctor->get_doctor_list($requested_data);


        if (!empty($get_doctor_list)) {
            $this->my_response = array(
                "status" => true,
                "message" => lang("common_detail_found"),
                "data" => $get_doctor_list,
            );
        } else {
            $this->my_response = array(
                "status" => true,
                "message" => lang("common_detail_not_found"),
            );
        }
        $this->send_response();
    }

    /**
     * Description :- This function is used to update the profile of the doctor
     * 
     * @author Manish Ramnani
     * 
     * Modified Date :- 2018-05-25
     */
    public function update_profile_doctor_post() {
        try {

            $user_id = !empty($this->post_data['user_id']) ? trim($this->Common_model->escape_data($this->post_data['user_id'])) : "";
            $first_name = !empty($this->post_data['first_name']) ? trim($this->Common_model->escape_data($this->post_data['first_name'])) : "";
            $last_name = !empty($this->post_data['last_name']) ? trim($this->Common_model->escape_data($this->post_data['last_name'])) : "";
            $email = !empty($this->post_data['email']) ? trim($this->Common_model->escape_data($this->post_data['email'])) : "";
            $gender = !empty($this->post_data['gender']) ? trim($this->Common_model->escape_data($this->post_data['gender'])) : "";
            $language = !empty($this->post_data['language']) ? trim($this->Common_model->escape_data($this->post_data['language'])) : "";
            $user_type = !empty($this->post_data['user_type']) ? trim($this->Common_model->escape_data($this->post_data['user_type'])) : "";
            $address = !empty($this->post_data['address']) ? trim($this->Common_model->escape_data($this->post_data['address'])) : "";
            $address1 = !empty($this->post_data['address1']) ? trim($this->Common_model->escape_data($this->post_data['address1'])) : "";
            $city_id = !empty($this->post_data['city_id']) ? trim($this->Common_model->escape_data($this->post_data['city_id'])) : "";
            $state_id = !empty($this->post_data['state_id']) ? trim($this->Common_model->escape_data($this->post_data['state_id'])) : "";
            $country_id = !empty($this->post_data['country_id']) ? trim($this->Common_model->escape_data($this->post_data['country_id'])) : "";
            $pincode = !empty($this->post_data['pincode']) ? trim($this->Common_model->escape_data($this->post_data['pincode'])) : "";
            $latitude = !empty($this->post_data['latitude']) ? trim($this->Common_model->escape_data($this->post_data['latitude'])) : "";
            $longitude = !empty($this->post_data['longitude']) ? trim($this->Common_model->escape_data($this->post_data['longitude'])) : "";
            $year_of_exp = !empty($this->post_data['year_of_exp']) ? trim($this->Common_model->escape_data($this->post_data['year_of_exp'])) : "";
            $phone_number = !empty($this->post_data['phone_number']) ? trim($this->Common_model->escape_data($this->post_data['phone_number'])) : "";
            $locality = !empty($this->post_data['locality']) ? trim($this->Common_model->escape_data($this->post_data['locality'])) : "";
            $specialization_id = !empty($this->post_data['specialization_id']) ? trim($this->Common_model->escape_data($this->post_data['specialization_id'])) : "";
            $education_qualification = !empty($this->post_data['education_qualification']) ? trim(($this->post_data['education_qualification'])) : "";
            $registration_details = !empty($this->post_data['registration_details']) ? trim(($this->post_data['registration_details'])) : "";
            $speciality = !empty($this->post_data['speciality']) ? trim($this->Common_model->escape_data($this->post_data['speciality'])) : "";

            if (empty($user_id)) {
                $this->bad_request();
                exit;
            }
            $this->load->model("User_model");
            $check_user_exit = $this->User_model->get_user_exist($user_id);

            if (empty($check_user_exit)) {
                $this->my_response['status'] = false;
                $this->my_response['message'] = lang("user_not_found");
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

            if (!empty($phone_number) && validate_phone_number($phone_number)) {
                $this->my_response['status'] = false;
                $this->my_response['message'] = lang("invalid_phone_number");
                $this->send_response();
            }

            if (!empty($year_of_exp) && validate_date_only($year_of_exp)) {
                $this->my_response['status'] = false;
                $this->my_response['message'] = lang("invalid_date");
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
                                      user_id != '" . $user_id . "'
                                 AND 
                                      user_type = '" . $user_type . "' ";

                $check_email = $this->Common_model->get_single_row_by_query($check_email_sql);

                if (!empty($check_email)) {
                    $this->my_response['status'] = false;
                    $this->my_response['message'] = lang("user_register_email_exist");
                    $this->send_response();
                }
            }

            $update_data = array();
            if (!empty($first_name)) {
                $update_data['user_first_name'] = ucwords(strtolower($first_name));
            }
            if (!empty($last_name)) {
                $update_data['user_last_name'] = ucwords(strtolower($last_name));
            }

            if (!empty($gender)) {
                $update_data['user_gender'] = $gender;
            }
            if (!empty($phone_number)) {
                $check_number = $this->User_model->check_user_number_exists($phone_number, $user_type);
                if (!empty($check_number)) {
                    if ($check_number['user_id'] != $this->user_id) {
                        $this->my_response['status'] = false;
                        $this->my_response['message'] = lang('user_register_phone_number_exist');
                        $this->send_response();
                        exit;
                    }
                }
                //$update_data['user_phone_number'] = $phone_number;
            }


            $update_data['user_modified_at'] = $this->utc_time_formated;
            $user_is_updated = $this->User_model->update_profile($user_id, $update_data);

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
            if (!empty($locality)) {
                $update_address_data['address_locality'] = $locality;
            }
            $address_is_update = $this->User_model->update_address($user_id, $update_address_data);

            //update the user details
            $update_doctor_details = array();
            if (!empty($language)) {
                $update_doctor_details['doctor_detail_language_id'] = $language;
            }

            if (!empty($year_of_exp)) {
                $update_doctor_details['doctor_detail_year_of_experience'] = $year_of_exp;
            }

            if (!empty($speciality)) {
                $update_doctor_details['doctor_detail_speciality'] = $speciality;
            }

            $doctor_details_is_update = $this->doctor->update_doctor_details($user_id, $update_doctor_details);

            $phone_number_updated = 2;
            $email_updated = 2;

            //get old email id and phonenumber
            $getting_old_details = $this->User_model->get_user_details_by_id($user_id);
            
            //if existing phone number and old number are not same then send the otp for verification
            if (!empty($phone_number) && ($getting_old_details['user_phone_number'] != $phone_number)) {

                $otp = getUniqueToken(6, 'numeric');
                //$otp = '123456';

                $message = sprintf(OTP_MESSAGE, $otp);
                $send_otp = array(
                    'phone_number' => DEFAULT_COUNTRY_CODE . $phone_number,
                    'message' => $message,
                );
                $sening_sms = send_message_by_vibgyortel($send_otp);

                //$sening_sms = TRUE;
                if ($sening_sms) {

                    $auth_update_data = array(
                        'auth_code' => $otp,
                        'auth_otp_expiry_time' => date('Y-m-d H:i:s', strtotime(OTP_EXPIRE_TOKEN_TIME, strtotime($this->utc_time_formated))),
                        'auth_phone_number' => $phone_number
                    );
                    $auth_update_where = array(
                        'auth_user_id' => $user_id,
                        'auth_type' => 2
                    );
                    $this->User_model->update_auth_details($auth_update_data, $auth_update_where);
                    $phone_number_updated = 1;
                }
            }

            if (!empty($email) && (strtolower($getting_old_details['user_email']) != strtolower($email))) {
                $reset_token = str_rand_access_token(20);

                $user_auth_email_update_array = array(
                    'auth_code' => $reset_token,
                    'auth_otp_expiry_time' => date('Y-m-d H:i:s', strtotime(OTP_EXPIRE_TOKEN_TIME, strtotime($this->utc_time_formated))),
                    'auth_created_at' => $this->utc_time_formated,
                    'auth_phone_number' => $email
                );

                $auth_where = array(
                    'auth_user_id' => $user_id,
                    'auth_type' => 1
                );
                $is_update = $this->User_model->update_auth_details($user_auth_email_update_array, $auth_where);

                if ($is_update > 0) {

                    $email_updated = 1;
                    
                    $verify_link = DOMAIN_URL . "verifyaccount/" . $reset_token;

                    if (!empty($getting_old_details['user_first_name'])) {
                        $user_name = $getting_old_details['user_first_name'] . " " . $getting_old_details['user_last_name'];
                    } else {
                        $user_name = "User";
                    }

                    //this is use for get view and store data in variable
                    //EMAIL TEMPLATE START BY PRAGNESH
                    $this->load->model('Emailsetting_model');
                    $email_template_data = $this->Emailsetting_model->get_emailtemplate_by_id(12);
                    $parse_arr = array(
                        '{UserName}' => $user_name,
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
                }
            }

            if ($user_is_updated > 0 || $address_is_update > 0 || $doctor_details_is_update > 0) {

                /* entry into specialization table */
                if (!empty($specialization_id)) {
                    $this->doctor->add_specialization($user_id, $specialization_id, $_FILES);
                    unset($_FILES['specialization_images']);
                }
                /* entry into educations table */
                if (!empty($education_qualification)) {
                    if (!empty($education_qualification)) {
                        $this->doctor->add_educations_details($user_id, $education_qualification, $_FILES);
                        unset($_FILES['education_qualification']);
                    }
                }
                /* entry into educations table */
                if (!empty($registration_details)) {
                    $this->doctor->add_registrations_details($user_id, $registration_details, $_FILES);
                    unset($_FILES['registration_images']);
                }

                //send the flag wheter the phone number is update or not
                $getting_old_details['phone_number_updated'] = $phone_number_updated;
                
                if ($getting_old_details['user_email_verified'] == 1) {
                    if (!empty($getting_old_details['user_first_name'])) {
                        $user_name = $getting_old_details['user_first_name'] . " " . $getting_old_details['user_last_name'];
                    } else {
                        $user_name = "User";
                    }
                    $send_update_mail = array(
                        'user_name' => $user_name,
                        'user_email' => $getting_old_details['user_email'],
                        'template_id' => 24
                    );
                    $cron_job_path = CRON_PATH . " notification/send_mail_background/" . base64_encode(json_encode($send_update_mail));
                    exec(PHP_PATH . " " . $cron_job_path . " > /dev/null &");
                }
                
                
                $this->my_response['status'] = true;
                if ($email_updated == 1 && $phone_number_updated == 1) {
                    $this->my_response['message'] = lang("user_profile_update_success_phone_email");
                } else if ($email_updated == 1) {
                    $this->my_response['message'] = lang("user_profile_update_success_email");
                } else if ($phone_number_updated == 1) {
                    $this->my_response['message'] = lang("user_profile_update_success_phone");
                } else {
                    $this->my_response['message'] = lang("user_profile_update_success");
                }
                $this->my_response['data'] = $getting_old_details;
            } else {
                $this->my_response['status'] = false;
                $this->my_response['message'] = lang("user_profile_update_fail");
            }
            $this->send_response();
        } catch (Exception $ex) {
            $this->error = $ex->getMessage();
            $this->store_error();
        }
    }

    /**
     * Description :- This function is used to get the list of those doctors who are active and
     * doctor comes under clinic is also active
     * 
     * @author Manish Ramnani
     * 
     */
    public function search_doctor_post() {
        
        $search_keyword = !empty($this->post_data['search_keyword']) ? trim($this->Common_model->escape_data($this->post_data['search_keyword'])) : "";
        $filter_option = !empty($this->post_data['filter_option']) ? trim($this->Common_model->escape_data($this->post_data['filter_option'])) : "";
        $fees = !empty($this->post_data['fees']) ? trim($this->Common_model->escape_data($this->post_data['fees'])) : "";
        $year_of_experience = !empty($this->post_data['year_of_experience']) ? trim($this->Common_model->escape_data($this->post_data['year_of_experience'])) : "";
        $gender = !empty($this->post_data['gender']) ? trim($this->Common_model->escape_data($this->post_data['gender'])) : "";
        $doctor_type = !empty($this->post_data['doctor_type']) ? trim($this->Common_model->escape_data($this->post_data['doctor_type'])) : "";
        $language = !empty($this->post_data['language']) ? trim($this->Common_model->escape_data($this->post_data['language'])) : "";
        $patient_id = !empty($this->post_data['patient_id']) ? trim($this->Common_model->escape_data($this->post_data['patient_id'])) : "";
        $latitude = !empty($this->post_data['latitude']) ? trim($this->Common_model->escape_data($this->post_data['latitude'])) : "";
        $longitude = !empty($this->post_data['longitude']) ? trim($this->Common_model->escape_data($this->post_data['longitude'])) : "";
        $appointment_type = !empty($this->post_data['appointment_type']) ? trim($this->Common_model->escape_data($this->post_data['appointment_type'])) : "";
        $specialization = !empty($this->post_data['specialization']) ? trim($this->Common_model->escape_data($this->post_data['specialization'])) : "";
        $page = !empty($this->post_data['page']) ? trim($this->Common_model->escape_data($this->post_data['page'])) : "1";
        $per_page = !empty($this->post_data['per_page']) ? trim($this->Common_model->escape_data($this->post_data['per_page'])) : "10";
        
        /*get city and country id*/
        $city_name = !empty($this->post_data['city_name']) ? trim($this->Common_model->escape_data($this->post_data['city_name'])) : "";
        $country_name = !empty($this->post_data['country_name']) ? trim($this->Common_model->escape_data($this->post_data['country_name'])) : "";
        
        $cityId ='';
        $countryId ='';
        if(!empty($city_name)){
            $where_array = array(
                'city_name' => $city_name,
                'city_status' => 1
            );
            $columns = 'city_id';
            $cityDetails = $this->Common_model->get_single_row(TBL_CITIES, $columns, $where_array);
            $cityId = isset($cityDetails['city_id']) ? $cityDetails['city_id'] : '';
        }
        if(!empty($country_name)){
            $where = array(
                'country_name' => $country_name,
                'country_status' => '1'
            );
            $field_name = array("country_id");
            $countryDetails = $this->Common_model->get_single_row(TBL_COUNTRIES, $field_name, $where);
            $countryId = isset($countryDetails['country_id']) ? $countryDetails['country_id'] : '';
        }
        /*end get city and country id*/
        
        $option_array = !empty($filter_option) ? explode(',', $filter_option) : array();

        if (in_array(3, $option_array) && (empty($latitude) || empty($longitude)) || empty($patient_id)) {
            $this->bad_request();
            exit;
        }
        try {
            $fees_array = array();
            if (!empty($fees))
                $fees_array = explode('-', $fees);

            if (!empty($gender)) {
                if ($gender == 1) {
                    $gender = 'male';
                } else if ($gender == 2) {
                    $gender = 'female';
                }
            }


            if (!empty($fees)) {
                if (
                        (!empty($fees_array[0]) && !is_numeric($fees_array[0])) ||
                        (!empty($fees_array[1]) && (!is_numeric($fees_array[1]) && $fees_array[1] != '<' ))
                ) {
                    $this->my_response['status'] = true;
                    $this->my_response['message'] = lang('mycontroller_invalid_request');
                    $this->send_response();
                }
            }

            if (!empty($year_of_experience) && !is_numeric($year_of_experience)) {
                $this->my_response['status'] = true;
                $this->my_response['message'] = lang('mycontroller_invalid_request');
                $this->send_response();
            }

            /* Get primary doctor detail if page 1 */
            if ($page == 1) {
                $primary_doctor_data = $this->doctor->get_primary_doctor($patient_id, $page, 1, true);
                $primary_doctor_id = !empty($primary_doctor_data['user_id']) ? $primary_doctor_data['user_id'] : '';
            } else {
                $temp_page = 1;
                $primary_doctor_data = $this->doctor->get_primary_doctor($patient_id, $temp_page, 1, true);
                $primary_doctor_id = !empty($primary_doctor_data['user_id']) ? $primary_doctor_data['user_id'] : '';
                $primary_doctor_data = array();
            }
            

            if (
                    empty($search_keyword) &&
                    empty($fees_array) &&
                    empty($option_array) &&
                    empty($year_of_experience) &&
                    empty($gender) &&
                    empty($doctor_type) &&
                    empty($language) &&
                    empty($appointment_type) &&
                    empty($specialization)
            ) {
                $total_count = $this->doctor->get_primary_doctor($patient_id, $page, $per_page, false, true);
                $total_count--;
                $doctor_list = $this->doctor->get_primary_doctor($patient_id, $page, $per_page, false);
            } else {

                $filter_array = array(
                    'search_keyword' => $search_keyword,
                    'filter_option' => $option_array,
                    'fees_array' => $fees_array,
                    'year_of_experience' => $year_of_experience,
                    'gender' => $gender,
                    'doctor_type' => $doctor_type,
                    'language' => $language,
                    "patient_id" => $patient_id,
                    "latitude" => $latitude,
                    "longitude" => $longitude,
                    "countryId"=>$countryId,
                    "cityId"=>$cityId,
                    "page" => $page,
                    "per_page" => $per_page,
                    "appointment_type" => $appointment_type,
                    "primary_doctor" => $primary_doctor_id,
                    "specilization" => $specialization
                );
                $total_count = $this->doctor->get_doctor_search_list($this->user_id, $filter_array, true);
                $doctor_list = $this->doctor->get_doctor_search_list($this->user_id, $filter_array);
            }
            $final_array = array();
            if (!empty($doctor_list)) {
                foreach ($doctor_list as $doctor) {
                    $final_array[] = array(
                        "doctor_color_code" => 'null',
                        "doctor_user_id" => $doctor['user_id'],
                        "doctor_first_name" => DOCTOR.' '.$doctor['user_first_name'],
                        "doctor_last_name" => $doctor['user_last_name'],
                        "doctor_photo" => $doctor['user_photo_filepath'],
                        "doctor_experience" => $doctor['doctor_detail_year_of_experience'],
                        "doctor_specialisation" => implode(',', array_unique(explode(',', $doctor['specialization']))),
                        "doctor_fees" => $doctor['doctor_clinic_mapping_fees'],
                        "doctor_qualification" => $doctor['doctor_qualification_degree'],
                        "doctor_clinic_id" => $doctor['clinic_id'],
                        "doctor_timing_start" => $doctor['doctor_clinic_doctor_session_1_start_time'],
                        "doctor_timing_end" => $doctor['doctor_clinic_doctor_session_1_end_time'],
                        "doctor_desc" => $doctor['doctor_detail_desc'],
                        "doctor_known_languages" => $doctor['language'],
                        "doctor_clinic_service" => $doctor['clinic_services'],
                        "doctor_verification_status" => $doctor['user_status'],
                        "doctor_phone_number" => $doctor['user_phone_number'],
                        "doctor_speciality" => implode(',', array_unique(explode(',', $doctor['speciality']))),
                        "country_id" => $doctor['address_country_id'],
                        "city_id" => $doctor['address_city_id'],
                    );
                }
            }
            $this->my_response['status'] = true;
            $this->my_response['message'] = lang('common_detail_found');
            if (!empty($primary_doctor_data)) {

                $this->my_response['primary_doctor_data'] = array(
                    "doctor_color_code" => PRIMARY_DOCTOR_COLOR_CODE,
                    "doctor_user_id" => $primary_doctor_data['user_id'],
                    "doctor_first_name" => DOCTOR.' '.$primary_doctor_data['user_first_name'],
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
            }
            
            $this->my_response['doctor_data'] = $final_array;
            $this->my_response['total_count'] = $total_count;
            $this->my_response['per_page'] = $per_page;
            $this->my_response['current_page'] = $page;
            $this->send_response();
        } catch (Exception $ex) {
            $this->error = $ex->getMessage();
            $this->store_error();
        }
    }

    /**
     * Description :- This function is used to get the detail of the doctor and doctor belongs to the clinic id
     * 
     * @author Manish Ramnani
     * 
     */
    public function doctor_detail_post() {

        $doctor_id = !empty($this->post_data['doctor_id']) ? trim($this->Common_model->escape_data($this->post_data['doctor_id'])) : "";
        $clinic_id = !empty($this->post_data['clinic_id']) ? trim($this->Common_model->escape_data($this->post_data['clinic_id'])) : "";
        $appointment_type = !empty($this->post_data['appointment_type']) ? trim($this->Common_model->escape_data($this->post_data['appointment_type'])) : "";

        try {
            if (empty($doctor_id)) {
                $this->bad_request();
                exit;
            }
            /* GET doctor data from DB */
            $doctor_data = $this->doctor->doctor_detail($doctor_id, $clinic_id, '', $appointment_type);

            $final_doctor_array = array();
            $clinic_list = array();
            foreach ($doctor_data as $doctor) {
                if (empty($final_doctor_array)) {
                    $final_doctor_array = array(
                        "doctor_user_id" => $doctor['user_id'],
                        "doctor_first_name" => $doctor['user_first_name'],
                        "doctor_last_name" => $doctor['user_last_name'],
                        "doctor_email" => $doctor['user_email'],
                        "doctor_phone_number" => $doctor['user_phone_number'],
                        "doctor_photo" => $doctor['user_photo_filepath'],
                        "doctor_experience" => $doctor['doctor_detail_year_of_experience'],
                        "doctor_specialisation" => $doctor['specialization'],
                        "doctor_speciality" => implode(',', array_unique(explode(',', $doctor['speciality']))),
                        "doctor_timing_start" => !empty($doctor['doctor_availability_session_1_start_time']) ? $doctor['doctor_availability_session_1_start_time'] : '',
                        "doctor_timing_end" => !empty($doctor['doctor_availability_session_1_end_time']) ? $doctor['doctor_availability_session_1_end_time'] : '',
                        "doctor_timing_start2" => !empty($doctor['doctor_availability_session_2_start_time']) ? $doctor['doctor_availability_session_2_start_time'] : '',
                        "doctor_timing_end2" => !empty($doctor['doctor_availability_session_2_end_time']) ? $doctor['doctor_availability_session_2_end_time'] : '',
                        "doctor_fees" => $doctor['doctor_clinic_mapping_fees'],
                        "doctor_qualification" => $doctor['doctor_qualification_degree'],
                        "doctor_desc" => $doctor['doctor_detail_desc'],
                        "doctor_known_languages" => $doctor['language'],
                        "doctor_clinic_service" => $doctor['clinic_services'],
                        "doctor_verification_status" => $doctor['user_status'],
                        "doctor_phone_number" => $doctor['user_phone_number'],
                    );
                }

                $clinic_list[] = array(
                    "clinic_id" => $doctor['clinic_id'],
                    "clinic_name" => $doctor['clinic_name'],
                    "clinic_image" => $doctor['clinic_filepath'],
                    "clinic_address" => $doctor['address_name'],
                    "clinic_address1" => $doctor['address_name_one'],
                    "clinic_email" => $doctor['clinic_email'],
                    "clinic_phone_number" => $doctor['clinic_contact_number']
                );
            }
            $this->my_response['status'] = true;
            $this->my_response['message'] = lang('common_detail_found');
            $this->my_response['doctor_data'] = $final_doctor_array;
            $this->my_response['clinic_data'] = $clinic_list;
            $this->send_response();
        } catch (Exception $ex) {
            $this->error = $ex->getMessage();
            $this->store_error();
        }
    }

    /**
     * Description :- This function is used to get the availability of the doctor based on the clinic and datawise
     * 
     * @author Manish Ramnani
     * 
     */
    public function get_availability_post() {

        $doctor_id = !empty($this->post_data['doctor_id']) ? trim($this->Common_model->escape_data($this->post_data['doctor_id'])) : "";
        $clinic_id = !empty($this->post_data['clinic_id']) ? trim($this->Common_model->escape_data($this->post_data['clinic_id'])) : "";
        $date = !empty($this->post_data['date']) ? trim($this->Common_model->escape_data($this->post_data['date'])) : "";
        $appointment_type = !empty($this->post_data['appointment_type']) ? trim($this->Common_model->escape_data($this->post_data['appointment_type'])) : "";

        try {
            if (
                    empty($date) ||
                    empty($doctor_id) ||
                    empty($clinic_id)
            ) {
                $this->bad_request();
                exit;
            }

            if (validate_date_only($date)) {
                $this->my_response['status'] = false;
                $this->my_response['message'] = lang('invalid_date');
                $this->send_response();
            }

            $request_data = array(
                "doctor_id" => $doctor_id,
                "clinic_id" => $clinic_id,
                "date" => $date,
                "appointment_type" => $appointment_type
            );

            $timeslots_data = $this->doctor->get_doctor_availibility($request_data);

            $final_time_slot_array = array();
            if (!empty($timeslots_data)) {

                /* check doctor availibity for that time */
                $doctor_data = $this->doctor->get_availibity($request_data);

                /* check doctor block calender for that time */
                $doctor_block_data = $this->doctor->check_block_calender($request_data);

                /* For first time slots */
                $timeslots_data['start_session'] = $timeslots_data['doctor_availability_session_1_start_time'];
                $timeslots_data['end_session'] = $timeslots_data['doctor_availability_session_1_end_time'];

                $first_time_slot_array = $this->get_timeslot_availibity($timeslots_data, $doctor_data, $doctor_block_data);

                /* For second time slots */
                if (!empty($timeslots_data['doctor_availability_session_2_start_time'])) {
                    $timeslots_data['start_session'] = $timeslots_data['doctor_availability_session_2_start_time'];
                    $timeslots_data['end_session'] = $timeslots_data['doctor_availability_session_2_end_time'];
                    $second_time_slot_array = $this->get_timeslot_availibity($timeslots_data, $doctor_data, $doctor_block_data);
                } else {
                    $second_time_slot_array = array();
                }

                $final_time_slot_array = array_merge($first_time_slot_array, $second_time_slot_array);
            }

            $this->my_response['status'] = true;
            $this->my_response['data'] = $final_time_slot_array;
            $this->send_response();
        } catch (Exception $ex) {
            $this->error = $ex->getMessage();
            $this->store_error();
        }
    }

    public function get_timeslot_availibity($timeslots_data, $doctor_data, $doctor_block_data) {
        $final_time_slot_array = array();
        /* For first time slots */

        $availibility_id = $timeslots_data['doctor_availability_id'];
        for ($i = $timeslots_data['start_session']; strtotime($i) < strtotime($timeslots_data['end_session']);
        ) {

            $time = strtotime($i);
            $new_duration_time = date("H:i", strtotime($timeslots_data['doctor_clinic_mapping_duration'] . ' minutes', $time));
            $start_time = date("H:i", $time);
            $is_available = true;
            foreach ($doctor_data as $doctor_availability) {
                $already_appoint_start_date = date('H:i', strtotime($doctor_availability['appointment_from_time']));

                $already_appoint_end_date = date('H:i', strtotime($doctor_availability['appointment_to_time']));

                if (
                        ($already_appoint_start_date >= $start_time && $already_appoint_start_date < $new_duration_time) ||
                        ($already_appoint_end_date > $start_time && $already_appoint_end_date < $new_duration_time)
                ) {
                    $is_available = false;
                    break;
                }

                if (
                        ($already_appoint_start_date <= $start_time && $start_time < $already_appoint_end_date) ||
                        ($already_appoint_start_date < $new_duration_time && $new_duration_time < $already_appoint_end_date)
                ) {
                    $is_available = false;
                    break;
                }
            }


            /* check block data */
            if ($is_available) {

                foreach ($doctor_block_data as $block_data) {
                    $new_block_start_date = date("H:i", strtotime($block_data['calender_block_start_time']));
                    $new_block_end_date = date("H:i", strtotime($block_data['calender_block_end_time']));

                    if ($block_data['calender_block_duration_type'] == 1) {
                        $is_available = FALSE;
                        break;
                    }
                    if (
                            ($start_time >= $new_block_start_date && $start_time < $new_block_end_date) ||
                            ($new_duration_time > $new_block_start_date && $new_duration_time <= $new_block_end_date) ||
                            ($new_block_start_date >= $start_time && $new_block_start_date < $new_duration_time) ||
                            ($new_block_end_date > $start_time && $new_block_end_date <= $new_duration_time)
                    ) {
                        $is_available = false;
                        break;
                    }
                }
            }

            $i = date("Y-m-d H:i:s", strtotime($timeslots_data['doctor_clinic_mapping_duration'] . ' minutes', $time));

            if (strtotime($i) > strtotime($timeslots_data['end_session'])) {
                break;
            }

            $final_time_slot_array[] = array(
                "start_time" => $start_time,
                "end_time" => $new_duration_time,
                'is_available' => $is_available,
                'doctor_availability_id' => $availibility_id
            );
        }
        return $final_time_slot_array;
    }

    /**
     * Description :- This function is used to get the past history of the doctor
     * 
     * @author Manish Ramnani
     * 
     */
    public function doctor_past_history_post() {
        try {
            $doctor_id = !empty($this->post_data['doctor_id']) ? trim($this->Common_model->escape_data($this->post_data['doctor_id'])) : "";
            $patient_id = !empty($this->post_data['patient_id']) ? trim($this->Common_model->escape_data($this->post_data['patient_id'])) : "";
            $date = !empty($this->post_data['date']) ? trim($this->Common_model->escape_data($this->post_data['date'])) : "";


            if (
                    empty($doctor_id) ||
                    empty($patient_id)
            ) {
                $this->bad_request();
                exit;
            }


            /* get past data from db */
            $doctor_data = $this->doctor->get_past_history($doctor_id, $patient_id, $date);
            $this->my_response['status'] = true;
            $this->my_response['message'] = 'Past history';
            $past_history = array();
            if (!empty($doctor_data)) {
                foreach ($doctor_data as $doctor) {
                    unset($doctor['user_id']);
                    $past_history[] = $doctor;
                }
            }
            $this->my_response['data'] = $past_history;
            $this->send_response();
        } catch (Exception $ex) {
            $this->error = $ex->getMessage();
            $this->store_error();
        }
    }

    public function get_doctor_whole_details_post() {
        try {
            $doctor_data = $this->doctor->get_doctor_detail($this->user_id);

            $this->my_response['status'] = true;
            $this->my_response['data'] = '';
            if (!empty($doctor_data)) {
                $this->my_response['data'] = $doctor_data;
            }
            $this->send_response();
        } catch (Exception $ex) {
            $this->error = $ex->getMessage();
            $this->store_error();
        }
    }

    public function get_doctor_edu_details_post() {
        try {
            $doctor_edu_data = $this->doctor->get_doctor_edu_detail($this->user_id);
            $doctor_specialisation_data = $this->doctor->get_doctor_specialisation_detail($this->user_id);
            $doctor_speciality = $this->doctor->get_doctor_detail($this->user_id);

            $this->my_response['status'] = true;
            $this->my_response['edu_data'] = $doctor_edu_data;
            $this->my_response['specialisation_data'] = $doctor_specialisation_data;
            $this->my_response['speciality_data'] = $doctor_speciality['doctor_detail_speciality'];
            $this->send_response();
        } catch (Exception $ex) {
            $this->error = $ex->getMessage();
            $this->store_error();
        }
    }

    public function get_doctor_reg_details_post() {
        try {
            $doctor_reg_data = $this->doctor->get_doctor_reg_detail($this->user_id);


            $this->my_response['status'] = true;
            $this->my_response['reg_data'] = $doctor_reg_data;
            $this->send_response();
        } catch (Exception $ex) {
            $this->error = $ex->getMessage();
            $this->store_error();
        }
    }

    public function get_doctor_award_details_post() {
        try {
            $doctor_award_data = $this->doctor->get_doctor_award_detail($this->user_id);
            $this->my_response['status'] = true;
            $this->my_response['award_data'] = $doctor_award_data;
            $this->send_response();
        } catch (Exception $ex) {
            $this->error = $ex->getMessage();
            $this->store_error();
        }
    }

    public function update_doctor_other_details_post() {
        try {

            $specialisation_array = json_decode($this->post_data['specialization'], TRUE);
            $education_qualification_array = json_decode($this->post_data['education_qualification'], TRUE);
            $speciality = !empty($this->post_data['speciality']) ? trim($this->Common_model->escape_data($this->post_data['speciality'])) : "";

            //first specialisation update
            $this->doctor->update_specialisation_data($this->user_id, $specialisation_array, $_FILES);
            $this->doctor->update_education_data($this->user_id, $education_qualification_array, $_FILES);

            if (!empty($speciality)) {

                $update_speciality_data = array(
                    'doctor_detail_speciality' => $speciality,
                    'doctor_detail_modified_at' => $this->utc_time_formated
                );

                $speciality_where = array(
                    'doctor_detail_doctor_id' => $this->user_id,
                );
                $this->Common_model->update(TBL_DOCTOR_DETAILS, $update_speciality_data, $speciality_where);
            }

            $this->my_response['status'] = true;
            $this->my_response['message'] = lang('common_detail_update');
            $this->send_response();
        } catch (Exception $ex) {
            $this->error = $ex->getMessage();
            $this->store_error();
        }
    }

    public function update_doctor_other_reg_details_post() {
        try {

            $registration_details_array = json_decode($this->post_data['registration_details'], TRUE);

            //first specialisation update
            $this->doctor->update_registration_data($this->user_id, $registration_details_array, $_FILES);

            $this->my_response['status'] = true;
            $this->my_response['message'] = lang('common_detail_update');
            $this->send_response();
        } catch (Exception $ex) {
            $this->error = $ex->getMessage();
            $this->store_error();
        }
    }

    public function update_doctor_other_award_details_post() {
        try {

            $registration_details_array = json_decode($this->post_data['award_details'], TRUE);

            //first specialisation update
            $this->doctor->update_award_data($this->user_id, $registration_details_array, $_FILES);

            $this->my_response['status'] = true;
            $this->my_response['message'] = lang('common_detail_update');
            $this->send_response();
        } catch (Exception $ex) {
            $this->error = $ex->getMessage();
            $this->store_error();
        }
    }

    public function get_profile_per_post() {
        try {
            //get first tab count
            $personal_profile_data = $this->doctor->get_percentage($this->user_id);
            $filled_count = 0;

            if (!empty($personal_profile_data)) {
                foreach ($personal_profile_data as $key => $value) {
                    if (!empty($value))
                        $filled_count++;
                }
            }
            //get sec tab count
            $doctor_edu_data = $this->doctor->get_doctor_edu_detail($this->user_id, true);
            if (!empty($doctor_edu_data)) {
                $filled_count++;
            }
            $doctor_specialisation_data = $this->doctor->get_doctor_specialisation_detail($this->user_id, true);
            if (!empty($doctor_specialisation_data)) {
                $filled_count++;
            }

            $doctor_reg_data = $this->doctor->get_doctor_reg_detail($this->user_id, true);
            if (!empty($doctor_reg_data)) {
                $filled_count++;
            }

            $percentage = 0;
            if (!empty($filled_count)) {
                $percentage = round(($filled_count * 100) / 16, 0);
            }
            echo $percentage;
            exit;
        } catch (Exception $ex) {
            $this->error = $ex->getMessage();
            $this->store_error();
        }
    }

    /**
     * Description :- This function is used to add the block calendar of the particular doctor
     * if appointment exists with the doctor confirmation cancelling all the appointments
     * 
     * @author Manish Ramnani
     * 
     * Modified Date :- 2018-05-10
     * 
     */
    public function add_block_calendar_post() {

        try {

            $doctor_id = !empty($this->post_data['doctor_id']) ? trim($this->Common_model->escape_data($this->post_data['doctor_id'])) : "";
            $block_type = !empty($this->post_data['block_type']) ? trim($this->Common_model->escape_data($this->post_data['block_type'])) : "";
            $start_date = !empty($this->post_data['start_date']) ? trim($this->Common_model->escape_data($this->post_data['start_date'])) : "";
            $end_date = !empty($this->post_data['end_date']) ? trim($this->Common_model->escape_data($this->post_data['end_date'])) : "";
            $start_time = !empty($this->post_data['start_time']) ? trim($this->Common_model->escape_data($this->post_data['start_time'])) : "";
            $end_time = !empty($this->post_data['end_time']) ? trim($this->Common_model->escape_data($this->post_data['end_time'])) : "";
            $blockslot_date = !empty($this->post_data['blockslot_date']) ? trim($this->Common_model->escape_data($this->post_data['blockslot_date'])) : "";
            $details = !empty($this->post_data['details']) ? trim($this->Common_model->escape_data($this->post_data['details'])) : "";
            $cancel_appointment = !empty($this->post_data['cancel_appointment']) ? trim($this->Common_model->escape_data($this->post_data['cancel_appointment'])) : 2;
            $inserted_flag = 1;

            if ($block_type == 1 &&
                    (empty($start_date) ||
                    empty($end_date) ||
                    empty($doctor_id)
                    )
            ) {
                $this->bad_request();
            }

            if ($block_type == 2 &&
                    (empty($start_time) ||
                    empty($end_time) ||
                    empty($blockslot_date) ||
                    empty($doctor_id)
                    )
            ) {
                $this->bad_request();
            }

            if ($block_type == 1 && ( strtotime(date('Y-m-d', time())) > strtotime($start_date) || strtotime(date('Y-m-d', time())) > strtotime($end_date) )) {
                $this->my_response['status'] = false;
                $this->my_response['message'] = lang("invalid_date_time");
                $this->send_response();
            }

            if ($block_type == 2 && strtotime(date('Y-m-d', time())) > strtotime($blockslot_date)) {
                $this->my_response['status'] = false;
                $this->my_response['message'] = lang("invalid_date_time");
                $this->send_response();
            }

            $block_calendar_data = array(
                'doctor_id' => $doctor_id,
                'block_type' => $block_type,
                'start_date' => $start_date,
                'end_date' => $end_date,
                'block_slot_date' => $blockslot_date,
                'start_time' => $start_time,
                'end_time' => $end_time
            );
            $get_block_calendar_data = $this->doctor->calendar_block_date_exists($block_calendar_data);

            if (!empty($get_block_calendar_data)) {
                $this->my_response['status'] = false;
                $this->my_response['message'] = lang("block_calendar_already_added");
                $this->send_response();
            }

            $get_appointment_data = $this->doctor->appointment_exists_block_calendar($block_calendar_data);

            if (empty($get_appointment_data)) {
                $inserted_flag = 1;
            }

            if (!empty($get_appointment_data) && $cancel_appointment == 2) {
                $inserted_flag = 2;
                $this->my_response['status'] = false;
                $this->my_response['message'] = lang("have_appointment");
                $this->my_response['have_appointment'] = 1;
            }

            $appointment_ids = '';
            if ($cancel_appointment == 1 && !empty($get_appointment_data)) {

                foreach ($get_appointment_data as $single_appointment_data) {
                    $appointment_ids .= $single_appointment_data['appointment_id'] . ',';
                }

                $appointment_ids = rtrim($appointment_ids, ',');

//cancel all the appointments
                $cancel_appointment_sql = " UPDATE 
                                                " . TBL_APPOINTMENTS . " 
                                            SET 
                                                appointment_status = 9, 
                                                appointment_updated_at = '" . $this->utc_time_formated . "'  
                                            WHERE
                                                appointment_id IN (" . $appointment_ids . ") ";

                $this->Common_model->query($cancel_appointment_sql);
            }

            if ($inserted_flag == 1) {

                $insert_block_calendar = array(
                    'calender_block_user_id' => $doctor_id,
                    'calender_block_title' => $details,
                    'calender_block_duration_type' => $block_type,
                    'calender_block_created_at' => $this->utc_time_formated
                );

                if ($block_type == 1) {
                    $insert_block_calendar['calender_block_from_date'] = $start_date;
                    $insert_block_calendar['calender_block_to_date'] = $end_date;
                } else {
                    $insert_block_calendar['calender_block_from_date'] = $blockslot_date;
                    $insert_block_calendar['calender_block_start_time'] = $start_time;
                    $insert_block_calendar['calender_block_end_time'] = $end_time;
                }

                $inserted_id = $this->Common_model->insert(TBL_DOCTOR_CALENDER_BLOCK, $insert_block_calendar);

                if ($inserted_id > 0) {
                    $this->my_response['status'] = true;
                    $this->my_response['message'] = lang("block_calendar_added");
                    $this->my_response['have_appointment'] = 2;
                } else {
                    $this->my_response['status'] = false;
                    $this->my_response['message'] = lang("failure");
                    $this->my_response['have_appointment'] = 2;
                }
            }
            $this->send_response();
        } catch (ErrorException $ex) {
            $this->error = $ex->getMessage();
            $this->store_error();
        }
    }

    /**
     * Description :- This function is used to edit the block calendar of the particular doctor
     * if appointment exists with the doctor confirmation cancelling all the appointments
     * 
     * @author Manish Ramnani
     * 
     * Modified Date :- 2018-05-11
     * 
     */
    public function edit_block_calendar_post() {

        try {

            $block_calendar_id = !empty($this->post_data['block_calendar_id']) ? trim($this->Common_model->escape_data($this->post_data['block_calendar_id'])) : "";
            $doctor_id = !empty($this->post_data['doctor_id']) ? trim($this->Common_model->escape_data($this->post_data['doctor_id'])) : "";
            $block_type = !empty($this->post_data['block_type']) ? trim($this->Common_model->escape_data($this->post_data['block_type'])) : "";
            $start_date = !empty($this->post_data['start_date']) ? trim($this->Common_model->escape_data($this->post_data['start_date'])) : "";
            $end_date = !empty($this->post_data['end_date']) ? trim($this->Common_model->escape_data($this->post_data['end_date'])) : "";
            $start_time = !empty($this->post_data['start_time']) ? trim($this->Common_model->escape_data($this->post_data['start_time'])) : "";
            $end_time = !empty($this->post_data['end_time']) ? trim($this->Common_model->escape_data($this->post_data['end_time'])) : "";
            $blockslot_date = !empty($this->post_data['blockslot_date']) ? trim($this->Common_model->escape_data($this->post_data['blockslot_date'])) : "";
            $details = !empty($this->post_data['details']) ? trim($this->Common_model->escape_data($this->post_data['details'])) : "";
            $cancel_appointment = !empty($this->post_data['cancel_appointment']) ? trim($this->Common_model->escape_data($this->post_data['cancel_appointment'])) : 2;
            $update_flag = 1;

            if ($block_type == 1 &&
                    (empty($start_date) ||
                    empty($end_date) ||
                    empty($block_calendar_id) ||
                    empty($doctor_id)
                    )
            ) {
                $this->bad_request();
            }

            if ($block_type == 2 &&
                    (empty($start_time) ||
                    empty($end_time) ||
                    empty($blockslot_date) ||
                    empty($block_calendar_id) ||
                    empty($doctor_id)
                    )
            ) {
                $this->bad_request();
            }

            if ($block_type == 1 && ( strtotime(date('Y-m-d', time())) > strtotime($start_date) || strtotime(date('Y-m-d', time())) > strtotime($end_date) )) {
                $this->my_response['status'] = false;
                $this->my_response['message'] = lang("invalid_date_time");
                $this->send_response();
            }

            if ($block_type == 2 && strtotime(date('Y-m-d', time())) > strtotime($blockslot_date)) {
                $this->my_response['status'] = false;
                $this->my_response['message'] = lang("invalid_date_time");
                $this->send_response();
            }

            $block_calendar_data = array(
                'doctor_id' => $doctor_id,
                'block_type' => $block_type,
                'start_date' => $start_date,
                'end_date' => $end_date,
                'block_slot_date' => $blockslot_date,
                'start_time' => $start_time,
                'end_time' => $end_time,
                'block_calendar_id' => $block_calendar_id
            );
            $get_block_calendar_data = $this->doctor->calendar_block_date_exists($block_calendar_data);


            if (!empty($get_block_calendar_data)) {
                $this->my_response['status'] = false;
                $this->my_response['message'] = lang("block_calendar_already_added");
                $this->send_response();
            }

            $get_appointment_data = $this->doctor->appointment_exists_block_calendar($block_calendar_data);

            if (empty($get_appointment_data)) {
                $update_flag = 1;
            }

            if (!empty($get_appointment_data) && $cancel_appointment == 2) {
                $update_flag = 2;
                $this->my_response['status'] = false;
                $this->my_response['message'] = lang("have_appointment");
                $this->my_response['have_appointment'] = 1;
            }

            $appointment_ids = '';
            if ($cancel_appointment == 1 && !empty($get_appointment_data)) {

                foreach ($get_appointment_data as $single_appointment_data) {
                    $appointment_ids .= $single_appointment_data['appointment_id'] . ',';
                }

                $appointment_ids = rtrim($appointment_ids, ',');

//cancel all the appointments
                $cancel_appointment_sql = " UPDATE 
                                                " . TBL_APPOINTMENTS . " 
                                            SET 
                                                appointment_status = 9, 
                                                appointment_updated_at = '" . $this->utc_time_formated . "'  
                                            WHERE
                                                appointment_id IN (" . $appointment_ids . ") ";

                $this->Common_model->query($cancel_appointment_sql);
            }

            if ($update_flag == 1) {

                $update_block_calendar = array(
                    'calender_block_title' => $details,
                    'calender_block_duration_type' => $block_type,
                    'calender_block_updated_at' => $this->utc_time_formated
                );

                if ($block_type == 1) {
                    $update_block_calendar['calender_block_from_date'] = $start_date;
                    $update_block_calendar['calender_block_to_date'] = $end_date;
                } else {
                    $update_block_calendar['calender_block_from_date'] = $blockslot_date;
                    $update_block_calendar['calender_block_start_time'] = $start_time;
                    $update_block_calendar['calender_block_end_time'] = $end_time;
                }

                $where_block_calendar = array(
                    'calender_block_id' => $block_calendar_id
                );

                $is_updated = $this->Common_model->update(TBL_DOCTOR_CALENDER_BLOCK, $update_block_calendar, $where_block_calendar);

                if ($is_updated > 0) {
                    $this->my_response['status'] = true;
                    $this->my_response['message'] = lang("block_calendar_update");
                    $this->my_response['have_appointment'] = 2;
                } else {
                    $this->my_response['status'] = false;
                    $this->my_response['message'] = lang("failure");
                    $this->my_response['have_appointment'] = 2;
                }
            }
            $this->send_response();
        } catch (ErrorException $ex) {
            $this->error = $ex->getMessage();
            $this->store_error();
        }
    }

    /**
     * Description :- This function is used to remove the block calendar
     * 
     * @author Manish Ramnani
     * 
     * Modified Date :- 2018-05-11
     */
    public function delete_block_calendar_post() {

        try {
            $block_calendar_id = !empty($this->post_data['block_calendar_id']) ? trim($this->Common_model->escape_data($this->post_data['block_calendar_id'])) : "";

            if (empty($block_calendar_id)) {
                $this->bad_request();
            }

            $update_array = array(
                'calender_block_status' => 9,
                'calender_block_updated_at' => $this->utc_time_formated
            );

            $where_array = array(
                'calender_block_id' => $block_calendar_id
            );

            $is_update = $this->Common_model->update(TBL_DOCTOR_CALENDER_BLOCK, $update_array, $where_array);

            if ($is_update) {
                $this->my_response['status'] = true;
                $this->my_response['message'] = lang("block_calendar_delete");
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
     * Description :- This function is used to send 
     * the clinic availability for the particular day
     * 
     * @param type $day
     * @param type $array
     * @return type
     */
    public function search_clinic_availability_time($day, $array) {
        foreach ($array as $key => $val) {
            if ($val['clinic_availability_week_day'] == $day) {
                return $val;
            }
        }
        return null;
    }

    /**
     * Description :- This function is used to set the availablity of the doctor day wise
     * 
     * @author Manish Ramnani
     * 
     */
    public function set_doctor_avialability_post() {

        try {

            $clinic_id = !empty($this->post_data['clinic_id']) ? $this->Common_model->escape_data($this->post_data['clinic_id']) : '';
            $doctor_id = !empty($this->post_data['doctor_id']) ? $this->Common_model->escape_data($this->post_data['doctor_id']) : '';
            $appointment_type = !empty($this->post_data['appointment_type']) ? $this->Common_model->escape_data($this->post_data['appointment_type']) : '';
            $set_availability = !empty($this->post_data['set_availability']) ? $this->post_data['set_availability'] : '';
            $set_availability = json_decode($set_availability, true);

            if (empty($clinic_id) ||
                    empty($doctor_id) ||
                    empty($set_availability)
            ) {
                $this->bad_request();
            }

            $this->db->trans_start();

            $get_role_details = $this->Common_model->get_the_role($this->user_id);
            if (!empty($get_role_details['user_role_data'])) {
                $permission_data = array(
                    'role_data' => $get_role_details['user_role_data'],
                    'module' => 13,
                    'key' => 2
                );
                $check_module_permission = $this->check_module_permission($permission_data);
                if ($check_module_permission == 2) {
                    $this->my_response['status'] = false;
                    $this->my_response['message'] = lang('permission_error');
                    $this->send_response();
                }
            }

            $set_availability_array = array();


            //if already set the availability then delete it
            $requested_data = array(
                'clinic_id' => $clinic_id,
                'doctor_id' => $doctor_id,
                'appointment_type' => $appointment_type
            );

            $get_availablity = $this->doctor->get_doctor_availability($requested_data);
            $status = 1;
            if (!empty($get_availablity['doctor_availability_id'])) {
                $status = $get_availablity['doctor_availability_status'];
                $update_doctor_availablity = array(
                    'doctor_availability_status' => 9,
                    'doctor_availability_modified_at' => $this->utc_time_formated
                );

                $update_doctor_availablity_where = array(
                    'doctor_availability_clinic_id' => $clinic_id,
                    'doctor_availability_user_id' => $doctor_id,
                    'doctor_availability_appointment_type' => $appointment_type
                );

                $this->Common_model->update(TBL_DOCTOR_AVAILABILITY, $update_doctor_availablity, $update_doctor_availablity_where);
            }

            //get clinic time from doctor mapping table
            $clinic_avail_columns = 'clinic_availability_week_day,
                                    clinic_availability_session_1_start_time,
                                    clinic_availability_session_1_end_time,
                                    clinic_availability_session_2_start_time,
                                    clinic_availability_session_2_end_time';

            $clinic_avail_where = array(
                'clinic_availability_clinic_id' => $clinic_id,
                'clinic_availability_status' => 1
            );
            $get_clinic_avail_time = $this->Common_model->get_all_rows(TBL_CLINIC_AVAILABILITY, $clinic_avail_columns, $clinic_avail_where);

            foreach ($set_availability as $avilability) {

                $session_1_start_time = $avilability['session_1_start_time'];
                $session_1_end_time = $avilability['session_1_end_time'];
                $session_2_start_time = !empty($avilability['session_2_start_time']) ? $avilability['session_2_start_time'] : NULL;
                $session_2_end_time = !empty($avilability['session_2_end_time']) ? $avilability['session_2_end_time'] : NULL;

                $set_availability_array[] = array(
                    'doctor_availability_clinic_id' => $clinic_id,
                    'doctor_availability_user_id' => $doctor_id,
                    'doctor_availability_week_day' => $avilability['day'],
                    'doctor_availability_session_1_start_time' => $session_1_start_time,
                    'doctor_availability_session_1_end_time' => $session_1_end_time,
                    'doctor_availability_session_2_start_time' => $session_2_start_time,
                    'doctor_availability_session_2_end_time' => $session_2_end_time,
                    'doctor_availability_created_at' => $this->utc_time_formated,
                    'doctor_availability_appointment_type' => $appointment_type,
                    'doctor_availability_status' => $status,
                );

                if (validate_time($session_1_start_time) ||
                        validate_time($session_1_end_time)
                ) {
                    $this->db->trans_rollback();
                    $this->my_response['status'] = false;
                    $this->my_response['message'] = lang('invalid_time');
                    $this->send_response();
                }

                if (
                        (!empty($session_2_start_time) && validate_time($session_2_start_time)) ||
                        (!empty($session_2_end_time) && validate_time($session_2_end_time))
                ) {
                    $this->db->trans_rollback();
                    $this->my_response['status'] = false;
                    $this->my_response['message'] = lang('invalid_time');
                    $this->send_response();
                }

                $start_1_timestamp = strtotime($session_1_start_time . ":00");
                $end_1_timestamp = strtotime($session_1_end_time . ":00");

                $get_day_wise_time = $this->search_clinic_availability_time($avilability['day'], $get_clinic_avail_time);

                $stored_start_1_timestamp = '';
                $stored_end_1_timestamp = '';

                if (!empty($get_day_wise_time)) {
                    $stored_start_1_timestamp = strtotime($get_day_wise_time['clinic_availability_session_1_start_time']);
                    $stored_end_1_timestamp = strtotime($get_day_wise_time['clinic_availability_session_1_end_time']);
                }


                if (!empty($session_2_start_time) && !empty($session_2_end_time)) {

                    $start_2_timestamp = strtotime($session_2_start_time . ":00");
                    $end_2_timestamp = strtotime($session_2_end_time . ":00");
                    $stored_start_2_timestamp = '';
                    $stored_end_2_timestamp = '';

                    if (!empty($get_day_wise_time)) {
                        $stored_start_2_timestamp = strtotime($get_day_wise_time['clinic_availability_session_2_start_time']);
                        $stored_end_2_timestamp = strtotime($get_day_wise_time['clinic_availability_session_2_end_time']);
                    }

                    if ($start_2_timestamp >= $end_2_timestamp) {
                        $this->db->trans_rollback();
                        $this->my_response['status'] = false;
                        $this->my_response['message'] = lang('invalid_time');
                        $this->send_response();
                    }

                    if (
                            (!empty($stored_start_2_timestamp) && !empty($stored_end_2_timestamp) ) &&
                            ($start_2_timestamp < $stored_start_2_timestamp || $end_2_timestamp > $stored_end_2_timestamp)
                    ) {
                        $this->db->trans_rollback();
                        $this->my_response['status'] = false;
                        $this->my_response['message'] = lang('invalid_availability');
                        $this->send_response();
                    }
                }

                if ($start_1_timestamp >= $end_1_timestamp) {
                    $this->db->trans_rollback();
                    $this->my_response['status'] = false;
                    $this->my_response['message'] = lang('invalid_time');
                    $this->send_response();
                }

                if ((!empty($stored_start_1_timestamp) && !empty($stored_end_1_timestamp) ) &&
                        ($start_1_timestamp < $stored_start_1_timestamp || $end_1_timestamp > $stored_end_1_timestamp)
                ) {
                    $this->db->trans_rollback();
                    $this->my_response['status'] = false;
                    $this->my_response['message'] = lang('invalid_availability');
                    $this->send_response();
                }
            }

            $this->Common_model->insert_multiple(TBL_DOCTOR_AVAILABILITY, $set_availability_array);

            if ($this->db->trans_status() !== FALSE) {
                $this->db->trans_commit();
                $this->my_response['status'] = true;
                $this->my_response['message'] = lang('availability_set');
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
     * Description :- This function is used to get the avialability of the doctor as well as the clinic
     * 
     * @author Manish Ramnani
     * 
     */
    public function get_doctor_avialability_post() {

        try {
            $clinic_id = !empty($this->post_data['clinic_id']) ? $this->Common_model->escape_data($this->post_data['clinic_id']) : '';
            $doctor_id = !empty($this->post_data['doctor_id']) ? $this->Common_model->escape_data($this->post_data['doctor_id']) : '';

            if (empty($doctor_id) ||
                    empty($clinic_id)
            ) {
                $this->bad_request();
            }

            $get_role_details = $this->Common_model->get_the_role($this->user_id);
            if (!empty($get_role_details['user_role_data'])) {
                $permission_data = array(
                    'role_data' => $get_role_details['user_role_data'],
                    'module' => 13,
                    'key' => 3
                );
                $check_module_permission = $this->check_module_permission($permission_data);
                if ($check_module_permission == 2) {
                    $this->my_response['status'] = false;
                    $this->my_response['message'] = lang('permission_error');
                    $this->send_response();
                }
            }


            $clinic_columns = "clinic_availability_id, 
                               clinic_availability_clinic_id, 
                               clinic_availability_week_day, 
                               clinic_availability_session_1_start_time, 
                               clinic_availability_session_1_end_time, 
                               clinic_availability_session_2_start_time, 
                               clinic_availability_session_2_end_time, 
                               clinic_availability_created_at, 
                               clinic_availability_status ";

            $clinic_where_availability = array(
                'clinic_availability_clinic_id' => $clinic_id,
                'clinic_availability_status !=' => 9
            );

            $get_clinic_availability = $this->Common_model->get_all_rows(TBL_CLINIC_AVAILABILITY, $clinic_columns, $clinic_where_availability);

            $columns = "doctor_availability_id, 
                    doctor_availability_clinic_id, 
                    doctor_availability_user_id, 
                    doctor_availability_week_day, 
                    doctor_availability_appointment_type,
                    doctor_availability_session_1_start_time,
                    doctor_availability_session_1_end_time, 
                    doctor_availability_session_2_start_time, 
                    doctor_availability_session_2_end_time,
                    doctor_availability_status";


            $where_availability = array(
                'doctor_availability_clinic_id' => $clinic_id,
                'doctor_availability_user_id' => $doctor_id,
                'doctor_availability_status !=' => 9
            );

            $get_doctor_availability = $this->Common_model->get_all_rows(TBL_DOCTOR_AVAILABILITY, $columns, $where_availability);
            $send_availability = array();

            if (!empty($get_doctor_availability)) {

                $appointment_type = array(1);
                $alreay_appointment_type = array();

                foreach ($get_doctor_availability as $availability) {
                    $send_availability[$availability['doctor_availability_appointment_type']][] = $availability;
                }

                $final_array = array();
                $index = 0;
                foreach ($send_availability as $key => $data) {
                    $alreay_appointment_type[] = $key;
                    $final_array[$index]['doctor_availability_appointment_type'] = $key;
                    $final_array[$index]['data'] = $data;
                    $index++;
                }

                $appointment_type_difference = array_diff($appointment_type, $alreay_appointment_type);

                foreach ($appointment_type_difference as $key => $data) {
                    $final_array[$index]['doctor_availability_appointment_type'] = $data;
                    $final_array[$index]['data'] = array();
                    $index++;
                }

                $final_array = msort($final_array, 'doctor_availability_appointment_type');

                $this->my_response['status'] = true;
                $this->my_response['message'] = lang('common_detail_found');
                $this->my_response['data'] = $final_array;
                $this->my_response['clinic_availability'] = $get_clinic_availability;
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
     * Description :- This function is used to set the availablity of the doctor day wise
     * 
     * @author Manish Ramnani
     * 
     */
    public function set_doctor_avialability_status_post() {

        try {

            $clinic_id = !empty($this->post_data['clinic_id']) ? $this->Common_model->escape_data($this->post_data['clinic_id']) : '';
            $doctor_id = !empty($this->post_data['doctor_id']) ? $this->Common_model->escape_data($this->post_data['doctor_id']) : '';
            $appointment_type = !empty($this->post_data['appointment_type']) ? $this->Common_model->escape_data($this->post_data['appointment_type']) : '';
            $status = !empty($this->post_data['status']) ? $this->Common_model->escape_data($this->post_data['status']) : '';

            if (empty($clinic_id) ||
                    empty($doctor_id) ||
                    empty($appointment_type)
            ) {
                $this->bad_request();
            }


            $get_role_details = $this->Common_model->get_the_role($this->user_id);
            if (!empty($get_role_details['user_role_data'])) {
                $permission_data = array(
                    'role_data' => $get_role_details['user_role_data'],
                    'module' => 13,
                    'key' => 2
                );
                $check_module_permission = $this->check_module_permission($permission_data);
                if ($check_module_permission == 2) {
                    $this->my_response['status'] = false;
                    $this->my_response['message'] = lang('permission_error');
                    $this->send_response();
                }
            }


            $update_doctor_availablity = array(
                'doctor_availability_status' => $status,
                'doctor_availability_modified_at' => $this->utc_time_formated
            );

            $update_doctor_availablity_where = array(
                'doctor_availability_clinic_id' => $clinic_id,
                'doctor_availability_user_id' => $doctor_id,
                'doctor_availability_appointment_type' => $appointment_type,
                'doctor_availability_status !=' => 9
            );

            $is_update = $this->Common_model->update(TBL_DOCTOR_AVAILABILITY, $update_doctor_availablity, $update_doctor_availablity_where);

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
     * Description :- This function is used to set the doctor alert
     * 
     * @author Manish Ramnani
     * 
     */
    public function set_doctor_alert_post() {


        try {

            $clinic_id = !empty($this->post_data['clinic_id']) ? $this->Common_model->escape_data($this->post_data['clinic_id']) : '';
            $doctor_id = !empty($this->post_data['doctor_id']) ? $this->Common_model->escape_data($this->post_data['doctor_id']) : '';
            $language_id = !empty($this->post_data['language_id']) ? $this->Common_model->escape_data($this->post_data['language_id']) : '';
            $setting_array = !empty($this->post_data['settings']) ? $this->post_data['settings'] : '';
            $setting_array = json_decode($setting_array, true);


            if (empty($clinic_id) ||
                    empty($doctor_id) ||
                    empty($setting_array) ||
                    empty($language_id)
            ) {
                $this->bad_request();
            }

            $category_type = array(
                "appointment" => 1,
                "payment" => 2
            );

            $setting_type = array(
                "confirmation" => 1,
                "rescheduling" => 2,
                "cancellation" => 3,
                "payment" => 4
            );

            //check alreay setting entry is their or not
            $where_communication_setting = array(
                'communication_setting_clinic_id' => $clinic_id,
                'communication_setting_doctor_id' => $doctor_id,
                'communication_setting_status' => 1
            );
            $get_setting_array = $this->Common_model->get_all_rows(TBL_COMMUNICATION_SETTING, '*', $where_communication_setting);
            $set_setting_array = array();
            $is_update = 1;

            if (!empty($get_setting_array)) {

                $update_data = array(
                    'communication_setting_status' => 9,
                    'communication_setting_updated_at' => $this->utc_time_formated
                );

                $update_data_where = array(
                    'communication_setting_clinic_id' => $clinic_id,
                    'communication_setting_doctor_id' => $doctor_id
                );

                $is_update = $this->Common_model->update(TBL_COMMUNICATION_SETTING, $update_data, $update_data_where);
            }

            if ($is_update > 0) {

                foreach ($setting_array as $category_key => $settings) {

                    foreach ($settings as $key => $setting) {

                        $set_setting_array[] = array(
                            'communication_setting_clinic_id' => $clinic_id,
                            'communication_setting_doctor_id' => $doctor_id,
                            'communication_setting_communication_language_id' => $language_id,
                            'communication_setting_category' => $category_type[$category_key],
                            'communication_setting_type' => $setting_type[$key],
                            'communication_setting_sms_status' => $setting['sms'],
                            'communication_setting_email_status' => $setting['email'],
                            'communication_setting_push_status' => $setting['push'],
                            'communication_setting_created_at' => $this->utc_time_formated
                        );
                    }
                }

                $this->Common_model->insert_multiple(TBL_COMMUNICATION_SETTING, $set_setting_array);
                $this->my_response['status'] = true;
                $this->my_response['message'] = lang('setting_set');
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
     * Description :- This function is used to get the alert setting of the doctor
     * 
     * @author Manish Ramnani
     */
    public function get_doctor_alert_post() {
        try {

            $clinic_id = !empty($this->post_data['clinic_id']) ? $this->Common_model->escape_data($this->post_data['clinic_id']) : '';
            $doctor_id = !empty($this->post_data['doctor_id']) ? $this->Common_model->escape_data($this->post_data['doctor_id']) : '';

            if (empty($clinic_id) ||
                    empty($doctor_id)
            ) {
                $this->bad_request();
            }

            $columns = 'communication_setting_id, 
                            communication_setting_clinic_id, 
                            communication_setting_doctor_id, 
                            communication_setting_communication_language_id, 
                            communication_setting_category, 
                            communication_setting_type, 
                            communication_setting_sms_status, 
                            communication_setting_email_status, 
                            communication_setting_push_status';

            $where = array(
                'communication_setting_clinic_id' => $clinic_id,
                'communication_setting_doctor_id' => $doctor_id,
                'communication_setting_status' => 1
            );

            $get_alert_settings = $this->Common_model->get_all_rows(TBL_COMMUNICATION_SETTING, $columns, $where);

            $alert_setting = array();
            foreach ($get_alert_settings as $setting) {
                $alert_setting[$setting['communication_setting_category']][] = $setting;
            }

            $final_array = array();
            $index = 0;
            foreach ($alert_setting as $key => $data) {
                $final_array[$index]['category_type'] = $key;
                $final_array[$index]['data'] = $data;
                $index++;
            }

            if (!empty($get_alert_settings)) {
                $this->my_response['status'] = true;
                $this->my_response['message'] = lang('common_detail_found');
                $this->my_response['data'] = $final_array;
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
     * Description :- This function is used to get the list of 
     * the patient based on the clinic and doctor wise
     * 
     * @author Manish Ramnani
     * 
     */
    public function get_doctor_patient_list_post() {

        try {

            $clinic_id = !empty($this->Common_model->escape_data($this->post_data['clinic_id'])) ? trim($this->Common_model->escape_data($this->post_data['clinic_id'])) : '';
            $doctor_id = !empty($this->Common_model->escape_data($this->post_data['doctor_id'])) ? trim($this->Common_model->escape_data($this->post_data['doctor_id'])) : '';
            $patient_list_type = !empty($this->Common_model->escape_data($this->post_data['patient_list_type'])) ? trim($this->Common_model->escape_data($this->post_data['patient_list_type'])) : '';
            $page = !empty($this->Common_model->escape_data($this->post_data['page'])) ? trim($this->Common_model->escape_data($this->post_data['page'])) : '1';
            $per_page = !empty($this->Common_model->escape_data($this->post_data['per_page'])) ? trim($this->Common_model->escape_data($this->post_data['per_page'])) : '10';

            if (empty($clinic_id) ||
                    empty($doctor_id) ||
                    empty($patient_list_type)
            ) {
                $this->bad_request();
            }

            if (!is_numeric($clinic_id) ||
                    !is_numeric($doctor_id) ||
                    !in_array($patient_list_type, array(1, 2, 3))
            ) {
                $this->bad_request();
            }

            $requested_data = array(
                'clinic_id' => $clinic_id,
                'doctor_id' => $doctor_id,
                'patient_list_type' => $patient_list_type,
                "page" => $page,
                "per_page" => $per_page,
            );
            $patient_data = $this->doctor->get_doctor_patient_list($requested_data);

            if (!empty($patient_data['patient_data'])) {
                $this->my_response['status'] = true;
                $this->my_response['message'] = lang('common_detail_found');
                $this->my_response['data'] = $patient_data;
            } else {
                $this->my_response['status'] = false;
                $this->my_response['message'] = lang('common_detail_not_found');
                $this->my_response['data'] = array();
            }

            $this->send_response();
        } catch (ErrorException $ex) {
            $this->error = $ex->getMessage();
            $this->store_error();
        }
    }

    public function get_doctor_patient_detail_post() {
        try {

            $clinic_id = !empty($this->Common_model->escape_data($this->post_data['clinic_id'])) ? trim($this->Common_model->escape_data($this->post_data['clinic_id'])) : '';
            $doctor_id = !empty($this->Common_model->escape_data($this->post_data['doctor_id'])) ? trim($this->Common_model->escape_data($this->post_data['doctor_id'])) : '';
            $patient_id = !empty($this->Common_model->escape_data($this->post_data['patient_id'])) ? trim($this->Common_model->escape_data($this->post_data['patient_id'])) : '';
            $appointment_id = !empty($this->Common_model->escape_data($this->post_data['appointment_id'])) ? trim($this->Common_model->escape_data($this->post_data['appointment_id'])) : '';

            if (empty($clinic_id) ||
                    empty($doctor_id) ||
                    empty($patient_id) ||
                    empty($appointment_id) ||
                    !is_numeric($clinic_id) ||
                    !is_numeric($doctor_id) ||
                    !is_numeric($patient_id) ||
                    !is_numeric($appointment_id)
            ) {
                $this->bad_request();
            }

            $requested_data = array(
                'clinic_id' => $clinic_id,
                'doctor_id' => $doctor_id,
                'patient_id' => $patient_id,
                'appointment_id' => $appointment_id
            );

            $get_patient_detail = $this->doctor->get_doctor_patient_detail($requested_data);

            if (!empty($get_patient_detail)) {

                $get_vital_data = $this->Common_model->get_vital_data($patient_id);

                if (!empty($get_vital_data['vital_report_weight'])) {
                    $get_patient_detail['user_details_weight'] = $get_vital_data['vital_report_weight'];
                }

                $this->my_response['status'] = true;
                $this->my_response['message'] = lang('common_detail_found');
                $this->my_response['data'] = $get_patient_detail;
            } else {
                $this->my_response['status'] = false;
                $this->my_response['message'] = lang('common_detail_not_found');
                $this->my_response['data'] = array();
            }
            $this->send_response();
        } catch (ErrorException $ex) {
            $this->error = $ex->getMessage();
            $this->store_error();
        }
    }

    public function search_web_doctor_post() {

        try {
            $doctor_id = !empty($this->Common_model->escape_data($this->post_data['doctor_id'])) ? trim($this->Common_model->escape_data($this->post_data['doctor_id'])) : '';
            $search = !empty($this->Common_model->escape_data($this->post_data['search'])) ? trim($this->Common_model->escape_data($this->post_data['search'])) : '';


            if (empty($doctor_id)) {
                $this->bad_request();
                exit;
            }

            $request_data = array(
                "doctor_id" => $doctor_id,
                "search" => $search,
            );
            $search_doctor_result = $this->doctor->get_search_doctor($request_data);
            $this->my_response['status'] = true;
            $this->my_response['data'] = $search_doctor_result;
            $this->send_response();
        } catch (Exception $ex) {
            $this->error = $ex->getMessage();
            $this->store_error();
        }
    }

    public function fav_doctor_post() {

        try {
            $doctor_id = !empty($this->Common_model->escape_data($this->post_data['doctor_id'])) ? trim($this->Common_model->escape_data($this->post_data['doctor_id'])) : '';
            $fav_user_id = !empty($this->Common_model->escape_data($this->post_data['fav_user_id'])) ? trim($this->Common_model->escape_data($this->post_data['fav_user_id'])) : '';
            $is_fav = !empty($this->Common_model->escape_data($this->post_data['is_fav'])) ? trim($this->Common_model->escape_data($this->post_data['is_fav'])) : '';


            if (empty($doctor_id) ||
                    empty($fav_user_id)) {
                $this->bad_request();
                exit;
            }
            if (empty($is_fav)) {
                $is_fav = 1;
            }
            $where = array(
                "fav_doctors_other_doctor_id" => $fav_user_id,
                "fav_doctors_status" => 1,
                "fav_doctors_doctor_id" => $doctor_id
            );

            $get_role_details = $this->Common_model->get_the_role($this->user_id);

            //remove fav doctor
            if ($is_fav == 2) {
                if (!empty($get_role_details['user_role_data'])) {
                    $permission_data = array(
                        'role_data' => $get_role_details['user_role_data'],
                        'module' => 22,
                        'key' => 4
                    );
                    $check_module_permission = $this->check_module_permission($permission_data);
                    if ($check_module_permission == 2) {
                        $this->my_response['status'] = false;
                        $this->my_response['message'] = lang('permission_error');
                        $this->send_response();
                    }
                }

                //remove fav doctor
                $update_data = array(
                    "fav_doctors_status" => 9,
                    "fav_doctors_modified_at" => $this->utc_time_formated,
                );
                $is_deleted = $this->Common_model->update(TBL_FAV_DOCTORS, $update_data, $where);
                if ($is_deleted > 0) {
                    $this->my_response['status'] = true;
                    $this->my_response['message'] = lang("fav_remove_success");
                } else {
                    $this->my_response['status'] = false;
                    $this->my_response['message'] = lang("failure");
                }
            } else {
                //check fav added already

                if (!empty($get_role_details['user_role_data'])) {
                    $permission_data = array(
                        'role_data' => $get_role_details['user_role_data'],
                        'module' => 22,
                        'key' => 1
                    );
                    $check_module_permission = $this->check_module_permission($permission_data);
                    if ($check_module_permission == 2) {
                        $this->my_response['status'] = false;
                        $this->my_response['message'] = lang('permission_error');
                        $this->send_response();
                    }
                }

                $is_exist = $this->Common_model->get_single_row(TBL_FAV_DOCTORS, 'fav_doctors_id', $where);
                if (!empty($is_exist)) {
                    $this->my_response['status'] = false;
                    $this->my_response['message'] = lang("fav_already_added");
                } else {
                    //insert new records
                    $insert_data = array(
                        "fav_doctors_doctor_id" => $doctor_id,
                        "fav_doctors_other_doctor_id" => $fav_user_id,
                        "fav_doctors_created_at" => $this->utc_time_formated,
                        "fav_doctors_status" => 1
                    );
                    $inserted_id = $this->Common_model->insert(TBL_FAV_DOCTORS, $insert_data);
                    if ($inserted_id > 0) {
                        $this->my_response['status'] = true;
                        $this->my_response['message'] = lang("fav_added_success");
                    } else {
                        $this->my_response['status'] = false;
                        $this->my_response['message'] = lang("failure");
                    }
                }
            }
            $this->send_response();
        } catch (Exception $ex) {
            $this->error = $ex->getMessage();
            $this->store_error();
        }
    }

    public function get_fav_doctor_listing_post() {

        try {
            $doctor_id = !empty($this->Common_model->escape_data($this->post_data['doctor_id'])) ? trim($this->Common_model->escape_data($this->post_data['doctor_id'])) : '';
            $search = !empty($this->Common_model->escape_data($this->post_data['search'])) ? trim($this->Common_model->escape_data($this->post_data['search'])) : '';

            if (empty($doctor_id)) {
                $this->bad_request();
                exit;
            }

            $get_role_details = $this->Common_model->get_the_role($this->user_id);
            if (!empty($get_role_details['user_role_data'])) {
                $permission_data = array(
                    'role_data' => $get_role_details['user_role_data'],
                    'module' => 22,
                    'key' => 3
                );
                $check_module_permission = $this->check_module_permission($permission_data);
                if ($check_module_permission == 2) {
                    $this->my_response['status'] = false;
                    $this->my_response['message'] = lang('permission_error');
                    $this->send_response();
                }
            }

            $request_data = array(
                "doctor_id" => $doctor_id,
                "search" => $search
            );
            $doctor_data = $this->doctor->get_fav_doctor($request_data);
            $this->my_response['status'] = true;
            $this->my_response['data'] = $doctor_data;

            $this->send_response();
        } catch (Exception $ex) {
            $this->error = $ex->getMessage();
            $this->store_error();
        }
    }

    /**
     * Description :- This function is used to add the refer by the doctor
     * 
     * @author Manish Ramnani
     * 
     */
    public function add_refer_post() {

        try {

            $doctor_id = !empty($this->Common_model->escape_data($this->post_data['doctor_id'])) ? trim($this->Common_model->escape_data($this->post_data['doctor_id'])) : '';
            $patient_id = !empty($this->Common_model->escape_data($this->post_data['patient_id'])) ? trim($this->Common_model->escape_data($this->post_data['patient_id'])) : '';
            $other_doctor_id = !empty($this->Common_model->escape_data($this->post_data['other_doctor_id'])) ? trim($this->Common_model->escape_data($this->post_data['other_doctor_id'])) : '';

            if (empty($doctor_id) ||
                    empty($patient_id) ||
                    empty($other_doctor_id)
            ) {
                $this->bad_request();
                exit;
            }

            //check doctor id exists or not
            $user_where = array(
                'user_id' => $other_doctor_id,
                'user_type' => 2,
                'user_status !=' => 9
            );
            $is_valid_data = $this->Common_model->validate_data(TBL_USERS, 'user_id', $user_where);

            if ($is_valid_data == 2) {
                $this->my_response['status'] = false;
                $this->my_response['message'] = lang("mycontroller_invalid_request");
                $this->send_response();
            }

            //check already refered or not
            $refer_where = array(
                'refer_doctor_id' => $doctor_id,
                'refer_user_id' => $patient_id,
                'refer_other_doctor_id' => $other_doctor_id
            );

            $get_refer_data = $this->Common_model->get_single_row(TBL_REFER, 'refer_id', $refer_where);

            if (!empty($get_refer_data)) {
                $this->my_response['status'] = false;
                $this->my_response['message'] = lang('already_refer');
                $this->send_response();
            }

            $insert_refer = array(
                'refer_doctor_id' => $doctor_id,
                'refer_user_id' => $patient_id,
                'refer_other_doctor_id' => $other_doctor_id,
                'refer_created_at' => $this->utc_time_formated
            );

            $inserted_id = $this->Common_model->insert(TBL_REFER, $insert_refer);

            if ($inserted_id > 0) {
                $this->my_response['status'] = true;
                $this->my_response['message'] = lang('refer_added');
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

    public function get_refer_post() {

        try {

            $flag = !empty($this->Common_model->escape_data($this->post_data['flag'])) ? trim($this->Common_model->escape_data($this->post_data['flag'])) : '';

            if (empty($flag)) {
                $this->bad_request();
                exit;
            }

            $send_data = array(
                'user_id' => $this->user_id,
                'flag' => $flag
            );

            $get_refer_data = $this->doctor->get_refer($send_data);

            if (!empty($get_refer_data)) {
                $this->my_response['status'] = true;
                $this->my_response['message'] = lang('common_detail_found');
                $this->my_response['data'] = $get_refer_data;
            } else {
                $this->my_response['status'] = true;
                $this->my_response['message'] = lang('common_detail_not_found');
            }

            $this->send_response();
        } catch (ErrorException $ex) {
            $this->error = $ex->getMessage();
            $this->store_error();
        }
    }

    /**
     * Description :- This function is used to share the records of the patient by the doctor
     * 
     * @author Manish Ramnani
     * 
     */
    public function share_record_post() {
        try {
            
            error_reporting(E_ALL);
            ini_set("display_errors", "-1");
            
            $patient_id = !empty($this->Common_model->escape_data($this->post_data['patient_id'])) ? trim($this->Common_model->escape_data($this->post_data['patient_id'])) : '';
            $appointment_id = !empty($this->Common_model->escape_data($this->post_data['appointment_id'])) ? trim($this->Common_model->escape_data($this->post_data['appointment_id'])) : '';
            $doctor_id = !empty($this->Common_model->escape_data($this->post_data['doctor_id'])) ? trim($this->Common_model->escape_data($this->post_data['doctor_id'])) : '';
            $with_vitalsign = !empty($this->Common_model->escape_data($this->post_data['with_vitalsign'])) ? trim($this->Common_model->escape_data($this->post_data['with_vitalsign'])) : '';
            $with_clinicnote = !empty($this->Common_model->escape_data($this->post_data['with_clinicnote'])) ? trim($this->Common_model->escape_data($this->post_data['with_clinicnote'])) : '';
            $with_prescription = !empty($this->Common_model->escape_data($this->post_data['with_prescription'])) ? trim($this->Common_model->escape_data($this->post_data['with_prescription'])) : '';
            $with_patient_lab_orders = !empty($this->Common_model->escape_data($this->post_data['with_patient_lab_orders'])) ? trim($this->Common_model->escape_data($this->post_data['with_patient_lab_orders'])) : '';
            $with_procedure = !empty($this->Common_model->escape_data($this->post_data['with_procedure'])) ? trim($this->Common_model->escape_data($this->post_data['with_procedure'])) : '';
            $email = !empty($this->Common_model->escape_data($this->post_data['email'])) ? trim($this->Common_model->escape_data($this->post_data['email'])) : '';
            $with_treatment = 2;
            
            if (empty($patient_id) ||
                    empty($appointment_id) ||
                    empty($doctor_id)
            ) {
                $this->bad_request();
            }

            $requested_data = array(
                'patient_id' => $patient_id,
                'appointment_id' => $appointment_id,
                'doctor_id' => $doctor_id
            );
            $check_patient_appointment = $this->doctor->check_paient_appointment($requested_data);

            if (!empty($check_patient_appointment)) {

                if (!empty($email)) {
                    
                    $clinic_id = $check_patient_appointment['appointment_clinic_id'];
                    $date = $check_patient_appointment['appointment_date'];
                    
                    $get_patient_column = " user_id,
                                            user_first_name,
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
                        "files_data" => array()
                    );

                    $requested_data = array(
                        'date' => $date,
                        'clinic_id' => $clinic_id,
                        'patient_id' => $patient_id,
                        'appointment_id' => $appointment_id,
                        'doctor_id' => $doctor_id,
                        'user_type' => 2
                    );
                    
                    $this->load->model('User_model', 'user');
                    
                    if (!empty($with_vitalsign) && $with_vitalsign == 1) {
                        $requested_data['key'] = 1;
                        $get_vitalsign_data = $this->user->get_patient_report_detail($requested_data);
                        $view_data['vitalsign_data'] = $get_vitalsign_data;
                    }
                    
                    
                    if (!empty($with_clinicnote) && $with_clinicnote == 1) {
                        $requested_data['key'] = 2;
                        $get_clinicnote_data = $this->user->get_patient_report_detail($requested_data);
                        $view_data['clinicnote_data'] = $get_clinicnote_data;
                    }


                    if (!empty($with_prescription) && $with_prescription == 1) {
                        $patient_prescription = $this->doctor->get_prescription_for_patient($appointment_id);
                        $view_data['prescription_data'] = $patient_prescription;
                    }

                    if (!empty($with_patient_lab_orders) && $with_patient_lab_orders == 1) {
                        $requested_data['key'] = 4;
                        $get_lab_orders = $this->user->get_patient_report_detail($requested_data);
                        $view_data['patient_lab_orders_data'] = $get_lab_orders;
                    }

                    if (!empty($with_procedure) && $with_procedure == 1) {
                        $requested_data['key'] = 5;
                        $get_procedure_report = $this->user->get_patient_report_detail($requested_data);
                        $view_data['procedure_data'] = $get_procedure_report;
                    }

                    if (!empty($with_treatment) && $with_treatment == 1) {

                        $this->load->model('Billing_model', 'billing');
                        $billing_requested_data = array(
                            'appointment_id' => $appointment_id,
                            'doctor_id' => $doctor_id,
                            'patient_id' => $patient_id
                        );
                        $billing_data = $this->billing->get_billing_information_for_doctor($billing_requested_data);
                        $view_data['billing_data'] = $billing_data;
                    }
                    $view_data['billing_data'] = array();
                    
                    $patient_phone_number = $check_patient_appointment['user_phone_number'];
                    $doctor_name = $check_patient_appointment['user_first_name'] . ' ' . $check_patient_appointment['user_last_name'];
                    $patient_name = $get_patient_data['user_first_name'] . ' ' . $get_patient_data['user_last_name'];
                    $user_id = $get_patient_data['user_id'];
                    $clinic_name = $check_patient_appointment['clinic_name'];

                    $pdf = array(
                        'load_template' => 'charting',
                        'patient_phone_number' => $patient_phone_number,
                        'patient_name' => $patient_name,
                        'doctor_name' => $doctor_name,
                        'email' => $email,
                        'patient_id' => $user_id,
                        'clinic_name' => $clinic_name
                    );

                    $return = $this->generate_pdf_share($view_data, $pdf);

                    if ($return) {
                        $this->my_response['status'] = true;
                        $this->my_response['message'] = lang('record_share');
                    } else {
                        $this->my_response['status'] = false;
                        $this->my_response['message'] = lang('failure');
                    }
                } else {
                    $this->my_response['status'] = false;
                    $this->my_response['message'] = lang('failure');
                }
            } else {
                $this->my_response['status'] = false;
                $this->my_response['message'] = lang('common_detail_not_found');
            }
            $this->send_response();
        } catch (Exception $ex) {
            $this->error = $ex->getMessage();
            $this->store_error();
        }
    }

    /**
     * Description :- This function is used to share the invoice of the patient by the doctor
     * 
     * @author Manish Ramnani
     * 
     */
    public function share_invoice_post() {

        try {

            $patient_id = !empty($this->Common_model->escape_data($this->post_data['patient_id'])) ? trim($this->Common_model->escape_data($this->post_data['patient_id'])) : '';
            $appointment_id = !empty($this->Common_model->escape_data($this->post_data['appointment_id'])) ? trim($this->Common_model->escape_data($this->post_data['appointment_id'])) : '';
            $doctor_id = !empty($this->Common_model->escape_data($this->post_data['doctor_id'])) ? trim($this->Common_model->escape_data($this->post_data['doctor_id'])) : '';
            $email = !empty($this->Common_model->escape_data($this->post_data['email'])) ? trim($this->Common_model->escape_data($this->post_data['email'])) : '';

            if (empty($patient_id) ||
                    empty($appointment_id) ||
                    empty($doctor_id)
            ) {
                $this->bad_request();
            }

            $requested_data = array(
                'patient_id' => $patient_id,
                'appointment_id' => $appointment_id,
                'doctor_id' => $doctor_id
            );
            $check_patient_appointment = $this->doctor->check_paient_appointment($requested_data);

            if (!empty($check_patient_appointment)) {

                $doctor_name = $check_patient_appointment['doctor_first_name'] . ' ' . $check_patient_appointment['doctor_last_name'];
                $patient_name = $check_patient_appointment['patient_first_name'] . ' ' . $check_patient_appointment['patient_last_name'];
                $doctor_speciality = !empty($check_patient_appointment['doctor_detail_speciality']) ? $check_patient_appointment['doctor_detail_speciality'] : '-';
                $patient_phone_number = $check_patient_appointment['patient_phone_number'];
                $user_id = $check_patient_appointment['patient_user_id'];
                $patient_email = $check_patient_appointment['patient_user_email'];

                $kco = '';
                if (!empty($check_patient_appointment['kco'])) {
                    $kco = str_replace("\",\"", ",", $check_patient_appointment['kco']);
                    $kco = str_replace("[\"", "", $kco);
                    $kco = str_replace("\"]", "", $kco);
                    $kco = str_replace(",[]", ",", $kco);
                }

                $patient_detail = array(
                    'patient_name' => $patient_name,
                    'patient_id' => $user_id,
                    'patient_number' => $patient_phone_number,
                    'patient_email' => $patient_email,
                    'kco' => $kco
                );

                $this->load->model('Billing_model', 'billing');
                $billing_requested_data = array(
                    'appointment_id' => $appointment_id,
                    'doctor_id' => $doctor_id,
                    'patient_id' => $patient_id
                );
                $billing_data = $this->billing->get_billing_information_for_doctor($billing_requested_data);

                $view_data = array();
                $view_data['patient_detail'] = $patient_detail;
                $view_data['doctor_name'] = $doctor_name;
                $view_data['billing_data'] = $billing_data;

                $pdf = array(
                    'load_template' => 'invoice',
                    'doctor_speciality' => $doctor_speciality,
                    'patient_phone_number' => $patient_phone_number,
                    'patient_name' => $patient_name,
                    'doctor_name' => $doctor_name,
                    'email' => $email,
                    'patient_id' => $user_id
                );

                $return = $this->generate_pdf_share($view_data, $pdf);

                if ($return) {
                    $this->my_response['status'] = true;
                    $this->my_response['message'] = lang('record_share');
                } else {
                    $this->my_response['status'] = false;
                    $this->my_response['message'] = lang('failure');
                }
            } else {
                $this->my_response['status'] = false;
                $this->my_response['message'] = lang('common_detail_not_found');
            }
            $this->send_response();
        } catch (Exception $ex) {
            $this->error = $ex->getMessage();
            $this->store_error();
        }
    }

    /**
     * Description :- This function is used to genereate the pdf and send the mail to the user
     * 
     * @author Manish Ramnani
     * 
     * @param type $view_data
     * @param type $requested_data
     * @return int
     */
    public function generate_pdf_share($view_data, $requested_data) {
                
        $load_template = $requested_data['load_template'];
        $pdf_name = '';
        $this->load->model('Emailsetting_model');

        switch ($load_template) {

            case 'invoice':
                $view_html = $this->load->view("prints/invoice", $view_data, true);
                $pdf_name = 'Invoice_' . $requested_data['patient_name'];
                $email_template_data = $this->Emailsetting_model->get_emailtemplate_by_id(20);
                break;

            case 'charting':
                $view_html = $this->load->view("prints/charting", $view_data, true);
                $pdf_name = 'Charting_' . $requested_data['patient_name'];
                $email_template_data = $this->Emailsetting_model->get_emailtemplate_by_id(19);
                break;
        }

        require_once MPDF_PATH;
        $lang_code = 'en-GB';
        $mpdf = new MPDF(
                $lang_code, 'A4', 0, 'arial', 8, 8, 35, 8, 8, 5, 'P'
        );
        $mpdf->useOnlyCoreFonts = true;
        $mpdf->SetDisplayMode('real');
        $mpdf->list_indent_first_level = 0;
        $mpdf->setAutoBottomMargin = 'stretch';
        $mpdf->SetHTMLHeader('
            <table style="width:100%;border-bottom:1px solid #000">
                <tr>
                    <td width="50%" style="text-align:left;vertical-align:top">
                     ' . $view_data['doctor_data']['user_first_name'] . " " . $doctor_data['user_last_name'] . "<br>" . '
                     ' . $view_data['doctor_data']['doctor_detail_speciality'] . "<br>" . '
                     ' . $view_data['doctor_data']['doctor_qualification'] . "<br>" . '    
                    </td>
                    <td width="50%" style="text-align:right;vertical-align:top">
                        ' . $view_data['doctor_data']['clinic_name'] . "<br>" . '
                        ' . $view_data['doctor_data']['address_name'] . "<br>" . '
                        ' . $view_data['doctor_data']['clinic_contact_number'] . ", " . '
                        ' . $view_data['doctor_data']['clinic_email'] . "<br>" . '
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

        $upload_path = DOCROOT_PATH . 'uploads/' . PDF_FOLDER . '/';

        if (!file_exists($upload_path)) {
            mkdir($upload_path, 0777, TRUE);
            chmod($upload_path, 0777);
        }

        $file_name = $requested_data['patient_id'] . '.pdf';
        $mpdf->Output($upload_path . $file_name, 'F');

        $attachment_path = $upload_path . $file_name;

        if (!empty($attachment_path)) {

            $patient_name = $requested_data['patient_name'];
            $doctor_name = $requested_data['doctor_name'];
            $email = $requested_data['email'];
            $clinic_name = $requested_data['clinic_name'];

            $parse_arr = array(
                '{PatientName}' => $patient_name,
                '{DoctorName}' => $doctor_name,
                '{ClinicName}' => $clinic_name,
                '{WebUrl}' => DOMAIN_URL,
                '{AppName}' => APP_NAME,
                '{MailContactNumber}' => $email_template_data['email_static_data']['contact_number'],
                '{MailEmailAddress}' => $email_template_data['email_static_data']['email_id'],
                '{MailCompanyName}' => $email_template_data['email_static_data']['company_name'],
                '{CopyRightsYear}' => date('Y')
            );

            $message = replace_values_in_string($parse_arr, $email_template_data['email_template_message']);
            $subject = $email_template_data['email_template_subject'];

            $attachment_path_array = array($attachment_path, $pdf_name);
            $this->send_attachment_email(array($email => $email), $subject, $message, $attachment_path_array);
            unlink($attachment_path);
        } else {
            return 0;
        }
        return 1;
    }

    public function update_terms_condtion_flag_post() {
        try {
            $flag = !empty($this->post_data['is_term_accept']) ? trim($this->Common_model->escape_data($this->post_data['is_term_accept'])) : "";
            if (empty($flag)) {
                $this->bad_request();
            }
            $user_detail = $this->Common_model->get_single_row(TBL_DOCTOR_DETAILS, '*', array(
                'doctor_detail_doctor_id' => $this->user_id,
            ));
            if (empty($user_detail)) {
                $user_detail = array(
                    'doctor_detail_doctor_id' => $this->user_id,
                    'doctor_detail_is_term_accepted' => $flag,
                    'doctor_detail_term_accepted_date' => date('Y-m-d H:i:s')
                );
                $update = $this->Common_model->insert(TBL_DOCTOR_DETAILS, $user_detail);
            } else {
                $where = array(
                    'doctor_detail_doctor_id' => $this->user_id
                );
                $update = array(
                    'doctor_detail_is_term_accepted' => $flag,
                    'doctor_detail_term_accepted_date' => date('Y-m-d H:i:s')
                );
                $update = $this->Common_model->update(TBL_DOCTOR_DETAILS, $update, $where);
            }
            
            if ($update) {
                $this->my_response = array(
                    "status" => true,
                    "message" => "successfully",
                    "data" => '',
                );
            } else {
                $this->my_response = array(
                    "status" => false,
                    "message" => 'please try again',
                    "data" => '',
                );
            }
            $this->send_response();
        } catch (Exception $ex) {
            $this->error = $ex->getMessage();
            $this->store_error();
        }
    }

}
