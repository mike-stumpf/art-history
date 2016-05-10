<?php
/*
  Plugin Name: Toolset Types
  Plugin URI: http://wordpress.org/extend/plugins/types/
  Description: Toolset Types defines custom content in WordPress. Easily create custom post types, fields and taxonomy and connect everything together.
  Author: OnTheGoSystems
  Author URI: http://www.onthegosystems.com
  Version: 2.0.1
 */

// abort if called directly
if( !function_exists( 'add_action' ) )
	die( 'Types is a WordPress plugin and can not be called directly.' );

// version
if( ! defined( 'TYPES_VERSION' ) )
	define( 'TYPES_VERSION', '2.0.1' );

// backward compatibility
if ( ! defined( 'WPCF_VERSION' ) )
	define( 'WPCF_VERSION', TYPES_VERSION );

// release notes
if( ! defined( 'TYPES_RELEASE_NOTES' ) )
	define( 'TYPES_RELEASE_NOTES', 'https://wp-types.com/version/types-2-0/?utm_source=typesplugin&utm_campaign=types&utm_medium=release-notes-admin-notice&utm_term=Types 2.0 release notes' );

/*
 * Path Constants
 */
if( ! defined( 'TYPES_ABSPATH' ) )
	define( 'TYPES_ABSPATH', dirname( __FILE__ ) );

if( ! defined( 'TYPES_RELPATH' ) )
	define( 'TYPES_RELPATH', plugins_url() . '/' . basename( TYPES_ABSPATH ) );

if( ! defined( 'TYPES_DATA' ) )
	define( 'TYPES_DATA', dirname( __FILE__ ) . '/application/data' );

/*
 * Bootstrap Types
 */
require_once( dirname( __FILE__ ) . '/application/bootstrap.php' );