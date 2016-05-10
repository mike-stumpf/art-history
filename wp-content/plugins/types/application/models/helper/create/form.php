<?php

class Types_Helper_Create_Form {

	public function for_post( $type, $name = false ) {
		if( ! defined( 'CRED_CLASSES_PATH' ) )
			return false;

		if( ! file_exists( CRED_CLASSES_PATH . '/CredFormCreator.php' ) )
			return false;

		require_once( CRED_CLASSES_PATH . '/CredFormCreator.php' );

		if( ! class_exists( 'CredFormCreator' )
		    || ! method_exists( 'CredFormCreator', 'cred_create_form' ) )
			return false;

		if( ! $name ) {
			$type_object = get_post_type_object( $type );
			$name = sprintf( __( 'Form for %s', 'types' ), $type_object->labels->name );
		}

		$name = $this->validate_name( $name );

		$id = CredFormCreator::cred_create_form( $name, 'new', $type );
		return $id;
	}

	private function validate_name( $name, $id = 1 ) {
		$name_exists = get_page_by_title( html_entity_decode( $name ), OBJECT, CRED_FORMS_CUSTOM_POST_NAME );

		if( $name_exists !== null ) {
			$name = $id > 1 ? rtrim( rtrim( $name, $id - 1 ) ) : $name;
			return $this->validate_name( $name . ' ' . $id, $id + 1 );
		}

		return $name;
	}

}
