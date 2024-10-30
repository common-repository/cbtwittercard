<?php
/**
 * Plugin Name:       CBX Twitter Card
 * Plugin URI:        http://wpboxr.com/product/cbx-twitter-card
 * Description:       Twitter Card For Wordpress
 * Version:           1.0.7
 * Author:            WPBoxr Team
 * Author URI:        http://wpboxr.com
 * Text Domain:       cbtwittercard
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/*----------------------------------------------------------------------------*
 * Public-Facing Functionality
 *----------------------------------------------------------------------------*/
define('CBTWITTERCARDNAME', __('CBX Twitter Card','cbtwittercard'));
define('CBTWITTERCARDSUFFIX', '_cbtwittercard');
define('CBTWITTERCARDVERSION', '1.0.7');

require_once( plugin_dir_path( __FILE__ ) . 'public/class-cbtwittercard.php' );


register_activation_hook( __FILE__, array( 'CbtwitterCard', 'activate' ) );
register_deactivation_hook( __FILE__, array( 'CbtwitterCard', 'deactivate' ) );

add_action( 'plugins_loaded', array( 'cbtwittercard', 'get_instance' ) );


if ( is_admin() && ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) ) {

	require_once( plugin_dir_path( __FILE__ ) . 'admin/class-cbtwittercard-admin.php' );
	add_action( 'plugins_loaded', array( 'CbtwitterCardAdmin', 'get_instance' ) );
        //add_action( 'activated_plugin', array( 'CbtwitterCardAdmin','cbtwittercard_activation_error'));
        

}
