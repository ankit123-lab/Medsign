<?php

if (!function_exists('get_raw_password')) {

    /* returns 6 character random password
      include 2 alpha,1 uppercase ,2 num, 1 special char
     * 


     */

    function get_raw_password() {

        $alpha = "abcdefghijklmnopqrstuvwxyz";
        $password = substr(str_shuffle($alpha), 0, 2);
        $upercase = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
        $password .= substr(str_shuffle($upercase), 0, 1);
        $num = "0123456789";
        $password .= substr(str_shuffle($num), 0, 2);
        $special_char = "!@#$%^&*_-";
        $password .= substr(str_shuffle($special_char), 0, 1);
        return $password;
    }

}

if (!function_exists('send_notification_android')) {

    function send_notification_android($registration_ids, $message, $fcm_key = '') {
        if (!empty($fcm_key)) {
            $url = 'https://fcm.googleapis.com/fcm/send';

            $headers = array(
                'Authorization: key=' . $fcm_key,
                'Content-Type: application/json'
            );

            $fields = array(
                'registration_ids' => $registration_ids,
                'data' => $message
            );

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
            $result = curl_exec($ch);
            curl_close($ch);

            $resultArr = json_decode($result, true);

            if ($resultArr['success'] == 1) {
                return true;
            }
        }
        return false;
    }

}




if (!function_exists('get_current_time')) {

    /* return utc timestamp in seconds */

    function get_current_time($timezone = "UTC") {

        date_default_timezone_set($timezone);
        return time();
    }

}



/* For truncate string and add the ellipses to string */
if (!function_exists('truncate')) {

    function truncate($string, $del, $dot = false) {
        $len = strlen($string);
        if ($len > $del) {
            $new = substr($string, 0, $del);
            if ($dot == true) {
                $new .= "...";
            }
            return $new;
        } else {
            return $string;
        }
    }

}

/* * ***************** Hour to second ******** ************ */
if (!function_exists('hr_to_sec')) {

    function hr_to_sec($hr_time) {
        if ($hr_time == '') {
            return false;
        }
        $hr_time_arr = explode(':', $hr_time);
        $hr_time_hr = (isset($hr_time_arr['0'])) ? $hr_time_arr['0'] : 0;
        $hr_time_mnt = (isset($hr_time_arr['1'])) ? $hr_time_arr['1'] : 0;
        $hr_time_sec = (isset($hr_time_arr['1'])) ? $hr_time_arr['1'] : 0;
        $total_time_sec = ($hr_time_hr * 3600) + ($hr_time_mnt *
                60) + ($hr_time_sec);
        return $total_time_sec;
    }

}
/* * ***************** Second to hour ******** ************ */

if (!function_exists('sec_to_hr')) {

    function sec_to_hr($sec_time, $format = 'H:i:s') {
        if ($sec_time == '') {

            return false;
        }
        $hr = floor($sec_time / 3600);
        $mnt = floor(($sec_time % 3600) / 60);
        $sec = ($sec_time % 3600) % 60;
        if ($format == 'H:i:s') {
            $total_hr = $hr . ':' . $mnt . ':' . $mnt;
        } else if ($format == 'H:i') {
            $total_hr = $hr . ':' . $mnt;
        } else if ($format == 'i:s') {
            $total_hr = $mnt . ':' . $sec;
        } else if ($format == 'H:s') {
            $total_hr = $hr . ':' . $sec;
        } else if ($format == 'H') {
            $total_hr = $hr;
        } else if
        ($format == 's') {
            $total_hr = $sec;
        } else if ($format == 'i') {
            $total_hr = $mnt;
        } else {
            $total_hr = $hr . ':' . $mnt . ':' . $sec;
        }
        return $total_hr;
    }

}





/* * ***************** Day Name To week day no ******************** */
if (!function_exists('dayname_to_weekdayno')) {

    function dayname_to_weekdayno($data = 'Monday') {
        $numDaysToMon = '';
        switch ($data) {
            case 'Monday': $numDaysToMon = 1;
                break;
            case 'Tuesday': $numDaysToMon = 2;
                break;
            case 'Wednesday': $numDaysToMon = 3;
                break;
            case 'Thursday': $numDaysToMon = 4;
                break;
            case 'Friday': $numDaysToMon = 5;
                break;
            case 'Saturday': $numDaysToMon = 6;
                break;
            case 'Sunday': $numDaysToMon = 7;
                break;
        }
        return $numDaysToMon;
    }

}


/* * ***************** week day no To Day Name ********  */
if (!function_exists('weekdayno_to_dayname')) {

    function weekdayno_to_dayname($data = '1') {
        $numDaysToMon = '';
        switch ($data) {
            case '1': $numDaysToMon = 'Monday';
                break;
            case '2': $numDaysToMon = 'Tuesday';
                break;
            case '3': $numDaysToMon = 'Wednesday';
                break;
            case '4': $numDaysToMon = 'Thursday';
                break;
            case '5': $numDaysToMon = 'Friday';
                break;
            case '6': $numDaysToMon = 'Saturday';
                break;
            case '7': $numDaysToMon = 'Sunday';
                break;
        }
        return $numDaysToMon;
    }

}

/* * ********* Generates a Photo From Url Code **************** */
if (!function_exists('get_image_from_url')) {

    function get_image_from_url($link) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_POST, 0);
        curl_setopt($ch, CURLOPT_URL, $link);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_BINARYTRANSFER, 1);
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
    }

}

/* * ********* Get The Week Start date **************** */
if (!function_exists('get_week_start_date')) {

    function get_week_start_date($wk_num, $yr, $first = 0) {
        $wk_ts = strtotime('+' . $wk_num . ' weeks', strtotime($yr . '0101'));
        $mon_ts = strtotime('-' . date('w', $wk_ts) + $first . ' days', $wk_ts);
        return $mon_ts;
    }

}

if (!function_exists('get_last_week_time_array')) {

    function get_last_week_time_array($weekCount = '52') {  // KD MAX 52 WEEK // max 52;
        $past_year = date('Y', time()) - 1;
        $year_weak = array();
        for ($week_number = 0; $week_number < 56; $week_number++) {
            $year_weak[] = $this->get_week_start_date($week_number, date('Y', time()) - 1);
        }
        for ($week_number = 0; $week_number < 56; $week_number++) {
            $weektime = $this->get_week_start_date($week_number, date('Y', time()));
            if ($weektime <= $this->utc_time) {
                $year_weak[] = $weektime;
            }
        }
        $year_weak = array_unique($year_weak, SORT_STRING);
        asort($year_weak);
        $k = array();
        foreach ($year_weak as $key => $value) {
            $k[] = $value;
        }
        for ($i = count($k); $i > count($k) - $weekCount; $i--) {
            $j[] = $k[$i - 1];
        }
        return $j;
    }

}


/* * ********* Get The last day Of Month  **************** **************** **************** */
if (!function_exists('lastday_month')) {

    function lastday_month($month = '', $year = '') {
        if (empty($month)) {
            $month = date('m');
        }
        if (empty($year)) {
            $year = date('Y');
        }
        $result = strtotime("{$year}-{$month}-01");
        $result = strtotime('-1 second', strtotime('+1 month', $result));
        return $result;
    }

}


if (!function_exists('get_last_month_time_array')) {

    function get_last_month_time_array($total_month_point) {  // KD MAX 52 WEEK // max 52;
        $k = 0;
        $year = date('Y', $this->utc_time);
        $current_month = date('m', $this->utc_time) + 1;
//  $total_month_point = 31;
        $kd = 0;
        $month_array = array();
        for ($i = 0; $i < 3; $i++) {
            if ($i == '0') {
                for ($j = $current_month; $j > 0 && $kd < $total_month_point; $j--) {
                    $kd = $kd + 1;
                    $month_array[] = $this->lastday_month($j, $year);
                }
            } else {
                for ($j = 12; $j > 0 && $kd < $total_month_point; $j--) {
                    $kd = $kd + 1;
                    $month_array[] = $this->lastday_month($j, $year);
                }
            }
            $year = $year - 1;
        }
        return $month_array;
    }

}



/* * ********* CUSTOM ERROR MESSAGES  **************** **************** **************** */

