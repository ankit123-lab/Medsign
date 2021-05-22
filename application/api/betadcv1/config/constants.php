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
    define('DB_USER', $GLOBALS['ENV_VARS']['DB_USER']);
    define('DB_PASS', $GLOBALS['ENV_VARS']['DB_PASS']);
    define('DB_DATABASE', $GLOBALS['ENV_VARS']['DB_DATABASE']); //medeasy_staging , medeasy_betav0_1
    define('DOMAIN_URL', $GLOBALS['ENV_VARS']['DOMAIN_URL']);
    define('BASE_URL', DOMAIN_URL);
    define("DOCROOT_PATH", $GLOBALS['ENV_VARS']['DOCROOT_PATH']);
    define("PHP_PATH", "php");
} else {
	die('Unauthorized access !');
}

define("API_VERSION", $GLOBALS['ENV_VARS']['API_VERSION']);
define('BASE_URL', DOMAIN_URL . API_VERSION);
define("CRON_PATH", DOCROOT_PATH . API_VERSION . 'index.php');
define("LOG_FILE_PATH", DOCROOT_PATH . "application/" . API_VERSION . 'logs/');
define("CACHE_PATH", DOCROOT_PATH . "application/" . API_VERSION . 'cache/');

//Database Table name Constants
define("TBL_PREFIX", "me_");
define("TBL_API", TBL_PREFIX . "apis");
define("TBL_API_LOGS", TBL_PREFIX . "api_logs");
define("TBL_ERRORS", TBL_PREFIX . "error_log");
define("TBL_USERS", TBL_PREFIX . "users");
define("TBL_USER_DEVICE_TOKENS", TBL_PREFIX . "user_device_tokens");
define("TBL_BAD_TOKENS", TBL_PREFIX . "bad_tokens");
define("TBL_SPECIALIZATION", TBL_PREFIX . "specialization");

define("TBL_ADVERTISEMENT", TBL_PREFIX . "advertisement");
define("TBL_ADMIN_SOURCE_MASTER", TBL_PREFIX . "admin_source_master");
define("TBL_ADVERTISEMENT_TYPE", TBL_PREFIX . "advertisement_type");
define("TBL_ADVERTISEMENT_ASSIGNMENT", TBL_PREFIX . "advertisement_assignment");

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
define("TBL_AUDIT_LOG", TBL_PREFIX . "audit_log");
define("TBL_RX_UPLOAD_REPORTS", TBL_PREFIX . "rx_upload_reports");
define("TBL_RX_UPLOAD_REPORTS_IMAGES", TBL_PREFIX . "rx_upload_reports_images");

//Path Constants
define("ASSETS_PATH", DOMAIN_URL . 'assets/');
define("ADMIN_ASSETS_PATH", ASSETS_PATH . 'admin');

//Upload Constants
define("UPLOAD_REL_PATH", '../../uploads');
define("UPLOAD_ABS_PATH", DOMAIN_URL . 'uploads/');

define('USER_FOLDER', 'users');
define('USER_ID_PROOF_FOLDER', 'id_proof');
define('USER_SIGN_FOLDER', 'signature');
define('CLINIC_FOLDER', 'clinics');
define('REPORT_FOLDER', 'report');
define('RX_FOLDER', 'rx');
define('CILINICAL_REPORT', 'clinical_report');
define('DOCTOR_SPECIALIZATION_FOLDER', 'doctor_specialization');
define('DOCTOR_EDUCATIONS_FOLDER', 'doctor_education');
define('DOCTOR_REGISTRATIONS_FOLDER', 'doctor_registration');
define('DOCTOR_AWARDS_FOLDER', 'doctor_award');
define('PRESCRIPTION_FOLDER', 'prescription');
define('ISSUE_FOLDER', 'issue');
define('PDF_FOLDER', 'pdf');

define("OTP_MESSAGE", "%s is the OTP for accessing your MedSign account valid for 30 min. Please do not share it with anyone.");
//define("OTP_MESSAGE", "Your OTP is %s");
// twiillio credentails.
define('TWILIO_ACC_SID',    'ACbd9b982d2009b922aefbe9a661d0916c'); // test
define('TWILIO_AUTH_TOKEN', 'd7f9d5505bf2bfe797fc53e33dd4c035'); // test
define('TWILIO_REG_NUMBER', '+16195667476');

