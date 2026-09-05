/**
 * BundliX Core JavaScript
 * 
 * Core functionality for the BundliX application.
 * Handles AJAX requests, utilities, and global functions.
 */

(function($) {
    'use strict';
    
    // BundliX Core Object
    window.BundliX = window.BundliX || {
        config: bundlixConfig || {},
        
        /**
         * Initialize core functionality
         */
        init: function() {
            this.setupAJAX();
            this.setupEventListeners();
            this.log('BundliX Core initialized');
        },
        
        /**
         * Setup AJAX defaults
         */
        setupAJAX: function() {
            $.ajaxSetup({
                headers: {
                    'X-WP-Nonce': this.config.nonce
                }
            });
        },
        
        /**
         * Setup global event listeners
         */
        setupEventListeners: function() {
            // Handle all internal links with SPA navigation
            $(document).on('click', 'a[href^="' + this.config.appUrl + '"]', function(e) {
                if (!e.ctrlKey && !e.metaKey && !e.shiftKey) {
                    e.preventDefault();
                    var url = $(this).attr('href');
                    BundliX.Router.navigate(url);
                }
            });
            
            // Handle form submissions
            $(document).on('submit', '.bundlix-form', function(e) {
                e.preventDefault();
                BundliX.Forms.handleSubmit($(this));
            });
        },
        
        /**
         * Make AJAX request
         */
        request: function(options) {
            var defaults = {
                method: 'GET',
                dataType: 'json',
                beforeSend: function(xhr) {
                    xhr.setRequestHeader('X-WP-Nonce', BundliX.config.nonce);
                }
            };
            
            var settings = $.extend({}, defaults, options);
            
            return $.ajax(settings);
        },
        
        /**
         * Show loading indicator
         */
        showLoading: function(container) {
            container = container || $('#bundlix-content');
            container.addClass('loading');
        },
        
        /**
         * Hide loading indicator
         */
        hideLoading: function(container) {
            container = container || $('#bundlix-content');
            container.removeClass('loading');
        },
        
        /**
         * Show notification
         */
        notify: function(message, type) {
            type = type || 'info';
            
            var notification = $('<div class="bundlix-notification notification-' + type + '">' + 
                '<span>' + message + '</span>' +
                '<button class="notification-close">&times;</button>' +
                '</div>');
            
            $('body').append(notification);
            
            setTimeout(function() {
                notification.fadeOut(300, function() {
                    $(this).remove();
                });
            }, 5000);
            
            notification.find('.notification-close').on('click', function() {
                notification.fadeOut(300, function() {
                    $(this).remove();
                });
            });
        },
        
        /**
         * Format currency (GHS)
         */
        formatCurrency: function(amount) {
            return 'GH₵ ' + parseFloat(amount).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
        },
        
        /**
         * Format phone number
         */
        formatPhoneNumber: function(phone) {
            // Remove non-numeric characters
            phone = phone.replace(/\D/g, '');
            
            // Ghana phone number formatting
            if (phone.length === 10) {
                return phone.replace(/(\d{3})(\d{3})(\d{4})/, '$1 $2 $3');
            } else if (phone.length === 9) {
                return phone.replace(/(\d{3})(\d{3})(\d{3})/, '$1 $2 $3');
            }
            
            return phone;
        },
        
        /**
         * Validate Ghana phone number
         */
        validatePhone: function(phone) {
            var pattern = /^(?:\+233|0)(?:20|23|24|26|27|28|50|53|54|55|56|57|59)\d{7}$/;
            return pattern.test(phone.replace(/\s+/g, ''));
        },
        
        /**
         * Get current route
         */
        getCurrentRoute: function() {
            return this.config.currentRoute || '';
        },
        
        /**
         * Check if user is logged in
         */
        isLoggedIn: function() {
            return this.config.user && this.config.user.loggedIn;
        },
        
        /**
         * Get current user ID
         */
        getCurrentUserId: function() {
            return this.isLoggedIn() ? this.config.user.id : null;
        },
        
        /**
         * Log to console (only in debug mode)
         */
        log: function(message) {
            if (this.config.debug) {
                console.log('[BundliX]', message);
            }
        },
        
        /**
         * Handle errors
         */
        handleError: function(error) {
            this.log('Error: ' + error);
            this.notify(error.message || 'An error occurred', 'error');
        }
    };
    
    // Initialize on document ready
    $(document).ready(function() {
        BundliX.init();
    });
    
})(jQuery);
