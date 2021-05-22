<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/*
  | -------------------------------------------------------------------------
  | URI ROUTING
  | -------------------------------------------------------------------------
  | This file lets you re-map URI requests to specific controller functions.
  |
  | Typically there is a one-to-one relationship between a URL string
  | and its corresponding controller class/method. The segments in a
  | URL normally follow this pattern:
  |
  |	example.com/class/method/id/
  |
  | In some instances, however, you may want to remap this relationship
  | so that a different class/function is called than the one
  | corresponding to the URL.
  |
  | Please see the user guide for complete details:
  |
  |	https://codeigniter.com/user_guide/general/routing.html
  |
  | -------------------------------------------------------------------------
  | RESERVED ROUTES
  | -------------------------------------------------------------------------
  |
  | There are three reserved routes:
  |
  |	$route['default_controller'] = 'welcome';
  |
  | This route indicates which controller class should be loaded if the
  | URI contains no data. In the above example, the "welcome" class
  | would be loaded.
  |
  |	$route['404_override'] = 'errors/page_missing';
  |
  | This route will tell the Router which controller/method to use if those
  | provided in the URL cannot be matched to a valid route.
  |
  |	$route['translate_uri_dashes'] = FALSE;
  |
  | This is not exactly a route, but allows you to automatically route
  | controller and method names that contain dashes. '-' isn't a valid
  | class or method name character, so it requires translation.
  | When you set this option to TRUE, it will replace ALL dashes in the
  | controller and method URI segments.
  |
  | Examples:	my-controller/index	-> my_controller/index
  |		my-controller/my-method	-> my_controller/my_method
 */
$route['default_controller'] = 'Common';
$route['404_override'] = 'Common/not_found_response';
$route['translate_uri_dashes'] = FALSE;

$route['documentation'] = "documentation/index";

//my routes
$route['get_last_api_log'] = "common/get_last_api_log";
$route['push_notification_test'] = "common/push_notification_test";
$route['pushwoosh_notification_test'] = "common/send_pushwoosh_notification"; 
$route['get_country_code'] = 'common/get_country_code';
$route['get_states'] = 'common/get_states';
$route['get_cities'] = 'common/get_cities';
$route['get_laboratories'] = 'common/get_laboratories';
$route['get_reports_types'] = 'common/get_reports_types';
$route['get_health_anlaytics_test'] = 'common/get_health_anlaytics_test';
$route['get_all_health_anlaytics_test'] = 'common/get_all_health_anlaytics_test';
$route['get_health_anlaytics'] = 'common/get_health_anlaytics';
$route['get_colleges'] = 'common/get_colleges';
$route['get_qualifications'] = 'common/get_qualifications';
$route['get_councils'] = 'common/get_councils';
$route['get_language'] = 'common/get_language';
$route['get_specialization'] = 'common/get_specialization';
$route['get_diseases'] = 'common/get_diseases';

$route['login'] = 'user/login';
$route['register'] = 'user/register';
$route['update_profile'] = 'user/update_profile';
$route['upload_image'] = 'user/upload_image';
$route['upload_sign_image'] = 'user/upload_sign_image';
$route['change_password'] = 'user/change_password';
$route['verify_otp'] = 'user/verify_otp';
$route['resend_otp'] = 'user/resend_otp';
$route['logout'] = 'user/logout';
$route['update_device_token'] = 'user/update_device_token';
$route['forgot_password'] = 'user/forgot_password';
$route['get_user_details'] = 'user/get_user_details';
$route['add_family_member'] = 'user/add_family_member';
$route['get_family_members'] = 'user/get_family_members';
$route['get_advertisement'] = "user/get_advertisement";

$route['get_doctor_list'] = 'doctor/get_doctor_list';
$route['resetpassword/(:any)'] = "resetpassword/index/$1";
$route['resetpasswordpost'] = "resetpassword/reset";

//reminder routes
$route['add_reminder'] = 'reminder/add_reminder';
$route['edit_reminder'] = 'reminder/edit_reminder';
$route['delete_reminder'] = 'reminder/delete_reminder';
$route['sync_reminder'] = 'reminder/sync_reminder';
$route['add_reminder_record'] = 'reminder/add_reminder_record';
$route['get_reminder_chart'] = 'reminder/get_reminder_chart';

