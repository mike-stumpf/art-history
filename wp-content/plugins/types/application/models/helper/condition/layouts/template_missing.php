<?php

class Types_Helper_Condition_Layouts_Template_Missing extends Types_Helper_Condition_Layouts_Template_Exists {

	public function valid() {
		if( parent::$layout_id !== null && parent::$layout_id !== false )
			return false;

		return ! parent::valid();
	}

}