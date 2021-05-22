<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Extend the Security Class. Use this if u re using CSRF protection
 * @updated by budy k
 * @link https://gist.github.com/codetrash/6e12ce0c559ea4eab7c0927fd3b7cdca
 */
class MY_Security extends CI_Security {

    //overriding the normal csrf_verify, this gets automatically called in the Input library's constructor
    //verifying on POST and PUT and DELETE
    public function csrf_verify() {
        
        //attach to global POST variable
        //This will also handle JSON payload from angular
        $_POST = array_merge($_POST, (array) json_decode(trim(file_get_contents('php://input')), true));        
        //$_POST = array_merge($_POST, $this->input->input_stream());
        $useragent = $_SERVER['HTTP_USER_AGENT'];
        if (isset($_POST['device_type']) && $_POST['device_type'] == 'web') {

            $request_method = strtoupper($_SERVER['REQUEST_METHOD']);
            //If it is GET, ignore the rest
            if ($request_method == 'GET' || $request_method == 'HEAD' || $request_method == 'OPTIONS') {
                return $this->csrf_set_cookie();
            }
            // Check if URI has been whitelisted from CSRF checks
            if ($exclude_uris = config_item('csrf_exclude_uris')) {
                $uri = load_class('URI', 'core');
                if (in_array($uri->uri_string(), $exclude_uris)) {
                    $this->csrf_set_cookie();
                    return $this;
                }
            }
            //Double submit cookie method: COOKIE needs to exist and at least either
            //POST or SERVER needs to exist and at least one of the POST or SERVER must match the COOKIE

            if (
                    !isset($_COOKIE[$this->_csrf_cookie_name]) ||
                    (
                    !isset($_POST[$this->_csrf_cookie_name]) &&
                    !isset($_SERVER['HTTP_X_CSRF_TOKEN'])
                    )
            ) {
                $this->csrf_show_error();
            }

            //if CSRF token was in the POST, then it needs to match the cookie
            if (isset($_POST[$this->_csrf_token_name])) {
                if ($_POST[$this->_csrf_token_name] !== $_COOKIE[$this->_csrf_cookie_name]) {
                    $this->csrf_show_error();
                }
            }
            //if CSRF token was in the SERVER (headers), then it needs to match the cookie
            if (isset($_SERVER['HTTP_X_CSRF_TOKEN'])) {
                if ($_SERVER['HTTP_X_CSRF_TOKEN'] !== $_COOKIE[$this->_csrf_cookie_name]) {
                    $this->csrf_show_error();
                }
            }

            // We kill this since we're done and we don't want to polute the _POST array
            unset($_POST[$this->_csrf_token_name]);
            if (config_item('csrf_regenerate')) {
                unset($_COOKIE[$this->_csrf_cookie_name]);
                $this->_csrf_hash = NULL; //must be null
            }
            $this->_csrf_set_hash();
            $this->csrf_set_cookie();
            log_message('debug', 'CSRF token verified');

            return $this;
        } else if (isset($_POST['device_type']) && ($_POST['device_type'] == 'ios' || $_POST['device_type'] == 'android')) {
            
        } else if (isset($_POST['access_token']) && $_POST['access_token'] == 'medeasy') {
            
        } else {
            $request_method = strtoupper($_SERVER['REQUEST_METHOD']);
            //If it is GET, ignore the rest
            if ($request_method == 'GET' || $request_method == 'HEAD' || $request_method == 'OPTIONS') {
                return $this->csrf_set_cookie();
            }
            // Check if URI has been whitelisted from CSRF checks
            if ($exclude_uris = config_item('csrf_exclude_uris')) {
                $uri = load_class('URI', 'core');
                if (in_array($uri->uri_string(), $exclude_uris)) {
                    $this->csrf_set_cookie();
                    return $this;
                }
            }
            //Double submit cookie method: COOKIE needs to exist and at least either
            //POST or SERVER needs to exist and at least one of the POST or SERVER must match the COOKIE

            if (
                    !isset($_COOKIE[$this->_csrf_cookie_name]) ||
                    (
                    !isset($_POST[$this->_csrf_cookie_name]) &&
                    !isset($_SERVER['HTTP_X_CSRF_TOKEN'])
                    )
            ) {
                $this->csrf_show_error();
            }

            //if CSRF token was in the POST, then it needs to match the cookie
            if (isset($_POST[$this->_csrf_token_name])) {
                if ($_POST[$this->_csrf_token_name] !== $_COOKIE[$this->_csrf_cookie_name]) {
                    $this->csrf_show_error();
                }
            }
            //if CSRF token was in the SERVER (headers), then it needs to match the cookie
            if (isset($_SERVER['HTTP_X_CSRF_TOKEN'])) {
                if ($_SERVER['HTTP_X_CSRF_TOKEN'] !== $_COOKIE[$this->_csrf_cookie_name]) {
                    $this->csrf_show_error();
                }
            }

            // We kill this since we're done and we don't want to polute the _POST array
            unset($_POST[$this->_csrf_token_name]);
            if (config_item('csrf_regenerate')) {
                unset($_COOKIE[$this->_csrf_cookie_name]);
                $this->_csrf_hash = NULL; //must be null
            }
            $this->_csrf_set_hash();
            $this->csrf_set_cookie();
            log_message('debug', 'CSRF token verified');

            return $this;
        }
    }

}
