<?php

class User_model extends MY_Model {

    protected $users_table;
    protected $user_details_table;
    protected $user_details_columns;
    protected $user_columns;

    function __construct() {
        parent::__construct();
        $this->users_table = TBL_USERS;
        $this->user_type_table = TBL_USER_TYPE;
        $this->user_auth_table = TBL_USER_AUTH;
        $this->address_table = TBL_ADDRESS;
        $this->user_details_table = TBL_USER_DETAILS;

        $this->user_details_columns = array(
            'user_id',
            'user_first_name',
            'user_last_name',
            'user_email',
            'user_phone_number',
            'user_phone_verified',
            'user_email_verified',
            'user_details_languages_known',
            'user_status',
            'user_unique_id',
            'user_photo_filepath',
            'user_gender',
            'user_caregiver_id',
            'user_language_id',
            'address_name',
            'address_name_one',
            'address_city_id',
            'city_name as address_city_name',
            'address_state_id',
            'state_name as address_state_name',
            'address_country_id',
            'address_pincode',
            'address_latitude',
            'address_longitude',
            'address_locality',
            'user_details_height',
            'user_details_weight',
            'user_details_dob',
            'user_details_blood_group',
            'user_details_food_allergies',
            'user_details_medicine_allergies',
            'user_details_other_allergies',
            'user_details_chronic_diseases',
            'user_details_injuries',
            'user_details_surgeries',
            'user_details_smoking_habbit',
            'user_details_alcohol',
            'user_details_activity_level',
            'user_details_activity_days',
            'user_details_activity_hours',
            'user_details_food_preference',
            'user_details_occupation',
            'user_details_marital_status',
            'user_details_emergency_contact_person',
            'user_details_emergency_contact_number',
            'user_details_modifed_at',
            'user_password',
            'user_type',
            'auth_phone_number'
        );

        $this->user_columns = array(
            'user_id',
            'user_first_name',
            'user_last_name',
            'user_email',
            'user_phone_number',
            'user_phone_verified',
            'user_email_verified',
            'user_password',
            'user_status',
            'user_source_id',
            'user_photo_filepath',
            'user_unique_id',
            'user_language_id',
            'user_type AS user_type_id'
        );
        $this->user_family_health_coulumns = array(
            'family_medical_history_id',
            'family_medical_history_medical_condition_id',
            'family_medical_history_relation',
            'family_medical_history_date',
            'family_medical_history_comment'
        );
    }

    /**
     * Description :- This function is used to get the user details by phone number
     * 
     * 
     * 
     * Modified Date :- 2018-02-27
     * 
     * @param type $phone_number
     * @return type
     */
    public function get_details_by_number($phone_number = NULL, $user_type = '', $user_id = '') {
        $result = array();
        if (!empty($phone_number)) {
            $where = array(
                "user_phone_number" => $phone_number,
                "user_status !=" => 9,
            );
            if(!empty($user_id))
                $where["user_id !="] = $user_id;
            if (!empty($user_type)) {
                $where["user_type"] = $user_type;
            }

            $join_array = array(
                TBL_ADDRESS => 'user_id = address_user_id',
                TBL_USER_DETAILS => 'user_id = user_details_user_id',
                TBL_STATES => 'address_state_id = state_id',
                TBL_CITIES => 'address_city_id = city_id',
                TBL_USER_AUTH => 'user_id = auth_user_id AND auth_type = 1'
            );

//            $result = $this->get_single_row($this->users_table, $this->user_columns, $where);
            $result = $this->get_single_row($this->users_table, $this->user_details_columns, $where, $join_array, 'LEFT');
        }
        return $result;
    }

    /**
     * Description :- This function is used to get the user details by phone number
     * 
     * 
     * 
     * Modified Date :- 2018-02-27
     * 
     * @param type $phone_number
     * @return type
     */
    public function check_user_number_exists($phone_number = NULL, $user_type = '') {
        $result = array();
        if (!empty($phone_number)) {
            $where = array(
                "user_phone_number" => $phone_number,
                "user_status !=" => 9,
            );
            if (!empty($user_type)) {
                $where["user_type"] = $user_type;
            }
            $result = $this->get_single_row($this->users_table, $this->user_columns, $where);
        }
        return $result;
    }

    /**
     * Description :- This function is used to get the user details by email
     * 
     * 
     * 
     * Modified Date :- 2018-02-27
     * 
     * @param type $phone_number
     * @return type
     */
    public function get_details_by_email($email = NULL, $user_type = '') {
        $result = array();
        if (!empty($email)) {
            $where = array(
                "user_email" => $email,
                "user_status !=" => 9
            );

            if (!empty($user_type)) {
                $where['user_type'] = $user_type;
            }

            $result = $this->get_single_row($this->users_table, $this->user_columns, $where);
        }
        return $result;
    }

    /**
     * Description :- This function is used to get the user details by id
     * @param type $phone_number
     * @return type
     */
    public function get_details_by_id($id = NULL, $columns = '') {
        if(empty($columns)) {
            $columns = $this->user_columns;
        }
        $result = array();
        if (!empty($id)) {
            $where = array(
                "user_id" => $id,
                "user_status !=" => 9
            );
            $result = $this->get_single_row($this->users_table, $columns, $where);
        }
        return $result;
    }
	
    /**
     * Description :- This function is used to get the user details from the temporary table by id
     * 
     * 
     * 
     * Modified Date :- 2018-10-01
     * 
     * @param type $id
     * @return type
     */
    public function get_user_temp_details_by_id($id = NULL) {
        $result = array();
        if (!empty($id)) {
            $where = array(
                "temp_user_id" => $id,
                "temp_user_status !=" => 9
            );

            $column = " temp_user_id,
                        temp_user_email,
                        temp_user_phone_number,
                        temp_user_phone_verified,
                        temp_user_status,
                        temp_user_unique_id,
                        temp_auth_otp_expiry_time, 
                        temp_auth_resend_timestamp, 
                        temp_auth_resend_count, 
                        temp_auth_attempt_count,
                        temp_auth_code,
                        temp_user_password,
                        temp_user_gender,
                        temp_user_language_id, 
                        temp_user_user_type,
                        temp_is_term_accepted,
                        temp_is_term_accepted_date";

            $result = $this->get_single_row(TBL_USER_TEMP, $column, $where);
        }
        return $result;
    }

    /**
     * Description :- This function is used to get the user details by unique id
     * 
     * 
     * 
     * Modified Date :- 2018-02-27
     * 
     * @param type $phone_number
     * @return type
     */
    public function get_user_details_by_uniqueid($unique_id = NULL, $user_type = '') {
        $result = array();
        if (!empty($unique_id)) {
            $where = array(
                "user_unique_id" => $unique_id,
                "user_status !=" => 9
            );

            if (!empty($user_type)) {
                $where['user_type'] = $user_type;
            }

            $result = $this->get_single_row($this->users_table, $this->user_columns, $where);
        }
        return $result;
    }

    /**
     * Description :- This function is used to store the user details.
     * 
     * 
     * 
     * Modified Date :- 2018-02-27
     * 
     * @param type $user_data
     * @return int
     */
    public function register_user($user_data = array()) {
        if (!empty($user_data)) {
            $insert_id = $this->insert($this->users_table, $user_data);
            return $insert_id;
        }
        return 0;
    }

    /**
     * Description :- This function is used register the user as temporary 
     * after otp verify user will registered
     * 
     * 
     * 
     * Modified Date :- 2018-10-01
     * 
     * @param type $user_data
     * @return int
     */
    public function register_temp_user($user_data = array()) {
        if (!empty($user_data)) {
            $inserted_id = $this->insert(TBL_USER_TEMP, $user_data);
            return $inserted_id;
        }
        return 0;
    }

    /**
     * Description :- This function is used to store the user details.
     * 
     * 
     * 
     * Modified Date :- 2018-02-27
     * @param type $user_type
     * @return int
     */
    public function store_user_type($user_type = array()) {
        if (!empty($user_type)) {
            $insert_user_type = $this->insert($this->user_type_table, $user_type);
            return $insert_user_type;
        }
        return 0;
    }

    /**
     * Description :- This function is used to store the auth details.
     * 
     * 
     * 
     * Modified Date :- 2018-02-27
     * @param type $user_type
     * @return int
     */
    public function store_auth_detail($user_auth = array()) {
        if (!empty($user_auth)) {
            $auth_id = $this->insert($this->user_auth_table, $user_auth);
            return $auth_id;
        }
        return 0;
    }

    /**
     * Description :- This function is used to check the user exists or not
     * 
     * 
     * 
     * Modified Date :- 2018-02-27
     * 
     * @param type $id
     * @return type
     */
    public function get_user_exist($id = NULL) {
        $result = array();
        if (!empty($id)) {

            $where = array(
                "user_id" => $id,
                "user_status !=" => 9
            );

            $result = $this->get_single_row($this->users_table, "user_id", $where);
        }
        return $result;
    }

    /**
     * Description :- This function is used the details of user based on the id.
     * 
     * 
     * 
     * Modified Date :- 2018-02-27
     * 
     * @param type $id
     * @return type
     */
    public function get_user_details_by_id($id = NULL, $type = '', $columns = '') {
        $result = array();
        if (!empty($id)) {
            $where = array(
                "user_id" => $id,
                "user_status !=" => 9
            );

            if (!empty($type) && $type == 1) {
                $where['user_type'] = $type;
            }

            $join_array = array(
                TBL_ADDRESS => 'user_id = address_user_id',
                TBL_USER_DETAILS => 'user_id = user_details_user_id',
                TBL_STATES => 'address_state_id = state_id',
                TBL_CITIES => 'address_city_id = city_id',
                TBL_COUNTRIES => 'address_country_id = country_id',
                TBL_USER_AUTH => 'user_id = auth_user_id AND auth_type = 1'
            );
            if(empty($columns)) {
                $columns = $this->user_details_columns;
            }
            $result = $this->get_single_row($this->users_table, $columns, $where, $join_array, 'LEFT');
        }
        return $result;
    }

    /**
     * Description :- This function is used the details with clinic details.
     * 
     * 
     * @param array $where
     * @param columns $columns
     * @return array
     */
    public function get_user_details_with_clinic_mapp_by_id($where, $columns = '*') {
        $result = array();
        if (is_array($where) && count($where) > 0) {
            $this->db->select($columns)->from($this->users_table . ' u');
            $this->db->join(TBL_DOCTOR_CLINIC_MAPPING . ' c', 'c.doctor_clinic_mapping_user_id=u.user_id AND c.doctor_clinic_mapping_status=1');
            foreach ($where as $key => $value) {
                $this->db->where($key, $value);
            }
            $query = $this->db->get();
            if($query->num_rows() > 0) {
                return $query->row();
            }
        }
        return $result;
    }

    /**
     * Description :- This function is used the update the details of the user.
     * 
     * 
     * 
     * Modified Date :- 2018-02-27
     * 
     * @param type $id
     * @param type $request_data
     * @return type
     */
    public function update_profile($id = 0, $update_data) {
        $is_update = 0;
        if (!empty($id) && is_numeric($id)) {
            $is_update = $this->update($this->users_table, $update_data, array('user_id' => $id));
        }
        return $is_update;
    }

