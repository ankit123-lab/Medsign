<?php

class Clinic_model extends MY_Model {

    protected $users_table;
    protected $clinic_columns;

    function __construct() {
        parent::__construct();
        $this->users_table = TBL_USERS;
        $this->address_table = TBL_ADDRESS;
        $this->clinic_table = TBL_CLINICS;
        $this->address_table = TBL_ADDRESS;
        $this->clinic_columns = array(
            'clinic_id',
            "clinic_name",
            "clinic_contact_number",
            "clinic_email",
            "clinic_phone_number"
        );
    }

    /**
     * Description :- This function is used to add the clinic detail
     * 
     * @param type $clinic_array
     * @return type3
     */
    public function add_clinic($clinic_array) {
        return $this->insert(TBL_CLINICS, $clinic_array);
    }

    /**
     * Description :- This function is used to update the clinic detail
     * 
     * @param type $update_array
     * @param type $update_where
     * @return int
     */
    public function update_clinic($update_array, $update_where) {
        if (!empty($update_array) && !empty($update_where)) {
            return $this->update(TBL_CLINICS, $update_array, $update_where);
        }
        return 0;
    }

    /**
     * Description :- This function is used to add the clinic address
     * 
     * @param type $clinic_address_array
     * @return type
     */
    public function add_clinic_address($clinic_address_array) {
        return $this->insert($this->address_table, $clinic_address_array);
    }

    /**
     * Description :- This function is used to update the clinic address
     * 
     * @param type $update_clinic_address_array
     * @param type $update_clinic_address_where
     * @return int
     */
    public function update_clinic_address($update_clinic_address_array, $update_clinic_address_where) {

        if (!empty($update_clinic_address_array) && !empty($update_clinic_address_where)) {
            return $this->update($this->address_table, $update_clinic_address_array, $update_clinic_address_where);
        }
        return 0;
    }

    /**
     * Description :- THis function is used to get the clinic detail based on the clinic id
     * 
     * @param type $clinic_id
     * @return type
     */
    public function get_clinic_detail($clinic_id) {

        $join_array = array(
            $this->address_table => "address_user_id =clinic_id"
        );
        $where = array('clinic_id' => $clinic_id);

        $column = 'clinic_id,
                   clinic_name,
                   clinic_description,
                   clinic_contact_number,
                   clinic_email,
                   clinic_website,
                   clinic_services,
                   clinic_specialization_id,
                   clinic_gstin,
                   clinic_image,
                   clinic_filepath,
                   clinic_session1_start_time,
                   clinic_session1_end_time,
                   clinic_session2_start_time,
                   clinic_session2_end_time,
                   clinic_availability_type,
                   clinic_phone_verified,
                   clinic_email_verified,
                   address_name,
                   address_name_one,
                   address_city_id,
                   address_state_id,
                   address_country_id,
                   address_pincode,
                   address_latitude,
                   address_longitude,
                   address_locality';

        $data = $this->get_single_row($this->clinic_table, $column, $where, $join_array, 'LEFT');

        return $data;
    }

    /**
     * Description :- This function is used to get all the clinics of the doctor
     * 
     * 
     * @param type $doctor_id
     * @return type
     */
    public function get_doctor_clinics($doctor_id) {

        $clinic_query = "
           SELECT
                clinic_id,
                clinic_name,
                clinic_contact_number,
                clinic_email,
                address_name_one,
                address_name,
                address_city_id,
                address_state_id,
                address_country_id,
                address_pincode,
                address_latitude,
                address_longitude,
                address_locality,
                doctor_clinic_mapping_duration,
                doctor_clinic_doctor_session_1_start_time,
                doctor_clinic_doctor_session_1_end_time,
                doctor_clinic_doctor_session_2_start_time,
                doctor_clinic_doctor_session_2_end_time
            FROM 
                " . TBL_CLINICS . " 
            LEFT JOIN 
                " . TBL_ADDRESS . " 
            ON 
                address_user_id=clinic_id AND 
                address_type=2 AND 
                address_status=1
            LEFT JOIN 
                " . TBL_DOCTOR_CLINIC_MAPPING . " 
            ON 
                doctor_clinic_mapping_clinic_id=clinic_id AND 
                doctor_clinic_mapping_user_id=" . $doctor_id . " AND 
                doctor_clinic_mapping_status=1
            WHERE
                clinic_status=1   
            AND 
                clinic_created_by=" . $doctor_id . "              
                    
           ";
        $clinic_data = $this->get_all_rows_by_query($clinic_query);
        return $clinic_data;
    }

