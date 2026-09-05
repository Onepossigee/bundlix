<?php
/**
 * BundliX Blank Canvas Template
 * Loads specific route templates without theme elements
 */

if (!defined('ABSPATH')) {
    exit;
}

// Get current route
$current_route = BundliX_Router::get_current_route();
$route_param = BundliX_Router::get_route_param();

// Map routes to template files
$template_map = array(
    'login' => 'auth/login.php',
    'register' => 'auth/register.php',
    'forgot-password' => 'auth/forgot-password.php',
    'reset-password' => 'auth/reset-password.php',
    'dashboard' => 'dashboard/index.php',
    'wallet' => 'wallet/index.php',
    'profile' => 'profile/index.php',
    // Add more as we build them
);

// Determine template file
$template_file = isset($template_map[$current_route]) 
    ? $template_map[$current_route] 
    : 'dashboard/index.php'; // Default to dashboard

// Build full path
$template_path = BUNDLIX_PLUGIN_DIR . 'templates/' . $template_file;

// Check if template exists, otherwise show error
if (!file_exists($template_path)) {
    // Template not found - show simple error
    ?>
    <!DOCTYPE html>
    <html <?php language_attributes(); ?>>
    <head>
        <meta charset="<?php bloginfo('charset'); ?>">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Page Not Found - BundliX</title>
        <?php wp_head(); ?>
    </head>
    <body>
        <div style="text-align:center;padding:50px;font-family:sans-serif;">
            <h1>Page Under Construction</h1>
            <p>The page you're looking for is being built.</p>
            <a href="<?php echo home_url('/app/dashboard'); ?>">Go to Dashboard</a>
        </div>
        <?php wp_footer(); ?>
    </body>
    </html>
    <?php
    exit;
}
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="theme-color" content="#4F46E5">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="BundliX">
    
    <title><?php echo esc_html(get_bloginfo('name')); ?> - <?php echo esc_html(ucfirst(str_replace('-', ' ', $current_route))); ?></title>
    
    <?php wp_head(); ?>
    
    <!-- Preconnect to improve performance -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    
    <!-- PWA Manifest -->
    <link rel="manifest" href="<?php echo BUNDLIX_PLUGIN_URL; ?>assets/manifest.json">
    
    <!-- Favicon -->
    <link rel="icon" href="<?php echo BUNDLIX_PLUGIN_URL; ?>assets/images/favicon.ico" sizes="any">
    <link rel="apple-touch-icon" href="<?php echo BUNDLIX_PLUGIN_URL; ?>assets/images/apple-touch-icon.png">
</head>
<body <?php body_class('bundlix-app bundlix-blank-canvas bundlix-route-' . sanitize_html_class($current_route)); ?>>
    <div id="bundlix-app" class="bundlix-app-container">
        <!-- Content loaded from PHP template -->
        <div id="bundlix-content" class="bundlix-content">
            <?php include $template_path; ?>
        </div>
    </div>
    
    <?php wp_footer(); ?>
</body>
</html>
