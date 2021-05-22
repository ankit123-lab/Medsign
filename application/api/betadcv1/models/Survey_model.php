<?php

class Survey_model extends MY_Model {

    protected $survey_table;
    protected $assignment_table;
    protected $options_table;
    protected $questions_table;
    protected $survey_type_table;

    function __construct() {
        parent::__construct();
        $this->survey_table = TBL_SURVEY;
        $this->assignment_table = TBL_SURVEY_ASSIGNMENT;
        $this->options_table = TBL_SURVEY_OPTIONS;
        $this->questions_table = TBL_SURVEY_QUESTIONS;
        $this->survey_type_table = TBL_SURVEY_TYPE;
    }

    public function get_survey_list($doctor_id = 0, $start_date, $end_date, $doctor_specialisation_ids, $doctor_qua_ids) {
        $result = array();
        $get_survey_sql = "SELECT 
                                        s.survey_id,
                                        s.survey_title,
                                        s.survey_description,
                                        s.survey_start_date,
                                        s.survey_end_date,
                                        s.survey_created_at,
                                        st.survey_type_id,
                                        st.title,
                                        st.survey_file_path,
                                        st.survey_videourl
                                        
                                   FROM 
                                        " . $this->survey_table . " s
                                   LEFT JOIN
                                        " . $this->survey_type_table . " st ON s.survey_id = st.survey_id 
                                   JOIN
                                       " . $this->assignment_table . " sa ON s.survey_id = sa.survey_id      
                                        ";

        $doctor_specialisation_sql = "";
        if(!empty($doctor_specialisation_ids)) {
            $doctor_specialisation_sql = " OR (
                                CONCAT(',', `survey_assignment_value`, ',') REGEXP ',(".$doctor_specialisation_ids."),' AND  
                                survey_assignment_type = 3)";
        }
        $doctor_qua_sql = "";
        if(!empty($doctor_qua_ids)) {
            $doctor_qua_sql = " OR (
                                CONCAT(',', `survey_assignment_value`, ',') REGEXP ',(" . $doctor_qua_ids . "),' AND  
                                survey_assignment_type = 4)";
        }                                
        $get_survey_sql .= "
                           WHERE
                                s.survey_status = 1 AND 
                                survey_start_date < '".$start_date."' AND  
                                survey_end_date > '".$end_date."' AND 
                                (
                                (survey_assignment_type = 1) OR
                                (FIND_IN_SET($doctor_id, survey_assignment_value) AND  
                                survey_assignment_type = 2) 
                                {$doctor_specialisation_sql} 
                                {$doctor_qua_sql} 
                                )
                                order by s.survey_ranking asc
                                ";
        $result = $this->get_all_rows_by_query($get_survey_sql);
        return $result;
    }

    public function get_survey_questions($id) {
        $columns = 's.survey_id,s.survey_title,s.survey_description,s.survey_consent,o.survey_id,o.survey_question_id,o.survey_option_description,q.question_description,q.survey_type,q.question_status';
        $this->db->select($columns)->from($this->options_table . ' o');
        $this->db->join($this->questions_table . ' q', 'q.survey_id=o.survey_id AND q.question_id=o.survey_question_id');
        $this->db->join($this->survey_table . ' s', 's.survey_id=o.survey_id');
        $this->db->where('o.survey_id', $id);
        $this->db->where('o.survey_option_status !=', 9);
        $this->db->where('q.question_status !=', 9);
        $this->db->where('s.survey_status !=', 9);
        $query = $this->db->get();
        return $query->result();
    }

    function insert_doctor_survey_data($data){
        $this->db->insert_batch('me_doctor_survey', $data);
        return true;
    }

    function count_doctor_survey($doctor_id, $survey_id) {
        $this->db->select('doctor_survey_id')->from('me_doctor_survey');
        $this->db->where('survey_id', $survey_id);
        $this->db->where('doctor_id', $doctor_id);
        $this->db->where('survey_status !=', 9);
        $query = $this->db->get();
        return $query->num_rows();
    }

    function get_survey_by_doctor($doctor_id, $survey_id, $columns = '*') {
        $this->db->select($columns)->from('me_doctor_survey');
        $this->db->where('survey_id', $survey_id);
        $this->db->where('doctor_id', $doctor_id);
        $this->db->where('survey_status !=', 9);
        $query = $this->db->get();
        return $query->result();
    }

    function get_survey_filled($doctor_id) {
        $this->db->select('survey_id,created_at')->from('me_doctor_survey');
        $this->db->where('survey_status !=', 9);
        $this->db->where('doctor_id', $doctor_id);
        $this->db->group_by('survey_id');
        $query = $this->db->get();
        return $query->result_array();
    }

    function get_survey_question_count($survey_id) {
        $this->db->select('survey_id')->from($this->questions_table);
        if (is_array($survey_id)) {
            $this->db->where_in('survey_id', $survey_id);
        } else {
            $this->db->where('survey_id', $survey_id);
        }
        $this->db->group_by('survey_id');
        $query = $this->db->get();
        return $query->result_array();
    }

    
}