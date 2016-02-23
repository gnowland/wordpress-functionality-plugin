<?php
/**
 * @category Page Options Metabox
 * @package  functionality
 * @author   Gifford Nowland
 *
 */

namespace Nowland\Functionality\Metaboxes;

function page_options() {
	// Start with an underscore to hide fields from custom fields list
	$prefix = '_sitename_';

	$page_options = new_cmb2_box( array(
		'id'            => $prefix . 'page_options',
		'title'         => __('Page Options', 'functions'),
		'object_types'  => array('page'),
		'context'       => 'side',
		'priority'      => 'default',
		'show_names'    => false, // Show field names on the left
	) );
	$page_options->add_field( array(
			'name' => 'Hide Sidebar',
			'desc' => 'Hide Sidebar',
			'id'   => $prefix . 'hide_sidebar',
			'type' => 'checkbox',
	) );

}
add_action( 'cmb2_init', __NAMESPACE__ . '\\page_options' );
