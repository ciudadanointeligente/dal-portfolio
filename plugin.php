<?php
/**
 * Plugin Name: DAL Portfolio Gallery
 * Plugin URI: http://github.com/ciudadanointeligente/dalboot
 * Description: Portfolio Gallery provides an easy way to display your portfolio on your website
 *
 * Version: 1.0
 *
 * Author: Montse Lobos
 * Author URI: http://www.ciudadanointeligente.org
 *
 * License: GNU General Public License v2.0
 * License URI: http://www.opensource.org/licenses/gpl-license.php
 */

// Plugin localization support
load_plugin_textdomain('dal-portfolio', false, basename( dirname( __FILE__ ) ) . '/languages' );

register_activation_hook( __FILE__, 'dal_portfolio_activation' );
/**
 * This function runs on plugin activation. It checks for the existence of the CPT
 * and creates it otherwise
 *
 * @since 0.9
 */
function dal_portfolio_activation() {

    if( ! post_type_exists( 'portfolio' ) ) {
	dal_portfolio_init();
	global $_dal_portfolio;
	$_dal_portfolio->create_post_type();
	$_dal_portfolio->create_taxonomy();
    }
    flush_rewrite_rules();
}

register_deactivation_hook( __FILE__, 'dal_portfolio_deactivation' );
/**
 * This function runs on deactivation and flushes the re-write rules so permalinks work properly
 * 
 * @since 1.0 
 */
function dal_portfolio_deactivation() {
    
    flush_rewrite_rules();
}


add_action( 'after_setup_theme', 'dal_portfolio_init' );
/**
 * Initializes the plugin
 * Includes the libraries, defines global variables, instantiates the class
 *
 * @since 0.9
 */
function dal_portfolio_init() {
    global $_dal_portfolio;

    define( 'dal-portfolio_URL', "/plugins/dal-portfolio/" );
    define( 'dal-portfolio_VERSION', '1.1' );

    /** Includes **/
    require_once( dirname( __FILE__ ) . '/includes/class-portafolio.php' );


    /** Instantiate **/
    $_dal_portfolio = new Dal_Portfolio;

}


?>
