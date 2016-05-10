<?php


abstract class Types_Helper_Condition {

	protected $condition;
	public static $post_type;

	public function set_condition( $value ) {
		$this->condition = $value;
	}

	public function valid() {}

	public static function set_post_type( $posttype = false ) {
		if( ! $posttype ) {
			global $typenow;

			$posttype = isset( $typenow ) && ! empty( $typenow ) ? $typenow : false;
		}

		if( $posttype )
			self::$post_type = get_post_type_object( $posttype );
	}

	public static function get_post_type() {
		if( self::$post_type === null )
			self::set_post_type();

		return self::$post_type;
	}
}