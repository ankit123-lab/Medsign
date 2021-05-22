
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
$route['default_controller'] = 'web';
$route['404_override'] = 'errors/error_404';
$route['translate_uri_dashes'] = FALSE;
$route['resetpassword/(:any)'] = "resetpassword/index/$1";
$route['reset_password'] = "resetpassword/reset";
$route['verifyaccount/(:any)'] = "resetpassword/verify_account/$1";

/*
$route['about-us'] = "Staticpages/about_us";
$route['plan'] = "Staticpages/plan";
$route['contact-us'] = "Staticpages/contact_us";
$route['terms-conditions'] = "Staticpages/terms_conditions";
$route['privacy-policy'] = "Staticpages/privacy_policy";
$route['faq'] = "Staticpages/faq";
 * 
 */
$route['terms-conditions'] = "web/web_static_pages/1";
$route['privacy-policy'] = "web/web_static_pages/2";
$route['about-us'] = "web/web_static_pages/3";
$route['contact-us'] = "web/web_static_pages/4";
$route['terms-condition-hcps'] = "web/web_static_pages/5";
$route['av'] = "web/audio_view";

$route['dp/(:any)'] = "cron/download_prescription/$1";
$route['viewqr/(:any)'] = "cron/viewqr/$1";
$route['inv/(:any)'] = "cron/view_invoice/$1";
$route['rm/(:any)'] = "cron/add_reminder_record/$1";
$route['health_advice/(:any)'] = "web/health_advice/$1";
$route['health_advice_list/(:any)'] = "web/health_advice_list/$1";
$route['appdwn'] = "cron/app_download";
$route['f/(:any)'] = "cron/forward_link/$1";
$route['f/(:any)/(:any)'] = "cron/forward_link/$1/$2";

