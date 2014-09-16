<?php
/*
 Plugin Name: List mediafiles widget
Plugin URI: https://github.com/klasske/list_mediafiles_widget
Description: WordPress widget plugin that lists recent media files in a sidebar
Version: 1.0
Author: Klaske van Vuurden
Author URI: https://github.com/klasske/
Copyright: Klaske van Vuurden
*/


defined('ABSPATH') or die("Don't access directly!");

class List_MediaFiles_Widget extends WP_Widget {

	/**
	 * Sets up the widgets name etc
	 */
	public function __construct() {
		// widget actual processes
		parent::__construct(
			'list_mediafiles_widget', // Base ID
			__('List Mediafiles', 'text_domain'), // Name
			array( 'description' => __( 'A Widget to display Mediafiles', 'text_domain' ), ) // Args
		);
	}

	/**
	 * Outputs the content of the widget
	 *
	 * @param array $args
	 * @param array $instance
	 */
	public function widget( $args, $instance ) {
		// outputs the content of the widget
		echo $args['before_widget'];
		if ( ! empty( $instance['title'] ) ) {
			echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ). $args['after_title'];
		}else{
			echo $args['before_title'] . apply_filters( 'widget_title', __( 'Uploaded media', 'text_domain' )). $args['after_title'];
		}	
		$number_of_posts = -1;
		if ( ! empty( $instance['show_number_files'] ) ) {
			$number_of_posts = $instance['show_number_files'];
		}

		$orderby = 'date';
		if ( ! empty( $instance['sort_parameter'] ) ) {
			$orderby = $instance['sort_parameter'];
		}



		$args = array(
		    'post_type' => 'attachment',
		    'numberposts' => $number_of_posts,
		    'post_status' => null,
		    'post_parent' => null, 
			'orderby' => $orderby,
			'order' => 'DESC',
		    ); 
		$attachments = get_posts($args);
		if ($attachments) {
		    foreach ($attachments as $post) {
		        setup_postdata($post);
		        echo '<h3>' . get_the_title($post->ID) . '</h3>';
		        the_attachment_link($post->ID, false);
		        the_excerpt();
		    }
		}
		echo $args['after_widget'];
	}

	/**
	 * Outputs the options form on admin
	 *
	 * @param array $instance The widget options
	 */
	public function form( $instance ) {
		// outputs the options form on admin
		if ( isset( $instance[ 'title' ] ) ) {
			$title = $instance[ 'title' ];
		}
		else {
			$title = __( 'Uploaded media', 'text_domain' );
		}
		?>
				<p>
				<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label> 
				<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
				</p>
		<?php
		if ( isset( $instance[ 'show_number_files' ] ) ) {
			$number_of_files = $instance[ 'show_number_files' ];
		}
		else {
			$number_of_files = 5;
		}
		?>
				<p>
				<label for="<?php echo $this->get_field_id( 'show_number_files' ); ?>"><?php _e( 'Number of files to show:' ); ?></label> 
				<input class="widefat" 
					id="<?php echo $this->get_field_id( 'show_number_files' ); ?>" 
					name="<?php echo $this->get_field_name( 'show_number_files' ); ?>" 
					type="number" value="<?php echo esc_attr( $number_of_files ); ?>">
				</p>
		<?php
		if ( isset( $instance[ 'sort_parameter' ] ) ) {
			$orderby = $instance[ 'sort_parameter' ];
		}
		else {
			$orderby = 'date';
		}
		?>
				<p>
				<label for="<?php echo $this->get_field_id( 'sort_parameter' ); ?>"><?php _e( 'Sort by:' ); ?></label>
				<select class="widefat"
					id="<?php echo $this->get_field_id( 'sort_parameter' ); ?>" 
					name="<?php echo $this->get_field_name( 'sort_parameter' ); ?>" >
					<option value="date" <?php echo (($orderby == 'date') ? 'selected' : ''); ?>>Date</option>
					<option value="title" <?php echo (($orderby == 'title') ? 'selected' : ''); ?>>Title</option>
					<option value="rand" <?php echo (($orderby == 'rand') ? 'selected' : ''); ?>>Random</option>

				</select>
				</p>
		<?php
		if ( isset( $instance[ 'sort_direction' ] ) ) {
			$sort_direction = $instance[ 'sort_direction' ];
		}
		else {
			$sort_direction = 'DESC';
		}
		?>
				<p>
				<label for="<?php echo $this->get_field_id( 'sort_direction' ); ?>"><?php _e( 'Sort direction:' ); ?></label>
				<input class="widefat" id="<?php echo $this->get_field_id( 'sort_direction' ); ?>" name="<?php echo $this->get_field_name( 'sort_direction' ); ?>" type="text" value="<?php echo esc_attr( $sort_direction ); ?>">
				</p>
		<?php
				
	}

	/**
	 * Processing widget options on save
	 *
	 * @param array $new_instance The new options
	 * @param array $old_instance The previous options
	 */
	public function update( $new_instance, $old_instance ) {
		// processes widget options to be saved
		$instance = array();
		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
		$instance['show_number_files'] = ( ! empty( $new_instance['show_number_files'] ) ) ? strip_tags( $new_instance['show_number_files'] ) : '';
		$instance['sort_parameter'] = ( ! empty( $new_instance['sort_parameter'] ) ) ? strip_tags( $new_instance['sort_parameter'] ) : '';

		return $instance;
	}
}

add_action( 'widgets_init', function(){
	register_widget( 'List_MediaFiles_Widget' );
});

