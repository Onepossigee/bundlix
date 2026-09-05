/**
 * BundliX Dashboard JavaScript
 * 
 * Dashboard-specific functionality and UI components.
 */

(function($) {
    'use strict';
    
    BundliX.Dashboard = {
        /**
         * Initialize dashboard
         */
        init: function() {
            this.setupNavigation();
            this.loadDashboardData();
            BundliX.log('Dashboard initialized');
            
            // Signal that app is ready
            if (typeof window.bundlixReady === 'function') {
                window.bundlixReady();
            }
        },
        
        /**
         * Setup navigation menu
         */
        setupNavigation: function() {
            var navHtml = this.getNavigationHTML();
            $('#bundlix-nav').html(navHtml).show();
            
            // Highlight active menu item
            this.highlightActiveMenu();
        },
        
        /**
         * Get navigation HTML
         */
        getNavigationHTML: function() {
            var currentRoute = BundliX.Router.getCurrentRoute();
            
            return `
                <div class="bundlix-nav-container">
                    <div class="bundlix-nav-brand">
                        <a href="${BundliX.config.appUrl}/dashboard">
                            <span class="brand-logo">BundliX</span>
                        </a>
                    </div>
                    
                    <ul class="bundlix-nav-menu">
                        <li class="nav-item ${currentRoute === 'dashboard' ? 'active' : ''}">
                            <a href="${BundliX.config.appUrl}/dashboard">
                                <span class="nav-icon">📊</span>
                                <span class="nav-text">Dashboard</span>
                            </a>
                        </li>
                        <li class="nav-item ${currentRoute === 'buy-data' ? 'active' : ''}">
                            <a href="${BundliX.config.appUrl}/buy-data">
                                <span class="nav-icon">📶</span>
                                <span class="nav-text">Buy Data</span>
                            </a>
                        </li>
                        <li class="nav-item ${currentRoute === 'buy-airtime' ? 'active' : ''}">
                            <a href="${BundliX.config.appUrl}/buy-airtime">
                                <span class="nav-icon">📞</span>
                                <span class="nav-text">Buy Airtime</span>
                            </a>
                        </li>
                        <li class="nav-item ${currentRoute === 'buy-pins' ? 'active' : ''}">
                            <a href="${BundliX.config.appUrl}/buy-pins">
                                <span class="nav-icon">🎫</span>
                                <span class="nav-text">Buy Pins</span>
                            </a>
                        </li>
                        <li class="nav-item ${currentRoute === 'wallet' ? 'active' : ''}">
                            <a href="${BundliX.config.appUrl}/wallet">
                                <span class="nav-icon">💳</span>
                                <span class="nav-text">Wallet</span>
                            </a>
                        </li>
                        <li class="nav-item ${currentRoute === 'transactions' ? 'active' : ''}">
                            <a href="${BundliX.config.appUrl}/transactions">
                                <span class="nav-icon">📜</span>
                                <span class="nav-text">History</span>
                            </a>
                        </li>
                        <li class="nav-item ${currentRoute === 'referrals' ? 'active' : ''}">
                            <a href="${BundliX.config.appUrl}/referrals">
                                <span class="nav-icon">👥</span>
                                <span class="nav-text">Referrals</span>
                            </a>
                        </li>
                        <li class="nav-item ${currentRoute === 'profile' ? 'active' : ''}">
                            <a href="${BundliX.config.appUrl}/profile">
                                <span class="nav-icon">⚙️</span>
                                <span class="nav-text">Profile</span>
                            </a>
                        </li>
                        <li class="nav-item ${currentRoute === 'support' ? 'active' : ''}">
                            <a href="${BundliX.config.appUrl}/support">
                                <span class="nav-icon">💬</span>
                                <span class="nav-text">Support</span>
                            </a>
                        </li>
                    </ul>
                    
                    <div class="bundlix-nav-footer">
                        <div class="user-balance">
                            <span class="balance-label">Balance:</span>
                            <span class="balance-amount" id="user-balance">GH₵ 0.00</span>
                        </div>
                        <a href="${BundliX.config.appUrl}/logout" class="btn btn-outline btn-block">Logout</a>
                    </div>
                </div>
            `;
        },
        
        /**
         * Highlight active menu item
         */
        highlightActiveMenu: function() {
            var currentRoute = BundliX.Router.getCurrentRoute();
            $('.nav-item').removeClass('active');
            $(`.nav-item a[href*="${currentRoute}"]`).closest('.nav-item').addClass('active');
        },
        
        /**
         * Load dashboard data
         */
        loadDashboardData: function() {
            if (!BundliX.isLoggedIn()) {
                return;
            }
            
            // Load user balance
            this.loadUserBalance();
            
            // Load recent transactions
            this.loadRecentTransactions();
            
            // Load quick stats
            this.loadQuickStats();
        },
        
        /**
         * Load user balance
         */
        loadUserBalance: function() {
            BundliX.request({
                url: BundliX.config.restUrl + 'wallet/balance',
                method: 'GET',
                success: function(response) {
                    if (response.success) {
                        $('#user-balance').text(BundliX.formatCurrency(response.data.balance));
                    }
                }
            });
        },
        
        /**
         * Load recent transactions
         */
        loadRecentTransactions: function() {
            // Placeholder - will be implemented in wallet feature
            BundliX.log('Loading recent transactions...');
        },
        
        /**
         * Load quick stats
         */
        loadQuickStats: function() {
            // Placeholder - will be implemented in dashboard feature
            BundliX.log('Loading quick stats...');
        },
        
        /**
         * Refresh dashboard data
         */
        refresh: function() {
            this.loadDashboardData();
            BundliX.notify('Dashboard refreshed', 'success');
        }
    };
    
    // Initialize dashboard when route changes to dashboard
    $(document).on('bundlix-route-change', function(e, route) {
        if (route === 'dashboard' && BundliX.isLoggedIn()) {
            BundliX.Dashboard.init();
        }
    });
    
    // Auto-initialize if already on dashboard
    if (BundliX.Router.getCurrentRoute() === 'dashboard' && BundliX.isLoggedIn()) {
        $(document).ready(function() {
            BundliX.Dashboard.init();
        });
    }
    
})(jQuery);
