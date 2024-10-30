<?php

class ClassicPlus_Post
{
    private static $post_options;

    public static function init()
    {
        if (is_admin()) {
            return false;
        }

        self::$post_options = ClassicPlus_Settings::get_section('post');

        add_filter('the_content', [__CLASS__, 'post_content'], 9);

        add_shortcode('list_category', [__CLASS__, 'list_category']);
        add_shortcode('list_catalog', [__CLASS__, 'list_catalog']);
    }

    public static function post_content($content = '')
    {
        $post_options = self::$post_options;

        // if archive
        if ((is_archive() || is_home() || is_search()) && ClassicPlus_Settings::is_enable('archive_display_excerpt', $post_options)) {
            $link = sprintf(
                '<a href="%1$s" class="more-link">%2$s</a>',
                esc_url(get_permalink(get_the_ID())),
                /* translators: %s: Post title. */
                sprintf(__('Continue reading %s'), '<span class="screen-reader-text">' . get_the_title(get_the_ID()) . '</span><span class="meta-nav">&rarr;</span>')
            );
            return wp_trim_words($content, 55, ' &hellip; ' . $link);
        }

        $post = get_post();
        //if post password
        if (post_password_required($post) && ClassicPlus_Settings::is_enable('split_protected_posts', $post_options)) {
            $content = explode('<!--more-->', $post->post_content)[0] . $content;
        }

        // if singular
        if (is_singular()) {
            // register document
            if (is_single() && ClassicPlus_Settings::get_option('register_document')) {
                $content .= ClassicPlus_Document::nav_document();
            }
        }

        // img prefix replace
        if (!empty($post_options['img_original_prefix']) && !empty($post_options['img_replace_prefix'])) {
            $content = preg_replace_callback(
                '/<img.*?src="(.*?)".*?alt="(.*?)".*?\/?>/i',
                function ($matches) use ($post_options) {
                    return @preg_replace("/{$post_options['img_original_prefix']}/i", esc_url($post_options['img_replace_prefix']), $matches[0]);
                },
                $content
            );
        }

        return $content;
    }

    public static function list_category($atts = [], $content = '')
    {
        $list = wp_list_categories([
            'echo'                => 0,
            'title_li'            => '',
            'hide_title_if_empty' => true
        ]);
        $content .= sprintf('<ul class="list-category">%1$s</ul>', $list);

        return $content;
    }

    public static function list_catalog($atts = [], $content = '')
    {
        global $post;

        $post_id = $post ? $post->ID : 0;
        $categories = is_single() && $post_id ? get_the_category($post->ID) : [];
        if (!empty($categories)) {
            foreach ($categories as $cate) {
                if ($cate->parent) $category = $cate;
            }
            if (!isset($category)) $category = $categories[0];

            if (isset($category->slug) && $category->slug != 'uncategorized') {
                $posts = get_posts([
                    'numberposts' => isset($atts['number']) ? $atts['number'] : get_option('posts_per_page'),
                    'order'       => 'ASC',
                    'category'    => $category->term_id
                ]);

                $content .= sprintf('<h2><i class="far fa-file-archive"></i> %1$s</h2><ul class="list-catalog">', $category->name);
                foreach ($posts as $post) {
                    $content .= sprintf(
                        '<li class="%1$s"><a href="%2$s">%3$s</a></li>',
                        $post_id == $post->ID ? 'current_item' : '',
                        esc_url(get_permalink($post)),
                        get_the_title($post)
                    );
                }
                $content .= '</ul>';
            }
        }

        return $content;
    }
}

ClassicPlus_Post::init();
