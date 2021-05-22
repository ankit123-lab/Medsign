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
        $special_char = "!@#$%^&*()_-=+;:,.?";
        $password .= substr(str_shuffle($special_char), 0, 1);
        return $password;
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
        $total_time_sec = ($hr_time_hr * 3600) + ($hr_time_mnt * 60) + ($hr_time_sec);
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
        } else if ($format == 's') {
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
        $bytes = (int) ($log / 8) + 1; // length in bytes
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
        $seedings['customupperalphanum'] = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789'; //Confusing chars like 0,O,1,I not included
        // Choose seed
        if (isset($seedings[$seeds])) {
            $seeds = $seedings[$seeds];
        }

        // Seed generator
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
        $filetType = 3; //File other than image and video
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
 * @return    Array 
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
            'orange', 'light-orange', 'orange2', 'purple', 'pink', 'pink2', 'brown', 'grey', 'light-grey');
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
                $retval .= (($time > 1) ? $key . 's' : $key);
                $granularity--;
            }
            if ($granularity == '0') {
                break;
            }
        }
        if ($ago)
            return $retval . ' ago';
        else
            return $retval;
    }

}

if (!function_exists('address_to_latlng')) {

    function address_to_latlng($address = '', $city = '', $state = '', $country, $zip = '') {
        $latlng = array(
            'latitude' => '',
            'longitude' => '',
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
            $latlng['latitude'] = $data->results[0]->geometry->location->lat;
            $latlng['longitude'] = $data->results[0]->geometry->location->lng;
        }

        return $latlng;
    }

}

if (!function_exists('get_name_title')) {

    function get_name_title($titleId) {
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

        $age = (date("md", date("U", mktime(0, 0, 0, $birthDate[1], $birthDate[2], $birthDate[0]))) > date("md") ? ((date("Y") - $birthDate[0]) - 1) : (date("Y") - $birthDate[0]));
        return $age;
    }

}

if (!function_exists('current_time')) {

    function current_time($format = 'Y-m-d H:i:s') {
        return date($format);
    }

}

//code for photo upload
//code for photo upload


if (!function_exists('do_upload')) {

    function do_upload($upload_path, $file) {

        $CI = & get_instance();
        reset($file);
        $file_object = key($file);

        $return_file_names = "";

        //if single file is selected
        if (!is_array($file[$file_object]['name'])) {

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
            $totalfiles = count($file[$file_object]['name']);

            for ($file_index = 0; $file_index < $totalfiles; $file_index++) {
                $_FILES[$file_object . $file_index]['name'] = $file[$file_object]['name'][$file_index];
                $_FILES[$file_object . $file_index]['size'] = $file[$file_object]['size'][$file_index];
                $_FILES[$file_object . $file_index]['type'] = $file[$file_object]['type'][$file_index];
                $_FILES[$file_object . $file_index]['tmp_name'] = $file[$file_object]['tmp_name'][$file_index];
                $_FILES[$file_object . $file_index]['error'] = $file[$file_object]['error'][$file_index];

                if ($_FILES[$file_object . $file_index]['error'] != 0 || empty($_FILES[$file_object . $file_index]['name'])) {
                    unset($_FILES[$file_object . $file_index]);
                }
            }
        }

        if (!isset($totalfiles)) {
            $totalfiles = 1;
        }


        //check upload path folder exist or not?

        if (!file_exists($upload_path)) {
            mkdir($upload_path, 0777, TRUE);
            chmod($upload_path, 0777);
        }

        $CI->load->library('upload');
        $CI->load->library('image_lib');
        $return_file_names = array();
        for ($file_index = 0; $file_index < $totalfiles; $file_index++) {
            $file_name = basename($upload_path);

            $config['upload_path'] = $upload_path;
            $config['allowed_types'] = "*";
            $config['overwrite'] = FALSE;
            $config['file_name'] = $file_name . "_" . uniqid();
            
            $CI->upload->initialize($config);
            if ($CI->upload->do_upload($file_object . $file_index)) {
                $upload_array = array();
                $upload_array = $CI->upload->data();
                chmod($upload_path . "/" . $upload_array['file_name'], 0777);

                if (is_array($upload_array) && count($upload_array) > 0) {
                    //thumb image
                    //check thumb folder exist or not?
                    $thumb_path = $upload_path . "/thumb";
                    if (!file_exists($thumb_path)) {
                        mkdir($thumb_path);
                        chmod($thumb_path, 0777);
                    }

                    $return_file_names[] = $upload_array['file_name'];

                    if ((isset($upload_array['image_width']) && $upload_array['image_width'] > 600) || (isset($upload_array['image_height']) && $upload_array['image_height'] > 600)) {
                        $config = array();
                        $config['image_library'] = 'gd2';
                        $config['source_image'] = $upload_path . "/" . $upload_array['file_name'];
                        $config['new_image'] = $thumb_path . "/" . $upload_array['file_name'];
                        $config['maintain_ratio'] = TRUE;
                        $config['width'] = 600;
                        $config['height'] = 600;

                        $CI->image_lib->clear();
                        $CI->image_lib->initialize($config);
                        if ($CI->image_lib->resize()) {
                            chmod($thumb_path . '/' . $upload_array['file_name'], 0777);
                        }
                    }
                }
            } else {
//                _px($CI->upload->display_errors());
                return FALSE;
            }
        }
        return $return_file_names;
    }

}

