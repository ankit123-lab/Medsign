<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/*
  |--------------------------------------------------------------------------
  | Display Debug backtrace
  |--------------------------------------------------------------------------
  |
  | If set to TRUE, a backtrace will be displayed along with php errors. If
  | error_reporting is disabled, the backtrace will not display, regardless
  | of this setting
  |
 */
defined('SHOW_DEBUG_BACKTRACE') OR define('SHOW_DEBUG_BACKTRACE', TRUE);

/*
  |--------------------------------------------------------------------------
  | File and Directory Modes
  |--------------------------------------------------------------------------
  |
  | These prefs are used when checking and setting modes when working
  | with the file system.  The defaults are fine on servers with proper
  | security, but you may wish (or even need) to change the values in
  | certain environments (Apache running a separate process for each
  | user, PHP under CGI with Apache suEXEC, etc.).  Octal values should
  | always be used to set the mode correctly.
  |
 */
defined('FILE_READ_MODE') OR define('FILE_READ_MODE', 0644);
defined('FILE_WRITE_MODE') OR define('FILE_WRITE_MODE', 0666);
defined('DIR_READ_MODE') OR define('DIR_READ_MODE', 0755);
defined('DIR_WRITE_MODE') OR define('DIR_WRITE_MODE', 0755);

/*
  |--------------------------------------------------------------------------
  | File Stream Modes
  |--------------------------------------------------------------------------
  |
  | These modes are used when working with fopen()/popen()
  |
 */
defined('FOPEN_READ') OR define('FOPEN_READ', 'rb');
defined('FOPEN_READ_WRITE') OR define('FOPEN_READ_WRITE', 'r+b');
defined('FOPEN_WRITE_CREATE_DESTRUCTIVE') OR define('FOPEN_WRITE_CREATE_DESTRUCTIVE', 'wb'); // truncates existing file data, use with care
defined('FOPEN_READ_WRITE_CREATE_DESTRUCTIVE') OR define('FOPEN_READ_WRITE_CREATE_DESTRUCTIVE', 'w+b'); // truncates existing file data, use with care
defined('FOPEN_WRITE_CREATE') OR define('FOPEN_WRITE_CREATE', 'ab');
defined('FOPEN_READ_WRITE_CREATE') OR define('FOPEN_READ_WRITE_CREATE', 'a+b');
defined('FOPEN_WRITE_CREATE_STRICT') OR define('FOPEN_WRITE_CREATE_STRICT', 'xb');
defined('FOPEN_READ_WRITE_CREATE_STRICT') OR define('FOPEN_READ_WRITE_CREATE_STRICT', 'x+b');

/*
  |--------------------------------------------------------------------------
  | Exit Status Codes
  |--------------------------------------------------------------------------
  |
  | Used to indicate the conditions under which the script is exit()ing.
  | While there is no universal standard for error codes, there are some
  | broad conventions.  Three such conventions are mentioned below, for
  | those who wish to make use of them.  The CodeIgniter defaults were
  | chosen for the least overlap with these conventions, while still
  | leaving room for others to be defined in future versions and user
  | applications.
  |
  | The three main conventions used for determining exit status codes
  | are as follows:
  |
  |    Standard C/C++ Library (stdlibc):
  |       http://www.gnu.org/software/libc/manual/html_node/Exit-Status.html
  |       (This link also contains other GNU-specific conventions)
  |    BSD sysexits.h:
  |       http://www.gsp.com/cgi-bin/man.cgi?section=3&topic=sysexits
  |    Bash scripting:
  |       http://tldp.org/LDP/abs/html/exitcodes.html
  |
 */