//vibgyortel credentials
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
//Patient Email Constants
define('PATIENT_EMAIL_HOST', $GLOBALS['ENV_VARS']['PATIENT_EMAIL_HOST']);
define('PATIENT_EMAIL_USER', $GLOBALS['ENV_VARS']['PATIENT_EMAIL_USER']);
define('PATIENT_EMAIL_PASS', $GLOBALS['ENV_VARS']['PATIENT_EMAIL_PASS']);
define('PATIENT_EMAIL_PORT', $GLOBALS['ENV_VARS']['PATIENT_EMAIL_PORT']);
define('PATIENT_EMAIL_FROM', $GLOBALS['ENV_VARS']['PATIENT_EMAIL_FROM']);
define('EMAIL_CERTIFICATE', $GLOBALS['ENV_VARS']['EMAIL_CERTIFICATE']);

//Push Notification Constants
//Android
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
define('ALLOW_IOS_PUSH', true); // true or false
define('ALLOW_ANDROID_PUSH', true); // true or false
define("ALLOW_MULTI_LOGIN", FALSE); // true or false
define("DEFAULT_LANG", 'en'); // EN
define("DEFAULT_COUNTRY_CODE", '+91'); // +91
define("EMAIL_FORGOT_PASSWORD_EXPIRE_TOKEN_TIME", "+30 minutes");
define("OTP_EXPIRE_TOKEN_TIME", "+30 minutes");
define('SWIFT_MAILER_PATH', '../../swiftmailer/lib/swift_required.php');
define('MPDF_PATH', '../../mpdf/mpdf.php');
//STAGING SERVER S3 ACCESS
/* AWS USER S3 BUCKET CREATED FROM THE ADMINPANEL SERVER ACCOUNT 
   AWS USE NAME: staging_medsign
   Console login link: https://850841023467.signin.aws.amazon.com/console	
 */
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
    "11" => "22:00",
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

//define('PW_AUTH', 'voamnZySqBbdy3DHEOsJiz7E2MPPeEWtlPCo8JclnPwxP79X9Y5cXjMV5eftHbTokohiyvXV5ZUFiGZWmbx8');
define('PW_AUTH', 'EDeQjWmt6QWyaoGP2EL9FWpijLK97XXumaWZjFnNPUKd1CIODLJ4O835woaxzk0ZPVldzUx8kXq5bcf4gwuh');
define('PW_APPLICATION', 'EC0D3-F8E8B');
//define('PW_APPLICATION', 'FBD55-E30E1');
define("LOGIN_WRONG_ATTEMPT", "5");
define('MAX_FILE_UPLOAD_SIZE', '120000'); //120MB
define('FILE_UPLOAD_ALLOWED_EXTENSION', 'gif|jpg|jpeg|png|pdf');

/*Mail Template Constant @kanaiya Makwana */
define('PRESCRIPTION_TEMPLATE', '27');
define('IVESTIGATION_TEMPLATE', '26');
define('PATIENT_DETAIL_SHARE', '28');

define("TBL_INVOICE_DETAIL", TBL_PREFIX . "invoice_details");
define("DOCTOR", "Dr.");
define('DRUGS_EXCEL_FOLDER', 'drug_database_import_export');

define("TBL_TESTING_DRUGS", TBL_PREFIX . "script_testing_drug");
define("TBL_TESTING_DRUGS_FREQUENCY", TBL_PREFIX . "script_testing_drug_frequency");
define("TBL_TESTING_DRUGS_GENERIC", TBL_PREFIX . "script_testing_drug_generic");
define("TBL_TESTING_DRUGS_UNIT", TBL_PREFIX . "script_testing_drug_units");
define("TBL_DRUG_DATA_IMPORT", TBL_PREFIX . "me_drug_data_import");

define("TBL_SURVEY", TBL_PREFIX . "survey");
define("TBL_SURVEY_ASSIGNMENT", TBL_PREFIX . "survey_assignment");
define("TBL_SURVEY_OPTIONS", TBL_PREFIX . "survey_options");
define("TBL_SURVEY_QUESTIONS", TBL_PREFIX . "survey_questions");
define("TBL_SURVEY_TYPE", TBL_PREFIX . "survey_type");

