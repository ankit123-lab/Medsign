<?php

class Cron extends CI_Controller {
    public $log_file;
    public function __construct() {
        parent::__construct();
        $this->log_file = LOG_FILE_PATH . 'log_' . date('d-m-Y') . ".txt";
    }
    public function copy_clinical_report_images($requested_data) {

        $requested_data = json_decode(base64_decode($requested_data), true);
        //get the images of the clinical notes
        $column = 'clinic_notes_reports_images_url,
                   clinic_notes_reports_images_type';
        $where = array(
            'clinic_notes_reports_images_reports_id' => $requested_data['existing_id'],
            'clinic_notes_reports_images_status' => 1
        );
        $get_images = $this->Common_model->get_all_rows(TBL_CLINICAL_NOTES_REPORT_IMAGE, $column, $where);
        
        $inserted_id = $requested_data['inserted_id'];
        $folder_path = UPLOAD_REL_PATH . "/" . CILINICAL_REPORT . "/" . $inserted_id;

        if (!file_exists($folder_path)) {
            mkdir($folder_path, 0777, TRUE);
            chmod($folder_path, 0777);
        }

        if (!empty($get_images)) {
            foreach ($get_images as $image) {
                if (!empty($image['clinic_notes_reports_images_url'])) {
                    $file_get_contents = file_get_contents(get_file_full_path($image['clinic_notes_reports_images_url']));
                    $get_extension = pathinfo(parse_url(get_file_full_path($image['clinic_notes_reports_images_url']), PHP_URL_PATH), PATHINFO_EXTENSION);
                    $file_name = $inserted_id . "_" . uniqid() . "." . $get_extension;
                    file_put_contents($folder_path . "/" . $file_name, $file_get_contents);
                    $source = $folder_path . "/" . $file_name;
                    chmod($source, 0777);
                    include_once BUCKET_HELPER_PATH;
                    uploadimage($source, CILINICAL_REPORT . "/" . $inserted_id . "/" . $file_name);
                    $reports_images_url = get_file_json_detail(CILINICAL_REPORT . "/" . $inserted_id . "/" . $file_name);
                    $report_image[] = array(
                        'clinic_notes_reports_images_reports_id' => $inserted_id,
                        'clinic_notes_reports_images_url' => $reports_images_url,
                        'clinic_notes_reports_images_type' => $image['clinic_notes_reports_images_type'],
                        'clinic_notes_reports_images_size' => get_file_size(get_file_full_path($reports_images_url)),
                        'clinic_notes_reports_images_created_at' => date('Y-m-d H:i:s', time())
                    );
                }
            }
            
            exec("rm -rf " . DOCROOT_PATH . 'uploads/' . CILINICAL_REPORT . "/" . $inserted_id);
            $this->Common_model->insert_multiple(TBL_CLINICAL_NOTES_REPORT_IMAGE, $report_image);
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
                file_put_contents($this->log_file, "\n\n ======== send by php mail ===== \n\n", FILE_APPEND | LOCK_EX);
                $this->send_simple_attach_email($to_email_address, $subject, $message, $attachment1, $attachment2);
            }
        } catch (\Swift_TransportException $e) {
            $response = $e->getMessage();
            file_put_contents($this->log_file, "\n\n ======== response" . json_encode($response) . "===== \n\n", FILE_APPEND | LOCK_EX);
            //$this->send_simple_attach_email($to_email_address, $subject, $message, $attachment1, $attachment2);
        } catch (Exception $e) {
            $response = $e->getMessage();
            file_put_contents($this->log_file, "\n\n ======== response" . json_encode($response) . "===== \n\n", FILE_APPEND | LOCK_EX);
            //$this->send_simple_attach_email($to_email_address, $subject, $message, $attachment1, $attachment2);
        }
    }

    /**
     * 
     * @param type $to_email_address
     * @param type $subject
     * @param type $message
     * @param type $attachment1
     * @param type $attachment2
     * @return boolean
     */
    public function send_simple_attach_email($to_email_address, $subject, $message, $attachment1 = array(), $attachment2 = array()) {
        try {
            $from = APP_NAME . "<" . EMAIL_USER . ">";
            $mime_boundary = "==Multipart_Boundary_x" . md5(mt_rand()) . "x";
            $headers = "From: $from\r\n" .
                    "MIME-Version: 1.0\r\n" .
                    "Content-Type: multipart/mixed;\r\n" .
                    " boundary=\"{$mime_boundary}\"";

            $message = "This is a multi-part message in MIME format.\n\n" .
                    "--{$mime_boundary}\n" .
                    "Content-Type: text/html; charset=\"utf-8\"\n" .
                    "Content-Transfer-Encoding: 7bit\n\n" .
                    $message . "\n\n";

            $filename_list = array($attachment1[0], $attachment2[0]);

            foreach ($filename_list as $tmp_name) {
                $file = fopen($tmp_name, 'rb');
                $data = fread($file, filesize($tmp_name));
                fclose($file);
                $data = chunk_split(base64_encode($data));
                $name = basename($tmp_name);
                $type = filetype($tmp_name);

                $message .= "--{$mime_boundary}\n" .
                        "Content-Type: {$type};\n" .
                        " name=\"{$name}\"\n" .
                        "Content-Disposition: attachment;\n" .
                        " filename=\"{$name}\"\n" .
                        "Content-Transfer-Encoding: base64\n\n" .
                        $data . "\n\n";
            }

            $message.="--{$mime_boundary}--\n";

            foreach ($to_email_address as $email => $name) {
                @mail($email, $subject, $message, $headers);
            }
            return true;
        } catch (Exception $e) {
            $response = $e->getMessage();
            file_put_contents($this->log_file, "\n\n ======== response" . json_encode($response) . "===== \n\n", FILE_APPEND | LOCK_EX);
            return FALSE;
        }
    }

    public function generate_payment_receipt($payment_id) {
        if(empty($payment_id)) {
            exit;
        }
        require_once MPDF_PATH;
        $this->load->model('Emailsetting_model');
        $this->load->model('subscription_model');
        $paymet_detail = $this->subscription_model->get_doctor_payment_details_by_payment_id($payment_id);
        $clinic_details = $this->subscription_model->get_doctor_clinic_detail($paymet_detail->user_id);
        $global_setting = $this->subscription_model->get_global_setting();
        $view_data = array();

        $view_data['paymet_detail'] = $paymet_detail;
        $view_data['global_setting'] = array_column($global_setting, 'global_setting_value', 'global_setting_key');
        $view_data['is_apply_igst'] = true;
        if(!empty($clinic_details->address_state_id) && $clinic_details->address_state_id == $view_data['global_setting']['gst_registration_state_id']) {
            $view_data['is_apply_igst'] = false;
        }
        if($paymet_detail->detail_type == 'sms') {
            $email_template_data = $this->Emailsetting_model->get_emailtemplate_by_id(49);
        } elseif($paymet_detail->detail_type == 'teleconsult_minutes') {
            $email_template_data = $this->Emailsetting_model->get_emailtemplate_by_id(52);
        } else {
            $email_template_data = $this->Emailsetting_model->get_emailtemplate_by_id(38);
        }
        $parse_arr = array(
            '{SupportEmail}' => $email_template_data['email_static_data']['email_id'],
            '{ContactNumber}' => $email_template_data['email_static_data']['contact_number'],
            '{ExpiryDate}' => date('d/m/Y', strtotime($paymet_detail->plan_end_date)),
        );
        if($paymet_detail->detail_type == 'sms' || $paymet_detail->detail_type == 'teleconsult_minutes') {
            $view_data['global_setting']['payment_receipt_note'] = "For more details please contact us on ".$email_template_data['email_static_data']['email_id']." or ".$email_template_data['email_static_data']['contact_number'].".";
        } else {
            $view_data['global_setting']['payment_receipt_note'] = nl2br(replace_values_in_string($parse_arr, $view_data['global_setting']['payment_receipt_note']));
        }
        $lang_code = 'en-GB';
        $mpdf = new MPDF(
                $lang_code, 'A4', 0, 'arial', 8, 8, 55, 8, 8, 5, 'P'
        );
        $mpdf->useOnlyCoreFonts = true;
        $mpdf->SetDisplayMode('real');
        $mpdf->list_indent_first_level = 0;
        $mpdf->setAutoBottomMargin = 'stretch';
        $header_html = $this->load->view("prints/payment_receipt_header", $view_data, true);
        $mpdf->SetHTMLHeader($header_html);
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
        $view_html = $this->load->view("prints/payment_receipt", $view_data, true);
        
        $mpdf->WriteHTML($view_html);
        // $mpdf->Output('payment_receipt_'.$paymet_detail->invoice_no.'.pdf', 'D');
        // echo $mpdf->Output();die;
        $upload_path = DOCROOT_PATH . 'uploads/' . PAYMENT_RECEIPT_FOLDER . '/'.$paymet_detail->user_id.'/';
        
        if (!file_exists($upload_path)) {
            mkdir($upload_path, 0777, TRUE);
            chmod($upload_path, 0777);
        }
        
        $file_name = 'payment_receipt_'.$paymet_detail->invoice_no.'.pdf';
        $mpdf->Output($upload_path . $file_name, 'F');
        $attachment_path = $upload_path . $file_name;
        upload_to_s3($attachment_path, PAYMENT_RECEIPT_FOLDER.'/'.$paymet_detail->user_id.'/'.$file_name);
        $receipt_url = get_file_json_detail(PAYMENT_RECEIPT_FOLDER.'/'.$paymet_detail->user_id.'/'.$file_name);
        $this->subscription_model->update_doctor_payment_detail_payment_id($payment_id,['receipt_url' => $receipt_url]);
        $doctor_name = DOCTOR . ' ' . $paymet_detail->user_first_name . ' ' . $paymet_detail->user_last_name;
        $email = $paymet_detail->user_email;
        
        $datetime = get_display_date_time('Y-m-d H:i:s');
        $parse_arr = array(
            '{DoctorName}' => $doctor_name,
            '{WebUrl}' => DOMAIN_URL,
            '{AppName}' => APP_NAME,
            '{Date}' => date('d/m/Y', strtotime($datetime)),
            '{ExpiryDate}' => date('d/m/Y', strtotime($paymet_detail->plan_end_date)),
            '{MailContactNumber}' => $email_template_data['email_static_data']['contact_number'],
            '{MailEmailAddress}' => $email_template_data['email_static_data']['email_id'],
            '{MailCompanyName}' => $email_template_data['email_static_data']['company_name'],
            '{CopyRightsYear}' => date('Y')
        );

        $message = replace_values_in_string($parse_arr, $email_template_data['email_template_message']);
        $subject = $email_template_data['email_template_subject'];

        $attachment_path_array = array($attachment_path, $file_name);
        $this->send_attachment_email(array($email => $email), $subject, $message, $attachment_path_array);
        if(!IS_SERVER_UPLOAD)
            unlink($attachment_path);
        if(!empty($GLOBALS['ENV_VARS']['PAYMENT_NOTIFICATION_EMAIL'])) {
            if($paymet_detail->detail_type == 'sms') {
                $email_template_data = $this->Emailsetting_model->get_emailtemplate_by_id(50);
            } elseif($paymet_detail->detail_type == 'teleconsult_minutes') {
                $email_template_data = $this->Emailsetting_model->get_emailtemplate_by_id(53);
            } else {
                $email_template_data = $this->Emailsetting_model->get_emailtemplate_by_id(39);
            }
            $parse_arr = array(
                '{DoctorName}' => $doctor_name,
                '{PaidAmount}' => number_format($paymet_detail->paid_amount,2),
                '{SubPlanName}' => $paymet_detail->sub_plan_name,
                '{TotalCredits}' => $paymet_detail->sub_plan_name,
                '{TeleMinuteCredits}' => $paymet_detail->sub_plan_name . ' Minutes',
                '{WebUrl}' => DOMAIN_URL,
                '{AppName}' => APP_NAME,
                '{MailContactNumber}' => $email_template_data['email_static_data']['contact_number'],
                '{MailEmailAddress}' => $email_template_data['email_static_data']['email_id'],
                '{MailCompanyName}' => $email_template_data['email_static_data']['company_name'],
                '{CopyRightsYear}' => date('Y')
            );
            $message = replace_values_in_string($parse_arr, $email_template_data['email_template_message']);
            $subject = $email_template_data['email_template_subject'];
            $this->send_attachment_email(explode(',', $GLOBALS['ENV_VARS']['PAYMENT_NOTIFICATION_EMAIL']), $subject, $message);
        }
    }

    public function download_invoice($payment_id = '') {
        if(!empty($_SERVER['HTTP_REFERER']) && !empty($payment_id)) {
            $this->load->model('subscription_model');
            $paymet_detail = $this->subscription_model->get_doctor_payment_details_by_payment_id($payment_id);
            $paymet_detail->receipt_url = get_file_full_path($paymet_detail->receipt_url);
            if(!empty($paymet_detail->receipt_url)) {
                $arr = explode('/', $paymet_detail->receipt_url);
                $file_name = end($arr);
                header('Content-Type: application/octet-stream');
                header("Content-Transfer-Encoding: Binary"); 
                header("Content-disposition: attachment; filename=\"".$file_name."\""); 
                readfile($paymet_detail->receipt_url);
            }
        }
        exit;
    }

    public function send_patient_prescription_mail($prescription_email_data) {
        $this->load->model('User_model');
        $this->load->model('Clinic_model');
        $this->load->model('Emailsetting_model');
        $this->load->model('Appointments_model');
        $prescription_email_data = json_decode(base64_decode($prescription_email_data), true);
        $patient_id     = $prescription_email_data['patient_id'];
        $doctor_id      = $prescription_email_data['doctor_id'];
        $clinic_id      = $prescription_email_data['clinic_id'];
        $appointment_id = $prescription_email_data['appointment_id'];
        $appointmentData = $this->Appointments_model->get_appointment_detail_byid($appointment_id);
        $patient_mail_flag = isset($appointmentData['patient_mail_flag']) ? $appointmentData['patient_mail_flag'] : '';
        if ($patient_mail_flag == 0) {
            $allUserData = $this->User_model->get_details_by_ids([$patient_id,$doctor_id]);
            if(!empty($allUserData)){
                $ptKy = array_search($patient_id, array_column($allUserData,'user_id'));
                $getPatientDetail = (isset($allUserData[$ptKy]) && !empty($allUserData[$ptKy])) ? $allUserData[$ptKy] : [];
                $patientName = $getPatientDetail['user_first_name'] . ' ' . $getPatientDetail['user_last_name'];
                $email       = $getPatientDetail['user_email'];
                
                $dcKy = array_search($doctor_id, array_column($allUserData,'user_id'));
                $getDoctorDetail = (isset($allUserData[$dcKy]) && !empty($allUserData[$dcKy])) ? $allUserData[$dcKy] : [];
                $doctorName      = $getDoctorDetail['user_first_name'] . ' ' . $getDoctorDetail['user_last_name'];
            
                $getclinicDetail = $this->Clinic_model->get_clinic_detail($clinic_id);
                $clinic_name = $getclinicDetail['clinic_name'];
                $email_template_data = $this->Emailsetting_model->get_emailtemplate_by_id(PRESCRIPTION_TEMPLATE);
                if(!empty($email_template_data)){
                    $parse_arr = array(
                        '{Email}'               => $email,
                        '{PatientName}'         => $patientName,
                        '{DoctorName}'          => $doctorName,
                        '{ClinicName}'          => $clinic_name,
                        '{PatientWebLink}'      => '<a href="'.DOMAIN_URL.'patient">MedSign Web</a>',
                        '{AppName}'             => APP_NAME,
                        '{MailContactNumber}'   => $email_template_data['email_static_data']['contact_number'],
                        '{MailEmailAddress}'    => $email_template_data['email_static_data']['email_id'],
                        '{MailCompanyName}'     => $email_template_data['email_static_data']['company_name'],
                        '{CopyRightsYear}'      => date('Y')
                    );
                    $message = replace_values_in_string($parse_arr, $email_template_data['email_template_message']);
                    $columns = 'u.user_email,u.user_email_verified';
                    $parent_members = $this->User_model->get_linked_family_members($patient_id, $columns);
                    $patient_email_id_arr = array();
                    foreach ($parent_members as $parent_member) {
                        if($parent_member->user_email_verified == 1) {
                            $patient_email_id_arr[$parent_member->user_email] = $parent_member->user_email;
                        }
                    }
                    if(!empty($email)){
                        $patient_email_id_arr[$email] = $email;
                    }
                    $subject = $email_template_data['email_template_subject'];
                    if(count($patient_email_id_arr) > 0) {
                        // $this->send_attachment_email($patient_email_id_arr, $subject, $message);
                        /*$update_mail_flag = array('patient_mail_flag' => 1);
                        $update_mail_flag_where = array('appointment_id' => $appointment_id);
                        $this->Common_model->update(TBL_APPOINTMENTS, $update_mail_flag, $update_mail_flag_where);*/
                    }
                }
            }
        }
    }

    public function send_promotional_sms($id) {
        $where = [
            'patient_communication_id' => $id, 
            'patient_communication_status' => 1,
            'patient_communication_by' => 1
        ];
        $columns = "patient_communication_id,patient_communication_doctor_id,patient_communication_message,patient_communication_type_id,communication_sms_template_is_promotional";
        $join = ['me_communication_sms_templates' => 'communication_sms_template_id=patient_communication_template_id'];
        $communication_data = $this->Common_model->get_single_row('me_patients_communication', $columns, $where, $join);
        if(!empty($communication_data['patient_communication_type_id']) && !empty($communication_data['patient_communication_message'])) {
            $sql_query = "SELECT 
                user_id,
                CONCAT(user_first_name, ' ', user_last_name) as patient_name, 
                user_phone_number 
                FROM me_users 
                WHERE user_status = 1 
                AND user_type = 1 
                AND user_id IN(".$communication_data['patient_communication_type_id'].")";
                $patients = $this->Common_model->get_all_rows_by_query($sql_query);

                if(!empty($patients)) {
                    $insert_communication_data = [];
                    foreach ($patients as $key => $value) {
                        $send_message = array(
                            'is_return_response' => true,
                            'is_promotional' => false,
                            'phone_number' => DEFAULT_COUNTRY_CODE . $value['user_phone_number'],
                            'message' => $communication_data['patient_communication_message']
                        );
                        if($communication_data['communication_sms_template_is_promotional'] == 1)
                            $send_message['is_promotional'] = true;
                        
                        $response = send_message_by_vibgyortel($send_message);
                        if(!empty($response['status']) && $response['status'] == 'success') {
                            $status = 1;
                        } else {
                            $status = 3;
                        }
                        $insert_communication_data[] = array(
                            'communication_pt_id' => $communication_data['patient_communication_id'],
                            'communication_sender_id' => $communication_data['patient_communication_doctor_id'],
                            'communication_receiver_id' => $value['user_id'],
                            'communication_message' => json_encode($response),
                            'communication_time' => date('Y-m-d H:i:s'),
                            'communication_delivery_status' => $status, 
                            'communication_sms_credit' => 1, 
                            'communication_created_at' => date('Y-m-d H:i:s')
                        );
                    }
                    
                    if(count($insert_communication_data) > 0)
                        $this->Common_model->insert_multiple('me_communications', $insert_communication_data);
                }
        }
        exit;
    }

}
