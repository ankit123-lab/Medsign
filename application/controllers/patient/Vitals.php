<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Vitals extends MY_Controller {

    public function __construct() {

        parent::__construct();
        $this->load->library('patient_auth');
        $this->load->model('patient_model','patient');
        $this->load->library('form_validation');
        $this->load->library("pagination");
        $this->load->library('datatables');
        if (!$this->patient_auth->is_logged_in()) {
            redirect('patient/login');
        }
    }

    public function vital_list() {
        $view_data = array();
        $view_data['breadcrumbs'] = "Vitals";
        $view_data['page_title'] = "Vitals";
        $view_data['start_date'] = date("d/m/Y", strtotime("-3 month", strtotime(get_display_date_time("Y-m-d H:i:s"))));
        $view_data['end_date'] = get_display_date_time("d/m/Y");
        $this->load->view('patient/vital_list_view', $view_data);
    }

    public function vital_data() {
        header('Content-Type: application/json');
        $data = $this->patient->vitals_data();
        $rs = json_decode($data, true);
        $date_col = array_column($rs['data'], 'vital_report_date','vital_report_date');
        $label_arr = ['WT' => 'Wieght', 'PR' => 'Pulse Rate','RR' => 'Resp. Rate','SpO2' => "SpO<sub>2</sub>",'BP' => "Blood Pressure",'TEMP' => "Temperature"];
        $newData = [];
        $columns = [];
        if(!empty($rs['data'][0][0])){
            $columns = array_column($rs['data'], '0');
            $vital_report_id_arr = array_column($rs['data'], '11');
        }
        foreach ($columns as $key => $value) {
            $title = (($rs['data'][$key][13] == 2) ? DOCTOR : '') . $rs['data'][$key][12];
            $dateVal = date("d/m/Y", strtotime($value));
            $edit_vital = "";
            $is_delete_vital = false;
            $vital_report_id = "";
            if(get_display_date_time("Y-m-d", $rs['data'][$key][9]) == get_display_date_time("Y-m-d") && $rs['data'][$key][13] == 1) {
                $edit_vital = "edit_vital";
                $is_delete_vital = true;
                $vital_report_id = encrypt_decrypt($vital_report_id_arr[$key], 'encrypt');
            }
            $columns[$key] = "<span class='".$edit_vital."' vital_report_id='".$vital_report_id."' data-title='".$dateVal."' title='".$title."'>".$dateVal."</span>";
            if($is_delete_vital) {
                $columns[$key] .= " <a class='delete-vital-icon' href='".site_url('patient/delete_vital/'. $vital_report_id)."'  title='Delete' onclick=\"return confirm('Are you sure to delete this vitals?')\"><i class=\"fa fa-times\" aria-hidden=\"true\"></i></a>"; 
            }
        }
        if(count($columns) > 0 && count($columns) < 6) {
            $j = 6 - count($columns);
            for ($i=0; $i < $j; $i++) { 
                $columns[] = "";
            }
        }
        if(count($columns) == 0)
            $columns[] = 'Vitals';
        else
            array_unshift($columns , 'Vitals');
        foreach ($label_arr as $lb => $lb_full_form) {
            $row = [];
            $row[0] = $lb_full_form;
            foreach ($rs['data'] as $key => $value) {
                $style = "";
                if($value[13] == 2){
                    $style = "color:red;";
                }
                $added_by = ($value[13]==2 ? DOCTOR . " " : '') . $value[12];
                $edit_vital = "";
                $vital_report_id = "";
                if(get_display_date_time("Y-m-d", $rs['data'][$key][9]) == get_display_date_time("Y-m-d") && $rs['data'][$key][13] == 1) {
                    $edit_vital = "edit_vital";
                    $vital_report_id = encrypt_decrypt($vital_report_id_arr[$key], 'encrypt');
                }
                if($lb == 'WT'){
                    $row[$key+1] = "<span class='".$edit_vital."' vital_report_id='".$vital_report_id."' data-title='".date("d/m/Y", strtotime($value[0]))." \n(".$added_by.")' style='" . $style . "'>".(!empty($value[1]) ? $value[1] : 'N/A')."</span>";
                }
                if($lb == 'PR'){
                    $row[$key+1] = "<span class='".$edit_vital."' vital_report_id='".$vital_report_id."' data-title='".date("d/m/Y", strtotime($value[0]))." \n(".$added_by.")' style='" . $style . "'>".(!empty($value[2]) ? $value[2] : 'N/A') ."</span>";
                }
                if($lb == 'RR'){
                    $row[$key+1] = "<span class='".$edit_vital."' vital_report_id='".$vital_report_id."' data-title='".date("d/m/Y", strtotime($value[0]))." \n(".$added_by.")' style='" . $style . "'>".(!empty($value[3]) ? $value[3] : 'N/A') ."</span>";
                }
                if($lb == 'SpO2'){
                    $row[$key+1] = "<span class='".$edit_vital."' vital_report_id='".$vital_report_id."' data-title='".date("d/m/Y", strtotime($value[0]))." \n(".$added_by.")' style='" . $style . "'>".(!empty($value[4]) ? $value[4] : 'N/A') ."</span>";
                }
                if($lb == 'BP'){
                    $row[$key+1] = "<span class='".$edit_vital."' vital_report_id='".$vital_report_id."' data-title='".date("d/m/Y", strtotime($value[0]))." \n(".$added_by.")' style='" . $style . "'>" . ((!empty($value[5]) || !empty($value[6])) ? $value[5] . "/" . $value[6] : 'N/A')."</span>";
                }
                if($lb == 'TEMP'){
                    $temp_sign = "(℉)";
                    if($value[8] == 2)
                        $temp_sign = "(℃)";
                    $row[$key+1] = "<span class='".$edit_vital."' vital_report_id='".$vital_report_id."' data-title='".date("d/m/Y", strtotime($value[0]))." \n(".$added_by.")' style='" . $style . "'>".(!empty($value[7]) ? $value[7].$temp_sign : 'N/A')."</span>";
                }
            }
            $row[] = '';
            $newData[] = $row;
        }
        $rs['data'] = $newData;
        $rs['columns'] = $columns;
        echo json_encode($rs);
        die;
    }

    public function vital_graph_data() {
        header('Content-type: application/json');
        $this->form_validation->set_rules('start_date', 'start date', 'required|trim');
        $this->form_validation->set_rules('end_date', 'end date', 'required|trim');
        if ($this->form_validation->run() == FALSE) {
            $errors = validation_errors();
            echo json_encode(['status' => false,'error' => nl2br($errors)]);
        } else {
            $start_date_arr = explode('/', set_value('start_date'));
            $start_date = $start_date_arr[2].'-'.$start_date_arr[1].'-'.$start_date_arr[0];
            $end_date_arr = explode('/', set_value('end_date'));
            $end_date = $end_date_arr[2].'-'.$end_date_arr[1].'-'.$end_date_arr[0];
            $where = [
                'start_date' => $start_date,
                'end_date' => $end_date,
            ];
            $result = $this->patient->get_vitals($where);
            foreach ($result as $key => $value) {
                if(!empty($value->vital_report_weight))
                    $result[$key]->vital_report_weight = PoundToKG($value->vital_report_weight);
                $result[$key]->vital_report_date = date("d/m/Y", strtotime($value->vital_report_date));
            }
            echo json_encode(['status' => true,'graph_data' => $result]);
        }
    }

    public function add_vital() {
        $view_data = array();
        $view_data['breadcrumbs'] = "Add Vitals";
        $view_data['page_title'] = "Add Vitals";
        $this->load->view('patient/add_vital_view', $view_data);
    }

    public function delete_vital($id) {
        $id = encrypt_decrypt($id, 'decrypt');
        $vital_data = ['vital_report_updated_at' => date("Y-m-d H:i:s"), 'vital_report_status' => 9];
        $where = array('vital_report_id' => $id, 'vital_report_user_id' => $this->patient_auth->get_user_id(), 'vital_report_doctor_id' => $this->patient_auth->get_user_id());
        $this->Common_model->update('me_vital_reports', $vital_data, $where);
        $this->session->set_flashdata('message', 'Vitals deleted successfully');
        redirect(site_url('patient/vitals'));
    }
    public function edit_vital($id) {
        $id = encrypt_decrypt($id, 'decrypt');
        $view_data = array();
        $view_data['vitals'] = $this->Common_model->get_single_row('me_vital_reports', '*', ['vital_report_id' => $id, 'vital_report_status' => 1]);
        if(empty($view_data['vitals']) || $view_data['vitals']['vital_report_user_id'] != $this->patient_auth->get_user_id() || $view_data['vitals']['vital_report_doctor_id'] != $this->patient_auth->get_user_id() || get_display_date_time("Y-m-d") != get_display_date_time("Y-m-d", $view_data['vitals']['vital_report_created_at'])) {
            redirect(site_url('patient/vitals'));
        }
        if(!empty($view_data['vitals']['vital_report_weight'])) {
            $view_data['vitals']['vital_report_weight'] = PoundToKG($view_data['vitals']['vital_report_weight']);
        }
        if(!empty($view_data['vitals']['vital_report_temperature']) && $view_data['vitals']['vital_report_temperature_type'] == 2) {
            $view_data['vitals']['vital_report_temperature'] = FahrenhiteToCelcius($view_data['vitals']['vital_report_temperature']);
        }
        $view_data['breadcrumbs'] = "Edit Vitals";
        $view_data['page_title'] = "Edit Vitals";
        $this->load->view('patient/add_vital_view', $view_data);
    }

    public function save_vital() {
        $this->form_validation->set_error_delimiters('', '');
        $this->form_validation->set_rules('vital_report_date', 'date', 'required|trim');
        $this->form_validation->set_rules('vital_report_id', 'vital id', 'trim');
        $this->form_validation->set_rules('vital_report_weight', 'weight', 'trim|numeric|callback_weightRange');
        $this->form_validation->set_rules('vital_report_pulse', 'pulse', 'trim|numeric|callback_pulseRange');
        $this->form_validation->set_rules('vital_report_resp_rate', 'resp', 'trim|numeric|callback_respRateRange');
        $this->form_validation->set_rules('vital_report_spo2', 'spo2', 'trim|numeric|callback_spo2Range');
        $this->form_validation->set_rules('vital_report_bloodpressure_systolic', 'systolic', 'trim|numeric|callback_systolicRange');
        $this->form_validation->set_rules('vital_report_bloodpressure_diastolic', 'diastolic', 'trim|numeric|callback_diastolicRange');
        $this->form_validation->set_rules('vital_report_temperature', 'temperature', 'trim|numeric|callback_temperatureRange');
        
        if ($this->form_validation->run() == FALSE) {
            $errors = validation_errors();
            echo json_encode(['status' => false,'error' => nl2br($errors)]);
        } else {
            if(set_value('vital_report_weight') == '' && 
                set_value('vital_report_pulse') == '' && 
                set_value('vital_report_resp_rate') == '' && 
                set_value('vital_report_spo2') == '' && 
                set_value('vital_report_bloodpressure_systolic') == '' && 
                set_value('vital_report_bloodpressure_diastolic') == '' && 
                set_value('vital_report_temperature') == ''
            ) {
                echo json_encode(['status' => false,'error' => 'Please Enter At Least One Vital Sign']);
                exit;
            }
            $date_arr = explode('/', set_value('vital_report_date'));
            $date = $date_arr[2].'-'.$date_arr[1].'-'.$date_arr[0];
            $vital_data = array(
                'vital_report_user_id' => $this->patient_auth->get_user_id(),
                'vital_report_doctor_id' => $this->patient_auth->get_user_id(),
                'vital_report_date' => $date,
                'vital_report_weight' => kgToPound(set_value('vital_report_weight')),
                'vital_report_pulse' => set_value('vital_report_pulse'),
                'vital_report_resp_rate' => set_value('vital_report_resp_rate'),
                'vital_report_spo2' => set_value('vital_report_spo2'),
                'vital_report_bloodpressure_systolic' => set_value('vital_report_bloodpressure_systolic'),
                'vital_report_bloodpressure_diastolic' => set_value('vital_report_bloodpressure_diastolic'),
                'vital_report_bloodpressure_type' => set_value('vital_report_bloodpressure_type'),
                'vital_report_temperature' => set_value('vital_report_temperature'),
                'vital_report_temperature_type' => set_value('vital_report_temperature_type'),
                'vital_report_temperature_taken' => set_value('vital_report_temperature_taken'),
            );
            if(set_value('vital_report_temperature_type') == 2) {
                $vital_data['vital_report_temperature'] = CelciusToFahrenhite(set_value('vital_report_temperature'));
            }
            if(set_value('vital_report_id') != '') {
                $vital_data['vital_report_updated_at'] = date("Y-m-d H:i:s");
                $vital_data['vital_report_updated_by'] = $this->patient_auth->get_logged_user_id();
                $this->Common_model->update('me_vital_reports', $vital_data, array('vital_report_id' => set_value('vital_report_id')));
                $this->session->set_flashdata('message', 'Vitals updated successfully');
            } else {
                $vital_data['vital_report_user_id'] = $this->patient_auth->get_user_id();
                $vital_data['vital_report_doctor_id'] = $this->patient_auth->get_user_id();
                $vital_data['vital_report_created_at'] = date("Y-m-d H:i:s");
                $vital_data['vital_report_share_status'] = 1;
                $vital_data['vital_report_status'] = 1;
                $vital_data['vital_report_created_by'] = $this->patient_auth->get_logged_user_id();
                $this->Common_model->insert('me_vital_reports', $vital_data);
                $this->session->set_flashdata('message', 'Vitals added successfully');
            }
            $response = [];
            $response['status'] = true;
            echo json_encode($response);
        }
    }
    public function weightRange($num) {
        if (!empty($num) && ($num < 1 || $num > 200)) {
            $this->form_validation->set_message(
                'weightRange',
                'Weight can not be less then 1 nor greater than 200'
            );
            return FALSE;
        } else{
            return TRUE;
        }
    }
    public function pulseRange($num) {
        if (!empty($num) && ($num < 10 || $num > 500)) {
            $this->form_validation->set_message(
                'pulseRange',
                'Pulse rate cannot be lesser than 10 nor greater than 500'
            );
            return FALSE;
        } else{
            return TRUE;
        }
    }
    public function respRateRange($num) {
        if (!empty($num) && ($num < 10 || $num > 70)) {
            $this->form_validation->set_message(
                'respRateRange',
                'Respiration rate cannot be lesser than 10 nor greater than 70'
            );
            return FALSE;
        } else{
            return TRUE;
        }
    }
    public function spo2Range($num) {
        if (!empty($num) && ($num < 1 || $num > 100)) {
            $this->form_validation->set_message(
                'spo2Range',
                'SpO2 cannot be lesser than 1 nor greater than 100.'
            );
            return FALSE;
        } else{
            return TRUE;
        }
    }
    public function systolicRange($num) {
        if (!empty($num) && ($num < 50 || $num > 300)) {
            $this->form_validation->set_message(
                'systolicRange',
                'Systolic Blood Pressure cannot be lesser than 50 nor greater than 300'
            );
            return FALSE;
        } else{
            return TRUE;
        }
    }
    public function diastolicRange($num) {
        if (!empty($num) && ($num < 25 || $num > 200)) {
            $this->form_validation->set_message(
                'diastolicRange',
                'Diastolic Blood Pressure cannot be lesser than 25 nor greater than 200'
            );
            return FALSE;
        } else{
            return TRUE;
        }
    }
    public function temperatureRange($num) {
        if($this->input->post('vital_report_temperature_type') == 1) {
            if (!empty($num) && ($num < 75.2 || $num > 109.4)) {
                $this->form_validation->set_message(
                    'temperatureRange',
                    'Temperature cannot be lesser than 75.2 nor greater than 109.4'
                );
                return FALSE;
            } else{
                return TRUE;
            }
        } else {
            if (!empty($num) && ($num < 24 || $num > 43)) {
                $this->form_validation->set_message(
                    'temperatureRange',
                    'Temperature cannot be lesser than 24 nor greater than 43'
                );
                return FALSE;
            } else{
                return TRUE;
            }
        }
    }
}