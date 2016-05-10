<?php

/**
 * Numeric field type.
 * 
 * @since 2.0
 */
final class Types_Field_Type_Definition_Numeric extends Types_Field_Type_Definition {
	
	
	public function __construct( $args ) {
		parent::__construct( Types_Field_Type_Definition_Factory::NUMERIC, $args );
	}


	/**
	 * Add the 'number' validation if it was not already there, and activate it.
	 * 
	 * @param array $definition_array
	 * @return array
	 * @since 2.0
	 */
	protected function sanitize_numeric_validation( $definition_array ) {

		// Get the original setting or a default one.
		$validation_setting = wpcf_ensarr( 
			wpcf_getnest( $definition_array, array( 'data', 'validate', 'number' ) ), 
			array( 'active' => true, 'message' => __( 'Please enter numeric data', 'wpcf' ) ) 
		);
		
		// Force the activation of this validation.
		$validation_setting['active'] = true;
		
		// Store the setting.
		$definition_array['data']['validate']['number'] = $validation_setting;
		
		return $definition_array;
	}
}