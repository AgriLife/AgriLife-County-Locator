<?php
namespace AgriLife\OfficeLocator;

/**
 * Plugin deactivation class
 * @package AgriLife-Core
 * @since 1.0.0
 */
class Deactivate {

  public function run( $network_wide ) {

    if ( function_exists( 'is_multisite' ) && is_multisite() ) {
      if ( $network_wide ) {
        $this->deactivate_network();
      } else {
        $this->deactivate_single_site();
      }
    } else {
      $this->deactivate_single_site();
    }

  }

  /**
   * Fires activation commands for each site on the network
   * @since 1.0.0
   * @return void
   */
  private function deactivate_network() {

    $blog_ids = $this->get_blog_ids();

    foreach ( $blog_ids as $id ) {
      switch_to_blog( $id );
      $this->deactivate_single_site();
    }

    restore_current_blog();

  }

  /**
   * Runs activation commands for a single site
   * @since 1.0.0
   * @return void
   */
  private function deactivate_single_site() {

  }

}