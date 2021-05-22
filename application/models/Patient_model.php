<?php

class Patient_model extends MY_Model {

    public function __construct() {
        parent::__construct();
    }

    public function get_patient_by_phone($phone) {
        $this->db->select("user_id,user_first_name,user_last_name,user_phone_number,user_password,user_email,user_plan_expiry_date")->from("me_users");
        $this->db->where("user_status !=", 9);
        $this->db->where("user_type", 1);
        $this->db->where("user_phone_number", $phone);
        $query = $this->db->get();
        if($query->num_rows() > 0)
            return $query->row();
        else
            return false;
    }

    public function phone_exist($phone, $user_id = '') {
        $this->db->select("user_id")->from("me_users");
        $this->db->where("user_status !=", 9);
        if(!empty($user_id))
            $this->db->where("user_id !=", $user_id);
        $this->db->where("user_type", 1);
        $this->db->where("user_phone_number", $phone);
        $query = $this->db->get();
        if($query->num_rows() > 0)
            return true;
        else
            return false;
    }

    public function email_exist($email, $user_id = '') {
        $this->db->select("user_id")->from("me_users");
        $this->db->where("user_status !=", 9);
        if(!empty($user_id))
            $this->db->where("user_id !=", $user_id);
        $this->db->where("user_type", 1);
        $this->db->where("LOWER(user_email)", strtolower($email));
        $query = $this->db->get();
        if($query->num_rows() > 0)
            return true;
        else
            return false;
    }

