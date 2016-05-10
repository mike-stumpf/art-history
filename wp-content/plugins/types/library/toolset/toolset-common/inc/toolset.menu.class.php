<?php

if ( ! defined( 'WPT_MENU' ) ) {
    define( 'WPT_MENU', true );
}

/**
* Toolset_Menu
*
* Generic class for the shared menu entry for the Toolset family.
*
* @since 1.9
*/

if ( ! class_exists( 'Toolset_Menu' ) ) {

    /**
     * Class to show promotion message.
     *
     * @since 1.5
     * @access  public
     */
    class Toolset_Menu {
		
		public $toolset_pages;
		
        public function __construct() {
			
			$this->toolset_pages = array();
			
            add_action( 'init',													array( $this, 'init' ), 1 );
            add_action( 'admin_init',											array( $this, 'admin_init' ), 1 );
			add_action( 'admin_menu',											array( $this, 'admin_menu' ) );
			add_action( 'admin_enqueue_scripts', 								array( $this, 'admin_enqueue_scripts' ) );
			
			add_action( 'toolset_action_register_submenu_pages',				array( $this, 'register_shared_pages_in_menu' ), 60 );
			add_action( 'toolset_action_register_submenu_pages',				array( $this, 'register_final_pages_in_menu' ), 90 );
			
			add_filter( 'wpml_show_admin_language_switcher',					array( $this, 'disable_wpml_admin_lang_switcher' ) );
			
        }
		
		public function init() {
			$toolset_pages = array(
				'toolset-settings', 'toolset-help', 'toolset-debug-information'
			);
			$toolset_pages = apply_filters( 'toolset_filter_register_common_page_slug', $toolset_pages );
			$this->toolset_pages = $toolset_pages;
		}

        public function admin_init() {
			global $pagenow;
			if ( 
				$pagenow == 'admin.php' 
				&& isset( $_GET['page'] ) 
				&& in_array( $_GET['page'], $this->toolset_pages )
			) {
				$current_page = sanitize_text_field( $_GET['page'] );
				do_action( 'toolset_action_admin_init_in_toolset_page', $current_page );
			}
        }
		
		public function admin_menu() {
			/**
			* Ordering menu items by plugin:
			* 10: Toolset Types
			* 20: Toolset Access
			* 30: Toolset Layouts
			* 40: Toolset Views
			* 50: Toolset CRED
			* 60: Toolset Common - Settings, Export/Import and Help
			* 70: Toolset Module Manager
			* 80: Toolset Reference Sites
			* 90: Toolset ending pages - for anything else, like the debug page
			*/
			$registered_pages = apply_filters( 'toolset_filter_register_menu_pages', array() );
			if ( count( $registered_pages ) > 0 ) {
				
				$top_level_page_registered = false;
				
				while (
					count( $registered_pages ) > 0 
					&& ! $top_level_page_registered
				) {
					$top_level_page = array_shift( $registered_pages );
					$top_level_page['capability'] = isset( $top_level_page['capability'] ) ? $top_level_page['capability'] : 'manage_options';
					if ( current_user_can( $top_level_page['capability'] ) ) {
						$hook = add_menu_page( $top_level_page['page_title'], __( 'Toolset', 'wpv-views' ), $top_level_page['capability'], $top_level_page['slug'], $top_level_page['callback'] );
						$this->add_menu_page_hooks( $top_level_page, $hook );
						$top_level_page_registered = true;
					}
				}
				
				if ( 
					$top_level_page_registered 
					&& is_array( $registered_pages )
				) {
					$this->add_submenu_page( $top_level_page, $top_level_page );
					foreach ( $registered_pages as $page ) {
						$this->add_submenu_page( $page, $top_level_page );
					}
				}
				
			}
		}
		
		public function add_submenu_page( $page, $top_level_page ) {
			$page['capability'] = isset( $page['capability'] ) ? $page['capability'] : 'manage_options';
			$callback = isset( $page['callback'] ) ? $page['callback'] : null;
			$hook = add_submenu_page( $top_level_page['slug'], $page['page_title'], $page['menu_title'], $page['capability'], $page['slug'], $callback );
			$this->add_menu_page_hooks( $page, $hook );
		}
		
		public function add_menu_page_hooks( $page, $hook ) {
			global $wp_version;
			$load_action = sprintf(
				'load-%s',
				$hook
			);
			if ( 
				! empty( $page['load_hook'] ) 
				&& is_callable( $page['load_hook'] ) 
			) {
				add_action( $load_action, $page['load_hook'] );
			}
			
			if ( version_compare( $wp_version, '3.2.1', '>' ) ) {
				if ( 
					! empty( $page['contextual_help_hook'] ) 
					&& is_callable( $page['contextual_help_hook'] ) 
				) {
					add_action( $load_action, $page['contextual_help_hook'] );
				}
			} else {
				if ( ! empty( $page['contextual_help_legacy'] ) ) {
					add_contextual_help( $hook, $page['contextual_help_legacy'] );
				}
			}
		}
		
		public function admin_enqueue_scripts() {
			global $pagenow;
			if ( 
				$pagenow == 'admin.php' 
				&& isset( $_GET['page'] ) 
				&& in_array( $_GET['page'], $this->toolset_pages )
			) {
				$current_page = sanitize_text_field( $_GET['page'] );
				do_action( 'toolset_enqueue_styles', array( 'toolset-common', 'toolset-notifications-css' ) );
				do_action( 'toolset_enqueue_scripts', $current_page );
			}
		}
		
		public function disable_wpml_admin_lang_switcher( $state ) {
			global $pagenow;
			if ( 
				$pagenow == 'admin.php' 
				&& isset( $_GET['page'] ) 
				&& in_array( $_GET['page'], $this->toolset_pages )
			) {
				$state = false;
			}
			return $state;
		}
		
		public function register_shared_pages_in_menu( $args ) {
			// Help - DEPRECATED
			// add_submenu_page( $args['slug'], __( 'Help', 'wpv-views' ), __( 'Help', 'wpv-views' ), $args['capability'], 'toolset-help', array( $this, 'help_page' ) );
		}
		
		public function help_page() {
			// @todo add tracking data, create a utils::static method for this
			?>
			<div class="wrap">
				<h2><?php _e( 'Toolset Help', 'wpv-views' ) ?></h2>
				<h3 style="margin-top:3em;"><?php _e('Documentation and Support', 'wpv-views'); ?></h3>
				<ul>
					<li>
						<?php printf(
							'<a target="_blank" href="http://wp-types.com/documentation/user-guides/"><strong>%s</strong></a>'.__( ' - everything you need to know about using Toolset', 'wpv-views' ),
							__( 'User Guides', 'wpv-views')
						); ?>
					</li>
					<li>
						<?php printf(
							'<a target="_blank" href="http://discover-wp.com/"><strong>%s</strong></a>'.__( ' - learn to use Toolset by experimenting with fully-functional learning sites', 'wpv-views' ),
							__( 'Discover WP', 'wpv-views' ) 
						); ?>
					</li>
					<li>
						<?php printf(
							'<a target="_blank" href="http://wp-types.com/forums/forum/support-2/"><strong>%s</strong></a>'.__( ' - online help by support staff', 'wpv-views' ),
							__( 'Support forum', 'wpv-views' ) 
						); ?>
					</li>
				</ul>
				<h3 style="margin-top:3em;"><?php _e('Debug information', 'wpv-views'); ?></h3>
				<p>
					<?php printf(
						__( 'For retrieving debug information if asked by a support person, use the <a href="%s">debug information</a> page.', 'wpv-views' ),
						admin_url('admin.php?page=toolset-debug-information')
					); ?>
				</p>
			</div>
			<?php
		}
		
		public function register_final_pages_in_menu( $args ) {
			$page = isset( $_GET['page'] ) ? sanitize_text_field( $_GET['page'] ) : '';
			if ( 'toolset-debug-information' == $page ) {
				add_submenu_page( $args['slug'], __( 'Debug information', 'wpv-views' ), __( 'Debug information', 'wpv-views' ), $args['capability'], 'toolset-debug-information', array( $this, 'debug_page' ) );
			}
		}
		
		public function debug_page() {
			$toolset_common_bootstrap = Toolset_Common_Bootstrap::getInstance();
			$toolset_common_sections = array(
				'toolset_debug'
			);
			$toolset_common_bootstrap->load_sections( $toolset_common_sections );
		}

    }

}
