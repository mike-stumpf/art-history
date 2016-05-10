<?php

/**
 * Enqueue scripts and styles.
 */
function arthistory_scripts() {
    wp_enqueue_style( 'arthistory-style-base', get_template_directory_uri().'/build/app.base.min.css' );
    wp_enqueue_style( 'arthistory-style-main', get_template_directory_uri().'/build/app.main.min.css' );
    wp_enqueue_script( 'arthistory-logic-base', get_template_directory_uri().'/build/app.base.min.js');
    wp_enqueue_script( 'arthistory-logic-main', get_template_directory_uri().'/build/app.main.min.js', array(), '20160509', true );

//    todo, load hbs templates and vis http://visjs.org/docs/timeline/#groups

}
add_action( 'wp_enqueue_scripts', 'arthistory_scripts' );

/**
 * custom admin css
 */
function arthistory_admin_css() { ?>
    <style>
        #menu-comments, #menu-tools, /* sidebar */
        #wp-admin-bar-wp-logo, #wp-admin-bar-comments, /*top bar*/
        #contextual-help-link-wrap    /* misc */
        {
            display: none!important;
        }
    </style>
<?php }

add_action('admin_head', 'arthistory_admin_css');
