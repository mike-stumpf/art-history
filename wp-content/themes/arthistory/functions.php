<?php

/**
 * Enqueue scripts and styles.
 */
function arthistory_scripts() {
    wp_enqueue_style( 'arthistory-style-base', get_template_directory_uri().'/build/app.base.min.css' );
    wp_enqueue_style( 'arthistory-style-main', get_template_directory_uri().'/build/app.main.min.css' );
    wp_enqueue_script( 'arthistory-logic-base', get_template_directory_uri().'/build/app.base.min.js');
    wp_enqueue_script( 'arthistory-logic-handlebars', get_template_directory_uri().'/build/app.handlebars.min.js');
    wp_enqueue_script( 'arthistory-logic-main', get_template_directory_uri().'/build/app.main.min.js', array(), '20160509', true );
}
add_action( 'wp_enqueue_scripts', 'arthistory_scripts' );


/**
 * WordPress generate html titles 
 * https://make.wordpress.org/core/2014/10/29/title-tags-in-4-1/
 */
function theme_slug_setup() {
    add_theme_support( 'title-tag' );
}
add_action( 'after_setup_theme', 'theme_slug_setup' );


/**
 * custom admin css
 */
function arthistory_admin_css() { ?>
    <style>
        #menu-comments, #menu-tools, #menu-posts, /* sidebar */
        #wp-admin-bar-wp-logo, #wp-admin-bar-comments, /*top bar*/
        #contextual-help-link-wrap, /* misc */
        #types-information-table /* editor page */
        {
            display: none!important;
        }
    </style>
<?php }

add_action('admin_head', 'arthistory_admin_css');


/**
 * remove unnecessary wp code
 */
function disable_embeds_init() {

    // Remove the REST API endpoint.
    remove_action('rest_api_init', 'wp_oembed_register_route');

    // Turn off oEmbed auto discovery.
    // Don't filter oEmbed results.
    remove_filter('oembed_dataparse', 'wp_filter_oembed_result', 10);

    // Remove oEmbed discovery links.
    remove_action('wp_head', 'wp_oembed_add_discovery_links');

    // Remove oEmbed-specific JavaScript from the front-end and back-end.
    remove_action('wp_head', 'wp_oembed_add_host_js');
}

add_action('init', 'disable_embeds_init', 9999);

remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
remove_action( 'wp_print_styles', 'print_emoji_styles' );