<?php

class Notification extends CI_Controller {
	public function __construct() {
        parent::__construct();
        $this->email_log_file = LOG_FILE_PATH . '/email_log_' . date('d-m-Y') . '.txt';
        ini_set('max_execution_time', 0); 
        ini_set('memory_limit','2048M');
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
            }
            $mailer->send($sendMessage);
        } catch (\Swift_TransportException $e) {
            $response = $e->getMessage();
            file_put_contents($this->email_log_file, "\n\n ======== response" . json_encode($response) . "===== \n\n", FILE_APPEND | LOCK_EX);
            
        } catch (Exception $e) {
            $response = $e->getMessage();
            file_put_contents($this->email_log_file, "\n\n ======== response" . json_encode($response) . "===== \n\n", FILE_APPEND | LOCK_EX);
        }
    }
}