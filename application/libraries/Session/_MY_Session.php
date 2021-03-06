<?php

class MY_Session extends CI_Session {

    public function __construct(array $params = array()) {
        parent::__construct($params);
    }

    /**
     * Configuration
     *
     * Handle input parameters and configuration defaults
     *
     * @param   array   &$params    Input parameters
     * @return  void
     */
    protected function _configure(&$params) {

        $ci = & get_instance();
        $ci->load->model('Common_model');

        $column_slider = array(
            'doctor_settings_inactivity_duration',
        );

        $row = $ci->Common_model->get_single_row(TBL_DOCTOR_SETTINGS, $column_slider, array('doctor_settings_user_id' => 1));

        $phppos_session_expiration = NULL;

        if (!empty($row)) {
            if (is_numeric($row['doctor_settings_inactivity_duration'])) {
                $phppos_session_expiration = (int) $row['doctor_settings_inactivity_duration'] * 60;
            }
        }

        $expiration = $phppos_session_expiration !== NULL ? $phppos_session_expiration : config_item('sess_expiration');

        if (isset($params['cookie_lifetime'])) {
            $params['cookie_lifetime'] = (int) $params['cookie_lifetime'];
        } else {
            $params['cookie_lifetime'] = (!isset($expiration) && config_item('expire_on_close')) ? 0 : (int) $expiration;
        }

        isset($params['cookie_name']) OR $params['cookie_name'] = config_item('cookie_name');
        if (empty($params['cookie_name'])) {
            $params['cookie_name'] = ini_get('session.name');
        } else {
            ini_set('session.name', $params['cookie_name']);
        }

        isset($params['cookie_path']) OR $params['cookie_path'] = config_item('cookie_path');
        isset($params['cookie_domain']) OR $params['cookie_domain'] = config_item('cookie_domain');
        isset($params['cookie_secure']) OR $params['cookie_secure'] = (bool) config_item('cookie_secure');

        session_set_cookie_params(
                $params['cookie_lifetime'], $params['cookie_path'], $params['cookie_domain'], $params['cookie_secure'], TRUE // HttpOnly; Yes, this is intentional and not configurable for security reasons
        );

        if (empty($expiration)) {
            $params['expiration'] = (int) ini_get('session.gc_maxlifetime');
        } else {
            $params['expiration'] = (int) $expiration;
            ini_set('session.gc_maxlifetime', $expiration);
        }

        $params['match_ip'] = (bool) (isset($params['match_ip']) ? $params['match_ip'] : config_item('match_ip'));

        isset($params['save_path']) OR $params['save_path'] = config_item('save_path');
        $params['expire_on_close'] = true;

        $this->_config = $params;

        ini_set('session.use_trans_sid', 0);
        ini_set('session.use_strict_mode', 1);
        ini_set('session.use_cookies', 1);
        ini_set('session.use_only_cookies', 1);
        ini_set('session.hash_function', 1);
        ini_set('session.hash_bits_per_character', 4);
    }

}
