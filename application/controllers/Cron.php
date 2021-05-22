<?php
use Twilio\Rest\Client;
use OpenTok\OpenTok;
use Mpdf\Mpdf;
class Cron extends CI_Controller {
    public function __construct() {
        parent::__construct();
        $this->email_log_file = LOG_FILE_PATH . '/email_log_' . date('d-m-Y') . '.txt';
        $this->health_link_file = LOG_FILE_PATH . '/health_link_log_' . date('d-m-Y') . '.txt';
        $this->whatsapp_log_file = LOG_FILE_PATH . '/whatsapp_log_' . date('d-m-Y') . '.txt';
        ini_set('max_execution_time', 0); 
        ini_set('memory_limit','2048M');
    }

    public function google_translate() {
        require_once $GLOBALS['ENV_VARS']['GOOGLE_TRANSLATE_FILE_PATH'];
        $text = "After food";
        $targetLang = "hi";
        echo google_translate($text, $targetLang);
    }
	public function clear_api_logs_table() {
        $table_name = TBL_API_LOGS;
        $folder_path = UPLOAD_FILE_FULL_PATH . API_LOG_BKP . "/";
        if (!file_exists($folder_path)) {
            mkdir($folder_path, 0777, TRUE);
            chmod($folder_path, 0777);
        }
        $filePathPrefix = date('Y_m_d_H_i', strtotime('-1 day', strtotime(get_display_date_time('Y-m-d H:i:s'))));
        $this->load->dbutil();
        $prefs = array(
            'tables'        => array($table_name),                     // Array of tables to backup.
            'ignore'        => array(),                     // List of tables to omit from the backup
            'format'        => 'zip',                       // gzip, zip, txt
            'filename'      => $filePathPrefix.'.sql', // File name - NEEDED ONLY WITH ZIP FILES
            'add_drop'      => TRUE,                        // Whether to add DROP TABLE statements to backup file
            'add_insert'    => TRUE,                        // Whether to add INSERT data to backup file
            'newline'       => "\n"                         // Newline character used in backup file
        );
        // Backup your entire database and assign it to a variable
        $backup = $this->dbutil->backup($prefs);
        // Load the file helper and write the file to your server
        $this->load->helper('file');
        $filename = $filePathPrefix.'.zip';
        if(write_file($folder_path.$filename, $backup) ) {
            $zip = new ZipArchive();
            if ($zip->open(UPLOAD_FILE_FULL_PATH . $filePathPrefix.'_apilog.zip', ZipArchive::CREATE) === TRUE) {
                $secretKey = encrypt_decrypt(str_replace('_', '', $filePathPrefix), 'encrypt');
                $zip->setPassword($secretKey);
                $zip->addFile('uploads/'.API_LOG_BKP . '/'. $filename);
                $zip->setEncryptionName('uploads/'.API_LOG_BKP . '/'. $filename, ZipArchive::EM_AES_256);
                $zip->close();
                unlink($folder_path.$filename);
                $rs = upload_to_s3(UPLOAD_FILE_FULL_PATH . $filePathPrefix.'_apilog.zip', API_LOG_BKP . "/". $filePathPrefix.'_apilog.zip');
                $this->dbBackupFileMoveToGDrive($filePathPrefix);
                if($rs){
                    $this->db->query("TRUNCATE $table_name");
                    unlink(UPLOAD_FILE_FULL_PATH . $filePathPrefix.'_apilog.zip');
                    $msg = "Api log Table has been backed up successfully.";
                } else {
                    $msg = "Api log Table Fail to upload S3.";
                }
            }
        } else {
            $msg = "Sorry, something went wrong. Api log Table.";
        }
        $log_file = LOG_FILE_PATH . '/ApilogTableBackUp_' . date('d-m-Y') . '.txt';
        file_put_contents($log_file, "=================" .get_display_date_time("Y-m-d H:i:s"). "=================\n " . $msg . "\n", FILE_APPEND | LOCK_EX);
        echo "Done.";
        exit;
    }
    public function dbBackupFileMoveToGDrive($fileName){
        if(empty($fileName))
            return false;
        
        require_once DOCROOT_PATH . 'application/third_party/google_api/google-api-php-client-2.2.3/vendor/autoload.php';
        $client = new Google_Client();
        //The json file you got after creating the service account
        putenv('GOOGLE_APPLICATION_CREDENTIALS=' . DOCROOT_PATH . 'application/third_party/google_api/key/medsigndevelopment-8a95c11708e7.json'); //GOOGLE DRIVE-48f93ac19c8a.json
        $client->useApplicationDefaultCredentials();
        $client->setApplicationName("GDrive");
        $client->addScope(Google_Service_Drive::DRIVE);
        $client->setAccessType('offline');
        $folderId = '1mA1TvVMzYh6gApgYDMALlzkBYKn1lcuG'; //https://drive.google.com/drive/folders/1mA1TvVMzYh6gApgYDMALlzkBYKn1lcuG
        $driveService = new Google_Service_Drive($client);

        /*[START] Create the folder on the Google drive*/
        $file = new Google_Service_Drive_DriveFile(
            ['name'=>date('d-m-Y').'_APILOG',
            'title'=>date('d-m-Y').'_APILOG',
            'description'=>'Database backup at '.date('d-m-Y'),
            'parents' => array($folderId),
            'mimeType'=>"application/vnd.google-apps.folder"
        ]);
        $objFolder = $driveService->files->create($file, array());
        $folderId = $objFolder->id;
        /*[END] Create the folder on the Google drive*/
        /*[START] Uploading apilog table backup file on the Google drive*/
        $dbFileName = $fileName.'_apilog.zip';
        $fileMetadata = new Google_Service_Drive_DriveFile(array('name' => $dbFileName,'parents' => array($folderId)));
        $content = file_get_contents(UPLOAD_FILE_FULL_PATH . '/' . $dbFileName);
        $file = $driveService->files->create($fileMetadata, array('data' => $content, 'mimeType' => 'application/zip', 'uploadType' => 'multipart', 'fields' => 'id'));        
        if(isset($file->id) && $file->id != '') {
            
        }
    }
    public function send_whatsapp_bulk_message($page = 1) {
        $per_page = 100;
        $result = $this->Common_model->get_all_doctors($page, $per_page);
        // echo "<pre>";
        echo "Page: " . $page . "<br>";
        echo count($result);
        die;
        $this->load->library('whatsapp');
        $msg = "During normal times or Pandemic times, MedSign supports your clinic management needs. MedSign for all Times.
Email - support@medsign.in https://www.medsign.in/";

        foreach ($result as $key => $value) {
            if(strlen($value->user_phone_number) == 10) {
                $whatsapp_data = [
                    'is_promotional' => true,
                    'doctor_id' => $value->user_id,
                    'user_type' => 2,
                    'mobile'=> $value->user_phone_number,
                    'body'=> $msg
                ];
                $this->whatsapp->send_test_message($whatsapp_data);
            } else {
                echo $value->user_phone_number. "<br>";
            }
        }
    }
    public function whatsapp_send_test() {
        $this->load->library('whatsapp');
        $msg = "MedSign video-consult. All patient records at fingertips during remote consultation. Email - support@medsign.in https://www.medsign.in/";
        $whatsapp_data = [
            // 'is_promotional' => true,
            'doctor_id' => 147,
            'user_type' => 2,
            'mobile'=> 9723394348,
            'body'=> $msg
        ];
        echo "<pre>";
        // print_r($whatsapp_data);die;
        var_dump($msg);
        $this->whatsapp->send_test_message($whatsapp_data);
        die;
    }
    public function send_bulk_message() {
        $datetime = get_display_date_time("Y-m-d H:i");
        // $datetime = "2020-08-05 08:30";
        // echo $datetime."<br>";
        $templates = $this->Common_model->get_bulk_sms_template($datetime);
        // echo "<pre>";
        $user_count = 0;
        foreach ($templates as $template) {
            $result = $this->Common_model->get_all_rows_by_query($template->database_query);
            if($template->message_type == 2 && !empty($result)) {
                $this->load->library('whatsapp');
                $msg = $template->message_template;
                // $msg = lang($template->template_name);
                if(!empty($template->dynamic_data)) {
                    $dynamic_data_arr = json_decode($template->dynamic_data, true);
                    $columns = array_keys($result[0]);
                    foreach ($dynamic_data_arr as $key => $value) {
                        if(!is_array($value))
                            $msg = str_replace('{{'.$key.'}}', $value, $msg);
                    }
                }
                foreach ($result as $user_val) {
                    $message_body = $msg;
                    if(!empty($dynamic_data_arr['columns'])) {
                        foreach ($dynamic_data_arr['columns'] as $k => $value) {
                            $message_body = str_replace('{{'.$k.'}}', $user_val[$columns[$value]], $message_body);
                        }
                    }
                    if(!empty($user_val['user_phone_number']) && strlen($user_val['user_phone_number']) == 10 && !empty($user_val['user_id'])) {
                        $message_body = nl2br($message_body);
                        $message_body = str_replace("<br />", "\n", $message_body);
                        $whatsapp_data = [
                            'is_promotional' => true,
                            'doctor_id' => $user_val['user_id'],
                            'user_type' => 2,
                            'mobile'=> $user_val['user_phone_number'],
                            // 'mobile'=> "9714980101",
                            'body'=> $message_body
                        ];
                        // print_r($whatsapp_data);
                        $this->whatsapp->send_test_message($whatsapp_data);
                        // var_dump($message_body);die;
                        $user_count++;
                    }
                }
            }
            $update_data = array(
                "status" => 3,
                "updated_at" => date("Y-m-d H:i:s")
            );
            $this->Common_model->update('me_promotional_message_cron',$update_data,array("id" => $template->id));
        }
        if($user_count > 0) {
            $cron_file_run_log = LOG_FILE_PATH . '/bulk_message_cron_' . date('d-m-Y') . '.txt';
            file_put_contents($cron_file_run_log, "\n\n =================" .$datetime. "=================\n Bulk message cron run\n\n Total message send: " . $user_count . "\n\n", FILE_APPEND | LOCK_EX);
        }
        echo "<b>Total message sent: " . $user_count;
    }

    public function send_doctor_email() {
        $date = date("Y-m-d H:i:s");
        // $date = "2019-03-18 07:37:45";
        $doctors = $this->Common_model->get_doctor_email_send($date);
        // echo "<pre>";
        // print_r($doctors);die;
        if(!empty($doctors) && count($doctors) > 0) {
            $this->load->model('Emailsetting_model');
            $email_template_data = $this->Emailsetting_model->get_emailtemplate_by_id(47); //After 1 Hour email template
        }
        foreach ($doctors as $key => $value) {
            if(!empty($value->user_phone_number)) {
                $send_message['is_not_check_setting_flag'] = true;
                $send_message['is_stop_sms'] = false;
                $send_message['is_stop_whatsapp_sms'] = false;
                $send_message['is_promotional'] = true;
                $send_message['doctor_id'] = $value->user_id;
                $send_message['phone_number'] = $value->user_phone_number;
                $send_message['message'] = sprintf(lang('doctor_sms_after_one_hour'), DOCTOR . ' ' . ucwords(strtolower($value->doctor_name)), DOCTOR . ' ' . ucwords(strtolower($value->doctor_name)), MEDSIGN_WEB_CARE_URL);
                $send_message['user_type'] = 2;
                $send_message['whatsapp_sms_body'] = sprintf(lang('wa_template_22_after_one_hour'), DOCTOR . ' ' . ucwords(strtolower($value->doctor_name)), DOCTOR . ' ' . ucwords(strtolower($value->doctor_name)), MEDSIGN_WEB_CARE_URL);
                send_communication($send_message);
            }
            
            $parse_arr = array(
                '{DoctorName}' => DOCTOR . ' ' . ucwords(strtolower($value->doctor_name)),
                '{doctor_name}' => ucwords(strtolower($value->doctor_name)),
                '{Speciality}' => $value->doctor_detail_speciality,
                '{ClinicName}' => $value->clinic_name,
                '{userPhoneNumber}' => $value->user_phone_number,
                '{Address}' => $value->address_name,
                '{MobileAppLink}' => MEDSIGN_WEB_CARE_URL,
                '{WebUrl}' => DOMAIN_URL,
                '{AppName}' => APP_NAME,
                '{MailContactNumber}' => $email_template_data['email_static_data']['contact_number'],
                '{MailEmailAddress}' => $email_template_data['email_static_data']['email_id'],
                '{MailCompanyName}' => $email_template_data['email_static_data']['company_name'],
                '{CopyRightsYear}' => date('Y')
            );
            $message = replace_values_in_string($parse_arr, $email_template_data['email_template_message']);
            $subject = $email_template_data['email_template_subject'];
            $this->send_email($value->user_email, $subject, $message);
            echo ucwords(strtolower($value->doctor_name));
            echo "<br>";
        }
        echo "Done";
        exit;
    }

