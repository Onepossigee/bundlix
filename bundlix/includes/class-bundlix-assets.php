<?php
/**
 * BundliX Assets - Asset Management System
 * 
 * Handles CSS and JS loading exclusively from plugin directory.
 * Implements version-based cache busting and optimization.
 */

if (!defined('ABSPATH')) {
    exit;
}

class BundliX_Assets {
    
    /**
     * Initialize asset manager
     */
    public static function init() {
        add_action('wp_enqueue_scripts', array(__CLASS__, 'register_app_assets'));
        add_action('admin_enqueue_scripts', array(__CLASS__, 'register_admin_assets'));
    }
    
    /**
     * Register frontend app assets
     */
    public static function register_app_assets() {
        // Only load on BundliX routes
        if (!self::is_bundlix_route()) {
            return;
        }
        
        // Deregister theme styles on BundliX routes
        self::deregister_theme_assets();
        
        // Register core CSS
        wp_register_style(
            'bundlix-core',
            BUNDLIX_PLUGIN_URL . 'assets/css/bundlix-core.css',
            array(),
            self::get_asset_version(),
            'all'
        );
        
        // Register dashboard CSS
        wp_register_style(
            'bundlix-dashboard',
            BUNDLIX_PLUGIN_URL . 'assets/css/bundlix-dashboard.css',
            array('bundlix-core'),
            self::get_asset_version(),
            'all'
        );
        
        // Register vendor CSS (e.g., FontAwesome, custom fonts)
        wp_register_style(
            'bundlix-vendor',
            BUNDLIX_PLUGIN_URL . 'assets/css/vendor.css',
            array(),
            self::get_asset_version(),
            'all'
        );
        
        // Enqueue styles
        wp_enqueue_style('bundlix-vendor');
        wp_enqueue_style('bundlix-core');
        wp_enqueue_style('bundlix-dashboard');
        
        // Register core JS
        wp_register_script(
            'bundlix-core',
            BUNDLIX_PLUGIN_URL . 'assets/js/bundlix-core.js',
            array('jquery'),
            self::get_asset_version(),
            true
        );
        
        // Register router JS for SPA navigation
        wp_register_script(
            'bundlix-router',
            BUNDLIX_PLUGIN_URL . 'assets/js/bundlix-router.js',
            array('bundlix-core'),
            self::get_asset_version(),
            true
        );
        
        // Register dashboard JS
        wp_register_script(
            'bundlix-dashboard',
            BUNDLIX_PLUGIN_URL . 'assets/js/bundlix-dashboard.js',
            array('bundlix-router'),
            self::get_asset_version(),
            true
        );
        
        // Register auth JS for login/register handling
        wp_register_script(
            'bundlix-auth',
            BUNDLIX_PLUGIN_URL . 'assets/js/bundlix-auth.js',
            array('bundlix-core'),
            self::get_asset_version(),
            true
        );
        
        // Localize script with app data
        wp_localize_script('bundlix-core', 'bundlixApp', array(
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('bundlix_auth_nonce'),
            'siteUrl' => home_url(),
            'appUrl' => home_url('/app'),
            'restUrl' => rest_url('bundlix/v1'),
            'currentRoute' => BundliX_Router::get_current_route(),
            'routeParam' => BundliX_Router::get_route_param(),
            'user' => is_user_logged_in() ? array(
                'id' => get_current_user_id(),
                'loggedIn' => true
            ) : array(
                'loggedIn' => false
            ),
            'i18n' => array(
                'loading' => __('Loading...', 'bundlix'),
                'error' => __('An error occurred', 'bundlix'),
                'success' => __('Success', 'bundlix')
            )
        ));
        
        // Enqueue scripts
        wp_enqueue_script('bundlix-core');
        wp_enqueue_script('bundlix-router');
        wp_enqueue_script('bundlix-auth'); // Always load auth for login pages
        wp_enqueue_script('bundlix-dashboard');
    }
    
    /**
     * Register admin assets
     */
    public static function register_admin_assets($hook) {
        // Only load on BundliX admin pages
        if (strpos($hook, 'bundlix') === false) {
            return;
        }
        
        // Register admin CSS
        wp_register_style(
            'bundlix-admin',
            BUNDLIX_PLUGIN_URL . 'assets/css/bundlix-admin.css',
            array('wp-color-picker'),
            self::get_asset_version(),
            'all'
        );
        
        // Register admin JS
        wp_register_script(
            'bundlix-admin',
            BUNDLIX_PLUGIN_URL . 'assets/js/bundlix-admin.js',
            array('jquery', 'wp-color-picker'),
            self::get_asset_version(),
            true
        );
        
        wp_enqueue_style('bundlix-admin');
        wp_enqueue_script('bundlix-admin');
    }
    
    /**
     * Deregister theme assets on BundliX routes
     */
    private static function deregister_theme_assets() {
        global $wp_styles, $wp_scripts;
        
        // Get current theme
        $theme = wp_get_theme();
        $theme_slug = $theme->get_template();
        
        // Remove theme stylesheets (except essential ones)
        if ($wp_styles) {
            foreach ($wp_styles->queue as $handle) {
                if (strpos($handle, $theme_slug) !== false && $handle !== 'dashicons') {
                    wp_dequeue_style($handle);
                    wp_deregister_style($handle);
                }
            }
        }
        
        // Remove theme scripts (except essential ones)
        if ($wp_scripts) {
            foreach ($wp_scripts->queue as $handle) {
                if (strpos($handle, $theme_slug) !== false) {
                    wp_dequeue_script($handle);
                    wp_deregister_script($handle);
                }
            }
        }
    }
    
    /**
     * Check if current request is a BundliX route
     */
    private static function is_bundlix_route() {
        $route = BundliX_Router::get_current_route();
        return !empty($route);
    }
    
    /**
     * Get asset version for cache busting
     */
    private static function get_asset_version() {
        // Use plugin version in production, timestamp in development
        if (defined('WP_DEBUG') && WP_DEBUG) {
            return time();
        }
        return BUNDLIX_VERSION;
    }
    
    /**
     * Minify and combine assets (for production)
     * This is a placeholder - implement build process separately
     */
    public static function optimize_assets() {
        // Implementation for production build
        // Consider using Gulp, Webpack, or similar build tools
    }
}
