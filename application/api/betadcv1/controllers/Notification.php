<?php

class Notification extends CI_Controller {

    public $log_file;

    public function __construct() {
        parent::__construct();
        $this->log_file = LOG_FILE_PATH . 'log_' . date('d-m-Y') . ".txt";
        $this->load->library('whatsapp');
    }

    public function testpush($flag) {
        $message = array();
        $message['send_date'] = "now";
        $message['link'] = $GLOBALS['ENV_VARS']['APP_NOTIFICATION_URL'] . '/staging';
        $message['content'] = "Your email verified successfully";
        $message['data']['notification_list_type'] = 4;

        if ($flag == 1) {
            $message['data']['email_flag'] = 1;
        } else {
            $message['data']['email_flag'] = 2;
        }
        $message['data']['email_verified'] = 1;
        $message['devices'] = array(
            "d5uCrC5lXtk:APA91bHy7xld1sc0RAQLFPox6I8MeLiv8btYmBZydPSSHz9PVTt_k_SI7ifzVRtqkD8mF7I3SLLT1QRHsg7_LaFfT9yGjBNuSSh47cRGHtY1gHNJt_UOK96UI-UbwALzHI6sXqyHB_E8",
        );

        send_pushwoosh_notification($message);
        echo 'send';
        exit;
    }

    public function send_notification($recieve_data) {
        $recieve_data = json_decode(base64_decode($recieve_data), true);
        if(empty($recieve_data['notification_list_ids'])) {
            return;
        }
        $log = LOG_FILE_PATH . 'notification_' . date('d-m-Y') . ".txt";
        file_put_contents($log, "\n  ================ START ".date('d-m-Y h:i:s')." =====================    \n\n", FILE_APPEND);
        $notification_list_type = $recieve_data['notification_list_type'];
        $doctor_id = $recieve_data['doctor_id'];
        $clinic_id = $recieve_data['clinic_id'];
        $title = "MedSign Alert";
        $action = $recieve_data['action'];
        if($action == "book")
            $title = "Appointment Book";
        elseif($action == "cancel")
            $title = "Appointment Cancel";
        elseif($action == "reschedule")
            $title = "Appointment Reschedule";
        $notification_where = array(
            'udt_status' => 1,
            'notification_list_type' => $notification_list_type,
            'udt_device_token !=' => '',
            'notification_list_status' => 2
        );
        $columns = 'udt_u_id,
                    udt_device_token,
                    udt_device_type,
                    notification_list_message,
                    notification_list_user_type,
                    notification_list_user_id';

        $join_array = array(
            TBL_NOTIFICATION => 'udt_u_id = notification_list_user_id'
        );
        $this->db->where_in("notification_list_id", $recieve_data['notification_list_ids']);
        $device_tokens = $this->Common_model->get_all_rows(TBL_USER_DEVICE_TOKENS, $columns, $notification_where, $join_array, array(), '', 'LEFT', '', 'udt_u_id,udt_device_type');
        // echo "<pre>";
        // print_r($device_tokens);
        file_put_contents($log, json_encode($device_tokens), FILE_APPEND);

        if (!empty($device_tokens)) {
            foreach ($device_tokens as $data) {
                $notification_message = $data['notification_list_message'];
                $user_id = $data['notification_list_user_id'];
                //check wheter the notification status is on or off
                //get the share settings for the vital
                $setting_where = array(
                    'setting_type' => 3,
                    'setting_user_id' => $doctor_id,
                    'setting_clinic_id' => $clinic_id,
                );
                $get_setting = $this->Common_model->get_setting($setting_where);
                $notification_status = 1;
                if (!empty($get_setting)) {
                    $setting_array = json_decode($get_setting['setting_data'], true);
                    if (!empty($setting_array) && is_array($setting_array)) {
                        foreach ($setting_array as $setting) {
                            if (!empty($setting['id']) && $setting['id'] == 25 && $action == 'book') {
                                $notification_status = $setting['status'];
                                break;
                            } else if (!empty($setting['id']) && $setting['id'] == 27 && $action == 'reschedule') {
                                $notification_status = $setting['status'];
                                break;
                            } else if (!empty($setting['id']) && $setting['id'] == 26 && $action == 'cancel') {
                                $notification_status = $setting['status'];
                                break;
                            }
                        }
                    }
                } else {
                    $notification_status = 1;
                }
				//$notification_status = 1;
                if ($notification_status == 1) {
					$message = array();
                    $message['device_token'] = $data['udt_device_token'];
                    $message['title'] = $title;
                    $message['body'] = $notification_message;
                    $message['click_action'] = DOMAIN_URL;
                    $result = send_firebase_notification($message);
                    /* $notification_result = send_notification_android(array($data['udt_device_token']), $message, ANDROID_FCM_KEY); */
                    if ($result) {
                        file_put_contents($log, "\n  Successfully send User Id : " . $data['udt_u_id'] . "  \n\n", FILE_APPEND);
                        file_put_contents($log, "\n  Successfully send Message : " . json_encode($message) . "  \n\n", FILE_APPEND);
                        file_put_contents($log, "\n  Push log : " . $result . "  \n\n", FILE_APPEND);
                    } else {
                        file_put_contents($log, "\n  Fail to send notification \n User Id : " . $data['udt_u_id'] . "  \n\n", FILE_APPEND);
                    }
                }
            }
            file_put_contents($log, "\n  ================ END =====================    \n\n", FILE_APPEND);
            $update_where = array(
                'notification_list_status' => 2,
                'notification_list_type' => $notification_list_type
            );
            $update_data = array(
                'notification_list_updated_at' => date('Y-m-d H:i:s'),
                'notification_list_status' => 1
            );
            $this->db->where_in("notification_list_id", $recieve_data['notification_list_ids']);
            $this->Common_model->update(TBL_NOTIFICATION, $update_data, $update_where);
        }
    }

