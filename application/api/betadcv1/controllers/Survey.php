<?php
/*** This controller use for Doctor related activity*/
class Survey extends MY_Controller {

    public function __construct() {
		parent::__construct();
        $this->load->model("Survey_model", "survey");
        $this->load->model(array("Doctor_model","Auditlog_model", "User_model"));
    }

    /**
     * Description :- This function is used to get the list of survey
     */
    public function get_survey_post() {
        try {
            $doctor_id = !empty($this->post_data['doctor_id']) ? $this->post_data['doctor_id'] : "";
            if (empty(trim($doctor_id))) {
                $this->bad_request();
                exit;
            }
            if(!empty($this->user_id)) {
                $doctor_id = $this->user_id;
            }
            if(is_numeric($doctor_id)) {
                $where = array(
                    'user_id' => $doctor_id
                );
                $get_user_details = $this->User_model->get_user_details_with_clinic_mapp_by_id($where, 'doctor_clinic_mapping_role_id,user_status');
                if ($get_user_details->doctor_clinic_mapping_role_id != 1 || $get_user_details->user_status != 1) {
                    $this->my_response = array(
                        "status" => false,
                        "data" => array(),
                        "message" => lang('common_detail_not_found')
                    );
                    $this->send_response();
                }
                $start_date = date("Y-m-d H:i:s");
                $end_date   = date("Y-m-d H:i:s");
                
                $doctor_specialisation_edu_ids = $this->Doctor_model->get_doctor_specialisation_edu_ids($doctor_id);
                $doctor_specialisation_ids     = $doctor_specialisation_edu_ids['doctor_specialization_specialization_ids'];
                $doctor_qua_ids                = $doctor_specialisation_edu_ids['doctor_qualification_qualification_ids'];
                $survey_list = $this->survey->get_survey_list($doctor_id, $start_date, $end_date, $doctor_specialisation_ids, $doctor_qua_ids);
                $result = array();
                $survey_filled = $this->survey->get_survey_filled($doctor_id);
                if(count($survey_filled) > 0) {
                    $survey_filled = array_column($survey_filled, 'created_at', 'survey_id');
                }
                $survey_id_arr = array();
                foreach ($survey_list as $key => $value) {
                    if(empty($result[$value['survey_id']])) {
                        $survey_id_arr[] = $value['survey_id'];
                        $result[$value['survey_id']] = array(
                            'survey_id' => $value['survey_id'],
                            'survey_title' => $value['survey_title'],
                            'survey_description' => nl2br($value['survey_description']),
                            'survey_start_date' => $value['survey_start_date'],
                            'survey_end_date' => $value['survey_end_date'],
                            'survey_created_at' => $value['survey_created_at'],
                            'is_question' => 'No'
                        );
                        if(!empty($survey_filled[$value['survey_id']])) {
                            $result[$value['survey_id']]['is_submitted'] = 1; 
                            $result[$value['survey_id']]['submitted_date'] = $survey_filled[$value['survey_id']]; 
                        }
                    }
                    
                    if(!empty($value['survey_type_id'])) {
                        if(empty($result[$value['survey_id']]['survey_type_data']))
                            $result[$value['survey_id']]['survey_type_data'] = array();
                        $result[$value['survey_id']]['survey_type_data'][] = array(
                            'survey_type_id' => $value['survey_type_id'],
                            'title' => $value['title'],
                            'survey_file_path' => get_file_full_path($value['survey_file_path']),
                            'survey_videourl' => $value['survey_videourl']
                        );
                    }
                }
                if (count($survey_id_arr)) {
                    $questions = $this->survey->get_survey_question_count($survey_id_arr);
                    foreach ($questions as $key => $value) {
                        if(!empty($result[$value['survey_id']])) {
                            $result[$value['survey_id']]['is_question'] = 'Yes';
                        }
                    }
                }
                foreach ($result as $key => $value) {
                    if(empty($value['survey_type_data']) && $value['is_question'] == 'No') {
                        unset($result[$key]);
                    }
                }

                $result = array_values($result);
                if(count($result) > 0) {
                    $this->my_response = array(
                        "status" => true,
                        "message" => "success",
                        "survey_data" => $result,
                    );
                } else {
                    $this->my_response = array(
                        "status" => false,
                        "data" => array(),
                        "message" => lang('common_detail_not_found')
                    );
                }
            } else {
                $this->my_response = array(
                    "status" => false,
                    "message" => lang("mycontroller_bad_request")
                );
            }
            $this->send_response();
        } catch (Exception $e) {
            $this->error = $e->getMessage();
            $this->store_error();
        }
    }

