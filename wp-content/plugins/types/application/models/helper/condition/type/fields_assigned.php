<?php

class Types_Helper_Condition_Type_Fields_Assigned extends Types_Helper_Condition {

	public function valid() {
		$post_type = Types_Helper_Condition::get_post_type();

		$query = new WP_Query( 'post_type=' . $post_type->name . '&posts_per_page=1' );
		if( $query->have_posts() ) {

			if( !function_exists( 'wpcf_admin_post_get_post_groups_fields') )
				include_once( WPCF_EMBEDDED_ABSPATH . '/includes/fields-post.php' );

			$fields = wpcf_admin_post_get_post_groups_fields( $query->posts[0] );
		}

		if(
			isset( $fields )
			&& is_array( $fields )
		    && !empty( $fields )
		) return true;

		return false;
	}
}