    public function notify_doctor($recieve_data) {

        $recieve_data = json_decode(base64_decode($recieve_data), true);
        //get the nofication status of the doctor
        $notification_where = array(
            'setting_user_id' => $recieve_data['doctor_id'],
            'setting_clinic_id' => $recieve_data['clinic_id'],
            'setting_type' => 3,
            'setting_status' => 1
        );
        $get_setting_data = $this->Common_model->get_single_row(TBL_SETTING, 'setting_data', $notification_where);
		
        $book_sms_notify = 2;
        $book_email_notify = 2;
        $reschedule_sms_notify = 2;
        $reschedule_email_notify = 2;
        $cancel_sms_notify = 2;
        $cancel_email_notify = 2;

        $book_sms_patient_notify = 2;
        $book_email_patient_notify = 2;
        $reschedule_sms_patient_notify = 2;
        $reschedule_email_patient_notify = 2;
        $cancel_sms_patient_notify = 2;
        $cancel_email_patient_notify = 2;
        $book_whatsapp_patient_notify = 1;
        $cancel_whatsapp_patient_notify = 1;
        $reschedule_whatsapp_patient_notify = 1;
        $google_sync_notify = 1;
        if (!empty($get_setting_data)) {
            $get_setting_data = json_decode($get_setting_data['setting_data'], true);
            if (!empty($get_setting_data)) {
                foreach ($get_setting_data as $data) {
                    if ($data['id'] == 1 && $data['status'] == 1) {
                        $book_sms_notify = 1;
                    }
                    if ($data['id'] == 2 && $data['status'] == 1) {
                        $book_email_notify = 1;
                    }
                    if ($data['id'] == 3 && $data['status'] == 1) {
                        $cancel_sms_notify = 1;
                    }
                    if ($data['id'] == 4 && $data['status'] == 1) {
                        $cancel_email_notify = 1;
                    }
                    if ($data['id'] == 7 && $data['status'] == 1) {
                        $reschedule_sms_notify = 1;
                    }
                    if ($data['id'] == 8 && $data['status'] == 1) {
                        $reschedule_email_notify = 1;
                    }
                    if ($data['id'] == 15 && $data['status'] == 1) {
                        $book_sms_patient_notify = 1;
                    }
                    if ($data['id'] == 16 && $data['status'] == 1) {
                        $cancel_sms_patient_notify = 1;
                    }
                    if ($data['id'] == 17 && $data['status'] == 1) {
                        $reschedule_sms_patient_notify = 1;
                    }
                    if ($data['id'] == 18 && $data['status'] == 1) {
                        $book_email_patient_notify = 1;
                    }
                    if ($data['id'] == 19 && $data['status'] == 1) {
                        $cancel_email_patient_notify = 1;
                    }
                    if ($data['id'] == 20 && $data['status'] == 1) {
                        $reschedule_email_patient_notify = 1;
                    }
                    if ($data['id'] == 21 && $data['status'] == 2) {
                        $book_whatsapp_patient_notify = 2;
                    }
                    if ($data['id'] == 22 && $data['status'] == 2) {
                        $cancel_whatsapp_patient_notify = 2;
                    }
                    if ($data['id'] == 23 && $data['status'] == 2) {
                        $reschedule_whatsapp_patient_notify = 2;
                    }
                    if ($data['id'] == 24 && $data['status'] == 2) {
                        $google_sync_notify = 2;
                    }
                }
            }
        }
        $is_sms_patient_notify = 2;
        $is_wa_patient_notify = 2;
        $email_action = $recieve_data['email_action'];
        $doctor_number  = $recieve_data['doctor_phone_number'];
        $doctor_email   = $recieve_data['doctor_email'];
        $patient_email  = $recieve_data['patient_email'];
        $patient_number = $recieve_data['patient_phone_number'];
        $clinic_name    = !empty($recieve_data['clinic_name']) ? $recieve_data['clinic_name'] : '';
        $sms_message    = '';
        $this->load->model('User_model', 'user');
        $columns = 'u.user_id,u.user_phone_number,u.user_phone_verified,u.user_email,u.user_email_verified';
        $parent_members = $this->user->get_linked_family_members($recieve_data['patient_user_id'], $columns);
        $PaymentLink = "";
        $SMSPaymentLink = "";
        $WhatsAppPaymentLink = "";
        $TeleConsultationFee = "";
        $PhoneNumber = "";
        $minutes = "";
        if($recieve_data['appointment_type'] == 5) {
            $get_global_setting_data = $this->Common_model->get_single_row('me_global_settings', 'global_setting_value', ['global_setting_key' => 'teleconsultant_link_send_minutes']);
            $minutes = trim($get_global_setting_data['global_setting_value']);
        }
        if(($recieve_data['appointment_type'] == 5 || $recieve_data['appointment_type'] == 4) && ($email_action == 'book' || !empty($recieve_data['is_send_tele_sms']))) {
            $PhoneNumber = $doctor_number;
            $where = ['doctor_id' => $recieve_data['doctor_id'], 'clinic_id' => $recieve_data['clinic_id']];
            $doctor_payment_mode = $this->Common_model->doctor_payment_mode_link($where);
            foreach ($doctor_payment_mode as $key => $value) {
                if($value->doctor_payment_mode_master_id == 5) {
                    $bank_details = json_decode($value->doctor_payment_mode_upi_link);
                    $PaymentLink .= "Bank Name: " . $bank_details->bank_name . "<br>A/c Holder's Name: " . $bank_details->bank_holder_name . "<br> IFSC Code: " . $bank_details->ifsc_code . "<br>A/c No: " . $bank_details->account_no . "<br><br>";
                    if(empty($SMSPaymentLink))
                        $SMSPaymentLink .= "IFSC Code: " . $bank_details->ifsc_code . " A/c No: " . $bank_details->account_no;
                    $WhatsAppPaymentLink .= "*Bank Name:* " . $bank_details->bank_name . ", *A/c Holder's Name:* " . $bank_details->bank_holder_name . ", *IFSC Code:* " . $bank_details->ifsc_code . ", *A/c No:* " . $bank_details->account_no . " ";
                } else {
                    $PaymentLink .= $value->payment_mode_name . ': ' . $value->doctor_payment_mode_upi_link . " <br><br>";
                    if(empty($SMSPaymentLink))
                        $SMSPaymentLink .= $value->payment_mode_name . ": " . $value->doctor_payment_mode_upi_link;
                    $WhatsAppPaymentLink .= "*".$value->payment_mode_name . ":* " . $value->doctor_payment_mode_upi_link . " ";
                }
            }
            $where_array = array(
                'doctor_clinic_mapping_status' => 1,
                'doctor_clinic_mapping_user_id' => $recieve_data['doctor_id'],
                'doctor_clinic_mapping_clinic_id' => $recieve_data['clinic_id']
            );
            $doctor_clinic_mapping = $this->Common_model->get_single_row('me_doctor_clinic_mapping', 'doctor_clinic_mapping_tele_fees', $where_array);
            if(!empty($doctor_clinic_mapping['doctor_clinic_mapping_tele_fees']) && $doctor_clinic_mapping['doctor_clinic_mapping_tele_fees'] > 0) {
                $TeleConsultationFeeArr = explode(".", $doctor_clinic_mapping['doctor_clinic_mapping_tele_fees']);
                $TeleConsultationFee = $TeleConsultationFeeArr[0];
            }
        }
        if (
                (!empty($doctor_number) || !empty($patient_number)) &&
                (($email_action == 'book') ||
                ($email_action == 'cancel') || 
				($email_action == 'cancel_on_calendar_change') || 
                ($email_action == 'reschedule'))
        ) {

            $send_appointment_message = array();
            $send_appointment_message['patient_id'] = $recieve_data['patient_user_id'];
            $send_appointment_message['doctor_id'] = $recieve_data['doctor_id'];
            $send_appointment_message['is_sms_count'] = true;
            $send_appointment_message['is_check_sms_credit'] = $GLOBALS['ENV_VARS']['CHECK_SMS_CREDIT']['APPOINTMENT_BOOK'];
            $send_promoting_message = array();
            $send_promoting_message['patient_id'] = $recieve_data['patient_user_id'];
            $send_promoting_message['doctor_id'] = $recieve_data['doctor_id'];
            $send_promoting_message['is_sms_count'] = true;
            $send_promoting_message['is_check_sms_credit'] = $GLOBALS['ENV_VARS']['CHECK_SMS_CREDIT']['APPOINTMENT_BOOK'];
            $send_message = array();
            $send_message['doctor_id'] = $recieve_data['doctor_id'];
            $send_message['is_sms_count'] = true;
            $send_message['is_check_sms_credit'] = $GLOBALS['ENV_VARS']['CHECK_SMS_CREDIT']['APPOINTMENT_BOOK'];
            $patient_name = $recieve_data['patient_name'];
            $doctor_name = $recieve_data['doctor_name'];
			$appointment_time = date(DATE_FORMAT, strtotime($recieve_data['appointment_date'])) . ' at ' . $recieve_data['appointment_time'];
			$appointment_date_time_arr = [];
			if ($email_action == 'cancel_on_calendar_change'){
				$appointment_date_time_arr = $recieve_data['patient_all_appointment_date_time_list'];
			}
			if($email_action == 'book' || (($recieve_data['appointment_type'] == 4 || $recieve_data['appointment_type'] == 5) && !empty($recieve_data['is_send_tele_sms']))) {
                $patients_id_arr = [];
                $patients_id_arr[] = $recieve_data['patient_user_id'];
                $patients_id_arr = array_merge($patients_id_arr,array_column($parent_members, 'user_id'));
                $share_link_result = $this->Common_model->get_patient_share_link_log($patients_id_arr);
                $share_link_patients = array_column($share_link_result, 'id','patient_id');
                $share_link_insert_data = [];
                $share_link_update_data = [];
                $patient_share_link_arr = [];
                foreach ($patients_id_arr as $share_link_pt_id) {
                    $reset_token = str_rand_access_token(20);
                    if(empty($share_link_patients[$share_link_pt_id])) {
                        $share_link_insert_data[] = array(
                            'patient_id' => $share_link_pt_id,
                            'doctor_id' => $recieve_data['doctor_id'],
                            'share_clinic_id' => $recieve_data['clinic_id'],
                            'unique_code' => $reset_token,
                            'is_set_password' => 0,
                            'created_at' => date("Y-m-d H:i:s")
                        );
                    } else {
                        $share_link_update_data[] = array(
                            'id' => $share_link_patients[$share_link_pt_id],
                            'doctor_id' => $recieve_data['doctor_id'],
                            'share_clinic_id' => $recieve_data['clinic_id'],
                            'unique_code' => $reset_token,
                            'status' => 1,
                            'updated_at' => date("Y-m-d H:i:s")
                        );
                    }
                    // $PATIENT_SHARE_LINK = DOMAIN_URL . 'pt/'. $reset_token;
                    // $patient_share_link_arr[$share_link_pt_id] = MEDSIGN_WEB_CARE_URL . 'pt/'. $reset_token;
                    $link_id = base64_encode(json_encode(1));
                    $patient_share_link_arr[$share_link_pt_id] = DOMAIN_URL . 'f/'.$link_id.'/' . $reset_token;
                }
                // print_r($patient_share_link_arr);die;
                if(!empty($share_link_insert_data))
                    $this->Common_model->insert_multiple('me_patient_share_link_log', $share_link_insert_data);
                if(!empty($share_link_update_data))
                    $this->Common_model->update_multiple('me_patient_share_link_log', $share_link_update_data, 'id');
            }
            if ($email_action == 'book') {
                $send_appointment_message['phone_number'] = $patient_number;
                $send_promoting_message['phone_number'] = $patient_number;
                $promoting_sms_text = sprintf(lang('promoting_medsign_sms'), $patient_name, create_shorturl($patient_share_link_arr[$recieve_data['patient_user_id']]), DOMAIN_URL, PATIENT_EMAIL_FROM);
                $caretaker_promoting_sms_text = sprintf(lang('promoting_medsign_sms'), $patient_name, 'CARETAKER_SHARE_LINK', DOMAIN_URL, PATIENT_EMAIL_FROM);

                $promoting_whatsapp_sms = sprintf(lang('promoting_wa_medsign_sms'), $patient_name, create_shorturl($patient_share_link_arr[$recieve_data['patient_user_id']]), DOMAIN_URL, PATIENT_EMAIL_FROM);
                $caretaker_promoting_whatsapp_text = sprintf(lang('promoting_wa_medsign_sms'), $patient_name, 'CARETAKER_SHARE_LINK', DOMAIN_URL, PATIENT_EMAIL_FROM);

                if($recieve_data['appointment_type'] == 1) {
                    $patient_sms_text = sprintf(lang('appointment_book_patient'), $patient_name, $doctor_name, date(DATE_FORMAT, strtotime($recieve_data['appointment_date'])) . " at " . $recieve_data['appointment_time'], DOMAIN_URL, PATIENT_EMAIL_FROM);
                    $caretaker_sms_text = sprintf(lang('appointment_book_patient'), $patient_name, $doctor_name, date(DATE_FORMAT, strtotime($recieve_data['appointment_date'])) . " at " . $recieve_data['appointment_time'], DOMAIN_URL, PATIENT_EMAIL_FROM);

                    $whatsapp_sms = sprintf(lang('wa_template_23_book_patient_appoint'), $patient_name, $doctor_name, date(DATE_FORMAT, strtotime($recieve_data['appointment_date'])) . " at " . $recieve_data['appointment_time'], DOMAIN_URL, PATIENT_EMAIL_FROM);
                    $caretaker_whatsapp_text = sprintf(lang('wa_template_23_book_patient_appoint'), $patient_name, $doctor_name, date(DATE_FORMAT, strtotime($recieve_data['appointment_date'])) . " at " . $recieve_data['appointment_time'], DOMAIN_URL, PATIENT_EMAIL_FROM);
                } elseif($recieve_data['appointment_type'] == 4) {
                    $patient_sms_text = sprintf(lang('tele_appointment_book_patient'), $patient_name, $doctor_name, date(DATE_FORMAT, strtotime($recieve_data['appointment_date'])),$recieve_data['appointment_time'],$TeleConsultationFee, $SMSPaymentLink . " \n" . $doctor_name, "\n".$patient_share_link_arr[$recieve_data['patient_user_id']]);
                    $caretaker_sms_text = sprintf(lang('tele_appointment_book_patient'), $patient_name, $doctor_name, date(DATE_FORMAT, strtotime($recieve_data['appointment_date'])),$recieve_data['appointment_time'],$TeleConsultationFee, $SMSPaymentLink . " \n" . $doctor_name, 'CARETAKER_SHARE_LINK');
                    
                    $whatsapp_sms = sprintf(lang('wa_template_25_book_patient_tele_appoint'), $patient_name, $doctor_name, date(DATE_FORMAT, strtotime($recieve_data['appointment_date'])) . " at " . $recieve_data['appointment_time'],$TeleConsultationFee, $WhatsAppPaymentLink, $doctor_name, $patient_share_link_arr[$recieve_data['patient_user_id']]);
                    $caretaker_whatsapp_text = sprintf(lang('wa_template_25_book_patient_tele_appoint'), $patient_name, $doctor_name, date(DATE_FORMAT, strtotime($recieve_data['appointment_date'])) . " at " . $recieve_data['appointment_time'],$TeleConsultationFee, $WhatsAppPaymentLink, $doctor_name, 'CARETAKER_SHARE_LINK');
                } elseif($recieve_data['appointment_type'] == 5) {
                    $patient_sms_text = sprintf(lang('video_appointment_book_patient'), $patient_name, $doctor_name, date(DATE_FORMAT, strtotime($recieve_data['appointment_date'])) . " at " . $recieve_data['appointment_time'], $TeleConsultationFee, $SMSPaymentLink . " \n", $minutes, DOMAIN_URL, PATIENT_EMAIL_FROM);
                    $caretaker_sms_text = sprintf(lang('video_appointment_book_patient'), $patient_name, $doctor_name, date(DATE_FORMAT, strtotime($recieve_data['appointment_date'])) . " at " . $recieve_data['appointment_time'], $TeleConsultationFee, $SMSPaymentLink . " \n", $minutes, DOMAIN_URL, PATIENT_EMAIL_FROM);
                    
                    $whatsapp_sms = sprintf(lang('wa_template_26_book_patient_video_appoint'), $patient_name, $doctor_name, date(DATE_FORMAT, strtotime($recieve_data['appointment_date'])) . " at " . $recieve_data['appointment_time'], $TeleConsultationFee, $WhatsAppPaymentLink, $minutes, DOMAIN_URL, PATIENT_EMAIL_FROM);
                    $caretaker_whatsapp_text = sprintf(lang('wa_template_26_book_patient_video_appoint'), $patient_name, $doctor_name, date(DATE_FORMAT, strtotime($recieve_data['appointment_date'])) . " at " . $recieve_data['appointment_time'], $TeleConsultationFee, $WhatsAppPaymentLink, $minutes, DOMAIN_URL, PATIENT_EMAIL_FROM);
                }
                if($book_sms_patient_notify == 1){
                    $send_appointment_message['message'] = $patient_sms_text;
                    $send_promoting_message['message'] = $promoting_sms_text;
                    $is_sms_patient_notify = 1;
                }
                if($book_whatsapp_patient_notify == 1){
                    $send_appointment_message['whatsapp_sms_body'] = $whatsapp_sms;
                    $send_promoting_message['whatsapp_sms_body'] = $promoting_whatsapp_sms;
                    $is_wa_patient_notify = 1;
                }
                if(!empty($patient_number) && $recieve_data['patient_user_phone_verified'] == 1){
                    send_communication($send_appointment_message);
                    send_communication($send_promoting_message);
                }
                $send_message['message'] = sprintf(lang('appointment_book_doctor'), $doctor_name, $patient_name, $clinic_name, $appointment_time);
                if ($book_sms_notify == 1) {
                    $send_message['phone_number'] = DEFAULT_COUNTRY_CODE . $doctor_number;
                    if($recieve_data['appointment_type'] == 1) {
                        $send_message['message'] = sprintf(lang('appointment_book_doctor'), $doctor_name, $patient_name, $clinic_name, $appointment_time);
                        send_message_by_vibgyortel($send_message);
                    } elseif($recieve_data['appointment_type'] == 4) {
                        $send_message['message'] = sprintf(lang('tele_appointment_book_doctor'), $doctor_name, $patient_name, date(DATE_FORMAT, strtotime($recieve_data['appointment_date'])),$recieve_data['appointment_time'],$patient_number,$TeleConsultationFee, $SMSPaymentLink);
                        send_message_by_vibgyortel($send_message);
                    } elseif($recieve_data['appointment_type'] == 5) {
                        $send_message['message'] = sprintf(lang('video_appointment_book_doctor'), $doctor_name, $patient_name, date(DATE_FORMAT, strtotime($recieve_data['appointment_date'])) . " at " . $recieve_data['appointment_time'], $TeleConsultationFee . "\n" . $SMSPaymentLink, $minutes);
                        send_message_by_vibgyortel($send_message);
                    }
                }
                $patients_email_arr = array();
                foreach ($parent_members as $parent_member) {
                    if($parent_member->user_email_verified == 1) {
                        $patients_email_arr[] = $parent_member->user_email;
                    }
                }
                if(!empty($patient_email)){
                    $patients_email_arr[] = $patient_email;
                }

                /*[START] GOOGLE CAL EVENT SYNC */
                //Sync appointment to patient calendar
                $patient_event_data = array(
                    'email_arr' => $patients_email_arr,
                    'start_timestamp' => $recieve_data['start_timestamp'], // IST Timestamp
                    'end_timestamp' => $recieve_data['end_timestamp'], // IST Timestamp
                    'summary' => APP_NAME . ': ' .lang('patient_event_summary'),
                    'description' => $patient_sms_text,
                    'appointment_id' => $recieve_data['appointment_id'],
                    'user_id' => $recieve_data['patient_user_id'],
                    'is_sync' => $book_email_patient_notify,
                );

                //Sync appointment to doctor calendar
                $doctor_event_data = array(
                    'email_arr' => array($doctor_email),
                    'start_timestamp' => $recieve_data['start_timestamp'], // IST Timestamp
                    'end_timestamp' => $recieve_data['end_timestamp'], // IST Timestamp
                    'summary' => APP_NAME . ': ' .lang('doctor_event_summary'),
                    'description' => $send_message['message'],
                    'appointment_id' => $recieve_data['appointment_id'],
                    'user_id' => $recieve_data['doctor_id'],
                    'is_sync' => $google_sync_notify,
                );
                /*[END] GOOGLE CAL EVENT SYNC */

            } else if ($email_action == 'cancel') {
                $send_appointment_message['phone_number'] = $patient_number;
                $patient_sms_text = sprintf(lang('appointment_cancel_patient'), $patient_name, $doctor_name, short_clinic_name($clinic_name), $appointment_time, DOMAIN_URL, PATIENT_EMAIL_FROM);
                $whatsapp_sms = sprintf(lang('wa_template_29_appoint_cancel_patient'), $patient_name, $doctor_name, $clinic_name, $appointment_time, DOMAIN_URL, PATIENT_EMAIL_FROM);
                if($cancel_sms_patient_notify == 1){
                    $send_appointment_message['message'] = $patient_sms_text;
                    $is_sms_patient_notify = 1;
                }
                if($cancel_whatsapp_patient_notify == 1){
                    $send_appointment_message['whatsapp_sms_body'] = $whatsapp_sms;
                    $is_wa_patient_notify = 1;
                }
                send_communication($send_appointment_message);
                if ($cancel_sms_notify == 1) {
                    $send_message['phone_number'] = DEFAULT_COUNTRY_CODE . $doctor_number;
                    $send_message['message'] = sprintf(lang('appointment_cancel'), $doctor_name, $patient_name, $clinic_name, $appointment_time);
                    send_message_by_vibgyortel($send_message);
                }
                $this->delete_google_event($recieve_data['appointment_id']);
			} else if ($email_action == 'cancel_on_calendar_change') {
                $send_appointment_message['phone_number'] = $patient_number;	
				$doc_disp_app_dtime = '';
				if(count($appointment_date_time_arr) > 0){
					$doc_disp_app_dtime = date(DATE_FORMAT, strtotime($appointment_date_time_arr[0]['appointment_date'])) . ' ' . $appointment_date_time_arr[0]['appointment_time'];
					foreach($appointment_date_time_arr as $appointmentDateTime){
						$appointment_time = $appointmentDateTime['appointment_date'] . ' at ' . $appointmentDateTime['appointment_time'];
						$patient_sms_text = sprintf(lang('appointment_cancel_patient'), $patient_name, $doctor_name, short_clinic_name($clinic_name), $appointment_time, DOMAIN_URL, PATIENT_EMAIL_FROM);
                        $whatsapp_sms = sprintf(lang('wa_template_29_appoint_cancel_patient'), $patient_name, $doctor_name, $clinic_name, $appointment_time, DOMAIN_URL, PATIENT_EMAIL_FROM);

                        if($cancel_sms_patient_notify == 1){
						    $send_appointment_message['message'] = $patient_sms_text;
                            $is_sms_patient_notify = 1;
                        }
                        if($cancel_whatsapp_patient_notify == 1){
                            $send_appointment_message['whatsapp_sms_body'] = $whatsapp_sms;
                            $is_wa_patient_notify = 1;
                        }
                        send_communication($send_appointment_message);
                        $this->delete_google_event($appointmentDateTime['appointment_id']);
					}
				}
				
                if ($cancel_sms_notify == 1) {
                    $send_message['phone_number'] = DEFAULT_COUNTRY_CODE . $doctor_number;
                    $send_message['message'] = sprintf(lang('appointment_cancel_when_doc_change_calendar_doctor'), $doctor_name, $patient_name, short_clinic_name($clinic_name), $doc_disp_app_dtime);
                    send_message_by_vibgyortel($send_message);
                }
            } else if ($email_action == 'reschedule') {
                $send_appointment_message['phone_number'] = $patient_number;                
                if($recieve_data['appointment_type'] == 4 && !empty($recieve_data['is_send_tele_sms'])) {
                    $patient_sms_text = sprintf(lang('tele_appointment_book_patient'), $patient_name, $doctor_name, date(DATE_FORMAT, strtotime($recieve_data['appointment_date'])),$recieve_data['appointment_time'],$TeleConsultationFee, $SMSPaymentLink . " \n". $doctor_name, "\n".$patient_share_link_arr[$recieve_data['patient_user_id']]."\n");
                    $caretaker_sms_text = sprintf(lang('tele_appointment_book_patient'), $patient_name, $doctor_name, date(DATE_FORMAT, strtotime($recieve_data['appointment_date'])),$recieve_data['appointment_time'],$TeleConsultationFee, $SMSPaymentLink . " \n". $doctor_name, 'CARETAKER_SHARE_LINK');

                    $whatsapp_sms = sprintf(lang('wa_template_25_book_patient_tele_appoint'), $patient_name, $doctor_name, date(DATE_FORMAT, strtotime($recieve_data['appointment_date'])) . " at " . $recieve_data['appointment_time'],$TeleConsultationFee, $WhatsAppPaymentLink, $doctor_name, $patient_share_link_arr[$recieve_data['patient_user_id']]);
                    $caretaker_whatsapp_text = sprintf(lang('wa_template_25_book_patient_tele_appoint'), $patient_name, $doctor_name, date(DATE_FORMAT, strtotime($recieve_data['appointment_date'])) . " at " . $recieve_data['appointment_time'],$TeleConsultationFee, $WhatsAppPaymentLink, $doctor_name, 'CARETAKER_SHARE_LINK');
                } elseif($recieve_data['appointment_type'] == 5 && !empty($recieve_data['is_send_tele_sms'])) {
                    $patient_sms_text = sprintf(lang('video_appointment_book_patient'), $patient_name, $doctor_name, date(DATE_FORMAT, strtotime($recieve_data['appointment_date'])) . " at " . $recieve_data['appointment_time'], $TeleConsultationFee, $SMSPaymentLink . " \n", $minutes, DOMAIN_URL, PATIENT_EMAIL_FROM);
                    $caretaker_sms_text = sprintf(lang('video_appointment_book_patient'), $patient_name, $doctor_name, date(DATE_FORMAT, strtotime($recieve_data['appointment_date'])) . " at " . $recieve_data['appointment_time'], $TeleConsultationFee, $SMSPaymentLink . " \n", $minutes, DOMAIN_URL, PATIENT_EMAIL_FROM);

                    $whatsapp_sms = sprintf(lang('wa_template_26_book_patient_video_appoint'), $patient_name, $doctor_name, date(DATE_FORMAT, strtotime($recieve_data['appointment_date'])) . " at " . $recieve_data['appointment_time'], $TeleConsultationFee, $WhatsAppPaymentLink, $minutes, DOMAIN_URL, PATIENT_EMAIL_FROM);
                    $caretaker_whatsapp_text = sprintf(lang('wa_template_26_book_patient_video_appoint'), $patient_name, $doctor_name, date(DATE_FORMAT, strtotime($recieve_data['appointment_date'])) . " at " . $recieve_data['appointment_time'], $TeleConsultationFee, $WhatsAppPaymentLink, $minutes, DOMAIN_URL, PATIENT_EMAIL_FROM);
                } else {
                    $patient_sms_text = sprintf(lang('appointment_reschedule_patient'), $patient_name, $doctor_name, short_clinic_name($clinic_name), $appointment_time, DOMAIN_URL, PATIENT_EMAIL_FROM);

                    $whatsapp_sms = sprintf(lang('wa_template_29_appoint_reschedule_patient'), $patient_name, $doctor_name, $clinic_name, $appointment_time, DOMAIN_URL, PATIENT_EMAIL_FROM);
                }
                if($reschedule_sms_patient_notify == 1) {
                    $send_appointment_message['message'] = $patient_sms_text;
                    $is_sms_patient_notify = 1;
                }
                if($reschedule_whatsapp_patient_notify == 1){
                    $send_appointment_message['whatsapp_sms_body'] = $whatsapp_sms;
                    $is_wa_patient_notify = 1;
                }
                send_communication($send_appointment_message);
                $send_message['message'] = sprintf(lang('appointment_reschedule'), $doctor_name, $patient_name, short_clinic_name($clinic_name), $appointment_time);
                if ($reschedule_sms_notify == 1) {
                    $send_message['phone_number'] = DEFAULT_COUNTRY_CODE . $doctor_number;
                    if($recieve_data['appointment_type'] == 4 && !empty($recieve_data['is_send_tele_sms'])) {
                        $send_message['message'] = sprintf(lang('tele_appointment_book_doctor'), $doctor_name, $patient_name, date(DATE_FORMAT, strtotime($recieve_data['appointment_date'])),$recieve_data['appointment_time'],$patient_number,$TeleConsultationFee, $SMSPaymentLink);
                        send_message_by_vibgyortel($send_message);
                    } elseif($recieve_data['appointment_type'] == 5 && !empty($recieve_data['is_send_tele_sms'])) {
                        $send_message['message'] = sprintf(lang('video_appointment_book_doctor'), $doctor_name, $patient_name, date(DATE_FORMAT, strtotime($recieve_data['appointment_date'])),$recieve_data['appointment_time'], $minutes);
                        send_message_by_vibgyortel($send_message);
                    } else {
                        send_message_by_vibgyortel($send_message);
                    }
                }
                $google_update_data = [
                    'doctor_id' => $recieve_data['doctor_id'],
                    'doctor_description' => $send_message['message'],
                    'patient_description' => $patient_sms_text
                ];
                $this->update_google_event($recieve_data['appointment_id'], $google_update_data);
            }
            if($is_sms_patient_notify == 1 || $is_wa_patient_notify == 1) {
                // Send sms to care giver
                foreach ($parent_members as $parent_member) {
                    if($parent_member->user_phone_verified == 1) {
                        $send_appointment_message['phone_number'] = $parent_member->user_phone_number;
                        $send_appointment_message['patient_id'] = $parent_member->user_id;
                        if($is_sms_patient_notify == 2)
                            $send_appointment_message['message'] = '';
                        if($is_wa_patient_notify == 2)
                            $send_appointment_message['whatsapp_sms_body'] = '';
                        send_communication($send_appointment_message);
                        //Send Promoting sms to care giver
                        if($email_action == 'book'){
                            $send_promoting_message['phone_number'] = $parent_member->user_phone_number;
                            $send_promoting_message['patient_id'] = $parent_member->user_id;
                            if(!empty($caretaker_promoting_sms_text) && !empty($patient_share_link_arr[$parent_member->user_id])){
                                $send_promoting_message['message'] = str_replace('CARETAKER_SHARE_LINK', create_shorturl($patient_share_link_arr[$parent_member->user_id]), $caretaker_promoting_sms_text);
                            }
                            if(!empty($caretaker_promoting_whatsapp_text) && !empty($patient_share_link_arr[$parent_member->user_id])){
                                $send_promoting_message['whatsapp_sms_body'] = str_replace('CARETAKER_SHARE_LINK', create_shorturl($patient_share_link_arr[$parent_member->user_id]), $caretaker_promoting_whatsapp_text);
                            }
                            if($is_sms_patient_notify == 2)
                                $send_promoting_message['message'] = '';
                            if($is_wa_patient_notify == 2)
                                $send_promoting_message['whatsapp_sms_body'] = '';
                            send_communication($send_promoting_message);
                        }
                    }
                }
            }
        }
        $is_send_email = is_email_communication($recieve_data['doctor_id']);
        if(($email_action == 'reschedule' || $email_action == 'book') && $recieve_data['appointment_type'] == 5) {
            $global_settings = $this->Common_model->get_single_row('me_global_settings','global_setting_value', ['global_setting_key'=> 'teleconsultant_link_send_minutes']);
            $link_send_minutes = $global_settings['global_setting_value'];
            $current_time = get_display_date_time("Y-m-d H:i:s");
            $appointment_date_time = $recieve_data['appointment_date'] . ' ' . date('H:i:s', $recieve_data['start_timestamp']);
            if(($link_send_minutes*60) > strtotime($appointment_date_time) - strtotime($current_time)) {
                $where_array = array(
                    'doctor_id' => $recieve_data['doctor_id'],
                    'patient_id' => $recieve_data['patient_user_id'],
                );
                $get_token_data = $this->Common_model->get_single_row('me_video_conf_token', 'session_id,token_id', $where_array);
                if (empty($get_token_data)) {
                   $insert_data = [
                        'doctor_id' => $recieve_data['doctor_id'],
                        'patient_id' => $recieve_data['patient_user_id'],
                        'created_at' => date('Y-m-d H:i:s')
                    ];
                    $this->Common_model->insert('me_video_conf_token', $insert_data);
                } else {
                    $update_data = [
                        'session_id' => NULL,
                        'token_id' => NULL,
                        'updated_at' => date('Y-m-d H:i:s')
                    ];
                    $this->Common_model->update('me_video_conf_token', $update_data, $where_array);
                }
                // $video_call_url = MEDSIGN_WEB_CARE_URL . 'telecall/' . encrypt_decrypt($recieve_data['appointment_id'], 'encrypt');
                $link_id = base64_encode(json_encode(3));
                $video_call_url = create_shorturl(DOMAIN_URL . 'f/'.$link_id.'/' . encrypt_decrypt($recieve_data['appointment_id'], 'encrypt'));
                $send_message = [];
                $send_message['patient_id'] = $recieve_data['patient_user_id'];
                $send_message['doctor_id'] = $recieve_data['doctor_id'];
                $send_message['is_sms_count'] = true;
                $send_message['is_check_sms_credit'] = false;
                $send_message['phone_number'] = $patient_number;
                $send_message['message'] = sprintf(lang('patient_join_video_call_link_sms'), $patient_name, $doctor_name, date('d/m/Y', strtotime($appointment_date_time)) . " at " . date('h:i A', strtotime($appointment_date_time)), $minutes, "\n".$video_call_url."\n");
                $send_message['whatsapp_sms_body'] = sprintf(lang('wa_template_24_patient_join_video_call_link'), $patient_name, $doctor_name, date('d/m/Y', strtotime($appointment_date_time)) . " at " . date('h:i A', strtotime($appointment_date_time)), $minutes, $video_call_url);
                if(!empty($patient_number))
                    send_communication($send_message); 
                foreach ($parent_members as $parent_member) {
                    if($parent_member->user_phone_verified == 1) {
                        $send_message['phone_number'] = $parent_member->user_phone_number;
                        $send_message['patient_id'] = $parent_member->user_id;
                        send_communication($send_message);
                    }
                }
                if($is_send_email && !empty($patient_email)) {
                    $this->load->model('Emailsetting_model');
                    $email_template_data = $this->Emailsetting_model->get_emailtemplate_by_id(51);
                    $parse_arr = array(
                        '{PatientName}' => $patient_name,
                        '{AppointmentTime}' => date('d/m/Y h:i A', strtotime($appointment_date_time)),
                        '{DoctorName}' => $doctor_name,
                        '{callStartTime}' => $minutes,
                        '{PatientVideoCallUrl}' => $video_call_url,
                        '{WebUrl}' => DOMAIN_URL,
                        '{AppName}' => APP_NAME,
                        '{MailContactNumber}' => $email_template_data['email_static_data']['contact_number'],
                        '{MailEmailAddress}' => $email_template_data['email_static_data']['email_id'],
                        '{MailCompanyName}' => $email_template_data['email_static_data']['company_name'],
                        '{CopyRightsYear}' => date('Y')
                    );
                    $message = replace_values_in_string($parse_arr, $email_template_data['email_template_message']);
                    $subject = $email_template_data['email_template_subject'];
                    $this->patient_send_email($patient_email, $subject, $message);
                }
            }
        }
        if (
                (!empty($doctor_email) || !empty($patient_email)) &&
                (($email_action == 'book') || 
                ($email_action == 'cancel') || 
				($email_action == 'cancel_on_calendar_change') || 
                ($email_action == 'reschedule'))
        ) {
            //this is use for get view and store data in variable
            //EMAIL TEMPLATE START BY PRAGNESH
            $this->load->model('Emailsetting_model');
            $send_to_doctor_also = 2;
            $send_to_patient_also = 2;
            if ($email_action == 'book') {
                if($recieve_data['appointment_type'] == 1) {
                    $patient_email_temp_id = 6;
                    $doctor_email_temp_id = 7;
                } elseif($recieve_data['appointment_type'] == 4) {
                    $patient_email_temp_id = 43;
                    $doctor_email_temp_id = 44;
                } elseif($recieve_data['appointment_type'] == 5) {
                    $patient_email_temp_id = 54;
                    $doctor_email_temp_id = 55;
                }
                if($book_email_patient_notify == 1) {
                    $email_template_data = $this->Emailsetting_model->get_emailtemplate_by_id($patient_email_temp_id);
                    $send_to_patient_also = 1;
                }
                if ($book_email_notify == 1) {
                    $doctor_email_template_data = $this->Emailsetting_model->get_emailtemplate_by_id($doctor_email_temp_id);
                    $send_to_doctor_also = 1;
                }
            } else if ($email_action == 'cancel') {
                if($cancel_email_patient_notify == 1) {
                    $email_template_data = $this->Emailsetting_model->get_emailtemplate_by_id(17);
                    $send_to_patient_also = 1;
                }
                if ($cancel_email_notify == 1) {
                    $doctor_email_template_data = $this->Emailsetting_model->get_emailtemplate_by_id(18);
                    $send_to_doctor_also = 1;
                }
			} else if ($email_action == 'cancel_on_calendar_change') {
                $patient_email_temp_id = 29;
                $doctor_email_temp_id = 30;
                if($cancel_email_patient_notify == 1) {
                    $email_template_data = $this->Emailsetting_model->get_emailtemplate_by_id($patient_email_temp_id);
                    $send_to_patient_also = 1;
                }
                if ($cancel_email_notify == 1) {
                    $doctor_email_template_data = $this->Emailsetting_model->get_emailtemplate_by_id($doctor_email_temp_id);
                    $send_to_doctor_also = 1;
                }
            } else if ($email_action == 'reschedule') {
                $patient_email_temp_id = 8;
                $doctor_email_temp_id = 9;
                if($recieve_data['appointment_type'] == 4 && !empty($recieve_data['is_send_tele_sms'])) {
                    $patient_email_temp_id = 43;
                    $doctor_email_temp_id = 44;
                } elseif($recieve_data['appointment_type'] == 5 && !empty($recieve_data['is_send_tele_sms'])) {
                    $patient_email_temp_id = 54;
                    $doctor_email_temp_id = 55;
                }
                if($reschedule_email_patient_notify == 1) {
                    $email_template_data = $this->Emailsetting_model->get_emailtemplate_by_id($patient_email_temp_id);
                    $send_to_patient_also = 1;
                }
                if ($reschedule_email_notify == 1) {
                    $doctor_email_template_data = $this->Emailsetting_model->get_emailtemplate_by_id($doctor_email_temp_id);
                    $send_to_doctor_also = 1;
                }
            }

			$app_time = date(DATE_FORMAT, strtotime($recieve_data['appointment_date'])) . ' ' . $recieve_data['appointment_time'];
			if ($email_action == 'cancel_on_calendar_change')
				$app_time =	implode(', ', array_column($appointment_date_time_arr,'appointment_date_time'));
			
            if ($is_send_email && $send_to_doctor_also == 1) {
				$parse_arr_doctor = array(
                    '{DoctorName}' => $recieve_data['doctor_name'],
                    '{PatientName}' => $recieve_data['patient_name'],
                    '{ClinicName}' => $recieve_data['clinic_name'],
                    '{TeleConsultationFee}' => $TeleConsultationFee,
                    '{PaymentLink}' => $PaymentLink,
                    '{PriorMinutes}' => $minutes,
                    '{Time}' => $app_time,
                    '{WebUrl}' => DOMAIN_URL,
                    '{AppName}' => APP_NAME,
                    '{Address}' => $recieve_data['address'],
                    '{MailContactNumber}' => $doctor_email_template_data['email_static_data']['contact_number'],
                    '{MailEmailAddress}' => $doctor_email_template_data['email_static_data']['email_id'],
                    '{MailCompanyName}' => $doctor_email_template_data['email_static_data']['company_name'],
                    '{CopyRightsYear}' => date('Y')
                );
                
                $message = replace_values_in_string($parse_arr_doctor, $doctor_email_template_data['email_template_message']);
                $subject = $doctor_email_template_data['email_template_subject'];
                //this function help you to send mail to single ot multiple users
                $this->send_email(array($doctor_email => $doctor_email), $subject, $message);
            }
            if($is_send_email && $send_to_patient_also == 1) {
                $patient_email_id_arr = array();
                foreach ($parent_members as $parent_member) {
                    if($parent_member->user_email_verified == 1) {
                        $patient_email_id_arr[$parent_member->user_email] = $parent_member->user_email;
                    }
                }
    			if(!empty($patient_email)){
                    $patient_email_id_arr[$patient_email] = $patient_email;
                }
                if(count($patient_email_id_arr) > 0) {
    				$parse_arr = array(
    					'{DoctorName}' => $recieve_data['doctor_name'],
    					'{PatientName}' => $recieve_data['patient_name'],
    					'{ClinicName}' => $recieve_data['clinic_name'],
                        '{TeleConsultationFee}' => $TeleConsultationFee,
                        '{PaymentLink}' => $PaymentLink,
                        '{PhoneNumber}' => $PhoneNumber,
                        '{PriorMinutes}' => $minutes,
    					'{Time}' => $app_time,
    					'{WebUrl}' => DOMAIN_URL,
    					'{AppName}' => APP_NAME,
    					'{Address}' => $recieve_data['address'],
    					'{MailContactNumber}' => $email_template_data['email_static_data']['contact_number'],
    					'{MailEmailAddress}' => $email_template_data['email_static_data']['email_id'],
    					'{MailCompanyName}' => $email_template_data['email_static_data']['company_name'],
    					'{CopyRightsYear}' => date('Y')
    				);
    				$message = replace_values_in_string($parse_arr, $email_template_data['email_template_message']);
    				$subject = $email_template_data['email_template_subject'];
    				//this function help you to send mail to single ot multiple users
    				$this->patient_send_email($patient_email_id_arr, $subject, $message);
    			}
            }
        }
        if(!empty($doctor_event_data) && !empty($patient_event_data)) {
            $this->add_google_event(['doctor'=> $doctor_event_data, 'patient' => $patient_event_data]);
        }
    }

