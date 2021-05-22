<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
// use Mpdf\Mpdf;
class Uas7diary extends MY_Controller {

    public function __construct() {

        parent::__construct();
        $this->load->library('patient_auth');
        $this->load->model('patient_model','patient');
        $this->load->library('form_validation');
        $this->load->library('pagination');
        if (!$this->patient_auth->is_logged_in()) {
            redirect('patient/login');
        }
    }

    public function list() {
        $where = ['user_id' => $this->patient_auth->get_logged_user_id(), 'detail_type' => 'diary'];
        $columns = "user_id,plan_end_date";
        $last_payment = $this->patient->get_patient_last_payment($columns, $where);
        if(empty($last_payment)) {
            redirect(site_url('patient/utilities_list'));
        }
        $view_data = array();
        $view_data['is_plan_active'] = true;
        if($last_payment->plan_end_date < get_display_date_time("Y-m-d")) {
            $view_data['is_plan_active'] = false;
        }
        $view_data['breadcrumbs'] = "UAS7 Diary";
        $view_data['page_title'] = "UAS7 Diary";
        $uas7 = get_assign_uas7($this->patient_auth->get_user_id());
        $view_data['patient_uas7_detail'] = $uas7;
        $where = [
            'patient_id' => $this->patient_auth->get_user_id(),
            'diary_type' => 1
        ];
        $columns = "patient_diary_id,patient_diary_date";
        $view_data['last_para'] = $this->patient->get_uas7_last_para($columns, $where);
        $view_data['is_add_params'] = true;
        if(!empty($view_data['last_para']->patient_diary_date) && $view_data['last_para']->patient_diary_date == get_display_date_time("Y-m-d")) {
            $view_data['is_add_params'] = false;
        }
        $get_health_analytics_test = $this->Common_model->get_single_row('me_health_analytics_test', 'health_analytics_test_validation', array('health_analytics_test_id' => 308));
        $view_data['uas7_range'] = json_decode($get_health_analytics_test['health_analytics_test_validation'], true);
        // $view_data['last_para'] = [];
        $this->load->view('patient/uas7diary_list_view', $view_data);
    }

    public function add_date_list() {
        $view_data = [];
        $view_data['breadcrumbs'] = "Select UAS parameter date";
        $view_data['page_title'] = "Add UAS7 Parameters";
        $where = ['patient_id' => $this->patient_auth->get_user_id(), 'is_get_all' => true];
        $result = $this->patient->get_uas7_para_data($where);
        $all_date_arr = array_column($result,'patient_diary_id', 'patient_diary_date');
        $missing_date = [];
        $current_date = get_display_date_time("Y-m-d");
        $last_data_row = end($result);
        $last_data_row->patient_diary_date;
        $start_date = new DateTime($last_data_row->patient_diary_date);
        $end_date = new DateTime(get_display_date_time("Y-m-d"));
        $diff_days = $end_date->diff($start_date)->format("%a");
        for ($i = 0; $i <= $diff_days; $i++) {
            $date = date("Y-m-d", strtotime("-".$i." days", strtotime($current_date)));
            if(empty($all_date_arr[$date])) {
                $missing_date[] = [
                    'date' => $date
                ];
            } else {
                $row_key = array_search($all_date_arr[$date], array_column($result, 'patient_diary_id'));
                if(is_numeric($row_key) && get_display_date_time("Y-m-d") == get_display_date_time("Y-m-d", $result[$row_key]->patient_diary_created_at)) {
                    $missing_date[] = [
                        'date' => $result[$row_key]->patient_diary_date,
                        'patient_diary_id' => $result[$row_key]->patient_diary_id
                    ];
                }
            }
        }
        if(count($missing_date) == 1) {
            if(!empty($missing_date[0]['patient_diary_id'])) {
                redirect(site_url('patient/edit_uas7_para/'.encrypt_decrypt($missing_date[0]['patient_diary_id'],'encrypt')));
            } elseif(!empty($missing_date[0]['date'])) {
                redirect(site_url('patient/add_uas7_para').'/'.encrypt_decrypt($missing_date[0]['date'], 'encrypt'));
            }
        }
        $config = array();
        $config["base_url"] = site_url() . "patient/add_date_list/";
        $config["total_rows"] = count($missing_date);
        $config["per_page"] = 10;
        $config["uri_segment"] = 3;
        $config['reuse_query_string'] = true;
        $config['attributes'] = array('class' => 'page-link');
        $this->pagination->initialize($config);
        $page = ($this->uri->segment(3))? $this->uri->segment(3) : 0;
        $view_data["links"] = $this->pagination->create_links();
        $view_data['missing_date'] = array_slice($missing_date, $page, $config["per_page"]);
        $this->load->view('patient/uas7diary_add_date_view', $view_data);
    }