define("TBL_USER_ROLE_ACCESS", TBL_PREFIX . "user_role_access");

define("TBL_PATIENT_FAMILY_MEMBER_MAPPING", TBL_PREFIX . "patient_family_member_mapping");

define ("COLOR_CODE", array('e6194B','3cb44b','ffe119','4363d8','f58231','911eb4','42d4f4','f032e6','bfef45','fabebe','469990','e6beff','9A6324','fffac8','800000','aaffc3','808000','ffd8b1','000075','a9a9a9','0562C9','AE5F82','A0765C','09A562','E480F5','E746D3','973D51','D5A1CB','EA9C54','E3FA6C','630E2B','D935CA','A0E853','62AB01','ED4185','BFC6A3','F12A59','0A8ED1','3AD860','31EFC2','2CA08D','D4E51F','0943C7','CBAF68','4587A1','18397C','7B8D59','A2DC6F','EA958F','19C5D6','E6B041','8162C4','E039C8','1BFD6A','384F10','CF6042','FADCB3','834CB7','48D712','2A0CE6','A81F43','B20D7A','978AB3','10C283','C7A2B5','E92A45','A367CF','6C9FA5','23E5B8','160AFD','B71043','B437E1','BC19D0','1DEB30','3705D9','0D1AC8','80623D','A1D642','659BF7','029ED1','07B821','60B58A','EF512A','A8F3C6','EFD517','D6BA72','A8072F','FEB3AC','1FC9AE','DF2730','6DECF0','A6BCF1','FB436C','1A35F6','92EF04','5F6870','DC83A4','F15078','9650BC','A1EF30','17C9D4','1D90FB','2468B3','D2963B','D03FC7','215984','69C8AB','4E20CF','847B19','3F27E5','08BAF3','BF9103','5E6A70','D80431','EB521D','93E517','C76240','90CBAD','43820C','8BFCA0','420A51','F6BE72','D6AF40','B5DFA8','4D5807','FCD97B','49573F','FD1CBA','57A3DC','D13EFB','0BCAE9','50C83D','47C9FA','2B1734','68E2C4','0D564A','06AE7C','DE954C','D6812F','182A6D','6415C3','DE956C','AB5D17','69A124','83B502','1854ED','AB1830','23E698','AE1F60','0AB816','2A9C13','3A4789','F1B70A','93CB7A','57D9C3','F14B6C','25D3E6','875A39','014AE6','20DEF4','3542F7','B41FC3','E61AB0','D3C512','7CE690','1C5E92','8ECD2F','1EBC56','4B9CE3','EC1B0F','3F258C','16A078','E572CF','CB2649','12058C','7D649B','6385D4','EF9DA8','E7F296','4AE56B','F9813A','CE9D3B','07DAFE','43FED5','541372','3048BF','AB46D0','279BA0','574E6A','AC4105','78BE2A','31C8F0','1C590A','16BEAC','FD45BA','AFD364','5BF043','18D02B','0F2B17','D97A6F','590ADE'));

/*Audit action slug define*/
define("AUDIT_SLUG_ARR", array('LOGIN_ACTION' => 'login', 'LOGOUT_ACTION' => 'logout', 'USER_PROFILE_ACTION' => 'profile', 'SURVEY_VIEW' => 'survey_view', 'SURVEY_CONTENT_VIEW' => 'survey_content_view', 'SURVEY_CONSENT_ACCEPT' => 'survey_consent_accept', 'IMPORT_DOCTOR_DATA' => 'import_doctor_data', 'PATIENT_DOB_UPDATE' => 'patient_dob_update', 'PATIENT_PAST_RX_REFER' => 'patient_past_rx_refer'));
define("IS_AUDIT_LOG_ENABLE",true);
define("PRESCRIPTION_PRINT_SETTING_TYPE",4);

/* File caching ON/OFF */
define("IS_FILE_CACHING_ACTIVE",1); //0 = deactive and 1 = active

