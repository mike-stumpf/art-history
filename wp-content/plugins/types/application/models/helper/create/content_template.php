<?php

class Types_Helper_Create_Content_Template {

	public function for_post( $type, $name = false ) {

		if( ! $this->needed_components_loaded() )
			return false;

		global $WPV_settings;
		$option = sanitize_text_field( sprintf( 'views_template_for_%s', $type ) );

		// already has an content template
		if( isset( $WPV_settings[$option] ) && is_numeric( $WPV_settings[$option] ) && $WPV_settings[$option] > 0 )
			return $WPV_settings[$option];

		if( ! $name ) {
			$type_object = get_post_type_object( $type );
			$name = sprintf( __( 'Template for %s', 'types' ), $type_object->labels->name );
		}

		$name = $this->validate_name( $name );

		if( ! $name )
			return false;

		$ct = WPV_Content_Template::create( $name );
		$ct_post = get_post( $ct->id );

		if( $ct_post === null )
			return false;

		$WPV_settings[$option] = $ct_post->ID;
		$WPV_settings->save();

		$posts = get_posts( 'post_type=' . $type . '&post_status=any&posts_per_page=-1&fields=ids' );

		foreach( $posts as $id ) {
			$ct = get_post_meta( $id, '_views_template', true );

			if( empty( $ct ) )
				update_post_meta( $id, '_views_template', $ct_post->ID );

		}

		return $ct_post->ID;
	}

	private function needed_components_loaded( ) {
		global $WPV_settings;
		if(
			! is_object( $WPV_settings )
			|| ! class_exists( 'WPV_Content_Template' )
			|| ! method_exists( 'WPV_Content_Template', 'create' )
		) return false;

		return true;
	}

	private function validate_name( $name, $id = 1 ) {
		$name_exists = get_page_by_title( html_entity_decode( $name ), OBJECT, 'view-template' );

		if( $name_exists ) {
			$name = $id > 1 ? rtrim( rtrim( $name, $id - 1 ) ) : $name;
			return $this->validate_name( $name . ' ' . $id, $id + 1 );
		}

		return $name;
	}

}
