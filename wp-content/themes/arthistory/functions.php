<?php

/**
 * Enqueue scripts and styles.
 */
function arthistory_scripts() {
    $scriptDate = '20160806';
    //css
    wp_enqueue_style(
        'arthistory-style-base',
        get_template_directory_uri().'/build/app.base.min.css',
        array(),
        $scriptDate,
        false
    );
    wp_enqueue_style(
        'arthistory-style-main',
        get_template_directory_uri().'/build/app.main.min.css',
        array(
            'arthistory-style-base'
        ),
        $scriptDate,
        false
    );
    //js
    wp_enqueue_script(
        'arthistory-logic-base',
        get_template_directory_uri().'/build/app.base.min.js',
        array(),
        $scriptDate,
        true
    );
    wp_enqueue_script(
        'arthistory-logic-handlebars',
        get_template_directory_uri().'/build/app.handlebars.min.js',
        array(
            'arthistory-logic-base'
        ),
        $scriptDate,
        true
    );
    wp_enqueue_script(
        'arthistory-logic-main',
        get_template_directory_uri().'/build/app.main.min.js',
        array(
            'arthistory-logic-base',
            'arthistory-logic-handlebars'
        ),
        $scriptDate,
        true
    );
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


/**
 * add custom settings
 */
function register_default_timeline_setting() {
    add_settings_section(
        'ah_settings_section', // Section ID
        'Art History Settings', // Section Title
        'ah_section_options_callback', // Callback
        'general' // page
    );

    add_settings_field(
        'ah_default_timeline', // Option ID
        'Default Timeline Slug', // Label
        'ah_default_timeline_callback', // callback
        'general', // Page
        'ah_settings_section', // Name of our section
        array(
            'type' => 'string',
            'sanitize_callback' => 'sanitize_text_field',
            'default' => NULL,
            'ah_default_timeline' // Should match Option ID
        )
    );

    register_setting('general','ah_default_timeline', 'esc_attr');
}


function ah_section_options_callback() {
    echo '<p>Custom theme options</p>';
}

function ah_default_timeline_callback($args) {
    $option = get_option($args[0]);
    echo '<input type="text" id="'. $args[0] .'" name="'. $args[0] .'" value="' . $option . '" />';
}

add_action( 'admin_init', 'register_default_timeline_setting' );