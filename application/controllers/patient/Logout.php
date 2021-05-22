<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Logout extends MY_Controller {

    public function __construct() {

        parent::__construct();
        $this->load->library('session');
        $this->load->library('patient_auth');
        $this->load->model('patient_model','patient');
        $this->load->library('form_validation');
    }

    public function index() {
        $this->patient_auth->logout();
        redirect('patient/login');
    }

}