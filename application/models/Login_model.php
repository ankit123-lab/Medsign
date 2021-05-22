<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Login_model
 *
 * @author Prashant Suthar
 */
class Login_model extends MY_Model {
    /*
     * Base table name
     * @var String
     */

    protected $table_name;
    protected $pickup_address_table;

    public function __construct() {
        parent::__construct();
    }
}