if (!function_exists('code_to_message')) {

    function code_to_message($code) {
        switch ($code) {
            case UPLOAD_ERR_INI_SIZE:
                $message = "The uploaded file exceeds the upload_max_filesize directive in php.ini (Allow max file upload size :  " . ini_get('upload_max_filesize') . ")";
                break;
            case UPLOAD_ERR_FORM_SIZE:
                $message = "The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form";
                break;
            case UPLOAD_ERR_PARTIAL:
                $message = "The uploaded file was only partially uploaded";
                break;
            case UPLOAD_ERR_NO_FILE:
                $message = "No file was uploaded";
                break;
            case UPLOAD_ERR_NO_TMP_DIR:
                $message = "Missing a temporary folder";
                break;
            case UPLOAD_ERR_CANT_WRITE:
                $message = "Failed to write file to disk";
                break;
            case UPLOAD_ERR_EXTENSION:
                $message = "File upload stopped by extension";
                break;
            default:
                $message = "Unknown upload error";
                break;
        }
        return $message;
    }

}

// Function for generate random string for access token generate

if (!function_exists('str_rand_access_token')) {

    function str_rand_access_token($length = 32, $seeds = 'allalphanum') {
// Possible seeds
        $seedings['alpha'] = 'abcdefghijklmnopqrstuvwqyz';
        $seedings['numeric'] = '0123456789';
        $seedings['alphanum'] = 'abcdefghijklmnopqrstuvwqyz0123456789';
        $seedings['allalphanum'] = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwqyz0123456789';
        $seedings['upperalphanum'] = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $seedings['alphanumspec'] = 'abcdefghijklmnopqrstuvwqyz0123456789!@#$%^*-_=+';
        $seedings['alphacapitalnumspec'] = 'abcdefghijklmnopqrstuvwqyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789#@!*-_';
        $seedings['hexidec'] = '0123456789abcdef';
        $seedings['customupperalphanum'] = 'ABCDEFGHJKLMNP QRSTUVWXYZ23456789'; //Confusing chars like 0,O,1,I not included
// Choose seed
        if (isset($seedings[$seeds])) {
            $seeds = $seedings[$seeds];
        }

// Seed gener ator
        list($usec, $sec) = explode(' ', microtime());
        $seed = (float) $sec + ((float) $usec * 100000) + rand(10,9999);
        mt_srand($seed);

// Generate
        $str = '';
        $seeds_count = strlen($seeds);

        for ($i = 0; $length > $i; $i++) {
            $str .= $seeds{mt_rand(0, $seeds_count - 1)};
        }

        return $str;
    }

}


//Get file type

if (!function_exists('get_file_type')) {

    function get_file_type($ext) {
        $filetType = 3; //File ot her than  image and video
        $imageExtensions = array('jpeg', 'JPEG', 'gif', 'GIF', 'png', 'PNG', 'jpg', 'JPG');
        $videoExtensions = array('wmv', 'WMV', 'wav', 'WAV', 'm4r', 'M4R', 'mpeg', 'MPEG', 'mpg', 'MPG', 'mpe', 'MPE', 'mov', 'MOV', 'avi', 'AVI', 'mp4', 'MP4', 'm4v', 'M4V', '3gp', '3GP', 'flv', 'FLV', 'pem', 'PEM');
        $audioExtensions = array('mp3', 'm4a', 'm4b', 'ra', 'ram', 'wav', 'ogg', 'oga', 'mid', 'midi', 'wma', 'wax', 'mka');
        if (in_array($ext, $imageExtensions)) {
            $filetType = 1; //Image file
        } elseif (in_array($ext, $videoExtensions)) {
            $filetType = 2; //Video file
        } elseif (in_array($ext, $audioExtensions)) {
            $filetType = 3; //Video file
        }
        return $filetType;
    }

}



/**
 * createArray($data) 
 * 
 * This is adds the contents of the return xml into the array for easier processing. 
 * 
 * @access    public 
 * @param    string    $data this is the string of the xml data 
 * @return     Array 
 */
if (!function_exists('create_array')) {

    function create_array($xml) {
        $values = array();
        $index = array();
        $array = array();
        $parser = xml_parser_create();
        xml_parser_set_option($parser, XML_OPTION_SKIP_WHITE, 1);
        xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 0);
        xml_parse_into_struct($parser, $xml, $values, $index);
        xml_parser_free($parser);
        $i = 0;
        $name = $values[$i]['tag'];
        $array[$name] = isset($values[$i]['attributes']) ? $values[$i]['attributes'] : '';
        $array[$name] = $this->_struct_to_array($values, $i);
        return $array;
    }

}


/** * ************ GET REQUEST WITH AJAX CALL OR NOT FROM DATABASE ******** */
if (!function_exists('is_ajax')) {

    function is_ajax() {
        if ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')
            return true;
        return false;
    }

}


if (!function_exists('get_data_by_curl_with_get_url')) {

    function get_data_by_curl_with_get_url($url) {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($ch);
        curl_close($ch);
        return $output;
    }

}
if (!function_exists('delete_image_in_folder')) {

    function delete_image_in_folder($k_image, $path) {
        if (file_exists($path)) {
            @unlink($path . $k_image);
        }
        if (file_exists($path)) {
            @unlink($path . IMG_THUMB_PRE . $k_image);
        }
    }

}


/* * ***************** DLETE IMAGES AND DIRECTORY ******** ********* */
if (!function_exists('delete_folder')) {

    function delete_folder($directory, $empty = false) {
        if (substr($directory, -1) == "/") {
            $directory = substr($directory, 0, -1);
        }
        if (!file_exists($directory) || !is_dir($directory)) {
            return false;
        } elseif (!is_readable($directory)) {
            return false;
        } else {
            $directoryHandle = opendir($directory);
            while ($contents = readdir($directoryHandle)) {
                if ($contents != '.' && $contents != '..') {
                    $path = $directory . "/" . $contents;
                    if (is_dir($path)) {
                        $this->deleteAll($path);
                    } else {
                        unlink($path);
                    }
                }
            }
            closedir($directoryHandle);
            if ($empty == false) {
                if (!rmdir($directory)) {
                    return false;
                }
            }
            return true;
        }
    }

}



if (!function_exists('_px')) {

    function _px($arr) {
        echo "<pre>";
        print_r($arr);
        echo "</pre>";
        exit;
    }

}

if (!function_exists('_ex')) {

    function _ex($string) {
        echo $string;
        exit;
    }

}

if (!function_exists('random_color_class')) {

    function random_color_class() {
        $colorArray = array('dark', 'red', 'light-red', 'blue', 'light-blue', 'green', 'light-green',
            'orange', 'light-orange', 'orange2', 'purple', 'pink', 'pink2', 'bro wn', 'grey', 'light-grey');
        $value = array_rand($colorArray);
        return " " . $colorArray[$value];
    }

}

if (!function_exists('get_thumb_filename')) {

    function get_thumb_filename($fileName) {
        $ext = substr($fileName, strrpos($fileName, "."));
        $name = substr($fileName, 0, strrpos($fileName, "."));
        $thumbFile = $name . "_thumb" . $ext;
        return $thumbFile;
    }

}

if (!function_exists('calculate_age')) {

    function calculate_age($dob) {
        return date_diff(date_create($dob), date_create('today'))->y;
    }

}

if (!function_exists('time_ago')) {

// DISPLAYS COMMENT POST TIME AS "1 year, 1 week ago" or "5 minutes, 7 seconds ago", etc...
    function time_ago($time, $granularity = 2, $ago = true) {
        $retval = '';
        $difference = time() - $time;
        $periods = array('decade' => 315360000,
            'year' => 31536000,
            'month' => 2628000,
            'week' => 604800,
            'day' => 86400,
            'hour' => 3600,
            'minute' => 60,
            'second' => 1);

        foreach ($periods as $key => $value) {
            if ($difference >= $value) {
                $time = floor($difference / $value);
                $difference %= $value;
                $retval .= ($retval ? ' ' : '') . $time . ' ';
                $retval .= (($time > 1) ? $key . 's' :
                                $key);
                $granularity --;
            }
            if ($granularity ==
                    '0') {
                break;
            }
        }
        if ($ago)
            return $retval
                    . ' ago';
        else
            return $retval

            ;
    }

}

if (!function_exists('address_to_latlng')) {

    function address_to_latlng($address = '', $city = '', $state = '', $country, $zip = '') {
        $latlng = array(
            'latitude' => '', 'longitude' => '',
        );

//Calling goole api to get lat and lng
        $googleUrl = "https://maps.googleapis.com/maps/api/geocode/json?address=";
        if (!empty($address)) {
            $googleUrl .= urlencode($address);
        }

        if (!empty($city)) {
            $googleUrl .= urlencode($city);
        }

        if (!empty($state)) {
            $googleUrl .= urlencode($state);
        }

        if (!empty($zip)) {
            $googleUrl .= urlencode($zip);
        }

        if (!empty($country)) {
            $googleUrl .= urlencode($country);
        }

        $json_content = file_get_contents($googleUrl);
        $data = json_decode($json_content);
        if ($data->status == 'OK') {
            $latlng['latitude'] = $data->
                    results[0]->geometry->
                    location->lat;
            $latlng['longitude'] = $data->results[0
                    ]->geometry->location->lng;
        }

        return $latlng;
    }

}

