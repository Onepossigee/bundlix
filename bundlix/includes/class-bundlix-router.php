<?php
/**
 * BundliX Router - Handles custom URL endpoints and SPA routing
 * 
 * Manages virtual endpoints like /app/dashboard, /app/login, etc.
 * without creating actual WordPress pages.
 */

if (!defined('ABSPATH')) {
    exit;
}

class BundliX_Router {
    
    /**
     * List of valid app routes
     */
    private static $routes = array(
        'login',
        'register',
        'dashboard',
        'wallet',
        'buy-data',
        'buy-airtime',
        'buy-pins',
        'transactions',
        'profile',
        'referrals',
        'storefront',
        'support',
        'forgot-password',
        'reset-password',
        'verify-email',
        'kyc-verification'
    );
    
    /**
     * Initialize router
     */
    public static function init() {
        add_action('init', array(__CLASS__, 'register_endpoints'));
        add_action('template_redirect', array(__CLASS__, 'handle_app_requests'));
        add_filter('request', array(__CLASS__, 'modify_request_query'));
    }
    
    /**
     * Register custom rewrite rules for app endpoints
     */
    public static function register_endpoints() {
        // Add query var for BundliX routes
        add_rewrite_tag('%bundlix_route%', '([^/]+)');
        
        // Create rewrite rules for each route
        foreach (self::$routes as $route) {
            add_rewrite_rule(
                '^app/' . $route . '/?$',
                'index.php?bundlix_route=' . $route,
                'top'
            );
            
            // Also allow routes with parameters (e.g., /app/storefront/username)
            add_rewrite_rule(
                '^app/' . $route . '/([^/]+)/?$',
                'index.php?bundlix_route=' . $route . '&bundlix_param=$matches[1]',
                'top'
            );
        }
        
        // Default app route
        add_rewrite_rule(
            '^app/?$',
            'index.php?bundlix_route=dashboard',
            'top'
        );
    }
    
    /**
     * Modify the query to handle BundliX routes
     */
    public static function modify_request_query($query_vars) {
        if (isset($query_vars['bundlix_route'])) {
            // Prevent WordPress from looking for actual pages
            $query_vars['pagename'] = '';
        }
        return $query_vars;
    }
    
    /**
     * Handle app route requests and load custom template
     */
    public static function handle_app_requests() {
        $route = get_query_var('bundlix_route');
        
        if (empty($route)) {
            return;
        }
        
        // Validate route
        if (!in_array($route, self::$routes)) {
            // Invalid route - show 404
            global $wp_query;
            $wp_query->set_404();
            status_header(404);
            include(get_query_template('404'));
            exit;
        }
        
        // Load blank canvas template
        locate_template('bundlix-blank-canvas.php', true, true) 
            or locate_template('blank-canvas.php', true, true)
            or include BUNDLIX_PLUGIN_DIR . 'templates/blank-canvas.php';
        
        exit;
    }
    
    /**
     * Get current route
     */
    public static function get_current_route() {
        return get_query_var('bundlix_route');
    }
    
    /**
     * Get route parameter
     */
    public static function get_route_param() {
        return get_query_var('bundlix_param', '');
    }
    
    /**
     * Generate app URL
     */
    public static function app_url($route = '', $param = '') {
        $base_url = home_url('/app');
        
        if (empty($route)) {
            return $base_url;
        }
        
        $url = trailingslashit($base_url . '/' . $route);
        
        if (!empty($param)) {
            $url = trailingslashit($url . $param);
        }
        
        return $url;
    }
    
    /**
     * Flush rewrite rules (use on activation)
     */
    public static function flush_rules() {
        self::register_endpoints();
        flush_rewrite_rules(true);
    }
}
