<?php

class Emailsetting_model extends MY_Model {

    function __construct() {
        parent::__construct();
    }

    /**
     * Description :- This function is used to get email subject and message
     * 
     * @author Pragnesh Rupapara
     * 
     * @param type $id
     * @return type
     */
    public function get_emailtemplate_by_id($id) {

        $get_email_template_sql = "SELECT 
                                        email_template_subject,
                                        email_template_message
                                    FROM 
                                        " . TBL_EMAIL_TEMPLATE . " 
                                    WHERE 
                                        email_template_id = '" . $id . "' AND
                                        email_template_status = '1' 
                                    ";

        $get_email_template_data = $this->get_single_row_by_query($get_email_template_sql);

        $header = $this->get_header();
        $footer = $this->get_footer();
        $email_static_data = $this->email_setting();

        $return_array = array(
            'email_template_message' => $header['email_template_message'] . ' ' . $get_email_template_data['email_template_message'] . ' ' . $footer['email_template_message'],
            'email_template_subject' => $get_email_template_data['email_template_subject'],
            'email_static_data' => $email_static_data
        );

        return $return_array;
    }

    public function get_header() {

        $get_email_template_sql = "SELECT 
                                        email_template_message
                                    FROM 
                                        " . TBL_EMAIL_TEMPLATE . " 
                                    WHERE 
                                        email_template_id = 22 AND
                                        email_template_status = '1' 
                                    ";

        $get_email_template_data = $this->get_single_row_by_query($get_email_template_sql);
        return $get_email_template_data;
    }

    public function get_footer() {

        $get_email_template_sql = "SELECT 
                                        email_template_message
                                    FROM 
                                        " . TBL_EMAIL_TEMPLATE . " 
                                    WHERE 
                                        email_template_id = 23 AND
                                        email_template_status = '1' 
                                    ";

        $get_email_template_data = $this->get_single_row_by_query($get_email_template_sql);
        return $get_email_template_data;
    }

    public function email_setting() {

        $setting_where = "'email_id', 'company_name', 'contact_number' ";

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

        if (!empty($setting_where)) {
            $get_setting_sql .= " AND global_setting_name IN (" . $setting_where . ") ";
        }

        $get_setting = $this->get_all_rows_by_query($get_setting_sql);

        return array_column($get_setting, 'global_setting_value', 'global_setting_name');
    }

}