//medicine routes
$route['get_drug_list'] = 'drug/get_drug_list';
$route['add_drug'] = 'drug/add_drug';

//update profile for doctor
$route['update_profile_doctor'] = 'doctor/update_profile_doctor';
$route['update_terms_condtion_flag'] = 'doctor/update_terms_condtion_flag';
//medical condition code
$route['get_medical_condition'] = 'user/get_medical_condition';
$route['add_family_medical_history'] = 'user/add_family_medical_history';
$route['edit_family_medical_history'] = 'user/edit_family_medical_history';
$route['delete_family_medical_history'] = 'user/delete_family_medical_history';

//doctor apis
$route['search_doctor'] = 'doctor/search_doctor';
$route['doctor_detail'] = 'doctor/doctor_detail';
$route['get_availability'] = 'doctor/get_availability';
$route['doctor_past_history'] = 'doctor/doctor_past_history';

//appointment apis
$route['confirm_appointment'] = 'appointments/confirm_appointment';
$route['get_my_appointments'] = 'appointments/get_my_appointments';
$route['appointment_detail'] = 'appointments/appointment_detail';
$route['cancel_appointment'] = 'appointments/cancel_appointment';
$route['reschedule_appointment'] = 'appointments/reschedule_appointment';
$route['get_appointments_list'] = 'appointments/get_appointments_list';
$route['appointments_book_send_otp'] = 'appointments/appointments_book_send_otp';
$route['update_appointment_status'] = 'appointments/update_appointment_status';

//clinic routes
$route['add_clinic'] = 'clinic/add_clinic';
$route['edit_clinic'] = 'clinic/edit_clinic';
$route['get_clinic_detail'] = 'clinic/get_clinic_detail';

//patient routes
$route['add_patient'] = 'patient/add_patient';
$route['update_patient_dob'] = 'patient/update_patient_dob';

//extra routes
$route['get_support_contact'] = 'common/get_support_contact';
$route['add_issue'] = 'common/add_issue';
$route['get_whats_new_data'] = 'common/get_whats_new_data';

//doctor routes
$route['get_doctors_clinics'] 		  = 'clinic/get_doctors_clinics';
$route['get_doctor_whole_details'] 	  = 'doctor/get_doctor_whole_details';
$route['get_doctor_edu_details'] 	  = 'doctor/get_doctor_edu_details';
$route['get_doctor_reg_details'] 	  = 'doctor/get_doctor_reg_details';
$route['get_doctor_award_details'] 	  = 'doctor/get_doctor_award_details';
$route['update_doctor_other_details'] = 'doctor/update_doctor_other_details';
$route['update_doctor_other_reg_details']   = 'doctor/update_doctor_other_reg_details';
$route['update_doctor_other_award_details'] = 'doctor/update_doctor_other_award_details';
$route['get_profile_per'] 			  = 'doctor/get_profile_per';
$route['get_block_calendar_slot'] 	  = 'doctor/get_block_calendar_slot';
$route['add_block_calendar'] 		  = 'doctor/add_block_calendar';
$route['edit_block_calendar'] 		  = 'doctor/edit_block_calendar';
$route['delete_block_calendar'] 	  = 'doctor/delete_block_calendar';

