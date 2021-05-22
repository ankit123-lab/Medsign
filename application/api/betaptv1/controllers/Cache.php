<?php
class Cache extends CI_Controller {

    public function __construct() {
        parent::__construct();
        
    }

    public function clear() {
    	$this->load->driver('cache');
        $this->cache->file->clean();
    	die('Cache cleared');
    }
}
