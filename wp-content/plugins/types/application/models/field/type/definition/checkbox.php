<?php

/**
 * Checkbox field type.
 *
 * @since 2.0
 */
final class Types_Field_Type_Definition_Checkbox extends Types_Field_Type_Definition_Singular {


	public function __construct( $args ) {
		parent::__construct( Types_Field_Type_Definition_Factory::CHECKBOX, $args );
	}


	/**
	 * @inheritdoc
	 *
	 * Checkbox field definition needs to contain these keys:
	 *
	 * - data/set_value: The value that will be saved to database when the field is checked. Default is '1'
	 *
	 * @param array $definition_array
	 * @return array
	 * @since 2.0
	 */
	public function sanitize_field_definition_array( $definition_array ) {
		$definition_array = parent::sanitize_field_definition_array( $definition_array );
		
		$set_value = wpcf_getnest( $definition_array, array( 'data', 'set_value' ), '1' );
		if( !is_string( $set_value ) && !is_numeric( $set_value ) ) {
			$set_value = '1';
		}
		$definition_array['data']['set_value'] = $set_value;
		
		return $definition_array;
	}

}