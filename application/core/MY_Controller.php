<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class MY_Controller extends CI_Controller {

    public $controller_name;
    public $method_name;
    public $utc_time;

    public function __construct() {
        parent::__construct();
        $lang = !empty($this->session->userdata('language')) ? $this->session->userdata('language') : DEFAULT_LANG;
		//$this->utc_time = time();
        $this->utc_time = date(DATE_FORMAT, time());
        $this->config->set_item('language', $lang);
        $this->utc_time_formated = date('Y-m-d H:i:s', time());
        $this->lang->load($lang);

        $controller_name = $this->router->fetch_class();
        
        $this->get_instance()->method_name = $this->router->fetch_method();
        $this->get_instance()->controller_name = $controller_name;

        $admin_without_security_token = array(
            'common',
            'login',
            'home',
            'dashboard',
            'profile',
            'documents',
            'appointment',
            'teleconsultant',
            'prescription',
            'vitals',
            'utilities',
            'uas7diary',
            'support',
            'care',
            'payment',
            'analytics',
            'patient_share',
            'family_member',
            'document_view',
            'logout',
            'web'
        );

        if (!$this->input->is_ajax_request()) {
            if (!in_array($controller_name, $admin_without_security_token)) {
                if ($this->session->userdata('user_id') == NULL) {
                    redirect(DOMAIN_URL);
                }
            }
        }
    }

    public function notfound() {
        $this->load->view('/errors/404'); //loading view
    }

    public function internal_server() {
        $this->load->view('/errors/500'); //loading view
        return false;
    }

    /**
     * This function is use for send email
     * @param array $to_email_address
     * @param string $subject
     * @param text $message
     */
    /*public function send_email($to_email_address, $subject, $message) {
        try {
            // Swift Mailer Library
            require_once SWIFT_MAILER_PATH;

            // Mail Transport
            //$transport = Swift_SmtpTransport::newInstance(EMAIL_HOST, EMAIL_PORT, "ssl")
            $transport = Swift_SmtpTransport::newInstance(EMAIL_HOST, EMAIL_PORT)
                    ->setUsername(EMAIL_USER)
                    ->setPassword(EMAIL_PASS);
            // Mailer
            $mailer = Swift_Mailer::newInstance($transport);

            $mailLogger = new \Swift_Plugins_Loggers_ArrayLogger();
            $mailer->registerPlugin(new \Swift_Plugins_LoggerPlugin($mailLogger));

            // Create a message
            $sendMessage = Swift_Message::newInstance($subject)
                    ->setFrom(array(EMAIL_FROM => APP_NAME))
                    ->setTo($to_email_address)
                    ->setBody($message, 'text/html');
            // Send the message
            if (!$mailer->send($sendMessage)) {
                // SEND MAIL USING SWIFT MAILER
                $headers = "MIME-Version: 1.0\r\n";
                $headers .= "Content-type: text/html; charset=iso-8859-1\r\n";
                $headers .= "X-Priority: 3\r\n";
                $headers .= "X-Mailer: PHP" . phpversion() . "\r\n";
                $headers .= "From:" . APP_NAME . "<" . EMAIL_FROM . ">" . "\r\n";

                foreach ($to_email_address as $to) {
                    mail($to, $subject, $message, $headers);
                }
            }
        } catch (Exception $e) {
            log_message("ERROR", $e->getMessage());
        }
    }*/
    public function send_email($to_email_address, $subject, $message) {
        try {
            require_once SWIFT_MAILER_PATH;
            $transport = Swift_SmtpTransport::newInstance(EMAIL_HOST, EMAIL_PORT, EMAIL_CERTIFICATE)
                    ->setUsername(EMAIL_USER)
                    ->setPassword(EMAIL_PASS);
            $mailer = Swift_Mailer::newInstance($transport);
            $sendMessage = Swift_Message::newInstance($subject)
                    ->setFrom(array(EMAIL_FROM => APP_NAME))
                    ->setTo($to_email_address);
            $doc = new DOMDocument();
            @$doc->loadHTML($message);
//            $tags = $doc->getElementsByTagName('img');
//            foreach ($tags as $tag) {
//                if (filter_var($tag->getAttribute('src'), FILTER_VALIDATE_URL)) {
//                    
//                } else {
//                    //$attachment = Swift_Image::newInstance($tag->getAttribute('src'),"image.png",'image/png')->setDisposition('inline');
//                    $imag_src = str_replace("data:image/png;base64,", "", $tag->getAttribute('src'));
//                    $imag_src = str_replace(" ", "+", $imag_src);
//                    $attachment = new Swift_Image(base64_decode($imag_src), 'image.png', 'image/png');
//                    //echo $attachment;exit;
//                    $cid = $sendMessage->embed($attachment);
//                    $tag->setAttribute('src', $cid);
//                }
//            }
            $sendMessage->setBody($doc->saveHTML(), "text/html");


            if (!$mailer->send($sendMessage)) {
                $this->send_normal_email($to_email_address, $subject, $message);
            }
        } catch (\Swift_TransportException $e) {
            $response = $e->getMessage();
            $this->send_normal_email($to_email_address, $subject, $message);
            file_put_contents($this->log_file, "\n\n ======== swift send_email ex: " . json_encode($response) . "===== \n\n", FILE_APPEND | LOCK_EX);
        } catch (Exception $e) {
            $response = $e->getMessage();
            $this->send_normal_email($to_email_address, $subject, $message);
            file_put_contents($this->log_file, "\n\n ======== send_email ex: " . json_encode($response) . "===== \n\n", FILE_APPEND | LOCK_EX);
        }
    }

    /**
     * Removing extra slashes from the response string recursively
     * @param type $variable
     * @return type
     */
    function strip_slashes_recursive($variable) {
        if (is_null($variable))
            $variable = "";
        if (is_string($variable))
            return stripslashes($variable);
        if (is_array($variable))
            foreach ($variable as $i => $value)
                $variable[$i] = $this->strip_slashes_recursive($value);

        return $variable;
    }

    public function pr($arr) {
        echo "<pre>";
        print_r($arr);
        die;
    }

    public function breadcrumb() {
        $controller = $this->router->fetch_class();
        $action = $this->router->fetch_method();
        $controller_icon = '';
        $view_icon = '';
        $breadcrumb_str = '';

        switch ($controller) {
            case 'users':
                $controller_icon = 'person';
                break;
            case 'settings':
                $controller_icon = 'settings';
                break;
            case 'dashboard':
                $controller_icon = 'home';
                break;
        }

        switch ($action) {
            case 'add':
                $view_icon = 'add';
                break;
            case 'edit':
                $view_icon = 'edit';
                break;
            case 'view':
                $view_icon = 'view_list';
                break;
            case 'change_password':
                $action = 'Change Password';
                $view_icon = 'lock';
                break;
            case 'edit_profile':
                $action = 'Edit Profile';
                $view_icon = 'person';
                break;
        }
        if ($controller == 'dashboard' && $action == 'index') {
            $breadcrumb_str = '<ol class="breadcrumb">
                <li class="active">
                    <i class="material-icons">home</i> Home
                </li>';
        } else {
            $breadcrumb_str = '<ol class="breadcrumb">
                <li class="active">
                    <a href="' . DASHBOARD_PATH . '"><i class="material-icons">home</i> Home</a>
                </li>';
        }
        if ($action != 'index') {
            if ($controller != 'dashboard') {
                $breadcrumb_str .= '<li class="active">
                                    <a href="' . BASE_URL . '/' . $controller . '"><i class="material-icons">' . $controller_icon . '</i> ' . ucfirst($controller) . '</a>
                                </li>';
            }
            $breadcrumb_str .= '<li class="active">
                                    <i class="material-icons">' . $view_icon . '</i> ' . ucfirst($action) . '
                                </li>';
        } else {
            if ($controller != 'dashboard') {
                $breadcrumb_str .= '<li class="active">
                                        <i class="material-icons">' . $controller_icon . '</i> ' . ucfirst($controller) . '
                                    </li>';
            }
        }
        $breadcrumb_str .= '</ol>';
        return $breadcrumb_str;
    }
}