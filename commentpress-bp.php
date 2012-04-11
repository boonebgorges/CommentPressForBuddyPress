<?php
/*
Plugin Name: Commentpress for BuddyPress
Version: 1.0
Plugin URI: http://www.futureofthebook.org/commentpress/
Description: This plugin integrates CommentPress with BuddyPress and BP Groupblog in a WordPress Multisite environment.
Author: Institute for the Future of the Book
Author URI: http://www.futureofthebook.org
Network: true
*/





// define version
define( 'CPBP_PLUGIN_VERSION', '1.0' );

// store reference to this file
if ( !defined( 'CPBP_PLUGIN_FILE' ) ) {
	define( 'CPBP_PLUGIN_FILE', __FILE__ );
}

// store URL to this plugin's directory
if ( !defined( 'CPBP_PLUGIN_URL' ) ) {
	define( 'CPBP_PLUGIN_URL', plugin_dir_url( CPBP_PLUGIN_FILE ) );
}
// store PATH to this plugin's directory
if ( !defined( 'CPBP_PLUGIN_PATH' ) ) {
	define( 'CPBP_PLUGIN_PATH', plugin_dir_path( CPBP_PLUGIN_FILE ) );
}





/*
----------------------------------------------------------------
Init plugin
----------------------------------------------------------------
*/

// do we have our class?
if ( !class_exists( 'CommentPressBuddyPress' ) ) {



	// Sanity check

	// define filename
	$cpbp_class_file = 'class_commentpress_bp.php';

	// define path to our class file
	$cpbp_class_file_path = CPBP_PLUGIN_PATH . $cpbp_class_file;

	// is our class definition present?
	if ( !is_file( $cpbp_class_file_path ) ) {
	
		// oh no!
		die( 'Class file "'.$cpbp_class_file.'" is missing from the plugin directory.' );
	
	}
	
	
	
	// Include and init

	// we're fine, include class definition
	require_once( $cpbp_class_file_path );
	
	// instantiate it
	$cpbp_obj = new CommentPressBuddyPress;
	

	
}






/*
--------------------------------------------------------------------------------
Misc Debugging Functions
--------------------------------------------------------------------------------
*/

/** 
 * @description: utility to show theme environment
 * @todo: 
 *
 */
function _cpbp_environment() {
	
	// don't show in admin
	if ( !is_admin() ) {
		
		// dump our environment
		echo 'TEMPLATEPATH<br />'.TEMPLATEPATH.'<br /><br />';
		echo 'STYLESHEETPATH<br />'.STYLESHEETPATH.'<br /><br />';
		echo 'stylesheet_directory<br />'.get_bloginfo('stylesheet_directory').'<br /><br />';
		echo 'template_directory<br />'.get_bloginfo('template_directory').'<br /><br />';	
		echo 'template_url<br />'.get_bloginfo('template_url').'<br /><br />';	
		echo 'stylesheet_url<br />'.get_bloginfo('stylesheet_url').'<br /><br />';
		echo 'get_stylesheet_directory<br />'.get_stylesheet_directory().'<br /><br />';
		echo 'get_stylesheet_directory_uri<br />'.get_stylesheet_directory_uri().'<br /><br />';
		echo 'get_template_directory<br />'.get_template_directory().'<br /><br />';
		echo 'get_template_directory_uri<br />'.get_template_directory_uri().'<br /><br />';
		echo 'locate_template<br />'.locate_template( array( 'style/js/cp_js_common.js' ), false ).'<br /><br />';
		die();
	
	}
	
}

//add_action( 'template_redirect', '_cpbp_environment' );






/** 
 * @description: utility to show tests
 * @todo: 
 *
 */
function _cpbp_test() {

	global $commentpress_obj;
	//print_r( $commentpress_obj ); die();
	
}

//add_action( 'wp_head', '_cpbp_test' );






?>