<?php

class Billing extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('Billing_model', 'billing');
    }

    /**
     * Description :- This function is used to add the tax
     */
    public function add_tax_post() {
        try {
            $doctor_id = !empty($this->Common_model->escape_data($this->post_data['doctor_id'])) ? $this->Common_model->escape_data($this->post_data['doctor_id']) : '';
            $tax_data = !empty($this->post_data['tax_data']) ? $this->post_data['tax_data'] : '';
            $tax_data = json_decode($tax_data, true);

            if (empty($doctor_id) ||
                    empty($tax_data)
            ) {
                $this->bad_request();
            }

            $get_role_details = $this->Common_model->get_the_role($this->user_id);
            if (!empty($get_role_details['user_role_data'])) {
                $permission_data = array(
                    'role_data' => $get_role_details['user_role_data'],
                    'module' => 16,
                    'key' => 1
                );
                $check_module_permission = $this->check_module_permission($permission_data);
                if ($check_module_permission == 2) {
                    $this->my_response['status'] = false;
                    $this->my_response['message'] = lang('permission_error');
                    $this->send_response();
                }
            }

            foreach ($tax_data as $tax) {

                $tax_value = $tax['tax_value'];
                $tax_name = $tax['tax_name'];

                if (!is_numeric($tax_value)) {
                    $this->my_response['status'] = false;
                    $this->my_response['message'] = lang('mycontroller_invalid_request');
                    $this->send_response();
                }

                
                if (validate_percentage($tax_value)) {
                    $this->my_response['status'] = false;
                    $this->my_response['message'] = lang('mycontroller_invalid_request');
                    $this->send_response();
                }

                //check same tax name already added or not
                $where = array(
                    'tax_name' => strtolower($tax_name),
                    'tax_doctor_id' => $doctor_id,
                    'tax_status' => 1
                );

                $get_tax_data = $this->Common_model->get_single_row(TBL_TAXES, 'tax_id', $where);

                if (empty($get_tax_data)) {
                    $tax_data_array[] = array(
                        'tax_name' => $tax_name,
                        'tax_value' => $tax_value,
                        'tax_doctor_id' => $doctor_id,
                        'tax_created_at' => $this->utc_time_formated
                    );
                }
            }

            if (!empty($tax_data_array)) {
                $this->Common_model->insert_multiple(TBL_TAXES, $tax_data_array);
            }
            $this->my_response['status'] = true;
            $this->my_response['message'] = lang('tax_added');
            $this->send_response();
        } catch (ErrorException $ex) {
            $this->error = $ex->getMessage();
            $this->store_error();
        }
    }

    /**
     * Description :- This function is used to edit the tax
     * 
     * 
     * 
     */
    public function edit_tax_post() {

        try {
            $tax_id = !empty($this->Common_model->escape_data($this->post_data['tax_id'])) ? $this->Common_model->escape_data($this->post_data['tax_id']) : '';
            $tax_name = !empty($this->Common_model->escape_data($this->post_data['tax_name'])) ? $this->Common_model->escape_data($this->post_data['tax_name']) : '';
            $tax_value = !empty($this->Common_model->escape_data($this->post_data['tax_value'])) ? $this->Common_model->escape_data($this->post_data['tax_value']) : '';
            $doctor_id = !empty($this->Common_model->escape_data($this->post_data['doctor_id'])) ? $this->Common_model->escape_data($this->post_data['doctor_id']) : '';

            if (empty($tax_id) ||
                    empty($tax_name) ||
                    empty($tax_value) ||
                    empty($doctor_id)
            ) {
                $this->bad_request();
            }

            $get_role_details = $this->Common_model->get_the_role($this->user_id);
            if (!empty($get_role_details['user_role_data'])) {
                $permission_data = array(
                    'role_data' => $get_role_details['user_role_data'],
                    'module' => 16,
                    'key' => 2
                );
                $check_module_permission = $this->check_module_permission($permission_data);
                if ($check_module_permission == 2) {
                    $this->my_response['status'] = false;
                    $this->my_response['message'] = lang('permission_error');
                    $this->send_response();
                }
            }

            if (!is_numeric($tax_value)) {
                $this->my_response['status'] = false;
                $this->my_response['message'] = lang('mycontroller_invalid_request');
                $this->send_response();
            }

            if (validate_percentage($tax_value)) {
                $this->my_response['status'] = false;
                $this->my_response['message'] = lang('mycontroller_invalid_request');
                $this->send_response();
            }

            //check tax belongs to doctor id or not
            $requested_data = array(
                'tax_id' => $tax_id,
                'doctor_id' => $doctor_id
            );
            $this->check_tax_belongs($requested_data);

            //check same tax name already added or not
            $where = array(
                'tax_name' => strtolower($tax_name),
                'tax_doctor_id' => $doctor_id,
                'tax_status' => 1,
                'tax_id !=' => $tax_id
            );

            $get_tax_data = $this->Common_model->get_single_row(TBL_TAXES, 'tax_id', $where);

            if (!empty($get_tax_data)) {
                $this->my_response['status'] = false;
                $this->my_response['message'] = lang('tax_name_already_exists');
                $this->send_response();
            }

            $tax_data_update = array(
                'tax_name' => $tax_name,
                'tax_value' => $tax_value,
                'tax_updated_at' => $this->utc_time_formated
            );

            $tax_data_where = array(
                'tax_id' => $tax_id
            );

            $is_update = $this->Common_model->update(TBL_TAXES, $tax_data_update, $tax_data_where);

            if ($is_update > 0) {
                $this->my_response['status'] = true;
                $this->my_response['message'] = lang('tax_updated');
            } else {
                $this->my_response['status'] = false;
                $this->my_response['message'] = lang('failure');
            }
            $this->send_response();
        } catch (ErrorException $ex) {
            $this->error = $ex->getMessage();
            $this->store_error();
        }
    }

    /**
     * Description :- This function is used to get the tax based on the doctor id
     */
    public function get_tax_post() {
        try {
            $doctor_id = !empty($this->Common_model->escape_data($this->post_data['doctor_id'])) ? $this->Common_model->escape_data($this->post_data['doctor_id']) : '';
			
            if (empty($doctor_id)) {
                $this->bad_request();
            }

            $get_role_details = $this->Common_model->get_the_role($this->user_id);
            if (!empty($get_role_details['user_role_data'])) {
                $permission_data = array(
                    'role_data' => $get_role_details['user_role_data'],
                    'module' => 16,
                    'key' => 3
                );
                $check_module_permission = $this->check_module_permission($permission_data);
                if ($check_module_permission == 2) {
                    $this->my_response['status'] = false;
                    $this->my_response['message'] = lang('permission_error');
                    $this->send_response();
                }
            }

            $where_tax = array(
                'tax_doctor_id' => $doctor_id,
                'tax_status' => 1
            );

            $columns = 'tax_id, tax_name, tax_value';
            $get_tax_data = $this->Common_model->get_all_rows(TBL_TAXES, $columns, $where_tax);

            if (!empty($get_tax_data)) {
                $this->my_response['status'] = true;
                $this->my_response['message'] = lang('common_detail_found');
                $this->my_response['data'] = $get_tax_data;
            } else {
                $this->my_response['status'] = false;
                $this->my_response['message'] = lang('common_detail_not_found');
                $this->my_response['data'] = array();
            }
            $this->send_response();
        } catch (ErrorException $ex) {
            $this->error = $ex->getMessage();
            $this->store_error();
        }
    }

    /**
     * Description :- This function is used delete the tax data
     * 
     * 
     * 
     */
    public function delete_tax_post() {

        try {

            $tax_id = !empty($this->Common_model->escape_data($this->post_data['tax_id'])) ? $this->Common_model->escape_data($this->post_data['tax_id']) : '';
            $doctor_id = !empty($this->Common_model->escape_data($this->post_data['doctor_id'])) ? $this->Common_model->escape_data($this->post_data['doctor_id']) : '';

            if (empty($tax_id) ||
                    empty($doctor_id)
            ) {
                $this->bad_request();
            }

            $get_role_details = $this->Common_model->get_the_role($this->user_id);
            if (!empty($get_role_details['user_role_data'])) {
                $permission_data = array(
                    'role_data' => $get_role_details['user_role_data'],
                    'module' => 16,
                    'key' => 4
                );
                $check_module_permission = $this->check_module_permission($permission_data);
                if ($check_module_permission == 2) {
                    $this->my_response['status'] = false;
                    $this->my_response['message'] = lang('permission_error');
                    $this->send_response();
                }
            }

            //check tax belongs to doctor id or not
            $requested_data = array(
                'tax_id' => $tax_id,
                'doctor_id' => $doctor_id
            );
            $this->check_tax_belongs($requested_data);

            $update_tax = array(
                'tax_status' => 9,
                'tax_updated_at' => $this->utc_time_formated
            );

            $update_tax_where = array(
                'tax_id' => $tax_id
            );

            $is_update = $this->Common_model->update(TBL_TAXES, $update_tax, $update_tax_where);

            if ($is_update > 0) {
                $this->my_response['status'] = true;
                $this->my_response['message'] = lang('tax_deleted');
            } else {
                $this->my_response['status'] = false;
                $this->my_response['message'] = lang('failure');
            }
            $this->send_response();
        } catch (ErrorException $ex) {
            $this->error = $ex->getMessage();
            $this->store_error();
        }
    }

    /**
     * Description :- This function is used to check the data belongs to the particular user or not
     * 
     * 
     * 
     * @param type $requested_data
     */
    public function check_tax_belongs($requested_data) {

        $where_array = array(
            'tax_status' => 1,
            'tax_id' => $requested_data['tax_id'],
            'tax_doctor_id' => $requested_data['doctor_id']
        );

        $check_tax_belongs_doctor = $this->Common_model->get_single_row(TBL_TAXES, 'tax_id', $where_array);

        if (empty($check_tax_belongs_doctor)) {
            $this->my_response['status'] = false;
            $this->my_response['message'] = lang('mycontroller_invalid_request');
            $this->send_response();
        }
    }

    /**
     * Description :- This function is used to get the payment type
     * 
     * 
     * 
     * 
     */
    public function get_payment_type_post() {

        try {

            $get_role_details = $this->Common_model->get_the_role($this->user_id);
            if (!empty($get_role_details['user_role_data'])) {
                $permission_data = array(
                    'role_data' => $get_role_details['user_role_data'],
                    'module' => 16,
                    'key' => 3
                );
                $check_module_permission = $this->check_module_permission($permission_data);
                if ($check_module_permission == 2) {
                    $this->my_response['status'] = false;
                    $this->my_response['message'] = lang('permission_error');
                    $this->send_response();
                }
            }

            $where_array = array(
                'payment_type_status' => 1
            );
            $columns = 'payment_type_id, payment_type_name';
            $get_payment_type = $this->Common_model->get_payment_type($columns);

            if (!empty($get_payment_type)) {
                $this->my_response['status'] = true;
                $this->my_response['message'] = lang('common_detail_found');
                $this->my_response['data'] = $get_payment_type;
            } else {
                $this->my_response['status'] = false;
                $this->my_response['message'] = lang('common_detail_not_found');
                $this->my_response['data'] = array();
            }

            $this->send_response();
        } catch (ErrorException $ex) {
            $this->error = $ex->getMessage();
            $this->store_error();
        }
    }

    /**
     * Description :- This function is used to add the payment mode
     * 
     * 
     * 
     */
    public function add_payment_mode_post() {

        try {
            $doctor_id = !empty($this->Common_model->escape_data($this->post_data['doctor_id'])) ? $this->Common_model->escape_data($this->post_data['doctor_id']) : '';
            $payment_mode_data = !empty($this->post_data['payment_mode_data']) ? $this->post_data['payment_mode_data'] : '';
            $payment_mode_data = json_decode($payment_mode_data, true);


            if (empty($doctor_id) ||
                    empty($payment_mode_data)
            ) {
                $this->bad_request();
            }

            $get_role_details = $this->Common_model->get_the_role($this->user_id);
            if (!empty($get_role_details['user_role_data'])) {
                $permission_data = array(
                    'role_data' => $get_role_details['user_role_data'],
                    'module' => 16,
                    'key' => 1
                );
                $check_module_permission = $this->check_module_permission($permission_data);
                if ($check_module_permission == 2) {
                    $this->my_response['status'] = false;
                    $this->my_response['message'] = lang('permission_error');
                    $this->send_response();
                }
            }

            foreach ($payment_mode_data as $payment) {

                $payment_vendor_fee = $payment['fee'];
                $payment_mode_name = $payment['name'];
                $payment_type = $payment['payment_type'];

                if (!empty($payment_vendor_fee) && !is_numeric($payment_vendor_fee)) {
                    $this->my_response['status'] = false;
                    $this->my_response['message'] = lang('mycontroller_invalid_request');
                    $this->send_response();
                }

                if (!empty($payment_vendor_fee) && validate_percentage($payment_vendor_fee)) {
                    $this->my_response['status'] = false;
                    $this->my_response['message'] = lang('mycontroller_invalid_request');
                    $this->send_response();
                }

                //check same tax name already added or not
                $where = array(
                    'payment_mode_name' => strtolower($payment_mode_name),
                    'payment_mode_doctor_id' => $doctor_id,
                    'payment_mode_status' => 1,
                    'payment_mode_payment_type_id' => $payment_type
                );

                $get_payment_mode = $this->Common_model->get_single_row(TBL_PAYMENT_MODE, 'payment_mode_id', $where);

                if (empty($get_payment_mode)) {
                    $payment_data[] = array(
                        'payment_mode_name' => $payment_mode_name,
                        'payment_mode_vendor_fee' => $payment_vendor_fee,
                        'payment_mode_doctor_id' => $doctor_id,
                        'payment_mode_payment_type_id' => $payment_type,
                        'payment_mode_created_at' => $this->utc_time_formated
                    );
                }
            }

            if (!empty($payment_data)) {
                $this->Common_model->insert_multiple(TBL_PAYMENT_MODE, $payment_data);
            }

            $this->my_response['status'] = true;
            $this->my_response['message'] = lang('payment_added');
            $this->send_response();
        } catch (ErrorException $ex) {
            $this->error = $ex->getMessage();
            $this->store_error();
        }
    }

    /**
     * Description :- This function is used to get the payment mode based on the doctor id
     * 
     * 
     */
    public function get_payment_mode_post() {

        try {

            $doctor_id = !empty($this->Common_model->escape_data($this->post_data['doctor_id'])) ? $this->Common_model->escape_data($this->post_data['doctor_id']) : '';

            if (empty($doctor_id)) {
                $this->bad_request();
            }

            $get_role_details = $this->Common_model->get_the_role($this->user_id);
            if (!empty($get_role_details['user_role_data'])) {
                $permission_data = array(
                    'role_data' => $get_role_details['user_role_data'],
                    'module' => 16,
                    'key' => 3
                );
                $check_module_permission = $this->check_module_permission($permission_data);
                if ($check_module_permission == 2) {
                    $this->my_response['status'] = false;
                    $this->my_response['message'] = lang('permission_error');
                    $this->send_response();
                }
            }

            $where_payment = array(
                'payment_mode_doctor_id' => $doctor_id,
                'payment_mode_status' => 1
            );

            $columns = 'payment_mode_id, payment_mode_name, 
                        payment_mode_vendor_fee, payment_type_id,
                        payment_type_name';

            $join_array = array(
                TBL_PAYMENT_TYPE => 'payment_mode_payment_type_id = payment_type_id'
            );

            $get_payment_data = $this->Common_model->get_all_rows(TBL_PAYMENT_MODE, $columns, $where_payment, $join_array, array(), '', 'LEFT');

            if (!empty($get_payment_data)) {
                $this->my_response['status'] = true;
                $this->my_response['message'] = lang('common_detail_found');
                $this->my_response['data'] = $get_payment_data;
            } else {
                $this->my_response['status'] = false;
                $this->my_response['message'] = lang('common_detail_not_found');
                $this->my_response['data'] = array();
            }
            $this->send_response();
        } catch (ErrorException $ex) {
            $this->error = $ex->getMessage();
            $this->store_error();
        }
    }

    /**
     * Description :- This function is used delete the tax data
     * 
     * 
     * 
     */
    public function delete_payment_mode_post() {

        try {

            $payment_mode_id = !empty($this->Common_model->escape_data($this->post_data['payment_mode_id'])) ? $this->Common_model->escape_data($this->post_data['payment_mode_id']) : '';
            $doctor_id = !empty($this->Common_model->escape_data($this->post_data['doctor_id'])) ? $this->Common_model->escape_data($this->post_data['doctor_id']) : '';

            if (empty($payment_mode_id) ||
                    empty($doctor_id)
            ) {
                $this->bad_request();
            }

            $get_role_details = $this->Common_model->get_the_role($this->user_id);
            if (!empty($get_role_details['user_role_data'])) {
                $permission_data = array(
                    'role_data' => $get_role_details['user_role_data'],
                    'module' => 16,
                    'key' => 4
                );
                $check_module_permission = $this->check_module_permission($permission_data);
                if ($check_module_permission == 2) {
                    $this->my_response['status'] = false;
                    $this->my_response['message'] = lang('permission_error');
                    $this->send_response();
                }
            }

            //check tax belongs to doctor id or not
            $requested_data = array(
                'payment_mode_id' => $payment_mode_id,
                'payment_mode_doctor_id' => $doctor_id
            );
            $this->check_payment_mode_belongs($requested_data);

            $update_payment_mode = array(
                'payment_mode_status' => 9,
                'payment_mode_updated_at' => $this->utc_time_formated
            );

            $update_payment_mode_where = array(
                'payment_mode_id' => $payment_mode_id
            );

            $is_update = $this->Common_model->update(TBL_PAYMENT_MODE, $update_payment_mode, $update_payment_mode_where);

            if ($is_update > 0) {
                $this->my_response['status'] = true;
                $this->my_response['message'] = lang('payment_deleted');
            } else {
                $this->my_response['status'] = false;
                $this->my_response['message'] = lang('failure');
            }
            $this->send_response();
        } catch (ErrorException $ex) {
            $this->error = $ex->getMessage();
            $this->store_error();
        }
    }

    /**
     * Description :- This function is used to edit the payment mode
     * 
     * 
     * 
     */
    public function edit_payment_mode_post() {

        try {

            $payment_mode_id = !empty($this->Common_model->escape_data($this->post_data['payment_mode_id'])) ? $this->Common_model->escape_data($this->post_data['payment_mode_id']) : '';
            $doctor_id = !empty($this->Common_model->escape_data($this->post_data['doctor_id'])) ? $this->Common_model->escape_data($this->post_data['doctor_id']) : '';
            $payment_mode_name = !empty($this->Common_model->escape_data($this->post_data['payment_mode_name'])) ? $this->Common_model->escape_data($this->post_data['payment_mode_name']) : '';
            $payment_type = !empty($this->Common_model->escape_data($this->post_data['payment_type'])) ? $this->Common_model->escape_data($this->post_data['payment_type']) : '';
            $payment_vendor_fee = !empty($this->Common_model->escape_data($this->post_data['payment_vendor_fee'])) ? $this->Common_model->escape_data($this->post_data['payment_vendor_fee']) : '';

            if (empty($payment_mode_id) ||
                    empty($payment_mode_name) ||
                    empty($payment_type) ||
                    empty($doctor_id) ||
                    empty($payment_vendor_fee)
            ) {
                $this->bad_request();
            }

            $get_role_details = $this->Common_model->get_the_role($this->user_id);
            if (!empty($get_role_details['user_role_data'])) {
                $permission_data = array(
                    'role_data' => $get_role_details['user_role_data'],
                    'module' => 16,
                    'key' => 2
                );
                $check_module_permission = $this->check_module_permission($permission_data);
                if ($check_module_permission == 2) {
                    $this->my_response['status'] = false;
                    $this->my_response['message'] = lang('permission_error');
                    $this->send_response();
                }
            }

            if (!is_numeric($payment_vendor_fee)) {
                $this->my_response['status'] = false;
                $this->my_response['message'] = lang('mycontroller_invalid_request');
                $this->send_response();
            }


            if (validate_percentage($payment_vendor_fee)) {
                $this->my_response['status'] = false;
                $this->my_response['message'] = lang('mycontroller_invalid_request');
                $this->send_response();
            }


            //check tax belongs to doctor id or not
            $requested_data = array(
                'payment_mode_id' => $payment_mode_id,
                'payment_mode_doctor_id' => $doctor_id
            );
            $this->check_payment_mode_belongs($requested_data);


            //check same tax name already added or not
            $where = array(
                'payment_mode_name' => strtolower($payment_mode_name),
                'payment_mode_doctor_id' => $doctor_id,
                'payment_mode_status' => 1,
                'payment_mode_id !=' => $payment_mode_id
            );

            $get_payment_mode_data = $this->Common_model->get_single_row(TBL_PAYMENT_MODE, 'payment_mode_id', $where);

            if (!empty($get_payment_mode_data)) {
                $this->my_response['status'] = false;
                $this->my_response['message'] = lang('payment_name_already_exists');
                $this->send_response();
            }

            $payment_mode_data_update = array(
                'payment_mode_name' => $payment_mode_name,
                'payment_mode_payment_type_id' => $payment_type,
                'payment_mode_vendor_fee' => $payment_vendor_fee,
                'payment_mode_updated_at' => $this->utc_time_formated
            );

            $payment_mode_data_where = array(
                'payment_mode_id' => $payment_mode_id
            );

            $is_update = $this->Common_model->update(TBL_PAYMENT_MODE, $payment_mode_data_update, $payment_mode_data_where);

            if ($is_update > 0) {
                $this->my_response['status'] = true;
                $this->my_response['message'] = lang('payment_updated');
            } else {
                $this->my_response['status'] = false;
                $this->my_response['message'] = lang('failure');
            }
            $this->send_response();
        } catch (ErrorException $ex) {
            $this->error = $ex->getMessage();
            $this->store_error();
        }
    }

    /**
     * Description :- This function is used to check the data belongs to the particular user or not
     * 
     * 
     * 
     * @param type $requested_data
     */
    public function check_payment_mode_belongs($requested_data) {

        $where_array = array(
            'payment_mode_status' => 1,
            'payment_mode_id' => $requested_data['payment_mode_id'],
            'payment_mode_doctor_id' => $requested_data['payment_mode_doctor_id']
        );

        $check_payment_mode_belongs_doctor = $this->Common_model->get_single_row(TBL_PAYMENT_MODE, 'payment_mode_id', $where_array);

        if (empty($check_payment_mode_belongs_doctor)) {
            $this->my_response['status'] = false;
            $this->my_response['message'] = lang('mycontroller_invalid_request');
            $this->send_response();
        }
    }

    /**
     * Description :- This function is used to add the pricing
     * 
     * 
     * 
     */
    public function add_pricing_post() {
        try {

            $tax_id = !empty($this->Common_model->escape_data($this->post_data['tax_id'])) ? $this->Common_model->escape_data($this->post_data['tax_id']) : '';
            $pricing_name = !empty($this->Common_model->escape_data($this->post_data['pricing_name'])) ? $this->Common_model->escape_data($this->post_data['pricing_name']) : '';
            $pricing_cost = !empty($this->Common_model->escape_data($this->post_data['pricing_cost'])) ? $this->Common_model->escape_data($this->post_data['pricing_cost']) : '';
            $pricing_instruction = !empty($this->Common_model->escape_data($this->post_data['pricing_instruction'])) ? $this->Common_model->escape_data($this->post_data['pricing_instruction']) : '';
            $doctor_id = !empty($this->Common_model->escape_data($this->post_data['doctor_id'])) ? $this->Common_model->escape_data($this->post_data['doctor_id']) : '';
            $clinic_id = !empty($this->Common_model->escape_data($this->post_data['clinic_id'])) ? $this->Common_model->escape_data($this->post_data['clinic_id']) : '';

            if (
                    empty($pricing_name) ||
                    empty($pricing_cost) ||
                    empty($clinic_id) ||
                    empty($doctor_id)
            ) {
                $this->bad_request();
            }


            $get_role_details = $this->Common_model->get_the_role($this->user_id);
            if (!empty($get_role_details['user_role_data'])) {
                $permission_data = array(
                    'role_data' => $get_role_details['user_role_data'],
                    'module' => 15,
                    'key' => 1
                );
                $check_module_permission = $this->check_module_permission($permission_data);
                if ($check_module_permission == 2) {
                    $this->my_response['status'] = false;
                    $this->my_response['message'] = lang('permission_error');
                    $this->send_response();
                }
            }


            if (!is_numeric($pricing_cost)) {
                $this->my_response['status'] = false;
                $this->my_response['message'] = lang('mycontroller_invalid_request');
                $this->send_response();
            }


            $pricing_data = array(
                'doctor_id' => $doctor_id,
                'clinic_id' => $clinic_id,
                'pricing_name' => $pricing_name
            );
            $this->check_pricing_name_exists($pricing_data, 2);

            $pricing_data_array = array(
                'pricing_catalog_name' => $pricing_name,
                'pricing_catalog_cost' => $pricing_cost,
                'pricing_catalog_tax_id' => $tax_id,
                'pricing_catalog_doctor_id' => $doctor_id,
                'pricing_catalog_clinic_id' => $clinic_id,
                'pricing_catalog_instructions' => $pricing_instruction,
                'pricing_catalog_created_at' => $this->utc_time_formated
            );

            $inserted_id = $this->Common_model->insert(TBL_PRICING_CATALOG, $pricing_data_array);

            if ($inserted_id > 0) {
                $this->my_response['status'] = true;
                $this->my_response['message'] = lang('pricing_added');
            } else {
                $this->my_response['status'] = false;
                $this->my_response['message'] = lang('failure');
            }

            $this->send_response();
        } catch (ErrorException $ex) {
            $this->error = $ex->getMessage();
            $this->store_error();
        }
    }

    /**
     * Description :- This function is used to add the pricing
     * 
     * 
     * 
     */
    public function edit_pricing_post() {
        try {

            $tax_id = !empty($this->Common_model->escape_data($this->post_data['tax_id'])) ? $this->Common_model->escape_data($this->post_data['tax_id']) : '';
            $pricing_name = !empty($this->Common_model->escape_data($this->post_data['pricing_name'])) ? $this->Common_model->escape_data($this->post_data['pricing_name']) : '';
            $pricing_cost = !empty($this->Common_model->escape_data($this->post_data['pricing_cost'])) ? $this->Common_model->escape_data($this->post_data['pricing_cost']) : '';
            $pricing_instruction = !empty($this->Common_model->escape_data($this->post_data['pricing_instruction'])) ? $this->Common_model->escape_data($this->post_data['pricing_instruction']) : '';
            $doctor_id = !empty($this->Common_model->escape_data($this->post_data['doctor_id'])) ? $this->Common_model->escape_data($this->post_data['doctor_id']) : '';
            $clinic_id = !empty($this->Common_model->escape_data($this->post_data['clinic_id'])) ? $this->Common_model->escape_data($this->post_data['clinic_id']) : '';
            $pricing_id = !empty($this->Common_model->escape_data($this->post_data['pricing_id'])) ? $this->Common_model->escape_data($this->post_data['pricing_id']) : '';

            if (
                    empty($pricing_name) ||
                    empty($pricing_cost) ||
                    empty($doctor_id) ||
                    empty($clinic_id) ||
                    empty($pricing_id)
            ) {
                $this->bad_request();
            }

            $get_role_details = $this->Common_model->get_the_role($this->user_id);
            if (!empty($get_role_details['user_role_data'])) {
                $permission_data = array(
                    'role_data' => $get_role_details['user_role_data'],
                    'module' => 15,
                    'key' => 2
                );
                $check_module_permission = $this->check_module_permission($permission_data);
                if ($check_module_permission == 2) {
                    $this->my_response['status'] = false;
                    $this->my_response['message'] = lang('permission_error');
                    $this->send_response();
                }
            }

            if (!is_numeric($pricing_cost) || !is_numeric($pricing_id)) {
                $this->my_response['status'] = false;
                $this->my_response['message'] = lang('mycontroller_invalid_request');
                $this->send_response();
            }

            $requested_data = array(
                'pricing_id' => $pricing_id,
                'doctor_id' => $doctor_id
            );
            $this->check_pricing_belongs($requested_data);

            $pricing_data = array(
                'doctor_id' => $doctor_id,
                'clinic_id' => $clinic_id,
                'pricing_name' => $pricing_name,
                'pricing_id' => $pricing_id
            );
            $this->check_pricing_name_exists($pricing_data, 1);

            $pricing_data_update = array(
                'pricing_catalog_name' => $pricing_name,
                'pricing_catalog_cost' => $pricing_cost,
                'pricing_catalog_tax_id' => $tax_id,
                'pricing_catalog_doctor_id' => $doctor_id,
                'pricing_catalog_instructions' => $pricing_instruction,
                'pricing_catalog_updated_at' => $this->utc_time_formated
            );

            $pricing_data_update_where = array(
                'pricing_catalog_id' => $pricing_id
            );

            $is_update = $this->Common_model->update(TBL_PRICING_CATALOG, $pricing_data_update, $pricing_data_update_where);

            if ($is_update > 0) {
                $this->my_response['status'] = true;
                $this->my_response['message'] = lang('pricing_updated');
            } else {
                $this->my_response['status'] = false;
                $this->my_response['message'] = lang('failure');
            }

            $this->send_response();
        } catch (ErrorException $ex) {
            $this->error = $ex->getMessage();
            $this->store_error();
        }
    }

    /**
     * Description :- This function is used to check the pricing name exists or not for same doctor
     * 
     * 
     * 
     * @param type $requested_data
     * @param type $is_edit
     */
    public function check_pricing_name_exists($requested_data, $is_edit = 2) {

        $where_array = array(
            'pricing_catalog_doctor_id' => $requested_data['doctor_id'],
            'pricing_catalog_clinic_id' => $requested_data['clinic_id'],
            'pricing_catalog_name' => $requested_data['pricing_name'],
            'pricing_catalog_status' => 1
        );

        if ($is_edit == 1) {
            $where_array['pricing_catalog_id !='] = $requested_data['pricing_id'];
        }

        $get_pricing_data = $this->Common_model->get_single_row(TBL_PRICING_CATALOG, 'pricing_catalog_id', $where_array);

        if (!empty($get_pricing_data)) {
            $this->my_response['status'] = false;
            $this->my_response['message'] = lang('pricing_name_already_exists');
            $this->send_response();
        }
    }

    /**
     * Description :- This function is used to get the pricing of the doctor
     */
    public function get_pricing_post() {
        try {
            $doctor_id = !empty($this->Common_model->escape_data($this->post_data['doctor_id'])) ? $this->Common_model->escape_data($this->post_data['doctor_id']) : '';
            $clinic_id = !empty($this->Common_model->escape_data($this->post_data['clinic_id'])) ? $this->Common_model->escape_data($this->post_data['clinic_id']) : '';
            $search = !empty($this->Common_model->escape_data($this->post_data['search'])) ? $this->Common_model->escape_data($this->post_data['search']) : '';

            if (empty($doctor_id)) {
                $this->bad_request();
            }

            $get_role_details = $this->Common_model->get_the_role($this->user_id);
            if (!empty($get_role_details['user_role_data'])) {
                $permission_data = array(
                    'role_data' => $get_role_details['user_role_data'],
                    'module' => 15,
                    'key' => 3
                );
                $check_module_permission = $this->check_module_permission($permission_data);
                if ($check_module_permission == 2) {
                    $this->my_response['status'] = false;
                    $this->my_response['message'] = lang('permission_error');
                    $this->send_response();
                }
            }

            $requested_data = array(
                'doctor_id' => $doctor_id,
                'clinic_id' => $clinic_id,
                'search' => $search
            );

            $get_pricing_data = $this->billing->get_pricing($requested_data);

            if (!empty($get_pricing_data)) {
                $this->my_response['status'] = true;
                $this->my_response['message'] = lang('common_detail_found');
                $this->my_response['data'] = $get_pricing_data;
            } else {
                $this->my_response['status'] = false;
                $this->my_response['message'] = lang('common_detail_not_found');
                $this->my_response['data'] = array();
            }
            $this->send_response();
        } catch (ErrorException $ex) {
            $this->error = $ex->getMessage();
            $this->store_error();
        }
    }

    /**
     * Description :- This function is used to delete the pricing of the doctor
     * 
     * 
     * 
     */
    public function delete_pricing_post() {

        try {

            $doctor_id = !empty($this->Common_model->escape_data($this->post_data['doctor_id'])) ? $this->Common_model->escape_data($this->post_data['doctor_id']) : '';
            $pricing_id = !empty($this->Common_model->escape_data($this->post_data['pricing_id'])) ? $this->Common_model->escape_data($this->post_data['pricing_id']) : '';

            if (empty($doctor_id) ||
                    empty($pricing_id)
            ) {
                $this->bad_request();
            }

            $get_role_details = $this->Common_model->get_the_role($this->user_id);
            if (!empty($get_role_details['user_role_data'])) {
                $permission_data = array(
                    'role_data' => $get_role_details['user_role_data'],
                    'module' => 15,
                    'key' => 4
                );
                $check_module_permission = $this->check_module_permission($permission_data);
                if ($check_module_permission == 2) {
                    $this->my_response['status'] = false;
                    $this->my_response['message'] = lang('permission_error');
                    $this->send_response();
                }
            }

            $requested_data = array(
                'pricing_id' => $pricing_id,
                'doctor_id' => $doctor_id
            );
            $this->check_pricing_belongs($requested_data);

            $update_pricing = array(
                'pricing_catalog_status' => 9,
                'pricing_catalog_updated_at' => $this->utc_time_formated
            );

            $update_pricing_where = array(
                'pricing_catalog_doctor_id' => $doctor_id,
                'pricing_catalog_id' => $pricing_id
            );

            $is_update = $this->Common_model->update(TBL_PRICING_CATALOG, $update_pricing, $update_pricing_where);

            if ($is_update > 0) {
                $this->my_response['status'] = true;
                $this->my_response['message'] = lang('pricing_deleted');
            } else {
                $this->my_response['status'] = false;
                $this->my_response['message'] = lang('failure');
            }
            $this->send_response();
        } catch (ErrorException $ex) {
            $this->error = $ex->getMessage();
            $this->store_error();
        }
    }

    /**
     * Description :- This function is used to pricing belongs to the particular doctor or not
     * 
     * 
     * 
     * @param type $requested_data
     */
    public function check_pricing_belongs($requested_data) {

        $where_array = array(
            'pricing_catalog_id' => $requested_data['pricing_id'],
            'pricing_catalog_doctor_id' => $requested_data['doctor_id'],
            'pricing_catalog_status' => 1
        );

        $get_pricing_data = $this->Common_model->get_single_row(TBL_PRICING_CATALOG, 'pricing_catalog_id', $where_array);

        if (empty($get_pricing_data)) {
            $this->my_response['status'] = false;
            $this->my_response['message'] = lang('mycontroller_invalid_request');
            $this->send_response();
        }
    }

    /**
     * Description :- This function is used to get the value of the tax 
     * based on the id
     * 
     * 
     * 
     * @param type $requested_data
     * @return type
     */
    public function get_tax_value($requested_data) {
        $get_tax_data = $this->billing->get_tax_value($requested_data);
        return $get_tax_data['tax_value'];
    }

    /**
     * Description :- This function is used to calculate the cost 
     * after applying all the taxes
     * 
     * 
     * 
     * @param type $requested_data
     * @return int
     */
    public function cost_after_tax($requested_data) {

        $basic_cost_with_tax = 0;
        $tax_amount = 0;
        $total_tax = 0;

        if (!empty($requested_data['tax_value'])) {
            $tax_value = explode(',', $requested_data['tax_value']);
            foreach ($tax_value as $value) {
                $total_tax = $total_tax + $value;
            }
            $tax_amount = (($requested_data['cost']) * $total_tax) / 100;
            $basic_cost_with_tax = ($requested_data['cost']) + $tax_amount;
            $return_array = array(
                'tax_amount' => $tax_amount,
                'basic_cost_with_tax' => $basic_cost_with_tax
            );
        } else {
            $return_array = array(
                'tax_amount' => 0,
                'basic_cost_with_tax' => $requested_data['cost']
            );
        }

        return $return_array;
    }

    /**
     * Description :- This function is used to add the bill 
     * for the patient by the doctor
     * 
     * 
     * 
     */
    public function add_billing_post() {
        try {
            $appointment_id = !empty($this->Common_model->escape_data($this->post_data['appointment_id'])) ? $this->Common_model->escape_data($this->post_data['appointment_id']) : '';
            $clinic_id = !empty($this->Common_model->escape_data($this->post_data['clinic_id'])) ? $this->Common_model->escape_data($this->post_data['clinic_id']) : '';
            $patient_id = !empty($this->Common_model->escape_data($this->post_data['patient_id'])) ? $this->Common_model->escape_data($this->post_data['patient_id']) : '';
            $total_discount = !empty($this->Common_model->escape_data($this->post_data['total_discount'])) ? $this->Common_model->escape_data($this->post_data['total_discount']) : '';
            $total_tax = !empty($this->Common_model->escape_data($this->post_data['total_tax'])) ? $this->Common_model->escape_data($this->post_data['total_tax']) : '';
            $grand_total = !empty($this->Common_model->escape_data($this->post_data['grand_total'])) ? $this->Common_model->escape_data($this->post_data['grand_total']) : '';
            $mode_of_payment_id = !empty($this->Common_model->escape_data($this->post_data['mode_of_payment_id'])) ? $this->Common_model->escape_data($this->post_data['mode_of_payment_id']) : '';
            $total_payable = !empty($this->Common_model->escape_data($this->post_data['total_payable'])) ? $this->Common_model->escape_data($this->post_data['total_payable']) : '';
            $advance_amount = !empty($this->Common_model->escape_data($this->post_data['advance_amount'])) ? $this->Common_model->escape_data($this->post_data['advance_amount']) : '';
            $paid_amount = !empty($this->Common_model->escape_data($this->post_data['paid_amount'])) ? $this->Common_model->escape_data($this->post_data['paid_amount']) : '';
            $user_type = !empty($this->Common_model->escape_data($this->post_data['user_type'])) ? $this->Common_model->escape_data($this->post_data['user_type']) : '';
            $doctor_id = !empty($this->Common_model->escape_data($this->post_data['doctor_id'])) ? $this->Common_model->escape_data($this->post_data['doctor_id']) : '';
            $invoice_no = !empty($this->Common_model->escape_data($this->post_data['invoice_no'])) ? $this->Common_model->escape_data($this->post_data['invoice_no']) : '';
            $billing_id = !empty($this->Common_model->escape_data($this->post_data['billing_id'])) ? $this->Common_model->escape_data($this->post_data['billing_id']) : '';
            $invoice_date = !empty($this->Common_model->escape_data($this->post_data['invoice_date'])) ? $this->Common_model->escape_data($this->post_data['invoice_date']) : date("Y-m-d");
            
            $payment_json = $this->post_data['payment_json'];
            $payment_json = json_decode($payment_json, true);

            if (empty($appointment_id) ||
                    empty($clinic_id) ||
                    empty($patient_id) ||
                    empty($payment_json) ||
                    empty($grand_total) ||
                    empty($mode_of_payment_id) ||
                    empty($total_payable) || 
					empty($doctor_id)
            ) {
                $this->bad_request();
            }

            $add_bill_permission = 1;
            $edit_bill_permisstion = 1;

            $get_role_details = $this->Common_model->get_the_role($this->user_id);
            if (!empty($get_role_details['user_role_data'])) {
                $permission_data = array(
                    'role_data' => $get_role_details['user_role_data'],
                    'module' => 10
                );

                $permission_data['key'] = 1;
                $check_add_permission = $this->check_module_permission($permission_data);
                if ($check_add_permission == 2) {
                    $add_bill_permission = 2;
                }

                $permission_data['key'] = 2;
                $check_edit_permission = $this->check_module_permission($permission_data);
                if ($check_edit_permission == 2) {
                    $edit_bill_permisstion = 2;
                }
            }
			
            //check mode of payment id belongs to the doctor or not
            $payment_mode_where = array(
                'payment_mode_doctor_id' => $doctor_id,
                'payment_mode_status' => 1,
                'payment_mode_id' => $mode_of_payment_id
            );
            $check_payment_mode = $this->Common_model->get_single_row(TBL_PAYMENT_MODE, 'payment_mode_id, payment_mode_vendor_fee', $payment_mode_where);
            if (empty($check_payment_mode)) {
                $this->my_response['status'] = false;
                $this->my_response['message'] = lang('mycontroller_invalid_request');
                $this->send_response();
            }

            //check doctor generate the bill appointment id belongs to or not
            $appointment_where = array(
                'appointment_doctor_user_id' => $doctor_id,
                'appointment_clinic_id' => $clinic_id,
                'appointment_user_id' => $patient_id,
                'appointment_id' => $appointment_id
            );
            $result = $this->Common_model->get_single_row(TBL_APPOINTMENTS, 'appointment_id', $appointment_where);

            if (empty($result)) {
                $this->my_response['status'] = false;
                $this->my_response['message'] = lang('mycontroller_invalid_request');
                $this->send_response();
            }
			
            $calculate_grand_total = 0;
            $calculate_total_tax = 0;
            $calculate_total_discount = 0;
            foreach ($payment_json as $payment) {
                if ($payment['is_delete'] == 2) {
                    $discount = 0;
                    $basic_cost = 0;
                    $get_tax_value = 0;
                    if(!empty($payment['tax_id'])){
                        $tax_data = array(
                            'doctor_id' => $doctor_id,
                            'tax_id' => $payment['tax_id']
                        );
                        $get_tax_value = $this->get_tax_value($tax_data);
                    }
                    $cost = $payment['cost'] * $payment['unit'];
					
                    //1=PRICE 2=PERCENTAGE
                    if ($payment['discount_type'] == 2) {
                        $discount = $payment['discount'];
                        $basic_cost = $cost - $discount;
                    } else {
                        $discount = ( $cost * $payment['discount'] ) / 100;
                        $basic_cost = $cost - $discount;
                    }
					
                    if (!empty($get_tax_value)) {
                        $cost_with_tax = array(
                            'cost' => $basic_cost,
                            'tax_value' => $get_tax_value
                        );
                        $cost_after_tax_array = $this->cost_after_tax($cost_with_tax);
                        $cost_after_tax = $cost_after_tax_array['basic_cost_with_tax'];
                        $calculate_total_tax = $calculate_total_tax + $cost_after_tax_array['tax_amount'];
                    } else {
                        $cost_after_tax = $basic_cost;
                    }

                    $calculate_grand_total = $calculate_grand_total + $cost_after_tax;
                    $calculate_total_discount = $calculate_total_discount + $discount;
                }
            }

			//$calculate_grand_total 	  = ((float)$calculate_grand_total - (float)$calculate_total_discount);
            $calculate_grand_total    = number_format((float)$calculate_grand_total, 2, '.', '');
            $calculate_total_discount = number_format((float)$calculate_total_discount, 2, '.', '');
			$calculate_total_tax 	  = number_format((float)$calculate_total_tax, 2, '.', '');

            //after calulcate the total price now caluculating the total payable amount with
            //payment mode taxes
            $payable_amount = ($calculate_grand_total * $check_payment_mode['payment_mode_vendor_fee']) / 100;
            $calculate_total_payable = $payable_amount + $calculate_grand_total;
            $calculate_total_payable = round(number_format((float) $calculate_total_payable, 2, '.', ''));
			
			/* echo ' grand total ' . $grand_total . " calculate grand total " . $calculate_grand_total . "<br />";
			echo ' discount total ' . $total_discount . ' calculate discount ' . $calculate_total_discount . "<br />";
			echo ' total tax ' . $total_tax . ' calculate total tax ' . $calculate_total_tax . "<br />";
			echo ' total payable ' . $total_payable . ' calculate payable ' . $calculate_total_payable . "<br />";
			exit; */
			
            if ($calculate_grand_total != $grand_total || 
				$calculate_total_discount != $total_discount || 
				$calculate_total_tax != $total_tax || 
				$calculate_total_payable != $total_payable) {
                $this->my_response['status'] = false;
                $this->my_response['message'] = lang('price_mismatch');
                $this->send_response();
            }

            $this->db->trans_start();

            // $get_bill_detail = $this->Common_model->get_single_row(TBL_BILLING, 'billing_id', $bill_where);
            if (!empty($billing_id) &&
                    $edit_bill_permisstion == 1
            ) {
                $bill_where = array(
                    'billing_id' => $billing_id,
                    'billing_status' => 1
                );
                $update_billing_array = array(
                    'billing_payment_mode_id' => $mode_of_payment_id,
                    'billing_discount' => $total_discount,
                    'billing_tax' => $total_tax,
                    'billing_grand_total' => $grand_total,
                    'billing_total_payable' => $total_payable,
                    'billing_advance_amount' => $advance_amount,
                    'billing_paid_amount' => $paid_amount,
                    'billing_invoice_date' => $invoice_date,
                    'billing_modified_at' => $this->utc_time_formated
                );
                $this->Common_model->update(TBL_BILLING, $update_billing_array, $bill_where);
                $inserted_id = $billing_id;
            } else if ($add_bill_permission == 1) { 
                /*add invoice number*/
                $invoice_where = array('doctor_id' => $doctor_id);
                $get_bill_detail = $this->Common_model->get_single_row(TBL_INVOICE_DETAIL, '*', $invoice_where);
                if(!empty($get_bill_detail)){
                    $invoiceId   = $get_bill_detail['id'];
                    $inv_prefix  = $get_bill_detail['inv_prefix'];
                    $inv_counter = $get_bill_detail['inv_counter']+1;
                }
				
				if(empty($invoice_no))
					$invoice_no = $inv_prefix.''.$inv_counter;
				
                $billing_array = array(
                    'billing_appointment_id' => $appointment_id,
                    'billing_user_id' => $patient_id,
                    'billing_doctor_user_id' => $doctor_id,
                    'billing_clinic_id' => $clinic_id,
                    'billing_payment_mode_id' => $mode_of_payment_id,
                    'billing_discount' => $total_discount,
                    'billing_tax' => $total_tax,
                    'billing_grand_total' => $grand_total,
                    'billing_total_payable' => $total_payable,
                    'billing_advance_amount' => $advance_amount,
                    'billing_paid_amount' => $paid_amount,
                    'billing_invoice_date' => $invoice_date,
                    'billing_created_at' => $this->utc_time_formated,
                    'invoice_number' => $invoice_no
                );
                $inserted_id = $this->Common_model->insert(TBL_BILLING, $billing_array);
                if($inserted_id > 0){
                    /*update payment state*/
                    $update_appointments_where = array('appointment_id' => $appointment_id);
                    $update_appointments_details = array('appointment_payment_state' => 1);
                    $this->Common_model->update(TBL_APPOINTMENTS, $update_appointments_details, $update_appointments_where);
					
                    /*update invoice details*/
                    $update_invoice_where = array('id' => $invoiceId);
                    $update_invoice_details = array('inv_counter' => $inv_counter);
                    $this->Common_model->update(TBL_INVOICE_DETAIL, $update_invoice_details, $update_invoice_where);
                }
            }
			
            if ($inserted_id > 0) {
                $billing_details = array();
                foreach ($payment_json as $payment) {
                    if (!empty($payment['id']) && $payment['is_delete'] == 2) {
                        $update_billing_details = array(
                            'billing_detail_name' => $payment['treatment_name'],
                            'billing_detail_unit' => $payment['unit'],
                            'billing_detail_basic_cost' => $payment['cost'],
                            'billing_detail_discount' => $payment['discount'],
                            'billing_detail_discount_type' => $payment['discount_type'],
                            'billing_detail_tax_id' => $payment['tax_id'],
                            'billing_detail_total' => $payment['amount'],
                            'billing_detail_tax' => $payment['tax_value'],
                            'billing_detail_modified_at' => $this->utc_time_formated
                        );
                        $update_billing_where = array(
                            'billing_detail_id' => $payment['id']
                        );
                        $this->Common_model->update(TBL_BILLING_DETAILS, $update_billing_details, $update_billing_where);
                    } else if (!empty($payment['id']) && $payment['is_delete'] == 1) {
                        $delete_billing_details = array(
                            'billing_detail_modified_at' => $this->utc_time_formated,
                            'billing_detail_status' => 9
                        );
                        $delete_billing_where = array(
                            'billing_detail_id' => $payment['id']
                        );
                        $this->Common_model->update(TBL_BILLING_DETAILS, $delete_billing_details, $delete_billing_where);
                    } else {
                        $insert_billing_details = array(
                            'billing_detail_billing_id' => $inserted_id,
                            'billing_detail_name' => $payment['treatment_name'],
                            'billing_detail_unit' => $payment['unit'],
                            'billing_detail_basic_cost' => $payment['cost'],
                            'billing_detail_discount' => $payment['discount'],
                            'billing_detail_discount_type' => $payment['discount_type'],
                            'billing_detail_tax_id' => $payment['tax_id'],
                            'billing_detail_total' => $payment['amount'],
                            'billing_detail_tax' => $payment['tax_value'],
                            'billing_detail_created_at' => $this->utc_time_formated
                        );
                        $this->Common_model->insert(TBL_BILLING_DETAILS, $insert_billing_details);
                    }
                }
                if ($this->db->trans_status() !== FALSE) {
                    $this->db->trans_commit();
                    $this->my_response['status'] = true;
                    $this->my_response['message'] = lang('bill_added');
                } else {
                    $this->db->trans_rollback();
                    $this->my_response['status'] = false;
                    $this->my_response['message'] = lang('failure');
                }
            } else if ($edit_bill_permisstion == 2 ||
                    $add_bill_permission == 2
            ) {
                $this->my_response['status'] = false;
                $this->my_response['message'] = lang('permission_error');
            } else {
                $this->my_response['status'] = false;
                $this->my_response['message'] = lang('failure');
            }
            $this->send_response();
        } catch (ErrorException $ex) {
            $this->error = $ex->getMessage();
            $this->store_error();
        }
    }

    /**
     * Description :- This function is used to get the billing 
     * details of the patient based on the appointment id
     */
    public function get_billing_post() {
        try {
            $appointment_id = !empty($this->Common_model->escape_data($this->post_data['appointment_id'])) ? $this->Common_model->escape_data($this->post_data['appointment_id']) : '';
            $billing_id = !empty($this->Common_model->escape_data($this->post_data['billing_id'])) ? $this->Common_model->escape_data($this->post_data['billing_id']) : '';
            $clinic_id = !empty($this->Common_model->escape_data($this->post_data['clinic_id'])) ? $this->Common_model->escape_data($this->post_data['clinic_id']) : '';
            $patient_id = !empty($this->Common_model->escape_data($this->post_data['patient_id'])) ? $this->Common_model->escape_data($this->post_data['patient_id']) : '';
			$doctor_id = !empty($this->Common_model->escape_data($this->post_data['doctor_id'])) ? $this->Common_model->escape_data($this->post_data['doctor_id']) : '';


            if (empty($appointment_id) ||
                    empty($clinic_id) ||
                    empty($billing_id) ||
                    empty($patient_id)
            ) {
                $this->bad_request();
            }
            $this->my_response['is_import_data'] = false;
            $get_role_details = $this->Common_model->get_the_role($this->user_id);
            if (!empty($get_role_details['user_role_data'])) {
                $permission_data = array(
                    'role_data' => $get_role_details['user_role_data'],
                    'module' => 10,
                    'key' => 3
                );
                $check_module_permission = $this->check_module_permission($permission_data);
                if ($check_module_permission == 2) {
                    $this->my_response['status'] = false;
                    $this->my_response['message'] = lang('permission_error');
                    $this->send_response();
                }
            }

            //check doctor generate the bill appointment id belongs to or not
            $appointment_where = array(
                'appointment_doctor_user_id' => (!empty($doctor_id)) ? $doctor_id : $this->user_id,
                'appointment_clinic_id' => $clinic_id,
                'appointment_user_id' => $patient_id,
                'appointment_id' => $appointment_id
            );
            $result = $this->Common_model->get_single_row(TBL_APPOINTMENTS, 'appointment_id', $appointment_where);

            if (empty($result)) {
                $this->my_response['status'] = false;
                $this->my_response['message'] = lang('mycontroller_invalid_request');
                $this->send_response();
            }
			
			$doctor_id = (!empty($doctor_id)) ? $doctor_id : $this->user_id;
            $requested_data = array(
                'appointment_id' => $appointment_id,
                'patient_id' => $patient_id,
                'doctor_id' => $doctor_id,
                'billing_id' => $billing_id,
            );
            $get_billing_data = $this->billing->get_billing_information_for_doctor($requested_data);
			
			if (!empty($get_billing_data)) {
                if($get_billing_data[0]['billing_is_import']) 
                    $this->my_response['is_import_data'] = true;
                $this->my_response['status'] = true;
                $this->my_response['message'] = lang('common_detail_found');
                $this->my_response['data'] = $get_billing_data;
            } else {
                $this->my_response['status'] = false;
                $this->my_response['message'] = lang('common_detail_not_found');
				/* [START] SENDING THE INVOICE NO DETAILS FOR NEW INVOICE */
				$billing_invoice_no_data = $this->billing->get_user_invoiceno_details($doctor_id);
				$this->my_response['billing_invoice_no_data'] = $billing_invoice_no_data;
				/* [END] */
            }
            $this->send_response();
        } catch (ErrorException $ex) {
            $this->error = $ex->getMessage();
            $this->store_error();
        }
    }

    public function get_invoice_list_post() {
        try {
            $appointment_id = !empty($this->Common_model->escape_data($this->post_data['appointment_id'])) ? $this->Common_model->escape_data($this->post_data['appointment_id']) : '';
            $clinic_id = !empty($this->Common_model->escape_data($this->post_data['clinic_id'])) ? $this->Common_model->escape_data($this->post_data['clinic_id']) : '';
            $patient_id = !empty($this->Common_model->escape_data($this->post_data['patient_id'])) ? $this->Common_model->escape_data($this->post_data['patient_id']) : '';
            $doctor_id = !empty($this->Common_model->escape_data($this->post_data['doctor_id'])) ? $this->Common_model->escape_data($this->post_data['doctor_id']) : '';


            if (empty($appointment_id) ||
                    empty($clinic_id) ||
                    empty($patient_id)
            ) {
                $this->bad_request();
            }
            $this->my_response['is_import_data'] = false;
            $get_role_details = $this->Common_model->get_the_role($this->user_id);
            if (!empty($get_role_details['user_role_data'])) {
                $permission_data = array(
                    'role_data' => $get_role_details['user_role_data'],
                    'module' => 10,
                    'key' => 3
                );
                $check_module_permission = $this->check_module_permission($permission_data);
                if ($check_module_permission == 2) {
                    $this->my_response['status'] = false;
                    $this->my_response['message'] = lang('permission_error');
                    $this->send_response();
                }
            }

            //check doctor generate the bill appointment id belongs to or not
            $appointment_where = array(
                'appointment_doctor_user_id' => (!empty($doctor_id)) ? $doctor_id : $this->user_id,
                'appointment_clinic_id' => $clinic_id,
                'appointment_user_id' => $patient_id,
                'appointment_id' => $appointment_id
            );
            $result = $this->Common_model->get_single_row(TBL_APPOINTMENTS, 'appointment_id', $appointment_where);

            if (empty($result)) {
                $this->my_response['status'] = false;
                $this->my_response['message'] = lang('mycontroller_invalid_request');
                $this->send_response();
            }
            
            $doctor_id = (!empty($doctor_id)) ? $doctor_id : $this->user_id;
            $requested_data = array(
                'appointment_id' => $appointment_id,
                'patient_id' => $patient_id,
                'doctor_id' => $doctor_id,
            );
            $get_billing_data = $this->billing->get_invoices($requested_data);
            if($get_billing_data[0]->billing_is_import) 
                $this->my_response['is_import_data'] = true;
            $this->my_response['status'] = true;
            $this->my_response['message'] = lang('common_detail_found');
            $this->my_response['data'] = $get_billing_data;
            $billing_invoice_no_data = $this->billing->get_user_invoiceno_details($doctor_id);
            $this->my_response['billing_invoice_no_data'] = $billing_invoice_no_data;
            $this->send_response();
        } catch (ErrorException $ex) {
            $this->error = $ex->getMessage();
            $this->store_error();
        }
    }

    public function delete_invoice_post() {
        try {
            $billing_id = !empty($this->Common_model->escape_data($this->post_data['billing_id'])) ? $this->Common_model->escape_data($this->post_data['billing_id']) : '';
            $doctor_id = !empty($this->Common_model->escape_data($this->post_data['doctor_id'])) ? $this->Common_model->escape_data($this->post_data['doctor_id']) : '';


            if (empty($billing_id)) {
                $this->bad_request();
            }
            $get_role_details = $this->Common_model->get_the_role($this->user_id);
            if (!empty($get_role_details['user_role_data'])) {
                $permission_data = array(
                    'role_data' => $get_role_details['user_role_data'],
                    'module' => 10,
                    'key' => 4
                );
                $check_module_permission = $this->check_module_permission($permission_data);
                if ($check_module_permission == 2) {
                    $this->my_response['status'] = false;
                    $this->my_response['message'] = lang('permission_error');
                    $this->send_response();
                }
            }
            $bill_where = array(
                'billing_id' => $billing_id,
                'billing_status' => 1
            );
            $update_billing_array = array(
                'billing_status' => 9,
                'billing_modified_at' => $this->utc_time_formated
            );
            $this->Common_model->update(TBL_BILLING, $update_billing_array, $bill_where);
            $delete_billing_details = array(
                'billing_detail_modified_at' => $this->utc_time_formated,
                'billing_detail_status' => 9
            );
            $delete_billing_where = array(
                'billing_detail_billing_id' => $billing_id,
                'billing_detail_status' => 1
            );
            $this->Common_model->update(TBL_BILLING_DETAILS, $delete_billing_details, $delete_billing_where);
            $this->my_response['status'] = true;
            $this->my_response['message'] = lang('bill_deleted');
            $this->send_response();
        } catch (ErrorException $ex) {
            $this->error = $ex->getMessage();
            $this->store_error();
        }
    }

    /**
     * Description :- This function is used to get the billing list of the patient
     * 
     * 
     * 
     */
    public function get_patient_billing_post() {

        try {
            $patient_id = !empty($this->Common_model->escape_data($this->post_data['patient_id'])) ? $this->Common_model->escape_data($this->post_data['patient_id']) : '';

            if (empty($patient_id)) {
                $this->bad_request();
            }

            $requested_data = array(
                'patient_id' => $patient_id
            );

            $get_patient_billing_data = $this->billing->get_billing_information_for_patient($requested_data);

            if (!empty($get_patient_billing_data)) {
                $this->my_response['status'] = true;
                $this->my_response['message'] = lang('common_detail_found');
                $this->my_response['data'] = $get_patient_billing_data;
            } else {
                $this->my_response['status'] = false;
                $this->my_response['message'] = lang('common_detail_not_found');
            }
            $this->send_response();
        } catch (ErrorException $ex) {
            $this->error = $ex->getMessage();
            $this->store_error();
        }
    }

    /**
     * Description :- This function is used to get the billing list of the patient
     * 
     * 
     * 
     */
    public function get_billing_detail_post() {

        try {

            $patient_id = !empty($this->Common_model->escape_data($this->post_data['patient_id'])) ? $this->Common_model->escape_data($this->post_data['patient_id']) : '';
            $billing_id = !empty($this->Common_model->escape_data($this->post_data['billing_id'])) ? $this->Common_model->escape_data($this->post_data['billing_id']) : '';

            if (empty($patient_id) ||
                    empty($billing_id)
            ) {
                $this->bad_request();
            }

            $columns = 'billing_discount,
                        billing_tax,
                        billing_grand_total,
                        billing_total_payable,
                        billing_advance_amount,
                        billing_paid_amount';


            $billing_where = array(
                'billing_user_id' => $patient_id,
                'billing_id' => $billing_id,
                'billing_status !=' => 9
            );

            $get_patient_billing_data = $this->Common_model->get_single_row(TBL_BILLING, $columns, $billing_where);

            if (!empty($get_patient_billing_data)) {

                $billing_detail_column = 'billing_detail_name,
                                          billing_detail_unit,
                                          billing_detail_basic_cost,
                                          billing_detail_discount,
                                          billing_detail_discount_type,
                                          billing_detail_total';

                //get the detail of the billing
                $billing_detail_where = array(
                    'billing_detail_billing_id' => $billing_id,
                    'billing_detail_status !=' => 9
                );

                $get_billing_detail = $this->Common_model->get_all_rows(TBL_BILLING_DETAILS, $billing_detail_column, $billing_detail_where);

                $get_patient_billing_data['billing_detail'] = $get_billing_detail;
                $this->my_response['status'] = true;
                $this->my_response['message'] = lang('common_detail_found');
                $this->my_response['data'] = $get_patient_billing_data;
            } else {
                $this->my_response['status'] = false;
                $this->my_response['message'] = lang('common_detail_not_found');
            }
            $this->send_response();
        } catch (ErrorException $ex) {
            $this->error = $ex->getMessage();
            $this->store_error();
        }
    }

}