    /*Patient followup reminder*/
    public function patient_followup_reminder() {
        $today_date = get_display_date_time("Y-m-d");
        // $today_date = "2021-01-06";
        $date = date("Y-m-d", strtotime("+2 day", strtotime($today_date)));
        $result = $this->Common_model->get_patient_followup_reminder($date);
        // echo "<pre>";
        // print_r($result);die;
        $followup_ids = [];
        foreach ($result as $key => $value) {
            $send_message['phone_number'] = $value->user_phone_number;
            $send_message['message'] = sprintf(lang('patient_followup_sms'), $value->patient_name, DOCTOR . ' ' .$value->doctor_name,date("d/m/Y", strtotime($value->follow_up_followup_date)), DOMAIN_URL, PATIENT_EMAIL_FROM);
            $send_message['whatsapp_sms_body'] = sprintf(lang('wa_template_patient_followup_sms'), $value->patient_name, DOCTOR . ' ' .$value->doctor_name,date("d/m/Y", strtotime($value->follow_up_followup_date)), DOMAIN_URL, PATIENT_EMAIL_FROM);
            $send_message['is_sms_count'] = true;
            $send_message['is_check_sms_credit'] = true;
            $send_message['patient_id'] = $value->patient_id;
            $send_message['doctor_id'] = $value->doctor_id;
            $send_message['user_type'] = 2;
            $send_message['is_return_response'] = true;
            $send_message['no_global_log'] = true;
            $cron_file_run_log = LOG_FILE_PATH . '/patient_followup_reminder_sms_log_' . date('d-m-Y') . '.txt';
            //Send message to patient's self number
            if(!empty($value->user_phone_number) && !in_array($value->follow_up_id, $followup_ids)) {
                $response = send_communication($send_message);
                if(!empty($response['sms'])) {
                    file_put_contents($cron_file_run_log, "\n\n =================" .get_display_date_time("Y-m-d H:i:s"). "=================\n Patient followup reminder\n\n " . json_encode($response['sms']) . "\n\n", FILE_APPEND | LOCK_EX);
                }
            }
            //Send message to patient's care giver
            if(!empty($value->caretaker_phone_number)) {
                $send_message['phone_number'] = $value->caretaker_phone_number;
                $response = send_communication($send_message);
                if(!empty($response['sms'])) {
                    file_put_contents($cron_file_run_log, "\n\n =================" .get_display_date_time("Y-m-d H:i:s"). "=================\n Patient followup reminder\n\n " . json_encode($response['sms']) . "\n\n", FILE_APPEND | LOCK_EX);
                }
            }
            $followup_ids[] = $value->follow_up_id;
        }
        echo "Done.";
        exit;
    }
    /*teleconsultant send link to patient*/
    public function teleconsultant_send_link() {
        //if any update in global_setting_data and email template then must delete cache 'cron_cache' dir
        $today_date = get_display_date_time("Y-m-d H:i:s");
        // $today_date = "2020-09-23 14:05:02";
        $get_global_setting_data = $this->Common_model->get_global_setting_by_key_arr(['teleconsultant_link_send_minutes','minimum_minutes_video_call_invitation']);
        $get_global_setting_arr = array_column($get_global_setting_data, 'global_setting_value', 'global_setting_key');
        $minutes = trim($get_global_setting_arr['teleconsultant_link_send_minutes']);
        $datetime = date("Y-m-d H:i:s" ,strtotime('+'.$minutes.' minutes', strtotime($today_date)));
        $result = $this->Common_model->get_teleconsultant_appointments($datetime);
        // echo "<pre>";
        // print_r($result);die;
        if(!empty($result) && count($result) > 0) {
            $appointments_list = [];
            foreach ($result as $key => $value) {
                if(empty($appointments_list[$value->appointment_id])) {
                    $appointments_list[$value->appointment_id] = $value;
                    $appointments_list[$value->appointment_id]->caretaker_phone_number_arr = [];
                    $appointments_list[$value->appointment_id]->caretaker_email_arr = [];
                    if(!empty($value->caretaker_phone_number)) {
                        $appointments_list[$value->appointment_id]->caretaker_phone_number_arr[] = $value->caretaker_phone_number;
                    }
                    if(!empty($value->caretaker_email)) {
                        $appointments_list[$value->appointment_id]->caretaker_email_arr[] = $value->caretaker_email;
                    }
                } else {
                    if(!empty($value->caretaker_phone_number)) {
                        $appointments_list[$value->appointment_id]->caretaker_phone_number_arr[] = $value->caretaker_phone_number;
                    }
                    if(!empty($value->caretaker_email)) {
                        $appointments_list[$value->appointment_id]->caretaker_email_arr[] = $value->caretaker_email;
                    }
                }
            }
            $insert_data = [];
            $updated_patient_ids = [];
            $email_template_data = $this->Common_model->get_emailtemplate_by_id(51);
            $minimum_minutes = trim($get_global_setting_arr['minimum_minutes_video_call_invitation']);
            foreach ($appointments_list as $key => $value) {
                if(empty($value->setting_data) || ($minimum_minutes > ($value->setting_data/60))) {
                    continue;
                }
                if(!empty($value->patient_id) && !empty($value->doctor_id)) {
                    $updated_patient_ids[$value->appointment_doctor_user_id] = $value->appointment_user_id;
                } else {
                    $insert_data[] = [
                        'doctor_id' => $value->appointment_doctor_user_id,
                        'patient_id' => $value->appointment_user_id,
                        'created_at' => date('Y-m-d H:i:s')
                    ];
                }
                // $video_call_url = MEDSIGN_WEB_CARE_URL . 'telecall/' . encrypt_decrypt($value->appointment_id, 'encrypt');
                $link_id = base64_encode(json_encode(3));
                $video_call_url = create_shorturl(DOMAIN_URL . 'f/'.$link_id.'/' . encrypt_decrypt($value->appointment_id, 'encrypt'), true);
                $send_message = [];
                $send_message['message'] = sprintf(lang('patient_join_video_call_link_sms'), ucwords(strtolower($value->patient_name)), DOCTOR . ' ' .$value->doctor_name, date('d/m/Y', strtotime($value->appointment_date_time)) . " at " . date('h:i A', strtotime($value->appointment_date_time)), $minutes, "\n".$video_call_url."\n");
                $send_message['whatsapp_sms_body'] = sprintf(lang('wa_template_24_patient_join_video_call_link'), ucwords(strtolower($value->patient_name)), DOCTOR . ' ' .$value->doctor_name, date('d/m/Y', strtotime($value->appointment_date_time)) . " at " . date('h:i A', strtotime($value->appointment_date_time)), $minutes, $video_call_url);
                $send_message['is_return_response'] = true;
                $send_message['no_global_log'] = true;
                $send_message['user_type'] = 2;
                $send_message['patient_id'] = $value->appointment_user_id;
                $send_message['doctor_id'] = $value->appointment_doctor_user_id;
                $phone_number_arr = [];
                if(!empty($value->user_phone_number)) {
                    $phone_number_arr[] = $value->user_phone_number;
                }
                if(!empty($value->caretaker_phone_number_arr)) {
                    foreach ($value->caretaker_phone_number_arr as $caretaker_number) {
                        $phone_number_arr[] = $caretaker_number;
                    }
                }
                if(!empty($phone_number_arr)) {
                    foreach ($phone_number_arr as $pt_phone_number) {
                        $send_message['phone_number'] = $pt_phone_number;
                        $response = send_communication($send_message);
                        if(!empty($response['sms'])) {
                            $cron_file_run_log = LOG_FILE_PATH . '/cron_file_run_log_' . date('d-m-Y') . '.txt';
                            file_put_contents($cron_file_run_log, "\n\n =================" .get_display_date_time("Y-m-d H:i:s"). "=================\n Patient Send link cron file run\n\n " . json_encode($response['sms']) . "\n\n", FILE_APPEND | LOCK_EX);
                        }
                    }
                }
                $email_id_arr = [];
                if(!empty($value->user_email)) {
                    $email_id_arr[] = $value->user_email;
                }
                if(!empty($value->caretaker_email_arr)) {
                    $email_id_arr = array_merge($email_id_arr, $value->caretaker_email_arr);
                }
                if(!empty($email_id_arr)) {
                    $parse_arr = array(
                        '{PatientName}' => ucwords(strtolower($value->patient_name)),
                        '{AppointmentTime}' => date('d/m/Y h:i A', strtotime($value->appointment_date_time)),
                        '{DoctorName}' => DOCTOR . ' ' .$value->doctor_name,
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
                    $this->patient_send_email($email_id_arr, $subject, $message);
                }
            }
            if(!empty($updated_patient_ids)) {
                $update_data = [
                    'session_id' => NULL,
                    'token_id' => NULL,
                    'updated_at' => date('Y-m-d H:i:s')
                ];
                $this->Common_model->update_video_conf_token_data($update_data,$updated_patient_ids);
            }
            if(!empty($insert_data))
                $this->Common_model->insert_multiple('me_video_conf_token', $insert_data);
        }
        echo "Done";
        exit;
    }

    /*force call disconnect cron*/
    public function force_call_disconnect() {
        $result = $this->Common_model->get_force_disconnect_data();
        // echo "<pre>";
        // print_r($result);die;
        $opentok = new OpenTok($GLOBALS['ENV_VARS']['VIDEO_CONF_KEY'], $GLOBALS['ENV_VARS']['VIDEO_CONF_SECRET']);
        $current_date = date('Y-m-d H:i:s');
        $update_data = [];
        $setting_data = [];
        $send_signal_data = [];
        $force_disconnect_data = [];
        $delete_token_data = [];
        $send_notification_data = [];
        if(!empty($result) && count($result) > 0) {
            $get_global_setting_data = $this->Common_model->get_global_setting_by_key_arr(['force_video_call_disconnect_minutes']);
            $get_global_setting_arr = array_column($get_global_setting_data, 'global_setting_value', 'global_setting_key');
            $call_end_min_minutes = trim($get_global_setting_arr['force_video_call_disconnect_minutes'])*60;
        }
        foreach ($result as $key => $value) {
            if((strtotime($current_date) - strtotime($value->call_start_date_time)) >= $value->setting_data) {
                $call_duration_time = strtotime($current_date) - strtotime($value->call_start_date_time);
                $update_data[] = [
                    'appointment_id' => $value->appointment_id,
                    'call_end_date_time' => $current_date,
                    'call_duration_time' => $call_duration_time,
                    'actual_call_duration_time' => $call_duration_time,
                    'updated_at' => date('Y-m-d H:i:s')
                ];
                $setting_data[] = [
                    'setting_id' => $value->setting_id,
                    'setting_data' => '0',
                    'setting_updated_at' => date('Y-m-d H:i:s'),
                ];
                $delete_token_data[] = [
                    'doctor_id' => $value->doctor_id,
                    'patient_id' => $value->patient_id
                ];
                $send_signal_data[] = [
                    'session_id' => $value->session_id,
                    'message' => lang('no_video_call_credit_message')
                ];
                if(!empty($value->doctor_connection_id)){
                    $force_disconnect_data[] = ['session_id' => $value->session_id, 'connection_id' => $value->doctor_connection_id];
                }
                if(!empty($value->patient_connection_id)){
                    $force_disconnect_data[] = ['session_id' => $value->session_id, 'connection_id' => $value->patient_connection_id];
                }
                $send_notification_data[] = [
                    'doctor_id' => $value->doctor_id,
                    'patient_id' => $value->patient_id,
                    'appointment_id' => $value->appointment_id,
                    'message' => lang('no_video_call_credit_message')
                ];
                continue;
            }
            
            if(!empty($value->doctor_start_date_time) && empty($value->patient_start_date_time) && !empty($value->doctor_connection_id)) {
                if((strtotime($current_date) - strtotime($value->call_start_date_time)) >= $call_end_min_minutes) {
                    $update_data[] = [
                        'appointment_id' => $value->appointment_id,
                        'call_end_date_time' => $current_date,
                        'call_duration_time' => $call_end_min_minutes,
                        'actual_call_duration_time' => $call_end_min_minutes,
                        'updated_at' => date('Y-m-d H:i:s'),
                    ];
                    $setting_data[] = [
                        'setting_id' => $value->setting_id,
                        'setting_data' => $value->setting_data-$call_end_min_minutes,
                        'setting_updated_at' => date('Y-m-d H:i:s'),
                    ];
                    $delete_token_data[] = [
                        'doctor_id' => $value->doctor_id,
                        'patient_id' => $value->patient_id
                    ];
                    $send_signal_data[] = [
                        'session_id' => $value->session_id,
                        'message' => lang('doctor_call_disconnect_message')
                    ];
                    $force_disconnect_data[] = ['session_id' => $value->session_id, 'connection_id' => $value->doctor_connection_id];
                    $send_notification_data[] = [
                        'doctor_id' => $value->doctor_id,
                        'patient_id' => $value->patient_id,
                        'appointment_id' => $value->appointment_id,
                        'message' => lang('doctor_call_disconnect_message')
                    ];
                }
            } elseif(empty($value->doctor_start_date_time) && !empty($value->patient_start_date_time) && !empty($value->patient_connection_id)) {
                if((strtotime($current_date) - strtotime($value->call_start_date_time)) >= $call_end_min_minutes) {
                    $update_data[] = [
                        'appointment_id' => $value->appointment_id,
                        'call_end_date_time' => $current_date,
                        'call_duration_time' => $call_end_min_minutes,
                        'actual_call_duration_time' => $call_end_min_minutes,
                        'updated_at' => date('Y-m-d H:i:s'),
                    ];
                    $setting_data[] = [
                        'setting_id' => $value->setting_id,
                        'setting_data' => $value->setting_data-$call_end_min_minutes,
                        'setting_updated_at' => date('Y-m-d H:i:s'),
                    ];
                    $delete_token_data[] = [
                        'doctor_id' => $value->doctor_id,
                        'patient_id' => $value->patient_id
                    ];
                    $force_disconnect_data[] = ['session_id' => $value->session_id, 'connection_id' => $value->patient_connection_id];
                    $send_notification_data[] = [
                        'doctor_id' => $value->doctor_id,
                        'patient_id' => $value->patient_id,
                        'appointment_id' => $value->appointment_id,
                        'message' => lang('patient_call_disconnect_message')
                    ];
                }
            }
        }
        if(count($update_data) > 0) {
            $this->Common_model->update_teleconsultant_call_history($update_data);
            $cron_file_run_log = LOG_FILE_PATH . '/force_call_disconnect_log_' . date('d-m-Y') . '.txt';
            file_put_contents($cron_file_run_log, "\n\n =================" .get_display_date_time("Y-m-d H:i:s"). "=================\n force call disconnect cron file run\n\n " . json_encode($update_data) . "\n\n", FILE_APPEND | LOCK_EX);
        }
        if(count($setting_data) > 0) {
            $this->Common_model->update_setting_data($setting_data);
        }
        if(count($delete_token_data) > 0) {
            $this->Common_model->delete_token_data($delete_token_data);
        }
        try {
            if(count($send_signal_data) > 0) {
                foreach ($send_signal_data as $key => $value) {
                    $signalPayload = array(
                        'data' => $value['message']
                    );
                    $opentok->signal($value['session_id'], $signalPayload);
                }
            }
            if(count($force_disconnect_data) > 0) {
                foreach ($force_disconnect_data as $key => $value) {
                    $opentok->forceDisconnect($value['session_id'],$value['connection_id']);
                }
            }
        } catch (Exception $ex) {
            
        }
        if(count($send_notification_data) > 0) {
            $notification_insert = [];
            foreach ($send_notification_data as $key => $value) {
                $notification_insert[] = [
                    'notification_list_user_id' => $value['doctor_id'],
                    'notification_list_user_type' => 2,
                    'notification_list_device_type' => 'web',
                    'notification_list_type' => 5,
                    'notification_list_message' => $value['message'],
                    'notification_list_created_at' => date('Y-m-d H:i:s'),
                    'notification_list_status' => 1
                ];
            }
            $this->Common_model->insert_multiple('me_notification_list', $notification_insert);
        }
        if(count($send_notification_data) > 0) {
            foreach ($send_notification_data as $key => $value) {
                $this->sendFirebaseNotification($value);
            }
        }
        die('Done');
    }

    //Send Appointment, Lab Test and General reminder notification
    public function send_appointment_notification() {
        $datetime = get_display_date_time("Y-m-d H:i:s");
        $result = $this->Common_model->get_push_reminders($datetime);
        if(!empty($result) && count($result) > 0) {
            $this->load->model('Emailsetting_model');
            $appointment_email_template_data = $this->Emailsetting_model->get_emailtemplate_by_id(64);
            $lab_test_email_template_data = $this->Emailsetting_model->get_emailtemplate_by_id(66);
            $general_email_template_data = $this->Emailsetting_model->get_emailtemplate_by_id(67);
            $user_ids = array_unique(array_column($result, 'user_id'));
            $caregiver = $this->Common_model->get_caregiver_members($user_ids);
            $caregiverUsers = [];
            foreach ($caregiver as $key => $value) {
                $caregiverUsers[$value->patient_id][] = $value;
            }
        }
        foreach ($result as $key => $value) {
            if(!is_patient_reminder_access($value,$caregiverUsers)) {
                continue;
            }
            $reminderidArr = [$value->reminder_id];
            $request_data = [];
            $link = $GLOBALS['ENV_VARS']['MEDSIGN_WEB_CARE_URL'] . "rmvw/".base64_encode(json_encode($reminderidArr));
            $shortLink = create_shorturl($link,true);
            $emailArr = [];
            if(!empty($value->user_email) && (!empty($value->setting_value) || (!empty($value->user_plan_expiry_date) && $value->user_plan_expiry_date >= get_display_date_time("Y-m-d"))))
                $emailArr[] = $value->user_email;
            if(!empty($caregiverUsers[$value->user_id])) {
                foreach ($caregiverUsers[$value->user_id] as $parent) {
                    if(!empty($parent->user_email)) {
                        if(!empty($parent->setting_value) ||  (!empty($parent->user_plan_expiry_date) && $parent->user_plan_expiry_date >= get_display_date_time("Y-m-d")))
                            $emailArr[] = $parent->user_email;
                    }
                }
            }
            $sms_content = "";
            $whatsapp_sms_body = "";
            if($value->reminder_type == 2) {
                $appointment_date_time = date("d/m/Y", strtotime($value->appointment_date)) . " at " . date("h:i a", strtotime($value->appointment_from_time));
                $request_data = [
                    'title' => 'Appointment Reminder',
                    'body' => "Hello " . $value->patient_name . ", This is reminder for your appointment booked with " . $value->reminder_doctor_name . " for " .$appointment_date_time. " Wishing Good Health! (A MedSign Care initiative)",
                    'click_action' => $link,
                ];
                $sms_content = sprintf(lang('sms_appointment_reminder_to_patient'), $value->patient_name, $value->reminder_doctor_name, $appointment_date_time . ' ' . $shortLink . ' ');
                $whatsapp_sms_body = sprintf(lang('appointment_reminder_to_patient_58'), $value->patient_name, $value->reminder_doctor_name, $appointment_date_time . ' ' . $shortLink . ' ');
                if(!empty($value->udt_device_token) && $value->udt_device_type == "web") {
                    $request_data['device_token'] = $value->udt_device_token;
                    if(!empty($value->setting_value) || (!empty($value->user_plan_expiry_date) && $value->user_plan_expiry_date >= get_display_date_time("Y-m-d")))
                        send_firebase_notification($request_data);
                }
                if(!empty($emailArr)) {
                    $parse_arr = array(
                        '{userName}' => $value->patient_name,
                        '{drName}' => $value->reminder_doctor_name,
                        '{dateTime}' => $appointment_date_time,
                        '{WebUrl}' => DOMAIN_URL,
                        '{AppName}' => APP_NAME,
                        '{MailContactNumber}' => $appointment_email_template_data['email_static_data']['contact_number'],
                        '{MailEmailAddress}' => $appointment_email_template_data['email_static_data']['email_id'],
                        '{MailCompanyName}' => $appointment_email_template_data['email_static_data']['company_name'],
                        '{CopyRightsYear}' => date('Y')
                    );
                    $message = replace_values_in_string($parse_arr, $appointment_email_template_data['email_template_message']);
                    $subject = $appointment_email_template_data['email_template_subject'];
                    $this->patient_send_email($emailArr, $subject, $message);
                }
            } elseif($value->reminder_type == 3) {
                $request_data = [
                    'title' => 'Investigation Reminder',
                    'body' => "Hello " . $value->patient_name . ", This is reminder for Investigations to be done as advised by your Doctor. ". $value->reminder_lab_report_name . ", Take Care! (A MedSign Care initiative)",
                    'click_action' => $link,
                ];
                if(!empty($value->udt_device_token) && $value->udt_device_type == "web") {
                    $request_data['device_token'] = $value->udt_device_token;
                    if(!empty($value->setting_value) || (!empty($value->user_plan_expiry_date) && $value->user_plan_expiry_date >= get_display_date_time("Y-m-d")))
                        send_firebase_notification($request_data);
                }
                if(strlen($value->reminder_lab_report_name) > 30) {
                    $lab_report_name = substr($value->reminder_lab_report_name, 0, 25) . "...";
                } else {
                    $lab_report_name = $value->reminder_lab_report_name;
                }
                $sms_content = sprintf(lang('sms_investigation_reminder_to_patient'), $value->patient_name, $lab_report_name . ' ' . $shortLink);
                $whatsapp_sms_body = sprintf(lang('investigation_reminder_to_patient_60'), $value->patient_name, $lab_report_name . ' ' . $shortLink);
                if(!empty($emailArr)) {
                    $parse_arr = array(
                        '{userName}' => $value->patient_name,
                        '{InvestigationsNames}' => $value->reminder_lab_report_name,
                        '{WebUrl}' => DOMAIN_URL,
                        '{AppName}' => APP_NAME,
                        '{MailContactNumber}' => $lab_test_email_template_data['email_static_data']['contact_number'],
                        '{MailEmailAddress}' => $lab_test_email_template_data['email_static_data']['email_id'],
                        '{MailCompanyName}' => $lab_test_email_template_data['email_static_data']['company_name'],
                        '{CopyRightsYear}' => date('Y')
                    );
                    $message = replace_values_in_string($parse_arr, $lab_test_email_template_data['email_template_message']);
                    $subject = $lab_test_email_template_data['email_template_subject'];
                    $this->patient_send_email($emailArr, $subject, $message);
                }
            } elseif($value->reminder_type == 4) {
                $request_data = [
                    'title' => $value->reminder_general_title,
                    'body' => $value->reminder_note,
                    'click_action' => $link,
                ];
                if(!empty($value->udt_device_token) && $value->udt_device_type == "web") {
                    $request_data['device_token'] = $value->udt_device_token;
                    if(!empty($value->setting_value) || (!empty($value->user_plan_expiry_date) && $value->user_plan_expiry_date >= get_display_date_time("Y-m-d")))
                        send_firebase_notification($request_data);
                }
                if(!empty($emailArr)) {
                    $parse_arr = array(
                        '{userName}' => $value->patient_name,
                        '{Title}' => $value->reminder_general_title,
                        '{Notes}' => $value->reminder_note,
                        '{WebUrl}' => DOMAIN_URL,
                        '{AppName}' => APP_NAME,
                        '{MailContactNumber}' => $general_email_template_data['email_static_data']['contact_number'],
                        '{MailEmailAddress}' => $general_email_template_data['email_static_data']['email_id'],
                        '{MailCompanyName}' => $general_email_template_data['email_static_data']['company_name'],
                        '{CopyRightsYear}' => date('Y')
                    );
                    $message = replace_values_in_string($parse_arr, $general_email_template_data['email_template_message']);
                    $subject = $general_email_template_data['email_template_subject'];
                    // $this->patient_send_email($emailArr, $subject, $message);
                }
            }
            $send_message = [];
            $send_message['message'] = $sms_content;
            $send_message['whatsapp_sms_body'] = $whatsapp_sms_body;
            $send_message['is_not_check_setting_flag'] = true;
            $send_message['no_global_log'] = true;
            $send_message['is_return_response'] = true;
            $send_message['is_stop_sms'] = IS_STOP_SMS;
            $send_message['is_stop_whatsapp_sms'] = IS_STOP_WHATSAPP_SMS;
            $send_message['patient_id'] = $value->user_id;
            $send_message['user_type'] = 1;
            $reminders_log = LOG_FILE_PATH . '/reminders_sms_log_' . date('d-m-Y') . '.txt';
            if(!empty($value->setting_value) || (!empty($value->user_plan_expiry_date) && $value->user_plan_expiry_date >= get_display_date_time("Y-m-d"))) {
                if(!empty($value->user_phone_number) && in_array($value->reminder_type, [2,3])) {
                    $send_message['phone_number'] = $value->user_phone_number;
                    $response = send_communication($send_message);
                    if(!empty($response['sms'])) {
                        file_put_contents($reminders_log, "\n\n =================" .get_display_date_time("Y-m-d H:i:s"). "=================\n Patient reminders log\n\n " . json_encode($response['sms']) . "\n\n", FILE_APPEND | LOCK_EX);
                    }
                }
            }
            if(!empty($request_data) && !empty($caregiverUsers[$value->user_id])) {
                foreach ($caregiverUsers[$value->user_id] as $parent) {
                    if(!empty($parent->setting_value) || (!empty($parent->user_plan_expiry_date) && $parent->user_plan_expiry_date >= get_display_date_time("Y-m-d"))) {
                        if(!empty($parent->udt_device_token) && $parent->udt_device_type == "web") {
                            $request_data['device_token'] = $parent->udt_device_token;
                            send_firebase_notification($request_data);
                        }
                    
                        if(!empty($parent->user_phone_number) && in_array($value->reminder_type, [2,3])) {
                            $send_message['phone_number'] = $parent->user_phone_number;
                            $response = send_communication($send_message);
                            if(!empty($response['sms'])) {
                                file_put_contents($reminders_log, "\n\n =================" .get_display_date_time("Y-m-d H:i:s"). "=================\n Patient reminders log\n\n " . json_encode($response['sms']) . "\n\n", FILE_APPEND | LOCK_EX);
                            }
                        }
                    }
                }
            }
            $log = LOG_FILE_PATH . "push_reminder_" . date('d-m-Y') . ".txt";
                file_put_contents($log, "\n Reminder ID: " . $value->reminder_id . " => " . $link . "\n", FILE_APPEND);
        }
        echo "Done";
        exit;
    }

    public function medicine_reminder() {
        $today_date = get_display_date_time("Y-m-d");
        $time = get_display_date_time("H:i");
        $frequencyName = get_frequency($time);
        $result = $this->Common_model->get_medicine_reminders($today_date,$time);
        $reminder_data = array();
        foreach ($result as $key => $value) {
            $time_arr = explode(",", $value['reminder_timing']);
            if(in_array($time, $time_arr) && in_array($value['reminder_day'], [1,2,3,4])) {
                if(empty($reminder_data[$value['reminder_user_id']])){
                    $reminder_data[$value['reminder_user_id']] = array(
                        'user_id' => $value['reminder_user_id'], 
                        'doctor_id' => $value['prescription_doctor_user_id'], 
                        'user_phone_number' => $value['user_phone_number'],
                        'user_email' => $value['user_email'],
                        'patient_name' => $value['patient_name'],
                        'user_plan_expiry_date' => $value['user_plan_expiry_date'],
                        'setting_value' => $value['setting_value'],
                        'udt_device_token' => $value['udt_device_token'],
                        'udt_device_type' => $value['udt_device_type'],
                    );
                }
                $reminder_data[$value['reminder_user_id']]['drugs'][] = $value['reminder_drug_name'];
                $reminder_data[$value['reminder_user_id']]['reminder_ids'][] = $value['reminder_id'];
            }
            if(in_array($value['reminder_day'], [5]) && in_array($time, $time_arr)) {
                $startDate = $value['reminder_start_date'];
                $durationAdd = 0;
                while ($today_date >= $startDate) {
                    $week_day = date("w", strtotime($startDate))+1;
                    if(in_array($week_day, explode(",", $value['reminder_week_day']))) {
                        $durationAdd++;
                        if($today_date == $startDate) {
                            if(empty($reminder_data[$value['reminder_user_id']])){
                                $reminder_data[$value['reminder_user_id']] = array(
                                    'user_id' => $value['reminder_user_id'], 
                                    'doctor_id' => $value['prescription_doctor_user_id'], 
                                    'user_phone_number' => $value['user_phone_number'],
                                    'user_email' => $value['user_email'],
                                    'patient_name' => $value['patient_name'],
                                    'user_plan_expiry_date' => $value['user_plan_expiry_date'],
                                    'setting_value' => $value['setting_value'],
                                    'udt_device_token' => $value['udt_device_token'],
                                    'udt_device_type' => $value['udt_device_type'],
                                );
                            }
                            $reminder_data[$value['reminder_user_id']]['drugs'][] = $value['reminder_drug_name'];
                            $reminder_data[$value['reminder_user_id']]['reminder_ids'][] = $value['reminder_id'];
                        }
                    }
                    $startDate = date("Y-m-d", strtotime($startDate . ' +1 day'));
                    if($durationAdd==$value['reminder_duration'])
                        break;
                }
            }
        }
        if(!empty($reminder_data) && count($reminder_data) > 0) {
            $this->load->model('Emailsetting_model');
            $medicine_email_template_data = $this->Emailsetting_model->get_emailtemplate_by_id(65);
            $user_ids = array_unique(array_column($reminder_data, 'user_id'));
            $caregiver = $this->Common_model->get_caregiver_members($user_ids);
            $caregiverUsers = [];
            foreach ($caregiver as $key => $value) {
                $caregiverUsers[$value->patient_id][] = $value;
            }
        }
        foreach ($reminder_data as $key => $value) {
            $userObj = (object) $value;
            if(!is_patient_reminder_access($userObj,$caregiverUsers)) {
                continue;
            }
            $drugsName = "";
            foreach ($value['drugs'] as $no => $drug) {
                if(!empty($drugsName))
                    $drugsName .= ",";
                $drugsName .= $drug;
            }
            $emailArr = [];
            if(!empty($value['user_email']) && (!empty($value['setting_value']) ||  (!empty($value['user_plan_expiry_date']) && $value['user_plan_expiry_date'] >= get_display_date_time("Y-m-d"))))
                $emailArr[] = $value['user_email'];
            if(!empty($caregiverUsers[$value['user_id']])) {
                foreach ($caregiverUsers[$value['user_id']] as $parent) {
                    if(!empty($parent->user_email) && (!empty($parent->setting_value) ||  (!empty($parent->user_plan_expiry_date) && $parent->user_plan_expiry_date >= get_display_date_time("Y-m-d")))) {
                        $emailArr[] = $parent->user_email;
                    }
                }
            }
            $requestData = ['reminder_ids' => $value['reminder_ids'], 'time' => $time];
            $link = $GLOBALS['ENV_VARS']['MEDSIGN_WEB_CARE_URL'] . "rmvw/".base64_encode(json_encode($requestData));
            $request_data = [
                'title' => "Medicine Reminder",
                'body' => "Hello " . $value['patient_name'] . ", This is reminder for your medicine administration of " . $drugsName . " in the " . $frequencyName . " as advised by your Doctor. Wishing Good Health! (A MedSign Care initiative)",
                'click_action' => $link,
            ];
            if(strlen($drugsName) > 60) {
                $drugsNameShort = substr($drugsName, 0, 55) . "...";
            } else {
                $drugsNameShort = $drugsName;
            }
            $shortLink = create_shorturl($link,true);
            $send_message = [];
            $send_message['message'] = sprintf(lang('sms_medicine_reminder_to_patient'), $value['patient_name'], $drugsNameShort,$frequencyName,$shortLink);
            $send_message['whatsapp_sms_body'] = sprintf(lang('medicine_reminder_to_patient_59'), $value['patient_name'], $drugsNameShort, $frequencyName, $shortLink);
            $send_message['is_not_check_setting_flag'] = true;
            $send_message['no_global_log'] = true;
            $send_message['is_return_response'] = true;
            $send_message['is_stop_sms'] = IS_STOP_SMS;
            $send_message['is_stop_whatsapp_sms'] = IS_STOP_WHATSAPP_SMS;
            $send_message['patient_id'] = $value['user_id'];
            $send_message['user_type'] = 1;
            $reminders_log = LOG_FILE_PATH . '/reminders_sms_log_' . date('d-m-Y') . '.txt';
            if(!empty($value['user_phone_number']) && (!empty($value['setting_value']) ||  (!empty($value['user_plan_expiry_date']) && $value['user_plan_expiry_date'] >= get_display_date_time("Y-m-d")))) {
                $send_message['phone_number'] = $value['user_phone_number'];
                $response = send_communication($send_message);
                if(!empty($response['sms'])) {
                    file_put_contents($reminders_log, "\n\n =================" .get_display_date_time("Y-m-d H:i:s"). "=================\n Patient reminders log\n\n " . json_encode($response['sms']) . "\n\n", FILE_APPEND | LOCK_EX);
                }
            }
            if(!empty($caregiverUsers[$value['user_id']])) {
                foreach ($caregiverUsers[$value['user_id']] as $parent) {
                    if(!empty($parent->user_phone_number) && (!empty($parent->setting_value) ||  (!empty($parent->user_plan_expiry_date) && $parent->user_plan_expiry_date >= get_display_date_time("Y-m-d")))) {
                        $send_message['phone_number'] = $parent->user_phone_number;
                        $response = send_communication($send_message);
                        file_put_contents($reminders_log, "\n\n =================" .get_display_date_time("Y-m-d H:i:s"). "=================\n Patient reminders log\n\n " . json_encode($response['sms']) . "\n\n", FILE_APPEND | LOCK_EX);
                    }
                }
            }
            $log = LOG_FILE_PATH . "medicine_push_reminder_" . date('d-m-Y') . ".txt";
            if(!empty($value['udt_device_token']) && $value['udt_device_type'] == "web" && (!empty($value['setting_value']) ||  (!empty($value['user_plan_expiry_date']) && $value['user_plan_expiry_date'] >= get_display_date_time("Y-m-d")))) {
                $request_data['device_token'] = $value['udt_device_token'];
                send_firebase_notification($request_data);
                file_put_contents($log, "\n " . $value['user_id'] . " => " . json_encode($request_data) . "\n", FILE_APPEND);
            }
            if(!empty($caregiverUsers[$value['user_id']])) {
                foreach ($caregiverUsers[$value['user_id']] as $parent) {
                    if(!empty($parent->udt_device_token) && $parent->udt_device_type == "web" && (!empty($parent->setting_value) ||  (!empty($parent->user_plan_expiry_date) && $parent->user_plan_expiry_date >= get_display_date_time("Y-m-d")))) {
                        $request_data['device_token'] = $parent->udt_device_token;
                        send_firebase_notification($request_data);
                        file_put_contents($log, "\n Parent ID: " . $parent->parent_patient_id . " => " . json_encode($request_data) . "\n", FILE_APPEND);
                    }
                }
            }
            if(!empty($emailArr)) {
                $parse_arr = array(
                    '{userName}' => $value['patient_name'],
                    '{drugName}' => $drugsName,
                    '{Time}' => $frequencyName,
                    '{WebUrl}' => DOMAIN_URL,
                    '{AppName}' => APP_NAME,
                    '{MailContactNumber}' => $medicine_email_template_data['email_static_data']['contact_number'],
                    '{MailEmailAddress}' => $medicine_email_template_data['email_static_data']['email_id'],
                    '{MailCompanyName}' => $medicine_email_template_data['email_static_data']['company_name'],
                    '{CopyRightsYear}' => date('Y')
                );
                $message = replace_values_in_string($parse_arr, $medicine_email_template_data['email_template_message']);
                $subject = $medicine_email_template_data['email_template_subject'];
                $this->patient_send_email($emailArr, $subject, $message);
            }
        }
        echo "Done";
        exit;
    }
    public function sendFirebaseNotification($data) {
        $doctor_id = $data['doctor_id'];
        $patient_id = $data['patient_id'];
        $appointment_id = $data['appointment_id'];
        $notification_message = $data['message'];
        $user_device_token_data = $this->Common_model->get_single_row('me_user_device_tokens', 'udt_device_token', ['udt_u_id' => $doctor_id]);
        if(!empty($user_device_token_data['udt_device_token'])) {
            $device_token = $user_device_token_data['udt_device_token'];
            $request_data = [
                'device_token' => $device_token,
                'title' => 'MedSign Alert',
                'body' => $notification_message,
                'click_action' => DOMAIN_URL,
            ];
            $response = send_firebase_notification($request_data);
            $cron_file_run_log = LOG_FILE_PATH . '/force_call_disconnect_push_log_' . date('d-m-Y') . '.txt';
            file_put_contents($cron_file_run_log, "\n=================" .get_display_date_time("Y-m-d H:i:s"). "=================\n force call disconnect push cron file run. Appointment ID: ".$appointment_id." \n" . json_encode($response) . "\n", FILE_APPEND | LOCK_EX);
        }
        echo 'send';
        exit;
    }
    public function send_pushwoosh_notification($data) {
        $doctor_id = $data['doctor_id'];
        $patient_id = $data['patient_id'];
        $appointment_id = $data['appointment_id'];
        $notification_message = !empty($this->post_data['message']) ? $this->post_data['message'] : "";
        $user_device_token_data = $this->Common_model->get_single_row('me_user_device_tokens', 'udt_device_token', ['udt_u_id' => $doctor_id]);
        if(!empty($user_device_token_data['udt_device_token'])) {
            $device_token = $user_device_token_data['udt_device_token'];
            $message = array();
            $message['send_date'] = "now";
            // $message['link'] = $GLOBALS['ENV_VARS']['APP_NOTIFICATION_URL'] . '/staging';
            $message['content'] = $data['message'];
            $message["platforms"] = [10,11,12,13]; // optional. 1 — iOS; 2 — BB; 3 — Android; 5 — Windows Phone; 7 — OS X; 8 — Windows 8; 9 — Amazon; 10 — Safari; 11 — Chrome; 12 — Firefox; 13 - IE11; ignored if "devices" < 10
            $message["chrome_title"] = "MedSign Alert"; // optional. You can specify the header of the message in this parameter.
            $message["firefox_title"] = "MedSign Alert"; // optional. You can specify the header of the message in this parameter.
            $message["chrome_icon"] = $GLOBALS['ENV_VARS']['DOMAIN_URL'] . $GLOBALS['ENV_VARS']['APP_DIR'] .'logo.png'; // full path URL to the icon or extension resources file path
            $message["firefox_icon"] = $GLOBALS['ENV_VARS']['DOMAIN_URL'] . $GLOBALS['ENV_VARS']['APP_DIR'] . 'logo.png'; // full path URL to the icon or extension resources file path
            $message["chrome_gcm_ttl"] = 3600; // optional. Time to live parameter – maximum message lifespan in seconds.
            $message["chrome_duration"] = 0; // optional. Changes chrome push display time. Set to 0 to display push until user interacts with it.
            $message["chrome_image"] = $GLOBALS['ENV_VARS']['DOMAIN_URL'] . $GLOBALS['ENV_VARS']['APP_DIR'] . 'logo.png'; // optional. URL to large image. 
            $message['data']['doctor_id'] = $doctor_id;
            $message['data']['patient_id'] = $patient_id;
            $message['data']['appointment_id'] = $appointment_id;
            $message['devices'] = array($device_token);
            send_pushwoosh_notification($message, true);
        }
        echo 'send';
        exit;
    }

    public function compliance_reminder() {
        $today_date = "2020-01-30";
        // $today_date = get_display_date_time("Y-m-d");
        $result = $this->Common_model->get_reminders($today_date);
        // echo "<pre>";
        $reminder_data = array();
        $time = "08:00";
        // $time = get_display_date_time("H:i");
        $reminder_record_date = date('Y-m-d H:i:s',strtotime('-5 hour -30 minutes',strtotime($today_date. " " . $time . ":10")));
        foreach ($result as $key => $value) {
            $time_arr = explode(",", $value['reminder_timing']);
            if(in_array($time, $time_arr) && $value['reminder_day'] == 1) {
                if(empty($reminder_data[$value['reminder_user_id']]))
                    $reminder_data[$value['reminder_user_id']] = array('user_id' => $value['reminder_user_id'], 'doctor_id' => $value['prescription_doctor_user_id'], 'user_phone_number' => $value['user_phone_number']);
                $reminder_data[$value['reminder_user_id']]['drugs'][] = $value['drug_name_with_unit'];
                $reminder_data[$value['reminder_user_id']]['reminder_ids'][$value['reminder_id']] = $time."#".$reminder_record_date;
            }
            if(in_array($value['reminder_day'], [2,3]) && in_array($time, $time_arr)) {
                $is_reminded = $this->is_reminded($today_date, $value['reminder_day'], $value['reminder_duration'], $value['reminder_start_date']);
                if(date("Y-m-d", strtotime($today_date . " -1 days")) == $value['reminder_start_date'] || $is_reminded) {
                    if(empty($reminder_data[$value['reminder_user_id']]))
                        $reminder_data[$value['reminder_user_id']] = array('user_id' => $value['reminder_user_id'], 'doctor_id' => $value['prescription_doctor_user_id'], 'user_phone_number' => $value['user_phone_number']);
                    $reminder_data[$value['reminder_user_id']]['drugs'][] = $value['drug_name_with_unit'];
                    $reminder_data[$value['reminder_user_id']]['reminder_ids'][$value['reminder_id']] = $time."#".$reminder_record_date;
                }
            }
        }
        // print_r($reminder_data);
        $this->load->library('whatsapp');
        foreach ($reminder_data as $key => $value) {
            $message = "*Have you taken your medicine?*\n";
            foreach ($value['drugs'] as $no => $drug) {
                $message .= "*".($no+1) . ".* ". $drug . "\n";
            }
            $link = $GLOBALS['ENV_VARS']['DOMAIN_URL'] . "rm/".base64_encode(json_encode($value['reminder_ids']));
            if($value['user_phone_number'] == 9723394348) {
                // $message .= $link;
                // echo $message;
            }
            // $this->whatsapp->send_message(['patient_id' => $value['user_id'], 'doctor_id' => $value['doctor_id'], 'user_type' => 1, 'mobile'=>$value['user_phone_number'], 'body'=> $message]);
        }
        die;
    }

    public function add_reminder_record($request) {
        $reminder_record_data_list = json_decode(base64_decode($request), true);
        if(!empty($reminder_record_data_list)){
            $reminder_id_arr = array_keys($reminder_record_data_list);
            $columns = 'reminder_record_reminder_id,reminder_record_date,reminder_time';
            $where = array('reminder_record_reminder_id' => $reminder_id_arr);
            $get_reminder_records = $this->Common_model->get_reminder_records($where, $columns);
            $reminder_records_arr = array();
            foreach ($get_reminder_records as $key => $value) {
                $k = $value['reminder_record_reminder_id'].'_'.date('Ymd',strtotime($value['reminder_record_date'])).'_'.$value['reminder_time'];
                $reminder_records_arr[] = $k;
            }
            $insert_array = array();
            foreach($reminder_record_data_list as $reminder_id => $value){
                $status = 1;
                $arr = explode("#", $value);
                $reminder_time = $arr[0];
                $date = $arr[1];
                
                $k = $reminder_id.'_'.date('Ymd',strtotime($date)).'_'.$reminder_time;
                $reminder_record_status = 1;
                if(in_array($k, $reminder_records_arr)) {
                    continue;
                }
                $insert_array[] = array(
                    "reminder_record_reminder_id"   => $reminder_id,
                    "reminder_record_taken_status"  => $status,
                    "reminder_record_date"          => $date,
                    "reminder_time"                 => $reminder_time,
                    "reminder_record_created_at"    => date("Y-m-d H:i:s"),
                    "reminder_record_status"        => $reminder_record_status
                );
            }
            if (!empty($insert_array)) {
                $this->Common_model->insert_multiple('me_reminder_records', $insert_array);
            }
        }
        $data['breadcrumbs'] = 'Compliance Capture';
        $data['page_title'] = 'Compliance Capture';
        $this->load->view("web/add_reminder_records", $data);
    }

    static function is_reminded($date, $reminder_day, $duration, $start_date) {
        for($i=1; $i <= $duration; $i++) {
            if($reminder_day == 2) {
                $remind_date = date("Y-m-d", strtotime($start_date . "+ " . $i ." week"));
                if($remind_date == $date) {
                    return true;
                    break;
                }
            } elseif($reminder_day == 3) {
                $remind_date = date("Y-m-d", strtotime($start_date . "+ " . $i ." month"));
                if($remind_date == $date) {
                    return true;
                    break;
                }
            }
        }
        return false;
    }

    public function whatsapp() {
        $this->load->library('whatsapp');
        $whatsapp_data = [
            'patient_id'=>204,
            'doctor_id'=>166,
            'user_type'=>2,
            'mobile'=>9723394348,
            'body'=>"Hello, \nHow are you?"
        ];
        $this->whatsapp->send_message($whatsapp_data);
        die;
    }
    public function whatsapp_comein_callback() {
        $this->load->library('whatsapp');
        $this->whatsapp->add_whatsapp_comein_log($_POST);
        file_put_contents($this->whatsapp_log_file, "\n\n =================" .get_display_date_time("Y-m-d H:i:s"). "=================\n" . json_encode($_POST) . "\n\n", FILE_APPEND | LOCK_EX);
    }
    public function whatsapp_fallback_callback() {
        file_put_contents($this->whatsapp_log_file, "\n\n =================" .get_display_date_time("Y-m-d H:i:s"). "=================\n" . json_encode($_POST) . "\n\n", FILE_APPEND | LOCK_EX);
    }
    public function whatsapp_status_callback() {
        $this->load->library('whatsapp');
        $this->whatsapp->update_log($_POST);
        file_put_contents($this->whatsapp_log_file, "\n\n =================" .get_display_date_time("Y-m-d H:i:s"). "=================\n" . json_encode($_POST) . "\n\n", FILE_APPEND | LOCK_EX);
    }
    public function inactive_previous_reminder() {
		exit;
        $reminder_sql = "UPDATE " . TBL_REMINDERS . " 
                         SET 
						   reminder_modified_at = '" . date(DATE_FORMAT, time()) . "', 
						   reminder_status = 9 
                         WHERE 
							IF(
							   reminder_duration > 0,
							   DATE_ADD(reminder_start_date, INTERVAL reminder_duration DAY) < CURDATE(),
							   reminder_start_date < CURDATE()
							) ";
        $this->Common_model->query($reminder_sql);
		
        $get_updated_sql = " SELECT *,
                                    CURDATE() as today_date,
                                    DATE_ADD(reminder_start_date, INTERVAL reminder_duration DAY)
                             FROM 
                                    " . TBL_REMINDERS . " 
                             WHERE 
                                IF
                                (
                                    reminder_duration > 0,
                                    DATE_ADD(reminder_start_date, INTERVAL reminder_duration DAY) < CURDATE(),
                                    reminder_start_date < CURDATE()
                                )  ";
        
        $get_data = $this->Common_model->get_all_rows_by_query($get_updated_sql);

        $log = LOG_FILE_PATH . "inactive_reminder_" . date('d-m-Y') . ".txt";
        file_put_contents($log, "\n  ================ START =====================    \n\n", FILE_APPEND | LOCK_EX);
        file_put_contents($log, json_encode($get_data), FILE_APPEND | LOCK_EX);
        file_put_contents($log, "\n  ================ END =====================    \n\n", FILE_APPEND | LOCK_EX);
    }

    public function reset_block_opt_users_account() {
		$update_auth_data = array(
            "auth_attempt_count" => 0,
            "auth_resend_count" => 0
        );
        $this->Common_model->update(TBL_USER_AUTH,$update_auth_data,array("auth_type" => 2));
    }

    public function get_api_logs_table_bkp(){
        exit;
		$tableName 	   = TBL_API_LOGS;
		//$folder_path = UPLOAD_REL_PATH . "/" . API_LOG_BKP . "/". 'mysql.sql';
        $folder_path   ='/var/www/html/uploads/apilog/'.date("Y-m-d").'.sql';
        $query         = "SELECT * INTO OUTFILE '$folder_path' FROM $tableName";//        $sql = "SELECT * FROM TBL_API_LOGS";//
        $result 	   = $this->db->query($query);
        exit;
        
        if(API_LOG_BKP_DATE == 0){
            $this->db->truncate(TBL_TEST_LOGS);
        }else if(API_LOG_BKP_DATE > 0){
			$curdate = date('Y-m-d h:m:s');
			$newDate = strtotime('-'.API_LOG_BKP_DATE.' day', strtotime($curdate));
			$get_setting_sql = " DELETE FROM " . TBL_API_LOGS . " WHERE al_created_date < $newDate ";
			$this->db->query($get_setting_sql);
        }
    }

    public function payment_notification() {
        $result = $this->Common_model->get_doctor_subscription();
        $this->load->model('Emailsetting_model');
        $email_template_data = $this->Emailsetting_model->get_emailtemplate_by_id(40);
        foreach ($result as $key => $value) {
            $parse_arr = array(
                '{DoctorName}' => DOCTOR . ' ' . $value->user_first_name . ' ' . $value->user_last_name,
                '{ExpiryDate}' => date('d/m/Y', strtotime($value->plan_expiry_date)),
                '{WebUrl}' => DOMAIN_URL,
                '{AppName}' => APP_NAME,
                '{MailContactNumber}' => $email_template_data['email_static_data']['contact_number'],
                '{MailEmailAddress}' => $email_template_data['email_static_data']['email_id'],
                '{MailCompanyName}' => $email_template_data['email_static_data']['company_name'],
                '{CopyRightsYear}' => date('Y')
            );
            $message = replace_values_in_string($parse_arr, $email_template_data['email_template_message']);
            $subject = $email_template_data['email_template_subject'];
            // $this->send_email(array($value->user_email => $value->user_email), $subject, $message);
        }
    }

     /**
     * Description :- This function is used to genereate the pdf and send the mail to the user
     * @param type $view_data
     * @param type $requested_data
     * @return int
     */
    // below code will delete
    public function not_used_prescription_pdf_share($requested_data) {
        $requested_data = json_decode(base64_decode($requested_data), true);
        $view_data = [];
        $patient_id = $requested_data['patient_id'];
        $doctor_id = $requested_data['doctor_id'];
        $appointment_id = $requested_data['appointment_id'];
        $appointment_whare = [
            'patient_id' => $patient_id ,
            'doctor_id' => $doctor_id,
            'appointment_id' => $appointment_id
        ];
        $check_patient_appointment = $this->Common_model->check_paient_appointment($appointment_whare);
        $clinic_id = $check_patient_appointment['appointment_clinic_id'];
        $view_data['doctor_data'] = $check_patient_appointment;
        $patient_data_where = ['user_id' => $patient_id, 'appointment_id' => $appointment_id];
        $get_patient_data = $this->Common_model->get_patient_data($patient_data_where);
        $view_data['patient_data'] = $get_patient_data;
        $view_data['with_signature'] = $requested_data['with_signature'];
        $view_data['vitalsign_data'] = [];
        if(!empty($requested_data['with_vitalsign']) && $requested_data['with_vitalsign'] == 'true' && !empty($get_patient_data['vital_report_id'])) {
            $view_data['vitalsign_data'] = [
                'vital_report_weight' => $get_patient_data['vital_report_weight'],
                'vital_report_bloodpressure_systolic' => $get_patient_data['vital_report_bloodpressure_systolic'],
                'vital_report_bloodpressure_diastolic' => $get_patient_data['vital_report_bloodpressure_diastolic'],
                'vital_report_pulse' => $get_patient_data['vital_report_pulse'],
                'vital_report_temperature' => $get_patient_data['vital_report_temperature'],
                'vital_report_temperature_type' => $get_patient_data['vital_report_temperature_type'],
                'vital_report_resp_rate' => $get_patient_data['vital_report_resp_rate'],
            ];
        }
        $view_data['clinicnote_data'] = [];
        if((!empty($requested_data['with_clinicnote']) && $requested_data['with_clinicnote'] == 'true') || (!empty($requested_data['with_only_diagnosis']) && $requested_data['with_only_diagnosis'] == 'true')) {
            if(!empty($get_patient_data['clinical_notes_reports_id'])) {
                $view_data['clinicnote_data'] = [
                    'clinical_notes_reports_kco' => $get_patient_data['clinical_notes_reports_kco'],
                    'clinical_notes_reports_complaints' => $get_patient_data['clinical_notes_reports_complaints'],
                    'clinical_notes_reports_observation' => $get_patient_data['clinical_notes_reports_observation'],
                    'clinical_notes_reports_diagnoses' => $get_patient_data['clinical_notes_reports_diagnoses'],
                    'clinical_notes_reports_add_notes' => $get_patient_data['clinical_notes_reports_add_notes'],
                ];
            }
        }
        $with_generic = $requested_data['with_generic'];
        $view_data['with_clinicnote'] = $requested_data['with_clinicnote'];
        $view_data['with_only_diagnosis'] = $requested_data['with_only_diagnosis'];
        $view_data['with_generic'] = $with_generic;
        $view_data['prescription_data'] = [];
        if(!empty($requested_data['with_prescription']) && $requested_data['with_prescription'] == 'true') {
            $view_data['prescription_data'] = $this->Common_model->get_patient_prescription($appointment_id, $with_generic);
        }
        $view_data['patient_lab_orders_data'] = [];
        if(!empty($requested_data['with_patient_lab_orders']) && $requested_data['with_patient_lab_orders'] == 'true' && !empty($get_patient_data['lab_report_id'])) {
            $view_data['patient_lab_orders_data'] = [
                'lab_report_test_name' => $get_patient_data['lab_report_test_name']
            ];
        }
        if (!empty($requested_data['with_procedure']) && $requested_data['with_procedure'] == 'true' && !empty($get_patient_data['lab_report_id'])) {
            $view_data['procedure_data'] = [
                'procedure_report_procedure_text' => $get_patient_data['procedure_report_procedure_text'],
                'procedure_report_note' => $get_patient_data['procedure_report_note'],
            ];
        }
        $view_data['reports'] = array();
        if(!empty($requested_data['with_anatomy_diagram']) && $requested_data['with_anatomy_diagram'] == 'true') {
            $view_data['reports'] = $this->Common_model->get_anatomy_diagram($appointment_id);
            $patient_tool_document = $this->Common_model->get_all_rows('me_patient_documents_shared','id', ['appointment_id'=> $appointment_id]);
            $view_data['patient_tool_document'] = $patient_tool_document;
        }
        $with_teleCunsultation = (!empty($check_patient_appointment['appointment_type']) && in_array($check_patient_appointment['appointment_type'], [4,5])) ? true : false;
        if($with_teleCunsultation) {
            $view_data['teleConsultationMsg'] = 'The prescription is given on telephonic consultation.';
        }
        $view_data['billing_data'] = [];
        if(!empty($get_patient_data['patient_analytics_id'])) {
            $share_link_row = $this->Common_model->get_single_row('me_patient_share_link_log', 'id,unique_code,doctor_id', ['patient_id' => $patient_id]);
            $reset_token = str_rand_access_token(20);
            if(empty($share_link_row['id'])) {
                $share_link_data = array(
                    'patient_id' => $patient_id,
                    'doctor_id' => $doctor_id,
                    'share_clinic_id' => $clinic_id,
                    'unique_code' => $reset_token,
                    'is_set_password' => 0,
                    'created_at' => date("Y-m-d H:i:s")
                );
                $this->Common_model->insert('me_patient_share_link_log', $share_link_data);
            } else {
                if($share_link_row['doctor_id'] == $doctor_id) {
                    $reset_token = $share_link_row['unique_code'];
                } else {
                    $share_link_data = array(
                        'doctor_id' => $doctor_id,
                        'share_clinic_id' => $clinic_id,
                        'unique_code' => $reset_token,
                        'status' => 1,
                        'updated_at' => date("Y-m-d H:i:s")
                    );
                    $this->Common_model->update('me_patient_share_link_log', $share_link_data, array('id' => $share_link_row['id']));
                }
            }
            $view_data['patient_share_link'] = MEDSIGN_WEB_CARE_URL . 'pt/'. $reset_token.'_uas7';
        }
        $pdf_name = '';
        $this->load->model('Emailsetting_model');
        $language_id = $requested_data['language_id'];
        if(!empty($language_id)){
            $languages = $this->Common_model->get_single_row('me_languages', 'LOWER(language_code) AS language_code', ['language_id' => $language_id]);
            $view_data['language_id'] = $language_id;
        } else {
            $view_data['language_id'] = 1;
        }

        if(!empty($languages) && !empty($languages['language_code'])) {
            $language_code = $languages['language_code'];
            if(!empty($language_code) && $language_code == 'hi')
                $language_code = 'hn';
            $view_data['language_code'] = $language_code;        
        } else {
            $view_data['language_code'] = 'en';
        }
        $patient_link_enable = $this->Common_model->get_single_row('me_global_settings','global_setting_value', ['global_setting_key'=> 'patient_link_enable']);
        $view_html = $this->load->view("prints/charting", $view_data, true);
        $patient_name = $get_patient_data['user_first_name'] . ' ' . $get_patient_data['user_last_name'];
        $pdf_name = 'RX_' . $patient_name . '.pdf';
        $email_template_data = $this->Emailsetting_model->get_emailtemplate_by_id(19);
        $view_data['doctor_data']['doctor_detail_speciality'] = str_replace(',', ', ', $view_data['doctor_data']['doctor_detail_speciality']);
        $view_data['doctor_data']['doctor_qualification'] = str_replace(',', ', ', $view_data['doctor_data']['doctor_qualification']);
        $address_data = [
            'address_name' => $view_data['doctor_data']['address_name'],
            'address_name_one' => $view_data['doctor_data']['address_name_one'],
            'address_locality' => $view_data['doctor_data']['address_locality'],
            'city_name' => $view_data['doctor_data']['city_name'],
            'state_name' => $view_data['doctor_data']['state_name'],
            'address_pincode' => $view_data['doctor_data']['address_pincode']
        ];
        // require_once MPDF_PATH;
        // $lang_code = 'en-GB';
        // $mpdf = new MPDF(
        //         $lang_code, 'A4', 0, 'arial', 8, 8, $header_margin, 8, 8, 5, 'P'
        // );
        $defaultConfig = (new \Mpdf\Config\ConfigVariables())->getDefaults();
        $fontDirs = $defaultConfig['fontDir'];
        $defaultFontConfig = (new \Mpdf\Config\FontVariables())->getDefaults();
        $fontData = $defaultFontConfig['fontdata'];
        $mpdf = new \Mpdf\Mpdf([
                'tempDir' => DOCROOT_PATH . 'uploads',
                'fontDir' => array_merge($fontDirs, [
                DOCROOT_PATH.'assets/fonts'
            ]),
            'fontdata' => $fontData + [
                'gotham_book' => [
                    'R' => 'Gotham-Book.ttf',
                    'I' => 'Gotham-Book.ttf',
                ],
            ],
            'default_font' => 'gotham_book',
            'mode' => 'en-GB',
            'format' => 'A4',
            'margin_top' => 0,
            'margin_bottom' => 8,
            'margin_left' => 8,
            'margin_right' => 8,
            'margin_header' => 8,
            'margin_footer' => 5
        ]);
        $mpdf->useOnlyCoreFonts = true;
        $mpdf->setAutoTopMargin = true;
        $mpdf->SetDisplayMode('real');
        $mpdf->list_indent_first_level = 0;
        $mpdf->setAutoBottomMargin = 'stretch';
        $mpdf->SetHTMLHeader('
            <table style="width:100%;border-bottom:1px solid #000">
                <tr>
                    <td width="50%" style="text-align:left;vertical-align:top">
                     ' . DOCTOR. " ".$view_data['doctor_data']['user_first_name'] . " " . $view_data['doctor_data']['user_last_name'] . "<br>" . '
                     ' . 'Reg. No. '.$view_data['doctor_data']['doctor_regno'] . "<br>" . '
                     ' . $view_data['doctor_data']['doctor_detail_speciality'] . "<br>" . '
                     ' . $view_data['doctor_data']['doctor_qualification'] . "<br>" . '    
                    </td>
                    <td width="50%" style="text-align:right;vertical-align:top">
                        ' . $view_data['doctor_data']['clinic_name'] . "<br>" . '
                        ' . clinic_address($address_data) . "<br>" . '
                        ' . $view_data['doctor_data']['clinic_contact_number'] . ", " . '
                        ' . $view_data['doctor_data']['clinic_email'] . "<br>" . '
                    </td>
                </tr>
            </table>
        ');
        $patient_link_data = "";
        if(!empty($patient_link_enable) && $patient_link_enable['global_setting_value'] == "1"){
            $patient_link_data = '<tr>
                <td align="center" colspan="3" width="100%" style="font-size:10px">
                    <b>Please Visit MedSign Patient: </b> <a target="_blank" href="'.MEDSIGN_WEB_CARE_URL . '">' . MEDSIGN_WEB_CARE_URL . '</a>
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

        $upload_path = DOCROOT_PATH . 'uploads/' . PDF_FOLDER . '/';

        if (!file_exists($upload_path)) {
            mkdir($upload_path, 0777, TRUE);
            chmod($upload_path, 0777);
        }

        $file_name = $requested_data['patient_id'] . '.pdf';
        $mpdf->Output($upload_path . $file_name, 'F');

        $attachment_path = $upload_path . $file_name;

        if (!empty($attachment_path)) {

            $patient_email = $get_patient_data['user_email'];
            $user_phone_number = $get_patient_data['user_phone_number'];
            $doctor_name = DOCTOR . $check_patient_appointment['user_first_name'] . ' ' . $check_patient_appointment['user_last_name'];
            $email = $requested_data['email'];
            $clinic_name = $view_data['doctor_data']['clinic_name'];
            if(!empty($requested_data['share_via']) && !empty($email) && ($requested_data['share_via'] == 'email' || $requested_data['share_via'] == 'emailSms')) {
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
                $this->patient_send_attachment_email(array($email => $email), $subject, $message, $attachment_path_array);
            }
            if(!empty($requested_data['share_via']) && !empty($requested_data['mobile_no']) && ($requested_data['share_via'] == 'sms' || $requested_data['share_via'] == 'whatsapp' || $requested_data['share_via'] == 'emailSms')){
                $share_file_name = $requested_data['appointment_id'] . '_'. time() . '.pdf';
                upload_to_s3($attachment_path, PATIENT_PRESCRIPTION_SHARE.'/'.$requested_data['patient_id'].'/'.$share_file_name);
                $pdf_url = IMAGE_MANIPULATION_URL.PATIENT_PRESCRIPTION_SHARE.'/'.$requested_data['patient_id'].'/'.$share_file_name;
                $share_data = array(
                    'doctor_id' => $requested_data['doctor_id'],
                    'patient_id' => $requested_data['patient_id'],
                    'appointment_id' => $requested_data['appointment_id'],
                    'file_name' => $share_file_name,
                    'file_url' => $pdf_url,
                    'created_at' => date('Y-m-d H:i:s')
                );
                $last_id = $this->Common_model->insert('me_patient_record_share', $share_data);
                $download_link = create_shorturl(DOMAIN_URL.'dp/' . base64_encode(json_encode($last_id)));
                /*Share prescription via SMS or Whatsapp*/
                if(!empty($requested_data['share_via']) && ($requested_data['share_via'] == 'sms' || $requested_data['share_via'] == 'emailSms')) {
                    $send_message['phone_number'] = $requested_data['mobile_no'];
                    $send_message['message'] = sprintf(lang('shared_prescription'), $patient_name, $doctor_name, short_clinic_name($clinic_name), get_display_date_time("d/m/Y h:i A"), $download_link);
                    $send_message['whatsapp_sms_body'] = sprintf(lang('template_31_shared_prescription'), $patient_name, $doctor_name, $clinic_name, get_display_date_time("d/m/Y h:i A"), $download_link);
                    $send_message['doctor_id'] = $requested_data['doctor_id'];
                    $send_message['user_type'] = 2;
                    $send_message['patient_id'] = $requested_data['patient_id'];
                    $send_message['is_sms_count'] = true;
                    $send_message['is_check_sms_credit'] = $GLOBALS['ENV_VARS']['CHECK_SMS_CREDIT']['PRESCRIPTION_SHARE'];
                    if(!empty($requested_data['mobile_no']))
                        send_communication($send_message);
                } elseif(!empty($requested_data['share_via']) && $requested_data['share_via'] == 'whatsapp') {
                    
                }
                /*END Share prescription via SMS or Whatsapp*/
            }
            unlink($attachment_path);
            if((($requested_data['share_via'] == 'emailSms' || $requested_data['share_via'] == 'emailSms') && strtolower($email) != strtolower($patient_email)) || (($requested_data['share_via'] == 'sms' || $requested_data['share_via'] == 'emailSms') && $requested_data['mobile_no'] != $user_phone_number)) {
                if(is_send_email_to_patient($requested_data['doctor_id'])) {
                    /*Start Patient Details Share Mail Send to Patient*/
                    $email = $patient_email;
                    $columns = 'u.user_email,u.user_email_verified';
                    $parent_members = $this->Common_model->get_linked_family_members($patient_id, $columns);
                    $patient_email_id_arr = array();
                    foreach ($parent_members as $parent_member) {
                        if($parent_member->user_email_verified == 1) {
                            $patient_email_id_arr[$parent_member->user_email] = $parent_member->user_email;
                        }
                    }
                    if(!empty($email)) {
                        $patient_email_id_arr[$email] = $email;
                    }
                    if(count($patient_email_id_arr) > 0) {
                        $email_template_data = $this->Emailsetting_model->get_emailtemplate_by_id(28);
                        $parse_arr = array(
                            '{PatinetName}' => $patient_name,
                            '{DrName}' => $doctor_name,
                            '{DrLastName}' => DOCTOR . $check_patient_appointment['user_last_name'],
                            '{WebUrl}' => DOMAIN_URL,
                            '{AppName}' => APP_NAME,
                            '{MailEmailAddress}' => $email_template_data['email_static_data']['email_id'],
                            '{MailCompanyName}' => $email_template_data['email_static_data']['company_name'],
                            '{CopyRightsYear}' => date('Y')
                        );
                        $message = replace_values_in_string($parse_arr, $email_template_data['email_template_message']);
                        $subject = $email_template_data['email_template_subject'];
                        $this->patient_send_attachment_email($patient_email_id_arr, $subject, $message);
                    }
                }
            }
        } else {
            return 0;
        }
        return 1;
    }

    public function download_prescription($requested_data) {
        $id = json_decode(base64_decode($requested_data), true);
        $row = $this->Common_model->patient_prescription_share_details($id);
        if($row['status'] == 1) {
            $view_pdf_url = DOMAIN_URL . 'prescription/' . base64_encode(urldecode(DOMAIN_URL . 'pdf_preview/web/view_pdf.php?file_url=' . base64_encode(urldecode(get_file_full_path($row['file_url'])))));
            $file_name = $row['patient_name'] . ' '. get_display_date_time("d_m_Y H_i_s") .'.pdf';
            $pdf_url = $row['file_url'];
            $update_data = ['open_count' => $row['open_count']+1, 'updated_at' => date('Y-m-d H:i:s')];
            $where = ['id' => $id];
            $this->Common_model->update('me_patient_record_share', $update_data, $where);
            redirect($view_pdf_url);
            // header('Content-Type: application/octet-stream');
            // header("Content-Transfer-Encoding: Binary"); 
            // header("Content-disposition: attachment; filename=\"".$file_name."\""); 
            // readfile($pdf_url);
        } else {
            die('File not available');
        }
    }

    public function viewqr($id) {
        $user_id = encrypt_decrypt($id, 'decrypt');
        include_once BUCKET_HELPER_PATH;
        $is_exist_qr_code = checkResource('doctorqrcode/'.$user_id.'.pdf');
        if(!empty($is_exist_qr_code)) {
            $qr_code_pdf = IMAGE_MANIPULATION_URL . 'doctorqrcode/'.$user_id.'.pdf';
            $view_pdf_url = DOMAIN_URL . 'qrcode/' . base64_encode(urldecode(DOMAIN_URL . 'pdf_preview/web/view_pdf.php?file_url=' . base64_encode(urldecode($qr_code_pdf))));
            redirect($view_pdf_url);
        } else {
            die('QR code not available');
        }
    }

    public function view_invoice($requested_data) {
        $id = json_decode(base64_decode($requested_data), true);
        $row = $this->Common_model->patient_prescription_share_details($id);
        if($row['status'] == 1) {
            $view_pdf_url = DOMAIN_URL . 'invoice/' . base64_encode(urldecode(DOMAIN_URL . 'pdf_preview/web/view_pdf.php?file_url=' . base64_encode(urldecode(get_file_full_path($row['file_url'])))));
            $file_name = $row['patient_name'] . ' '. get_display_date_time("d_m_Y H_i_s") .'.pdf';
            $update_data = ['open_count' => $row['open_count']+1, 'updated_at' => date('Y-m-d H:i:s')];
            $where = ['id' => $id];
            $this->Common_model->update('me_patient_record_share', $update_data, $where);
            redirect($view_pdf_url);
        } else {
            die('File not available');
        }
    }

    public function delete_prescription() {
        $result = $this->Common_model->get_patient_prescription_share();
        if(count($result) > 0) {
            foreach ($result as $key => $value) {
                $file_path = PATIENT_PRESCRIPTION_SHARE.'/'.$value['patient_id'].'/'.$value['file_name'];
                delete_file_from_s3($file_path);
            }
            $this->Common_model->update_patient_prescription_share(array_column($result, 'id'), ['status' => 9]);
        }
        die('Deleted');
    }

    public function plan_expiry_report($days = '') {
        if(!empty($days) && !is_numeric($days)) {
            echo "Invalid days value.";
            exit;
        }
        if(empty($GLOBALS['ENV_VARS']['PLAN_EXPIRY_EMAIL'])) {
            echo "Email address not set.";
            exit;
        }
        $email_id_arr = explode(',', $GLOBALS['ENV_VARS']['PLAN_EXPIRY_EMAIL']);

        $result = $this->Common_model->get_doctor_subscription_expiry($days);
        if(count($result) > 0) {
            $datetime = get_display_date_time("Y-m-d H:i:s");
            if(!empty($days)) {
                $start_date = date('d/m/Y', strtotime($datetime));
                $end_date  = date('d/m/Y', strtotime("+" . $days . " days", strtotime($datetime)));
            } else {
                $start_date = date('01/m/Y', strtotime($datetime));
                $end_date  = date('t/m/Y', strtotime($datetime));
            }
            // echo $start_date . '==' . $end_date;die;
            $this->load->library('excel');
            $object = new PHPExcel();
            $object->setActiveSheetIndex(0);

            $objDrawing = new PHPExcel_Worksheet_MemoryDrawing();
            $object = report_header($object,$objDrawing);
            $table_columns = array();
            $table_columns[] = ["label" => "Unique ID", "width" => 15];
            $table_columns[] = ["label" => "Name", "width" => 25];
            $table_columns[] = ["label" => "Email", "width" => 40];
            $table_columns[] = ["label" => "Phone number", "width" => 20];
            $table_columns[] = ["label" => "Last login date", "width" => 20];
            $table_columns[] = ["label" => "Address", "width" => 40];
            $table_columns[] = ["label" => "City", "width" => 15];
            $table_columns[] = ["label" => "State", "width" => 15];
            $table_columns[] = ["label" => "Plan name", "width" => 15];
            $table_columns[] = ["label" => "Expiry date", "width" => 15];
            $table_columns[] = ["label" => "Plan Type", "width" => 15];
            $table_columns[] = ["label" => "Source name", "width" => 20];
            $table_columns[] = ["label" => "Source email", "width" => 40];
            $table_columns[] = ["label" => "Source phone", "width" => 15];
            $table_columns[] = ["label" => "Source city", "width" => 15];
            $column = 0;
            $excel_row = 5;
            $object->getActiveSheet()->setCellValueByColumnAndRow(2, $excel_row, 'Filters:');
            $excel_row++;
            $object->getActiveSheet()->setCellValueByColumnAndRow(2, $excel_row, 'Start Date:');
            $object->getActiveSheet()->setCellValueByColumnAndRow(3, $excel_row, $start_date);
            $excel_row++;

            $object->getActiveSheet()->setCellValueByColumnAndRow(2, $excel_row, 'End Date:');
            $object->getActiveSheet()->setCellValueByColumnAndRow(3, $excel_row, $end_date);
            $excel_row++;
            foreach($table_columns as $key => $field) {
                $object = report_header_row($object,$column, $excel_row, $field['label'],$field['width']);
                $column++;
            }
            $excel_row++;
            foreach ($result as $row) {
                $object->getActiveSheet()->setCellValueByColumnAndRow(0, $excel_row, $row->user_unique_id);
                $object->getActiveSheet()->setCellValueByColumnAndRow(1, $excel_row, $row->user_first_name . ' ' . $row->user_last_name);
                $object->getActiveSheet()->setCellValueByColumnAndRow(2, $excel_row, $row->user_email);
                $object->getActiveSheet()->setCellValueByColumnAndRow(3, $excel_row, $row->user_phone_number);
                $object->getActiveSheet()->setCellValueByColumnAndRow(4, $excel_row, get_display_date_time("d/m/Y H:i:s", $row->last_login_at));
                $object->getActiveSheet()->setCellValueByColumnAndRow(5, $excel_row, $row->address_name_one);
                $object->getActiveSheet()->setCellValueByColumnAndRow(6, $excel_row, $row->city_name);
                $object->getActiveSheet()->setCellValueByColumnAndRow(7, $excel_row, $row->state_name);
                $object->getActiveSheet()->setCellValueByColumnAndRow(8, $excel_row, $row->sub_plan_name);
                $object->getActiveSheet()->setCellValueByColumnAndRow(9, $excel_row, get_display_date_time("d/m/Y", $row->plan_expiry_date));
                $object->getActiveSheet()->setCellValueByColumnAndRow(10, $excel_row, doctor_sub_plan_type($row->doctor_plan_type));
                $object->getActiveSheet()->setCellValueByColumnAndRow(11, $excel_row, $row->src_master_contact_person_name);
                $object->getActiveSheet()->setCellValueByColumnAndRow(12, $excel_row, $row->src_master_email);
                $object->getActiveSheet()->setCellValueByColumnAndRow(13, $excel_row, $row->src_master_phone);
                $object->getActiveSheet()->setCellValueByColumnAndRow(14, $excel_row, $row->src_master_city);
                $excel_row++;
            }
            $upload_path = DOCROOT_PATH . 'uploads/temp/';
            if (!file_exists($upload_path)) {
                mkdir($upload_path, 0777, TRUE);
                chmod($upload_path, 0777);
            }
            $object_writer = new PHPExcel_Writer_Excel2007($object);
            // header('Content-Type: application/vnd.ms-excel');
            // header('Content-Disposition: attachment;filename="Doctor free plan expiry report.xls"');
            // $object_writer->save('php://output');
            $filename = 'Doctors free plan expiry report.xlsx';
            $object_writer->save($upload_path.$filename);
            $this->load->model('Emailsetting_model');
            $email_template_data = $this->Emailsetting_model->get_emailtemplate_by_id(41);
            $parse_arr = array(
                '{WebUrl}' => DOMAIN_URL,
                '{AppName}' => APP_NAME,
                '{MailEmailAddress}' => $email_template_data['email_static_data']['email_id'],
                '{MailCompanyName}' => $email_template_data['email_static_data']['company_name'],
                '{CopyRightsYear}' => date('Y')
            );
            $message = replace_values_in_string($parse_arr, $email_template_data['email_template_message']);
            $subject = $email_template_data['email_template_subject'];
            $attachment_path_array = array($upload_path.$filename, $filename);
            // $this->send_attachment_email($email_id_arr, $subject, $message, $attachment_path_array);
            unlink($upload_path.$filename);
            echo "Doctors free plan expiry report sent successfuly.";
        } else {
            echo "Doctors free plan expiry data not found.";
        }
        exit;
    }

    public function send_health_advice() {
        // echo $link = $GLOBALS['ENV_VARS']['DOMAIN_URL'] . "health_advice/".base64_encode(json_encode(['h_a_id' => 1, 'p_id' => 204, 'p_h_id' => 2]));
        // die;
        // echo "<pre>";
        // $time = "08:00:00";
        $time = get_display_date_time("H:i") . ":00";
        $result = $this->Common_model->get_patient_health_advice($time);
        // print_r($result);
        $patient_health_advice = [];
        if(count($result) > 0) {
            foreach ($result as $key => $value) {
                if($value->patient_health_advice_schedule == 1 || 
                    ($value->patient_health_advice_send_day == date("N") && $value->patient_health_advice_schedule == 2) || ($value->patient_health_advice_schedule == 3 && date("Y-m-d") != date('Y-m-d', strtotime($value->patient_health_advice_created_at)) && (date('d', strtotime($value->patient_health_advice_created_at)) == date("d") || date('Y-m-d', strtotime("+1 days", strtotime($value->patient_health_advice_created_at))) == date("Y-m-d")))) {
                    $patient_health_advice[] = $value;
                }
            }
        }
        if(count($patient_health_advice) > 0) {
            $group_ids = array_unique(array_column($patient_health_advice, 'patient_health_advice_group_id'));
            $health_advice = $this->Common_model->get_health_advice($group_ids);
            $health_advice_final = $this->get_patient_send_health_advice($health_advice);
            $this->load->model('Emailsetting_model');
            $email_template_data = $this->Emailsetting_model->get_emailtemplate_by_id(42);
            $audit_log_insert = [];
            $update_patient_health_advice = [];
            // echo "<pre>";
            // print_r($health_advice_final);die;
            foreach ($patient_health_advice as $key => $value) {
                $last_send_order = 1;
                if(!empty($value->patient_health_advice_last_send_order))
                    $last_send_order = $value->patient_health_advice_last_send_order + 1;

                if(empty($health_advice_final[$value->patient_health_advice_group_id][$last_send_order])) {
                    continue;
                }
                $health_advice_row = $health_advice_final[$value->patient_health_advice_group_id][$last_send_order];
                $health_advice_image = '';
                if(!empty($health_advice_row->health_advice_image)) {
                    $health_advice_image = '<img src="'.$health_advice_row->health_advice_image.'" />';
                }
                $health_advice_video = '';
                if(!empty($health_advice_row->health_advice_video_url)) {
                    $health_advice_video = '<b>Video:</b> '.$health_advice_row->health_advice_video_url;
                }
                $link = $GLOBALS['ENV_VARS']['DOMAIN_URL'] . "health_advice/".base64_encode(json_encode(['h_a_id' => $health_advice_row->health_advice_id, 'p_id' => $value->patient_health_advice_patient_id, 'p_h_id' => $value->patient_health_advice_id]));

                file_put_contents($this->health_link_file, "\n\n =================" .get_display_date_time("Y-m-d H:i:s"). " " . $value->patient_name . "=================\n" . $link . "\n\n", FILE_APPEND | LOCK_EX);

                $parse_arr = array(
                    '{PatientName}' => $value->patient_name,
                    '{HealthAdviceImage}' => $health_advice_image,
                    '{HealthAdviceVideo}' => $health_advice_video,
                    '{WebViewLink}' => $link,
                    '{HealthAdviceDesc}' => nl2br($health_advice_row->health_advice_desc),
                    '{WebUrl}' => DOMAIN_URL,
                    '{AppName}' => APP_NAME,
                    '{MailEmailAddress}' => $email_template_data['email_static_data']['email_id'],
                    '{MailCompanyName}' => $email_template_data['email_static_data']['company_name'],
                    '{CopyRightsYear}' => date('Y')
                );
                $message = replace_values_in_string($parse_arr, $email_template_data['email_template_message']);
                $subject = str_replace('{HealthAdvice}', $health_advice_row->health_advice_name, $email_template_data['email_template_subject']);
                if(!empty($value->user_email) && $value->patient_health_advice_is_send_email == 1) {
                    // $this->send_attachment_email($value->user_email, $subject, $message);
                    $health_advice_log_data = array(
                        'health_advice_id' => $health_advice_row->health_advice_id,
                        'health_advice_group_id' => $health_advice_row->health_advice_group_id,
                        'health_advice_order' => $health_advice_row->health_advice_order,
                    );
                    $audit_log_insert[] = array(
                        'action_slug_name' => 'health_advice_sent',
                        'user_id' => $value->patient_health_advice_doctor_id,
                        'user_type' => 1,
                        'table_name' => 'me_users',
                        'table_primary_key_name' => 'user_id',
                        'table_primary_key_value' => $value->patient_health_advice_patient_id,
                        'table_old_value' => $health_advice_row->health_advice_id,
                        'table_new_value' => json_encode($health_advice_log_data),
                        'audit_created_at' => date('Y-m-d H:i:s')
                    );
                    $patient_health_advice_end_date = NULL;
                    if(empty($health_advice_final[$value->patient_health_advice_group_id][$last_send_order+1])) {
                        $patient_health_advice_end_date = date("Y-m-d");
                    }
                    $update_patient_health_advice[] = ['patient_health_advice_id' => $value->patient_health_advice_id, 'patient_health_advice_last_send_order' => $last_send_order, 'patient_health_advice_end_date' => $patient_health_advice_end_date, 'patient_health_advice_updated_at' => date("Y-m-d H:i:s")];

                }
            }
            if(count($update_patient_health_advice) > 0)
                $this->Common_model->update_multiple('me_patients_health_advice', $update_patient_health_advice, 'patient_health_advice_id');
            if(count($audit_log_insert) > 0)
                $this->Common_model->insert_multiple('me_audit_log', $audit_log_insert);
        }
        die("Done!");
    }

    public function get_patient_send_health_advice($health_advice) {
        $health_advice_array = [];
        foreach ($health_advice as $key => $value) {
            $health_advice_array[$value->health_advice_group_id][$value->health_advice_order] = $value;
        }
        return $health_advice_array;
    }

    public function app_download() {
        $this->load->helper('download');
        force_download(DOCROOT_PATH . 'uploads/MedSignV1.0.apk', NULL);
        exit;
    }

    public function uas7_report_generate() {
        $query = "SELECT 
                COUNT(distinct patient_diary_id) AS total,
                patient_diary_patient_id,
                MAX(patient_diary_date) AS patient_diary_date,
                MAX(file_report_date) AS file_report_date,
                count(distinct (CASE WHEN patient_diary_date > (SELECT MAX(file_report_date) FROM me_files_reports WHERE file_report_user_id=patient_diary_patient_id AND file_report_report_type_id=13 AND file_report_status=1) THEN patient_diary_id END)) AS new_points
                FROM
                me_patient_diary 
                LEFT JOIN me_files_reports ON file_report_user_id=patient_diary_patient_id AND file_report_report_type_id=13 AND file_report_status=1
                WHERE patient_diary_status = 1 
                GROUP BY patient_diary_patient_id";
        $result = $this->Common_model->get_all_rows_by_query($query);
        $patient_ids_arr = [];
        foreach ($result as $key => $value) {
            if(empty($value['file_report_date']) && $value['total'] >= 14) {
                $patient_ids_arr[] = $value['patient_diary_patient_id'];
            } elseif(!empty($value['file_report_date']) && $value['new_points'] >= 14) {
                $patient_ids_arr[] = $value['patient_diary_patient_id'];
            }
        }
        if(!empty($patient_ids_arr) && count($patient_ids_arr) > 0) {
            $this->load->model('patient_model','patient');
            $uas7_result = $this->patient->get_uas7_all_data_by_patient_id($patient_ids_arr);
            $uas7_patient_data = [];
            $uas7_weekly_data = [];
            foreach ($uas7_result as $key => $value) {
                $label_arr = explode(",", $value->patient_diary_label);
                $value_arr = explode(",", $value->patient_diary_value);
                $uas7_val_arr = array_combine($label_arr, $value_arr);
                $uas7_patient_data[$value->patient_diary_patient_id][] = [
                    'patient_diary_date' => $value->patient_diary_date,
                    'diary_date' => date("d/m/y", strtotime($value->patient_diary_date)),
                    'wheal_label' => 'wheal_count',
                    'pruritus_label' => 'pruritus_count',
                    'wheal_value' => $uas7_val_arr['wheal_count'],
                    'pruritus_value' => $uas7_val_arr['pruritus_count'],
                    'uas_total_value' => $uas7_val_arr['wheal_count'] + $uas7_val_arr['pruritus_count']
                ];
                if(count($uas7_patient_data[$value->patient_diary_patient_id]) % 7 == 0) {
                    $uas7_weekly_data[$value->patient_diary_patient_id][] = $uas7_patient_data[$value->patient_diary_patient_id];
                    $uas7_patient_data[$value->patient_diary_patient_id] = [];
                }
            }
            $get_health_analytics_test = $this->Common_model->get_single_row('me_health_analytics_test', 'health_analytics_test_validation', array('health_analytics_test_id' => 308));
            $view_data['uas7_range'] = json_decode($get_health_analytics_test['health_analytics_test_validation'], true);
            // require_once MPDF_PATH;
            $lang_code = 'en-GB';
            $report_array = [];
            $insert_report_image_array = [];
            $files_reports_auto_id = $this->patient->get_next_auto_id('me_files_reports');
            foreach ($uas7_weekly_data as $patient_id => $value) {
                $labels = [];
                $uas7_values = [];
                $daily_labels = [];
                $uas_daily_values = [];
                foreach ($value as $key => $uas_data) {
                    $wheal_total = array_sum(array_map(function($item) { 
                        return $item['wheal_value']; 
                    }, $uas_data));
                    $pruritus_total = array_sum(array_map(function($item) { 
                        return $item['pruritus_value']; 
                    }, $uas_data));
                    $uas7_values[] = $pruritus_total + $wheal_total;
                    $labels[] = $uas_data[0]['patient_diary_date'];
                    $daily_labels = array_merge($daily_labels,array_column($uas_data, 'diary_date'));
                    $uas_daily_values = array_merge($uas_daily_values,array_column($uas_data, 'uas_total_value'));
                }
                $file_report_date = $value[0][0]['patient_diary_date'];
                $daily_labels = daily_graph_date_label($daily_labels);
                $view_data['uas7_result'] = $value;
                // $mpdf = new MPDF(
                //         $lang_code, 'A4', 0, 'arial', 8, 8, 25, 8, 8, 5, 'P'
                // );
                $defaultConfig = (new \Mpdf\Config\ConfigVariables())->getDefaults();
                $fontDirs = $defaultConfig['fontDir'];
                $defaultFontConfig = (new \Mpdf\Config\FontVariables())->getDefaults();
                $fontData = $defaultFontConfig['fontdata'];
                $mpdf = new \Mpdf\Mpdf([
                        'tempDir' => DOCROOT_PATH . 'uploads',
                        'fontDir' => array_merge($fontDirs, [
                        DOCROOT_PATH.'assets/fonts'
                    ]),
                    'fontdata' => $fontData + [
                        'gotham_book' => [
                            'R' => 'Gotham-Book.ttf',
                            'I' => 'Gotham-Book.ttf',
                        ],
                    ],
                    'default_font' => 'gotham_book',
                    'mode' => 'en-GB',
                    'format' => 'A4',
                    'margin_top' => 25,
                    'margin_bottom' => 8,
                    'margin_left' => 8,
                    'margin_right' => 8,
                    'margin_header' => 8,
                    'margin_footer' => 5
                ]);
                $mpdf->useOnlyCoreFonts = true;
                $mpdf->SetDisplayMode('real');
                $mpdf->list_indent_first_level = 0;
                $mpdf->setAutoBottomMargin = 'stretch';
                $mpdf->SetHTMLHeader('
                        <table style="width:100%;border-bottom:1px solid #000;">
                            <tr>
                                <td width="50%" style="text-align:left;vertical-align:top;">UAS7 Diary</td>
                                <td width="50%" style="text-align:right;vertical-align:top;">Date: '.get_display_date_time("d/m/Y").'</td>
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
                $graph_image_url = "https://quickchart.io/chart?c={type:'line',data:{labels:".str_replace('"', "'", json_encode($labels)).", datasets:[{label:'UAS7', data: ".json_encode($uas7_values).", fill:false,borderColor:'%2330aca5'}]},options: {title: {display: true,text: 'UAS7',position:'bottom'},legend: {display: false,position: 'bottom'},scales: {yAxes: [{id: 'y-axis-0',type: 'linear',display: true,scaleLabel: {display: true,labelString: 'UAS7'},position: 'left'}],xAxes : [{scaleLabel: {display: true,labelString: 'Date'},position: 'bottom'}]},plugins: {datalabels:{display:true,align:'bottom',backgroundColor:'%23ccc',borderRadius:3},}}}";
                
                $view_data['graph_image_url'] = $graph_image_url;

                $daily_graph_image_url = "https://quickchart.io/chart?height=400&c={type:'line',data:{labels:".str_replace('"', "'", json_encode($daily_labels)).", datasets:[{label:'UAS7', data: ".json_encode($uas_daily_values).", fill:false,borderColor:'%2330aca5'}]},options: {title: {display: true,text: 'UAS',position:'bottom'},legend: {display: false,position: 'bottom'},scales: {yAxes: [{id: 'y-axis-0',type: 'linear',display: true,scaleLabel: {display: true,labelString: 'UAS'},position: 'left'}],xAxes : [{scaleLabel: {display: true,labelString: 'Date'},position: 'bottom'}]},plugins: {datalabels:{display:true,align:'bottom',backgroundColor:'%23ccc',borderRadius:3},}}}";

                $view_data['daily_graph_image_url'] = $daily_graph_image_url;
                $view_html = $this->load->view("patient/uas7_report_view", $view_data, true);
                // echo $view_html;die;
                $mpdf->WriteHTML($view_html);
                $upload_path = DOCROOT_PATH . 'uploads/' . REPORT_FOLDER . '/';
                if (!file_exists($upload_path)) {
                    mkdir($upload_path, 0777, TRUE);
                    chmod($upload_path, 0777);
                }
                $datetime = get_display_date_time('Y_m_d_H_i_s');
                $file_name = "UAS7_Report_".$patient_id."_".$datetime.".pdf";
                // echo $mpdf->Output();die;
                $mpdf->Output($upload_path . $file_name, 'F');
                $attachment_path = $upload_path . $file_name;
                $report_array[] = array(
                    'file_report_user_id' => $patient_id,
                    'file_report_doctor_user_id' => $patient_id,
                    'file_report_appointment_id' => NULL,
                    'file_report_clinic_id' => NULL,
                    'file_report_name' => "UAS7",
                    'file_report_report_type_id' => 13,
                    'file_report_date' => $file_report_date,
                    'file_report_share_status' => 1,
                    'file_report_created_at' => date("Y-m-d H:i:s")
                    
                );
                $inserted_id = $files_reports_auto_id;
                $upload_path = UPLOAD_FILE_FULL_PATH . REPORT_FOLDER . "/" . $inserted_id;
                $upload_folder = REPORT_FOLDER . "/" . $inserted_id;
                upload_to_s3($attachment_path, $upload_folder.'/'.$file_name);
                $report_image_url = get_file_json_detail(REPORT_FOLDER . "/" . $inserted_id . "/" . $file_name);
                $insert_report_image_array[] = array(
                    'file_report_image_file_report_id' => $inserted_id,
                    'file_report_image_url' => $report_image_url,
                    'report_file_size' => get_file_size(get_file_full_path($report_image_url)),
                    'file_report_image_created_at' => date("Y-m-d H:i:s")
                );
                $files_reports_auto_id++;
                if(!IS_SERVER_UPLOAD && file_exists($attachment_path))
                    unlink($attachment_path);
            }
            $this->Common_model->insert_multiple('me_files_reports', $report_array);
            $this->Common_model->insert_multiple('me_files_reports_images', $insert_report_image_array);
        }
        echo "Done";
        exit;
    }
    public function uas7_report_email() {
        $date = get_display_date_time("Y-m-d");
        $this->load->model('patient_model','patient');
        $result = $this->patient->get_uas7_reports($date);
        $report_data = [];
        if(!empty($result)) {
            $this->load->model('Emailsetting_model');
            $email_template_data = $this->Emailsetting_model->get_emailtemplate_by_id(59);
            foreach ($result as $key => $value) {
                if(empty($report_data[$value->file_report_id])){
                    $report_data[$value->file_report_id] = $value;
                    $report_data[$value->file_report_id]->email_ids = [];
                    if(!empty($value->user_email))
                        $report_data[$value->file_report_id]->email_ids[] = $value->user_email;

                    if(!empty($value->parent_user_email))
                        $report_data[$value->file_report_id]->email_ids[] = $value->parent_user_email;
                } else {
                    if(!empty($value->parent_user_email))
                        $report_data[$value->file_report_id]->email_ids[] = $value->parent_user_email;
                }
            }
        }
        foreach ($report_data as $key => $value) {
            if(!empty($value->email_ids)) {
                $patient_name = $value->user_first_name . ' ' . $value->user_last_name;
                $parse_arr = array(
                    '{PatientName}' => $patient_name,
                    '{WebUrl}' => DOMAIN_URL,
                    '{AppName}' => APP_NAME,
                    '{MailContactNumber}' => $email_template_data['email_static_data']['contact_number'],
                    '{MailEmailAddress}' => $email_template_data['email_static_data']['email_id'],
                    '{MailCompanyName}' => $email_template_data['email_static_data']['company_name'],
                    '{CopyRightsYear}' => date('Y')
                );
                $message = replace_values_in_string($parse_arr, $email_template_data['email_template_message']);
                $subject = $email_template_data['email_template_subject'];
                $attachment_path = get_file_full_path($value->file_report_image_url);
                $file_name = end(explode("/", $attachment_path));
                $attachment_path_array = array($attachment_path, $file_name);
                $this->patient_send_attachment_email($value->email_ids, $subject, $message, $attachment_path_array);
            }
        }
        echo "Done";
        exit;
    }
    
    // before 48 hours appointment reminder
    public function appointment_reminder() {
        $today_date = get_display_date_time("Y-m-d H:i:s");
        // $today_date = "2020-09-21 20:10:00";
        $datetime = date("Y-m-d H:i:s" ,strtotime("+48 hour", strtotime($today_date)));
        $result = $this->Common_model->appointment_reminder($datetime);
        // echo "<pre>";
        // print_r($result);die;
        // $insert_share_link_data = [];
        // $update_share_link_data = [];
        $tele_consultation_appomnt_doctor_ids = [];
        $doctor_payment_links = [];
        $payment_links_for_whatsapp = [];
        $minutes = "";
        $appointments_data = [];
        if(!empty($result) && count($result) > 0) {
            foreach ($result as $key => $value) {
                if($value->appointment_type == 4 || $value->appointment_type == 5) {
                    $tele_consultation_appomnt_doctor_ids[] = $value->appointment_doctor_user_id;
                }
                if(empty($minutes) && $value->appointment_type == 5) {
                    $get_global_setting_data = $this->Common_model->get_single_row('me_global_settings', 'global_setting_value', ['global_setting_key' => 'teleconsultant_link_send_minutes']);
                    $minutes = trim($get_global_setting_data['global_setting_value']);
                }
                if(empty($appointments_data[$value->appointment_id])) {
                    $appointments_data[$value->appointment_id] = $value;
                    $appointments_data[$value->appointment_id]->reminder_users = [];
                    if(!empty($value->user_phone_number)) {
                        $appointments_data[$value->appointment_id]->reminder_users[] = (object)[
                            'patient_id' => $value->appointment_user_id,
                            'user_phone_number' => $value->user_phone_number,
                            'doctor_id' => $value->doctor_id,
                            'share_link_id' => $value->share_link_id,
                            'unique_code' => $value->unique_code
                        ];
                    }
                    if(!empty($value->caretaker_phone_number)) {
                        $appointments_data[$value->appointment_id]->reminder_users[] = (object)[
                            'patient_id' => $value->parent_patient_id,
                            'user_phone_number' => $value->caretaker_phone_number,
                            'doctor_id' => $value->psl2_doctor_id,
                            'share_link_id' => $value->psl2_share_link_id,
                            'unique_code' => $value->psl2_unique_code
                        ];
                    }
                } else {
                    if(!empty($value->caretaker_phone_number)) {
                        $appointments_data[$value->appointment_id]->reminder_users[] = (object)[
                            'patient_id' => $value->parent_patient_id,
                            'user_phone_number' => $value->caretaker_phone_number,
                            'doctor_id' => $value->psl2_doctor_id,
                            'share_link_id' => $value->psl2_share_link_id,
                            'unique_code' => $value->psl2_unique_code
                        ];
                    }
                }
            }
            if(count($tele_consultation_appomnt_doctor_ids) > 0) {
                $doctor_payment_mode = $this->Common_model->doctor_payment_mode_link($tele_consultation_appomnt_doctor_ids);
                foreach ($doctor_payment_mode as $payment_mode) {
                    if(empty($doctor_payment_links[$payment_mode->doctor_payment_mode_doctor_id]))
                        $doctor_payment_links[$payment_mode->doctor_payment_mode_doctor_id] = "";
                    if(empty($payment_links_for_whatsapp[$payment_mode->doctor_payment_mode_doctor_id]))
                        $payment_links_for_whatsapp[$payment_mode->doctor_payment_mode_doctor_id] = "";

                    if($payment_mode->doctor_payment_mode_master_id == 5) {
                        $bank_details = json_decode($payment_mode->doctor_payment_mode_upi_link);
                        if(empty($doctor_payment_links[$payment_mode->doctor_payment_mode_doctor_id])) {
                            $doctor_payment_links[$payment_mode->doctor_payment_mode_doctor_id] .= "IFSC Code: " . $bank_details->ifsc_code . " A/c No: " . $bank_details->account_no;
                        }
                        $payment_links_for_whatsapp[$payment_mode->doctor_payment_mode_doctor_id] .= "*Bank Name:* " . $bank_details->bank_name . ", *A/c Holder's Name:* " . $bank_details->bank_holder_name . ", *IFSC Code:* " . $bank_details->ifsc_code . ", *A/c No:* " . $bank_details->account_no . " ";
                    } else {
                        if(empty($doctor_payment_links[$payment_mode->doctor_payment_mode_doctor_id])) {
                            $doctor_payment_links[$payment_mode->doctor_payment_mode_doctor_id] .= $payment_mode->payment_mode_name . ": " . $payment_mode->doctor_payment_mode_upi_link;
                        }
                        $payment_links_for_whatsapp[$payment_mode->doctor_payment_mode_doctor_id] .= "*" . $payment_mode->payment_mode_name . ":* " . $payment_mode->doctor_payment_mode_upi_link . " ";
                    }
                }
            }
        }
        foreach ($appointments_data as $key => $value) {
            foreach ($value->reminder_users as $patient) {
                /*if(!empty($patient->share_link_id)) {
                    if(!empty($patient->unique_code) && $patient->doctor_id == $value->appointment_doctor_user_id) {
                        $reset_token = $patient->unique_code;
                    } else {
                        $reset_token = str_rand_access_token(20);
                        $update_share_link_data[] = array(
                            'id' => $patient->share_link_id,
                            'doctor_id' => $value->appointment_doctor_user_id,
                            'share_clinic_id' => $value->appointment_clinic_id,
                            'unique_code' => $reset_token,
                            'status' => 1,
                            'updated_at' => date("Y-m-d H:i:s")
                        );
                    }
                } else {
                    $reset_token = str_rand_access_token(20);
                    $insert_share_link_data[] = array(
                        'patient_id' => $patient->patient_id,
                        'doctor_id' => $value->appointment_doctor_user_id,
                        'share_clinic_id' => $value->appointment_clinic_id,
                        'share_clinic_id' => $value->appointment_clinic_id,
                        'unique_code' => $reset_token,
                        'is_set_password' => 0,
                        'created_at' => date("Y-m-d H:i:s")
                    );
                }
                // $PATIENT_SHARE_LINK = MEDSIGN_WEB_CARE_URL . 'pt/'. $reset_token;
                $link_id = base64_encode(json_encode(1));
                $PATIENT_SHARE_LINK = DOMAIN_URL . 'f/'.$link_id.'/' . $reset_token;*/
                $send_message = [
                    'phone_number' => $patient->user_phone_number,
                    'doctor_id' => $value->appointment_doctor_user_id,
                    'patient_id' => $patient->patient_id,
                    'user_type' => 2,
                    'is_return_response' => true,
                    'no_global_log' => true
                ];
                $doctor_name = DOCTOR.$value->doctor_name;
                if($value->appointment_type == 4 || $value->appointment_type == 5) {
                    $TeleConsultationFee = "0";
                    if(!empty($value->doctor_clinic_mapping_tele_fees)) {
                        $TeleConsultationFeeArr = explode(".", $value->doctor_clinic_mapping_tele_fees);
                        $TeleConsultationFee = $TeleConsultationFeeArr[0];
                    }
                    $SMSPaymentLink = "";
                    $WhatsAppPaymentLink = "";
                    if(!empty($doctor_payment_links[$value->appointment_doctor_user_id])) {
                        $SMSPaymentLink = $doctor_payment_links[$value->appointment_doctor_user_id];
                    }
                    if(!empty($payment_links_for_whatsapp[$value->appointment_doctor_user_id])) {
                        $WhatsAppPaymentLink = $payment_links_for_whatsapp[$value->appointment_doctor_user_id];
                    }
                }
                if($value->appointment_type == 1) {
                    $send_message['message'] = sprintf(lang('appointment_book_patient'), $value->patient_name, $doctor_name, date("d/m/Y", strtotime($value->appointment_date_time)) . " at " . date("h:i A", strtotime($value->appointment_date_time)), DOMAIN_URL, PATIENT_EMAIL_FROM);

                    $send_message['whatsapp_sms_body'] = sprintf(lang('wa_template_23_book_patient_appoint'), $value->patient_name, $doctor_name, date("d/m/Y", strtotime($value->appointment_date_time)) . " at " . date("h:i A", strtotime($value->appointment_date_time)), DOMAIN_URL, PATIENT_EMAIL_FROM);
                } elseif($value->appointment_type == 4) {
                    $send_message['message'] = sprintf(lang('tele_appointment_book_patient'), $value->patient_name, $doctor_name, date("d/m/Y", strtotime($value->appointment_date_time)),date("h:i A", strtotime($value->appointment_date_time)), $TeleConsultationFee, $SMSPaymentLink . " \n" . $doctor_name, "\n".$PATIENT_SHARE_LINK);

                    $send_message['whatsapp_sms_body'] = sprintf(lang('wa_template_25_book_patient_tele_appoint'), $value->patient_name, $doctor_name, date("d/m/Y", strtotime($value->appointment_date_time)) . " at " . date("h:i A", strtotime($value->appointment_date_time)), $TeleConsultationFee, $WhatsAppPaymentLink, $doctor_name, $PATIENT_SHARE_LINK);
                } elseif($value->appointment_type == 5) {
                    $send_message['message'] = sprintf(lang('video_appointment_book_patient'), $value->patient_name, $doctor_name, date("d/m/Y", strtotime($value->appointment_date_time)) . " at " . date("h:i A", strtotime($value->appointment_date_time)), $TeleConsultationFee, $SMSPaymentLink . " \n", $minutes, DOMAIN_URL, PATIENT_EMAIL_FROM);

                    $send_message['whatsapp_sms_body'] = sprintf(lang('wa_template_26_book_patient_video_appoint'), $value->patient_name, $doctor_name, date("d/m/Y", strtotime($value->appointment_date_time)) . " at " . date("h:i A", strtotime($value->appointment_date_time)), $TeleConsultationFee, $WhatsAppPaymentLink, $minutes, DOMAIN_URL, PATIENT_EMAIL_FROM);
                }
                $response = send_communication($send_message);
                if(!empty($response['sms'])) {
                    $cron_file_run_log = LOG_FILE_PATH . '/appointment_reminder_sms_log_' . date('d-m-Y') . '.txt';
                    file_put_contents($cron_file_run_log, "\n\n =================" .get_display_date_time("Y-m-d H:i:s"). "=================\n Appointment reminder before 48 Hrs\n\n " . json_encode($response['sms']) . "\n\n", FILE_APPEND | LOCK_EX);
                }
            }
        }
        /*if(!empty($update_share_link_data) && count($update_share_link_data) > 0)
            $this->Common_model->update_multiple('me_patient_share_link_log', $update_share_link_data, 'id');
        if(!empty($insert_share_link_data) && count($insert_share_link_data))
            $this->Common_model->insert_multiple('me_patient_share_link_log', $insert_share_link_data);*/
        echo "Done";
        exit;
    }

    public function share_invoice_pdf_generate($requested_data) {
        $requested_data = json_decode(base64_decode($requested_data), true);
        $appointment_id = $requested_data['appointment_id'];
        $doctor_id = $requested_data['doctor_id'];
        $billing_id_arr = $requested_data['billing_id'];
        $patient_id = $requested_data['patient_id'];
        $check_patient_appointment_sql = "
            SELECT 
                appointment_date,
                doctor.user_first_name as doctor_first_name,
                doctor.user_last_name as doctor_last_name,
                doctor.user_phone_number as doctor_phone_number,
                patient.user_id as patient_user_id,
                patient.user_email as patient_user_email,
                patient.user_first_name as patient_first_name,
                patient.user_last_name as patient_last_name,
                patient.user_phone_number as patient_phone_number,
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
                GROUP_CONCAT(DISTINCT(clinical_notes_reports_kco)) as kco,
                GROUP_CONCAT(DISTINCT(doctor_qualification_degree)) AS doctor_qualification
            FROM 
                " . TBL_APPOINTMENTS . "
            LEFT JOIN 
                " . TBL_USERS . " as doctor
            ON 
                appointment_doctor_user_id=doctor.user_id
            LEFT JOIN 
                " . TBL_USERS . " as patient
            ON 
                appointment_user_id=patient.user_id
            LEFT JOIN 
                " . TBL_CLINICAL_REPORTS . "
            ON 
                clinical_notes_reports_user_id=patient.user_id
            LEFT JOIN 
                " . TBL_DOCTOR_DETAILS . " ON doctor.user_id = doctor_detail_doctor_id
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
            WHERE
                appointment_user_id='" . $patient_id . "' AND 
                appointment_doctor_user_id='" . $doctor_id . "' AND 
                appointment_id='" . $appointment_id . "'
        ";
        $check_patient_appointment = $this->Common_model->get_single_row_by_query($check_patient_appointment_sql);
        $doctor_name = DOCTOR . $check_patient_appointment['doctor_first_name'] . ' ' . $check_patient_appointment['doctor_last_name'];
        $patient_name = $check_patient_appointment['patient_first_name'] . ' ' . $check_patient_appointment['patient_last_name'];
        $doctor_speciality = !empty($check_patient_appointment['doctor_detail_speciality']) ? $check_patient_appointment['doctor_detail_speciality'] : '-';
        $patient_phone_number = $check_patient_appointment['patient_phone_number'];
        $user_id = $check_patient_appointment['patient_user_id'];
        $patient_email = $check_patient_appointment['patient_user_email'];
        $doctor_qualification = $check_patient_appointment['doctor_qualification'];
        $clinic_name = $check_patient_appointment['clinic_name'];
        $clinic_contact_number = $check_patient_appointment['clinic_contact_number'];
        $clinic_email = $check_patient_appointment['clinic_email'];
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

        $billing_requested_data = array(
            'appointment_id' => $appointment_id,
            'doctor_id' => $doctor_id,
            'patient_id' => $patient_id
        );
        $attachment_path_array = array();
        $upload_path = DOCROOT_PATH . 'uploads/' . PATIENT_INVOICE_SHARE . '/' . $patient_id.'/';
        if (!file_exists($upload_path)) {
            mkdir($upload_path, 0777, TRUE);
            chmod($upload_path, 0777);
        }
        $address_data = [
            'address_name' => $check_patient_appointment['address_name'],
            'address_name_one' => $check_patient_appointment['address_name_one'],
            'address_locality' => $check_patient_appointment['address_locality'],
            'city_name' => $check_patient_appointment['city_name'],
            'state_name' => $check_patient_appointment['state_name'],
            'address_pincode' => $check_patient_appointment['address_pincode']
        ];
        $all_invoice_view_html = "";
        $view_data = array();
        $view_data['patient_detail'] = $patient_detail;
        $view_data['doctor_name'] = $check_patient_appointment['doctor_first_name'] . ' ' . $check_patient_appointment['doctor_last_name'];
        $view_data['doctor_qualification'] = $doctor_qualification;
        $view_data['clinic_name'] = $clinic_name;
        $view_data['clinic_contact_number'] = $clinic_contact_number;
        $view_data['clinic_email'] = $clinic_email;
        $view_data['doctor_speciality'] = $doctor_speciality;
        foreach ($billing_id_arr as $key => $billing_id) {
            $billing_requested_data['billing_id'] = $billing_id;
            $billing_data = $this->Common_model->get_billing_information_for_doctor($billing_requested_data);
            $view_data['billing_data'] = $billing_data;
            $invoice_body = $this->load->view("prints/invoice_body", $view_data, true);
            $view_html = $this->load->view("prints/invoice", ['invoice_body' => $invoice_body], true);
            $all_invoice_view_html .= $invoice_body;
            if(($key+1) != count($billing_id_arr))
                $all_invoice_view_html .= "<pagebreak>";
            if(!empty($requested_data['email'])) {
                $pdf_name = 'Invoice_' . $patient_name . '_' .$billing_data[0]['invoice_number']. '.pdf';
                /*require_once MPDF_PATH;
                $lang_code = 'en-GB';
                $mpdf = new MPDF(
                        $lang_code, 'A4', 0, 'arial', 8, 8, 35, 8, 8, 5, 'P'
                );*/
                $defaultConfig = (new \Mpdf\Config\ConfigVariables())->getDefaults();
                $fontDirs = $defaultConfig['fontDir'];
                $defaultFontConfig = (new \Mpdf\Config\FontVariables())->getDefaults();
                $fontData = $defaultFontConfig['fontdata'];
                $mpdf = new \Mpdf\Mpdf([
                        'tempDir' => DOCROOT_PATH . 'uploads',
                        'fontDir' => array_merge($fontDirs, [
                        DOCROOT_PATH.'assets/fonts'
                    ]),
                    'fontdata' => $fontData + [
                        'gotham_book' => [
                            'R' => 'Gotham-Book.ttf',
                            'I' => 'Gotham-Book.ttf',
                        ],
                    ],
                    'default_font' => 'gotham_book',
                    'mode' => 'en-GB',
                    'format' => 'A4',
                    'margin_top' => 0,
                    'margin_bottom' => 8,
                    'margin_left' => 8,
                    'margin_right' => 8,
                    'margin_header' => 8,
                    'margin_footer' => 5
                ]);
                $mpdf->useOnlyCoreFonts = true;
                $mpdf->setAutoTopMargin = true;
                $mpdf->SetDisplayMode('real');
                $mpdf->list_indent_first_level = 0;
                $mpdf->setAutoBottomMargin = 'stretch';
                $mpdf->SetHTMLHeader('
                    <table style="width:100%;border-bottom:1px solid #000">
                        <tr>
                            <td width="50%" style="text-align:left;vertical-align:top">
                             ' . $doctor_name . "<br>" . '
                             ' . $view_data['doctor_speciality'] . "<br>" . '
                             ' . $view_data['doctor_qualification'] . "<br>" . '    
                            </td>
                            <td width="50%" style="text-align:right;vertical-align:top">
                                ' . $view_data['clinic_name'] . "<br>" . '
                                ' . clinic_address($address_data) . "<br>" . '
                                ' . $view_data['clinic_contact_number'] . ", " . '
                                ' . $view_data['clinic_email'] . "<br>" . '
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
                $file_name = $billing_id . '.pdf';
                $mpdf->Output($upload_path . $file_name, 'F');
                $attachment_path = $upload_path . $file_name;
                $attachment_path_array[] = array('path' => $attachment_path, 'name' => $pdf_name);
            }
        }

        /*Invoice share on SMS*/
        if(!empty($requested_data['mobile_no'])) {
            $defaultConfig = (new \Mpdf\Config\ConfigVariables())->getDefaults();
            $fontDirs = $defaultConfig['fontDir'];
            $defaultFontConfig = (new \Mpdf\Config\FontVariables())->getDefaults();
            $fontData = $defaultFontConfig['fontdata'];
            $mpdf = new \Mpdf\Mpdf([
                    'tempDir' => DOCROOT_PATH . 'uploads',
                    'fontDir' => array_merge($fontDirs, [
                    DOCROOT_PATH.'assets/fonts'
                ]),
                'fontdata' => $fontData + [
                    'gotham_book' => [
                        'R' => 'Gotham-Book.ttf',
                        'I' => 'Gotham-Book.ttf',
                    ],
                ],
                'default_font' => 'gotham_book',
                'mode' => 'en-GB',
                'format' => 'A4',
                'margin_top' => 0,
                'margin_bottom' => 8,
                'margin_left' => 8,
                'margin_right' => 8,
                'margin_header' => 8,
                'margin_footer' => 5
            ]);
            $mpdf->useOnlyCoreFonts = true;
            $mpdf->setAutoTopMargin = true;
            $mpdf->SetDisplayMode('real');
            $mpdf->list_indent_first_level = 0;
            $mpdf->setAutoBottomMargin = 'stretch';
            $mpdf->SetHTMLHeader('
                <table style="width:100%;border-bottom:1px solid #000">
                    <tr>
                        <td width="50%" style="text-align:left;vertical-align:top">
                         ' . $view_data['doctor_name'] . "<br>" . '
                         ' . $view_data['doctor_speciality'] . "<br>" . '
                         ' . $view_data['doctor_qualification'] . "<br>" . '    
                        </td>
                        <td width="50%" style="text-align:right;vertical-align:top">
                            ' . $view_data['clinic_name'] . "<br>" . '
                            ' . clinic_address($address_data) . "<br>" . '
                            ' . $view_data['clinic_contact_number'] . ", " . '
                            ' . $view_data['clinic_email'] . "<br>" . '
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
            $view_html = $this->load->view("prints/invoice", ['invoice_body' => $all_invoice_view_html], true);
            $mpdf->WriteHTML($view_html);
            $share_file_name = $requested_data['appointment_id'] . '_'. time() . '.pdf';
            $file_share_name = $share_file_name;
            $mpdf->Output($upload_path . $file_share_name, 'F');
            if(IS_S3_UPLOAD)
                upload_to_s3($upload_path . $file_share_name, PATIENT_INVOICE_SHARE.'/'.$requested_data['patient_id'].'/'.$share_file_name);
            if(!IS_SERVER_UPLOAD && file_exists($upload_path . $file_share_name))
                unlink($upload_path . $file_share_name);
            $pdf_url = PATIENT_INVOICE_SHARE.'/'.$requested_data['patient_id'].'/'.$share_file_name;
            $share_data = array(
                'doctor_id' => $requested_data['doctor_id'],
                'patient_id' => $requested_data['patient_id'],
                'appointment_id' => $requested_data['appointment_id'],
                'file_name' => $share_file_name,
                'file_url' => get_file_json_detail($pdf_url),
                'created_at' => date('Y-m-d H:i:s')
            );
            $last_id = $this->Common_model->insert('me_patient_record_share', $share_data);
            $download_link = create_shorturl(DOMAIN_URL.'inv/' . base64_encode(json_encode($last_id)));
            $send_message['phone_number'] = $requested_data['mobile_no'];
            $send_message['message'] = sprintf(lang('patient_invoice_shared_by_doctor'), $patient_name, $doctor_name, short_clinic_name($clinic_name), get_display_date_time("d/m/Y h:i A"), $download_link);
            $send_message['whatsapp_sms_body'] = sprintf(lang('wa_patient_invoice_shared_by_doctor_35'), $patient_name, $doctor_name, $clinic_name, get_display_date_time("d/m/Y h:i A"), $download_link);
            $send_message['doctor_id'] = $requested_data['doctor_id'];
            $send_message['user_type'] = 2;
            $send_message['patient_id'] = $requested_data['patient_id'];
            $send_message['is_sms_count'] = true;
            $send_message['is_check_sms_credit'] = $GLOBALS['ENV_VARS']['CHECK_SMS_CREDIT']['PRESCRIPTION_SHARE'];
            send_communication($send_message);
        }
        /*END Invoice share on SMS*/

        if (!empty($attachment_path_array) && count($attachment_path_array) > 0) {
            $this->load->model('Emailsetting_model');
            $email_template_data = $this->Emailsetting_model->get_emailtemplate_by_id(20);
            $email = $requested_data['email'];
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
            $this->patient_send_attachment_email(array($email => $email), $subject, $message, $attachment_path_array);
            foreach ($attachment_path_array as $key => $value) {
                if(!IS_SERVER_UPLOAD)
                    unlink($value['path']);
            }
        } else {
            return 0;
        }
        return 1;
    }

    public function forward_link($id='', $param0='') {
        if(!empty($id)) {
            $id = json_decode(base64_decode($id));
            $row = $this->Common_model->get_single_row('me_sms_short_link_template', 'template_link', ['id' => $id]);
            if(!empty($row['template_link'])) {
                $url = $row['template_link'];
                if(!empty($param0))
                    $url = str_replace('param0', $param0, $url);
                $query_arr = $this->input->get();
                $query_string = "";
                if(!empty($query_arr)) {
                    foreach ($query_arr as $key => $value) {
                        if(!empty($query_string))
                            $query_string .= '&';
                        $query_string .= $key.'='.$value;
                    }
                }
                if(!empty($query_string)) {
                    $url .= '?'.$query_string;
                }
                redirect($url);
            } else {
                redirect(DOMAIN_URL);
            }
        }
        exit;
    }
    
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
            $sendMessage->setBody($doc->saveHTML(), "text/html");
            $mailer->send($sendMessage);
        } catch (\Swift_TransportException $e) {
            $response = $e->getMessage();
            file_put_contents($this->email_log_file, "\n\n ======== swift send_email ex: " . json_encode($response) . "===== \n\n", FILE_APPEND | LOCK_EX);
        } catch (Exception $e) {
            $response = $e->getMessage();
            file_put_contents($this->email_log_file, "\n\n ======== send_email ex: " . json_encode($response) . "===== \n\n", FILE_APPEND | LOCK_EX);
        }
    }

    public function patient_send_email($to_email_address, $subject, $message) {
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
            $sendMessage->setBody($doc->saveHTML(), "text/html");
            $mailer->send($sendMessage);
        } catch (\Swift_TransportException $e) {
            $response = $e->getMessage();
            file_put_contents($this->email_log_file, "\n\n ======== swift send_email ex: " . json_encode($response) . "===== \n\n", FILE_APPEND | LOCK_EX);
        } catch (Exception $e) {
            $response = $e->getMessage();
            file_put_contents($this->email_log_file, "\n\n ======== send_email ex: " . json_encode($response) . "===== \n\n", FILE_APPEND | LOCK_EX);
        }
    }

    /**
     * 
     * @param type $to_email_address
     * @param type $subject
     * @param type $message
     * @param type $attachment1
     * @param type $attachment2
     */
    public function send_attachment_email($to_email_address, $subject, $message, $attachment1 = array(), $attachment2 = array()) {

        try {
            require_once SWIFT_MAILER_PATH;
            $transport = Swift_SmtpTransport::newInstance(EMAIL_HOST, EMAIL_PORT, EMAIL_CERTIFICATE)
                    ->setUsername(EMAIL_USER)
                    ->setPassword(EMAIL_PASS);
            $mailer = Swift_Mailer::newInstance($transport);
            $sendMessage = Swift_Message::newInstance()
                    ->setSubject($subject)
                    ->setTo($to_email_address)
                    ->setFrom(array(EMAIL_FROM => APP_NAME))
                    ->setBody($message, 'text/html');

            if(!empty($attachment1[0]['path'])){
                foreach ($attachment1 as $key => $value) {
                    $sendMessage->attach(Swift_Attachment::fromPath($value['path'])->setFilename($value['name']));
                }
            } elseif (!empty($attachment1)) {
                $sendMessage->attach(Swift_Attachment::fromPath($attachment1[0])->setFilename($attachment1[1]));
            }

            if (!empty($attachment2)) {
                $sendMessage->attach(Swift_Attachment::fromPath($attachment2[0])->setFilename($attachment2[1]));
            }

            if (!$mailer->send($sendMessage)) {
                file_put_contents($this->email_log_file, "\n\n ======== send by php mail ===== \n\n", FILE_APPEND | LOCK_EX);
                $this->send_simple_attach_email($to_email_address, $subject, $message, $attachment1, $attachment2);
            }
        } catch (\Swift_TransportException $e) {
            $response = $e->getMessage();
            file_put_contents($this->email_log_file, "\n\n ======== response" . json_encode($response) . "===== \n\n", FILE_APPEND | LOCK_EX);
            //$this->send_simple_attach_email($to_email_address, $subject, $message, $attachment1, $attachment2);
        } catch (Exception $e) {
            $response = $e->getMessage();
            file_put_contents($this->email_log_file, "\n\n ======== response" . json_encode($response) . "===== \n\n", FILE_APPEND | LOCK_EX);
            //$this->send_simple_attach_email($to_email_address, $subject, $message, $attachment1, $attachment2);
        }
    }

    public function patient_send_attachment_email($to_email_address, $subject, $message, $attachment1 = array(), $attachment2 = array()) {

        try {
            require_once SWIFT_MAILER_PATH;
            $transport = Swift_SmtpTransport::newInstance(PATIENT_EMAIL_HOST, PATIENT_EMAIL_PORT, EMAIL_CERTIFICATE)
                    ->setUsername(PATIENT_EMAIL_USER)
                    ->setPassword(PATIENT_EMAIL_PASS);
            $mailer = Swift_Mailer::newInstance($transport);
            $sendMessage = Swift_Message::newInstance()
                    ->setSubject($subject)
                    ->setTo($to_email_address)
                    ->setFrom(array(PATIENT_EMAIL_FROM => APP_NAME))
                    ->setBody($message, 'text/html');

            if(!empty($attachment1[0]['path'])){
                foreach ($attachment1 as $key => $value) {
                    $sendMessage->attach(Swift_Attachment::fromPath($value['path'])->setFilename($value['name']));
                }
            } elseif (!empty($attachment1)) {
                $sendMessage->attach(Swift_Attachment::fromPath($attachment1[0])->setFilename($attachment1[1]));
            }

            if (!empty($attachment2)) {
                $sendMessage->attach(Swift_Attachment::fromPath($attachment2[0])->setFilename($attachment2[1]));
            }

            if (!$mailer->send($sendMessage)) {
                file_put_contents($this->email_log_file, "\n\n ======== send by php mail ===== \n\n", FILE_APPEND | LOCK_EX);
                $this->send_simple_attach_email($to_email_address, $subject, $message, $attachment1, $attachment2);
            }
        } catch (\Swift_TransportException $e) {
            $response = $e->getMessage();
            file_put_contents($this->email_log_file, "\n\n ======== response" . json_encode($response) . "===== \n\n", FILE_APPEND | LOCK_EX);
            //$this->send_simple_attach_email($to_email_address, $subject, $message, $attachment1, $attachment2);
        } catch (Exception $e) {
            $response = $e->getMessage();
            file_put_contents($this->email_log_file, "\n\n ======== response" . json_encode($response) . "===== \n\n", FILE_APPEND | LOCK_EX);
            //$this->send_simple_attach_email($to_email_address, $subject, $message, $attachment1, $attachment2);
        }
    }
}
