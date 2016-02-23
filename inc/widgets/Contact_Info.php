<?php
/**
 * @category Contact Info Widget (schema-tagged)
 * @package  functionality
 * @author   Gifford Nowland
 *
 */

namespace Nowland\Functionality\Widgets;

// Prevent direct file access
if(!defined('WPINC')){die('Abort!');}

if(!class_exists('\Nowland\Functionality\Widgets\Contact_Info')){

	/**
	 * Adds Contact_Info widget.
	 */
	class Contact_Info extends \WP_Widget {

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
		protected $widget_slug = 'contact_info';

		/**
		 * Register widget with WordPress.
		 */
		public function __construct() {

			$widget_ops = array(
				// 'classname'  => $this->get_widget_slug().'-class',
				'description' => __( 'Accordion sidebar navigation', $this->get_widget_slug() )
			);

			parent::__construct(
				$this->get_widget_slug(),	// Base ID
				__('&raquo; Contact Info', $this->get_widget_slug() ), // Name
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

			// Inputs
			//$title          = !empty($instance['title'])          ? $instance['title']          : '';
			$company        = !empty($instance['company'])        ? $instance['company']        : '';
			$map_url        = !empty($instance['map_url'])        ? $instance['map_url']        : '';
			$address_line_1 = !empty($instance['address_line_1']) ? $instance['address_line_1'] : '';
			$address_line_2 = !empty($instance['address_line_2']) ? $instance['address_line_2'] : '';
			$city           = !empty($instance['city'])           ? $instance['city']           : '';
			$state          = !empty($instance['state'])          ? $instance['state']          : '';
			$zip            = !empty($instance['zip'])            ? $instance['zip']            : null;
			$phone          = !empty($instance['phone'])          ? $instance['phone']          : '';
			$fax            = !empty($instance['fax'])            ? $instance['fax']            : '';
			$email          = !empty($instance['email'])          ? $instance['email']          : '';

			// Put everything into a string
			$widget_string = $before_widget;

			// Widget Display
			ob_start();

			// Display Widget Title
			//$title = apply_filters( 'widget_title', empty( $instance['title'] ) ? '' : $instance['title'], $instance, $this->id_base );
			//if ($title){
			//	echo $before_title . $title . $after_title;
			//}

			$schema_start = '<div itemscope itemtype="http://schema.org/LocalBusiness" class="metadata">'.PHP_EOL;

			if($company){
				$schema_start .= '<meta itemprop="name" content="' . $company . '">'.PHP_EOL;
			}

			if($address_line_1 || $address_line_2 || $city || $state || $zip){
				$schema_address = '<div itemprop="address" itemscope itemtype="http://schema.org/PostalAddress">';

				if($map_url){
					$schema_address .= '<a href="'.$map_url.'" target="_blank" title="Open Map">';
				}

				if($address_line_1 || $address_line_2){
					$schema_address .= '<span itemprop="streetAddress">' . ($address_line_1 ? $address_line_1 : '') . ($address_line_2 ? ', '.$address_line_2 : '') . '</span>';
				}

				if($city){
					$schema_address .= ', <br><span itemprop="addressLocality">' . $city . '</span>';
				}

				if($state){
					$schema_address .= ', <span itemprop="addressRegion">' . $state . '</span>';
				}

				if($zip){
					$schema_address .= ' <span itemprop="postalCode">' . $zip . '</span>';
				}

				if($map_url){
					$schema_address .= '</a>'.PHP_EOL; // end map_url
				}

				$schema_address .= '</div>'.PHP_EOL; // end PostalAddress
			}

			if($phone){
				$phone_digits = preg_replace("/[^0-9+]/", "", $phone);
				$schema_phone = '<div itemprop="telephone"><a href="tel:' . $phone_digits . '">' . $phone . '</a></div>'.PHP_EOL;
			}

			if($fax){
				$schema_phone .= '<div itemprop="faxNumber">' . $fax . '</div>'.PHP_EOL;
			}

			if($email){
				$schema_email = '<div itemprop="email"><a href="mailto:' . $email . '">' . str_replace('@', '@<wbr />', $email) . '</a></div>'.PHP_EOL;
			}

			$schema_end = '</div>'.PHP_EOL; // end LocalBusiness

			if($args['id'] == 'sidebar-footer'){
				// If this is the footer, move email to the middle
				echo $schema_start.
						 '<div class="col">'.$schema_address.'</div>'.
						 '<div class="col">'.$schema_email . '</div>'.
						 '<div class="col">'.$schema_phone . '</div>'.
						 $schema_end;
			} else {
				// Show email last everywhere else
				echo $schema_start.
						 $schema_address.
						 $schema_phone.
						 $schema_email.
						 $schema_end;
			}

			$widget_string .= ob_get_clean();
			$widget_string .= $after_widget;

			// Set Cache
			$cache[ $args['widget_id'] ] = $widget_string;
			wp_cache_set( $this->get_widget_slug(), $cache, 'widget' );

			print $widget_string;

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
					//'title'          => '',
					'company'        => '',
					'map_url'        => '',
					'address_line_1' => '',
					'address_line_2' => '',
					'city'           => '',
					'state'          => '',
					'zip'            => '',
					'phone'          => '',
					'fax'            => '',
					'email'          => ''
				)
			);

			//$instance['title']          = strip_tags($new_instance['title']);
			$instance['company']        = strip_tags(stripslashes($new_instance['company']));
			$instance['map_url']        = esc_url_raw($new_instance['map_url']);
			$instance['address_line_1'] = strip_tags(stripslashes($new_instance['address_line_1']));
			$instance['address_line_2'] = strip_tags(stripslashes($new_instance['address_line_2']));
			$instance['city']           = strip_tags(stripslashes($new_instance['city']));
			$instance['state']          = strip_tags(stripslashes($new_instance['state']));
			$instance['zip']            = (int) $new_instance['zip'];
			$instance['phone']          = strip_tags(stripslashes($new_instance['phone']));
			$instance['fax']            = strip_tags(stripslashes($new_instance['fax']));
			$instance['email']          = is_email($new_instance['email']);

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
					//'title'          => '',
					'company'        => '',
					'map_url'        => '',
					'address_line_1' => '',
					'address_line_2' => '',
					'city'           => '',
					'state'          => '',
					'zip'            => '',
					'phone'          => '',
					'fax'            => '',
					'email'          => ''
				)
			);

			//$title          = strip_tags($instance['title']);
			$company        = strip_tags(stripslashes($instance['company']));
			$map_url        = esc_url($instance['map_url']);
			$address_line_1 = strip_tags(stripslashes($instance['address_line_1']));
			$address_line_2 = strip_tags(stripslashes($instance['address_line_2']));
			$city           = strip_tags(stripslashes($instance['city']));
			$state          = strip_tags(stripslashes($instance['state']));
			$zip            = strip_tags(stripslashes($instance['zip']));
			$phone          = strip_tags(stripslashes($instance['phone']));
			$fax            = strip_tags(stripslashes($instance['fax']));
			$email          = is_email($instance['email']);

			$us_states = array('AL', 'AK', 'AS', 'AZ', 'AR', 'CA', 'CO', 'CT', 'DE', 'DC', 'FM', 'FL', 'GA', 'GU', 'HI', 'ID', 'IL', 'IN', 'IA', 'KS', 'KY', 'LA', 'ME', 'MH', 'MD', 'MA', 'MI', 'MN', 'MS', 'MO', 'MT', 'NE', 'NV', 'NH', 'NJ', 'NM', 'NY', 'NC', 'ND', 'MP', 'OH', 'OK', 'OR', 'PW', 'PA', 'PR', 'RI', 'SC', 'SD', 'TN', 'TX', 'UT', 'VT', 'VI', 'VA', 'WA', 'WV', 'WI', 'WY', 'AE', 'AA', 'AP');

			/* <p><label><?php _e('Title:'); ?> <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" /></label></p> */

