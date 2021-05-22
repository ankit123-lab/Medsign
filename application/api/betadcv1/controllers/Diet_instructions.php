<?php

Class Diet_instructions extends MY_Controller {

    public function __construct() {
        parent::__construct();
    }

    public function add_diet_instructions_post() {
        try {
            $doctor_id = !empty($this->Common_model->escape_data($this->post_data['doctor_id'])) ? $this->Common_model->escape_data($this->post_data['doctor_id']) : '';
            $diet_instruction = !empty(trim($this->post_data['diet_instruction'])) ? trim($this->post_data['diet_instruction']) : '';
            $type = !empty(trim($this->post_data['type'])) ? trim($this->post_data['type']) : '';
            $translate_data = !empty($this->post_data['translate_data']) ? $this->post_data['translate_data'] : '';
            if (empty($doctor_id) ||
                    empty($diet_instruction) || empty($type)
            ) {
                $this->bad_request();
            }
            $get_role_details = $this->Common_model->get_the_role($this->user_id);
            if (!empty($get_role_details['user_role_data'])) {
                $permission_data = array(
                    'role_data' => $get_role_details['user_role_data'],
                    'module' => 43,
                    'key' => 1
                );
                $check_module_permission = $this->check_module_permission($permission_data);
                if ($check_module_permission == 2) {
                    $this->my_response['status'] = false;
                    $this->my_response['message'] = lang('permission_error');
                    $this->send_response();
                }
            }
            $requested_data = array(
                'doctor_id' => $doctor_id,
                'type' => $type,
                'diet_instruction' => $diet_instruction
            );
            $this->check_diet_instruction_exists($requested_data, 2);
            if(($type == 2 || $type == 1) && !empty($translate_data)) {
                $lang_id_arr = [];
                $is_lang_duplicate = false;
                foreach ($translate_data as $key => $value) {
                    if(in_array($value['language_id'], $lang_id_arr))
                        $is_lang_duplicate = true;
                    $lang_id_arr[] = $value['language_id'];
                }
                if($is_lang_duplicate) {
                    $this->my_response['status'] = false;
                    $this->my_response['message'] = lang('lang_duplicate_error');
                    $this->send_response();
                }
            }
            $insert_diet_instruction = array(
                'diet_instruction' => $diet_instruction,
                'doctor_id' => $doctor_id,
                'instruction_type' => $type,
                'created_at' => $this->utc_time_formated
            );
            $inserted_id = $this->Common_model->insert('me_diet_instruction', $insert_diet_instruction);
            if ($inserted_id > 0) {
                if($type == 2 || $type == 1) {
                    $translation_insert_data = [];
                    if(!empty($translate_data) && is_array($translate_data)) {
                        foreach ($translate_data as $key => $value) {
                            $translation_insert_data[] = [
                                'translation_note_id' => $inserted_id,
                                'translation_text' => $value['note'],
                                'translation_lang_id' => $value['language_id'],
                                'translation_created_at' => $this->utc_time_formated,
                                'translation_created_by' => $this->user_id,
                            ];
                        }
                    }
                    if(count($translation_insert_data) > 0) {
                        $this->Common_model->insert_multiple('me_translation', $translation_insert_data);
                    }
                }
                $this->my_response['status'] = true;
                $this->my_response['message'] = lang('diet_instruction_added');
                $this->my_response['inserted_id'] = $inserted_id;
            } else {
                $this->my_response['status'] = false;
                $this->my_response['message'] = lang('failure');
            }
            $this->send_response();
        } catch (ErrorException $ex) {
            $this->error = $ex->getMessage();
            $this->store_error();
        }
    }

    public function edit_diet_instructions_post() {
        try {
            $doctor_id = !empty($this->Common_model->escape_data($this->post_data['doctor_id'])) ? $this->Common_model->escape_data($this->post_data['doctor_id']) : '';
            $diet_instruction_id = !empty($this->Common_model->escape_data($this->post_data['diet_instruction_id'])) ? $this->Common_model->escape_data($this->post_data['diet_instruction_id']) : '';
            $diet_instruction = !empty(trim($this->post_data['diet_instruction'])) ? trim($this->post_data['diet_instruction']) : '';
            $type = !empty(trim($this->post_data['type'])) ? trim($this->post_data['type']) : '';
            $translate_data = !empty($this->post_data['translate_data']) ? $this->post_data['translate_data'] : '';

            if (empty($doctor_id) ||
                    empty($diet_instruction) || empty($type) || 
                    empty($diet_instruction_id)
            ) {
                $this->bad_request();
            }
            $get_role_details = $this->Common_model->get_the_role($this->user_id);
            if (!empty($get_role_details['user_role_data'])) {
                $permission_data = array(
                    'role_data' => $get_role_details['user_role_data'],
                    'module' => 43,
                    'key' => 2
                );
                $check_module_permission = $this->check_module_permission($permission_data);
                if ($check_module_permission == 2) {
                    $this->my_response['status'] = false;
                    $this->my_response['message'] = lang('permission_error');
                    $this->send_response();
                }
            }
            $requested_data = array(
                'doctor_id' => $doctor_id,
                'id' => $diet_instruction_id,
                'type' => $type,
                'diet_instruction' => $diet_instruction
            );
            $this->check_diet_instruction_exists($requested_data, 1);
            if(($type == 2 || $type == 1) && !empty($translate_data)) {
                $lang_id_arr = [];
                $is_lang_duplicate = false;
                foreach ($translate_data as $key => $value) {
                    if(in_array($value['language_id'], $lang_id_arr))
                        $is_lang_duplicate = true;
                    $lang_id_arr[] = $value['language_id'];
                }
                if($is_lang_duplicate) {
                    $this->my_response['status'] = false;
                    $this->my_response['message'] = lang('lang_duplicate_error');
                    $this->send_response();
                }
            }
            $clinical_data = array(
                'doctor_id' => $doctor_id,
                'id' => $diet_instruction_id
            );
            $this->check_diet_instruction_belongs($clinical_data);
            $update_array = array(
                'diet_instruction' => $diet_instruction,
                'updated_at' => $this->utc_time_formated
            );
            $update_where = array(
                'id' => $diet_instruction_id
            );
            $is_update = $this->Common_model->update('me_diet_instruction', $update_array, $update_where);
            if($type == 2 || $type == 1) {
                $translation_insert_data = [];
                if(!empty($translate_data) && is_array($translate_data)) {
                    foreach ($translate_data as $key => $value) {
                        $translation_insert_data[] = [
                            'translation_note_id' => $diet_instruction_id,
                            'translation_text' => $value['note'],
                            'translation_lang_id' => $value['language_id'],
                            'translation_created_at' => $this->utc_time_formated,
                            'translation_created_by' => $this->user_id,
                        ];
                    }
                }
                $update_translation = [
                    'translation_status' => 9,
                    'translation_updated_by' => $this->user_id,
                    'translation_updated_at' => $this->utc_time_formated
                ];
                $this->Common_model->update('me_translation', $update_translation, ['translation_note_id' => $diet_instruction_id]);
                if(count($translation_insert_data) > 0) {
                    $this->Common_model->insert_multiple('me_translation', $translation_insert_data);
                }
            }
            if ($is_update > 0) {
                $this->my_response['status'] = true;
                $this->my_response['message'] = lang('diet_instruction_updated');
            } else {
                $this->my_response['status'] = false;
                $this->my_response['message'] = lang('failure');
            }
            $this->send_response();
        } catch (ErrorException $ex) {
            $this->error = $ex->getMessage();
            $this->store_error();
        }
    }

    public function get_instructions_post() {
        try {
            $doctor_id = !empty($this->Common_model->escape_data($this->post_data['doctor_id'])) ? $this->Common_model->escape_data($this->post_data['doctor_id']) : '';
            $search = !empty($this->Common_model->escape_data($this->post_data['search'])) ? $this->Common_model->escape_data($this->post_data['search']) : '';
            $type = !empty($this->Common_model->escape_data($this->post_data['type'])) ? $this->Common_model->escape_data($this->post_data['type']) : '';
            $page = !empty($this->Common_model->escape_data($this->post_data['page'])) ? trim($this->Common_model->escape_data($this->post_data['page'])) : '1';
            $per_page = !empty($this->Common_model->escape_data($this->post_data['per_page'])) ? trim($this->Common_model->escape_data($this->post_data['per_page'])) : '10';
            if (empty($doctor_id) || empty($type)) {
                $this->bad_request();
            }

            $get_role_details = $this->Common_model->get_the_role($this->user_id);
            if (!empty($get_role_details['user_role_data'])) {
                $permission_data = array(
                    'role_data' => $get_role_details['user_role_data'],
                    'module' => 43,
                    'key' => 3
                );
                $check_module_permission = $this->check_module_permission($permission_data);
                if ($check_module_permission == 2) {
                    $this->my_response['status'] = false;
                    $this->my_response['message'] = lang('permission_error');
                    $this->send_response();
                }
            }
            $where = ['doctor_id' => $doctor_id, 'search' => $search, 'type' => $type];
            $where['page'] = $page;
            $where['per_page'] = $per_page;
            $get_diet_instruction = $this->Common_model->get_instruction($where);
            $count = $this->Common_model->get_instruction($where,true);
            if (!empty($get_diet_instruction)) {
                $this->my_response['status'] = true;
                $this->my_response['message'] = lang('common_detail_found');
                $this->my_response['data'] = $get_diet_instruction;
                $this->my_response['count'] = $count;
            } else {
                $this->my_response['status'] = false;
                $this->my_response['message'] = lang('common_detail_not_found');
                $this->my_response['data'] = array();
            }
            $this->send_response();
        } catch (ErrorException $ex) {
            $this->error = $ex->getMessage();
            $this->store_error();
        }
    }

    public function delete_diet_instructions_post() {
        try {
            $doctor_id = !empty($this->Common_model->escape_data($this->post_data['doctor_id'])) ? $this->Common_model->escape_data($this->post_data['doctor_id']) : '';
            $diet_instruction_id = !empty($this->Common_model->escape_data($this->post_data['diet_instruction_id'])) ? $this->Common_model->escape_data($this->post_data['diet_instruction_id']) : '';
            if (empty($diet_instruction_id) ||
                    empty($doctor_id)
            ) {
                $this->bad_request();
            }
            $get_role_details = $this->Common_model->get_the_role($this->user_id);
            if (!empty($get_role_details['user_role_data'])) {
                $permission_data = array(
                    'role_data' => $get_role_details['user_role_data'],
                    'module' => 43,
                    'key' => 4
                );
                $check_module_permission = $this->check_module_permission($permission_data);
                if ($check_module_permission == 2) {
                    $this->my_response['status'] = false;
                    $this->my_response['message'] = lang('permission_error');
                    $this->send_response();
                }
            }
            $requested_data = array(
                'doctor_id' => $doctor_id,
                'id' => $diet_instruction_id
            );
            $this->check_diet_instruction_belongs($requested_data);
            $update_data = array(
                'status' => 9,
                'updated_at' => $this->utc_time_formated
            );
            $update_where = array(
                'id' => $diet_instruction_id
            );
            $is_update = $this->Common_model->update('me_diet_instruction', $update_data, $update_where);
            if ($is_update > 0) {
                $this->my_response['status'] = true;
                $this->my_response['message'] = lang('diet_instruction_deleted');
            } else {
                $this->my_response['status'] = false;
                $this->my_response['message'] = lang('failure');
            }
            $this->send_response();
        } catch (ErrorException $ex) {
            $this->error = $ex->getMessage();
            $this->store_error();
        }
    }

    public function check_diet_instruction_exists($requested_data, $is_edit = 2) {
        $where_array = array(
            'doctor_id' => $requested_data['doctor_id'],
            'instruction_type' => $requested_data['type'],
            'diet_instruction' => $requested_data['diet_instruction'],
            'status' => 1
        );
        if ($is_edit == 1) {
            $where_array['id !='] = $requested_data['id'];
        }
        $get_diet_instruction = $this->Common_model->get_single_row('me_diet_instruction', 'id', $where_array);
        if (!empty($get_diet_instruction)) {
            $this->my_response['status'] = false;
            $this->my_response['message'] = lang('diet_instruction_already_exists');
            $this->send_response();
        }
    }

    public function check_diet_instruction_belongs($requested_data) {
        $where_array = array(
            'id' => $requested_data['id'],
            'doctor_id' => $requested_data['doctor_id'],
            'status' => 1
        );
        $get_diet_instruction = $this->Common_model->get_single_row('me_diet_instruction', 'id', $where_array);
        if (empty($get_diet_instruction)) {
            $this->my_response['status'] = false;
            $this->my_response['message'] = lang('mycontroller_invalid_request');
            $this->send_response();
        }
    }

    public function get_investigation_instructions_post() {
        try {
            $doctor_id = !empty($this->Common_model->escape_data($this->post_data['doctor_id'])) ? $this->Common_model->escape_data($this->post_data['doctor_id']) : '';
            $health_analytics_test_id = !empty($this->Common_model->escape_data($this->post_data['health_analytics_test_id'])) ? $this->Common_model->escape_data($this->post_data['health_analytics_test_id']) : '';
            if (empty($doctor_id) || empty($health_analytics_test_id)) {
                $this->bad_request();
            }
            $get_role_details = $this->Common_model->get_the_role($this->user_id);
            if (!empty($get_role_details['user_role_data'])) {
                $permission_data = array(
                    'role_data' => $get_role_details['user_role_data'],
                    'module' => 43,
                    'key' => 3
                );
                $check_module_permission = $this->check_module_permission($permission_data);
                if ($check_module_permission == 2) {
                    $this->my_response['status'] = false;
                    $this->my_response['message'] = lang('permission_error');
                    $this->send_response();
                }
            }
            $where = ['doctor_id' => $doctor_id, 'health_analytics_test_id' => $health_analytics_test_id];
            $investigation_instructions = $this->Common_model->get_investigation_instructions($where);
            $this->my_response['status'] = true;
            $this->my_response['message'] = lang('common_detail_found');
            $this->my_response['data'] = $investigation_instructions;
            $this->send_response();
        } catch (ErrorException $ex) {
            $this->error = $ex->getMessage();
            $this->store_error();
        }
    }
    public function get_investigations_post() {
        try {
            $doctor_id = !empty($this->Common_model->escape_data($this->post_data['doctor_id'])) ? $this->Common_model->escape_data($this->post_data['doctor_id']) : '';
            $search = !empty($this->Common_model->escape_data($this->post_data['search'])) ? $this->Common_model->escape_data($this->post_data['search']) : '';
            $page = !empty($this->Common_model->escape_data($this->post_data['page'])) ? trim($this->Common_model->escape_data($this->post_data['page'])) : '1';
            $per_page = !empty($this->Common_model->escape_data($this->post_data['per_page'])) ? trim($this->Common_model->escape_data($this->post_data['per_page'])) : '10';
            if (empty($doctor_id)) {
                $this->bad_request();
            }

            $get_role_details = $this->Common_model->get_the_role($this->user_id);
            if (!empty($get_role_details['user_role_data'])) {
                $permission_data = array(
                    'role_data' => $get_role_details['user_role_data'],
                    'module' => 43,
                    'key' => 3
                );
                $check_module_permission = $this->check_module_permission($permission_data);
                if ($check_module_permission == 2) {
                    $this->my_response['status'] = false;
                    $this->my_response['message'] = lang('permission_error');
                    $this->send_response();
                }
            }
            $where = ['doctor_id' => $doctor_id, 'search' => $search];
            $where['page'] = $page;
            $where['per_page'] = $per_page;
            $get_investigations = $this->Common_model->get_investigations($where);
            $count = $this->Common_model->get_investigations($where,true);

            if (!empty($get_investigations)) {
                $this->my_response['status'] = true;
                $this->my_response['message'] = lang('common_detail_found');
                $this->my_response['data'] = $get_investigations;
                $this->my_response['count'] = $count;
            } else {
                $this->my_response['status'] = false;
                $this->my_response['message'] = lang('common_detail_not_found');
                $this->my_response['data'] = array();
            }
            $this->send_response();
        } catch (ErrorException $ex) {
            $this->error = $ex->getMessage();
            $this->store_error();
        }
    }

    public function add_edit_investigation_post() {
        try {
            $doctor_id = !empty($this->Common_model->escape_data($this->post_data['doctor_id'])) ? $this->Common_model->escape_data($this->post_data['doctor_id']) : '';
            $health_analytics_test_name = !empty(trim($this->post_data['health_analytics_test_name'])) ? trim($this->post_data['health_analytics_test_name']) : '';
            $health_analytics_test_id = !empty(trim($this->post_data['health_analytics_test_id'])) ? trim($this->post_data['health_analytics_test_id']) : '';
            $health_analytics_test_doctor_id = !empty(trim($this->post_data['health_analytics_test_doctor_id'])) ? trim($this->post_data['health_analytics_test_doctor_id']) : '';
            $instruction = !empty($this->post_data['instruction']) ? $this->post_data['instruction'] : [];
            if (empty($doctor_id) ||
                    empty($health_analytics_test_name)
            ) {
                $this->bad_request();
            }
            $get_role_details = $this->Common_model->get_the_role($this->user_id);
            if (!empty($get_role_details['user_role_data'])) {
                $permission_data = array(
                    'role_data' => $get_role_details['user_role_data'],
                    'module' => 43,
                    'key' => 1
                );
                $check_module_permission = $this->check_module_permission($permission_data);
                if ($check_module_permission == 2) {
                    $this->my_response['status'] = false;
                    $this->my_response['message'] = lang('permission_error');
                    $this->send_response();
                }
            }
            $requested_data = array(
                'doctor_id' => $doctor_id,
                'diet_instruction' => $diet_instruction
            );
            if(!empty($health_analytics_test_id)) {
                if(!empty($health_analytics_test_doctor_id)) {
                    $health_analytics_test_data = array(
                        'health_analytics_test_name' => strtoupper($health_analytics_test_name),
                        'health_analytics_test_name_precise' => strtoupper($health_analytics_test_name),
                        'health_analytics_test_updated_at' => $this->utc_time_formated,
                        'health_analytics_test_updated_by' => $this->user_id
                    );
                    $update_where = array(
                        'health_analytics_test_id' => $health_analytics_test_id
                    );
                    $is_update = $this->Common_model->update('me_health_analytics_test', $health_analytics_test_data, $update_where);
                }
                $this->my_response['message'] = lang('investigation_updated');
            } else {
                $health_analytics_test_data = array(
                    'health_analytics_test_doctor_id' => $doctor_id,
                    'health_analytics_test_name' => strtoupper($health_analytics_test_name),
                    'health_analytics_test_name_precise' => strtoupper($health_analytics_test_name),
                    'health_analytics_test_parent_id' => 0,
                    'health_analytics_test_created_at' => $this->utc_time_formated,
                    'health_analytics_test_status' => 1,
                    'health_analytics_test_type' => 2,
                    'health_analytics_test_created_by' => $this->user_id
                );
                $health_analytics_test_id = $this->Common_model->insert('me_health_analytics_test', $health_analytics_test_data);
                $this->my_response['message'] = lang('investigation_added');
            }
            $where = [
                'health_analytics_test_id' => $health_analytics_test_id,
                'doctor_id' => $doctor_id,
            ];
            $this->Common_model->delete_data('me_investigation_instructions', $where);
            if(!empty($instruction)) {
                $investigation_instructions_data = [];
                foreach ($instruction as $key => $value) {
                    if(empty($value['instruction']))
                        continue;
                    $investigation_instructions_data[] = [
                        'health_analytics_test_id' => $health_analytics_test_id,
                        'instruction' => $value['instruction'],
                        'doctor_id' => $doctor_id,
                        'created_by' => $this->user_id,
                        'created_at' => $this->utc_time_formated,
                    ];
                }
                if(!empty($investigation_instructions_data))
                    $this->Common_model->insert_multiple('me_investigation_instructions', $investigation_instructions_data);
            }
            $this->my_response['status'] = true;
            $this->send_response();
        } catch (ErrorException $ex) {
            $this->error = $ex->getMessage();
            $this->store_error();
        }
    }

    public function delete_investigation_post() {
        try {
            $doctor_id = !empty($this->Common_model->escape_data($this->post_data['doctor_id'])) ? $this->Common_model->escape_data($this->post_data['doctor_id']) : '';
            $health_analytics_test_id = !empty($this->Common_model->escape_data($this->post_data['health_analytics_test_id'])) ? $this->Common_model->escape_data($this->post_data['health_analytics_test_id']) : '';
            if (empty($health_analytics_test_id) ||
                    empty($doctor_id)
            ) {
                $this->bad_request();
            }
            $get_role_details = $this->Common_model->get_the_role($this->user_id);
            if (!empty($get_role_details['user_role_data'])) {
                $permission_data = array(
                    'role_data' => $get_role_details['user_role_data'],
                    'module' => 43,
                    'key' => 4
                );
                $check_module_permission = $this->check_module_permission($permission_data);
                if ($check_module_permission == 2) {
                    $this->my_response['status'] = false;
                    $this->my_response['message'] = lang('permission_error');
                    $this->send_response();
                }
            }
            $requested_data = array(
                'doctor_id' => $doctor_id,
                'id' => $diet_instruction_id
            );
            $update_data = array(
                'health_analytics_test_status' => 9,
                'health_analytics_test_updated_by' => $this->user_id,
                'health_analytics_test_updated_at' => $this->utc_time_formated
            );
            $update_where = array(
                'health_analytics_test_id' => $health_analytics_test_id,
                'health_analytics_test_doctor_id' => $doctor_id,
            );
            $this->Common_model->update('me_health_analytics_test', $update_data, $update_where);
            $where = [
                'health_analytics_test_id' => $health_analytics_test_id,
                'doctor_id' => $doctor_id,
            ];
            $this->Common_model->delete_data('me_investigation_instructions', $where);
            $this->my_response['status'] = true;
            $this->my_response['message'] = lang('investigation_deleted');
            $this->send_response();
        } catch (ErrorException $ex) {
            $this->error = $ex->getMessage();
            $this->store_error();
        }
    }

    public function translation_note_post() {
        try {
            $note_id = !empty($this->Common_model->escape_data($this->post_data['note_id'])) ? $this->Common_model->escape_data($this->post_data['note_id']) : '';
            $lang_id = !empty($this->Common_model->escape_data($this->post_data['lang_id'])) ? $this->Common_model->escape_data($this->post_data['lang_id']) : '';
            $text = !empty($this->Common_model->escape_data($this->post_data['text'])) ? $this->Common_model->escape_data($this->post_data['text']) : '';
            if (empty($note_id) || empty($lang_id) || empty($text)) {
                $this->bad_request();
            }
            $lang_row = $this->Common_model->get_single_row('me_languages', 'language_code', ['language_id' => $lang_id, 'language_status' => 1]);
            if(empty($lang_row)) {
                $this->my_response['status'] = false;
                $this->my_response['message'] = lang('invalid_language');
                $this->send_response();
            }
            $where = ['translation_note_id' => $note_id, 'translation_lang_id' => $lang_id, 'translation_status' => 1];
            $result = $this->Common_model->get_single_row('me_translation', 'translation_note_id', $where);
            require_once $GLOBALS['ENV_VARS']['GOOGLE_TRANSLATE_FILE_PATH'];
            $targetLang = $lang_row['language_code'];
            $translate = google_translate($text, $targetLang);
            if(!empty($translate)) {
                $translation_data = ['translation_text' => $translate];
                if(!empty($result)) {
                    $translation_data['translation_updated_at'] = $this->utc_time_formated;
                    $translation_data['translation_updated_by'] = $this->user_id;
                    $this->Common_model->update('me_translation', $translation_data, $where);
                } else {
                    $translation_data['translation_note_id'] = $note_id;
                    $translation_data['translation_lang_id'] = $lang_id;
                    $translation_data['translation_created_at'] = $this->utc_time_formated;
                    $translation_data['translation_created_by'] = $this->user_id;
                    $inserted_id = $this->Common_model->insert('me_translation', $translation_data);
                }
                $this->my_response['status'] = true;
                $this->my_response['message'] = lang('translate_success');
            } else {
                $this->my_response['status'] = false;
                $this->my_response['message'] = lang('failure');
            }
            $this->send_response();
        } catch (ErrorException $ex) {
            $this->error = $ex->getMessage();
            $this->store_error();
        }
    }

    public function get_translate_post() {
        try {
            $note_id = !empty($this->Common_model->escape_data($this->post_data['note_id'])) ? $this->Common_model->escape_data($this->post_data['note_id']) : '';
            if (empty($note_id)
            ) {
                $this->bad_request();
            }
            $result = $this->Common_model->get_all_rows('me_translation', 'translation_lang_id,translation_text', ['translation_note_id' => $note_id, 'translation_status' => 1]);
            $this->my_response['status'] = true;
            $this->my_response['data'] = $result;
            $this->send_response();
        } catch (ErrorException $ex) {
            $this->error = $ex->getMessage();
            $this->store_error();
        }
    }

}