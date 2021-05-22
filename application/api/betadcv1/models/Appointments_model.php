<?php

class Appointments_model extends MY_Model {

    function __construct() {
        parent::__construct();
    }

    /**
     * Description :- This function is used to get the appointment of 
     * all patient of the the particular doctor and clinic wise
     * 
     * 
     * 
     * @param type $array
     * @return type
     */
    public function get_appointments_list($array) {

        $get_appointment_sql = "SELECT 
                                    u.user_id,
                                    u.user_first_name,
                                    u.user_last_name,
                                    u.user_email,
                                    u.user_phone_number,
                                    pu.user_phone_number as parent_user_phone_number,
                                    c.clinic_color_code,
                                    c.clinic_name,
                                    appointment_doctor_user_id,
                                    appointment_clinic_id,
                                    appointment_type,
                                    appointment_from_time,
                                    appointment_to_time,
                                    appointment_date,
                                    appointment_id,
                                    appointment_type
                                FROM 
                                    " . TBL_APPOINTMENTS . " 
                                LEFT JOIN
                                    " . TBL_USERS . " AS u ON appointment_user_id = u.user_id
                                LEFT JOIN
                                    " . TBL_PATIENT_FAMILY_MEMBER_MAPPING . " AS pfm ON pfm.patient_id = u.user_id AND pfm.mapping_status=1
                                LEFT JOIN
                                    " . TBL_USERS . " AS pu ON pu.user_id = pfm.parent_patient_id
                                LEFT JOIN
                                    " . TBL_CLINICS . " AS c ON c.clinic_id = appointment_clinic_id     
                                WHERE 
                                    appointment_date between '" . $array['start_date'] . "' AND '" . $array['end_date'] . "' AND
                                    appointment_status = '1' AND
                                    u.user_status = '1' 
                                    
                                ";

        if (!empty($array['patient_id'])) {
            $get_appointment_sql .= " AND appointment_user_id='" . $array['patient_id'] . "'";
        }

        if (!empty($array['doctor_id'])) {
            $get_appointment_sql .= " AND appointment_doctor_user_id in(" . $array['doctor_id'] . ")";
        }
        if (!empty($array['clinic_id'])) {
            $get_appointment_sql .= " AND appointment_clinic_id='" . $array['clinic_id'] . "'";
        }
        if (!empty($array['clinic_id_arr'])) {
            $get_appointment_sql .= " AND appointment_clinic_id IN(" . implode(',', $array['clinic_id_arr']) . ")";
        }
        $get_appointment_sql .= " GROUP BY appointment_id ORDER BY pfm.created_at";
        // echo $get_appointment_sql;die;
        $get_appointments_list = $this->get_all_rows_by_query($get_appointment_sql);

        return $get_appointments_list;
    }

