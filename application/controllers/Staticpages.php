<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * 
 * This controller use for common apis
 * 
 * @author Dipesh Shihora
 * 
 * Modified Date :- 2018-08-22
 * 
 */
class Staticpages extends CI_Controller {

    public function __construct() {
        parent::__construct();
    }

    public function about_us() {
        $get_contact_us_where = array(
            "static_page_title" => "about_us",
            "static_page_status" => 1
        );
        $get_contact_us = $this->Common_model->get_single_row(TBL_STATIC_PAGE, "static_page_content", $get_contact_us_where);
        $html_content = "<h1 style='text-align:center'>Comming Soon....</h1>";
        if (!empty($get_contact_us)) {
            $html_content = $get_contact_us['static_page_content'];
        }


        $view_data = array(
            "html_content" => $html_content
        );

        $this->load->view('staticpages/header');
        $this->load->view('staticpages/index', $view_data);
        $this->load->view('staticpages/footer');
    }
    public function plan() {
        
        $html_content = "<h1 style='text-align:center'>Comming Soon....</h1>";
      
        $view_data = array(
            "html_content" => $html_content
        );

        $this->load->view('staticpages/header');
        $this->load->view('staticpages/index', $view_data);
        $this->load->view('staticpages/footer');
    }

    public function contact_us() {
        $get_contact_us_where = array(
            "static_page_title" => "contact_us",
            "static_page_status" => 1
        );
        $get_contact_us = $this->Common_model->get_single_row(TBL_STATIC_PAGE, "static_page_content", $get_contact_us_where);
        $html_content = "<h1 style='text-align:center'>Comming Soon....</h1>";
        if (!empty($get_contact_us)) {
            $html_content = $get_contact_us['static_page_content'];
        }


        $view_data = array(
            "html_content" => $html_content
        );

        $this->load->view('staticpages/header');
        $this->load->view('staticpages/index', $view_data);
        $this->load->view('staticpages/footer');
    }

    public function terms_conditions() {
        $get_contact_us_where = array(
            "static_page_title" => "terms_conditions",
            "static_page_status" => 1
        );
        $get_contact_us = $this->Common_model->get_single_row(TBL_STATIC_PAGE, "static_page_content", $get_contact_us_where);
        $html_content = "<h1 style='text-align:center'>Comming Soon....</h1>";
        if (!empty($get_contact_us)) {
            $html_content = $get_contact_us['static_page_content'];
        }


        $view_data = array(
            "html_content" => $html_content
        );

        $this->load->view('staticpages/header');
        $this->load->view('staticpages/index', $view_data);
        $this->load->view('staticpages/footer');
    }

    public function privacy_policy() {
        $get_contact_us_where = array(
            "static_page_title" => "privacy_policy",
            "static_page_status" => 1
        );
        $get_contact_us = $this->Common_model->get_single_row(TBL_STATIC_PAGE, "static_page_content", $get_contact_us_where);
        $html_content = "<h1 style='text-align:center'>Comming Soon....</h1>";
        if (!empty($get_contact_us)) {
            $html_content = $get_contact_us['static_page_content'];
        }


        $view_data = array(
            "html_content" => $html_content
        );

        $this->load->view('staticpages/header');
        $this->load->view('staticpages/index', $view_data);
        $this->load->view('staticpages/footer');
    }

    public function faq() {
        $faq_array = array();
        $faq_array[] = array(
            "id" => 1,
            "question" => "What is Lorem Ipsum?",
            "answer" => "Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book.",
        );
        $faq_array[] = array(
            "id" => 2,
            "question" => "What is Lorem Ipsum?",
            "answer" => "Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book.",
        );
        $faq_array[] = array(
            "id" => 3,
            "question" => "What is Lorem Ipsum?",
            "answer" => "Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book.",
        );
        $faq_array[] = array(
            "id" => 4,
            "question" => "What is Lorem Ipsum?",
            "answer" => "Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book.",
        );
        $faq_array[] = array(
            "id" => 5,
            "question" => "What is Lorem Ipsum?",
            "answer" => "Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book.",
        );
        
        
        $view_data = array(
            "faq_array" => $faq_array
        );

        $this->load->view('staticpages/header');
        $this->load->view('staticpages/faq', $view_data);
        $this->load->view('staticpages/footer');
    }

}
