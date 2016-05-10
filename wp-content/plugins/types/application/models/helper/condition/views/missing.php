<?php


class Types_Helper_Condition_Views_Missing extends Types_Helper_Condition_Views_Active {

	public function valid() {
		return ! parent::valid();
	}

}