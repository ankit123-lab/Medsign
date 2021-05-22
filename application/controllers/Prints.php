<?php
use Mpdf\QrCode\QrCode;
use Mpdf\QrCode\Output;
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * 
 * This controller use for common apis
 * 
 * Modified Date :- 2018-08-02
 * 
 */
class Prints extends CI_Controller {

    public function __construct() {
        parent::__construct();

        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', 0);

        ignore_user_abort(true);
        set_time_limit(0);
    }

    /**
     
     * 
     * Modified Date :- 2018-08-02
     * 
     */
    public function charting($requested_data = '') {
        if(!empty($requested_data)){
            $requested_data = json_decode(base64_decode($requested_data), true);
            if(!empty($requested_data['isPatientView'])){
                $appointment_id = $requested_data['appointment_id'];
                $isShare = false;
            } elseif(!empty($requested_data['isCallFromApi'])){
                $appointment_id = $requested_data['appointment_id'];
                $isShare = false;
                $isGeneratePastPrescription = true;
            } else {
                $appointment_id = $requested_data['appointment_id'];
                $language_id = $requested_data['language_id'];
                $with_vitalsign = $requested_data['with_vitalsign'];
                $with_clinicnote = $requested_data['with_clinicnote'];
                $with_only_diagnosis = $requested_data['with_only_diagnosis'];
                $with_prescription = $requested_data['with_prescription'];
                $with_generic = $requested_data['with_generic'];
                $with_patient_lab_orders = $requested_data['with_patient_lab_orders'];
                $with_procedure = $requested_data['with_procedure'];
                $with_signature = $requested_data['with_signature'];
                $with_anatomy_diagram = $requested_data['with_anatomy_diagram'];
                $isShare = true;
            }
        } else {
            $appointment_id = $this->input->get("appointment_id");
            $language_id = $this->input->get("language_id");
            $with_vitalsign = $this->input->get("with_vitalsign");
            $with_clinicnote = $this->input->get("with_clinicnote");
            $with_only_diagnosis = $this->input->get("with_only_diagnosis");
            $with_prescription = $this->input->get("with_prescription");
            $with_generic = $this->input->get("with_generic");
            $with_patient_lab_orders = $this->input->get("with_patient_lab_orders");
            $with_procedure = $this->input->get("with_procedure");
            $with_signature = $this->input->get("with_signature");
            $with_anatomy_diagram = $this->input->get("with_anatomy_diagram");
            $isShare = false;
            $isDoctorView = true;
        }
        //check appointment with doctor
        $appointment_whare = [
            'appointment_id' => $appointment_id
        ];
        $check_patient_appointment = $this->Common_model->check_paient_appointment($appointment_whare);
        if (empty($check_patient_appointment)) {
            http_response_code(404);
            die();
        }
        if(!empty($requested_data['isPatientView']) || !empty($requested_data['isCallFromApi'])) {
            $check_patient_appointment['share_status_json'];
            if(!empty($check_patient_appointment['share_status_json'])) {
                $share_status = json_decode($check_patient_appointment['share_status_json']);
            } else {
                $share_status = [];
            }
            $language_id = 1;
            if(!empty($share_status->language_id)){
                $language_id = $share_status->language_id;
            }
            $with_vitalsign = 'false';
            if(empty($share_status) || (!empty($share_status->vital) && $share_status->vital == 1)) {
                $with_vitalsign = 'true';
            }
            $with_clinicnote = 'false';
            if(empty($share_status) || (!empty($share_status->clinical_note) && $share_status->clinical_note == 1)) {
                $with_clinicnote = 'true';
            }
            $with_only_diagnosis = 'false';
            if(empty($share_status) || (!empty($share_status->only_diagnosis) && $share_status->only_diagnosis == 1)) {
                $with_only_diagnosis = 'true';
            }
            $with_prescription = 'false';
            if(empty($share_status) || (!empty($share_status->prescriptions) && $share_status->prescriptions == 1)) {
                $with_prescription = 'true';
            }
            $with_generic = 'false';
            if(empty($share_status) || (!empty($share_status->generic) && $share_status->generic == 1)) {
                $with_generic = 'true';
            }
            $with_patient_lab_orders = 'false';
            if(empty($share_status) || (!empty($share_status->investigations) && $share_status->investigations == 1)) {
                $with_patient_lab_orders = 'true';
            }
            $with_procedure = 'false';
            if(empty($share_status) || (!empty($share_status->procedures) && $share_status->procedures == 1)) {
                $with_procedure = 'true';
            }
            $with_signature = 'false';
            if(empty($share_status) || (!empty($share_status->signature) && $share_status->signature == 1)) {
                $with_signature = 'true';
            }
            $with_anatomy_diagram = 'false';
            if(empty($share_status) || (!empty($share_status->patient_tools) && $share_status->patient_tools == 1)) {
                $with_anatomy_diagram = 'true';
            }
        }
        $doctor_id = $check_patient_appointment['appointment_doctor_user_id'];
        $patient_id = $check_patient_appointment['appointment_user_id'];
        $appointment_type = $check_patient_appointment['appointment_type'];
        $prescription_template_id = 1;
        $header_space = 0.8; //CM
        $footer_space = 0.8; //CM
        $left_space = 1; //CM
        $right_space = 1; //CM
        $printSettingArr = [];
        if(!empty($check_patient_appointment['setting_data'])) {
            $printDataArr = json_decode($check_patient_appointment['setting_data'],true);
            if(!empty($printDataArr[0])) {
                $key = array_search($appointment_type, array_column($printDataArr, 'appointment_type'));
                if(is_numeric($key) && isset($key) && $key >= 0) {
                    $printSettingArr = (object) $printDataArr[$key];
                }
            } else {
                $printSettingArr = json_decode($check_patient_appointment['setting_data']);
            }
            // echo "<pre>";
            // print_r($printSettingArr);die;
            if($printSettingArr->template_id)
                $prescription_template_id = $printSettingArr->template_id;
            if(!empty($printSettingArr->header_space) && $printSettingArr->header_space > 0) {
                $header_space = $printSettingArr->header_space;
            }
            if(!empty($printSettingArr->footer_space) && $printSettingArr->footer_space > 0) {
                $footer_space = $printSettingArr->footer_space;
            }
            if(!empty($printSettingArr->left_space) && $printSettingArr->left_space > 0) {
                $left_space = $printSettingArr->left_space;
            }
            if(!empty($printSettingArr->right_space) && $printSettingArr->right_space > 0) {
                $right_space = $printSettingArr->right_space;
            }
        }
        $prescriptionTemplate = $this->Common_model->get_single_row('me_prescription_template', 'template_header,template_footer,template_image', ['template_id' => $prescription_template_id, 'template_status' => 1]);
        // print_r($prescriptionTemplate);
        // die;
        $header_margin = 0;
        $margin_left = 10;
        $margin_right = 10;
        $footer_margin = 5;
        // $hide_header_footer = $this->input->get("hide_header_footer");
        if(isset($header_space) || isset($footer_space) || isset($left_space) || isset($right_space)) {
            $MilliMeters = 10; //1 CM = 10 MM
            $header_margin = (!empty($header_space) && $header_space > 0) ? ($MilliMeters * $header_space) : 0; // Conver CM to MM
            $footer_margin = (!empty($footer_space) && $footer_space > 0) ? $MilliMeters * $footer_space : 0; // Conver CM to MM
            $margin_left = (!empty($left_space) && $left_space > 0) ? $MilliMeters * $left_space : 0; // Conver CM to MM
            $margin_right = (!empty($right_space) && $right_space > 0) ? $MilliMeters * $right_space : 0; // Conver CM to MM
        }
        $with_teleCunsultation = (!empty($check_patient_appointment['appointment_type']) && in_array($check_patient_appointment['appointment_type'], [4,5])) ? true : false;

        $doctor_speciality = !empty($check_patient_appointment['doctor_detail_speciality']) ? $check_patient_appointment['doctor_detail_speciality'] : '-';
        $patient_data_where = ['user_id' => $patient_id, 'appointment_id' => $appointment_id];
        $get_patient_data = $this->Common_model->get_patient_data($patient_data_where);
        $view_data = array(
            "doctor_data" => $check_patient_appointment,
            "patient_data" => $get_patient_data,
            "vitalsign_data" => array(),
            "clinicnote_data" => array(),
            "prescription_data" => array(),
            "patient_lab_orders_data" => array(),
            "files_data" => array(),
            "printSettingData" => $printSettingArr,
            "with_signature" => $with_signature
        );
        $view_data['reports'] = array();
        if(!empty($with_anatomy_diagram) && $with_anatomy_diagram == 'true') {
            $view_data['reports'] = $this->Common_model->get_anatomy_diagram($appointment_id);
            $patient_tool_document = $this->Common_model->get_all_rows('me_patient_documents_shared','id', ['appointment_id'=> $appointment_id]);
            $view_data['patient_tool_document'] = $patient_tool_document;
        }
        if (!empty($with_vitalsign) && $with_vitalsign == 'true' && !empty($get_patient_data['vital_report_id'])) {
            $view_data['vitalsign_data'] = [
                'vital_report_weight' => $get_patient_data['vital_report_weight'],
                'vital_report_bloodpressure_systolic' => $get_patient_data['vital_report_bloodpressure_systolic'],
                'vital_report_bloodpressure_diastolic' => $get_patient_data['vital_report_bloodpressure_diastolic'],
                'vital_report_pulse' => $get_patient_data['vital_report_pulse'],
                'vital_report_temperature' => $get_patient_data['vital_report_temperature'],
                'vital_report_temperature_type' => $get_patient_data['vital_report_temperature_type'],
                'vital_report_resp_rate' => $get_patient_data['vital_report_resp_rate'],
            ];
        }
        $view_data['with_clinicnote'] = $with_clinicnote;
        $view_data['with_only_diagnosis'] = $with_only_diagnosis;
        $view_data['with_generic'] = $with_generic;
        if ((!empty($with_clinicnote) && $with_clinicnote == 'true') || (!empty($with_only_diagnosis) && $with_only_diagnosis == 'true')) {
            if(!empty($get_patient_data['clinical_notes_reports_id'])) {
                $view_data['clinicnote_data'] = [
                    'clinical_notes_reports_kco' => $get_patient_data['clinical_notes_reports_kco'],
                    'clinical_notes_reports_complaints' => $get_patient_data['clinical_notes_reports_complaints'],
                    'clinical_notes_reports_observation' => $get_patient_data['clinical_notes_reports_observation'],
                    'clinical_notes_reports_diagnoses' => $get_patient_data['clinical_notes_reports_diagnoses'],
                    'clinical_notes_reports_add_notes' => $get_patient_data['clinical_notes_reports_add_notes'],
                ];
            }
        }

        if (!empty($with_prescription) && $with_prescription == 'true') {
            $patient_prescription = $this->Common_model->get_patient_prescription($appointment_id, $with_generic);
            $view_data['prescription_data'] = $patient_prescription;
        }

        if (!empty($with_patient_lab_orders) && $with_patient_lab_orders == 'true' && !empty($get_patient_data['lab_report_id'])) {
            $view_data['patient_lab_orders_data'] = [
                'lab_report_test_name' => $get_patient_data['lab_report_test_name']
            ];
        }

        if (!empty($with_procedure) && $with_procedure == 'true' && !empty($get_patient_data['lab_report_id'])) {
            $view_data['procedure_data'] = [
                'procedure_report_procedure_text' => $get_patient_data['procedure_report_procedure_text'],
                'procedure_report_note' => $get_patient_data['procedure_report_note'],
            ];
        }

        if($with_teleCunsultation){
            $view_data['teleConsultationMsg'] = 'The prescription is given on telephonic consultation.';
        }
        
        if(!empty($get_patient_data['patient_analytics_id'])) {
            $share_link_row = $this->Common_model->get_single_row('me_patient_share_link_log', 'id,unique_code,doctor_id', ['patient_id' => $patient_id]);
            $reset_token = str_rand_access_token(20);
            if(empty($share_link_row['id'])) {
                $share_link_data = array(
                    'patient_id' => $patient_id,
                    'doctor_id' => $doctor_id,
                    'share_clinic_id' => $check_patient_appointment['appointment_clinic_id'],
                    'unique_code' => $reset_token,
                    'is_set_password' => 0,
                    'created_at' => date("Y-m-d H:i:s")
                );
                $this->Common_model->insert('me_patient_share_link_log', $share_link_data);
            } else {
                if($share_link_row['doctor_id'] == $doctor_id) {
                    $reset_token = $share_link_row['unique_code'];
                } else {
                    $share_link_data = array(
                        'doctor_id' => $doctor_id,
                        'share_clinic_id' => $check_patient_appointment['appointment_clinic_id'],
                        'unique_code' => $reset_token,
                        'status' => 1,
                        'updated_at' => date("Y-m-d H:i:s")
                    );
                    $this->Common_model->update('me_patient_share_link_log', $share_link_data, array('id' => $share_link_row['id']));
                }
            }
            $view_data['patient_share_link'] = MEDSIGN_WEB_CARE_URL . 'pt/'. $reset_token.'_uas7';
        }
        $view_data['billing_data'] = array();
        if(!empty($language_id)){
            $languages = $this->Common_model->get_single_row('me_languages', 'LOWER(language_code) AS language_code', ['language_id' => $language_id]);
            $view_data['language_id'] = $language_id;
        } else {
            $view_data['language_id'] = 1;
        }

        if(!empty($languages) && !empty($languages['language_code'])) {
            $language_code = $languages['language_code'];
            if(!empty($language_code) && $language_code == 'hi')
                $language_code = 'hn';
            $view_data['language_code'] = $language_code;        
        } else {
            $view_data['language_code'] = 'en';
        }
        $patient_link_enable = $this->Common_model->get_single_row('me_global_settings','global_setting_value', ['global_setting_key'=> 'patient_link_enable']);
        $doctor_data = $view_data['doctor_data'];
        $doctor_data['doctor_detail_speciality'] = str_replace(',', ', ', $doctor_data['doctor_detail_speciality']);
        $doctor_data['doctor_qualification'] = str_replace(',', ', ', $doctor_data['doctor_qualification']);
        $address_data = [
            'address_name' => $doctor_data['address_name'],
            'address_name_one' => $doctor_data['address_name_one'],
            'address_locality' => $doctor_data['address_locality'],
            'city_name' => $doctor_data['city_name'],
            'state_name' => $doctor_data['state_name'],
            'address_pincode' => $doctor_data['address_pincode']
        ];
        $headerTitle = '';
        $leftLogo = '';
        $centerLogo = '';
        $rightLogo = '';
        $margin_header = 0;
        $margin_top = 0;
        $isHideHeaderBorder = false;
        $isSetAutoTopMargin = false;
        if(!empty($printSettingArr->hide_header_footer) && !empty($isDoctorView)) {
            $margin_top = $header_margin;
            $headerLeftContent = '';
            $headerRightContent = '';
            $isHideHeaderBorder = true;
        } else {
            if(!empty($printSettingArr->header_left_check) || !empty($printSettingArr->header_right_check) || !empty($printSettingArr->header_title_check)) {
                $margin_header = $header_margin;
                $margin_top = 0;
                // $isSetAutoTopMargin = true;
                if(!empty($printSettingArr->header_left_text) && !empty($printSettingArr->header_right_text) && !empty($printSettingArr->header_title_text)){
                    // $isHideHeaderBorder = true;
                } elseif(!empty($printSettingArr->header_left_text) || !empty($printSettingArr->header_right_text) || !empty($printSettingArr->header_title_text)) {
                    $isHideHeaderBorder = false;
                }
            } elseif (empty($printSettingArr->header_left_check) && empty($printSettingArr->header_right_check) && empty($printSettingArr->header_title_check)) {
                $margin_header = $header_margin;
                $margin_top = 0;
                $isSetAutoTopMargin = true;
                $isHideHeaderBorder = false;
            } else {
                $margin_header = 0;
                $margin_top = $header_margin;
                // $isHideHeaderBorder = true;
            }
            if(empty($isDoctorView) && empty($printSettingArr->header_left_text) && !empty($printSettingArr->logo_position) && $printSettingArr->logo_position == 'left' && !empty($printSettingArr->logo_img_path)){
                $headerLeftContent = '';
            } elseif(empty($printSettingArr->header_left_text) && empty($isDoctorView)) {
                $headerLeftContent = DOCTOR." ".  $doctor_data['user_first_name'] . " " . $doctor_data['user_last_name'] . "<br>";
                $headerLeftContent .= 'Reg. No. '.$doctor_data['doctor_regno'] . "<br>";
                $headerLeftContent .= $doctor_data['doctor_detail_speciality'] . "<br>";
                $headerLeftContent .= $doctor_data['doctor_qualification'];

            } elseif(!empty($printSettingArr->header_left_check)) {
                $headerLeftContent = nl2br($printSettingArr->header_left_text);
            } else {
                $headerLeftContent = DOCTOR." ".  $doctor_data['user_first_name'] . " " . $doctor_data['user_last_name'] . "<br>";
                $headerLeftContent .= 'Reg. No. '.$doctor_data['doctor_regno'] . "<br>";
                $headerLeftContent .= $doctor_data['doctor_detail_speciality'] . "<br>";
                $headerLeftContent .= $doctor_data['doctor_qualification'];
            }
            if(empty($isDoctorView) && empty($printSettingArr->header_right_text) && !empty($printSettingArr->logo_position) && $printSettingArr->logo_position == 'right' && !empty($printSettingArr->logo_img_path)){
                $headerRightContent = '';
            } elseif(empty($printSettingArr->header_right_text) && empty($isDoctorView)) {
                $headerRightContent = $doctor_data['clinic_name'] . "<br>";
                $headerRightContent .= clinic_address($address_data) . "<br>";
                $headerRightContent .= $doctor_data['clinic_contact_number'] . ", " . $doctor_data['clinic_email'];
            } elseif(!empty($printSettingArr->header_right_check)) {
                $headerRightContent = nl2br($printSettingArr->header_right_text);
            } else {
                $headerRightContent = $doctor_data['clinic_name'] . "<br>";
                $headerRightContent .= clinic_address($address_data) . "<br>";
                $headerRightContent .= $doctor_data['clinic_contact_number'] . ", " . $doctor_data['clinic_email'];
            }
            if(!empty($printSettingArr->header_title_check)) {
                $headerTitle = str_replace(['<p style="','<p>'], ['<p style="margin:0;','<p style="margin:0;">'], $printSettingArr->header_title);
            }
            if(!empty($printSettingArr->logo_img_path)) {
                $logo_img_path = get_file_full_path($printSettingArr->logo_img_path);
                if(!empty($printSettingArr->logo_width)) {
                    $logoWidth = $printSettingArr->logo_width*3;
                } else {
                    $logoWidth = 150;
                }
                if(!empty($printSettingArr->logo_position) && $printSettingArr->logo_position == 'left'){
                    $leftLogo = '<img src="'.$logo_img_path.'" style="width: ' . $logoWidth . 'px;">';
                    $isSetAutoTopMargin = true;
                    $isHideHeaderBorder = false;
                    $margin_header = $header_margin;
                    $margin_top = 0;
                    $isLogoSet = true;
                } elseif(!empty($printSettingArr->logo_position) && $printSettingArr->logo_position == 'center'){
                    $centerLogo = '<img src="'.$logo_img_path.'" style="width: ' . $logoWidth . 'px;">';
                    $isSetAutoTopMargin = true;
                    $isHideHeaderBorder = false;
                    $margin_header = $header_margin;
                    $margin_top = 0;
                    $isLogoSet = true;
                } elseif(!empty($printSettingArr->logo_position) && $printSettingArr->logo_position == 'right'){
                    $rightLogo = '<img src="'.$logo_img_path.'" style="width: ' . $logoWidth . 'px;">';
                    $isSetAutoTopMargin = true;
                    $isHideHeaderBorder = false;
                    $margin_header = $header_margin;
                    $margin_top = 0;
                    $isLogoSet = true;
                }
            }
            if(!empty($isDoctorView) && empty($printSettingArr->header_title) && empty($printSettingArr->header_left_text) && empty($printSettingArr->header_right_text)) {
                $margin_top = $header_margin;
                $isHideHeaderBorder = true;
                if(empty($printSettingArr->header_title_check) && empty($printSettingArr->header_left_check) && empty($printSettingArr->header_right_check)){
                    $isHideHeaderBorder = false;
                    $margin_top = 0;
                }
                if(!empty($printSettingArr->logo_position) && $printSettingArr->logo_position != 'none' && !empty($printSettingArr->logo_img_path))
                    $isHideHeaderBorder = false;
                if(!empty($isLogoSet)){
                    $isSetAutoTopMargin = true;
                    $margin_top = 0;
                }
            } else {
                $isSetAutoTopMargin = true;
            }
            if(empty($isDoctorView) && empty($printSettingArr->header_title) && empty($printSettingArr->header_left_text) && empty($printSettingArr->header_right_text)) {
                $margin_header = 8;
            }
        }
        $font_size_2 = 14;
        if(!empty($printSettingArr->font_size_2))
            $font_size_2 = $printSettingArr->font_size_2;
        $font_size_3 = 10;
        if(!empty($printSettingArr->font_size_3))
            $font_size_3 = $printSettingArr->font_size_3;
        $header_parse_arr = [
            '{header_title}' => $headerTitle,
            '{left_logo}' => $leftLogo,
            '{header_left}' => $headerLeftContent,
            '{center_logo}' => $centerLogo,
            '{header_right}' => $headerRightContent,
            '{right_logo}' => $rightLogo,
            '{font_size_2}' => 'font-size: '.$font_size_2.'px;'
        ];
        if($isHideHeaderBorder) {
            $header_parse_arr['border-bottom:1px solid #000;'] = '';
        }
        $prescriptionHeader = replace_values_in_string($header_parse_arr, $prescriptionTemplate['template_header']);
        /*require_once MPDF_PATH;
        $lang_code = 'en-GB';
        $mpdf = new MPDF(
                $lang_code, // mode - default '' //sd
                'A4', // format - A4, for example, default ''
                0, // font size - default 0
                'arial', // default font family
                8, // margin_left
                8, // margin right
                $header_margin, // margin top
                8, // margin bottom
                8, // margin header
                $footer_margin, // margin footer
                'P'   // L - landscape, P - portrait
        );*/
        $defaultConfig = (new \Mpdf\Config\ConfigVariables())->getDefaults();
        $fontDirs = $defaultConfig['fontDir'];
        $defaultFontConfig = (new \Mpdf\Config\FontVariables())->getDefaults();
        $fontData = $defaultFontConfig['fontdata'];
        //Default set Gotham font
        $fontArray = [
            'gotham_book' => [
                'R' => 'Gotham-Book.ttf',
                'B' => 'Gotham-Medium.ttf',
                'I' => 'GothamBookItalic.ttf',
            ],
            'gotham_medium' => [
                'R' => 'Gotham-Medium.ttf',
            ],
            'gotham_light' => [
                'R' => 'Gotham-Light.ttf',
            ],
        ];
        if(!empty($printSettingArr->font_family) && $printSettingArr->font_family == "gotham_book") {
            //gotham 1
            $fontArray = [
                'gotham_book' => [
                    'R' => 'Gotham-Book.ttf',
                    'B' => 'Gotham-Medium.ttf',
                    'I' => 'GothamBookItalic.ttf',
                ],
                'gotham_medium' => [
                    'R' => 'Gotham-Medium.ttf',
                ],
                'gotham_light' => [
                    'R' => 'Gotham-Light.ttf',
                ],
            ];
            //gotham 2
            /*$fontArray = [
                'gotham_book' => [
                    'R' => 'Gotham-Book.ttf',
                    'B' => 'Gotham-Medium.ttf',
                ],
                'gotham_medium' => [
                    'R' => 'Gotham-Medium.ttf',
                ],
                'gotham_light' => [
                    'R' => 'Gotham-Light.ttf',
                ],
            ];*/
            //gotham 3
            /*$fontArray = [
                'gotham_book' => [
                    'R' => 'Gotham-Book.ttf',
                    'B' => 'Gotham-Medium.ttf',
                ],
                'gotham_medium' => [
                    'R' => 'Gotham-Book.ttf',
                    'B' => 'Gotham-Medium.ttf',
                ],
                'gotham_light' => [
                    'R' => 'Gotham-Light.ttf',
                ],
            ];*/
            //gotham 4
            /*$fontArray = [
                'gotham_book' => [
                    'R' => 'Gotham-Book.ttf',
                    'B' => 'Gotham-Medium.ttf',
                    'I' => 'GothamBookItalic.ttf',
                ],
                'gotham_medium' => [
                    'R' => 'Gotham-Book.ttf',
                    'B' => 'Gotham-Medium.ttf',
                    'I' => 'GothamMediumItalic.ttf',
                ],
                'gotham_light' => [
                    'R' => 'Gotham-Light.ttf',
                    'I' => 'GothamLightItalic.ttf',
                ],
            ];*/
        } elseif(!empty($printSettingArr->font_family) && $printSettingArr->font_family == "Arial") {
            $fontArray = [
                'Arial' => [
                    'R' => 'ARIAL-REGULAR.TTF',
                    'B' => 'ARIAL-BOLD.TTF',
                    'I' => 'ARIAL-ITALIC.TTF',
                ],
            ];
            //arial 2
            /*$fontArray = [
                'Arial' => [
                    'R' => 'Arial-CE-Regular.ttf',
                    'B' => 'Arial-CE-Bold.ttf',
                    'I' => 'Arial-CE-Italic.ttf',
                ],
            ];*/
        } elseif(!empty($printSettingArr->font_family) && $printSettingArr->font_family == "Calibri") {
            $fontArray = [
                'Calibri' => [
                    'R' => 'Calibri.ttf',
                    'B' => 'calibrib.ttf',
                    'I' => 'calibril.ttf',
                ],
            ];
        } elseif(!empty($printSettingArr->font_family) && $printSettingArr->font_family == "Times New Roman") {
            $fontArray = [
                'Times New Roman' => [
                    'R' => 'times new roman.ttf',
                    'B' => 'times new roman bold.ttf',
                    'I' => 'times new roman italic.ttf',
                    'BI' => 'times new roman bold italic.ttf',
                ],
            ];
        }
        $format = "A4";
        if(!empty($printSettingArr->page_type) && $printSettingArr->page_type == "A5") {
            $format = [180,250]; //18cm x 25cm
        } elseif (!empty($printSettingArr->page_type)) {
            $format = $printSettingArr->page_type;
        }
        $mpdf = new \Mpdf\Mpdf([
                'tempDir' => DOCROOT_PATH . 'uploads',
                'fontDir' => array_merge($fontDirs, [
                DOCROOT_PATH.'assets/fonts'
            ]),
            'fontdata' => $fontData + $fontArray,
            'default_font' => 'arial',
            'mode' => 'en-GB',
            'format' => $format,
            'orientation' => !empty($printSettingArr->orientation) ? $printSettingArr->orientation : 'P',
            'margin_top' => $margin_top,
            'margin_bottom' => 0,
            'margin_left' => $margin_left,
            'margin_right' => $margin_right,
            'margin_header' => $margin_header,
            'margin_footer' => $footer_margin
        ]);
        $mpdf->useOnlyCoreFonts = true;
        if($isSetAutoTopMargin)
            $mpdf->setAutoTopMargin = true;
        $mpdf->SetDisplayMode('real');
        $mpdf->list_indent_first_level = 0;
        $mpdf->setAutoBottomMargin = 'stretch';
        if(!empty($printSettingArr->watermark_check) && !empty($printSettingArr->watermark_img_path)) {
            $opacity = 0.2;
            if(!empty($printSettingArr->watermark_opacity))
                $opacity = $printSettingArr->watermark_opacity/100;
            $mpdf->SetWatermarkImage(get_image_thumb($printSettingArr->watermark_img_path),$opacity,'P','P');
            $mpdf->showWatermarkImage = true;
        }
        $date_and_time = date('m/d/Y h:i:s a', time());
        $patient_name = $view_data['patient_data']['user_first_name'];
        $mpdf->SetTitle('Rx_'.$patient_name.'_'.$date_and_time);
        // $mpdf->SetHTMLHeader($prescriptionHeader);
        $view_data['prescriptionHeader'] = $prescriptionHeader;
        $patient_link_data = "";
        $no_footer_data = "";
        if(!empty($patient_link_enable) && $patient_link_enable['global_setting_value'] == "1"){
            $patient_link_data = '<tr>
                <td align="center" colspan="3" width="100%" style="font-size:10px">
                    <b>Please Visit MedSign Patient: </b> <a target="_blank" href="'.MEDSIGN_WEB_CARE_URL . '">' . MEDSIGN_WEB_CARE_URL . '</a>
                </td>
                </tr>';
        }
        if (!empty($printSettingArr->hide_header_footer) || !empty($printSettingArr->footer_content_check)) {
            $footer_parse_arr = [
                '{footer_content}' => !empty($printSettingArr->footer_content) ? $printSettingArr->footer_content : '',
                '{font_size_3}' => 'font-size: '.$font_size_3.'px;'
            ];
            $prescriptionFooter = replace_values_in_string($footer_parse_arr, $prescriptionTemplate['template_footer']);
            // $mpdf->SetHTMLFooter($prescriptionFooter);
            $view_data['prescriptionFooter'] = $prescriptionFooter;
        } else {
            /*$mpdf->SetHTMLFooter('
                <table width="100%" style="font-size: '.$font_size_3.'px;">
                    ' . $patient_link_data . '
                    <tr>
                        <td width="33%">
                            Generated On: {DATE d/m/Y}
                        </td>
                        <td width="33%" align="center">Page No. {PAGENO}/{nbpg}</td>
                        <td width="33%" align="right">Powerd by Medsign</td>
                    </tr>
                </table>
            ');*/
            $view_data['prescriptionFooter'] = '<table width="100%" style="font-size: '.$font_size_3.'px;">
                    ' . $patient_link_data . '
                    <tr>
                        <td width="33%">
                            Generated On: {DATE d/m/Y}
                        </td>
                        <td width="33%" align="center">Page No. {PAGENO}/{nbpg}</td>
                        <td width="33%" align="right">Powerd by Medsign</td>
                    </tr>
                </table>';
        }
        //echo $view_html;exit;
        $fileName = 'Rx_'.$patient_name.'_'.$date_and_time;
        $view_html = $this->load->view("prints/charting", $view_data, true);
        $mpdf->WriteHTML($view_html);
        if(!empty($isShare)) {
            $this->load->model('Emailsetting_model');
            $email_template_data = $this->Emailsetting_model->get_emailtemplate_by_id(19);
            $patient_name = $get_patient_data['user_first_name'] . ' ' . $get_patient_data['user_last_name'];
            $pdf_name = 'RX_' . $patient_name . '.pdf';
            $upload_path = DOCROOT_PATH . 'uploads/' . PATIENT_PRESCRIPTION_SHARE . '/' . $requested_data['patient_id'] . '/';
            if (!file_exists($upload_path)) {
                mkdir($upload_path, 0777, TRUE);
                chmod($upload_path, 0777);
            }
            $share_file_name = $requested_data['appointment_id'] . '_'. time() . '.pdf';
            $file_name = $share_file_name;
            $mpdf->Output($upload_path . $file_name, 'F');
            $attachment_path = $upload_path . $file_name;
            if (!empty($attachment_path)) {
                $patient_email = $get_patient_data['user_email'];
                $user_phone_number = $get_patient_data['user_phone_number'];
                $doctor_name = DOCTOR . $check_patient_appointment['user_first_name'] . ' ' . $check_patient_appointment['user_last_name'];
                $email = $requested_data['email'];
                $clinic_name = $view_data['doctor_data']['clinic_name'];
                if(!empty($requested_data['share_via']) && !empty($email) && ($requested_data['share_via'] == 'email' || $requested_data['share_via'] == 'emailSms')) {
                    $parse_arr = array(
                        '{PatientName}' => $patient_name,
                        '{DoctorName}' => $doctor_name,
                        '{ClinicName}' => $clinic_name,
                        '{WebUrl}' => DOMAIN_URL,
                        '{AppName}' => APP_NAME,
                        '{MailContactNumber}' => $email_template_data['email_static_data']['contact_number'],
                        '{MailEmailAddress}' => $email_template_data['email_static_data']['email_id'],
                        '{MailCompanyName}' => $email_template_data['email_static_data']['company_name'],
                        '{CopyRightsYear}' => date('Y')
                    );

                    $message = replace_values_in_string($parse_arr, $email_template_data['email_template_message']);
                    $subject = $email_template_data['email_template_subject'];

                    $attachment_path_array = array($attachment_path, $pdf_name);
                    $this->send_attachment_email(array($email => $email), $subject, $message, $attachment_path_array);
                }
                if(!empty($requested_data['share_via']) && !empty($requested_data['mobile_no']) && ($requested_data['share_via'] == 'sms' || $requested_data['share_via'] == 'whatsapp' || $requested_data['share_via'] == 'emailSms')){
                    if(IS_S3_UPLOAD)
                        upload_to_s3($attachment_path, PATIENT_PRESCRIPTION_SHARE.'/'.$requested_data['patient_id'].'/'.$share_file_name);
                    $pdf_url = PATIENT_PRESCRIPTION_SHARE.'/'.$requested_data['patient_id'].'/'.$share_file_name;
                    $share_data = array(
                        'doctor_id' => $requested_data['doctor_id'],
                        'patient_id' => $requested_data['patient_id'],
                        'appointment_id' => $requested_data['appointment_id'],
                        'file_name' => $share_file_name,
                        'file_url' => get_file_json_detail($pdf_url),
                        'created_at' => date('Y-m-d H:i:s')
                    );
                    $last_id = $this->Common_model->insert('me_patient_record_share', $share_data);
                    $download_link = create_shorturl(DOMAIN_URL.'dp/' . base64_encode(json_encode($last_id)));
                    /*Share prescription via SMS or Whatsapp*/
                    if(!empty($requested_data['share_via']) && ($requested_data['share_via'] == 'sms' || $requested_data['share_via'] == 'emailSms')) {
                        $send_message['phone_number'] = $requested_data['mobile_no'];
                        $send_message['message'] = sprintf(lang('shared_prescription'), $patient_name, $doctor_name, short_clinic_name($clinic_name), get_display_date_time("d/m/Y h:i A"), $download_link);
                        $send_message['whatsapp_sms_body'] = sprintf(lang('template_31_shared_prescription'), $patient_name, $doctor_name, $clinic_name, get_display_date_time("d/m/Y h:i A"), $download_link);
                        $send_message['doctor_id'] = $requested_data['doctor_id'];
                        $send_message['user_type'] = 2;
                        $send_message['patient_id'] = $requested_data['patient_id'];
                        $send_message['is_sms_count'] = true;
                        $send_message['is_check_sms_credit'] = $GLOBALS['ENV_VARS']['CHECK_SMS_CREDIT']['PRESCRIPTION_SHARE'];
                        if(!empty($requested_data['mobile_no']))
                            send_communication($send_message);
                    } elseif(!empty($requested_data['share_via']) && $requested_data['share_via'] == 'whatsapp') {
                        
                    }
                    /*END Share prescription via SMS or Whatsapp*/
                }
                if(!IS_SERVER_UPLOAD)
                    unlink($attachment_path);
                if((($requested_data['share_via'] == 'emailSms' || $requested_data['share_via'] == 'emailSms') && strtolower($email) != strtolower($patient_email)) || (($requested_data['share_via'] == 'sms' || $requested_data['share_via'] == 'emailSms') && $requested_data['mobile_no'] != $user_phone_number)) {
                    if(is_send_email_to_patient($requested_data['doctor_id'])) {
                        /*Start Patient Details Share Mail Send to Patient*/
                        $email = $patient_email;
                        $columns = 'u.user_email,u.user_email_verified';
                        $parent_members = $this->Common_model->get_linked_family_members($patient_id, $columns);
                        $patient_email_id_arr = array();
                        foreach ($parent_members as $parent_member) {
                            if($parent_member->user_email_verified == 1) {
                                $patient_email_id_arr[$parent_member->user_email] = $parent_member->user_email;
                            }
                        }
                        if(!empty($email)) {
                            $patient_email_id_arr[$email] = $email;
                        }
                        if(count($patient_email_id_arr) > 0) {
                            $email_template_data = $this->Emailsetting_model->get_emailtemplate_by_id(28);
                            $parse_arr = array(
                                '{PatinetName}' => $patient_name,
                                '{DrName}' => $doctor_name,
                                '{DrLastName}' => DOCTOR . $check_patient_appointment['user_last_name'],
                                '{WebUrl}' => DOMAIN_URL,
                                '{AppName}' => APP_NAME,
                                '{MailEmailAddress}' => $email_template_data['email_static_data']['email_id'],
                                '{MailCompanyName}' => $email_template_data['email_static_data']['company_name'],
                                '{CopyRightsYear}' => date('Y')
                            );
                            $message = replace_values_in_string($parse_arr, $email_template_data['email_template_message']);
                            $subject = $email_template_data['email_template_subject'];
                            $this->send_attachment_email($patient_email_id_arr, $subject, $message);
                        }
                    }
                }
                echo "Share successfully";
            } else {
                echo "Share fail";
            }
            exit;
        } elseif(!empty($isGeneratePastPrescription)) {
            $patient_name = $get_patient_data['user_first_name'] . ' ' . $get_patient_data['user_last_name'];
            $upload_path = DOCROOT_PATH . 'uploads/' . PAST_PRESCRIPTION . '/';
            if (!file_exists($upload_path)) {
                mkdir($upload_path, 0777, TRUE);
                chmod($upload_path, 0777);
            }
            $file_name = $appointment_id."_prescription".".pdf";
            $mpdf->Output($upload_path . $file_name, 'F');
            $attachment_path = $upload_path . $file_name;
            $response = [];
            if(file_exists($attachment_path)) {
                upload_to_s3($attachment_path, PAST_PRESCRIPTION.'/'.$file_name);
                $pdf_url = IMAGE_MANIPULATION_URL.PAST_PRESCRIPTION.'/'.$file_name;
                $response['ObjectURL'] = $pdf_url;
                $response['status'] = true;
            } else {
                $response['message'] = 'Prescription not found.';
                $response['status'] = false;
            }
            echo json_encode($response);
            exit;
        } else {
            $mpdf->Output($fileName.'.pdf', 'I');
        }
    }

