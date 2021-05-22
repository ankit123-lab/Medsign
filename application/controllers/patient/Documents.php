<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Documents extends MY_Controller {

    public function __construct() {

        parent::__construct();
        $this->load->library('patient_auth');
        $this->load->model('patient_model','patient');
        $this->load->library('form_validation');
        $this->load->library("pagination");
        if (!$this->patient_auth->is_logged_in()) {
            redirect('patient/login');
        }
    }

    public function report() {
        $view_data = array();
        $view_data['breadcrumbs'] = "Report";
        $view_data['page_title'] = "Report";
        $config = array();
        $config["base_url"] = site_url() . "patient/report/";
        $config["total_rows"] = $this->patient->get_reports($this->patient_auth->get_user_id(), true);
        $config["per_page"] = 10;
        $config["uri_segment"] = 3;
        $config['reuse_query_string'] = true;
        $config['attributes'] = array('class' => 'page-link');
        $this->pagination->initialize($config);
        $page = ($this->uri->segment(3))? $this->uri->segment(3) : 0;
        $view_data["links"] = $this->pagination->create_links();
        $view_data['reports'] = $this->patient->get_reports($this->patient_auth->get_user_id(), false, $config["per_page"], $page);
        $this->load->view('patient/report_view', $view_data);
    }

    public function add_report() {
        $view_data = array();
        $view_data['breadcrumbs'] = "Add Report";
        $view_data['page_title'] = "Add Report";
        $view_data['report_types'] = $this->patient->get_report_type();
        $user_id = $this->patient_auth->get_user_id();
        $this->form_validation->set_rules('report_name', 'report name', 'required|trim');
        $this->form_validation->set_rules('type_of_report', 'type of report', 'required|trim');
        $this->form_validation->set_rules('date_of_report', 'date of report', 'required|trim');
        $view_data['report_file_error'] = "";
        if (empty($_FILES['report_file']['name'][0]) && !empty($_POST['submit'])) {
            $view_data['report_file_error'] = "The report file field is required.";
        } elseif(!empty($_POST['submit'])) {
            $totalfiles = count($_FILES['report_file']['name']);
            if ($totalfiles > 0) {
                for($i=0; $i < $totalfiles; $i++) {
                    $ext = pathinfo($_FILES['report_file']['name'][$i], PATHINFO_EXTENSION);
                    if(!in_array(strtolower($ext), ["jpg","jpeg","png","gif","pdf"])) {
                        $view_data['report_file_error'] = "Your selected file type is invalid.";
                        break;
                    }
                }
            }
        }
        if ($this->form_validation->run() !== FALSE && empty($view_data['report_file_error'])) {
            $date = set_value('date_of_report');
            $arr = explode('/', $date);
            if(count($arr) == 3) {
                $report_date = $arr[2] . "-" . $arr[1] . "-" . $arr[0];
            } else {
                $report_date = date("Y-m-d");
            }
            $report_array = array(
                'file_report_user_id' => $user_id,
                'file_report_doctor_user_id' => $user_id,
                'file_report_appointment_id' => NULL,
                'file_report_clinic_id' => NULL,
                'file_report_name' => set_value('report_name'),
                'file_report_report_type_id' => set_value('type_of_report'),
                'file_report_date' => $report_date,
                'file_report_share_status' => 1,
                'file_report_added_by_user_id' => $this->patient_auth->get_logged_user_id(),
                'file_report_created_at' => $this->utc_time_formated
                
            );
            $inserted_id = $this->Common_model->insert('me_files_reports', $report_array);
            $upload_path = UPLOAD_FILE_FULL_PATH . "/" . REPORT_FOLDER . "/" . $inserted_id;
            $upload_folder = REPORT_FOLDER . "/" . $inserted_id;
            $reports_uploaded = do_upload_multiple($upload_path, $_FILES['report_file'], $upload_folder);
            if (!empty($reports_uploaded) && count($reports_uploaded) > 0) {
                foreach ($reports_uploaded as $image) {
                    if (!empty($image)) {
                        $insert_image_array[] = array(
                            'file_report_image_file_report_id' => $inserted_id,
                            'file_report_image_url' => IMAGE_MANIPULATION_URL . REPORT_FOLDER . "/" . $inserted_id . "/" . $image,
                            'report_file_size' => get_file_size(IMAGE_MANIPULATION_URL . REPORT_FOLDER . "/" . $inserted_id . "/" . $image),
                            'file_report_image_created_at' => $this->utc_time_formated
                        );
                    }
                }
                $this->Common_model->insert_multiple('me_files_reports_images', $insert_image_array);
            }
            $this->session->set_flashdata('message', 'Report uploaded successfully');
            redirect(site_url('patient/report'));
        }
        $view_data['report_name'] = set_value('report_name');
        $view_data['type_of_report'] = set_value('type_of_report');
        $view_data['date_of_report'] = set_value('date_of_report');
        $this->load->view('patient/add_report_view', $view_data);
    }

    public function view_report($id) {
        $view_data = array();
        $view_data['breadcrumbs'] = "View Report";
        $view_data['page_title'] = "View Report";
        $report_id = encrypt_decrypt($id,'decrypt');
        $view_data['report'] = $this->patient->get_report_by_id($report_id);
        $image_where = array(
            'file_report_image_file_report_id' => $report_id,
            'file_report_image_status' => 1
        );
        $view_data['report_image'] = $this->Common_model->get_all_rows('me_files_reports_images', 'file_report_image_url', $image_where);
        // echo "<pre>";
        // print_r($view_data['report_image']);
        $this->load->view('patient/read_report_view', $view_data);
    }

    public function delete_report($id) {
        $report_id = encrypt_decrypt($id,'decrypt');
        $update_report_data = array(
            'file_report_status' => 9,
            'file_report_updated_by_user_id' => $this->patient_auth->get_logged_user_id(),
            'file_report_updated_at' => $this->utc_time_formated,
        );
        $update_report_where = array(
            'file_report_id' => $report_id
        );
        $this->Common_model->update('me_files_reports', $update_report_data, $update_report_where);
        $update_report_images_data = array(
            'file_report_image_updated_at' => $this->utc_time_formated,
            'file_report_image_status' => 9
        );
        $update_report_images_where = array(
            'file_report_image_file_report_id' => $report_id
        );
        $this->Common_model->update('me_files_reports_images', $update_report_images_data, $update_report_images_where);
        $this->session->set_flashdata('message', 'Report deleted successfully');
        redirect(site_url('patient/report'));
    }

}