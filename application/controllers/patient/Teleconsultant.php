<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
use OpenTok\OpenTok;
class Teleconsultant extends MY_Controller {

    public function __construct() {

        parent::__construct();
        $this->load->library('session');
        $this->load->library('patient_auth');
        $this->load->model('patient_model','patient');
        $this->load->library('form_validation');
    }

    public function tele_call_url($id) {
        $id = trim($id, '@');
        $appointment_id = encrypt_decrypt($id, 'decrypt');
        $where_array = array(
            'appointment_id' => $appointment_id
        );
        $get_appointment_data = $this->Common_model->get_single_row('me_appointments', 'appointment_id,appointment_user_id,appointment_doctor_user_id', $where_array);
        if(!empty($get_appointment_data)) {
            $requested_data = [
                'a_id' => $get_appointment_data['appointment_id'],
                'd_id' => $get_appointment_data['appointment_doctor_user_id'],
                'p_id' => $get_appointment_data['appointment_user_id']
            ];
            $url = DOMAIN_URL . 'patient/videocall/'.encrypt_decrypt(json_encode($requested_data), 'encrypt');
            redirect($url);
        } else {
            redirect('patient');
        }
    }

    public function videocall($requested_data) {
        $view_data = array();
        $data_arr = json_decode(encrypt_decrypt($requested_data, 'decrypt'), true);
        if(!empty($data_arr['a_id']) && !empty($data_arr['d_id']) && !empty($data_arr['p_id'])) {
            $where_array = array(
                'appointment_id' => $data_arr['a_id']
            );
            $get_appointment_data = $this->Common_model->get_appointment_data_by_id($data_arr['a_id']);
            if(empty($get_appointment_data)) {
                $this->session->set_flashdata('error', 'This appointment is cancelled');
                redirect('patient/call_end');
            } elseif($get_appointment_data['appointment_type'] != 5){
                $this->session->set_flashdata('error', 'This appointment type is not video call');
                redirect('patient/call_end');
            } elseif(empty($get_appointment_data['call_end_date_time']) && $get_appointment_data['appointment_type'] == 5) {
                if($get_appointment_data['appointment_date'] < get_display_date_time("Y-m-d")){
                    $this->session->set_flashdata('error', 'This appointment is past. So you did not connect call.');
                    redirect('patient/call_end');
                }
                $view_data['breadcrumbs'] = "Teleconsultant Call";
                $view_data['page_title'] = "Teleconsultant Call";
                $view_data['doctor_id'] = $data_arr['d_id'];
                $view_data['patient_id'] = $data_arr['p_id'];
                $view_data['appointment_id'] = $data_arr['a_id'];
                $view_data['apiKey'] = $GLOBALS['ENV_VARS']['VIDEO_CONF_KEY'];
                $view_data['isVideoCallActive'] = true;
                $this->load->view('patient/videocall_view', $view_data);
            } else {
                $this->session->set_flashdata('error', 'Your call is ended');
                redirect('patient/call_end');
            }
        } else {
            redirect('patient');
        }
    }

    public function call_end() {
        $view_data = array();
        $view_data['breadcrumbs'] = "Teleconsultant Call End";
        $view_data['page_title'] = "Teleconsultant Call End";
        $this->load->view('patient/call_end_view', $view_data);
    }

