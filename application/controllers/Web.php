<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * This function use for login regsitration for doctors.
 *
 * @author Prashant Suthar
 */
class Web extends MY_Controller {

    public function __construct() {

        parent::__construct();
        $this->load->library('session');
    }

    public function index() {
        $view_data = array();
        $query = "SELECT testimonial_title,testimonial_description,testimonial_photo_url FROM me_testimonial WHERE testimonial_status=1";
        $view_data['testimonials'] = $this->Common_model->get_all_rows_by_query($query);
        $this->load->view('web/index', $view_data);
        $this->load->library('form_validation');
        $this->load->helper('form');
    }

    public function save_getintouch_post() {

        $this->form_validation->set_error_delimiters('', '');
        $this->form_validation->set_rules('name', 'name', 'required');
        $this->form_validation->set_rules('subject', 'subject', 'required');
        $this->form_validation->set_rules('message', 'message', 'required');
        $this->form_validation->set_rules('email', 'email', 'required|valid_email');
        $this->form_validation->set_rules('comment_captcha', 'captcha', 'required|trim');
        $this->form_validation->set_rules('phone_number', 'mobile number', 'required|trim|numeric|regex_match[/^[0-9]{10}$/]');

        if ($this->form_validation->run() == FALSE) {
            $errors = validation_errors();
            echo json_encode(['error' => $errors]);
        } else {
            if ($this->input->post('comment_captcha') && $this->session->userdata('contact_captcha_code') != $this->input->post('comment_captcha')) {
                echo json_encode(['error' => "Invalid captcha code"]);
                exit;
            }
            $name = !empty($this->input->post('name')) ? trim($this->Common_model->escape_data($this->input->post('name'))) : "";
            $email = !empty($this->input->post('email')) ? trim($this->Common_model->escape_data($this->input->post('email'))) : "";
            $phone_number = !empty($this->input->post('phone_number')) ? trim($this->Common_model->escape_data($this->input->post('phone_number'))) : "";
            $subject = !empty($this->input->post('subject')) ? trim($this->Common_model->escape_data($this->input->post('subject'))) : "";
            $message = !empty($this->input->post('message')) ? trim($this->Common_model->escape_data($this->input->post('message'))) : "";
            $is_subscription_interested = !empty($this->input->post('subscription_interested')) ? "1" : "0";
            $insert_data['name'] = $name;
            $insert_data['email'] = $email;
            $insert_data['phone_number'] = $phone_number;
            $insert_data['subject'] = $subject;
            $insert_data['message'] = $message;
            $insert_data['is_subscription_interested'] = $is_subscription_interested;
            $insert_data['flag'] = 1;
            $insert_data['ip_address'] = $_SERVER['REMOTE_ADDR'];
            $insert_data['created_date'] = $this->utc_time_formated;
            $insert_data['user_agent_detail'] = $_SERVER['HTTP_USER_AGENT'];

            $this->Common_model->insert(TBL_GET_IN_TOUCH, $insert_data);

            /* email sending code */
            /* $this->load->model('Emailsetting_model');

              $email_template_data = $this->Emailsetting_model->get_emailtemplate_by_id(31);
              $parse_arr = array(
              '{IssueEmail}' => $email,
              '{UserName}' => $name,
              '{Issue}' => $message,
              '{WebUrl}' => DOMAIN_URL,
              '{AppName}' => APP_NAME,
              '{MailContactNumber}' => $email_template_data['email_static_data']['contact_number'],
              '{MailEmailAddress}' => $email_template_data['email_static_data']['email_id'],
              '{MailCompanyName}' => $email_template_data['email_static_data']['company_name'],
              '{CopyRightsYear}' => date('Y')
              );

              $message = replace_values_in_string($parse_arr, $email_template_data['email_template_message']);

              $subject = $email_template_data['email_template_subject'];
              //EMAIL TEMPLATE END BY PRAGNESH
              //this function help you to send mail to single ot multiple users
              $this->send_email(array($email => $email), $subject, $message); */

            echo json_encode(true);
        }
    }

