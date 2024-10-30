<?php

class ClassicPlus_Link
{
    public static function init()
    {
        if (!ClassicPlus_Settings::get_option('link_manager')) {
            return false;
        }

        add_filter('pre_option_link_manager_enabled', '__return_true');
    }
}

ClassicPlus_Link::init();
