<?php

/**
 * Public Types hook API.
 *
 * This should be the only point where other plugins (incl. Toolset) interact with Types directly.
 *
 * Note: Types_Api is initialized on after_setup_theme with priority 10.
 *
 * @since 2.1
 */
final class Types_Api {

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

		/**
		 * get all field group ids by post type
		 * @todo document
		 */
		add_filter( 'types_filter_get_field_group_ids_by_post_type', array( 'Types_Api_Helper', 'get_field_group_ids_by_post_type' ), 10, 2 );

		/**
		 * types_filter_query_field_definitions
		 *
		 * @param mixed $default
		 * @param array $query Field definition query. See Types_Field_Definition_Factory::query() for allowed arguments.
		 *     Additionally, you can specify:
		 *     - 'domain': Field domain (see Types_Field_Utils; legacy domain names are also accepted):
		 *       'posts'|'users'|'terms'|'postmeta'|'usermeta'|'termmeta'|'all'. For 'all',
		 *       the method returns a multidimensional arrays with results for individual domains:
		 *       array( 'posts' => array( ... ), 'users' => array( ... ),  ... ).
		 * @return null|array Field definition arrays, sanitized as per field type, or null if an error has occurred.
		 * @since 2.1
		 */
		add_filter( 'types_filter_query_field_definitions', array( $this, 'query_field_definitions' ), 10, 2 );
	}


	/**
	 * Hook for types_filter_query_field_definitions.
	 *
	 * @param mixed $ignored
	 * @param array $query Field definition query. See Types_Field_Definition_Factory::query() for supported arguments.
	 *     Additionally, you can specify:
	 *     - 'domain': A single field domain (see Types_Field_Utils) or 'all'. Legacy domain names are also accepted.
	 *       For 'all', the method returns a multidimensional arrays with results for individual domains:
	 *       array( 'posts' => array( ... ), 'users' => array( ... ),  ... ).
	 *
	 * @return null|array Field definition arrays, sanitized as per field type, or null if an error has occurred.
	 * @since 2.1
	 */
	public function query_field_definitions(
		/** @noinspection PhpUnusedParameterInspection */ $ignored, $query )
	{
		$domain = wpcf_getarr( $query, 'domain', 'all' );

		if( 'all' == $domain ) {

			// Call itself for each available domain.
			$results_by_domain = array();
			$domains = Types_Field_Utils::get_domains();
			foreach( $domains as $field_domain ) {
				$per_domain_query = $query;
				$per_domain_query['domain'] = $field_domain;
				$results_by_domain[ $field_domain ] = $this->query_field_definitions( null, $per_domain_query );
			}

			return $results_by_domain;

		} else {

			// Sanitize input
			if( ! is_string( $domain ) || ! is_array( $query ) ) {
				return null;
			}

			// Get the factory by domain, and if it fails, try to convert from legacy meta_type value.
			try {
				$definition_factory = Types_Field_Utils::get_definition_factory_by_domain( $domain );
			} catch( InvalidArgumentException $e ) {
				$definition_factory = null;
			}

			if ( null == $definition_factory ) {
				try {
					$definition_factory = Types_Field_Utils::get_definition_factory_by_domain( Types_Field_Utils::legacy_meta_type_to_domain( $domain ) );
				} catch( InvalidArgumentException $e ) {
					return null;
				}
			}

			// Allways query only Types fields.
			$query['filter'] = 'types';

			/** @var WPCF_Field_Definition[] $definitions */
			$definitions = $definition_factory->query_definitions( $query );
			$definition_arrays = array();
			foreach( $definitions as $definition ) {
				$definition_arrays[] = $definition->get_definition_array();
			}

			return $definition_arrays;
		}
	}
}
