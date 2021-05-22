<?php

class Common_model extends MY_Model {

    protected $TBL_LANGUAGES;
    protected $news_table;
    protected $contact_us_table;

    function __construct() {
        parent::__construct();
        $this->language_table	= TBL_LANGUAGES;
        $this->news_table 		= TBL_NEWS;
        $this->contact_us_table = TBL_CONTACT_US;
        $this->users_table 		= TBL_USERS;
        $this->clinic_table 	= TBL_CLINICS;
        $this->api_logs_table 	= TBL_API_LOGS;
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

        $get_contact_us_sql = "SELECT contact_us_name, 
                                      contact_us_email, 
                                      contact_us_country_id,
                                      contact_us_phone_number,
                                      CONCAT(contact_us_country_code,' ',contact_us_phone_number) as contact_number
                               FROM 
                                    " . $this->contact_us_table . " 
                               WHERE 
                                    contact_us_status = 1 AND ( 
                                    contact_us_country_id = '" . $requested_data['country_id'] . "'  OR 
                                    contact_us_country_id = 0 ) ";

        if (!empty($requested_data)) {
            $get_contact_us_sql .= " AND contact_us_type = ".$requested_data['user_type'];
        }

        $get_contact_us_data = $this->get_all_rows_by_query($get_contact_us_sql);
        return $get_contact_us_data;
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
    public function validate_data($table_name, $fields, $where, $where_in = []) {
		$get_data = $this->get_single_row($table_name, $fields, $where, [], '', [], [], $where_in);
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
            if($get_user_role['doctor_clinic_mapping_role_id'] == 2) {
                $role_where = array(
                    'user_role_access_user_id' => $user_id,
                );
                $get_role_access = $this->Common_model->get_single_row(TBL_USER_ROLE_ACCESS, 'user_role_access_data', $role_where);
                $get_role_detail = array('user_role_data' => $get_role_access['user_role_access_data']);
            } else {
                $role_where = array(
                    'user_role_id' => $get_user_role['doctor_clinic_mapping_role_id'],
                    'user_role_status' => 1
                );
                $get_role_detail = $this->Common_model->get_single_row(TBL_USER_ROLE, 'user_role_data', $role_where);
            }
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

        $get_setting_sql = " SELECT 
                                " . $column . "  
                            FROM 
                                " . TBL_GLOBAL_SETTINGS . " 
                            WHERE 
                                global_setting_status = 1 ";

        if (!empty($requested)) {
            $get_setting_sql .= " AND global_setting_name IN (" . $requested . ") ";
        }

        $get_setting = $this->get_all_rows_by_query($get_setting_sql);

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
        $html_content = "<h1 style='text-align:center'></h1>";
        if (!empty($get_contact_us)) {
            $html_content = $get_contact_us['static_page_content'];
        }
        return $html_content;
    }

    public function create_bulk($table_name, $data) {
        $this->db->insert_batch($table_name, $data);
        return true;
    }

    public function get_cache_tables() {
        $this->db->select('global_setting_value');
        $this->db->from('me_global_settings');
        $this->db->where('global_setting_key', 'cache_tables');
        $selectQueStr = $this->db->get_compiled_select();
       if(IS_FILE_CACHING_ACTIVE == 1){
            create_cache_sub_dir('me_global_settings');
            $hashObj = sha1($selectQueStr);
            $this->load->driver('cache');
            if(!$resultCached = $this->cache->file->get($hashObj)){
                $result = $this->Common_model->get_all_rows_by_query($selectQueStr);
                $this->cache->file->save($hashObj, $result, CACHE_TTL);
            }else{
                $result = $resultCached;
            }
        }else{
            $result = $this->Common_model->get_all_rows_by_query($selectQueStr);
        }
        if(!empty($result[0]['global_setting_value']))
            return json_decode($result[0]['global_setting_value'],true);
        else 
            return array();
    }

    public function get_states($country_id, $columns) {
        $this->db->select($columns);
        $this->db->from(TBL_STATES);
        $this->db->where('state_country_id', $country_id);
        $this->db->where('state_status', 1);
        $selectQueStr = $this->db->get_compiled_select();
       if(IS_FILE_CACHING_ACTIVE == 1){
            create_cache_sub_dir('me_state');
            $hashObj = sha1($selectQueStr);
            $this->load->driver('cache');
            if(!$resultCached = $this->cache->file->get($hashObj)){
                $result = $this->Common_model->get_all_rows_by_query($selectQueStr);
                $this->cache->file->save($hashObj, $result, CACHE_TTL);
            }else{
                $result = $resultCached;
            }
        }else{
            $result = $this->Common_model->get_all_rows_by_query($selectQueStr);
        }
        return $result;
    }

    public function get_cities($state_id, $columns) {
        $this->db->select($columns);
        $this->db->from(TBL_CITIES);
        $this->db->where('city_state_id', $state_id);
        $this->db->where('city_status', 1);
        $selectQueStr = $this->db->get_compiled_select();
       if(IS_FILE_CACHING_ACTIVE == 1){
            create_cache_sub_dir('me_city');
            $hashObj = sha1($selectQueStr);
            $this->load->driver('cache');
            if(!$resultCached = $this->cache->file->get($hashObj)){
                $result = $this->Common_model->get_all_rows_by_query($selectQueStr);
                $this->cache->file->save($hashObj, $result, CACHE_TTL);
            }else{
                $result = $resultCached;
            }
        }else{
            $result = $this->Common_model->get_all_rows_by_query($selectQueStr);
        }
        return $result;
    }

    public function get_colleges($search_college, $columns) {
        $this->db->select($columns);
        $this->db->from(TBL_COLLEGE);
        if (!empty($search_college)) {
            $this->db->like('college_name', $search_college);
        }
        $this->db->where('college_status', 1);
        $this->db->order_by('college_id', 'ASC');
        $selectQueStr = $this->db->get_compiled_select();
       if(IS_FILE_CACHING_ACTIVE == 1){
            create_cache_sub_dir('me_college');
            $hashObj = sha1($selectQueStr);
            $this->load->driver('cache');
            if(!$resultCached = $this->cache->file->get($hashObj)){
                $result = $this->Common_model->get_all_rows_by_query($selectQueStr);
                $this->cache->file->save($hashObj, $result, CACHE_TTL);
            }else{
                $result = $resultCached;
            }
        }else{
            $result = $this->Common_model->get_all_rows_by_query($selectQueStr);
        }
        return $result;
    }

    public function get_country_code($columns) {
        $this->db->select($columns);
        $this->db->from(TBL_COUNTRIES);
        $this->db->where('country_status', 1);
        $selectQueStr = $this->db->get_compiled_select();
       if(IS_FILE_CACHING_ACTIVE == 1){
            create_cache_sub_dir('me_countries');
            $hashObj = sha1($selectQueStr);
            $this->load->driver('cache');
            if(!$resultCached = $this->cache->file->get($hashObj)){
                $result = $this->Common_model->get_all_rows_by_query($selectQueStr);
                $this->cache->file->save($hashObj, $result, CACHE_TTL);
            }else{
                $result = $resultCached;
            }
        }else{
            $result = $this->Common_model->get_all_rows_by_query($selectQueStr);
        }
        return $result;
    }

    public function get_diseases($columns) {
        $this->db->select($columns);
        $this->db->from(TBL_DISEASES);
        $this->db->where('disease_status', 1);
        $selectQueStr = $this->db->get_compiled_select();
       if(IS_FILE_CACHING_ACTIVE == 1){
            create_cache_sub_dir('me_disease');
            $hashObj = sha1($selectQueStr);
            $this->load->driver('cache');
            if(!$resultCached = $this->cache->file->get($hashObj)){
                $result = $this->Common_model->get_all_rows_by_query($selectQueStr);
                $this->cache->file->save($hashObj, $result, CACHE_TTL);
            }else{
                $result = $resultCached;
            }
        }else{
            $result = $this->Common_model->get_all_rows_by_query($selectQueStr);
        }
        return $result;
    }

    public function get_payment_type($columns) {
        $this->db->select($columns);
        $this->db->from(TBL_PAYMENT_TYPE);
        $this->db->where('payment_type_status', 1);
        $selectQueStr = $this->db->get_compiled_select();
       if(IS_FILE_CACHING_ACTIVE == 1){
            create_cache_sub_dir('me_payment_types');
            $hashObj = sha1($selectQueStr);
            $this->load->driver('cache');
            if(!$resultCached = $this->cache->file->get($hashObj)){
                $result = $this->Common_model->get_all_rows_by_query($selectQueStr);
                $this->cache->file->save($hashObj, $result, CACHE_TTL);
            }else{
                $result = $resultCached;
            }
        }else{
            $result = $this->Common_model->get_all_rows_by_query($selectQueStr);
        }
        return $result;
    }

    public function get_procedure($search, $columns) {
        $this->db->select($columns);
        $this->db->from(TBL_PROCEDURE);
        $this->db->where('procedure_status', 1);
        if (!empty($search)) {
            $this->db->like('procedure_title', $search);
        }
        $selectQueStr = $this->db->get_compiled_select();
       if(IS_FILE_CACHING_ACTIVE == 1){
            create_cache_sub_dir('me_procedure');
            $hashObj = sha1($selectQueStr);
            $this->load->driver('cache');
            if(!$resultCached = $this->cache->file->get($hashObj)){
                $result = $this->Common_model->get_all_rows_by_query($selectQueStr);
                $this->cache->file->save($hashObj, $result, CACHE_TTL);
            }else{
                $result = $resultCached;
            }
        }else{
            $result = $this->Common_model->get_all_rows_by_query($selectQueStr);
        }
        return $result;
    }

    public function get_reports_types($parent_id,$columns) {
        $this->db->select($columns);
        $this->db->from(TBL_REPORT_TYPES);
        $this->db->where('report_type_status', 1);
        $this->db->where_not_in('report_type_id', [15,16]);
        if(!empty($parent_id))
            $this->db->where('report_type_parent_id', 1);
        $this->db->order_by('report_type_order_by');
        $selectQueStr = $this->db->get_compiled_select();
       if(IS_FILE_CACHING_ACTIVE == 1){
            create_cache_sub_dir('me_report_types');
            $hashObj = sha1($selectQueStr);
            $this->load->driver('cache');
            if(!$resultCached = $this->cache->file->get($hashObj)){
                $result = $this->Common_model->get_all_rows_by_query($selectQueStr);
                $this->cache->file->save($hashObj, $result, CACHE_TTL);
            }else{
                $result = $resultCached;
            }
        }else{
            $result = $this->Common_model->get_all_rows_by_query($selectQueStr);
        }
        return $result;
    }

    public function get_health_anlaytics_test($columns, $where) {
        $this->db->select($columns);
        $this->db->from(TBL_HEALTH_ANALYTICS);
        $this->db->where('health_analytics_test_status', 1);
        if(!empty($where['health_analytics_test_parent_id']))
            $this->db->where('health_analytics_test_parent_id', $where['health_analytics_test_parent_id']);

        if(!empty($where['health_analytics_test_name']))
            $this->db->like('health_analytics_test_name', $where['health_analytics_test_name']);

        if(!empty($where['health_analytics_test_type'])){
            $this->db->where('health_analytics_test_type', $where['health_analytics_test_type']);
        } else {
            $this->db->where('health_analytics_test_type', 2);
        }
        $order_by = "";
        if(!empty($where['medsign_speciality_id'])){
            $find_where = '';
            $medsign_speciality_id_arr = explode(',', $where['medsign_speciality_id']);
            foreach ($medsign_speciality_id_arr as $speciality_id) {
                $find_where .= "FIND_IN_SET(".$speciality_id.", health_analytics_medsign_speciality_id) OR ";
            }
            $this->db->where("(".trim($find_where)." health_analytics_test_doctor_id=".$where['doctor_id'].")");
            $sort_data = INVESTIGATION_SORT_ARR[$medsign_speciality_id_arr[0]];
            $order_by = " ORDER BY FIELD(health_analytics_medsign_speciality_id, ".$sort_data."),health_analytics_test_rank";
        }
        $selectQueStr = $this->db->get_compiled_select();
        $selectQueStr .= $order_by;
        /*if(IS_FILE_CACHING_ACTIVE == 1){
            create_cache_sub_dir('me_health_analytics_test');
            $hashObj = sha1($selectQueStr);
            $this->load->driver('cache');
            if(!$resultCached = $this->cache->file->get($hashObj)){
                $result = $this->Common_model->get_all_rows_by_query($selectQueStr);
                $this->cache->file->save($hashObj, $result, CACHE_TTL);
            }else{
                $result = $resultCached;
            }
        }else{
            $result = $this->Common_model->get_all_rows_by_query($selectQueStr);
        }*/
        $result = $this->Common_model->get_all_rows_by_query($selectQueStr);
        return $result;
    }

    public function get_investigation_instruction($where) {
        $this->db->select('ins.health_analytics_test_id,ins.instruction');
        $this->db->from('me_investigation_instructions ins');
        $this->db->join('me_health_analytics_test ha', "ha.health_analytics_test_id=ins.health_analytics_test_id");
        $this->db->where('ins.status', 1);
        $this->db->where('ins.doctor_id', $where['doctor_id']);
        if(!empty($where['search']))
            $this->db->like('LOWER(ins.instruction)', strtolower($where['search']));
        if(!empty($where['health_analytics_ids'])){
            $this->db->where_in('ins.health_analytics_test_id', $where['health_analytics_ids']);
        } elseif(!empty($where['parent_id'])){
            $this->db->where('ins.health_analytics_test_id', $where['parent_id']);
        } else{
            $this->db->where('ha.health_analytics_test_name', $where['health_analytics_test_name']);
        }

        $this->db->order_by('ins.created_at', "DESC");
        $query = $this->db->get();
        return $query->result();
    }

    function update_multiple($table,$data,$field) {
        $this->db->update_batch($table,$data, $field); 
    }

    public function get_medical_condition() {
        $this->db->select('medical_condition_id,medical_condition_name');
        $this->db->from(TBL_MEDICAL_CONDITIONS);
        $this->db->where('medical_condition_status', 1);
        $selectQueStr = $this->db->get_compiled_select();
       if(IS_FILE_CACHING_ACTIVE == 1){
            create_cache_sub_dir('me_medical_conditions');
            $hashObj = sha1($selectQueStr);
            $this->load->driver('cache');
            if(!$resultCached = $this->cache->file->get($hashObj)){
                $result = $this->Common_model->get_all_rows_by_query($selectQueStr);
                $this->cache->file->save($hashObj, $result, CACHE_TTL);
            }else{
                $result = $resultCached;
            }
        }else{
            $result = $this->Common_model->get_all_rows_by_query($selectQueStr);
        }
        return $result;
    }
    public function get_user_setting($where) {
        $this->db->select('*');
        $this->db->from(TBL_SETTING);
        if(!empty($where['setting_clinic_id']))
            $this->db->where('setting_clinic_id', $where['setting_clinic_id']);
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

    public function search_instruction($where) {
        if(!empty($where['search'])) {
            $this->db->select('id, diet_instruction');
            $this->db->from('me_diet_instruction');
            $this->db->where('(doctor_id = '. $where['doctor_id'] . ' OR doctor_id IS NULL)');
            $this->db->where('instruction_type', $where['type']);
            $this->db->like('diet_instruction', $where['search']);
            $this->db->where('status', 1);
            $this->db->order_by('id', 'DESC');
            $this->db->limit(50);
            $query = $this->db->get();
            return $query->result();
        } else {
            return array();
        }
    }
    public function check_instruction_exists($where) {
        if(!empty($where['search'])) {
            $this->db->select('id, LOWER(diet_instruction) AS diet_instruction');
            $this->db->from('me_diet_instruction');
            $this->db->where('(doctor_id = '. $where['doctor_id'] . ' OR doctor_id IS NULL)');
            $this->db->where('instruction_type', $where['type']);
            if(is_array($where['search']))
                $this->db->where_in('diet_instruction', $where['search']);
            else
                $this->db->where('diet_instruction', $where['search']);
            $this->db->where('status', 1);
            $query = $this->db->get();
            // echo $this->db->last_query();die;
            return $query->result();
        } else {
            return array();
        }
    }
    public function get_instruction($where, $count = false) {
        if(!empty($where['doctor_id'])) {
            $this->db->select('id, diet_instruction');
            $this->db->from('me_diet_instruction');
            $this->db->where('doctor_id', $where['doctor_id']);
            $this->db->where('instruction_type', $where['type']);
            $this->db->where('status', 1);
            if(!empty($where['search']))
                $this->db->like('diet_instruction', $where['search']);
            $this->db->order_by('id', 'DESC');
            if(!$count)
                $this->db->limit($where['per_page'], (($where['page'] - 1) * $where['per_page']));
            $query = $this->db->get();
            if($count)
                return $query->num_rows();
            else
                return $query->result();
        } else {
            return array();
        }
    }

    public function get_investigations($where, $count=false) {
        if(!empty($where['doctor_id'])) {
            $this->db->select('
                ha.health_analytics_test_id,
                ha.health_analytics_test_doctor_id,
                ha.health_analytics_test_name,
                ha.health_analytics_test_created_at
            ');
            $this->db->from('me_health_analytics_test ha');
            $this->db->join('me_investigation_instructions ins', "ha.health_analytics_test_id = ins.health_analytics_test_id");
            $this->db->where("ins.doctor_id", $where['doctor_id']);
            $this->db->where("ha.health_analytics_test_status", 1);
            $this->db->where('ha.health_analytics_test_type', 2);
            if(!empty($where['search']))
                $this->db->like('ha.health_analytics_test_name', $where['search']);
            $this->db->group_by("ha.health_analytics_test_id");
            $query1 = $this->db->get_compiled_select();

            $this->db->select('
                ha.health_analytics_test_id,
                ha.health_analytics_test_doctor_id,
                ha.health_analytics_test_name,
                ha.health_analytics_test_created_at
            ');
            $this->db->from('me_health_analytics_test ha');
            $this->db->join('me_investigation_instructions ins', "ha.health_analytics_test_id = ins.health_analytics_test_id", "LEFT");
            $this->db->where("ins.doctor_id IS NULL");
            $this->db->where("ha.health_analytics_test_status", 1);
            $this->db->where('ha.health_analytics_test_type', 2);
            $this->db->where('ha.health_analytics_test_doctor_id', $where['doctor_id']);
            if(!empty($where['search']))
                $this->db->like('ha.health_analytics_test_name', $where['search']);
            $this->db->group_by("ha.health_analytics_test_id");
            if(!$count)
                $this->db->limit($where['per_page'], (($where['page'] - 1) * $where['per_page']));
            $query2 = $this->db->get_compiled_select();
            // echo $query1 . ' UNION ' . $query2;die;
            $query = $this->db->query($query1 . ' UNION ' . $query2);
            if($count)
                return $query->num_rows();
            else
                return $query->result();
        } else {
            return array();
        }
    }

    public function get_investigation_instructions($where) {
        if(!empty($where['doctor_id'])) {
            $this->db->select('ins.instruction');
            $this->db->from('me_investigation_instructions ins');
            $this->db->where('doctor_id', $where['doctor_id']);
            $this->db->where('health_analytics_test_id', $where['health_analytics_test_id']);
            $this->db->where('status', 1);
            $query = $this->db->get();
            return $query->result();
        } else {
            return array();
        }
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

    public function get_instruction_exists($where) {
        if(!empty($where['search'])) {
            $this->db->select('id, LOWER(diet_instruction) AS diet_instruction, translation_lang_id, translation_text');
            $this->db->from('me_diet_instruction');
            $this->db->join('me_translation', 'id=translation_note_id AND translation_status=1', "LEFT");
            $this->db->where('(doctor_id = '. $where['doctor_id'] . ' OR doctor_id IS NULL)');
            $this->db->where('instruction_type', $where['type']);
            if(is_array($where['search']))
                $this->db->where_in('diet_instruction', $where['search']);
            else
                $this->db->where('diet_instruction', $where['search']);
            $this->db->where('status', 1);
            $query = $this->db->get();
            // echo $this->db->last_query();die;
            return $query->result();
        } else {
            return array();
        }
    }

    public function get_share_link($where) {
    	if(!empty($where['doctor_id'])) {
    		$this->db->select('
    			registration_share_id,
                DATE_FORMAT(registration_share_expiry_date, "%d/%m/%Y") as expiry_date,
                registration_share_expiry_date,
                registration_share_social_media_id,
                registration_share_clinic_id,
                social_media_name,
                clinic_name
                ');
    		$this->db->from('me_registration_share_link');
    		$this->db->join('me_social_media_master', 'social_media_id=registration_share_social_media_id', "LEFT");
    		$this->db->join('me_clinic', 'clinic_id=registration_share_clinic_id', "LEFT");
    		$this->db->where('registration_share_status', 1);
            $this->db->where('registration_share_user_type', 2);
    		$this->db->where('registration_share_doctor_id', $where['doctor_id']);
    		$query = $this->db->get();
    		return $query->result();
    	} else {
    		return array();
    	}
    }

    public function get_prescription_template($where) {
        $this->db->select('template_id,template_title,template_header,template_footer,template_meta,template_image');
        $this->db->from('me_prescription_template');
        $this->db->where('(template_user_id = '. $where['doctor_id'] . ' OR template_user_id IS NULL)');
        $this->db->where('template_type', $where['template_type']);
        $this->db->where('template_status', 1);
        $query = $this->db->get();
        return $query->result();
    }

    public function getDoctorAlldetails($doctor_id, $clinic_id) {
        $this->db->select("
            d.user_id as doctor_id,
            CONCAT(d.user_first_name,' ',d.user_last_name) as doctor_name,
            d.user_sign_filepath,
            dd.doctor_detail_speciality,
            a.address_name,
            a.address_name_one,
            a.address_pincode,
            a.address_locality,
            c.clinic_name,
            c.clinic_email,
            c.clinic_contact_number,
            c.clinic_id,
            dcm.doctor_clinic_mapping_tele_fees,
            dcm.doctor_clinic_mapping_fees,
            GROUP_CONCAT(DISTINCT(dq.doctor_qualification_degree) ORDER BY doctor_qualification_id ASC) as doctor_qualification_degree,
            GROUP_CONCAT(DISTINCT(dreg.doctor_council_registration_number)) AS doctor_regno,
            city.city_name,
            state.state_name,
            s.setting_data
        ");
        $this->db->from("me_users d");
        $this->db->join("me_doctor_details dd", "d.user_id = dd.doctor_detail_doctor_id");
        $this->db->join("me_doctor_clinic_mapping dcm", "d.user_id = dcm.doctor_clinic_mapping_user_id ANd dcm.doctor_clinic_mapping_role_id=1");
        $this->db->join('me_settings s', 's.setting_user_id=d.user_id AND s.setting_clinic_id=dcm.doctor_clinic_mapping_clinic_id AND s.setting_type=4 AND s.setting_status=1', "LEFT");
        $this->db->join("me_clinic c", "c.clinic_id = dcm.doctor_clinic_mapping_clinic_id");
        $this->db->join("me_address a", "a.address_user_id = c.clinic_id AND a.address_type = 2");
        $this->db->join("me_city city", "a.address_city_id = city.city_id", "LEFT");
        $this->db->join("me_state state", "a.address_state_id = state.state_id", "LEFT");
        $this->db->join("me_doctor_qualifications dq", "dq.doctor_qualification_user_id=d.user_id AND dq.doctor_qualification_status=1", "LEFT");
        $this->db->join("me_doctor_registration dreg", "d.user_id = dreg.doctor_registration_user_id AND dreg.doctor_registration_status = 1", "LEFT");
        $this->db->where('d.user_type', 2);
        $this->db->where('d.user_status', 1);
        $this->db->where('d.user_id', $doctor_id);
        $this->db->where('c.clinic_id', $clinic_id);
        $this->db->group_by('d.user_id');
        $query = $this->db->get();
        return $query->row();
    }

}