defined('EXIT_SUCCESS') OR define('EXIT_SUCCESS', 0); // no errors
defined('EXIT_ERROR') OR define('EXIT_ERROR', 1); // generic error
defined('EXIT_CONFIG') OR define('EXIT_CONFIG', 3); // configuration error
defined('EXIT_UNKNOWN_FILE') OR define('EXIT_UNKNOWN_FILE', 4); // file not found
defined('EXIT_UNKNOWN_CLASS') OR define('EXIT_UNKNOWN_CLASS', 5); // unknown class
defined('EXIT_UNKNOWN_METHOD') OR define('EXIT_UNKNOWN_METHOD', 6); // unknown class member
defined('EXIT_USER_INPUT') OR define('EXIT_USER_INPUT', 7); // invalid user input
defined('EXIT_DATABASE') OR define('EXIT_DATABASE', 8); // database error
defined('EXIT__AUTO_MIN') OR define('EXIT__AUTO_MIN', 9); // lowest automatically-assigned error code
defined('EXIT__AUTO_MAX') OR define('EXIT__AUTO_MAX', 125); // highest automatically-assigned error code

define('DOCROOT', realpath(dirname(__FILE__) . '/../') . "/");

/*
  User defined Constants
 */

if (ENVIRONMENT == 'production') {
    define('DB_SERVER', $GLOBALS['ENV_VARS']['DB_SERVER']);
    define('DB_USER', 	$GLOBALS['ENV_VARS']['DB_USER']);
    define('DB_PASS', 	$GLOBALS['ENV_VARS']['DB_PASS']);
    define('DB_DATABASE', $GLOBALS['ENV_VARS']['DB_DATABASE']); //medeasy_staging
    define('DOMAIN_URL', $GLOBALS['ENV_VARS']['DOMAIN_URL']);
    define('BASE_URL', DOMAIN_URL);
    define("DOCROOT_PATH", $GLOBALS['ENV_VARS']['DOCROOT_PATH']);
    define("PHP_PATH", "php");
} else {
    die('Unauthorized access !');
}

define("API_VERSION", $GLOBALS['ENV_VARS']['API_VERSION_APP']);
define('BASE_URL', DOMAIN_URL . API_VERSION);
define("CRON_PATH", DOCROOT_PATH . API_VERSION . 'index.php');
define("LOG_FILE_PATH", DOCROOT_PATH . "application/" . API_VERSION . 'logs/');

//Database Table name Constants
define("TBL_PREFIX", "me_");
define("TBL_API", TBL_PREFIX . "apis");
define("TBL_API_LOGS", TBL_PREFIX . "api_logs");
define("TBL_ERRORS", TBL_PREFIX . "error_log");
define("TBL_USERS", TBL_PREFIX . "users");
define("TBL_USER_DEVICE_TOKENS", TBL_PREFIX . "user_device_tokens");
define("TBL_BAD_TOKENS", TBL_PREFIX . "bad_tokens");
define("TBL_SPECIALIZATION", TBL_PREFIX . "specialization");

define("TBL_LANGUAGE", TBL_PREFIX . "language");
define("TBL_LANGUAGES", TBL_PREFIX . "languages");

define("TBL_COUNTRIES", TBL_PREFIX . "countries");
define("TBL_STATES", TBL_PREFIX . "state");
define("TBL_CITIES", TBL_PREFIX . "city");
define("TBL_USER_TYPE", TBL_PREFIX . "user_type");
define('TBL_USER_AUTH', TBL_PREFIX . "auth");

define("TBL_ADDRESS", TBL_PREFIX . "address");
define("TBL_USER_DETAILS", TBL_PREFIX . "user_details");
define("TBL_LABORATORIES", TBL_PREFIX . "laboratories");
define("TBL_REPORT_TYPES", TBL_PREFIX . "report_types");
define("TBL_DRUGS", TBL_PREFIX . "drugs");
define("TBL_COLLEGE", TBL_PREFIX . "college");
define("TBL_QUALIFICATION", TBL_PREFIX . "qualification");
define("TBL_COUNCILS", TBL_PREFIX . "councils");
define("TBL_DRUG_FREQUENCY", TBL_PREFIX . "drug_frequency");
define("TBL_DRUG_GENERIC", TBL_PREFIX . "drug_generic");

//Reminder tables
define("TBL_REMINDERS", TBL_PREFIX . "reminders");
define("TBL_REMINDER_RECORDS", TBL_PREFIX . "reminder_records");

