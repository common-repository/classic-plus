<?php


class ClassicPlus_Settings
{
    private static $settings_api;

    public static function settings_sections()
    {
        $sections = [
            'basics'  => [
                'id'    => 'classicplus_basics',
                'title' => __('Basic Settings', 'classicplus')
            ],
            'post' => [
                'id'    => 'classicplus_content',
                'title' => __('Post Content', 'classicplus')
            ],
            'login'   => [
                'id'    => 'classicplus_login',
                'title' => __('Login Limit', 'classicplus')
            ],
            'smtp'    => [
                'id'    => 'classicplus_smtp',
                'title' => __('SMTP Settings', 'classicplus')
            ],
        ];
        return $sections;
    }

    /**
     * Returns all the settings fields
     *
     * @return array settings fields
     */
    public static function settings_fields()
    {
        $settings_sections = self::settings_sections();
        $highlight_files = list_files(ClassicPlus::$plugin_path . 'assets/css/prism', 2);

        sort($highlight_files);
        $highlight_styles = ['' => __('Please select a highlight style')];
        foreach ($highlight_files as $v) {
            $key = substr($v, strripos($v, 'prism/') + strlen('prism/'));
            $key && $highlight_styles[$key] = $key;
        }

        $settings_fields = [
            $settings_sections['basics']['id'] => [
                [
                    'name'  => 'register_document',
                    'label' => __('Register Document', 'classicplus'),
                    'desc'  => __('Register hierarchical posts to build documents, two short codes: <code>[list_document]</code> and <code>[nav_document]</code>', 'classicplus'),
                    'type'  => 'checkbox',
                ],
                [
                    'name'  => 'link_manager',
                    'label' => __('Link Manager', 'classicplus'),
                    'desc'  => __('Enable', 'classicplus'),
                    'type'  => 'checkbox',
                ],
                [
                    'name'  => 'enable_classic_editor',
                    'label' => __('Enable Classic Editor', 'classicplus'),
                    'desc'  => __('Enable', 'classicplus'),
                    'type'  => 'checkbox',
                ],
                [
                    'name'  => 'enable_classic_widgets',
                    'label' => __('Enable Classic Widgets', 'classicplus'),
                    'desc'  => __('Enable', 'classicplus'),
                    'type'  => 'checkbox',
                ],
                [
                    'name'    => 'gravatar_mirror_server',
                    'label'   => __('Gravatar Mirror Server', 'classicplus'),
                    'type'    => 'select',
                    'default' => '',
                    'options' => [
                        ''                  => __('Please select a mirror server'),
                        'cravatar.cn'       => __('cravatar.cn'),
                        'gravatar.loli.net' => __('gravatar.loli.net'),
                        'sdn.geekzu.org'    => __('sdn.geekzu.org'),
                    ]
                ],
            ],

            $settings_sections['post']['id'] => [
                [
                    'name'  => 'archive_display_excerpt',
                    'label' => __('Archive Show Excerpt', 'classicplus'),
                    'desc'  => __('In archive and home page display the post excerpt, two short codes: <code>[list_category]</code> and <code>[list_catalog]</code>', 'classicplus'),
                    'type'  => 'checkbox',
                ],
                [
                    'name'  => 'split_protected_posts',
                    'label' => __('Split Protected Posts', 'classicplus'),
                    'desc'  => __('Password protected posts only the content after <code>&lt;--more--&gt;</code> require password', 'classicplus'),
                    'type'  => 'checkbox',
                ],
                [
                    'name'  => 'html_code_demo',
                    'label' => __('Html Code Demo', 'classicplus'),
                    'desc'  => __('Enable, Use vuejs and <code>&lt;div class="html-demo"&gt;&lt;/div&gt;</code> to preview html code', 'classicplus'),
                    'type'  => 'checkbox',
                ],
                [
                    'name'    => 'highlight_style',
                    'label'   => __('Highlight Style', 'classicplus'),
                    'type'    => 'select',
                    'default' => '',
                    'options' => $highlight_styles
                ],
                [
                    'name'              => 'img_original_prefix',
                    'label'             => __('Img Src Prefix', 'classicplus'),
                    'placeholder'       => __('Original prefix, Use regular expression', 'classicplus'),
                    'type'              => 'text',
                    'sanitize_callback' => 'sanitize_text_field'
                ],
                [
                    'name'              => 'img_replace_prefix',
                    'desc'              => __('Img url prefix is temporarily replaced before output', 'classicplus'),
                    'placeholder'       => __('Replace prefix', 'classicplus'),
                    'type'              => 'text',
                    'sanitize_callback' => 'sanitize_text_field'
                ],
            ],

            $settings_sections['login']['id'] => [
                [
                    'name'  => 'limit_login_attempts',
                    'label' => __('Limit Login Attempts', 'classicplus'),
                    'desc'  => __('Enable', 'classicplus'),
                    'type'  => 'checkbox',
                ],
                [
                    'name'              => 'login_attempts_retries',
                    'label'             => __('Login Attempts Retries', 'classicplus'),
                    'desc'              => __('Number of retries allowed ', 'classicplus'),
                    'min'               => 0,
                    'step'              => 1,
                    'default'           => 5,
                    'type'              => 'number',
                    'sanitize_callback' => 'floatval'
                ],
                [
                    'name'              => 'login_time_diff',
                    'label'             => __('Login Time Diff', 'classicplus'),
                    'desc'              => __('Time diff of retries allowed, Unit: <b>minutes</b> ', 'classicplus'),
                    'min'               => 0,
                    'step'              => 1,
                    'default'           => 10,
                    'type'              => 'number',
                    'sanitize_callback' => 'floatval'
                ],
            ],

            $settings_sections['smtp']['id'] => [
                [
                    'name'              => 'smtp_from',
                    'label'             => __('From Email Address', 'classicplus'),
                    'type'              => 'text',
                    'sanitize_callback' => 'sanitize_text_field'
                ],
                [
                    'name'              => 'smtp_from_name',
                    'label'             => __('From Name', 'classicplus'),
                    'type'              => 'text',
                    'default'           => get_bloginfo('name'),
                    'sanitize_callback' => 'sanitize_text_field'
                ],
                [
                    'name'              => 'smtp_host',
                    'label'             => __('SMTP Host', 'classicplus'),
                    'type'              => 'text',
                    'default'           => 'smtp.example.com',
                    'sanitize_callback' => 'sanitize_text_field'
                ],
                [
                    'name'    => 'smtp_secure',
                    'label'   => __('Type of Encryption', 'classicplus'),
                    'type'    => 'radio',
                    'default' => 'ssl',
                    'options' => [
                        ''    => 'None',
                        'ssl' => 'SSL',
                        'tls' => 'TLS'
                    ]
                ],
                [
                    'name'              => 'smtp_port',
                    'label'             => __('SMTP Port', 'classicplus'),
                    'desc'              => __('Default port: 25, SSL: 465, TLS: 587', 'classicplus'),
                    'default'           => 465,
                    'type'              => 'number',
                    'sanitize_callback' => 'floatval'
                ],
                [
                    'name'    => 'smtp_auth',
                    'label'   => __('SMTP Authentication', 'classicplus'),
                    'type'    => 'radio',
                    'default' => 1,
                    'options' => [
                        0 => 'No',
                        1 => 'Yes',
                    ]
                ],
                [
                    'name'              => 'smtp_username',
                    'label'             => __('SMTP Username', 'classicplus'),
                    'type'              => 'text',
                    'sanitize_callback' => 'sanitize_text_field'
                ],
                [
                    'name'              => 'smtp_password',
                    'label'             => __('SMTP Password', 'classicplus'),
                    'type'              => 'password',
                    'sanitize_callback' => 'sanitize_text_field'
                ],
                [
                    'name' => 'html',
                    'desc' => __('<strong>Test Email:</strong> Maybe you can click <a href="' . esc_url(wp_lostpassword_url()) . '" target="_blank">Lost your password?</a>', 'classicplus'),
                    'type' => 'html'
                ],
            ],
        ];

        return $settings_fields;
    }


