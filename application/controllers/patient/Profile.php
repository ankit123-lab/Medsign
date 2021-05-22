<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Profile extends MY_Controller {

    public function __construct() {

        parent::__construct();
        $this->load->library('patient_auth');
        $this->load->model('patient_model','patient');
        $this->load->library('form_validation');
        if (!$this->patient_auth->is_logged_in()) {
            redirect('patient/login');
        }
    }

    public function update() {
        $view_data = array();
        $view_data['breadcrumbs'] = "Profile Update";
        $view_data['page_title'] = "Profile Update";
        $view_data['languages'] = $this->Common_model->get_all_rows('me_languages', 'language_id,language_name', ['language_status' => 1]);
        $view_data['smoking_habbit'] = $this->Common_model->get_all_rows('me_smoking_habbit', 'smoking_habbit_id,smoking_habbit_name_en', ['smoking_habbit_status' => 1]);
        $view_data['alcohol'] = $this->Common_model->get_all_rows('me_alcohol', 'alcohol_id,alcohol_name_en', ['alcohol_status' => 1]);
        $view_data['food_preference'] = $this->Common_model->get_all_rows('me_food_preference', 'food_preference_id,food_preference_name_en', ['food_preference_status' => 1]);
        $view_data['occupations'] = $this->Common_model->get_all_rows('me_occupations', 'occupation_id,occupation_name_en', ['occupation_status' => 1]);
        $view_data['activity_levels'] = $this->Common_model->get_all_rows('me_activity_levels', 'activity_level_id,activity_level_name_en', ['activity_level_status' => 1]);
        $view_data['states'] = $this->Common_model->get_all_rows('me_state', 'state_id,state_name', ['state_status' => 1]);
        $view_data['medical_conditions'] = $this->Common_model->get_all_rows('me_medical_conditions', 'medical_condition_id,medical_condition_name', ['medical_condition_status' => 1]);
        $view_data['cities'] = array();
        $view_data['blood_groups'] = ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'];
        $view_data['activity_days'] = ['1', '2', '3', '4', '5', '6', '7'];
        $view_data['activity_hours'] = ['15', '30', '45', '60', '75', '90', '105'];
        $view_data['family_relation']= get_relation();

        $view_data['family_medical_history'] = $this->Common_model->get_all_rows('me_family_medical_history', '*', ['family_medical_history_user_id' => $this->patient_auth->get_user_id(),'family_medical_history_status' => 1]);
        // print_r($view_data['family_medical_history']);die;
        $view_data['patient_details'] = $this->patient->get_patient_all_details($this->patient_auth->get_user_id());
        if(!empty($view_data['patient_details']->user_details_id_proof_image)) {
            $view_data['patient_details']->user_details_id_proof_image_thumb = get_image_thumb($view_data['patient_details']->user_details_id_proof_image);
        }
        if(!empty($view_data['patient_details']->address_state_id)){
            $view_data['cities'] = $this->Common_model->get_all_rows('me_city', 'city_id,city_name', ['city_status' => 1 , 'city_state_id' => $view_data['patient_details']->address_state_id]);
        }
        if(!empty($view_data['patient_details']->user_details_languages_known))
            $view_data['patient_details']->user_details_languages_known = explode(',', $view_data['patient_details']->user_details_languages_known);
        else
            $view_data['patient_details']->user_details_languages_known = [];

        if(!empty($view_data['patient_details']->user_details_activity_level))
            $view_data['patient_details']->user_details_activity_level = explode(', ', $view_data['patient_details']->user_details_activity_level);
        else
            $view_data['patient_details']->user_details_activity_level = [];

        if(!empty($view_data['patient_details']->user_details_activity_days))
            $view_data['patient_details']->user_details_activity_days = explode(',', $view_data['patient_details']->user_details_activity_days);
        else
            $view_data['patient_details']->user_details_activity_days = [];

        if(!empty($view_data['patient_details']->user_details_activity_hours))
            $view_data['patient_details']->user_details_activity_hours = explode(',', $view_data['patient_details']->user_details_activity_hours);
        else
            $view_data['patient_details']->user_details_activity_hours = [];

        if($this->patient_auth->get_logged_user_id() == $this->patient_auth->get_user_id()) {
            $patients = $this->patient->get_family_members($this->patient_auth->get_logged_user_id());
            foreach ($patients as $key => $value) {
                $patients[$key]->relation = relation_map($value->mapping_relation);
            }
            $view_data['family_members'] = $patients;
        }
        // echo "<pre>";
        // print_r($view_data['patient_details']);die;
        $this->load->view('patient/profile_update_view', $view_data);
    }

    public function update_data() {
        $this->form_validation->set_error_delimiters('', '');
        $this->form_validation->set_rules('user_first_name', 'first name', 'required|trim');
        $this->form_validation->set_rules('user_last_name', 'last name', 'required|trim');
        $this->form_validation->set_rules('user_phone_number', 'phone number', 'trim|numeric|regex_match[/^[0-9]{10}$/]|callback_phone_exist');
        $this->form_validation->set_rules('user_email', 'email', 'trim|valid_email|callback_email_exist');
        $this->form_validation->set_rules('date_of_birth', 'date of birth', 'required|trim');
        $this->form_validation->set_rules('gender', 'gender', 'required|trim');
        $this->form_validation->set_rules('language_id', 'language', 'trim');
        $this->form_validation->set_rules('user_height', 'height', 'trim');
        $this->form_validation->set_rules('user_weight', 'height', 'trim');
        $this->form_validation->set_rules('blood_group', 'blood group', 'trim');
        $this->form_validation->set_rules('address_name_one', 'Address', 'trim');
        $this->form_validation->set_rules('address_name', 'Landmark', 'trim');
        $this->form_validation->set_rules('state_id', 'state', 'trim');
        $this->form_validation->set_rules('city_id', 'city', 'trim');
        $this->form_validation->set_rules('user_locality', 'locality', 'trim');
        $this->form_validation->set_rules('user_pin_code', 'pincode', 'trim');
        $this->form_validation->set_rules('emergency_contact_name', 'emergency contact name', 'trim');
        $this->form_validation->set_rules('emergency_contact_number', 'emergency contact number', 'trim');
        $this->form_validation->set_rules('marital_status', 'marital status', 'trim');
        $this->form_validation->set_rules('smoking_habbit_id', 'smoking habbit', 'trim');
        $this->form_validation->set_rules('alcohol_id', 'alcohol', 'trim');
        $this->form_validation->set_rules('food_preference', 'food preference', 'trim');
        $this->form_validation->set_rules('occupation', 'occupation', 'trim');
        $this->form_validation->set_rules('activity_levels', 'activity levels', 'trim');
        $this->form_validation->set_rules('activity_days', 'activity days', 'trim');
        $this->form_validation->set_rules('activity_hours', 'activity hours', 'trim');
        $this->form_validation->set_rules('chronic_diseases_text', 'chronic diseases', 'trim');
        $this->form_validation->set_rules('patient_injuries_text', 'patient injuries', 'trim');
        $this->form_validation->set_rules('patient_surgeries_text', 'patient injuries', 'trim');
        $this->form_validation->set_rules('patient_food_sllergies_text', 'patient injuries', 'trim');
        $this->form_validation->set_rules('patient_medicine_sllergies_text', 'patient injuries', 'trim');
        $this->form_validation->set_rules('patient_other_sllergies_text', 'patient injuries', 'trim');
        $this->form_validation->set_rules('id_proof_detail', 'ID Proof Detail', 'trim');
        $this->form_validation->set_rules('id_proof_type', 'ID Proof Type', 'trim');
        
        if ($this->form_validation->run() == FALSE) {
            $errors = validation_errors();
            echo json_encode(['status' => false, 'error' => nl2br($errors)]);
            exit;
        } else {
            $user_id = $this->patient_auth->get_user_id();
            $user_data = array(
                'user_first_name' => set_value('user_first_name'),
                'user_last_name' => set_value('user_last_name'),
                'user_gender' => set_value('gender'),
                'user_modified_at' => $this->utc_time_formated,
            );
            $email = set_value('user_email');
            if(!empty($email)){
                $user_data['user_email'] = $email;
                $user_data['user_email_verified'] = 1;
            }
            $user_phone_number = set_value('user_phone_number');
            if(!empty($user_phone_number)){
                $user_data['user_phone_number'] = $user_phone_number;
                $user_data['user_phone_verified'] = 1;
            }

            $this->Common_model->update('me_users', $user_data, ['user_id' => $this->patient_auth->get_user_id()]);
            $this->session->set_userdata(array(
                'patient_name' => $user_data['user_first_name'] . ' ' . $user_data['user_last_name']
            ));
            $address_data = array(
                'address_user_id' => $user_id,
                'address_type' => 1,
                'address_name_one' => set_value('address_name_one'),
                'address_name' => set_value('address_name'),
                'address_state_id' => set_value('state_id'),
                'address_city_id' => set_value('city_id'),
                'address_locality' => set_value('user_locality'),
                'address_pincode' => set_value('user_pin_code'),
            );
            $where = array('address_user_id' => $user_id, 'address_type' => 1);
            $get_address_details = $this->Common_model->get_single_row('me_address', 'address_id', array('address_user_id' => $user_id, 'address_type' => 1));
            if (!empty($get_address_details)) {
                $address_data['address_modified_at'] = $this->utc_time_formated;
                $address_is_update = $this->Common_model->update('me_address', $address_data, array('address_id' => $get_address_details['address_id']));
            } else {
                $address_data['address_created_at'] = $this->utc_time_formated;
                $this->Common_model->insert('me_address', $address_data);
            }
            $arr = explode('/', set_value('date_of_birth'));
            $dob = NULL;
            if(count($arr) == 3)
                $dob = $arr[2].'-'.$arr[1].'-'.$arr[0];
            $user_details_data = array(
                'user_details_user_id' => $user_id,
                'user_details_dob' => $dob,
                'user_details_languages_known' => !(empty(set_value('language_id[]'))) ? implode(',', set_value('language_id[]')) : NULL,
                'user_details_agree_medical_share' => ($user_data['user_gender'] == 'undisclosed') ? 1 : 2,
                'user_details_height' => set_value('user_height'),
                'user_details_weight' => kgToPound(set_value('user_weight')),
                'user_details_blood_group' => set_value('blood_group'),
                'user_details_emergency_contact_person' => set_value('emergency_contact_name'),
                'user_details_emergency_contact_number' => set_value('emergency_contact_number'),
                'user_details_marital_status' => set_value('marital_status'),
                'user_details_smoking_habbit' => set_value('smoking_habbit_id'),
                'user_details_alcohol' => set_value('alcohol_id'),
                'user_details_food_preference' => set_value('food_preference'),
                'user_details_occupation' => set_value('occupation'),
                'user_details_id_proof_type' => set_value('id_proof_type'),
                'user_details_id_proof_detail' => set_value('id_proof_detail'),
                'user_details_activity_level' => !(empty(set_value('activity_levels[]'))) ? implode(', ', set_value('activity_levels[]')) : NULL,
                'user_details_activity_days' => !(empty(set_value('activity_days[]'))) ? implode(',', set_value('activity_days[]')) : NULL,
                'user_details_activity_hours' => !(empty(set_value('activity_hours[]'))) ? implode(',', set_value('activity_hours[]')) : NULL,
                'user_details_chronic_diseases' => !(empty(set_value('chronic_diseases_text'))) ? set_value('chronic_diseases_text') : NULL,
                'user_details_injuries' => !(empty(set_value('patient_injuries_text'))) ? set_value('patient_injuries_text') : NULL,
                'user_details_surgeries' => !(empty(set_value('patient_surgeries_text'))) ? set_value('patient_surgeries_text') : NULL,
                'user_details_food_allergies' => !(empty(set_value('patient_food_sllergies_text'))) ? set_value('patient_food_sllergies_text') : NULL,
                'user_details_medicine_allergies' => !(empty(set_value('patient_medicine_sllergies_text'))) ? set_value('patient_medicine_sllergies_text') : NULL,
                'user_details_other_allergies' => !(empty(set_value('patient_other_sllergies_text'))) ? set_value('patient_other_sllergies_text') : NULL,

            );
            $get_user_details = $this->Common_model->get_single_row('me_user_details', 'user_details_id', array('user_details_user_id' => $user_id));
            if (!empty($get_user_details)) {
                $user_details_data['user_details_modifed_at'] = $this->utc_time_formated;
                $this->Common_model->update('me_user_details', $user_details_data, array('user_details_user_id' => $user_id));
            } else {
                $user_details_data['user_details_created_at'] = $this->utc_time_formated;
                $this->Common_model->insert('me_user_details', $user_details_data);
            }

            if(!empty($this->input->post('family_relation')) && count($this->input->post('family_relation')) > 0) {
                $family_relation = $this->input->post('family_relation');
                $family_medical_conditions = $this->input->post('family_medical_conditions');
                $family_since_when = $this->input->post('family_since_when');
                $family_comment = $this->input->post('family_comment');
                $this->Common_model->update('me_family_medical_history', ['family_medical_history_status' => 9, 'family_medical_history_updated_at' => $this->utc_time_formated], ['family_medical_history_user_id' => $user_id]);
                $created_time = $this->utc_time_formated;
                $insert_family_history_array = array();
                for($i=0; $i < count($this->input->post('family_relation')) ; $i++) {
                    if(!empty($family_relation[$i]) && !empty($family_medical_conditions[$i]) && !empty($family_since_when[$i])) {
                        $arr = explode('/', $family_since_when[$i]);
                        $history_date = NULL;
                        if(count($arr) == 3)
                            $history_date = $arr[2].'-'.$arr[1].'-'.$arr[0];
                        $insert_family_history_array[] = array(
                            "family_medical_history_user_id" => $user_id,
                            "family_medical_history_medical_condition_id" => !empty($family_medical_conditions[$i]) ? implode(',', $family_medical_conditions[$i]) : NULL,
                            "family_medical_history_relation" => $family_relation[$i],
                            "family_medical_history_date" => $history_date,
                            "family_medical_history_comment" => $family_comment[$i],
                            "family_medical_history_created_at" => $created_time
                        );
                    }
                }
                // print_r($insert_family_history_array);die;
                if(count($insert_family_history_array) > 0)
                    $this->Common_model->insert_multiple('me_family_medical_history', $insert_family_history_array);
            }
            echo json_encode(['status' => true, 'msg' => "Your profile data updated successfully"]);
            exit;
        }
    }

    public function get_city() {
        $this->form_validation->set_rules('state_id', 'state id', 'required|trim');
        $response = [];
        if ($this->form_validation->run() == FALSE) {
            $errors = validation_errors();
            $response['errors'] = $errors;
            $response['status'] = false;
        } else {
            $cities = $this->Common_model->get_all_rows('me_city', 'city_id,city_name', ['city_status' => 1 , 'city_state_id' => set_value('state_id')]);
            $response['status'] = true;
            $response['data'] = $cities;
        }
        echo json_encode($response);
    }

    public function patient_list() {
        $response = [];
        $view_data = [];
        $user = $this->Common_model->get_all_rows('me_users', 'user_id, user_first_name, user_last_name', ['user_id' => $this->patient_auth->get_logged_user_id()]);
        $user[0] = (object) $user[0];
        $patients = $this->patient->get_family_members($this->patient_auth->get_logged_user_id());
        $view_data['patients'] = array_merge($user, $patients);
        $response['status'] = true;
        $response['html'] = $this->load->view('patient/patient_list_view', $view_data, true);
        echo json_encode($response);
    }

    public function change_patient() {
        if($this->input->post('patient_id')) {
            $user = $this->Common_model->get_single_row('me_users', 'user_first_name, user_last_name', ['user_id' => $this->input->post('patient_id')]);
            $this->session->set_userdata(array(
                'patient_id' => $this->input->post('patient_id'),
                'patient_name' => $user['user_first_name'] . ' ' . $user['user_last_name']
            ));
        }
        $response = [];
        $response['status'] = true;
        echo json_encode($response);
    }

    public function document_upload() {
        $response = ['status' => true];
        $ext = pathinfo($_FILES['id_proof_file']['name'], PATHINFO_EXTENSION);
        if(!in_array(strtolower($ext), ["jpg","jpeg","png","gif"])) {
            $response['status'] = false;
            $response['msg'] = "Your selected image type is invalid.";
            echo json_encode($response);
            exit;
        }
        $files = [];
        $files['name'][0] = $_FILES['id_proof_file']['name'];
        $files['type'][0] = $_FILES['id_proof_file']['type'];
        $files['tmp_name'][0] = $_FILES['id_proof_file']['tmp_name'];
        $files['error'][0] = $_FILES['id_proof_file']['error'];
        $files['size'][0] = $_FILES['id_proof_file']['size'];
        $user_id = $this->patient_auth->get_user_id();
        $upload_path = UPLOAD_REL_PATH . "/" . USER_ID_PROOF_FOLDER . "/" . $user_id;
        $upload_folder = USER_ID_PROOF_FOLDER . "/" . $user_id;
        $id_proof_image = do_upload_multiple($upload_path, $files, $upload_folder);
        if(!empty($id_proof_image[0])) {
            $user_details_data = [];
            $user_details_data['user_details_id_proof_image'] = IMAGE_MANIPULATION_URL . USER_ID_PROOF_FOLDER . "/" . $user_id . "/" . $id_proof_image[0];
            $user_details_data['user_details_modifed_at'] = $this->utc_time_formated;
            $this->Common_model->update('me_user_details', $user_details_data, array('user_details_user_id' => $user_id));
            $response['img_path'] = $user_details_data['user_details_id_proof_image'];
            $response['img_path_thumb'] = get_image_thumb($user_details_data['user_details_id_proof_image']);
        }
        $response['msg'] = "Success";
        echo json_encode($response);
    }

    public function phone_exist($phone) {
        if(!empty($phone) && $this->patient->phone_exist($phone, $this->patient_auth->get_user_id())) {
            $this->form_validation->set_message('phone_exist', 'The {field} is already exist');
            return false;
        }
        return true;
    }

    public function email_exist($email) {
        if(!empty($email) && $this->patient->email_exist($email, $this->patient_auth->get_user_id())) {
            $this->form_validation->set_message('email_exist', 'The {field} is already exist');
            return false;
        }
        return true;
    }

}