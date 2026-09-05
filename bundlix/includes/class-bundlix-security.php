<?php
/**
 * BundliX Security - Security Hardening Layer
 * 
 * Implements security measures to protect the platform.
 * Blocks XML-RPC, restricts REST API, and adds additional security headers.
 */

if (!defined('ABSPATH')) {
    exit;
}

class BundliX_Security {
    
    /**
     * Initialize security hardening
     */
    public static function init() {
        // Block XML-RPC
        add_filter('xmlrpc_enabled', '__return_false');
        add_filter('xmlrpc_methods', array(__CLASS__, 'disable_xmlrpc_methods'));
        
        // Restrict REST API access
        add_filter('rest_endpoints', array(__CLASS__, 'restrict_rest_api'), 100);
        
        // Add security headers
        add_action('send_headers', array(__CLASS__, 'add_security_headers'));
        
        // Prevent file editing via admin
        if (!defined('DISALLOW_FILE_EDIT')) {
            define('DISALLOW_FILE_EDIT', true);
        }
        
        // Limit login attempts (basic implementation)
        add_action('wp_login_failed', array(__CLASS__, 'log_failed_login'));
        add_filter('authenticate', array(__CLASS__, 'check_failed_login_limit'), 30, 3);
        
        // Sanitize input data
        add_action('init', array(__CLASS__, 'sanitize_request_data'));
        
        // Block suspicious user agents
        add_action('init', array(__CLASS__, 'block_suspicious_agents'));
        
        // Prevent direct file access
        add_action('init', array(__CLASS__, 'prevent_direct_file_access'));
    }
    
    /**
     * Disable XML-RPC methods
     */
    public static function disable_xmlrpc_methods($methods) {
        return array();
    }
    
    /**
     * Restrict REST API access to authenticated users only
     */
    public static function restrict_rest_api($endpoints) {
        // Only allow public endpoints for non-authenticated users
        if (!is_user_logged_in()) {
            foreach ($endpoints as $route => $endpoint) {
                // Allow only specific public routes
                $public_routes = array(
                    '/bundlix/v1/public/',
                    '/bundlix/v1/auth/login',
                    '/bundlix/v1/auth/register'
                );
                
                $is_public = false;
                foreach ($public_routes as $public_route) {
                    if (strpos($route, $public_route) !== false) {
                        $is_public = true;
                        break;
                    }
                }
                
                if (!$is_public) {
                    unset($endpoints[$route]);
                }
            }
        }
        
        return $endpoints;
    }
    
    /**
     * Add security headers
     */
    public static function add_security_headers() {
        // Prevent clickjacking
        header('X-Frame-Options: SAMEORIGIN');
        
        // XSS Protection
        header('X-XSS-Protection: 1; mode=block');
        
        // Prevent MIME type sniffing
        header('X-Content-Type-Options: nosniff');
        
        // Referrer Policy
        header('Referrer-Policy: strict-origin-when-cross-origin');
        
        // Content Security Policy (adjust as needed)
        header("Content-Security-Policy: default-src 'self'; script-src 'self' 'unsafe-inline' 'unsafe-eval'; style-src 'self' 'unsafe-inline'; img-src 'self' data: https:; font-src 'self' data:; connect-src 'self' https:;");
        
        // Remove server signature
        remove_action('wp_head', 'wp_generator');
    }
    
    /**
     * Log failed login attempts
     */
    public static function log_failed_login($username) {
        $ip = self::get_client_ip();
        $transient_key = 'bundlix_failed_login_' . md5($ip);
        
        $failed_attempts = get_transient($transient_key);
        
        if ($failed_attempts === false) {
            set_transient($transient_key, 1, 3600); // 1 hour lockout window
        } else {
            set_transient($transient_key, intval($failed_attempts) + 1, 3600);
        }
        
        // Log for admin review (implement proper logging later)
        error_log(sprintf(
            '[BundliX] Failed login attempt for username: %s from IP: %s',
            $username,
            $ip
        ));
    }
    
    /**
     * Check failed login limit and block if exceeded
     */
    public static function check_failed_login_limit($user, $username, $password) {
        $ip = self::get_client_ip();
        $transient_key = 'bundlix_failed_login_' . md5($ip);
        
        $failed_attempts = get_transient($transient_key);
        
        if ($failed_attempts !== false && intval($failed_attempts) >= 5) {
            // Block for 1 hour after 5 failed attempts
            return new WP_Error(
                'too_many_attempts',
                __('Too many failed login attempts. Please try again in 1 hour.', 'bundlix')
            );
        }
        
        return $user;
    }
    
    /**
     * Sanitize request data
     */
    public static function sanitize_request_data() {
        // Basic sanitization of GET/POST data
        if (!empty($_GET)) {
            $_GET = array_map('sanitize_text_field', $_GET);
        }
        
        if (!empty($_POST)) {
            $_POST = array_map('sanitize_text_field', $_POST);
        }
    }
    
    /**
     * Block suspicious user agents
     */
    public static function block_suspicious_agents() {
        $suspicious_agents = array(
            'sqlmap',
            'nikto',
            'nmap',
            'masscan',
            'dirbuster',
            'gobuster'
        );
        
        $user_agent = isset($_SERVER['HTTP_USER_AGENT']) ? strtolower($_SERVER['HTTP_USER_AGENT']) : '';
        
        foreach ($suspicious_agents as $agent) {
            if (strpos($user_agent, $agent) !== false) {
                wp_die(
                    __('Access denied.', 'bundlix'),
                    __('Forbidden', 'bundlix'),
                    array('response' => 403)
                );
            }
        }
    }
    
    /**
     * Prevent direct file access to sensitive files
     */
    public static function prevent_direct_file_access() {
        // Block direct access to config files
        if (isset($_SERVER['PHP_SELF']) && 
            strpos($_SERVER['PHP_SELF'], '/wp-config.php') !== false) {
            wp_die(__('Access denied.', 'bundlix'), __('Forbidden', 'bundlix'), array('response' => 403));
        }
    }
    
    /**
     * Get client IP address
     */
    private static function get_client_ip() {
        $ip = '';
        
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
        }
        
        return filter_var($ip, FILTER_VALIDATE_IP) ? $ip : '0.0.0.0';
    }
    
    /**
     * Generate secure nonce
     */
    public static function generate_nonce($action = -1) {
        return wp_create_nonce($action);
    }
    
    /**
     * Verify nonce
     */
    public static function verify_nonce($nonce, $action = -1) {
        return wp_verify_nonce($nonce, $action);
    }
}
