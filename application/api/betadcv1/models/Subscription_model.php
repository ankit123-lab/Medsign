<?php

class Subscription_model extends MY_Model {

    function __construct() {
        parent::__construct();
    }

    public function get_sub_plan_by_id($id) {
        $this->db->select('*');
        $this->db->from('me_subscription_plan_master');
        $this->db->where('sub_plan_id',$id);
        $query = $this->db->get();
        return $query->row(); 
    }

    public function get_setting_data($id) {
        $this->db->where('sub_setting_data_plan_id', $id);
        return $this->db->get('me_sub_setting_data')->result();
    }

    public function update_doctor_data($data) {
        $this->db->update_batch('me_users',$data, 'user_id'); 
    }

    public function doctors_global_setting_update($doctor_id, $data) {
        $data['updated_at'] = date('Y-m-d H:i:s');
        $this->db->where('doctor_id', $doctor_id);
        $this->db->update('me_doctors_global_setting', $data);
    }

    public function create_bulk_rows($table_name,$data) {
        $this->db->insert_batch($table_name, $data);
    }

    public function update_doctor_subscriptions($data) {
        $this->db->update_batch('me_doctor_subscriptions',$data, 'doctor_id'); 
    }

    public function get_doctor_subscription($id, $columns = '*') {
        $this->db->select($columns);
        $this->db->from('me_doctor_subscriptions ds');
        $this->db->join('me_subscription_plan_master spm', 'spm.sub_plan_id=ds.sub_plan_id');
        $this->db->where('ds.doctor_id', $id);
        $this->db->where('ds.doctor_subscriptions_status', 1);
        $query = $this->db->get();
        return $query->row(); 
    }

    public function get_subscription_plan($columns = '*', $where = array()) {
        $this->db->select($columns);
        $this->db->from('me_subscription_plan_master');
        $this->db->where('sub_status', 1);
        $this->db->where('sub_plan_id !=', 3);
        if(!empty($where['sub_price'])) {
            $this->db->where('sub_price >', $where['sub_price']);
        }
        $query = $this->db->get();
        return $query->result(); 
    }

    public function get_doctors_global_setting($doctor_id, $columns = '*', $setting_name = '') {
        $this->db->select($columns);
        $this->db->from('me_doctors_global_setting');
        $this->db->where('setting_status', 1);
        $this->db->where('doctor_id', $doctor_id);
        if(!empty($setting_name))
            $this->db->where('setting_name', $setting_name);
        $query = $this->db->get();
        return $query->result();
    }