    /**
     * Description :- This functoin is used to update the addres of the user
     * 
     * 
     * 
     * Modified Date :- 2018-03-06
     * 
     * @param type $update_address_data
     * @param type $user_id
     */
    public function update_address($user_id, $update_address_data) {
        $address_is_update = 0;
        //check user address is exist or not
        $get_address_details = $this->get_single_row($this->address_table, 'address_id', array('address_user_id' => $user_id, 'address_type' => 1));

        if (!empty($get_address_details)) {
            $update_address_data['address_modified_at'] = $this->utc_time_formated;
            $address_is_update = $this->update($this->address_table, $update_address_data, array('address_id' => $get_address_details['address_id']));
        } else {
            $update_address_data['address_user_id'] = $user_id;
            $update_address_data['address_created_at'] = $this->utc_time_formated;
            $address_is_update = $this->insert($this->address_table, $update_address_data);
        }
        return $address_is_update;
    }

    /**
     * Description :- This function is used to store the user details
     * 
     * 
     * 
     * Modified Date :- 2018-03-06
     * 
     * @param type $user_id
     * @param type $update_user_details
     * @return type
     */
    public function update_user_details($user_id, $update_user_details) {

        $user_details_is_update = 0;

        //check user details is exist or not
        $get_user_details = $this->get_single_row($this->user_details_table, 'user_details_id', array('user_details_user_id' => $user_id));

        if (!empty($get_user_details)) {
            $update_user_details['user_details_modifed_at'] = $this->utc_time_formated;
            $user_details_is_update = $this->update($this->user_details_table, $update_user_details, array('user_details_user_id' => $user_id));
        } else {
            $update_user_details['user_details_created_at'] = $this->utc_time_formated;
            $update_user_details['user_details_user_id'] = $user_id;
            $user_details_is_update = $this->insert($this->user_details_table, $update_user_details);
        }

        return $user_details_is_update;
    }

    /**
     * Description :- This function is used to fetch the otp details for verification.
     * 
     * 
     * 
     * Modified Date :- 2018-02-27
     * 
     * @param type $phone_number
     * @return int
     */
    public function get_otp_details_by_phone_number($phone_number = NULL, $user_type = 1) {
        if (!empty($phone_number)) {
            $where = array(
                'user_phone_number' => $phone_number,
                'user_status !=' => 9,
                'user_type' => $user_type
            );
            $left_join = array(
                $this->user_auth_table => "user_id = auth_user_id AND auth_type=2 ",
                'me_doctor_clinic_mapping' => "user_id = doctor_clinic_mapping_user_id AND doctor_clinic_mapping_status=1 ",
            );
            $get_details = $this->get_single_row($this->users_table, 'auth_id,auth_otp_expiry_time,auth_attempt_count, auth_code, user_phone_verified, user_id,doctor_clinic_mapping_role_id,doctor_clinic_mapping_doctor_id', $where, $left_join, 'LEFT');
            return $get_details;
        }
        return 0;
    }

    /**
     * Description :- This function is used to fetch the email token for verification.
     * 
     * 
     * 
     * Modified Date :- 2018-02-27
     * 
     * @param type $phone_number
     * @return int
     */
    public function get_verification_details_by_email($email = NULL, $user_type = 1) {
        if (!empty($email)) {
            $where = array(
                'user_email' => $email,
                'user_status !=' => 9,
                'auth_type' => 1,
                'user_type' => $user_type
            );
            $left_join = array(
                $this->user_auth_table => "user_id = auth_user_id"
            );

            $columns = 'user_first_name, user_last_name, 
                        auth_otp_expiry_time, auth_code, 
                        user_email_verified, user_id';

            $get_details = $this->get_single_row($this->users_table, $columns, $where, $left_join, 'LEFT');
            return $get_details;
        }
        return 0;
    }

    /**
     * Description :- This function is used to update the authentication details of the user.
     * 
     * 
     * 
     * Modified Date :- 2018-02-27
     * 
     * @param type $auth_update_data
     * @param type $auth_update_where
     * @return int
     */
    public function update_auth_details($auth_update_data = array(), $auth_update_where = array()) {
        if (!empty($auth_update_data) && !empty($auth_update_where)) {
            $check_auth_entry = $this->get_single_row($this->user_auth_table, "auth_id", $auth_update_where);
            if (!empty($check_auth_entry)) {
                $is_update = $this->update($this->user_auth_table, $auth_update_data, $auth_update_where);
            } else {
                $auth_update_data['auth_user_id'] = $auth_update_where['auth_user_id'];
                $auth_update_data['auth_created_at'] = date("Y-m-d H:i:s");

                $is_update = $this->insert($this->user_auth_table, $auth_update_data);
            }
            return $is_update;
        }
        return 0;
    }

    /**
     * Description :- This function is used to get the family members details
     * 
     * 
     * 
     * Modified Date :- 2018-03-20
     * 
     * @param type $where
     * @return type
     */
    public function get_family_members($where = array()) {

        $get_family_member = array();
        $columns = 'user_id, user_first_name, user_last_name, user_photo_filepath, user_email, user_parent_relation';
        if (!empty($where)) {
            $get_family_member = $this->get_all_rows(TBL_USERS, $columns, $where);
            if (!empty($get_family_member) && count($get_family_member) > 0) {
                return $get_family_member;
            }
        }
        return $get_family_member;
    }

    /**
     * Description :- THis function is used to get the family medical history 
     * 
     * @param type $user_id
     * @return type
     */
    public function get_family_medical_history($user_id) {

        $where = array(
            "family_medical_history_user_id" => $user_id,
            "family_medical_history_status" => 1
        );

        $family_data = $this->get_all_rows(TBL_FAMILY_MEDICAL_HISTORY, $this->user_family_health_coulumns, $where, array(), array(), array(), '', 'LEFT');
        return $family_data;
    }

    /**
     * Description :- This function is used to search the patient 
     * 
     * 
     * 
     * @param type $search_text
     * @return type
     */
    public function search_patient($search_text = '', $doctor_id = '', $is_patient_from_gdb = false, $user_source_id_arr = array()) {

        $get_search_patient_sql = "SELECT 
                                        CONCAT(u.user_first_name, ' ',u.user_last_name) AS user_name, 
                                        CONCAT(u.user_first_name, ' ',u.user_last_name,' ',u.user_phone_number,' ',u.user_unique_id) AS user_search, 
                                        u.user_unique_id,
                                        u.user_patient_id,
                                        u.user_id,
                                        u.user_phone_number,
                                        u.user_email,
                                        u.user_source_id,
                                        fm.mapping_relation,
                                        CONCAT(pu.user_first_name, ' ',pu.user_last_name) AS parent_user_name,
                                        pu.user_phone_number AS parent_user_phone_number, 
                                        appointment_id,
                                        appointment_clinic_id
                                   FROM 
                                        " . $this->users_table . " AS u 
                                   LEFT JOIN
                                        " . TBL_APPOINTMENTS . " ON u.user_id = appointment_user_id AND appointment_status != 9 ";

        if (!empty($doctor_id)) {
            $get_search_patient_sql.=" AND appointment_doctor_user_id=" . $doctor_id . " ";
        }

        $get_search_patient_sql .= "LEFT JOIN
                                        " . TBL_PATIENT_FAMILY_MEMBER_MAPPING . " AS fm ON fm.patient_id = u.user_id AND fm.mapping_status = 1
                                        LEFT JOIN
                                        " . $this->users_table . " AS pu  ON pu.user_id = fm.parent_patient_id AND pu.user_status != 9 AND
                                        pu.user_type = 1 ";

        $get_search_patient_sql .= "
                           WHERE 
                                        u.user_status != 9 AND
                                        u.user_type = 1 AND
                                        (
                                        CONCAT(u.user_first_name, ' ',u.user_last_name) LIKE '%" . $search_text . "%' OR
                                        u.user_unique_id LIKE '%" . $search_text . "%' OR
                                        u.user_phone_number LIKE '%" . $search_text . "%' OR
                                        u.user_patient_id LIKE '%" . $search_text . "%' OR
                                        CONCAT(pu.user_first_name, ' ',pu.user_last_name) LIKE '%" . $search_text . "%' OR
                                        pu.user_unique_id LIKE '%" . $search_text . "%' OR
                                        pu.user_phone_number LIKE '%" . $search_text . "%'    
                                        ) ";
        if(!$is_patient_from_gdb && !empty($doctor_id) && count($user_source_id_arr) > 0) {
            $get_search_patient_sql .= " AND (u.user_source_id IN (" . implode(',', $user_source_id_arr) . ") OR appointment_doctor_user_id = " . $doctor_id . ") ";                                
        }

        $get_search_patient_sql .= " GROUP BY u.user_id ORDER BY user_name ASC";
                                        //echo $get_search_patient_sql;die;
        $get_patient_data = $this->get_all_rows_by_query($get_search_patient_sql);
        return $get_patient_data;
    }

    /**
     * Description :- This function is used to get the details of patient diseases
     * 
     * 
     * 
     * @param type $user_id
     * @return type
     */
    public function get_diseases_data($user_id) {

        $columns = 'disease_name, disease_id';

        $where = array(
            "patient_disease_user_id" => $user_id,
            "patient_disease_status" => 1
        );
        $join_array = array(
            TBL_DISEASES => 'patient_disease_disease_id = disease_id',
        );

        $diseases_data = $this->get_all_rows(TBL_PATIENT_DISEASES, $columns, $where, $join_array, array(), array(), '', 'LEFT');

        return $diseases_data;
    }

    /**
     * Description :- This function is used to get the vitals of the patient
     * 
     * 
     * 
     * @param type $vital_data
     * @return type
     */
    public function get_patient_vital($vital_data) {

        $columns = "vital_report_date,
                    vital_report_spo2,
                    vital_report_weight,
                    vital_report_bloodpressure_systolic,
                    vital_report_bloodpressure_diastolic,
                    vital_report_bloodpressure_type,
                    vital_report_pulse,
                    vital_report_temperature,
                    vital_report_temperature_type,
                    vital_report_temperature_taken,
                    vital_report_resp_rate,
                    vital_report_doctor_id,
                    vital_report_doctor_id,
                    user_first_name,
                    user_last_name,
                    user_type,
                    CONCAT(user_first_name,' ',user_last_name) as user_name,
                    vital_report_user_id,
                    vital_report_doctor_id as created_by,
                    vital_report_id";

        $get_vital_query = "SELECT
                                " . $columns . " 
                            FROM 
                                " . TBL_VITAL_REPORTS . " 
                            LEFT JOIN 
                                " . TBL_USERS . " ON user_id = vital_report_doctor_id
                            WHERE
                                vital_report_status != 9  ";


        if (!empty($vital_data['user_id'])) {
            $get_vital_query .= " AND vital_report_user_id = '" . $vital_data['user_id'] . "'  ";
        }

        if (!empty($vital_data['flag'] == 2)) {
            $get_vital_query .= " AND vital_report_doctor_id != " . $vital_data['user_id'];
        }

        if (!empty($vital_data['flag'] == 3)) {
            $get_vital_query .= " AND vital_report_doctor_id = '" . $vital_data['user_id'] . "'  ";
        }

        $get_vital_query .= " ORDER BY vital_report_date DESC ";

        $total_records = $this->get_count_by_query($get_vital_query);

        if (!empty($vital_data['page'])) {
            $get_vital_query .= " LIMIT " . (($vital_data['page'] - 1) * $vital_data['per_page']) . "," . $vital_data['per_page'] . " ";
        }

        $get_vital_data = $this->get_all_rows_by_query($get_vital_query);

        $return_array = array(
            'data' => $get_vital_data,
            'total_records' => $total_records
        );

        return $return_array;
    }

