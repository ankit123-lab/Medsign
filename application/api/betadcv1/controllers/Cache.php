<?php
class Cache extends CI_Controller {

    public function __construct() {
        parent::__construct();
        
    }

    public function clear($table_name) {
    	if(!empty($table_name)) {
	    	$this->config->set_item('cache_path', APPPATH. 'cache/'.$table_name.'/');
	    	$this->load->driver('cache');
	        $this->cache->file->clean();
    	}
    	die('Cache cleared');
    }
}