$route['search_patient'] = "patient/search_patient";
$route['add_staff'] = "clinic/add_staff";
$route['get_staff'] = "clinic/get_staff";
$route['testmail'] = "clinic/testmail";
$route['delete_staff_user'] = "clinic/delete_staff_user";
$route['change_status_staff_user'] = "clinic/change_status_staff_user";
$route['set_doctor_avialability'] = "doctor/set_doctor_avialability";
$route['get_doctor_avialability'] = "doctor/get_doctor_avialability";
$route['set_doctor_avialability_status'] = "doctor/set_doctor_avialability_status";
$route['resend_email_verify_link'] = "user/resend_email_verify_link";
$route['verify_update_number_otp'] = "user/verify_update_number_otp";
$route['set_doctor_alert'] = "doctor/set_doctor_alert";
$route['get_doctor_alert'] = "doctor/get_doctor_alert";
$route['set_clinic_availability'] = "clinic/set_clinic_availability";
$route['update_clinic_duration'] = "clinic/update_clinic_duration";
$route['add_tax'] = "billing/add_tax";
$route['get_tax'] = "billing/get_tax";
$route['delete_tax'] = "billing/delete_tax";
$route['edit_tax'] = "billing/edit_tax";
$route['get_payment_type'] = "billing/get_payment_type";
$route['add_payment_mode'] = "billing/add_payment_mode";
$route['get_payment_mode'] = "billing/get_payment_mode";
$route['delete_payment_mode'] = "billing/delete_payment_mode";
$route['edit_payment_mode'] = "billing/edit_payment_mode";
$route['add_pricing'] = "billing/add_pricing";
$route['get_pricing'] = "billing/get_pricing";
$route['delete_pricing'] = "billing/delete_pricing";
$route['edit_pricing'] = "billing/edit_pricing";
$route['my_primary_doctor'] = "patient/my_primary_doctor";
$route['add_clinical_notes'] = "notes/add_clinical_notes";
$route['edit_clinical_notes'] = "notes/edit_clinical_notes";
$route['get_clinical_notes'] = "notes/get_clinical_notes";
$route['delete_clinical_notes'] = "notes/delete_clinical_notes";
$route['get_drugs'] = "drug/get_drugs";
$route['get_drug_detail'] = "drug/get_drug_detail";
$route['get_drug_brand_type'] = "drug/get_drug_brand_type";
$route['get_drug_frequency'] = 'drug/get_drug_frequency';
$route['get_drug_generic'] = 'drug/get_drug_generic';
$route['get_drug_generic_detail'] = 'drug/get_drug_generic_detail';
$route['add_drug_by_doctor'] = 'drug/add_drug_by_doctor';
$route['get_doctor_drug'] = 'drug/get_doctor_drug';
$route['delete_drug'] = 'drug/delete_drug';
$route['add_template'] = 'template/add_template';
$route['get_template_detail'] = 'template/get_template_detail';
$route['delete_template'] = 'template/delete_template';
$route['edit_template'] = 'template/edit_template';
$route['get_template'] = 'template/get_template';
// $route['get_patient_group'] = 'patientgroup/get_patient_group';
$route['get_lab_test'] = 'common/get_lab_test';
// $route['add_patient_group'] = 'patientgroup/add_patient_group';
// $route['delete_patient_group'] = 'patientgroup/delete_patient_group';
// $route['edit_patient_group'] = 'patientgroup/edit_patient_group';
$route['set_setting'] = 'common/set_setting';
$route['get_setting'] = 'common/get_setting';
$route['set_staff_setting'] = 'common/set_staff_setting';
$route['get_doctor_patient_list'] = 'doctor/get_doctor_patient_list';
$route['get_doctor_patient_detail'] = 'doctor/get_doctor_patient_detail';
$route['get_patient_appointment_date'] = 'appointments/get_patient_appointment_date';
$route['add_vital_for_patient'] = 'patient/add_vital_for_patient';
$route['edit_vital_for_patient'] = 'patient/edit_vital_for_patient';
$route['add_previous_vitals'] = 'patient/add_previous_vitals';
$route['get_previous_vitals'] = 'patient/get_previous_vitals';
$route['add_vital'] = 'patient/add_vital';
$route['get_vital'] = 'patient/get_vital';
$route['add_report'] = 'patient/add_report';
$route['get_report'] = 'patient/get_report';
$route['delete_report'] = 'patient/delete_report';
$route['get_report_detail'] = 'patient/get_report_detail';
$route['add_clinic_notes'] = 'patient/add_clinic_notes';
$route['delete_clinic_notes_image'] = 'patient/delete_clinic_notes_image';
$route['add_prescription'] = 'patient/add_prescription';
$route['edit_prescription'] = 'patient/edit_prescription';
$route['delete_prescription'] = 'patient/delete_prescription';
$route['add_investigation'] = 'patient/add_investigation';
$route['edit_investigation'] = 'patient/edit_investigation';
$route['add_procedure'] = 'patient/add_procedure';
$route['edit_procedure'] = 'patient/edit_procedure';
$route['get_patient_report_detail'] = 'patient/get_patient_report_detail';
$route['add_health_analytics'] = 'patient/add_health_analytics';
$route['edit_health_analytics'] = 'patient/edit_health_analytics';
$route['get_patient_health_analytics_report'] = 'patient/get_patient_health_analytics_report';
$route['get_patient_health_analytics'] = 'patient/get_patient_health_analytics';
$route['add_refer'] = 'doctor/add_refer';
$route['search_web_doctor'] = 'doctor/search_web_doctor';
$route['fav_doctor'] = 'doctor/fav_doctor';
$route['get_fav_doctor_listing'] = 'doctor/get_fav_doctor_listing';
$route['add_followup_data'] = 'patient/add_followup_data';
$route['change_template'] = 'patient/change_template';
$route['send_sms'] = 'common/send_sms';
$route['get_patient_clinical_notes_report'] = 'patient/get_patient_clinical_notes_report';
$route['get_patient_procedure_report'] = 'patient/get_patient_procedure_report';
$route['get_patient_investigation_report'] = 'patient/get_patient_investigation_report';
$route['get_patient_prescription'] = 'patient/get_patient_prescription';
$route['save_template_based_appointment'] = 'template/save_template_based_appointment';
$route['resend_otp_for_clinic'] = 'user/resend_otp_for_clinic';
$route['verify_otp_for_clinic'] = 'user/verify_otp_for_clinic';
$route['resend_email_link_for_clinic'] = 'user/resend_email_link_for_clinic';
$route['get_refer'] = 'doctor/get_refer';
$route['clone_prescription'] = 'patient/clone_prescription';
$route['get_notification_list'] = 'user/get_notification_list';
$route['send_email'] = 'common/send_email';
$route['get_video'] = 'common/get_video';
$route['edit_kco'] = 'patient/edit_kco';
$route['set_appointment_state'] = 'appointments/set_appointment_state';
$route['get_related_drugs'] = 'drug/get_related_drugs';
$route['add_prescription_by_patient'] = 'drug/add_prescription_by_patient';
$route['get_procedure'] = 'common/get_procedure';
$route['static_page'] = 'common/static_page';
$route['add_billing'] = 'billing/add_billing';
$route['get_billing'] = 'billing/get_billing';
$route['get_my_prescription'] = 'drug/get_my_prescription';
$route['get_my_prescription_detail'] = 'drug/get_my_prescription_detail';
$route['share_record'] = 'doctor/share_record';
$route['get_patient_billing'] = 'billing/get_patient_billing';
$route['get_billing_detail'] = 'billing/get_billing_detail';
$route['share_invoice'] = 'doctor/share_invoice';
$route['get_appointed_patient_details'] = 'appointments/get_appointed_patient_details';
$route['get_staff_detail'] = 'clinic/get_staff_detail';
$route['delete_vital'] = 'patient/delete_vital';