    /**
     * Description :- This function is used to get the reports of the patient
     * 
     * 
     * 
     * @param type $report_data
     * @return type
     */
    public function get_patient_report($report_data) {
		$columns = "file_report_id,
                    file_report_name,
                    file_report_date,
                    file_report_created_at AS created_at,
                    report_type_name,
                    u1.user_first_name as patient_first_name,
                    u1.user_last_name as patient_last_name,
                    CONCAT(u1.user_first_name,' ',u1.user_last_name) as user_patient_name,
                    u.user_first_name,
                    u.user_last_name,
                    u.user_type,
                    cm.doctor_clinic_mapping_role_id,
                    CONCAT(u.user_first_name,' ',u.user_last_name) as user_name,
                    CONCAT(u2.user_first_name,' ',u2.user_last_name) as added_by,
                    file_report_share_status,
                    report_type_id,
                    file_report_doctor_user_id as created_by";

        $get_report_query = "SELECT
                                " . $columns . " 
                            FROM 
                                " . TBL_FILE_REPORTS . " 
                            LEFT JOIN
                                " . TBL_DOCTOR_CLINIC_MAPPING . " as cm ON file_report_added_by_user_id = cm.doctor_clinic_mapping_user_id AND cm.doctor_clinic_mapping_status=1        
                            LEFT JOIN 
                                " . TBL_USERS . " as u ON file_report_doctor_user_id = u.user_id        
                            LEFT JOIN 
                                " . TBL_USERS . " as u1 ON file_report_user_id = u1.user_id 
                            LEFT JOIN 
                                " . TBL_USERS . " as u2 ON file_report_added_by_user_id = u2.user_id           
                            LEFT JOIN
                                " . TBL_REPORT_TYPES . " ON file_report_report_type_id = report_type_id
                            WHERE
                                file_report_status != 9 AND file_report_report_type_id NOT IN(15,16) ";

        if (!empty($report_data['user_id'])) {
            $get_report_query .= " AND file_report_user_id = '" . $report_data['user_id'] . "' ";
        }

        if (!empty($report_data['flag'] == 2)) {
            $get_report_query .= " AND file_report_appointment_id != '' ";
        }

        if (!empty($report_data['flag'] == 3)) {
            $get_report_query .= " AND file_report_appointment_id IS NULL ";
        }
		
		if (!empty($report_data['file_report_report_type_id'])) {
            $get_report_query .= " AND file_report_report_type_id = ".$report_data['file_report_report_type_id'];
        }
		$get_report_query .= " AND (file_report_report_type_id != 11 OR file_report_doctor_user_id=".$report_data['logged_in'].")";
        if(!empty($report_data['search']))
            $get_report_query .= " AND file_report_name LIKE '%".$report_data['search']."%'";
        /*Rx Upload Quary*/
        $columns = "rx_upload_id AS file_report_id,
                    rx_upload_name AS file_report_name,
                    rx_upload_date AS file_report_date,
                    rx_upload_created_at AS created_at,
                    report_type_name,
                    u1.user_first_name as patient_first_name,
                    u1.user_last_name as patient_last_name,
                    CONCAT(u1.user_first_name,' ',u1.user_last_name) as user_patient_name,
                    u.user_first_name,
                    u.user_last_name,
                    u.user_type,
                    cm.doctor_clinic_mapping_role_id,
                    CONCAT(u.user_first_name,' ',u.user_last_name) as user_name,
                    CONCAT(u2.user_first_name,' ',u2.user_last_name) as added_by,
                    rx_upload_share_status AS file_report_share_status,
                    report_type_id,
                    rx_upload_doctor_user_id as created_by";

        $get_report_query .= " UNION SELECT
                                " . $columns . " 
                            FROM 
                                " . TBL_RX_UPLOAD_REPORTS . " 
                            LEFT JOIN
                                " . TBL_DOCTOR_CLINIC_MAPPING . " as cm ON rx_upload_added_by_user_id = cm.doctor_clinic_mapping_user_id AND cm.doctor_clinic_mapping_status=1        
                            LEFT JOIN 
                                " . TBL_USERS . " as u ON rx_upload_doctor_user_id = u.user_id        
                            LEFT JOIN 
                                " . TBL_USERS . " as u1 ON rx_upload_user_id = u1.user_id 
                            LEFT JOIN 
                                " . TBL_USERS . " as u2 ON rx_upload_added_by_user_id = u2.user_id           
                            LEFT JOIN
                                " . TBL_REPORT_TYPES . " ON report_type_id=12
                            WHERE
                                rx_upload_status != 9  ";

        if (!empty($report_data['user_id'])) {
            $get_report_query .= " AND rx_upload_user_id = '" . $report_data['user_id'] . "' ";
        }

        if (!empty($report_data['flag'] == 2)) {
            $get_report_query .= " AND rx_upload_appointment_id != '' ";
        }

        if (!empty($report_data['flag'] == 3)) {
            $get_report_query .= " AND rx_upload_appointment_id IS NULL ";
        }
        
        if (!empty($report_data['file_report_report_type_id'])) {
            $get_report_query .= " AND report_type_id = ".$report_data['file_report_report_type_id'];
        }
        $get_report_query .= " AND (report_type_id != 12 OR rx_upload_doctor_user_id=".$report_data['logged_in'].")";
        if(!empty($report_data['search']))
            $get_report_query .= " AND rx_upload_name LIKE '%".$report_data['search']."%'";

        /*End Rx Upload Quary*/
        $get_report_query .= " ORDER BY file_report_date DESC, created_at DESC ";
        // echo $get_report_query;die;
        $total_records = $this->get_count_by_query($get_report_query);
		if (!empty($report_data['page'])) {
            $get_report_query .= " LIMIT " . (($report_data['page'] - 1) * $report_data['per_page']) . "," . $report_data['per_page'] . " ";
        }
        $get_report_data = $this->get_all_rows_by_query($get_report_query);
        $return_array = array(
            'data' => $get_report_data,
            'total_records' => $total_records
        );
        return $return_array;
    }

    /**
     * Description :- This function is used to get the detail of the report 
     * @param type $report_data
     * @return type
     */
    public function get_report_detail($report_data) {

        $columns = "file_report_id,
                    file_report_name,
                    file_report_date,
                    report_type_name,
                    u1.user_first_name as patient_first_name,
                    u1.user_last_name as patient_last_name,
                    CONCAT(u1.user_first_name,' ',u1.user_last_name) as user_patient_name,
                    u.user_first_name,
                    u.user_last_name,
                    u.user_type,
                    CONCAT(u.user_first_name,' ',u.user_last_name) as user_name";

        $get_report_query = "SELECT
                                " . $columns . " 
                            FROM 
                                " . TBL_FILE_REPORTS . " 
                            LEFT JOIN 
                                " . TBL_USERS . " as u ON file_report_doctor_user_id = u.user_id        
                            LEFT JOIN 
                                " . TBL_USERS . " as u1 ON file_report_user_id = u1.user_id        
                            LEFT JOIN
                                " . TBL_REPORT_TYPES . " ON file_report_report_type_id = report_type_id
                            WHERE
                                file_report_id = '" . $report_data['report_id'] . "'
                            AND
                                file_report_status != 9 ";


        $get_report_data = $this->get_single_row_by_query($get_report_query);

        if (!empty($get_report_data)) {
            $image_where = array(
                'file_report_image_file_report_id' => $report_data['report_id'],
                'file_report_image_status' => 1
            );
            $get_report_images = $this->Common_model->get_all_rows(TBL_FILE_REPORTS_IMAGES, 'file_report_image_url', $image_where);
            $get_report_data["images_thumb"] = array();
            foreach ($get_report_images as $key => $value) {
                $get_report_data["images_thumb"][] = get_image_thumb($value['file_report_image_url']);
                $get_report_data["images"][] = get_file_full_path($value['file_report_image_url']);
            }
        }

        return $get_report_data;
    }

    public function get_rx_upload_detail($report_data) {

        $columns = "rx_upload_id AS file_report_id,
                    rx_upload_name AS file_report_name,
                    rx_upload_date AS file_report_date,
                    report_type_name,
                    u1.user_first_name as patient_first_name,
                    u1.user_last_name as patient_last_name,
                    CONCAT(u1.user_first_name,' ',u1.user_last_name) as user_patient_name,
                    u.user_first_name,
                    u.user_last_name,
                    u.user_type,
                    CONCAT(u.user_first_name,' ',u.user_last_name) as user_name";

        $get_report_query = "SELECT
                                " . $columns . " 
                            FROM 
                                " . TBL_RX_UPLOAD_REPORTS . " 
                            LEFT JOIN 
                                " . TBL_USERS . " as u ON rx_upload_doctor_user_id = u.user_id        
                            LEFT JOIN 
                                " . TBL_USERS . " as u1 ON rx_upload_user_id = u1.user_id        
                            LEFT JOIN
                                " . TBL_REPORT_TYPES . " ON report_type_id=12
                            WHERE
                                rx_upload_id = '" . $report_data['report_id'] . "'
                            AND
                                rx_upload_status != 9 ";


        $get_report_data = $this->get_single_row_by_query($get_report_query);

        if (!empty($get_report_data)) {
            $image_where = array(
                'rx_upload_report_id' => $report_data['report_id'],
                'rx_upload_report_image_status' => 1
            );
            $get_report_images = $this->Common_model->get_all_rows(TBL_RX_UPLOAD_REPORTS_IMAGES, 'rx_upload_report_image_url AS file_report_image_url', $image_where);
            $get_report_data["images_thumb"] = array();
            foreach ($get_report_images as $key => $value) {
                $get_report_data["images_thumb"][] = get_image_thumb($value['file_report_image_url']);
                $get_report_data["images"][] = get_file_full_path($value['file_report_image_url']);
            }
        }

        return $get_report_data;
    }

    
	/**
     * Description :- This function is used to get all details of reports 
     * @param type $report_data is array of reports ids
     * @return type
     */
    public function get_all_report_detail($report_data) {
        $columns = "file_report_id,
                    file_report_name,
                    file_report_date,
                    report_type_name,
                    file_report_image_url";
        $get_report_query = "SELECT 
                                " . $columns . " 
                            FROM 
                                " . TBL_FILE_REPORTS . " 
                            LEFT JOIN 
                                " . TBL_REPORT_TYPES . " ON file_report_report_type_id = report_type_id 
							LEFT JOIN 
                                " . TBL_FILE_REPORTS_IMAGES . " ON file_report_image_file_report_id = file_report_id AND file_report_image_status = 1 
                            WHERE 
                                file_report_id IN (" . implode(',',$report_data['report_id']) . ") 
                            AND 
                                file_report_status != 9 ";
		$get_report_data_all = $this->get_all_rows_by_query($get_report_query);
        if (!empty($get_report_data_all)) {
			$get_report_data = [];
            foreach ($get_report_data_all as $key => $value) {
				if(!isset($get_report_data[$value['file_report_id']]))
					$get_report_data[$value['file_report_id']] = [];
				$get_report_data[$value['file_report_id']]['file_report_id'] 		= $value['file_report_id'];
				$get_report_data[$value['file_report_id']]['file_report_name'] 		= $value['file_report_name'];
				$get_report_data[$value['file_report_id']]['file_report_date'] 		= $value['file_report_date'];
				$get_report_data[$value['file_report_id']]['report_type_name'] 		= $value['report_type_name'];
				$get_report_data[$value['file_report_id']]['images'][] 				= get_file_full_path($value['file_report_image_url']);
				$get_report_data[$value['file_report_id']]['images_thumb'][] 		= get_image_thumb($value['file_report_image_url']);
            }
			$get_report_data = array_values($get_report_data);
        }
        return $get_report_data;
    }
	
