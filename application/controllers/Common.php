<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * 
 * This controller use for common apis
 * 
 
 * 
 * Modified Date :- 2016-09-15
 * 
 */
class Common extends CI_Controller {

    public function __construct() {
        parent::__construct();
    }

    /**
     * This function use for show test page view for testing apis
     * 
     
     * 
     * Modified Date :- 2016-09-15
     * 
     */
    public function index() {
        echo "<h1 style='text-align:center'>Comming Soon....</h1>";
    }

    /**
     * 
     * This function use for testing push notification fot both device
     * 
     
     * 
     * Modified Date :- 2017-02-17
     * 
     */
    public function not_found_response() {
        //404 page
    }

    public function get_all_countries() {
        $response = array();
        $id = !empty($this->input->post('id')) && is_numeric($this->input->post('id')) ? $this->input->post('id') : 0;
        $result = $this->Common_model->get_all_countries($id);
        if (!$this->input->is_ajax_request()) {
            return $result;
        } else {
            if (!empty($result) && count($result) > 0) {
                $response['success'] = true;
                $response['result_list'] = $result;
                $response['message'] = lang("common_select_country");
            } else {
                $response['success'] = false;
                $response['message'] = lang("common_no_country_found");
            }
            $this->output->set_content_type('application/json');
            $this->output->set_output(json_encode($response));
        }
    }

    public function get_all_states() {
        $response = array();
        $id = !empty($this->input->post('id')) && is_numeric($this->input->post('id')) ? $this->input->post('id') : 0;
        $result = $this->Common_model->get_all_states($id);

        if (!$this->input->is_ajax_request()) {
            return $result;
        } else {
            if (!empty($result) && count($result) > 0) {
                $response['success'] = true;
                $response['result_list'] = $result;
                $response['message'] = lang("common_select_state");
            } else {
                $response['success'] = false;
                $response['message'] = lang("common_no_state_found");
            }
            $this->output->set_content_type('application/json');
            $this->output->set_output(json_encode($response));
        }
    }

    public function get_all_cities() {
        $response = array();
        $id = !empty($this->input->post('id')) && is_numeric($this->input->post('id')) ? $this->input->post('id') : 0;
        $result = $this->Common_model->get_all_cities($id);

        if (!$this->input->is_ajax_request()) {
            return $result;
        } else {
            if (!empty($result) && count($result) > 0) {
                $response['success'] = true;
                $response['result_list'] = $result;
                $response['message'] = lang("common_select_city");
            } else {
                $response['success'] = false;
                $response['message'] = lang("common_no_city_found");
            }
            $this->output->set_content_type('application/json');
            $this->output->set_output(json_encode($response));
        }
    }
}