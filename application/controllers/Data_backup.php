<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Data_backup extends CI_Controller {

	public $is_session;
	function __construct() {
		parent::__construct();
		$this->email_log_file = LOG_FILE_PATH . '/email_log_' . date('d-m-Y') . '.txt';
		ini_set('max_execution_time', 0); 
        ini_set('memory_limit','2048M');
	}
	
	public function backup(){
		$data_backup_log = LOG_FILE_PATH . '/data_backup_log_' . date('d-m-Y') . '.txt';
        file_put_contents($data_backup_log, "\n\n =================" .get_display_date_time("Y-m-d H:i:s"). " Data backup start =================", FILE_APPEND | LOCK_EX);
		$this->load->library('zip');
		$this->load->helper('file');
		$filePathPrefix = date('Y_m_d_H_i', strtotime('-1 day', strtotime(get_display_date_time('Y-m-d H:i:s'))));
		/*[Uploads data create zip]*/
		$folderArr = ['advertisement','anatomical_diagram','clinical_report','clinics','doctor_award','doctor_education','doctor_registration','doctorqrcode','help_av','id_proof','issue','past_prescription','patient_invoice_share','patient_prescription_share','payment_receipt','report','rx','rx_print_logo','rx_print_watermark','signature','survey','uas7','users'];
		foreach ($folderArr as $key => $folderName) {
			$path = UPLOAD_FILE_FULL_PATH . $folderName . '/';
			$this->zip->read_dir($path,false);
		}
		$this->zip->archive(UPLOAD_FILE_FULL_PATH . '/'. $filePathPrefix.'_doc.zip');
		$this->zip->clear_data();
		file_put_contents($data_backup_log, "\n\n Documents Zip created", FILE_APPEND | LOCK_EX);
		$zip = new ZipArchive();
		$secretKey = encrypt_decrypt(str_replace('_', '', $filePathPrefix), 'encrypt');
		if ($zip->open(UPLOAD_FILE_FULL_PATH . '/'. $filePathPrefix.'_documents.zip', ZipArchive::CREATE) === TRUE) {
		    $zip->setPassword($secretKey);
		    $zip->addFile('uploads/'. $filePathPrefix.'_doc.zip');
		    $zip->setEncryptionName('uploads/'. $filePathPrefix.'_doc.zip', ZipArchive::EM_AES_256);
		    $zip->close();
		    if(file_exists(UPLOAD_FILE_FULL_PATH . '/'. $filePathPrefix.'_documents.zip')) {
				foreach ($folderArr as $key => $folderName) {
					if($folderName != 'anatomical_diagram'){
						$path = UPLOAD_FILE_FULL_PATH . $folderName . '/';
						delete_files($path, TRUE);
					}
				}
			}
			file_put_contents($data_backup_log, "\n\n Documents password protect zip created", FILE_APPEND | LOCK_EX);
		    unlink(UPLOAD_FILE_FULL_PATH . '/'. $filePathPrefix.'_doc.zip');
		} else {
		    $eparams['doc']["status"] = 'Failed';
			$eparams['doc']["logdata"] = 'Sorry, something went wrong. Please contact system admin.';
			$this->dbbackup_email($eparams);
		}

		// Load the DB utility class
		$this->load->dbutil();
		$ignoreTables = [
			'me_api_logs',
			// 'me_drugs',
			'me_doctor_qualifications_vw',
			'me_doctor_specialization_vw',
			'users_used_size_vw'
		];
		$prefs = array(
			'tables'        => array(),                     // Array of tables to backup.
			'ignore'        => $ignoreTables,                     // List of tables to omit from the backup
			'format'        => 'zip',                       // gzip, zip, txt
			'filename'      => $filePathPrefix.'_database.sql', // File name - NEEDED ONLY WITH ZIP FILES
			'add_drop'      => TRUE,                        // Whether to add DROP TABLE statements to backup file
			'add_insert'    => TRUE,                        // Whether to add INSERT data to backup file
			'foreign_key_checks'    => false,
			'newline'       => "\n"                         // Newline character used in backup file
		);
		
		// Backup your entire database and assign it to a variable
		$backup = $this->dbutil->backup($prefs);
		// Load the file helper and write the file to your server
		$filename = $filePathPrefix.'_database.zip';
		if(write_file(UPLOAD_FILE_FULL_PATH . '/' . $filename, $backup) ){
			file_put_contents($data_backup_log, "\n\n Database zip created", FILE_APPEND | LOCK_EX);
			$zip = new ZipArchive();
			if ($zip->open(UPLOAD_FILE_FULL_PATH . '/'. $filePathPrefix.'_db.zip', ZipArchive::CREATE) === TRUE) {
			    $zip->setPassword($secretKey);
			    $zip->addFile('uploads/'. $filename);
			    $zip->setEncryptionName('uploads/'. $filename, ZipArchive::EM_AES_256);
			    $zip->close();
			    unlink(UPLOAD_FILE_FULL_PATH . '/'. $filename);
			    file_put_contents($data_backup_log, "\n\n Database password protect zip created", FILE_APPEND | LOCK_EX);
				$this->dbBackupFileMoveToGDrive($filePathPrefix);
				file_put_contents($data_backup_log, "\n\n Backed up successfully done.", FILE_APPEND | LOCK_EX);
				echo "Database has been backed up successfully.";
			} else {
			    echo "Sorry, something went wrong. Please contact system admin.";
				$eparams['db']["status"] = 'Failed';
				$eparams['db']["logdata"] = 'Sorry, something went wrong. Please contact system administrator.';
				file_put_contents($data_backup_log, "\n\n ".$eparams['db']["logdata"], FILE_APPEND | LOCK_EX);
				$this->dbbackup_email($eparams);
			}
		}else{
			echo "Sorry, something went wrong. Please contact system admin.";
			$eparams['db']["status"] = 'Failed';
			$eparams['db']["logdata"] = 'Sorry, something went wrong. Please contact system admin.';
			file_put_contents($data_backup_log, "\n\n ".$eparams['db']["logdata"], FILE_APPEND | LOCK_EX);
			$this->dbbackup_email($eparams);
		}
	}
	
	public function dbBackupFileMoveToGDrive($fileName){
		if(empty($fileName))
			return false;
		
		require_once DOCROOT_PATH . 'application/third_party/google_api/google-api-php-client-2.2.3/vendor/autoload.php';
		$client = new Google_Client();
		//The json file you got after creating the service account
		putenv('GOOGLE_APPLICATION_CREDENTIALS=' . DOCROOT_PATH . 'application/third_party/google_api/key/medsigndevelopment-8a95c11708e7.json'); //GOOGLE DRIVE-48f93ac19c8a.json
		$client->useApplicationDefaultCredentials();
		$client->setApplicationName("GDrive");
		$client->addScope(Google_Service_Drive::DRIVE);
		$client->setAccessType('offline');
		$folderId = '1mA1TvVMzYh6gApgYDMALlzkBYKn1lcuG'; //https://drive.google.com/drive/folders/1mA1TvVMzYh6gApgYDMALlzkBYKn1lcuG
		$driveService = new Google_Service_Drive($client);

		/*[START] Create the folder on the Google drive*/
		$file = new Google_Service_Drive_DriveFile(
			['name'=>date('d-m-Y').'_BACKUP',
			'title'=>date('d-m-Y').'_BACKUP',
			'description'=>'Database backup at '.date('d-m-Y'),
			'parents' => array($folderId),
			'mimeType'=>"application/vnd.google-apps.folder"
		]);
		$objFolder = $driveService->files->create($file, array());
		$folderId = $objFolder->id;
		/*[END] Create the folder on the Google drive*/
		/*[START] Uploading database backup file on the Google drive*/
		$dbFileName = $fileName.'_db.zip';
		$fileMetadata = new Google_Service_Drive_DriveFile(array('name' => $dbFileName,'parents' => array($folderId)));
		$content = file_get_contents(UPLOAD_FILE_FULL_PATH . '/' . $dbFileName);
		$file = $driveService->files->create($fileMetadata, array('data' => $content, 'mimeType' => 'application/zip', 'uploadType' => 'multipart', 'fields' => 'id'));        
		$emailParams['db']["filename"] = $dbFileName;
		if(isset($file->id) && $file->id != ''){
			$emailParams['db']["status"] = 'Success';
			$emailParams['db']["logdata"] = 'File ID:'.$file->id;
			unlink(UPLOAD_FILE_FULL_PATH . '/' . $dbFileName);
		}else{
			$emailParams['db']["status"] = 'Google Drive Sync Fail.';
			$emailParams['db']["logdata"] = !empty($file) ? json_encode($file) : '';
		}
		/*[END] Uploading database backup file on the Google drive*/
		/*[START] Uploading documents on the Google drive*/
		$docFileName = $fileName.'_documents.zip';
		$fileMetadata = new Google_Service_Drive_DriveFile(array('name' => $docFileName,'parents' => array($folderId)));
		$content = file_get_contents(UPLOAD_FILE_FULL_PATH . '/' . $docFileName);
		$file = $driveService->files->create($fileMetadata, array('data' => $content, 'mimeType' => 'application/zip', 'uploadType' => 'multipart', 'fields' => 'id'));        
		$emailParams['doc']["filename"] = $docFileName;
		if(isset($file->id) && $file->id != ''){
			$emailParams['doc']["status"] = 'Success';
			$emailParams['doc']["logdata"] = 'File ID:'.$file->id;
			unlink(UPLOAD_FILE_FULL_PATH . '/' . $docFileName);
		}else{
			$emailParams['doc']["status"] = 'Google Drive Sync Fail.';
			$emailParams['doc']["logdata"] = !empty($file) ? json_encode($file) : '';
		}
		/*[END] Uploading documents on the Google drive*/
		$this->dbbackup_email($emailParams);
		return false;
	}

	/*
	* SEND DATABASE BACKUP DETAIL TO ADMIN
	*/
	private function dbbackup_email($params = array())
	{
		$this->load->model('Emailsetting_model');
        $email_template_data = $this->Emailsetting_model->get_emailtemplate_by_id(69);
        $messageContent = '';
        if(!empty($params['db'])) {
        	$messageContent .= 'DB Status: ' . $params['db']['status'] . "<br>";
        	$messageContent .= $params['db']['logdata'];
        }
        if(!empty($params['doc'])) {
        	if(!empty($messageContent))
        		$messageContent .= "<br><br>";
        	$messageContent .= 'Documents Status: ' . $params['doc']['status'] . "<br>";
        	$messageContent .= $params['doc']['logdata'];
        }
        $parse_arr = array(
            '{WebUrl}' => DOMAIN_URL,
            '{AppName}' => APP_NAME,
            '{messageContent}' => $messageContent,
            '{MailEmailAddress}' => $email_template_data['email_static_data']['email_id'],
            '{MailCompanyName}' => $email_template_data['email_static_data']['company_name'],
            '{CopyRightsYear}' => date('Y')
        );
        $message = replace_values_in_string($parse_arr, $email_template_data['email_template_message']);
        $subject = $email_template_data['email_template_subject'] . ' | ' . get_display_date_time('Y-m-d H:i');
        $email_id_arr = explode(',', $GLOBALS['ENV_VARS']['BACKUP_NOTIFICATION_EMAIL']);
        $this->send_email($email_id_arr, $subject, $message);
	}

	/**
     * 
     * @param type $to_email_address
     * @param type $subject
     * @param type $message
     * @param type $attachment
     */
    public function send_email($to_email_address, $subject, $message, $attachment = array()) {
        try {
            require_once SWIFT_MAILER_PATH;
            $transport = Swift_SmtpTransport::newInstance(EMAIL_HOST, EMAIL_PORT, EMAIL_CERTIFICATE)
                    ->setUsername(EMAIL_USER)
                    ->setPassword(EMAIL_PASS);
            $mailer = Swift_Mailer::newInstance($transport);
            $sendMessage = Swift_Message::newInstance()
                    ->setSubject($subject)
                    ->setTo($to_email_address)
                    ->setFrom(array(EMAIL_FROM => APP_NAME))
                    ->setBody($message, 'text/html');

            if(!empty($attachment[0]['path'])){
                foreach ($attachment as $key => $value) {
                    $sendMessage->attach(Swift_Attachment::fromPath($value['path'])->setFilename($value['name']));
                }
            } elseif (!empty($attachment)) {
                $sendMessage->attach(Swift_Attachment::fromPath($attachment[0])->setFilename($attachment[1]));
            }

            if (!$mailer->send($sendMessage)) {
                file_put_contents($this->email_log_file, "\n\n ======== send by php mail ===== \n\n", FILE_APPEND | LOCK_EX);
            }
        } catch (\Swift_TransportException $e) {
            $response = $e->getMessage();
            file_put_contents($this->email_log_file, "\n\n ======== response" . json_encode($response) . "===== \n\n", FILE_APPEND | LOCK_EX);
        } catch (Exception $e) {
            $response = $e->getMessage();
            file_put_contents($this->email_log_file, "\n\n ======== response" . json_encode($response) . "===== \n\n", FILE_APPEND | LOCK_EX);
        }
    }

}