    public function get_video_conf_token() {
        $this->form_validation->set_error_delimiters('', '');
        $this->form_validation->set_rules('doctor_id', 'doctor id', 'required|trim');
        $this->form_validation->set_rules('patient_id', 'patient id', 'required|trim');
        $this->form_validation->set_rules('appointment_id', 'appointment id', 'required|trim');
        if ($this->form_validation->run() == FALSE) {
            $errors = validation_errors();
            echo json_encode(['status' => false,'error' => nl2br($errors)]);
        } else {
            $doctor_id = set_value('doctor_id');
            $patient_id = set_value('patient_id');
            $appointment_id = set_value('appointment_id');
            $requested_data = array(
                'doctor_id' => $doctor_id,
                'patient_id' => $patient_id,
                'appointment_id' => $appointment_id,
            );
            if(!$this->check_appointment_belongs($requested_data)) {
                $errors = validation_errors();
                echo json_encode(['status' => false,'error' => 'Invalid appointment id']);
                exit;
            }
            $where_array = array(
                'doctor_id' => $doctor_id,
                'patient_id' => $patient_id,
            );
            $get_token_data = $this->Common_model->get_single_row('me_video_conf_token', 'session_id,token_id', $where_array);
            if(!empty($get_token_data['session_id']) && !empty($get_token_data['token_id'])) {
                 $response = [
                    'session_id' => $get_token_data['session_id'],
                    'token_id' => $get_token_data['token_id']
                ];
            } else {
                $apiObj = new OpenTok($GLOBALS['ENV_VARS']['VIDEO_CONF_KEY'], $GLOBALS['ENV_VARS']['VIDEO_CONF_SECRET']);
                $session = $apiObj->createSession();
                $session_id = $session->getSessionId();
                $token = $apiObj->generateToken($session_id);
                if(!empty($get_token_data)) {
                    $update_data = [
                        'session_id' => $session_id,
                        'token_id' => $token,
                        'updated_at' => $this->utc_time_formated
                    ];
                    $this->Common_model->update('me_video_conf_token', $update_data, $where_array);
                } else {
                    $insert_data = [
                        'doctor_id' => $doctor_id,
                        'patient_id' => $patient_id,
                        'session_id' => $session_id,
                        'token_id' => $token,
                        'created_at' => $this->utc_time_formated
                    ];
                    $this->Common_model->insert('me_video_conf_token', $insert_data);
                }
                $response = [
                    'session_id' => $session_id,
                    'token_id' => $token
                ];
            }
            $call_history_data = $this->Common_model->get_single_row('me_teleconsultant_call_history', 'appointment_id,call_start_date_time,patient_start_date_time', ['appointment_id' => $appointment_id]);
            if(!empty($call_history_data)) {
                $call_history_update_data = [
                    'updated_at' => $this->utc_time_formated,
                ];
                if(empty($call_history_data['patient_start_date_time']))
                    $call_history_update_data['patient_start_date_time'] = $this->utc_time_formated;
                if(empty($call_history_data['call_start_date_time']))
                    $call_history_update_data['call_start_date_time'] = $this->utc_time_formated;
                if(empty($call_history_data['patient_start_date_time']) || empty($call_history_data['call_start_date_time']))
                    $this->Common_model->update('me_teleconsultant_call_history', $call_history_update_data, ['appointment_id' => $appointment_id]);
            } else {
                $call_history_insert_data = [
                    'appointment_id' => $appointment_id,
                    'doctor_id' => $doctor_id,
                    'patient_id' => $patient_id,
                    'call_start_date_time' => $this->utc_time_formated,
                    'patient_start_date_time' => $this->utc_time_formated,
                    'created_at' => $this->utc_time_formated,
                ];
                $this->Common_model->insert('me_teleconsultant_call_history', $call_history_insert_data);
            }
            $response['status'] = true;
            echo json_encode($response);
        }
    }

    public function end_video_conf_call() {
        $this->form_validation->set_error_delimiters('', '');
        $this->form_validation->set_rules('doctor_id', 'doctor id', 'required|trim');
        $this->form_validation->set_rules('patient_id', 'patient id', 'required|trim');
        $this->form_validation->set_rules('appointment_id', 'appointment id', 'required|trim');
        if ($this->form_validation->run() == FALSE) {
            $errors = validation_errors();
            echo json_encode(['status' => false,'error' => nl2br($errors)]);
        } else {
            $doctor_id = set_value('doctor_id');
            $patient_id = set_value('patient_id');
            $appointment_id = set_value('appointment_id');
            $requested_data = array(
                'doctor_id' => $doctor_id,
                'patient_id' => $patient_id,
                'appointment_id' => $appointment_id,
            );
            if(!$this->check_appointment_belongs($requested_data)) {
                $errors = validation_errors();
                echo json_encode(['status' => false,'error' => 'Invalid appointment id']);
                exit;
            }
            $where_array = array(
                'doctor_id' => $doctor_id,
                'patient_id' => $patient_id,
            );
            $this->Common_model->delete_data('me_video_conf_token', $where_array);
            $call_history_update_data = [
                'call_end_date_time' => $this->utc_time_formated,
                'updated_at' => $this->utc_time_formated,
            ];
            $this->Common_model->update('me_teleconsultant_call_history', $call_history_update_data, ['appointment_id' => $appointment_id]);
            $response = [];
            $response['status'] = true;
            $response['msg'] = 'Your call ended';
            echo json_encode($response);
        }
    }