    public function send_attachment_email($to_email_address, $subject, $message, $attachment1 = array(), $attachment2 = array()) {

        try {
            require_once SWIFT_MAILER_PATH;
            $transport = Swift_SmtpTransport::newInstance(PATIENT_EMAIL_HOST, PATIENT_EMAIL_PORT, EMAIL_CERTIFICATE)
                    ->setUsername(PATIENT_EMAIL_USER)
                    ->setPassword(PATIENT_EMAIL_PASS);
            $mailer = Swift_Mailer::newInstance($transport);
            $sendMessage = Swift_Message::newInstance()
                    ->setSubject($subject)
                    ->setTo($to_email_address)
                    ->setFrom(array(PATIENT_EMAIL_FROM => APP_NAME))
                    ->setBody($message, 'text/html');

            if(!empty($attachment1[0]['path'])){
                foreach ($attachment1 as $key => $value) {
                    $sendMessage->attach(Swift_Attachment::fromPath($value['path'])->setFilename($value['name']));
                }
            } elseif (!empty($attachment1)) {
                $sendMessage->attach(Swift_Attachment::fromPath($attachment1[0])->setFilename($attachment1[1]));
            }

            if (!empty($attachment2)) {
                $sendMessage->attach(Swift_Attachment::fromPath($attachment2[0])->setFilename($attachment2[1]));
            }

            if (!$mailer->send($sendMessage)) {
                file_put_contents($this->email_log_file, "\n\n ======== send by php mail ===== \n\n", FILE_APPEND | LOCK_EX);
                $this->send_simple_attach_email($to_email_address, $subject, $message, $attachment1, $attachment2);
            }
        } catch (\Swift_TransportException $e) {
            $response = $e->getMessage();
            file_put_contents($this->email_log_file, "\n\n ======== response" . json_encode($response) . "===== \n\n", FILE_APPEND | LOCK_EX);
            //$this->send_simple_attach_email($to_email_address, $subject, $message, $attachment1, $attachment2);
        } catch (Exception $e) {
            $response = $e->getMessage();
            file_put_contents($this->email_log_file, "\n\n ======== response" . json_encode($response) . "===== \n\n", FILE_APPEND | LOCK_EX);
            //$this->send_simple_attach_email($to_email_address, $subject, $message, $attachment1, $attachment2);
        }
    }