    public static function init()
    {
        if (!is_admin()) {
            return false;
        }
        self::$settings_api = new ClassicPlus_SettingsAPI();

        add_action('admin_init', [__CLASS__, 'admin_init']);
        add_action('admin_menu', [__CLASS__, 'admin_menus']);

        add_filter('plugin_action_links', [__CLASS__, 'plugin_action_links'], 10, 2);
    }

    public static function admin_init()
    {
        //set the settings
        self::$settings_api->set_sections(self::settings_sections());
        self::$settings_api->set_fields(self::settings_fields());

        //initialize settings
        self::$settings_api->admin_init();
    }

    public static function admin_menus()
    {
        add_options_page(__('ClassicPlus Settings', 'classicplus'), __('ClassicPlus', 'classicplus'), 'manage_options', 'classicplus', [__CLASS__, 'page_settings']);
    }

    public static function plugin_action_links($links, $file)
    {
        if ($file == plugin_basename(ClassicPlus::$plugin_path . '/classic-plus.php')) {
            $links[] = '<a href="' . esc_url(add_query_arg(['page' => 'classicplus'], admin_url('options-general.php'))) . '">' . esc_html__('Settings') . '</a>';
        }

        return $links;
    }

    public static function page_settings()
    {
        include_once ClassicPlus::$plugin_path . '/views/settings.php';
    }

    public static function get_section($section = '', $default = false)
    {
        $settings_sections = self::settings_sections();
        $section = $settings_sections[$section ? $section : 'basics']['id'];

        return get_option($section, $default);
    }

    public static function is_enable($option, $section)
    {
        $option = isset($section[$option]) ? $section[$option] : false;

        return 'off' === $option ? false : ('on' === $option ? true : $option);
    }

    public static function get_option($option, $section = '', $default = false)
    {
        $section = self::get_section($section, $default);

        return self::is_enable($option, $section);
    }
}

ClassicPlus_Settings::init();


