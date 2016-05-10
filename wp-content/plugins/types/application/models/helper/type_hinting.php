<?php


class Types_Helper_Type_Hinting {

	/**
	 * As we don't have type hinting in good old 5.2.4 here a little helper
	 * If a string is given for $object, the method check if class exists and than do the type check.
	 *
	 * @param $object string|object
	 * @param $type
	 *
	 * @return false|object
	 */
	public static function valid( $object, $type ) {
		if( ! is_object( $object ) ) {
			if( ! class_exists( $object ) )
				return false;

			$object = new $object();
			return self::valid( $object, $type );
		}

		// type hinting the old way
		if( ! is_a( $object, $type ) )
			return false;

		return $object;
	}
}