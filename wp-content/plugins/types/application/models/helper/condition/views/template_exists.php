<?php


class Types_Helper_Condition_Views_Template_Exists extends Types_Helper_Condition_Views_Views_Exist {

	private static $template_id;
	private static $template_name;

	public function valid() {
		if( self::$template_id !== null && self::$template_id !== false )
			return true;

		// if views not active
		if( ! defined( 'WPV_VERSION' ) )
			return false;

		if( isset( $_GET['post'] ) )
			return $this->valid_per_post();

		return $this->valid_per_post_type();
	}

	// needed for single post edit screen
	private function valid_per_post( ) {
		if( function_exists( 'has_wpv_content_template' ) ) {
			$template = has_wpv_content_template( $_GET['post'] );

			if( ! $template
			    || $template === 0
			    || ! get_post_type( $template )
			)
				return false;

			self::$template_id = $template;
		}

		return true;
	}

	// needed for cpt / fields group edit screen
	private function valid_per_post_type() {
		$cpt = Types_Helper_Condition::get_post_type();

		$wpv_options = get_option( 'wpv_options', array() );

		if( empty( $wpv_options )
		    || ! isset( $wpv_options['views_template_for_'.$cpt->name] )
		    || ! get_post_type( $wpv_options['views_template_for_'.$cpt->name] )
		)
			return false;

		$title = get_the_title( $wpv_options['views_template_for_'.$cpt->name] );
		self::$template_id = $wpv_options['views_template_for_'.$cpt->name];
		self::$template_name = $title;
		return true;
	}

	public static function get_template_id() {
		if( self::$template_id === null ) {
			$self = new Types_Helper_Condition_Views_Template_Exists();
			$self->valid();
		}

		return self::$template_id;
	}

	public static function get_template_name() {
		if( self::$template_name === null )
			self::$template_name = get_the_title( self::get_template_id() );

		return self::$template_name;
	}

}