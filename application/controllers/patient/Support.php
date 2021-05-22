<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Support extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->library('patient_auth');
        $this->load->model('patient_model','patient');
        $this->load->library('form_validation');
        if (!$this->patient_auth->is_logged_in()) {
            redirect('patient/login');
        }
    }

    public function add_issue() {
        $view_data = array();
        $view_data['breadcrumbs'] = "Help & Support";
        $view_data['page_title'] = "Help & Support";
        $this->form_validation->set_rules('issue_message', 'message', 'required|trim');
        $this->form_validation->set_rules('comment_captcha', 'captcha', 'required|trim');
        $this->form_validation->set_rules('issue_email', 'email', 'trim|valid_email');
        $this->form_validation->set_rules('user_name', 'user_name', 'trim');
        $view_data['screenshot_error'] = "";

            if(!empty($_POST['submit']) && !empty($_FILES['screenshot']['name'][0])) {
                $totalfiles = count($_FILES['screenshot']['name']);
                if ($totalfiles > 0) {
                    for($i=0; $i < $totalfiles; $i++) {
                        $ext = pathinfo($_FILES['screenshot']['name'][$i], PATHINFO_EXTENSION);
                        if(!in_array(strtolower($ext), ["jpg","jpeg","png","gif"])) {
                            $view_data['screenshot_error'] = "Your selected image type is invalid.";
                            break;
                        }
                    }
                }
            }
            if ($this->form_validation->run() !== FALSE && empty($view_data['screenshot_error'])) {
                if ($this->input->post('comment_captcha') && $this->session->userdata('support_captcha_code') != $this->input->post('comment_captcha')) {
                    $view_data['invalid_captcha'] = "Invalid captcha code";
                } else {
                    $issue_email = set_value('issue_email');
                    $user_issue_array = array(
                        'user_issue_user_id' => $this->patient_auth->get_logged_user_id(),
                        'user_issue_message' => set_value('issue_message'),
                        'user_type' => 1,
                        'user_issue_created_at' => $this->utc_time_formated
                    );
                    if(!empty($issue_email))
                        $user_issue_array['user_issue_email'] = $issue_email;
                    $inserted_id = $this->Common_model->insert('me_user_issue', $user_issue_array);
                    if(!empty($_FILES['screenshot']['name'][0])) {
                        $upload_path = UPLOAD_FILE_FULL_PATH . "/" . ISSUE_FOLDER . "/" . $inserted_id;
                        $upload_folder = ISSUE_FOLDER . "/" . $inserted_id;
                        $reports_uploaded = do_upload_multiple($upload_path, $_FILES['screenshot'], $upload_folder);
                        if (!empty($reports_uploaded) && count($reports_uploaded) > 0) {
                            foreach ($reports_uploaded as $image) {
                                if (!empty($image)) {
                                    $insert_image_array[] = array(
                                        'user_issue_attachment_issue_id' => $inserted_id,
                                        'user_issue_attachment_name' => $image,
                                        'user_issue_attachment_filepath' => IMAGE_MANIPULATION_URL . ISSUE_FOLDER . "/" . $inserted_id . "/" . $image,
                                        'user_issue_attachment_status' => 1,
                                        'user_issue_attachment_created_at' => $this->utc_time_formated
                                    );
                                }
                            }
                            $this->Common_model->insert_multiple('me_user_issue_attachment', $insert_image_array);
                        }
                    }
                    if(!empty($inserted_id)) {
                        $request_data = array(
                            'user_name' => set_value('user_name'),
                            'issue_id' => $inserted_id
                        );
                        $cron_job_path = DOCROOT_PATH . "index.php notification/support_mail/" . base64_encode(json_encode($request_data));
                        exec(PHP_PATH . " " . $cron_job_path . " > /dev/null &");
                    }
                    $this->session->set_flashdata('message', 'Issue submitted successfully');
                    redirect(site_url('patient/add_issue'));
                }
            }
        
        $user = $this->Common_model->get_single_row('me_users', 'user_first_name, user_last_name, user_email', ['user_id' => $this->patient_auth->get_logged_user_id()]);
        $view_data['user_row'] = $user;
        $view_data['issue_message'] = set_value('issue_message');
        $view_data['issue_email'] = set_value('issue_email', $user['user_email']);
        $this->load->view('patient/support_add_issue', $view_data);
    }

    public function captcha_code() {
        $this->load->helper('captcha');
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
        $this->session->set_userdata(['support_captcha_code' => $cap['word']]);
        echo json_encode(['status' => true, 'image' => $cap['image']]);
    }
}