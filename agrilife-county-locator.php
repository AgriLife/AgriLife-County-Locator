<?php
/**
 * Plugin Name: AgriLife County Office Locator
 * Plugin URI: https://github.com/AgriLife/AgriLife-County-Office-Locator
 * Description: County Office Locator Widget for AgriLife Extension sites
 * Version: 1.0
 * Author: Zach Watkins
 * Author URI: http://github.com/ZachWatkins
 * Author Email: watkinza@gmail.com
 * License: GPL2+
 */

// require 'vendor/autoload.php';

define( 'AG_COU_DIRNAME', 'agrilife-county-locator' );
define( 'AG_COU_DIR_PATH', plugin_dir_path( __FILE__ ) );
define( 'AG_COU_DIR_FILE', __FILE__ );
define( 'AG_COU_DIR_URL', plugin_dir_url( __FILE__ ) );
define( 'AG_COU_TEMPLATE_PATH', AG_COU_DIR_PATH . 'view' );

require AG_COU_DIR_PATH . 'vendor/autoload.php';

// Register plugin activation functions
$activate = new \AgriLife\Core\Activate;
register_activation_hook( __FILE__, array( $activate, 'run') );

// Register plugin deactivation functions
$deactivate = new \AgriLife\Core\Deactivate;
register_deactivation_hook( __FILE__, array( $deactivate, 'run' ) );

$ext_asset = new \AgriLife\OfficeLocator\Asset();

$ext_ajax = new \AgriLife\OfficeLocator\Ajax();

add_action( 'widgets_init', function() {
  register_widget( '\AgriLife\OfficeLocator\Widget' );
});