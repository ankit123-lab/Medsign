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

/*User defined Constants*/
if (ENVIRONMENT == 'production') {
    define('DB_SERVER', $GLOBALS['ENV_VARS']['DB_SERVER']);
    define('DB_USER', $GLOBALS['ENV_VARS']['DB_USER']);
    define('DB_PASS', $GLOBALS['ENV_VARS']['DB_PASS']);
	  define('DB_DATABASE', $GLOBALS['ENV_VARS']['DB_DATABASE']); //medeasy_staging , medeasy_betav0_1
    define('DOMAIN_URL', $GLOBALS['ENV_VARS']['DOMAIN_URL']);
    define('BASE_URL', DOMAIN_URL);
    define("DOCROOT_PATH", $GLOBALS['ENV_VARS']['DOCROOT_PATH']);
} else {
    die('Unauthorized access !');
}

define("LOG_FILE_PATH", DOCROOT_PATH . "application/logs/");

//NEW DATABASE CHAHES
define("TBL_PREFIX", "me_");
define("TBL_USERS", TBL_PREFIX . "users");
define("TBL_USER_AUTH", TBL_PREFIX . "auth");
define("TBL_ERRORS", TBL_PREFIX . "error_log");
define("TBL_USER_DEVICE_TOKENS", TBL_PREFIX . "user_device_tokens");
define("TBL_COUNTRIES", TBL_PREFIX . "countries");
define("TBL_STATES", TBL_PREFIX . "state");
define("TBL_CITIES", TBL_PREFIX . "city");
define("TBL_CLINICS", TBL_PREFIX . "clinic");
define("TBL_DOCTOR_EDUCATIONS", TBL_PREFIX . "doctor_qualifications");
define("TBL_DOCTOR_SPECIALIZATIONS", TBL_PREFIX . "doctor_specializations");
define("TBL_DOCTOR_REGISTRATIONS", TBL_PREFIX . "doctor_registration");
define("TBL_REMINDERS", TBL_PREFIX . "reminders");
define("TBL_DOCTOR_DETAILS", TBL_PREFIX . "doctor_details");
define("TBL_USER_DETAILS", TBL_PREFIX . "user_details");
define("TBL_PRESCRIPTION_FOLLOUP", TBL_PREFIX . "prescription_follow_up");

define("TBL_APPOINTMENTS", TBL_PREFIX . "appointments");
define("TBL_VITAL_REPORTS", TBL_PREFIX . "vital_reports");
define("TBL_CLINICAL_REPORTS", TBL_PREFIX . "clinical_notes_reports");
define("TBL_PRESCRIPTION_REPORTS", TBL_PREFIX . "prescription_reports");
define("TBL_DRUG_FREQUENCY", TBL_PREFIX . "drug_frequency");
define("TBL_LAB_REPORTS", TBL_PREFIX . "lab_reports");
define("TBL_PROCEDURE_REPORTS", TBL_PREFIX . "procedure_reports");
define("TBL_BILLING", TBL_PREFIX . "billing");
define("TBL_BILLING_DETAILS", TBL_PREFIX . "billing_details");
define("TBL_PAYMENT_MODE", TBL_PREFIX . "payment_mode");
define("TBL_DRUG_UNIT", TBL_PREFIX . "drug_units");
define("TBL_DRUG_GENERIC", TBL_PREFIX . "drug_generic");
define("TBL_ADDRESS", TBL_PREFIX . "address");

define("TBL_STATIC_PAGE", TBL_PREFIX . "static_pages");

//Path Constants
define("ASSETS_PATH", DOMAIN_URL . 'assets/');
define("ADMIN_ASSETS_PATH", ASSETS_PATH . 'admin/');

define("DASHBOARD_PATH", BASE_URL);
define("USERS_PATH", BASE_URL . 'users');
define("COMMON_PATH", BASE_URL . 'common');

//Folder Constant
define("UPLOAD_FILE_FOLDER", 'uploads');
define("DEFAULT_IMAGE_FOLDER", 'images');
define("UPLOAD_ADMIN_FOLDER", 'admin');
define("UPLOAD_USERS_FOLDER", 'users');

