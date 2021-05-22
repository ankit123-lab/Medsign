<?php
/**
 * This controller use for user related activity
 */
class Appointments extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model("Appointments_model", "appointments");
        $this->load->model("Doctor_model", "doctor");
    }

    /**
     * Description :- This function is used to book the appointment of the user 
      before check the doctor is available or not on that date or
      any appointment is their or not for same date and time
     */
    public function confirm_appointment_post() {
        try {
            $doctor_id = !empty($this->post_data['doctor_id']) ? trim($this->Common_model->escape_data($this->post_data['doctor_id'])) : "";
            $patient_id = !empty($this->post_data['patient_id']) ? trim($this->Common_model->escape_data($this->post_data['patient_id'])) : "";
            $clinic_id = !empty($this->post_data['clinic_id']) ? trim($this->Common_model->escape_data($this->post_data['clinic_id'])) : "";
            $date = !empty($this->post_data['date']) ? trim($this->Common_model->escape_data($this->post_data['date'])) : "";
            $start_time = !empty($this->post_data['start_time']) ? trim($this->Common_model->escape_data($this->post_data['start_time'])) : "";
            $end_time = !empty($this->post_data['end_time']) ? trim($this->Common_model->escape_data($this->post_data['end_time'])) : "";
            $doctor_avialibility_id = !empty($this->post_data['doctor_availability_id']) ? trim($this->Common_model->escape_data($this->post_data['doctor_availability_id'])) : "";
            $appointment_type = !empty($this->post_data['appointment_type']) ? trim($this->Common_model->escape_data($this->post_data['appointment_type'])) : 1;
            $user_type = !empty($this->post_data['user_type']) ? trim($this->Common_model->escape_data($this->post_data['user_type'])) : '';
            $otp = !empty($this->post_data['otp']) ? trim($this->Common_model->escape_data($this->post_data['otp'])) : '';
            $is_new_patient = !empty($this->post_data['is_new_patient']) ? trim($this->Common_model->escape_data($this->post_data['is_new_patient'])) : '';

            if (
                    empty($doctor_id) ||
                    empty($patient_id) ||
                    empty($start_time) ||
                    empty($end_time) ||
                    empty($clinic_id) ||
                    empty($date) ||
                    empty($user_type)
            ) {
                $this->bad_request();
                exit;
            }

            if ($user_type == 2) {
                $get_role_details = $this->Common_model->get_the_role($this->user_id);
                if (!empty($get_role_details['user_role_data'])) {
                    $permission_data = array(
                        'role_data' => $get_role_details['user_role_data'],
                        'module' => 2,
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

            if (validate_date($date)) {
                $this->my_response['status'] = false;
                $this->my_response['message'] = lang('invalid_date');
                $this->send_response();
            }

            if (validate_time($start_time) || validate_time($end_time)) {
                $this->my_response['status'] = false;
                $this->my_response['message'] = lang('invalid_time');
                $this->send_response();
            }

            $start_timestamp = strtotime($date . " " . $start_time . ":00");
            $end_timestamp = strtotime($date . " " . $end_time . ":00");

            $current_timestamp = strtotime(convert_utc_to_local(date("Y-m-d H:i:s")));

            if ($start_timestamp < $current_timestamp ||
                    $end_timestamp < $current_timestamp
            ) {

                $this->my_response['status'] = false;
                $this->my_response['message'] = lang("invalid_date_time");
                $this->send_response();
            }

            //check doctor status
            $get_user_detail = $this->Common_model->get_single_row(TBL_USERS, 'user_id', array(
                'user_status' => 1,
                'user_id' => $doctor_id
            ));
            if (empty($get_user_detail)) {
                $this->my_response['status'] = false;
                $this->my_response['message'] = lang("mycontroller_invalid_request");
                $this->send_response();
            }
            if($appointment_type == 4 || $appointment_type == 5) {
                $where = ['doctor_id' => $doctor_id, 'clinic_id' => $clinic_id];
                $doctor_payment_mode = $this->Common_model->doctor_payment_mode_link($where);
                $where_array = array(
                    'doctor_clinic_mapping_status' => 1,
                    'doctor_clinic_mapping_user_id' => $doctor_id,
                    'doctor_clinic_mapping_clinic_id' => $clinic_id
                );
                $doctor_clinic_mapping = $this->Common_model->get_single_row('me_doctor_clinic_mapping', 'doctor_clinic_mapping_tele_fees', $where_array);
                if(empty($doctor_payment_mode)) {
                    $this->my_response['status'] = false;
                    $this->my_response['message'] = lang("payment_mode_error");
                    $this->send_response();
                }
                if(empty($doctor_clinic_mapping['doctor_clinic_mapping_tele_fees']) || $doctor_clinic_mapping['doctor_clinic_mapping_tele_fees'] <= 0) {
                    $this->my_response['status'] = false;
                    $this->my_response['message'] = lang("tele_consultation_fees_error");
                    $this->send_response();
                }
            }
            if($appointment_type == 5) {
                $global_settings = $this->Common_model->get_single_row('me_global_settings','global_setting_value', ['global_setting_key'=> 'minimum_minutes_video_call_invitation']);
                $minimum_minutes = $global_settings['global_setting_value'];
                $where_array = array(
                    'setting_user_id' => $doctor_id,
                    'setting_type' => 10,
                    'setting_status' => 1
                );
                $get_setting_data = $this->Common_model->get_single_row(TBL_SETTING, 'setting_id,setting_data', $where_array);
                $minimum_minutes = $global_settings['global_setting_value'];
                if(empty($get_setting_data['setting_data']) || ($minimum_minutes > ($get_setting_data['setting_data']/60))) {
                    $this->my_response['status'] = false;
                    $this->my_response['message'] = lang("video_call_minute_error");
                    $this->send_response();
                }
            }
            //check otp
            if(!empty($is_new_patient) && $is_new_patient == 'Yes') {
                $is_otp_verified = false;
                $where = array(
                    'auth_user_id' => $patient_id,
                    'auth_type' => 4 // 4=Patient Appointment Book Verification
                );
                $get_auth_details = $this->Common_model->get_single_row(TBL_USER_AUTH, "*", $where);
                if (!empty($get_auth_details)) {
                    if (strtotime($get_auth_details['auth_otp_expiry_time']) >= $this->utc_time) {
                        if ($get_auth_details['auth_code'] == $otp) {
                            $is_otp_verified = true;
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
                if(!$is_otp_verified) {
                    $this->send_response();
                }
            }
            
            $doctor_block_calendar = array(
                'block_type' => 2,
                'start_time' => $start_time,
                'end_time' => $end_time,
                'block_slot_date' => $date,
                'doctor_id' => $doctor_id
            );

            $is_doctor_calendar_block = $this->doctor->calendar_block_date_exists($doctor_block_calendar);

            if (!empty($is_doctor_calendar_block)) {
                $this->my_response['status'] = false;
                $this->my_response['message'] = lang('doctor_not_available');
                $this->send_response();
            }

            //check doctor available or not
            $request_data = array(
                "doctor_id" => $doctor_id,
                "date" => $date,
                "start_time" => $start_time,
                "end_time" => $end_time,
            );
            $is_doctor_available = $this->doctor->check_doctor_available($request_data);

            if ($is_doctor_available) {
                $this->my_response['status'] = true;

                $insert_array = array(
                    "appointment_user_id" => $patient_id,
                    "appointment_doctor_user_id" => $doctor_id,
                    "appointment_clinic_id" => $clinic_id,
                    "appointment_type" => $appointment_type,
                    "appointment_doctor_availability_id" => $doctor_avialibility_id,
                    "appointment_from_time" => $start_time,
                    "appointment_to_time" => $end_time,
                    "appointment_date" => $date,
                    "appointment_booked_by" => $this->user_id,
                    "appointment_created_at" => $this->utc_time_formated,
                );
                $is_booked = $this->appointments->insert(TBL_APPOINTMENTS, $insert_array);
                if ($is_booked > 0) {

                    //set the reminder before 1 hour after booking the appointment
                    $get_doctor_details = $this->doctor->doctor_detail($doctor_id, $clinic_id, 1);
                    $doctor_name = '';
                    $doctor_address = '';

                    if (!empty($get_doctor_details[0]) && !empty($get_doctor_details[0]['user_first_name'])) {
                        $doctor_name = DOCTOR . $get_doctor_details[0]['user_first_name'] . " " . $get_doctor_details[0]['user_last_name'];
                    }

                    if (!empty($get_doctor_details[0])) {
                        $address_data = [
                            'address_name' => $get_doctor_details[0]['address_name'],
                            'address_name_one' => $get_doctor_details[0]['address_name_one'],
                            'address_locality' =>$get_doctor_details[0]['address_locality'],
                            'city_name' => $get_doctor_details[0]['city_name'],
                            'state_name' => $get_doctor_details[0]['state_name'],
                            'address_pincode' => $get_doctor_details[0]['address_pincode']
                        ];
                        $doctor_address = clinic_address($address_data);
                    }

                    $doctor_fees = !empty($get_doctor_details[0]['doctor_clinic_mapping_fees']) ? $get_doctor_details[0]['doctor_clinic_mapping_fees'] : '';
                    $clinic_name = !empty($get_doctor_details[0]['clinic_name']) ? $get_doctor_details[0]['clinic_name'] : '';

                    $notification_array = array();
                    $this->load->model('User_model', 'user');

                    // if appointment is booked by some one else instead of the patient then send the notification
                    // to who booked the appointment and to the patient
                    //get the user detail
                    $patient_user_detail = $this->user->get_details_by_id($patient_id);
                    if(empty($patient_user_detail['user_source_id']) && !empty($is_new_patient) && $is_new_patient == 'Yes') {
                        $this->user->update_profile($patient_id, array('user_source_id' => $doctor_id));
                    }
                    $patient_user_name = '';

                    if (!empty($patient_user_detail) && !empty($patient_user_detail['user_first_name'])) {
                        $patient_user_name = $patient_user_detail['user_first_name'] . ' ' . $patient_user_detail['user_last_name'];
                    }

                    $appointment_time = date(DATE_FORMAT, strtotime($date)) . ' ' . date('h:i A', $start_timestamp);
                    $send_message = sprintf(lang('appointment_book_patient'), $patient_user_name, $doctor_name, $appointment_time, DOMAIN_URL, PATIENT_EMAIL_FROM);
                    //send notification to the patient    
                    $notification_array[] = array(
                        'notification_list_user_id' => $patient_id,
                        'notification_list_user_type' => 1,
                        'notification_list_type' => 2,
                        'notification_list_message' => $send_message,
                        'notification_list_created_at' => $this->utc_time_formated
                    );
                    $columns = 'u.user_id';
                    $parent_members = $this->user->get_linked_family_members($patient_id, $columns);
                    foreach ($parent_members as $parent_member) {
                        $notification_array[] = array(
                            'notification_list_user_id' => $parent_member->user_id,
                            'notification_list_user_type' => 1,
                            'notification_list_type' => 2,
                            'notification_list_message' => $send_message,
                            'notification_list_created_at' => $this->utc_time_formated
                        );
                    }
                    $notification_list_ids = [];
                    $notification_last_id = $this->Common_model->insert_multiple(TBL_NOTIFICATION, $notification_array);
                    $notification_list_ids[] = $notification_last_id;
                    if(!empty($parent_members)) {
                        for ($i=1; $i <= count($parent_members); $i++) { 
                            $notification_list_ids[] = $notification_last_id+$i;
                        }
                    }
                    if(is_patient_reminder_access($patient_id)) {
                        //set reminder time before the 24 hour of the appointment
                        //if time is less than 24 hour then set the alarm before 2 hour
                        //if time is less than 2 hour then set no alarm
                        $reminder_time_24_hour = date('Y-m-d H:i:s', $start_timestamp - 86400);
                        $reminder_time_2_hour = date('Y-m-d H:i:s', $start_timestamp - 7200);
                        $reminder_time_stamp = convert_utc_to_local(date("Y-m-d H:i:s"));
                        $reminder_array = array(
                            'reminder_type' => 2,
                            'reminder_user_id' => $patient_id,
                            'reminder_created_by' => $this->user_id,
                            'reminder_start_date' => $date,
                            'reminder_doctor_id' => $doctor_id,
                            'reminder_doctor_address' => $doctor_address,
                            'reminder_doctor_name' => $doctor_name,
                            'reminder_doctor_fee' => $doctor_fees,
                            'reminder_note' => "Booked appointment",
                            'reminder_created_at' => $this->utc_time_formated,
                            'reminder_appointment_id' => $is_booked
                        );

                        if (strtotime($reminder_time_24_hour) > strtotime($reminder_time_stamp)) {
                            $reminder_array['reminder_timing'] = date('H:i', $start_timestamp - 86400);
                            $reminder_array['reminder_start_date'] = date('Y-m-d', $start_timestamp - 86400);
                            $this->Common_model->insert(TBL_REMINDERS, $reminder_array);
                        } else {

                            if (strtotime($reminder_time_2_hour) > strtotime($reminder_time_stamp)) {
                                $reminder_array['reminder_timing'] = date('H:i', $start_timestamp - 7200);
                                $reminder_array['reminder_start_date'] = date('Y-m-d', $start_timestamp - 7200);
                                $this->Common_model->insert(TBL_REMINDERS, $reminder_array);
                            }
                        }
                    }
                    $send_notification_data = array(
                        'notification_list_type' => 2,
                        'notification_list_ids' => $notification_list_ids,
                        'doctor_id' => $doctor_id,
                        'clinic_id' => $clinic_id,
                        'action' => 'book'
                    );
                    $cron_job_path = CRON_PATH . " notification/send_notification/" . base64_encode(json_encode($send_notification_data));
                    $cron_job_path2 = $cron_job_path;
                    exec(PHP_PATH . " " . $cron_job_path . " > /dev/null &");

                    //send sms and email to the doctor based on the status
                    $send_doctor_data = array(
                        'doctor_id' => $doctor_id,
                        'patient_name' => $patient_user_name,
                        'doctor_name' => $doctor_name,
                        'doctor_phone_number' => $get_doctor_details[0]['user_phone_number'],
                        'doctor_email' => ($get_doctor_details[0]['user_email_verified'] == 1 ? $get_doctor_details[0]['user_email'] : ""),
                        'patient_email' => ($patient_user_detail['user_email_verified'] == 1 ? $patient_user_detail['user_email'] : ""),
                        'patient_phone_number' => $patient_user_detail['user_phone_number'],
                        'patient_user_id' => $patient_user_detail['user_id'],
                        'patient_user_phone_verified' => $patient_user_detail['user_phone_verified'],
                        'appointment_date' => $date,
                        'address' => $doctor_address,
                        'appointment_time' => date('h:i A', $start_timestamp),
                        'start_timestamp' => $start_timestamp,
                        'end_timestamp' => $end_timestamp,
                        'appointment_id' => $is_booked,
                        'clinic_name' => $clinic_name,
                        'user_type' => $user_type,
                        'appointment_type' => $appointment_type,
                        'clinic_id' => $clinic_id,
                        'email_action' => 'book'
                    );

                    $cron_job_path = CRON_PATH . " notification/notify_doctor/" . base64_encode(json_encode($send_doctor_data));
                    exec(PHP_PATH . " " . $cron_job_path . " > /dev/null &");
                    
                    //insert into the track report
                    $insert_track_array = array(
                        'patient_report_track_appointment_id' => $is_booked,
                        'patient_report_track_created_at' => $this->utc_time_formated
                    );
                    $this->Common_model->insert(TBL_PATIENT_REPORT_TRACK, $insert_track_array);
                    $this->my_response['status'] = true;
                    $this->my_response['cron_job_path'] = $cron_job_path;
                    $this->my_response['message'] = lang('appointment_booked');
                } else {
                    $this->my_response['status'] = false;
                    $this->my_response['message'] = lang('failure');
                }
            } else {
                $this->my_response['status'] = false;
                $this->my_response['message'] = lang('doctor_not_available');
            }
            echo $this->send_response();
        } catch (Exception $ex) {
            $this->error = $ex->getMessage();
            $this->store_error();
        }
    }

    public function appointments_book_send_otp_post() {
        try {
            $patient_id = !empty($this->post_data['patient_id']) ? trim($this->Common_model->escape_data($this->post_data['patient_id'])) : "";
            $doctor_id = !empty($this->post_data['doctor_id']) ? trim($this->Common_model->escape_data($this->post_data['doctor_id'])) : "";
            $clinic_id = !empty($this->post_data['clinic_id']) ? trim($this->Common_model->escape_data($this->post_data['clinic_id'])) : "";
            $patient_phone_number = !empty($this->post_data['patient_phone_number']) ? trim($this->Common_model->escape_data($this->post_data['patient_phone_number'])) : "";
            $is_resend_otp = !empty($this->post_data['is_resend_otp']) ? true : false;

            if (empty($patient_id) || empty($patient_phone_number) || empty($doctor_id) || empty($clinic_id)) {
                $this->bad_request();
                exit;
            }
            $credit = check_sms_whatsapp_credit($doctor_id, 'sms');
            if($credit == 0 && !empty($GLOBALS['ENV_VARS']['CHECK_SMS_CREDIT']['GLOBAL_PATIENT_APPOINTMENT_BOOK_OTP'])) {
                $this->my_response['status'] = false;
                $this->my_response['message'] = lang('no_sms_credit');
                $this->send_response();
            }
            $this->load->model(array('User_model','Clinic_model'));
            $auth_resend_count = 0;
            if($is_resend_otp) {
                $auth_update_where = array(
                    'auth_user_id' => $patient_id,
                    "auth_type" => 4 // 4=Patient Appointment Book Verification
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
            $getclinicDetail = $this->Clinic_model->get_clinic_detail($clinic_id, 'clinic_name');
            $doctorDetail = $this->Common_model->get_single_row(TBL_USERS, "user_first_name,user_last_name", array(
                "user_id" => $doctor_id)
            );
            $doctor_name = DOCTOR .' ' .$doctorDetail['user_first_name'] . ' ' . $doctorDetail['user_last_name'];
            $otp = getUniqueToken(6, 'numeric');
            $message = sprintf(lang('patient_book_appointment'), $otp,$doctor_name,short_clinic_name($getclinicDetail['clinic_name']));
            $whatsapp_sms_body = sprintf(lang('wa_template_34_patient_book_appointment'), $otp,$doctor_name,$getclinicDetail['clinic_name']);
            $send_otp = array(
                'phone_number' => $patient_phone_number,
                'message' => $message,
                'whatsapp_sms_body' => $whatsapp_sms_body
            );
            $send_otp['doctor_id'] = $doctor_id;
            $send_otp['patient_id'] = $patient_id;
            $send_otp['is_sms_count'] = true;
            $send_otp['is_check_sms_credit'] = $GLOBALS['ENV_VARS']['CHECK_SMS_CREDIT']['GLOBAL_PATIENT_APPOINTMENT_BOOK_OTP'];
            $sening_sms = send_communication($send_otp);
            $auth_update_data = array(
                'auth_code' => $otp,
                'auth_otp_expiry_time' => date('Y-m-d H:i:s', strtotime(OTP_EXPIRE_TOKEN_TIME, strtotime($this->utc_time_formated))),
                'auth_phone_number' => $patient_phone_number,
                'auth_resend_count' => $auth_resend_count,
                'auth_type' => 4  // 4=Patient Appointment Book Verification
            );
            $auth_update_where = array(
                'auth_user_id' => $patient_id,
                'auth_type' => 4 // 4=Patient Appointment Book Verification
            );
            $this->User_model->update_auth_details($auth_update_data, $auth_update_where);
            $this->my_response['status'] = true;
            $this->my_response['message'] = lang('otp_sent_to_mobile');
            echo $this->send_response();
        } catch (Exception $ex) {
            $this->error = $ex->getMessage();
            $this->store_error();
        }
    }

    /**
     * Description :- This function is used to get the appointments of the user
     *  
     */
    public function get_my_appointments_post() {
        try {
            $patient_id = !empty($this->post_data['patient_id']) ? trim($this->Common_model->escape_data($this->post_data['patient_id'])) : "";
            $page = !empty($this->post_data['page']) ? trim($this->Common_model->escape_data($this->post_data['page'])) : "1";
            $per_page = !empty($this->post_data['per_page']) ? trim($this->Common_model->escape_data($this->post_data['per_page'])) : "10";
            $appointment_type = !empty($this->post_data['appointment_type']) ? trim($this->Common_model->escape_data($this->post_data['appointment_type'])) : "";
            $is_past_appointment = !empty($this->post_data['is_past_appointment']) ? trim($this->Common_model->escape_data($this->post_data['is_past_appointment'])) : 2;

            if (empty($patient_id)) {
                $this->bad_request();
                exit;
            }
            //get appointment data from db
            $request_data = array(
                "patient_id" => $patient_id,
                "appointment_type" => $appointment_type,
                "is_past_appointment" => $is_past_appointment
            );
            $total_count = $this->appointments->get_appointments($request_data);
            $request_data = array(
                "patient_id" => $patient_id,
                "page" => $page,
                "per_page" => $per_page,
                "appointment_type" => $appointment_type,
                "is_past_appointment" => $is_past_appointment
            );

            $appointment_data = $this->appointments->get_appointments($request_data);
            $final_appointment_data = array();
            foreach ($appointment_data as $appointment) {
                $final_appointment_data[] = array(
                    "doctor_user_id" => $appointment['user_id'],
                    "doctor_first_name" => $appointment['user_first_name'],
                    "doctor_last_name" => $appointment['user_last_name'],
                    "doctor_photo" => get_file_full_path($appointment['user_photo_filepath']),
                    "doctor_experience" => $appointment['doctor_detail_year_of_experience'],
                    "doctor_specialisation" => $appointment['specialization'],
                    "doctor_qualification" => $appointment['doctor_qualification'],
                    "appointment_id" => $appointment['appointment_id'],
                    "appointment_from_time" => $appointment['appointment_from_time'],
                    "appointment_date" => $appointment['appointment_date'],
                    "appointment_type" => $appointment['appointment_type'],
                    "doctor_fees" => $appointment['doctor_clinic_mapping_fees'],
                    "clinic_id" => $appointment['appointment_clinic_id'],
                    "clinic_address" => $appointment['address_name'],
                    "clinic_latitude" => $appointment['address_latitude'],
                    "clinic_longitude" => $appointment['address_longitude'],
                    "doctor_phone_number" => $appointment['user_phone_number'],
                    "doctor_speciality" => implode(',', array_unique(explode(',', $appointment['speciality']))),
                );
            }
            $this->my_response['status'] = true;
            $this->my_response['message'] = lang('my_appointment');
            $this->my_response['data'] = $final_appointment_data;
            $this->my_response['total_count'] = $total_count;
            $this->my_response['per_page'] = $per_page;
            $this->my_response['current_page'] = $page;
            echo $this->send_response();
        } catch (Exception $ex) {
            $this->error = $ex->getMessage();
            $this->store_error();
        }
    }

    /**
     * Description :- This appoitment is used to get the detail of the appointment
     */
    public function appointment_detail_post() {
        try {
            $appointment_id = !empty($this->post_data['appointment_id']) ? trim($this->Common_model->escape_data($this->post_data['appointment_id'])) : "";
            if (empty($appointment_id)) {
                $this->bad_request();
                exit;
            }

            $appointment_data = $this->doctor->get_appointment_detail($appointment_id);
            $this->my_response['status'] = true;
            $this->my_response['data'] = $appointment_data;
            echo $this->send_response();
        } catch (Exception $ex) {
            $this->error = $ex->getMessage();
            $this->store_error();
        }
    }

    /**
     * Description :- This function is used to cancel the appointment
     */
    public function cancel_appointment_post() {
        try {
            $appointment_id = !empty($this->post_data['appointment_id']) ? trim($this->Common_model->escape_data($this->post_data['appointment_id'])) : "";
            $patient_id = !empty($this->post_data['patient_id']) ? trim($this->Common_model->escape_data($this->post_data['patient_id'])) : "";
            $user_type = !empty($this->post_data['user_type']) ? trim($this->Common_model->escape_data($this->post_data['user_type'])) : '';

            if (empty($appointment_id)) {
                $this->bad_request();
                exit;
            }

            //get the appointment details
            $appointment_data = array(
                'appointment_id' => $appointment_id,
                'appointment_user_id' => $patient_id
            );
            $get_appointment_detail = $this->appointments->get_appointment_detail($appointment_data);
			if (!empty($get_appointment_detail)) {
                $appointment_time = (strtotime($get_appointment_detail['appointment_date'] . ' ' . $get_appointment_detail['appointment_from_time']) - 60);
                $current_timestamp = strtotime(convert_utc_to_local(date("Y-m-d H:i:s")));
                if ($appointment_time < $current_timestamp) {
                    $this->my_response['status'] = false;
                    $this->my_response['message'] = lang('not_able_to_cancel');
                    $this->send_response();
                }

                if ($user_type == 2) {
                    $get_role_details = $this->Common_model->get_the_role($this->user_id);
                    if (!empty($get_role_details['user_role_data'])) {
                        $permission_data = array(
                            'role_data' => $get_role_details['user_role_data'],
                            'module' => 2,
                            'key' => 4
                        );
                        $check_module_permission = $this->check_module_permission($permission_data);
                        if ($check_module_permission == 2) {
                            $this->my_response['status'] = false;
                            $this->my_response['message'] = lang('permission_error');
                            $this->send_response();
                        }
                    }
                }

                $date = $get_appointment_detail['appointment_date'];
                $start_time = $get_appointment_detail['appointment_from_time'];
                $doctor_id = $get_appointment_detail['appointment_doctor_user_id'];
                $clinic_id = $get_appointment_detail['appointment_clinic_id'];
                $start_timestamp = strtotime($date . " " . $start_time);

                $this->load->model('User_model', 'user');

                //if appointment taken then not be reschedule again
                $patient_detail_params = array(
                    'appointment_id' => $appointment_id,
                    'clinic_id' => $clinic_id,
                    'doctor_id' => $doctor_id,
                    'date' => $date,
                    'patient_id' => $patient_id,
                    'user_type' => $user_type
                );

                $is_appointment_taken = $this->check_appointment_already_taken($patient_detail_params);
                if ($is_appointment_taken == 1) {
                    $this->my_response['status'] = false;
                    $this->my_response['message'] = lang('appointment_alreay_taken');
                    $this->send_response();
                }
				
                $update_array = array(
                    "appointment_updated_at" => $this->utc_time_formated,
                    "appointment_status" => 9
                );
				
                $where = array(
                    "appointment_id" => $appointment_id
                );
                $is_deleted = $this->doctor->update(TBL_APPOINTMENTS, $update_array, $where);
                if ($is_deleted > 0) {
                    //delete the reminder if exists
                    $update_where = array(
                        'reminder_appointment_id' => $appointment_id
                    );
                    $update_data = array(
                        'reminder_modified_at' => $this->utc_time_formated,
                        'reminder_status' => 9
                    );
                    $this->Common_model->update(TBL_REMINDERS, $update_data, $update_where);

                    $doctor_name = '';
                    $doctor_address = '';

                    if (!empty($doctor_id)) {
                        $get_doctor_details = $this->doctor->doctor_detail($doctor_id, $clinic_id, 1);

                        if (!empty($get_doctor_details[0]) && !empty($get_doctor_details[0]['user_first_name'])) {
                            $doctor_name = DOCTOR . $get_doctor_details[0]['user_first_name'] . " " . $get_doctor_details[0]['user_last_name'];
                        }

                        if (!empty($get_doctor_details[0])) {
                            $address_data = [
                                'address_name' => $get_doctor_details[0]['address_name'],
                                'address_name_one' => $get_doctor_details[0]['address_name_one'],
                                'address_locality' =>$get_doctor_details[0]['address_locality'],
                                'city_name' => $get_doctor_details[0]['city_name'],
                                'state_name' => $get_doctor_details[0]['state_name'],
                                'address_pincode' => $get_doctor_details[0]['address_pincode']
                            ];
                            $doctor_address = clinic_address($address_data);
                        }

                        $doctor_fees = !empty($get_doctor_details[0]['doctor_clinic_mapping_fees']) ? $get_doctor_details[0]['doctor_clinic_mapping_fees'] : '';
                        $clinic_name = !empty($get_doctor_details[0]['clinic_name']) ? $get_doctor_details[0]['clinic_name'] : '';
                    }

                    $patient_user_detail = $this->user->get_details_by_id($patient_id);
                    $patient_user_name = '';
                    if (!empty($patient_user_detail) && !empty($patient_user_detail['user_first_name'])) {
                        $patient_user_name = $patient_user_detail['user_first_name'] . ' ' . $patient_user_detail['user_last_name'];
                    }

                    $notification_array = array();

                    $appointment_time = date(DATE_FORMAT, strtotime($date)) . ' ' . date('h:i A', $start_timestamp);

                    $send_message = sprintf(lang('appointment_cancel_patient'), $patient_user_name, $doctor_name, $clinic_name, $appointment_time, DOMAIN_URL, PATIENT_EMAIL_FROM);

                    //send notification to user
                    $notification_array[] = array(
                        'notification_list_user_id' => $patient_id,
                        'notification_list_user_type' => 1,
                        'notification_list_type' => 2,
                        'notification_list_message' => $send_message,
                        'notification_list_created_at' => $this->utc_time_formated
                    );
                    $columns = 'u.user_id';
                    $parent_members = $this->user->get_linked_family_members($patient_id, $columns);
                    foreach ($parent_members as $parent_member) {
                        $notification_array[] = array(
                            'notification_list_user_id' => $parent_member->user_id,
                            'notification_list_user_type' => 1,
                            'notification_list_type' => 2,
                            'notification_list_message' => $send_message,
                            'notification_list_created_at' => $this->utc_time_formated
                        );
                    }
                    $notification_list_ids = [];
                    $notification_last_id = $this->Common_model->insert_multiple(TBL_NOTIFICATION, $notification_array);
                    $notification_list_ids[] = $notification_last_id;
                    if(!empty($parent_members)) {
                        for ($i=1; $i <= count($parent_members); $i++) { 
                            $notification_list_ids[] = $notification_last_id+$i;
                        }
                    }
                    $send_notification_data = array(
                        'notification_list_type' => 2,
                        'notification_list_ids' => $notification_list_ids,
                        'doctor_id' => $doctor_id,
                        'clinic_id' => $clinic_id,
                        'action' => 'cancel'
                    );
                    $cron_job_path = CRON_PATH . " notification/send_notification/" . base64_encode(json_encode($send_notification_data));
                    exec(PHP_PATH . " " . $cron_job_path . " > /dev/null &");

                    //send sms and email to the doctor based on the status
                    $send_doctor_data = array(
                        'doctor_id' => $doctor_id,
                        'patient_name' => $patient_user_name,
                        'doctor_name' => $doctor_name,
                        'doctor_phone_number' => $get_doctor_details[0]['user_phone_number'],
                        'doctor_email' => ($get_doctor_details[0]['user_email_verified'] == 1 ? $get_doctor_details[0]['user_email'] : ""),
                        'patient_email' => ($patient_user_detail['user_email_verified'] == 1 ? $patient_user_detail['user_email'] : ""),
                        'patient_phone_number' => $patient_user_detail['user_phone_number'],
                        'patient_user_id' => $patient_user_detail['user_id'],
                        'appointment_date' => $date,
                        'appointment_id' => $appointment_id,
                        'address' => $doctor_address,
                        'appointment_time' => date('h:i A', $start_timestamp),
                        'clinic_name' => $clinic_name,
                        'user_type' => $user_type,
                        'clinic_id' => $clinic_id,
                        'email_action' => 'cancel'
                    );
                    $cron_job_path = CRON_PATH . " notification/notify_doctor/" . base64_encode(json_encode($send_doctor_data));
                    exec(PHP_PATH . " " . $cron_job_path . " > /dev/null &");

                    $this->my_response['status'] = true;
                    $this->my_response['message'] = lang('appointment_cancel_message');
                } else {
                    $this->my_response['status'] = false;
                    $this->my_response['message'] = lang('failure');
                }
            } else {
                $this->my_response['status'] = false;
                $this->my_response['message'] = lang('failure');
            }

            echo $this->send_response();
        } catch (Exception $ex) {
            $this->error = $ex->getMessage();
            $this->store_error();
        }
    }

    /**
     * Description :- This function is used to cancel the appointment
     */
    public function reschedule_appointment_post() {
        try {

            $appointment_id = !empty($this->post_data['appointment_id']) ? trim($this->Common_model->escape_data($this->post_data['appointment_id'])) : "";
            $patient_id = !empty($this->post_data['patient_id']) ? trim($this->Common_model->escape_data($this->post_data['patient_id'])) : "";
            $doctor_id = !empty($this->post_data['doctor_id']) ? trim($this->Common_model->escape_data($this->post_data['doctor_id'])) : "";
            $clinic_id = !empty($this->post_data['clinic_id']) ? trim($this->Common_model->escape_data($this->post_data['clinic_id'])) : "";
            $date = !empty($this->post_data['date']) ? trim($this->Common_model->escape_data($this->post_data['date'])) : "";
            $start_time = !empty($this->post_data['start_time']) ? trim($this->Common_model->escape_data($this->post_data['start_time'])) : "";
            $end_time = !empty($this->post_data['end_time']) ? trim($this->Common_model->escape_data($this->post_data['end_time'])) : "";
            $appointment_type = !empty($this->post_data['appointment_type']) ? trim($this->Common_model->escape_data($this->post_data['appointment_type'])) : 1;
            $doctor_avialibility_id = !empty($this->post_data['doctor_availability_id']) ? trim($this->Common_model->escape_data($this->post_data['doctor_availability_id'])) : "";
            $user_type = !empty($this->post_data['user_type']) ? trim($this->Common_model->escape_data($this->post_data['user_type'])) : 1;

            if (
                    empty($appointment_id) ||
                    empty($doctor_id) ||
                    empty($start_time) ||
                    empty($end_time) ||
                    empty($date) ||
                    empty($user_type) ||
                    empty($clinic_id)
            ) {
                $this->bad_request();
                exit;
            }

            $this->load->model('User_model', 'user');

            //get the appointment details
            $appointment_data = array(
                'appointment_id' => $appointment_id,
                'appointment_user_id' => $patient_id,
                'appointment_status' => 1
            );
            $get_appointmet_detail = $this->appointments->get_appointment_detail($appointment_data);

            if (empty($get_appointmet_detail)) {
                $this->my_response['status'] = false;
                $this->my_response['message'] = lang("mycontroller_invalid_request");
                $this->send_response();
            }

            if (validate_date($date)) {
                $this->my_response['status'] = false;
                $this->my_response['message'] = lang('invalid_date');
                $this->send_response();
            }

            if (validate_time($start_time) || validate_time($end_time)) {
                $this->my_response['status'] = false;
                $this->my_response['message'] = lang('invalid_time');
                $this->send_response();
            }

            if($appointment_type == 4 || $appointment_type == 5) {
                $where = ['doctor_id' => $doctor_id, 'clinic_id' => $clinic_id];
                $doctor_payment_mode = $this->Common_model->doctor_payment_mode_link($where);
                $where_array = array(
                    'doctor_clinic_mapping_status' => 1,
                    'doctor_clinic_mapping_user_id' => $doctor_id,
                    'doctor_clinic_mapping_clinic_id' => $clinic_id
                );
                $doctor_clinic_mapping = $this->Common_model->get_single_row('me_doctor_clinic_mapping', 'doctor_clinic_mapping_tele_fees', $where_array);
                if(empty($doctor_payment_mode)) {
                    $this->my_response['status'] = false;
                    $this->my_response['message'] = lang("payment_mode_error");
                    $this->send_response();
                }
                if(empty($doctor_clinic_mapping['doctor_clinic_mapping_tele_fees']) || $doctor_clinic_mapping['doctor_clinic_mapping_tele_fees'] <= 0) {
                    $this->my_response['status'] = false;
                    $this->my_response['message'] = lang("tele_consultation_fees_error");
                    $this->send_response();
                }
            }

            if($appointment_type == 5) {
                $global_settings = $this->Common_model->get_single_row('me_global_settings','global_setting_value', ['global_setting_key'=> 'minimum_minutes_video_call_invitation']);
                $minimum_minutes = $global_settings['global_setting_value'];
                $where_array = array(
                    'setting_user_id' => $doctor_id,
                    'setting_type' => 10,
                    'setting_status' => 1
                );
                $get_setting_data = $this->Common_model->get_single_row(TBL_SETTING, 'setting_id,setting_data', $where_array);
                $minimum_minutes = $global_settings['global_setting_value'];
                if(empty($get_setting_data['setting_data']) || ($minimum_minutes > ($get_setting_data['setting_data']/60))) {
                    $this->my_response['status'] = false;
                    $this->my_response['message'] = lang("video_call_minute_error");
                    $this->send_response();
                }
            }

            $appointment_time = (strtotime($get_appointmet_detail['appointment_date'] . ' ' . $get_appointmet_detail['appointment_from_time']) - 60);
            $current_timestamp = strtotime(convert_utc_to_local(date("Y-m-d H:i:s")));

            
            if ($appointment_time < $current_timestamp) {
                $this->my_response['status'] = false;
                $this->my_response['message'] = lang('not_able_to_reschedule');
                $this->send_response();
            }

            if ($user_type == 2) {
                $get_role_details = $this->Common_model->get_the_role($this->user_id);
                if (!empty($get_role_details['user_role_data'])) {
                    $permission_data = array(
                        'role_data' => $get_role_details['user_role_data'],
                        'module' => 2,
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

            //if appointment taken then not be reschedule again
            $patient_detail_params = array(
                'appointment_id' => $appointment_id,
                'clinic_id' => $clinic_id,
                'doctor_id' => $doctor_id,
                'date' => $date,
                'patient_id' => $patient_id,
                'user_type' => $user_type
            );

            $is_appointment_taken = $this->check_appointment_already_taken($patient_detail_params);

            if ($is_appointment_taken == 1) {
                $this->my_response['status'] = false;
                $this->my_response['message'] = lang('appointment_alreay_taken');
                $this->send_response();
            }

            $start_timestamp = strtotime($date . " " . $start_time . ":00");
            $end_timestamp = strtotime($date . " " . $end_time . ":00");

            if ($start_timestamp < $current_timestamp ||
                    $end_timestamp < $current_timestamp
            ) {
                $this->my_response['status'] = false;
                $this->my_response['message'] = lang("invalid_date_time");
                $this->send_response();
            }

            $doctor_block_calendar = array(
                'block_type' => 2,
                'start_time' => $start_time,
                'end_time' => $end_time,
                'block_slot_date' => $date,
                'doctor_id' => $doctor_id
            );

            $is_doctor_calendar_block = $this->doctor->calendar_block_date_exists($doctor_block_calendar);

            if (!empty($is_doctor_calendar_block)) {
                $this->my_response['status'] = false;
                $this->my_response['message'] = lang('doctor_not_available');
                $this->send_response();
            }

            //check doctor available or not
            $request_data = array(
                "doctor_id" => $doctor_id,
                "date" => $date,
                "start_time" => $start_time,
                "end_time" => $end_time,
            );
            $is_doctor_available = $this->doctor->check_doctor_available($request_data, $appointment_id);

            if ($is_doctor_available) {
                $this->my_response['status'] = true;

                $update_array = array(
                    "appointment_clinic_id" => $clinic_id,
                    "appointment_from_time" => $start_time,
                    "appointment_to_time" => $end_time,
                    "appointment_date" => $date,
                    "appointment_type" => $appointment_type,
                    "appointment_booked_by" => $this->user_id,
                    "appointment_updated_at" => $this->utc_time_formated,
                    "appointment_doctor_availability_id" => $doctor_avialibility_id,
                );
                $where = array(
                    "appointment_id" => $appointment_id
                );

                $is_booked = $this->doctor->update(TBL_APPOINTMENTS, $update_array, $where);
                $notification_array = array();
                if ($is_booked > 0) {

                    $patient_user_detail = $this->user->get_details_by_id($patient_id);
                    $patient_user_name = '';
                    if (!empty($patient_user_detail) && !empty($patient_user_detail['user_first_name'])) {
                        $patient_user_name = $patient_user_detail['user_first_name'] . ' ' . $patient_user_detail['user_last_name'];
                    }

                    //set the reminder before 1 hour after booking the appointment
                    $get_doctor_details = $this->doctor->doctor_detail($doctor_id, $clinic_id, 1);
                    $doctor_user_name = '';
                    $doctor_address = '';

                    if (!empty($get_doctor_details[0]) && !empty($get_doctor_details[0]['user_first_name'])) {
                        $doctor_user_name = DOCTOR . $get_doctor_details[0]['user_first_name'] . " " . $get_doctor_details[0]['user_last_name'];
                    }

                    if (!empty($get_doctor_details[0])) {
                        $address_data = [
                            'address_name' => $get_doctor_details[0]['address_name'],
                            'address_name_one' => $get_doctor_details[0]['address_name_one'],
                            'address_locality' =>$get_doctor_details[0]['address_locality'],
                            'city_name' => $get_doctor_details[0]['city_name'],
                            'state_name' => $get_doctor_details[0]['state_name'],
                            'address_pincode' => $get_doctor_details[0]['address_pincode']
                        ];
                        $doctor_address = clinic_address($address_data);
                    }

                    $doctor_fees = !empty($get_doctor_details[0]['doctor_clinic_mapping_fees']) ? $get_doctor_details[0]['doctor_clinic_mapping_fees'] : '';
                    $clinic_name = !empty($get_doctor_details[0]['clinic_name']) ? $get_doctor_details[0]['clinic_name'] : '';
                    $appointment_time = date(DATE_FORMAT, strtotime($date)) . '' . date('h:i A', $start_timestamp);

                    $send_message = sprintf(lang('appointment_reschedule_patient'), $patient_user_name, $doctor_user_name, $clinic_name, $appointment_time, DOMAIN_URL, PATIENT_EMAIL_FROM);

                    //send notification to the patient    
                    $notification_array[] = array(
                        'notification_list_user_id' => $patient_id,
                        'notification_list_user_type' => 1,
                        'notification_list_type' => 2,
                        'notification_list_message' => $send_message,
                        'notification_list_created_at' => $this->utc_time_formated
                    );
                    $columns = 'u.user_id';
                    $parent_members = $this->user->get_linked_family_members($patient_id, $columns);
                    foreach ($parent_members as $parent_member) {
                        $notification_array[] = array(
                            'notification_list_user_id' => $parent_member->user_id,
                            'notification_list_user_type' => 1,
                            'notification_list_type' => 2,
                            'notification_list_message' => $send_message,
                            'notification_list_created_at' => $this->utc_time_formated
                        );
                    }

                    //set reminder time before the 24 hour of the appointment
                    //if time is less than 24 hour then set the alarm before 2 hour
                    //if time is less than 2 hour then set no alarm
                    $reminder_time_24_hour = date('Y-m-d H:i:s', $start_timestamp - 86400);
                    $reminder_time_2_hour = date('Y-m-d H:i:s', $start_timestamp - 7200);
                    $reminder_time_stamp = convert_utc_to_local(date("Y-m-d H:i:s"));
                    $reminder_time = date('H:i', $start_timestamp);
                    $reminder_start_date = date('Y-m-d');

                    $delete_reminder = 1;
                    if (strtotime($reminder_time_24_hour) > strtotime($reminder_time_stamp)) {
                        $reminder_time = date('H:i', $start_timestamp - 86400);
                        $reminder_start_date = date('Y-m-d', $start_timestamp - 86400);
                        $delete_reminder = 2;
                    } else {
                        if (strtotime($reminder_time_2_hour) > strtotime($reminder_time_stamp)) {
                            $reminder_time = date('H:i', $start_timestamp - 7200);
                            $reminder_start_date = date('Y-m-d', $start_timestamp - 7200);
                            $delete_reminder = 2;
                        }
                    }

                    //check reminder exists or not
                    $reminder_where = array(
                        'reminder_appointment_id' => $appointment_id,
                        'reminder_status' => 1
                    );
                    $get_reminder = $this->Common_model->get_single_row(TBL_REMINDERS, 'reminder_id', $reminder_where);
                    if (empty($get_reminder)) {
                        $reminder_array = array(
                            'reminder_type' => 2,
                            'reminder_user_id' => $patient_id,
                            'reminder_created_by' => $this->user_id,
                            'reminder_start_date' => $reminder_start_date,
                            'reminder_timing' => $reminder_time,
                            'reminder_doctor_id' => $doctor_id,
                            'reminder_doctor_address' => $doctor_address,
                            'reminder_doctor_name' => $doctor_user_name,
                            'reminder_doctor_fee' => $doctor_fees,
                            'reminder_note' => "Booked appointment",
                            'reminder_created_at' => $this->utc_time_formated,
                            'reminder_appointment_id' => $appointment_id
                        );
                        if(is_patient_reminder_access($patient_id))
                            $this->Common_model->insert(TBL_REMINDERS, $reminder_array);
                    } else {
                        $update_where = array(
                            'reminder_appointment_id' => $appointment_id
                        );

                        if ($delete_reminder == 1) {
                            $update_data = array(
                                'reminder_modified_at' => $this->utc_time_formated,
                                'reminder_status' => 9
                            );
                        } else {
                            $update_data = array(
                                'reminder_modified_at' => $this->utc_time_formated,
                                'reminder_timing' => $reminder_time,
                                'reminder_start_date' => $reminder_start_date,
                                'reminder_status' => 1
                            );
                        }

                        $this->Common_model->update(TBL_REMINDERS, $update_data, $update_where);
                    }
                    $notification_list_ids = [];
                    $notification_last_id = $this->Common_model->insert_multiple(TBL_NOTIFICATION, $notification_array);
                    $notification_list_ids[] = $notification_last_id;
                    if(!empty($parent_members)) {
                        for ($i=1; $i <= count($parent_members); $i++) { 
                            $notification_list_ids[] = $notification_last_id+$i;
                        }
                    }
                    $send_notification_data = array(
                        'notification_list_type' => 2,
                        'notification_list_ids' => $notification_list_ids,
                        'doctor_id' => $doctor_id,
                        'clinic_id' => $clinic_id,
                        'action' => 'reschedule'
                    );
                    $cron_job_path = CRON_PATH . " notification/send_notification/" . base64_encode(json_encode($send_notification_data));
                    exec(PHP_PATH . " " . $cron_job_path . " > /dev/null &");

                    //send sms and email to the doctor based on the status
                    $send_doctor_data = array(
                        'doctor_id' => $doctor_id,
                        'patient_name' => $patient_user_name,
                        'doctor_name' => $doctor_user_name,
                        'doctor_phone_number' => $get_doctor_details[0]['user_phone_number'],
                        'doctor_email' => ($get_doctor_details[0]['user_email_verified'] == 1 ? $get_doctor_details[0]['user_email'] : ""),
                        'patient_email' => ($patient_user_detail['user_email_verified'] == 1 ? $patient_user_detail['user_email'] : ""),
                        'patient_phone_number' => $patient_user_detail['user_phone_number'],
                        'patient_user_id' => $patient_user_detail['user_id'],
                        'appointment_date' => $date,
                        'address' => $doctor_address,
                        'appointment_time' => date('h:i A', $start_timestamp),
                        'start_timestamp' => $start_timestamp,
                        'end_timestamp' => $end_timestamp,
                        'appointment_id' => $appointment_id,
                        'clinic_name' => $clinic_name,
                        'user_type' => $user_type,
                        'appointment_type' => $appointment_type,
                        'clinic_id' => $clinic_id,
                        'email_action' => 'reschedule'
                    );
                    $send_doctor_data['is_send_tele_sms'] = false;
                    if($get_appointmet_detail['appointment_type'] != $appointment_type && ($appointment_type == 4 || $appointment_type == 5)) {
                        $send_doctor_data['is_send_tele_sms'] = true;
                    }

                    $cron_job_path = CRON_PATH . " notification/notify_doctor/" . base64_encode(json_encode($send_doctor_data));
                    exec(PHP_PATH . " " . $cron_job_path . " > /dev/null &");

                    $this->my_response['status'] = true;
                    $this->my_response['message'] = lang('appointment_reschedule_message');
                } else {
                    $this->my_response['status'] = false;
                    $this->my_response['message'] = lang('failure');
                }
            } else {
                $this->my_response['status'] = false;
                $this->my_response['message'] = lang('doctor_not_available');
            }
            echo $this->send_response();
        } catch (Exception $ex) {
            $this->error = $ex->getMessage();
            $this->store_error();
        }
    }

    /**
     * Description :- This function is used to get the appointments of the doctor booked by the patient
     */
    public function get_appointments_list_post() {
        //pass start date and end date in Y-m-d format
        $user_id = !empty($this->post_data['user_id']) ? trim($this->Common_model->escape_data($this->post_data['user_id'])) : "";
        $patient_id = !empty($this->post_data['patient_id']) ? trim($this->Common_model->escape_data($this->post_data['patient_id'])) : "";
        $doctor_id = !empty($this->post_data['doctor_id']) ? trim($this->Common_model->escape_data($this->post_data['doctor_id'])) : "";
        $start_date = !empty($this->post_data['start_date']) ? trim($this->Common_model->escape_data($this->post_data['start_date'])) : "";
        $end_date = !empty($this->post_data['end_date']) ? trim($this->Common_model->escape_data($this->post_data['end_date'])) : "";
        $clinic_id = !empty($this->post_data['clinic_id']) ? trim($this->Common_model->escape_data($this->post_data['clinic_id'])) : "";
        $clinic_id_arr = !empty($this->post_data['clinic_id_arr']) ? $this->post_data['clinic_id_arr'] : "";



        $get_role_details = $this->Common_model->get_the_role($this->user_id);
        if (!empty($get_role_details['user_role_data'])) {
            $permission_data = array(
                'role_data' => $get_role_details['user_role_data'],
                'module' => 2,
                'key' => 3
            );
            $check_module_permission = $this->check_module_permission($permission_data);
            if ($check_module_permission == 2) {
                $this->my_response['status'] = false;
                $this->my_response['message'] = lang('permission_error');
                $this->send_response();
            }
        }

        $array = array(
            'patient_id' => $patient_id,
            'doctor_id' => $doctor_id,
            'start_date' => $start_date,
            'end_date' => $end_date,
            'clinic_id_arr' => $clinic_id_arr,
            // 'clinic_id' => $clinic_id,
        );

        $appointments_list = $this->appointments->get_appointments_list($array);
        foreach ($appointments_list as $key => $value) {
            if(empty($value['user_phone_number'])) {
                $appointments_list[$key]['user_phone_number'] = $value['parent_user_phone_number'];
            }
            $appointments_list[$key]['appointment_time'] = date('h:i A', strtotime($value['appointment_from_time']));
            $current_date_time = get_display_date_time('Y-m-d H:i:s');
            $appointment_date_time = date('Y-m-d H:i:s', strtotime($value['appointment_date'] .' '. $value['appointment_from_time']));
            $appointments_list[$key]['is_past'] = false;  
            if($appointment_date_time < $current_date_time) {
                $appointments_list[$key]['is_past'] = true;    
            }
        }
        if (!empty($appointments_list)) {
            $this->my_response = array(
                "status" => true,
                "message" => lang("common_detail_found"),
                "data" => $appointments_list,
            );
        } else {
            $this->my_response = array(
                "status" => true,
                "message" => lang("common_detail_not_found"),
                "data" => array()
            );
        }
        $this->send_response();
    }

    /**
     * Description :- THis function is used to get the appointment dates
     * based on the doctor and clinic wise
     */
    public function get_patient_appointment_date_post() {

        try {

            $clinic_id = !empty($this->Common_model->escape_data($this->post_data['clinic_id'])) ? trim($this->Common_model->escape_data($this->post_data['clinic_id'])) : '';
            $doctor_id = !empty($this->Common_model->escape_data($this->post_data['doctor_id'])) ? trim($this->Common_model->escape_data($this->post_data['doctor_id'])) : '';
            $patient_id = !empty($this->Common_model->escape_data($this->post_data['patient_id'])) ? trim($this->Common_model->escape_data($this->post_data['patient_id'])) : '';
            $follow_up_flag = !empty($this->Common_model->escape_data($this->post_data['follow_up_flag'])) ? trim($this->Common_model->escape_data($this->post_data['follow_up_flag'])) : 2;
            $page = !empty($this->Common_model->escape_data($this->post_data['page'])) ? trim($this->Common_model->escape_data($this->post_data['page'])) : '1';
            $per_page = !empty($this->Common_model->escape_data($this->post_data['per_page'])) ? trim($this->Common_model->escape_data($this->post_data['per_page'])) : '10';

            if (empty($clinic_id) ||
                    empty($doctor_id) ||
                    empty($patient_id) ||
                    !is_numeric($clinic_id) ||
                    !is_numeric($doctor_id) ||
                    !is_numeric($patient_id)
            ) {
                $this->bad_request();
            }

            $requested_data = array(
                'clinic_id' => $clinic_id,
                'doctor_id' => $doctor_id,
                'patient_id' => $patient_id
            );

            if ($follow_up_flag == 2) {
                $requested_data['page'] = $page;
                $requested_data['per_page'] = $per_page;
            } elseif($follow_up_flag == 1) {
                //Follow up template show only 6 records
                $requested_data['page'] = 1;
                $requested_data['per_page'] = 7;
            }

            $get_patient_appointment_data = $this->appointments->get_patient_appointment_data($requested_data);
            foreach ($get_patient_appointment_data['data'] as $key => $value) {
                $get_patient_appointment_data['data'][$key]['is_call_started_from_patient'] = false;
                if($value['appointment_type'] == 5 && !empty($value['call_start_date_time']) && !empty($value['patient_start_date_time']) && empty($value['doctor_start_date_time']))
                    $get_patient_appointment_data['data'][$key]['is_call_started_from_patient'] = true;
            }
            if (!empty($get_patient_appointment_data['data'])) {
                $this->my_response['status'] = true;
                $this->my_response['message'] = lang('common_detail_found');
                $this->my_response['data'] = $get_patient_appointment_data['data'];
                $this->my_response['total_count'] = $get_patient_appointment_data['no_of_records'];
                $this->my_response['per_page'] = $per_page;
                $this->my_response['current_page'] = $page;
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

    /**
     * Description :- This function is used to set the appointment state
     */
    public function set_appointment_state_post() {

        try {
            $appointment_id = !empty($this->post_data['appointment_id']) ? trim($this->Common_model->escape_data($this->post_data['appointment_id'])) : "";
            $patient_id = !empty($this->post_data['patient_id']) ? trim($this->Common_model->escape_data($this->post_data['patient_id'])) : "";
            $flag = !empty($this->post_data['flag']) ? trim($this->Common_model->escape_data($this->post_data['flag'])) : "";

            if (
                    empty($appointment_id) ||
                    empty($patient_id) ||
                    empty($flag)
            ) {
                $this->bad_request();
                exit;
            }

            if (!in_array($flag, $this->appointment_state)) {
                $this->my_response['status'] = false;
                $this->my_response['message'] = lang("mycontroller_invalid_request");
                $this->send_response();
            }

            //get the appointment details
            $appointment_data = array(
                'appointment_id' => $appointment_id,
                'appointment_user_id' => $patient_id,
                'appointment_status' => 1
            );
            $get_appointmet_detail = $this->appointments->get_appointment_detail($appointment_data);

            if (empty($get_appointmet_detail)) {
                $this->my_response['status'] = false;
                $this->my_response['message'] = lang("mycontroller_invalid_request");
                $this->send_response();
            }

            $update_data = array(
                'appointment_state' => $flag,
                'appointment_updated_at' => $this->utc_time_formated
            );
            $update_where = array(
                'appointment_id' => $appointment_id
            );
            $is_update = $this->Common_model->update(TBL_APPOINTMENTS, $update_data, $update_where);

            if ($is_update > 0) {
                $this->my_response['status'] = true;
                $this->my_response['message'] = lang('common_detail_update');
            } else {
                $this->my_response['status'] = false;
                $this->my_response['message'] = lang('failure');
            }

            $this->send_response();
        } catch (Exception $ex) {
            $this->error = $ex->getMessage();
            $this->store_error();
        }
    }

    /**
     * Description :- This function is used to get the appointed patient details
     */
    public function get_appointed_patient_details_post() {

        try {
            //other user id = patient id
            $other_user_id = !empty($this->post_data['other_user_id']) ? $this->Common_model->escape_data($this->post_data['other_user_id']) : "";
            if (empty($other_user_id)) { $this->bad_request(); }
            $requested_data = array('user_id' => $other_user_id);
            $user_details = $this->appointments->get_appointed_patient_details($requested_data);
            if (!empty($user_details)) {
                $this->load->model('User_model');
                $user_details['user_photo_filepath'] = get_image_thumb($user_details['user_photo_filepath']);
                //get family medical history data
                $family_data = $this->User_model->get_family_medical_history($other_user_id);
                $user_details['family_medical_history_data'] = $family_data;
                $columns = "u.user_phone_number,u.user_first_name,u.user_last_name,map.mapping_relation";
                $caretaker_data = $this->User_model->get_linked_family_members($other_user_id, $columns);
                foreach ($caretaker_data as $key => $caretaker) {
                    $caretaker_data[$key]->relation = relation_map($caretaker->mapping_relation);
                }
                $user_details['caretaker_data'] = $caretaker_data;

                //get kco details
                $kco_where = array(
                    'clinical_notes_reports_user_id' => $other_user_id,
                    'clinical_notes_reports_status' => 1
                );
                $get_kco_detail = $this->Common_model->get_single_row(TBL_CLINICAL_NOTES_REPORT, 'GROUP_CONCAT(clinical_notes_reports_kco) as kco', $kco_where);
                $user_details['kco'] = $get_kco_detail['kco'];

                $get_vital_data = $this->Common_model->get_vital_data($other_user_id);

                if (!empty($get_vital_data['vital_report_weight'])) {
                    $user_details['user_details_weight'] = $get_vital_data['vital_report_weight'];
                }
				
				$this->my_response['status'] = true;
                $this->my_response['message'] = lang('user_detail_found');
                $this->my_response['user_data'] = $user_details;
            } else {
                $this->my_response['status'] = false;
                $this->my_response['message'] = lang('user_detail_not_found');
            }

            $this->send_response();
        } catch (Exception $ex) {
            $this->error = $ex->getMessage();
            $this->store_error();
        }
    }

    /**
     * Description :- This function is used to check the appointment already taken
     * @param type $patient_detail_params
     * @return int
     */
    public function check_appointment_already_taken($patient_detail_params = array()) {
        if (empty($patient_detail_params)) {
            return 2;
        }
		
        $is_appointment_taken = 2;
        $patient_detail_params['key'] = 1;
        $vital = $this->user->get_patient_report_detail($patient_detail_params);

        if (empty($vital)) {
            $patient_detail_params['key'] = 2;
            $clinical_notes = $this->user->get_patient_report_detail($patient_detail_params);

            if (empty($clinical_notes)) {
                $patient_detail_params['key'] = 3;
                $prescription = $this->user->get_patient_report_detail($patient_detail_params);

                if (empty($prescription)) {
                    $patient_detail_params['key'] = 4;
                    $investigation = $this->user->get_patient_report_detail($patient_detail_params);

                    if (empty($investigation)) {
                        $patient_detail_params['key'] = 5;
                        $procedure = $this->user->get_patient_report_detail($patient_detail_params);

                        if (empty($procedure)) {
                            $patient_detail_params['key'] = 6;
                            $reports = $this->user->get_patient_report_detail($patient_detail_params);

                            if (!empty($reports)) {
                                $is_appointment_taken = 1;
                            }
                        } else {
                            $is_appointment_taken = 1;
                        }
                    } else {
                        $is_appointment_taken = 1;
                    }
                } else {
                    $is_appointment_taken = 1;
                }
            } else {
                $is_appointment_taken = 1;
            }
        } else {
            $is_appointment_taken = 1;
        }
		
        return $is_appointment_taken;
    }

    /**
     * Description :- This function is used to set the appointment state
     */
    public function update_appointment_status_post() {

        try {
            $appointment_id = !empty($this->post_data['appointment_id']) ? trim($this->Common_model->escape_data($this->post_data['appointment_id'])) : "";
            $flag = !empty($this->post_data['flag']) ? trim($this->Common_model->escape_data($this->post_data['flag'])) : 0;

            if (empty($appointment_id)) {
                $this->bad_request();
                exit;
            }

            $update_data = array(
                'is_done' => $flag,
                'appointment_updated_at' => $this->utc_time_formated
            );
            $update_where = array(
                'appointment_id' => $appointment_id
            );
            $is_update = $this->Common_model->update(TBL_APPOINTMENTS, $update_data, $update_where);

            if ($is_update > 0) {
                $this->my_response['status'] = true;
                $this->my_response['message'] = lang('common_detail_update');
            } else {
                $this->my_response['status'] = false;
                $this->my_response['message'] = lang('failure');
            }

            $this->send_response();
        } catch (Exception $ex) {
            $this->error = $ex->getMessage();
            $this->store_error();
        }
    }

    public function change_appointment_type_post() {
        try {
            $appointment_id = !empty($this->post_data['appointment_id']) ? trim($this->Common_model->escape_data($this->post_data['appointment_id'])) : "";
            $appointment_type = !empty($this->post_data['appointment_type']) ? trim($this->Common_model->escape_data($this->post_data['appointment_type'])) : '';

            if (empty($appointment_id) || empty($appointment_type)) {
                $this->bad_request();
                exit;
            }
            $update_data = array(
                'appointment_type' => $appointment_type,
                'appointment_updated_at' => $this->utc_time_formated
            );
            $update_where = array(
                'appointment_id' => $appointment_id
            );
            $is_update = $this->Common_model->update(TBL_APPOINTMENTS, $update_data, $update_where);
            if ($is_update > 0) {
                $this->my_response['status'] = true;
                $this->my_response['message'] = lang('appointment_type_update');
            } else {
                $this->my_response['status'] = false;
                $this->my_response['message'] = lang('failure');
            }
            $this->send_response();
        } catch (Exception $ex) {
            $this->error = $ex->getMessage();
            $this->store_error();
        }
    }

}