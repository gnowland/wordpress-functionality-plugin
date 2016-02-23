<?php
/**
 * @category Sidebar Image Gallery Widget
 * @package  functionality
 * @author   Gifford Nowland
 *
 */

namespace Nowland\Functionality\Widgets;

// Prevent direct file access
if(!defined('WPINC')){die('Abort!');}

if(!class_exists('\Nowland\Functionality\Widgets\Sidebar_Image_Gallery')){

	/**
	 * Adds Sidebar_Image_Gallery widget.
	 */
	class Sidebar_Image_Gallery extends \WP_Widget {

		/**
		 * Widget Slug
		 * A unique identifier for your widget.
		 *
		 * The widget slug is used as the text domain when internationalizing strings
		 * of text. Its value should match the Text Domain file header in the main
		 * widget file.
		 *
		 * @since    1.0.0
		 * @var      string
		 */
		protected $widget_slug = 'sidebar_image_gallery';

		/**
		 * Register widget with WordPress.
		 */
		public function __construct() {

			$widget_ops = array(
				// 'classname'  => $this->get_widget_slug().'-class',
				'description' => __( 'Displays the images that are added to a page\'s Sidebar Image Gallery', $this->get_widget_slug() )
			);

			parent::__construct(
				$this->get_widget_slug(),	// Base ID
				__('&raquo; Sidebar Image Gallery', $this->get_widget_slug() ), // Name
				$widget_ops
			);

			// Refresh the widget's cached output with each new post
			add_action( 'save_post',    array( $this, 'flush_widget_cache' ));
			add_action( 'deleted_post', array( $this, 'flush_widget_cache' ));
			add_action( 'switch_theme', array( $this, 'flush_widget_cache' ));

		}

		/**
		 * Return the widget slug.
		 *
		 * @since    1.0.0
		 *
		 * @return    Plugin slug variable.
		 */
		public function get_widget_slug() {
				return $this->widget_slug;
		}

		/*--------------------------------------------------*/
		/* Widget API Functions
		/*--------------------------------------------------*/

		/**
		 * Outputs the content of the widget.
		 *
		 * @param array args  The array of form elements
		 * @param array instance The current instance of the widget
		 */
		public function widget( $args, $instance ) {

			// Check if there is a cached output
			$cache = wp_cache_get( $this->get_widget_slug(), 'widget' );
			if(!is_array($cache)){ $cache = array(); }
			if(!isset($args['widget_id'])){ $args['widget_id'] = $this->id; }
			if(isset($cache[ $args['widget_id']])){ return print $cache[$args['widget_id']]; }

			// Extract $args
			extract($args, EXTR_SKIP);

			global $post;
			setup_postdata( $post );

			$prefix = '_sitename_';

			// Get the list of files
			$files = get_post_meta( get_the_ID(), $prefix . 'sidebar_image_gallery_array', true );

			if( $files ){

				// Put everything into a string
				$widget_string = $before_widget;

				// Widget Display
				ob_start();

				// Display Widget Title
				$title = apply_filters( 'widget_title', empty( $instance['title'] ) ? '' : $instance['title'], $instance, $this->id_base );
				if ($title){
					echo $before_title . $title . $after_title;
				}

				echo "\n";

				// echo '<div class="file-list-wrap">';

				// Loop through them and output an image
				foreach ( (array) $files as $attachment_id => $attachment_url ) {
					$attachment_url_medium = wp_get_attachment_image_src( $attachment_id, 'medium' )[0];
					$attachment_url_large = wp_get_attachment_image_src( $attachment_id, 'large' )[0];
					$attachment_title = get_the_title( $attachment_id );

					echo '<a href="'.$attachment_url_large.'" class="swipebox"'.(($attachment_title)?' title="'.$attachment_title.'"':'').'>';
					echo '<figure class="sidebar-image" style="background-image: url(\''.$attachment_url_medium.'\');">';
					if( $attachment_title ){
						echo '<figcaption>'.$attachment_title.'</figcaption>';
					}
					echo "</figure></a>\n";
				}

				// echo '</div>';

				$widget_string .= ob_get_clean();
				$widget_string .= $after_widget;

				// Set Cache
				$cache[ $args['widget_id'] ] = $widget_string;
				wp_cache_set( $this->get_widget_slug(), $cache, 'widget' );

				print $widget_string;
			}

			wp_reset_postdata();

		} // end widget

		public function flush_widget_cache(){
			wp_cache_delete( $this->get_widget_slug(), 'widget' );
		}

		/**
		 * Sanitize widget form values as they are saved.
		 *
		 * @see WP_Widget::update()
		 *
		 * @param array $new_instance Values just sent to be saved.
		 * @param array $old_instance Previously saved values from database.
		 *
		 * @return array Updated safe values to be saved.
		 */
		public function update( $new_instance, $old_instance ) {
			$instance = $old_instance;

			$new_instance = wp_parse_args(
				(array) $new_instance,
				array(
					'title' => ''
				)
			);

			$instance['title'] = strip_tags($new_instance['title']);

			return $instance;
		} // end update

		/**
		 * Back-end widget form.
		 *
		 * @see WP_Widget::form()
		 *
		 * @param array $instance Previously saved values from database.
		 */
		public function form( $instance ) {

			// Set Defaults
			$instance = wp_parse_args(
				(array) $instance,
				array(
					'title' => ''
				)
			);

			$title = strip_tags($instance['title']);

?>
		<p><label><?php _e('Title:'); ?> <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" /></label></p>
		<p>This widget displays the images that are added to <a href="<?= admin_url('edit.php?post_type=page'); ?>">a page</a>'s Sidebar Image Gallery.</p>
<?php

		} // end back-end form

	}// class Sidebar_Image_Gallery

	// Register Sidebar_Image_Gallery widget
	add_action( 'widgets_init', function(){
		register_widget( '\Nowland\Functionality\Widgets\Sidebar_Image_Gallery' );
	});

} // end if(!class_exists)
