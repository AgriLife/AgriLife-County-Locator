<?php

namespace AgriLife\OfficeLocator;

class Widget extends \WP_Widget {

  public function __construct() {
    
    parent::__construct(
      'agrilife_county_locator',
      __( 'County Office Locator', 'agrilife' ),
      array( 'description' => __( 'Locates the current visitor\'s county and displays related information' ) )
    );

  }

  public function widget( $args, $instance ) {
    
    wp_localize_script( 'county-office-locator', 'Ag', array(
      'ajax_url' => admin_url( 'admin-ajax.php' ),
      'counties' => json_encode( get_transient('county_office_locator') )
    ) );
    wp_enqueue_script( 'underscore' );
    wp_enqueue_script( 'jquery-cookie' );
    wp_enqueue_script( 'county-office-locator' );
    $title = apply_filters( 'widget_title', $instance['title'] );

    echo $args['before_widget'];
    if ( ! empty( $title ) )
      echo $args['before_title'] . $title . $args['after_title'];
    ob_start();
    include AG_COU_DIR_PATH . '/view/county-locator.php';
    $content = ob_get_clean();
    echo $content;
    echo $args['after_widget'];

  }

  public function form( $instance ) {

    if ( isset( $instance['title'] ) ) {
      $title = $instance['title'];
    } else {
      $title = __( 'County Office', 'agrilife' );
    }

    echo '<p>';
    printf( '<label for="%s">%s</label>',
      $this->get_field_id( 'title' ),
      __( 'Title:' )
    );

    printf( '<input class="widefat" id="%s" name="%s" type="text" value="%s" />',
      $this->get_field_id( 'title' ),
      $this->get_field_name( 'title' ),
      esc_attr( $title )
    );
    echo '</p>';

  }

  public function update( $new_instance, $old_instance ) {

    $instance = array();
    $instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';

    return $instance;

  }

}