//function for post image upload

if (!function_exists('do_upload_post')) {

    function do_upload_post($upload_path, $file) {
        $CI = & get_instance();
        reset($file);
        $file_object = key($file);

        $return_file_names = "";

        //if single file is selected
        if (!is_array($file[$file_object]['name'])) {

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

        if (!file_exists($upload_path)) {
            mkdir($upload_path, 0777, TRUE);
            chmod($upload_path, 0777);
        }

        $CI->load->library('upload');
        $CI->load->library('image_lib');

        for ($file_index = 0; $file_index < $totalfiles; $file_index++) {


            $file_name = basename($upload_path);

            $config['upload_path'] = $upload_path;
            $config['allowed_types'] = "*";
            $config['overwrite'] = FALSE;
            $config['file_name'] = $file_name . "_" . time();



            $CI->upload->initialize($config);
            if ($CI->upload->do_upload($file_object . $file_index)) {
                $upload_array = array();
                $upload_array = $CI->upload->data();
                chmod($upload_path . "/" . $upload_array['file_name'], 0777);

                if (is_array($upload_array) && count($upload_array) > 0) {


                    //thumb image
                    //check thumb folder exist or not?
                    $thumb_path = $upload_path . "/thumb";
                    if (!file_exists($thumb_path)) {
                        mkdir($thumb_path);
                        chmod($thumb_path, 0777);
                    }

                    $return_file_names = $upload_array['file_name'];

                    if ((isset($upload_array['image_width']) && $upload_array['image_width'] > 600) || (isset($upload_array['image_height']) && $upload_array['image_height'] > 600)) {

                        $config = array();
                        $config['image_library'] = 'gd2';
                        $config['source_image'] = $upload_path . "/" . $upload_array['file_name'];
                        $config['new_image'] = $thumb_path . "/" . $upload_array['file_name'];
                        $config['maintain_ratio'] = TRUE;
                        $config['width'] = 600;
                        $config['height'] = 600;

                        $CI->image_lib->clear();
                        $CI->image_lib->initialize($config);
                        if ($CI->image_lib->resize()) {
                            chmod($thumb_path . '/' . $upload_array['file_name'], 0777);
                        }
                    }
                }
            } else {

                //   _px($CI->upload->display_errors());
//                return FALSE;
            }
        }
        return $return_file_names;
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


if (!function_exists("send_feedback_request")) {

    //FUNCTION to check if there is an error response from Apple
    //Returns TRUE if there was an error and FALSE if there was not
    function send_feedback_request() {
        //connect to the APNS feedback servers
        //make sure you're using the right dev/production server & cert combo!
        $stream_context = stream_context_create();
        stream_context_set_option($stream_context, 'ssl', 'local_cert', IOS_PEM_PATH);
        if (APP_IS_LIVE == 'true') {
            $apns = stream_socket_client('ssl://feedback.push.apple.com:2196', $errcode, $errstr, 60, STREAM_CLIENT_CONNECT, $stream_context);
        } else {
            $apns = stream_socket_client('ssl://feedback.sandbox.push.apple.com:2196', $errcode, $errstr, 60, STREAM_CLIENT_CONNECT, $stream_context);
        }

//        echo '<pre>';
//        print_r($apns);exit;
        if (!$apns) {
            echo "ERROR $errcode: $errstr\n";
            return;
        }
        $feedback_tokens = array();
        //and read the data on the connection:
        while (!feof($apns)) {
            print_r($apns);
            $data = fread($apns, 38);
            echo '<pre>';
            print_r($data);
            exit;
            if (strlen($data)) {
                $feedback_tokens[] = unpack("N1timestamp/n1length/H*devtoken", $data);
            }
        }
        echo '<pre>';
        print_r($feedback_tokens);
        exit;
        fclose($apns);
        echo '<pre>';
        print_r($feedback_tokens);
        exit;
        return $feedback_tokens;
    }

}
if (!function_exists("check_apple_error_response")) {

    //FUNCTION to check if there is an error response from Apple
    //Returns TRUE if there was an error and FALSE if there was not
    function check_apple_error_response($fp, $device_token = null) {
        //echo 'in';
        usleep(500000);
        //byte1=always 8, byte2=StatusCode, bytes3,4,5,6=identifier(rowID). Should return nothing if OK.
        $apple_error_response = fread($fp, 6);
        echo '<pre>';
        print_r($apple_error_response);
        exit;
        //NOTE: Make sure you set stream_set_blocking($fp, 0) or 
        //else fread will pause your script and wait forever when there is no response to be sent.

        if ($apple_error_response) {
            //unpack the error response (first byte 'command" should always be 8)
            $error_response = unpack('Ccommand/Cstatus_code/Nidentifier', $apple_error_response);
            if ($error_response['status_code'] == '0') {
                $error_response['status_code'] = '0-No errors encountered';
            } else if ($error_response['status_code'] == '1') {
                $error_response['status_code'] = '1-Processing error';
            } else if ($error_response['status_code'] == '2') {
                $error_response['status_code'] = '2-Missing device token';
            } else if ($error_response['status_code'] == '3') {
                $error_response['status_code'] = '3-Missing topic';
            } else if ($error_response['status_code'] == '4') {
                $error_response['status_code'] = '4-Missing payload';
            } else if ($error_response['status_code'] == '5') {
                $error_response['status_code'] = '5-Invalid token size';
            } else if ($error_response['status_code'] == '6') {
                $error_response['status_code'] = '6-Invalid topic size';
            } else if ($error_response['status_code'] == '7') {
                $error_response['status_code'] = '7-Invalid payload size';
            } else if ($error_response['status_code'] == '8') {
                $error_response['status_code'] = '8-Invalid token';
            } else if ($error_response['status_code'] == '255') {
                $error_response['status_code'] = '255-None (unknown)';
            } else {
                $error_response['status_code'] = $error_response['status_code'] . '-Not listed';
            }
            // generate proper device token error message.
            $error_response_message = 'Response Command:<b>' . $error_response['command'] . '</b>, Identifier:<b>' . $error_response['identifier'] . '</b>, Status:<b>' . $error_response['status_code'] . '</b>';
            if (!empty($error_response)) {
                $ci = &get_instance();
                $check_token_exists = $ci->Common_model->get_single_row(TBL_BAD_TOKENS, '*', array('bt_device_token' => $device_token));
                if (empty($check_token_exists)) {
                    $insert_array = array();
                    $insert_array['bt_device_token'] = $ci->Common_model->escape_data($device_token);
                    $insert_array['bt_response_message'] = json_encode($ci->Common_model->escape_data($error_response_message));
                    $insert_array['bt_device_type'] = 'ios';
                    $insert_array['bt_created_date'] = time();
                    $ci->Common_model->insert(TBL_BAD_TOKENS, $insert_array);
                }
            }
            return true;
        }
        return false;
    }

}
if (!function_exists("send_notification_ios_old")) {

    function send_notification_ios_old($userdeviceToken, $body = array(), $pem_file_path, $app_is_live = '1') {

        // End of Configurable Items
        $payload = json_encode($body);
        $ctx = stream_context_create();
        $filename = $pem_file_path;
        if (!file_exists($filename)) {
            return true;
        }
        /*    stream_context_set_option($ctx, 'ssl', 'local_cert', $filename);

          // assume the private key passphase was removed.
          //    $fp = stream_socket_client('ssl://gateway.sandbox.push.apple.com:2195', $err, $errstr, 60, STREAM_CLIENT_CONNECT, $ctx);
          $fp = stream_socket_client('ssl://gateway.push.apple.com:2195', $err, $errstr, 60, STREAM_CLIENT_CONNECT, $ctx);
          if (!$fp)
          {
          return "Failed to connect $err $errstr";
          }
          else
          { */
        if (is_array($userdeviceToken) && count($userdeviceToken) > 0) {
            foreach ($userdeviceToken as $key => $token_rec_id) {
                stream_context_set_option($ctx, 'ssl', 'local_cert', $filename);
                if ($app_is_live == '1')
                    $fp = stream_socket_client('ssl://gateway.push.apple.com:2195', $err, $errstr, 60, STREAM_CLIENT_CONNECT, $ctx);
                else
                    $fp = stream_socket_client('ssl://gateway.sandbox.push.apple.com:2195', $err, $errstr, 60, STREAM_CLIENT_CONNECT, $ctx);

                if (!$fp) {
                    continue;
                } else {
                    //        $token_rec_id = '';
                    //       $token_rec_id =  $value['IOS_TOKEN_UDID_ID'];
                    if ($token_rec_id != '') {
                        $msg = chr(0) . pack("n", 32) . pack('H*', str_replace(' ', '', $token_rec_id)) . pack("n", strlen($payload)) . $payload;
                        fwrite($fp, $msg);
                    }
                }
                fclose($fp);
            }
        } else {
            stream_context_set_option($ctx, 'ssl', 'local_cert', $filename);

            // assume the private key passphase was removed.
            //    $fp = stream_socket_client('ssl://gateway.sandbox.push.apple.com:2195', $err, $errstr, 60, STREAM_CLIENT_CONNECT, $ctx);
            //    $fp = stream_socket_client('ssl://gateway.push.apple.com:2195', $err, $errstr, 60, STREAM_CLIENT_CONNECT, $ctx);

            if ($app_is_live == '1')
                $fp = stream_socket_client('ssl://gateway.push.apple.com:2195', $err, $errstr, 60, STREAM_CLIENT_CONNECT, $ctx);
            else
                $fp = stream_socket_client('ssl://gateway.sandbox.push.apple.com:2195', $err, $errstr, 60, STREAM_CLIENT_CONNECT, $ctx);

            if (!$fp) {
                return false;
                //    return "Failed to connect $err $errstr";
            } else {
                $token_rec_id = $userdeviceToken;
                if ($token_rec_id != '') {
                    $msg = chr(0) . pack("n", 32) . pack('H*', str_replace(' ', '', $token_rec_id)) . pack("n", strlen($payload)) . $payload;
                    fwrite($fp, $msg);
                }
            }
            fclose($fp);
        }
        return true;
        // END CODE FOR PUSH NOTIFICATIONS TO ALL USERS
    }

}

if (!function_exists('get_photo_url')) {

    /**
     * Description : This function is used to send the image url
     * @param type $type
     * @param type $id
     * @param type $photo_name
     * @return string
     */
    function get_photo_url($type, $id, $photo_name) {

        $photo_url = '';
        if (!empty($id) && !empty($photo_name)) {
            $photo_url = UPLOAD_ABS_PATH . $type . '/' . $id . '/' . $photo_name;
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

if (!function_exists('get_thumb_photo_url')) {

    /**
     * Description : This function is used to send the thumb image url
     * @param type $type
     * @param type $id
     * @param type $photo_name
     * @return string
     */
    function get_thumb_photo_url($type, $id, $photo_name) {

        $photo_url = '';
        if (!empty($id) && !empty($photo_name)) {
            $photo_url = UPLOAD_ABS_PATH . $type . '/' . $id . '/thumb/' . $photo_name;
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
if (!function_exists('convert_to_cm')) {

    /**
     * Converts a height value given in feet/inches to cm
     *
     * @param int $feet
     * @param int $inches
     * @return int
     */
    function convert_to_cm($feet, $inches = 0) {


        $inches = ($feet * 12) + $inches;
        $number = (float) $inches / 0.393701;
        return number_format($number, 2, '.', '');
    }

}

if (!function_exists('do_upload_multiple')) {

    function do_upload_multiple($upload_path, $files, $upload_folder) {
        $CI = & get_instance();
        reset($files);
        $return_file_names = array();
        $totalfiles = count($files);

        if ($totalfiles > 0) {
            if (!file_exists($upload_path)) {
                mkdir($upload_path, 0777, TRUE);
                chmod($upload_path, 0777);
            }

            $CI->load->library('upload');
            $CI->load->library('image_lib');

            foreach ($files as $key => $file) {

                $file_name = basename($upload_path);
                $config['upload_path'] = $upload_path;
                $config['allowed_types'] = "*";
                $config['overwrite'] = FALSE;
                $config['file_name'] = $file_name . "_" . uniqid();

                $CI->upload->initialize($config);

                if ($CI->upload->do_upload($key)) {

                    $upload_array = array();
                    $upload_array = $CI->upload->data();

                    if (is_array($upload_array) && count($upload_array) > 0) {
                        $file_path = $upload_path . "/" . $upload_array['file_name'];
                        //$return_file_names[$key] = $upload_array['file_name'];
                        chmod($file_path, 0777);

                        //upload on s3 bucket
                        include_once BUCKET_HELPER_PATH;
                        $upload_flag = uploadfilesS3($file_path, $upload_folder . "/" . $upload_array['file_name']);
                        if ($upload_flag) {
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
                            unlink($file_path);
                            $return_file_names[$key] = $upload_array['file_name'];
                        }
                    }
                } else {
                    continue;
                }
            }
        }

        return $return_file_names;
    }

}

if (!function_exists('do_upload_multiple2')) {

    function do_upload_multiple2($upload_path, $files, $upload_folder) {

        $CI = & get_instance();
        reset($files);
        $return_file_names = array();
        $totalfiles = count($files);
        $file_object = key($files);

        if ($totalfiles > 0) {
            if (!file_exists($upload_path)) {
                mkdir($upload_path, 0777, TRUE);
                chmod($upload_path, 0777);
            }

            $CI->load->library('upload');
            $CI->load->library('image_lib');


            if (!is_array($files[$file_object]['name'])) {

                $file_index = 0;
                $_FILES[$file_object . $file_index]['name'] = $files[$file_object]['name'];
                $_FILES[$file_object . $file_index]['size'] = $files[$file_object]['size'];
                $_FILES[$file_object . $file_index]['type'] = $files[$file_object]['type'];
                $_FILES[$file_object . $file_index]['tmp_name'] = $files[$file_object]['tmp_name'];
                $_FILES[$file_object . $file_index]['error'] = $files[$file_object]['error'];

                if ($_FILES[$file_object . $file_index]['error'] != 0) {
                    unset($_FILES[$file_object . $file_index]);
                }
                unset($_FILES[$file_object]);
            } else {
                $totalfiles = count($files[$file_object]['name']);

                for ($file_index = 0; $file_index <= $totalfiles; $file_index++) {
                    if (isset($files[$file_object]['name'][$file_index])) {
                        $_FILES[$file_object . $file_index]['name'] = $files[$file_object]['name'][$file_index];
                        $_FILES[$file_object . $file_index]['size'] = $files[$file_object]['size'][$file_index];
                        $_FILES[$file_object . $file_index]['type'] = $files[$file_object]['type'][$file_index];
                        $_FILES[$file_object . $file_index]['tmp_name'] = $files[$file_object]['tmp_name'][$file_index];
                        $_FILES[$file_object . $file_index]['error'] = $files[$file_object]['error'][$file_index];

                        if ($_FILES[$file_object . $file_index]['error'] != 0) {
                            unset($_FILES[$file_object . $file_index]);
                        }
                    }
                }
            }

            for ($file_index = 0; $file_index <= $totalfiles; $file_index++) {
                $file_name = basename($upload_path);
                $config['upload_path'] = $upload_path;
                $config['allowed_types'] = "*";
                $config['overwrite'] = FALSE;
                $config['file_name'] = $file_name . "_" . time();
                if (isset($_FILES[$file_object . $file_index])) {
                    $CI->upload->initialize($config);
                    if ($CI->upload->do_upload($file_object . $file_index)) {
                        $upload_array = array();
                        $upload_array = $CI->upload->data();
                        chmod($upload_path . "/" . $upload_array['file_name'], 0777);

                        if (is_array($upload_array) && count($upload_array) > 0) {

                            $file_path = $upload_path . "/" . $upload_array['file_name'];
                            //$return_file_names[$key] = $upload_array['file_name'];
                            chmod($file_path, 0777);

                            //upload on s3 bucket
                            include_once BUCKET_HELPER_PATH;
                            $upload_flag = uploadfilesS3($file_path, $upload_folder . "/" . $upload_array['file_name']);
                            if ($upload_flag) {
                                unlink($file_path);
                                $return_file_names[$file_index] = $upload_array['file_name'];
                            }
                        }
                    }
                } else {
                    $return_file_names[$file_index] = '';
                }
            }

            return $return_file_names;
        }
    }

}

if (!function_exists('do_upload_multiple3')) {

    function do_upload_multiple3($upload_path, $files, $upload_folder) {

        $CI = & get_instance();
        reset($files);
        $return_file_names = array();
        $totalfiles = count($files);
        $file_object = key($files);

        if ($totalfiles > 0) {
            if (!file_exists($upload_path)) {
                mkdir($upload_path, 0777, TRUE);
                chmod($upload_path, 0777);
            }

            $CI->load->library('upload');
            $CI->load->library('image_lib');


            if (!is_array($files[$file_object]['name'])) {

                $file_index = 0;
                $_FILES[$file_object . $file_index]['name'] = $files[$file_object]['name'];
                $_FILES[$file_object . $file_index]['size'] = $files[$file_object]['size'];
                $_FILES[$file_object . $file_index]['type'] = $files[$file_object]['type'];
                $_FILES[$file_object . $file_index]['tmp_name'] = $files[$file_object]['tmp_name'];
                $_FILES[$file_object . $file_index]['error'] = $files[$file_object]['error'];

                if ($_FILES[$file_object . $file_index]['error'] != 0) {
                    unset($_FILES[$file_object . $file_index]);
                }
                unset($_FILES[$file_object]);
            } else {
                $temp = $files[$file_object]['name'];
                end($temp);
//                $totalfiles = count($files[$file_object]['name']);
                $totalfiles = key($temp) + 1;

                for ($file_index = 0; $file_index < $totalfiles; $file_index++) {
                    if (isset($files[$file_object]['name'][$file_index])) {
                        $_FILES[$file_object . $file_index]['name'] = $files[$file_object]['name'][$file_index];
                        $_FILES[$file_object . $file_index]['size'] = $files[$file_object]['size'][$file_index];
                        $_FILES[$file_object . $file_index]['type'] = $files[$file_object]['type'][$file_index];
                        $_FILES[$file_object . $file_index]['tmp_name'] = $files[$file_object]['tmp_name'][$file_index];
                        $_FILES[$file_object . $file_index]['error'] = $files[$file_object]['error'][$file_index];

                        if ($_FILES[$file_object . $file_index]['error'] != 0) {
                            unset($_FILES[$file_object . $file_index]);
                        }
                    }
                }
            }

            for ($file_index = 0; $file_index < $totalfiles; $file_index++) {
                $file_name = basename($upload_path);
                $config['upload_path'] = $upload_path;
                $config['allowed_types'] = "*";
                $config['overwrite'] = FALSE;
                $config['file_name'] = $file_name . "_" . time();
                if (isset($_FILES[$file_object . $file_index])) {
                    $CI->upload->initialize($config);
                    if ($CI->upload->do_upload($file_object . $file_index)) {
                        $upload_array = array();
                        $upload_array = $CI->upload->data();
                        chmod($upload_path . "/" . $upload_array['file_name'], 0777);

                        if (is_array($upload_array) && count($upload_array) > 0) {

                            $file_path = $upload_path . "/" . $upload_array['file_name'];
                            //$return_file_names[$key] = $upload_array['file_name'];
                            chmod($file_path, 0777);

                            //upload on s3 bucket
                            include_once BUCKET_HELPER_PATH;
                            $upload_flag = uploadfilesS3($file_path, $upload_folder . "/" . $upload_array['file_name']);
                            if ($upload_flag) {
                                unlink($file_path);
                                $return_file_names[$file_index] = $upload_array['file_name'];
                            }
                        }
                    }
                } else {
                    $return_file_names[$file_index] = '';
                }
            }

            return $return_file_names;
        }
    }

}

function random_color_part() {
    return str_pad(dechex(mt_rand(0, 255)), 2, '0', STR_PAD_LEFT);
}

function random_color() {
    return random_color_part() . random_color_part() . random_color_part();
}

if (!function_exists("validate_email")) {

    function validate_email($email) {
        if (!preg_match("/^[A-Za-z][A-Za-z0-9._]*\@[A-Za-z]*\.[A-Za-z]{2,3}$/", $email)) {
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

if (!function_exists("validate_pincode")) {

    function validate_pincode($pincode) {
        if (!preg_match("/^[0-9]{6}$/", $pincode)) {
            return 1;
        } else {
            return 0;
        }
        return 0;
    }

}

if (!function_exists("validate_dob")) {

    function validate_dob($validate_dob) {
        $date_arr = explode('-', $validate_dob);
        if (count($date_arr) == 3) {
            if (!checkdate($date_arr[1], $date_arr[2], $date_arr[0])) {
                return 1;
            } else {
                $max_year_allowed = (date('Y', time()) - 100);
                if ($date_arr[0] < $max_year_allowed) {
                    return 1;
                }
            }
        } else {
            return 1;
        }
        return 0;
    }

}

if (!function_exists("validate_date_only")) {

    function validate_date_only($date) {
        $date_arr = explode('-', $date);
        if (count($date_arr) == 3) {
            if (!checkdate($date_arr[1], $date_arr[2], $date_arr[0])) {
                return 1;
            } else {
                return 0;
            }
        } else {
            return 1;
        }
        return 0;
    }

}

/**
 * Description :- This function is used to valid the date of the confirm appointment
 */
if (!function_exists("validate_date")) {

    function validate_date($date) {
        $date_arr = explode('-', $date);
        if (count($date_arr) == 3) {
            if (!checkdate($date_arr[1], $date_arr[2], $date_arr[0])) {
                return 1;
            } else {

                //can allowed max 6 months of future date
                $effective_date = date('Y-m-d', strtotime("+6 months", time()));
                if ($date > $effective_date) {
                    return 1;
                }

                //check whether the date is past or not
                $today_date = date('Y-m-d', time());
                if ($date < $today_date) {
                    return 1;
                }
            }
        } else {
            return 1;
        }
        return 0;
    }

}


if (!function_exists("validate_time")) {

    function validate_time($time) {

        $dateObj = DateTime::createFromFormat('d.m.Y H:i', "10.10.2010 " . $time);

        if ($dateObj !== false && $dateObj && $dateObj->format('G') == intval($time)) {
            return 0;
        } else {
            return 1;
        }
        return 0;
    }

}

if (!function_exists('validate_percentage')) {
    function validate_percentage($percentage) {
        if (!preg_match('/^[0-9]{1,3}([.][0-9]{0,2})?$/', $percentage)) {
            return 1;
        }
        return 0;
    }
}

if (!function_exists("msort")) {

    function msort($array, $key, $sort_flags = SORT_REGULAR) {
        if (is_array($array) && count($array) > 0) {
            if (!empty($key)) {
                $mapping = array();
                foreach ($array as $k => $v) {
                    $sort_key = '';
                    if (!is_array($key)) {
                        $sort_key = $v[$key];
                    } else {
                        // @TODO This should be fixed, now it will be sorted as string
                        foreach ($key as $key_key) {
                            $sort_key .= $v[$key_key];
                        }
                        $sort_flags = SORT_STRING;
                    }
                    $mapping[$k] = $sort_key;
                }
                asort($mapping, $sort_flags);
                $sorted = array();
                foreach ($mapping as $k => $v) {
                    $sorted[] = $array[$k];
                }
                return $sorted;
            }
        }
        return $array;
    }

}


if (!function_exists('validate_characters')) {
    function validate_characters($character) {
        if (!preg_match("/^[a-zA-Z ]+$/", $character)) {
            return 1;
        }
        return 0;
    }
}

if (!function_exists("upload_zip_data")) {

    function upload_zip_data($recieve_data) {
        include_once BUCKET_HELPER_PATH;
        $send_array = array();
        $CI = & get_instance();
        $CI->load->library('image_lib');
        if (!empty($recieve_data['file_name'])) {
            $zip = new ZipArchive;
            $res = $zip->open($recieve_data['upload_path'] . "/" . $recieve_data['file_name']);

            if ($res === TRUE) {
                $zip->extractTo($recieve_data['upload_path']);
                $zip->close();
                unlink($recieve_data['upload_path'] . "/" . $recieve_data['file_name']);
                chmod($recieve_data['upload_path'], 0777);
                $all_file_array = scandir($recieve_data['upload_path']);

                foreach ($all_file_array as $key => $file) {
                    $file = trim($file);
                    if ($file != "." && $file != '..' && $file != 'thumb') {
                        chmod($recieve_data['upload_path'] . "/" . $file, 0777);
                        $image_ext = pathinfo($file, PATHINFO_EXTENSION);
                        $new_file_name = $recieve_data['id'] . "_" . uniqid() . "_" . $key . "." . $image_ext;

                        if (in_array($image_ext, array('pdf', 'jpg', 'jpeg', 'png'))) {
                            $send_array[] = $new_file_name;
                            copy($recieve_data['upload_path'] . "/" . $file, $recieve_data['upload_path'] . "/" . $new_file_name);
                            chmod($recieve_data['upload_path'] . "/" . $new_file_name, 0777);
                            if (in_array($image_ext, array('jpg', 'jpeg', 'png'))) {
                                $arr = explode(".", $new_file_name);
                                $thumb_file_name = $arr[0] . "_thumb." . $arr[1];
                                $config = array(
                                        'source_image'      => $recieve_data['upload_path'] . "/" . $file,
                                        'new_image'         => $recieve_data['upload_path'] . "/" . $thumb_file_name,
                                        'maintain_ratio'    => true,
                                        'width'             => 100,
                                        'height'            => 110
                                    );
                                
                                $CI->image_lib->initialize($config);
                                if($CI->image_lib->resize()) {
                                    chmod($recieve_data['upload_path'] . "/" . $thumb_file_name, 0777);
                                }
                            }

                        } else {
                            unlink($recieve_data['upload_path'] . "/" . $file);
                        }
                    }
                }
                // include_once BUCKET_HELPER_PATH;
                transfer_file(DOCROOT_PATH . 'uploads/' . $recieve_data['upload_folder'], $recieve_data['upload_folder']);
                //exec("rm -rf " . DOCROOT_PATH . 'uploads/' . $recieve_data['upload_folder']);
            }
        }

        return $send_array;
    }

}

if (!function_exists("send_message_by_twilio")) {

    function send_message_by_twilio($requested_data) {

        require_once APPPATH . "/third_party/twilio/Services/Twilio.php";

        try {
            $AccountSid = TWILIO_ACC_SID;
            $AuthToken = TWILIO_AUTH_TOKEN;
            $client = new Services_Twilio($AccountSid, $AuthToken);
            $sms = $client->account->messages->sendMessage(
                    TWILIO_REG_NUMBER, "+" . $requested_data['phone_number'], $requested_data['message']
            );
            if ($sms) {
                return 1;
            } else {
                return 0;
            }
        } catch (ErrorException $e) {
            return 0;
        }
    }

}

function send_communication($data) {
    if(!empty($data['phone_number']) && strlen($data['phone_number']) == 10) {
        $CI = & get_instance();
        if(!empty($data['doctor_id']) && empty($data['is_not_check_setting_flag'])) {
            $where_doctor_global_setting = [
                'doctor_id' => $data['doctor_id'],
                'setting_name' => ['sms_communication','whatsapp_communication'],
            ];
            $doctor_global_setting_data = $CI->Common_model->get_doctor_global_setting($where_doctor_global_setting);
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
            $send_message['user_type'] = 2;
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
                'user_type'=> 2,
                'mobile'=> $data['phone_number'],
                'body'=> $data['whatsapp_sms_body']
            ];
            if(!empty($data['patient_id']))
                $whatsapp_data['patient_id'] = $data['patient_id'];
            if(!empty($data['doctor_id']))
                $whatsapp_data['doctor_id'] = $data['doctor_id'];
            if(!empty($data['is_sms_count'])) //If true then No deduct doctor's credit
                $whatsapp_data['is_promotional'] = $data['is_sms_count'];
            
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
			$response = [];
			if(isset($requested_data['phone_number']) && !empty($requested_data['phone_number']) && strlen($requested_data['phone_number']) > 0){
				$mobileNo = str_replace('+','',$requested_data['phone_number']);
				if(!empty($requested_data['is_promotional'])) {
                    $textlocal_hash_key = $GLOBALS['ENV_VARS']['PROMOTIONAL_TEXTLOCAL_API_KEY'];
                    $textlocal_senderid = $GLOBALS['ENV_VARS']['PROMOTIONAL_TEXTLOCAL_SENDERID'];
                    $data = "apikey=".$textlocal_hash_key."&message=".urlencode($requested_data['message'])."&sender=".$textlocal_senderid."&numbers=".urlencode($mobileNo)."&test=".TEXTLOCAL_TEST;
                } else {
                    $data = "username=".TEXTLOCAL_USERNAME."&hash=".TEXTLOCAL_HASH_KEY."&message=".urlencode($requested_data['message'])."&sender=".TEXTLOCAL_SENDERID."&numbers=".urlencode($mobileNo)."&test=".TEXTLOCAL_TEST;
                }
				$ch = curl_init('https://api.textlocal.in/send/?');
				curl_setopt($ch, CURLOPT_POST, true);
				curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				$response = curl_exec($ch); // This is the result from the API
				curl_close($ch);
                /* [START] Make responce entry on LOG file */
                    $log = LOG_FILE_PATH . 'sms_log_' . date('d-m-Y') . ".txt";
                    file_put_contents($log, "\n  ================ START ".date('d-m-Y H:i:s')." =====================    \n\n", FILE_APPEND);
                    file_put_contents($log, "\n" .$response."  \n\n", FILE_APPEND);
                    file_put_contents($log, "\n  ================ END =====================    \n\n", FILE_APPEND);
                /* [END] Make responce entry on LOG file */
                $requested_data['json_data'] = $response;
                insert_sms_log($requested_data);
			}
            if(!empty($response))
                $response = json_decode($response,true);
            if(!empty($response['status']) && $response['status'] != 'success') {
                $log = LOG_FILE_PATH . 'invalid_sms_log_' . date('d-m-Y') . ".txt";
                file_put_contents($log, "\n  ================ START ".date('d-m-Y H:i:s')." =====================\n ". $requested_data['message'] ." \n", FILE_APPEND);
                file_put_contents($log, "\n" .json_encode($response)."  \n", FILE_APPEND);
                file_put_contents($log, "\n  ================ END =====================\n", FILE_APPEND);
            }
			if (isset($response['status']) && $response['status'] == 'success') {
                return 1;
            } else {
                return 0;
            }
        } catch (ErrorException $ex) {
            return 0;
        }
	}
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
    $log_data['user_type'] = 1;
    if(!empty($data['user_type']))
        $log_data['user_type'] = $data['user_type'];
    if(!empty($data['json_data']))
        $log_data['json_data'] = $data['json_data'];
    $CI = &get_instance();
    $CI->Common_model->insert('me_message_tracking', $log_data);
}

if (!function_exists("validate_report_date")) {

    function validate_report_date($date) {
        $date_arr = explode('-', $date);
        if (count($date_arr) == 3) {
            if (!checkdate($date_arr[1], $date_arr[2], $date_arr[0])) {
                return 1;
            } else {

                //can allowed max 6 months of future date
                $effective_date = date('Y-m-d', strtotime("+6 months", time()));
                if ($date > $effective_date) {
                    return 1;
                }

                //can allowed max 6 months of future date
                $effective_past_date = date('Y-m-d', strtotime("-6 months", time()));
                if ($date < $effective_past_date) {
                    return 1;
                }
            }
        } else {
            return 1;
        }
        return 0;
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


if (!function_exists('send_pushwoosh_notification')) {

    function send_pushwoosh_notification($payload) {
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

        $log = LOG_FILE_PATH . 'notification_' . date('d-m-Y') . ".txt";

        $response = curl_exec($ch);
        file_put_contents($log, "\n " . json_encode($response) . "   \n\n", FILE_APPEND | LOCK_EX);
        curl_close($ch);


        return true;
    }

}

if (!function_exists('convert_utc_to_local')) {

    function convert_utc_to_local($date, $time_zone = 'Asia/Kolkata') {
        $date = new DateTime($date, new DateTimeZone('UTC'));
        $date->setTimezone(new DateTimeZone($time_zone));
        return $date->format('Y-m-d H:i:s');
    }

}
function pr($array, $exit = '') {
        echo '<pre>';
        print_r($array);
        if (!$exit) {
            exit;
        }
    }

if(!function_exists('get_image_thumb')) {
    function get_image_thumb($file_url) {
        if (!empty($file_url)) {
            include_once BUCKET_HELPER_PATH;
            $thumb_file_url = get_thumb_filename($file_url);
            $thumb_file_name = substr($thumb_file_url, strrpos($thumb_file_url, BUCKET_NAME_DOWNLOAD."/"));
            $image_ext = pathinfo($thumb_file_name, PATHINFO_EXTENSION);
            if (in_array($image_ext, array('jpg', 'jpeg', 'png', 'gif'))) {
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
if (!function_exists('date_convert')){

    function date_convert($datetime, $timezone = '', $date_format, $timezone_convert_to = "UTC", $date_format_2) {
        // create DateTime object
        $d = DateTime::createFromFormat($date_format, $datetime, new DateTimeZone($timezone));
        // convert timezone
        $d->setTimeZone(new DateTimeZone($timezone_convert_to));
        // convert dateformat
        return $d->format($date_format_2);
    }
}

function is_gmail_email($email) {
    list($user, $domain) = explode('@', $email);
    if (strtolower($domain) == 'gmail.com')
        return true;
    else
        return false;
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
function is_send_sms_to_patient($doctor_id, $doctor_global_setting_data = []) {
    if(!empty($doctor_global_setting_data)) {
        $setting_data = array_column($doctor_global_setting_data, 'setting_value', 'setting_name');
        if(!empty($setting_data['sms_communication']) && $setting_data['sms_communication'] == "1")
            return true;
        else
            return false;
    } else {
        $CI = &get_instance();
        $where = [
            'doctor_id' => $doctor_id,
            'setting_name' => ['sms_communication'],
        ];
        $doctor_setting = $CI->Common_model->get_doctor_global_setting($where);
        if(!empty($doctor_setting[0]->setting_value) && $doctor_setting[0]->setting_value == 1)
            return true;
        return false;
    }
}

function is_send_whatsapp_sms($doctor_id, $doctor_global_setting_data = []) {
    if(!empty($doctor_global_setting_data)) {
        $setting_data = array_column($doctor_global_setting_data, 'setting_value', 'setting_name');
        if(!empty($setting_data['whatsapp_communication']) && $setting_data['whatsapp_communication'] == "1")
            return true;
        else
            return false;
    } else {
        $CI = &get_instance();
        $where = [
            'doctor_id' => $doctor_id,
            'setting_name' => ['whatsapp_communication'],
        ];
        $doctor_setting = $CI->Common_model->get_doctor_global_setting($where);
        if(!empty($doctor_setting[0]->setting_value) && $doctor_setting[0]->setting_value == 1)
            return true;
        return false;
    }
}

function is_email_communication($doctor_id) {
    if(!empty($doctor_global_setting_data)) {
        $setting_data = array_column($doctor_global_setting_data, 'setting_value', 'setting_name');
        if(!empty($setting_data['email_communication']) && $setting_data['email_communication'] == "1")
            return true;
        else
            return false;
    } else {
        $CI = &get_instance();
        $where = [
            'doctor_id' => $doctor_id,
            'setting_name' => ['email_communication'],
        ];
        $doctor_setting = $CI->Common_model->get_doctor_global_setting($where);
        if(!empty($doctor_setting[0]->setting_value) && $doctor_setting[0]->setting_value == 1)
            return true;
        return false;
    }
}