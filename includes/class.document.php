<?php

class ClassicPlus_Document
{
    public static function init()
    {
        if (!ClassicPlus_Settings::get_option('register_document')) {
            return false;
        }

        add_action('init', [__CLASS__, 'register_post_type']);
        add_action('pre_get_posts', [__CLASS__, 'pre_get_posts']);
        add_action('admin_menu', [__CLASS__, 'admin_menus']);
        add_action('admin_enqueue_scripts', [__CLASS__, 'enqueue_scripts']);

        add_shortcode('list_document', [__CLASS__, 'list_document']);
        add_shortcode('nav_document', [__CLASS__, 'nav_document']);

        add_filter('widget_posts_args', [__CLASS__, 'widget_posts_args']);
        add_filter('admin_init', [__CLASS__, 'admin_init']);
        add_filter('quick_edit_dropdown_pages_args', [__CLASS__, 'quick_edit_dropdown_pages_args']);
    }

    public static function admin_init()
    {
        global $pagenow;

        if (is_admin() && $pagenow == 'edit.php' && empty($_GET)) {
            wp_redirect(admin_url('edit.php?orderby=date&order=desc'));
            exit;
        }
    }

    public static function admin_menus()
    {
        add_posts_page(__('Documentation'), __('Documentation'), 'publish_posts', 'classicplus_document', [__CLASS__, 'page_document']);
    }

    public static function page_document()
    {
        include_once ClassicPlus::$plugin_path . '/views/documents.php';
    }

    public static function enqueue_scripts($hook)
    {
        if ('posts_page_classicplus_document' != $hook) {
            return false;
        }

        wp_enqueue_script('classicplus-vue2', ClassicPlus::$plugin_url . 'assets/js/vue.min.js', [], ClassicPlus::$version, true);
        wp_enqueue_script('classicplus-sweetalert', ClassicPlus::$plugin_url . 'assets/js/sweetalert2.min.js', ['jquery'], ClassicPlus::$version, true);
        wp_enqueue_script('classicplus-document', ClassicPlus::$plugin_url . 'assets/js/document.js', ['jquery', 'jquery-ui-sortable', 'wp-util'], ClassicPlus::$version, true);
        wp_localize_script('classicplus-document', 'classicPlus', [
            'edit_url'     => admin_url('post.php?action=edit&post='),
            'view_url'     => home_url('/?p='),
            'document_url' => admin_url('admin.php?page=classicplus_document&post='),

            'post_data'   => ClassicPlus_Ajax::document_list(isset($_GET['post']) ? absint($_GET['post']) : -1),
            'post_status' => __('Publish'),
            'ajax_nonce'  => wp_create_nonce('classicplus-ajax-nonce'),

            'placeholder_value' => __('Enter title here', 'classicplus'),
            'delete_title'      => __('Are you sure?', 'classicplus'),
            'button_cancel'     => __('Cancel', 'classicplus'),
            'button_confirm'    => __('Yes', 'classicplus'),
            'button_delete'     => __('Yes, delete it!', 'classicplus'),

            'enter_document_title' => __('Enter document title', 'classicplus'),
            'delete_document_text' => __('Are you sure to delete the entire document? Sections and articles inside this document will be deleted too!', 'classicplus'),

            'enter_section_title' => __('Enter section title', 'classicplus'),
            'delete_section_text' => __('Are you sure to delete the entire section? Articles inside this section will be deleted too!', 'classicplus'),

            'enter_article_title' => __('Enter article title', 'classicplus'),
            'delete_article_text' => __('Are you sure to delete the article?', 'classicplus'),

            'post_deleted_text' => __('This posts has been deleted', 'classicplus'),
        ]);
    }