	/**
     * Description :- This function is used to get the detail of the clinical notes report
     * 
     * 
     * 
     * @param type $report_data
     * @return type
     */
    public function get_clinic_report_detail($report_data) {

        $columns = "*";

        $get_clinic_report_query = "SELECT
                                " . $columns . " 
                            FROM 
                                " . TBL_CLINICAL_NOTES_REPORT . " 
                            WHERE
                                clinical_notes_reports_id = '" . $report_data['clinical_notes_reports_id'] . "'
                            AND
                                clinical_notes_reports_status != 9 ";


        $get_clinic_report_data = $this->get_single_row_by_query($get_clinic_report_query);

        if (!empty($get_clinic_report_data)) {

            $image_where = array(
                'clinic_notes_reports_images_reports_id' => $get_clinic_report_data['clinical_notes_reports_id'],
                'clinic_notes_reports_images_status' => 1
            );

            $images_columns = 'clinic_notes_reports_images_url, 
                               clinic_notes_reports_images_id,
                               clinic_notes_reports_images_type';

            $get_images = $this->Common_model->get_all_rows(TBL_CLINICAL_NOTES_REPORT_IMAGE, $images_columns, $image_where);
            foreach ($get_images as $key => $value) {
                $get_images[$key]["image_thumb_url"] = get_image_thumb($value['clinic_notes_reports_images_url']);
                $get_images[$key]["clinic_notes_reports_images_url"] = get_file_full_path($value['clinic_notes_reports_images_url']);
            }
            $get_clinic_report_data["images"] = $get_images;
        }

        return $get_clinic_report_data;
    }

    /**
     * Description :- This function is used to get the prescription detail
     * 
     * 
     * 
     * @param type $requested_data
     * @return type
     */
    public function get_prescription_detail($requested_data) {

        $columns = "prescription_id
                    prescription_user_id,
                    prescription_doctor_user_id,
                    prescription_appointment_id,
                    prescription_drug_id,
                    prescription_clinic_id,
                    prescription_date,
                    prescription_drug_name,
					prescription_drug_name_with_unit,
                    prescription_generic_id,
                    prescription_unit_id,
                    prescription_unit_value,
                    prescription_frequency_id,
                    prescription_frequency_value,
                    prescription_frequency_instruction,
                    prescription_intake,
                    prescription_intake_instruction,
                    prescription_duration,
                    prescription_duration_value,
                    prescription_diet_instruction,
                    prescription_dosage,
                    prescription_next_follow_up,
                    user_first_name,
                    user_last_name,
                    user_type,
                    CONCAT(user_first_name,' ',user_last_name) as user_name ";

        $get_prescription_query = "SELECT
                                " . $columns . " 
                            FROM 
                                " . TBL_PRESCRIPTION . " 
                            LEFT JOIN 
                                " . TBL_USERS . " ON user_id = prescription_doctor_user_id        
                            WHERE
                                prescription_id = '" . $requested_data['prescription_id'] . "'
                            AND
                                prescription_status != 9 ";

        $get_prescription_data = $this->get_single_row_by_query($get_prescription_query);
        return $get_prescription_data;
    }

    /**
     * Description :- This function is used to get the lab report detail
     * 
     * 
     * 
     * @param type $requested_data
     * @return type
     */
    public function get_investigation_detail($requested_data) {

        $columns = "lab_report_user_id,
                    lab_report_doctor_user_id,
                    lab_report_appointment_id,
                    lab_report_clinic_id,
                    lab_report_date,
                    lab_report_test_name,
                    lab_report_instruction,
                    user_first_name,
                    user_last_name,
                    user_type,
                    CONCAT(user_first_name,' ',user_last_name) as user_name ";

        $get_lab_report_query = "SELECT
                                " . $columns . " 
                            FROM 
                                " . TBL_LAB_REPORTS . " 
                            LEFT JOIN 
                                " . TBL_USERS . " ON user_id = lab_report_doctor_user_id        
                            WHERE
                                lab_report_id = '" . $requested_data['lab_report_id'] . "'
                            AND
                                lab_report_status != 9 ";

        $get_lab_report_data = $this->get_single_row_by_query($get_lab_report_query);

        return $get_lab_report_data;
    }

    public function check_investigation_exist($requested_data) {
        if(!empty($requested_data['health_analytics_test_name'])) {
            $this->db->select('health_analytics_test_id,LOWER(health_analytics_test_name) AS health_analytics_test_name')->from('me_health_analytics_test');
            $this->db->where_in('LOWER(health_analytics_test_name)', $requested_data['health_analytics_test_name']);
            $this->db->where('health_analytics_test_type', 2);
            $this->db->where('health_analytics_test_status', 1);
            $this->db->where("(health_analytics_test_doctor_id IS NULL OR health_analytics_test_doctor_id=".$requested_data['doctor_id'].")");

            $query = $this->db->get();
            return $query->result();
        } else {
            $result = [];
            return $result;
        }
    }

    public function check_investigation_instruction_exist($requested_data, $instruction_where) {
        $this->db->select('health_analytics_test_id,LOWER(instruction) AS instruction')->from('me_investigation_instructions');
        $this->db->where('status', 1);
        $this->db->where("doctor_id", $requested_data['doctor_id']);
        $this->db->group_start();
        foreach ($instruction_where as $key => $value) {
            $this->db->or_group_start();
            $this->db->where("LOWER(instruction)", $value['instruction']);
            $this->db->where("health_analytics_test_id", $value['health_analytics_test_id']);
            $this->db->group_end();
        }
        $this->db->group_end();
        $query = $this->db->get();
        // echo $this->db->last_query();die;
        return $query->result_array();
    }

    /**
     * Description :- This function is used to get the lab report detail
     * 
     * 
     * 
     * @param type $requested_data
     * @return type
     */
    public function get_procedure_detail($requested_data) {

        $columns = "procedure_report_user_id,
                    procedure_report_doctor_user_id,
                    procedure_report_appointment_id,
                    procedure_report_clinic_id,
                    procedure_report_date,
                    procedure_report_procedure_text,
                    procedure_report_note,
                    user_first_name,
                    user_last_name,
                    user_type,
                    CONCAT(user_first_name,' ',user_last_name) as user_name ";

        $get_procedure_query = "SELECT
                                " . $columns . " 
                            FROM 
                                " . TBL_PROCEDURE_REPORTS . " 
                            LEFT JOIN 
                                " . TBL_USERS . " ON user_id = procedure_report_doctor_user_id        
                            WHERE
                                procedure_report_id = '" . $requested_data['procedure_report_id'] . "'
                            AND
                                procedure_report_status != 9 ";

        $get_procedure_data = $this->get_single_row_by_query($get_procedure_query);

        return $get_procedure_data;
    }

