<?php
/**
 * BundliX Login Template
 * Loaded via the Blank Canvas template engine
 */
if (!defined('ABSPATH')) exit;
?>

<div class="bundlix-auth-container">
    <div class="bundlix-auth-box">
        <div class="bundlix-logo-area">
            <h2>BundliX</h2>
            <p>Secure Login</p>
        </div>

        <form id="bundlix-login-form" class="bundlix-form">
            <?php wp_nonce_field('bundlix_auth_nonce', 'bundlix_nonce_field'); ?>
            
            <div class="form-group">
                <label for="bx-username">Phone Number or Email</label>
                <input type="text" id="bx-username" name="username" required placeholder="e.g. 0541234567" autocomplete="username">
            </div>

            <div class="form-group">
                <label for="bx-password">Password</label>
                <input type="password" id="bx-password" name="password" required placeholder="••••••••" autocomplete="current-password">
            </div>

            <div class="form-group checkbox-group">
                <label>
                    <input type="checkbox" name="remember" value="1"> Remember Me
                </label>
                <a href="<?php echo home_url('/app/forgot-password'); ?>" class="forgot-link">Forgot Password?</a>
            </div>

            <div id="bx-login-message" class="bx-message"></div>

            <button type="submit" id="bx-login-btn" class="bx-btn-primary">
                <span class="btn-text">Login</span>
                <span class="btn-loader" style="display:none;">Logging in...</span>
            </button>
        </form>

        <div class="auth-footer">
            <p>Don't have an account? <a href="<?php echo home_url('/app/register'); ?>">Register Now</a></p>
        </div>
    </div>
</div>

<style>
    /* Minimal scoped CSS for Login */
    .bundlix-auth-container { display: flex; justify-content: center; align-items: center; min-height: 80vh; padding: 20px; }
    .bundlix-auth-box { background: #fff; padding: 40px; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); width: 100%; max-width: 400px; }
    .bundlix-logo-area { text-align: center; margin-bottom: 30px; }
    .bundlix-logo-area h2 { color: #2c3e50; margin: 0; font-size: 28px; }
    .form-group { margin-bottom: 20px; }
    .form-group label { display: block; margin-bottom: 8px; color: #555; font-weight: 600; }
    .form-group input[type="text"], .form-group input[type="password"] { width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 4px; font-size: 16px; box-sizing: border-box; }
    .checkbox-group { display: flex; justify-content: space-between; align-items: center; font-size: 14px; }
    .bx-btn-primary { width: 100%; padding: 14px; background: #007bff; color: white; border: none; border-radius: 4px; font-size: 16px; cursor: pointer; transition: background 0.3s; }
    .bx-btn-primary:hover { background: #0056b3; }
    .bx-btn-primary:disabled { background: #ccc; cursor: not-allowed; }
    .bx-message { margin-bottom: 15px; padding: 10px; border-radius: 4px; font-size: 14px; display: none; }
    .bx-message.error { background: #f8d7da; color: #721c24; display: block; }
    .bx-message.success { background: #d4edda; color: #155724; display: block; }
    .auth-footer { text-align: center; margin-top: 20px; font-size: 14px; color: #666; }
    .auth-footer a { color: #007bff; text-decoration: none; }
</style>