    /**
     * This function is use add event in google calendar
     * 
     * @param type array $event_data_arr
     */
    public function add_google_event($event_data_arr) {
        require_once $GLOBALS['ENV_VARS']['GOOGLE_CALENDER_FILE_PATH'];
        $add_event_data = array();
        if(!empty($event_data_arr['doctor']) && $event_data_arr['doctor']['is_sync'] == 1) {
            $event_data = $event_data_arr['doctor'];
            $email_arr = $event_data['email_arr'];
            $email_varified_arr = array();
            foreach ($email_arr as $value) {
                if (is_gmail_email($value)) {
                    $email_varified_arr[] = $value;
                }
            }
            if(count($email_varified_arr) > 0) {
                $appointment_start_datetime = date('Y-m-d H:i:s', $event_data['start_timestamp']);
                $appointment_end_datetime = date('Y-m-d H:i:s', $event_data['end_timestamp']);
                $appointment_start_datetime_utc = date_convert($appointment_start_datetime,'Asia/Kolkata','Y-m-d H:i:s',"UTC",'Y-m-d H:i:s');
                $appointment_end_datetime_utc = date_convert($appointment_end_datetime,'Asia/Kolkata','Y-m-d H:i:s',"UTC",'Y-m-d H:i:s');
                $start_time = date('Y-m-d', strtotime($appointment_start_datetime_utc)).'T'.date('H:i:s', strtotime($appointment_start_datetime_utc)).'-00:00';
                $end_time = date('Y-m-d', strtotime($appointment_end_datetime_utc)).'T'.date('H:i:s', strtotime($appointment_end_datetime_utc)).'-00:00';
                $rs = add_event($email_arr,$start_time, $end_time,$event_data['summary'],$event_data['description']);

                if(!empty($event_data['user_id'])) {
                    $add_event_data[$event_data['user_id']] = array(
                        'htmlLink' => $rs->htmlLink,
                        'id' => $rs->id
                    );
                }
            }
        }
        if(!empty($event_data_arr['patient']) && $event_data_arr['patient']['is_sync'] == 1) {
            $event_data = $event_data_arr['patient'];
            $email_arr = $event_data['email_arr'];
            $email_varified_arr = array();
            foreach ($email_arr as $value) {
                if (is_gmail_email($value)) {
                    $email_varified_arr[] = $value;
                }
            }
            if(count($email_varified_arr) > 0) {
                $appointment_start_datetime = date('Y-m-d H:i:s', $event_data['start_timestamp']);
                $appointment_end_datetime = date('Y-m-d H:i:s', $event_data['end_timestamp']);
                $appointment_start_datetime_utc = date_convert($appointment_start_datetime,'Asia/Kolkata','Y-m-d H:i:s',"UTC",'Y-m-d H:i:s');
                $appointment_end_datetime_utc = date_convert($appointment_end_datetime,'Asia/Kolkata','Y-m-d H:i:s',"UTC",'Y-m-d H:i:s');
                $start_time = date('Y-m-d', strtotime($appointment_start_datetime_utc)).'T'.date('H:i:s', strtotime($appointment_start_datetime_utc)).'-00:00';
                $end_time = date('Y-m-d', strtotime($appointment_end_datetime_utc)).'T'.date('H:i:s', strtotime($appointment_end_datetime_utc)).'-00:00';
                $rs = add_event($email_arr,$start_time, $end_time,$event_data['summary'],$event_data['description']);
                if(!empty($event_data['user_id'])) {
                    $add_event_data[$event_data['user_id']] = array(
                        'htmlLink' => $rs->htmlLink,
                        'id' => $rs->id
                    );
                }
            }
        }
        if(!empty($event_data_arr['patient']['appointment_id']) && count($add_event_data) > 0) {
            $this->load->model("Doctor_model", "doctor");
            $update_array = array(
                "appointment_updated_at" => date("Y-m-d H:i:s"),
                "calendar_event_json" => json_encode($add_event_data)
            );
            
            $where = array(
                "appointment_id" => $event_data_arr['patient']['appointment_id']
            );
            $this->doctor->update(TBL_APPOINTMENTS, $update_array, $where);
        }
    }