//medical condition 
define("TBL_MEDICAL_CONDITIONS", TBL_PREFIX . "medical_conditions");
define("TBL_FAMILY_MEDICAL_HISTORY", TBL_PREFIX . "family_medical_history");

//doctor table
define("TBL_DOCTOR_DETAILS", TBL_PREFIX . "doctor_details");
define("TBL_DOCTOR_SPECIALIZATIONS", TBL_PREFIX . "doctor_specializations");
define("TBL_DOCTOR_EDUCATIONS", TBL_PREFIX . "doctor_qualifications");
define("TBL_DOCTOR_REGISTRATIONS", TBL_PREFIX . "doctor_registration");
define("TBL_DOCTOR_CLINIC_MAPPING", TBL_PREFIX . "doctor_clinic_mapping");
define("TBL_CLINICS", TBL_PREFIX . "clinic");
define("TBL_CLINIC_IMAGES", TBL_PREFIX . "clinic_photos");
define("TBL_SPECIALISATIONS", TBL_PREFIX . "specialization");
define("TBL_APPOINTMENTS", TBL_PREFIX . "appointments");
define("TBL_DOCTOR_CALENDER_BLOCK", TBL_PREFIX . "calender_block");
define("TBL_PRESCRIPTION_REPORTS", TBL_PREFIX . "prescription_reports");
define("TBL_VITAL_REPORTS", TBL_PREFIX . "vital_reports");
define("TBL_CLINICAL_REPORTS", TBL_PREFIX . "clinical_notes_reports");
define("TBL_PROCEDURE_REPORTS", TBL_PREFIX . "procedure_reports");
define("TBL_NEWS", TBL_PREFIX . "news");
define("TBL_CONTACT_US", TBL_PREFIX . "contact_us");
define("TBL_DOCTOR_AWARDS", TBL_PREFIX . "doctor_awards");
define("TBL_DISEASES", TBL_PREFIX . "disease");
define("TBL_PATIENT_DISEASES", TBL_PREFIX . "patient_diseases");
define("TBL_DOCTOR_AVAILABILITY", TBL_PREFIX . "doctor_availability");
define("TBL_CLINIC_AVAILABILITY", TBL_PREFIX . "clinic_availability");
define("TBL_COMMUNICATION_SETTING", TBL_PREFIX . "communication_settings");
define("TBL_TAXES", TBL_PREFIX . "taxes");
define("TBL_PAYMENT_TYPE", TBL_PREFIX . "payment_types");
define("TBL_PAYMENT_MODE", TBL_PREFIX . "payment_mode");
define("TBL_PRICING_CATALOG", TBL_PREFIX . "pricing_catalog");
define("TBL_CLINICAL_NOTES", TBL_PREFIX . "clinical_notes_catalog");
define("TBL_DRUG_UNIT", TBL_PREFIX . "drug_units");
define("TBL_TEMPLATE", TBL_PREFIX . "template");
define("TBL_TEMPLATE_DRUG", TBL_PREFIX . "template_drug_binding");
define("TBL_LAB_TEST", TBL_PREFIX . "lab_tests");
define("TBL_PATIENT_GROUP", TBL_PREFIX . "patient_groups");
define("TBL_SETTING", TBL_PREFIX . "settings");
define("TBL_FILE_REPORTS", TBL_PREFIX . "files_reports");
define("TBL_FILE_REPORTS_IMAGES", TBL_PREFIX . "files_reports_images");
define("TBL_CLINICAL_NOTES_REPORT", TBL_PREFIX . "clinical_notes_reports");
define("TBL_PRESCRIPTION", TBL_PREFIX . "prescription_reports");
define("TBL_LAB_REPORTS", TBL_PREFIX . "lab_reports");
define("TBL_NOTIFICATION", TBL_PREFIX . "notification_list");
define("TBL_CLINICAL_NOTES_REPORT_IMAGE", TBL_PREFIX . "clinic_notes_reports_images");
define("TBL_HEALTH_ANALYTICS", TBL_PREFIX . "health_analytics_test");
define("TBL_HEALTH_ANALYTICS_REPORT", TBL_PREFIX . "health_analytics_report");
define("TBL_PATIENT_ANALYTICS", TBL_PREFIX . "patient_analytics");
define("TBL_REFER", TBL_PREFIX . "refer");
define("TBL_FAV_DOCTORS", TBL_PREFIX . "fav_doctors");
define("TBL_PRESCRIPTION_FOLLOUP", TBL_PREFIX . "prescription_follow_up");
define("TBL_VIDEO", TBL_PREFIX . "video");
define("TBL_USER_ISSUE", TBL_PREFIX . "user_issue");
define("TBL_PATIENT_PRESCRIPTION", TBL_PREFIX . "patient_prescription");
define("TBL_PATIENT_PRESCRIPTION_IMAGES", TBL_PREFIX . "patient_prescription_photo");
define("TBL_PROCEDURE", TBL_PREFIX . "procedure");
define("TBL_USER_ISSUE_ATTACHMENT", TBL_PREFIX . 'user_issue_attachment');
define("TBL_STATIC_PAGE", TBL_PREFIX . "static_pages");
define("TBL_BILLING", TBL_PREFIX . "billing");
define("TBL_BILLING_DETAILS", TBL_PREFIX . "billing_details");
define("TBL_EMAIL_TEMPLATE", TBL_PREFIX . "email_template");
define("TBL_MENU", TBL_PREFIX . "menu");
define("TBL_USER_ROLE", TBL_PREFIX . "user_role");
define("TBL_ACTIVITY_LEVES", TBL_PREFIX . "activity_levels");
define("TBL_ALCOHOL", TBL_PREFIX . "alcohol");
define("TBL_APPOINTMENT_TYPE", TBL_PREFIX . "appointment_type");
define("TBL_FOOD_PREFERENCE", TBL_PREFIX . "food_preference");
define("TBL_SMOKING_HABBIT", TBL_PREFIX . "smoking_habbit");
define("TBL_OCCUPATIONS", TBL_PREFIX . "occupations");
define("TBL_GLOBAL_SETTINGS", TBL_PREFIX . "global_settings");
define("TBL_UNDER_MAINTENANCE", TBL_PREFIX . "under_maintenance");
define("TBL_APPLICATION_VERSION", TBL_PREFIX . "application_version");
define("TBL_USER_TEMP", TBL_PREFIX . "temp_user");
define("TBL_PATIENT_REPORT_TRACK", TBL_PREFIX . "patient_report_track");
define("TBL_PATIENT_INVOICE", TBL_PREFIX . "patient_invoice");
define("TBL_PATIENT_INVOICE_IMAGES", TBL_PREFIX . "patient_invoice_photo");
define("TBL_AUDIT_LOG", TBL_PREFIX . "audit_log");
define("TBL_FAMILY_MEMBER_LOG", TBL_PREFIX . "family_member_log");
define("TBL_PATIENT_FAMILY_MEMBER_MAPPING", TBL_PREFIX . "patient_family_member_mapping");

