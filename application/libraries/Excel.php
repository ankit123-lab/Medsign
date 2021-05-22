<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

$path = dirname(dirname(APPPATH));
require_once $path . "/html/phpexcel/PHPExcel.php";

class Excel extends PHPExcel {

    public function __construct() {
        parent::__construct();
    }

}

?>