    public function delete_google_event($appointment_id) {
        require_once $GLOBALS['ENV_VARS']['GOOGLE_CALENDER_FILE_PATH'];
        $this->load->model("Appointments_model", "appointments");
        $get_appointmet_detail = $this->appointments->get_appointment_detail_byid($appointment_id,'calendar_event_json');
        if(!empty($get_appointmet_detail['calendar_event_json'])) {
            foreach (json_decode($get_appointmet_detail['calendar_event_json']) as $key => $value) {
                delete_event($value->id);
            }
        }
    }

    public function update_google_event($appointment_id, $data) {
        require_once $GLOBALS['ENV_VARS']['GOOGLE_CALENDER_FILE_PATH'];
        $this->load->model("Appointments_model", "appointments");
        $get_appointmet_detail = $this->appointments->get_appointment_detail_byid($appointment_id,'calendar_event_json,appointment_from_time,appointment_to_time,appointment_date');
        if(!empty($get_appointmet_detail['calendar_event_json'])) {
            $appointment_start_datetime = $get_appointmet_detail['appointment_date'] . ' ' . $get_appointmet_detail['appointment_from_time'];
            $appointment_end_datetime = $get_appointmet_detail['appointment_date'] . ' ' . $get_appointmet_detail['appointment_to_time'];
            $appointment_start_datetime_utc = date_convert($appointment_start_datetime,'Asia/Kolkata','Y-m-d H:i:s',"UTC",'Y-m-d H:i:s');
            $appointment_end_datetime_utc = date_convert($appointment_end_datetime,'Asia/Kolkata','Y-m-d H:i:s',"UTC",'Y-m-d H:i:s');
            $start_time = date('Y-m-d', strtotime($appointment_start_datetime_utc)).'T'.date('H:i:s', strtotime($appointment_start_datetime_utc)).'-00:00';
            $end_time = date('Y-m-d', strtotime($appointment_end_datetime_utc)).'T'.date('H:i:s', strtotime($appointment_end_datetime_utc)).'-00:00';
            foreach (json_decode($get_appointmet_detail['calendar_event_json']) as $key => $value) {
                if($data['doctor_id'] == $key)
                    $description = $data['doctor_description'];
                else
                    $description = $data['patient_description'];
                update_event($value->id,$start_time,$end_time,$description);
            }
        }
    }

