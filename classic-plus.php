<?php

/**
 * Plugin Name:       ClassicPlus To Enable Classic Functions
 * Plugin URI:        https://wordpress.org/plugins/classic-plus/
 * Description:       A plug-in can enable all classic functions, and also contains some practical functions
 * Version:           1.1.2
 * Author:            Lzw.
 * Author URI:        https://lzwdot.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       classicplus
 * Domain Path:       /languages
 *
 * @package classicplus
 */

if (!defined('ABSPATH')) {
    exit;
}

class ClassicPlus
{
    public static $version = '1.1.2';
    public static $plugin_path = '';
    public static $plugin_url = '';
    public static $post_type = 'post';
    public static $meta_key = 'post_type';
    public static $meta_value = 'document';

    public static function plugin_init()
    {
        self::$plugin_path = plugin_dir_path(__FILE__);
        self::$plugin_url = plugin_dir_url(__FILE__);

        load_plugin_textdomain('classicplus', false, basename(dirname(__FILE__)) . '/languages');

        register_deactivation_hook(__FILE__, array(__CLASS__, 'deactivation_hook'));
        register_activation_hook(__FILE__, array(__CLASS__, 'activation_hook'));

        add_action('admin_enqueue_scripts', [__CLASS__, 'admin_enqueue_scripts']);
        add_action('wp_enqueue_scripts', [__CLASS__, 'wp_enqueue_scripts']);

        self::include_dependencies();
    }


    public static function activation_hook()
    {
    }

    public static function deactivation_hook()
    {
    }

    public static function wp_enqueue_scripts()
    {
        wp_enqueue_style('classicplus-fontend-style', ClassicPlus::$plugin_url . 'assets/css/fontend.css');
        wp_enqueue_style('classicplus-fontawesome-style', ClassicPlus::$plugin_url . 'assets/css/fontawesome-all.min.css');
    }

    public static function admin_enqueue_scripts()
    {
        wp_enqueue_style('classicplus-admin-style', ClassicPlus::$plugin_url . 'assets/css/admin.css');
    }

    public static function include_dependencies()
    {
        // libs
        include_once self::$plugin_path . 'libs/SettingsAPI.php';

        // commons
        include_once self::$plugin_path . 'includes/class.settings.php';
        include_once self::$plugin_path . 'includes/class.email.php';
        include_once self::$plugin_path . 'includes/class.link.php';
        include_once self::$plugin_path . 'includes/class.gravatar.php';
        include_once self::$plugin_path . 'includes/class.document.php';

        // admins
        include_once self::$plugin_path . 'includes/class.ajax.php';
        include_once self::$plugin_path . 'includes/class.login.php';
        include_once self::$plugin_path . 'includes/class.classic.php';

        // frontends
        include_once self::$plugin_path . 'includes/class.frontend.php';
        include_once self::$plugin_path . 'includes/class.post.php';
    }
}

ClassicPlus::plugin_init();
