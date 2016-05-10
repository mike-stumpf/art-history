<?php

/**
 * Static class for shortcut functions related to field types, groups, definitions and instances.
 * @since 1.9
 */
final class Types_Field_Utils {

	private function __construct() { }


	// Field domains supported by the page.
	const DOMAIN_POSTS = 'posts';
	const DOMAIN_USERS = 'users';
	const DOMAIN_TERMS = 'terms';

	// Since PHP 5.6, noooo!
	// const DOMAINS = array( self::DOMAIN_POSTS, self::DOMAIN_USERS, self::DOMAIN_TERMS );

	
	/**
	 * Array of valid field domains.
	 *
	 * Replacement for self::DOMAINS because damn you old PHP versions.
	 *
	 * @return array
	 * @since 2.0
	 */
	public static function get_domains() {
		return array( self::DOMAIN_POSTS, self::DOMAIN_USERS, self::DOMAIN_TERMS );
	}
	
	
	public static function get_definition_factory_by_domain( $domain ) {
		switch( $domain ) {
			case self::DOMAIN_POSTS:
				return WPCF_Field_Definition_Factory_Post::get_instance();
			case self::DOMAIN_USERS:
				return WPCF_Field_Definition_Factory_User::get_instance();
			case self::DOMAIN_TERMS:
				return WPCF_Field_Definition_Factory_Term::get_instance();
			default:
				throw new InvalidArgumentException( 'Invalid field domain.' );
		}
	}


	/**
	 * For a given field domain, return the appropriate field group factory instance.
	 *
	 * @param string $domain
	 * @return WPCF_Field_Group_Factory
	 * @since 2.0
	 */
	public static function get_group_factory_by_domain( $domain ) {
		switch( $domain ) {
			case self::DOMAIN_POSTS:
				return WPCF_Field_Group_Post_Factory::get_instance();
			case self::DOMAIN_USERS:
				return WPCF_Field_Group_User_Factory::get_instance();
			case self::DOMAIN_TERMS:
				return WPCF_Field_Group_Term_Factory::get_instance();
			default:
				throw new InvalidArgumentException( 'Invalid field domain.' );
		}
	}


	/**
	 * Translate a field domain into a "meta_type" value, which is used in field definition arrays.
	 *
	 * @param string $domain
	 * @return string
	 * @since 2.0
	 */
	public static function domain_to_legacy_meta_type( $domain ) {
		static $map = array(
			self::DOMAIN_POSTS => 'postmeta',
			self::DOMAIN_USERS => 'usermeta',
			self::DOMAIN_TERMS => 'termmeta'
		);
		return wpcf_getarr( $map, $domain );
	}

	
	/**
	 * Create a term field instance.
	 *
	 * @param string $field_slug Slug of existing field definition.
	 * @param int $term_id ID of the term where the field belongs.
	 *
	 * @return null|WPCF_Field_Instance Field instance or null if an error occurs.
	 * @since 1.9
	 */
	public static function create_term_field_instance( $field_slug, $term_id ) {
		try {
			return new WPCF_Field_Instance_Term( WPCF_Field_Definition_Factory_Term::get_instance()->load_field_definition( $field_slug ), $term_id );
		} catch( Exception $e ) {
			return null;
		}
	}


	/**
	 * Obtain toolset-forms "field configuration", which is an array of settings for specific field instance.
	 *
	 * @param WPCF_Field_Instance $field
	 * @since 1.9
	 */
	public static function get_toolset_forms_field_config( $field ) {
		return wptoolset_form_filter_types_field(
			$field->get_definition()->get_definition_array(),
			$field->get_object_id()
		);
	}


	/**
	 * Gather an unique array of field definitions from given groups.
	 *
	 * The groups are expected to belong to the same domain (term/post/user), otherwise problems may occur when
	 * field slugs conflict.
	 *
	 * @param WPCF_Field_Group[] $field_groups
	 * @return WPCF_Field_Definition[]
	 * @since 1.9
	 */
	public static function get_field_definitions_from_groups( $field_groups ) {
		$field_definitions = array();
		foreach( $field_groups as $group ) {
			$group_field_definitions = $group->get_field_definitions();

			foreach( $group_field_definitions as $field_definition ) {
				$field_definitions[ $field_definition->get_slug() ] = $field_definition;
			}
		}
		return $field_definitions;
	}


