<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="theme-color" content="#4F46E5">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="BundliX">
    
    <title><?php echo esc_html(get_bloginfo('name')); ?> - Digital Services Platform</title>
    
    <?php wp_head(); ?>
    
    <!-- Preconnect to improve performance -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    
    <!-- PWA Manifest (to be added later) -->
    <link rel="manifest" href="<?php echo BUNDLIX_PLUGIN_URL; ?>assets/manifest.json">
    
    <!-- Favicon -->
    <link rel="icon" href="<?php echo BUNDLIX_PLUGIN_URL; ?>assets/images/favicon.ico" sizes="any">
    <link rel="apple-touch-icon" href="<?php echo BUNDLIX_PLUGIN_URL; ?>assets/images/apple-touch-icon.png">
</head>
<body <?php body_class('bundlix-app bundlix-blank-canvas'); ?>>
    <div id="bundlix-app" class="bundlix-app-container">
        <!-- Loading Screen -->
        <div id="bundlix-loading" class="bundlix-loading-screen">
            <div class="bundlix-loading-spinner">
                <div class="spinner"></div>
                <p class="loading-text"><?php esc_html_e('Loading BundliX...', 'bundlix'); ?></p>
            </div>
        </div>
        
        <!-- App Content (rendered by JavaScript) -->
        <div id="bundlix-content" class="bundlix-content" style="display:none;">
            <!-- Dynamic content will be injected here -->
        </div>
        
        <!-- Navigation (for authenticated users) -->
        <nav id="bundlix-nav" class="bundlix-navigation" style="display:none;">
            <!-- Navigation will be rendered by JS -->
        </nav>
    </div>
    
    <script>
        // Hide loading screen when app is ready
        window.bundlixReady = function() {
            document.getElementById('bundlix-loading').style.display = 'none';
            document.getElementById('bundlix-content').style.display = 'block';
        };
        
        // Show navigation for logged-in users
        if (bundlixConfig && bundlixConfig.user && bundlixConfig.user.loggedIn) {
            document.addEventListener('DOMContentLoaded', function() {
                // Navigation will be initialized by bundlix-dashboard.js
            });
        }
    </script>
    
    <?php wp_footer(); ?>
</body>
</html>
