<?php


class Types_Helper_Placeholder {

	private static $post_type;
	private static $post_type_template_file;
	private static $post_type_archive_file;
	private static $post_type_create_layout_template;
	private static $post_type_create_layout_archive;

	private static $archive_link;
	private static $single_link;

	private static $post_type_edit_layout_template;
	private static $post_type_edit_layout_archive;

	private static $post_type_edit_views_template;
	private static $post_type_edit_views_archive;
	private static $post_type_create_views_archive;

	private static $post_type_create_view;

	public static function set_post_type( $posttype = false ) {

		if( ! $posttype ) {
			global $typenow;

			$posttype = isset( $typenow ) && ! empty( $typenow ) ? $typenow : false;
		}

		if( $posttype )
			self::$post_type = get_post_type_object( $posttype );
	}

	public static function replace( &$original ) {
		if( self::$post_type == null )
			self::set_post_type();

		if( is_array( $original ) ) {
			foreach( $original as &$nested ) {
				self::replace( $nested );
			}
			return;
		}

		// skip if there are no placeholders
		if( strpos( $original, '%' ) === false )
			return;


		// placeholders
		$admin_url = admin_url();
		$placeholders = array(
			'%POST-LABEL-PLURAL%'               => self::$post_type->labels->name,
			'%POST-PERMALINK%'                  => self::get_permalink(),
			'%POST-ARCHIVE-PERMALINK%'          => self::get_archive_permalink(),
			'%POST-LABEL-SINGULAR%'             => self::$post_type->labels->singular_name,
			'%POST-TYPE-NAME%'                  => self::$post_type->name,
			'%THEME-NAME%'                      => wp_get_theme(),
			'%POST-TEMPLATE-FILE%'              => '<nobr>' . self::get_post_template_file() . '</nobr>',
			'%POST-ARCHIVE-FILE%'               => '<nobr>' . self::get_post_archive_file() . '</nobr>',
			'%POST-FORMS-LIST%'                 => self::get_post_type_forms_list(),
			'%POST-CREATE-FORM%'                => $admin_url . 'admin.php?page=types-helper&action=new-form&type=' . self::$post_type->name,
			'%POST-TYPE-EDIT-HAS-ARCHIVE%'      => $admin_url . 'admin.php?page=wpcf-edit-type&wpcf-post-type=' . self::$post_type->name . '#types_options',
		);

		// Views specifics
		if( defined( 'WPV_VERSION' ) ) {
			$placeholders = array_merge( $placeholders, array(
				//'%POST-CREATE-LAYOUT-TEMPLATE%'     => self::get_post_create_layout_template(),
				'%POST-CONTENT-TEMPLATE-NAME%'     => Types_Helper_Condition_Views_Template_Exists::get_template_name(),
				'%POST-VIEWS-ARCHIVE%'             => Types_Helper_Condition_Views_Archive_Exists::get_template_name(),
				'%POST-EDIT-VIEWS-ARCHIVE%'        => self::get_post_edit_views_archive(),
				'%POST-EDIT-CONTENT-TEMPLATE%'     => self::get_post_edit_views_template(),
				'%POST-CREATE-CONTENT-TEMPLATE%'   => $admin_url . 'admin.php?page=types-helper&action=new-content-template&type='.self::$post_type->name,
				'%POST-CREATE-VIEWS-ARCHIVE%'      => $admin_url . 'admin.php?page=types-helper&action=new-wordpress-archive&type='.self::$post_type->name,
				//'%POST-CREATE-VIEWS-ARCHIVE%'      => self::get_post_create_views_archive(),
				'%POST-VIEWS-LIST%'                => self::get_post_type_views_list(),
				'%POST-CREATE-VIEW%'               => $admin_url . 'admin.php?page=types-helper&action=new-view&type='.self::$post_type->name,
			) );

		}

		// Layouts specifics
		if( defined( 'WPDDL_DEVELOPMENT' ) || defined( 'WPDDL_PRODUCTION' ) )  {
			$placeholders = array_merge( $placeholders, array(
				//'%POST-CREATE-LAYOUT-TEMPLATE%'     => self::get_post_create_layout_template(),
				'%POST-CREATE-LAYOUT-TEMPLATE%'     => $admin_url . 'admin.php?page=types-helper&action=new-layout-template&type='.self::$post_type->name,
				'%POST-CREATE-LAYOUT-ARCHIVE%'      => self::get_post_create_layout_archive(),
				'%POST-EDIT-LAYOUT-TEMPLATE%'       => self::get_post_edit_layout_template(),
				'%POST-EDIT-LAYOUT-ARCHIVE%'        => self::get_post_edit_layout_archive(),
				'%POST-LAYOUT-TEMPLATE%'            => Types_Helper_Condition_Layouts_Template_Exists::get_layout_name(),
				'%POST-LAYOUT-ARCHIVE%'             => Types_Helper_Condition_Layouts_Archive_Exists::get_layout_name(),
			) );
		}

		$original = strtr( $original, $placeholders );
	}

	private static function get_post_create_views_archive() {
		if( self::$post_type_create_views_archive === null ){
			$tool_admin_bar = Toolset_Admin_Bar_Menu::get_instance();

			$post_type = self::$post_type->name == 'post' ? 'home-blog' : self::$post_type->name;
			self::$post_type_create_views_archive = $tool_admin_bar->get_edit_link( 'views', true, $post_type, 'archive', 0 );
		}

		return self::$post_type_create_views_archive;
	}

