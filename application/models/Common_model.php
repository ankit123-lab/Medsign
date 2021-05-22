<?php

class Common_model extends MY_Model {

    public function __construct() {
        parent::__construct();
    }

    /**
     * Description : Use this function for get all countries with country id if parameter pass
     * 
     * get particular country if country id passed
     * 
     * @author Nitinkumar vaghani
     * Last modified date : 29-12-2017
     * 
     * @param type $id
     * @return type
     */
    public function get_all_countries($id = 0) {
        $data = array();
        $column = array('cnt_id as id', 'cnt_iso as iso', 'cnt_name as name', 'cnt_phonecode as phonecode');
        $where = array('cnt_status' => 1);

        if (!empty($id)) {
            $where['cnt_id'] = $id;
        }

        $orderby = array('cnt_name' => 'ASC');
        $data = $this->get_all_rows(TBL_COUNTRIES, $column, $where, array(), $orderby);

        return $data;
    }

    /**
     * Description : Use this function for get all states
     * 
     * get particular states if country id passed
     * 
     * @author Nitinkumar vaghani
     * Last modified date : 29-12-2017
     * 
     * @param type $id
     * @return type
     */
    public function get_all_states($id = 0) {
        $data = array();
        $column = array('st_id as id', 'st_cnt_id as country_id', 'st_name as name');
        $where = array('st_status' => 1);

        if (!empty($id)) {
            $where['st_cnt_id'] = $id;
        }

        $orderby = array('st_name' => 'ASC');
        $data = $this->get_all_rows(TBL_STATES, $column, $where, array(), $orderby);

        return $data;
    }

    /**
     * Description : Use this function for get all cities
     * 
     * get particular city if state id passed
     * 
     * @author Nitinkumar vaghani
     * Last modified date : 29-12-2017
     * 
     * @param type $id
     * @return type
     */
    public function get_all_cities($id = 0) {
        $data = array();
        $column = array('city_id as id', 'city_st_id as state_id', 'city_name as name');
        $where = array('city_status' => 1);

        if (!empty($id)) {
            $where['city_st_id'] = $id;
        }

        $orderby = array('city_name' => 'ASC');
        $data = $this->get_all_rows(TBL_CITIES, $column, $where, array(), $orderby);

        return $data;
    }

    public function get_user_detail($request_data) {

        $column = 'user_type';
        $where = array(
            'user_id' => $request_data['user_id']
        );
        $get_data = $this->get_single_row(TBL_USERS, $column, $where);
        return $get_data;
    }
    
    /**
     * Description :- This function is used to get static pages
     * 
     * @author Kanaiya Makwana
     * 
     * @param type $id
     * @return type
     */
    public function get_staticpage_by_id($id) {

        $get_static_page_sql = "SELECT 
                                        static_page_title,
                                        static_page_content
                                    FROM 
                                        " . TBL_STATIC_PAGE . " 
                                    WHERE 
                                        static_page_id = '" . $id . "' AND
                                        static_page_status = '1' 
                                    ";

        $get_static_page_data = $this->get_single_row_by_query($get_static_page_sql);

        $return_array = array(
            'static_page_content' => $get_static_page_data['static_page_content'],
            'static_page_title' => $get_static_page_data['static_page_title']
        );

        return $return_array;
    }