define("DOCTOR_IMPORT_FILE_PATH", "../../uploads/doctor_data/");
define("TBL_DOCTOR_IMPORT_TABLE", TBL_PREFIX . "doctor_import");
define("TBL_IMPORT_FILE_MAPPING_TABLE", TBL_PREFIX . "import_file_mapping");
define("TBL_IMPORT_FILE_TYPE_MASTER", TBL_PREFIX . "import_file_type_master");
define("DATE_FORMAT", "d/m/Y");
define('DEFAULT_USER_PLAN_ID', 3);
define('DEFAULT_DOCTOR_SUB_PLAN_ID', 2);
define("DOCTOR_GLOBAL_SETTING", array('ASSISTANT_LIMIT' => 3, 'RECEPTIONIST_LIMIT' => 3));
define('PAYMENT_RECEIPT_FOLDER', 'payment_receipt');
define('EXCLUDE_ANALYTICS_TEST_ID', [27,28,29,30]);
define('KCO_DIAGNOSES', [734 => 'Hyperthyroidism', 747 => 'Hypothyroidism',1398 => 'Hyperthyroidism', 1411 => 'Hypothyroidism']);
define('CACHE_TTL', 0); // 3600 S = 1HR
define('PAST_PRESCRIPTION', 'past_prescription');
define('DB_CACHE_PATH', [
  'me_qualification' => CACHE_PATH. 'me_qualification/',
  'me_councils' => CACHE_PATH. 'me_councils/',
  'me_languages' => CACHE_PATH. 'me_languages/',
  'me_specialization' => CACHE_PATH. 'me_specialization/',
  'me_drugs' => CACHE_PATH. 'me_drugs/',
  'me_drug_units' => CACHE_PATH. 'me_drug_units/',
  'me_drug_generic' => CACHE_PATH. 'me_drug_generic/',
  'me_drug_frequency' => CACHE_PATH. 'me_drug_frequency/',
  'me_global_settings' => CACHE_PATH. 'me_global_settings/',
  'me_state' => CACHE_PATH. 'me_state/',
  'me_city' => CACHE_PATH. 'me_city/',
  'me_college' => CACHE_PATH. 'me_college/',
  'me_countries' => CACHE_PATH. 'me_countries/',
  'me_disease' => CACHE_PATH. 'me_disease/',
  'me_payment_types' => CACHE_PATH. 'me_payment_types/',
  'me_procedure' => CACHE_PATH. 'me_procedure/',
  'me_report_types' => CACHE_PATH. 'me_report_types/',
  'me_health_analytics_test' => CACHE_PATH. 'me_health_analytics_test/',
  'me_medical_conditions' => CACHE_PATH. 'me_medical_conditions/',
]);
define('MOBILE_APP_LINK', $GLOBALS['ENV_VARS']['MOBILE_APP_LINK']);
define('INVESTIGATION_SORT_ARR', [
  "1" => "'1','1,2','2,1','2'",
  "2" => "'2','2,1','1,2','1'",
  "3" => "'3','3,2','2,3','2'"
]);
define('UAS7_FOLDER', 'uas7');
define('IS_STOP_SMS', $GLOBALS['ENV_VARS']['IS_STOP_SMS']);
define('IS_STOP_WHATSAPP_SMS', $GLOBALS['ENV_VARS']['IS_STOP_WHATSAPP_SMS']);
define('CLINIC_COLOR_CODE', ['08BAF3','90CBAD','8BFCA0','FD1CBA','F14B6C','D3C512','CB2649','F9813A','279BA0','16BEAC','10C283','E6B041','A2DC6F','CBAF68','3AD860','F12A59','A0E853','EA9C54','a9a9a9','469990']);
define("MEDSIGN_WEB_CARE_URL", $GLOBALS['ENV_VARS']['MEDSIGN_WEB_CARE_URL']);
define("ENABLE_API_LOG", $GLOBALS['ENV_VARS']['ENABLE_API_LOG']);
define('RX_PRINT_LOGO', 'rx_print_logo');
define('RX_PRINT_WATERMARK', 'rx_print_watermark');
define("FIREBASE_API_ACCESS_KEY", $GLOBALS['ENV_VARS']['FIREBASE_API_ACCESS_KEY']);
define("IS_S3_UPLOAD", $GLOBALS['ENV_VARS']['IS_S3_UPLOAD']);
define("IS_SERVER_UPLOAD", $GLOBALS['ENV_VARS']['IS_SERVER_UPLOAD']);