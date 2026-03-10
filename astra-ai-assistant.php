<?php
/**
 * Plugin Name: Astra AI Assistant
 * Description: AI-powered chat assistant for Astra Theme users
 * Version: 1.0.0
 * Author: Tejas
 */

if ( ! defined( "ABSPATH" ) ) exit;

define( "AAA_PATH", plugin_dir_path( __FILE__ ) );
define( "AAA_URL",  plugin_dir_url( __FILE__ ) );

require_once AAA_PATH . "includes/class-settings.php";
require_once AAA_PATH . "includes/class-admin-page.php";
require_once AAA_PATH . "includes/class-context.php";
require_once AAA_PATH . "includes/class-ai-connector.php";

function aaa_init() {
    new AAA_Settings();
    new AAA_Admin_Page();
}
add_action( "plugins_loaded", "aaa_init" );
