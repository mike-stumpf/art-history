<?php


class Types_Helper_Condition_Views_Active extends Types_Helper_Condition {

	public function valid() {
		if( defined( 'WPV_VERSION' ) )
			return true;

		return false;
	}

}