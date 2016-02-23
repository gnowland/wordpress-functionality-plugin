<?php
/**
 * @category Image Gallery Metabox
 * @package  functionality
 * @author   Gifford Nowland
 *
 */

namespace Nowland\Functionality\Metaboxes;

function image_gallery_metabox() {
	// Start with an underscore to hide fields from custom fields list
	$prefix = '_sitename_';

	$image_gallery_metabox = new_cmb2_box( array(
		'id'            => $prefix . 'sidebar_image_gallery_metabox',
		'title'         => __('Sidebar Image Gallery', 'functions'),
		'object_types'  => array('page'),
		'context'       => 'side',
		'priority'      => 'default',
		'show_names'    => false, // Show field names on the left
	) );
	$image_gallery_metabox->add_field( array(
			'name' => 'Image Gallery',
			//'desc' => '',
			'id'   => $prefix . 'sidebar_image_gallery_array',
			'type' => 'file_list',
			'preview_size' => array(105, 105),
			'options' => array(
					'add_upload_files_text' => 'Add or Upload Images',
					'remove_image_text' => 'Remove Image',
					'file_text' => 'Image:',
					'file_download_text' => 'Download',
					'remove_text' => 'Remove',
			),
	) );

}
add_action( 'cmb2_init', __NAMESPACE__ . '\\image_gallery_metabox' );
