<?php

/**
 * OSFA_Opening_Hours_Widget
 * 
 * @author Eric Daams <eric@164a.com>
 */

class OSFA_Opening_Hours_Widget extends WP_Widget {

	/**
	 * Widget constructor. 
	 */
	public function OSFA_Opening_Hours_Widget() {
		$widget_ops = array( 'classname' => 'osfa_opening_hours_widget', 'description' => __( 'Display your store\'s opening hours', 'osfa_opening_hours' ) );
		$control_ops = array( 'id_base' => 'osfa_opening_hours_widget' );
		$this->WP_Widget('osfa_opening_hours_widget', __('Opening Hours', 'osfa_opening_hours'), $widget_ops, $control_ops);
	}

	/**
	 * Widget form. 
	 *
	 * @param array $instance
	 * @return void
	 */
	public function form($instance) {
		$title = isset($instance['title']) ? esc_attr($instance['title']) : '';
		?>

		<p>
			<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e( 'Title:', 'osfa_opening_hours_widget' ); ?></label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id('title') ); ?>" name="<?php echo esc_attr( $this->get_field_name('title') ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
		</p>

		<?php
	}

	/**
	 * Widget's update routine. 
	 *
	 * @param array $new_instance
	 * @param array $instance
	 * @return void
	 */
	public function update($new_instance, $old_instance) {
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		return $instance;
	}

	/**
	 * Widget's front-end display. 
	 * 
	 * @param array $args
	 * @param array $instance
	 * @return void
	 */	
	public function widget($args, $instance) {

		extract($args);

		// Title, with default 
		$title = apply_filters('widget_title', empty($instance['title']) ? __('Opening Hours', 'osfa_opening_hours' ) : $instance['title'], $instance, $this->id_base);
		
		// Start widget output
		echo $before_widget;

		// Widget title
		if ( $title ) echo $before_title . $title . $after_title; 

		$days = array(
    		__( 'Monday', 'osfa_opening_hours' ),
    		__( 'Tuesday', 'osfa_opening_hours' ),
    		__( 'Wednesday', 'osfa_opening_hours' ),
    		__( 'Thursday', 'osfa_opening_hours' ),
    		__( 'Friday', 'osfa_opening_hours' ),
    		__( 'Saturday', 'osfa_opening_hours' ),
    		__( 'Sunday', 'osfa_opening_hours' )
		);

		$hours = OSFA_Opening_Hours::get_plugin_setting('hours');

		if ( $hours ) : 			
			?>

			<dl>
				<?php foreach( $days as $key => $day ) :

					$day_hours = array_key_exists( 'closed', $hours[$key] ) && $hours[$key]['closed'] == 'on' 
						? __( 'Closed', 'osfa_opening_hours' ) 
						: '<span class="open_time">'.$hours[$key]['from'].'</span> - <span class="closing_time">'.$hours[$key]['to'].'</span>';
					?>

					<dt><?php echo $day ?></dt>
					<dd><?php echo $day_hours ?></dd>

				<?php endforeach ?>
			</dl>

			<?php if ( OSFA_Opening_Hours::get_plugin_setting('comment') ) : ?>

				<p><?php echo OSFA_Opening_Hours::get_plugin_setting('comment') ?></p>

			<?php 
			endif;

		endif;
	}
}