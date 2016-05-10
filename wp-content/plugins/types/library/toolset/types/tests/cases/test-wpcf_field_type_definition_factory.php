<?php

/**
 * Types_Field_Type_Definition_Factory tests.
 *
 * @since 1.9.1
 */
final class Test_Types_Field_Type_Definition_Factory extends WPCF_UnitTestCase {


	private function get_instance() {
		return Types_Field_Type_Definition_Factory::get_instance();
	}


	private function get_all_field_types() {
		$field_types = array(
			'audio' => '/fields/audio.php',
			'checkboxes' => '/fields/checkboxes.php',
			'checkbox' => '/fields/checkbox.php',
			'colorpicker' => '/fields/colorpicker.php',
			'date' => '/fields/date.php',
			'email' => '/fields/email.php',
			'embed' => '/fields/embed.php',
			'file' => '/fields/file.php',
			'image' => '/fields/image.php',
			// 'map' => '/fields/map.php', // works only with Toolset Maps
			'numeric' => '/fields/numeric.php',
			'phone' => '/fields/phone.php',
			'radio' => '/fields/radio.php',
			'select' => '/fields/select.php',
			'skype' => '/fields/skype.php',
			'textarea' => '/fields/textarea.php',
			'textfield' => '/fields/textfield.php',
			//'twitter' => '/fields/twitter.php',
			'url' => '/fields/url.php',
			'video' => '/fields/video.php',
			'wysiwyg' => '/fields/wysiwyg.php',
		);

		$result = array();
		foreach( $field_types as $field_type_slug => $relative_path ) {
			$result[ $field_type_slug ] = WPCF_EMBEDDED_INC_ABSPATH . $relative_path;
		}

		return $result;
	}


	private function get_all_field_types_with_prefixes() {
		return array(); // todo
	}


	function test_load_field_type_definition_loadable_slugs() {
		$loadable_slugs = array_keys( $this->get_all_field_types() );

		$this->assertTrue( $this->get_instance() instanceof Types_Field_Type_Definition_Factory );

		foreach( $loadable_slugs as $field_slug ) {
			$this->check_successful_loading( $field_slug );
		}
	}


	private function check_successful_loading( $field_slug ) {
		$field_type_definition = $this->get_instance()->load_field_type_definition( $field_slug );

		$this->assertNotNull( $field_type_definition, "Field type definition '$field_slug' was not loaded properly."  );
		$this->assertInstanceOf( 'Types_Field_Type_Definition', $field_type_definition );
		$this->assertEquals( $field_slug, $field_type_definition->get_slug() );

		$field_type_definition_statically_loaded = Types_Field_Type_Definition_Factory::load( $field_slug );
		$this->assertNotNull( $field_type_definition_statically_loaded );
		$this->assertSame( $field_type_definition_statically_loaded, $field_type_definition );

		$field_type_definition_with_prefix = $this->get_instance()->load_field_type_definition( 'wpcf-' . $field_slug );
		$this->assertNotNull( $field_type_definition_with_prefix );
		$this->assertSame( $field_type_definition, $field_type_definition_with_prefix );

	}
}