    /**
     * Description :- This function is used to get the detail of the 
     * patient prescription, reports, clinical notes etc... 
     * added by doctor while taking appointment
     * 
     * 
     * 
     * @param type $requested_data
     * @return type
     */
    public function get_patient_report_detail($requested_data) {

        $get_detail = array();

        if ($requested_data['key'] == 1) {

            $vital_columns = 'vital_report_id,
                        vital_report_user_id,
                        vital_report_doctor_id,
                        vital_report_appointment_id,
                        vital_report_clinic_id,
                        vital_report_date,
                        vital_report_spo2,
                        vital_report_weight,
                        vital_report_bloodpressure_systolic,
                        vital_report_bloodpressure_diastolic,
                        vital_report_bloodpressure_type,
                        vital_report_pulse,
                        vital_report_temperature,
                        vital_report_temperature_type,
                        vital_report_temperature_taken,
                        vital_report_resp_rate
                        ';

            $vitals_where = array(
                'vital_report_date' => $requested_data['date'],
                'vital_report_clinic_id' => $requested_data['clinic_id'],
                'vital_report_doctor_id' => $requested_data['doctor_id'],
                'vital_report_appointment_id' => $requested_data['appointment_id'],
                'vital_report_status' => 1,
                'vital_report_user_id' => $requested_data['patient_id']
            );

            if ($requested_data['user_type'] == 1) {
                $vitals_where['vital_report_share_status'] = 1;
            }

            $get_detail = $this->Common_model->get_single_row(TBL_VITAL_REPORTS, $vital_columns, $vitals_where);
        } else if ($requested_data['key'] == 2) {

            $clinic_notes_columns = 'clinical_notes_reports_id,
                                    clinical_notes_reports_user_id,
                                    clinical_notes_reports_doctor_user_id,
                                    clinical_notes_reports_appointment_id,
                                    clinical_notes_reports_clinic_id,
                                    clinical_notes_reports_date,
                                    clinical_notes_reports_kco,
                                    clinical_notes_reports_complaints,
                                    clinical_notes_reports_observation,
                                    clinical_notes_reports_diagnoses,
                                    clinical_notes_reports_add_notes
                                    ';

            $clinic_notes_where = array(
                'clinical_notes_reports_date' => $requested_data['date'],
                'clinical_notes_reports_clinic_id' => $requested_data['clinic_id'],
                'clinical_notes_reports_doctor_user_id' => $requested_data['doctor_id'],
                'clinical_notes_reports_appointment_id' => $requested_data['appointment_id'],
                'clinical_notes_reports_status' => 1,
                'clinical_notes_reports_user_id' => $requested_data['patient_id']
            );

            if ($requested_data['user_type'] == 1) {
                $clinic_notes_where['clinical_notes_reports_share_status'] = 1;
            }

            $get_detail = $this->Common_model->get_single_row(TBL_CLINICAL_NOTES_REPORT, $clinic_notes_columns, $clinic_notes_where);

            if (!empty($get_detail)) {

                $image_where = array(
                    'clinic_notes_reports_images_reports_id' => $get_detail['clinical_notes_reports_id'],
                    'clinic_notes_reports_images_status' => 1
                );

                $images_columns = 'clinic_notes_reports_images_url, 
                               clinic_notes_reports_images_id,
                               clinic_notes_reports_images_type';

                $get_images = $this->Common_model->get_all_rows(TBL_CLINICAL_NOTES_REPORT_IMAGE, $images_columns, $image_where);
                foreach ($get_images as $key => $value) {
                    $get_images[$key]["images_thumb_url"] = get_image_thumb($value['clinic_notes_reports_images_url']);
                    $get_images[$key]["clinic_notes_reports_images_url"] = get_file_full_path($value['clinic_notes_reports_images_url']);
                }
                $get_detail["images"] = $get_images;
            }
        } else if ($requested_data['key'] == 3) {

            $prescription_columns = 'prescription_id,
                                    prescription_user_id,
                                    prescription_doctor_user_id,
                                    prescription_appointment_id,
                                    prescription_drug_id,
                                    prescription_clinic_id,
                                    prescription_date,
                                    prescription_drug_name,
									prescription_drug_name_with_unit,
                                    prescription_generic_id,
                                    prescription_unit_id,
                                    prescription_unit_value,
                                    prescription_dosage,
                                    prescription_frequency_id,
                                    prescription_frequency_value,
                                    prescription_frequency_instruction,
                                    prescription_intake,
                                    prescription_intake_instruction,
                                    prescription_duration,
                                    prescription_duration_value,
                                    prescription_diet_instruction,
                                    prescription_next_follow_up,
                                    drug_frequency_name,
                                    drug_name,
                                    drug_status,
									drug_name_with_unit,
                                    drug_unit_name,
                                    prescription_is_import,
                                    is_capture_compliance,
                                    GROUP_CONCAT(DISTINCT(drug_generic_title)) as generic_title
                                    ';


            $get_prescription_query = "SELECT 
                                            " . $prescription_columns . " 
                                       FROM 
                                            " . TBL_PRESCRIPTION_REPORTS . " 
                                       LEFT JOIN
                                            " . TBL_DRUGS . " ON prescription_drug_id = drug_id
                                       LEFT JOIN
                                            " . TBL_DRUG_UNIT . " ON drug_unit_id = drug_drug_unit_id
                                       LEFT JOIN
                                            " . TBL_DRUG_FREQUENCY . " ON prescription_frequency_id = drug_frequency_id 
                                       LEFT JOIN
                                            " . TBL_DRUG_GENERIC . " ON FIND_IN_SET(drug_generic_id,prescription_generic_id)
                                       WHERE 
                                            prescription_date = '" . $requested_data['date'] . "' 
                                       AND
                                            prescription_clinic_id = '" . $requested_data['clinic_id'] . "' 
                                       AND
                                            prescription_doctor_user_id = '" . $requested_data['doctor_id'] . "' 
                                       AND
                                            prescription_appointment_id = '" . $requested_data['appointment_id'] . "'
                                       AND
                                            prescription_status = 1 
                                       AND
                                            prescription_user_id = '" . $requested_data['patient_id'] . "' ";

            if ($requested_data['user_type'] == 1) {
                $get_prescription_query .= " AND prescription_share_status = 1 ";
            }
            $get_prescription_query .= "GROUP BY prescription_id ";

            $get_detail = $this->get_all_rows_by_query($get_prescription_query);
            foreach ($get_detail as $key => $value) {
                if($value['prescription_is_import'] > 0)
                    $get_detail[$key]['drug_name'] = $value['prescription_drug_name'];
                if($value['prescription_frequency_id'] == 6) {
                        $get_detail[$key]['freq_value'] = "&#10003; - &#10003; - &#10003; - &#10003;";
                } else {
                    if(empty($value['prescription_dosage']) && ($value['drug_unit_name'] == 'Tablets' || $value['drug_unit_name'] == 'IU')) {
                        if($value['drug_unit_name'] == 'IU')
                            $get_detail[$key]['freq_value'] = ucwords(str_replace('-', ' IU - ', $value['prescription_frequency_value'])) . " IU";
                        else
                            $get_detail[$key]['freq_value'] = ucwords(str_replace('-', ' - ', $value['prescription_frequency_value']));
                    } else {
                        $freq_arr = explode('-', $value['prescription_frequency_value']);
                        $freq_data = "";
                        foreach ($freq_arr as $freq_val) {
                            if(trim($freq_val) =="1") {
                                $freq_data .= "&#10003; - ";
                            } elseif(trim($freq_val) =="0") {
                                $freq_data .= "&#10005; - ";
                            }
                        }
                        $get_detail[$key]['freq_value'] = trim($freq_data,' - ');
                    }
                }
            }
        } else if ($requested_data['key'] == 4) {

            $investigation_columns = 'lab_report_id,
                                    lab_report_user_id,
                                    lab_report_doctor_user_id,
                                    lab_report_appointment_id,
                                    lab_report_clinic_id,
                                    lab_report_date,
                                    lab_report_test_name,
                                    lab_report_instruction
                                    ';

            $investigation_where = array(
                'lab_report_date' => $requested_data['date'],
                'lab_report_clinic_id' => $requested_data['clinic_id'],
                'lab_report_doctor_user_id' => $requested_data['doctor_id'],
                'lab_report_appointment_id' => $requested_data['appointment_id'],
                'lab_report_status' => 1,
                'lab_report_user_id' => $requested_data['patient_id']
            );

            $get_detail = $this->Common_model->get_single_row(TBL_LAB_REPORTS, $investigation_columns, $investigation_where);
        } else if ($requested_data['key'] == 5) {

            $procedure_columns = 'procedure_report_id,
                                    procedure_report_user_id,
                                    procedure_report_doctor_user_id,
                                    procedure_report_appointment_id,
                                    procedure_report_clinic_id,
                                    procedure_report_date,
                                    procedure_report_procedure_text,
                                    procedure_report_note
                                    ';

            $procedure_where = array(
                'procedure_report_date' => $requested_data['date'],
                'procedure_report_clinic_id' => $requested_data['clinic_id'],
                'procedure_report_doctor_user_id' => $requested_data['doctor_id'],
                'procedure_report_appointment_id' => $requested_data['appointment_id'],
                'procedure_report_status' => 1,
                'procedure_report_user_id' => $requested_data['patient_id']
            );

            $get_detail = $this->Common_model->get_single_row(TBL_PROCEDURE_REPORTS, $procedure_columns, $procedure_where);
        } else if ($requested_data['key'] == 6) {

            $report_columns = "file_report_id,
                                file_report_user_id,
                                file_report_doctor_user_id,
                                file_report_appointment_id,
                                file_report_clinic_id,
                                file_report_report_type_id,
                                file_report_name,
                                file_report_date,
                                report_type_name,
                                file_report_created_at,
                                file_report_image_url";

            $get_report_query = "   SELECT
                                        " . $report_columns . " 
                                    FROM 
                                        " . TBL_FILE_REPORTS . " 
                                    LEFT JOIN
                                        " . TBL_REPORT_TYPES . " ON file_report_report_type_id = report_type_id
                                    LEFT JOIN
                                         " . TBL_FILE_REPORTS_IMAGES . " ON file_report_image_file_report_id = file_report_id AND file_report_image_status=1 
                                    WHERE
                                        file_report_doctor_user_id = '" . $requested_data['doctor_id'] . "' 
                                    AND
                                        file_report_appointment_id = '" . $requested_data['appointment_id'] . "'
                                    AND
                                        file_report_clinic_id = '" . $requested_data['clinic_id'] . "' 
                                    AND 
                                        file_report_status  = 1
                                    AND    
                                        report_type_id != 11
                                    AND
                                        file_report_user_id = '" . $requested_data['patient_id'] . "' ";

            if ($requested_data['user_type'] == 1) {
                $get_report_query .= " AND file_report_share_status = 1 ";
            }
            
            /*role wise data 3= Receiptionalist Role Id*/
            if ($requested_data['doctor_clinic_mapping_role_id'] == 3) {
                $get_report_query .= " AND file_report_added_by_user_id = '" . $requested_data['user_id'] . "' ";
            }
            
            //$get_report_query .= " GROUP BY file_report_id ";
            $get_detail = $this->get_all_rows_by_query($get_report_query);

            if (!empty($get_detail)) {
                foreach ($get_detail as &$detail) {
                    $detail['is_edit'] = false;
                    if(get_display_date_time("Y-m-d",$detail['file_report_created_at']) == get_display_date_time("Y-m-d")) {
                        $detail['is_edit'] = true;
                    }
                    if (!empty($detail['file_report_image_url'])) {
                        $extension = pathinfo(get_file_full_path($detail['file_report_image_url']), PATHINFO_EXTENSION);
                        if ($extension == 'pdf') {
                            $detail['is_pdf'] = 1;
                        } else {
                            $detail['is_pdf'] = 2;
                        }
                    }
                    $detail['file_report_image_url'] = get_file_full_path($detail['file_report_image_url']);
                }
            }
        } else if ($requested_data['key'] == 7) {

            $health_analytics_columns = 'health_analytics_report_id,
                                    health_analytics_report_user_id,
                                    health_analytics_report_doctor_user_id,
                                    health_analytics_report_appointment_id,
                                    health_analytics_report_clinic_id,
                                    health_analytics_report_date,
                                    health_analytics_report_data
                                    ';

            $health_analytics_where = array(
                'health_analytics_report_date' => $requested_data['date'],
                'health_analytics_report_clinic_id' => $requested_data['clinic_id'],
                'health_analytics_report_doctor_user_id' => $requested_data['doctor_id'],
                'health_analytics_report_appointment_id' => $requested_data['appointment_id'],
                'health_analytics_report_status' => 1,
                'health_analytics_report_user_id' => $requested_data['patient_id']
            );

            $get_detail = $this->Common_model->get_single_row(TBL_HEALTH_ANALYTICS_REPORT, $health_analytics_columns, $health_analytics_where);

            if (!empty($get_detail)) {

                $columns = 'patient_analytics_analytics_id, patient_analytics_name';
                $analytics_where = array(
                    'patient_analytics_user_id' => $requested_data['patient_id'],
                    'patient_analytics_status !=' => 9
                );

                $get_patient_analytics = $this->get_all_rows(TBL_PATIENT_ANALYTICS, $columns, $analytics_where);

                if (!empty($get_patient_analytics)) {

                    $stored_analytics = json_decode($get_detail['health_analytics_report_data'], true);

                    $stored_analytics_id = array_column($stored_analytics, "id");

                    $get_patient_analytics_id = array_column($get_patient_analytics, "patient_analytics_analytics_id");

                    if (count($stored_analytics_id) > count($get_patient_analytics_id)) {
                        $difference_id = array_values(array_diff($stored_analytics_id, $get_patient_analytics_id));
                    } else {
                        $difference_id = array_values(array_diff($get_patient_analytics_id, $stored_analytics_id));
                    }

                    if (!empty($difference_id)) {

                        //get the name of the analytics 
                        $get_analytics_query = "SELECT 
                                                patient_analytics_analytics_id as id, 
                                                patient_analytics_name as name,
                                                patient_analytics_doctor_id as doctor_id,
                                                patient_analytics_name_precise as precise_name,
                                                health_analytics_test_id,
                                                health_analytics_test_validation
                                            FROM
                                                " . TBL_PATIENT_ANALYTICS . " 
                                            LEFT JOIN
                                                " . TBL_HEALTH_ANALYTICS . " ON patient_analytics_analytics_id = health_analytics_test_id
                                            WHERE 
                                                patient_analytics_analytics_id IN (" . implode(',', $difference_id) . ") 
                                            AND
                                                patient_analytics_status != 9 
                                            AND 
                                                patient_analytics_user_id = '" . $requested_data['patient_id'] . "' ";

                        $get_analytics_data = $this->get_all_rows_by_query($get_analytics_query);

                        if (!empty($get_analytics_data)) {
                            $array_decode = json_decode($get_detail['health_analytics_report_data'], true);
                            $merge_array = array_merge($array_decode, $get_analytics_data);

                            foreach ($merge_array as &$data) {
                                $data['health_analytics_test_validation'] = json_decode($data['health_analytics_test_validation'], true);
                            }
                            $health_report_data = json_encode($merge_array);
                            $get_detail['health_analytics_report_data'] = $health_report_data;
                        }
                    } else {

                        $stored_analytics = json_decode($get_detail['health_analytics_report_data'], true);
                        $stored_analytics_id = array_column($stored_analytics, "id");
                        $new_json_data = array();

                        if (!empty($stored_analytics)) {
                            //get the name of the analytics 
                            $get_analytics_query = "SELECT 
                                                    health_analytics_test_id,
                                                    health_analytics_test_validation
                                                FROM
                                                    " . TBL_HEALTH_ANALYTICS . "
                                                WHERE 
                                                    health_analytics_test_id IN (" . implode(',', $stored_analytics_id) . ") 
                                                ";
                            $get_analytics_data = $this->get_all_rows_by_query($get_analytics_query);

                            foreach ($stored_analytics as $single_data) {
                                foreach ($get_analytics_data as $analytics_data) {
                                    if ($analytics_data['health_analytics_test_id'] == $single_data['id']) {
                                        $single_data['health_analytics_test_validation'] = json_decode($analytics_data['health_analytics_test_validation'], true);
                                        $new_json_data[] = $single_data;
                                    }
                                }
                            }

                            if (!empty($new_json_data)) {
                                $get_detail['health_analytics_report_data'] = json_encode($new_json_data);
                            }
                        }
                    }
                }
            } else {

                //get the name of the analytics 
                $get_analytics_query = " SELECT 
                                                patient_analytics_analytics_id as id, 
                                                patient_analytics_name as name,
                                                patient_analytics_doctor_id as doctor_id,
                                                patient_analytics_name_precise as precise_name,
                                                health_analytics_test_id,
                                                health_analytics_test_validation
                                            FROM 
                                                " . TBL_PATIENT_ANALYTICS . " 
                                            LEFT JOIN
                                                " . TBL_HEALTH_ANALYTICS . " ON patient_analytics_analytics_id = health_analytics_test_id        
                                            WHERE 
                                                patient_analytics_user_id  = '" . $requested_data['patient_id'] . "'
                                            AND 
                                                patient_analytics_status != 9 ";

                $get_analytics_data = $this->get_all_rows_by_query($get_analytics_query);
                if (!empty($get_analytics_data)) {
                    foreach ($get_analytics_data as &$data) {
                        $data['health_analytics_test_validation'] = json_decode($data['health_analytics_test_validation'], true);
                    }
                    $json_format = json_encode($get_analytics_data);
                    $get_detail['health_analytics_report_data'] = $json_format;
                }
            }
        }
        return $get_detail;
    }