?>
			<p><label><?php _e('Company Name'); ?>
				<input class="widefat" id="<?php echo $this->get_field_id('company'); ?>" name="<?php echo $this->get_field_name('company'); ?>" type="text" value="<?php echo esc_attr($company); ?>" />
			</label></p>
			<p><label><?php _e('Address'); ?>
				<input class="widefat" id="<?php echo $this->get_field_id('address_line_1'); ?>" name="<?php echo $this->get_field_name('address_line_1'); ?>" type="text" value="<?php echo esc_attr($address_line_1); ?>" /></label><br>
				<input class="widefat" id="<?php echo $this->get_field_id('address_line_2'); ?>" name="<?php echo $this->get_field_name('address_line_2'); ?>" type="text" value="<?php echo esc_attr($address_line_2); ?>" />
			</p>
			<p style="display: table; width: 100%;">
				<label style="display: table-cell; width: 50%;"><?php _e('City'); ?><br>
					<input class="widefat" id="<?php echo $this->get_field_id('city'); ?>" name="<?php echo $this->get_field_name('city'); ?>" type="text" value="<?php echo esc_attr($city); ?>" />
				</label>
				<span style="display: table-cell; width: 25%;">
					<label for="<?php echo $this->get_field_id('state'); ?>"><?php _e( 'State' ); ?></label>
					<select class="widefat" id="<?php echo $this->get_field_id('state'); ?>" name="<?php echo $this->get_field_name('state'); ?>">
						<option value="" <?php selected( $state, '' ); ?>></option>
						<?php foreach($us_states as $state_abbv) : ?>
							<option value="<?php echo $state_abbv ?>" <?php selected( $state, $state_abbv); ?>><?php _e($state_abbv); ?></option>
						<?php endforeach; ?>
					</select>
				</span>
				<label style="display: table-cell; width: 25%;"><?php _e('Zip'); ?><br>
					<input class="widefat" id="<?php echo $this->get_field_id('zip'); ?>" name="<?php echo $this->get_field_name('zip'); ?>" type="text" value="<?php echo esc_attr($zip); ?>" />
				</label>
			</p>
			<p><label><?php _e('Map URL'); ?>
				<input class="widefat" id="<?php echo $this->get_field_id('map_url'); ?>" name="<?php echo $this->get_field_name('map_url'); ?>" type="text" value="<?php echo esc_attr($map_url); ?>" />
			</label></p>
			<p><label><?php _e('Phone'); ?>
				<span style="display: table;">
					<?php // <span style="display: table-cell">+1&nbsp;</span> ?>
					<span style="display: table-cell"><input class="widefat" id="<?php echo $this->get_field_id('phone'); ?>" name="<?php echo $this->get_field_name('phone'); ?>" type="text" value="<?php echo esc_attr($phone); ?>" /></span>
				</span>
			</label></p>
			<p><label><?php _e('Fax'); ?>
					<span style="display: table;">
						<?php // <span style="display: table-cell">+1&nbsp;</span> ?>
						<span style="display: table-cell;"><input class="widefat" id="<?php echo $this->get_field_id('fax'); ?>" name="<?php echo $this->get_field_name('fax'); ?>" type="text" value="<?php echo esc_attr($fax); ?>" /></span>
					</span>
			</label></p>
			<p><label><?php _e('Email'); ?>
				<input class="widefat" id="<?php echo $this->get_field_id('email'); ?>" name="<?php echo $this->get_field_name('email'); ?>" type="text" value="<?php echo esc_attr($email); ?>" />
			</label></p>
<?php


		} // end back-end form

	}// class Contact_Info

	// Register Contact_Info widget
	add_action( 'widgets_init', function(){
		register_widget( '\Nowland\Functionality\Widgets\Contact_Info' );
	});

} // end if(!class_exists)