    public function send_mail_background($recieve_data) {

        $recieve_data = json_decode(base64_decode($recieve_data), true);
        
        if (!empty($recieve_data)) {
            $user_name = $recieve_data['user_name'];
            $email = $recieve_data['user_email'];
            $templated_id = $recieve_data['template_id'];

            if (in_array($templated_id, array(1, 2))) {
                $verify_link = $recieve_data['verify_link'];
            }

            //send the updatation male to the user
            $this->load->model('Emailsetting_model');
            $email_template_data = $this->Emailsetting_model->get_emailtemplate_by_id($templated_id);

            switch ($templated_id) {
                case 1:
                    $parse_arr = array(
                        '{PatientName}' => $user_name,
                        '{VerificationLink}' => $verify_link,
                        '{WebUrl}' => DOMAIN_URL,
                        '{AppName}' => APP_NAME,
                        '{MailContactNumber}' => $email_template_data['email_static_data']['contact_number'],
                        '{MailEmailAddress}' => $email_template_data['email_static_data']['email_id'],
                        '{MailCompanyName}' => $email_template_data['email_static_data']['company_name'],
                        '{CopyRightsYear}' => date('Y')
                    );
                    break;
                case 2:
                    $parse_arr = array(
                        '{DoctorName}' => $user_name,
                        '{VerificationLink}' => $verify_link,
                        '{WebUrl}' => DOMAIN_URL,
                        '{AppName}' => APP_NAME,
                        '{MailContactNumber}' => $email_template_data['email_static_data']['contact_number'],
                        '{MailEmailAddress}' => $email_template_data['email_static_data']['email_id'],
                        '{MailCompanyName}' => $email_template_data['email_static_data']['company_name'],
                        '{CopyRightsYear}' => date('Y')
                    );
                    break;
                case 24:
                    $parse_arr = array(
                        '{PatientName}' => $user_name,
                        '{WebUrl}' => DOMAIN_URL,
                        '{AppName}' => APP_NAME,
                        '{MailContactNumber}' => $email_template_data['email_static_data']['contact_number'],
                        '{MailEmailAddress}' => $email_template_data['email_static_data']['email_id'],
                        '{MailCompanyName}' => $email_template_data['email_static_data']['company_name'],
                        '{CopyRightsYear}' => date('Y')
                    );
                    break;
                case 16:
                    $issue = !empty($recieve_data['issue']) ? $recieve_data['issue'] : '';
                    $issue_email = !empty($recieve_data['issue_email']) ? $recieve_data['issue_email'] : '';
                    $attachment_link = !empty($recieve_data['attachment_link']) ? $recieve_data['attachment_link'] : '';
                    $attachment_label = '';
                    if(!empty($attachment_link))
                        $attachment_label = 'Click the followings links to see the issues';
                    $parse_arr = array(
                        '{UserName}' => $user_name,
                        '{Issue}' => $issue,
                        '{IssueEmail}' => $issue_email,
                        '{AttachmentLink}' => $attachment_link,
                        '{AttachmentLabel}' => $attachment_label,
                        '{WebUrl}' => DOMAIN_URL,
                        '{AppName}' => APP_NAME,
                        '{MailContactNumber}' => $email_template_data['email_static_data']['contact_number'],
                        '{MailEmailAddress}' => $email_template_data['email_static_data']['email_id'],
                        '{MailCompanyName}' => $email_template_data['email_static_data']['company_name'],
                        '{CopyRightsYear}' => date('Y')
                    );
                    break;
                case 25:
                    $parse_arr = array(
                        '{DoctorName}' => $user_name,
                        '{WebUrl}' => DOMAIN_URL,
                        '{AppName}' => APP_NAME,
                        '{MailContactNumber}' => $email_template_data['email_static_data']['contact_number'],
                        '{MailEmailAddress}' => $email_template_data['email_static_data']['email_id'],
                        '{MailCompanyName}' => $email_template_data['email_static_data']['company_name'],
                        '{CopyRightsYear}' => date('Y')
                    );
                    break;
                case 27:
                    $doctor_name = $recieve_data['doctor_name'];
                    $parse_arr = array(
                        '{UserName}' => $user_name,
                        '{DoctorName}' => $doctor_name,
                        '{WebUrl}' => DOMAIN_URL,
                        '{AppName}' => APP_NAME,
                        '{MailContactNumber}' => $email_template_data['email_static_data']['contact_number'],
                        '{MailEmailAddress}' => $email_template_data['email_static_data']['email_id'],
                        '{MailCompanyName}' => $email_template_data['email_static_data']['company_name'],
                        '{CopyRightsYear}' => date('Y')
                    );
                    break;
                case 26:
                    $doctor_name = $recieve_data['doctor_name'];
                    $clinic_name = $recieve_data['clinic_name'];
                    $parse_arr = array(
                        '{UserName}' => $user_name,
                        '{DoctorName}' => $doctor_name,
                        '{ClinicName}' =>$clinic_name,
                        '{WebUrl}' => DOMAIN_URL,
                        '{AppName}' => APP_NAME,
                        '{MailContactNumber}' => $email_template_data['email_static_data']['contact_number'],
                        '{MailEmailAddress}' => $email_template_data['email_static_data']['email_id'],
                        '{MailCompanyName}' => $email_template_data['email_static_data']['company_name'],
                        '{CopyRightsYear}' => date('Y')
                    );
                    break;
            }
            
            $message = replace_values_in_string($parse_arr, $email_template_data['email_template_message']);
            $subject = $email_template_data['email_template_subject'];
            //EMAIL TEMPLATE END BY PRAGNESH
            //this function help you to send mail to single ot multiple users
            if(!empty($email)){
                if($email_template_data['email_user_type'] == 1) {
                    $this->patient_send_email(array($email => $email), $subject, $message);
                } else {
                    $this->send_email(array($email => $email), $subject, $message);
                }
            }
        }
    }