    /**
     * Description :- This function is used to get the list of survey questions
     */
    public function get_survey_questions_post() {
        try {
            $survey_id = !empty($this->post_data['survey_id']) ? $this->post_data['survey_id'] : "";
            $doctor_id = !empty($this->post_data['doctor_id']) ? $this->post_data['doctor_id'] : "";
            if (empty(trim($survey_id)) || empty($doctor_id)) {
                $this->bad_request();
                exit;
            }
            if(is_numeric($survey_id)) {

                $questions_list = $this->survey->get_survey_questions($survey_id);
                $result = array();
                foreach ($questions_list as $key => $value) {
                    if(count($result) == 0) {
                        $result = array(
                            'survey_id' => $value->survey_id,
                            'survey_title' => $value->survey_title,
                            'survey_description' => nl2br($value->survey_description),
                            'survey_consent' => nl2br($value->survey_consent),
                        );
                    }
                    if(empty($result['questions_data'][$value->survey_question_id])) {
                        $result['questions_data'][$value->survey_question_id] = array(
                            'survey_question_id' => $value->survey_question_id,
                            'question_description' => $value->question_description,
                            'survey_type' => $value->survey_type
                        );
                    }
                    $result['questions_data'][$value->survey_question_id]['options'][] = array(
                        'survey_option_description' => $value->survey_option_description
                    );
                }
                $result['questions_data'] = array_values($result['questions_data']);
                if(count($result) > 0) {
                    $user = $this->User_model->get_details_by_id($doctor_id, 'user_first_name,user_last_name');
                    $result['survey_consent'] = str_replace("{doctor_name}", DOCTOR . ' '.$user['user_first_name'] . ' ' . $user['user_last_name'], $result['survey_consent']);
                    $this->my_response = array(
                        "status" => true,
                        "message" => "success",
                        "survey_data" => $result,
                    );
                } else {
                    $this->my_response = array(
                        "status" => false,
                        "data" => array(),
                        "message" => lang('common_detail_not_found')
                    );
                }
            } else {
                $this->my_response = array(
                    "status" => false,
                    "message" => lang("mycontroller_bad_request")
                );
            }
            $this->send_response();
        } catch (Exception $e) {
            $this->error = $e->getMessage();
            $this->store_error();
        }
    }

    /**
     * Description :- This function is used to get the list of survey questions
     */
    public function save_doctor_survey_data_post() {
        try {
            $doctor_id = !empty($this->post_data['doctor_id']) ? $this->post_data['doctor_id'] : "";
            $survey_id = !empty($this->post_data['survey_id']) ? $this->post_data['survey_id'] : "";
            $clinic_id = !empty($this->post_data['clinic_id']) ? $this->post_data['clinic_id'] : "";
            $questions = !empty($this->post_data['questions']) ? $this->post_data['questions'] : "";
            if (empty(trim($survey_id)) || empty($doctor_id) || empty($questions)) {
                $this->bad_request();
                exit;
            }
            //$questions = json_decode($questions, true); //Use only when we do testing from API directly. 
            $count = $this->survey->count_doctor_survey($doctor_id, $survey_id);
            if($count == 0) {
                $surveyData = array();
                foreach ($questions as $key => $value) {
                    $surveyData[] = array(
                        'survey_id' => $survey_id,
                        'doctor_id' => $doctor_id,
                        'user_id' => $this->user_id,
                        'clinic_id' => $clinic_id,
                        'question_id' => $value['question_id'],
                        'options' => json_encode($value['options']),
                        'option_ans_text' => (!empty($value['option_ans_text'])) ? $value['option_ans_text'] : '',
                        'created_at' => $this->utc_time_formated,
                        'created_by' => $this->user_id,
                        'survey_status' => 1
                    );
                }
                if(count($surveyData) > 0) {
                    $this->survey->insert_doctor_survey_data($surveyData);
                }
                $this->my_response = array(
                    "status" => true,
                    "message" => lang('survey_submitted')
                );
            } else {
                $this->my_response = array(
                    "status" => false,
                    "message" => lang('survey_already_submitted')
                );
            }
            $this->send_response();
        } catch (Exception $e) {
            $this->error = $e->getMessage();
            $this->store_error();
        }
    }

    /**
     * Description :- This function is used to save survey log
     */
    public function save_survey_log_post() {
        try {
            $doctor_id = !empty($this->post_data['doctor_id']) ? $this->post_data['doctor_id'] : "";
            $survey_id = !empty($this->post_data['survey_id']) ? $this->post_data['survey_id'] : "";
            $log_type = !empty($this->post_data['log_type']) ? $this->post_data['log_type'] : "";
            $survey_type_id = !empty($this->post_data['survey_type_id']) ? $this->post_data['survey_type_id'] : "";
            if (empty(trim($survey_id)) || empty($doctor_id) || empty($log_type)) {
                $this->bad_request();
                exit;
            }
            if(!in_array($log_type, array(1,2,3))) {
                $this->my_response = array(
                    "status" => false,
                    "message" => lang('survey_invalid_log_type')
                );
				$this->send_response();
            }
            //Create audit log
            $other_data = array();
            if(!empty($survey_type_id)) {
                $other_data['survey_type_id'] = $survey_type_id;
            }
            $log_type_arr = array(1 => 'SURVEY_VIEW', 2 => 'SURVEY_CONTENT_VIEW', 3 => 'SURVEY_CONSENT_ACCEPT');

            $this->Auditlog_model->create_audit_log($doctor_id, 2, AUDIT_SLUG_ARR[$log_type_arr[$log_type]], array(), array(), TBL_SURVEY, 'survey_id', $survey_id, $other_data);
            $this->my_response = array(
                    "status" => true,
                    "message" => 'success'
                );
            $this->send_response();
        } catch (Exception $e) {
            $this->error = $e->getMessage();
            $this->store_error();
        }
    }

}