//Path Constants
define("ASSETS_PATH", DOMAIN_URL . 'assets/');
define("ADMIN_ASSETS_PATH", ASSETS_PATH . 'admin');

//Upload Constants
define("UPLOAD_REL_PATH", '../../uploads');
define("UPLOAD_ABS_PATH", DOMAIN_URL . 'uploads/');

define('USER_FOLDER', 'users');
define('CLINIC_FOLDER', 'clinics');
define('REPORT_FOLDER', 'report');
define('CILINICAL_REPORT', 'clinical_report');
define('DOCTOR_SPECIALIZATION_FOLDER', 'doctor_specialization');
define('DOCTOR_EDUCATIONS_FOLDER', 'doctor_education');
define('DOCTOR_REGISTRATIONS_FOLDER', 'doctor_registration');
define('DOCTOR_AWARDS_FOLDER', 'doctor_award');
define('PRESCRIPTION_FOLDER', 'prescription');
define('INVOICE_FOLDER', 'invoice');
define('ISSUE_FOLDER', 'issue');
define('PDF_FOLDER', 'pdf');

define("OTP_MESSAGE", "%s is the OTP for accessing your MedSign account valid for 30 min. Please do not share it with anyone.");

// twiillio credentails.
define('TWILIO_ACC_SID', 'ACbd9b982d2009b922aefbe9a661d0916c'); // test
define('TWILIO_AUTH_TOKEN', 'd7f9d5505bf2bfe797fc53e33dd4c035'); // test
define('TWILIO_REG_NUMBER', '+16195667476');

