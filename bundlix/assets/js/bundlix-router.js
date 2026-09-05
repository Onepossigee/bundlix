/**
 * BundliX Router - SPA Navigation
 * 
 * Handles client-side routing for seamless navigation
 * without page reloads.
 */

(function($) {
    'use strict';
    
    BundliX.Router = {
        history: [],
        currentIndex: -1,
        
        /**
         * Initialize router
         */
        init: function() {
            this.handlePopState();
            BundliX.log('Router initialized');
        },
        
        /**
         * Navigate to a URL
         */
        navigate: function(url) {
            if (!url || url === window.location.href) {
                return;
            }
            
            // Extract route from URL
            var route = this.extractRoute(url);
            
            // Update browser history
            history.pushState({ route: route, url: url }, '', url);
            
            // Load content
            this.loadRoute(route, url);
        },
        
        /**
         * Extract route from URL
         */
        extractRoute: function(url) {
            var appUrl = BundliX.config.appUrl + '/';
            var path = url.replace(appUrl, '').split('/')[0];
            return path || 'dashboard';
        },
        
        /**
         * Load route content
         */
        loadRoute: function(route, url) {
            BundliX.showLoading();
            
            // Fetch route content via AJAX
            BundliX.request({
                url: url,
                method: 'GET',
                success: function(response) {
                    BundliX.hideLoading();
                    
                    // Update content
                    $('#bundlix-content').html(response);
                    
                    // Update current route in config
                    BundliX.config.currentRoute = route;
                    
                    // Trigger route change event
                    $(document).trigger('bundlix-route-change', [route]);
                    
                    // Scroll to top
                    window.scrollTo(0, 0);
                    
                    BundliX.log('Route loaded: ' + route);
                },
                error: function(xhr, status, error) {
                    BundliX.hideLoading();
                    BundliX.handleError({ message: 'Failed to load page' });
                }
            });
        },
        
        /**
         * Handle browser back/forward buttons
         */
        handlePopState: function() {
            var self = this;
            
            $(window).on('popstate', function(e) {
                if (e.originalEvent.state) {
                    var state = e.originalEvent.state;
                    self.loadRoute(state.route, state.url);
                } else {
                    // Default to dashboard
                    self.navigate(BundliX.config.appUrl + '/dashboard');
                }
            });
        },
        
        /**
         * Go back in history
         */
        back: function() {
            history.back();
        },
        
        /**
         * Go forward in history
         */
        forward: function() {
            history.forward();
        },
        
        /**
         * Replace current history state
         */
        replace: function(url) {
            var route = this.extractRoute(url);
            history.replaceState({ route: route, url: url }, '', url);
            this.loadRoute(route, url);
        },
        
        /**
         * Get current route
         */
        getCurrentRoute: function() {
            return BundliX.config.currentRoute || 'dashboard';
        },
        
        /**
         * Check if route is active
         */
        isActive: function(route) {
            return this.getCurrentRoute() === route;
        }
    };
    
    // Initialize router when core is ready
    $(document).on('bundlix-core-ready', function() {
        BundliX.Router.init();
    });
    
})(jQuery);
