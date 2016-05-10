<?php

/**
 * Post field group.
 *
 * @since 2.0
 */
final class WPCF_Field_Group_Post extends WPCF_Field_Group {


	const POST_TYPE = 'wp-types-group';

	
	/**
	 * @param WP_Post $field_group_post Post object representing a post field group.
	 * @throws InvalidArgumentException
	 */
	public function __construct( $field_group_post ) {
		parent::__construct( $field_group_post );
		if( self::POST_TYPE != $field_group_post->post_type ) {
			throw new InvalidArgumentException( 'incorrect post type' );
		}
	}


	/**
	 * @return WPCF_Field_Definition_Factory Field definition factory of the correct type.
	 */
	protected function get_field_definition_factory() {
		return WPCF_Field_Definition_Factory_Post::get_instance();
	}
	

}