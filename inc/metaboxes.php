<?php
/**
 * Includes and sets up custom metaboxes and fields.
 *
 * @category metaboxes
 * @package  functionality
 * @see      CMB2: https://github.com/WebDevStudios/CMB2
 *
 * Find sample actions in ../node_modules/cmb/example-functions.php
 * or {@link https://github.com/WebDevStudios/CMB2/blob/master/example-functions.php}
 *
 */

namespace Nowland\Functionality\Metaboxes;

/* Load CMB2 */
if ( file_exists( $plugin_base_dir . '/node_modules/cmb/init.php' ) ) {
	require_once $plugin_base_dir . '/node_modules/cmb/init.php';
}

 /*
 * Automatically include all PHP files from a plugin subfolder while avoiding adding an unnecessary global
 * just to determine a path that is already available everywhere via WP core functions:
 */
foreach ( glob( plugin_dir_path( __FILE__ ) . "metaboxes/*.php" ) as $file ) {
	include_once $file;
}
