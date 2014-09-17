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
		}
				
		$number_of_posts = -1;
		if ( ! empty( $instance['show_number_files'] ) ) {
			$number_of_posts = $instance['show_number_files'];
		}

		$orderby = 'date';
		if ( ! empty( $instance['sort_parameter'] ) ) {
			$orderby = $instance['sort_parameter'];
		}
		
		$order_direction = 'DESC';
		if ( ! empty( $instance['sort_direction'] ) ) {
			$order_direction = $instance['sort_direction'];
		}

		// TODO: get template choice. Echo css based on choice
		
		echo '<style>.list_mediafiles_box{';
		echo 'border: 1px solid #e2e2e2;';
		echo 'margin: 5px;';
		echo 'padding: 5px;'; 
		echo '} .list_mediafiles_box img{';
		echo 'max-width:100px;';
		echo '} </style>';
		
		$args = array(
		    'post_type' => 'attachment',
		    'numberposts' => $number_of_posts,
		    'post_status' => null,
		    'post_parent' => null, 
			'orderby' => $orderby,
			'order' => $order_direction,
		    ); 
		
		$attachments = get_posts($args);
		if ($attachments) {
		    foreach ($attachments as $post) {
		    	echo '<div class="list_mediafiles_box">';
		        setup_postdata($post);
		        echo '<h4>' . get_the_title($post->ID) . '</h4>';
		        the_attachment_link($post->ID, false);
		        echo '<span class="aligncenter" style="word-break:break-word;">';
		        the_excerpt();
		        echo '</span>';
		    	echo '</div>';
		    }
		}
		//echo $args['after_widget'];
	}

	/**
	 * Outputs the options form on admin
	 *
	 * @param array $instance The widget options
	 */
	public function form( $instance ) {
		// outputs the options form on admin
		
		$defaults = array( 
				'title' => __( 'Uploaded media', 'text_domain' ), 
				'show_number_files' => 5, 
				'sort_parameter' => 'date', 
				'sort_direction' => 'DESC' );
		$instance = wp_parse_args( (array) $instance, $defaults );
		
		
		$title = $instance[ 'title' ];
		?>
				<p>
				<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label> 
				<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
				</p>
		<?php
		$number_of_files = $instance[ 'show_number_files' ];
		?>
				<p>
				<label for="<?php echo $this->get_field_id( 'show_number_files' ); ?>"><?php _e( 'Number of files to show:' ); ?></label> 
				<input class="widefat" 
					id="<?php echo $this->get_field_id( 'show_number_files' ); ?>" 
					name="<?php echo $this->get_field_name( 'show_number_files' ); ?>" 
					type="number" value="<?php echo esc_attr( $number_of_files ); ?>">
				</p>
		<?php
		$orderby = $instance[ 'sort_parameter' ];
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
		$sort_direction = $instance[ 'sort_direction' ];
		?>
				<p>
				<span><?php _e( 'Sort direction:' ); ?></span>
				<label for="<?php echo $this->get_field_id( 'sort_direction' ) . '-desc'; ?>"><?php _e( 'Descending' ); ?></label>
				
				<input 
					class="widefat" 
					id="<?php echo $this->get_field_id( 'sort_direction' ) . '-desc'; ?>" 
					name="<?php echo $this->get_field_name( 'sort_direction' ); ?>" 
					type="radio" 
					value="DESC"
					<?php echo (($sort_direction == 'DESC') ? 'checked' : ''); ?>
					>
				<label for="<?php echo $this->get_field_id( 'sort_direction' ) . '-asc'; ?>"><?php _e( 'Ascending' ); ?></label>
				<input 
					class="widefat" 
					id="<?php echo $this->get_field_id( 'sort_direction' ) . '-asc'; ?>" 
					name="<?php echo $this->get_field_name( 'sort_direction' ); ?>" 
					type="radio" 
					value="ASC"
					<?php echo (($sort_direction == 'ASC') ? 'checked' : ''); ?>
					>	
					
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
		$instance['sort_direction'] = ( ! empty( $new_instance['sort_direction'] ) ) ? strip_tags( $new_instance['sort_direction'] ) : '';
		
		return $instance;
	}
}

add_action( 'widgets_init', function(){
	register_widget( 'List_MediaFiles_Widget' );
});

