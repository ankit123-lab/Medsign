<?php

class Patient_group_model extends MY_Model {

    function __construct() {
        parent::__construct();
    }

    public function get_patient_groups($where) {
        if(empty($where['doctor_id']))
            return array();
        $user_source_id_con = "";
        if($where['user_source_id_arr'])
            $user_source_id_con = " OR u.user_source_id IN(" . implode(',', $where['user_source_id_arr']) .")";

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
            AND `pg`.`patient_group_doctor_id` = " . $where['doctor_id'] . "
            GROUP BY `pg`.`patient_group_id`";
        return $this->Common_model->get_all_rows_by_query($sql_query);
        /*$this->db->select("
                pg.patient_group_id,
                pg.patient_group_title,
                pg.patient_group_disease_id,
                pg.patient_group_gender,
                pg.patient_group_age,
                pg.patient_group_all_added,
                pg.patient_group_auto_added,
                GROUP_CONCAT(DISTINCT d.disease_name SEPARATOR ', ') AS disease_name,
                count(DISTINCT gm.patient_group_member_user_id) as total_member
            ");
        $this->db->from('me_patient_groups pg');
        $this->db->join("me_patient_group_members gm", "gm.patient_group_member_patient_group_id = pg.patient_group_id AND gm.patient_group_member_status = 1", "LEFT");
        $this->db->join("me_disease d", "FIND_IN_SET(d.disease_id, pg.patient_group_disease_id) > 0", "LEFT");
        $this->db->where("pg.patient_group_status", 1);
        $this->db->where("pg.patient_group_doctor_id", $where['doctor_id']);
        $this->db->group_by('pg.patient_group_id');
        $this->db->order_by('pg.patient_group_id' , 'DESC');
        $query = $this->db->get();
        return $query->result();*/
    }

    public function get_patient_group_members($where, $count=false) {
        if(empty($where['patient_group_id']))
            return array();

        $this->db->select("
                u.user_id,
                CONCAT(u.user_first_name, ' ', u.user_last_name) as patient_name,
                u.user_phone_number,
                u.user_email
            ");
        $this->db->from('me_users u');
        $this->db->join("me_patient_group_members gm", "u.user_id = gm.patient_group_member_user_id AND gm.patient_group_member_status = 1");
        $this->db->where("u.user_type", 1);
        $this->db->where("u.user_status", 1);
        $this->db->where("gm.patient_group_member_patient_group_id", $where['patient_group_id']);
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
    public function search_patient_group($where, $count=false, $is_all=false) {
        if($is_all) {
            $this->db->select("
                u.user_id
            ");
        } else {
            $this->db->select("
                u.user_id,
                CONCAT(u.user_first_name, ' ', u.user_last_name) as patient_name,
                u.user_phone_number,
                u.user_email
            ");
        }
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
        $this->db->where("u.user_phone_number !=''");
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
        if(!$count)
            $this->db->limit($where['per_page'], (($where['page'] - 1) * $where['per_page']));
        $query = $this->db->get();
        // if(!$count)
        // echo $this->db->last_query();die;
        if($count && !$is_all)
            return $query->num_rows();
        else
            return $query->result();
    }

    public function get_disease_by_patient($where) {
        $this->db->select("
                d.disease_id,
                d.disease_name
            ");
        $this->db->from('me_users u');
        $this->db->join("me_appointments appo", "u.user_id = appo.appointment_user_id AND appo.appointment_status != 9", "LEFT");
        $this->db->join('me_clinical_notes_reports cnr', "u.user_id=cnr.clinical_notes_reports_user_id");
        $this->db->join("me_disease d", "FIND_IN_SET(d.disease_name,REPLACE(REPLACE(REPLACE(cnr.clinical_notes_reports_kco,'[',''),']',''),'\"',''))  != 0");

        $this->db->where('cnr.clinical_notes_reports_status',1);

        $this->db->group_start();
        $this->db->where_in("u.user_source_id", $where['user_source_id_arr']);
        $this->db->or_where("appo.appointment_doctor_user_id", $where['doctor_id']);
        $this->db->group_end();
        $this->db->where("u.user_type", 1);
        $this->db->where("u.user_status", 1);
        $this->db->group_by(array('d.disease_name'));
        $this->db->order_by('d.disease_name');
        $query = $this->db->get();
        // echo $this->db->last_query();die;
        return $query->result();
    }
}