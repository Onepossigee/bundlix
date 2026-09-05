<?php
/**
 * BundliX Dashboard Template
 * Main user dashboard after login
 */

if (!defined('ABSPATH')) {
    exit;
}

// Check if user is logged in
if (!is_user_logged_in()) {
    // Redirect to login
    wp_redirect(home_url('/app/login'));
    exit;
}

$current_user = wp_get_current_user();
?>

<div class="bundlix-dashboard">
    <header class="bundlix-dashboard-header">
        <div class="header-content">
            <h1 class="dashboard-title">Welcome, <?php echo esc_html($current_user->display_name); ?></h1>
            <div class="header-actions">
                <button id="bx-logout-btn" class="bx-btn-secondary">Logout</button>
            </div>
        </div>
    </header>

    <div class="bundlix-dashboard-content">
        <!-- Quick Stats -->
        <div class="stats-grid">
            <div class="stat-card">
                <h3>Wallet Balance</h3>
                <p class="stat-value">GHS 0.00</p>
                <a href="<?php echo home_url('/app/wallet'); ?>" class="stat-link">Fund Wallet</a>
            </div>
            <div class="stat-card">
                <h3>Total Transactions</h3>
                <p class="stat-value">0</p>
                <a href="<?php echo home_url('/app/transactions'); ?>" class="stat-link">View All</a>
            </div>
            <div class="stat-card">
                <h3>Referrals</h3>
                <p class="stat-value">0</p>
                <a href="<?php echo home_url('/app/referrals'); ?>" class="stat-link">Invite Friends</a>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="quick-actions">
            <h2>Quick Actions</h2>
            <div class="actions-grid">
                <a href="<?php echo home_url('/app/buy-data'); ?>" class="action-card">
                    <div class="action-icon">📶</div>
                    <span>Buy Data</span>
                </a>
                <a href="<?php echo home_url('/app/buy-airtime'); ?>" class="action-card">
                    <div class="action-icon">📱</div>
                    <span>Buy Airtime</span>
                </a>
                <a href="<?php echo home_url('/app/buy-pins'); ?>" class="action-card">
                    <div class="action-icon">🎓</div>
                    <span>Buy Exam Pins</span>
                </a>
                <a href="<?php echo home_url('/app/wallet'); ?>" class="action-card">
                    <div class="action-icon">💳</div>
                    <span>Fund Wallet</span>
                </a>
            </div>
        </div>

        <!-- Recent Transactions -->
        <div class="recent-transactions">
            <h2>Recent Transactions</h2>
            <div class="transactions-placeholder">
                <p>No transactions yet. Start by purchasing a service!</p>
            </div>
        </div>
    </div>
</div>

<style>
    .bundlix-dashboard { max-width: 1200px; margin: 0 auto; padding: 20px; }
    .bundlix-dashboard-header { background: linear-gradient(135deg, #4F46E5 0%, #7C3AED 100%); color: white; padding: 30px; border-radius: 12px; margin-bottom: 30px; }
    .header-content { display: flex; justify-content: space-between; align-items: center; }
    .dashboard-title { margin: 0; font-size: 28px; }
    .bx-btn-secondary { background: rgba(255,255,255,0.2); color: white; border: 1px solid rgba(255,255,255,0.3); padding: 10px 20px; border-radius: 6px; cursor: pointer; transition: all 0.3s; }
    .bx-btn-secondary:hover { background: rgba(255,255,255,0.3); }
    
    .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-bottom: 30px; }
    .stat-card { background: white; padding: 25px; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.08); text-align: center; }
    .stat-card h3 { margin: 0 0 15px 0; color: #666; font-size: 14px; text-transform: uppercase; letter-spacing: 0.5px; }
    .stat-value { font-size: 32px; font-weight: bold; color: #2c3e50; margin: 0 0 10px 0; }
    .stat-link { color: #4F46E5; text-decoration: none; font-size: 14px; font-weight: 600; }
    .stat-link:hover { text-decoration: underline; }
    
    .quick-actions { margin-bottom: 30px; }
    .quick-actions h2 { color: #2c3e50; margin-bottom: 20px; }
    .actions-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 15px; }
    .action-card { background: white; padding: 30px 20px; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.08); text-align: center; text-decoration: none; color: #2c3e50; transition: transform 0.3s, box-shadow 0.3s; }
    .action-card:hover { transform: translateY(-5px); box-shadow: 0 5px 15px rgba(0,0,0,0.15); }
    .action-icon { font-size: 40px; margin-bottom: 10px; }
    
    .recent-transactions h2 { color: #2c3e50; margin-bottom: 20px; }
    .transactions-placeholder { background: #f8f9fa; padding: 40px; border-radius: 12px; text-align: center; color: #666; }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const logoutBtn = document.getElementById('bx-logout-btn');
    if (logoutBtn) {
        logoutBtn.addEventListener('click', function() {
            if (confirm('Are you sure you want to logout?')) {
                const formData = new FormData();
                formData.append('action', 'bundlix_logout');
                formData.append('nonce', bundlixApp.nonce);
                
                fetch(bundlixApp.ajaxUrl, {
                    method: 'POST',
                    body: formData,
                    credentials: 'same-origin'
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        window.location.href = data.data.redirect_url;
                    }
                })
                .catch(error => console.error('Logout error:', error));
            }
        });
    }
});
</script>
