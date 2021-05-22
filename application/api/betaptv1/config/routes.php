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
$route['get_country_code'] = 'common/get_country_code';
$route['get_states'] = 'common/get_states';
$route['get_cities'] = 'common/get_cities';
$route['get_laboratories'] = 'common/get_laboratories';
$route['get_reports_types'] = 'common/get_reports_types';
$route['get_health_anlaytics_test'] = 'common/get_health_anlaytics_test';
$route['get_health_anlaytics'] = 'common/get_health_anlaytics';
$route['get_colleges'] = 'common/get_colleges';
$route['get_qualifications'] = 'common/get_qualifications';
$route['get_councils'] = 'common/get_councils';
$route['get_language'] = 'common/get_language';
$route['get_specialization'] = 'common/get_specialization';
$route['get_diseases'] = 'common/get_diseases';
$route['patient_help_and_support'] = 'common/patient_help_and_support';

$route['login'] = 'user/login';

$route['register'] = 'user/register';
$route['update_profile'] = 'user/update_profile';
$route['upload_image'] = 'user/upload_image';
$route['change_password'] = 'user/change_password';
$route['verify_otp'] = 'user/verify_otp';
$route['resend_otp'] = 'user/resend_otp';
$route['logout'] = 'user/logout';
$route['update_device_token'] = 'user/update_device_token';
$route['forgot_password'] = 'user/forgot_password';
$route['get_user_details'] = 'user/get_user_details';
$route['add_family_member'] = 'user/add_family_member';
$route['get_family_members'] = 'user/get_family_members';

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
$route['get_all_reminder_chart'] = 'reminder/get_all_reminder_chart';

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

//clinic routes
$route['add_clinic'] = 'clinic/add_clinic';
$route['edit_clinic'] = 'clinic/edit_clinic';
$route['get_clinic_detail'] = 'clinic/get_clinic_detail';

//patient routes
$route['add_patient'] = 'patient/add_patient';

//extra routes
$route['get_support_contact'] = 'common/get_support_contact';
$route['add_issue'] = 'common/add_issue';

$route['get_whats_new_data'] = 'common/get_whats_new_data';

//doctor routes
$route['get_doctors_clinics'] = 'clinic/get_doctors_clinics';
$route['get_doctor_whole_details'] = 'doctor/get_doctor_whole_details';
$route['get_doctor_edu_details'] = 'doctor/get_doctor_edu_details';
$route['get_doctor_reg_details'] = 'doctor/get_doctor_reg_details';
$route['get_doctor_award_details'] = 'doctor/get_doctor_award_details';
$route['update_doctor_other_details'] = 'doctor/update_doctor_other_details';
$route['update_doctor_other_reg_details'] = 'doctor/update_doctor_other_reg_details';
$route['update_doctor_other_award_details'] = 'doctor/update_doctor_other_award_details';
$route['get_profile_per'] = 'doctor/get_profile_per';
$route['add_block_calendar'] = 'doctor/add_block_calendar';
$route['edit_block_calendar'] = 'doctor/edit_block_calendar';
$route['delete_block_calendar'] = 'doctor/delete_block_calendar';

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
$route['get_patient_group'] = 'patientgroup/get_patient_group';
$route['get_lab_test'] = 'common/get_lab_test';
$route['add_patient_group'] = 'patientgroup/add_patient_group';
$route['delete_patient_group'] = 'patientgroup/delete_patient_group';
$route['edit_patient_group'] = 'patientgroup/edit_patient_group';
$route['set_setting'] = 'common/set_setting';
$route['get_setting'] = 'common/get_setting';
$route['set_staff_setting'] = 'common/set_staff_setting';
$route['get_doctor_patient_list'] = 'doctor/get_doctor_patient_list';
$route['get_doctor_patient_detail'] = 'doctor/get_doctor_patient_detail';
$route['get_patient_appointment_date'] = 'appointments/get_patient_appointment_date';
$route['add_vital_for_patient'] = 'patient/add_vital_for_patient';
$route['edit_vital_for_patient'] = 'patient/edit_vital_for_patient';
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
$route['add_invoice_by_patient'] = 'drug/add_invoice_by_patient';
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
$route['delete_invoice_by_patient'] = "patient/delete_invoice_by_patient";
$route['member_verify_otp'] = "user/member_verify_otp";
$route['accept_family_member_request'] = "user/accept_family_member_request";
$route['remove_family_member'] = "user/remove_family_member";
$route['get_linked_family_member'] = "user/get_linked_family_member";
$route['check_patient_exist'] = "user/check_patient_exist";
$route['member_resend_otp'] = "user/member_resend_otp";
$route['get_all_master_data'] = "common/get_all_master_data";
$route['get_user_all_data'] = "common/get_user_all_data";