    /**
     * Description :- This function is used to get the appointments of the patient
     * 
     * 
     * 
     * @param type $request
     * @return type
     */
    public function get_appointments($request) {
        $appointment_query = "
             SELECT
                user_id,                
                appointment_id,
                appointment_clinic_id,
                appointment_date,
                appointment_type,
                appointment_from_time,
                appointment_to_time,
                user_first_name,
                user_last_name,
                user_photo,
                user_photo_filepath,
                user_phone_number,
                GROUP_CONCAT(DISTINCT(specialization_title)) as specialization,
                GROUP_CONCAT(doctor_detail_speciality) as speciality,
                GROUP_CONCAT(DISTINCT(doctor_qualification_degree)) as doctor_qualification,
                doctor_detail_year_of_experience,
                doctor_clinic_mapping_fees,
                address_name,
                address_city_id,
                address_state_id,
                address_country_id,
                address_latitude,
                address_longitude
            FROM 
                " . TBL_USERS . " 
            JOIN 
                " . TBL_APPOINTMENTS . "
            ON 
                appointment_doctor_user_id=user_id AND appointment_status=1                               
            JOIN 
                " . TBL_CLINICS . " 
            ON 
                clinic_id=appointment_clinic_id AND clinic_status=1
            JOIN 
                " . TBL_DOCTOR_CLINIC_MAPPING . " 
            ON 
                doctor_clinic_mapping_clinic_id=clinic_id AND doctor_clinic_mapping_user_id=user_id AND doctor_clinic_mapping_status = 1
            LEFT JOIN 
                " . TBL_ADDRESS . " 
            ON 
                address_user_id=clinic_id AND address_status=1 AND address_type=2
            LEFT JOIN 
                " . TBL_DOCTOR_DETAILS . " 
            ON 
                doctor_detail_doctor_id=user_id AND 
                doctor_detail_status=1    
            LEFT JOIN 
                " . TBL_DOCTOR_SPECIALIZATIONS . " 
            ON 
                doctor_specialization_doctor_id=user_id AND 
                doctor_specialization_status=1
            LEFT JOIN 
                " . TBL_DOCTOR_EDUCATIONS . " 
            ON 
                doctor_qualification_user_id=user_id AND 
                doctor_qualification_status=1
            LEFT JOIN 
                " . TBL_SPECIALISATIONS . " 
            ON 
                doctor_specialization_specialization_id=specialization_id AND 
                specialization_status=1 ";

        $appointment_query.= "
            WHERE
                appointment_user_id='" . $request['patient_id'] . "'
            ";

        if (!empty($request['appointment_type'])) {
            $appointment_query.=" AND appointment_type=" . $request['appointment_type'] . " ";
        }
        $appointment_query.=" GROUP BY appointment_id ORDER BY appointment_date DESC ";

        if (empty($request['page'])) {
            return $this->get_count_by_query($appointment_query);
        }

        $limit = " LIMIT " . (($request['page'] - 1) * $request['per_page']) . "," . $request['per_page'] . " ";
        $appointment_query.=$limit;
        $appointment_data = $this->get_all_rows_by_query($appointment_query);
        return $appointment_data;
    }

    /**
     * Description :- THis function is used to get the appointment date 
     * based on the doctor and clinic wise
     * 
     * 
     * 
     * @param type $requested_data
     * @return type
     */
    public function get_patient_appointment_data($requested_data) {

        $get_patient_app_date_query = " SELECT 
                                            a.appointment_id,
                                            a.appointment_date,
											a.appointment_from_time,
											a.appointment_to_time,
											a.appointment_state,
                                            a.appointment_is_import,
                                            a.is_done,
											a.appointment_payment_state,
                                            a.appointment_type, 
                                            ch.call_end_date_time, 
                                            ch.call_start_date_time, 
                                            ch.doctor_start_date_time, 
                                            ch.patient_start_date_time 
                                        FROM 
                                            " . TBL_APPOINTMENTS . " a
                                        LEFT JOIN me_teleconsultant_call_history ch
                                        ON ch.appointment_id=a.appointment_id 
                                        WHERE 
                                            a.appointment_user_id = '" . $requested_data['patient_id'] . "'
                                        AND
                                            a.appointment_clinic_id = '" . $requested_data['clinic_id'] . "' 
                                        AND
                                            a.appointment_doctor_user_id = '" . $requested_data['doctor_id'] . "' 
                                        AND 
                                           a.appointment_status != 9 
                                        ORDER BY 
                                           CONCAT(a.appointment_date, ' ',a.appointment_from_time) DESC ";

        $get_total_records = $this->get_count_by_query($get_patient_app_date_query);

        if (!empty($requested_data['page'])) {
            $get_patient_app_date_query .= " LIMIT  " . (($requested_data['page'] - 1) * $requested_data['per_page']) . "," . $requested_data['per_page'] . " ";
        }

        $get_patient_app_data = $this->get_all_rows_by_query($get_patient_app_date_query);
        foreach ($get_patient_app_data as $key => $value) {
            $get_patient_app_data[$key]['appointment_date_show'] = date('d/m/Y', strtotime($value['appointment_date']));
        }
        $return_array = array(
            'data' => $get_patient_app_data,
            'no_of_records' => $get_total_records
        );

        return $return_array;
    }