    public function patient_welcome_email($recieve_data) {
        $recieve_data = json_decode(base64_decode($recieve_data), true);
        $user_id = $recieve_data['user_id'];
        $doctor_id = $recieve_data['doctor_id'];
        $notification_where = array(
            'setting_user_id' => $doctor_id,
            'setting_type' => 12,
            'setting_status' => 1
        );
        $get_setting_data = $this->Common_model->get_single_row(TBL_SETTING, 'setting_data', $notification_where);
        
        $sms_notify = 2;
        $email_notify = 2;
        $whatsapp_notify = 2;
        if (!empty($get_setting_data)) {
            $get_setting_data = json_decode($get_setting_data['setting_data'], true);
            if (!empty($get_setting_data)) {
                foreach ($get_setting_data as $data) {
                    if ($data['id'] == 1 && $data['status'] == 1) {
                        $sms_notify = 1;
                    }
                    if ($data['id'] == 2 && $data['status'] == 1) {
                        $email_notify = 1;
                    }
                    if ($data['id'] == 3 && $data['status'] == 1) {
                        $whatsapp_notify = 1;
                    }
                }
            }
        }
        $this->load->model('Emailsetting_model');
        $email_template_data = $this->Emailsetting_model->get_emailtemplate_by_id(63);
        $user_row = $this->Common_model->get_single_row('me_users', 'user_first_name,user_last_name,user_email,user_phone_number', ['user_id' => $user_id]);

        if(!empty($user_row['user_phone_number'])) {
            $send_message['phone_number'] = $user_row['user_phone_number'];
            if($sms_notify == 1)
                $send_message['message'] = sprintf(lang('patient_reg_sms'), $user_row['user_first_name'] . ' ' .$user_row['user_last_name'], "email- " . PATIENT_EMAIL_FROM);
            if($whatsapp_notify == 1)
                $send_message['whatsapp_sms_body'] = sprintf(lang('patient_register_msg_49'), $user_row['user_first_name'] . ' ' .$user_row['user_last_name'], "email- ".PATIENT_EMAIL_FROM." or call - +91 7977409143 / +91 7507352233");
            $send_message['is_not_check_setting_flag'] = true;
            $send_message['patient_id'] = $user_id;
            $send_message['doctor_id'] = $doctor_id;
            $send_message['is_sms_count'] = true;
            $send_message['user_type'] = 2;
            if($sms_notify == 1 || $whatsapp_notify == 1)
                send_communication($send_message);
        }
        $parse_arr = array(
            '{userName}' => $user_row['user_first_name'] . ' ' .$user_row['user_last_name'],
            '{WebUrl}' => DOMAIN_URL,
            '{AppName}' => APP_NAME,
            '{MailContactNumber}' => $email_template_data['email_static_data']['contact_number'],
            '{MailEmailAddress}' => $email_template_data['email_static_data']['email_id'],
            '{MailCompanyName}' => $email_template_data['email_static_data']['company_name'],
            '{CopyRightsYear}' => date('Y')
        );
        $message = replace_values_in_string($parse_arr, $email_template_data['email_template_message']);
        $subject = $email_template_data['email_template_subject'];
        if(!empty($user_row['user_email']) && $email_notify == 1){
            $is_send_email = is_email_communication($doctor_id);
            if($is_send_email)
                $this->patient_send_email(array($user_row['user_email'] => $user_row['user_email']), $subject, $message);
        }
        die("Done");
    }

