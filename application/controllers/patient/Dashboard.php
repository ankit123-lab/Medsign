<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Dashboard extends MY_Controller {

    public function __construct() {

        parent::__construct();
        $this->load->library('patient_auth');
        $this->load->model('patient_model','patient');
        $this->load->library('form_validation');
        if (!$this->patient_auth->is_logged_in()) {
            redirect('patient/login');
        }
    }

    public function index() {
        $view_data = array();
        $view_data['breadcrumbs'] = "Dashboard";
        $view_data['page_title'] = "Dashboard";
        $this->load->view('patient/dashboard_view', $view_data);
    }
}