    public function doctor_details($doctor_id) {
        $this->db->select('u.user_first_name,u.user_last_name,a.address_name_one,u.user_email,u.user_phone_number');
        $this->db->from("me_users u");
        $this->db->join("me_doctor_clinic_mapping cm", "cm.doctor_clinic_mapping_user_id=u.user_id");
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

    public function create_doctor_payment_details($data) {
        $this->db->insert('me_user_payment_details', $data);
        return $this->db->insert_id();
    }

    function update_doctor_subscription_by_id($doctor_id, $data) {
        $this->db->where('doctor_id', $doctor_id);
        $this->db->where('doctor_subscriptions_status', 1);
        $this->db->update('me_doctor_subscriptions', $data);
    }

    function update_doctor_payment_details($order_id, $data) {
        $this->db->where('razorpay_order_id', $order_id);
        $this->db->update('me_user_payment_details', $data);
    }

    function get_doctor_payment_details_by_payment_id($payment_id) {
        $this->db->select('
            u.user_first_name,
            u.user_last_name,
            u.user_email,
            a.address_name_one,
            a.address_pincode,
            dpd.payment_id,
            dpd.user_id,
            dpd.invoice_no,
            dpd.created_at,
            dpd.plan_start_date,
            dpd.plan_end_date,
            dpd.sub_total,
            dpd.settlement_discount,
            dpd.tax_cgst_percent,
            dpd.tax_sgst_percent,
            dpd.tax_igst_percent,
            dpd.tax_cgst_amount,
            dpd.tax_sgst_amount,
            dpd.tax_igst_amount,
            dpd.discount_amount,
            dpd.paid_amount,
            dpd.razorpay_payment_id,
            dpd.razorpay_order_id,
            dpd.payment_status,
            dpd.sub_plan_name,
            dpd.sub_plan_validity,
            dpd.receipt_url,
            dpd.detail_type,
            c.clinic_name,
            city.city_name,
            s.state_name,
            cou.country_name,
            spm.sub_description
        ');
        $this->db->from("me_user_payment_details dpd");
        $this->db->join("me_users u", "u.user_id=dpd.user_id");
        $this->db->join('me_subscription_plan_master spm', 'spm.sub_plan_id=dpd.sub_plan_id', "LEFT");
        $this->db->join("me_doctor_clinic_mapping cm", "cm.doctor_clinic_mapping_user_id=u.user_id AND cm.doctor_clinic_mapping_is_primary=1");
        $this->db->join("me_clinic c", "cm.doctor_clinic_mapping_clinic_id=c.clinic_id");
        $this->db->join("me_address a", "
                a.address_user_id=cm.doctor_clinic_mapping_clinic_id AND 
                a.address_type=2 AND 
                a.address_status=1");
        $this->db->join("me_city city", "city.city_id=a.address_city_id");
        $this->db->join("me_state s", "s.state_id=a.address_state_id");
        $this->db->join("me_countries cou", "cou.country_id=a.address_country_id");
        $this->db->where("cm.doctor_clinic_mapping_role_id", 1);
        $this->db->where("dpd.payment_status", 1);
        $this->db->where('payment_id', $payment_id);
        $query = $this->db->get();
        return $query->row();
    }

    public function get_global_setting() {
        $this->db->select('global_setting_key,global_setting_value');
        $this->db->from('me_global_settings');
        $this->db->where('global_setting_status', 1);
        $this->db->where_in('global_setting_key', ['Company Name','invoice_address','gujarat_branch_gst','payment_receipt_note','gst_registration_state_id']);
        $query = $this->db->get();
        return $query->result();
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

    public function get_doctor_subscription_history($doctor_id) {
        $this->db->select('
            dpd.payment_id,
            dpd.invoice_no,
            dpd.created_at,
            dpd.plan_start_date,
            dpd.plan_end_date,
            dpd.sub_total,
            dpd.tax_cgst_percent,
            dpd.tax_sgst_percent,
            dpd.tax_cgst_amount,
            dpd.tax_sgst_amount,
            dpd.paid_amount,
            dpd.payment_status,
            dpd.sub_plan_name,
            dpd.sub_plan_validity,
            dpd.receipt_url,
        ');
        $this->db->from("me_user_payment_details dpd");
        $this->db->join('me_subscription_plan_master spm', 'spm.sub_plan_id=dpd.sub_plan_id');
        $this->db->where("dpd.payment_status", 1);
        $this->db->where('dpd.user_id', $doctor_id);
        $this->db->order_by('dpd.payment_id', 'DESC');
        $query = $this->db->get();
        return $query->result();
    }

    function update_doctor_payment_detail_payment_id($payment_id, $data) {
        $this->db->where('payment_id', $payment_id);
        $this->db->update('me_user_payment_details', $data);
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
    function get_doctor_last_payment_details($where, $columns = '*') {
        $this->db->select($columns);
        $this->db->from("me_user_payment_details");
        $this->db->where('payment_status', 1);
        foreach ($where as $key => $value) {
            $this->db->where($key, $value);
        }
        $this->db->order_by('payment_id', 'DESC');
        $this->db->limit(1);
        $query = $this->db->get();
        return $query->row();
    }

    function get_promo_code($promo_code, $doctor_id) {
        $datetime = get_display_date_time("Y-m-d H:i:s");
        $this->db->select('pc.promo_id,pc.promo_code,pc.promo_discount_type,pc.promo_discount,pu.promo_users_status');
        $this->db->from("me_promo_code pc");
        $this->db->join("me_promo_users pu", "pu.promo_code=pc.promo_code AND pu.promo_users_status=1 AND pu.user_id=".$doctor_id, "LEFT");
        $this->db->where('pc.promo_status', 1);
        $this->db->where('LOWER(pc.promo_code)', strtolower($promo_code));
        $this->db->where('pc.promo_start_date <=', date('Y-m-d', strtotime($datetime)));
        $this->db->where('pc.promo_expiry_date >=', date('Y-m-d', strtotime($datetime)));
        $this->db->group_start();
        $this->db->where('pc.promo_assign_user_id IS NULL');
        $this->db->or_where("FIND_IN_SET('".$doctor_id."',pc.promo_assign_user_id) != 0");
        $this->db->group_end();
        $query = $this->db->get();
        return $query->row();
    }

    function get_promo_by_id($promo_id) {
        $this->db->select('promo_id,promo_code,promo_discount_type,promo_discount,promo_start_date,promo_expiry_date,promo_assign_user_id');
        $this->db->from("me_promo_code");
        $this->db->where('promo_id', $promo_id);
        $query = $this->db->get();
        return $query->row();
    }

    public function create_promo_user($data) {
        $this->db->insert('me_promo_users', $data);
        return $this->db->insert_id();
    }

    function get_doctor_payment_by_id($razorpay_order_id, $columns = '*') {
        $this->db->select($columns);
        $this->db->from("me_user_payment_details");
        $this->db->where('razorpay_order_id', $razorpay_order_id);
        $query = $this->db->get();
        return $query->row();
    }

    function update_promo_users($payment_id, $data) {
        $this->db->where('payment_id', $payment_id);
        $this->db->update('me_promo_users', $data);
    }
}