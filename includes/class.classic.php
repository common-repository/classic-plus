<?php

class ClassicPlus_Classic
{
    public static function init()
    {
        if (ClassicPlus_Settings::get_option('enable_classic_editor')) {
            add_filter('use_block_editor_for_post_type', '__return_false', 100);
            add_filter('gutenberg_can_edit_post_type', '__return_false', 100);
            remove_action('wp_enqueue_scripts', 'wp_common_block_scripts_and_styles');

            // editor style
            add_editor_style(ClassicPlus::$plugin_url . 'assets/css/editor-styles.css');
        }

        if (ClassicPlus_Settings::get_option('enable_classic_widgets')) {
            // Disables the block editor from managing widgets in the Gutenberg plugin.
            add_filter('gutenberg_use_widgets_block_editor', '__return_false');
            // Disables the block editor from managing widgets.
            add_filter('use_widgets_block_editor', '__return_false');
        }


    }
}

ClassicPlus_Classic::init();
