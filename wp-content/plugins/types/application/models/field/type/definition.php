<?php

/**
 * Field type definition.
 *
 * This represents a single field type like "email", "audio", "checkbox" and so on. This class must be instantiated
 * exclusively through Types_Field_Type_Definition_Factory.
 */
class Types_Field_Type_Definition {


	/**
	 * @var string Slug of the registered field type.
	 */
	private $field_type_slug;


	/**
	 * @var string Name of the field type that can be displayed to the user.
	 */
	private $display_name;


	/**
	 * @var string Field description entered by the user.
	 */
	private $description;


	/**
	 * @var array Arguments defining the field type. Can contain some legacy values.
	 */
	private $args;


	/**
	 * Types_Field_Type_Definition constructor.
	 *
	 * @param string $field_type_slug Field type slug.
	 * @param array $args Additional array of arguments which should contain at least 'display_name' (or 'title')
	 * and 'description' elements, but omitting them is not critical.
	 */
	public function __construct( $field_type_slug, $args ) {

		if( sanitize_title( $field_type_slug ) != $field_type_slug ) {
			throw new InvalidArgumentException( 'Invalid field type slug.' );
		}

		if( ! is_array( $args ) ) {
			throw new InvalidArgumentException( 'Wrong arguments provided.' );
		}

		$this->field_type_slug = $field_type_slug;

		// Try to fall back to legacy "title", and if even that fails, use id instead.
		$this->display_name = sanitize_text_field( wpcf_getarr( $args, 'display_name', wpcf_getarr( $args, 'title', $field_type_slug ) ) );

		$this->description = wpcf_getarr( $args, 'description', '' );
		$this->args = $args;
	}


	public function get_slug() { return $this->field_type_slug; }

	public function get_display_name() { return $this->display_name; }

	public function get_description() { return $this->description; }


	/**
	 * Determine if the fields of this type can be repetitive.
	 *
	 * @return bool
	 * @since 2.0
	 */
	public function can_be_repetitive() { return true; }


	/**
	 * Direct access to the field type configuration.
	 *
	 * It is strongly encouraged to write custom (and safe) getters for anything you need to get from it.
	 *
	 * @param null|string $argument_name Specific argument name or null to return all arguments.
	 * @param string $default Default value when a specific argument is not set.
	 * @return array|mixed
	 * @since 2.0
	 */
	public function get_args( $argument_name = null, $default = '' ) {
		if( null == $argument_name ) {
			return $this->args;
		} else {
			return wpcf_getarr( $this->args, $argument_name, $default );
		}
	}


	/**
	 * Retrieve CSS classes for a field type icon.
	 * 
	 * To be placed in the i tag.
	 * 
	 * @return string One or more CSS classes separated by spaces.
	 * @since 2.0
	 */
	public function get_icon_classes() {
		$fa_class = $this->get_args( 'font-awesome', null );
		if( null != $fa_class ) {
			return sprintf( 'fa fa-%s', esc_attr( $fa_class ) );
		}
		
		$types_class = $this->get_args( 'types-field-image', null );
		if( null != $types_class ) {
			return sprintf( 'types-field-icon types-field-icon-%s', esc_attr( $types_class ) );
		}
		
		return '';		
	}


	/**
	 * Make sure that the field definition array contains all necessary information.
	 * 
	 * Note: This is a WIP, currently it sanitizes only very specific cases. It should be extended in the future.
	 *
	 * Expected definition array structure common to all fields:
	 *
	 * - slug: string
	 * - data: array
	 *     - validate: array 
	 * 
	 * @param array $definition_array Field definition array
	 * @return array Field definition array that is safe to be used even with legacy code.
	 * @since 2.0
	 */
	public function sanitize_field_definition_array( $definition_array ) {
		
		$definition_array['data'] = wpcf_ensarr( wpcf_getarr( $definition_array, 'data' ) );

		$definition_array['data']['validate'] = wpcf_ensarr( wpcf_getarr( $definition_array['data'], 'validate' ) );
		
		$definition_array = $this->sanitize_numeric_validation( $definition_array );
		
		return $definition_array;
	}


	/**
	 * For all fields, remove the "number" validation option.
	 * 
	 * Numeric field will override this and do the opposite instead.
	 * 
	 * @param array $definition_array
	 * @return array
	 * @since 2.0
	 */
	protected function sanitize_numeric_validation( $definition_array ) {

		// This is what wpcf_admin_custom_fields_change_type() was doing.
		if( isset( $definition_array['data']['validate']['number'] ) ) {
			unset( $definition_array['data']['validate']['number'] );
		}

		return $definition_array;
	}

}