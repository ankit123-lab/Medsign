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

    function do_upload_multiple($upload_path, $files, $upload_folder, $width=100, $height=110, $quality='') {
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
                $config['allowed_types'] = FILE_UPLOAD_ALLOWED_EXTENSION;
                $config['max_size'] = MAX_FILE_UPLOAD_SIZE;
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
                        if(IS_S3_UPLOAD) {
                            include_once BUCKET_HELPER_PATH;
                            uploadfilesS3($file_path, $upload_folder . "/" . $upload_array['file_name']);
                        }
                        $image_ext = pathinfo($upload_array['file_name'], PATHINFO_EXTENSION);
						if (in_array(strtolower($image_ext), array('jpg', 'jpeg', 'png','gif'))) {
                            $arr = explode(".", $upload_array['file_name']);
                            $thumb_file_name = $arr[0] . "_thumb." . $arr[1];
                            list($ori_width, $ori_height) = getimagesize($file_path);
                            if($width > $ori_width)
                                $width = $ori_width;
                            if($height > $ori_height)
                                $height = $ori_height;
                            $config = array(
                                'source_image'      => $file_path,
                                'new_image'         => $upload_path.'/'.$thumb_file_name,
                                'image_library'     => 'gd2',
                                'maintain_ratio'    => true,
                                'width'             => $width,
                                'height'            => $height
                            );
                            if(!empty($quality))
                                $config['quality'] = $quality;
                            $CI->image_lib->initialize($config);
                            $CI->image_lib->resize();
                            if(IS_S3_UPLOAD) {
                                uploadfilesS3($upload_path.'/'.$thumb_file_name, $upload_folder . "/" . $thumb_file_name);
                            }
                            if(!IS_SERVER_UPLOAD)
                                unlink($upload_path.'/'.$thumb_file_name);
                        }
                        if(!IS_SERVER_UPLOAD)
                            unlink($file_path);
                        $return_file_names[$key] = $upload_array['file_name'];
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
                $config['allowed_types'] = FILE_UPLOAD_ALLOWED_EXTENSION;
                $config['max_size'] = MAX_FILE_UPLOAD_SIZE;
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
                            if(IS_S3_UPLOAD) {
                            include_once BUCKET_HELPER_PATH;
                                $upload_flag = uploadfilesS3($file_path, $upload_folder . "/" . $upload_array['file_name']);
                            }
                            if (!IS_SERVER_UPLOAD) {
                                unlink($file_path);
                            }
                            $return_file_names[$file_index] = $upload_array['file_name'];
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
                $config['allowed_types'] = FILE_UPLOAD_ALLOWED_EXTENSION;
                $config['max_size'] = MAX_FILE_UPLOAD_SIZE;
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
                            if(IS_S3_UPLOAD) {
                            include_once BUCKET_HELPER_PATH;
                                $upload_flag = uploadfilesS3($file_path, $upload_folder . "/" . $upload_array['file_name']);
                            }
                            $image_ext = pathinfo($upload_array['file_name'], PATHINFO_EXTENSION);
                            if (in_array(strtolower($image_ext), array('jpg', 'jpeg', 'png','gif'))) {
                                $arr = explode(".", $upload_array['file_name']);
                                $thumb_file_name = $arr[0] . "_thumb." . $arr[1];
                                $config = array(
                                    'source_image'      => $file_path,
                                    'new_image'         => $upload_path.'/'.$thumb_file_name,
                                    'maintain_ratio'    => true,
                                    'width'             => 100,
                                    'height'            => 100
                                );
                                $CI->image_lib->clear();
                                $CI->image_lib->initialize($config);
                                $CI->image_lib->resize();
                                if(IS_S3_UPLOAD) {
                                    $upload_flag = uploadfilesS3($upload_path.'/'.$thumb_file_name, $upload_folder . "/" . $thumb_file_name);
                                }
                                if(!IS_SERVER_UPLOAD)
                                    unlink($upload_path.'/'.$thumb_file_name);
                            }
                            if(!IS_SERVER_UPLOAD)
                                unlink($file_path);
                            $return_file_names[$file_index] = $upload_array['file_name'];
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
        // old => /^[A-Za-z][A-Za-z0-9._]*\@[A-Za-z]*\.[A-Za-z]{2,3}$/
        
        if (!preg_match("/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/", $email)) {
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

if (!function_exists("validate_clinic_number")) {

    function validate_clinic_number($phone_number) {
        if (!preg_match("/^[0-9]{10,12}$/", $phone_number)) {
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
        if (!preg_match("/^[a-zA-Z\. ]+$/", $character)) {
            return 1;
        }
        return 0;
    }

}

if (!function_exists("upload_zip_data")) {

    function upload_zip_data($recieve_data) {

        $send_array = array();

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
                        } else {
                            unlink($recieve_data['upload_path'] . "/" . $file);
                        }
                    }
                }

                include_once BUCKET_HELPER_PATH;
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
				/* [START] Make responce entry on LOG file */
					$log = LOG_FILE_PATH . 'sms_log_' . date('d-m-Y') . ".txt";
					file_put_contents($log, "\n  ================ START ".date('d-m-Y H:i:s')." =====================    \n\n", FILE_APPEND);
					file_put_contents($log, "\n" .$response."  \n\n", FILE_APPEND);
					file_put_contents($log, "\n  ================ END =====================    \n\n", FILE_APPEND);
				/* [END] Make responce entry on LOG file */
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
                $effective_past_date = date('Y-m-d', strtotime("-12 months", time()));
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
            $arr = json_decode($file_url,true);
            if(!empty($arr) && is_array($arr)) {
                $thumb_file_url = get_thumb_filename($arr['path']);
                if ($arr['type'] == 1) {
                    $thumb_file_name = $thumb_file_url;
                    $result = checkResource($thumb_file_name, $arr['name']);
                    if(!$result && strpos($thumb_file_name, 'anatomical_diagrams/images/') !== false){
                        $thumb_file_name = str_replace('anatomical_diagrams/images/', 'anatomical_diagram/', $thumb_file_name);
                        if(file_exists(UPLOAD_REL_PATH.'/'.$thumb_file_name))
                            return $GLOBALS['ENV_VARS']['DOCUMENT_LOAD_SERVER_URL'].'uploads/'.$thumb_file_name;
                    }
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
                    if(strpos($thumb_file_name, 'anatomical_diagrams/images/') !== false){
                        $thumb_file_name = str_replace('anatomical_diagrams/images/', 'anatomical_diagram/', $thumb_file_name);
                        if(file_exists(UPLOAD_REL_PATH.'/'.$thumb_file_name))
                            return $GLOBALS['ENV_VARS']['DOCUMENT_LOAD_SERVER_URL'].'uploads/'.$thumb_file_name;
                    }
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

if (!function_exists('read_csv')) {
    function read_csv($filename, $file_path) {
        $handle = fopen($file_path. '/' . $filename, "r");
        $result = array();
        while (($row = fgetcsv($handle)) != FALSE)
        {
            $result[] =$row;
        }
        return $result;
    }
}

if (!function_exists('read_xlsx')) {
    function read_xlsx($filename, $file_path, $sub_sheet_name = '') {
        require_once APPPATH . 'third_party/PHPExcel/PHPExcel.php';
        $result = array();
        $file_type = PHPExcel_IOFactory::identify($file_path . '/' . $filename);
        $objReader = PHPExcel_IOFactory::createReader($file_type);
        if(!empty($sub_sheet_name)) {
            $objReader->setLoadSheetsOnly(array($sub_sheet_name));
        }
        $objPHPExcel = $objReader->load($file_path . '/' . $filename);
        $sheet_data = $objPHPExcel->getActiveSheet()->toArray(null, true, true, true);
        if(!empty($sheet_data) && count($sheet_data) > 0) {
            foreach ($sheet_data as $key => $value) {
                $result[] = $value;
            }
        }
        return $result;
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

function is_send_sms_to_patient($doctor_id, $doctor_global_setting_data = []) {
    /*$arr = array();
    if(!empty($GLOBALS['ENV_VARS']['NOTIFICATION_OFF_DOCTOR_ID'])) {
        $arr = explode(',', $GLOBALS['ENV_VARS']['NOTIFICATION_OFF_DOCTOR_ID']);
    }
    $doctor_setting = doctor_sub_plan_setting($doctor_id);
    if(!in_array($doctor_id, $arr) && !empty($doctor_setting['sms_communication']) && $doctor_setting['sms_communication'] == 1)
        return true;
    return false;*/
    if(!empty($doctor_global_setting_data)) {
        $setting_data = array_column($doctor_global_setting_data, 'setting_value', 'setting_name');
        if(!empty($setting_data['sms_communication']) && $setting_data['sms_communication'] == "1")
            return true;
        else
            return false;
    } else {
        $CI = & get_instance();
        $CI->load->model('subscription_model');
        $doctor_setting = $CI->subscription_model->get_doctors_global_setting($doctor_id, 'setting_value', 'sms_communication');
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
        $CI = & get_instance();
        $CI->load->model('subscription_model');
        $doctor_setting = $CI->subscription_model->get_doctors_global_setting($doctor_id, 'setting_value', 'whatsapp_communication');
        if(!empty($doctor_setting[0]->setting_value) && $doctor_setting[0]->setting_value == 1)
            return true;
        return false;
    }
}

function is_email_communication($doctor_id) {
    /*$arr = array();
    if(!empty($GLOBALS['ENV_VARS']['NOTIFICATION_OFF_DOCTOR_ID'])) {
        $arr = explode(',', $GLOBALS['ENV_VARS']['NOTIFICATION_OFF_DOCTOR_ID']);
    }
    $doctor_setting = doctor_sub_plan_setting($doctor_id);
    if(!in_array($doctor_id, $arr) && !empty($doctor_setting['email_communication']) && $doctor_setting['email_communication'] == 1)
        return true;
    return false;*/
    if(!empty($doctor_global_setting_data)) {
        $setting_data = array_column($doctor_global_setting_data, 'setting_value', 'setting_name');
        if(!empty($setting_data['email_communication']) && $setting_data['email_communication'] == "1")
            return true;
        else
            return false;
    } else {
        $CI = & get_instance();
        $CI->load->model('subscription_model');
        $doctor_setting = $CI->subscription_model->get_doctors_global_setting($doctor_id, 'setting_value', 'email_communication');
        if(!empty($doctor_setting[0]->setting_value) && $doctor_setting[0]->setting_value == 1)
            return true;
        return false;
    }
}

if(!function_exists('file_upload_validate')) {
    function file_upload_validate($files) {
        $return = array('status' => true);
        foreach ($files as $key => $value) {
            $file_ext = pathinfo($value['name'], PATHINFO_EXTENSION);
            if(!in_array(strtolower($file_ext), explode('|', FILE_UPLOAD_ALLOWED_EXTENSION))) {
                $return['status'] = false;
                $return['error'] = "The filetype you are attempting to upload is not allowed.";
                return $return;
                break;
            }
            $file_size = $value['size'] / 1000; // convert to KB
            if($file_size > MAX_FILE_UPLOAD_SIZE) {
                $return['status'] = false;
                $return['error'] = "The uploaded file exceeds the maximum size allowed by the system.";
                return $return;
                break;
            }
        }
        return $return;
    }
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

function upload_import_report($source, $dest_path, $width = 100, $height = 110) {
    if(!IS_S3_UPLOAD)
        return false;
    include_once BUCKET_HELPER_PATH;
    $image_ext = pathinfo($dest_path, PATHINFO_EXTENSION);
    if (in_array(strtolower($image_ext), array('pdf', 'jpg', 'jpeg', 'png'))) {
        $is_upload = uploadfilesS3($source, $dest_path);
        $is_thumb_upoad = true;
        if (in_array(strtolower($image_ext), array('jpg', 'jpeg', 'png'))) {
            $CI = & get_instance();
            $CI->load->library('image_lib');
            $config = array(
                    'source_image'      => $source,
                    'new_image'         => get_thumb_filename($source),
                    'maintain_ratio'    => true,
                    'width'             => $width,
                    'height'            => $height
                );
            
            $CI->image_lib->initialize($config);
            if($CI->image_lib->resize()) {
                chmod(get_thumb_filename($source), 0777);
                $is_thumb_upoad = uploadfilesS3(get_thumb_filename($source), get_thumb_filename($dest_path));
            }
        }
        if($is_upload && $is_thumb_upoad) {
            return true;
        }
    }
    return false;
}

/*$plan_type 1=Free, 2=Paid*/
function assign_sub_plan($doctor_id, $plan_id, $plan_type = 1) {
    $doctor_subscriptions_data_insert = array();
    $doctor_subscriptions_data_update = array();
    $doctor_user_data_update = array();
    $doctors_global_setting_data = array();
    $CI = & get_instance();
    $CI->load->model('subscription_model');
    $plan_detail = $CI->subscription_model->get_sub_plan_by_id($plan_id);
    $plan_setting_data = $CI->subscription_model->get_setting_data($plan_id);
    if($plan_type == 1) {
        if(!empty($GLOBALS['ENV_VARS']['DEFAULT_FREE_TRIAL_DATE'])) {
            $expiry_date = $GLOBALS['ENV_VARS']['DEFAULT_FREE_TRIAL_DATE'];
        } else {
            $expiry_date = date('Y-m-d', strtotime("+" . ($plan_detail->sub_free_days - 1) . " days"));
        }
    } else {
        if($plan_detail->sub_plan_type == 2) {
            $time_add = "+ " . $plan_detail->sub_plan_validity . " years";
        } else {
            $time_add = "+ " . $plan_detail->sub_plan_validity . " months";
        }
        $expiry_date = date('Y-m-d', strtotime($time_add));
    }
    $doctor_subscriptions_data_insert[] = array(
        'doctor_id' => $doctor_id,
        'sub_plan_id' => $plan_id,
        'plan_start_date' => date('Y-m-d'),
        'plan_expiry_date' => $expiry_date,
        'doctor_plan_type' => $plan_type,
        'created_at' => date('Y-m-d H:i:s')
    );
    $doctor_subscriptions_data_update[] = array(
        'doctor_id' => $doctor_id,
        'doctor_subscriptions_status' => 2,
        'updated_at' => date('Y-m-d H:i:s')
    );
    $doctor_user_data_update[] = array(
        'user_id' => $doctor_id,
        'user_plan_id' => $plan_id
    );
    
    foreach ($plan_setting_data as $setting) {
        $doctors_global_setting_data[] = array(
            'doctor_id' => $doctor_id,
            'sub_plan_id' => $plan_id,
            'setting_name' => $setting->sub_setting_data_name,
            'setting_value' => $setting->sub_setting_data_value,
            'created_at' => date('Y-m-d H:i:s')
        );
    }
    
    $CI->subscription_model->update_doctor_subscriptions($doctor_subscriptions_data_update);
    $CI->subscription_model->update_doctor_data($doctor_user_data_update);
    $CI->subscription_model->create_bulk_rows('me_doctor_subscriptions', $doctor_subscriptions_data_insert);
    $CI->subscription_model->doctors_global_setting_update($doctor_id, ['setting_status' => 9]);
    $CI->subscription_model->create_bulk_rows('me_doctors_global_setting', $doctors_global_setting_data);
}
function validity_month_arr($val = '') {
    $arr = array(
        '1' => '1 Month',
        '2' => '2 Months',
        '3' => '3 Months',
        '4' => '4 Months',
        '5' => '5 Months',
        '6' => '6 Months',
        '7' => '7 Months',
        '8' => '8 Months',
        '9' => '9 Months',
        '10' => '10 Months',
        '11' => '11 Months',
    );
    if(!empty($val) && !empty($arr[$val]))
        return $arr[$val];
    return $arr;
}
function validity_year_arr($val = '') {
    $arr = array(
        '1' => '1 Year',
        '2' => '2 Years',
        '3' => '3 Years',
        '4' => '4 Years',
        '5' => '5 Years'
    );
    if(!empty($val) && !empty($arr[$val]))
        return $arr[$val];
    return $arr;
}
function get_sub_plan_validity($plan_type, $plan_validity) {
    if($plan_type == 1)
        return validity_month_arr($plan_validity);
    elseif($plan_type == 2)
        return validity_year_arr($plan_validity);
}
function is_sub_active($doctor_id) {
    $CI = & get_instance();
    $CI->load->model('subscription_model');
    $columns = 'ds.plan_expiry_date';
    $sub_detail = $CI->subscription_model->get_doctor_subscription($doctor_id, $columns);
    if(!empty($sub_detail->plan_expiry_date) && $sub_detail->plan_expiry_date >= date('Y-m-d')) {
        return true;
    } else {
        return false;
    }
}
function doctor_sub_plan_setting($doctor_id) {
    $CI = & get_instance();
    $CI->load->model('subscription_model');
    $sub_setting = $CI->subscription_model->get_doctors_global_setting($doctor_id, 'setting_name,setting_value');
    $sub_plan_setting = array_column($sub_setting, 'setting_value', 'setting_name');
    $sub_plan_setting['medsign_speciality_display_name'] = '';
    if(!empty($sub_plan_setting['medsign_speciality_id'])) {
        $speciality_ids = explode(',', $sub_plan_setting['medsign_speciality_id']);
        $speciality_ids = array_diff($speciality_ids, [1]);
        if(!empty($speciality_ids) && count($speciality_ids) > 0) {
            $medsign_speciality_query = "SELECT medsign_speciality_id, medsign_speciality_display_name 
            FROM me_medsign_speciality 
            WHERE medsign_speciality_id IN(".implode(',', $speciality_ids).") ORDER BY medsign_speciality_id";
            $medsign_speciality = $CI->Common_model->get_all_rows_by_query($medsign_speciality_query);
            if(!empty($medsign_speciality[0]['medsign_speciality_display_name'])) {
                $sub_plan_setting['medsign_speciality_display_name'] = $medsign_speciality[0]['medsign_speciality_display_name'];
            }
        }
    } else {
        $sub_plan_setting['medsign_speciality_id'] = "1";
    }
    return $sub_plan_setting;
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

if (!function_exists('upload_to_s3')) {
    function upload_to_s3($files, $upload_path) {
        //upload on s3 bucket
        if(IS_S3_UPLOAD) {
            include_once BUCKET_HELPER_PATH;
            return uploadfilesS3($files, $upload_path);
        }
    }
}

function delete_file_from_s3($path) {
    include_once BUCKET_HELPER_PATH;
    return deleteSingleFileS3($path);
}

function patient_profile_completion($name, $user_phone_number, $user_photo_filepath,$emergency_contact_person,$email,$dob,$marital_status,$address_name,$weight,$height,$gender,$food_allergies,$medicine_allergies,$other_allergies,$family_medical_history_data,$chronic_diseases,$injuries,$surgeries,$blood_group,$smoking_habbit,$alcohol,$food_preference,$occupation,$activity_level) {
    $percent = 0;
    if(!empty($name)) {
        $percent += 4;
    }
    if(!empty($user_phone_number)) {
        $percent += 4;
    }
    if(!empty($user_photo_filepath)) {
        $percent += 8;
    }
    if(!empty($emergency_contact_person)) {
        $percent += 4;
    }
    if(!empty($email)) {
        $percent += 4;
    }
    if(!empty($dob)) {
        $percent += 4;
    }
    if(!empty($marital_status)) {
        $percent += 4;
    }
    if(!empty($address_name)) {
        $percent += 4;
    }
    if(!empty($weight)) {
        $percent += 4;
    }
    if(!empty($height)) {
        $percent += 4;
    }
    if(!empty($gender)) {
        $percent += 4;
    }
    if(!empty($food_allergies)) {
        $percent += 4;
    }
    if(!empty($medicine_allergies)) {
        $percent += 4;
    }
    if(!empty($other_allergies)) {
        $percent += 4;
    }
    if(!empty($family_medical_history_data) && $family_medical_history_data > 0) {
        $percent += 4;
    }
    if(!empty($chronic_diseases)) {
        $percent += 4;
    }
    if(!empty($injuries)) {
        $percent += 4;
    }
    if(!empty($surgeries)) {
        $percent += 4;
    }
    if(!empty($blood_group)) {
        $percent += 4;
    }
    if(!empty($smoking_habbit) && $smoking_habbit > 0) {
        $percent += 4;
    }
    if(!empty($alcohol) && $alcohol > 0) {
        $percent += 4;
    }
    if(!empty($food_preference)) {
        $percent += 4;
    }
    if(!empty($occupation)) {
        $percent += 4;
    }
    if(!empty($activity_level)) {
        $percent += 4;
    }
    return $percent;
}

function get_status($val) {
    $arr = array(1 => 'Active', 2 => 'Inactive', 3 => 'Pending', 4 => 'Reject', 9 => 'Deleted');
    if(!empty($arr[$val])) {
        return $arr[$val];
    } else {
        return '';
    }
}

function report_header($object,$objDrawing) {
    $gdImage = imagecreatefrompng('../../'.$GLOBALS['ENV_VARS']['APP_DIR'].'app/images/logo.png');
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
    $report_date = get_display_date_time("d/m/Y H:i:s");
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
function report_header_sub_row($object,$column, $excel_row, $field_name, $h_align = true) {
    $object->getActiveSheet()->setCellValueByColumnAndRow($column, $excel_row, $field_name);
    $col = PHPExcel_Cell::stringFromColumnIndex($column);
    $object->getActiveSheet()->getStyle($col.$excel_row)->getFill()->applyFromArray(array(
        'type' => PHPExcel_Style_Fill::FILL_SOLID,
        'startcolor' => array(
             'rgb' => 'd6dce4'
        ),
    ));
    $styleArray = array(
        'font'  => array(
            'bold'  => true,
            'color' => array('rgb' => '000000'),
            
        ),
        'alignment' => array(
            'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
        )
    );
    if($h_align) {
        $styleArray['alignment']['horizontal'] = PHPExcel_Style_Alignment::HORIZONTAL_CENTER;
    }
    $object->getActiveSheet()->getStyle($col.$excel_row)->applyFromArray($styleArray);
    $object->getActiveSheet()->getRowDimension($excel_row)->setRowHeight(20);
    return $object;
}
function get_diagonisis_list($val) {
    $string = str_replace(['[',']','"'], ['','',''], $val);
    $arr = explode(',', $string);
    $arr_unq = array_unique(array_filter($arr));
    return implode(', ', $arr_unq);
}
function create_cache_sub_dir($path_key, $sub_dir = '') {
    $path = DB_CACHE_PATH[$path_key];
    if(!empty($sub_dir)) {
        $path .= $sub_dir.'/';
    }
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
function get_hour_format($doctor_id) {
    $setting_where = array(
                'setting_user_id' => $doctor_id,
                'setting_type' => 5
            );
    $ci = &get_instance();
    $get_setting_data = $ci->Common_model->get_single_row(TBL_SETTING, 'setting_id,setting_data', $setting_where);
    if (!empty($get_setting_data['setting_id'])) {
        return $get_setting_data['setting_data'];
    } else {
        return '2';
    }
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
            $ci->Common_model->insert_multiple(TBL_SETTING, $insert_setting_array);
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

function patient_age_group() {
    $arr = [
        "1" => "0-5",
        "2" => "5-14",
        "3" => "15-25",
        "4" => "26-40",
        "5" => "40-60",
        "6" => "60"
    ];
    return $arr;
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

function check_diff_multi($array1, $array2){
    $result = array();
    foreach($array1 as $key => $val) {
         if(isset($array2[$key])){
           if(is_array($val) && $array2[$key]){
               $result[$key] = check_diff_multi($val, $array2[$key]);
           }
       } else {
           $result[$key] = $val;
       }
    }
    return $result;
}

function get_time_slots($clinic_availability_list, $minTime, $maxTime, $minDuration) {
    $slots = array();
    foreach ($clinic_availability_list as $key => $value) {
        $slot = get_slots($value['clinic_availability_session_1_start_time'],$value['clinic_availability_session_1_end_time'], $minDuration);
        if(!empty($slots[$value['clinic_availability_week_day']])) {
            $slots[$value['clinic_availability_week_day']] = array_merge($slots[$value['clinic_availability_week_day']], $slot);
        } else {
            $slots[$value['clinic_availability_week_day']] = $slot;
        }
        if($value['clinic_availability_session_2_start_time']) {
            $slot = get_slots($value['clinic_availability_session_2_start_time'],$value['clinic_availability_session_2_end_time'], $minDuration);
            if(!empty($slots[$value['clinic_availability_week_day']])) {
                $slots[$value['clinic_availability_week_day']] = array_merge($slots[$value['clinic_availability_week_day']], $slot);
            } else {
                $slots[$value['clinic_availability_week_day']] = $slot;
            }
        }
    }
    function time_sort($a, $b) {
        return strtotime($a) - strtotime($b);
    }
    foreach ($slots as $key => $value) {
        usort($value, "time_sort");
        $slots[$key] = $value;
    }
    foreach ($slots as $key => $slot) {
        $new_slot_arr = [];
        foreach ($slot as $k => $value) {
            $new_slot_arr[] = compare_slot_time($value, $minTime, $maxTime, $minDuration);
        }
        $slots[$key] = $new_slot_arr;
    }
    return $slots;
}

function compare_slot_time($time, $starttime, $endtime, $duration) {
    $start_time = strtotime ($starttime);
    $end_time = strtotime ($endtime);
    $time_stamp = strtotime ($time);
    $add_mins  = $duration * 60;
    while ($start_time <= $end_time) {
        if($time_stamp == $start_time){
            return date ("H:i:s", $start_time);
            break;
        } elseif($start_time > $time_stamp) {
            $diff_min = ($start_time - $time_stamp) / 60;
            $diff_stamp = ($duration - $diff_min) * 60;
            return date ("H:i:s", ($time_stamp-$diff_stamp));
            break;
        }
        $start_time += $add_mins;
    }
}

function get_slots($starttime, $endtime, $duration) {
    $array_of_time = array();
    $start_time = strtotime ($starttime);
    $end_time = strtotime ($endtime);
    $add_mins = $duration * 60;
    while ($start_time <= $end_time) {
       $array_of_time[] = date ("H:i:s", $start_time);
       $start_time += $add_mins;
    }
    return $array_of_time;
}

function delete_past_prescription($appointment_id) {
    $file_path = PAST_PRESCRIPTION . "/" . $appointment_id."_prescription.pdf";
    delete_file_from_s3($file_path);
}

function create_shorturl($url) {
    $text_local_api_key = $GLOBALS['ENV_VARS']['TEXT_LOCAL_API_KEY'];
    $data = "apikey=".$text_local_api_key."&url=" . $url;
    $ch = curl_init('https://api.textlocal.in/create_shorturl/?' . $data);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    curl_close($ch);
    $result = json_decode($response);
    if(!empty($result->status) && $result->status == "success") {
        $log = LOG_FILE_PATH . 'shorturl_log_' . date('d-m-Y') . ".txt";
        $log_content = "\n========= START ".date('d-m-Y H:i:s')." ==========\n";
        $log_content .= $url."\n";
        $log_content .= $response."\n";
        $log_content .= "========= END =======\n";
        file_put_contents($log, $log_content, FILE_APPEND);
        return $result->shorturl;
    } else {
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

function get_file_full_path($path) {
    $arr = json_decode($path, true);
    if(!empty($arr) && is_array($arr)){
        if($arr['type'] == 1){
            include_once BUCKET_HELPER_PATH;
            $result = checkResource($arr['path'], $arr['name']);
            if($result){
                return $GLOBALS['ENV_VARS']['DOCUMENT_LOAD_S3_URL'].$arr['name'].'/'.$arr['path'];
            } else {
                $file_name = $arr['path'];
                if(strpos($file_name, 'anatomical_diagrams/images/') !== false){
                    $file_name = str_replace('anatomical_diagrams/images/', 'anatomical_diagram/', $file_name);
                    if(file_exists(UPLOAD_REL_PATH.'/'.$file_name)){
                        return $GLOBALS['ENV_VARS']['DOCUMENT_LOAD_SERVER_URL'].'uploads/'.$file_name;
                    }
                }
                if($arr['alternate'] && file_exists(UPLOAD_REL_PATH.'/'.$arr['path'])) {
                    return $GLOBALS['ENV_VARS']['DOCUMENT_LOAD_SERVER_URL'].'uploads/'.$arr['path'];
                }
                return ASSETS_PATH . 'web/img/no-image-placeholder.png';
            }
        } elseif($arr['type'] == 2) {
            $file_name = $arr['path'];
            if(strpos($file_name, 'anatomical_diagrams/images/') !== false){
                $file_name = str_replace('anatomical_diagrams/images/', 'anatomical_diagram/', $file_name);
                if(file_exists(UPLOAD_REL_PATH.'/'.$file_name)){
                    return $GLOBALS['ENV_VARS']['DOCUMENT_LOAD_SERVER_URL'].'uploads/'.$file_name;
                }
            }
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
function is_patient_reminder_access($patient_id) {
    $ci = &get_instance();
    $ci->load->model("Patient_model", "patient");
    $rs = $ci->patient->check_patient_reminder_access($patient_id);
    if(!empty($rs)) {
        $date = $rs[0]->user_plan_expiry_date;
        if(!empty($rs[0]->setting_value) ||  (!empty($date) && $date >= get_display_date_time("Y-m-d"))) {
            return true;
        }
        foreach ($rs as $key => $value) {
            $date = $value->caregiver_expiry_date;
            if(!empty($value->caregiver_setting_value) ||  (!empty($date) && $date >= get_display_date_time("Y-m-d"))) {
                return true;
                break;
            }
        }
    }
    return false;
}