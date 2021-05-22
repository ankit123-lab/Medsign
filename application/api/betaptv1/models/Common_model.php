<?php

class Common_model extends MY_Model {

    protected $TBL_LANGUAGES;
    protected $news_table;
    protected $contact_us_table;

    function __construct() {
        parent::__construct();
        $this->language_table = TBL_LANGUAGES;
        $this->news_table = TBL_NEWS;
        $this->contact_us_table = TBL_CONTACT_US;
        $this->laboratories = TBL_LABORATORIES;
    }

    /**
     * Description :- This function is used to get the id of the language
     * 
     * 
     * 
     * @return int
     */
    public function get_language_id() {
        $fields = array('l_id');
        $where = array(
            'l_code' => $this->current_lang,
            'l_status' => 1,
        );
        $language_id = $this->get_single_row($this->language_table, $fields, $where);

        if (!empty($language_id) && count($language_id) > 0) {
            return $language_id['l_id'];
        }
        return 0;
    }

    /**
     * Description :- This function is used to get the news added by the admin
     * 
     * 
     * 
     * @return type
     */
    public function get_news() {
        $where = array(
            'news_status' => 1
        );
        $get_news = $this->get_all_rows(TBL_NEWS, '*', $where);

        return $get_news;
    }

    /**
     * Description :- This function is used to get the contact details about ME
     * 
     * 
     * 
     * @param type $requested_data
     * @return type
     */
    public function get_contact_us_info($requested_data) {
        $columns = 'contact_us_name, 
          contact_us_email, 
          contact_us_country_id,
          contact_us_phone_number,
          CONCAT(contact_us_country_code," ",contact_us_phone_number) as contact_number';
        $this->db->select($columns)->from($this->contact_us_table);
        if (!empty($requested_data)) {
            $this->db->where('contact_us_type', 1); 
        }
        $this->db->where("contact_us_status = 1 AND 
                        contact_us_country_id = '" . $requested_data['country_id'] . "'  OR 
                        contact_us_country_id = 0 "); 
        
        $selectQueStr = $this->db->get_compiled_select();
        if(IS_FILE_CACHING_ACTIVE == 1){
            $hashObj = $this->contact_us_table . '_' .sha1($selectQueStr);
            $this->load->driver('cache');
            if(!$resultCached = $this->cache->file->get($hashObj)){
                $result = $this->Common_model->get_all_rows_by_query($selectQueStr);
                $this->cache->file->save($hashObj, $result, 3600); // 3600 S = 1HR
            }else{
                $result = $resultCached;
            }
        }else{
            $result = $this->Common_model->get_all_rows_by_query($selectQueStr);
        }
        return $result;
    }

    /**
     * Description :- This function is used to generate the random string based on the count input
     * 
     * 
     * 
     * @param type $alpha_count
     * @param type $uppercase_count
     * @param type $special_count
     * @param type $numeric_count
     * @return type
     */
    public function generate_random_string($alpha_count, $uppercase_count, $special_count, $numeric_count) {

        $alpha = '123456789abcdefghijklmnopqrstuvwxyz';
        $uppercase = 'ABCDEFGHJKLMNOPQRSTUVWXYZ';
        $special = "!@#$%^&*()_-=+;:?";
        $numeric = '23456789';

        $alpha_length = strlen($alpha);
        $random_string = '';

        for ($i = 0; $i < $alpha_count; $i++) {
            $random_string .= $alpha[rand(0, $alpha_length - 1)];
        }

        $uppercase_length = strlen($uppercase);
        for ($i = 0; $i < $uppercase_count; $i++) {
            $random_string .= $uppercase[rand(0, $uppercase_length - 1)];
        }

        $special_length = strlen($special);
        for ($i = 0; $i < $special_count; $i++) {
            $random_string .= $special[rand(0, $special_length - 1)];
        }

        $numeric_length = strlen($numeric);
        for ($i = 0; $i < $numeric_count; $i++) {
            $random_string .= $numeric[rand(0, $numeric_length - 1)];
        }

        return str_shuffle($random_string);
    }

    /**
     * Description :- This function is used to get the setting of the user
     * 
     * 
     * 
     * @param type $requested_data
     * @return type
     */
    public function get_setting($requested_data) {

        $column = 'setting_data, 
                   setting_user_id, 
                   setting_clinic_id, 
                   setting_type, 
                   setting_data_type,
                   user_type';

        $left_join = array(
            TBL_USERS => 'setting_user_id = user_id'
        );
        $get_setting_data = $this->get_single_row(TBL_SETTING, $column, $requested_data, $left_join, 'LEFT');
        return $get_setting_data;
    }

    /**
     * Description :- THis function is used to validate data based on the input
     * 
     * 
     * 
     * @param type $table_name
     * @param type $fields
     * @param type $where
     * @return int
     */
    public function validate_data($table_name, $fields, $where) {
        $get_data = $this->get_single_row($table_name, $fields, $where);
        if (empty($get_data)) {
            return 2;
        }
        return 1;
    }

    /**
     * Description :- This function is used to get the weight of the user
     * 
     * 
     * 
     * @param type $user_id
     * @return type
     */
    public function get_vital_data($user_id) {
        //get the weight from the vital record
        $get_vital_sql = "  SELECT 
                                vital_report_weight,
                                vital_report_updated_at
                            FROM 
                                " . TBL_VITAL_REPORTS . " 
                            WHERE 
                                vital_report_user_id = '" . $user_id . "'
                            AND        
                                vital_report_status = 1
                            ORDER BY 
                                 vital_report_id DESC LIMIT 0,1 ";

        $get_vital_data = $this->Common_model->get_single_row_by_query($get_vital_sql);

        return $get_vital_data;
    }

    /**
     * Description :- This function get the modules permission based on the user role
     * 
     * 
     * 
     * @param type $user_id
     * @return int
     */
    public function get_the_role($user_id) {

        //get the role of the user
        $where = array(
            'doctor_clinic_mapping_user_id' => $user_id,
            'doctor_clinic_mapping_status' => 1
        );
        $get_user_role = $this->Common_model->get_single_row(TBL_DOCTOR_CLINIC_MAPPING, 'doctor_clinic_mapping_role_id', $where);

        if (!empty($get_user_role)) {
            //also sending the role wise permissions of the user
            $role_where = array(
                'user_role_id' => $get_user_role['doctor_clinic_mapping_role_id'],
                'user_role_status' => 1
            );
            $get_role_detail = $this->Common_model->get_single_row(TBL_USER_ROLE, 'user_role_data', $role_where);

            if (!empty($get_role_detail)) {
                return $get_role_detail;
            }
        }

        return 0;
    }

    public function get_global_settings($requested='', $flag='') {

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

        if (!empty($requested)) {
            $selectQueStr .= " AND global_setting_name IN (" . $requested . ") ";
        }

        if(IS_FILE_CACHING_ACTIVE == 1){
            $hashObj = TBL_GLOBAL_SETTINGS . '_' .sha1($selectQueStr);
            $this->load->driver('cache');
            if(!$resultCached = $this->cache->file->get($hashObj)){
                $get_setting = $this->Common_model->get_all_rows_by_query($selectQueStr);
                $this->cache->file->save($hashObj, $result, 3600); // 3600 S = 1HR
            }else{
                $get_setting = $resultCached;
            }
        }else{
            $get_setting = $this->Common_model->get_all_rows_by_query($selectQueStr);
        }

        if ($flag == 1) {
            return array_column($get_setting, 'global_setting_value', 'global_setting_name');
        }
        return $get_setting;
    }

    public function terms_conditions_html() {
        $get_contact_us_where = array(
            "static_page_title" => "terms_conditions",
            "static_page_status" => 1
        );
        $get_contact_us = $this->Common_model->get_single_row(TBL_STATIC_PAGE, "static_page_content", $get_contact_us_where);
        $html_content = "<h1 style='text-align:center'>Comming Soon....</h1>";
        if (!empty($get_contact_us)) {
            $html_content = $get_contact_us['static_page_content'];
        }
        return $html_content;
    }
    
    /**
     * Description :- This function is used to add the laboratory 
     * 
     * @author Kanaiya Makwana
     * 
     * Modified Date :- 2018-12-04
     * 
     * @param type $laboratory_array
     * @return type inserted id
     */
    public function add_laboratory($laboratory_array = array()) {
        if (!empty($laboratory_array)) {
            $insert_id = $this->insert($this->laboratories, $laboratory_array);
            return $insert_id;
        }
        return 0;
    }

    public function get_activity_levels($where, $columns = '*') {
        $this->db->select($columns)->from(TBL_ACTIVITY_LEVES);
        foreach ($where as $key => $value) {
            $this->db->where($key, $value); 
        }
        $selectQueStr = $this->db->get_compiled_select();
        if(IS_FILE_CACHING_ACTIVE == 1){
            $hashObj = TBL_ACTIVITY_LEVES . '_' .sha1($selectQueStr);
            $this->load->driver('cache');
            if(!$resultCached = $this->cache->file->get($hashObj)){
                $result = $this->Common_model->get_all_rows_by_query($selectQueStr);
                $this->cache->file->save($hashObj, $result, 3600); // 3600 S = 1HR
            }else{
                $result = $resultCached;
            }
        }else{
            $result = $this->Common_model->get_all_rows_by_query($selectQueStr);
        }
        return $result;
    }

    public function get_alcohol($where, $columns = '*') {
        $this->db->select($columns)->from(TBL_ALCOHOL);
        foreach ($where as $key => $value) {
            $this->db->where($key, $value); 
        }
        $selectQueStr = $this->db->get_compiled_select();
        if(IS_FILE_CACHING_ACTIVE == 1){
            $hashObj = TBL_ALCOHOL . '_' .sha1($selectQueStr);
            $this->load->driver('cache');
            if(!$resultCached = $this->cache->file->get($hashObj)){
                $result = $this->Common_model->get_all_rows_by_query($selectQueStr);
                $this->cache->file->save($hashObj, $result, 3600); // 3600 S = 1HR
            }else{
                $result = $resultCached;
            }
        }else{
            $result = $this->Common_model->get_all_rows_by_query($selectQueStr);
        }
        return $result;
    }

    public function get_appointment_type($where, $columns = '*') {
        $this->db->select($columns)->from(TBL_APPOINTMENT_TYPE);
        foreach ($where as $key => $value) {
            $this->db->where($key, $value); 
        }
        $selectQueStr = $this->db->get_compiled_select();
        if(IS_FILE_CACHING_ACTIVE == 1){
            $hashObj = TBL_APPOINTMENT_TYPE . '_' .sha1($selectQueStr);
            $this->load->driver('cache');
            if(!$resultCached = $this->cache->file->get($hashObj)){
                $result = $this->Common_model->get_all_rows_by_query($selectQueStr);
                $this->cache->file->save($hashObj, $result, 3600); // 3600 S = 1HR
            }else{
                $result = $resultCached;
            }
        }else{
            $result = $this->Common_model->get_all_rows_by_query($selectQueStr);
        }
        return $result;
    }

    public function get_food_preference($where, $columns = '*') {
        $this->db->select($columns)->from(TBL_FOOD_PREFERENCE);
        foreach ($where as $key => $value) {
            $this->db->where($key, $value); 
        }
        $selectQueStr = $this->db->get_compiled_select();
        if(IS_FILE_CACHING_ACTIVE == 1){
            $hashObj = TBL_FOOD_PREFERENCE . '_' .sha1($selectQueStr);
            $this->load->driver('cache');
            if(!$resultCached = $this->cache->file->get($hashObj)){
                $result = $this->Common_model->get_all_rows_by_query($selectQueStr);
                $this->cache->file->save($hashObj, $result, 3600); // 3600 S = 1HR
            }else{
                $result = $resultCached;
            }
        }else{
            $result = $this->Common_model->get_all_rows_by_query($selectQueStr);
        }
        return $result;
    }

    public function get_smoking_habbit($where, $columns = '*') {
        $this->db->select($columns)->from(TBL_SMOKING_HABBIT);
        foreach ($where as $key => $value) {
            $this->db->where($key, $value); 
        }
        $selectQueStr = $this->db->get_compiled_select();
        if(IS_FILE_CACHING_ACTIVE == 1){
            $hashObj = TBL_SMOKING_HABBIT . '_' .sha1($selectQueStr);
            $this->load->driver('cache');
            if(!$resultCached = $this->cache->file->get($hashObj)){
                $result = $this->Common_model->get_all_rows_by_query($selectQueStr);
                $this->cache->file->save($hashObj, $result, 3600); // 3600 S = 1HR
            }else{
                $result = $resultCached;
            }
        }else{
            $result = $this->Common_model->get_all_rows_by_query($selectQueStr);
        }
        return $result;
    }

    public function get_occupation($where, $columns = '*') {
        $this->db->select($columns)->from(TBL_OCCUPATIONS);
        foreach ($where as $key => $value) {
            $this->db->where($key, $value); 
        }
        $selectQueStr = $this->db->get_compiled_select();
        if(IS_FILE_CACHING_ACTIVE == 1){
            $hashObj = TBL_OCCUPATIONS . '_' .sha1($selectQueStr);
            $this->load->driver('cache');
            if(!$resultCached = $this->cache->file->get($hashObj)){
                $result = $this->Common_model->get_all_rows_by_query($selectQueStr);
                $this->cache->file->save($hashObj, $result, 3600); // 3600 S = 1HR
            }else{
                $result = $resultCached;
            }
        }else{
            $result = $this->Common_model->get_all_rows_by_query($selectQueStr);
        }
        return $result;
    }

    public function get_language($where, $columns = '*') {
        $this->db->select($columns)->from(TBL_LANGUAGES);
        foreach ($where as $key => $value) {
            $this->db->where($key, $value); 
        }
        $selectQueStr = $this->db->get_compiled_select();
        if(IS_FILE_CACHING_ACTIVE == 1){
            $hashObj = TBL_LANGUAGES . '_' .sha1($selectQueStr);
            $this->load->driver('cache');
            if(!$resultCached = $this->cache->file->get($hashObj)){
                $result = $this->Common_model->get_all_rows_by_query($selectQueStr);
                $this->cache->file->save($hashObj, $result, 3600); // 3600 S = 1HR
            }else{
                $result = $resultCached;
            }
        }else{
            $result = $this->Common_model->get_all_rows_by_query($selectQueStr);
        }
        return $result;
    }

    public function get_medical_condition($where, $columns = '*') {
        $this->db->select($columns)->from(TBL_MEDICAL_CONDITIONS);
        foreach ($where as $key => $value) {
            $this->db->where($key, $value); 
        }
        $selectQueStr = $this->db->get_compiled_select();
        if(IS_FILE_CACHING_ACTIVE == 1){
            $hashObj = TBL_MEDICAL_CONDITIONS . '_' .sha1($selectQueStr);
            $this->load->driver('cache');
            if(!$resultCached = $this->cache->file->get($hashObj)){
                $result = $this->Common_model->get_all_rows_by_query($selectQueStr);
                $this->cache->file->save($hashObj, $result, 3600); // 3600 S = 1HR
            }else{
                $result = $resultCached;
            }
        }else{
            $result = $this->Common_model->get_all_rows_by_query($selectQueStr);
        }
        return $result;
    }

    public function get_user_setting($where, $columns = '*') {
        $this->db->select($columns)->from(TBL_SETTING);
        foreach ($where as $key => $value) {
          if(is_array($value))
            $this->db->where_in($key, $value); 
          else
            $this->db->where($key, $value); 
        }
        $query = $this->db->get();
        return $query->result_array();
    }

    public function doctor_payment_mode_link($where) {
        if(!empty($where['doctor_id']) && !empty($where['clinic_id'])) {
            $this->db->select('dp.doctor_payment_mode_id, dp.doctor_payment_mode_upi_link,dp.doctor_payment_mode_qrcode_img_path,dp.doctor_payment_mode_master_id,pm.payment_mode_name');
            $this->db->from('me_doctor_payment_mode_link dp');
            $this->db->join('me_payment_mode_master pm', 'pm.payment_mode_id=dp.doctor_payment_mode_master_id');
            $this->db->where('doctor_payment_mode_doctor_id', $where['doctor_id']);
            $this->db->where('doctor_payment_mode_clinic_id', $where['clinic_id']);
            $this->db->where('doctor_payment_mode_status', 1);
            $query = $this->db->get();
            return $query->result();
        } else {
            return array();
        }
    }

    public function get_doctor_global_setting($where) {
        if(!empty($where['doctor_id'])) {
            $this->db->select('setting_name,setting_value');
            $this->db->from('me_doctors_global_setting');
            $this->db->where('doctor_id', $where['doctor_id']);
            $this->db->where('setting_status', 1);
            if(!empty($where['setting_name']))
                $this->db->where_in('setting_name', $where['setting_name']);
            $query = $this->db->get();
            return $query->result();
        } else {
            return array();
        }
    }

    public function get_patient_share_link_log($patients_id_arr) {
        if(!empty($patients_id_arr)) {
            $this->db->select('id,patient_id');
            $this->db->from('me_patient_share_link_log');
            $this->db->where_in('patient_id', $patients_id_arr);
            $this->db->where('status', 1);
            $query = $this->db->get();
            return $query->result();
        } else {
            return array();
        }
    }

    function update_multiple($table,$data,$field) {
        $this->db->update_batch($table,$data, $field); 
    }
}
