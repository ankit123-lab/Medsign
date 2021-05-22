<?php

class Reminder_model extends MY_Model {

    protected $users_table;
    protected $reminder_table;

    function __construct() {
        parent::__construct();
        $this->users_table = TBL_USERS;
        $this->reminder_table = TBL_REMINDERS;
    }

    /**
     * Description :- This function is used to add the reminder 
     * 
     
     * 
     * Modified Date :- 2018-03-07
     * 
     * @param type $reminder_array
     * @return type inserted id
     */
    public function add_reminder($reminder_array = array()) {
        if (!empty($reminder_array)) {
            $insert_id = $this->insert($this->reminder_table, $reminder_array);
            return $insert_id;
        }
        return 0;
    }

    /**
     * Description :- This function is used to update existing reminder 
     * 
     
     * 
     * Modified Date :- 2018-03-09
     * 
     * @param type $update_array
     * @return type reminder id
     */
    public function edit_reminder($update_array, $reminder_where) {
        if (!empty($update_array)) {
            $is_updated = $this->update($this->reminder_table, $update_array, $reminder_where);
            return $is_updated;
        }
        return 0;
    }

    /**
     * Description :- This function is used to get existing reminders 
     * 
     
     * 
     * Modified Date :- 2018-03-09
     * 
     * @param type $user_id
     * @return type reminder data
     */
    public function get_sync_reminders($user_id, $sync_date, $reminder_type) {

        $columns = "user_type, 
                    prescription_intake as drug_intake,
                    prescription_frequency_id as drug_drug_frequency_id,
                    drug_frequency_name,
                    
                    appointment_date,
                    appointment_from_time,
                    appointment_to_time,
                    
                    reminder_id, 
                    reminder_prescription_report_id,
                    reminder_appointment_id, 
                    reminder_type, 
                    reminder_user_id,
                    reminder_created_by, 
                    reminder_drug_id, 
                    reminder_drug_name, 
                    reminder_timing, 
                    reminder_duration,
                    reminder_day, 
                    reminder_start_date, 
                    reminder_week_day, 
                    reminder_doctor_id, 
                    reminder_doctor_name, 
                    reminder_doctor_address,
                    reminder_note, 
                    reminder_lab_report_id, 
                    reminder_lab_report_name, 
                    reminder_laboratory_id, 
                    reminder_general_title, 
                    reminder_doctor_fee,
                    reminder_created_at, 
                    reminder_modified_at,
                    reminder_status";

        $reminder_query = "
            SELECT 
                " . $columns . "
            FROM 
                " . TBL_REMINDERS . " 
            LEFT JOIN
                " . TBL_APPOINTMENTS . " 
            ON 
                reminder_appointment_id = appointment_id
            LEFT JOIN
                " . TBL_PRESCRIPTION . " 
            ON 
                reminder_prescription_report_id = prescription_id
            LEFT JOIN
                " . TBL_DRUG_FREQUENCY . " 
            ON 
                prescription_frequency_id = drug_frequency_id
            LEFT JOIN
                    " . TBL_USERS . " ON reminder_created_by = user_id
                WHERE                    
                    (
                        reminder_created_by IN (" . $user_id . ") OR
                        reminder_user_id IN (" . $user_id . ")
                    )
                      ";
        if (!empty($sync_date)) {
			$reminder_query.=" AND ((reminder_created_at >='" . $sync_date . "' OR reminder_modified_at >='" . $sync_date . "') OR 
			(reminder_type IN (2,3) AND (CONCAT(reminder_start_date,' ',reminder_timing,':00') >= '$sync_date')))";
        }
		
        /* if ($reminder_type == 2 || $reminder_type == 3) {
            $reminder_query.="
                    AND
                    (
                       CONCAT(reminder_start_date,' ',reminder_timing,':00') >= '$this->utc_time_formated'
                    )
            ";
        } */
            /* This Code used for old reminder are remove from list
             * $date = date('Y-m-d h:i:s');
            $reminder_query.="
                    AND
                    (
                       DATE_ADD(reminder_created_at, INTERVAL reminder_duration DAY) >= '$date'
                    )
            ";*/
			
            $reminder_query.=" 
                    AND
                        reminder_status=1 
                    ";
        if (!empty($reminder_type)) {
            $reminder_query.=" 
                    AND reminder_type=" . $reminder_type . "
                ";
        }
        $reminder_query .= " GROUP BY reminder_id ";
        $reminder_query .= " ORDER BY reminder_created_at DESC ";
        $reminder_data = $this->get_all_rows_by_query($reminder_query);
        return $reminder_data;
    }
    
    /**
     * Description :- THis function is used to get the chart detail
     * 
     
     * 
     * @param type $user_id
     * @param type $date
     * @return type
     */
    public function get_chart_data($user_id, $date) {
		$date = date('Y-m-d', strtotime($date));
        $prev = date('Y-m-d', strtotime($date . '-3 month'));
        $query = "
                SELECT 
                    count(reminder_record_id) as take_count,                        
                    DAY(reminder_record_date) as reminder_day,                        
                    DATE_FORMAT(reminder_record_date, '%Y-%m-%d') as reminder_record_date,
                    drug_id,
                    drug_name,
                    reminder_user_id,
                    drug_color_code
                FROM 
                    " . TBL_REMINDER_RECORDS . " 
                JOIN 
                    " . TBL_REMINDERS . " 
                ON 
                    reminder_record_reminder_id=reminder_id AND 
                    reminder_status=1 
                JOIN 
                    " . TBL_DRUGS . " 
                ON 
                    drug_id=reminder_drug_id AND drug_status=1 
                WHERE 
                    reminder_record_status=1 AND 
                    reminder_user_id IN (" . $user_id . ") AND 
                    DATE_FORMAT(reminder_record_date, '%Y-%m-%d') <='" . $date . "' AND 
                    DATE_FORMAT(reminder_record_date, '%Y-%m-%d') >='" . $prev . "' 
                GROUP BY 
                    reminder_drug_id, 
                    reminder_user_id,
                    DAY(reminder_record_date) 
                ORDER BY 
                    DATE(reminder_record_date) 
            ";
		
		$reminder_chart_data = $this->get_all_rows_by_query($query);
        return $reminder_chart_data;
    }

    public function get_reminder_records($where, $columns = '*') {
        $this->db->select($columns)->from(TBL_REMINDER_RECORDS);
        
        foreach ($where as $key => $value) {
            if(is_array($value))
                $this->db->where_in($key, $value);
            else
                $this->db->where($key, $value);
        }
        $this->db->where('reminder_record_status', 1);
        $query = $this->db->get();
        return $query->result_array();
    }
}