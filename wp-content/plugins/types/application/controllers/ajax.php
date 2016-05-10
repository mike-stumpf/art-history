<?php

/**
 * Main AJAX call controller for Types.
 * 
 * All AJAX actions should be defined as constants and callbacks should be placed here. However, more complex logic
 * should probably be handled outside of this class, here you need only to parse arguments, authenticate, validate the
 * input and handle sending the response. All callbacks must use the ajax_begin() and ajax_finish() methods.
 *
 * @since 2.0
 */
final class Types_Ajax {


	const CALLBACK_FIELD_CONTROL_ACTION = 'field_control_action';
	
	
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
		$this->register_callbacks();
	}


	/**
	 * Register all callbacks. 
	 * 
	 * Each callback is registered as a "types_{$callback}" action and needs to have a "callback_{$callback_name}"
	 * method in this class.
	 * 
	 * @since 2.0
	 */
	private function register_callbacks() {

		$callbacks = array(
			self::CALLBACK_FIELD_CONTROL_ACTION
		);

		foreach( $callbacks as $callback_name ) {
			add_action( 'wp_ajax_types_' . $callback_name, array( $this, 'callback_' . $callback_name ) );
		}

	}
	
	
	public function get_action_js_name( $action ) {
		return 'types_' . $action;
	}



	/**
	 * Perform basic authentication check.
	 *
	 * Check user capability and nonce. Dies with an error message (wp_json_error() by default) if the authentization
	 * is not successful.
	 *
	 * @param array $args Arguments (
	 *     @type string $nonce_name Name of the nonce that should be verified. Mandatory
	 *     @type string $nonce_parameter Name of the parameter containing nonce value.
	 *         Optional, defaults to "wpnonce".
	 *     @type string $parameter_source Determines where the function should look for the nonce parameter.
	 *         Allowed values are 'get' and 'post'. Optional, defaults to 'post'.
	 *     @type string $capability_needed Capability that user has to have in order to pass the check.
	 *         Optional, default is "manage_options".
	 *     @type string $type_of_death How to indicate failure:
	 *         - 'die': Call wp_json_error with array( 'type' => 'capability'|'nonce', 'message' => $error_message )
	 *         - 'return': Do not die, just return the error array as above.
	 *         Optional, default is 'die'.
	 *     )
	 *
	 * @return array|void
	 *
	 * @since 2.0
	 */
	private function ajax_authenticate( $args = array() ) {
		// Read arguments
		$type_of_death = wpcf_getarr( $args, 'type_of_death', 'die', array( 'die', 'return' ) );
		$nonce_name = wpcf_getarr( $args, 'nonce' );
		$nonce_parameter = wpcf_getarr( $args, 'nonce_parameter', 'wpnonce' );
		$capability_needed = wpcf_getarr( $args, 'capability_needed', 'manage_options' );
		$parameter_source_name = wpcf_getarr( $args, 'parameter_source', 'post', array( 'get', 'post' ) );
		$parameter_source = ( $parameter_source_name == 'get' ) ? $_GET : $_POST;

		$is_error = false;
		$error_message = null;
		$error_type = null;

		// Check permissions
		if ( ! current_user_can( $capability_needed ) ) {
			$error_message = __( 'You do not have permissions for that.', 'wpv-views' );
			$error_type = 'capability';
			$is_error = true;
		}

		// Check nonce
		if ( !$is_error && !wp_verify_nonce( wpcf_getarr( $parameter_source, $nonce_parameter, '' ), $nonce_name ) ) {
			$error_message = __( 'Your security credentials have expired. Please reload the page to get new ones.', 'wpv-views' );
			$error_type = 'nonce';
			$is_error = true;
		}

		if( $is_error ) {
			$error_description = array( 'type' => $error_type, 'message' => $error_message );
			switch( $type_of_death ) {

				case 'die':
					wp_send_json_error( $error_description );
					break;

				case 'return':
				default:
					return $error_description;
			}
		}

		return true;
	}


	/**
	 * Begin an AJAX call handling.
	 * 
	 * To be extended in the future.
	 * 
	 * @param array $args See ajax_authenticate for details
	 * @return array|void
	 * @since 2.0
	 */
	private function ajax_begin( $args ) {
		return $this->ajax_authenticate( $args );
	}


	/**
	 * Complete an AJAX call handling.
	 * 
	 * Sends a success/error response in a standard way.
	 * 
	 * To be extended in the future.
	 * 
	 * @param array $response Custom response data
	 * @param bool $is_success
	 * @since 2.0
	 */
	private function ajax_finish( $response, $is_success = true ) {
		if( $is_success ) {
			wp_send_json_success( $response );
		} else {
			wp_send_json_error( $response );
		}
	}


	/**
	 * Handle action with field definitions on the Field Control page.
	 * 
	 * todo comment
	 * @since 2.0
	 */
	public function callback_field_control_action() {
		$this->ajax_begin( array( 'nonce' => $this->get_action_js_name( self::CALLBACK_FIELD_CONTROL_ACTION ) ) );

		// Read and validate input
		$field_action = wpcf_getpost( 'field_action' );
		$fields = wpcf_getpost( 'fields' );

		$current_domain = wpcf_getpost( 'domain', null, Types_Field_Utils::get_domains() );
		if( null == $current_domain ) {
			$this->ajax_finish( array( 'message' => __( 'Wrong field domain.', 'wpcf' ) ), false );
		}

		if( !is_array( $fields ) || empty( $fields ) ) {
			$this->ajax_finish( array( 'message' => __( 'No fields have been selected.', 'wpcf' ) ), false );
		}
		
		$action_specific_data = wpcf_getpost( 'action_specific', array() );

		// Process fields one by one
		$errors = array();
		$results = array();
		foreach( $fields as $field ) {

			$result = $this->single_field_control_action( $field_action, $field, $current_domain, $action_specific_data );

			if( is_array( $result ) ) {
				// Array of errors
				$errors = array_merge( $errors, $result );
			} else if( $result instanceof WP_Error ) {
				// Single error
				$errors[] = $result;
			} else if( false == $result ) {
				// This should not happen...!
				$errors[] = new WP_Error( 0, __( 'An unexpected error happened while processing the request.', 'wpcf' ) );
			} else  {
				// Success

				// Save the field definition model as a result if we got a whole definition
				if( $result instanceof WPCF_Field_Definition ) {
					$result = $result->to_json();
				}

				$results[ wpcf_getarr( $field, 'slug' ) ] = $result;
			}
		}

		$data = array( 'results' => $results );
		$is_success = empty( $errors );

		if( !$is_success ) {
			$error_messages = array();
			/** @var WP_Error $error */
			foreach( $errors as $error ) {
				$error_messages[] = $error->get_error_message();
			}
			$data['messages'] = $error_messages;
		}
		
		$this->ajax_finish( $data, $is_success );
	}


	/**
	 * @param string $action_name One of the allowed action names: 'manage_with_types'
	 * @param array $field Field definition model passed from JS.
	 * @param string $domain Field domain name.
	 * @param mixed $action_specific_data
	 * @return bool|mixed|null|WP_Error|WP_Error[]|WPCF_Field_Definition An error, array of errors, boolean indicating
	 *     success or a result value to be passed back to JS.
	 * @since 2.0
	 */
	private function single_field_control_action( $action_name, $field, $domain, $action_specific_data ) {

		$field_slug = wpcf_getarr( $field, 'slug' );

		switch ( $action_name ) {

			case 'manage_with_types':
				return Types_Field_Utils::start_managing_field( wpcf_getarr( $field, 'metaKey' ), $domain );

			case 'stop_managing_with_types':
				return Types_Field_Utils::stop_managing_field( $field_slug, $domain );

			case 'change_group_assignment':
				return Types_Field_Utils::change_assignment_to_groups( $field_slug, $domain, $action_specific_data );

			case 'delete_field':
				return Types_Field_Utils::delete_field( $field_slug, $domain );

			case 'change_field_type':
				return Types_Field_Utils::change_field_type( $field_slug, $domain, $action_specific_data );

			case 'change_field_cardinality':
				return Types_Field_Utils::change_field_cardinality( $field_slug, $domain, $action_specific_data );

			default:
				return new WP_Error( 42, __( 'Invalid action name.', 'wpcf' ) );
		}
	}

}