//vibgyortel credentials
//define("VIBGYORTEL_KEY", '7bf0558c071bbb82');
define("VIBGYORTEL_KEY", $GLOBALS['ENV_VARS']['VIBGYORTEL_KEY']); //SMS API KEY
define("SENDERID", $GLOBALS['ENV_VARS']['SENDERID']);

//textlocal credentials
define("TEXTLOCAL_USERNAME","medeasysagar@gmail.com");
define("TEXTLOCAL_HASH_KEY","2d34071c98a4ad214f5eab36b05f99a4ccb6c53d868bc0345d14aaf0fa038536");
define("TEXTLOCAL_TEST","0");
define("TEXTLOCAL_SENDERID", $GLOBALS['ENV_VARS']['SENDERID']); // //TXTLCL

//Email Constants
define('EMAIL_HOST', $GLOBALS['ENV_VARS']['EMAIL_HOST']);
define('EMAIL_USER', $GLOBALS['ENV_VARS']['EMAIL_USER']);
define('EMAIL_PASS', $GLOBALS['ENV_VARS']['EMAIL_PASS']);
define('EMAIL_PORT', $GLOBALS['ENV_VARS']['EMAIL_PORT']);
define('EMAIL_FROM', $GLOBALS['ENV_VARS']['EMAIL_FROM']);
define('EMAIL_CERTIFICATE', $GLOBALS['ENV_VARS']['EMAIL_CERTIFICATE']);

//Push Notification Constants
//Android
//define('ANDROID_FCM_KEY', 'AIzaSyBG27HJt1iNLCzY1Y7-rJOBRw_R3BtTPDk');
define('ANDROID_FCM_KEY', $GLOBALS['ENV_VARS']['ANDROID_FCM_KEY']);

//IOS
define('PUSH_ENVIRONMENT', 'false');
if (PUSH_ENVIRONMENT == 'true') {
    define('IOS_PEM_PATH', '../../pem/apns_prod.pem');
    define('APP_IS_LIVE', 'true');
} else {
    define('IOS_PEM_PATH', '../../pem/apns-dev.pem');
    define('APP_IS_LIVE', 'false');
}

//Other contains (AWS S3 Bucket, SMS Gateway, Google MAP KEY, etc.)
define('APP_NAME', 'MedSign');
define('ALLOW_IOS_PUSH', true); 	// true or false
define('ALLOW_ANDROID_PUSH', true); // true or false
define("ALLOW_MULTI_LOGIN", FALSE); // true or false
define("DEFAULT_LANG", 'en'); 		// EN
define("DEFAULT_COUNTRY_CODE", '+91'); // +91
define("EMAIL_FORGOT_PASSWORD_EXPIRE_TOKEN_TIME", "+30 minutes");
define("OTP_EXPIRE_TOKEN_TIME", "+30 minutes");
define('SWIFT_MAILER_PATH', '../../swiftmailer/lib/swift_required.php');
define('MPDF_PATH', '../../mpdf/mpdf.php');

define('BUCKET_NAME_DOWNLOAD', $GLOBALS['ENV_VARS']['BUCKET_NAME_DOWNLOAD']);
define('BUCKET_ACCESS_KEY',    $GLOBALS['ENV_VARS']['BUCKET_ACCESS_KEY']);
define('BUCKET_ACCESS_SECRET', $GLOBALS['ENV_VARS']['BUCKET_ACCESS_SECRET']);
define('BUCKET_HELPER_PATH', '../../functions_amazon.php');
define("IMAGE_MANIPULATION_URL", "https://s3.ap-south-1.amazonaws.com/" . BUCKET_NAME_DOWNLOAD . "/");
define("ADMIN_EMAIL", $GLOBALS['ENV_VARS']['ADMIN_EMAIL']);