$route['get_sidebar_menu'] = 'common/get_sidebar_menu';
$route['get_clinic_list'] = 'clinic/get_clinic_list';
$route['update_caregiver'] = 'user/update_caregiver';
$route['update_staff'] = "clinic/update_staff";
$route['get_static_data'] = "common/get_static_data";
$route['delete_family_member'] = "user/delete_family_member";
$route['search_doctor_list'] = "patient/search_doctor_list";
$route['get_global_settings'] = "common/get_global_settings";
$route['delete_vital_by_patient'] = "patient/delete_vital_by_patient";
$route['delete_prescription_by_patient'] = "patient/delete_prescription_by_patient";
$route['delete_report_by_patient'] = "patient/delete_report_by_patient";
$route['invite_user'] = "user/invite_user";
$route['register_verify_otp'] = "user/register_verify_otp";
$route['register_resend_otp'] = "user/register_resend_otp";
$route['reset_user_password'] = "user/reset_user_password";
$route['update_terms_condtion'] = "user/update_terms_condtion";
$route['get_api_log'] = "common/get_api_log";
$route['get_prescription_print_setting'] = "common/get_prescription_print_setting";
$route['set_prescription_print_setting'] = "common/set_prescription_print_setting";

$route['update_invoice_no_setting'] = "doctor/update_invoice_no_setting";
$route['manage_previous_patient_health_analytics'] = "patient/manage_previous_patient_health_analytics";

