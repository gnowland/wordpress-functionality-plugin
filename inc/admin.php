<?php
/**
 * Admin Functions
 *
 * @category admin
 * @package  functionality
 * @author   Gifford Nowland
 *
 */

namespace Nowland\Functionality\Admin;


/**
 *  Set allowed mime types in media uploader
 */
function allow_upload_mime_types($mimes) {
	$mimes['svg'] = 'image/svg+xml';
	return $mimes;
}
add_filter('upload_mimes', __NAMESPACE__ . '\\allow_upload_mime_types');


/**
 *  Use the ite logo to replace the login logo, dynamically
 */
function login_image() {
		if( function_exists('get_custom_header') ){
				$width = get_custom_header()->width;
				$height = get_custom_header()->height;
		} else {
				$width = HEADER_IMAGE_WIDTH;
				$height = HEADER_IMAGE_HEIGHT;
		}
		echo '<style>
			.login h1 a {
				background-image: url( '; header_image(); echo ' ) !important;
				width: 100%;
				background-size: 100%;
			}
			.interim-login h1 a {
				margin-bottom: 0;
			}
		</style>';
}
add_action( 'login_head', __NAMESPACE__ . '\\login_image' );

// changing the alt text on the logo to show your site name
function login_title() { return get_option('blogname'); }
add_filter('login_headertitle', __NAMESPACE__ . '\\login_title');

// changing the logo link from wordpress.org to your site
function login_url() {  return home_url(); }
add_filter('login_headerurl', __NAMESPACE__ . '\\login_url');


/**
 *  Reorganize Location of "Pages" in the Admin Menu
 */
function custom_admin_menu_order() {
	return array(
		'index.php',
		'separator1', //Separator
		'edit.php',
		'edit.php?post_type=page'
	);
}
add_filter( 'custom_menu_order', '__return_true' );
add_filter( 'menu_order', __NAMESPACE__ . '\\custom_admin_menu_order' );


/**
 *  Disable Update Notifications for all users except user 0 (sysadmin)
 */
function disable_update_notifications() {
	global $user_ID;
	get_currentuserinfo();
	if ($user_ID !== 1) { // Change to ID of sysadmin
		add_action( 'init', create_function( '$a', "remove_action( 'init', 'wp_version_check' );" ), 2 );
		add_filter( 'pre_option_update_core', create_function( '$a', "return null;" ) );
	}
}
add_action('admin_init', __NAMESPACE__ . '\\disable_update_notifications');


/**
 *  Add custom CSS
 */
function custom_admin_css($hook) {
		if ( 'post.php' != $hook ) { return; }
		wp_enqueue_style( 'admin_css', plugin_dir_url( __FILE__ ) . 'css/admin.css', false, '1.0.0' );
}
add_action( 'admin_enqueue_scripts', __NAMESPACE__ . '\\custom_admin_css', 20 );

/**
 *  Remove Admin Menu Items
 */
function remove_menus(){
	remove_menu_page( 'edit.php' );                   //Posts
}
add_action( 'admin_menu', __NAMESPACE__ . '\\remove_menus' );
