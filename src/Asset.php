<?php

namespace AgriLife\OfficeLocator;

class Asset {

  public function __construct() {

    add_action( 'wp_enqueue_scripts', array( $this, 'register_js' ) );

  }

  public function register_js() {
    
    wp_register_script(
      'jquery-cookie',
      AG_COU_DIR_URL . 'bower_components/jquery-cookie/jquery.cookie.js',
      array( 'jquery' ),
      false,
      true
    );

    wp_register_script(
      'county-office-locator',
      AG_COU_DIR_URL . '/js/county-office-locator.js',
      array( 'jquery', 'jquery-cookie' ),
      false,
      true
    );

  }

}