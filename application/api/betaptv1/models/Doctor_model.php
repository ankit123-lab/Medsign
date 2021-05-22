<?php

class Doctor_model extends MY_Model {

    protected $users_table;

    function __construct() {
        parent::__construct();
        $this->users_table = TBL_USERS;
        $this->address_table = TBL_ADDRESS;
        $this->doctor_details_table = TBL_DOCTOR_DETAILS;
    }

    public function get_doctor_list($recieve_data) {

        $columns = 'd.user_id,
                    d.user_first_name,
                    d.user_last_name,
                    d.user_email,
                    doctor_detail_color_code,
                    doctor_clinic_mapping_role_id,
                    IFNULL(
                        SUM(
                            CASE 
                                WHEN
                                    IF(appointment_status != 9  AND appointment_date = curdate(),1,0 AND p.user_status = 1)
                                THEN
                                    1
                                ELSE
                                    0
                            END
                        ),0
                    ) as total_appointment ';

        $get_doctor_list_sql = "SELECT 
                                    " . $columns . " 
                                FROM 
                                    " . TBL_USERS . " as d
                                LEFT JOIN
                                    " . TBL_DOCTOR_DETAILS . "
                                ON
                                    d.user_id = doctor_detail_doctor_id AND 
                                    doctor_detail_status = 1
                                LEFT JOIN
                                    " . TBL_DOCTOR_CLINIC_MAPPING . "
                                ON
                                    d.user_id = doctor_clinic_mapping_user_id
                                LEFT JOIN
                                    " . TBL_APPOINTMENTS . " 
                                ON 
                                    d.user_id = appointment_doctor_user_id AND appointment_status = 1 
                                LEFT JOIN
                                    " . TBL_USERS . " as p ON p.user_id = appointment_user_id AND p.user_status = 1
                                WHERE 
                                    d.user_type = 2
                                AND
                                    d.user_status = 1 
                                AND
                                    doctor_clinic_mapping_clinic_id = '" . $recieve_data['clinic_id'] . "'
                                AND
                                    doctor_clinic_mapping_status = 1 
                                AND
                                    doctor_clinic_mapping_role_id = 1 
                                GROUP BY user_id ";

        
        $get_doctor_list = $this->get_all_rows_by_query($get_doctor_list_sql);

        return $get_doctor_list;
    }

    /**
     * Description :- This function is used to update the doctor details
     * 
     * 
     * @param type $user_id
     * @param type $update_user_details
     * @return type
     */
    public function update_doctor_details($user_id, $update_user_details) {
        $user_details_is_update = 0;

        //check user details is exist or not
        $get_user_details = $this->get_single_row($this->doctor_details_table, 'doctor_detail_id', array('doctor_detail_doctor_id' => $user_id));

        if (!empty($get_user_details)) {
            $update_user_details['doctor_detail_modified_at'] = $this->utc_time_formated;
            $user_details_is_update = $this->update($this->doctor_details_table, $update_user_details, array('doctor_detail_doctor_id' => $user_id));
        } else {
            $update_user_details['doctor_detail_created_at'] = $this->utc_time_formated;
            $update_user_details['doctor_detail_color_code'] = random_color();
            $update_user_details['doctor_detail_doctor_id'] = $user_id;
            $user_details_is_update = $this->insert($this->doctor_details_table, $update_user_details);
        }

        return $user_details_is_update;
    }

    /**
     * Description :- This function is used to add the specialization of the doctor
     * 
     * @param type $user_id
     * @param type $specialization_id
     * @param type $files
     */
    public function add_specialization($user_id, $specialization_id, $files) {

        //update first all specialization entry to 9
        $doctor_where = array(
            "doctor_specialization_doctor_id" => $user_id,
            "doctor_specialization_status" => 1
        );
        $update_array = array(
            'doctor_specialization_status' => 9,
            "doctor_specialization_updated_at" => $this->utc_time_formated
        );
        $this->update(TBL_DOCTOR_SPECIALIZATIONS, $update_array, $doctor_where);

        //insert new data
        $specialization_data = explode(",", $specialization_id);
        $insert_array = array();
        //upload files
        $upload_path = UPLOAD_REL_PATH . "/" . DOCTOR_SPECIALIZATION_FOLDER . "/" . $user_id;
        $upload_folder = DOCTOR_SPECIALIZATION_FOLDER . "/" . $user_id;
        $profile_image_name = do_upload_multiple2($upload_path, array('photo' => $files['specialization_images']), $upload_folder);


        foreach ($specialization_data as $key => $value) {

            $insert_array[] = array(
                "doctor_specialization_doctor_id" => $user_id,
                "doctor_specialization_specialization_id" => $value,
                "doctor_specialization_image" => $profile_image_name[$key],
                "doctor_specialization_image_full_path" => IMAGE_MANIPULATION_URL . DOCTOR_SPECIALIZATION_FOLDER . "/" . $user_id . "/" . $profile_image_name[$key],
                "doctor_specialization_created_at" => $this->utc_time_formated,
                "doctor_specialization_status" => 1
            );
        }

        $this->insert_multiple(TBL_DOCTOR_SPECIALIZATIONS, $insert_array);
    }

    /**
     * Description :- This function is used to add the education details of the doctor
     * 
     * @param type $user_id
     * @param type $educations_json
     * @param type $files
     */
    public function add_educations_details($user_id, $educations_json, $files) {


        //update first all specialization entry to 9
        $educations_array = json_decode($educations_json, true);

        $doctor_where = array(
            "doctor_qualification_user_id" => $user_id,
            "doctor_qualification_status" => 1
        );
        $update_array = array(
            'doctor_qualification_status' => 9,
            "doctor_qualification_modified_at" => $this->utc_time_formated
        );
        $this->update(TBL_DOCTOR_EDUCATIONS, $update_array, $doctor_where);

        //upload files
        $upload_path = UPLOAD_REL_PATH . "/" . DOCTOR_EDUCATIONS_FOLDER . "/" . $user_id;
        $upload_folder = DOCTOR_EDUCATIONS_FOLDER . "/" . $user_id;
        $profile_image_name = do_upload_multiple2($upload_path, array('photo' => $files['education_images']), $upload_folder);


        $insert_array = array();
        foreach ($educations_array as $key => $value) {
            $insert_array[] = array(
                "doctor_qualification_user_id" => $user_id,
                "doctor_qualification_degree" => $value['edu_degree'],
                "doctor_qualification_college" => $value['edu_college'],
                "doctor_qualification_completion_year" => $value['edu_year_temp'],
                "doctor_qualification_image" => $profile_image_name[$key],
                "doctor_qualification_image_full_path" => IMAGE_MANIPULATION_URL . DOCTOR_EDUCATIONS_FOLDER . "/" . $user_id . "/" . $profile_image_name [$key],
                "doctor_qualification_created_at" => $this->utc_time_formated
            );
        }

        $this->insert_multiple(TBL_DOCTOR_EDUCATIONS, $insert_array);
    }

    /**
     * Description :- This function is used to add the registration details of the doctor
     * 
     * @param type $user_id
     * @param type $registration_json
     * @param type $files
     */
    public function add_registrations_details($user_id, $registration_json, $files) {


        //update first all specialization entry to 9
        $registration_array = json_decode($registration_json, true);

        $doctor_where = array(
            "doctor_registration_user_id" => $user_id,
            "doctor_registration_status" => 1
        );
        $update_array = array(
            'doctor_registration_status' => 9,
            "doctor_registration_modified_at" => $this->utc_time_formated
        );
        $this->update(TBL_DOCTOR_REGISTRATIONS, $update_array, $doctor_where);

        //upload files
        $upload_path = UPLOAD_REL_PATH . "/" . DOCTOR_REGISTRATIONS_FOLDER . "/" . $user_id;
        $upload_folder = DOCTOR_REGISTRATIONS_FOLDER . "/" . $user_id;
        $file_names = do_upload_multiple2($upload_path, array('photo' => $files['registration_images']), $upload_folder);

        $insert_array = array();
        foreach ($registration_array as $key => $value) {
            $insert_array[] = array(
                "doctor_registration_user_id" => $user_id,
                "doctor_registration_council_id" => $value['reg_councel'],
                "doctor_council_registration_number" => $value['reg_detail'],
                "doctor_registration_year" => $value['reg_year_temp'],
                "doctor_registration_image" => $file_names[$key],
                "doctor_registration_image_filepath" => IMAGE_MANIPULATION_URL . DOCTOR_REGISTRATIONS_FOLDER . "/" . $user_id . "/" . $file_names[$key],
                "doctor_registration_created_at" => $this->utc_time_formated
            );
        }

        $this->insert_multiple(TBL_DOCTOR_REGISTRATIONS, $insert_array);
    }

