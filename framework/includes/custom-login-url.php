<?php
/**
 * Custom Login URL Handler - WordPress Default Style (FIXED)
 * 
 * This file handles custom login URL functionality for WordPress
 * - Custom login at: /do-loging
 * - Blocks access to /wp-admin and wp-login.php
 * - Uses EXACT WordPress default login styling
 * 
 * @package YourTheme
 * @version 1.0.1
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Custom Login URL Class
 */
class Custom_Login_URL {
    
    private $custom_login_slug = 'do-access';
    
    public function __construct() {
        add_action('init', array($this, 'handle_custom_login_url'));
        add_action('wp_login_failed', array($this, 'redirect_login_fail'));
        add_filter('authenticate', array($this, 'verify_username_password'), 1, 3);
        add_action('admin_init', array($this, 'block_wp_admin_access'));
        add_action('login_enqueue_scripts', array($this, 'hide_default_login'));
    }
    
    /**
     * Handle custom login URL routing
     */
    public function handle_custom_login_url() {
        // Get current request URI
        $request_uri = trim($_SERVER['REQUEST_URI'], '/');
        $parsed_url = parse_url($request_uri);
        $path = isset($parsed_url['path']) ? trim($parsed_url['path'], '/') : $request_uri;
        
        // Check if accessing custom login URL
        if ($path === $this->custom_login_slug) {
            $this->show_custom_login_page();
            exit;
        }
        
        // Block direct access to wp-login.php
        if ($this->is_wp_login_php()) {
            $this->send_404();
        }
    }
    