if (!function_exists('get_name_title')) {

    function

    get_name_title($titleId) {
        return $titleId;
        $title = '';
        switch ($titleId) {
            case 1:
                $title = lang('USER_ADD_MR');
                break;
            case 2:
                $title = lang('USER_ADD_MISS');
                break;
            case 3:
                $title = lang('USER_ADD_MRS');
                break;
            case 4:
                $title = lang('USER_ADD_DR');
                break;
        }
        return $title;
    }

}

if (!function_exists('send_text_message')) {

    function send_text_message($mobileNo, $message) {
        $user = SMS_USER_NAME;
        $password = SMS_PASSWORD;
        $api_id = SMS_API_ID;
        $baseurl = SMS_BASE_URL;

        $text = urlencode($message);
        $to = $mobileNo;

// auth call
        $url = "$baseurl/http/auth?user=$user&password=$password&api_id=$api_id";

// do auth call
        $ret = file($url);

// explode our response. return string is on first line of the data returned
        $sess = explode(":", $ret[0]);

        if ($sess[0] == "OK") {

            $sess_id = trim($sess[1]); // remove any whitespace

            $url = "$baseurl/http/sendmsg?session_id=$sess_id&to=$to&text=$text";

// do sendmsg call
            $ret = file($url);
            $send = explode(":", $ret[0]);

            if ($send[0] == "ID") {
                return $send[1];
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

}


if (!function_exists('seconds2human')) {

    function seconds2human($ss) {
        $M = floor($ss / 2592000);
        $w = floor($ss / 604800);
        $d = floor(($ss % 2592000) / 86400);
        $h = floor(($ss % 86400) / 3600);
        $m = floor(($ss % 3600) / 60);
        $s = $ss % 60;

//if ($M > 0)
//    return $M." month".(($M > 1) ? 's' : '');
//else if ($w > 0)
//    return $w." week".(($w > 1) ? 's' : '');
//else if ($d > 0)
//    return $d." day".(($d > 1) ? 's' : '');
//else if ($h > 0)
//    return $h." hour".(($h > 1) ? 's' : '');
//else if ($m > 0)
//    return $m." minute".(($m > 1) ? 's' : '');
//else if ($s > 0)
//    return $s." second".(($s > 1) ? 's' : '');
//else
//    return "0"." minutes";

        if ($M > 0)
            return $M . " " . lang('SM_MONTHS');
        else if ($w > 0)
            return $w . " " . lang('SM_WEEKS');
        else if ($d > 0)
            return $d . " " . lang('SM_DAYS');
        else if ($h > 0)
            return $h . " " . lang('SM_HOURS');
        else if ($m > 0)
            return $m . " " . lang('SM_MINUTES');
        else if ($s > 0)
            return $s . " " . lang('SM_SECONDS');
        else
            return "0" . " " . lang('SM_MINUTES');
    }

}

if (!function_exists('idFromUnit')) {

    function idFromUnit($unit) {
        $id = 0;
        switch (strtolower($unit)) {
            case 'hour':
            case 'hours':
                $id = 1;
                break;
            case 'day':
            case 'days':
                $id = 2;
                break;
            case 'week':
            case 'weeks':
                $id = 3;
                break;
            case 'month':
            case 'months':
                $id = 4;
                break;
        }
        return $id;
    }

}

if (!function_exists('calculateAge')) {

    function calculateAge($date) {
        $age = 0;

        $birthDate = explode('-', $date);

        $age = (date("md", date("U", mktime(0, 0, 0, $birthDate[1], $birthDate[2], $birthDate[0]))) > date("md") ? ((date("Y") - $birthDate[0]) - 1) : (
                        date(
                                "Y") - $birthDate

                        [0] )

                );
        return$age;
    }

}

if
 (!function_exists('current_time')) {

    function current_time($format = 'Y-m-d H:i:s') {
        return date($format);
    }

}

//code for photo upload

if (!function_exists('do_upload')) {

    function do_upload($upload_path, $file, $is_thumb = true) {

        $CI = &get_instance();
        reset($file);
        $file_object = key($file);

        $return_file_names = "";
        $file_names_array = array();

        $is_single = '';
//if single file is selected

        if
        (!is_array($file[$file_object] ['name'])) {

            $is_single = true;

            $file_index = 0;
            $_FILES[$file_object . $file_index]['name'] = $file[$file_object]['name'];
            $_FILES[$file_object . $file_index]['size'] = $file[$file_object]['size'];
            $_FILES[$file_object . $file_index]['type'] = $file[$file_object]['type'];
            $_FILES[$file_object . $file_index]['tmp_name'] = $file[$file_object]['tmp_name'];
            $_FILES[$file_object . $file_index]['error'] = $file[$file_object]['error'];

            if ($_FILES[$file_object . $file_index]['error'] != 0) {
                unset($_FILES[$file_object . $file_index]);
            }
            unset($_FILES[$file_object]);
        } else {
            $is_single = false;
            $totalfiles = count($file[$file_object]['name']);
            for ($file_index = 0; $file_index < $totalfiles; $file_index++) {
                $_FILES[$file_object . $file_index]['name'] = $file[$file_object]['name'][$file_index];
                $_FILES[$file_object . $file_index]['size'] = $file[$file_object]['size'][$file_index];
                $_FILES[$file_object . $file_index]['type'] = $file[$file_object]['type'][$file_index];
                $_FILES[$file_object . $file_index]['tmp_name'] = $file[$file_object]['tmp_name'][$file_index];
                $_FILES[$file_object . $file_index]['error'] = $file[$file_object]['error'][$file_index];

                if ($_FILES[$file_object . $file_index]['error'] != 0) {
                    unset($_FILES[$file_object . $file_index]);
                }
            }
        }

        if (!isset($totalfiles)) {
            $totalfiles = 1;
        }

//check upload path folder exist or not?
        $CI->load->library('upload');
        $CI->load->library('image_lib');

        for ($file_index = 0; $file_index < $totalfiles; $file_index++) {
            if (is_array($upload_path)) {
                $config['upload_path'] = $upload_path[$file_index];
                if (!file_exists($upload_path[$file_index])) {
                    mkdir($upload_path[$file_index], 0777, true);
                    chmod($upload_path[$file_index], 0777);
                }
                $file_name = basename($upload_path[$file_index]);
            } else {

                $config['upload_path'] = $upload_path;
                if (!file_exists($upload_path)) {
                    mkdir($upload_path, 0777, true);
                    chmod($upload_path, 0777);
                }
                $file_name = basename($upload_path);
            }

            $config['allowed_types'] = "*";
            $config['overwrite'] = FALSE;
            $config['file_name'] = $file_name . "_" . uniqid();
            $CI->upload->initialize($config);
            if ($CI->upload->do_upload($file_object . $file_index)) {
                $upload_array = array();
                $upload_array = $CI->upload->data();
                $return_file_names = $upload_array['file_name'];
            } else {

                _px($CI->upload->display_errors());
                exit;
                return FALSE;
            }
        }
        if ($is_single) {
            return $return_file_names;
        } else {
            return $file_names_array;
        }
    }

}
if (!function_exists('get_photo_url')) {

    /**
     * Description : This function is used to send the image url
     * @param type $folder_name
     * @param type $id
     * @param type $image_name
     * @return string
     */
    function get_photo_url($folder_name, $id, $image_name) {

        $photo_url = '';
        if (!empty($id) && !empty($image_name)) {
            $photo_url = UPLOAD_ABS_PATH . $folder_name . '/' . $id . '/' . $image_name;
            list($height, $width) = getimagesize($photo_url);
            if (empty($height) || empty($width)) {
                $photo_url = '';
            }
        } else {
            $photo_url = '';
        }
        return $photo_url;
    }

}


/* ----------------------------------------------- */
if (!function_exists('getUniqueToken')) {

    function getUniqueToken($length, $seeds = 'allalphanum') {
        $token = "";

// Possible seeds
        $seedings['alpha'] = 'abcdefghijklmnopqrstuvwqyz';
        $seedings['numeric'] = '0123456789';
        $seedings['alphanum'] = 'abcdefghijklmnopqrstuvwqyz0123456789';
        $seedings['allalphanum'] = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwqyz0123456789';
        $seedings['upperalphanum'] = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $seedings['uppper'] = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $seedings['alphanumspec'] = 'abcdefghijklmnopqrstuvwqyz0123456789!@#$%^*-_=+';
        $seedings['spec'] = '!@#$%^*-_=+';
        $seedings['alphacapitalnumspec'] = 'abcdefghijklmnopqrstuvwqyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789#@!*-_';
        $seedings['hexidec'] = '0123456789abcdef';
        $seedings['customupperalphanum'] = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789'; //Confusing chars like 0,O,1,I not included
// Choose seed
        if (isset($seedings[$seeds])) {
            $seeds = $seedings[$seeds];
        }


        for ($i = 0; $i < $length; $i++) {
            $token .= $seeds[crypto_rand_secure(0, strlen($seeds))];
        }
        return $token;
    }

}



/* ------------------------------------------------------------- */
if (!function_exists('crypto_rand_secure')) {

    function crypto_rand_secure($min, $max) {
        $range = $max - $min;
        if ($range < 0)
            return $min; // not so random...

        $log = log($range, 2);
        $bytes = (int) ($log / 8) + 1; // 

        $bits = (int) $log + 1; // length in bits

        $filter = (int) (1 << $bits) - 1; // set all lower bits to 1

        do {
            $rnd = hexdec(bin2hex(openssl_random_pseudo_bytes($bytes)));
            $rnd = $rnd & $filter; // discard irrelevant bits
        } while ($rnd >= $range);
        return $min + $rnd;
    }

}
/* ------------------------------------------------------------- */

if (!function_exists('get_thumb_photo_url')) {

    /**
     * Description : This function is used to send the thumb image url
     * @param type $folder_name
     * @param type $id
     * @param type $image_name
     * @return string
     */
    function get_thumb_photo_url($folder_name, $id, $image_name) {

        $photo_url = '';
        if (!empty($id) && !empty($image_name)) {
            $photo_url = UPLOAD_ABS_PATH . $folder_name . '/' . $id . '/thumb/' . $image_name;
            list($height, $width) = getimagesize($photo_url);
            if (empty($height) || empty($width)) {
                $photo_url = '';
            }
        } else {
            $photo_url = '';
        }
        return $photo_url;
    }

}

if (!function_exists("validate_email")) {

    function validate_email($email) {
        if (!preg_match("/^[A-Za-z0-9._]*\@[A-Za-z]*\.[A-Za-z]{2,3}$/", $email)) {
            return 1;
        } else {
            return 0;
        }
        return 0;
    }

}

if (!function_exists("validate_phone_number")) {

    function validate_phone_number($phone_number) {
        if (!preg_match("/^[0-9]{10}$/", $phone_number)) {
            return 1;
        } else {
            return 0;
        }
        return 0;
    }

}


if (!function_exists("send_notification_ios")) {

    function send_notification_ios($user_device_token, $body = array(), $pem_file_path = IOS_PEM_PATH, $app_is_live = APP_IS_LIVE) {

        //Setup notification message
        $body = array();
        $body['aps']['alert'] = 'My push notification message!';
        $body['aps']['sound'] = 'default';
        $body['aps']['badge'] = 1;
        $body['aps']['icon'] = 'appicon';
        $body['aps']['vibrate'] = 'true';
        $body['aps']['notification_id'] = (string) 1;

        //Setup stream (connect to Apple Push Server)
        $ctx = stream_context_create();
        stream_context_set_option($ctx, 'ssl', 'local_cert', $pem_file_path);
        if ($app_is_live == 'true') {
            $fp = stream_socket_client('ssl://gateway.push.apple.com:2195', $err, $errstr, 60, STREAM_CLIENT_CONNECT, $ctx);
        } else {
            $fp = stream_socket_client('ssl://gateway.sandbox.push.apple.com:2195', $err, $errstr, 60, STREAM_CLIENT_CONNECT, $ctx);
        }
        stream_set_blocking($fp, 0);

        if (!$fp) {
            return TRUE;
        } else {
            $apple_expiry = time() + (90 * 24 * 60 * 60); //Keep push alive (waiting for delivery) for 90 days
            $device_token = '';
            $ci = &get_instance();
            foreach ($user_device_token as $key => $value) {
                $apple_identifier = $key;
                $device_token = $value["token"];
                // check that current token is exist in bad token table or not.
                // if not exist then send push notification.
                $check_token_exists = $ci->Common_model->get_single_row(TBL_BAD_TOKENS, array('bt_id'), array('bt_device_token' => $device_token));
                if (empty($check_token_exists)) {
                    $payload = json_encode($body);
                    //Enhanced Notification
                    $msg = pack("C", 1) . pack("N", $apple_identifier) . pack("N", $apple_expiry) . pack("n", 32) . pack('H*', str_replace(' ', '', $device_token)) . pack("n", strlen($payload)) . $payload;
                    //SEND PUSH
                    fwrite($fp, $msg);
                    //usleep(500000);
                    //check_apple_error_response($fp, $device_token);
                }
            }
            // usleep(500000); //Pause for half a second. and the error message was still available to be retrieved
            //echo 'token = ' . $device_token;
            //echo 'DONE!';
            //check_apple_error_response($fp, $device_token);
            fclose($fp);
            //$res = send_feedback_request();
        }
        return true;
    }

}

if (!function_exists('send_pushwoosh_notification')) {

    function send_pushwoosh_notification($payload, $is_from_cron = false) {
        if (empty($payload['devices'])) {
            return true;
        }
        $url = 'https://cp.pushwoosh.com/json/1.3/createMessage';
        $data = array(
            "application" => PW_APPLICATION,
            "auth" => PW_AUTH,
            "notifications" => array(
                $payload
            )
        );
        $request = json_encode(['request' => $data]);
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        //curl_setopt($ch, CURLOPT_ENCODING, 'gzip, deflate');
        //curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $request);
        if($is_from_cron){
            $log = LOG_FILE_PATH . 'push_notification_log_cron' . date('d-m-Y') . ".txt";
        } else {
            $log = LOG_FILE_PATH . 'push_notification_log' . date('d-m-Y') . ".txt";
        }
        $response = curl_exec($ch);
        file_put_contents($log, "\n " . json_encode($response) . "   \n\n", FILE_APPEND);
        curl_close($ch);
        return 1;
    }
}
if (!function_exists('replace_values_in_string')) {

    function replace_values_in_string($array, $string) {
        foreach ($array as $key => $value) {

            if ($key == '{Email}' || $key == '{Password}') {
                continue;
            }
            $is_image = false;
            $height = 30;
            if($key == '{UniquePasswordImage}'){
                $is_image = true;
                $unique_id = $array['{UniqueId}'];
                $image_str = 'Unique Id: ' . $unique_id;
                $password = $array['{Password}'];
                if (!empty($password)) {
                    $height = 60;
                    $image_str .= "\n\n" . 'Password: ' . $password;
                }

            }elseif($key == '{EmailPasswordImage}'){
                $is_image = false;
                $email = $array['{Email}'];
                $image_str = 'Email: ' . $email;

                if (!empty($password)) {
                    $height = 60;
                    $image_str .= "\n\n" . 'Password: ' . $password;
                }
            }

            if ($is_image === true) {
                //ADD IMAGE GENERATE CODE HERE
                $image = new Imagick();
                $draw = new ImagickDraw();
                $pixel = new ImagickPixel('white');
                
                $image->newImage(350, $height, $pixel);
                $draw->setFontSize(15);
                $image->annotateImage($draw, 10, 20, 0, $image_str);
                $image->setImageFormat('png');
                $contents = $image->getImageBlob();

                $html = "<img src='data:image/png;base64," . base64_encode($contents) . "' />";

                $string = str_replace($key, $html, $string);
            } else {
                $string = str_replace($key, $value, $string);
            }
        }
        return $string;
    }

}

function is_send_sms_to_patient($doctor_id) {
    $arr = array();
    if(!empty($GLOBALS['ENV_VARS']['NOTIFICATION_OFF_DOCTOR_ID'])) {
        $arr = explode(',', $GLOBALS['ENV_VARS']['NOTIFICATION_OFF_DOCTOR_ID']);
    }
    $doctor_setting = doctor_sub_plan_setting($doctor_id);
    if(!in_array($doctor_id, $arr) && !empty($doctor_setting['sms_communication']) && $doctor_setting['sms_communication'] == 1)
        return true;
    return false;
}

function is_send_email_to_patient($doctor_id) {
    $arr = array();
    if(!empty($GLOBALS['ENV_VARS']['NOTIFICATION_OFF_DOCTOR_ID'])) {
        $arr = explode(',', $GLOBALS['ENV_VARS']['NOTIFICATION_OFF_DOCTOR_ID']);
    }
    $doctor_setting = doctor_sub_plan_setting($doctor_id);
    if(!in_array($doctor_id, $arr) && !empty($doctor_setting['email_communication']) && $doctor_setting['email_communication'] == 1)
        return true;
    return false;
}

function doctor_sub_plan_setting($doctor_id) {
    $CI = & get_instance();
    $sub_setting = $CI->Common_model->get_doctors_global_setting($doctor_id, 'setting_name,setting_value');
    return array_column($sub_setting, 'setting_value', 'setting_name');
}

if (!function_exists('get_display_date_time')) {
    /* convert datetime UTC to given timezone */
    function get_display_date_time($format, $datetime = '', $timezone = 'Asia/Kolkata') {
        if (empty($datetime)) {
            $datetime = date('Y-m-d H:i:s');
        }
        $dt = new DateTime($datetime);
        $tz = new DateTimeZone($timezone);
        $dt->setTimezone($tz);
        return $dt->format($format);
    }
}

function report_header($object,$objDrawing) {
    $gdImage = imagecreatefrompng($GLOBALS['ENV_VARS']['APP_DIR'].'app/images/logo.png');
    $transparent = imagecolorallocate($gdImage, 0, 0, 0);
    imagecolortransparent($gdImage, $transparent);
    $objDrawing->setName('Logo');
    $objDrawing->setDescription('Logo');
    $objDrawing->setImageResource($gdImage);
    $objDrawing->setRenderingFunction(PHPExcel_Worksheet_MemoryDrawing::RENDERING_PNG);
    $objDrawing->setMimeType(PHPExcel_Worksheet_MemoryDrawing::MIMETYPE_DEFAULT);
    $objDrawing->setCoordinates('A1');
    $objDrawing->setWorksheet($object->getActiveSheet());
    $object->getActiveSheet()->setCellValueByColumnAndRow(2, 2, 'Report Generated Date:');
    $report_date = get_display_date_time("Y-m-d H:i:s");
    $object->getActiveSheet()->setCellValueByColumnAndRow(3, 2, $report_date);
    return $object;
}

function report_header_row($object,$column, $excel_row, $field,$Width) {
    $object->getActiveSheet()->setCellValueByColumnAndRow($column, $excel_row, $field);
    $col = PHPExcel_Cell::stringFromColumnIndex($column);
    $object->getActiveSheet()->getColumnDimension($col)->setWidth($Width);
    $object->getActiveSheet()->getStyle($col.$excel_row)->getFill()->applyFromArray(array(
        'type' => PHPExcel_Style_Fill::FILL_SOLID,
        'startcolor' => array(
             'rgb' => '45546a'
        ),
    ));
    $styleArray = array(
        'font'  => array(
            'bold'  => true,
            'color' => array('rgb' => 'FFFFFF'),
        ),
        'alignment' => array(
            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
        )
    );
    $object->getActiveSheet()->getStyle($col.$excel_row)->applyFromArray($styleArray);
    $object->getActiveSheet()->getRowDimension($excel_row)->setRowHeight(25);
    return $object;
}
function doctor_sub_plan_type($val) {
    $arr = [1 => "Free", 2 => "Paid"];
    if(!empty($arr[$val]))
        return $arr[$val];
    return '';
}

if (!function_exists('upload_to_s3')) {
    function upload_to_s3($files, $upload_path) {
        //upload on s3 bucket
        include_once BUCKET_HELPER_PATH;
        return uploadfilesS3($files, $upload_path);
    }
}

function delete_file_from_s3($path) {
    include_once BUCKET_HELPER_PATH;
    return deleteSingleFileS3($path);
}

function send_communication($data) {
    if(!empty($data['phone_number']) && strlen($data['phone_number']) == 10) {
        $CI = & get_instance();
        if(!empty($data['doctor_id']) && empty($data['is_not_check_setting_flag'])) {
            $doctor_global_setting_data = $CI->Common_model->get_doctors_global_setting($data['doctor_id'],'setting_name,setting_value',['sms_communication','whatsapp_communication']);
            $setting_data = array_column($doctor_global_setting_data, 'setting_value', 'setting_name');
        } else {
            $setting_data = ['sms_communication' => "1", 'whatsapp_communication' => "1"];
        }
        if(!empty($data['is_stop_sms']) && $data['is_stop_sms'] == true)
            $setting_data['sms_communication'] = "";
        if(!empty($data['is_stop_whatsapp_sms']) && $data['is_stop_whatsapp_sms'] == true)
            $setting_data['whatsapp_communication'] = "";
        $response = [];
        /* ****************************************
        START Send message by text sms
        *******************************************/
        if(!empty($data['message']) && !empty($setting_data['sms_communication']) && $setting_data['sms_communication'] == "1") {
            $send_message = [
                'phone_number' => DEFAULT_COUNTRY_CODE . $data['phone_number'],
                'message' => $data['message']
            ];
            if(!empty($data['patient_id'])) {
                $send_message['patient_id'] = $data['patient_id'];
            }
            if(!empty($data['doctor_id'])) {
                $send_message['doctor_id'] = $data['doctor_id'];
            }
            if(!empty($data['is_sms_count'])) {
                $send_message['is_sms_count'] = $data['is_sms_count'];
            }
            if(!empty($data['is_check_sms_credit'])) {
                $send_message['is_check_sms_credit'] = $data['is_check_sms_credit'];
            }
            if(!empty($data['is_return_response'])) {
                $send_message['is_return_response'] = $data['is_return_response'];
            }
            if(!empty($data['no_global_log'])) {
                $send_message['no_global_log'] = $data['no_global_log'];
            }
            if(!empty($data['user_type'])) {
                $send_message['user_type'] = $data['user_type'];
            }
            if(!empty($data['is_promotional'])) {
                $send_message['is_promotional'] = $data['is_promotional'];
            }
            $response['sms'] = send_message_by_textlocal($send_message);
        }
        /* ****************************************
        END Send message by text sms
        *******************************************/

        /* ****************************************
        START Send message by whats app
        *******************************************/
        if(!empty($data['whatsapp_sms_body']) && !empty($setting_data['whatsapp_communication']) && $setting_data['whatsapp_communication'] == "1") {
            $CI->load->library('whatsapp');
            $whatsapp_data = [
                'user_type'=> $data['user_type'],
                'mobile'=> $data['phone_number'],
                'body'=> $data['whatsapp_sms_body']
            ];
            if(!empty($data['patient_id']))
                $whatsapp_data['patient_id'] = $data['patient_id'];
            if(!empty($data['doctor_id']))
                $whatsapp_data['doctor_id'] = $data['doctor_id'];
            if(!empty($data['is_sms_count'])) //If true then No deduct doctor's credit
                $whatsapp_data['is_promotional'] = $data['is_sms_count'];
            
            $optOutUser = $CI->Common_model->get_single_row('me_users', 'user_id', ['wa_optout' => 1, 'user_phone_number' => $data['phone_number'], 'user_status' => 1]);
            if(empty($optOutUser))
                $CI->whatsapp->send_message($whatsapp_data);
        }
        /* ****************************************
        END Send message by whats app
        *******************************************/
        return $response;
    }
}

if (!function_exists("send_message_by_vibgyortel")) {
    function send_message_by_vibgyortel($requested_data) {
        return send_message_by_textlocal($requested_data);
        /*try {
            $url = "https://apps.vibgyortel.in/client/api/sendmessage?apikey=" . VIBGYORTEL_KEY . "&senderid=" . SENDERID . "&mobiles=" . $requested_data['phone_number'] . "&sms=" . urlencode($requested_data['message']) . " ";
            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => "$url",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_HTTPHEADER => array(
                    "content-type: application/json"
                ),
            ));
            $response = json_decode(curl_exec($curl), true);
            curl_close($curl);
            if ($response['status']['error-code'] == '000') {
                $log = LOG_FILE_PATH . 'sms_log_' . date('d-m-Y') . ".txt";
                file_put_contents($log, "\n  ================ START ".date('d-m-Y H:i:s')." =====================    \n\n", FILE_APPEND);
                file_put_contents($log, "\n ".$requested_data['message']." \n" .json_encode($response)."  \n\n", FILE_APPEND);
                file_put_contents($log, "\n  ================ END =====================    \n\n", FILE_APPEND);
                return 1;
            } else {
                $log = LOG_FILE_PATH . 'sms_log_' . date('d-m-Y') . ".txt";
                file_put_contents($log, "\n  ================ START ".date('d-m-Y H:i:s')." =====================    \n\n", FILE_APPEND);
                file_put_contents($log, "\n ".$requested_data['message']." \n" .json_encode($response)."  \n\n", FILE_APPEND);
                file_put_contents($log, "\n  ================ END =====================    \n\n", FILE_APPEND);
                return 0;
            }
        } catch (ErrorException $ex) {
            return 0;
        }*/
    }
}

if (!function_exists("send_message_by_textlocal")) {
    function send_message_by_textlocal($requested_data) {
        try {
            if(!empty($requested_data['doctor_id']) && !empty($requested_data['is_sms_count']) && $requested_data['is_sms_count'] == true) {
                $sms_credit = check_sms_whatsapp_credit($requested_data['doctor_id'], 'sms');
                if($sms_credit == 0 && !empty($requested_data['is_check_sms_credit']))
                    return false;
            }
            $response = [];
            if(!empty($requested_data['phone_number'])){
                if(is_array($requested_data['phone_number'])) {
                    $mobileNo = implode(",", $requested_data['phone_number']);
                    $mobileNo = str_replace('+','',$mobileNo);
                } else {
                    $mobileNo = str_replace('+','',$requested_data['phone_number']);
                }
                if(!empty($requested_data['is_promotional'])) {
                    $textlocal_hash_key = $GLOBALS['ENV_VARS']['PROMOTIONAL_TEXTLOCAL_API_KEY'];
                    $textlocal_senderid = $GLOBALS['ENV_VARS']['PROMOTIONAL_TEXTLOCAL_SENDERID'];
                    $data = "apikey=".$textlocal_hash_key."&message=".urlencode($requested_data['message'])."&sender=".$textlocal_senderid."&numbers=".urlencode($mobileNo)."&test=".TEXTLOCAL_TEST;
                } else {
                    $text_local_api_key = $GLOBALS['ENV_VARS']['TEXT_LOCAL_API_KEY'];
                    $data = "apikey=".$text_local_api_key."&message=".urlencode($requested_data['message'])."&sender=".TEXTLOCAL_SENDERID."&numbers=".urlencode($mobileNo)."&test=".TEXTLOCAL_TEST;
                }
                $ch = curl_init('https://api.textlocal.in/send/?');
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                $response = curl_exec($ch); // This is the result from the API
                curl_close($ch);
                try {
                    if(empty($requested_data['no_global_log'])) {
                        /* [START] Make responce entry on LOG file */
                        $log = LOG_FILE_PATH . 'sms_log_' . date('d-m-Y') . ".txt";
                        file_put_contents($log, "\n  ================ START ".date('d-m-Y H:i:s')." =====================    \n\n", FILE_APPEND);
                        file_put_contents($log, "\n" .$response."  \n\n", FILE_APPEND);
                        file_put_contents($log, "\n  ================ END =====================    \n\n", FILE_APPEND);
                        /* [END] Make responce entry on LOG file */
                    }
                } catch (ErrorException $ex) {
                    
                }
                $requested_data['json_data'] = $response;
                insert_sms_log($requested_data);
                if(!empty($sms_credit) && $sms_credit > 0) {
                    $requested_data['sms_credit'] = $sms_credit;
                    deduct_sms_credit($requested_data);
                }
            }
            if(!empty($response))
                $response = json_decode($response,true);
            if(!empty($response['status']) && $response['status'] != 'success') {
                $log = LOG_FILE_PATH . 'invalid_sms_log_' . date('d-m-Y') . ".txt";
                file_put_contents($log, "\n  ================ START ".date('d-m-Y H:i:s')." =====================\n ". $requested_data['message'] ." \n", FILE_APPEND);
                file_put_contents($log, "\n" .json_encode($response)."  \n", FILE_APPEND);
                file_put_contents($log, "\n  ================ END =====================\n", FILE_APPEND);
            }
            if(!empty($requested_data['is_return_response'])) {
                return $response;
            } elseif(isset($response['status']) && $response['status'] == 'success') {
                return 1;
            } else {
                return 0;
            }
        } catch (ErrorException $ex) {
            return 0;
        }
    }
}

function deduct_sms_credit($data) {
    $update_setting_data = array(
        'setting_data' => $data['sms_credit'] - 1,
        'setting_updated_at' => date("Y-m-d H:i:s")
    );
    $update_setting_where = array(
        'setting_user_id' => $data['doctor_id'],
        'setting_type' => 7,
        'setting_data_type' => 2,
    );
    $CI = &get_instance();
    $CI->Common_model->update('me_settings', $update_setting_data, $update_setting_where);
}

function insert_sms_log($data) {
    $log_data = array(
        'sms_type' => 1,
        'created_at' => date("Y-m-d H:i:s")
    );
    if(!empty($data['doctor_id']))
        $log_data['doctor_id'] = $data['doctor_id'];
    if(!empty($data['patient_id']))
        $log_data['patient_id'] = $data['patient_id'];
    $log_data['user_type'] = 2;
    if(!empty($data['user_type']))
        $log_data['user_type'] = $data['user_type'];
    if(!empty($data['json_data']))
        $log_data['json_data'] = $data['json_data'];
    $CI = &get_instance();
    $CI->Common_model->insert('me_message_tracking', $log_data);
}

function check_sms_whatsapp_credit($doctor_id, $type = '') {
    $ci = &get_instance();
    $setting_where = array(
        'setting_user_id' => $doctor_id,
        'setting_type' => [7,8]
    );
    $get_setting_data = $ci->Common_model->get_user_setting($setting_where);
    if(empty($get_setting_data)) {
        $plan_setting = doctor_sub_plan_setting($doctor_id);
        $insert_setting_array = array();
        if(!empty($plan_setting['sms_credit'])) {
            $insert_setting_array[] = array(
                'setting_user_id' => $doctor_id,
                'setting_clinic_id' => 0,
                'setting_data' => $plan_setting['sms_credit'],
                'setting_type' => 7,
                'setting_data_type' => 2,
                'setting_created_at' => date("Y-m-d H:i:s")
            );
        }
        if(!empty($plan_setting['whatsapp_credit'])) {
            $insert_setting_array[] = array(
                'setting_user_id' => $doctor_id,
                'setting_clinic_id' => 0,
                'setting_data' => $plan_setting['whatsapp_credit'],
                'setting_type' => 8,
                'setting_data_type' => 2,
                'setting_created_at' => date("Y-m-d H:i:s")
            );
        }
        if(count($insert_setting_array) > 0) {
            $ci->Common_model->insert_multiple('me_settings', $insert_setting_array);
        }
    }
    if($type == 'sms') {
        $setting_where = array(
            'setting_user_id' => $doctor_id,
            'setting_type' => 7
        );
        $get_setting_data = $ci->Common_model->get_single_row('me_settings', '', $setting_where);
        if(!empty($get_setting_data)) {
            return (int) $get_setting_data['setting_data'];
        } else {
            return 0;
        }
    } elseif ($type == 'whatsapp') {
        $setting_where = array(
            'setting_user_id' => $doctor_id,
            'setting_type' => 8
        );
        $get_setting_data = $ci->Common_model->get_single_row('me_settings', '', $setting_where);
        if(!empty($get_setting_data)) {
            return (int) $get_setting_data['setting_data'];
        } else {
            return 0;
        }
    } else {
        $setting_where = array(
            'setting_user_id' => $doctor_id,
            'setting_type' => [7,8]
        );
        return $ci->Common_model->get_user_setting($setting_where);
    }
}

function PoundToKG($value) {
    if(!empty($value) && $value > 0) {
        $kg = $value / 2.20462;
        return number_format((float)$kg, 2, '.', '');
    } else {
        return '';
    }
}
function kgToPound($value) {
    if(!empty($value) && $value > 0) {
        $kg = $value * 2.20462;
        return number_format((float)$kg, 2, '.', '');
    } else {
        return '';
    }
}
function CelciusToFahrenhite($temperature) {
    if (!empty($temperature)) {
        $result = (($temperature * 1.8) + 32);
        return number_format((float)$result, 2, '.', '');
    }
    return '';
}
function FahrenhiteToCelcius($temperature) {
    if (!empty($temperature)) {
        $result = (($temperature - 32) / 1.8);
        return number_format((float)$result, 2, '.', '');
    }
    return '';
}
function temperature_edit($temperature, $type) {
    if($type == 2) {
        if (!empty($temperature)) {
            $result = (($temperature - 32) / 1.8);
            return number_format((float)$result, 2, '.', '');
        }
        return '';
    } else {
        return $temperature;
    }
}
function vital_edit($vital_report_created_at, $vital_report_doctor_id, $vital_report_id) {
    $CI = &get_instance();
    if($CI->patient_auth->get_user_id() == $vital_report_doctor_id && get_display_date_time("Y-m-d") == get_display_date_time("Y-m-d", $vital_report_created_at)) {
        return '<span class="icon delete-icon">
                    <a href="'.site_url("patient/edit_vital/" . encrypt_decrypt($vital_report_id, 'encrypt')) .'" title="Edit">
                        <i class="fa fa-edit"></i>
                    </a>
                </span>';
    } else {
        return '';
    }
}

function temperature_taken($val = '') {
    $arr = [
        1 => "Axillary (Armpit)",
        2 => "Oral (Mouth)",
        3 => "Tympanic (Ear)",
        4 => "Temporal (Forehead)",
        5 => "Rectal (Anus)",
        6 => "Digital"
    ];
    if(!empty($val)) {
        if(!empty($arr[$val])){
            return $arr[$val];
        } else {
            return '';
        }
    } else {
        return $arr;
    }
}
function bloodpressure_type($val = '') {
    $arr = [
        1 => "Sitting",
        2 => "Standing",
        3 => "Lying"
    ];
    if(!empty($val)) {
        if(!empty($arr[$val])){
            return $arr[$val];
        } else {
            return '';
        }
    } else {
        return $arr;
    }
}

if (!function_exists('do_upload_multiple')) {
    function do_upload_multiple($upload_path, $files, $upload_folder) {
        $CI = & get_instance();
        reset($files);
        $return_file_names = array();
        $totalfiles = count($files['name']);
        if ($totalfiles > 0) {
            if (!file_exists($upload_path)) {
                mkdir($upload_path, 0777, TRUE);
                chmod($upload_path, 0777);
            }
            $CI->load->library('upload');
            $CI->load->library('image_lib');
            for($i=0; $i < $totalfiles; $i++) {
                $_FILES['file']['name'] = $files['name'][$i];
                $_FILES['file']['type'] = $files['type'][$i];
                $_FILES['file']['tmp_name'] = $files['tmp_name'][$i];
                $_FILES['file']['error'] = $files['error'][$i];
                $_FILES['file']['size'] = $files['size'][$i];
                $file_name = basename($upload_path);
                $config['upload_path'] = $upload_path;
                $config['allowed_types'] = 'jpg|jpeg|png|gif|pdf';
                $config['overwrite'] = FALSE;
                $config['file_name'] = $file_name . "_" . uniqid();
                $CI->upload->initialize($config);
                if ($CI->upload->do_upload('file')) {
                    $upload_array = array();
                    $upload_array = $CI->upload->data();
                    if (is_array($upload_array) && count($upload_array) > 0) {
                        $file_path = $upload_path . "/" . $upload_array['file_name'];
                        chmod($file_path, 0777);
                        include_once BUCKET_HELPER_PATH;
                        $upload_flag = uploadfilesS3($file_path, $upload_folder . "/" . $upload_array['file_name']);
                        if ($upload_flag) {
                            if(strtolower($upload_array['file_ext']) != ".pdf") {
                                $config = array(
                                    'source_image'      => $file_path,
                                    'new_image'         => $upload_path,
                                    'maintain_ratio'    => true,
                                    'width'             => 100,
                                    'height'            => 110
                                );
                                $CI->image_lib->initialize($config);
                                $CI->image_lib->resize();
                                $arr = explode(".", $upload_array['file_name']);
                                $thumb_file_name = $arr[0] . "_thumb." . $arr[1];
                                $upload_flag = uploadfilesS3($file_path, $upload_folder . "/" . $thumb_file_name);
                            }
                            unlink($file_path);
                            $return_file_names[$i] = $upload_array['file_name'];
                        }
                    }
                } else {
                    // $error = array('error' => $CI->upload->display_errors());
                    // print_r($error);
                }
            }
        }
        return $return_file_names;
    }
}

function get_file_size($url) {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($ch, CURLOPT_HEADER, TRUE);
    curl_setopt($ch, CURLOPT_NOBODY, TRUE);
    $data = curl_exec($ch);
    $size = curl_getinfo($ch, CURLINFO_CONTENT_LENGTH_DOWNLOAD);
    curl_close($ch);
    $kb = $size/1000;
    return round($kb);
}

if (!function_exists('encrypt_decrypt')) {
    function encrypt_decrypt($string, $action) {
        $output = false;
        $encrypt_method = "AES-256-CBC";
        $secret_key = 'medsign secret key';
        $secret_iv = 'medsign secret iv';
        $key = hash('sha256', $secret_key);
        $iv = substr(hash('sha256', $secret_iv), 0, 16);
        if ($action == 'encrypt') {
            $output = openssl_encrypt($string, $encrypt_method, $key, 0, $iv);
            $output = base64_encode($output);
        } else if ($action == 'decrypt') {
            $output = openssl_decrypt(base64_decode($string), $encrypt_method, $key, 0, $iv);
        }
        return $output;
    }
}

if(!function_exists('get_image_thumb')) {
    function get_image_thumb($file_url) {
        if (!empty($file_url)) {
            include_once BUCKET_HELPER_PATH;
            $arr = json_decode($file_url,true);
            if(!empty($arr) && is_array($arr)) {
                $thumb_file_url = get_thumb_filename($arr['path']);
                if ($arr['type'] == 1) {
                    $thumb_file_name = $thumb_file_url;
                    $result = checkResource($thumb_file_name);
                    if(!$result) {
                        if($arr['alternate'] && file_exists(UPLOAD_REL_PATH.'/'.$thumb_file_name)) {
                            return $GLOBALS['ENV_VARS']['DOCUMENT_LOAD_SERVER_URL'].'uploads/'.$thumb_file_name;
                        }
                    }
                    if($result) {
                        return $GLOBALS['ENV_VARS']['DOCUMENT_LOAD_S3_URL'].$arr['name'].'/'.$thumb_file_url;
                    } else {
                        return get_file_full_path($file_url);
                    }
                } elseif($arr['type'] == 2) {
                    $thumb_file_name = $thumb_file_url;
                    if(file_exists(UPLOAD_REL_PATH.'/'.$thumb_file_name)){
                        return $GLOBALS['ENV_VARS']['DOCUMENT_LOAD_SERVER_URL'].$arr['name'].'/'.$thumb_file_url;
                    } else {
                        return get_file_full_path($file_url);
                    }
                }
            } else {
                $thumb_file_url = get_thumb_filename($file_url);
                $thumb_file_name = substr($thumb_file_url, strrpos($thumb_file_url, BUCKET_NAME_DOWNLOAD."/"));
                $thumb_file_name = str_replace(BUCKET_NAME_DOWNLOAD."/", "", $thumb_file_name);
                $result = checkResource($thumb_file_name);
                if($result) {
                    return $thumb_file_url;
                }
                return $file_url;
            }
        }
        return NULL;
    }
}
function clinic_address($data) {
    if(strrpos(strtolower($data['address_name']), strtolower($data['city_name'])) !== false) {
        $clinic_address = $data['address_name'];
    } else {
        $clinic_address = $data['address_name_one'];
        if(!empty($data['address_name']) && strrpos(strtolower($clinic_address), strtolower($data['address_name'])) === false) {
            $clinic_address .= ', '.$data['address_name'];
        }
        if(!empty($data['address_locality']) && strrpos(strtolower($clinic_address), strtolower($data['address_locality'])) === false) {
            $clinic_address .= ', '.$data['address_locality'];
        }
        $clinic_address .= ', '.$data['city_name'].', '.$data['state_name'].', '.$data['address_pincode'];
    }
    return $clinic_address;
}
function create_cache_sub_dir($path_key) {
    $path = DB_CACHE_PATH[$path_key];
    if(!file_exists($path)) {
        if (strpos($path, '/') !== false) {
            $folders = explode('/', $path);
            unset($folders[count($folders) - 1]);
            $dir = implode('/', $folders);
            mkdir($dir, 0777, TRUE);
            chmod($dir, 0777);
        }
    }
    $ci = &get_instance();
    $ci->config->set_item('cache_path', $path);
}

function get_assign_uas7($patient_id) {
    $CI = & get_instance();
    return $CI->patient->get_patient_uas7_analytics($patient_id);
}

function get_payment_status($val) {
    $arr = array(
        '1' => 'Success',
        '2' => 'Pending',
        '3' => 'Fail'
    );
    if(!empty($val) && !empty($arr[$val]))
        return $arr[$val];
    return '';
}
function patient_address($data) {
    if(strrpos(strtolower($data['address_name']), strtolower($data['city_name'])) !== false) {
        $patient_address = $data['address_name'];
        if(!empty($data['address_pincode']))
            $patient_address .= ', '.$data['address_pincode'];
    } else {
        $patient_address = $data['address_name_one'];
        if(!empty($data['address_name']) && strrpos(strtolower($patient_address), strtolower($data['address_name'])) === false) {
            $patient_address .= ', '.$data['address_name'];
        }
        if(!empty($data['address_locality']) && strrpos(strtolower($patient_address), strtolower($data['address_locality'])) === false) {
            $patient_address .= ', '.$data['address_locality'];
        }
        if(!empty($data['city_name']))
            $patient_address .= ', '.$data['city_name'];
        if(!empty($data['state_name']))
            $patient_address .= ', '.$data['state_name'];
        if(!empty($data['address_pincode']))
            $patient_address .= ', '.$data['address_pincode'];
    }
    return $patient_address;
}
function generate_uas7_score($uas7_para_data) {
    $wheal_value_arr = array_column($uas7_para_data, 'wheal_value');
    $pruritus_value_arr = array_column($uas7_para_data, 'pruritus_value');
    if(count($uas7_para_data) == 7) {
        $total = array_sum($wheal_value_arr) + array_sum($pruritus_value_arr);
    } else {
        $total = '';
    }
    return $total;
}

function s3_file_exist($filename) {
    include_once BUCKET_HELPER_PATH;
    return checkResource($filename);
}

function daily_graph_date_label($date_label) {
    if(count($date_label) >= 14) {
        $dates = [];
        foreach ($date_label as $key => $value) {
            if($key==0 || count($date_label) == $key+1 || ($key+1) % 7 == 0)
                $dates[] = $value;
            else
                $dates[] = "";
        }
        return $dates;
    }
    return $date_label;
}
function relation_map($val) {
    $relation_arr = array(
        1 => 'Mother',
        2 => 'Father',
        3 => 'Brother',
        4 => 'Sister',
        5 => 'Wife',
        6 => 'Son',
        7 => 'Daughter',
        8 => 'Husband',
        9 => 'Grandparent',
        10 => 'Grandchild',
        11 => 'Other Relative',
        12 => 'Others'
    );
    if(!empty($relation_arr[$val])) {
        return $relation_arr[$val];
    }
    return '';
}
function get_relation() {
    $relation = array(
        ['id' => 1, 'name' => 'Mother'],
        ['id' => 2, 'name' => 'Father'],
        ['id' => 3, 'name' => 'Brother'],
        ['id' => 4, 'name' => 'Sister'],
        ['id' => 5, 'name' => 'Wife'],
        ['id' => 6, 'name' => 'Son'],
        ['id' => 7, 'name' => 'Daughter'],
        ['id' => 8, 'name' => 'Husband'],
        ['id' => 9, 'name' => 'Grandparent'],
        ['id' => 10, 'name' => 'Grandchild'],
        ['id' => 11, 'name' => 'Other Relative'],
        ['id' => 12, 'name' => 'Others']
    );
    return $relation;
}

function get_sub_plan_validity($plan_type, $plan_validity) {
    if($plan_type == 1) {
        return $plan_validity . " Month" . (($plan_validity > 1) ? "s" : "");
    } elseif($plan_type == 2) {
        return $plan_validity . " Year" . (($plan_validity > 1) ? "s" : "");
    }
}
function create_shorturl($url, $isCron = false) {
    $text_local_api_key = $GLOBALS['ENV_VARS']['TEXT_LOCAL_API_KEY'];
    $data = "apikey=".$text_local_api_key."&url=" . $url;
    $ch = curl_init('https://api.textlocal.in/create_shorturl/?' . $data);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    curl_close($ch);
    $result = json_decode($response);
    if(!empty($result->status) && $result->status == "success") {
        if($isCron)
            $log = LOG_FILE_PATH . 'shorturl_log_cron_' . date('d-m-Y') . ".txt";
        else
            $log = LOG_FILE_PATH . 'shorturl_log_' . date('d-m-Y') . ".txt";
        $log_content = "\n========= START ".date('d-m-Y H:i:s')." ==========\n";
        $log_content .= $url."\n";
        $log_content .= $response."\n";
        $log_content .= "========= END =======\n";
        file_put_contents($log, $log_content, FILE_APPEND);
        return $result->shorturl;
    } else {
        if($isCron)
            $log = LOG_FILE_PATH . 'shorturl_error_log_cron_' . date('d-m-Y') . ".txt";
        else
            $log = LOG_FILE_PATH . 'shorturl_error_log_' . date('d-m-Y') . ".txt";
        $log_content = "\n========= START ".date('d-m-Y H:i:s')." ==========\n";
        $log_content .= $url."\n";
        $log_content .= $response."\n";
        $log_content .= "========= END =======\n";
        file_put_contents($log, $log_content, FILE_APPEND);
        return $url;
    }
}
function short_clinic_name($clinic_name) {
    if(strlen($clinic_name) > 30) {
        return substr($clinic_name, 0, 25)."...";
    } else {
        return $clinic_name;
    }
}
function send_firebase_notification($data) {
    $request_data = array("to" => $data['device_token'],
        "notification" => array(
            "title" => $data['title'], 
            "body" => $data['body'],
            "icon" => "push-icon.png", 
            "click_action" => $data['click_action']
        )
    );
    $data_string = json_encode($request_data);
    $headers = array(
        'Authorization: key=' . FIREBASE_API_ACCESS_KEY, 
        'Content-Type: application/json'
    );                                                                                 
    $ch = curl_init();  
    curl_setopt( $ch,CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send' );
    curl_setopt( $ch,CURLOPT_POST, true );
    curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers );
    curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );
    curl_setopt( $ch,CURLOPT_POSTFIELDS, $data_string);
    $response = curl_exec($ch);
    curl_close ($ch);
    $log = LOG_FILE_PATH . 'firebase_notification_log' . date('d-m-Y') . ".txt";
    file_put_contents($log, "\n UTC: ". date('Y-m-d H:i:s')." => " . $response . "\n", FILE_APPEND);
    return $response;
}
function get_frequency($time) {
    if(strtotime($time) >= strtotime("05:00") && strtotime($time) <= strtotime("11:59")) {
        return 'Morning';
    } elseif(strtotime($time) >= strtotime("12:00") && strtotime($time) <= strtotime("16:59")) {
        return 'Afternoon';
    } elseif(strtotime($time) >= strtotime("17:00") && strtotime($time) <= strtotime("20:59")) {
        return 'Evening';
    } elseif(strtotime($time) >= strtotime("21:00")) {
        return 'Bedtime';
    } else {
        return '';
    }
}
function get_file_full_path($path) {
    $arr = json_decode($path, true);
    if(!empty($arr) && is_array($arr)){
        if($arr['type'] == 1){
            include_once BUCKET_HELPER_PATH;
            $result = checkResource($arr['path'], $arr['name']);
            if($result){
                return $GLOBALS['ENV_VARS']['DOCUMENT_LOAD_S3_URL'].$arr['name'].'/'.$arr['path'];
            } else {
                if($arr['alternate'] && file_exists(UPLOAD_REL_PATH.'/'.$arr['path'])) {
                    return $GLOBALS['ENV_VARS']['DOCUMENT_LOAD_SERVER_URL'].'uploads/'.$arr['path'];
                }
                return ASSETS_PATH . 'web/img/no-image-placeholder.png';
            }
        } elseif($arr['type'] == 2) {
            if(file_exists(UPLOAD_REL_PATH.'/'.$arr['path'])) {
                return $GLOBALS['ENV_VARS']['DOCUMENT_LOAD_SERVER_URL'].$arr['name'].'/'.$arr['path'];
            } else {
                return ASSETS_PATH . 'web/img/no-image-placeholder.png';
            }
        }
    }
    return $path;
}
function get_file_json_detail($path) {
    $data = [
        'path' => $path,
        'type' => $GLOBALS['ENV_VARS']['FILE_UPLOAD_TYPE'],
        'name' => $GLOBALS['ENV_VARS']['FILE_UPLOAD_NAME'],
        'alternate' => (IS_S3_UPLOAD && IS_SERVER_UPLOAD) ? true : false
    ];
    return json_encode($data);
}
function is_patient_reminder_access($user, $careGiver) {
    if(!empty($user)) {
        $date = $user->user_plan_expiry_date;
        if(!empty($user->setting_value) ||  (!empty($date) && $date >= get_display_date_time("Y-m-d"))) {
            return true;
        }
        if(!empty($careGiver[$user->user_id])) {
            foreach ($careGiver[$user->user_id] as $key => $value) {
                $date = $value->user_plan_expiry_date;
                if(!empty($value->setting_value) ||  (!empty($date) && $date >= get_display_date_time("Y-m-d"))) {
                    return true;
                    break;
                }
            }
        }
    }
    return false;
}
?>