    /**
     * Description :- THis function is used to get the detail of the clinic based 
     * on the clinic id and doctor id
     * 
     * 
     * @param type $clinic_id
     * @param type $doctor_id
     * @return type
     */
    public function get_clinic_whole_detail($clinic_id, $doctor_id) {
        $clinic_query = "
            
            SELECT
                clinic_id,
                clinic_name,
                clinic_contact_number,
                clinic_email,
                
                address_id,
                address_name,
                address_name_one,
                address_city_id,
                address_state_id,
                address_country_id,
                address_pincode,
                address_latitude,
                address_longitude,
                address_locality,
                doctor_clinic_mapping_duration,
                doctor_clinic_mapping_fees,
                clinic_filepath,
                clinic_services,
                clinic_phone_verified,
                clinic_email_verified,
                doctor_clinic_mapping_fees,
                doctor_clinic_doctor_session_1_start_time,
                doctor_clinic_doctor_session_1_end_time,
                doctor_clinic_doctor_session_2_start_time,
                doctor_clinic_doctor_session_2_end_time
            FROM 
                " . TBL_CLINICS . " 
            LEFT JOIN 
                " . TBL_ADDRESS . " 
            ON 
                address_user_id=clinic_id AND 
                address_type=2 AND 
                address_status=1
            LEFT JOIN 
                " . TBL_DOCTOR_CLINIC_MAPPING . " 
            ON 
                doctor_clinic_mapping_clinic_id=clinic_id AND 
                doctor_clinic_mapping_status=1 AND 
                doctor_clinic_mapping_user_id=" . $doctor_id . " 
            WHERE
                clinic_status=1 
            AND 
                clinic_id=" . $clinic_id . " 
           ";
        $clinic_data = $this->get_single_row_by_query($clinic_query);
        return $clinic_data;
    }

    /**
     * Description :- This function is used to get the all images of the clinic
     * 
     * @author Manish Ramnani
     * 
     * @param type $clinic_id
     * @return type
     */
    public function get_clinic_images($clinic_id) {
        $clinic_query = "
           
            SELECT
                clinic_photo_id,
                clinic_photo_filepath,
                clinic_photo_type
            FROM 
                " . TBL_CLINIC_IMAGES . "             
            WHERE
                clinic_photo_status=1 
            AND 
                clinic_photo_clinic_id=" . $clinic_id . " 
                    
           ";
        $clinic_img_data = $this->get_all_rows_by_query($clinic_query);
        return $clinic_img_data;
    }

    /**
     * Description :- This function is used to update the image of the clinic
     * 
     * @param type $update_image_array
     * @param type $update_image_where
     * @return int
     */
    public function update_clinic_image($update_image_array, $update_image_where) {
        if (!empty($update_image_array) && $update_image_where) {
            return $this->update(TBL_CLINIC_IMAGES, $update_image_array, $update_image_where);
        }
        return 0;
    }

    /**
     * Description :- This function is used to get the staff details based on the clinic id
     * 
     * @author Manish Ramnani
     * 
     * @param type $request_data
     * @return type
     */
    public function get_staff_details($request_data) {

        $columns = "doctor_clinic_mapping_user_id as user_id,
                    doctor_clinic_mapping_clinic_id as clinic_id,  
                    doctor_clinic_mapping_doctor_id as doctor_id,
                    user_first_name, 
                    user_last_name, 
                    doctor_clinic_mapping_role_id as user_role,
                    user_status,
                    user_photo_filepath,
                    user_email,
                    user_phone_number,
                user_unique_id,
                address_id,
                address_user_id,
                address_name,
                address_name_one,
                address_city_id,
                address_state_id,
                address_country_id,
                address_pincode,
                address_latitude,
                address_longitude,
                address_locality"
        ;

        $where = array(
            'doctor_clinic_mapping_clinic_id' => $request_data['clinic_id'],
            'user_status !=' => 9
        );

        $join_array = array(
            TBL_USERS => 'doctor_clinic_mapping_user_id = user_id',
            TBL_ADDRESS => 'address_user_id = user_id AND address_type = 1'
        );

        $get_staff = $this->get_all_rows(TBL_DOCTOR_CLINIC_MAPPING, $columns, $where, $join_array, array(), '', 'LEFT');

        return $get_staff;
    }