    public function update_connection_id() {
        $this->form_validation->set_error_delimiters('', '');
        $this->form_validation->set_rules('doctor_id', 'doctor id', 'required|trim');
        $this->form_validation->set_rules('patient_id', 'patient id', 'required|trim');
        $this->form_validation->set_rules('appointment_id', 'appointment id', 'required|trim');
        $this->form_validation->set_rules('connection_id', 'connection id', 'required|trim');
        if ($this->form_validation->run() == FALSE) {
            $errors = validation_errors();
            echo json_encode(['status' => false,'error' => nl2br($errors)]);
        } else {
            $doctor_id = set_value('doctor_id');
            $patient_id = set_value('patient_id');
            $appointment_id = set_value('appointment_id');
            $connection_id = set_value('connection_id');
            $requested_data = array(
                'doctor_id' => $doctor_id,
                'patient_id' => $patient_id,
                'appointment_id' => $appointment_id,
            );
            if(!$this->check_appointment_belongs($requested_data)) {
                $errors = validation_errors();
                echo json_encode(['status' => false,'error' => 'Invalid appointment id']);
                exit;
            }
            $where_array = array(
                'doctor_id' => $doctor_id,
                'patient_id' => $patient_id,
            );
            $update_data = [
                'patient_connection_id' => $connection_id,
                'updated_at' => $this->utc_time_formated
            ];
            $this->Common_model->update('me_video_conf_token', $update_data, $where_array);
            $response = [];
            $response['status'] = true;
            $response['msg'] = 'Your call ended';
            echo json_encode($response);
        }
    }

    public function send_pushwoosh_notification() {
        $this->form_validation->set_error_delimiters('', '');
        $this->form_validation->set_rules('doctor_id', 'doctor id', 'required|trim');
        $this->form_validation->set_rules('patient_id', 'patient id', 'required|trim');
        $this->form_validation->set_rules('appointment_id', 'appointment id', 'required|trim');
        if ($this->form_validation->run() == FALSE) {
            $errors = validation_errors();
            echo json_encode(['status' => false,'error' => nl2br($errors)]);
        } else {
            $doctor_id = set_value('doctor_id');
            $patient_id = set_value('patient_id');
            $appointment_id = set_value('appointment_id');
            $requested_data = array(
                'doctor_id' => $doctor_id,
                'patient_id' => $patient_id,
                'appointment_id' => $appointment_id,
            );
            if(!$this->check_appointment_belongs($requested_data)) {
                $errors = validation_errors();
                echo json_encode(['status' => false,'error' => 'Invalid appointment id']);
                exit;
            }
            $user_device_token_data = $this->Common_model->get_single_row('me_user_device_tokens', 'udt_device_token', ['udt_u_id' => $doctor_id]);
            if(!empty($user_device_token_data['udt_device_token'])) {
                $appointment_detail = $this->patient->get_appointment_detail($appointment_id);
                $device_token = $user_device_token_data['udt_device_token'];
                $notification_message = sprintf(lang('doctor_push_message'), $appointment_detail['patient_name'], date('d/m/Y h:i A', strtotime($appointment_detail['appointment_date'] . ' ' . $appointment_detail['appointment_from_time'])));
                $message = array();
                $message['send_date'] = "now";
                // $message['link'] = $GLOBALS['ENV_VARS']['APP_NOTIFICATION_URL'] . '/staging';
                $message['content'] = $notification_message;
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
                send_pushwoosh_notification($message);
                $notification_insert = [
                    'notification_list_user_id' => $doctor_id,
                    'notification_list_user_type' => 2,
                    'notification_list_device_type' => 'web',
                    'notification_list_type' => 5,
                    'notification_list_message' => $notification_message,
                    'notification_list_created_at' => $this->utc_time_formated,
                    'notification_list_status' => 1
                ];
                $this->Common_model->insert('me_notification_list', $notification_insert);
                $response = [];
                $response['status'] = true;
                $response['msg'] = 'push notification send successfully';
            } else {
                $response = [];
                $response['status'] = false;
                $response['msg'] = 'Doctor token not found';
            }
            echo json_encode($response);
        }
    }

    public function check_appointment_belongs($requested_data) {
        $where_array = array(
            'appointment_status' => 1,
            'appointment_id' => $requested_data['appointment_id'],
            'appointment_user_id' => $requested_data['patient_id'],
            'appointment_doctor_user_id' => $requested_data['doctor_id']
        );
        $check_belongs_appointment = $this->Common_model->get_single_row('me_appointments', 'appointment_id', $where_array);
        if (empty($check_belongs_appointment)) {
            return false;
        } else {
            return true;
        }
    }

}