    public static function register_post_type()
    {
        register_post_type(ClassicPlus::$post_type, array(
            'labels'                => array_merge((array)get_post_type_labels(new WP_Post_Type(ClassicPlus::$post_type)), [
                'name_admin_bar' => _x('Post', 'add new from admin bar'),
            ]),
            'public'                => true,
            '_builtin'              => true, /* internal use only. don't use this when registering your own post type. */
            '_edit_link'            => 'post.php?post=%d', /* internal use only. don't use this when registering your own post type. */
            'capability_type'       => 'post',
            'map_meta_cap'          => true,
            'menu_position'         => 5,
            'hierarchical'          => true,
            'rewrite'               => false,
            'query_var'             => false,
            'delete_with_user'      => true,
            'supports'              => array('title', 'editor', 'author', 'page-attributes', 'thumbnail', 'excerpt', 'trackbacks', 'custom-fields', 'comments', 'revisions', 'post-formats'),
            'show_in_rest'          => true,
            'rest_base'             => 'posts',
            'rest_controller_class' => 'WP_REST_Posts_Controller',
        ));
    }

    public static function pre_get_posts($query)
    {
        if (!$query->is_main_query()) {
            return false;
        }

        if (!is_archive()) {
        } else {
            return false;
        }

        $post_ids = self::get_all_document_ids();
        if (!empty($post_ids) && !is_single()) {
            $query->set('post__not_in', $post_ids);
        }
    }

    public static function widget_posts_args($args)
    {
        $post_ids = self::get_all_document_ids();
        if (!empty($post_ids)) {
            $args['post__not_in'] = $post_ids;
        }

        return $args;
    }

    public static function quick_edit_dropdown_pages_args($dropdown_args)
    {
        $dropdown_args = array_merge($dropdown_args,
            ['meta_key'   => ClassicPlus::$meta_key,
             'meta_value' => ClassicPlus::$meta_value
            ]
        );

        return $dropdown_args;
    }

    public static function get_all_document_ids()
    {
        $posts = get_posts([
            'meta_key'    => ClassicPlus::$meta_key,
            'meta_value'  => ClassicPlus::$meta_value,
            'numberposts' => -1
        ]);

        $ids = [];
        if (!empty($posts)) {
            $ids = array_column($posts, 'ID');
        }

        return $ids;
    }

    public static function list_document($atts = [], $content = '')
    {
        $list = wp_list_pages(
            [
                'echo'         => 0,
                'title_li'     => '',
                'post_type'    => ClassicPlus::$post_type,
                'meta_key'     => ClassicPlus::$meta_key,
                'meta_value'   => ClassicPlus::$meta_value,
                'sort_column'  => 'menu_order, post_date',
//                'show_date' => true,
                'link_before'  => '<i class="fas fa-book"></i> ',
                'hierarchical' => true
            ]
        );

        if (!empty($list)) {
            $content .= sprintf('<div class="list-document"><ul>%1$s</ul></div>', $list);
        }

        return $content;
    }

    public static function nav_document($atts = [], $content = '')
    {
        if (isset($atts['post_id']) && $atts['post_id'] > 0) {
            $parent_id = intval($atts['post_id']);
        } else {
            $parent_id = $post_id = intval(get_post()->ID);
            $ancestors = get_post_ancestors($post_id);

            if (!empty($ancestors)) {
                $root = count($ancestors) - 1;
                $parent_id = $ancestors[$root];
            }
        }

        $posts = get_posts(
            [
                'post_parent' => $parent_id,
                'post_type'   => ClassicPlus::$post_type
            ]
        );

        if (!empty($posts)) {
            $parent_post = get_post($parent_id);
            $list = wp_list_pages(
                [
                    'echo'        => 0,
                    'child_of'    => $parent_id,
                    'title_li'    => '',
                    'post_type'   => ClassicPlus::$post_type,
                    'sort_column' => 'menu_order, post_date',
//                    'show_date' => true,
                    'link_before' => '<i class="far fa-file-alt"></i> ',
                ]
            );

            $content .= sprintf('<div class="nav-document"><h3>%1$s：<a href="%2$s">%3$s</a></h3><ul>%4$s</ul></div>', __('Documentation'), esc_url(get_permalink($parent_post)), get_the_title($parent_post), $list);
        }

        return $content;
    }
}

ClassicPlus_Document::init();