    /**
     * Description :- This function is used to get the list of the clinic
     * 
     * 
     * @param type $doctor_id
     * @return type
     */
    public function get_clinic_list($doctor_id) {
        
        $clinic_query = "
                        SELECT
                            clinic_id,
                            clinic_name,
                            B.doctor_clinic_mapping_duration,
                            B.doctor_clinic_doctor_session_1_start_time,
                            B.doctor_clinic_doctor_session_1_end_time,
                            B.doctor_clinic_doctor_session_2_start_time,
                            B.doctor_clinic_doctor_session_2_end_time
                        FROM 
                            " . TBL_CLINICS . " 
                        JOIN 
                            " . TBL_DOCTOR_CLINIC_MAPPING . " A
                        ON 
                            A.doctor_clinic_mapping_clinic_id=clinic_id AND 
                            A.doctor_clinic_mapping_user_id=" . $doctor_id . " AND 
                            A.doctor_clinic_mapping_status=1
                        LEFT JOIN 
                            " . TBL_DOCTOR_CLINIC_MAPPING . " B
                        ON 
                            B.doctor_clinic_mapping_clinic_id=clinic_id AND 
                            B.doctor_clinic_mapping_doctor_id IS NULL AND 
                            B.doctor_clinic_mapping_status=1 
                        WHERE
                            clinic_status=1";
                
        $clinic_data = $this->get_all_rows_by_query($clinic_query);

        return $clinic_data;
    }

    /**
     * get staff clinic staff detail by params
     * @param Array $params
     * @author Mehul Jethloja 
     */
    public function getStaffWholeDetail($params) {
        if ($params) {
            $this->db->select(array(
                'user_id',
                'user_first_name',
                'user_last_name',
                'user_email',
                'user_phone_number',
                'user_phone_verified',
                'user_email_verified',
                'user_password',
                'user_status',
                'user_gender',
                'user_photo_filepath',
                'user_unique_id',
                'address_id',
                'address_user_id',
                'address_name',
                'address_name_one',
                'address_city_id',
                'address_state_id',
                'address_country_id',
                'address_pincode',
                'address_latitude',
                'address_longitude',
                'address_locality',
            ));

            $this->db->from(TBL_USERS);
            $this->db->join(TBL_ADDRESS, 'address_user_id = user_id AND address_type = 1', 'left');
            if (isset($params['user_id']) && !empty($params['user_id'])) {
                $this->db->where('user_id', $params['user_id']);
            }
            return $this->db->get()->row();
        }
    }

    public function update2FASetting($user_id,$data){
        $where_array = array(
            'setting_user_id' => $user_id,
            'setting_type' => 2,
            'setting_status' => 1
        );
        /* $where_array = array(
            'setting_user_id' => $get_staff_details->user_id,
            'setting_type' => 2,
            'setting_status' => 1
        ); */
        
        //[{"id":1,"name":"2 Factor authentication","status":$scope.reception.tfa}]
        $get_setting_data = $this->Common_model->get_single_row(TBL_SETTING, 'setting_id,setting_data', $where_array);
        if (!empty($get_setting_data)) {
            $update_setting_data = array(
                'setting_data' => json_encode($data),
                'setting_updated_at' => $this->utc_time_formated
            );

            $update_setting_where = array(
                'setting_id' => $get_setting_data['setting_id']
            );
            $is_update = $this->Common_model->update(TBL_SETTING, $update_setting_data, $update_setting_where);
            
        }else{
            $insert_setting_array = array(
                'setting_user_id' => $user_id,
                'setting_clinic_id' => 0,
                'setting_data' => json_encode($data),
                'setting_type' => 2,
                'setting_data_type' => 1,
                'setting_created_at' => $this->utc_time_formated
            );

            $is_update = $this->Common_model->insert(TBL_SETTING, $insert_setting_array);
        }        
        return $is_update;
    }
}