    public function get_doctor_subscription() {
        $this->db->select('
            u.user_first_name,
            u.user_last_name,
            u.user_email,
            ds.doctor_id,
            ds.plan_expiry_date,
        ');
        $this->db->from("me_doctor_subscriptions ds");
        $this->db->join("me_users u", "u.user_id=ds.doctor_id");
        $this->db->where("ds.doctor_plan_type", 2);
        $this->db->where("ds.doctor_subscriptions_status", 1);
        $this->db->where("u.user_status", 1);
        $this->db->where("u.user_type", 2);
        $this->db->group_start();
        foreach (PAYMENT_NOTIFICATION_DAYS as $value) {
            $this->db->or_where("ds.plan_expiry_date", date('Y-m-d', strtotime("+" . $value . " days")));
        }
        $this->db->group_end();
        $query = $this->db->get();
        // echo $this->db->last_query();
        return $query->result();
    }

    public function get_linked_family_members($user_id, $columns) {
        // $columns = 'u.user_id, u.user_first_name' ;
        $this->db->select($columns, FALSE)->from(TBL_USERS . ' u');
        $this->db->join('me_patient_family_member_mapping map' , 'map.parent_patient_id = u.user_id');
        $this->db->where('u.user_status', 1);
        $this->db->where('u.user_type', 1);
        $this->db->where('map.mapping_status', 1);
        $this->db->where('map.patient_id',$user_id);
        $this->db->order_by('map.created_at', 'DESC');
        $query = $this->db->get();
        return $query->result();
    }

    public function get_doctors_global_setting($doctor_id, $columns = '*', $setting_name_arr = []) {
        $this->db->select($columns);
        $this->db->from('me_doctors_global_setting');
        $this->db->where('setting_status', 1);
        $this->db->where('doctor_id', $doctor_id);
        if(!empty($setting_name_arr))
                $this->db->where_in('setting_name', $setting_name_arr);
        $query = $this->db->get();
        return $query->result();
    }

    public function get_doctor_subscription_expiry($days) {
        $datetime = get_display_date_time("Y-m-d H:i:s");
        $this->db->select('
            u.user_unique_id,
            u.user_first_name,
            u.user_last_name,
            u.user_email,
            u.user_phone_number,
            u.last_login_at,
            a.address_name_one,
            c.city_name,
            s.state_name,
            spm.sub_plan_name,
            ds.plan_expiry_date,
            ds.doctor_plan_type,
            sm.src_master_contact_person_name,
            sm.src_master_email,
            sm.src_master_phone,
            sm.src_master_city,
            ds.doctor_id,
        ');
        $this->db->from("me_doctor_subscriptions ds");
        $this->db->join("me_users u", "u.user_id=ds.doctor_id");
        $this->db->join("me_admin_source_master sm", "sm.src_master_id=u.user_source_id", "LEFT");
        $this->db->join("me_subscription_plan_master spm", "spm.sub_plan_id=ds.sub_plan_id");
        $this->db->join("me_doctor_clinic_mapping cm", "cm.doctor_clinic_mapping_user_id=u.user_id");
        $this->db->join("me_address a", "
                a.address_user_id=cm.doctor_clinic_mapping_clinic_id AND 
                a.address_type=2 AND 
                a.address_status=1", "LEFT");
        $this->db->join("me_city c", "c.city_id = a.address_city_id", "LEFT");
        $this->db->join("me_state s", "s.state_id=a.address_state_id", "LEFT");
        $this->db->where("ds.doctor_plan_type", 1);
        $this->db->where("ds.doctor_subscriptions_status", 1);
        $this->db->where("u.user_status", 1);
        $this->db->where("u.user_type", 2);
        if(!empty($days)) {
            $this->db->where("ds.plan_expiry_date >= ", date('Y-m-d', strtotime($datetime)));
            $this->db->where("ds.plan_expiry_date <= ", date('Y-m-d', strtotime("+" . $days . " days", strtotime($datetime))));
        } else {
            $this->db->where("MONTH(ds.plan_expiry_date)", date('m', strtotime($datetime)));
            $this->db->where("YEAR(ds.plan_expiry_date)", date('Y', strtotime($datetime)));
        }
        $this->db->group_by('u.user_id');
        $this->db->order_by('plan_expiry_date', 'ASC');
        $query = $this->db->get();
        // echo $this->db->last_query();
        return $query->result();
    }

    public function get_patient_prescription_share() {
        $this->db->select('*');
        $this->db->from('me_patient_record_share');
        $this->db->where('status', 1);
        $this->db->where('DATE(created_at) <=', date('Y-m-d', strtotime("-7 days")));
        $query = $this->db->get();
        return $query->result_array();
    }

    public function update_patient_prescription_share($id_arr,$data) {
        $this->db->where_in('id', $id_arr);
        $this->db->update('me_patient_record_share', $data);
    }

    public function patient_prescription_share_details($id) {
        $this->db->select("status,file_name,file_url,open_count, CONCAT(user_first_name, ' ', user_last_name) as patient_name");
        $this->db->from('me_patient_record_share');
        $this->db->join('me_users','user_id=patient_id');
        $this->db->where('id', $id);
        $query = $this->db->get();
        return $query->row_array();
    }

    public function get_user_setting($where) {
        $this->db->select('*');
        $this->db->from('me_settings');
        if(!empty($where['setting_user_id']))
            $this->db->where('setting_user_id', $where['setting_user_id']);
        if(!empty($where['setting_type']) && is_array($where['setting_type']) && count($where['setting_type']) > 0)
            $this->db->where_in('setting_type', $where['setting_type']);
        elseif (!empty($where['setting_type'])) {
            $this->db->where('setting_type', $where['setting_type']);
        }
        $query = $this->db->get();
        return $query->result_array();
    }

    public function get_reminders($today_date) {
        $this->db->select('
            reminder_id,reminder_user_id,reminder_drug_name,reminder_timing,reminder_duration,reminder_day,reminder_start_date,user_phone_number,drug_name_with_unit,prescription_doctor_user_id,
            CASE
                WHEN reminder_day = 1 THEN DATE_ADD(reminder_start_date, INTERVAL reminder_duration DAY)
                WHEN reminder_day = 2 THEN DATE_ADD(reminder_start_date, INTERVAL reminder_duration WEEK)
                WHEN reminder_day = 3 THEN DATE_ADD(reminder_start_date, INTERVAL reminder_duration MONTH)
            END AS reminder_end_date
            ');
        $this->db->from('me_reminders');
        $this->db->join('me_users', "user_id=reminder_user_id");
        $this->db->join('me_drugs', "drug_id=reminder_drug_id");
        $this->db->join('me_prescription_reports', "prescription_id=reminder_prescription_report_id");
        $this->db->where('reminder_type', 1);
        $this->db->where('reminder_status', 1);
        $this->db->where('is_capture_compliance', 1);
        $this->db->where("CASE
                WHEN reminder_day = 1 THEN DATE_ADD(reminder_start_date, INTERVAL reminder_duration DAY)  >= '".$today_date."'
                WHEN reminder_day = 2 THEN DATE_ADD(reminder_start_date, INTERVAL reminder_duration WEEK)  > '".$today_date."'
                WHEN reminder_day = 3 THEN DATE_ADD(reminder_start_date, INTERVAL reminder_duration MONTH)  > '".$today_date."'
            END");
        $query = $this->db->get();
        return $query->result_array();
    }

    static function get_interval($val) {
        if($val == 1)
            return $val . " DAY";
        elseif($val == 2)
            return $val . " WEEK";
        elseif($val == 3)
            return $val . " MONTH";
    }

    public function get_reminder_records($where, $columns = '*') {
        $this->db->select($columns)->from('me_reminder_records');
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

    public function get_patient_health_advice($time) {
        $this->db->select("
            CONCAT(u.user_first_name,' ',u.user_last_name) AS patient_name,
            u.user_email,
            u.user_phone_number,
            pha.patient_health_advice_id,
            pha.patient_health_advice_patient_id,
            pha.patient_health_advice_doctor_id,
            pha.patient_health_advice_group_id,
            pha.patient_health_advice_schedule,
            pha.patient_health_advice_send_day,
            pha.patient_health_advice_is_send_email,
            pha.patient_health_advice_is_send_sms,
            pha.patient_health_advice_last_send_order,
            pha.patient_health_advice_created_at,
            ")->from('me_patients_health_advice pha');
        $this->db->join("me_users u", "u.user_id=pha.patient_health_advice_patient_id");
        $this->db->where("pha.patient_health_advice_status", 1);
        $this->db->where("pha.patient_health_advice_send_time", $time);
        $this->db->where("pha.patient_health_advice_end_date IS NULL");
        $query = $this->db->get();
        return $query->result();
    }

    public function get_health_advice($group_ids) {
        $this->db->select("*")->from('me_health_advice ha');
        $this->db->where('ha.health_advice_status', 1);
        $this->db->where_in('ha.health_advice_group_id', $group_ids);
        $query = $this->db->get();
        return $query->result();
    }

    public function get_comments($health_advice_id) {
        $this->db->select("*")->from('me_health_advice_comment');
        $this->db->where('status', 1);
        $this->db->where('health_advice_id', $health_advice_id);
        $query = $this->db->get();
        return $query->result();
    }

    public function get_health_advice_by_id($health_advice_id, $patient_id) {
        $this->db->select("ha.health_advice_name, ha.health_advice_image,ha.health_advice_desc, ha.health_advice_likes, MAX(al.audit_created_at) as audit_created_at, CONCAT(u.user_first_name, ' ', u.user_last_name) as doctor_name")->from('me_health_advice ha');
        $this->db->join("me_audit_log al", "al.table_old_value=ha.health_advice_id AND action_slug_name='health_advice_sent' AND table_primary_key_value=".$patient_id);
        $this->db->join("me_users u", "u.user_id=al.user_id");
        $this->db->where('ha.health_advice_status', 1);
        $this->db->where('ha.health_advice_id', $health_advice_id);
        $query = $this->db->get();
        return $query->row();
    }

    public function get_patient_health_advice_groups($patient_id) {
        $this->db->select("pha.patient_health_advice_id,pha.patient_health_advice_group_id,hag.health_advice_group_name,pha.patient_health_advice_last_send_order")->from('me_patients_health_advice pha');
        $this->db->join("me_health_advice_groups hag", "hag.health_advice_group_id=pha.patient_health_advice_group_id");
        $this->db->where('pha.patient_health_advice_status', 1);
        $this->db->where('pha.patient_health_advice_last_send_order IS NOT NULL');
        $this->db->where('pha.patient_health_advice_patient_id', $patient_id);
        $this->db->group_by('pha.patient_health_advice_group_id');
        $query = $this->db->get();
        return $query->result();
    }

    public function get_health_advice_by_group($group_id, $patient_id, $order) {
        $this->db->select("ha.health_advice_id, ha.health_advice_name, ha.health_advice_image,ha.health_advice_desc, al.audit_created_at, CONCAT(u.user_first_name, ' ', u.user_last_name) as doctor_name")->from('me_health_advice ha');
        $this->db->join("me_audit_log al", "al.table_old_value=ha.health_advice_id AND action_slug_name='health_advice_sent' AND table_primary_key_value=".$patient_id);
        $this->db->join("me_users u", "u.user_id=al.user_id");
        $this->db->where('ha.health_advice_status', 1);
        $this->db->where('ha.health_advice_group_id', $group_id);
        $this->db->where('ha.health_advice_order <=', $order);
        $query = $this->db->get();
        // echo $this->db->last_query();die;
        return $query->result();
    }

    function update_multiple($table,$data,$field) {
        $this->db->update_batch($table,$data, $field); 
    }

    public function get_teleconsultant_appointments($datetime) {
        $this->db->select("
            CONCAT(p.user_first_name, ' ', p.user_last_name) as patient_name,
            CONCAT(d.user_first_name, ' ', d.user_last_name) as doctor_name,
            p.user_email,
            p.user_phone_number,
            a.appointment_id,
            a.appointment_user_id,
            a.appointment_doctor_user_id,
            a.appointment_type,
            CONCAT(a.appointment_date, ' ', a.appointment_from_time) as appointment_date_time,
            s.setting_data,
            caretaker.user_phone_number as caretaker_phone_number,
            caretaker.user_email as caretaker_email,
            vct.doctor_id,
            vct.patient_id
            ")->from('me_appointments a');
        $this->db->join('me_users p', 'p.user_id=a.appointment_user_id');
        $this->db->join('me_patient_family_member_mapping pfm', 'pfm.patient_id=a.appointment_user_id AND pfm.mapping_status=1',"LEFT");
        $this->db->join('me_users caretaker', 'caretaker.user_id=pfm.parent_patient_id',"LEFT");
        $this->db->join('me_video_conf_token vct', 'vct.patient_id=a.appointment_user_id AND vct.doctor_id=a.appointment_doctor_user_id', "LEFT");
        $this->db->join('me_users d', 'd.user_id=a.appointment_doctor_user_id');
        $this->db->join('me_settings s', 's.setting_user_id=a.appointment_doctor_user_id AND s.setting_type=10 AND s.setting_status=1', "LEFT");
        $this->db->where('a.appointment_status', 1);
        $this->db->where('a.appointment_type', 5);
        $this->db->where('a.appointment_from_time', date("H:i:00", strtotime($datetime)));
        $this->db->where('a.appointment_date', date("Y-m-d", strtotime($datetime)));
        // $this->db->group_by('a.appointment_id');
        $query = $this->db->get();
        // echo $this->db->last_query();die;
        return $query->result();
    }

    public function get_appointment_data_by_id($appointment_id) {
        $this->db->select("
            a.appointment_id,
            a.appointment_type,
            a.appointment_date,
            ch.call_end_date_time
            ")->from('me_appointments a');
        $this->db->join('me_teleconsultant_call_history ch', 'ch.appointment_id=a.appointment_id', "LEFT");
        $this->db->where('a.appointment_status', 1);
        $this->db->where('a.appointment_id', $appointment_id);
        $query = $this->db->get();
        return $query->row_array();
    }
    public function get_force_disconnect_data() {
        $this->db->select("
            a.appointment_id,
            a.patient_id,
            a.doctor_id,
            a.call_end_date_time,
            a.call_start_date_time,
            a.doctor_start_date_time,
            a.patient_start_date_time,
            s.setting_id,
            s.setting_data,
            vct.doctor_id,
            vct.patient_id,
            vct.session_id,
            vct.doctor_connection_id,
            vct.patient_connection_id
            ")->from('me_teleconsultant_call_history a');
        $this->db->join('me_video_conf_token vct', 'vct.patient_id=a.patient_id AND vct.doctor_id=a.doctor_id');
        $this->db->join('me_settings s', 's.setting_user_id=a.doctor_id AND s.setting_type=10 AND s.setting_status=1');
        $this->db->where('vct.session_id IS NOT NULL');
        $this->db->where('vct.token_id IS NOT NULL');
        $this->db->where('a.call_end_date_time IS NULL');
        $query = $this->db->get();
        return $query->result();
    }

    public function update_teleconsultant_call_history($data) {
        $this->db->update_batch('me_teleconsultant_call_history',$data, 'appointment_id'); 
    }

    public function update_setting_data($data) {
        $this->db->update_batch('me_settings',$data, 'setting_id'); 
    }

    public function delete_token_data($where) {
        foreach ($where as $key => $value) {
            $this->db->or_group_start();
            $this->db->where('doctor_id', $value['doctor_id']);
            $this->db->where('patient_id', $value['patient_id']);
            $this->db->group_end();
        }
        $this->db->delete('me_video_conf_token'); 
    }

    public function get_global_setting_by_key_arr($key_arr) {
        $this->db->select('global_setting_key,global_setting_value');
        $this->db->from('me_global_settings');
        $this->db->where('global_setting_status', 1);
        $this->db->where_in('global_setting_key', $key_arr);
        $selectQueStr = $this->db->get_compiled_select();
        if(IS_FILE_CACHING_ACTIVE == 1) {
            create_cache_sub_dir('cron_cache');
            $hashObj = sha1($selectQueStr);
            $this->load->driver('cache');
            if(!$resultCached = $this->cache->file->get($hashObj)){
                $result = $this->Common_model->get_all_rows_by_query($selectQueStr);
                $this->cache->file->save($hashObj, $result, CACHE_TTL);
            }else{
                $result = $resultCached;
            }
        } else {
            $result = $this->Common_model->get_all_rows_by_query($selectQueStr);
        }
        return $result;
    }

    public function get_emailtemplate_by_id($id){
        $id_arr = [22,23];
        $id_arr[] = $id;
        $this->db->select('email_template_id,email_template_subject,email_template_message,email_template_user_type');
        $this->db->from(TBL_EMAIL_TEMPLATE);
        $this->db->where('email_template_status', 1);
        $this->db->where_in('email_template_id', $id_arr);
        $selectQueStr = $this->db->get_compiled_select();
        if(IS_FILE_CACHING_ACTIVE == 1) {
            create_cache_sub_dir('cron_cache');
            $hashObj = sha1($selectQueStr);
            $this->load->driver('cache');
            if(!$resultCached = $this->cache->file->get($hashObj)){
                $result = $this->Common_model->get_all_rows_by_query($selectQueStr);
                $this->cache->file->save($hashObj, $result, CACHE_TTL);
            }else{
                $result = $resultCached;
            }
        } else {
            $result = $this->Common_model->get_all_rows_by_query($selectQueStr);
        }
        foreach ($result as $key => $value) {
            if($value['email_template_id'] == 22)
                $header = $value['email_template_message'];
            if($value['email_template_id'] == 23)
                $footer = $value['email_template_message'];
            if($value['email_template_id'] == $id)
                $get_email_template_data = $value;
        }
        if(!empty($get_email_template_data)) {
            $email_static_data = $this->email_setting($get_email_template_data['email_template_user_type']);
            $return_array = array(
                'email_template_message' => $header . ' ' . $get_email_template_data['email_template_message'] . ' ' . $footer,
                'email_template_subject' => $get_email_template_data['email_template_subject'],
                'email_user_type' => $get_email_template_data['email_template_user_type'],
                'email_static_data' => $email_static_data
            );
            return $return_array;
        } else {
            return array();
        }
        return $return_array;
    }
    public function email_setting($user_type = '') {
        if($user_type == 1)
            $setting_where = "'patient_email_id', 'company_name', 'contact_number' ";
        else    
            $setting_where = "'email_id', 'company_name', 'contact_number' ";
        $column = 'global_setting_id, 
                   global_setting_name,
                   global_setting_key,
                   global_setting_value';
        $selectQueStr = " SELECT 
                                " . $column . "  
                            FROM 
                                " . TBL_GLOBAL_SETTINGS . " 
                            WHERE 
                                global_setting_status = 1 ";
        if (!empty($setting_where)) {
            $selectQueStr .= " AND global_setting_name IN (" . $setting_where . ") ";
        }
        if(IS_FILE_CACHING_ACTIVE == 1) {
            create_cache_sub_dir('cron_cache');
            $hashObj = sha1($selectQueStr);
            $this->load->driver('cache');
            if(!$resultCached = $this->cache->file->get($hashObj)){
                $result = $this->Common_model->get_all_rows_by_query($selectQueStr);
                $this->cache->file->save($hashObj, $result, CACHE_TTL);
            }else{
                $result = $resultCached;
            }
        } else {
            $result = $this->Common_model->get_all_rows_by_query($selectQueStr);
        }
        $result = array_column($result, 'global_setting_value', 'global_setting_name');
        if(!empty($result['patient_email_id'])){
            $result['email_id'] = $result['patient_email_id'];
            unset($result['patient_email_id']);
        }
        return $result;
    }

    public function update_video_conf_token_data($data, $patient_ids) {
        foreach ($patient_ids as $doctor_id => $patient_id) {
            $this->db->or_group_start();
            $this->db->where('doctor_id', $doctor_id);
            $this->db->where('patient_id', $patient_id);
            $this->db->group_end();
        }
        $this->db->update('me_video_conf_token', $data);
    }

    public function get_doctor_email_send($date) {
        $check_date = date('Y-m-d H:i', strtotime("-60 minutes", strtotime($date)));
        // $check_date2 = date('Y-m-d H:i', strtotime("-120 minutes", strtotime($date)));
        $this->db->select("
            user_id,
            CONCAT(user_first_name, ' ', user_last_name) as doctor_name,
            user_email,
            user_phone_number,
            user_created_at
            ")->from('me_users');
        $this->db->join("me_doctor_clinic_mapping", "user_id = doctor_clinic_mapping_user_id", "LEFT");
        $this->db->where("(doctor_clinic_mapping_role_id = 1 OR doctor_clinic_mapping_role_id IS NULL)");
        $this->db->where('user_type', 2);
        $this->db->where('user_status', 1);
        $this->db->group_start();
        // $this->db->where_in('user_id', [1614,1615]); // set doctor id array and Below 2 line comment
        $this->db->like('user_created_at', $check_date, "after");
        // $this->db->or_like('user_created_at', $check_date2, "after");
        $this->db->group_end();
        $this->db->group_by("user_id");
        $query = $this->db->get();
        // echo $this->db->last_query();
        return $query->result();
    }

    public function get_all_doctors($page, $per_page) {
        $this->db->select("
            user_id,
            user_phone_number
            ")->from('me_users');
        $this->db->join("me_doctor_clinic_mapping", "user_id = doctor_clinic_mapping_user_id", "LEFT");
        $this->db->where("(doctor_clinic_mapping_role_id = 1 OR doctor_clinic_mapping_role_id IS NULL)");
        $this->db->where('user_type', 2);
        $this->db->where('user_status', 1);
        // $this->db->where_in('user_phone_number', ['9723394348']);
        $this->db->group_by("user_id");
        $this->db->limit($per_page, (($page - 1) * $per_page));
        $query = $this->db->get();
        // echo $this->db->last_query();die;
        return $query->result();
    }

    public function get_bulk_sms_template($datetime) {
        $this->db->select("
            id,
            template_name,
            message_template,
            dynamic_data,
            database_query,
            message_type
            ")->from('me_promotional_message_cron');
        $this->db->like('message_send_datetime', $datetime, "after");
        $this->db->where('status', 1);
        $query = $this->db->get();
        return $query->result();
    }

    public function appointment_reminder($datetime) {
        $this->db->select("
            CONCAT(p.user_first_name, ' ', p.user_last_name) as patient_name,
            CONCAT(d.user_first_name, ' ', d.user_last_name) as doctor_name,
            p.user_phone_number,
            psl.doctor_id,
            psl.id AS share_link_id,
            psl.unique_code,
            pfm.parent_patient_id,
            psl2.doctor_id as psl2_doctor_id,
            psl2.id as psl2_share_link_id,
            psl2.unique_code as psl2_unique_code,
            dcm.doctor_clinic_mapping_tele_fees,
            caretaker.user_phone_number as caretaker_phone_number,
            a.appointment_id,
            a.appointment_user_id,
            a.appointment_clinic_id,
            a.appointment_doctor_user_id,
            a.appointment_type,
            CONCAT(a.appointment_date, ' ', a.appointment_from_time) as appointment_date_time,
            ")->from('me_appointments a');
        $this->db->join('me_users p', 'p.user_id=a.appointment_user_id');
        $this->db->join('me_patient_family_member_mapping pfm', 'pfm.patient_id=a.appointment_user_id AND pfm.mapping_status=1',"LEFT");
        $this->db->join('me_users caretaker', 'caretaker.user_id=pfm.parent_patient_id',"LEFT");
        $this->db->join('me_patient_share_link_log psl', 'p.user_id=psl.patient_id', "LEFT");
        $this->db->join('me_patient_share_link_log psl2', 'psl2.patient_id=caretaker.user_id', "LEFT");
        $this->db->join('me_users d', 'd.user_id=a.appointment_doctor_user_id');
        $this->db->join('me_doctor_clinic_mapping dcm', 'dcm.doctor_clinic_mapping_user_id=d.user_id AND dcm.doctor_clinic_mapping_clinic_id=a.appointment_clinic_id AND dcm.doctor_clinic_mapping_status=1');
        $this->db->where('a.appointment_status', 1);
        $this->db->where('a.appointment_from_time', date("H:i:00", strtotime($datetime)));
        $this->db->where('a.appointment_date', date("Y-m-d", strtotime($datetime)));
        $query = $this->db->get();
        return $query->result();
    }

    public function doctor_payment_mode_link($doctor_id) {
        if(!empty($doctor_id)) {
            $this->db->select('dp.doctor_payment_mode_doctor_id,dp.doctor_payment_mode_id, dp.doctor_payment_mode_upi_link,dp.doctor_payment_mode_qrcode_img_path,dp.doctor_payment_mode_master_id,pm.payment_mode_name');
            $this->db->from('me_doctor_payment_mode_link dp');
            $this->db->join('me_payment_mode_master pm', 'pm.payment_mode_id=dp.doctor_payment_mode_master_id');
            $this->db->where_in('doctor_payment_mode_doctor_id', $doctor_id);
            $this->db->where('doctor_payment_mode_status', 1);
            $query = $this->db->get();
            return $query->result();
        } else {
            return array();
        }
    }

    public function get_billing_information_for_doctor($requested_data) {
        $columns = 'billing_id,
                    billing_advance_amount,
                    billing_created_at,
                    billing_invoice_date,
                    billing_is_import,
                    billing_appointment_id,
                    billing_user_id,
                    billing_doctor_user_id,
                    billing_clinic_id,
                    billing_payment_mode_id,
                    billing_discount,
                    billing_tax,
                    billing_grand_total,
                    billing_total_payable,
                    billing_paid_amount,
                    invoice_number,
                    billing_detail_name,
                    billing_detail_unit,
                    billing_detail_basic_cost,
                    billing_detail_discount,
                    billing_detail_tax,
                    billing_detail_discount_type,
                    billing_detail_tax_id,
                    billing_detail_total,
                    billing_detail_id,
                    billing_detail_created_at,
                    payment_mode_name,
                    payment_mode_vendor_fee';

        $get_billing_sql = "    SELECT 
                                    " . $columns . " 
                                FROM 
                                    " . TBL_BILLING . " 
                                LEFT JOIN 
                                    " . TBL_BILLING_DETAILS . " 
                                ON 
                                    billing_id = billing_detail_billing_id AND billing_detail_status = 1
                                LEFT JOIN    
                                    " . TBL_PAYMENT_MODE . "
                                ON
                                    billing_payment_mode_id = payment_mode_id    
                                WHERE 
                                    billing_appointment_id = '" . $requested_data['appointment_id'] . "'
                                AND 
                                    billing_user_id = '" . $requested_data['patient_id'] . "'
                                AND
                                    billing_doctor_user_id = '" . $requested_data['doctor_id'] . "'
                                AND
                                    billing_id = '" . $requested_data['billing_id'] . "' 
                                AND 
                                    billing_status = 1 ";

        $get_billing_data = $this->get_all_rows_by_query($get_billing_sql);
        return $get_billing_data;
    }

    public function check_paient_appointment($where) {
        $check_patient_appointment_sql = "
            SELECT 
                appointment_date,
                share_status_json,
                appointment_user_id,
                appointment_clinic_id,
                appointment_doctor_user_id,
                user_first_name,
                user_last_name,
                user_sign_filepath,
                user_phone_number,
                doctor_detail_speciality,
                clinic_name,
                clinic_contact_number,
                clinic_email,
                address_name,
                address_name_one,
                address_locality,
                address_pincode,
                city_name,
                state_name,
                setting_data,
                appointment_type,
                GROUP_CONCAT(DISTINCT(doctor_qualification_degree) ORDER BY doctor_qualification_id ASC) AS doctor_qualification,
                GROUP_CONCAT(DISTINCT(doctor_council_registration_number)) AS doctor_regno
            FROM 
                " . TBL_APPOINTMENTS . "
            LEFT JOIN 
                " . TBL_USERS . " ON appointment_doctor_user_id=user_id
            LEFT JOIN 
                " . TBL_DOCTOR_DETAILS . " ON  user_id = doctor_detail_doctor_id
            LEFT JOIN 
                " . TBL_ADDRESS . " ON address_user_id = appointment_clinic_id AND address_type = 2
            LEFT JOIN 
                " . TBL_CITIES . " ON address_city_id = city_id
            LEFT JOIN 
                " . TBL_STATES . " ON address_state_id = state_id
            LEFT JOIN 
               " . TBL_CLINICS . " ON appointment_clinic_id = clinic_id
            LEFT JOIN 
               me_settings ON appointment_clinic_id = setting_clinic_id AND setting_user_id=appointment_doctor_user_id AND setting_type=4
            LEFT JOIN
                ".TBL_DOCTOR_EDUCATIONS." ON appointment_doctor_user_id = doctor_qualification_user_id AND doctor_qualification_status = 1 
            LEFT JOIN
                ".TBL_DOCTOR_REGISTRATIONS." ON appointment_doctor_user_id = doctor_registration_user_id AND doctor_registration_status = 1 
            WHERE appointment_id=" . $where['appointment_id'];
        $check_patient_appointment = $this->Common_model->get_single_row_by_query($check_patient_appointment_sql);
        return $check_patient_appointment;
    }

    public function get_patient_prescription($appointment_id, $with_generic) {
        $patient_prescription_sql = "
            SELECT 
                prescription_drug_name, 
                drug_frequency_name, 
                prescription_duration, 
                prescription_duration_value, 
                prescription_intake, 
                prescription_dosage,
                prescription_frequency_id,
                drug_unit_is_qty_calculate,
                drug_unit_medicine_type,
                drug_unit_name,
                ".($with_generic == 'true' ? 'GROUP_CONCAT(drug_generic_title) as drug_generic_title,':'')."
                prescription_intake_instruction,
                prescription_frequency_instruction,
                prescription_frequency_instruction_json,
                prescription_intake_instruction_json,
                follow_up_followup_date,
                prescription_unit_value,
                prescription_is_import,
                follow_up_instruction,
                follow_up_instruction_json,
                prescription_frequency_value as freq
            FROM 
                " . TBL_PRESCRIPTION_REPORTS . " 
            LEFT JOIN
                ".TBL_PRESCRIPTION_FOLLOUP." ON prescription_appointment_id = follow_up_appointment_id
            LEFT JOIN 
                " . TBL_DRUG_FREQUENCY . " ON prescription_frequency_id=drug_frequency_id 
            LEFT JOIN 
                " . TBL_DRUG_UNIT . " ON prescription_unit_id = drug_unit_id
            ".($with_generic == 'true' ? "LEFT JOIN 
                " . TBL_DRUG_GENERIC . " ON FIND_IN_SET(drug_generic_id, prescription_generic_id)  AND  drug_generic_status = 1":"")." 
            WHERE 
                prescription_appointment_id='" . $appointment_id . "' AND 
                prescription_status=1
            GROUP BY 
                prescription_id
        ";
        $patient_prescription = $this->Common_model->get_all_rows_by_query($patient_prescription_sql);
        return $patient_prescription;
    }

    public function get_anatomy_diagram($appointment_id) {
        $report_columns = "file_report_name,file_report_image_url";
        $get_report_query = "SELECT
                " . $report_columns . " 
            FROM 
                me_files_reports 
            LEFT JOIN
                 me_files_reports_images ON file_report_image_file_report_id = file_report_id AND file_report_image_status=1 
            WHERE
                file_report_appointment_id = '" . $appointment_id . "' 
            AND 
                file_report_status = 1
            AND 
                file_report_report_type_id = 11";
        return $this->Common_model->get_all_rows_by_query($get_report_query);
    }

    public function get_patient_data($where) {
        $this->db->select("
            user_first_name,
            user_last_name,
            user_gender,
            user_unique_id,
            user_patient_id,
            user_details_dob,
            user_phone_number,
            user_email,
            vital_report_id,
            vital_report_weight,
            vital_report_bloodpressure_systolic,
            vital_report_bloodpressure_diastolic,
            vital_report_pulse,
            vital_report_temperature,
            vital_report_temperature_type,
            vital_report_resp_rate,
            clinical_notes_reports_id,
            clinical_notes_reports_kco,
            clinical_notes_reports_complaints,
            clinical_notes_reports_observation,
            clinical_notes_reports_diagnoses,
            clinical_notes_reports_add_notes,
            lab_report_id,
            lab_report_test_name,
            procedure_report_id,
            procedure_report_procedure_text,
            procedure_report_note,
            patient_analytics_id
        ");
        $this->db->from(TBL_USERS);
        $this->db->join(TBL_USER_DETAILS, 'user_id = user_details_user_id', 'LEFT');
        $this->db->join(TBL_VITAL_REPORTS, 'user_id = vital_report_user_id AND vital_report_appointment_id=' . $where['appointment_id'] . ' AND vital_report_status=1', 'LEFT');
        $this->db->join(TBL_CLINICAL_REPORTS, 'user_id = clinical_notes_reports_user_id AND clinical_notes_reports_appointment_id=' . $where['appointment_id'] . ' AND clinical_notes_reports_status=1', 'LEFT');
        $this->db->join(TBL_LAB_REPORTS, 'user_id = lab_report_user_id AND lab_report_appointment_id=' . $where['appointment_id'] . ' AND lab_report_status=1', 'LEFT');
        $this->db->join(TBL_PROCEDURE_REPORTS, 'user_id = procedure_report_user_id AND procedure_report_appointment_id=' . $where['appointment_id'] . ' AND procedure_report_status=1', 'LEFT');
        $this->db->join('me_patient_analytics', 'user_id = patient_analytics_user_id AND patient_analytics_analytics_id=308 AND patient_analytics_status=1', 'LEFT');
        $this->db->where('user_id', $where['user_id']);
        $this->db->group_by('user_id');
        $query = $this->db->get();
        return $query->row_array();
    }

    public function get_patient_followup_reminder($date) {
        $this->db->select("
            follow_up_id,
            CONCAT(p.user_first_name, ' ', p.user_last_name) as patient_name,
            CONCAT(d.user_first_name, ' ', d.user_last_name) as doctor_name,
            p.user_phone_number,
            caretaker.user_phone_number as caretaker_phone_number,
            p.user_id as patient_id,
            d.user_id as doctor_id,
            fu.follow_up_followup_date
            ")->from('me_prescription_follow_up fu');
        $this->db->join('me_users p', 'p.user_id=fu.follow_up_user_id');
        $this->db->join('me_users d', 'd.user_id=fu.follow_up_doctor_id');
        $this->db->join('me_patient_family_member_mapping pfm', 'pfm.patient_id=fu.follow_up_user_id AND pfm.mapping_status=1',"LEFT");
        $this->db->join('me_users caretaker', 'caretaker.user_id=pfm.parent_patient_id',"LEFT");
        $this->db->where('fu.follow_up_status', 1);
        $this->db->where('fu.follow_up_followup_date', $date);
        // $this->db->group_by('p.user_id,d.user_id');
        $query = $this->db->get();
        return $query->result();
    }

    public function get_push_reminders($datetime) {
        $this->db->select("
            reminder_id,
            reminder_timing,
            reminder_doctor_name,
            reminder_type,
            reminder_general_title,
            reminder_note,
            CONCAT(user_first_name, ' ', user_last_name) as patient_name,
            user_id,
            user_email,
            user_phone_number,
            user_plan_expiry_date,
            setting_value,
            udt_device_token,
            udt_device_type,
            reminder_lab_report_name,
            appointment_from_time,
            appointment_date,
            appointment_type
        ")->from('me_reminders');
        $this->db->join('me_users','user_id=reminder_user_id');
        $this->db->join('me_doctors_global_setting',"doctor_id=user_id AND setting_name='reminder' AND setting_status=1", "LEFT");
        $this->db->join('me_appointments','appointment_id=reminder_appointment_id', "LEFT");
        $this->db->join('me_user_device_tokens','udt_u_id=reminder_user_id', "LEFT");
        $this->db->where_in('reminder_type', [2,3,4]);
        $this->db->where('reminder_timing', date("H:i", strtotime($datetime)));
        $this->db->where('reminder_start_date', date("Y-m-d", strtotime($datetime)));
        $this->db->where('reminder_status', 1);
        $query = $this->db->get();
        return $query->result();
    }

    public function get_medicine_reminders($today_date,$time) {
        $week_day = date("w", strtotime($today_date))+1;
        $this->db->select('
            reminder_id,reminder_user_id,reminder_drug_name,reminder_timing,reminder_duration,reminder_day,reminder_week_day,reminder_start_date,user_phone_number,user_plan_expiry_date,setting_value,drug_name_with_unit,prescription_doctor_user_id,udt_device_token,udt_device_type,
            CONCAT(user_first_name, " ", user_last_name) as patient_name,
            user_email,
            CASE
                WHEN reminder_day = 1 THEN DATE_ADD(reminder_start_date, INTERVAL (reminder_duration-1) DAY)
                WHEN reminder_day = 2 THEN DATE_ADD(reminder_start_date, INTERVAL (reminder_duration-1) WEEK)
                WHEN reminder_day = 3 THEN DATE_ADD(reminder_start_date, INTERVAL (reminder_duration-1) MONTH)
                WHEN reminder_day = 4 THEN DATE_ADD(reminder_start_date, INTERVAL ((reminder_duration-1)*4) DAY)
                WHEN reminder_day = 5 THEN DATE_ADD(reminder_start_date, INTERVAL (reminder_duration-1) WEEK)
            END AS reminder_end_date
            ');
        $this->db->from('me_reminders');
        $this->db->join('me_users', "user_id=reminder_user_id");
        $this->db->join('me_doctors_global_setting',"doctor_id=user_id AND setting_name='reminder' AND setting_status=1", "LEFT");
        $this->db->join('me_user_device_tokens','udt_u_id=reminder_user_id', "LEFT");
        $this->db->join('me_drugs', "drug_id=reminder_drug_id", "LEFT");
        $this->db->join('me_prescription_reports', "prescription_id=reminder_prescription_report_id", "LEFT");
        $this->db->where('reminder_type', 1);
        $this->db->where('reminder_status', 1);
        $this->db->where("FIND_IN_SET('".$time."',reminder_timing) <> 0");
        $this->db->where("CASE
                WHEN reminder_day = 1 THEN DATE_ADD(reminder_start_date, INTERVAL (reminder_duration-1) DAY)  >= '".$today_date."'
                WHEN reminder_day = 2 THEN DATE_ADD(reminder_start_date, INTERVAL (reminder_duration-1) WEEK)  >= '".$today_date."' AND WEEKDAY(reminder_start_date)=WEEKDAY('".$today_date."')
                WHEN reminder_day = 3 THEN DATE_ADD(reminder_start_date, INTERVAL (reminder_duration-1) MONTH)  >= '".$today_date."' AND DATEDIFF('".$today_date."',reminder_start_date)/30=CEILING(DATEDIFF('".$today_date."',reminder_start_date)/30)
                WHEN reminder_day = 4 THEN DATE_ADD(reminder_start_date, INTERVAL ((reminder_duration-1)*4) DAY)  >= '".$today_date."' AND DATEDIFF('".$today_date."',reminder_start_date)/4=CEILING(DATEDIFF('".$today_date."',reminder_start_date)/4)
                WHEN reminder_day = 5 THEN DATE_ADD(reminder_start_date, INTERVAL (reminder_duration-1) WEEK)  >= '".$today_date."' AND FIND_IN_SET('".$week_day."',reminder_week_day) <> 0
            END");
        $query = $this->db->get();
        // echo $this->db->last_query();die;
        return $query->result_array();
    }

    public function get_caregiver_members($user_ids) {
        $this->db->select('map.parent_patient_id,map.patient_id,u.user_email,u.user_phone_number,user_plan_expiry_date,setting_value,udt_device_token,udt_device_type')->from('me_patient_family_member_mapping map');
        $this->db->join(TBL_USERS . ' u', 'map.parent_patient_id = u.user_id');
        $this->db->join("me_doctors_global_setting","doctor_id=u.user_id AND setting_name='reminder' AND setting_status=1","LEFT");
        $this->db->join('me_user_device_tokens','udt_u_id=map.parent_patient_id', "LEFT");
        $this->db->where('u.user_status', 1);
        $this->db->where('u.user_type', 1);
        $this->db->where('map.mapping_status', 1);
        $this->db->where_in('map.patient_id',$user_ids);
        $query = $this->db->get();
        return $query->result();
    }
}