	private static function get_post_edit_views_archive() {
		if( self::$post_type_edit_views_archive === null ) {
			self::$post_type_edit_views_archive =
				admin_url() . 'admin.php?page=view-archives-editor&view_id='
				. Types_Helper_Condition_Views_Archive_Exists::get_template_id();
		}
		return self::$post_type_edit_views_archive;
	}

	private static function get_post_create_layout_archive() {
		if( self::$post_type_create_layout_archive === null ){
			$tool_admin_bar = Toolset_Admin_Bar_Menu::get_instance();
			self::$post_type_create_layout_archive = $tool_admin_bar->get_edit_link( 'layouts', true, self::$post_type->name, 'archive', 0 );
		}

		return self::$post_type_create_layout_archive;
	}

	private static function get_post_edit_layout_archive() {
		if( self::$post_type_edit_layout_archive === null ) {
			self::$post_type_edit_layout_archive =
				admin_url() . 'admin.php?page=dd_layouts_edit&action=edit&layout_id='
				. Types_Helper_Condition_Layouts_Archive_Exists::get_layout_id();
		}

		return self::$post_type_edit_layout_archive;
	}

	private static function get_post_edit_layout_template() {
		if( self::$post_type_edit_layout_template === null ) {
			self::$post_type_edit_layout_template =
				admin_url() . 'admin.php?page=dd_layouts_edit&action=edit&layout_id='
				. Types_Helper_Condition_Layouts_Template_Exists::get_layout_id();
		}

		return self::$post_type_edit_layout_template;
	}

	private static function get_post_edit_views_template() {
		if( self::$post_type_edit_views_template === null ) {
			self::$post_type_edit_views_template =
				admin_url() . 'admin.php?page=ct-editor&ct_id='
				. Types_Helper_Condition_Views_Template_Exists::get_template_id();
		}

		return self::$post_type_edit_views_template;
	}

	private static function get_post_template_file() {
		if( self::$post_type_template_file === null ) {
			$helper                        = new Types_Helper_Condition_Single_Exists();
			self::$post_type_template_file = basename( $helper->find_template() );
		}

		return self::$post_type_template_file;
	}

	private static function get_post_archive_file() {
		if( self::$post_type_archive_file === null ) {
			$helper                       = new Types_Helper_Condition_Archive_Exists();
			self::$post_type_archive_file = basename( $helper->find_template() );
		}

		return self::$post_type_archive_file;
	}

	public static function get_permalink( $id = 0 ) {
		if( self::$single_link !== null )
			return self::$single_link;

		$permalink = get_permalink( $id );

		if( $permalink ) {
			$query_args['preview'] = 'true';
			$permalink = add_query_arg( $query_args, $permalink );
			self::$single_link = $permalink;
			return $permalink;
		}

		if( isset( $_GET['post'] ) && $id != $_GET['post'] )
			return self::get_permalink( $_GET['post'] );

		// cpt edit page
		if( isset( $_GET['wpcf-post-type'] ) && $id == 0 ) {
			$query = new WP_Query( 'post_type=' . $_GET['wpcf-post-type'] . '&posts_per_page=1' );
			if( $query->have_posts() )
				return self::get_permalink( $query->posts[0]->ID );
		}

		// fields edit page
		if( is_object( self::$post_type ) && $id == 0 ){
			$query = new WP_Query( 'post_type=' . self::$post_type->name . '&posts_per_page=1' );
			if( $query->have_posts() )
				return self::get_permalink( $query->posts[0]->ID );
		}

		self::$single_link = false;
		return self::$single_link ;
	}

	public static function get_archive_permalink() {
		if( self::$archive_link !== null )
			return self::$archive_link;

		// cpt edit page
		if( isset( $_GET['wpcf-post-type'] ) ) {
			$query = new WP_Query( 'post_type=' . $_GET['wpcf-post-type'] . '&post_status=publish&posts_per_page=1' );
			if( $query->have_posts() ) {
				self::$archive_link = get_post_type_archive_link( $_GET['wpcf-post-type'] );
				return self::$archive_link;
			}

			self::$archive_link = false;
			return self::$archive_link;
		}

		if( ! is_object( self::$post_type ) )
			self::set_post_type();

		$query = new WP_Query( 'post_type=' . self::$post_type->name . '&post_status=publish&posts_per_page=1' );
		if( $query->have_posts() ) {
			self::$archive_link = get_post_type_archive_link( self::$post_type->name );
			return self::$archive_link;
		}

		self::$archive_link = false;
		return self::$archive_link;
	}

	private static function get_post_type_views_list() {
		// @todo use twig
		if( $views = Types_Helper_Condition_Views_Views_Exist::get_views_of_post_type() ) {
			$output = '<ul>';
			foreach( $views as $view ) {
				$view_edit_link = admin_url() . 'admin.php?page=views-editor&view_id=' . $view['id'];
				$output .= '<li><a href="'. $view_edit_link . '">'. $view['name'].'</a></li>';
			}
			$output .= '</ul>';

			return $output;
		}

		return __( 'No Views', 'types' );
	}

	private static function get_post_type_forms_list() {
		// @todo use twig
		if( $forms = Types_Helper_Condition_Cred_Forms_Exist::get_forms_of_post_type() ) {
			$output = '<ul>';
			foreach( $forms as $form ) {
				$view_edit_link = get_edit_post_link( $form['id'] );
				$output .= '<li><a href="'. $view_edit_link . '">'. $form['name'].'</a></li>';
			}
			$output .= '</ul>';

			return $output;
		}

		return __( 'No Forms', 'types' );
	}
}