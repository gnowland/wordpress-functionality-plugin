<?php
/**
 * Adds predefined "Formats" options to the WYSIWYG Editor (Tiny MCE)
 *
 * @category custom-wysiwyg-formats
 * @package  functionality
 * @see      https://codex.wordpress.org/TinyMCE_Custom_Styles
 *
 */

namespace Nowland\Functionality\TinyMCE;

// Callback function to insert 'styleselect' into the $buttons array
function tiny_mce_buttons_2( $buttons ) {
	array_unshift( $buttons, 'styleselect' );
	return $buttons;
}
// Register our callback to the appropriate filter
add_filter('mce_buttons_2', __NAMESPACE__ . '\\tiny_mce_buttons_2');

// Callback function to filter the MCE settings
function tiny_mce_before_init( $settings ) {
	// Define the style_formats array
	$style_formats = array(
		// Each array child is a format with it's own settings
		array(
			'title' => '',
			'block' => '',
			'classes' => '',
			'wrapper' => true
		),
	);

	// Insert the array, JSON ENCODED, into 'style_formats'
	$settings['style_formats'] = json_encode( $style_formats );

	return $settings;

}
// Attach callback to 'tiny_mce_before_init'
add_filter( 'tiny_mce_before_init', __NAMESPACE__ . '\\tiny_mce_before_init' );
