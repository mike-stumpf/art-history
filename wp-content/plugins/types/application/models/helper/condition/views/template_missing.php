<?php


class Types_Helper_Condition_Views_Template_Missing extends Types_Helper_Condition_Views_Template_Exists {

	public function valid() {
		// if views not active
		if( ! defined( 'WPV_VERSION' ) )
			return false;

		// opposite of parent "Views Template exists"
		return ! parent::valid();
	}

}