    public function get_patient_all_details($patient_id) {
        $this->db->select("
            u.user_id,
            u.user_first_name,
            u.user_last_name,
            u.user_email,
            u.user_phone_number,
            u.user_gender,
            a.address_name,
            a.address_name_one,
            a.address_name,
            a.address_locality,
            a.address_pincode,
            a.address_state_id,
            a.address_city_id,
            ud.user_details_dob,
            ud.user_details_height,
            ud.user_details_weight,
            ud.user_details_languages_known,
            ud.user_details_blood_group,
            ud.user_details_emergency_contact_person,
            ud.user_details_emergency_contact_number,
            ud.user_details_marital_status,
            ud.user_details_occupation,
            ud.user_details_id_proof_type,
            ud.user_details_id_proof_detail,
            ud.user_details_id_proof_image,
            ud.user_details_food_preference,
            ud.user_details_alcohol,
            ud.user_details_smoking_habbit,
            ud.user_details_activity_level,
            ud.user_details_activity_days,
            ud.user_details_activity_hours,
            ud.user_details_chronic_diseases,
            ud.user_details_injuries,
            ud.user_details_surgeries,
            ud.user_details_food_allergies,
            ud.user_details_medicine_allergies,
            ud.user_details_other_allergies,
            ");
        $this->db->from('me_users u');
        $this->db->join("me_address a", "
                a.address_user_id=u.user_id AND 
                a.address_type=1 AND 
                a.address_status=1", "LEFT");
        $this->db->join("me_user_details ud", "ud.user_details_user_id=u.user_id AND ud.user_details_status = 1", "LEFT");
        $this->db->where("u.user_type", 1);
        $this->db->where("u.user_status !=9");
        $this->db->where("u.user_id", $patient_id);
        $query = $this->db->get();
        return $query->row();
    }

    public function get_reports($user_id, $is_total = false, $limit = '', $start = '') {
        $this->db->select("
            file_report_id,
            file_report_name,
            file_report_date,
            report_type_name,
            u1.user_first_name as patient_first_name,
            u1.user_last_name as patient_last_name,
            CONCAT(u1.user_first_name,' ',u1.user_last_name) as user_patient_name,
            u.user_type,
            CONCAT(u.user_first_name,' ',u.user_last_name) as user_name,
            file_report_share_status,
            file_report_doctor_user_id as created_by
        ");
        $this->db->from("me_files_reports");
        $this->db->join("me_users u", "file_report_doctor_user_id = u.user_id");
        $this->db->join("me_users u1", "file_report_user_id = u1.user_id");
        $this->db->join("me_report_types", "file_report_report_type_id = report_type_id");
        if($this->input->get('search_txt')){
            $this->db->group_start();
            $this->db->like("LOWER(file_report_name)", strtolower($this->input->get('search_txt')));    
            $this->db->or_like("LOWER(report_type_name)", strtolower($this->input->get('search_txt')));
            $this->db->group_end();
        }
        $this->db->where("file_report_status !=", 9);
        $this->db->where("file_report_share_status", 1);
        $this->db->where("file_report_user_id", $user_id);
        $this->db->order_by('file_report_date', 'DESC');
        $this->db->order_by('file_report_id', 'DESC');
        if(!$is_total){
            $this->db->limit($limit, $start);
        }
        $query = $this->db->get();
        if($is_total){
            return $query->num_rows();
        } else {
            return $query->result();
        }
    }

    public function get_report_by_id($report_id) {
        $this->db->select("
            file_report_id,
            file_report_name,
            file_report_date,
            report_type_name,
            u1.user_first_name as patient_first_name,
            u1.user_last_name as patient_last_name,
            CONCAT(u1.user_first_name,' ',u1.user_last_name) as user_patient_name,
            u.user_type,
            CONCAT(u.user_first_name,' ',u.user_last_name) as user_name,
            file_report_share_status,
            file_report_doctor_user_id as created_by
        ");
        $this->db->from("me_files_reports");
        $this->db->join("me_users u", "file_report_doctor_user_id = u.user_id");
        $this->db->join("me_users u1", "file_report_user_id = u1.user_id");
        $this->db->join("me_report_types", "file_report_report_type_id = report_type_id");
        $this->db->where("file_report_status !=", 9);
        $this->db->where("file_report_share_status", 1);
        $this->db->where("file_report_id", $report_id);
        $query = $this->db->get();
        return $query->row();
    }

    public function get_report_type() {
        $this->db->select("report_type_id, report_type_name")->from("me_report_types");
        $this->db->where("report_type_status", 1);
        $this->db->where_not_in("report_type_id", [11,13]);
        $this->db->order_by('report_type_order_by');
        $query = $this->db->get();
        return $query->result();
    }

    public function get_appointments_list($user_id, $is_total = false, $limit = '', $start = '') {
        $this->db->select("
            a.appointment_id,
            a.appointment_date,
            a.appointment_from_time,
            a.patient_mail_flag,
            CONCAT(u.user_first_name,' ',u.user_last_name) as doctor_name,
            at.appointment_type_name_en,
            c.clinic_name,
            addre.address_name,
            addre.address_name_one,
            addre.address_locality,
            addre.address_pincode,
            city.city_name,
            state.state_name,
            dd.doctor_detail_speciality,
            vr.vital_report_id,
            cnr.clinical_notes_reports_id,
            pr.prescription_id,
            lr.lab_report_id,
            prdr.procedure_report_id,
        ");
        $this->db->from("me_appointments a");
        $this->db->join("me_users u", "a.appointment_doctor_user_id = u.user_id");
        $this->db->join("me_appointment_type at", "at.appointment_type_id = a.appointment_type");
        $this->db->join("me_clinic c", "c.clinic_id = a.appointment_clinic_id");
        $this->db->join("me_doctor_details dd", "dd.doctor_detail_doctor_id = a.appointment_doctor_user_id");
        $this->db->join("me_address addre", "addre.address_user_id = c.clinic_id AND addre.address_type=2");
        $this->db->join("me_city city", "addre.address_city_id = city.city_id", "LEFT");
        $this->db->join("me_state state", "addre.address_state_id = state.state_id", "LEFT");
        $this->db->join("me_vital_reports vr", "a.appointment_id = vr.vital_report_appointment_id AND vr.vital_report_status = 1 AND vr.vital_report_share_status=1", "LEFT");
        $this->db->join("me_clinical_notes_reports cnr", "a.appointment_id = cnr.clinical_notes_reports_appointment_id AND cnr.clinical_notes_reports_status = 1 AND cnr.clinical_notes_reports_share_status=1", "LEFT");
        $this->db->join("me_prescription_reports pr", "a.appointment_id = pr.prescription_appointment_id AND pr.prescription_status = 1 AND pr.prescription_share_status=1", "LEFT");
        $this->db->join("me_lab_reports lr", "a.appointment_id = lr.lab_report_appointment_id AND lr.lab_report_status = 1 AND lr.lab_report_share_status=1", "LEFT");
        $this->db->join("me_procedure_reports prdr", "a.appointment_id = prdr.procedure_report_appointment_id AND prdr.procedure_report_status = 1 AND prdr.procedure_report_share_status=1", "LEFT");
        $this->db->where('a.appointment_user_id', $user_id);
        $this->db->where('a.appointment_status', 1);
        if($this->input->get('doctor_id'))
            $this->db->where('a.appointment_doctor_user_id', $this->input->get('doctor_id')); 
        if($this->input->get('is_past'))
            $this->db->where('a.appointment_date <', get_display_date_time("Y-m-d"));
        else
            $this->db->where('a.appointment_date >=', get_display_date_time("Y-m-d"));
        if($this->input->get('appointment_type_id'))
            $this->db->where('a.appointment_type', $this->input->get('appointment_type_id'));
        if($this->input->get('appointment_date')){
            $arr = explode('/', $this->input->get('appointment_date'));
            if(count($arr) == 3)
                $this->db->where('a.appointment_date', $arr[2].'-'.$arr[1].'-'.$arr[0]);
        }
        $this->db->group_by('a.appointment_id');
        $this->db->order_by('a.appointment_date', 'DESC');
        $this->db->order_by('a.appointment_from_time', 'DESC');
        if(!$is_total){
            $this->db->limit($limit, $start);
        }
        $query = $this->db->get();
        if($is_total){
            return $query->num_rows();
        } else {
            return $query->result();
        }
    }

    public function get_patient_doctors($user_id) {
        $this->db->select("
            u.user_id,
            CONCAT(u.user_first_name,' ',u.user_last_name) as doctor_name,
        ");
        $this->db->from("me_appointments a");
        $this->db->join("me_users u", "a.appointment_doctor_user_id = u.user_id");
        $this->db->where('a.appointment_user_id', $user_id);
        $this->db->group_by('a.appointment_doctor_user_id');
        $query = $this->db->get();
        return $query->result();
    }

    public function get_primary_doctor($user_id) {
        $this->db->select("
            d.user_id as doctor_id,
            CONCAT(d.user_first_name,' ',d.user_last_name) as doctor_name,
            dd.doctor_detail_speciality,
            a.address_name,
            a.address_name_one,
            a.address_pincode,
            a.address_locality,
            c.clinic_name,
            dcm.doctor_clinic_mapping_tele_fees,
            dcm.doctor_clinic_mapping_fees,
            GROUP_CONCAT(DISTINCT(dq.doctor_qualification_degree) ORDER BY doctor_qualification_id ASC) as doctor_qualification_degree,
            city.city_name,
            state.state_name,
            dpm.doctor_payment_mode_id
        ");
        $this->db->from("me_users u");
        $this->db->join("me_users d", "d.user_id = u.user_source_id");
        $this->db->join("me_doctor_details dd", "d.user_id = dd.doctor_detail_doctor_id");
        $this->db->join("me_doctor_clinic_mapping dcm", "d.user_id = dcm.doctor_clinic_mapping_user_id ANd dcm.doctor_clinic_mapping_role_id=1");
        $this->db->join("me_clinic c", "c.clinic_id = dcm.doctor_clinic_mapping_clinic_id");
        $this->db->join("me_address a", "a.address_user_id = c.clinic_id AND a.address_type = 2");
        $this->db->join("me_city city", "a.address_city_id = city.city_id", "LEFT");
        $this->db->join("me_state state", "a.address_state_id = state.state_id", "LEFT");
        $this->db->join("me_doctor_qualifications dq", "dq.doctor_qualification_user_id=d.user_id AND dq.doctor_qualification_status=1");
        $this->db->join("me_doctor_payment_mode_link dpm", "dpm.doctor_payment_mode_doctor_id=d.user_id AND dpm.doctor_payment_mode_status=1", "LEFT");
        $this->db->where('d.user_type', 2);
        $this->db->where('d.user_status', 1);
        $this->db->where('u.user_id', $user_id);
        $this->db->where('d.user_id IS NOT NULL');
        $this->db->group_by('d.user_id');
        $query = $this->db->get();
        return $query->row();
    }
    public function search_doctors($search, $is_total = false, $limit = '', $start = '') {
        if(empty($search['fees']) && empty($search['year_of_experience']) && empty($search['sex']) && empty($search['speciality']) && empty($search['search_txt']) && empty($search['available_today'])) {
            return $is_total ? 0 : array();
        }
        $this->db->select("
            d.user_id as doctor_id,
            CONCAT(d.user_first_name,' ',d.user_last_name) as doctor_name,
            dd.doctor_detail_speciality,
            TIMESTAMPDIFF(YEAR, dd.doctor_detail_year_of_experience, NOW()) as year_of_experience,
            a.address_name,
            a.address_name_one,
            a.address_pincode,
            a.address_locality,
            c.clinic_name,
            c.clinic_id,
            dcm.doctor_clinic_mapping_tele_fees,
            dcm.doctor_clinic_mapping_fees,
            GROUP_CONCAT(DISTINCT(dq.doctor_qualification_degree) ORDER BY doctor_qualification_id ASC) as doctor_qualification_degree,
            city.city_name,
            state.state_name,
            dpm.doctor_payment_mode_id
        ");
        $this->db->from("me_users d");
        $this->db->join("me_doctor_details dd", "d.user_id = dd.doctor_detail_doctor_id");
        $this->db->join("me_doctor_clinic_mapping dcm", "d.user_id = dcm.doctor_clinic_mapping_user_id ANd dcm.doctor_clinic_mapping_role_id=1");
        $this->db->join("me_clinic c", "c.clinic_id = dcm.doctor_clinic_mapping_clinic_id");
        $this->db->join("me_address a", "a.address_user_id = c.clinic_id AND a.address_type = 2");
        $this->db->join("me_city city", "a.address_city_id = city.city_id", "LEFT");
        $this->db->join("me_state state", "a.address_state_id = state.state_id", "LEFT");
        $this->db->join("me_doctor_qualifications dq", "dq.doctor_qualification_user_id=d.user_id AND dq.doctor_qualification_status=1", "LEFT");
        $this->db->join("me_doctor_payment_mode_link dpm", "dpm.doctor_payment_mode_doctor_id=d.user_id AND dpm.doctor_payment_mode_clinic_id=c.clinic_id AND dpm.doctor_payment_mode_status=1", "LEFT");
        if(!empty($search['available_today'])) {
            $this->db->join("me_doctor_availability da", "d.user_id = da.doctor_availability_user_id 
                AND da.doctor_availability_appointment_type = '1' 
                AND da.doctor_availability_status = 1", "LEFT");
            $this->db->where('da.doctor_availability_week_day', date('N', strtotime(get_display_date_time("Y-m-d H:i:s"))));
        }
        $this->db->where('d.user_type', 2);
        $this->db->where('d.user_status', 1);
        if(!empty($search['fees'])) {
            $fees_array = explode('-', $search['fees']);
            $this->db->where('dcm.doctor_clinic_mapping_fees >=', $fees_array[0]);
            if(!empty($fees_array[1]) && is_numeric($fees_array[1]))
                $this->db->where('dcm.doctor_clinic_mapping_fees <=', $fees_array[1]);

        }
        if(!empty($search['year_of_experience']) && $search['year_of_experience'] > 0)
            $this->db->where("TIMESTAMPDIFF(YEAR, dd.doctor_detail_year_of_experience, NOW()) =", $search['year_of_experience']);

        if(!empty($search['sex']) && $search['sex'] != 'any')
            $this->db->where("d.user_gender", $search['sex']);

        if(!empty($search['speciality'])) {
            $find_where = '';
            foreach ($search['speciality'] as $value) {
                $find_where .= "FIND_IN_SET('".$value."', dd.doctor_detail_speciality) OR ";
            }
            if($find_where != '')
                $this->db->where("(".trim($find_where, ' OR ').")");
        }

        if(!empty($search['search_txt'])) {
            $this->db->group_start();
            $this->db->like("LOWER(CONCAT(d.user_first_name,' ',d.user_last_name))", strtolower($search['search_txt']));
            $this->db->or_like("LOWER(c.clinic_name)", strtolower($search['search_txt']));
            $this->db->or_like("LOWER(a.address_name)", strtolower($search['search_txt']));
            $this->db->or_like("LOWER(city.city_name)", strtolower($search['search_txt']));
            $this->db->group_end();
        }

        if(!empty($search['primary_doctor_id']))
            $this->db->where_not_in('d.user_id', $search['primary_doctor_id']);
        $this->db->group_by('c.clinic_id');
        $this->db->order_by("LOWER(CONCAT(d.user_first_name,' ',d.user_last_name))");
        if(!$is_total){
            $this->db->limit($limit, $start);
        }
        $query = $this->db->get();
        if($is_total) {
            return $query->num_rows();
        } else {
            return $query->result();
        }
    }

    public function doctor_details($doctor_id, $clinic_id='') {
        $this->db->select("
            d.user_id as doctor_id,
            CONCAT(d.user_first_name,' ',d.user_last_name) as doctor_name,
            d.user_photo_filepath,
            dd.doctor_detail_speciality,
            TIMESTAMPDIFF(YEAR, dd.doctor_detail_year_of_experience, NOW()) as year_of_experience,
            a.address_name,
            a.address_name_one,
            a.address_pincode,
            a.address_locality,
            c.clinic_name,
            c.clinic_contact_number,
            c.clinic_id,
            dcm.doctor_clinic_mapping_tele_fees,
            dcm.doctor_clinic_mapping_fees,
            GROUP_CONCAT(DISTINCT(dq.doctor_qualification_degree) ORDER BY doctor_qualification_id ASC) as doctor_qualification_degree,
            city.city_name,
            state.state_name,
            dpm.doctor_payment_mode_id,
            s.setting_data
        ");
        $this->db->from("me_users d");
        $this->db->join("me_doctor_details dd", "d.user_id = dd.doctor_detail_doctor_id");
        $this->db->join("me_doctor_clinic_mapping dcm", "d.user_id = dcm.doctor_clinic_mapping_user_id ANd dcm.doctor_clinic_mapping_role_id=1");
        $this->db->join('me_settings s', 's.setting_user_id=d.user_id AND s.setting_type=10 AND s.setting_status=1', "LEFT");
        $this->db->join("me_clinic c", "c.clinic_id = dcm.doctor_clinic_mapping_clinic_id");
        $this->db->join("me_address a", "a.address_user_id = c.clinic_id AND a.address_type = 2");
        $this->db->join("me_city city", "a.address_city_id = city.city_id", "LEFT");
        $this->db->join("me_state state", "a.address_state_id = state.state_id", "LEFT");
        $this->db->join("me_doctor_qualifications dq", "dq.doctor_qualification_user_id=d.user_id AND dq.doctor_qualification_status=1", "LEFT");
        $this->db->join("me_doctor_payment_mode_link dpm", "dpm.doctor_payment_mode_clinic_id=c.clinic_id AND dpm.doctor_payment_mode_doctor_id=d.user_id AND dpm.doctor_payment_mode_status=1", "LEFT");
        $this->db->where('d.user_type', 2);
        $this->db->where('d.user_status', 1);
        $this->db->where('d.user_id', $doctor_id);
        if(!empty($clinic_id))
            $this->db->where('c.clinic_id', $clinic_id);
        $this->db->group_by('d.user_id');
        $query = $this->db->get();
        return $query->row();
    }

    public function get_doctor_availibility($where) {
        $this->db->select("
            doctor_availability_id,
            doctor_availability_session_1_start_time,
            doctor_availability_session_1_end_time,
            doctor_availability_session_2_start_time,
            doctor_availability_session_2_end_time,
            doctor_clinic_mapping_duration
            ")->from('me_doctor_availability');
        $this->db->join("me_doctor_clinic_mapping", "doctor_availability_clinic_id = doctor_clinic_mapping_clinic_id AND doctor_availability_user_id = doctor_clinic_mapping_user_id AND doctor_clinic_mapping_status = 1");
        $this->db->where("doctor_availability_week_day", date('N', strtotime($where['date'])));
        $this->db->where("doctor_availability_clinic_id", $where['clinic_id']);
        $this->db->where("doctor_availability_user_id", $where['doctor_id']);
        $this->db->where("doctor_availability_appointment_type", $where['appointment_type']);
        $this->db->where("doctor_availability_status", 1);
        $query = $this->db->get();
        return $query->row_array();
    }

    public function get_availibity($request) {
        $this->db->select('
            appointment_from_time,
            appointment_to_time
            ')->from('me_appointments');
        $this->db->where('appointment_doctor_user_id', $request['doctor_id']);
        $this->db->where('appointment_date', $request['date']);
        $this->db->where('appointment_status', 1);
        $query = $this->db->get();
        return $query->result_array();
    }

    public function check_block_calender($request) {
        $this->db->select('
            calender_block_id,
            calender_block_duration_type,
            calender_block_start_time,
            calender_block_end_time
            ')->from('me_calender_block');
        $this->db->where('calender_block_user_id', $request['doctor_id']);
        $this->db->where('calender_block_status', 1);
        $this->db->group_start();
        $this->db->group_start();
            $this->db->where('calender_block_duration_type', 1);
            $this->db->where('calender_block_from_date <=', $request['date']);
            $this->db->where('calender_block_to_date >=', $request['date']);
        $this->db->group_end();
        $this->db->or_group_start();
            $this->db->or_where('calender_block_duration_type', 2);
            $this->db->where('calender_block_from_date', $request['date']);
        $this->db->group_end();
        $this->db->group_end();
        $query = $this->db->get();
        // echo $this->db->last_query();die;
        return $query->result_array();
    }

    public function get_speciality() {
        $this->db->select('specialization_title AS speciality_title')->from('me_specialization');
        $this->db->where('specialization_status', 1);
        $this->db->where('specialization_parent_id >', 0);
        $this->db->group_by('specialization_title');
        $query = $this->db->get();
        // echo $this->db->last_query();die;
        return $query->result();
    }

    public function calendar_block_date_exists($data = array()) {
        $check_block_calendar_sql = " SELECT 
                calender_block_id 
          FROM 
                " . TBL_DOCTOR_CALENDER_BLOCK . " 
          WHERE 
                calender_block_user_id = '" . $data['doctor_id'] . "' 
          AND  
                calender_block_status = 1 
          AND ";
        if (!empty($data['block_calendar_id'])) {
            $check_block_calendar_sql .= " calender_block_id != '" . $data['block_calendar_id'] . "' AND ";
        }
        if ($data['block_type'] == 1) {
            $check_block_calendar_sql .= "     
                (
                    (
                        calender_block_from_date <= '" . $data['start_date'] . "' AND
                        calender_block_to_date >= '" . $data['start_date'] . "'
                    ) 
                    OR
                    (
                        calender_block_from_date <= '" . $data['end_date'] . "' AND
                        calender_block_to_date >= '" . $data['end_date'] . "'
                    )
                )    
                ";
        } else {
            $check_block_calendar_sql .= "
                (
                    (
                        calender_block_start_time <= '" . $data['start_time'] . "' AND
                        calender_block_end_time >= '" . $data['start_time'] . "'
                    )
                    OR
                    (
                        calender_block_start_time <= '" . $data['end_time'] . "' AND
                        calender_block_end_time >= '" . $data['end_time'] . "'
                    )
                )    
                AND calender_block_from_date = '" . $data['block_slot_date'] . "'  ";
        }
        $get_block_calendar_data = $this->Common_model->get_all_rows_by_query($check_block_calendar_sql);
        return $get_block_calendar_data;
    }

    public function check_doctor_available($request, $appointment_id = '') {
        $doctor_available_query = "
                        SELECT 
                            appointment_id
                        FROM 
                            me_appointments 
                        WHERE
                            appointment_doctor_user_id=" . $request['doctor_id'] . " AND 
                            appointment_status=1 AND 
                            (
                               ( appointment_from_time >='" . $request['start_time'] . "' AND appointment_from_time <'" . $request['end_time'] . "' ) ||
                               ( appointment_to_time > '" . $request['start_time'] . "' AND appointment_to_time <='" . $request['end_time'] . "' ) ||                               
                               ( '" . $request['start_time'] . "' >= appointment_from_time AND  '" . $request['end_time'] . "' <= appointment_to_time)
                            ) AND
                            appointment_date='" . $request['date'] . "' 
            ";
        if (!empty($appointment_id)) {
            $doctor_available_query.= " AND  appointment_id !=" . $appointment_id . " ";
        }
        $is_available = $this->get_all_rows_by_query($doctor_available_query);

        if (empty($is_available)) {
            return true;
        }
        return FALSE;
    }

    public function is_patient_appointments_exist($where) {
        $this->db->from(TBL_APPOINTMENTS);
        foreach ($where as $key => $value) {
            $this->db->where($key,$value);
        }
        $this->db->where('appointment_status', 1);
        $query = $this->db->get();
        if($query->num_rows() > 0) 
            return true;
        else
            return false;
    }

    public function doctor_detail($doctor_id, $clinic_id, $flag = '', $appointment_type = '') {
        $doctor_query = "
             SELECT
                user_id,
                user_first_name,
                user_last_name,
                user_photo,
                user_photo_filepath,
                user_status,
                user_phone_number,
                user_email,
                user_email_verified,
                doctor_detail_year_of_experience,
                doctor_detail_desc,                                            
                GROUP_CONCAT(DISTINCT specialization_title) as specialization,       
                GROUP_CONCAT(doctor_detail_speciality) as speciality,
                doctor_clinic_mapping_fees,
                GROUP_CONCAT(DISTINCT doctor_qualification_degree ORDER BY doctor_qualification_id ASC) as doctor_qualification_degree,
                clinic_id,                
                clinic_name,
                clinic_image,
                clinic_filepath,
                clinic_services,
                clinic_email,
                clinic_contact_number,
                doctor_clinic_doctor_session_1_start_time,
                doctor_clinic_doctor_session_1_end_time,
                doctor_clinic_doctor_session_2_start_time,
                doctor_clinic_doctor_session_2_end_time, ";

        if (!empty($appointment_type)) {
            $doctor_query .= "doctor_availability_session_1_start_time, 
                doctor_availability_session_1_end_time,
                doctor_availability_session_2_start_time,
                doctor_availability_session_2_end_time,";
        }

        $doctor_query .= "
                address_name,
                address_name_one,
                address_city_id,
                address_state_id,
                address_country_id,
                city_name,
                state_name, 
                country_name,
                doctor_clinic_mapping_fees,
                (
                    SELECT GROUP_CONCAT(language_name) FROM 
                            " . TBL_LANGUAGES . " 
                        WHERE
                            FIND_IN_SET(language_id,doctor_detail_language_id) AND language_status=1
                ) as language
            FROM 
                " . TBL_USERS . " 
            LEFT JOIN " . TBL_DOCTOR_DETAILS . " 
                ON doctor_detail_doctor_id=user_id AND doctor_detail_status=1 ";

        if (!empty($appointment_type)) {
            $doctor_query .= "
                LEFT JOIN " . TBL_DOCTOR_AVAILABILITY . "
                    ON doctor_availability_user_id = user_id 
                    AND doctor_availability_status = 1 
                    AND doctor_availability_clinic_id = '" . $clinic_id . "' 
                    AND doctor_availability_week_day = '" . date('N', time()) . "' 
                    AND doctor_availability_appointment_type  = '" . $appointment_type . "'  ";
        }

        $doctor_query .= "
            LEFT JOIN " . TBL_DOCTOR_SPECIALIZATIONS . " 
                ON doctor_specialization_doctor_id=user_id AND doctor_specialization_status=1
            LEFT JOIN " . TBL_SPECIALISATIONS . " 
                ON doctor_specialization_specialization_id=specialization_id
            LEFT JOIN " . TBL_DOCTOR_CLINIC_MAPPING . " 
                ON doctor_clinic_mapping_user_id=user_id
            LEFT JOIN " . TBL_CLINICS . " 
                ON clinic_id=doctor_clinic_mapping_clinic_id AND clinic_status=1
            LEFT JOIN " . TBL_DOCTOR_EDUCATIONS . " 
                ON doctor_qualification_user_id=user_id AND doctor_qualification_status=1
            LEFT JOIN " . TBL_ADDRESS . " 
                ON address_user_id=clinic_id AND address_type=2
            LEFT JOIN " . TBL_CITIES . "    
                ON address_city_id = city_id
            LEFT JOIN " . TBL_STATES . "        
                ON address_state_id = state_id
            LEFT JOIN " . TBL_COUNTRIES . "    
                ON state_country_id = country_id
                
            ";

        if ($flag == 1) {
            $doctor_query.="
            WHERE
                user_id='" . $doctor_id . "'  AND 
                doctor_clinic_mapping_clinic_id = '" . $clinic_id . "' ";
        } else {
            $doctor_query.="
            WHERE
                user_id='" . $doctor_id . "'  
            GROUP BY
                user_id
            ORDER BY 
                doctor_clinic_mapping_fees ASC
          
            ";
        }
        $doctor_data = $this->get_all_rows_by_query($doctor_query);
        return $doctor_data;
    }

    public function get_details_by_id($id = NULL) {
        $result = array();
        if (!empty($id)) {
            $where = array(
                "user_id" => $id,
                "user_status !=" => 9
            );
            $columns = "user_source_id,user_first_name,user_last_name,user_email_verified,user_email,user_phone_number,user_id";
            $result = $this->get_single_row('me_users', $columns, $where);
        }
        return $result;
    }

    public function get_appointment_detail($appointment_id) {
        $this->db->select("
            a.appointment_id,
            a.appointment_date,
            a.appointment_from_time,
            a.appointment_doctor_user_id,
            a.appointment_clinic_id,
            CONCAT(p.user_first_name,' ',p.user_last_name) as patient_name
            ")->from("me_appointments a");
        $this->db->join("me_users p", "p.user_id=a.appointment_user_id");
        $this->db->where('a.appointment_id', $appointment_id);
        $this->db->where('a.appointment_status', 1);
        $query = $this->db->get();
        return $query->row_array();
    }

    public function get_linked_family_members($user_id, $columns = '') {
        if(empty($columns)) {
            $columns = 'u.user_id, u.user_first_name, u.user_last_name,u.user_email,u.user_photo_filepath,u.user_phone_number,map.created_at' ;
        } 
        $this->db->select($columns, FALSE)->from(TBL_USERS . ' u');
        $this->db->join(TBL_PATIENT_FAMILY_MEMBER_MAPPING . ' map' , 'map.parent_patient_id = u.user_id');
        $this->db->where('u.user_status', 1);
        $this->db->where('u.user_type', 1);
        $this->db->where('map.mapping_status', 1);
        $this->db->where('map.patient_id',$user_id);
        $this->db->order_by('map.created_at', 'DESC');
        $query = $this->db->get();
        return $query->result();
    }

    public function vitals_data() {
        $this->datatables->select("
            v.vital_report_date,
            v.vital_report_weight,
            v.vital_report_pulse,
            v.vital_report_resp_rate,
            v.vital_report_spo2,
            v.vital_report_bloodpressure_systolic,
            v.vital_report_bloodpressure_diastolic,
            v.vital_report_temperature,
            v.vital_report_temperature_type,
            v.vital_report_created_at,
            v.vital_report_doctor_id,
            v.vital_report_id,
            CONCAT(u.user_first_name,' ',u.user_last_name) as added_name,
            u.user_type,
            '' AS action,
            ");
        $this->datatables->from('me_vital_reports v');
        $this->datatables->join('me_users u', 'u.user_id=v.vital_report_doctor_id');
        $this->datatables->where('v.vital_report_status', 1);
        $this->datatables->where('v.vital_report_share_status', 1);
        $this->datatables->where('v.vital_report_user_id', $this->patient_auth->get_user_id());
        $this->datatables->edit_column('vital_report_weight', "$1","PoundToKG(vital_report_weight)");
        $this->datatables->edit_column('vital_report_temperature', "$1","temperature_edit(vital_report_temperature,vital_report_temperature_type)");
        $this->datatables->edit_column('action', "$1","vital_edit(vital_report_created_at,vital_report_doctor_id,vital_report_id)");
        return $this->datatables->generate();
    }

    public function get_vitals($where, $is_return_arr = false) {
        $this->db->select("
            v.vital_report_date,
            v.vital_report_weight,
            v.vital_report_pulse,
            v.vital_report_resp_rate,
            v.vital_report_spo2,
            v.vital_report_bloodpressure_systolic,
            v.vital_report_bloodpressure_diastolic,
            v.vital_report_temperature,
            v.vital_report_temperature_type,
            v.vital_report_created_at,
            v.vital_report_doctor_id,
            v.vital_report_user_id,
            v.vital_report_id,
            CONCAT(u.user_first_name,' ',u.user_last_name) as added_name,
            u.user_type
            ");
        $this->db->from('me_vital_reports v');
        $this->db->join('me_users u', 'u.user_id=v.vital_report_doctor_id');
        $this->db->where('v.vital_report_status', 1);
        $this->db->where('v.vital_report_share_status', 1);
        $this->db->where('v.vital_report_user_id', $this->patient_auth->get_user_id());
        $this->db->where('v.vital_report_date >=', $where['start_date']);
        $this->db->where('v.vital_report_date <=', $where['end_date']);
        $this->db->order_by('v.vital_report_date', 'DESC');
        $query = $this->db->get();
        if($is_return_arr)
            return $query->result_array();
        else
            return $query->result();
    }

    public function get_patient_uas7_analytics($patient_id) {
        $this->db->select("pa.patient_analytics_id, d.user_id AS doctor_id, CONCAT(d.user_first_name,' ',d.user_last_name) as doctor_name");
        $this->db->from('me_patient_analytics pa');
        $this->db->join('me_users d','d.user_id = pa.patient_analytics_doctor_id');
        $this->db->where('pa.patient_analytics_user_id', $patient_id);
        $this->db->where('pa.patient_analytics_status', 1);
        $this->db->where('pa.patient_analytics_analytics_id', 308);
        $this->db->order_by('pa.patient_analytics_id', 'DESC');
        $this->db->limit(1);
        $query = $this->db->get();
        return $query->row();
    }

    public function get_patient_last_payment($columns, $where) {
        $this->db->select($columns);
        $this->db->from('me_user_payment_details');
        $this->db->where('user_id', $where['user_id']);
        $this->db->where('payment_status', 1);
        $this->db->where('detail_type', $where['detail_type']);
        $this->db->order_by('payment_id', 'DESC');
        $this->db->limit(1);
        $query = $this->db->get();
        return $query->row();
    }

    public function get_uas7_last_para($columns, $where) {
        $this->db->select($columns);
        $this->db->from('me_patient_diary');
        $this->db->where('patient_diary_patient_id', $where['patient_id']);
        $this->db->where('patient_diary_status', 1);
        $this->db->where('patient_diary_type', $where['diary_type']);
        $this->db->order_by('patient_diary_date', 'DESC');
        $this->db->limit(1);
        $query = $this->db->get();
        return $query->row();
    }

    public function get_uas7_param_by_date($date_arr, $patient_id) {
        $this->db->select("patient_diary_id,patient_diary_label,patient_diary_date");
        $this->db->from('me_patient_diary');
        $this->db->where('patient_diary_patient_id', $patient_id);
        $this->db->where('patient_diary_status', 1);
        $this->db->where('patient_diary_type', 1);
        $this->db->where_in('patient_diary_date', $date_arr);
        $this->db->order_by('patient_diary_date', 'DESC');
        // $this->db->limit(1);
        $query = $this->db->get();
        // echo $this->db->last_query();die;
        return $query->result();
    }

    public function uas7_bulk_update($data) {
        $this->db->update_batch('me_patient_diary',$data, 'patient_diary_id'); 
    }

    public function search_medsign_doctors($search) {
        if(empty($search))
            return [];
        $this->db->select("
            CONCAT(user_first_name, ' ', user_last_name) as doctor_name,
            user_id
            ")->from('me_users');
        $this->db->join("me_doctor_clinic_mapping", "user_id = doctor_clinic_mapping_user_id", "LEFT");
        $this->db->where("(doctor_clinic_mapping_role_id = 1 OR doctor_clinic_mapping_role_id IS NULL)");
        $this->db->where('user_type', 2);
        $this->db->like("LOWER(CONCAT(user_first_name, ' ', user_last_name))", $search);
        $this->db->where('user_status', 1);
        $this->db->group_by("user_id");
        $this->db->limit(50);
        $query = $this->db->get();
        return $query->result();
    }

    public function get_global_setting_by_key_arr($key_arr) {
        $this->db->select('global_setting_key,global_setting_value');
        $this->db->from('me_global_settings');
        $this->db->where('global_setting_status', 1);
        $this->db->where_in('global_setting_key', $key_arr);
        $query = $this->db->get();
        return $query->result_array();
    }

    public function update_global_setting_by_key($key, $data) {
        $this->db->where('global_setting_key', $key);
        $this->db->update('me_global_settings', $data);
    }

    function get_payment_details_by_payment_id($payment_id) {
        $this->db->select('
            u.user_first_name,
            u.user_last_name,
            u.user_email,
            a.address_name,
            a.address_name_one,
            a.address_locality,
            a.address_pincode,
            upd.payment_id,
            upd.user_id,
            upd.invoice_no,
            upd.created_at,
            upd.plan_start_date,
            upd.plan_end_date,
            upd.sub_total,
            upd.settlement_discount,
            upd.tax_cgst_percent,
            upd.tax_sgst_percent,
            upd.tax_igst_percent,
            upd.tax_cgst_amount,
            upd.tax_sgst_amount,
            upd.tax_igst_amount,
            upd.discount_amount,
            upd.paid_amount,
            upd.razorpay_payment_id,
            upd.razorpay_order_id,
            upd.payment_status,
            upd.sub_plan_name,
            upd.sub_plan_validity,
            upd.receipt_url,
            upd.detail_type,
            city.city_name,
            s.state_name,
        ');
        $this->db->from("me_user_payment_details upd");
        $this->db->join("me_users u", "u.user_id=upd.user_id");
        $this->db->join("me_address a", "
                a.address_user_id=u.user_id AND 
                a.address_type=1 AND 
                a.address_status=1", "LEFT");
        $this->db->join("me_city city", "city.city_id=a.address_city_id", "LEFT");
        $this->db->join("me_state s", "s.state_id=a.address_state_id", "LEFT");
        $this->db->where("upd.payment_status", 1);
        $this->db->where('payment_id', $payment_id);
        $query = $this->db->get();
        return $query->row();
    }

    public function get_uas7_para_data($where, $is_total = false, $limit = '', $start = '') {
        $this->db->select("
            patient_diary_patient_id,
            patient_diary_added_by,
            patient_diary_date,
            patient_diary_created_at,
            GROUP_CONCAT(pd.patient_diary_id) AS patient_diary_id,
            GROUP_CONCAT(pd.patient_diary_label) AS patient_diary_label,
            GROUP_CONCAT(pd.patient_diary_value) AS patient_diary_value
            ");
        $this->db->from('me_patient_diary pd');
        $this->db->where('pd.patient_diary_patient_id', $where['patient_id']);
        $this->db->where('pd.patient_diary_type', 1);
        $this->db->where('pd.patient_diary_status', 1);
        $this->db->group_by('pd.patient_diary_date');
        $this->db->order_by('pd.patient_diary_date', "DESC");
        if(!empty($where['is_get_all'])) {
            $query = $this->db->get();
            return $query->result();
        } else {
            if(!$is_total){
                $this->db->limit($limit, $start);
            }
            $query = $this->db->get();
            if($is_total){
                return $query->num_rows();
            } else {
                return $query->result();
            }
        }
    }

    public function get_uas7_para_by_ids($where) {
        $this->db->select("
            patient_diary_date,
            patient_diary_doctor_id,
            patient_diary_is_medsign_doctor,
            patient_diary_created_at,
            GROUP_CONCAT(pd.patient_diary_id) AS patient_diary_id,
            GROUP_CONCAT(pd.patient_diary_label) AS patient_diary_label,
            GROUP_CONCAT(pd.patient_diary_value) AS patient_diary_value
            ");
        $this->db->from('me_patient_diary pd');
        $this->db->where('pd.patient_diary_patient_id', $where['patient_id']);
        $this->db->where_in('pd.patient_diary_id', $where['ids']);
        $this->db->where('pd.patient_diary_type', 1);
        $this->db->where('pd.patient_diary_status', 1);
        $this->db->group_by('pd.patient_diary_date');
        $this->db->order_by('pd.patient_diary_date', "DESC");
        $query = $this->db->get();
        return $query->result();
    }

    public function get_uas7_doctor($where) {
        $this->db->select("
            pd.patient_diary_is_medsign_doctor,
            CONCAT(u.user_first_name,' ',u.user_last_name) as doctor_name,
            u.user_email,
            nms.non_medsign_doctor_name,
            nms.non_medsign_doctor_email,
            ");
        $this->db->from('me_patient_diary pd');
        $this->db->join("me_users u", "u.user_id=pd.patient_diary_doctor_id", "LEFT");
        $this->db->join("me_non_medsign_doctor nms", "nms.non_medsign_doctor_id=pd.patient_diary_doctor_id", "LEFT");
        $this->db->where('pd.patient_diary_patient_id', $where['patient_id']);
        $this->db->where('pd.patient_diary_status', 1);
        $this->db->where('pd.patient_diary_type', $where['diary_type']);
        $this->db->order_by('pd.patient_diary_date', 'DESC');
        $this->db->limit(1);
        $query = $this->db->get();
        return $query->row();
    }

    public function get_uas7_all_data_by_patient_id($patient_ids) {
        $this->db->select("
            patient_diary_date,
            pd.patient_diary_patient_id,
            GROUP_CONCAT(pd.patient_diary_id) AS patient_diary_id,
            GROUP_CONCAT(pd.patient_diary_label) AS patient_diary_label,
            GROUP_CONCAT(pd.patient_diary_value) AS patient_diary_value
            ");
        $this->db->from('me_patient_diary pd');
        $this->db->where_in('pd.patient_diary_patient_id', $patient_ids);
        $this->db->where('pd.patient_diary_type', 1);
        $this->db->where('pd.patient_diary_status', 1);
        $this->db->group_by(['pd.patient_diary_date','pd.patient_diary_patient_id']);
        $this->db->order_by('pd.patient_diary_date', "DESC");
        $query = $this->db->get();
        return $query->result();
    }

    public function get_next_auto_id($table_name) {
        $next = $this->db->query("SHOW TABLE STATUS LIKE '" . $table_name . "'");
        $next = $next->row(0);
        $next->Auto_increment;
        return $next->Auto_increment;
    }

    public function get_uas7_reports($date) {
        $this->db->select("
            fr.file_report_id,
            rimg.file_report_image_url,
            u.user_email,
            u.user_first_name,
            u.user_last_name,
            parent.user_email AS parent_user_email
        ");
        $this->db->from('me_files_reports fr');
        $this->db->join('me_files_reports_images rimg', 'rimg.file_report_image_file_report_id=fr.file_report_id');
        $this->db->join('me_users u', 'u.user_id=fr.file_report_user_id');
        $this->db->join('me_patient_family_member_mapping fm', 'fm.patient_id=u.user_id', "LEFT");
        $this->db->join('me_users parent', 'parent.user_id=fm.parent_patient_id', "LEFT");
        $this->db->where('fr.file_report_report_type_id', 13);
        $this->db->where('fr.file_report_status', 1);
        $this->db->where('DATE(CONVERT_TZ(fr.file_report_created_at,"+00:00","+05:30"))', $date);
        $query = $this->db->get();
        return $query->result();
    }

    public function get_family_members($user_id, $columns = '') {
        if(empty($columns)) {
            $columns = 'u.user_id, u.user_first_name, u.user_last_name,u.user_phone_number,u.user_email,map.mapping_relation' ;
        } 
        $this->db->select($columns, FALSE)->from(TBL_USERS . ' u');
        $this->db->join(TBL_PATIENT_FAMILY_MEMBER_MAPPING . ' map' , 'map.patient_id = u.user_id');
        $this->db->where('u.user_status', 1);
        $this->db->where('u.user_type', 1);
        $this->db->where('map.mapping_status', 1);
        $this->db->where('map.parent_patient_id',$user_id);
        $this->db->order_by('map.created_at', 'DESC');
        $query = $this->db->get();
        return $query->result();
    }

}