//path constant
define("UPLOAD_REL_PATH", UPLOAD_FILE_FOLDER);
define("UPLOAD_FILE_FULL_PATH", DOCROOT_PATH . UPLOAD_FILE_FOLDER . '/');
define("UPLOAD_ABS_PATH", DOMAIN_URL . 'uploads/');

//Other contains (AWS S3 Bucket, SMS Gateway, Google MAP KEY, etc.)
define('APP_NAME', 'MedSign');
define('APP_SHORT_NAME', 'ME');
define('ALLOW_IOS_PUSH', true); // true or false
define('ALLOW_ANDROID_PUSH', true); // true or false
//define('DATE_FORMAT', 'd-m-Y h:i:s');
define('DATE_FORMAT', 'Y-m-d H:i:s');
define('DATE_FORMAT_JS', 'dd-MM-yyyy hh:mm:ss TT');
define("ALLOW_MULTI_LOGIN", TRUE); // true or false

define('SWIFT_MAILER_PATH', DOCROOT_PATH . 'swiftmailer/lib/swift_required.php');
define('ANDROID_FCM_KEY', $GLOBALS['ENV_VARS']['ANDROID_FCM_KEY']);

define("MPDF_PATH", "mpdf/mpdf.php");
define("DEFAULT_LANG", 'en'); // EN

//define('PW_AUTH', 'KUpECvLBTQu7Ro3tIAb26tMeAOaSCI5pOBJtCA36CWEnHgwI9jzwdoYjB9Pfl8ZAQESVBNtA6c0nbQ7000r2');
define('PW_AUTH', 'EDeQjWmt6QWyaoGP2EL9FWpijLK97XXumaWZjFnNPUKd1CIODLJ4O835woaxzk0ZPVldzUx8kXq5bcf4gwuh');

define('PW_APPLICATION', 'EC0D3-F8E8B');
// define('PW_APPLICATION', 'FBD55-E30E1');

define('API_LOG_BKP_DATE', 5);
define('API_LOG_BKP', 'apilog');
define("TBL_TEST_LOGS", TBL_PREFIX . "testdata");
define("TBL_API_LOGS", TBL_PREFIX . "api_logs");

define("TBL_GET_IN_TOUCH", TBL_PREFIX . "get_in_touch");
define("TBL_EMAIL_TEMPLATE", TBL_PREFIX . "email_template");
define("TBL_GLOBAL_SETTINGS", TBL_PREFIX . "global_settings");

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

define('CILINICAL_REPORT', 'clinical_report');
define('DRUGS_EXCEL_FOLDER', 'drug_database_import_export');
define("DOCTOR", "Dr.");

/* goole map key required for the frontend website */
define("GOOGLEMAPAPIKEY", $GLOBALS['ENV_VARS']['GOOGLEMAPAPIKEY']); //DEVELOPMENT KEY
define("PAYMENT_NOTIFICATION_DAYS",[30,15]);
define("PATIENT_PRESCRIPTION_SHARE",'patient_prescription_share');
define("PATIENT_INVOICE_SHARE",'patient_invoice_share');
define('BUCKET_NAME_DOWNLOAD', $GLOBALS['ENV_VARS']['BUCKET_NAME_DOWNLOAD']);
define('BUCKET_ACCESS_KEY',    $GLOBALS['ENV_VARS']['BUCKET_ACCESS_KEY']);
define('BUCKET_ACCESS_SECRET', $GLOBALS['ENV_VARS']['BUCKET_ACCESS_SECRET']);
define('BUCKET_HELPER_PATH', 'functions_amazon.php');
define("IMAGE_MANIPULATION_URL", "https://s3.ap-south-1.amazonaws.com/" . BUCKET_NAME_DOWNLOAD . "/");

define("DEFAULT_COUNTRY_CODE", '+91'); // +91
//vibgyortel credentials
define("VIBGYORTEL_KEY", $GLOBALS['ENV_VARS']['VIBGYORTEL_KEY']); //SMS API KEY
define("SENDERID", $GLOBALS['ENV_VARS']['SENDERID']);

//textlocal credentials
define("TEXTLOCAL_USERNAME","medeasysagar@gmail.com");
define("TEXTLOCAL_HASH_KEY","2d34071c98a4ad214f5eab36b05f99a4ccb6c53d868bc0345d14aaf0fa038536");
define("TEXTLOCAL_TEST","0");
define("TEXTLOCAL_SENDERID", $GLOBALS['ENV_VARS']['SENDERID']); // //TXTLCL

