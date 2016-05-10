<?php

class Types_Helper_Condition_Layouts_Template_Exists extends Types_Helper_Condition_Template {

	public static $layout_id;
	public static $layout_name;

	public function valid() {
		if( self::$layout_id !== null && self::$layout_id !== false )
			return true;

		$post = wpcf_admin_get_edited_post();
		if( ! empty( $post ) )
			return $this->valid_per_post( $post );

		return $this->valid_per_post_type();

	}

	// needed for single post edit screen
	private function valid_per_post( $post ) {
		if( ! class_exists( 'WPDD_Utils' ) || ! method_exists( 'WPDD_Utils', 'page_has_layout' )  || ! is_object( $post ) )
			return false;

		if( self::$layout_id = WPDD_Utils::page_has_layout( $post->ID ) ) {
			self::$layout_id = WPDD_Utils::get_layout_id_from_post_name( self::$layout_id );
			return true;
		}

		return false;
	}

	// needed for cpt / fields group edit screen
	private function valid_per_post_type() {
		$cpt = Types_Helper_Condition::get_post_type();

		global $wpdb;

		$layouts_per_post_type = $wpdb->get_results( "SELECT meta_value, post_id FROM $wpdb->postmeta WHERE meta_key = '_ddl_post_types_was_batched'" );

		foreach( $layouts_per_post_type as $setting ) {

			$setting->meta_value = unserialize( $setting->meta_value );

			if( is_array( $setting->meta_value )
			    && in_array( $cpt->name, $setting->meta_value ) ) {

				if( get_post_status( $setting->post_id) == 'trash' )
					continue;

				$title = get_the_title( $setting->post_id );
				self::$layout_id = $setting->post_id;
				self::$layout_name = $title;
				return true;
			}
		}

		return false;
	}

	public static function get_layout_id() {
		if( self::$layout_id === null ) {
			$self = new Types_Helper_Condition_Layouts_Template_Exists();
			$self->valid();
		}

		return self::$layout_id;
	}

	public static function get_layout_name() {
		if( self::$layout_name === null )
			self::$layout_name = get_the_title( self::get_layout_id() );

		return self::$layout_name;
	}
}