    public function save_subscriber_post() {
        $this->form_validation->set_error_delimiters('', '');
        $this->form_validation->set_rules('sub_email', 'email', 'required|valid_email');

        if ($this->form_validation->run() == FALSE) {
            $errors = validation_errors();
            echo json_encode(['error' => $errors]);
        } else {
            $sub_email = !empty($this->input->post('sub_email')) ? trim($this->Common_model->escape_data($this->input->post('sub_email'))) : "";

            $insert_data['email'] = $sub_email;
            $insert_data['flag'] = 2;
            $insert_data['ip_address'] = $_SERVER['REMOTE_ADDR'];
            $insert_data['created_date'] = $this->utc_time_formated;
            $insert_data['user_agent_detail'] = $_SERVER['HTTP_USER_AGENT'];

            $this->Common_model->insert(TBL_GET_IN_TOUCH, $insert_data);
            echo json_encode(true);
        }
    }

    public function web_static_pages($id) {
        
        $static_page = $this->Common_model->get_staticpage_by_id($id);
        $result['data'] = $static_page['static_page_content'];
        $result['breadcrumbs'] = ucwords(str_replace('_', ' ', $static_page['static_page_title']));
        $result['page_title'] = ucwords(str_replace('_', ' ', $static_page['static_page_title']));
        $this->load->view('web/static-page', $result);
    }

    public function audio_view() {
        $result['breadcrumbs'] = "Audio Visual";
        $result['page_title'] = "Welcome to MedSign";

        $this->load->view('web/audio_view', $result);
    }

    public function health_advice($request) {
        $request_data = json_decode(base64_decode($request), true);
        $data['health_advice_id'] = $health_advice_id = $request_data['h_a_id'];
        $data['patient_id'] = $request_data['p_id'];
        $data['patient_health_advice_id'] = $request_data['p_h_id'];
        $data['health_advice'] = $this->Common_model->get_health_advice_by_id($health_advice_id, $data['patient_id']);
        $data['health_advice_groups'] = $this->Common_model->get_patient_health_advice_groups($data['patient_id']);
        $data['comments'] = $this->Common_model->get_comments($health_advice_id);
        // print_r($data['comments']);die;
        $data['breadcrumbs'] = $data['health_advice']->health_advice_name;
        $data['page_title'] = $data['health_advice']->health_advice_name;
        $data['meta_description'] = $data['health_advice']->health_advice_desc;
        $data['btnShareImg'] = $data['health_advice']->health_advice_image;
        $this->load->view("web/health_advice_view", $data);
    }

    public function captcha_code() {
        $this->load->helper('captcha');
        $vals = array(
                'img_path'      => DOCROOT_PATH . 'captcha/',
                'img_url'       => BASE_URL . 'captcha/',
                'font_path'     => DOCROOT_PATH . 'system/fonts/texb.ttf',
                'img_width'     => '150',
                'img_height'    => 30,
                'expiration'    => 7200,
                'word_length'   => 6,
                'font_size'     => 16,
                'img_id'        => 'Imageid',
                'pool'          => '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ',
                // White background and border, black text and red grid
                'colors'        => array(
                        'background' => array(255, 255, 255),
                        'border' => array(255, 255, 255),
                        'text' => array(0, 0, 0),
                        'grid' => array(255, 40, 40)
                )
        );
        $cap = create_captcha($vals);
        $this->session->set_userdata(['captcha_code' => $cap['word']]);
        echo json_encode(['status' => true, 'image' => $cap['image']]);
    }

    public function health_advice_list($request) {
        $request_data = json_decode(base64_decode($request), true);
        $data['group_id'] = $health_advice_id = $request_data['h_g_id'];
        $data['order'] = $health_advice_id = $request_data['order'];
        $data['patient_id'] = $request_data['p_id'];
        $data['patient_health_advice_id'] = $request_data['p_h_id'];
        $data['health_advice'] = $this->Common_model->get_health_advice_by_group($data['group_id'], $data['patient_id'], $data['order']);
        $data['breadcrumbs'] = "Health Advice";
        $data['page_title'] = "Health Advice";
        $this->load->view("web/health_advice_list_view", $data);
    }

