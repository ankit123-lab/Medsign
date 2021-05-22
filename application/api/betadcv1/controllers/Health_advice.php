<?php
class Health_advice extends MY_Controller {

    public function __construct() {
		parent::__construct();
        $this->load->model("Health_advice_model", "health_advice");
    }

    public function get_health_advice_groups_post() {
        try {
            $doctor_id = !empty($this->post_data['doctor_id']) ? trim($this->Common_model->escape_data($this->post_data['doctor_id'])) : '';
            $patient_id = !empty($this->post_data['patient_id']) ? trim($this->Common_model->escape_data($this->post_data['patient_id'])) : '';
            if(empty($doctor_id) ||
                empty($patient_id)
            ) {
                $this->bad_request();
                exit;
            }
            $result = $this->health_advice->get_health_advice_groups();
            $health_advice_assigned = $this->health_advice->get_health_advice_assigned($patient_id);
            $health_advice_assigned_ids = array_column($health_advice_assigned, 'patient_health_advice_group_id');
            foreach ($result as $key => $value) {
                $result[$key]->is_assigned = false;
                if(in_array($value->health_advice_group_id, $health_advice_assigned_ids))
                    $result[$key]->is_assigned = true;
            }
            $this->my_response['status'] = true;
            $this->my_response['data'] = $result;
            $this->my_response['health_advice_assigned'] = implode(", ",array_column($health_advice_assigned, 'health_advice_group_name'));
            $this->send_response();
        } catch (Exception $ex) {
            $this->error = $ex->getMessage();
            $this->store_error();
        }
    }

    public function get_health_advice_post() {
        try {
            $doctor_id = !empty($this->post_data['doctor_id']) ? trim($this->Common_model->escape_data($this->post_data['doctor_id'])) : '';
            $health_advice_group_id = !empty($this->post_data['health_advice_group_id']) ? trim($this->Common_model->escape_data($this->post_data['health_advice_group_id'])) : '';
            if(empty($health_advice_group_id)) {
                $this->bad_request();
                exit;
            }
            $result = $this->health_advice->get_health_advice($health_advice_group_id);
            $this->my_response['status'] = true;
            $this->my_response['data'] = $result;
            $this->send_response();
        } catch (Exception $ex) {
            $this->error = $ex->getMessage();
            $this->store_error();
        }
    }

    public function add_patient_health_advice_post() {
        try {
            $doctor_id = !empty($this->post_data['doctor_id']) ? trim($this->Common_model->escape_data($this->post_data['doctor_id'])) : '';
            $health_advice_group_id = !empty($this->post_data['health_advice_group_id']) ? trim($this->Common_model->escape_data($this->post_data['health_advice_group_id'])) : '';
            $patient_health_advice_schedule = !empty($this->post_data['patient_health_advice_schedule']) ? trim($this->Common_model->escape_data($this->post_data['patient_health_advice_schedule'])) : '';
            $patient_health_advice_send_day = !empty($this->post_data['patient_health_advice_send_day']) ? trim($this->Common_model->escape_data($this->post_data['patient_health_advice_send_day'])) : '';
            $patient_id = !empty($this->post_data['patient_id']) ? trim($this->Common_model->escape_data($this->post_data['patient_id'])) : '';
            $is_send_email = !empty($this->post_data['is_send_email']) ? trim($this->Common_model->escape_data($this->post_data['is_send_email'])) : 2;
            $is_send_sms = !empty($this->post_data['is_send_sms']) ? trim($this->Common_model->escape_data($this->post_data['is_send_sms'])) : 2;
            if(empty($doctor_id) ||
                empty($health_advice_group_id) ||
                empty($patient_id) ||
                empty($patient_health_advice_schedule) 
            ) {
                $this->bad_request();
                exit;
            }
            if($this->health_advice->is_health_advice_assigned($patient_id, $health_advice_group_id)) {
                $this->my_response['status'] = false;
                $this->my_response['message'] = lang('health_advice_already_assigned');
                $this->send_response();
            }
            $patient_health_advice_data = array(
                'patient_health_advice_patient_id' => $patient_id,
                'patient_health_advice_doctor_id' => $doctor_id,
                'patient_health_advice_group_id' => $health_advice_group_id,
                'patient_health_advice_schedule' => $patient_health_advice_schedule,
                'patient_health_advice_send_time' => "08:00:00",
                'patient_health_advice_is_send_email' => $is_send_email,
                'patient_health_advice_is_send_sms' => $is_send_sms,
                'patient_health_advice_created_at' => $this->utc_time_formated,
            );
            if($patient_health_advice_schedule == "2")
                $patient_health_advice_data['patient_health_advice_send_day'] = $patient_health_advice_send_day;
            $this->Common_model->insert('me_patients_health_advice', $patient_health_advice_data);
            $this->my_response['status'] = true;
            $this->my_response['message'] = lang('health_advice_assigned');
            $this->send_response();
        } catch (Exception $ex) {
            $this->error = $ex->getMessage();
            $this->store_error();
        }
    }
}