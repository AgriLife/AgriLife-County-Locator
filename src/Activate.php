<?php
namespace AgriLife\OfficeLocator;

/**
 * Plugin activation class
 * @package AgriLife-Core
 * @since 1.0.0
 */
class Activate {

  public function run( $network_wide ) {

    if ( function_exists( 'is_multisite' ) && is_multisite() ) {
      if ( $network_wide ) {
        $this->activate_network();
      } else {
        $this->activate_single_site();
      }
    } else {
      $this->activate_single_site();
    }

  }

  /**
   * Fires activation commands for each site on the network
   * @since 1.0.0
   * @return void
   */
  protected function activate_network() {

    $blog_ids = $this->get_blog_ids();

    foreach ( $blog_ids as $id ) {
      switch_to_blog( $id );
      $this->activate_single_site();
    }

    restore_current_blog();

  }

  /**
   * Runs activation commands for a single site
   * @since 1.0.0
   * @return void
   */
  private function activate_single_site() {

    do_action( 'agrilife_activation' );

  }

}