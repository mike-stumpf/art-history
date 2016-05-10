<?php

class Types_Helper_Create_Layout {

	public function for_post( $type, $name = false ) {

		if( ! $name ) {
			$type_object = get_post_type_object( $type );
			$name = sprintf( __( 'Template for %s', 'types' ), $type_object->labels->name );
		}

		$name = $this->validate_name( $name );

		if( ! $name )
			return false;

		$layout_id = $this->create_layout( $name );

		if( ! $layout_id )
			return false;

		global $wpddlayout;
		$posts = get_posts( 'post_type=' . $type . '&post_status=any&posts_per_page=-1&fields=ids' );

		// store layout assignments before assign new
		$post_has_layout = array();
		foreach( $posts as $id ) {
			$layout = get_post_meta( $id, WPDDL_LAYOUTS_META_KEY, true );
			if( !empty( $layout ) ) {
				$post_has_layout[] = array(
					'id' => $id,
					'layout-slug' => $layout
				);
			}
		}

		// assign the new layout to all post types
		$wpddlayout->post_types_manager->handle_set_option_and_bulk_at_once( $layout_id, array( $type ), array( $type ) );

		// restore previously assigned layouts
		if( !empty( $post_has_layout ) ) {
			foreach( $post_has_layout as $post ) {
				update_post_meta( $post['id'], WPDDL_LAYOUTS_META_KEY, $post['layout-slug'] );
			}
		}

		return $layout_id;
	}

	private function needed_components_loaded( ) {
		global $wpddlayout;
		if(
			! is_object( $wpddlayout )
			|| ! class_exists( 'WPDD_Layouts' )
			|| ! class_exists( 'WPDD_Layouts_Users_Profiles' )
			|| ! method_exists( 'WPDD_Layouts', 'create_layout' )
			|| ! method_exists( 'WPDD_Layouts', 'save_layout_settings' )
			|| ! method_exists( 'WPDD_Layouts_Users_Profiles', 'user_can_create' )
			|| ! method_exists( 'WPDD_Layouts_Users_Profiles', 'user_can_assign' )
			|| ! method_exists( $wpddlayout, 'get_css_framework' )
		) return false;

		return true;
	}

	private function create_layout( $name ) {
		// @todo check with Ric to get a more handy class to create a new layout.
		// currently there is only (which I found)
		// - create_layout_auto(), which has a redirect
		// - create_layout_callback() for ajax only -> uses die()
		global $wpddlayout;

		if( ! $this->needed_components_loaded() )
			return false;

		// permissions
		if( ! current_user_can( 'manage_options' ) && WPDD_Layouts_Users_Profiles::user_can_create() && WPDD_Layouts_Users_Profiles::user_can_assign() )
			return false;

		$layout = WPDD_Layouts::create_layout( 12, 'fluid' );

		// Define layout parameters
		$layout['type'] = 'fluid'; // layout_type
		$layout['cssframework'] = $wpddlayout->get_css_framework();
		$layout['template'] = '';
		$layout['parent'] = '';
		$layout['name'] = $name;

		$args = array(
			'post_title'	=> $name,
			'post_content'	=> '',
			'post_status'	=> 'publish',
			'post_type'     => WPDDL_LAYOUTS_POST_TYPE
		);
		$layout_id = wp_insert_post( $args );

		// force layout object to take right ID
		// @see WPDD_Layouts::create_layout_callback() @ wpddl.class.php
		$layout_post = get_post( $layout_id );
		$layout['id'] = $layout_id;
		$layout['slug'] = $layout_post->post_name;

		// update changes
		WPDD_Layouts::save_layout_settings( $layout_id, $layout );

		return $layout_id;
	}

	private function validate_name( $name, $id = 1 ) {
		$name_exists = get_page_by_title( html_entity_decode( $name ), OBJECT, WPDDL_LAYOUTS_POST_TYPE );

		if( $name_exists ) {
			$name = $id > 1 ? rtrim( rtrim( $name, $id - 1 ) ) : $name;
			return $this->validate_name( $name . ' ' . $id, $id + 1 );
		}

		return $name;
	}

}