    /**
     * Description :- This function is used to get the health analytics detail
     * 
     * 
     * 
     * @param type $requested_data
     * @return type
     */
    public function get_health_analytics_detail($requested_data) {
        
        $columns = "health_analytics_report_id,
					health_analytics_report_user_id,
                    health_analytics_report_doctor_user_id,
                    health_analytics_report_appointment_id,
                    health_analytics_report_clinic_id,
                    health_analytics_report_date,
                    health_analytics_report_data,
                    u1.user_first_name as patient_first_name,
                    u1.user_last_name as patient_last_name,
                    CONCAT(u1.user_first_name,' ',u1.user_last_name) as user_patient_name,
                    u.user_first_name,
                    u.user_last_name,
                    u.user_type,
                    CONCAT(u.user_first_name,' ',u.user_last_name) as user_name";

        $get_analytics_query = "SELECT
                                " . $columns . " 
                            FROM 
                                " . TBL_HEALTH_ANALYTICS_REPORT . "         
                            LEFT JOIN 
                                " . TBL_USERS . " as u ON health_analytics_report_doctor_user_id = u.user_id        
                            LEFT JOIN 
                                " . TBL_USERS . " as u1 ON health_analytics_report_user_id = u1.user_id           
                            WHERE
                                health_analytics_report_status != 9 
                            ";
        if (!empty($requested_data['health_analytics_report_id'])) {
            $get_analytics_query .= " AND health_analytics_report_id = '" . $requested_data['health_analytics_report_id'] . "' ";
            $get_analytics_data = $this->get_single_row_by_query($get_analytics_query);
        }
		
        if (!empty($requested_data['user_id'])) {
            $get_analytics_query .= " AND health_analytics_report_user_id = '" . $requested_data['user_id'] . "' ";
            if (!empty($requested_data['flag'] == 2)) {
                $get_analytics_query .= " AND health_analytics_report_appointment_id != '' ";
            }
            if (!empty($requested_data['flag'] == 3)) {
                $get_analytics_query .= " AND (health_analytics_report_appointment_id IS NULL OR health_analytics_report_appointment_id = '' ) ";
            }
            $get_analytics_query .= " AND (u.user_id = '" . $requested_data['logged_in'] . "' OR  ( ";
            if ($requested_data['user_type'] == 2) {
                $get_analytics_query .= " u.user_type = 1 AND ";
            }
            $get_analytics_query .= " u.user_id !=  '" . $requested_data['logged_in'] . "' AND health_analytics_report_share_status = 1)) ";
            
			if (!empty($requested_data['flag']) && $requested_data['flag'] == 1 && isset($requested_data['isPatientPreviousHealthAnalytic']) && isset($requested_data['health_analytics_report_appointment_id'])) {
                $get_analytics_query .= " AND health_analytics_report_appointment_id = ".$requested_data['health_analytics_report_appointment_id']." ";
				$get_analytics_query .= " AND health_analytics_report_date  < '".date('Y-m-d')."' ";
            }
			$get_analytics_query .= "ORDER BY health_analytics_report_date DESC ";
			
			$no_of_records = $this->get_count_by_query($get_analytics_query);
            if (!empty($requested_data['page'])) {
                $get_analytics_query .= " LIMIT " . (($requested_data['page'] - 1) * $requested_data['per_page']) . "," . $requested_data['per_page'] . " ";
            }
			
			$analytics_data = $this->get_all_rows_by_query($get_analytics_query);
            $get_analytics_data = array(
                'no_of_records' => $no_of_records,
                'data' => $analytics_data
            );
        }

        return $get_analytics_data;
    }
	
	public function get_health_analytics_detail_with_group_date($requested_data) {
		/* set group concat max value first only fro current session */
        $columns = "health_analytics_report_id,
					health_analytics_report_user_id,
                    health_analytics_report_doctor_user_id,
                    health_analytics_report_appointment_id,
                    health_analytics_report_clinic_id,
                    health_analytics_report_date,
                    health_analytics_report_data,
                    u1.user_first_name as patient_first_name,
                    u1.user_last_name as patient_last_name,
                    CONCAT(u1.user_first_name,' ',u1.user_last_name) as user_patient_name,
                    u.user_first_name,
                    u.user_last_name,
                    u.user_type,
                    CONCAT(u.user_first_name,' ',u.user_last_name) as user_name";
					
        $get_analytics_query = "SELECT
                                " . $columns . " 
                            FROM 
                                " . TBL_HEALTH_ANALYTICS_REPORT . "         
                            LEFT JOIN 
                                " . TBL_USERS . " as u ON health_analytics_report_doctor_user_id = u.user_id        
                            LEFT JOIN 
                                " . TBL_USERS . " as u1 ON health_analytics_report_user_id = u1.user_id           
                            WHERE
                                health_analytics_report_status != 9 
                            ";
        if (!empty($requested_data['health_analytics_report_id'])) {
            $get_analytics_query .= " AND health_analytics_report_id = '" . $requested_data['health_analytics_report_id'] . "' ";
            $get_analytics_data = $this->get_single_row_by_query($get_analytics_query);
        }
		
        if (!empty($requested_data['user_id'])) {
            $get_analytics_query .= " AND health_analytics_report_user_id = '" . $requested_data['user_id'] . "' ";
            if (!empty($requested_data['flag'] == 2)) {
                $get_analytics_query .= " AND health_analytics_report_appointment_id != '' ";
            }
            if (!empty($requested_data['flag'] == 3)) {
                $get_analytics_query .= " AND (health_analytics_report_appointment_id IS NULL OR health_analytics_report_appointment_id = '' ) ";
            }
            /*$get_analytics_query .= " AND (u.user_id = '" . $requested_data['logged_in'] . "' OR  ( ";
            if ($requested_data['user_type'] == 2) {
                $get_analytics_query .= " u.user_type = 1 AND ";
            }
            $get_analytics_query .= " u.user_id !=  '" . $requested_data['logged_in'] . "' AND health_analytics_report_share_status = 1)) ";*/
            
			if (!empty($requested_data['flag']) && $requested_data['flag'] == 1 && isset($requested_data['isPatientPreviousHealthAnalytic']) && isset($requested_data['health_analytics_report_appointment_id'])) {
                $get_analytics_query .= " AND health_analytics_report_appointment_id = ".$requested_data['health_analytics_report_appointment_id']." ";
				$get_analytics_query .= " AND health_analytics_report_date  < '".date('Y-m-d')."' ";
            }else{
				// $get_analytics_query .= " GROUP BY health_analytics_report_user_id, health_analytics_report_doctor_user_id, health_analytics_report_appointment_id, health_analytics_report_date ";
			}
			$get_analytics_query .= "ORDER BY health_analytics_report_date DESC ";
			
			$no_of_records = $this->get_count_by_query($get_analytics_query);
            if (!empty($requested_data['page'])) {
                $get_analytics_query .= " LIMIT " . (($requested_data['page'] - 1) * $requested_data['per_page']) . "," . $requested_data['per_page'] . " ";
            }
			$analytics_data = $this->get_all_rows_by_query($get_analytics_query);
			$get_analytics_data = array(
                'no_of_records' => $no_of_records,
                'data' => $analytics_data
            );
        }
        return $get_analytics_data;
    }

    /**
     * Description :- This function is used to get the next followup date
     * 
     * 
     * 
     * @param type $requested_data
     * @return type
     */
    public function get_followup_data($requested_data) {


        $get_followup_query = "SELECT
                               follow_up_followup_date,
                               follow_up_instruction
                            FROM 
                                " . TBL_PRESCRIPTION_FOLLOUP . "                                     
                            WHERE
                                follow_up_appointment_id = " . $requested_data['appointment_id'] . " AND 
                                follow_up_status =1 ";

        $get_followup_data = $this->get_single_row_by_query($get_followup_query);
        return $get_followup_data;
    }

