<?php


class Types_Helper_Condition_Archive_Support extends Types_Helper_Condition {

	public function __construct() {}

	public function valid() {
		$cpt = Types_Helper_Condition::get_post_type();

		if ( ! $cpt->has_archive && $cpt->name != 'post' && $cpt->name != 'page' )
			return false;

		return true;
	}

}