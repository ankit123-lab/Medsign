<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of LanguageLoader
 * Created date : 2016-11-10
 */
class LanguageLoader {
    function initialize(){
        $ci = &get_instance();
        $ci->load->helper('language');
        $ci->lang->load($ci->config->item('language'));
    }
}