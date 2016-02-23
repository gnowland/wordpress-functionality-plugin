<?php
/*
Plugin Name:  WordPress Functionality Plugin
Plugin URI:
Description:  Facilitates the addition of custom functionality to a WordPress website, including Custom Post Types, Meta Fields, Widgets, Taxonomies, Shortcodes, etc.
Version:      1.0.0
Author:       Gifford Nowland
Author URI:   http://giffordnowland.com/
*/

namespace Nowland\Functionality;

$plugin_base_dir = plugin_dir_path( __FILE__ );

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) die('Abort!');

/*
 * Automatically include all PHP files from a plugin subfolder while avoiding adding an unnecessary global
 * just to determine a path that is already available everywhere via WP core functions:
 */
foreach ( glob( $plugin_base_dir . "inc/*.php" ) as $file ) {
  include_once $file;
}
