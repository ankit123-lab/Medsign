<?php

/**
 * Description of Error_404
 *
 * Date : 2016-09-26
 */
class Errors extends MY_Controller {

    public function __construct() {
        parent::__construct();
    }

    /**
     * 
     * This function is use for display custom error page
     * 
     
     * Modified Date :- 2016-11-30
     */
    public function index() {
        $this->load->view('errors/html/error_general');
    }

    /**
     * 
     * This function is use for display 404 page
     * 
     
     * Modified Date :- 2016-11-30
     * 
     */
    public function error_404() {
        $data['page_title'] = '404 Not Found!';
        $this->load->view('errors/html/error_header', $data);
        $this->load->view('errors/html/error_404');
        $this->load->view('errors/html/error_footer');
    }
}