define("FREQUENCY_TIMING", json_encode(array(
    "1" => "08:00",
    "2" => "14:00",
    "3" => "20:00",
    "4" => "08:00,20:00",
    "5" => "08:00,14:00,20:00",
    "6" => "08:00,12:00,16:00,20:00",
)));

define("RECORDS_LIMIT", '15');
define("VERIFY_OTP_LIMIT", "5");
define("RESEND_OTP_LIMIT", "5");
define("RESEND_OTP_NEXT_TIME", "60"); //in second
//for all module 1=Add 2=Edit 3=View 4=Delete
//for Appointment and invoice 5=able to share records or the invoice
define("ROLE_MODULE", json_encode(array(
    "1" => "Patient",
    "2" => "Appointment",
    "3" => "Patient_Appointment_Vital",
    "4" => "Patient_Appointment_Clinicalnotes",
    "5" => "Patient_Appointment_RX",
    "6" => "Patient_Appointment_Investigation",
    "7" => "Patient_Appointment_Procedure",
    "8" => "Patient_Appointment_Reports",
    "9" => "Patient_Appointment_Analytics",
    "10" => "Invoice",
    "11" => "Setting_Clinic_Details",
    "12" => "Setting_Clinic_Staff",
    "13" => "Setting_Calendar",
    "14" => "Setting_Alerts",
    "15" => "Setting_Fee_Structure",
    "16" => "Setting_Billing",
    "17" => "Setting_Data_Security",
    "18" => "Setting_Clincal_Notes_Catalog",
    "19" => "Setting_Brand_Catalog",
    "20" => "Setting_Diseases_Template",
    "21" => "Setting_Share_Record",
    "22" => "Setting_Favourite_Doctor",
    "23" => "My_Account",
    "24" => "subscription",
    "25" => "Patient_kco",
    "26" => "patient_right_panel",
    "27" => "patient_pdf",
    "28" => "patient_share",
    "29" => "Patient_FU_template",
    "30" => "Patient_DS_template",
    "31" => "Patient_Save_template",
    "32" => "Patient_Health_Anylytics",
    "33" => "Patient_Health_Advice",
    "34" => "Patient_Refer",
    "35" => "Doctor_Availibility"
)));

//define('PW_AUTH', 'KUpECvLBTQu7Ro3tIAb26tMeAOaSCI5pOBJtCA36CWEnHgwI9jzwdoYjB9Pfl8ZAQESVBNtA6c0nbQ7000r2');
define('PW_AUTH', 'EDeQjWmt6QWyaoGP2EL9FWpijLK97XXumaWZjFnNPUKd1CIODLJ4O835woaxzk0ZPVldzUx8kXq5bcf4gwuh');
//define('PW_APPLICATION', 'EC0D3-F8E8B');
define('PW_APPLICATION', 'FBD55-E30E1');
define("LOGIN_WRONG_ATTEMPT", "5");
define("DOCTOR", "Dr.");
define('PRESCRIPTION_PDF_FOLDER', 'prescription_and_invoice_pdf/prescription');
define('INVOICE_PDF_FOLDER', 'prescription_and_invoice_pdf/invoice');

define('PRIMARY_DOCTOR_COLOR_CODE', '#e6fcf9');
/*Audit action slug define*/
define('AUDIT_SLUG_ARR', array('LOGIN_ACTION' => 'login', 'LOGOUT_ACTION' => 'logout', 'USER_PROFILE_ACTION' => 'profile'));
define("DATE_FORMAT", "d/m/Y");
define('IS_FILE_CACHING_ACTIVE', 1);
define('DEFAULT_USER_PLAN_ID', 3);
define('MOBILE_APP_LINK', $GLOBALS['ENV_VARS']['MOBILE_APP_LINK']);
define("MEDSIGN_WEB_CARE_URL", $GLOBALS['ENV_VARS']['MEDSIGN_WEB_CARE_URL']);