    public function share_qr_code_link($recieve_data) {
        $recieve_data = json_decode(base64_decode($recieve_data), true);
        $patient_name = $recieve_data['patient_name'];
        $shareLink = MEDSIGN_WEB_CARE_URL . 'register?id=' . $recieve_data['encrp_id'];
        $this->load->model('Emailsetting_model');
        if($recieve_data['share_type'] == 'email') {
            $email_template_data = $this->Emailsetting_model->get_emailtemplate_by_id(68);
            $parse_arr = array(
                '{userName}'          => $patient_name,
                '{shareLink}'         => create_shorturl($shareLink),
                '{WebUrl}'            => DOMAIN_URL,
                '{AppName}'           => APP_NAME,
                '{MailContactNumber}' => $email_template_data['email_static_data']['contact_number'],
                '{MailEmailAddress}'  => $email_template_data['email_static_data']['email_id'],
                '{MailCompanyName}'   => $email_template_data['email_static_data']['company_name'],
                '{CopyRightsYear}'    => date('Y')
            );
            $message = replace_values_in_string($parse_arr, $email_template_data['email_template_message']);
            $subject = $email_template_data['email_template_subject'];
            $this->patient_send_email(array($recieve_data['email'] => $recieve_data['email']), $subject, $message);
        } elseif($recieve_data['share_type'] == 'sms' || $recieve_data['share_type'] == 'whatsapp') {
            $shareShortLink = create_shorturl($shareLink);
            $promoting_sms_text = sprintf(lang('promoting_medsign_sms'), $patient_name, $shareShortLink, DOMAIN_URL, PATIENT_EMAIL_FROM);
            $promoting_whatsapp_sms = sprintf(lang('promoting_wa_medsign_sms'), $patient_name, $shareShortLink, DOMAIN_URL, PATIENT_EMAIL_FROM);
            $send_promoting_message = array();
            $send_promoting_message['doctor_id'] = $doctor_id;
            $send_promoting_message['is_sms_count'] = true;
            $send_promoting_message['phone_number'] = $recieve_data['mobile_no'];
            if($recieve_data['share_type'] == 'sms')
                $send_promoting_message['message'] = $promoting_sms_text;
            if($recieve_data['share_type'] == 'whatsapp')
                $send_promoting_message['whatsapp_sms_body'] = $promoting_whatsapp_sms;
            send_communication($send_promoting_message);
        }
        echo "Share done";
        exit;
    }