	/**
	 * Start managing a field with given meta_key with Types.
	 *
	 * Looks if there already exists a field definition that uses the meta_key. If yes, it's most probably a "disabled"
	 * one, that is stored only for the possibility of later "re-activation" (which is happening now). In that case,
	 * the field definition will be simply updated.
	 *
	 * If there is no matching field definition whatsoever, it will be created with in some default manner. 
	 * Check WPCF_Field_Definition_Factory::create_field_definition_for_existing_fields() for details.
	 *
	 * AJAX callback helper only, do not use elsewhere.
	 *
	 * @param string $meta_key
	 * @param string $domain Field domain
	 * @return false|null|WPCF_Field_Definition The updated/newly created field definition or falsy value on failure.
	 * @since 2.0
	 */
	public static function start_managing_field( $meta_key, $domain ) {
		$factory = self::get_definition_factory_by_domain( $domain );
		$definition = $factory->meta_key_belongs_to_types_field( $meta_key, 'definition' );
		if( null == $definition ) {
			$result = $factory->create_field_definition_for_existing_fields( $meta_key );
			if( false != $result ) {
				return $factory->load_field_definition( $result );
			} else {
				return false;
			}
		} else {
			$is_success = $definition->set_types_management_status( true );
			return ( $is_success ? $definition : false );
		}
	}


	/**
	 * Stop managing a field with given field slug by Types.
	 *
	 * AJAX callback helper only, do not use elsewhere.
	 * 
	 * @param string $field_slug
	 * @param string $domain Field domain.
	 * @return WP_Error|WPCF_Field_Definition Error with a user-friendly message on failure
	 *     or the updated definition on success.
	 * @since 2.0
	 */
	public static function stop_managing_field( $field_slug, $domain ) {
		
		$factory = self::get_definition_factory_by_domain( $domain );
		$definition = $factory->load_field_definition( $field_slug );
		
		if( null == $definition ) {
			
			return new WP_Error( 42, sprintf( __( 'Field definition for field "%s" not found in options.', 'wpcf' ), sanitize_text_field( $field_slug ) ) );
			
		} else {
			
			$is_success = $definition->set_types_management_status( false );
			
			if( $is_success ) {
				return $definition;
			} else {
				return new WP_Error(
					42,
					sprintf(
						__( 'Unable to set types management status for field definition "%s".', 'wpcf' ),
						sanitize_text_field( $field_slug )
					)
				);
			}
		}
	}


	/**
	 * Change which groups is a field definition associated with.
	 *
	 * AJAX callback helper only, do not use elsewhere.
	 *
	 * @param string $field_slug Field definition slug.
	 * @param string $domain Field domain
	 * @param string[][] $groups Action-specific data passed through AJAX. Array containing a single key 'group_slugs',
	 *     containing an array of field group slugs.
	 *
	 * @return WP_Error|WPCF_Field_Definition The updated field definition on success or an error object.
	 * @since 2.0
	 */
	public static function change_assignment_to_groups( $field_slug, $domain, $groups ) {
		$factory = Types_Field_Utils::get_definition_factory_by_domain( $domain );
		$definition = $factory->load_field_definition( $field_slug );
		if( null == $definition ) {
			return new WP_Error( 42, sprintf( __( 'Field definition for field "%s" not found in options.', 'wpcf' ), sanitize_text_field( $field_slug ) ) );
		}
		$new_groups = wpcf_ensarr( wpcf_getarr( $groups, 'group_slugs' ) );
		$associated_groups = $definition->get_associated_groups();
		$is_success = true;
		foreach( $associated_groups as $group ) {
			if( !in_array( $group->get_slug(), $new_groups ) ) {
				$is_success = $is_success && $group->remove_field_definition( $definition );
			}
		}
		$group_factory = $factory->get_group_factory();
		foreach( $new_groups as $new_group_slug ) {
			$new_group = $group_factory->load_field_group( $new_group_slug );
			if( null != $new_group ) {
				$is_success = $is_success && $new_group->add_field_definition( $definition );
			} else {
				$is_success = false;
			}
		}

		if( $is_success ) {
			return $definition;
		} else {
			return new WP_Error();
		}
	}


	/**
	 * Delete a field definition and all values of the field within given domain.
	 * 
	 * @param string $field_slug
	 * @param string $domain
	 * @return bool|WP_Error True for success, false or WP_Error on error.
	 * @since 2.0
	 */
	public static function delete_field( $field_slug, $domain ) {
		
		$factory = Types_Field_Utils::get_definition_factory_by_domain( $domain );
		$definition = $factory->load_field_definition( $field_slug );
		if( null == $definition ) {
			return new WP_Error( 42, sprintf( __( 'Field definition for field "%s" not found in options.', 'wpcf' ), sanitize_text_field( $field_slug ) ) );
		} else if( ! $definition->is_managed_by_types() ) {
			return new WP_Error( 42, sprintf( __( 'Field "%s" will not be deleted because it is not managed by Types.', 'wpcf' ), sanitize_text_field( $field_slug ) ) );
		}
		
		$response = $factory->delete_definition( $definition );
			
		return $response;
	}


