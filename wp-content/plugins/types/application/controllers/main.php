<?php

/**
 * Main Types controller.
 *
 * Determines if we're in admin or front-end mode or if an AJAX call is being performed. Handles tasks that are common
 * to all three modes, if there are any.
 *
 * @since 2.0
 */
final class Types_Main {
	
	
	private static $instance;
	
	public static function get_instance() {
		if( null == self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}
	
	public static function initialize() {
		self::get_instance();
	}

	private function __clone() { }

	
	private function __construct() {

		add_action( 'after_setup_theme', array( $this, 'after_setup_theme' ), 10 );
		add_action( 'init', array( $this, 'on_init' ) );

	}



	/**
	 * Determine in which mode we are and initialize the right dedicated controller.
	 *
	 * @since 2.0
	 */
	public function on_init() {
		if( is_admin() ) {
			if( defined( 'DOING_AJAX' ) ) {
				Types_Ajax::initialize();
			} else {
				Types_Admin::initialize();
			}
		} else {
			Types_Frontend::initialize();
		}
	}


	/**
	 * Early loading actions.
	 * 
	 * Initialize the Toolset Common library with the new loader. 
	 * Initialize asset manager if we're not doing an AJAX call.
	 * 
	 * @since 2.0
	 */
	public function after_setup_theme() {
		Toolset_Common_Bootstrap::getInstance();

		// If an AJAX callback handler needs other assets, they should initialize the asset manager by themselves.
		if( !defined( 'DOING_AJAX' ) ) {
			Types_Assets::get_instance()->initialize_scripts_and_styles();
		}
	}


	/**
	 * In some cases, it may not be clear what legacy files are includes and what aren't. 
	 * 
	 * This method should make sure all is covered (add files when needed). Use only when necessary.
	 * 
	 * @since 2.0
	 */
	public function require_legacy_functions() {
		require_once WPCF_INC_ABSPATH . '/fields.php';
	}

}