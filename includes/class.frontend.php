<?php

class ClassicPlus_FrontEnd
{
    private static $post_options;

    public static function init()
    {
        if (is_admin()) {
            return false;
        }

        self::$post_options = ClassicPlus_Settings::get_section('post');

        add_action('wp_enqueue_scripts', [__CLASS__, 'enqueue_scripts']);
    }

    public static function enqueue_scripts()
    {
        $post_options = self::$post_options;

        if (ClassicPlus_Settings::is_enable('highlight_style', $post_options)) {
            wp_enqueue_style('classicplus-highlight-style', ClassicPlus::$plugin_url . 'assets/css/prism/' . $post_options['highlight_style']);
            wp_enqueue_script('classicplus-highlight', ClassicPlus::$plugin_url . 'assets/js/prism.min.js', [], ClassicPlus::$version, true);
        }

        if (ClassicPlus_Settings::is_enable('html_code_demo', $post_options)) {
            wp_enqueue_script('classicplus-vue3', ClassicPlus::$plugin_url . 'assets/js/vue.global.prod.js', [], ClassicPlus::$version, true);
            wp_enqueue_script('classicplus-htmlDemo', ClassicPlus::$plugin_url . 'assets/js/htmlDemo.js', [], ClassicPlus::$version, true);
            wp_localize_script('classicplus-htmlDemo', 'classicPlus', [
                'code_preview' => __('Source code preview', 'classicplus'),
                'view_code'    => __('View source code', 'classicplus'),
            ]);
        }
    }
}

ClassicPlus_FrontEnd::init();