    public function likes() {
        $this->form_validation->set_error_delimiters('', '');
        $this->form_validation->set_rules('like_count', 'like', 'required|trim');
        $this->form_validation->set_rules('health_advice_id', 'health advice id', 'required|trim');
        if ($this->form_validation->run() == FALSE) {
            $errors = validation_errors();
            echo json_encode(['error' => $errors]);
        } else {
            $update = ['health_advice_likes' => $this->input->post('like_count') +1];
            $this->Common_model->update('me_health_advice', $update, ['health_advice_id' => $this->input->post('health_advice_id')]);
            echo json_encode($update['health_advice_likes']);
        }
    }
    public function save_comment_post() {
        $this->form_validation->set_error_delimiters('', '');
        $this->form_validation->set_rules('comment_name', 'name', 'required|trim');
        $this->form_validation->set_rules('comment_phone', 'phone number', 'required|trim');
        $this->form_validation->set_rules('comment_email', 'email', 'required|trim');
        $this->form_validation->set_rules('message', 'message', 'required|trim');
        $this->form_validation->set_rules('comment_captcha', 'captcha', 'required|trim');
        
        if ($this->form_validation->run() == FALSE) {
            $errors = validation_errors();
            echo json_encode(['error' => $errors]);
        } elseif ($this->session->userdata('captcha_code') != $this->input->post('comment_captcha')) {
            echo json_encode(['invalid_captcha' => true]);
        } else {
            $comment_name = !empty($this->input->post('comment_name')) ? trim($this->Common_model->escape_data($this->input->post('comment_name'))) : "";
            $comment_phone = !empty($this->input->post('comment_phone')) ? trim($this->Common_model->escape_data($this->input->post('comment_phone'))) : "";
            $comment_email = !empty($this->input->post('comment_email')) ? trim($this->Common_model->escape_data($this->input->post('comment_email'))) : "";
            $message = !empty($this->input->post('message')) ? trim($this->Common_model->escape_data($this->input->post('message'))) : "";
            $insert_data['health_advice_id'] = $this->input->post('health_advice_id');
            $insert_data['patient_health_advice_id'] = $this->input->post('patient_health_advice_id');
            $insert_data['patient_id'] = $this->input->post('patient_id');
            $insert_data['comment_name'] = $comment_name;
            $insert_data['comment_phone'] = $comment_phone;
            $insert_data['comment_email'] = $comment_email;
            $insert_data['comment'] = $message;
            $insert_data['created_at'] = $this->utc_time_formated;
            $this->Common_model->insert('me_health_advice_comment', $insert_data);
            echo json_encode(true);
        }
    }

    public function contact_form_captcha() {
        $this->load->helper('captcha');
        if (!file_exists(DOCROOT_PATH . 'captcha/')) {
            mkdir(DOCROOT_PATH . 'captcha/', 0777, TRUE);
            chmod(DOCROOT_PATH . 'captcha/', 0777);
        }
        $vals = array(
                'img_path'      => DOCROOT_PATH . 'captcha/',
                'img_url'       => BASE_URL . 'captcha/',
                'font_path'     => DOCROOT_PATH . 'system/fonts/texb.ttf',
                'img_width'     => '150',
                'img_height'    => 45,
                'expiration'    => 7200,
                'word_length'   => 4,
                'font_size'     => 17,
                'img_id'        => 'Imageid',
                'pool'          => '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ',
                // White background and border, black text and red grid
                'colors'        => array(
                        'background' => array(255, 255, 255),
                        'border' => array(255, 255, 255),
                        'text' => array(0, 0, 0),
                        'grid' => array(255, 40, 40)
                )
        );
        $cap = create_captcha($vals);
        $this->session->set_userdata(['contact_captcha_code' => $cap['word']]);
        echo json_encode(['status' => true, 'image' => $cap['image']]);
    }
}
