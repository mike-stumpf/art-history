<?php

/**
 * Enqueue scripts and styles.
 */
function arthistory_scripts() {
	wp_enqueue_style( 'arthistory-style-base', get_template_directory_uri().'/build/app.base.min.css' );
    wp_enqueue_style( 'arthistory-style-main', get_template_directory_uri().'/build/app.main.min.css' );
    wp_enqueue_script( 'arthistory-logic-base', get_template_directory_uri().'/build/app.base.min.js');
	wp_enqueue_script( 'arthistory-logic-main', get_template_directory_uri().'/build/app.main.min.js', array(), '20160509', true );

}
add_action( 'wp_enqueue_scripts', 'arthistory_scripts' );