$route['get_survey'] = "survey/get_survey";
$route['get_survey_questions'] = "survey/get_survey_questions";
$route['save_doctor_survey_data'] = "survey/save_doctor_survey_data";
$route['save_survey_log'] = "survey/save_survey_log";

$route['validate_doctor_import_file'] = "doctor_data_import/validate_doctor_import_file";
$route['upload_doctor_import_file'] = "doctor_data_import/upload_doctor_import_file";
$route['get_doctor_import_files'] = "doctor_data_import/get_doctor_import_files";
$route['doctor_name_selection'] = "doctor_data_import/doctor_name_selection";
$route['ready_for_import'] = "doctor_data_import/ready_for_import";
$route['get_import_log'] = "doctor_data_import/get_import_log";
$route['get_import_file_type'] = "doctor_data_import/get_import_file_type";

$route['test_google_event'] = 'appointments/test_google_event';

$route['get_diagrams'] = "anatomical/get_diagrams";
$route['get_diagrams_category'] = "anatomical/get_diagrams_category";
$route['get_diagrams_sub_category'] = "anatomical/get_diagrams_sub_category";
$route['doctor_art'] = 'anatomical/upload_diagrams_image';
$route['permanent_delete_report'] = 'anatomical/permanent_delete_report';

$route['get_doctor_subscription'] = "subscription/get_doctor_subscription";
$route['get_subscription_plan'] = "subscription/get_subscription_plan";
$route['get_doctor_subscription_history'] = "subscription/get_doctor_subscription_history";
$route['view_sub_invoice'] = "subscription/view_sub_invoice";
$route['apply_promo_code'] = "subscription/apply_promo_code";
$route['order_without_payment'] = "subscription/order_without_payment";

$route['create_payment_order'] = "razorpay/create_payment_order";
$route['payment_capture'] = "razorpay/payment_capture";