    /**
     * Description :- This function is used to get the clinical data of the patient
     * 
     * 
     * 
     * @param type $clinical_data
     * @return type
     */
    public function get_patient_clinical_notes_report($clinical_data) {

        $columns = "
                    clinical_notes_reports_id,
                    clinical_notes_reports_complaints,
                    clinical_notes_reports_observation,
                    clinical_notes_reports_diagnoses,
                    clinical_notes_reports_add_notes,
                    clinical_notes_reports_date,
                    clinical_notes_reports_appointment_id,
                    user_first_name,
                    user_last_name,
                    user_type,
                    CONCAT(user_first_name,' ',user_last_name) as user_name";

        $get_clinic_query = "SELECT
                                " . $columns . " 
                            FROM 
                                " . TBL_CLINICAL_NOTES_REPORT . " 
                            LEFT JOIN 
                                " . TBL_USERS . " ON user_id = clinical_notes_reports_doctor_user_id
                            WHERE
                                clinical_notes_reports_status != 9  ";


        $total_records = 0;

        if (!empty($clinical_data['clinical_report_id'])) {
            $get_clinic_query .= " AND clinical_notes_reports_id = '" . $clinical_data['clinical_report_id'] . "'  ";
        } else {

            if (!empty($clinical_data['user_id'])) {
                $get_clinic_query .= " AND clinical_notes_reports_user_id = '" . $clinical_data['user_id'] . "' ";
            }

            if (!empty($clinical_data['flag'] == 2)) {
                $get_clinic_query .= " AND clinical_notes_reports_appointment_id != '' ";
            }

            if (!empty($clinical_data['flag'] == 3)) {
                $get_clinic_query .= " AND clinical_notes_reports_appointment_id IS NULL ";
            }


            $get_clinic_query .= " AND  (
                                    user_id = '" . $clinical_data['logged_in'] . "' 
                                    OR  
                                        ( ";

            if ($clinical_data['user_type'] == 2) {
                $get_clinic_query .= " user_type = 1 AND ";
            }
            $get_clinic_query .= " user_id !=  '" . $clinical_data['logged_in'] . "' AND clinical_notes_reports_share_status = 1 
                                        )
                                    ) ORDER BY clinical_notes_reports_date DESC  ";


            $total_records = $this->get_count_by_query($get_clinic_query);

            if (!empty($clinical_data['page'])) {
                $get_clinic_query .= " LIMIT " . (($clinical_data['page'] - 1) * $clinical_data['per_page']) . "," . $clinical_data['per_page'] . " ";
            }
        }

        $get_clinic_data = $this->get_all_rows_by_query($get_clinic_query);

        $return_array = array(
            'data' => $get_clinic_data,
            'total_records' => $total_records
        );

