<?php

class ClassicPlus_Gravatar
{
    private static $url;

    public static function init()
    {
        self::$url = ClassicPlus_Settings::get_option('gravatar_mirror_server');
        if (!self::$url) {
            return false;
        }

        add_filter('get_avatar', [__CLASS__, 'get_avatar'], 10, 3);
    }

    public static function get_avatar($avatar)
    {
        return str_replace([
            'www.gravatar.com',
            '0.gravatar.com',
            '1.gravatar.com',
            '2.gravatar.com',
            'secure.gravatar.com',
            'cn.gravatar.com',
            'gravatar.com',
        ], self::$url, $avatar);
    }
}

ClassicPlus_Gravatar::init();