    /**
     * Display custom login page
     */
    private function show_custom_login_page() {
        // If user is already logged in, redirect to admin
        if (is_user_logged_in()) {
            wp_safe_redirect(admin_url());
            exit;
        }
        
        // Handle login form submission
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['custom_login_submit'])) {
            $this->process_login();
        }
        
        // Get any error messages
        $error_message = '';
        $has_error = false;
        if (isset($_GET['login']) && $_GET['login'] === 'failed') {
            $error_message = '<strong>Error:</strong> Invalid username or password.';
            $has_error = true;
        }
        
        // Render the login form
        $this->render_login_form($error_message, $has_error);
    }
    
    /**
     * Process login form submission
     */
    private function process_login() {
        // Verify nonce
        if (!isset($_POST['custom_login_nonce']) || 
            !wp_verify_nonce($_POST['custom_login_nonce'], 'custom_login_action')) {
            wp_die('Security check failed');
        }
        
        $username = sanitize_user($_POST['log']);
        $password = $_POST['pwd'];
        $remember = isset($_POST['rememberme']);
        
        $creds = array(
            'user_login'    => $username,
            'user_password' => $password,
            'remember'      => $remember
        );
        
        $user = wp_signon($creds, is_ssl());
        
        if (is_wp_error($user)) {
            wp_safe_redirect(home_url($this->custom_login_slug . '?login=failed'));
            exit;
        } else {
            wp_safe_redirect(admin_url());
            exit;
        }
    }
    
    /**
     * Render WordPress default style login form
     */
    private function render_login_form($error_message = '', $has_error = false) {
        // Load WordPress core styles and scripts for login
        wp_enqueue_style('login');
        wp_enqueue_script('user-profile');
        
        nocache_headers();
        
        ?>
        <!DOCTYPE html>
        <html <?php language_attributes(); ?>>
        <head>
            <meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php bloginfo('charset'); ?>" />
            <title><?php echo get_bloginfo('name'); ?> &rsaquo; Log In</title>
            <?php
            wp_admin_css('login', true);
            
            // Colors from WordPress customizer
            ?>
            <style type="text/css">
                /* Make it look exactly like WordPress default */
            </style>
            <?php
            
            if ( wp_is_mobile() ) {
                ?>
                <meta name="viewport" content="width=device-width" />
                <?php
            }
            
            do_action('login_enqueue_scripts');
            do_action('login_head');
            ?>
        </head>
        <body class="login no-js login-action-login wp-core-ui">
            <script type="text/javascript">
                document.body.className = document.body.className.replace('no-js','js');
            </script>
            
            <div id="login">
                <h1><a href="<?php echo esc_url(home_url('/')); ?>"><?php bloginfo('name'); ?></a></h1>
                
                <?php if ($has_error && $error_message): ?>
                <div id="login_error"><?php echo $error_message; ?></div>
                <?php endif; ?>
                
                <form name="loginform" id="loginform" action="" method="post">
                    <?php wp_nonce_field('custom_login_action', 'custom_login_nonce'); ?>
                    
                    <p>
                        <label for="user_login">Username or Email Address</label>
                        <input type="text" name="log" id="user_login" class="input" value="" size="20" autocapitalize="off" />
                    </p>
                    
                    <div class="user-pass-wrap">
                        <label for="user_pass">Password</label>
                        <div class="wp-pwd">
                            <input type="password" name="pwd" id="user_pass" class="input password-input" value="" size="20" />
                            <button type="button" class="button button-secondary wp-hide-pw hide-if-no-js" data-toggle="0" aria-label="Show password">
                                <span class="dashicons dashicons-visibility" aria-hidden="true"></span>
                            </button>
                        </div>
                    </div>
                    
                    <?php do_action('login_form'); ?>
                    
                    <p class="forgetmenot">
                        <input name="rememberme" type="checkbox" id="rememberme" value="forever" />
                        <label for="rememberme">Remember Me</label>
                    </p>
                    
                    <p class="submit">
                        <input type="submit" name="custom_login_submit" id="wp-submit" class="button button-primary button-large" value="Log In" />
                    </p>
                </form>
                
                <p id="nav">
                    <a href="<?php echo esc_url(wp_lostpassword_url()); ?>">Lost your password?</a>
                </p>
                
                <p id="backtoblog">
                    <a href="<?php echo esc_url(home_url('/')); ?>">&larr; Go to <?php bloginfo('name'); ?></a>
                </p>
            </div>
            
            <?php do_action('login_footer'); ?>
            
            <script type="text/javascript">
            <?php if (wp_is_mobile()) : ?>
                (function(){
                    try {
                        document.getElementById('user_login').focus();
                    } catch(e) {}
                })();
            <?php endif; ?>
            
            // Password show/hide toggle
            (function() {
                var button = document.querySelector('.wp-hide-pw');
                var input = document.getElementById('user_pass');
                
                if (button && input) {
                    button.addEventListener('click', function() {
                        if (input.type === 'password') {
                            input.type = 'text';
                            button.querySelector('.dashicons').classList.remove('dashicons-visibility');
                            button.querySelector('.dashicons').classList.add('dashicons-hidden');
                            button.setAttribute('aria-label', 'Hide password');
                        } else {
                            input.type = 'password';
                            button.querySelector('.dashicons').classList.remove('dashicons-hidden');
                            button.querySelector('.dashicons').classList.add('dashicons-visibility');
                            button.setAttribute('aria-label', 'Show password');
                        }
                    });
                }
            })();
            </script>
        </body>
        </html>
        <?php
    }
    
    /**
     * Redirect failed login attempts
     */
    public function redirect_login_fail() {
        $referrer = wp_get_referer();
        
        // Check if coming from custom login page
        if ($referrer && strpos($referrer, $this->custom_login_slug) !== false) {
            wp_safe_redirect(home_url($this->custom_login_slug . '?login=failed'));
            exit;
        }
    }
    
    /**
     * Verify username and password authentication
     */
    public function verify_username_password($user, $username, $password) {
        // Only process if we have credentials
        if ($username == '' || $password == '') {
            return $user;
        }
        
        return $user;
    }
    
    /**
     * Block access to /wp-admin for non-logged-in users
     */
    public function block_wp_admin_access() {
        // Allow AJAX requests
        if (defined('DOING_AJAX') && DOING_AJAX) {
            return;
        }
        
        // If user is not logged in and trying to access wp-admin
        if (!is_user_logged_in()) {
            $this->send_404();
        }
    }
    
    /**
     * Hide default login page (wp-login.php)
     */
    public function hide_default_login() {
        // Only hide if not on custom login page
        if (!$this->is_custom_login_page()) {
            $this->send_404();
        }
    }
    
    /**
     * Check if current page is wp-login.php
     */
    private function is_wp_login_php() {
        $script_name = basename($_SERVER['SCRIPT_NAME']);
        return $script_name === 'wp-login.php';
    }
    
    /**
     * Check if current page is custom login page
     */
    private function is_custom_login_page() {
        $request_uri = trim($_SERVER['REQUEST_URI'], '/');
        return strpos($request_uri, $this->custom_login_slug) !== false;
    }
    
    /**
     * Send 404 response
     */
    private function send_404() {
        global $wp_query;
        
        if (!$wp_query) {
            status_header(404);
            nocache_headers();
            include(get_404_template());
            exit;
        }
        
        $wp_query->set_404();
        status_header(404);
        nocache_headers();
        
        if (file_exists(get_404_template())) {
            include(get_404_template());
        } else {
            // Fallback 404 page
            ?>
            <!DOCTYPE html>
            <html>
            <head>
                <title>404 Not Found</title>
                <style>
                    body { font-family: Arial, sans-serif; text-align: center; padding: 50px; }
                    h1 { font-size: 48px; color: #333; }
                    p { font-size: 18px; color: #666; }
                </style>
            </head>
            <body>
                <h1>404</h1>
                <p>Page not found.</p>
            </body>
            </html>
            <?php
        }
        exit;
    }
}

// Initialize the custom login URL handler
new Custom_Login_URL();