/*Patient routes*/
$route['patient'] = "patient/dashboard";
$route['patient/dashboard'] = "patient/dashboard";
$route['patient/logout'] = "patient/logout";
$route['patient/login/(:any)'] = "patient/login/login_view/$1";
$route['patient/login'] = "patient/login/login_view";
$route['patient/forgot'] = "patient/login/forgot_password";
$route['patient/resetpassword/(:any)'] = "patient/login/reset_password/$1";
$route['patient/reset'] = "patient/login/reset_success";
$route['patient/register/(:any)'] = "patient/login/register_view/$1";
$route['patient/register'] = "patient/login/register_view";
$route['pt/(:any)'] = "patient/login/rg/$1";
$route['patient/register/(:any)/(:any)'] = "patient/login/register_view/$1/$2";
$route['patient/patient_register'] = "patient/login/patient_register";
$route['patient/profile/update'] = "patient/profile/update";
$route['patient/patient_list'] = "patient/profile/patient_list";
$route['patient/change_patient'] = "patient/profile/change_patient";
$route['patient/report'] = "patient/documents/report";
$route['patient/report/(:any)'] = "patient/documents/report/$1";
$route['patient/add_report'] = "patient/documents/add_report";
$route['patient/delete_report/(:any)'] = "patient/documents/delete_report/$1";
$route['patient/view_report/(:any)'] = "patient/documents/view_report/$1";
$route['patient/appointment_list'] = "patient/appointment/appointment_list";
$route['patient/appointment_list/(:any)'] = "patient/appointment/appointment_list/$1";
$route['patient/prescription/(:any)'] = "patient/appointment/prescription/$1";
$route['patient/appointment_book'] = "patient/appointment/appointment_book";
$route['patient/appointment_book/(:any)'] = "patient/appointment/appointment_book/$1";
$route['patient/appointment_delete/(:any)'] = "patient/appointment/appointment_delete/$1";
$route['patient/book_now/(:any)'] = "patient/appointment/book_now/$1";
$route['patient/get_availability'] = "patient/appointment/get_availability";
$route['patient/videocall/(:any)'] = "patient/teleconsultant/videocall/$1";
$route['patient/get_video_conf_token'] = "patient/teleconsultant/get_video_conf_token";
$route['patient/end_video_conf_call'] = "patient/teleconsultant/end_video_conf_call";
$route['patient/telecall/(:any)'] = "patient/teleconsultant/tele_call_url/$1";
$route['patient/call_end'] = "patient/teleconsultant/call_end";
$route['patient/update_connection_id'] = "patient/teleconsultant/update_connection_id";
$route['patient/send_pushwoosh_notification'] = "patient/teleconsultant/send_pushwoosh_notification";
$route['patient/document/(:any)'] = "patient/document_view/view/$1";
$route['patient/vitals'] = "patient/vitals/vital_list";
$route['patient/add_vital'] = "patient/vitals/add_vital";
$route['patient/edit_vital/(:any)'] = "patient/vitals/edit_vital/$1";
$route['patient/delete_vital/(:any)'] = "patient/vitals/delete_vital/$1";
$route['patient/save_vital'] = "patient/vitals/save_vital";
$route['patient/vital_data'] = "patient/vitals/vital_data";
$route['patient/vital_graph_data'] = "patient/vitals/vital_graph_data";
$route['patient/utilities_list'] = "patient/utilities/list";
$route['patient/payment_popup'] = "patient/utilities/payment_popup";
$route['patient/create_payment_order'] = "patient/utilities/create_payment_credits_order";
$route['patient/payment_capture'] = "patient/utilities/payment_capture";
$route['patient/payment_success'] = "patient/utilities/payment_success";
$route['patient/download_invoice/(:any)'] = "patient/utilities/download_invoice/$1";
$route['patient/uas7diary'] = "patient/uas7diary/list";
$route['patient/add_uas7_para'] = "patient/uas7diary/add_uas7_para";
$route['patient/add_uas7_para/(:any)'] = "patient/uas7diary/add_uas7_para/$1";
$route['patient/save_uas7_para'] = "patient/uas7diary/save_uas7_para";
$route['patient/search_doctors'] = "patient/uas7diary/search_doctors";
$route['patient/uas7_para_list/(:any)'] = "patient/uas7diary/uas7_para_list/$1";
$route['patient/edit_uas7_para/(:any)'] = "patient/uas7diary/edit_uas7_para/$1";
$route['patient/uas7_para_graph_data'] = "patient/uas7diary/uas7_para_graph_data";
$route['patient/uas7_download'] = "patient/uas7diary/download";
$route['patient/share_uas7_report'] = "patient/uas7diary/share_uas7_report";
$route['patient/share_uas7_report_view'] = "patient/uas7diary/share_uas7_report_view";
$route['patient/save_as_report'] = "patient/uas7diary/save_as_report";
$route['patient/save_uas7_chart_image'] = "patient/uas7diary/save_uas7_chart_image";
$route['patient/add_date_list'] = "patient/uas7diary/add_date_list";
$route['patient/add_date_list/(:any)'] = "patient/uas7diary/add_date_list/$1";
$route['patient/add_member'] = "patient/family_member/add_member";
$route['patient/get_member'] = "patient/family_member/get_member";
$route['patient/remove_member_with_save_detail'] = "patient/family_member/remove_member_with_save_detail";
$route['patient/remove_member/(:any)'] = "patient/family_member/remove_member/$1";
$route['patient/add_issue'] = "patient/support/add_issue";
$route['patient/upgrade'] = "patient/payment/upgrade";
$route['patient/payment_order'] = "patient/payment/payment_order";
$route['patient/capture_payment'] = "patient/payment/capture_payment";
$route['patient/analytics_list'] = "patient/analytics/analytics_list";
$route['patient/add_analytics'] = "patient/analytics/add_analytics";
$route['patient/analytics_popup'] = "patient/analytics/analytics_popup";
$route['patient/get_analytics'] = "patient/analytics/get_analytics";
$route['patient/save_analytics'] = "patient/analytics/save_analytics";
$route['patient/analytics_data_list'] = "patient/analytics/analytics_data_list";
$route['patient/check_analytic_date'] = "patient/analytics/check_analytic_date";
$route['patient/get_uas_data'] = "patient/analytics/get_uas_data";
$route['patient/share_data'] = "patient/patient_share/share_data";
$route['patient/share_data_save'] = "patient/patient_share/share_data_save";
