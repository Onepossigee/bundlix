<?php
/**
 * BundliX Authentication Handler
 * Handles custom routes, template loading, and redirection logic for Auth.
 *
 * @package BundliX
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class BundliX_Auth {

    /**
     * List of custom auth endpoints
     */
    private $auth_endpoints = array(
        'login',
        'register',
        'forgot-password',
        'reset-password',
        'logout'
    );

    /**
     * Constructor
     */
    public function __construct() {
        // Register Rewrite Rules
        add_action('init', array($this, 'add_rewrite_rules'));
        
        // Flush rewrite rules on plugin activation (handled externally)
        
        // Intercept Template Loading
        add_filter('template_include', array($this, 'load_auth_templates'));
        
        // Handle Redirections
        add_action('template_redirect', array($this, 'handle_auth_redirects'));
    }

    /**
     * Add custom rewrite rules for auth endpoints
     */
    public function add_rewrite_rules() {
        foreach ($this->auth_endpoints as $endpoint) {
            add_rewrite_rule(
                '^app/' . $endpoint . '/?$',
                'index.php?bundlix_auth=' . $endpoint,
                'top'
            );
        }
        
        // Rule for reset password with token
        add_rewrite_rule(
            '^app/reset-password/([^/]+)/?$',
            'index.php?bundlix_auth=reset-password&bundlix_token=$matches[1]',
            'top'
        );
    }

    /**
     * Register query vars for our endpoints
     */
    public function add_query_vars($vars) {
        $vars[] = 'bundlix_auth';
        $vars[] = 'bundlix_token';
        return $vars;
    }
    
    /**
     * Initialize query vars (hooked in main plugin file usually, but safe here too)
     */
    public function init_query_vars() {
        add_filter('query_vars', array($this, 'add_query_vars'));
    }

    /**
     * Load custom templates for auth endpoints
     * 
     * @param string $template The path to the template file.
     * @return string The path to the custom template or original template.
     */
    public function load_auth_templates($template) {
        $auth_endpoint = get_query_var('bundlix_auth');

        if (!$auth_endpoint) {
            return $template;
        }

        // Map endpoint to template file
        $template_name = '';
        switch ($auth_endpoint) {
            case 'login':
                $template_name = 'auth/login.php';
                break;
            case 'register':
                $template_name = 'auth/register.php';
                break;
            case 'forgot-password':
                $template_name = 'auth/forgot-password.php';
                break;
            case 'reset-password':
                $template_name = 'auth/reset-password.php';
                break;
            case 'logout':
                // Handle logout immediately before loading template
                $this->process_logout();
                return $template; // Fallback
        }

        if ($template_name) {
            $custom_template = BUNDLIX_PLUGIN_DIR . 'templates/' . $template_name;
            
            if (file_exists($custom_template)) {
                // Use our blank canvas logic if not already applied, 
                // but for auth pages we often want a specific centered layout.
                // For now, we load the specific auth template.
                return $custom_template;
            } else {
                // Fallback for development if template missing
                error_log('BundliX: Auth template not found: ' . $template_name);
            }
        }

        return $template;
    }

    /**
     * Handle redirects based on login status
     */
    public function handle_auth_redirects() {
        $auth_endpoint = get_query_var('bundlix_auth');

        if (!$auth_endpoint) {
            return;
        }

        $is_logged_in = is_user_logged_in();

        // 1. If logged in, prevent access to login/register/forgot-password
        if ($is_logged_in && in_array($auth_endpoint, array('login', 'register', 'forgot-password'))) {
            wp_redirect(home_url('/app/dashboard'));
            exit;
        }

        // 2. If NOT logged in, prevent access to protected areas (handled in other classes usually, 
        // but good to ensure reset-password requires token validity later)
        
        // 3. Specific logic for reset-password: check token existence
        if ($auth_endpoint === 'reset-password') {
            $token = get_query_var('bundlix_token');
            if (empty($token)) {
                // No token provided, redirect to forgot password or login
                wp_redirect(home_url('/app/forgot-password'));
                exit;
            }
            // Token validation happens in the form processing logic (Phase 2.4)
        }
    }

    /**
     * Process Logout
     */
    private function process_logout() {
        // Verify nonce if passed via GET (optional security layer)
        // For now, standard WP logout with redirect
        
        wp_logout();
        
        // Redirect to login page with a logged-out message
        wp_redirect(home_url('/app/login?loggedout=true'));
        exit;
    }
    
    /**
     * Flush rewrite rules safely. 
     * Call this once on plugin activation.
     */
    public static function flush_rules() {
        $instance = new self();
        $instance->init_query_vars();
        $instance->add_rewrite_rules();
        flush_rewrite_rules(false);
    }
}

// Initialize Query Vars early
add_action('init', function() {
    $auth = new BundliX_Auth();
    $auth->init_query_vars();
});
