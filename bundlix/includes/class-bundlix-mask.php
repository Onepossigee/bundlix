<?php
/**
 * BundliX Mask - WordPress Masking Layer
 * 
 * Completely hides WordPress footprint from the frontend.
 * Removes all WP identifiers, version info, and standard theme elements.
 */

if (!defined('ABSPATH')) {
    exit;
}

class BundliX_Mask {
    
    /**
     * Initialize masking
     */
    public static function init() {
        // Remove WordPress version and generators
        add_action('init', array(__CLASS__, 'remove_wp_version'));
        
        // Remove WP metadata from head
        add_action('wp_head', array(__CLASS__, 'remove_wp_metadata'), 1);
        
        // Remove emoji scripts
        add_action('init', array(__CLASS__, 'remove_wp_emoji'));
        
        // Remove oEmbed discovery
        add_action('wp_head', array(__CLASS__, 'remove_oembed_discovery'), 1);
        
        // Remove REST API links
        add_action('wp_head', array(__CLASS__, 'remove_rest_api_links'), 1);
        
        // Remove wlwmanifest link
        add_action('wp_head', array(__CLASS__, 'remove_wlwmanifest'), 1);
        
        // Remove shortlink
        add_action('wp_head', array(__CLASS__, 'remove_shortlink'), 1);
        
        // Remove generator meta tags
        add_filter('the_generator', '__return_false');
        
        // Remove WP version from styles and scripts
        add_filter('style_loader_src', array(__CLASS__, 'remove_wp_version_from_assets'), 10, 2);
        add_filter('script_loader_src', array(__CLASS__, 'remove_wp_version_from_assets'), 10, 2);
        
        // Customize login error messages (security)
        add_filter('login_errors', array(__CLASS__, 'customize_login_errors'));
        
        // Block access to wp-login.php from frontend
        add_action('template_redirect', array(__CLASS__, 'block_wp_login_access'));
        
        // Redirect /wp-admin for non-admins
        add_action('admin_init', array(__CLASS__, 'restrict_admin_access'));
        
        // Custom admin logo
        add_action('login_enqueue_scripts', array(__CLASS__, 'custom_login_logo'));
        
        // Change login URL redirect
        add_filter('site_url', array(__CLASS__, 'custom_login_url'), 10, 4);
    }
    
    /**
     * Remove WordPress version number
     */
    public static function remove_wp_version() {
        global $wp_version;
        remove_action('wp_head', 'wp_generator');
    }
    
    /**
     * Remove WordPress metadata from head
     */
    public static function remove_wp_metadata() {
        remove_action('wp_head', 'wp_generator');
        remove_action('wp_head', 'wp_shortlink_wp_head', 10, 0);
        remove_action('wp_head', 'adjacent_posts_rel_link_wp_head', 10, 0);
        remove_action('wp_head', 'rsd_link');
        remove_action('wp_head', 'wlwmanifest_link');
        remove_action('wp_head', 'rest_output_link_wp_head');
        remove_action('wp_head', 'wp_oembed_add_discovery_links');
    }
    
    /**
     * Remove WordPress emoji scripts
     */
    public static function remove_wp_emoji() {
        remove_action('wp_head', 'print_emoji_detection_script', 7);
        remove_action('admin_print_scripts', 'print_emoji_detection_script');
        remove_action('wp_print_styles', 'print_emoji_styles');
        remove_action('admin_print_styles', 'print_emoji_styles');
        remove_filter('the_content_feed', 'wp_staticize_emoji');
        remove_filter('comment_text_rss', 'wp_staticize_emoji');
        remove_filter('wp_mail', 'wp_staticize_emoji_for_email');
    }
    
    /**
     * Remove oEmbed discovery links
     */
    public static function remove_oembed_discovery() {
        remove_action('wp_head', 'wp_oembed_add_discovery_links');
        remove_action('wp_head', 'wp_oembed_add_host_js');
    }
    
    /**
     * Remove REST API links
     */
    public static function remove_rest_api_links() {
        remove_action('wp_head', 'rest_output_link_wp_head', 10);
        remove_action('xmlrpc_rsd_apis', 'rest_output_rsd');
    }
    
    /**
     * Remove wlwmanifest link
     */
    public static function remove_wlwmanifest() {
        remove_action('wp_head', 'wlwmanifest_link');
    }
    
    /**
     * Remove shortlink
     */
    public static function remove_shortlink() {
        remove_action('wp_head', 'wp_shortlink_wp_head', 10, 0);
        remove_action('template_redirect', 'wp_shortlink_header', 11, 0);
    }
    
    /**
     * Remove WP version from asset URLs
     */
    public static function remove_wp_version_from_assets($src, $handle) {
        if (strpos($src, 'ver=' . get_bloginfo('version'))) {
            $src = remove_query_arg('ver', $src);
        }
        return $src;
    }
    
    /**
     * Customize login error messages for security
     */
    public static function customize_login_errors($errors) {
        return __('An error occurred. Please check your credentials.', 'bundlix');
    }
    
    /**
     * Block direct access to wp-login.php
     */
    public static function block_wp_login_access() {
        if (strpos($_SERVER['REQUEST_URI'], 'wp-login.php') !== false && !is_user_logged_in()) {
            // Redirect to custom login or show 404
            wp_redirect(home_url('/app/login'));
            exit;
        }
    }
    
    /**
     * Restrict admin access for non-admin users
     */
    public static function restrict_admin_access() {
        if (current_user_can('read') && !current_user_can('manage_options')) {
            wp_redirect(home_url('/app/dashboard'));
            exit;
        }
    }
    
    /**
     * Custom login page logo
     */
    public static function custom_login_logo() {
        ?>
        <style type="text/css">
            .login h1 a {
                background-image: url(<?php echo BUNDLIX_PLUGIN_URL; ?>assets/images/logo.png);
                background-size: contain;
                background-repeat: no-repeat;
                width: 320px;
                height: 80px;
            }
            body.login {
                background: #f5f5f5;
            }
        </style>
        <?php
    }
    
    /**
     * Custom login URL handling
     */
    public static function custom_login_url($url, $path, $scheme, $blog_id) {
        if (strpos($url, 'wp-login.php') !== false) {
            return home_url('/app/login');
        }
        return $url;
    }
    
    /**
     * Load blank canvas template
     */
    public static function load_blank_canvas() {
        include BUNDLIX_PLUGIN_DIR . 'templates/blank-canvas.php';
        exit;
    }
}