	/**
	 * Change a field type for given field definition.
	 *
	 * Performs checks if the conversion is allowed, and if not, generate a proper error message.
	 *
	 * @param string $field_slug
	 * @param string $domain
	 * @param string[] $arguments Needs to contain the 'field_type' key with target type slug.
	 * @return false|WP_Error|WPCF_Field_Definition The updated definition on succes, error/false otherwise.
	 * @since 2.0
	 */
	public static function change_field_type( $field_slug, $domain, $arguments ) {

		// Load all information we need, fail if it doesn't exist.
		$factory = Types_Field_Utils::get_definition_factory_by_domain( $domain );
		$definition = $factory->load_field_definition( $field_slug );
		if( null == $definition ) {
			return new WP_Error( 42, sprintf( __( 'Field definition for field "%s" not found in options.', 'wpcf' ), sanitize_text_field( $field_slug ) ) );
		} else if( ! $definition->is_managed_by_types() ) {
			return new WP_Error( 42, sprintf( __( 'Field "%s" will not be converted because it is not managed by Types.', 'wpcf' ), sanitize_text_field( $field_slug ) ) );
		}

		$type_slug = wpcf_getarr( $arguments, 'field_type' );
		$target_type = Types_Field_Type_Definition_Factory::get_instance()->load_field_type_definition( $type_slug );
		if( null == $target_type ) {
			return new WP_Error( 42, sprintf( __( 'Unknown field type "%s".', 'wpcf' ), $type_slug ) );
		}

		// Check if we can convert between types
		$is_conversion_possible = Types_Field_Type_Converter::get_instance()->is_conversion_possible( $definition->get_type(), $target_type );
		if( !$is_conversion_possible ) {
			return new WP_Error(
				42,
				sprintf(
					__( 'Conversion from type "%s" to "%s" is currently not allowed.', 'wpcf' ),
					$definition->get_type()->get_display_name(),
					$target_type->get_display_name()
				)
			);
		}

		// Check if we can do the conversion with current field's cardinality
		$is_cardinality_sustainable = ( ! $definition->get_is_repetitive() || $target_type->can_be_repetitive() );
		if( !$is_cardinality_sustainable ) {
			return new WP_Error(
				42,
				sprintf(
					__( 'Field "%s" cannot be converted from "%s" to "%s" because it is repetitive and the target type doesn\'t support that.', 'wpcf' ),
					$definition->get_display_name(),
					$definition->get_type()->get_display_name(),
					$target_type->get_display_name()
				)
			);
		}

		// All is fine, proceed.
		$result = $definition->change_type( $target_type );
		if( $result ) {
			return $definition;
		} else {
			// Something unexpected went wrong.
			return false;
		}
	}


	/**
	 * Change cardinality of given field, if it is permitted by its type.
	 *
	 * @param string $field_slug Field definition slug.
	 * @param string $domain Field domain.
	 * @param string[] $arguments Needs to contain the 'target_cardinality' key with 'single'|'repetitive' value.
	 * @return bool|WP_Error|WPCF_Field_Definition The updated definition on succes, error/false otherwise.
	 * @since 2.0
	 */
	public static function change_field_cardinality( $field_slug, $domain, $arguments ) {
		$factory = Types_Field_Utils::get_definition_factory_by_domain( $domain );
		$definition = $factory->load_field_definition( $field_slug );
		if( null == $definition ) {
			return new WP_Error( 42, sprintf( __( 'Field definition for field "%s" not found in options.', 'wpcf' ), sanitize_text_field( $field_slug ) ) );
		} else if( ! $definition->is_managed_by_types() ) {
			return new WP_Error( 42, sprintf( __( 'Field "%s" will not be converted because it is not managed by Types.', 'wpcf' ), sanitize_text_field( $field_slug ) ) );
		}

		$target_cardinality = wpcf_getarr( $arguments, 'target_cardinality', null, array( 'single', 'repetitive' ) );
		if( null == $target_cardinality ) {
			return false;
		}
		$set_as_repetitive = ( 'repetitive' == $target_cardinality );

		$result = $definition->set_is_repetitive( $set_as_repetitive );

		if( $result ) {
			return $definition;
		} else {
			return false;
		}
	}
	
}