    public function add_patient_email($recieve_data) {
        $recieve_data = json_decode(base64_decode($recieve_data), true);
        $verify_link = DOMAIN_URL . "verifyaccount/" . $recieve_data['reset_token'];
        $this->load->model('Emailsetting_model');
        $email_template_data = $this->Emailsetting_model->get_emailtemplate_by_id(14);
        $parse_arr = array(
            '{Email}'             => $recieve_data['email'],
            '{Email_id}'          => $recieve_data['email'],
            '{PatinetName}'       => $recieve_data['first_name'],
            '{Password}'          => $recieve_data['password'],
            '{EmailPasswordImage}'=> 1,
            '{UniqueId}'          => $recieve_data['unique_id'],
            '{VerificationLink}'  => $verify_link,
            '{WebUrl}'            => DOMAIN_URL,
            '{AppName}'           => APP_NAME,
            '{MailContactNumber}' => $email_template_data['email_static_data']['contact_number'],
            '{MailEmailAddress}'  => $email_template_data['email_static_data']['email_id'],
            '{MailCompanyName}'   => $email_template_data['email_static_data']['company_name'],
            '{CopyRightsYear}'    => date('Y')
        );
        $message = replace_values_in_string($parse_arr, $email_template_data['email_template_message']);
        $subject = $email_template_data['email_template_subject'];
        //this function help you to send mail to single ot multiple users
        $this->patient_send_email(array($recieve_data['email'] => $recieve_data['email']), $subject, $message);
    }

    /**
     * This function is use for send email
     * 
     * @param type $to_email_address
     * @param type $subject
     * @param type $message
     */
    public function send_email($to_email_address, $subject, $message) {
        try {
            require_once SWIFT_MAILER_PATH;
            $transport = Swift_SmtpTransport::newInstance(EMAIL_HOST, EMAIL_PORT, EMAIL_CERTIFICATE)
                    ->setUsername(EMAIL_USER)
                    ->setPassword(EMAIL_PASS);
            $mailer = Swift_Mailer::newInstance($transport);
            $sendMessage = Swift_Message::newInstance($subject)
                    ->setFrom(array(EMAIL_FROM => APP_NAME))
                    ->setTo($to_email_address);

            $doc = new DOMDocument();
            @$doc->loadHTML($message);
            $tags = $doc->getElementsByTagName('img');
            foreach ($tags as $tag) {
                if (filter_var($tag->getAttribute('src'), FILTER_VALIDATE_URL)) {
                    
                } else {
                    //$attachment = Swift_Image::newInstance($tag->getAttribute('src'),"image.png",'image/png')->setDisposition('inline');
                    $imag_src = str_replace("data:image/png;base64,", "", $tag->getAttribute('src'));
                    $imag_src = str_replace(" ", "+", $imag_src);
                    $attachment = new Swift_Image(base64_decode($imag_src), 'image.png', 'image/png');
                    //echo $attachment;exit;
                    $cid = $sendMessage->embed($attachment);
                    $tag->setAttribute('src', $cid);
                }
            }
            $sendMessage->setBody($doc->saveHTML(), "text/html");

            if (!$mailer->send($sendMessage)) {
                $this->send_normal_email($to_email_address, $subject, $message);
            }
        } catch (\Swift_TransportException $e) {
            $response = $e->getMessage();
            $this->send_normal_email($to_email_address, $subject, $message);
            file_put_contents($this->log_file, "\n\n ======== swift send_email ex: " . json_encode($response) . "===== \n\n", FILE_APPEND | LOCK_EX);
        } catch (Exception $e) {
            $response = $e->getMessage();
            $this->send_normal_email($to_email_address, $subject, $message);
            file_put_contents($this->log_file, "\n\n ======== send_email ex: " . json_encode($response) . "===== \n\n", FILE_APPEND | LOCK_EX);
        }
    }

    public function patient_send_email($to_email_address, $subject, $message) {
        // echo PATIENT_EMAIL_USER;
        // echo "====";
        // echo PATIENT_EMAIL_PASS;die;
        try {
            require_once SWIFT_MAILER_PATH;
            $transport = Swift_SmtpTransport::newInstance(PATIENT_EMAIL_HOST, PATIENT_EMAIL_PORT, EMAIL_CERTIFICATE)
                    ->setUsername(PATIENT_EMAIL_USER)
                    ->setPassword(PATIENT_EMAIL_PASS);
            $mailer = Swift_Mailer::newInstance($transport);
            $sendMessage = Swift_Message::newInstance($subject)
                    ->setFrom(array(PATIENT_EMAIL_FROM => APP_NAME))
                    ->setTo($to_email_address);

            $doc = new DOMDocument();
            @$doc->loadHTML($message);
            $tags = $doc->getElementsByTagName('img');
            foreach ($tags as $tag) {
                if (filter_var($tag->getAttribute('src'), FILTER_VALIDATE_URL)) {
                    
                } else {
                    //$attachment = Swift_Image::newInstance($tag->getAttribute('src'),"image.png",'image/png')->setDisposition('inline');
                    $imag_src = str_replace("data:image/png;base64,", "", $tag->getAttribute('src'));
                    $imag_src = str_replace(" ", "+", $imag_src);
                    $attachment = new Swift_Image(base64_decode($imag_src), 'image.png', 'image/png');
                    //echo $attachment;exit;
                    $cid = $sendMessage->embed($attachment);
                    $tag->setAttribute('src', $cid);
                }
            }
            $sendMessage->setBody($doc->saveHTML(), "text/html");
            if (!$mailer->send($sendMessage)) {
                $this->send_normal_email($to_email_address, $subject, $message);
            }
        } catch (\Swift_TransportException $e) {
            $response = $e->getMessage();
            $this->send_normal_email($to_email_address, $subject, $message);
            file_put_contents($this->log_file, "\n\n ======== swift send_email ex: " . json_encode($response) . "===== \n\n", FILE_APPEND | LOCK_EX);
        } catch (Exception $e) {
            $response = $e->getMessage();
            $this->send_normal_email($to_email_address, $subject, $message);
            file_put_contents($this->log_file, "\n\n ======== send_email ex: " . json_encode($response) . "===== \n\n", FILE_APPEND | LOCK_EX);
        }
    }

    /**
     * send mail if failed by swift mailer
     * @param type $to_email_address
     * @param type $subject
     * @param type $message
     */
    public function send_normal_email($to_email_address, $subject, $message) {
        try {
            $headers = "MIME-Version: 1.0\r\n";
            $headers .= "Content-type: text/html; charset=utf-8\r\n";
            $headers .= "X-Priority: 3\r\n";
            $headers .= "X-Mailer: PHP" . phpversion() . "\r\n";
            $headers .= "From:" . APP_NAME . "<" . EMAIL_USER . ">" . "\r\n";
            foreach ($to_email_address as $email => $name) {
                mail($email, $subject, $message, $headers);
            }
        } catch (Exception $e) {
            $response = $e->getMessage();
            file_put_contents($this->log_file, "\n\n ======== send_normal_email ex: " . json_encode($response) . "===== \n\n", FILE_APPEND | LOCK_EX);
        }
    }



}