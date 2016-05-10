<?php

class Types_Helper_Condition_Layouts_Archive_Exists extends Types_Helper_Condition_Template {

	private static $layout_id;
	private static $layout_name;

	public function valid() {
		if( ! defined( 'WPDDL_GENERAL_OPTIONS' ) )
			return false;

		$cpt = Types_Helper_Condition::get_post_type();

		$layouts = get_option( WPDDL_GENERAL_OPTIONS, array() );

		if( ! array_key_exists( 'layouts_cpt_' . $cpt->name, $layouts ) )
			return false;

		self::$layout_id = $layouts['layouts_cpt_' . $cpt->name];

		return true;
	}

	public static function get_layout_id() {
		if( self::$layout_id === null ) {
			$self = new Types_Helper_Condition_Layouts_Archive_Exists();
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