<?php

class ClassicPlus_Login
{
    private static $login_ip = 'classicplus_login_ip';
    private static $retries = 0;
    private static $time_diff = 60;  // a hours

    public static function init()
    {
        $options = ClassicPlus_Settings::get_section('login');
        if (empty($options) || !ClassicPlus_Settings::is_enable('limit_login_attempts',$options)) {
            return false;
        }

        self::$retries = $options['login_attempts_retries'];
        self::$time_diff = self::$time_diff * $options['login_time_diff'];

        add_action('wp_login_failed', [__CLASS__, 'login_failed']);
        add_action('wp_login', [__CLASS__, 'reset_retry'], 10, 2);

        add_filter('authenticate', [__CLASS__, 'authenticate'], 99, 3);
        add_filter('shake_error_codes', [__CLASS__, 'error_codes']);
    }

    public static function login_failed($username)
    {
        if (empty($username)) {
            return $username;
        }

        $ip2long = self::get_ip_long();
        $options = get_option(self::$login_ip);
        if (!isset($options[$ip2long])) {
            $options = [];
            $options[$ip2long]['retries'] = 0;
        }

        // increase 1
        $options[$ip2long]['retries'] += 1;
        $options[$ip2long]['time'] = time();

        update_option(self::$login_ip, $options);
    }

    public static function authenticate($user, $username, $password)
    {
        if (empty($username) || empty($password)) {
            return $user;
        }

        $ip2long = self::get_ip_long();
        $options = get_option(self::$login_ip);

        // no data
        if (!isset($options[$ip2long])) {
            return $user;
        }

        // time diff
        if (time() - $options[$ip2long]['time'] > self::$time_diff) {
            self::reset_retry($username, $user);
            return $user;
        }

        // to many retries
        if ($options[$ip2long]['retries'] >= self::$retries) {
            return new WP_Error('many_retries', __('<strong>ERROR</strong>: Too many retries.', 'classicplus'));
        }

        return $user;
    }

    public static function reset_retry($username = '', $user = '')
    {
        if (empty($username) || empty($user)) {
            return $user;
        }

        $ip2long = self::get_ip_long();
        $options = get_option(self::$login_ip);

        if (isset($options[$ip2long])) {
            $options[$ip2long]['retries'] = 0;
            $options[$ip2long]['time'] = time();

            update_option(self::$login_ip, $options);
        }
    }

    public static function error_codes($error_codes)
    {
        $error_codes[] = 'many_retries';
        return $error_codes;
    }

    public static function get_ip_long()
    {
        return isset($_SERVER['REMOTE_ADDR']) ? ip2long($_SERVER['REMOTE_ADDR']) : null;
    }
}

ClassicPlus_Login::init();
