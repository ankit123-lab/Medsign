<?php
use OpenTok\OpenTok;
Class Video_conf extends MY_Controller {

    public function __construct() {
        parent::__construct();
    }

    public function get_video_conf_token_post() {
        try {
            $doctor_id = !empty($this->Common_model->escape_data($this->post_data['doctor_id'])) ? $this->Common_model->escape_data($this->post_data['doctor_id']) : '';
            $patient_id = !empty($this->Common_model->escape_data($this->post_data['patient_id'])) ? $this->Common_model->escape_data($this->post_data['patient_id']) : '';
            $appointment_id = !empty($this->Common_model->escape_data($this->post_data['appointment_id'])) ? $this->Common_model->escape_data($this->post_data['appointment_id']) : '';
            
            if (empty($patient_id) || empty($doctor_id) || empty($appointment_id)) {
                $this->bad_request();
            }
            $requested_data = array(
                'doctor_id' => $doctor_id,
                'patient_id' => $patient_id,
                'appointment_id' => $appointment_id,
            );
            $this->check_appointment_belongs($requested_data);
            $call_history_data = $this->Common_model->get_single_row('me_teleconsultant_call_history', 'appointment_id,call_start_date_time,call_end_date_time', ['appointment_id' => $appointment_id]);
            if(!empty($call_history_data['call_end_date_time'])) {
                $this->my_response['status'] = false;
                $this->my_response['message'] = lang("video_call_ended_message");
                $this->send_response();
            }
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
            if(!empty($call_history_data)) {
                $call_history_update_data = [
                    'doctor_start_date_time' => $this->utc_time_formated,
                    'updated_at' => $this->utc_time_formated,
                ];
                if(empty($call_history_data['call_start_date_time']))
                    $call_history_update_data['call_start_date_time'] = $this->utc_time_formated;
                $this->Common_model->update('me_teleconsultant_call_history', $call_history_update_data, ['appointment_id' => $appointment_id]);
            } else {
                $call_history_insert_data = [
                    'appointment_id' => $appointment_id,
                    'doctor_id' => $doctor_id,
                    'patient_id' => $patient_id,
                    'call_start_date_time' => $this->utc_time_formated,
                    'doctor_start_date_time' => $this->utc_time_formated,
                    'created_at' => $this->utc_time_formated,
                ];
                $this->Common_model->insert('me_teleconsultant_call_history', $call_history_insert_data);
            }
            if (!empty($response)) {
                $this->my_response['status'] = true;
                $this->my_response['message'] = lang('common_detail_found');
                $this->my_response['data'] = $response;
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

    public function end_video_conf_call_post() {
        try {
            $doctor_id = !empty($this->Common_model->escape_data($this->post_data['doctor_id'])) ? $this->Common_model->escape_data($this->post_data['doctor_id']) : '';
            $patient_id = !empty($this->Common_model->escape_data($this->post_data['patient_id'])) ? $this->Common_model->escape_data($this->post_data['patient_id']) : '';
            $appointment_id = !empty($this->Common_model->escape_data($this->post_data['appointment_id'])) ? $this->Common_model->escape_data($this->post_data['appointment_id']) : '';
            
            if (empty($patient_id) || empty($doctor_id) || empty($appointment_id)) {
                $this->bad_request();
            }
            $requested_data = array(
                'doctor_id' => $doctor_id,
                'patient_id' => $patient_id,
                'appointment_id' => $appointment_id,
            );
            $this->check_appointment_belongs($requested_data);
            $where_array = array(
                'doctor_id' => $doctor_id,
                'patient_id' => $patient_id,
            );
            $this->Common_model->delete_data('me_video_conf_token', $where_array);
            $call_history_data = $this->Common_model->get_single_row('me_teleconsultant_call_history', 'appointment_id,doctor_start_date_time,patient_start_date_time', ['appointment_id' => $appointment_id]);
            $call_end_date_time = $this->utc_time_formated;
            $callDuration = 0;
            if(!empty($call_history_data['doctor_start_date_time']) && !empty($call_history_data['patient_start_date_time'])) {
                if($call_history_data['doctor_start_date_time'] < $call_history_data['patient_start_date_time']) {
                    $callDuration = strtotime($call_end_date_time) - strtotime($call_history_data['doctor_start_date_time']);
                } else {
                    $callDuration = strtotime($call_end_date_time) - strtotime($call_history_data['patient_start_date_time']);
                }
            } elseif(!empty($call_history_data['doctor_start_date_time']) && empty($call_history_data['patient_start_date_time'])) {
                $callDuration = strtotime($call_end_date_time) - strtotime($call_history_data['doctor_start_date_time']);
            } elseif(empty($call_history_data['doctor_start_date_time']) && !empty($call_history_data['patient_start_date_time'])) {
                $callDuration = strtotime($call_end_date_time) - strtotime($call_history_data['patient_start_date_time']);
            }
            $actual_call_duration_time = $callDuration;
            if($callDuration > 0) {
                $x = 5; //round up to nearest 5 minutes
                $callDuration = $callDuration/60;
                $callDuration = round(($callDuration+$x/2)/$x)*$x;
                $callDuration = $callDuration*60;
            }
            $call_history_update_data = [
                'call_end_date_time' => $call_end_date_time,
                'call_duration_time' => $callDuration,
                'actual_call_duration_time' => $actual_call_duration_time,
                'updated_at' => $this->utc_time_formated,
            ];
            if($callDuration > 0) {
                $where_array = array(
                    'setting_user_id' => $doctor_id,
                    'setting_type' => 10,
                    'setting_status' => 1
                );
                $get_setting_data = $this->Common_model->get_single_row(TBL_SETTING, 'setting_id,setting_data', $where_array);
                $total_seconds = !empty($get_setting_data) ? $get_setting_data['setting_data'] : 0;
                $update_setting_data = array(
                    'setting_data' => $total_seconds - $callDuration,
                    'setting_updated_at' => $this->utc_time_formated
                );
                $update_setting_where = array(
                    'setting_id' => $get_setting_data['setting_id']
                );
                $this->Common_model->update(TBL_SETTING, $update_setting_data, $update_setting_where);
            }
            $this->Common_model->update('me_teleconsultant_call_history', $call_history_update_data, ['appointment_id' => $appointment_id]);

            $this->my_response['status'] = true;
            $this->my_response['message'] = sprintf(lang('video_call_ended'), floor($update_setting_data['setting_data']/60));
            $this->send_response();
        } catch (ErrorException $ex) {
            $this->error = $ex->getMessage();
            $this->store_error();
        }
    }

    public function generate_video_url_patient_post() {
        try {
            $doctor_id = !empty($this->Common_model->escape_data($this->post_data['doctor_id'])) ? $this->Common_model->escape_data($this->post_data['doctor_id']) : '';
            $patient_id = !empty($this->Common_model->escape_data($this->post_data['patient_id'])) ? $this->Common_model->escape_data($this->post_data['patient_id']) : '';
            $appointment_id = !empty($this->Common_model->escape_data($this->post_data['appointment_id'])) ? $this->Common_model->escape_data($this->post_data['appointment_id']) : '';
            
            if (empty($patient_id) || empty($doctor_id) || empty($appointment_id)) {
                $this->bad_request();
            }
            $requested_data = array(
                'doctor_id' => $doctor_id,
                'patient_id' => $patient_id,
                'appointment_id' => $appointment_id,
            );
            $this->check_appointment_belongs($requested_data);
            $where_array = array(
                'doctor_id' => $doctor_id,
                'patient_id' => $patient_id,
            );
            $get_token_data = $this->Common_model->get_single_row('me_video_conf_token', 'session_id,token_id', $where_array);
            if (empty($get_token_data)) {
               $insert_data = [
                    'doctor_id' => $doctor_id,
                    'patient_id' => $patient_id,
                    'created_at' => $this->utc_time_formated
                ];
                $this->Common_model->insert('me_video_conf_token', $insert_data);
            } else {
                $update_data = [
                    'session_id' => NULL,
                    'token_id' => NULL,
                    'updated_at' => $this->utc_time_formated
                ];
                $this->Common_model->update('me_video_conf_token', $update_data, $where_array);
            }
            $patient_url = MEDSIGN_WEB_CARE_URL . 'telecall/' . encrypt_decrypt($appointment_id, 'encrypt');
            $this->my_response['status'] = true;
            $this->my_response['patient_url'] = $patient_url;
            $this->send_response();
        } catch (ErrorException $ex) {
            $this->error = $ex->getMessage();
            $this->store_error();
        }
    }

    public function update_connection_id_post() {
        try {
            $doctor_id = !empty($this->Common_model->escape_data($this->post_data['doctor_id'])) ? $this->Common_model->escape_data($this->post_data['doctor_id']) : '';
            $patient_id = !empty($this->Common_model->escape_data($this->post_data['patient_id'])) ? $this->Common_model->escape_data($this->post_data['patient_id']) : '';
            $appointment_id = !empty($this->Common_model->escape_data($this->post_data['appointment_id'])) ? $this->Common_model->escape_data($this->post_data['appointment_id']) : '';
            $connection_id = !empty($this->Common_model->escape_data($this->post_data['connection_id'])) ? $this->Common_model->escape_data($this->post_data['connection_id']) : '';
            
            if (empty($patient_id) || empty($doctor_id) || empty($appointment_id) || empty($connection_id)) {
                $this->bad_request();
            }
            $requested_data = array(
                'doctor_id' => $doctor_id,
                'patient_id' => $patient_id,
                'appointment_id' => $appointment_id,
            );
            $this->check_appointment_belongs($requested_data);
            $where_array = array(
                'doctor_id' => $doctor_id,
                'patient_id' => $patient_id,
            );
            $update_data = [
                'doctor_connection_id' => $connection_id,
                'updated_at' => $this->utc_time_formated
            ];
            $this->Common_model->update('me_video_conf_token', $update_data, $where_array);
            $this->my_response['status'] = true;
            $this->send_response();
        } catch (ErrorException $ex) {
            $this->error = $ex->getMessage();
            $this->store_error();
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
            $this->my_response['status'] = false;
            $this->my_response['message'] = lang('mycontroller_invalid_request');
            $this->send_response();
        }
    }

}