    public function invoice() {

        $appointment_id = $this->input->get("appointment_id");
        $doctor_id = $this->input->get("doctor_id");
        $patient_id = $this->input->get("patient_id");
        $billing_id = $this->input->get("billing_id");

        //check appointment with doctor
        $check_patient_appointment_sql = "
            
            SELECT 
                appointment_date,
                doctor.user_first_name as doctor_first_name,
                doctor.user_last_name as doctor_last_name,
                doctor.user_phone_number as doctor_phone_number,
                patient.user_id as patient_user_id,
                patient.user_email as patient_user_email,
                patient.user_first_name as patient_first_name,
                patient.user_last_name as patient_last_name,
                patient.user_phone_number as patient_phone_number,
                doctor_detail_speciality,
                clinic_name,
                clinic_contact_number,
                clinic_email,
                address_name,
                address_name_one,
                address_locality,
                address_pincode,
                city_name,
                state_name,
                GROUP_CONCAT(DISTINCT(clinical_notes_reports_kco)) as kco,
                GROUP_CONCAT(DISTINCT(doctor_qualification_degree) ORDER BY doctor_qualification_id ASC) AS doctor_qualification
            FROM 
                " . TBL_APPOINTMENTS . "
            LEFT JOIN 
                " . TBL_USERS . " as doctor
            ON 
                appointment_doctor_user_id=doctor.user_id
            LEFT JOIN 
                " . TBL_USERS . " as patient
            ON 
                appointment_user_id=patient.user_id
            LEFT JOIN 
                " . TBL_CLINICAL_REPORTS . "
            ON 
                clinical_notes_reports_user_id=patient.user_id
            LEFT JOIN 
                " . TBL_DOCTOR_DETAILS . " ON doctor.user_id = doctor_detail_doctor_id
            LEFT JOIN 
                " . TBL_ADDRESS . " ON address_user_id = appointment_clinic_id AND address_type = 2
            LEFT JOIN 
                " . TBL_CITIES . " ON address_city_id = city_id
            LEFT JOIN 
                " . TBL_STATES . " ON address_state_id = state_id
            LEFT JOIN 
               " . TBL_CLINICS . " ON appointment_clinic_id = clinic_id
            LEFT JOIN
                ".TBL_DOCTOR_EDUCATIONS." ON appointment_doctor_user_id = doctor_qualification_user_id AND doctor_qualification_status = 1        
            WHERE
                appointment_user_id='" . $patient_id . "' AND 
                appointment_doctor_user_id='" . $doctor_id . "' AND 
                appointment_id='" . $appointment_id . "'
        ";
        $check_patient_appointment = $this->Common_model->get_single_row_by_query($check_patient_appointment_sql);

        $kco = '';
        if (!empty($check_patient_appointment['kco'])) {
            $kco = str_replace("\",\"", ",", $check_patient_appointment['kco']);
            $kco = str_replace("[\"", "", $kco);
            $kco = str_replace("\"]", "", $kco);
            $kco = str_replace(",[]", "", $kco);
        }


        if (empty($check_patient_appointment)) {
            http_response_code(404);
            die();
        }

        $doctor_speciality = !empty($check_patient_appointment['doctor_detail_speciality']) ? $check_patient_appointment['doctor_detail_speciality'] : '-';

        //get billing detail
        $columns = 'billing_id,
                    billing_advance_amount,
                    billing_created_at,
                    billing_invoice_date,
                    billing_appointment_id,
                    billing_user_id,
                    billing_doctor_user_id,
                    billing_clinic_id,
                    billing_payment_mode_id,
                    billing_discount,
                    billing_tax,
                    billing_grand_total,
                    billing_total_payable,
                    billing_advance_amount,
                    billing_paid_amount,
                    
                    billing_detail_name,
                    billing_detail_unit,
                    billing_detail_basic_cost,
                    billing_detail_discount,
                    billing_detail_tax,
                    billing_detail_discount_type,
                    billing_detail_tax_id,
                    billing_detail_total,
                    billing_detail_id,
                    billing_detail_created_at,
                    invoice_number,
                    payment_mode_name,
                    payment_mode_vendor_fee';

        $get_billing_sql = "    SELECT 
                                    " . $columns . " 
                                FROM 
                                    " . TBL_BILLING . " 
                                LEFT JOIN 
                                    " . TBL_BILLING_DETAILS . " 
                                ON 
                                    billing_id = billing_detail_billing_id AND billing_detail_status = 1
                                LEFT JOIN    
                                    " . TBL_PAYMENT_MODE . "
                                ON
                                    billing_payment_mode_id = payment_mode_id
                                WHERE 
                                    billing_appointment_id = '" . $appointment_id . "'
                                AND 
                                    billing_user_id = '" . $patient_id . "'
                                AND
                                    billing_doctor_user_id = '" . $doctor_id . "'
                                AND
                                    billing_id = '" . $billing_id . "'
                                AND 
                                    billing_status = 1 ";

        $get_billing_data = $this->Common_model->get_all_rows_by_query($get_billing_sql);

        $view_data = array();
        $view_data['patient_detail'] = array(
            'patient_id' => "P" . $check_patient_appointment['patient_user_id'],
            'patient_name' => $check_patient_appointment['patient_first_name'] . ' ' . $check_patient_appointment['patient_last_name'],
            'patient_email' => $check_patient_appointment['patient_user_email'],
            'patient_number' => $check_patient_appointment['patient_phone_number'],
            'kco' => $kco
        );
        $view_data['doctor_name'] = $check_patient_appointment['doctor_first_name'] . ' ' . $check_patient_appointment['doctor_last_name'];
        $view_data['billing_data'] = $get_billing_data;
        $view_data['doctor_data'] = $check_patient_appointment;
        
        $invoice_body = $this->load->view("prints/invoice_body", $view_data, true);
        $view_html = $this->load->view("prints/invoice", ['invoice_body' => $invoice_body], true);

        // require_once MPDF_PATH;
        // $lang_code = 'en-GB';
        // $mpdf = new MPDF(
        //         $lang_code, // mode - default '' //sd
        //         'A4', // format - A4, for example, default ''
        //         0, // font size - default 0
        //         'arial', // default font family
        //         8, // margin_left
        //         8, // margin right
        //         35, // margin top
        //         8, // margin bottom
        //         8, // margin header
        //         5, // margin footer
        //         'P'   // L - landscape, P - portrait
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
            'margin_top' => 0,
            'margin_bottom' => 8,
            'margin_left' => 8,
            'margin_right' => 8,
            'margin_header' => 8,
            'margin_footer' => 5
        ]);
        $mpdf->useOnlyCoreFonts = true;
        $mpdf->setAutoTopMargin = true;
        $mpdf->SetDisplayMode('real');
        $mpdf->list_indent_first_level = 0;
        $mpdf->setAutoBottomMargin = 'stretch';
        $date_and_time = date('m/d/Y h:i:s a', time());
        $patient_name = $check_patient_appointment['patient_first_name'];
        $mpdf->SetTitle('Rx_'.$patient_name.'_'.$date_and_time);
        $address_data = [
            'address_name' => $view_data['doctor_data']['address_name'],
            'address_name_one' => $view_data['doctor_data']['address_name_one'],
            'address_locality' => $view_data['doctor_data']['address_locality'],
            'city_name' => $view_data['doctor_data']['city_name'],
            'state_name' => $view_data['doctor_data']['state_name'],
            'address_pincode' => $view_data['doctor_data']['address_pincode']
        ];
        $mpdf->SetHTMLHeader('
            <table style="width:100%;border-bottom:1px solid #000">
                <tr>
                    <td width="50%" style="text-align:left;vertical-align:top">
                     ' . DOCTOR." ". $view_data['doctor_data']['doctor_first_name'] . " " . $view_data['doctor_data']['doctor_last_name'] . "<br>" . '
                     ' . $view_data['doctor_data']['doctor_detail_speciality'] . "<br>" . '
                     ' . $view_data['doctor_data']['doctor_qualification'] . "<br>" . '    
                    </td>
                    <td width="50%" style="text-align:right;vertical-align:top">
                        ' . $view_data['doctor_data']['clinic_name'] . "<br>" . '
                        ' . clinic_address($address_data) . "<br>" . '
                        ' . $view_data['doctor_data']['clinic_contact_number'] . ", " . '
                        ' . $view_data['doctor_data']['clinic_email'] . "<br>" . '
                    </td>
                </tr>
            </table>
        ');
        $mpdf->SetHTMLFooter('
            <table width="100%">
                <tr>
                    <td width="33%" style="font-size:10px">
                        Generated On: {DATE d M Y}
                    </td>
                    <td width="33%" style="font-size:10px" align="center">Page No. {PAGENO}/{nbpg}</td>
                    <td width="33%" align="right" style="font-size:10px">Powerd by Medsign</td>
                </tr>
            </table>
        ');
//        echo $view_html;exit;
        $mpdf->WriteHTML($view_html);
        $fileName = 'Rx_'.$patient_name.'_'.$date_and_time;
        $mpdf->Output($fileName.'.pdf', 'I');
        //$mpdf->Output();
    }

    public function share_link_qr() {
        $share_id = $this->input->get("share_id");
        $share_row = $this->Common_model->get_single_row('me_registration_share_link', 'registration_share_id,registration_share_clinic_id,registration_share_doctor_id', ['registration_share_status' => 1, 'registration_share_id' => $share_id]);
        $doctor_id = $share_row['registration_share_doctor_id'];
        if(!empty($share_row['registration_share_clinic_id']))
            $clinic_id = $share_row['registration_share_clinic_id'];
        $doctor_details_sql = "
            SELECT 
                user_first_name,
                user_last_name,
                user_phone_number,
                doctor_detail_speciality,
                clinic_name,
                clinic_contact_number,
                clinic_email,
                address_name,
                address_name_one,
                address_locality,
                address_pincode,
                city_name,
                state_name,
                src_master_company_id
            FROM 
                " . TBL_USERS . " as doctor
            LEFT JOIN 
                " . TBL_DOCTOR_CLINIC_MAPPING . " ON doctor.user_id = doctor_clinic_mapping_user_id AND doctor_clinic_mapping_role_id=1 AND doctor_clinic_mapping_status=1
            LEFT JOIN 
                " . TBL_DOCTOR_DETAILS . " ON doctor.user_id = doctor_detail_doctor_id
            LEFT JOIN 
                me_admin_source_master ON src_master_id=doctor.user_source_id
            LEFT JOIN 
                " . TBL_ADDRESS . " ON address_user_id = doctor_clinic_mapping_clinic_id AND address_type = 2
            LEFT JOIN 
                " . TBL_CITIES . " ON address_city_id = city_id
            LEFT JOIN 
                " . TBL_STATES . " ON address_state_id = state_id
            LEFT JOIN 
               " . TBL_CLINICS . " ON doctor_clinic_mapping_clinic_id = clinic_id
            WHERE
                doctor.user_id='" . $doctor_id . "'
            " . (!empty($clinic_id) ? ' AND doctor_clinic_mapping_clinic_id = ' . $clinic_id : '') . " 
            GROUP BY doctor.user_id
        ";
        $doctor_details = $this->Common_model->get_single_row_by_query($doctor_details_sql);
        $share_link = MEDSIGN_WEB_CARE_URL . 'register?id=' . encrypt_decrypt($share_row['registration_share_id'],'encrypt');
        $qrCode = new QrCode($share_link);
        $output = new Output\Png();
        $view_data['doctor_data'] = $doctor_details;
        $view_data['qrcode_img'] = '<img src="data:image/png;base64,'.base64_encode($output->output($qrCode,300)).'" />';
        $doctor_data = $view_data['doctor_data'];
        $doctor_data['doctor_detail_speciality'] = str_replace(',', ', ', $doctor_data['doctor_detail_speciality']);
        $address_data = [
            'address_name' => $doctor_data['address_name'],
            'address_name_one' => $doctor_data['address_name_one'],
            'address_locality' => $doctor_data['address_locality'],
            'city_name' => $doctor_data['city_name'],
            'state_name' => $doctor_data['state_name'],
            'address_pincode' => $doctor_data['address_pincode']
        ];
        $view_data['clinic_address'] = clinic_address($address_data);
        $view_html = $this->load->view("prints/qrcode", $view_data, true);
        $logoImg = DOMAIN_URL . 'assets/web/img/medsign-logo.png';
        if($doctor_data['src_master_company_id'] == 6)
            $logoImg = DOMAIN_URL . 'assets/web/img/medsign-derma.png';
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
            'margin_top' => 0,
            'margin_bottom' => 8,
            'margin_left' => 8,
            'margin_right' => 8,
            'margin_header' => 2,
            'margin_footer' => 5
        ]);
        $mpdf->useOnlyCoreFonts = true;
        $mpdf->setAutoTopMargin = true;
        $mpdf->SetDisplayMode('real');
        $mpdf->list_indent_first_level = 0;
        $mpdf->setAutoBottomMargin = 'stretch';
        $date_and_time = date('m/d/Y h:i:s a', time());
        $mpdf->SetTitle('QR Code');
        $mpdf->SetHTMLHeader('
            <table style="width:100%;border-bottom:1px solid #000">
                <tr>
                    <td width="100%" style="text-align:center;vertical-align:top">
                     <img src="'.$logoImg.'" />
                    </td>
                </tr>
            </table>
        ');
        
        $mpdf->SetHTMLFooter('
            <table style="width:100%;border-top:1px solid #000">
                <tr>
                    <td width="67%" style="font-size:10px">'.MEDSIGN_WEB_CARE_URL.'</td>
                    <td width="33%" align="right" style="font-size:10px">Powerd by Medsign</td>
                </tr>
            </table>
        ');
        //echo $view_html;exit;
        $fileName = 'QR Code';
        $mpdf->WriteHTML($view_html);
        $mpdf->Output($fileName.'.pdf', 'I');
    }

}