$route['member_summary'] = "reports/member_summary";
$route['mob_summary'] = "reports/mob_summary";
$route['lost_patient'] = "reports/lost_patient";
$route['patient_progress'] = "reports/patient_progress";
$route['patients_city'] = "reports/patients_city";
$route['drug_generic'] = "reports/drug_generic";
$route['get_report_drugs'] = "reports/get_drugs";
$route['get_kco'] = "reports/get_kco";
$route['get_diagnoses'] = "reports/get_diagnoses";
$route['invoice_summary'] = "reports/invoice_summary";
$route['cancel_appointment_list'] = "reports/cancel_appointment";
$route['get_lost_patient_details'] = "reports/get_lost_patient_details";
$route['update_hour_format'] = 'common/update_hour_format';
$route['refer_rx_send_otp'] = 'patient/refer_rx_send_otp';
$route['refer_rx_verify_otp'] = 'patient/refer_rx_verify_otp';
$route['get_patient_rx_data'] = 'patient/get_patient_rx_data';
$route['get_prescription_pdf'] = 'patient/get_prescription_pdf';
$route['get_patient_user_details'] = 'patient/get_patient_user_details';
$route['search_generic'] = 'drug/search_generic';
$route['upload_rx'] = 'patient/upload_rx';
$route['get_uploaded_rx'] = 'patient/get_uploaded_rx';
$route['delete_rx_uploaded'] = 'patient/delete_rx_uploaded';
$route['get_doctor_setting'] = 'common/get_doctor_setting';
$route['get_invoice_list'] = 'billing/get_invoice_list';
$route['delete_invoice'] = 'billing/delete_invoice';
$route['search_instruction'] = 'common/search_instruction';
$route['get_instructions'] = 'diet_instructions/get_instructions';
$route['add_diet_instructions'] = 'diet_instructions/add_diet_instructions';
$route['delete_diet_instructions'] = 'diet_instructions/delete_diet_instructions';
$route['edit_diet_instructions'] = 'diet_instructions/edit_diet_instructions';
$route['get_health_advice_groups'] = 'health_advice/get_health_advice_groups';
$route['get_health_advice'] = 'health_advice/get_health_advice';
$route['add_patient_health_advice'] = 'health_advice/add_patient_health_advice';
$route['get_patients'] = 'communications/get_patients';
$route['get_commu_patient_groups'] = 'communications/get_commu_patient_groups';
$route['add_communication'] = 'communications/add_communication';
$route['get_communication_list'] = 'communications/get_communication_list';
$route['get_communication_date'] = 'communications/get_communication_date';
$route['get_sms_template'] = 'communications/get_sms_template';
$route['test_template_sms'] = 'communications/test_template_sms';
$route['create_payment_credits_order'] = 'communications/create_payment_credits_order';
$route['payment_credits_capture'] = 'communications/payment_credits_capture';
$route['get_auto_added_groups_member'] = 'communications/get_auto_added_groups_member';
$route['get_tele_payment_mode'] = 'tele_payment_mode/get_tele_payment_mode';
$route['add_tele_payment_mode'] = 'tele_payment_mode/add_tele_payment_mode';
$route['delete_tele_payment_mode'] = 'tele_payment_mode/delete_tele_payment_mode';
$route['get_payment_mode_master'] = 'tele_payment_mode/get_payment_mode_master';
$route['change_appointment_type'] = 'appointments/change_appointment_type';
$route['get_patient_groups'] = 'patient_groups/get_patient_groups';
$route['add_patient_group'] = 'patient_groups/add_patient_group';
$route['delete_patient_group'] = 'patient_groups/delete_patient_group';
$route['search_patient_group'] = 'patient_groups/search_patient_group';
$route['get_disease_by_patient'] = 'patient_groups/get_disease_by_patient';
$route['get_patient_group_members'] = 'patient_groups/get_patient_group_members';
$route['get_video_conf_token'] = 'video_conf/get_video_conf_token';
$route['end_video_conf_call'] = 'video_conf/end_video_conf_call';
$route['update_connection_id'] = 'video_conf/update_connection_id';
$route['generate_video_url_patient'] = 'video_conf/generate_video_url_patient';
$route['get_teleconsultation_date'] = 'teleconsultation/get_teleconsultation_date';
$route['get_teleconsultation_list'] = 'teleconsultation/get_teleconsultation_list';
$route['get_tele_global_data'] = 'teleconsultation/get_tele_global_data';
$route['create_payment_minutes_order'] = 'teleconsultation/create_payment_minutes_order';
$route['payment_minutes_capture'] = 'teleconsultation/payment_minutes_capture';
$route['daigrams_add_to_prescription'] = 'anatomical/daigrams_add_to_prescription';
$route['get_document_from_share'] = 'anatomical/get_document_from_share';
$route['search_investigation_instructions'] = 'common/search_investigation_instructions';
$route['get_investigations'] = 'diet_instructions/get_investigations';
$route['delete_investigation'] = 'diet_instructions/delete_investigation';
$route['add_edit_investigation'] = 'diet_instructions/add_edit_investigation';
$route['get_investigation_instructions'] = 'diet_instructions/get_investigation_instructions';
$route['translation_note'] = 'diet_instructions/translation_note';
$route['get_translate'] = 'diet_instructions/get_translate';
$route['save_uas7_data'] = 'patient/save_uas7_data';
$route['get_uas7_parameters'] = 'patient/get_uas7_parameters';
$route['get_my_procedure_report'] = 'patient/get_my_procedure_report';
$route['search_caretaker_list'] = 'patient/search_caretaker_list';
$route['send_caretaker_otp'] = 'patient/send_caretaker_otp';
$route['get_share_link'] = 'share_link/get_share_link';
$route['get_social_media_master'] = 'share_link/get_social_media_master';
$route['create_reg_link'] = 'share_link/create_reg_link';
$route['delete_share_link'] = 'share_link/delete_share_link';
$route['share_qrcode_link'] = 'share_link/share_qrcode_link';
$route['update_booking_status'] = "doctor/update_booking_status";
$route['get_prescription_template'] = "common/get_prescription_template";