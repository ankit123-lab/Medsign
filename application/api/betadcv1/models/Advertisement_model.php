<?php

class Advertisement_model extends MY_Model {

    protected $advertisement_table;
    protected $advertisement_type;
    protected $advertisement_assignment;
    
    function __construct() {
        parent::__construct();
        $this->advertisement_table = TBL_ADVERTISEMENT;
        $this->advertisement_type = TBL_ADVERTISEMENT_TYPE;
        $this->advertisement_assignment = TBL_ADVERTISEMENT_ASSIGNMENT;
    }

    /**
     * Description :- This function is used to get advertisement as per condition
     * 
     * 
     * @param $doctor_id $start_date $end_date $doctor_specialisation_ids $doctor_qua_ids
     * @return array
     */
    public function get_advertisement($doctor_id = 0, $start_date, $end_date, $doctor_specialisation_ids, $doctor_qua_ids) {
        $result = array();
        $get_advertisement_sql = "SELECT 
                                        `me_advertisement`.`advertisement_id`,
                                        advertisement_title,
                                        advertisement_descriptions,
                                        advertisement_rotate_time,
                                        advertisement_start_date,
                                        advertisement_end_date,
                                        advertisement_type_id,
                                        advertisement_type,
                                        advertisement_filepath,
                                        advertisement_text,
                                        advertisement_videourl,
                                        advertisement_url,
                                        advertisement_image_rotate_timing
                                   FROM 
                                        " . $this->advertisement_type . " 
                                   JOIN
                                        " . $this->advertisement_table . " ON " . $this->advertisement_table . ".advertisement_id = " . $this->advertisement_type . ".advertisement_id 
                                   JOIN
                                       " . $this->advertisement_assignment . " ON " . $this->advertisement_table . ".advertisement_id = " . $this->advertisement_assignment . ".advertisement_id      
                                        ";

        $doctor_specialisation_sql = "";
        if(!empty($doctor_specialisation_ids)) {
            $doctor_specialisation_sql = " OR (
                                CONCAT(',', `advertisement_assignment_value`, ',') REGEXP ',(".$doctor_specialisation_ids."),' AND  
                                advertisement_assignment_type = 3)";
        }
        $doctor_qua_sql = "";
        if(!empty($doctor_qua_ids)) {
            $doctor_qua_sql = " OR (
                                CONCAT(',', `advertisement_assignment_value`, ',') REGEXP ',(" . $doctor_qua_ids . "),' AND  
                                advertisement_assignment_type = 4)";
        }                                
        $get_advertisement_sql .= "
                           WHERE
                                advertisement_status = 1 AND 
                                advertisement_start_date < '".$start_date."' AND  
                                advertisement_end_date > '".$end_date."' AND 
                                (
                                (advertisement_assignment_type = 1) OR
                                (FIND_IN_SET($doctor_id, advertisement_assignment_value) AND  
                                advertisement_assignment_type = 2) 
                                {$doctor_specialisation_sql} 
                                {$doctor_qua_sql} 
                                )
                                order by advertisement_ranking asc
                                ";
        $result = $this->get_all_rows_by_query($get_advertisement_sql);
        return $result;
    }
}