    /*public function save_uas7_chart_image() {
        $chart_image_data = "data:".$this->input->post('chart_image_data');
        $data = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $chart_image_data));
        $upload_path = UPLOAD_FILE_FULL_PATH."temp/";
        $file_name = $this->patient_auth->get_user_id() . "_uas7_graph.png";
        if(!file_exists($upload_path)) {
            mkdir($upload_path, 0777, true);
        }
        if(file_exists($upload_path.$file_name)) {
            unlink($upload_path.$file_name);
        }
        file_put_contents($upload_path.$file_name, $data);
        echo json_encode(['status' => true]);
    }*/
    public function download() {
        $date = get_display_date_time('Y_m_d');
        $file_name = "UAS7_".$this->patient_auth->get_user_id()."_".$date.".pdf";
        $upload_folder = UAS7_FOLDER . "/" . $this->patient_auth->get_user_id();
        if(s3_file_exist($upload_folder."/".$file_name)) {
            $file_url = IMAGE_MANIPULATION_URL . $upload_folder . "/" . $file_name;
            header('Content-Type: application/octet-stream');
            header("Content-Transfer-Encoding: Binary"); 
            header("Content-disposition: attachment; filename=\"".$file_name."\""); 
            readfile($file_url);
            exit;
        }
        $view_data = [];
        $where = ['patient_id' => $this->patient_auth->get_user_id(), 'is_get_all' => true];
        $result = $this->patient->get_uas7_para_data($where);
        $uas7_para_data = [];
        $uas7_weekly_data = [];
        $labels = [];
        $uas7_values = [];
        $daily_labels = [];
        $uas_daily_values = [];
        foreach ($result as $key => $value) {
            $label_arr = explode(",", $value->patient_diary_label);
            $value_arr = explode(",", $value->patient_diary_value);
            $uas7_val_arr = array_combine($label_arr, $value_arr);
            $uas7_para_data[] = [
                'patient_diary_id' => $value->patient_diary_id,
                'patient_diary_date' => $value->patient_diary_date,
                'diary_date' => date("d/m/y", strtotime($value->patient_diary_date)),
                'patient_diary_created_at' => $value->patient_diary_created_at,
                'wheal_label' => 'wheal_count',
                'pruritus_label' => 'pruritus_count',
                'wheal_value' => $uas7_val_arr['wheal_count'],
                'pruritus_value' => $uas7_val_arr['pruritus_count'],
                'uas_total_value' => $uas7_val_arr['wheal_count'] + $uas7_val_arr['pruritus_count']
            ];
            if(($key+1) % 7 == 0 || $key == 0 || count($result) == $key+1) {
                $daily_labels[] = date("d/m/y", strtotime($value->patient_diary_date));
            } else {
                $daily_labels[] = "";
            }
            $uas_daily_values[] = $uas7_val_arr['wheal_count'] + $uas7_val_arr['pruritus_count'];
            if(($key+1) % 7 == 0) {
                $labels[] = date("d/m/Y", strtotime($uas7_para_data[0]['patient_diary_date']));
                $uas7_values[] = array_sum(array_map(function($item) { 
                                    return $item['uas_total_value']; 
                                }, $uas7_para_data));
                $uas7_weekly_data[] = $uas7_para_data;
                $uas7_para_data = [];
            }
        }
        
        if(!empty($uas7_para_data) && count($uas7_para_data) > 0)
            $uas7_weekly_data[] = $uas7_para_data;
        $view_data['uas7_result'] = $uas7_weekly_data;
        $get_health_analytics_test = $this->Common_model->get_single_row('me_health_analytics_test', 'health_analytics_test_validation', array('health_analytics_test_id' => 308));
        $view_data['uas7_range'] = json_decode($get_health_analytics_test['health_analytics_test_validation'], true);
        // require_once MPDF_PATH;
        // $lang_code = 'en-GB';
        // $mpdf = new MPDF(
        //         $lang_code, 'A4', 0, 'arial', 8, 8, 25, 8, 8, 5, 'P'
        // );
        $defaultConfig = (new \Mpdf\Config\ConfigVariables())->getDefaults();
        $fontDirs = $defaultConfig['fontDir'];
        $defaultFontConfig = (new \Mpdf\Config\FontVariables())->getDefaults();
        $fontData = $defaultFontConfig['fontdata'];
        $mpdf = new \Mpdf\Mpdf([
                'tempDir' => DOCROOT_PATH . 'uploads',
                'fontDir' => array_merge($fontDirs, [
                DOCROOT_PATH.'assets/fonts'
            ]),
            'fontdata' => $fontData + [
                'gotham_book' => [
                    'R' => 'Gotham-Book.ttf',
                    'I' => 'Gotham-Book.ttf',
                ],
            ],
            'default_font' => 'gotham_book',
            'mode' => 'en-GB',
            'format' => 'A4',
            'font_family' => 'arial',
            'margin_top' => 25,
            'margin_bottom' => 8,
            'margin_left' => 8,
            'margin_right' => 8,
        ]);
        $mpdf->useOnlyCoreFonts = true;
        $mpdf->SetDisplayMode('real');
        $mpdf->list_indent_first_level = 0;
        $mpdf->setAutoBottomMargin = 'stretch';
        $mpdf->SetHTMLHeader('
                <table style="width:100%;border-bottom:1px solid #000;">
                    <tr>
                        <td width="50%" style="text-align:left;vertical-align:top;">UAS7 Diary</td>
                        <td width="50%" style="text-align:right;vertical-align:top;">Date: '.get_display_date_time("d/m/Y").'</td>
                    </tr>
                </table>
            ');
        $mpdf->SetHTMLFooter('
            <table width="100%">
                <tr>
                    <td width="33%" style="font-size:10px">
                        Generated On: {DATE d/m/Y}
                    </td>
                    <td width="33%" style="font-size:10px" align="center">Page No. {PAGENO}/{nbpg}</td>
                    <td width="33%" align="right" style="font-size:10px">Powerd by Medsign</td>
                </tr>
            </table>
        ');
        $graph_image_url = "https://quickchart.io/chart?c={type:'line',data:{labels:".str_replace('"', "'", json_encode($labels)).", datasets:[{label:'UAS7', data: ".json_encode($uas7_values).", fill:false,borderColor:'%2330aca5'}]},options: {title: {display: true,text: 'UAS7',position:'bottom'},legend: {display: false,position: 'bottom'},scales: {yAxes: [{id: 'y-axis-0',type: 'linear',display: true,scaleLabel: {display: true,labelString: 'UAS7'},position: 'left'}],xAxes : [{scaleLabel: {display: true,labelString: 'Date'},position: 'bottom'}]},plugins: {datalabels:{display:true,align:'bottom',backgroundColor:'%23ccc',borderRadius:3},}}}";
        $view_data['graph_image_url'] = "";
        if(count($uas7_values) > 0)
            $view_data['graph_image_url'] = $graph_image_url;

        $daily_graph_image_url = "https://quickchart.io/chart?height=400&c={type:'line',data:{labels:".str_replace('"', "'", json_encode($daily_labels)).", datasets:[{label:'UAS7', data: ".json_encode($uas_daily_values).", fill:false,borderColor:'%2330aca5'}]},options: {title: {display: true,text: 'UAS',position:'bottom'},legend: {display: false,position: 'bottom'},scales: {yAxes: [{id: 'y-axis-0',type: 'linear',display: true,scaleLabel: {display: true,labelString: 'UAS'},position: 'left'}],xAxes : [{scaleLabel: {display: true,labelString: 'Date'},position: 'bottom'}]},plugins: {datalabels:{display:true,align:'bottom',backgroundColor:'%23ccc',borderRadius:3},}}}";

        $view_data['daily_graph_image_url'] = $daily_graph_image_url;
        $view_html = $this->load->view("patient/uas7_report_view", $view_data, true);
        //echo $view_html;die;
        $mpdf->WriteHTML($view_html);
        // echo $mpdf->Output();die;

        $upload_path = DOCROOT_PATH . 'uploads/' . UAS7_FOLDER . '/';
        if (!file_exists($upload_path)) {
            mkdir($upload_path, 0777, TRUE);
            chmod($upload_path, 0777);
        }
        $mpdf->Output($upload_path . $file_name, 'F');
        upload_to_s3($upload_path . $file_name, $upload_folder.'/'.$file_name);
        if(file_exists($upload_path . $file_name))
            unlink($upload_path . $file_name);
        $file_url = IMAGE_MANIPULATION_URL . $upload_folder . "/" . $file_name;
        header('Content-Type: application/octet-stream');
        header("Content-Transfer-Encoding: Binary"); 
        header("Content-disposition: attachment; filename=\"".$file_name."\""); 
        readfile($file_url);
        exit;
    }

    public function share_uas7_report_view() {
        $response = [];
        $view_data = [];
        $patient_id = $this->patient_auth->get_user_id();
        $response['html'] = $this->load->view("patient/share_uas7_report_view", $view_data, true);
        echo json_encode($response);
    }
    public function share_uas7_report() {
        $response = [];
        $this->form_validation->set_rules('share_doctor_name', 'doctor name', 'required|trim');
        $this->form_validation->set_rules('share_doctor_email', 'doctor email', 'required|trim');
        if ($this->form_validation->run() == FALSE) {
            $errors = validation_errors();
            $response['status'] = false;
            $response['errors'] = nl2br(strip_tags($errors));
        } else {
            $reuqest_arr = [
                'd_name' => set_value('share_doctor_name'),
                'd_email' => set_value('share_doctor_email'),
                'p_id' => $this->patient_auth->get_user_id()
            ];
            $reuqest_data = encrypt_decrypt(json_encode($reuqest_arr), 'encrypt');
            $cron_job_path = DOCROOT_PATH . "index.php cron/save_share_uas7_report/" . $reuqest_data;
                exec(PHP_PATH . " " . $cron_job_path . " > /dev/null &");
            $response['status'] = true;
        }
        echo json_encode($response);
    }

    public function save_as_report() {
        $reuqest_arr = [
            'p_id' => $this->patient_auth->get_user_id(),
            'is_save' => true
        ];
        $reuqest_data = encrypt_decrypt(json_encode($reuqest_arr), 'encrypt');
        $cron_job_path = DOCROOT_PATH . "index.php cron/save_share_uas7_report/" . $reuqest_data;
            exec(PHP_PATH . " " . $cron_job_path . " > /dev/null &");
        $response['status'] = true;
        echo json_encode($response);
    }

    public function uas7_para_graph_data() {
        $response = [];
        $where = ['patient_id' => $this->patient_auth->get_user_id(), 'is_get_all' => true];
        $result = $this->patient->get_uas7_para_data($where);
        $uas7_weekly_data = [];
        $uas7_daily_data = [];
        $uas7_score = 0;
        foreach ($result as $key => $value) {
            $value_arr = explode(",", $value->patient_diary_value);
            $uas7_score += array_sum($value_arr);
            $uas7_daily_data[] = [
                'patient_diary_date' => date("d/m/y", strtotime($value->patient_diary_date)),
                'uas7_score' => array_sum($value_arr)
            ];
            if(($key+1)%7 == 0){
                $uas7_weekly_data[] = [
                    'patient_diary_date' => date("d/m/y", strtotime($result[($key+1)-7]->patient_diary_date)),
                    'uas7_score' => $uas7_score,
                ];
                $uas7_score = 0;
            }
        }
        $get_health_analytics_test = $this->Common_model->get_single_row('me_health_analytics_test', 'health_analytics_test_validation', array('health_analytics_test_id' => 308));
        $response['weekly_graph_data'] = $uas7_weekly_data;
        $response['uas7_daily_data'] = $uas7_daily_data;
        $response['uas7_range'] = json_decode($get_health_analytics_test['health_analytics_test_validation']);
        echo json_encode($response);
    }
    public function uas7_para_list() {
        $response = [];
        $where = ['patient_id' => $this->patient_auth->get_user_id()];
        $config = array();
        $config["base_url"] = site_url() . "patient/uas7_para_list/";
        $config["total_rows"] = $this->patient->get_uas7_para_data($where, true);
        $config["per_page"] = 7;
        $config["uri_segment"] = 3;
        $config['use_page_numbers'] = TRUE;
        $config['reuse_query_string'] = true;
        $config['attributes'] = array('class' => 'page-link');
        $this->pagination->initialize($config);
        $page = ($this->uri->segment(3))? $this->uri->segment(3) : 0;
        if($page != 0) {
            $page = ($page-1) * $config["per_page"];
        }
        $response["links"] = $this->pagination->create_links();
        $result = $this->patient->get_uas7_para_data($where,false, $config["per_page"], $page);
        $uas7_para_data = [];
        foreach ($result as $key => $value) {
            $label_arr = explode(",", $value->patient_diary_label);
            $value_arr = explode(",", $value->patient_diary_value);
            $uas7_val_arr = array_combine($label_arr, $value_arr);
            $uas7_para_data[] = [
                'patient_diary_id' => $value->patient_diary_id,
                'patient_diary_patient_id' => $value->patient_diary_patient_id,
                'patient_diary_added_by' => $value->patient_diary_added_by,
                'patient_diary_date' => $value->patient_diary_date,
                'patient_diary_created_at' => $value->patient_diary_created_at,
                'wheal_label' => 'wheal_count',
                'pruritus_label' => 'pruritus_count',
                'wheal_value' => $uas7_val_arr['wheal_count'],
                'pruritus_value' => $uas7_val_arr['pruritus_count'],
            ];
        }
        $response['status'] = true;
        $response['uas7_score'] = generate_uas7_score($uas7_para_data);
        $view_data['uas7_result'] = $uas7_para_data;
        $response['html_data'] = $this->load->view('patient/uas7diary_list_data_view', $view_data, true);
        echo json_encode($response);
    }

    public function edit_uas7_para($id) {
        $ids = encrypt_decrypt($id, 'decrypt');
        $where['ids'] = explode(",", $ids);
        $where['patient_id'] = $this->patient_auth->get_user_id();
        $result = $this->patient->get_uas7_para_by_ids($where);
        if(get_display_date_time("Y-m-d") != get_display_date_time("Y-m-d", $result->patient_diary_created_at))
            redirect(site_url('patient/uas7diary'));
        $uas7_para_data = [];
        foreach ($result as $key => $value) {
            $label_arr = explode(",", $value->patient_diary_label);
            $value_arr = explode(",", $value->patient_diary_value);
            $uas7_val_arr = array_combine($label_arr, $value_arr);
            $uas7_para_data[] = [
                'patient_diary_id' => $value->patient_diary_id,
                'patient_diary_doctor_id' => $value->patient_diary_doctor_id,
                'is_medsign_doctor' => $value->patient_diary_is_medsign_doctor,
                'patient_diary_date' => $value->patient_diary_date,
                'wheal_label' => 'wheal_count',
                'pruritus_label' => 'pruritus_count',
                'wheal_value' => $uas7_val_arr['wheal_count'],
                'pruritus_value' => $uas7_val_arr['pruritus_count'],
            ];
        }
        if(empty($uas7_para_data))
            redirect(site_url('patient/uas7diary'));
        $view_data = array();
        $view_data['breadcrumbs'] = "Edit UAS7 Parameter";
        $view_data['page_title'] = "Edit UAS7 Parameter";
        $view_data['diff_days'] = 0;
        $view_data['is_update'] = true;
        $view_data['start_date'] = $uas7_para_data[0]['patient_diary_date'];
        $view_data['uas7_para_data'] = $uas7_para_data;
        $view_data['doctor_id'] = $uas7_para_data[0]['patient_diary_doctor_id'];
        $view_data['is_medsign_doctor'] = $uas7_para_data[0]['is_medsign_doctor'];
        $this->load->view('patient/add_uas7_para_view', $view_data);
    }
    public function add_uas7_para($add_date = "") {
        if(!empty($add_date))
            $add_date = encrypt_decrypt($add_date, 'decrypt');
        $where = ['user_id' => $this->patient_auth->get_logged_user_id(), 'detail_type' => 'diary'];
        $columns = "user_id,plan_end_date";
        $last_payment = $this->patient->get_patient_last_payment($columns, $where);
        if(empty($last_payment)) {
            redirect(site_url('patient/utilities_list'));
        }
        if(!empty($last_payment) && $last_payment->plan_end_date < get_display_date_time("Y-m-d"))
            redirect(site_url('patient/uas7diary'));
        $view_data = array();
        $view_data['breadcrumbs'] = "Add UAS7 Parameter";
        $view_data['page_title'] = "Add UAS7 Parameter";
        $view_data['diff_days'] = 0;
        $view_data['start_date'] = get_display_date_time("Y-m-d");
        if($this->input->post('diary_start_date')) {
            $view_data['doctor_id'] = $this->input->post('doctor_id');
            $date_arr = explode('/', $this->input->post('diary_start_date'));
            $date = $date_arr[2].'-'.$date_arr[1].'-'.$date_arr[0];
            $start_date = new DateTime($date);
            $end_date = new DateTime(get_display_date_time("Y-m-d"));
            $diff = $end_date->diff($start_date)->format("%a");
            $view_data['diff_days'] = $diff;
            $view_data['start_date'] = $date;
            $view_data['doctor_name'] = $this->input->post('doctor_name');
            $view_data['doctor_email'] = $this->input->post('doctor_email');
            $view_data['doctor_phone_number'] = $this->input->post('doctor_phone_number');
            $view_data['doctor_address'] = $this->input->post('doctor_address');
            $view_data['is_medsign_doctor'] = !empty($view_data['doctor_id']) ? 1 : 0;
        } else {
            if(!empty($add_date))
                $view_data['start_date'] = $add_date;
            $where = [
                'patient_id' => $this->patient_auth->get_user_id(),
                'diary_type' => 1
            ];
            $columns = "patient_diary_doctor_id,patient_diary_is_medsign_doctor,patient_diary_date";
            $last_para = $this->patient->get_uas7_last_para($columns, $where);
            $view_data['doctor_id'] = $last_para->patient_diary_doctor_id;
            $view_data['is_medsign_doctor'] = $last_para->patient_diary_is_medsign_doctor;
        }
        $this->load->view('patient/add_uas7_para_view', $view_data);
    }
    public function save_uas7_para() {
        $uas7_date = $this->input->post('uas7_date');
        $single_add_date = $this->input->post('single_add_date');
        $wheal_count = $this->input->post('wheal_count');
        $pruritus_count = $this->input->post('pruritus_count');
        $doctor_id = $this->input->post('doctor_id');
        $is_redirect = $this->input->post('is_redirect');
        if(empty($doctor_id)) {
            $inser_data = [
                'non_medsign_doctor_name' => $this->input->post('doctor_name'),
                'non_medsign_doctor_email' => $this->input->post('doctor_email'),
                'non_medsign_doctor_phone_number' => $this->input->post('doctor_phone_number'),
                'non_medsign_doctor_comment' => $this->input->post('doctor_address'),
                'non_medsign_doctor_created_at' => date("Y-m-d H:i:s")
            ];
            $doctor_id = $this->Common_model->insert('me_non_medsign_doctor', $inser_data);
        }
        $uas7_para_insert_data = [];
        $uas7_para_update_data = [];
        $result = $this->patient->get_uas7_param_by_date($uas7_date,$this->patient_auth->get_user_id());
        $wheal_data = [];
        $pruritus = [];
        foreach ($result as $value) {
            if($value->patient_diary_label == "wheal_count") {
                $wheal_data[] = $value;
            } else {
                $pruritus[] = $value;
            }
        }
        if(!empty($doctor_id)) {
            for ($i=0; $i < count($uas7_date); $i++) { 
                if(!empty($single_add_date) && $single_add_date != $uas7_date[$i])
                    continue;
                $key = array_search($uas7_date[$i], array_column($wheal_data, 'patient_diary_date'));
                if(is_numeric($key) && !empty($wheal_data[$key]->patient_diary_id)) {
                    $uas7_para_update_data[] = [
                        'patient_diary_id' => $wheal_data[$key]->patient_diary_id,
                        'patient_diary_value' => $wheal_count[$i],
                        'patient_diary_updated_at' => date("Y-m-d H:i:s")
                    ];
                } else {
                    $uas7_para_insert_data[] = [
                        'patient_diary_patient_id' => $this->patient_auth->get_user_id(),
                        'patient_diary_doctor_id' => $doctor_id,
                        'patient_diary_date' => $uas7_date[$i],
                        'patient_diary_label' => 'wheal_count',
                        'patient_diary_value' => $wheal_count[$i],
                        'patient_diary_created_at' => date("Y-m-d H:i:s"),
                        'patient_diary_type' => 1, //1=UAS7 Para
                        'patient_diary_is_medsign_doctor' => $this->input->post('is_medsign_doctor'),
                        'patient_diary_added_by' => $this->patient_auth->get_user_id()
                    ];
                }
                if(is_numeric($key) && !empty($pruritus[$key]->patient_diary_id)) {
                    $uas7_para_update_data[] = [
                        'patient_diary_id' => $pruritus[$key]->patient_diary_id,
                        'patient_diary_value' => $pruritus_count[$i],
                        'patient_diary_updated_at' => date("Y-m-d H:i:s")
                    ];
                } else {
                    $uas7_para_insert_data[] = [
                        'patient_diary_patient_id' => $this->patient_auth->get_user_id(),
                        'patient_diary_doctor_id' => $doctor_id,
                        'patient_diary_date' => $uas7_date[$i],
                        'patient_diary_label' => 'pruritus_count',
                        'patient_diary_value' => $pruritus_count[$i],
                        'patient_diary_created_at' => date("Y-m-d H:i:s"),
                        'patient_diary_type' => 1, //1=UAS7 Para
                        'patient_diary_is_medsign_doctor' => $this->input->post('is_medsign_doctor'),
                        'patient_diary_added_by' => $this->patient_auth->get_user_id()
                    ];
                }
            }
        }
        if(count($uas7_para_insert_data) > 0 || count($uas7_para_update_data) > 0) {
            if(count($uas7_para_insert_data) > 0)
                $this->Common_model->insert_multiple('me_patient_diary', $uas7_para_insert_data);
            if(count($uas7_para_update_data) > 0)
                $this->patient->uas7_bulk_update($uas7_para_update_data);
            if($this->input->post('is_update') == 1)
                $this->session->set_flashdata('message', 'UAS7 parameters updated successfully');
            else
                $this->session->set_flashdata('message', 'UAS7 parameters added successfully');

            $date = get_display_date_time('Y_m_d');
            $file_name = "UAS7_".$this->patient_auth->get_user_id()."_".$date.".pdf";
            $upload_folder = UAS7_FOLDER . "/" . $this->patient_auth->get_user_id();
            delete_file_from_s3($upload_folder."/".$file_name);
            $response['status'] = true;
            $response['is_redirect'] = $is_redirect;
            $response['doctor_id'] = $doctor_id;
        } else {
            $this->session->set_flashdata('error', 'No any UAS7 parameters found');
            $response['status'] = true;
        }
        echo json_encode($response);
    }

    public function search_doctors() {
        $response = array();
        if($this->input->post('query')) {
            $search = str_replace(['dr. ','dr.', 'dr ','dr'], ['','','',''], strtolower($this->input->post('query')));
            // echo $search;die;
            $records = $this->patient->search_medsign_doctors(trim($search));
            foreach($records as $row ) {
                $response[] = array("Id" => $row->user_id, "Name" => $row->doctor_name);
            }
        }
        echo json_encode($response);
    }
}