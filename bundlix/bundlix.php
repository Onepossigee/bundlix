<?php
/**
 * Plugin Name: BundliX - Digital Services Platform
 * Plugin URI: https://bundlix.com
 * Description: Enterprise-grade VTU platform for Ghana. Data, Airtime, WAEC Pins, and more. Completely masked frontend.
 * Version: 1.0.0
 * Author: BundliX Team
 * Author URI: https://bundlix.com
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: bundlix
 * Domain Path: /languages
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('BUNDLIX_VERSION', '1.0.0');
define('BUNDLIX_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('BUNDLIX_PLUGIN_URL', plugin_dir_url(__FILE__));
define('BUNDLIX_PLUGIN_BASENAME', plugin_basename(__FILE__));

// Core file includes
require_once BUNDLIX_PLUGIN_DIR . 'includes/class-bundlix-mask.php';
require_once BUNDLIX_PLUGIN_DIR . 'includes/class-bundlix-router.php';
require_once BUNDLIX_PLUGIN_DIR . 'includes/class-bundlix-assets.php';
require_once BUNDLIX_PLUGIN_DIR . 'includes/class-bundlix-security.php';

/**
 * Initialize BundliX Core
 */
function bundlix_init() {
    // Initialize masking layer
    BundliX_Mask::init();
    
    // Initialize router
    BundliX_Router::init();
    
    // Initialize asset manager
    BundliX_Assets::init();
    
    // Initialize security hardening
    BundliX_Security::init();
}
add_action('plugins_loaded', 'bundlix_init');

/**
 * Activation hook
 */
function bundlix_activate() {
    // Flush rewrite rules to register custom endpoints
    BundliX_Router::register_endpoints();
    flush_rewrite_rules(true);
    
    // Create necessary database tables (will be implemented in Phase 3)
    // BundliX_Installer::create_tables();
}
register_activation_hook(__FILE__, 'bundlix_activate');

/**
 * Deactivation hook
 */
function bundlix_deactivate() {
    // Flush rewrite rules to clean up
    flush_rewrite_rules(true);
}
register_deactivation_hook(__FILE__, 'bundlix_deactivate');