    /**
     * Description :- This function is used to add or update the clinic detail
     * 
     * 
     * @param type $clinic_array
     * @param type $user_id
     * @return type
     */
    public function add_clinic($clinic_array, $user_id) {
        $is_added = 0;
        if (!empty($clinic_array['clinic_id'])) {
            $update_array = $clinic_array;
            $where = array(
                "clinic_id" => $clinic_array['clinic_id'],
                "clinic_created_by" => $user_id,
                "clinic_status" => 1
            );
            unset($update_array['clinic_id']);
            $is_added = $this->update(TBL_CLINICS, $update_array, $where);
        } else {
            $insert_array = $clinic_array;
            unset($insert_array['clinic_id']);
            $is_added = $this->insert(TBL_CLINICS, $insert_array);
        }
        return $is_added;
    }

    /**
     * Description :- This function is used to get the listing of the doctor 
     * based on the different filterations.
     * 
     * @param type $user_id
     * @param type $filter_array
     * @param type $is_for_total_count
     * @return type
     */
    public function get_doctor_search_list($user_id, $filter_array = array(), $is_for_total_count = false) {

        $most_visted_count = '';
        if (in_array(1, $filter_array['filter_option'])) {
            $most_visted_count = " 
                ,(
                    SELECT  
                        count(*)
                    FROM 
                        " . TBL_APPOINTMENTS . "
                    WHERE                        
                        appointment_doctor_user_id=user_id AND
                        appointment_status=1
                  ) as visted_count
                ";
        }
        $is_available_today = "";
        if (in_array(2, $filter_array['filter_option'])) {
            $is_available_today = "
                ,
                    IFNULL(
                            (
                                SELECT 
                                    calender_block_id
                                FROM 
                                    " . TBL_DOCTOR_CALENDER_BLOCK . "
                                WHERE
                                        calender_block_status=1 AND 
                                        calender_block_user_id=user_id AND 
                                        (
                                            (
                                                calender_block_duration_type = 1 AND
                                                calender_block_from_date = '" . date('Y-m-d') . "'
                                            ) 
                                            OR
                                            (
                                                    calender_block_duration_type = 2 
                                                AND
                                                    calender_block_from_date = '" . date('Y-m-d') . "' 
                                                AND
                                                (    
                                                    (
                                                        doctor_clinic_doctor_session_1_start_time <= calender_block_start_time AND 
                                                        doctor_clinic_doctor_session_1_end_time >= calender_block_start_time
                                                    )  
                                                    OR
                                                    (
                                                        doctor_clinic_doctor_session_1_start_time <= calender_block_end_time AND 
                                                        doctor_clinic_doctor_session_1_end_time >= calender_block_end_time
                                                    )  
                                                )
                                                AND
                                                (  
                                                    doctor_clinic_doctor_session_2_start_time IS NULL
                                                    OR
                                                    (
                                                        doctor_clinic_doctor_session_2_start_time <= calender_block_start_time AND 
                                                        doctor_clinic_doctor_session_2_end_time >= calender_block_start_time
                                                    )  
                                                    OR
                                                    (
                                                        doctor_clinic_doctor_session_2_start_time <= calender_block_end_time AND 
                                                        doctor_clinic_doctor_session_2_end_time >= calender_block_end_time
                                                    )
                                                )
                                            )    
                                        )
                            ),NULL
                          ) as is_available_today
                ";
        }
        $doctor_query = "
            SELECT
                user_id,
                user_first_name,
                user_last_name,
                user_photo,
                user_photo_filepath,
                user_phone_number,
                doctor_detail_year_of_experience,
                GROUP_CONCAT(specialization_title) as specialization,
                GROUP_CONCAT(doctor_detail_speciality) as speciality,
                doctor_clinic_mapping_fees,
                clinic_id,
                doctor_clinic_doctor_session_1_start_time,
                doctor_clinic_doctor_session_1_end_time,
                doctor_detail_desc,
                clinic_services,                
                user_status,
                address_city_id,
                address_country_id,
                (
                    SELECT GROUP_CONCAT(language_name) FROM 
                            " . TBL_LANGUAGES . " 
                        WHERE
                            FIND_IN_SET(language_id,doctor_detail_language_id) AND language_status=1
                ) as language,
                TIMESTAMPDIFF(YEAR, doctor_detail_year_of_experience, NOW()) as experience,
                GROUP_CONCAT(DISTINCT(doctor_qualification_degree)) as doctor_qualification_degree " . $most_visted_count . " " . $is_available_today . " ";
        if (in_array(3, $filter_array['filter_option'])) {
            $doctor_query.= "
                                    ,COALESCE((
                                         3959 *  acos( cos( radians(address_latitude) ) * cos( radians(" . $filter_array['latitude'] . " ) )
                                                         * cos( radians( " . $filter_array['longitude'] . ") - radians(address_longitude) )
                                                         + sin( radians(address_latitude) )
                                                         * sin( radians( " . $filter_array['latitude'] . " ) ) )
                                     ),0) AS 'distance'  ";
        }

        $doctor_query .= "
            FROM 
                " . TBL_USERS . " 
            JOIN " . TBL_DOCTOR_DETAILS . " 
                ON doctor_detail_doctor_id=user_id AND doctor_detail_status=1            
            JOIN " . TBL_DOCTOR_CLINIC_MAPPING . " 
                ON
                    (
                        doctor_clinic_mapping_user_id=user_id AND
                        doctor_clinic_mapping_status=1 AND 
                        doctor_clinic_mapping_fees=
                            (select min(doctor_clinic_mapping_fees) from " . TBL_DOCTOR_CLINIC_MAPPING . "
                             WHERE 
                                doctor_clinic_mapping_user_id=user_id AND
                                doctor_clinic_mapping_status=1 AND
                                doctor_clinic_mapping_role_id=1
                             )
                    )    
                        
            LEFT JOIN " . TBL_DOCTOR_SPECIALIZATIONS . " 
                ON doctor_specialization_doctor_id=user_id AND doctor_specialization_status=1
            LEFT JOIN " . TBL_SPECIALISATIONS . " 
                ON doctor_specialization_specialization_id=specialization_id AND specialization_status=1
            JOIN " . TBL_CLINICS . " 
                ON clinic_id=doctor_clinic_mapping_clinic_id AND clinic_status=1
            LEFT JOIN " . TBL_DOCTOR_EDUCATIONS . " 
                ON doctor_qualification_user_id=user_id AND doctor_qualification_status=1
            
            ";
        $doctor_query.="
            JOIN " . TBL_ADDRESS . " 
                ON address_user_id=clinic_id
                AND address_status=1 
                AND address_type=2";
        if (in_array(3, $filter_array['filter_option'])) {
            /*city and country wise doctor search*/
            if (!empty($filter_array['cityId']) && $filter_array['countryId']) {
                $doctor_query.= " AND address_city_id = '" . $filter_array['cityId'] . "' ";
                $doctor_query.= " AND address_country_id = '" . $filter_array['countryId'] . "' ";
            }
            
            if (!empty($filter_array['cityId'])){
                $doctor_query.= " AND address_city_id = '" . $filter_array['cityId'] . "' ";
            }
            
             if (!empty($filter_array['countryId'])){
               $doctor_query.= " AND address_country_id = '" . $filter_array['countryId'] . "' ";
            }
            
        }

        if (!empty($filter_array['appointment_type'])) {
            $doctor_query.=" JOIN " . TBL_DOCTOR_AVAILABILITY . "
                ON user_id = doctor_availability_user_id 
                AND doctor_availability_appointment_type =  '" . $filter_array['appointment_type'] . "' 
                AND doctor_availability_status = 1  ";

            if (in_array(2, $filter_array['filter_option'])) {
                $doctor_query.= " AND doctor_availability_week_day = '" . date('N', time()) . "' ";
            }
        }

        $doctor_query.=" WHERE 1=1 AND user_status = 1 ";

        if (!empty($filter_array['primary_doctor'])) {
            $doctor_query .= " AND user_id != '" . $filter_array['primary_doctor'] . "' ";
        }

        if (!empty($filter_array['search_keyword'])) {

            $doctor_query.="
                AND 
                    (
                        user_first_name LIKE '%" . $filter_array['search_keyword'] . "%' OR
                        user_last_name LIKE '%" . $filter_array['search_keyword'] . "%' OR
                        CONCAT(user_first_name,user_last_name) LIKE '%" . $filter_array['search_keyword'] . "%' OR
                        clinic_name LIKE '%" . $filter_array['search_keyword'] . "%'    OR
                        LOWER(specialization_title) LIKE '%" . strtolower($filter_array['search_keyword']) . "%'   
                    )
                ";
        }

        if (!empty($filter_array['specilization'])) {
            $doctor_query .= " AND specialization_id IN (" . $filter_array['specilization'] . ")  ";
        }

        if (!empty($filter_array['fees_array'])) {
            $doctor_query.="
                AND 
                    (
                        doctor_clinic_mapping_fees >=" . $filter_array['fees_array'][0] . " ";
            if (is_numeric($filter_array['fees_array'][1])) {
                $doctor_query .= "AND doctor_clinic_mapping_fees <=" . $filter_array['fees_array'][1] . "  ";
            }
            $doctor_query .= " ) ";
        }

        if (!empty($filter_array['year_of_experience'])) {
            $doctor_query.="
                AND 
                    (
                       TIMESTAMPDIFF(YEAR, doctor_detail_year_of_experience, NOW()) =" . $filter_array['year_of_experience'] . " 
                    )
                ";
        }

        if (!empty($filter_array['gender']) && $filter_array['gender'] != 3) {
            $doctor_query.="
                AND 
                    user_gender='" . $filter_array['gender'] . "' 
                ";
        }

        if (!empty($filter_array['gender']) && $filter_array['gender'] == 3) {
            $doctor_query.="
                AND 
                    (   
                        user_gender='male'
                      OR  
                        user_gender='female' 
                      OR 
                        user_gender='other' 
                      OR 
                        user_gender='undisclosed' 
                    )
                ";
        }
        if (!empty($filter_array['doctor_type'])) {
            $doctor_query.=" 
                AND 
                    doctor_detail_is_medeasy='" . $filter_array['doctor_type'] . "' 
                ";
        }
        if (!empty($filter_array['language'])) {

            $language_array = explode(',', $filter_array['language']);
            $doctor_query .= " AND ( ";
            $count = count($language_array);

            foreach ($language_array as $key => $language) {
                if ($count - 1 == $key) {
                    $doctor_query.=" FIND_IN_SET('" . $language . "',doctor_detail_language_id) ";
                } else {
                    $doctor_query.=" FIND_IN_SET('" . $language . "',doctor_detail_language_id) OR ";
                }
            }
            $doctor_query.= " ) ";
        }

        $doctor_query.= " 
              GROUP BY 
                user_id ";

        if (in_array(2, $filter_array['filter_option'])) {
            $doctor_query.="
          HAVING
          is_available_today IS NULL
          ";
        }
        $order_by = array();
        if (in_array(1, $filter_array['filter_option'])) {
            $order_by[] = " visted_count DESC ";
        }
        if (in_array(3, $filter_array['filter_option'])) {
            $order_by[] = " distance ASC ";
        }
        if (in_array(4, $filter_array['filter_option'])) {
            $order_by[] = " user_first_name  ASC ";
        }


        if (!empty($order_by)) {
            $doctor_query.=" ORDER BY  " . implode(',', $order_by);
        }

        if ($is_for_total_count) {
            return $this->get_count_by_query($doctor_query);
        }

        //pr($doctor_query);exit;

        $limit = " LIMIT " . (($filter_array['page'] - 1) * $filter_array['per_page']) . "," . $filter_array['per_page'] . " ";
        $doctor_query.=$limit;

        $doctor_data = $this->get_all_rows_by_query($doctor_query);

        return $doctor_data;
    }

    /**
     * Description :- This function is used to get primary doctor of the patient
     * 
     * @param type $patient_id
     * @param type $page
     * @param type $per_page
     * @param type $flag
     * @param type $is_for_total_count
     * @return type
     */
    public function get_primary_doctor($patient_id, $page, $per_page, $flag = true, $is_for_total_count = false) {
        $primary_query = "
            SELECT
                user_id,
                user_first_name,
                user_last_name,
                user_photo,
                user_photo_filepath,
                user_phone_number,
                doctor_detail_year_of_experience,
                GROUP_CONCAT(DISTINCT(specialization_title)) as specialization,
                GROUP_CONCAT(DISTINCT(doctor_detail_speciality)) as speciality,
                doctor_clinic_mapping_fees,                
                GROUP_CONCAT(DISTINCT(doctor_qualification_degree)) as doctor_qualification_degree,
                clinic_id,
                appointment_id,
                doctor_clinic_doctor_session_1_start_time,
                doctor_clinic_doctor_session_1_end_time,
                doctor_detail_desc,
                clinic_services,
                user_status,
                (
                    SELECT GROUP_CONCAT(language_name) FROM 
                            " . TBL_LANGUAGES . " 
                        WHERE
                            FIND_IN_SET(language_id,doctor_detail_language_id) AND language_status=1
                ) as language
            FROM 
                " . TBL_USERS . " 
            JOIN 
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
                " . TBL_SPECIALISATIONS . " 
            ON 
                doctor_specialization_specialization_id=specialization_id
            JOIN 
                " . TBL_DOCTOR_CLINIC_MAPPING . " 
            ON 
                doctor_clinic_mapping_user_id=user_id AND 
                doctor_clinic_mapping_status=1
            JOIN 
                " . TBL_CLINICS . " 
            ON 
                clinic_id=doctor_clinic_mapping_clinic_id AND 
                clinic_status=1
            LEFT JOIN 
                " . TBL_DOCTOR_EDUCATIONS . " 
            ON 
                doctor_qualification_user_id=user_id AND 
                doctor_qualification_status=1
            JOIN 
                " . TBL_APPOINTMENTS . " 
            ON 
                appointment_user_id=" . $patient_id . " AND 
                appointment_doctor_user_id=user_id
            WHERE
                user_status = 1
            GROUP BY 
                user_id
            ORDER BY 
                appointment_id ASC            
            ";


        if ($is_for_total_count) {
            return $this->get_count_by_query($primary_query);
        }
        if (!$flag) {
            $limit = " LIMIT " . ((($page - 1) * $per_page) + 1) . "," . $per_page . " ";
            $primary_query.=$limit;

            $primary_data = $this->get_all_rows_by_query($primary_query);
        } else {
            $limit = " LIMIT " . (($page - 1) * $per_page) . "," . $per_page . " ";
            $primary_query.=$limit;

            $primary_data = $this->get_single_row_by_query($primary_query);
        }
        return $primary_data;
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
                GROUP_CONCAT(DISTINCT doctor_qualification_degree) as doctor_qualification_degree,
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
                address_locality,
                address_pincode,
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

    /**
     * Description :- This function is used to get the past history 
     * of the patient with doctor
     * 
     * @param type $doctor_id
     * @param type $patient_id
     * @param type $date
     * @return type
     */
    public function get_past_history($doctor_id, $patient_id, $date) {
        $doctor_query = "
             SELECT
                user_id,
                appointment_doctor_user_id as doctor_user_id,
                appointment_clinic_id as clinic_id,
                appointment_id,
                appointment_date
            FROM 
                " . TBL_USERS . " 
            JOIN " . TBL_APPOINTMENTS . "
                ON appointment_doctor_user_id=user_id AND appointment_status=1           
                        ";
        $doctor_query.="
            WHERE
                user_id='" . $doctor_id . "' AND 
                appointment_user_id='" . $patient_id . "' AND
                appointment_date <='" . $date . "'  
            ";

        $doctor_data = $this->get_all_rows_by_query($doctor_query);
        return $doctor_data;
    }

    /**
     * Description :- This function is used to get the slots of the clinic
     * 
     * @author Manish Ramnani
     * 
     * @param type $request
     * @return type
     */
    public function get_clinic_time_slots($request) {

        $new_timeslot_query = "
                        SELECT 
                            doctor_clinic_doctor_session_1_start_time,
                            doctor_clinic_doctor_session_1_end_time,
                            doctor_clinic_doctor_session_2_start_time,
                            doctor_clinic_doctor_session_2_end_time,
                            doctor_clinic_mapping_duration
                        FROM 
                            " . TBL_DOCTOR_CLINIC_MAPPING . "                       
                        JOIN " . TBL_CLINICS . " 
                            ON clinic_id = doctor_clinic_mapping_clinic_id AND clinic_status=1                           
               
                        WHERE  
                            doctor_clinic_mapping_clinic_id=" . $request['clinic_id'] . " 
                            AND doctor_clinic_mapping_user_id=" . $request['doctor_id'] . "                 
                            AND doctor_clinic_mapping_status=1  
            ";

        $timeslots_data = $this->get_single_row_by_query($new_timeslot_query);
        return $timeslots_data;
    }

    /**
     * Description :- This function is used to get the doctor availability time 
      based on the clinic id, appointment type and doctor id
     * 
     * @author Manish Ramnani
     * 
     * 
     * @param type $check_availability
     * @return type
     */
    public function get_doctor_availibility($check_availability = array()) {

        $get_availability_sql = "SELECT 
                                        doctor_availability_id,
                                        doctor_availability_session_1_start_time,
                                        doctor_availability_session_1_end_time,
                                        doctor_availability_session_2_start_time,
                                        doctor_availability_session_2_end_time,
                                        doctor_clinic_mapping_duration
                                 FROM
                                        " . TBL_DOCTOR_AVAILABILITY . " 
                                 LEFT JOIN
                                        " . TBL_DOCTOR_CLINIC_MAPPING . "
                                 ON
                                        doctor_availability_clinic_id = doctor_clinic_mapping_clinic_id AND
                                        doctor_availability_user_id = doctor_clinic_mapping_user_id AND
                                        doctor_clinic_mapping_status = 1
                                 WHERE 
                                        doctor_availability_week_day = '" . date('N', strtotime($check_availability['date'])) . "'
                                 AND           
                                        doctor_availability_clinic_id = '" . $check_availability['clinic_id'] . "' 
                                 AND
                                        doctor_availability_user_id = '" . $check_availability['doctor_id'] . "'
                                 AND
                                        doctor_availability_appointment_type = '" . $check_availability['appointment_type'] . "' 
                                 AND
                                        doctor_availability_status = 1 ";

        $timeslots_data = $this->get_single_row_by_query($get_availability_sql);

        return $timeslots_data;
    }

    public function get_availibity($request) {
        $availibity_query = "
                        SELECT 
                            appointment_from_time,
                            appointment_to_time
                        FROM 
                            " . TBL_APPOINTMENTS . " 
                        WHERE
                            appointment_doctor_user_id=" . $request['doctor_id'] . " AND 
                            appointment_status=1 AND                             
                            appointment_date='" . $request['date'] . "'                      
                            ";
        $doctor_available_data = $this->get_all_rows_by_query($availibity_query);
        return $doctor_available_data;
    }

    public function check_block_calender($request) {
        $calender_block_query = "
             SELECT
                calender_block_id,
                calender_block_duration_type,
                calender_block_start_time,
                calender_block_end_time
             FROM " . TBL_DOCTOR_CALENDER_BLOCK . "
                 WHERE
                    calender_block_user_id=" . $request['doctor_id'] . " AND
                    calender_block_status=1 AND 
                    (
                        (
                            calender_block_duration_type = 1 AND
                            calender_block_from_date<= '" . $request['date'] . "' AND 
                            calender_block_to_date >= '" . $request['date'] . "'  
                        ) OR
                        (
                            calender_block_duration_type = 2 AND
                            calender_block_from_date= '" . $request['date'] . "' 
                        )    
                    )
            ";

        $block_data = $this->get_all_rows_by_query($calender_block_query);
        return $block_data;
    }

    /**
     * Description :- This function is used to check the doctor available 
     * from and to time with date
     * 
     * @param type $request
     * @param type $appointment_id
     * @return boolean
     */
    public function check_doctor_available($request, $appointment_id = '') {
        $doctor_available_query = "
                        SELECT 
                            appointment_id
                        FROM 
                            " . TBL_APPOINTMENTS . " 
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

    /**
     * Description :- This function is used to get the appointment detail
     * 
     * @param type $appointment_id
     * @return type
     */
    public function get_appointment_detail($appointment_id) {

        $prescription_query = "
            SELECT
                drug_name,
                prescription_drug_id,                
                prescription_generic_name,
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
                prescription_next_follow_up
            FROM " . TBL_APPOINTMENTS . " 
               
            JOIN " . TBL_PRESCRIPTION_REPORTS . "
                ON prescription_appointment_id=appointment_id AND prescription_status=1
            JOIN " . TBL_DRUGS . "
                ON drug_id=prescription_drug_id AND drug_status=1
            WHERE
                prescription_appointment_id=" . $appointment_id . " AND
                prescription_status=1 AND
                prescription_share_status=1 AND 
                drug_status=1
            ";

        $prescription_data = $this->get_all_rows_by_query($prescription_query);

        $vital_query = "
            SELECT
                vital_signature_reports_weight,
                vital_signature_reports_bloodpressure,
                vital_signature_reports_pulse,
                vital_signature_reports_temperature,
                vital_signature_reports_resp_rate
            FROM " . TBL_VITAL_REPORTS . " 
            WHERE
               vital_signature_reports_status=1 AND 
               vital_signature_reports_share_status=1 AND 
               vital_signature_reports_appointment_id=" . $appointment_id . " 
            ";

        $vital_data = $this->get_all_rows_by_query($vital_query);

        $clinical_notes_query = "
            SELECT
                clinical_notes_reports_kco,
                clinical_notes_reports_complaints,
                clinical_notes_reports_observation,
                clinical_notes_reports_diagnoses,
                clinical_notes_reports_add_notes
            FROM  " . TBL_CLINICAL_REPORTS . " 
            WHERE
                clinical_notes_reports_status=1 AND 
                clinical_notes_reports_share_status=1 AND 
                clinical_notes_reports_appointment_id=" . $appointment_id . " 
            ";

        $clinical_data = $this->get_all_rows_by_query($clinical_notes_query);

        $procedure_query = "
            SELECT
                procedure_report_procedure_text,
                procedure_report_note
            FROM " . TBL_PROCEDURE_REPORTS . " 
            WHERE
                procedure_report_status=1 AND 
                procedure_report_share_status=1 AND
                procedure_report_appointment_id=" . $appointment_id . " 
            ";

        $procedure_data = $this->get_all_rows_by_query($procedure_query);

        return array(
            "prescription_data" => $prescription_data,
            "clinical_data" => $clinical_data,
            "procedure_data" => $procedure_data,
            "vital_data" => $vital_data
        );
    }

    /**
     * Description :- This function is used to get the required details of 
     * the doctor based on the id
     * 
     * @param type $doctor_id
     * @return type
     */
    public function get_doctor_detail($doctor_id) {
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
                user_gender,
                user_phone_verified,
                user_email_verified,
                doctor_detail_year_of_experience,                                                            
                doctor_detail_language_id,    
                doctor_detail_speciality,
                address_name,
                address_name_one,
                address_city_id,
                address_state_id,
                address_country_id,
                address_pincode,
                address_latitude,
                address_longitude,
                address_locality,
                auth_phone_number as user_updated_email,
                doctor_detail_is_term_accepted,
                doctor_detail_term_accepted_date
            FROM 
                " . TBL_USERS . " 
            LEFT JOIN 
                " . TBL_DOCTOR_DETAILS . " 
            ON 
                doctor_detail_doctor_id=user_id AND 
                doctor_detail_status=1     
            LEFT JOIN
                " . TBL_USER_AUTH . " 
            ON 
                user_id = auth_user_id AND auth_type = 1
            LEFT JOIN 
                " . TBL_ADDRESS . " 
            ON 
                address_user_id=user_id AND address_type=1
            ";
        $doctor_query.="
            WHERE
                user_id='" . $doctor_id . "'  
            GROUP BY
                user_id           
            ";

        $doctor_data = $this->get_single_row_by_query($doctor_query);
        return $doctor_data;
    }

    /**
     * Description :- This function is used to get the doctor education detail
     * 
     * 
     * @param type $doctor_id
     * @param type $flag
     * @return type
     */
    public function get_doctor_edu_detail($doctor_id, $flag = false) {
        $doctor_query = "
             SELECT
                *                              
            FROM 
                " . TBL_DOCTOR_EDUCATIONS . " 
            WHERE
                doctor_qualification_user_id=" . $doctor_id . " AND
                doctor_qualification_status=1    
            ";
        if ($flag) {
            return $this->get_single_row_by_query($doctor_query);
        }
        $doctor_data = $this->get_all_rows_by_query($doctor_query);
        return $doctor_data;
    }

    /**
     * Description :- This function is used to get the specialisation detail of the doctor
     * 
     * @param type $doctor_id
     * @param type $flag
     * @return type
     */
    public function get_doctor_specialisation_detail($doctor_id, $flag = false) {
        $doctor_query = "
            SELECT
                *                              
            FROM 
                " . TBL_DOCTOR_SPECIALIZATIONS . " 
            WHERE
                doctor_specialization_doctor_id=" . $doctor_id . " AND
                doctor_specialization_status=1    
            ";
        if ($flag) {
            return $this->get_single_row_by_query($doctor_query);
        }
        $doctor_data = $this->get_all_rows_by_query($doctor_query);
        return $doctor_data;
    }

    /**
     * Description :- This function is used to get the registration details of the doctor
     * 
     * 
     * 
     * @param type $doctor_id
     * @param type $flag
     * @return type
     */
    public function get_doctor_reg_detail($doctor_id, $flag = false) {
        $doctor_query = "
            SELECT
                *                              
            FROM 
                " . TBL_DOCTOR_REGISTRATIONS . " 
            WHERE
                doctor_registration_user_id=" . $doctor_id . " AND
                doctor_registration_status=1    
            ";

        if ($flag) {
            return $this->get_single_row_by_query($doctor_query);
        }
        $doctor_data = $this->get_all_rows_by_query($doctor_query);
        return $doctor_data;
    }

    /**
     * Description :- This function is used to get the detail of the doctor award
     * 
     * @author Manish Ramnani
     * 
     * @param type $doctor_id
     * @return type
     */
    public function get_doctor_award_detail($doctor_id) {
        $doctor_query = "
            SELECT
                *                              
            FROM 
                " . TBL_DOCTOR_AWARDS . " 
            WHERE
                doctor_award_doctor_id=" . $doctor_id . " AND
                doctor_award_status=1    
            ";

        $doctor_data = $this->get_all_rows_by_query($doctor_query);
        return $doctor_data;
    }

    /**
     * Description :- This function is used to update the specialisation 
     * detail of the doctor
     * 
     * @author Manish Ramnani
     * 
     * @param type $user_id
     * @param type $specialisation_array
     * @param type $files
     */
    public function update_specialisation_data($user_id, $specialisation_array, $files) {

        $insert_array = array();
        if (isset($files['specialization_images'])) {
            $upload_path = UPLOAD_REL_PATH . "/" . DOCTOR_SPECIALIZATION_FOLDER . "/" . $user_id;
            $upload_folder = DOCTOR_SPECIALIZATION_FOLDER . "/" . $user_id;
            $profile_image_name = do_upload_multiple3($upload_path, array('photo' => $files['specialization_images']), $upload_folder);
        }

        $this->db->trans_start();
        $doctor_where = array(
            "doctor_specialization_doctor_id" => $user_id,
            "doctor_specialization_status" => 1
        );
        $update_array = array(
            'doctor_specialization_status' => 9,
            "doctor_specialization_updated_at" => $this->utc_time_formated
        );
        $this->update(TBL_DOCTOR_SPECIALIZATIONS, $update_array, $doctor_where);
        foreach ($specialisation_array as $key => $sp) {
            if (empty($sp['doctor_specialization_id'])) {
                $insert_array[] = array(
                    "doctor_specialization_doctor_id" => $user_id,
                    "doctor_specialization_specialization_id" => $sp['doctor_specialization_specialization_id'],
                    "doctor_specialization_image_full_path" => IMAGE_MANIPULATION_URL . DOCTOR_SPECIALIZATION_FOLDER . "/" . $user_id . "/" . $profile_image_name[$key],
                    "doctor_specialization_image" => $profile_image_name[$key],
                    "doctor_specialization_created_at" => $this->utc_time_formated,
                    "doctor_specialization_status" => 1,
                );
            } else {
                $update_array = array(
                    "doctor_specialization_doctor_id" => $user_id,
                    "doctor_specialization_specialization_id" => $sp['doctor_specialization_specialization_id'],
                    "doctor_specialization_updated_at" => $this->utc_time_formated,
                    "doctor_specialization_status" => 1,
                );
                if (!empty($profile_image_name[$key])) {
                    $update_array['doctor_specialization_image_full_path'] = IMAGE_MANIPULATION_URL . DOCTOR_SPECIALIZATION_FOLDER . "/" . $user_id . "/" . $profile_image_name[$key];
                    $update_array['doctor_specialization_image'] = $profile_image_name[$key];
                }
                $where = array(
                    "doctor_specialization_id" => $sp['doctor_specialization_id'],
                );
                $this->update(TBL_DOCTOR_SPECIALIZATIONS, $update_array, $where);
            }
        }
        if (!empty($insert_array)) {
            $this->insert_multiple(TBL_DOCTOR_SPECIALIZATIONS, $insert_array);
        }
        if ($this->db->trans_status() !== FALSE) {
            $this->db->trans_commit();
        } else {
            $this->db->trans_rollback();
        }
    }

    /**
     * Description :- This function is used to update the education 
     * details of the doctor
     * 
     * @author Manish Ramnani
     * 
     * @param type $user_id
     * @param type $education_qualification_array
     * @param type $files
     */
    public function update_education_data($user_id, $education_qualification_array, $files) {

        $insert_array = array();
        if (isset($files['education_images'])) {
            $upload_path = UPLOAD_REL_PATH . "/" . DOCTOR_EDUCATIONS_FOLDER . "/" . $user_id;
            $upload_folder = DOCTOR_EDUCATIONS_FOLDER . "/" . $user_id;
            $profile_image_name = do_upload_multiple3($upload_path, array('photo' => $files['education_images']), $upload_folder);
        }
        $doctor_where = array(
            "doctor_qualification_user_id" => $user_id,
            "doctor_qualification_status" => 1
        );
        $update_array = array(
            'doctor_qualification_status' => 9,
            "doctor_qualification_modified_at" => $this->utc_time_formated
        );
        $this->update(TBL_DOCTOR_EDUCATIONS, $update_array, $doctor_where);

        foreach ($education_qualification_array as $key => $edu) {
            if (empty($edu['doctor_qualification_id'])) {
                $insert_array[] = array(
                    "doctor_qualification_user_id" => $user_id,
                    "doctor_qualification_degree" => $edu['doctor_qualification_degree'],
                    "doctor_qualification_college" => $edu['doctor_qualification_college'],
                    "doctor_qualification_completion_year" => $edu['doctor_qualification_completion_year'],
                    "doctor_qualification_image_full_path" => IMAGE_MANIPULATION_URL . DOCTOR_EDUCATIONS_FOLDER . "/" . $user_id . "/" . $profile_image_name[$key],
                    "doctor_qualification_image" => $profile_image_name[$key],
                    "doctor_qualification_created_at" => $this->utc_time_formated,
                    "doctor_qualification_status" => 1,
                );
            } else {

                $update_array = array(
                    "doctor_qualification_degree" => $edu['doctor_qualification_degree'],
                    "doctor_qualification_college" => $edu['doctor_qualification_college'],
                    "doctor_qualification_completion_year" => $edu['doctor_qualification_completion_year'],
                    "doctor_qualification_modified_at" => $this->utc_time_formated,
                    "doctor_qualification_status" => 1
                );
                if (!empty($profile_image_name[$key])) {
                    $update_array['doctor_qualification_image_full_path'] = IMAGE_MANIPULATION_URL . DOCTOR_EDUCATIONS_FOLDER . "/" . $user_id . "/" . $profile_image_name[$key];
                    $update_array['doctor_qualification_image'] = $profile_image_name[$key];
                }
                $where = array(
                    "doctor_qualification_id" => $edu['doctor_qualification_id']
                );
                $this->update(TBL_DOCTOR_EDUCATIONS, $update_array, $where);
            }
        }
        if (!empty($insert_array)) {
            $this->insert_multiple(TBL_DOCTOR_EDUCATIONS, $insert_array);
        }
    }

    /**
     * Description :- This function is used to update the registration details of the doctor
     * 
     * @author Manish Ramnani
     * 
     * @param type $user_id
     * @param type $registration_details_array
     * @param type $files
     */
    public function update_registration_data($user_id, $registration_details_array, $files) {

        $insert_array = array();
        if (isset($files['registration_images'])) {
            $upload_path = UPLOAD_REL_PATH . "/" . DOCTOR_REGISTRATIONS_FOLDER . "/" . $user_id;
            $upload_folder = DOCTOR_REGISTRATIONS_FOLDER . "/" . $user_id;
            $profile_image_name = do_upload_multiple3($upload_path, array('photo' => $files['registration_images']), $upload_folder);
        }

        $doctor_where = array(
            "doctor_registration_user_id" => $user_id,
            "doctor_registration_status" => 1
        );
        $update_array = array(
            'doctor_registration_status' => 9,
            "doctor_registration_modified_at" => $this->utc_time_formated
        );
        $this->update(TBL_DOCTOR_REGISTRATIONS, $update_array, $doctor_where);

        foreach ($registration_details_array as $key => $reg) {
            if (empty($reg['doctor_registration_id'])) {
                $insert_array[] = array(
                    "doctor_registration_user_id" => $user_id,
                    "doctor_registration_council_id" => $reg['doctor_registration_council_id'],
                    "doctor_council_registration_number" => $reg['doctor_council_registration_number'],
                    "doctor_registration_year" => $reg['doctor_registration_year'],
                    "doctor_registration_image_filepath" => IMAGE_MANIPULATION_URL . DOCTOR_REGISTRATIONS_FOLDER . "/" . $user_id . "/" . $profile_image_name[$key],
                    "doctor_registration_image" => $profile_image_name[$key],
                    "doctor_registration_created_at" => $this->utc_time_formated,
                    "doctor_registration_status" => 1,
                );
            } else {

                $update_array = array(
                    "doctor_registration_council_id" => $reg['doctor_registration_council_id'],
                    "doctor_council_registration_number" => $reg['doctor_council_registration_number'],
                    "doctor_registration_year" => $reg['doctor_registration_year'],
                    "doctor_registration_modified_at" => $this->utc_time_formated,
                    "doctor_registration_status" => 1,
                );
                if (!empty($profile_image_name[$key])) {
                    $update_array['doctor_registration_image_filepath'] = IMAGE_MANIPULATION_URL . DOCTOR_REGISTRATIONS_FOLDER . "/" . $user_id . "/" . $profile_image_name[$key];
                    $update_array['doctor_registration_image'] = $profile_image_name[$key];
                }
                $where = array(
                    "doctor_registration_id" => $reg['doctor_registration_id']
                );
                $this->update(TBL_DOCTOR_REGISTRATIONS, $update_array, $where);
            }
        }
        if (!empty($insert_array)) {
            $this->insert_multiple(TBL_DOCTOR_REGISTRATIONS, $insert_array);
        }
    }

    /**
     * Description :- This function is used to update the awards of the doctor
     * 
     * @author Manish Ramnani
     * 
     * @param type $user_id
     * @param type $award_details_array
     * @param type $files
     */
    public function update_award_data($user_id, $award_details_array, $files) {

        $insert_array = array();
        if (isset($files['award_images'])) {
            $upload_path = UPLOAD_REL_PATH . "/" . DOCTOR_AWARDS_FOLDER . "/" . $user_id;
            $upload_folder = DOCTOR_AWARDS_FOLDER . "/" . $user_id;
            $profile_image_name = do_upload_multiple3($upload_path, array('photo' => $files['award_images']), $upload_folder);
        }

        $doctor_where = array(
            "doctor_award_doctor_id" => $user_id,
            "doctor_award_status" => 1
        );
        $update_array = array(
            'doctor_award_status' => 9,
            "doctor_award_modified_at" => $this->utc_time_formated
        );
        $this->update(TBL_DOCTOR_AWARDS, $update_array, $doctor_where);

        foreach ($award_details_array as $key => $reg) {
            if (empty($reg['doctor_award_id'])) {
                $insert_array[] = array(
                    "doctor_award_doctor_id" => $user_id,
                    "doctor_award_name" => $reg['doctor_award_name'],
                    "doctor_award_year" => $reg['doctor_award_year'],
                    "doctor_award_image_fullpath" => IMAGE_MANIPULATION_URL . DOCTOR_AWARDS_FOLDER . "/" . $user_id . "/" . $profile_image_name[$key],
                    "doctor_award_image" => $profile_image_name[$key],
                    "doctor_award_created_at" => $this->utc_time_formated,
                    "doctor_award_status" => 1,
                );
            } else {

                $update_array = array(
                    "doctor_award_name" => $reg['doctor_award_name'],
                    "doctor_award_year" => $reg['doctor_award_year'],
                    "doctor_award_modified_at" => $this->utc_time_formated,
                    "doctor_award_status" => 1,
                );
                if (!empty($profile_image_name[$key])) {
                    $update_array['doctor_award_image_fullpath'] = IMAGE_MANIPULATION_URL . DOCTOR_AWARDS_FOLDER . "/" . $user_id . "/" . $profile_image_name[$key];
                    $update_array['doctor_award_image'] = $profile_image_name[$key];
                }
                $where = array(
                    "doctor_award_id" => $reg['doctor_award_id']
                );
                $this->update(TBL_DOCTOR_AWARDS, $update_array, $where);
            }
        }
        if (!empty($insert_array)) {
            $this->insert_multiple(TBL_DOCTOR_AWARDS, $insert_array);
        }
    }

    /**
     * Description :- This function get the details of the user to know 
     * how much percentage of the user profile complete
     * 
     * @author Manish Ramnani
     * 
     * @param type $doctor_id
     * @return type
     */
    public function get_percentage($doctor_id) {
        $count_query = "           
                SELECT
                   user_first_name,
                   user_last_name,
                   user_photo_filepath,
                   user_phone_number,
                   user_gender,
                   address_name,
                   address_name_one,
                   address_city_id,
                   address_state_id,
                   address_country_id,
                   address_pincode,
                   address_locality,
                   doctor_detail_language_id
                FROM 
                    " . TBL_USERS . "
                LEFT JOIN 
                    " . TBL_ADDRESS . " 
                ON 
                    address_user_id=user_id AND
                    address_type=1 AND 
                    address_status=1
                LEFT JOIN 
                    " . TBL_DOCTOR_DETAILS . " 
                ON 
                    doctor_detail_doctor_id=user_id AND 
                    doctor_detail_status=1
                WHERE
                    user_id=" . $doctor_id . " 
               ";

        $doctor_data = $this->get_single_row_by_query($count_query);
        return $doctor_data;
    }

    /**
     * Description :- If doctor already add the block calendar again he 
     * tried to enter the same data at that time this function validate the data
     * 
     * @author Manish Ramnani
     * 
     * @param type $data
     * @return type
     */
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

    /**
     * Description :- This function helps to know if the user books the 
     * appointment and that time if doctor is available or set the holiday 
     * by the doctor in that case not allow to book the appointment by the user
     * 
     * @author Manish Ramnani
     * 
     * @param type $data
     * @return type
     */
    public function appointment_exists_block_calendar($data = array()) {

        //get the appointment list if exists in the date blocked by the doctor
        $get_appointment_list_sql = " SELECT 
                                                appointment_id 
                                          FROM 
                                                " . TBL_APPOINTMENTS . " 
                                          WHERE 
                                                appointment_status = 1
                                          AND
                                                appointment_doctor_user_id = '" . $data['doctor_id'] . "' 
                                          AND ";

        if ($data['block_type'] == 1) {
            $get_appointment_list_sql .= "                                                   
                                                (
                                                    appointment_date >= '" . $data['start_date'] . "' AND
                                                    appointment_date <= '" . $data['end_date'] . "'
                                                ) 
                                            ";
        } else {
            $get_appointment_list_sql .= "
                                            (        
                                                (
                                                    appointment_from_time <= '" . $data['start_time'] . "' AND
                                                    appointment_to_time >= '" . $data['start_time'] . "'
                                                )
                                                OR
                                                (
                                                    appointment_from_time <= '" . $data['end_time'] . "' AND
                                                    appointment_to_time >= '" . $data['end_time'] . "'
                                                )
                                            )
                                            AND appointment_date = '" . $data['block_slot_date'] . "'  ";
        }

        $get_appointment_data = $this->Common_model->get_all_rows_by_query($get_appointment_list_sql);

        return $get_appointment_data;
    }

    /**
     * Description :- This function is used to get the availability of the doctor
     * based on the clinic and appointment type
     * 
     * @author Manish Ramnani
     * 
     * @param type $requested_data
     * @return type
     */
    public function get_doctor_availability($requested_data) {

        $get_doctor_availablity_sql = " SELECT 
                                            GROUP_CONCAT(doctor_availability_id) as doctor_availability_id,
                                            doctor_availability_status
                                        FROM 
                                                " . TBL_DOCTOR_AVAILABILITY . " 
                                        WHERE
                                                doctor_availability_clinic_id = '" . $requested_data['clinic_id'] . "'
                                        AND
                                                doctor_availability_user_id = '" . $requested_data['doctor_id'] . "'
                                        AND
                                                doctor_availability_appointment_type = '" . $requested_data['appointment_type'] . "'
                                        AND
                                                doctor_availability_status != 9;
                                        ";

        $get_doctor_availablity = $this->get_single_row_by_query($get_doctor_availablity_sql);

        return $get_doctor_availablity;
    }

    /**
     * Description :- This function is used to get the list of those patient 
     * who has appointment with particular doctor clinic wise
     * 
     * @author Manish Ramnani
     * 
     * @param type $requested_data
     * @return type
     */
    public function get_doctor_patient_list($requested_data) {

        $columns = "user_id, 
                    user_first_name, 
                    user_last_name, 
                    user_photo_filepath,
                    appointment_id,
                    appointment_doctor_user_id,
                    appointment_clinic_id,
                    CONCAT(user_first_name,' ',user_last_name) AS user_name ";

        $get_patient_list_query = " SELECT " . $columns . " 
                                    FROM 
                                            " . TBL_APPOINTMENTS . "  
                                    LEFT JOIN
                                            " . TBL_USERS . " ON appointment_user_id = user_id 
                                    WHERE
                                            appointment_status != 9 
                                    AND
                                            user_status  = 1
                                    AND 
                                            appointment_doctor_user_id = '" . $requested_data['doctor_id'] . "' 
                                    AND
                                            appointment_clinic_id = '" . $requested_data['clinic_id'] . "' ";

        if ($requested_data['patient_list_type'] == 1) {
            $get_patient_list_query .= " AND appointment_date = CURDATE() ";
        }

        $get_patient_list_query .= " GROUP BY user_id ";

        $get_total_records = $this->get_count_by_query($get_patient_list_query);

        $get_patient_list_query .= " ORDER BY appointment_id Desc ";

        if ($requested_data['patient_list_type'] == 2) {
            $get_patient_list_query .= " LIMIT 0, 10 ";
        }
        if ($requested_data['patient_list_type'] == 3) {
            $get_patient_list_query .= " LIMIT " . (($requested_data['page'] - 1) * $requested_data['per_page']) . "," . $requested_data['per_page'] . " ";
        }

        $patient_data = $this->get_all_rows_by_query($get_patient_list_query);

        $return_array = array(
            'patient_data' => $patient_data,
            'no_of_records' => $get_total_records
        );

        return $return_array;
    }

    /**
     * Description :- This function is used to get the detail of the patient doctor and clinic wise
     * 
     * @author Manish Ramnani
     * 
     * @param type $requested_data
     * @return type
     */
    public function get_doctor_patient_detail($requested_data) {

        $columns = "user_id, 
                    user_first_name,
                    user_last_name,
                    CONCAT(user_first_name,' ',user_last_name) AS user_name,
                    user_photo_filepath,
                    user_unique_id,
                    user_details_dob,
                    user_details_weight,
                    user_details_height,
                    user_details_smoking_habbit,
                    user_details_alcohol,
                    appointment_type,
                    GROUP_CONCAT(clinical_notes_reports_appointment_id) as kco_appointment_id,
                    GROUP_CONCAT(clinical_notes_reports_doctor_user_id) as kco_doctor_id,
                    GROUP_CONCAT(clinical_notes_reports_kco) as kco,
                    GROUP_CONCAT(clinical_notes_reports_clinic_id) as clinic_id
                    ";

        $get_patient_detail_query = " SELECT " . $columns . " 
                                      FROM 
                                            " . TBL_USERS . " 
                                      LEFT JOIN
                                            " . TBL_USER_DETAILS . " ON user_id = user_details_user_id
                                      LEFT JOIN 
                                            " . TBL_APPOINTMENTS . " ON user_id = appointment_user_id
                                      LEFT JOIN
                                            " . TBL_CLINICAL_NOTES_REPORT . " ON clinical_notes_reports_user_id = user_id AND clinical_notes_reports_status = 1
                                      WHERE 
                                           appointment_id = '" . $requested_data['appointment_id'] . "' 
                                      AND         
                                           appointment_user_id = '" . $requested_data['patient_id'] . "'
                                      AND
                                           appointment_clinic_id = '" . $requested_data['clinic_id'] . "' 
                                      AND
                                           appointment_doctor_user_id = '" . $requested_data['doctor_id'] . "' 
                                      AND 
                                           appointment_status != 9 ";

        $get_patient_data = $this->get_single_row_by_query($get_patient_detail_query);

        return $get_patient_data;
    }

    /**
     * Description :- This function is used to get the result of search doctor
     * 
     * @author Reena Gatecha
     * 
     * @param type $requested_data
     * @return type
     */
    public function get_search_doctor($requested_data) {

        $columns = "user_id, 
                    CONCAT(user_first_name,' ',user_last_name) AS user_name,
                    user_photo_filepath 
                    ";

        $get_doctor_detail_query = " SELECT 
                                        " . $columns . " 
                                    FROM 
                                        " . TBL_USERS . " 
                                    LEFT JOIN 
                                        " . TBL_DOCTOR_CLINIC_MAPPING . " 
                                    ON 
                                        user_id = doctor_clinic_mapping_user_id AND doctor_clinic_mapping_status = 1
                                    WHERE
                                        user_status=1 AND
                                        user_id != '" . $requested_data['doctor_id'] . "' AND
                                        user_type=2  AND
                                        doctor_clinic_mapping_role_id = 1 ";

        if (!empty($requested_data['search'])) {

            $get_doctor_detail_query .=" 
                                        AND 
                                        (
                                            user_first_name LIKE '%" . $requested_data['search'] . "%' OR
                                            user_last_name LIKE '%" . $requested_data['search'] . "%' OR
                                            user_email LIKE '%" . $requested_data['search'] . "%' 
                                        )
                                        ";
        }

        $get_doctor_data = $this->get_all_rows_by_query($get_doctor_detail_query);

        return $get_doctor_data;
    }

    /**
     * Description :- This function is used to get the result of my fav doctor
     * 
     * @author Reena Gatecha
     * 
     * @param type $requested_data
     * @return type
     */
    public function get_fav_doctor($requested_data) {

        $columns = "user_id, 
                    CONCAT(user_first_name,' ',user_last_name) AS user_name,
                    user_phone_number,
                    user_email
                    ";

        $get_doctor_detail_query = " SELECT " . $columns . " 
                    FROM 
                    " . TBL_FAV_DOCTORS . " 
                    JOIN " . TBL_USERS . " 
                    ON fav_doctors_other_doctor_id=user_id AND fav_doctors_status=1
                    WHERE
                    user_status=1 AND
                    user_type=2 AND 
                    fav_doctors_doctor_id=" . $requested_data['doctor_id'] . " 
                    ";
        if (!empty($requested_data)) {
            $get_doctor_detail_query.=" AND 
                    (
                    user_first_name LIKE '%" . $requested_data['search'] . "%' OR
                    user_last_name LIKE '%" . $requested_data['search'] . "%' OR
                    user_email LIKE '%" . $requested_data['search'] . "%' 
                    )
                    ";
        }


        $get_doctor_data = $this->get_all_rows_by_query($get_doctor_detail_query);

        return $get_doctor_data;
    }

    /**
     * This function is used to get refered by and refer to patient list
     * 
     * @author Manish Ramnani
     * 
     * @param type $requested_data
     * @return type
     */
    public function get_refer($requested_data) {

        $columns = 'u.user_id AS patient_id,
                    u.user_first_name AS patient_first_name,
                    u.user_last_name AS patient_last_name,
                    CONCAT(u.user_first_name," ",u.user_last_name) AS patient_name,
                    u.user_photo_filepath,
                    u1.user_first_name AS doctor_first_name,
                    u1.user_last_name AS doctor_last_name,
                    CONCAT(u1.user_first_name," ",u1.user_last_name) AS doctor_name,
                    refer_created_at,

                    appointment_id,
                    appointment_user_id
                    appointment_doctor_user_id,
                    appointment_clinic_id,
                    appointment_date
                    ';

        $get_refer_sql = " SELECT 
                    " . $columns . " 
                    FROM 
                    " . TBL_REFER . " 
                    LEFT JOIN
                    " . TBL_USERS . " u ON refer_user_id = u.user_id
                    LEFT JOIN
                    " . TBL_USERS . " u1 ON refer_doctor_id = u1.user_id
                    LEFT JOIN
                    " . TBL_APPOINTMENTS . " ON refer_appointment_id = appointment_id
                    WHERE
                    refer_status != 9 ";

        if ($requested_data['flag'] == 2) {
            $get_refer_sql .= " AND refer_doctor_id = '" . $requested_data['user_id'] . "' ";
        }
        if ($requested_data['flag'] == 1) {
            $get_refer_sql .= " AND refer_other_doctor_id = '" . $requested_data['user_id'] . "' ";
        }

        $get_refer_data = $this->get_all_rows_by_query($get_refer_sql);

        return $get_refer_data;
    }

    public function check_paient_appointment($requested_data) {

        //check appointment with doctor
        $check_patient_appointment_sql = "
            SELECT 
                appointment_date,
                user_first_name,
                user_last_name,
                user_phone_number,
                doctor_detail_speciality,
                appointment_clinic_id,
                clinic_name,
                clinic_contact_number,
                clinic_email,
                address_name,
                GROUP_CONCAT(DISTINCT(doctor_qualification_degree)) AS doctor_qualification
            FROM 
                " . TBL_APPOINTMENTS . "
            LEFT JOIN 
                " . TBL_USERS . " ON appointment_doctor_user_id=user_id
            LEFT JOIN 
                " . TBL_DOCTOR_DETAILS . " ON  user_id = doctor_detail_doctor_id
            LEFT JOIN 
                " . TBL_ADDRESS . " ON address_user_id = appointment_clinic_id AND address_type = 2
            LEFT JOIN 
               " . TBL_CLINICS . " ON appointment_clinic_id = clinic_id
            LEFT JOIN
                " . TBL_DOCTOR_EDUCATIONS . " ON appointment_doctor_user_id = doctor_qualification_user_id AND doctor_qualification_status = 1
            WHERE
                appointment_user_id='" . $requested_data['patient_id'] . "' AND 
                appointment_doctor_user_id='" . $requested_data['doctor_id'] . "' AND 
                appointment_id='" . $requested_data['appointment_id'] . "'
        ";

        $check_patient_appointment = $this->Common_model->get_single_row_by_query($check_patient_appointment_sql);

        return $check_patient_appointment;
    }

    public function get_prescription_for_patient($appointment_id) {

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
                    GROUP_CONCAT(drug_generic_title) as drug_generic_title,
                    prescription_intake_instruction,
                    prescription_frequency_instruction,
                    follow_up_followup_date,
                    follow_up_instruction,
                    prescription_frequency_value as freq
                FROM 
                    " . TBL_PRESCRIPTION_REPORTS . " 
                LEFT JOIN
                    " . TBL_PRESCRIPTION_FOLLOUP . " ON prescription_appointment_id = follow_up_appointment_id
                LEFT JOIN 
                    " . TBL_DRUG_FREQUENCY . " ON prescription_frequency_id=drug_frequency_id 
                LEFT JOIN 
                    " . TBL_DRUG_UNIT . " ON prescription_unit_id = drug_unit_id
                LEFT JOIN 
                    " . TBL_DRUG_GENERIC . " ON FIND_IN_SET(drug_generic_id, prescription_generic_id)  AND  drug_generic_status = 1
                WHERE 
                    prescription_appointment_id='" . $appointment_id . "' AND 
                    prescription_status=1
                GROUP BY 
                    prescription_id
            ";
        $patient_prescription = $this->Common_model->get_all_rows_by_query($patient_prescription_sql);
        return $patient_prescription;
    }

}