    /**
     * Description :- This function is used to get the detail of the appointment
     * 
     * 
     * 
     * @param type $requested_data
     * @return type
     */
    public function get_appointment_detail($requested_data) {

        $column = 'appointment_from_time, 
                   appointment_date,
                   appointment_user_id,
                   appointment_doctor_user_id,
                   appointment_type,
                   appointment_clinic_id';

        $where_array = array(
            'appointment_id' => $requested_data['appointment_id'],
            'appointment_user_id' => $requested_data['appointment_user_id']
        );

        if (!empty($requested_data['appointment_status'])) {
            $where_array['appointment_status'] = $requested_data['appointment_status'];
        }

        $get_appointment_detail = $this->get_single_row(TBL_APPOINTMENTS, $column, $where_array);

        return $get_appointment_detail;
    }

    public function get_appointed_patient_details($requested_data) {

        $where = array(
            "user_id" => $requested_data['user_id'],
            "user_status !=" => 9
        );

        $column = 'user_first_name,
                   user_last_name,
                   user_email,
                   user_photo_filepath,
                   user_phone_number,
                   user_gender,  
                   user_details_weight,
                   user_details_blood_group,
                   user_details_other_allergies,
                   user_details_medicine_allergies,
                   user_details_food_allergies,
                   user_details_height,
                   user_details_smoking_habbit,
                   user_details_alcohol,
                   user_details_dob,
                   user_details_surgeries,
                   user_details_emergency_contact_person,
                   user_details_emergency_contact_number
                   ';

        $join_array = array(
            TBL_USER_DETAILS => 'user_id = user_details_user_id',
        );

        $result = $this->get_single_row(TBL_USERS, $column, $where, $join_array, 'LEFT');
		return $result;
    }
    
    public function get_appointment_detail_byid($appointment_id, $column = '') {
        if(empty($column))
            $column = 'patient_mail_flag';
        $where_array = array('appointment_id' => $appointment_id);
        $get_appointment_detail = $this->get_single_row(TBL_APPOINTMENTS, $column, $where_array);
        return $get_appointment_detail;
    }

    public function check_appointment_exist($where) {
        $this->db->select('appointment_id,vital_report_id,vital_report_appointment_id')->from('me_appointments');
        $this->db->join('me_vital_reports', 'vital_report_appointment_id=appointment_id AND vital_report_status=1', 'LEFT');
        $this->db->where('appointment_user_id', $where['patient_id']);
        $this->db->where('appointment_doctor_user_id', $where['doctor_id']);
        $this->db->where('appointment_clinic_id', $where['clinic_id']);
        $this->db->where('appointment_date', $where['date']);
        $this->db->where_in('appointment_type', [1,4]);
        $this->db->where('appointment_status', 1);
        $this->db->order_by('appointment_id', 'DESC');
        $query = $this->db->get();
        return $query->row();
    }

    public function get_previous_vitals($where) {
        $this->db->select('vital_report_id,vital_report_appointment_id,vital_report_date,vital_report_spo2,vital_report_weight,vital_report_bloodpressure_systolic,vital_report_bloodpressure_diastolic,vital_report_bloodpressure_type,vital_report_pulse,vital_report_temperature,vital_report_temperature_type,vital_report_temperature_taken,vital_report_resp_rate')->from('me_vital_reports');
        $this->db->where('vital_report_user_id', $where['patient_id']);
        $this->db->where('vital_report_doctor_id', $where['doctor_id']);
        $this->db->where('vital_report_clinic_id', $where['clinic_id']);
        $this->db->group_start();
        $this->db->where('vital_report_date !=', get_display_date_time('Y-m-d'));
        $this->db->or_where('vital_report_appointment_id IS NULL');
        $this->db->group_end();
        $this->db->where('DATE(CONVERT_TZ(vital_report_created_at,"+00:00","+05:30"))', get_display_date_time('Y-m-d'));
        $this->db->where('vital_report_status', 1);
        $query = $this->db->get();
        return $query->result();
    }
}
