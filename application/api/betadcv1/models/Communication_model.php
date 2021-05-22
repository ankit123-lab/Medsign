<?php

class Communication_model extends MY_Model {

    function __construct() {
        parent::__construct();
    }

    public function get_patients($where, $count=false) {
        $this->db->select("
                u.user_id,
                CONCAT(u.user_first_name, ' ', u.user_last_name) as patient_name,
                u.user_phone_number,
                u.user_email,
            ");
        $this->db->from('me_users u');
        $this->db->join("me_appointments appo", "u.user_id = appo.appointment_user_id AND appo.appointment_status != 9", "LEFT");
        
        $this->db->group_start();
        $this->db->where_in("u.user_source_id", $where['user_source_id_arr']);
        $this->db->or_where("appo.appointment_doctor_user_id", $where['doctor_id']);
        $this->db->group_end();
        $this->db->where("u.user_type", 1);
        $this->db->where("u.user_status !=9");
        $this->db->where("u.user_phone_number !=''");
        if(!empty($where['search_str'])) {
            $this->db->group_start();
            $this->db->like('CONCAT(u.user_first_name, " ", u.user_last_name)', $where['search_str']);
            $this->db->or_like('u.user_email', $where['search_str']);
            $this->db->or_like('u.user_phone_number', $where['search_str']);
            $this->db->or_like('u.user_unique_id', $where['search_str']);
            $this->db->group_end();
        }
        $this->db->group_by('u.user_id');
        $this->db->order_by('CONCAT(u.user_first_name, " ", u.user_last_name)');
        if(!$count)
            $this->db->limit($where['per_page'], (($where['page'] - 1) * $where['per_page']));
        $query = $this->db->get();
        // echo $this->db->last_query();die;
        if($count)
            return $query->num_rows();
        else
            return $query->result();
    }

    public function get_commu_patient_groups($where, $count=false) {
        if(empty($where['doctor_id']))
            return array();
        if(!$count) {
            $user_source_id_con = "";
            if($where['user_source_id_arr'])
                $user_source_id_con = " OR u.user_source_id IN(" . implode(',', $where['user_source_id_arr']) .")";

            $search_str_con = "";
            if(!empty($where['search_str']))
                $search_str_con = " AND pg.patient_group_title LIKE '%".$where['search_str']."%'";
            $sql_query = "SELECT
                `pg`.`patient_group_id`,
                `pg`.`patient_group_title`,
                `pg`.`patient_group_disease_id`,
                `pg`.`patient_group_gender`,
                `pg`.`patient_group_age`,
                `pg`.`patient_group_all_added`,
                `pg`.`patient_group_auto_added`,
                CASE WHEN `pg`.`patient_group_auto_added` = 1 AND (`pg`.`patient_group_disease_id` IS NOT NULL OR `pg`.`patient_group_disease_id` != '') THEN
                (
                SELECT COUNT(DISTINCT u.user_id) FROM `me_users` `u`
                LEFT JOIN `me_appointments` `appo` ON `u`.`user_id` = `appo`.`appointment_user_id` AND `appo`.`appointment_status` != 9
                LEFT JOIN `me_user_details` `ud` ON `u`.`user_id` = `ud`.`user_details_user_id`
                JOIN `me_clinical_notes_reports` `cnr` ON `u`.`user_id`=`cnr`.`clinical_notes_reports_user_id`
                JOIN `me_disease` `dm` ON FIND_IN_SET(dm.disease_name,REPLACE(REPLACE(REPLACE(cnr.clinical_notes_reports_kco,'[',''),']',''),'\"','')) != 0
                WHERE `cnr`.`clinical_notes_reports_status` = 1
                AND FIND_IN_SET(dm.disease_id, pg.patient_group_disease_id)
                AND (`appo`.`appointment_doctor_user_id` = " . $where['doctor_id'] . $user_source_id_con . ")

                AND `u`.`user_type` = 1
                AND `u`.`user_status` = 1
                AND IF(`pg`.`patient_group_gender`='all',1=1,`u`.`user_gender` = `pg`.`patient_group_gender`)
                AND CASE
                WHEN `pg`.`patient_group_age` = 1 THEN (TIMESTAMPDIFF(YEAR, ud.user_details_dob, NOW()) >= '0' AND TIMESTAMPDIFF(YEAR, ud.user_details_dob, NOW()) <= '5')
                WHEN `pg`.`patient_group_age` = 2 THEN (TIMESTAMPDIFF(YEAR, ud.user_details_dob, NOW()) >= '5' AND TIMESTAMPDIFF(YEAR, ud.user_details_dob, NOW()) <= '14')
                WHEN `pg`.`patient_group_age` = 3 THEN (TIMESTAMPDIFF(YEAR, ud.user_details_dob, NOW()) >= '15' AND TIMESTAMPDIFF(YEAR, ud.user_details_dob, NOW()) <= '25')
                WHEN `pg`.`patient_group_age` = 4 THEN (TIMESTAMPDIFF(YEAR, ud.user_details_dob, NOW()) >= '26' AND TIMESTAMPDIFF(YEAR, ud.user_details_dob, NOW()) <= '40')
                WHEN `pg`.`patient_group_age` = 5 THEN (TIMESTAMPDIFF(YEAR, ud.user_details_dob, NOW()) >= '40' AND TIMESTAMPDIFF(YEAR, ud.user_details_dob, NOW()) <= '60')
                WHEN `pg`.`patient_group_age` = 6 THEN (TIMESTAMPDIFF(YEAR, ud.user_details_dob, NOW()) >= '60')
                ELSE 1=1
                END
                )
                WHEN `pg`.`patient_group_auto_added` = 1 AND (`pg`.`patient_group_disease_id` IS NULL OR `pg`.`patient_group_disease_id` = '') THEN
                (
                SELECT COUNT(DISTINCT u.user_id) FROM `me_users` `u`
                LEFT JOIN `me_appointments` `appo` ON `u`.`user_id` = `appo`.`appointment_user_id` AND `appo`.`appointment_status` != 9
                LEFT JOIN `me_user_details` `ud` ON `u`.`user_id` = `ud`.`user_details_user_id`
                WHERE (`appo`.`appointment_doctor_user_id` = " . $where['doctor_id'] . $user_source_id_con . " ) 
                AND `u`.`user_type` = 1
                AND `u`.`user_status` = 1
                AND IF(`pg`.`patient_group_gender`='all',1=1,`u`.`user_gender` = `pg`.`patient_group_gender`)
                AND CASE
                WHEN `pg`.`patient_group_age` = 1 THEN (TIMESTAMPDIFF(YEAR, ud.user_details_dob, NOW()) >= '0' AND TIMESTAMPDIFF(YEAR, ud.user_details_dob, NOW()) <= '5')
                WHEN `pg`.`patient_group_age` = 2 THEN (TIMESTAMPDIFF(YEAR, ud.user_details_dob, NOW()) >= '5' AND TIMESTAMPDIFF(YEAR, ud.user_details_dob, NOW()) <= '14')
                WHEN `pg`.`patient_group_age` = 3 THEN (TIMESTAMPDIFF(YEAR, ud.user_details_dob, NOW()) >= '15' AND TIMESTAMPDIFF(YEAR, ud.user_details_dob, NOW()) <= '25')
                WHEN `pg`.`patient_group_age` = 4 THEN (TIMESTAMPDIFF(YEAR, ud.user_details_dob, NOW()) >= '26' AND TIMESTAMPDIFF(YEAR, ud.user_details_dob, NOW()) <= '40')
                WHEN `pg`.`patient_group_age` = 5 THEN (TIMESTAMPDIFF(YEAR, ud.user_details_dob, NOW()) >= '40' AND TIMESTAMPDIFF(YEAR, ud.user_details_dob, NOW()) <= '60')
                WHEN `pg`.`patient_group_age` = 6 THEN (TIMESTAMPDIFF(YEAR, ud.user_details_dob, NOW()) >= '60')
                ELSE 1=1
                END
                )
                ELSE
                count(DISTINCT gm.patient_group_member_user_id)
                END as total_member
                FROM `me_patient_groups` `pg`
                LEFT JOIN `me_patient_group_members` `gm`
                ON `gm`.`patient_group_member_patient_group_id` = `pg`.`patient_group_id`
                AND `gm`.`patient_group_member_status` = 1
                WHERE `pg`.`patient_group_status` = 1
                " . $search_str_con . " 
                AND `pg`.`patient_group_doctor_id` = " . $where['doctor_id'] . "
                GROUP BY `pg`.`patient_group_id`
                LIMIT ". (($where['page'] - 1) * $where['per_page']) . ',' . $where['per_page'];
            return $this->Common_model->get_all_rows_by_query($sql_query);
        } else {
            $this->db->select("
                    pg.patient_group_id
                ");
            $this->db->from('me_patient_groups pg');
            $this->db->where("pg.patient_group_status", 1);
            $this->db->where("pg.patient_group_doctor_id", $where['doctor_id']);
            if(!empty($where['search_str']))
                $this->db->like('pg.patient_group_title', $where['search_str']);
            $this->db->group_by('pg.patient_group_id');
            $this->db->order_by('pg.patient_group_id' , 'DESC');
            $query = $this->db->get();
            return $query->num_rows();
        }
    }

    public function get_patient_group_members($patient_group_ids) {
        $this->db->select("
                u.user_id,
                CONCAT(u.user_first_name, ' ', u.user_last_name) as patient_name,
                u.user_phone_number,
                u.user_email,
                gm.patient_group_member_patient_group_id AS patient_group_id
            ");
        $this->db->from('me_patient_group_members gm');
        $this->db->join("me_users u", "u.user_id = gm.patient_group_member_user_id AND u.user_type = 1 AND u.user_status = 1");
        $this->db->where_in("gm.patient_group_member_patient_group_id", $patient_group_ids);
        $this->db->where("gm.patient_group_member_status", 1);
        $query = $this->db->get();
        return $query->result();
    }

    public function get_communication_list($where, $count=false) {
        if(empty($where['doctor_id']))
            return array();

        $this->db->select("
                patient_communication_id,
                patient_communication_name,
                patient_communication_message,
                patient_communication_credit_used as credit_used,
                DATE_FORMAT(CONVERT_TZ(patient_communication_created_at,'+00:00','+05:30'), '%d/%m/%Y %h:%i %p') as created_at,
                patient_communication_type_id,
                DATE_FORMAT(CONVERT_TZ(communication_time,'+00:00','+05:30'), '%d/%m/%Y %h:%i %p') as deliver_time
            ");
        $this->db->from('me_patients_communication');
        $this->db->join("me_communications", "communication_pt_id = patient_communication_id AND patient_communication_status = 1 AND communication_delivery_status = 1", "LEFT");
        $this->db->where("patient_communication_doctor_id", $where['doctor_id']);
        $this->db->where("DATE(CONVERT_TZ(patient_communication_created_at,'+00:00','+05:30'))", $where['date']);
        $this->db->where("patient_communication_status", 1);
        $this->db->group_by('patient_communication_id');
        $this->db->order_by('patient_communication_id' , 'DESC');
        if(!$count)
            $this->db->limit($where['per_page'], (($where['page'] - 1) * $where['per_page']));
        $query = $this->db->get();
        // echo $this->db->last_query();die;
        if($count)
            return $query->num_rows();
        else
            return $query->result();
    }

    public function get_communication_date($where, $count=false) {
        if(empty($where['doctor_id']))
            return array();

        $this->db->select("
                DATE_FORMAT(CONVERT_TZ(patient_communication_created_at,'+00:00','+05:30'), '%Y-%m-%d') as created_at,
                DATE_FORMAT(CONVERT_TZ(patient_communication_created_at,'+00:00','+05:30'), '%d/%m/%Y') as title_date
            ");
        $this->db->from('me_patients_communication');
        $this->db->where("patient_communication_doctor_id", $where['doctor_id']);
        $this->db->where("patient_communication_status", 1);
        $this->db->group_by('DATE(patient_communication_created_at)');
        $this->db->order_by('DATE(patient_communication_created_at)' , 'DESC');
        if(!$count)
            $this->db->limit($where['per_page'], (($where['page'] - 1) * $where['per_page']));
        $query = $this->db->get();
        // echo $this->db->last_query();die;
        if($count)
            return $query->num_rows();
        else
            return $query->result();
    }

    public function get_sms_template($where, $count=false) {
        if(empty($where['doctor_id']))
            return array();

        $this->db->select("
                communication_sms_template_id,
                communication_sms_template_title,
                communication_sms_placeholder_json
            ");
        $this->db->from('me_communication_sms_templates');
        $this->db->where("communication_sms_template_doctor_user_id IS NULL");
        $this->db->where("communication_sms_template_status", 1);
        $this->db->order_by('communication_sms_template_id' , 'DESC');
        $query = $this->db->get();
        return $query->result();
    }

    public function doctor_details($doctor_id) {
        $this->db->select('u.user_first_name,u.user_last_name,a.address_name_one,u.user_email,u.user_phone_number');
        $this->db->from("me_users u");
        $this->db->join("me_doctor_clinic_mapping cm", "cm.doctor_clinic_mapping_user_id=u.user_id AND cm.doctor_clinic_mapping_is_primary=1");
        $this->db->join("me_address a", "
                a.address_user_id=cm.doctor_clinic_mapping_clinic_id AND 
                a.address_type=2 AND 
                a.address_status=1");
        $this->db->where("cm.doctor_clinic_mapping_role_id", 1);
        $this->db->where("u.user_type", 2);
        $this->db->where("u.user_id", $doctor_id);
        $query = $this->db->get();
        return $query->row();
    }

    function get_doctor_clinic_detail($doctor_id) {
        $this->db->select('
            a.address_state_id,
        ');
        $this->db->from("me_users u");
        $this->db->join("me_doctor_clinic_mapping cm", "cm.doctor_clinic_mapping_user_id=u.user_id AND cm.doctor_clinic_mapping_is_primary=1");
        $this->db->join("me_clinic c", "cm.doctor_clinic_mapping_clinic_id=c.clinic_id");
        $this->db->join("me_address a", "
                a.address_user_id=cm.doctor_clinic_mapping_clinic_id AND 
                a.address_type=2 AND 
                a.address_status=1");
        $this->db->where("cm.doctor_clinic_mapping_role_id", 1);
        $this->db->where("u.user_id", $doctor_id);
        $query = $this->db->get();
        return $query->row();
    }

    public function get_global_setting_by_key($key) {
        $this->db->select('global_setting_key,global_setting_value');
        $this->db->from('me_global_settings');
        $this->db->where('global_setting_status', 1);
        $this->db->where('global_setting_key', $key);
        $query = $this->db->get();
        return $query->row();
    }

    public function update_global_setting_by_key($key, $data) {
        $this->db->where('global_setting_key', $key);
        $this->db->update('me_global_settings', $data);
    }

    public function get_auto_added_group_members($where) {
        $this->db->select("
            u.user_id,
            CONCAT(u.user_first_name, ' ', u.user_last_name) as patient_name,
            u.user_phone_number,
            u.user_email
        ");
        $this->db->from('me_users u');
        $this->db->join("me_appointments appo", "u.user_id = appo.appointment_user_id AND appo.appointment_status != 9", "LEFT");
        $this->db->join("me_user_details ud", "u.user_id = ud.user_details_user_id", "LEFT");

        if(!empty($where['patient_disease_ids'])) {
            $this->db->join('me_clinical_notes_reports cnr', "u.user_id=cnr.clinical_notes_reports_user_id");
            $this->db->join("me_disease d", "FIND_IN_SET(d.disease_name,REPLACE(REPLACE(REPLACE(cnr.clinical_notes_reports_kco,'[',''),']',''),'\"',''))  != 0");
            $this->db->where('cnr.clinical_notes_reports_status',1);
            $this->db->where_in('d.disease_id',$where['patient_disease_ids']);
        }

        $this->db->group_start();
        $this->db->where_in("u.user_source_id", $where['user_source_id_arr']);
        $this->db->or_where("appo.appointment_doctor_user_id", $where['doctor_id']);
        $this->db->group_end();
        $this->db->where("u.user_type", 1);
        $this->db->where("u.user_status", 1);
        if(!empty($where['patient_gender']) && $where['patient_gender'] != 'all') {
            $this->db->where('u.user_gender', $where['patient_gender']);
        }
        if(!empty($where['patient_age_group']) && $where['patient_age_group'] != '7') {
            $ageGroup = patient_age_group();
            if(!empty($ageGroup[$where['patient_age_group']])) {
                $ageRangeArr = explode('-', $ageGroup[$where['patient_age_group']]);
                if(!empty($ageRangeArr[0]))
                    $this->db->where("TIMESTAMPDIFF(YEAR, ud.user_details_dob, NOW()) >=", $ageRangeArr[0]);
                if(!empty($ageRangeArr[1]))
                    $this->db->where("TIMESTAMPDIFF(YEAR, ud.user_details_dob, NOW()) <=", $ageRangeArr[1]);
            }
        }
        $this->db->group_by('u.user_id');
        $this->db->order_by('CONCAT(u.user_first_name, " ", u.user_last_name)');
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
}