<?php
class Help extends CI_Controller {
 
    public function __construct() {
        parent::__construct();
    }
    
    function get_file_size($url) {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, TRUE);
        curl_setopt($ch, CURLOPT_NOBODY, TRUE);
        $data = curl_exec($ch);
        $size = curl_getinfo($ch, CURLINFO_CONTENT_LENGTH_DOWNLOAD);
        curl_close($ch);
        return $size;
    }
    function av($id) {
        if(!empty($_SERVER['HTTP_REFERER'])) {
            $id = encrypt_decrypt($id, 'decrypt');
            $file_url = 'https://s3.ap-south-1.amazonaws.com/medeasys3mock/help_av/'.$id.'.mp4';
            $path = 's3://medeasys3mock/help_av/'.$id.'.mp4';
            $fp = @fopen($file, 'rb');
            include_once BUCKET_HELPER_PATH;
            if (!($fp = registerStreamWrap($path))) {
                 die('Could not open stream for reading');
            }
            $size  = $this->get_file_size($file_url);
            $length = $size;           // Content length
            $start  = 0;               // Start byte
            $end    = $size - 1;       // End byte
            header('Content-type: video/mp4');
            //header("Accept-Ranges: 0-$length");
            header("Accept-Ranges: bytes");
            if (isset($_SERVER['HTTP_RANGE'])) {
                $c_start = $start;
                $c_end   = $end;
                list(, $range) = explode('=', $_SERVER['HTTP_RANGE'], 2);
                if (strpos($range, ',') !== false) {
                    header('HTTP/1.1 416 Requested Range Not Satisfiable');
                    header("Content-Range: bytes $start-$end/$size");
                    exit;
                }
                if ($range == '-') {
                    $c_start = $size - substr($range, 1);
                }else{
                    $range  = explode('-', $range);
                    $c_start = $range[0];
                    $c_end   = (isset($range[1]) && is_numeric($range[1])) ? $range[1] : $size;
                }
                $c_end = ($c_end > $end) ? $end : $c_end;
                if ($c_start > $c_end || $c_start > $size - 1 || $c_end >= $size) {
                    header('HTTP/1.1 416 Requested Range Not Satisfiable');
                    header("Content-Range: bytes $start-$end/$size");
                    exit;
                }
                $start  = $c_start;
                $end    = $c_end;
                $length = $end - $start + 1;
                fseek($fp, $start);
                header('HTTP/1.1 206 Partial Content');
            }
            header("Content-Range: bytes $start-$end/$size");
            header("Content-Length: ".$length);
            $buffer = 1024 * 8;
            while(!feof($fp) && ($p = ftell($fp)) <= $end) {
                if ($p + $buffer > $end) {
                    $buffer = $end - $p + 1;
                }
                set_time_limit(0);
                echo fread($fp, $buffer);
                flush();
            }
            fclose($fp);
        }
        exit();
    }
}