        return $return_array;
    }

    public function get_my_procedure_report($where) {
        if(empty($where['doctor_id']))
            return [];
        $this->db->select("procedure_report_procedure_text");
        $this->db->from(TBL_PROCEDURE_REPORTS);
        $this->db->where("procedure_report_status", 1);
        $this->db->where("procedure_report_doctor_user_id", $where['doctor_id']);
        $query = $this->db->get();
        return $query->result();
    }
    /**
     * Description :- This function is used to get the procdure data of the patient
     * 
     * 
     * 
     * @param type $procedure_data
     * @return type
     */
    public function get_patient_procedure_report($procedure_data) {

        $columns = "
                    procedure_report_id,
                    procedure_report_date,
                    procedure_report_procedure_text,
                    procedure_report_note,
                    procedure_report_appointment_id,
                    user_first_name,
                    user_last_name,
                    user_type,
                    CONCAT(user_first_name,' ',user_last_name) as user_name";

        $get_procedure_query = "SELECT
                                " . $columns . " 
                            FROM 
                                " . TBL_PROCEDURE_REPORTS . " 
                            LEFT JOIN 
                                " . TBL_USERS . " ON user_id = procedure_report_doctor_user_id
                            WHERE
                                procedure_report_status != 9  ";


        $total_records = 0;

        if (!empty($procedure_data['procedure_report_id'])) {
            $get_procedure_query .= " AND procedure_report_id = '" . $procedure_data['procedure_report_id'] . "'  ";
        } else {

            if (!empty($procedure_data['user_id'])) {
                $get_procedure_query .= " AND procedure_report_user_id = '" . $procedure_data['user_id'] . "' ";
            }

            if (!empty($procedure_data['flag'] == 2)) {
                $get_procedure_query .= " AND procedure_report_appointment_id != '' ";
            }

            if (!empty($procedure_data['flag'] == 3)) {
                $get_procedure_query .= " AND procedure_report_appointment_id IS NULL ";
            }


            $get_procedure_query .= " AND  (
                                    user_id = '" . $procedure_data['logged_in'] . "' 
                                    OR  
                                        ( ";

            if ($procedure_data['user_type'] == 2) {
                $get_procedure_query .= " user_type = 1 AND ";
            }
            $get_procedure_query .= " user_id !=  '" . $procedure_data['logged_in'] . "' AND procedure_report_share_status = 1 
                                        )
                                    ) ";


            $total_records = $this->get_count_by_query($get_procedure_query);

            if (!empty($procedure_data['page'])) {
                $get_procedure_query .= " LIMIT " . (($procedure_data['page'] - 1) * $procedure_data['per_page']) . "," . $procedure_data['per_page'] . " ";
            }
        }

        $get_procedure_data = $this->get_all_rows_by_query($get_procedure_query);

        $return_array = array(
            'data' => $get_procedure_data,
            'total_records' => $total_records
        );

        return $return_array;
    }

    /**
     * Description :- This function is used to get the procdure data of the patient
     * 
     * 
     * 
     * @param type $lab_report_data
     * @return type
     */
    public function get_patient_lab_report($lab_report_data) {

        $columns = "
                    lab_report_id,
                    lab_report_date,
                    lab_report_test_name,
                    lab_report_instruction,
                    lab_report_appointment_id,
                    user_first_name,
                    user_last_name,
                    user_type,
                    CONCAT(user_first_name,' ',user_last_name) as user_name";

        $get_lab_report_query = "SELECT
                                " . $columns . " 
                            FROM 
                                " . TBL_LAB_REPORTS . " 
                            LEFT JOIN 
                                " . TBL_USERS . " ON user_id = lab_report_doctor_user_id
                            WHERE
                                lab_report_status != 9  ";


        $total_records = 0;

        if (!empty($lab_report_data['lab_report_id'])) {
            $get_lab_report_query .= " AND lab_report_id = '" . $lab_report_data['lab_report_id'] . "'  ";
        } else {

            if (!empty($lab_report_data['user_id'])) {
                $get_lab_report_query .= " AND lab_report_user_id = '" . $lab_report_data['user_id'] . "' ";
            }

            if (!empty($lab_report_data['flag'] == 2)) {
                $get_lab_report_query .= " AND lab_report_appointment_id != '' ";
            }

            if (!empty($lab_report_data['flag'] == 3)) {
                $get_lab_report_query .= " AND lab_report_appointment_id IS NULL ";
            }


            $get_lab_report_query .= " AND  (
                                    user_id = '" . $lab_report_data['logged_in'] . "' 
                                    OR  
                                        ( ";

            if ($lab_report_data['user_type'] == 2) {
                $get_lab_report_query .= " user_type = 1 AND ";
            }
            $get_lab_report_query .= " user_id !=  '" . $lab_report_data['logged_in'] . "' AND lab_report_share_status = 1 
                                        )
                                    ) ORDER BY lab_report_date DESC  ";

            $total_records = $this->get_count_by_query($get_lab_report_query);

            if (!empty($lab_report_data['page'])) {
                $get_lab_report_query .= " LIMIT " . (($lab_report_data['page'] - 1) * $lab_report_data['per_page']) . "," . $lab_report_data['per_page'] . " ";
            }
        }


        $get_lab_report_data = $this->get_all_rows_by_query($get_lab_report_query);

        $return_array = array(
            'data' => $get_lab_report_data,
            'total_records' => $total_records
        );

        return $return_array;
    }

    /**
     * Description :- This function is used to get the prescription of the patient
     * 
     * 
     * 
     * @param type $prescription_data
     * @return type
     */
    public function get_patient_prescription($prescription_data) {

        $columns = "
                    prescription_id,
                    prescription_date,
                    prescription_drug_name,
					prescription_drug_name_with_unit,
                    prescription_drug_id,
                    prescription_appointment_id,
                    user_first_name,
                    user_last_name,
                    user_type,
                    CONCAT(user_first_name,' ',user_last_name) as user_name,
                    prescription_unit_id,
                    IF(prescription_unit_value IS NULL || prescription_unit_value ='', drug_unit_name,prescription_unit_value) as prescription_unit_value,
                    prescription_dosage,
                    prescription_frequency_id,
                    prescription_frequency_value,
                    prescription_frequency_instruction,
                    prescription_intake,
                    prescription_intake_instruction,
                    prescription_duration,
                    prescription_duration_value,
                    prescription_diet_instruction,
                    prescription_next_follow_up,
                    drug_frequency_name,
                    drug_name,
					drug_name_with_unit,
                    drug_unit_name,
                    drug_status,
                    GROUP_CONCAT(DISTINCT(drug_generic_title)) as generic_title,
                    prescription_generic_id
                    ";

        $get_prescription_query = "SELECT
                                        " . $columns . " 
                                    FROM 
                                        " . TBL_PRESCRIPTION_REPORTS . "
                                    LEFT JOIN 
                                        " . TBL_USERS . " ON user_id = prescription_doctor_user_id
                                    LEFT JOIN
                                            " . TBL_DRUGS . " ON prescription_drug_id = drug_id       
                                    LEFT JOIN
                                            " . TBL_DRUG_UNIT . " ON drug_unit_id = drug_drug_unit_id
                                    LEFT JOIN
                                            " . TBL_DRUG_FREQUENCY . " ON prescription_frequency_id = drug_frequency_id         
                                    LEFT JOIN 
                                        " . TBL_DRUG_GENERIC . " ON FIND_IN_SET(drug_generic_id,prescription_generic_id) ";

        $get_prescription_query .= "WHERE prescription_status != 9 ";

        if($prescription_data['flag'] == 1) {
            $get_prescription_query .= " AND prescription_id IN(SELECT MAX(prescription_id) FROM me_prescription_reports WHERE prescription_status != 9 AND prescription_doctor_user_id = ".$prescription_data['logged_in']." GROUP BY prescription_drug_id) ";
        }
        if (!empty($prescription_data['user_id']) && $prescription_data['flag'] == 2) {
            $get_prescription_query .= " AND prescription_user_id = '" . $prescription_data['user_id'] . "' ";
        }
        if ($prescription_data['flag'] == 2 && !empty($prescription_data['date'])) {
            $get_prescription_query .= " AND prescription_date = '" . $prescription_data['date'] . "' ";
        }
		
        $get_prescription_query .= " AND (user_id = '" . $prescription_data['logged_in'] . "' ) ";
        if ($prescription_data['flag'] == 1) {
            $get_prescription_query .= " AND drug_status != 9 ";
            $get_prescription_query.=" GROUP BY prescription_drug_id  ";
        } else if ($prescription_data['flag'] == 2 && empty($prescription_data['date'])) {
            $get_prescription_query.=" GROUP BY prescription_date  ";
        } else if ($prescription_data['flag'] == 2 && !empty($prescription_data['date'])) {
            $get_prescription_query.=" GROUP BY prescription_drug_id  ";
        }

        if ($prescription_data['flag'] == 1) {
            $get_prescription_query .= " HAVING COUNT(prescription_drug_id) >= 1 ORDER BY COUNT(prescription_drug_id) desc ";
        } else if ($prescription_data['flag'] == 2) {
            $get_prescription_query .=" ORDER BY prescription_date DESC ";
        }
		$total_records = $this->get_count_by_query($get_prescription_query);
		if (!empty($prescription_data['page']) && $prescription_data['flag'] == 2 && empty($prescription_data['is_all_data'])) {
            $get_prescription_query .= " LIMIT " . (($prescription_data['page'] - 1) * $prescription_data['per_page']) . "," . $prescription_data['per_page'] . " ";
        }

		//echo $get_prescription_query;exit;
        $get_lab_report_data = $this->get_all_rows_by_query($get_prescription_query);
        $return_array = array(
            'data' => $get_lab_report_data,
            'total_records' => $total_records
        );
        return $return_array;
    }

    /**
     * 
     * @param tiny $type 1=Verify 2=resend
     * @param bigint $auth_id
     * @param integer $new_count
     */
    public function increase_opt_auth_limit($type, $auth_id, $new_count) {
        $auth_data_update_where = array(
            "auth_id" => $auth_id
        );
        if ($type == 1) {
            $auth_data_update = array(
                "auth_attempt_count" => $new_count
            );
        } else {
            $auth_data_update = array(
                "auth_resend_count" => $new_count
            );
        }

        $this->update(TBL_USER_AUTH, $auth_data_update, $auth_data_update_where);
    }

    /**
     * 
     * @param tiny $type 1=Verify 2=resend
     * @param bigint $user_id
     * @param integer $new_count
     */
    public function increase_opt_temp_user_limit($type, $user_id, $new_count) {
        $auth_data_update_where = array(
            "temp_user_id" => $user_id
        );
        if ($type == 1) {
            $auth_data_update = array(
                "temp_auth_attempt_count" => $new_count
            );
        } else {
            $auth_data_update = array(
                "temp_auth_resend_count" => $new_count
            );
        }
        $this->update(TBL_USER_TEMP, $auth_data_update, $auth_data_update_where);
    }

    /**
     * Description :- This function is used to delete the all details of the user
     * 
     * 
     * 
     * @param type $user_id
     * @return boolean
     */
    public function delete_all_details_user($user_id) {

        $this->db->trans_start();

        //delete the appointment also
        $appointment_where = array(
            'appointment_user_id' => $user_id,
        );
        $appointment_update = array(
            'appointment_status' => 9,
            'appointment_updated_at' => $this->utc_time_formated
        );
        $this->Common_model->update(TBL_APPOINTMENTS, $appointment_update, $appointment_where);

        //delete the reminder also
        $reminder_where = array(
            'reminder_user_id' => $user_id
        );
        $reminder_update = array(
            'reminder_modified_at' => $this->utc_time_formated,
            'reminder_status' => 9
        );
        $this->Common_model->update(TBL_REMINDERS, $reminder_update, $reminder_where);

        //delete user details
        $user_detail_where = array(
            'user_details_user_id' => $user_id
        );
        $user_detail_update = array(
            'user_details_modifed_at' => $this->utc_time_formated,
            'user_details_status' => 9
        );
        $this->Common_model->update(TBL_USER_DETAILS, $user_detail_update, $user_detail_where);

        //delete user address
        $address_where = array(
            'address_user_id' => $user_id,
            'address_type' => 1
        );
        $address_update = array(
            'address_modified_at' => $this->utc_time_formated,
            'address_status' => 9
        );
        $this->Common_model->update(TBL_ADDRESS, $address_update, $address_where);

        //delete the vitals
        $vital_where = array(
            'vital_report_user_id' => $user_id
        );
        $vital_update = array(
            'vital_report_updated_at' => $this->utc_time_formated,
            'vital_report_status' => 9
        );
        $this->Common_model->update(TBL_VITAL_REPORTS, $vital_update, $vital_where);

        //delete clinical notes
        $clinical_where = array(
            'clinical_notes_reports_user_id' => $user_id
        );
        $clinical_update = array(
            'clinical_notes_reports_updated_at' => $this->utc_time_formated,
            'clinical_notes_reports_status' => 9
        );
        $this->Common_model->update(TBL_CLINICAL_NOTES_REPORT, $clinical_update, $clinical_where);

        //delete the prescription added by the patient
        $patient_pres_where = array(
            'patient_prescription_user_id' => $user_id
        );
        $patient_pres_update = array(
            'patient_prescription_modified_at' => $this->utc_time_formated,
            'patient_prescription_status' => 9
        );
        $this->Common_model->update(TBL_PATIENT_PRESCRIPTION, $patient_pres_update, $patient_pres_where);

        //delete the prescription added by the doctor for that patient
        $pres_where = array(
            'prescription_user_id' => $user_id,
        );
        $pres_update = array(
            'prescription_updated_at' => $this->utc_time_formated,
            'prescription_status' => 9
        );
        $this->Common_model->update(TBL_PRESCRIPTION_REPORTS, $pres_where, $pres_update);

        //delete the lab reports
        $investigation_where = array(
            'lab_report_user_id' => $user_id
        );
        $investigation_update = array(
            'lab_report_updated_at' => $this->utc_time_formated,
            'lab_report_status' => 9
        );
        $this->Common_model->update(TBL_LAB_REPORTS, $investigation_update, $investigation_where);

        //delete the procedure
        $proc_where = array(
            'procedure_report_user_id' => $user_id
        );
        $proc_update = array(
            'procedure_report_updated_at' => $this->utc_time_formated,
            'procedure_report_status' => 9
        );
        $this->Common_model->update(TBL_PROCEDURE_REPORTS, $proc_update, $proc_where);

        //delete the report
        $report_where = array(
            'file_report_user_id' => $user_id
        );
        $report_update = array(
            'file_report_updated_at' => $this->utc_time_formated,
            'file_report_status' => 9
        );
        $this->Common_model->update(TBL_FILE_REPORTS, $report_update, $report_where);

        //delete patient analytics
        $analy_where = array(
            'patient_analytics_user_id' => $user_id,
        );
        $analy_update = array(
            'patient_analytics_updated_at' => $this->utc_time_formated,
            'patient_analytics_status' => 9
        );
        $this->Common_model->update(TBL_PATIENT_ANALYTICS, $analy_update, $analy_where);

        //delete the analytics report
        $health_analy_where = array(
            'health_analytics_report_user_id' => $user_id
        );
        $health_analy_update = array(
            'health_analytics_report_updated_at' => $this->utc_time_formated,
            'health_analytics_report_status' => 9
        );
        $this->Common_model->update(TBL_HEALTH_ANALYTICS_REPORT, $health_analy_update, $health_analy_where);

        if ($this->db->trans_status() !== FALSE) {
            $this->db->trans_commit();
        } else {
            $this->db->trans_rollback();
            return false;
        }

        return true;
    }
    
    /**
     * Description :- This function is used to update the user last login details of the user.
     * 
     * @author Kanaiya Makwana
     * 
     * Modified Date :- 2018-11-02
     * 
     * @param type $user_update_data
     * @param type $user_update_where
     * @return int
     */
    public function update_user_data($user_update_data = array(), $user_update_where = array()) {
        if (!empty($user_update_data) && !empty($user_update_where)) {
            $check_user_entry = $this->get_single_row($this->users_table, "user_id", $user_update_where);
            if (!empty($check_user_entry)) {
                $is_update = $this->update($this->users_table, $user_update_data, $user_update_where);
            }
            return $is_update;
        }
        return 0;
    }
	
	/**
     * Description :- This function is used to get the user details by user ids
	 * @author MedEasy
     * @param type $ids = array of user ids
     * @return type = array user details where key is user id 
     */
    public function get_details_by_ids($ids = array()) {
		$result = array();
		if (!empty($ids)) {
			$where = array (
                "user_status !=" => 9
            );
			$this->db->where_in("user_id", array_unique($ids));
            $result = $this->get_all_rows($this->users_table, $this->user_columns, $where);
        }
		return $result;
    }

    /**
     * Description :- This function is used to get the user addess by user id
     * @param int $id
     * @return array $result  
     */
    public function get_user_address_by_id($id, $columns = '*') {
        $result = array();
        if (!empty($id)) {
            $where = array(
                "address_user_id" => $id,
                "address_status !=" => 9
            );
            $result = $this->get_single_row($this->address_table, $columns, $where);
        }
        return $result;
    }

    /**
     * Description :- This function is used to get the user addess by user id
     * @param int $id
     * @return array $result  
     */
    public function get_doctor_details_by_id($id, $columns = '*') {
        $result = array();
        if (!empty($id)) {
            $where = array(
                "doctor_detail_doctor_id" => $id,
                "doctor_detail_status !=" => 9
            );
            $result = $this->get_single_row(TBL_DOCTOR_DETAILS, $columns, $where);
        }
        return $result;
    }

    /**
     * Description :- This function is used the details with clinic details.
     * 
     * 
     * @param array $where
     * @param columns $columns
     * @return array
     */
    public function get_doctor_receptionist($doctor, $columns = '*') {
        $result = array();
        $this->db->select($columns)->from($this->users_table . ' u');
        $this->db->join(TBL_DOCTOR_CLINIC_MAPPING . ' c', 'c.doctor_clinic_mapping_user_id=u.user_id AND c.doctor_clinic_mapping_status=1');
        $this->db->where_in('c.doctor_clinic_mapping_role_id', [2,3]);
        $this->db->where('c.doctor_clinic_mapping_doctor_id', $doctor);
        $this->db->group_by('u.user_id');

        $query = $this->db->get();
        if($query->num_rows() > 0) {
            return $query->result_array();
        }
        return $result;
    }

    public function get_linked_family_members($user_id, $columns) {
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

    public function get_patient_details_by_id($id = NULL) {
        $result = array();
        if (!empty($id)) {
            $where = array(
                "user_id" => $id,
                "user_status !=" => 9
            );
            $join_array = array(
                TBL_ADDRESS => 'user_id = address_user_id',
                TBL_USER_DETAILS => 'user_id = user_details_user_id',
                TBL_STATES => 'address_state_id = state_id',
                TBL_CITIES => 'address_city_id = city_id',
                TBL_COUNTRIES => 'address_country_id = country_id',
                TBL_REFER => 'refer_user_id = user_id'
            );
            $columns = array(
                'user_id',
                'user_patient_id',
                'user_first_name',
                'user_last_name',
                'user_email',
                'user_phone_number',
                'user_phone_verified',
                'user_email_verified',
                'user_details_languages_known',
                'user_status',
                'user_unique_id',
                'user_photo_filepath',
                'user_gender',
                'user_caregiver_id',
                'user_language_id',
                'address_name',
                'address_name_one',
                'address_city_id',
                'city_name as address_city_name',
                'address_state_id',
                'state_name as address_state_name',
                'address_country_id',
                'address_pincode',
                'address_latitude',
                'address_longitude',
                'address_locality',
                'user_details_height',
                'user_details_weight',
                'user_details_dob',
                'user_details_blood_group',
                'user_details_food_allergies',
                'user_details_medicine_allergies',
                'user_details_other_allergies',
                'user_details_chronic_diseases',
                'user_details_injuries',
                'user_details_surgeries',
                'user_details_smoking_habbit',
                'user_details_alcohol',
                'user_details_activity_level',
                'user_details_activity_days',
                'user_details_activity_hours',
                'user_details_food_preference',
                'user_details_occupation',
                'user_details_id_proof_type',
                'user_details_id_proof_detail',
                'user_details_id_proof_image',
                'user_details_marital_status',
                'user_details_emergency_contact_person',
                'user_details_emergency_contact_number',
                'user_type',
                'refer_doctor_name',
                'refer_user_id',
                'refer_other_doctor_id',
                'user_details_agree_medical_share'
            );
            $result = $this->get_single_row($this->users_table, $columns, $where, $join_array, 'LEFT');
        }
        return $result;
    }
}