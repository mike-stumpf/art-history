<?php

class Types_Helper_Create_View {

	public function for_post( $type, $name = false ) {
		if( ! class_exists( 'WPV_View' )
		    || ! method_exists( 'WPV_View', 'create' ) )
			return false;

		if( ! $name ) {
			$type_object = get_post_type_object( $type );
			$name = sprintf( __( 'View for %s', 'types' ), $type_object->labels->name );
		}

		$name = $this->validate_name( $name );

		$args = array(
			'view_settings' => array(
				'view-query-mode' => 'normal',
				'view_purpose'    => 'all',
				'post_type'       => array( $type )
			)
		);

		$view = WPV_View::create( $name, $args );
		return $view->id;
	}

	private function validate_name( $name, $id = 1 ) {
		$name_exists = get_page_by_title( html_entity_decode( $name ), OBJECT, 'view' );
		if( $name_exists !== null ) {
			$name = $id > 1 ? rtrim( rtrim( $name, $id - 1 ) ) : $name;
			return $this->validate_name( $name . ' ' . $id, $id + 1 );
		}

		return $name;
	}

}