define('PDF_FOLDER', 'pdf');
define('SOCIAL_MEDIA_SHARING_BUTTON_LINKS', [
  'FACEBOOK'=>'https://www.facebook.com/sharer.php?u={{URL}}&t={{TEXT}}',
  'TWITTER'=>'http://twitter.com/intent/tweet?source=sharethiscom&text={{TEXT}}&url={{URL}}',
  'WHATSAPP'=>'https://wa.me/?text={{TEXT}}',
  'INSTAGRAM'=>'https://www.instagram.com/?url={{URL}}'
]);
define('DEFAULT_USER_PLAN_ID', 3);
define("API_VERSION", $GLOBALS['ENV_VARS']['API_VERSION']);
define("API_VERSION_APP", $GLOBALS['ENV_VARS']['API_VERSION_APP']);
define("CRON_PATH", DOCROOT_PATH . API_VERSION . 'index.php');
define("OTP_EXPIRE_TOKEN_TIME", "+30 minutes");
define("PHP_PATH", "php");
define('REPORT_FOLDER', 'report');
define('MOBILE_APP_LINK', $GLOBALS['ENV_VARS']['MOBILE_APP_LINK']);
define('WEB_VERSION', $GLOBALS['ENV_VARS']['WEB_VERSION']);
define("TBL_LANGUAGES", TBL_PREFIX . "languages");
define("TBL_DOCTOR_AVAILABILITY", TBL_PREFIX . "doctor_availability");
define("TBL_SPECIALISATIONS", TBL_PREFIX . "specialization");
define("TBL_DOCTOR_CLINIC_MAPPING", TBL_PREFIX . "doctor_clinic_mapping");
define("TBL_NOTIFICATION", TBL_PREFIX . "notification_list");
define("TBL_PATIENT_REPORT_TRACK", TBL_PREFIX . "patient_report_track");
define("APP_CRON_PATH", DOCROOT_PATH . API_VERSION_APP . 'index.php');
define("TBL_PATIENT_FAMILY_MEMBER_MAPPING", TBL_PREFIX . "patient_family_member_mapping");
define('AUDIT_SLUG_ARR', array('LOGIN_ACTION' => 'login', 'LOGOUT_ACTION' => 'logout', 'USER_PROFILE_ACTION' => 'profile'));
define("TBL_AUDIT_LOG", TBL_PREFIX . "audit_log");
define("IS_FILE_CACHING_ACTIVE",1); //0 = deactive and 1 = active
define('CACHE_TTL', 0); // 3600 S = 1HR
define("CACHE_PATH", DOCROOT_PATH . "application/" . API_VERSION . 'cache/');
define('DB_CACHE_PATH', [
  'cron_cache' => CACHE_PATH. 'cron_cache/'
]);
define('PAYMENT_RECEIPT_FOLDER', 'payment_receipt');
define('UAS7_GST_PERCENT', 18);
define('UAS7_FOLDER', 'uas7');
define('IS_STOP_SMS', $GLOBALS['ENV_VARS']['IS_STOP_SMS']);
define('IS_STOP_WHATSAPP_SMS', $GLOBALS['ENV_VARS']['IS_STOP_WHATSAPP_SMS']);
define('ISSUE_FOLDER', 'issue');
define("ADMIN_EMAIL", $GLOBALS['ENV_VARS']['ADMIN_EMAIL']);
define('USER_ID_PROOF_FOLDER', 'id_proof');
define("MEDSIGN_WEB_CARE_URL", $GLOBALS['ENV_VARS']['MEDSIGN_WEB_CARE_URL']);
define("PAST_PRESCRIPTION", 'past_prescription');
define("FIREBASE_API_ACCESS_KEY", $GLOBALS['ENV_VARS']['FIREBASE_API_ACCESS_KEY']);
define("IS_S3_UPLOAD", $GLOBALS['ENV_VARS']['IS_S3_UPLOAD']);
define("IS_SERVER_UPLOAD", $GLOBALS['ENV_VARS']['IS_SERVER_UPLOAD']);