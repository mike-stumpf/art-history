<?php


class Types_Helper_Condition_Views_Archive_Exists extends Types_Helper_Condition_Views_Views_Exist {

	private static $template_id;
	private static $template_name;

	public function valid() {
		if( self::$template_id !== null && self::$template_id !== false )
			return true;

		// if views not active
		if( ! defined( 'WPV_VERSION' )
		    || !function_exists( 'wpv_has_wordpress_archive') )
			return false;

		$cpt = Types_Helper_Condition::get_post_type();

		$archive = $cpt->name == 'post'
			? wpv_has_wordpress_archive()
			: wpv_has_wordpress_archive( 'post', $cpt->name );

		if( ! $archive && $archive === 0 )
			return false;

		self::$template_id = $archive;

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