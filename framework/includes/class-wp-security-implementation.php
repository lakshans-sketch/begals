<?php

/**
 * Class WP_Security_Hardening
 *
 * Handles WordPress admin/login access hardening.
 *
 * @package    Bagels
 * @subpackage Security
 * @author     DoMedia
 * @version    1.0.0
 */

if (!class_exists('WP_Security_Hardening')) {

    class WP_Security_Hardening
    {
        private $geoip_loaded = false;
        private $geoip_db_path = '';
        private $geoip_reader = null;

        private $allowed_countries = array(
            "LK",
        );

        private $whitelisted_ips = array(
            // "112.134.175.92"
        );
        private $login_guard_table = '';

        public function __construct()
        {
            global $wpdb;

            $this->geoip_db_path = defined('BAGELS_GEOLITE2_DIRECTORY') ? BAGELS_GEOLITE2_DIRECTORY . '/GeoLite2-Country.mmdb' : '';
            $this->login_guard_table = $wpdb->prefix . 'bagels_login_guard';
            $this->init();
        }

        private function init()
        {
            $this->ensure_login_guard_table_exists();

            $this->allowed_countries = apply_filters('bagels_allowed_countries', $this->allowed_countries);
            $this->whitelisted_ips = apply_filters('bagels_whitelisted_ips', $this->whitelisted_ips);
            $this->whitelisted_ips = $this->sanitize_ip_list($this->whitelisted_ips);

            add_action('init', array($this, 'handle_security_checks'));
            add_filter('authenticate', array($this, 'block_bruteforce_authentication'), 1, 3);
            add_filter('authenticate', array($this, 'trigger_otp_challenge'), 30, 3);
            add_action('wp_login_failed', array($this, 'record_failed_login'), 10, 2);
            add_action('wp_login', array($this, 'clear_failed_login_attempts'));
            add_filter('rest_authentication_errors', array($this, 'protect_rest_authentication'));
            add_action('login_form_bagels_otp', array($this, 'render_otp_form'));
            add_action('login_form_bagels_otp_verify', array($this, 'process_otp'));
            add_filter('login_message', array($this, 'render_login_attempt_notice'));
            add_action('login_head', array($this, 'hide_login_form_during_lockout'));
            add_filter('lostpassword_url', array($this, 'filter_lostpassword_url'), 10, 2);
            add_filter('retrieve_password_notification_email', array($this, 'filter_retrieve_password_notification_email'), 10, 4);
            add_filter('retrieve_password_message', array($this, 'filter_retrieve_password_message'), 10, 4);
            add_filter('wp_new_user_notification_email', array($this, 'filter_new_user_notification_email'), 10, 3);
            add_action('wp_set_password', array($this, 'invalidate_sessions_after_password_change'), 10, 3);
            // Keep WP reauth flow untouched (default behavior).
            
        }

        private function ensure_login_guard_table_exists()
        {
            static $table_checked = false;

            if ($table_checked) {
                return;
            }

            global $wpdb;

            $table_checked = true;

            if (!defined('ABSPATH')) {
                return;
            }

            $charset_collate = $wpdb->get_charset_collate();
            $table_name = $this->login_guard_table;
            $table_exists = $wpdb->get_var(
                $wpdb->prepare('SHOW TABLES LIKE %s', $table_name)
            ) === $table_name;

            require_once ABSPATH . 'wp-admin/includes/upgrade.php';

            $sql = "CREATE TABLE {$table_name} (
                id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
                ip_hash CHAR(32) NOT NULL,
                ip_address VARCHAR(45) NOT NULL DEFAULT '',
                stage_index SMALLINT(5) UNSIGNED NOT NULL DEFAULT 0,
                attempts_in_stage SMALLINT(5) UNSIGNED NOT NULL DEFAULT 0,
                lock_until BIGINT(20) UNSIGNED NOT NULL DEFAULT 0,
                total_failures INT(10) UNSIGNED NOT NULL DEFAULT 0,
                permanent_block TINYINT(1) UNSIGNED NOT NULL DEFAULT 0,
                permanent_blocked_at BIGINT(20) UNSIGNED NOT NULL DEFAULT 0,
                created_at DATETIME NOT NULL,
                updated_at DATETIME NOT NULL,
                PRIMARY KEY  (id),
                UNIQUE KEY ip_hash (ip_hash),
                KEY lock_until (lock_until),
                KEY permanent_block (permanent_block)
            ) {$charset_collate};";

            dbDelta($sql);

            if (!$table_exists) {
                error_log('Created login guard table: ' . $table_name);
            }
        }

        private function load_geoip()
        {
            if ($this->geoip_loaded && $this->geoip_reader) {
                return;
            }

            if (!defined('BAGELS_GEOLITE2_DIRECTORY')) {
                error_log('BAGELS_GEOLITE2_DIRECTORY is not defined.');
                return;
            }

            $autoloader = BAGELS_GEOLITE2_DIRECTORY . '/vendor/autoload.php';

            if (!file_exists($autoloader)) {
                error_log('GeoLite2 autoloader not found: ' . $autoloader);
                return;
            }

            require_once $autoloader;

            if (!class_exists('\GeoIp2\Database\Reader')) {
                error_log('GeoLite2 reader class is not available.');
                return;
            }

            if (!file_exists($this->geoip_db_path)) {
                error_log('GeoLite2 database not found: ' . $this->geoip_db_path);
                return;
            }

            try {
                $this->geoip_reader = new \GeoIp2\Database\Reader($this->geoip_db_path);
                $this->geoip_loaded = true;
            } catch (\Exception $e) {
                error_log('GeoLite2 reader initialization failed: ' . $e->getMessage());
            }
        }

        public function handle_security_checks()
        {
            if ($this->is_system_request()) {
                return;
            }

            $request_uri = $_SERVER['REQUEST_URI'] ?? '';

            if ($this->is_do_access_request($request_uri)) {
                $this->handle_do_access_route();
                return;
            }

            if (!$this->is_protected_request($request_uri)) {
                return;
            }

            if (!$this->verify_do_access_token()) {
                $this->send_not_found();
            }
        }

        private function is_system_request()
        {
            return (
                (defined('WP_CLI') && WP_CLI) ||
                (defined('DOING_CRON') && DOING_CRON) ||
                (defined('DOING_AJAX') && DOING_AJAX)
            );
        }

        private function is_do_access_request($request_uri)
        {
            return $this->get_request_path($request_uri) === 'do-access';
        }

        private function is_protected_request($request_uri)
        {
            $path = $this->get_request_path($request_uri);
            $is_reauth_login = (
                strpos($path, 'wp-login.php') === 0 &&
                (
                    (isset($_REQUEST['reauth']) && (string) $_REQUEST['reauth'] === '1') ||
                    (isset($_REQUEST['interim-login']) && (string) $_REQUEST['interim-login'] === '1')
                )
            );

            // Do not block WP's built-in session reauthentication flow.
            if ($is_reauth_login) {
                return false;
            }

            if ($path === 'admin' || $path === 'admin/') {
                return true;
            }

            $blocked_patterns = array(
                'wp-login.php',
                'wp-register.php',
                'wp-signup.php',
            );

            $pagenow_blocked = isset($GLOBALS['pagenow']) && in_array(
                $GLOBALS['pagenow'],
                $blocked_patterns,
                true
            );

            $uri_blocked = false;
            foreach ($blocked_patterns as $pattern) {
                if (strpos($path, $pattern) === 0) {
                    $uri_blocked = true;
                    break;
                }
            }

            $is_admin_uri = (strpos($path, 'wp-admin') === 0);

            $is_rest_auth = (strpos($path, 'wp-json/jwt-auth') !== false);

            return ($pagenow_blocked || $uri_blocked || $is_admin_uri || $is_rest_auth);
        }

        private function handle_do_access_route()
        {
            if (!$this->verify_do_access_token()) {
                // Fresh visitor - full geo + IP check
                $ip = $this->get_user_ip();

                if (!empty($this->whitelisted_ips) && !$this->is_whitelisted_ip($ip)) {
                    $this->send_forbidden();
                }

                $country = $this->get_country_from_ip($ip);

                if (!empty($this->allowed_countries) && !empty($country) && !$this->is_allowed_country($country)) {
                    $this->send_forbidden();
                }

                $this->set_do_access_token(true);
                $this->redirect_to_login();
                return;
            }

            // Existing valid gate token; no fresh OTP intent.
            $this->set_fresh_gate_login_cookie(false);
            $this->redirect_to_login();
        }

        private function get_request_path($request_uri)
        {
            $path = trim((string) parse_url($request_uri, PHP_URL_PATH), '/');
            $home_path = trim((string) parse_url(home_url('/'), PHP_URL_PATH), '/');

            if ($home_path !== '') {
                if (strpos($path, $home_path . '/') === 0) {
                    $path = substr($path, strlen($home_path) + 1);
                } elseif ($path === $home_path) {
                    $path = '';
                }
            }

            return trim($path, '/');
        }

        private function get_user_ip()
        {
            if (!empty($_SERVER['HTTP_CF_CONNECTING_IP'])) {
                $ip = trim((string) $_SERVER['HTTP_CF_CONNECTING_IP']);
                $ip = explode(',', $ip)[0];
                $ip = $this->normalize_ip(trim($ip));

                if (filter_var($ip, FILTER_VALIDATE_IP)) {
                    return sanitize_text_field($ip);
                }
            }

            $ip = isset($_SERVER['REMOTE_ADDR']) ? $this->normalize_ip(trim((string) $_SERVER['REMOTE_ADDR'])) : '';

            if (filter_var($ip, FILTER_VALIDATE_IP)) {
                return sanitize_text_field($ip);
            }

            return '0.0.0.0';
        }

        private function normalize_ip($ip)
        {
            if ($ip === '::1') {
                return '127.0.0.1';
            }

            if (stripos($ip, '::ffff:') === 0) {
                $ipv4 = substr($ip, 7);
                if (filter_var($ipv4, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
                    return $ipv4;
                }
            }

            return $ip;
        }

        private function get_country_from_ip($ip)
        {
            if (in_array($ip, array('127.0.0.1', '::1', '0.0.0.0'), true)) {
                return null;
            }

            if (!filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
                return null;
            }

            $this->load_geoip();

            if (!$this->geoip_loaded || !$this->geoip_reader) {
                return null;
            }

            try {
                $record = $this->geoip_reader->country($ip);

                return !empty($record->country->isoCode) ? $record->country->isoCode : null;
            } catch (\Exception $e) {
                error_log('GeoLite2 lookup failed: ' . $e->getMessage());
                return null;
            }
        }

        private function is_whitelisted_ip($ip)
        {
            $normalized_ip = $this->normalize_ip(trim((string) $ip));
            if (!filter_var($normalized_ip, FILTER_VALIDATE_IP)) {
                return false;
            }

            return in_array($normalized_ip, $this->whitelisted_ips, true);
        }

        private function is_current_ip_whitelisted()
        {
            return $this->is_whitelisted_ip($this->get_user_ip());
        }

        private function sanitize_ip_list($ip_list)
        {
            if (!is_array($ip_list)) {
                $ip_list = array($ip_list);
            }

            $sanitized = array();

            foreach ($ip_list as $raw_entry) {
                if (!is_scalar($raw_entry)) {
                    continue;
                }

                $parts = preg_split('/[\s,]+/', (string) $raw_entry);
                if (!is_array($parts)) {
                    continue;
                }

                foreach ($parts as $part) {
                    $candidate = $this->normalize_ip(trim($part));
                    if ($candidate === '' || !filter_var($candidate, FILTER_VALIDATE_IP)) {
                        continue;
                    }

                    $sanitized[] = $candidate;
                }
            }

            return array_values(array_unique($sanitized));
        }

        private function is_allowed_country($country)
        {
            return in_array($country, $this->allowed_countries, true);
        }

        private function set_do_access_token($mark_fresh_gate_login = false)
        {
            $token   = bin2hex(random_bytes(32));
            $ip      = $this->get_user_ip();
            $expires = time() + DAY_IN_SECONDS; // 24hrs

            set_transient(
                'do_access_' . md5($token),
                array('ip' => $ip, 'expires' => $expires),
                DAY_IN_SECONDS
            );

            setcookie(
                'do_access_token',
                $token,
                array(
                    'expires'  => $expires,
                    'path'     => '/',
                    'secure'   => is_ssl(),
                    'httponly' => true,
                    'samesite' => 'Strict',
                )
            );

            $_COOKIE['do_access_token'] = $token;
            $this->set_fresh_gate_login_cookie((bool) $mark_fresh_gate_login);

            return $token;
        }

        private function set_fresh_gate_login_cookie($enabled)
        {
            $cookie_name = 'do_access_fresh_login';

            if ($enabled) {
                $expires = time() + (15 * MINUTE_IN_SECONDS);
                setcookie(
                    $cookie_name,
                    '1',
                    array(
                        'expires'  => $expires,
                        'path'     => '/',
                        'secure'   => is_ssl(),
                        'httponly' => true,
                        'samesite' => 'Strict',
                    )
                );
                $_COOKIE[$cookie_name] = '1';
                return;
            }

            setcookie(
                $cookie_name,
                '',
                array(
                    'expires'  => time() - HOUR_IN_SECONDS,
                    'path'     => '/',
                    'secure'   => is_ssl(),
                    'httponly' => true,
                    'samesite' => 'Strict',
                )
            );
            unset($_COOKIE[$cookie_name]);
        }

        private function redirect_to_login()
        {
            $login_args    = $this->get_forwarded_login_query_args();
            $raw_login_url = site_url('wp-login.php', 'login');
            $login_url     = add_query_arg($login_args, $raw_login_url);

            wp_safe_redirect($login_url);
            exit;
        }

        private function get_forwarded_login_query_args()
        {
            $allowed_args = array('action', 'key', 'login', 'redirect_to', 'checkemail', 'reauth', 'interim-login');
            $login_args = array();

            foreach ($allowed_args as $arg_name) {
                if (!isset($_GET[$arg_name]) || !is_scalar($_GET[$arg_name])) {
                    continue;
                }

                $raw_value = wp_unslash($_GET[$arg_name]);
                $value = '';

                if ($arg_name === 'redirect_to') {
                    $value = esc_url_raw((string) $raw_value);
                } elseif (in_array($arg_name, array('action', 'checkemail', 'reauth', 'interim-login'), true)) {
                    $value = sanitize_key((string) $raw_value);
                } else {
                    $value = sanitize_text_field((string) $raw_value);
                }

                if ($value !== '') {
                    $login_args[$arg_name] = $value;
                }
            }

            return $login_args;
        }

        private function build_protected_login_url($args = array())
        {
            $base_url = home_url('/do-access/');
            $safe_args = array();
            $allowed_args = array('action', 'key', 'login', 'redirect_to', 'checkemail', 'reauth', 'interim-login');

            if (!is_array($args)) {
                return $base_url;
            }

            foreach ($args as $arg_name => $arg_value) {
                if (!in_array($arg_name, $allowed_args, true) || !is_scalar($arg_value)) {
                    continue;
                }

                if ($arg_name === 'redirect_to') {
                    $value = esc_url_raw((string) $arg_value);
                } elseif (in_array($arg_name, array('action', 'checkemail', 'reauth', 'interim-login'), true)) {
                    $value = sanitize_key((string) $arg_value);
                } else {
                    $value = sanitize_text_field((string) $arg_value);
                }

                if ($value !== '') {
                    $safe_args[$arg_name] = $value;
                }
            }

            if (empty($safe_args)) {
                return $base_url;
            }

            return add_query_arg($safe_args, $base_url);
        }

        private function rewrite_wp_login_urls_in_message($message)
        {
            if (!is_string($message) || $message === '') {
                return $message;
            }

            return preg_replace_callback(
                '#https?://[^\s<>"\']*wp-login\.php(?:\?[^\s<>"\']*)?#i',
                array($this, 'replace_wp_login_url_in_message'),
                $message
            );
        }

        private function replace_wp_login_url_in_message($matches)
        {
            $original_url = isset($matches[0]) ? $matches[0] : '';
            if ($original_url === '') {
                return $original_url;
            }

            $query = (string) parse_url($original_url, PHP_URL_QUERY);
            $query = html_entity_decode($query, ENT_QUOTES, 'UTF-8');
            $query_args = array();
            parse_str($query, $query_args);

            return $this->build_protected_login_url($query_args);
        }

        public function filter_lostpassword_url($lostpassword_url, $redirect)
        {
            $args = array('action' => 'lostpassword');

            if (!empty($redirect)) {
                $args['redirect_to'] = $redirect;
            }

            return $this->build_protected_login_url($args);
        }

        public function filter_retrieve_password_notification_email($defaults, $key, $user_login, $user_data)
        {
            if (!is_array($defaults) || empty($defaults['message']) || !is_string($defaults['message'])) {
                return $defaults;
            }

            $defaults['message'] = $this->rewrite_wp_login_urls_in_message($defaults['message']);

            return $defaults;
        }

        public function filter_retrieve_password_message($message, $key, $user_login, $user_data)
        {
            return $this->rewrite_wp_login_urls_in_message((string) $message);
        }

        public function filter_new_user_notification_email($wp_new_user_notification_email, $user, $blogname)
        {
            if (!is_array($wp_new_user_notification_email) || empty($wp_new_user_notification_email['message'])) {
                return $wp_new_user_notification_email;
            }

            $wp_new_user_notification_email['message'] = $this->rewrite_wp_login_urls_in_message(
                $wp_new_user_notification_email['message']
            );

            return $wp_new_user_notification_email;
        }

        public function invalidate_sessions_after_password_change($password, $user_id, $old_user_data = null)
        {
            $user_id = (int) $user_id;
            if ($user_id <= 0 || !class_exists('WP_Session_Tokens')) {
                return;
            }

            $sessions = WP_Session_Tokens::get_instance($user_id);
            if ($sessions) {
                $sessions->destroy_all();
            }

            if (get_current_user_id() === $user_id) {
                wp_destroy_current_session();
                wp_clear_auth_cookie();

                // Also revoke gate token so user must re-pass geo check
                $this->revoke_do_access_token();
                setcookie('do_access_token', '', time() - 3600, '/', '', is_ssl(), true);

                if (function_exists('WC') && WC() && WC()->session) {
                    WC()->session->destroy_session();
                }
            }
        }

        // private function generate_do_access_token()
        // {
        //     $ip = $this->get_user_ip();
        //     $user_agent = $this->get_user_agent();
        //     $time = time();
        //     $secret = defined('AUTH_KEY') ? AUTH_KEY : 'do-access-fallback-secret';
        //     $hash = hash_hmac('sha256', $ip . '|' . $user_agent . '|' . $time, $secret);

        //     return base64_encode($time . '|' . $hash);
        // }

        private function verify_do_access_token()
        {
            $token = $_COOKIE['do_access_token'] ?? '';

            if (empty($token)) {
                return false;
            }

            $record = get_transient('do_access_' . md5($token));

            if (empty($record) || empty($record['ip']) || empty($record['expires'])) {
                return false;
            }

            if ($record['ip'] !== $this->get_user_ip()) {
                return false;
            }

            if ($record['expires'] < time()) {
                return false;
            }

            return true;
        }

        public function block_bruteforce_authentication($user, $username, $password)
        {
            if (!$this->is_standard_login_request()) {
                return $user;
            }

            if ($this->is_current_ip_whitelisted()) {
                return $user;
            }

            if ($this->has_too_many_failed_logins()) {
                $state = $this->get_failed_login_state();
                $message = esc_html__('Access denied.', 'bagels');

                if (!empty($state['permanent_block'])) {
                    $message = esc_html__('This IP address is permanently blocked. Please contact the site administrator.', 'bagels');
                } elseif ((int) $state['lock_until'] > time()) {
                    $remaining = (int) $state['lock_until'] - time();
                    $message = sprintf(
                        /* translators: %s: Human readable duration */
                        esc_html__('Too many failed login attempts. Try again in %s.', 'bagels'),
                        esc_html(human_time_diff(time(), time() + max(1, $remaining)))
                    );
                }

                return new WP_Error(
                    'bagels_too_many_login_attempts',
                    $message
                );
            }

            return $user;
        }

        public function record_failed_login($username = '', $error = null)
        {
            if (!$this->is_standard_login_request()) {
                return;
            }

            if ($this->is_current_ip_whitelisted()) {
                $this->clear_failed_login_state();
                return;
            }

            if ($this->is_ip_permanently_blocked()) {
                return;
            }

            $state = $this->get_failed_login_state();
            $now = time();

            // Ignore retries while already in a timed lockout window.
            if ((int) $state['lock_until'] > $now) {
                return;
            }

            $policies = $this->get_bruteforce_lock_policies();
            $stage_index = (int) $state['stage_index'];
            $active_policy = $policies[min($stage_index, count($policies) - 1)];

            $state['attempts_in_stage'] = (int) $state['attempts_in_stage'] + 1;
            $state['total_failures'] = (int) $state['total_failures'] + 1;

            if ((int) $state['attempts_in_stage'] >= (int) $active_policy['attempts']) {
                $state['attempts_in_stage'] = 0;

                if (!empty($active_policy['permanent_block'])) {
                    $state['permanent_block'] = true;
                    $state['permanent_blocked_at'] = $now;
                    $state['lock_until'] = 0;
                } else {
                    $state['lock_until'] = $now + (int) $active_policy['lock_seconds'];
                    $state['stage_index'] = $stage_index + 1;
                }
            }

            $this->set_failed_login_state($state);
        }

        public function clear_failed_login_attempts($user_login = '')
        {
            $this->clear_failed_login_state();
        }

        public function protect_rest_authentication($result)
        {
            if (!empty($result) || !$this->is_protected_rest_request()) {
                return $result;
            }

            if (!$this->verify_do_access_token()) {
                return new WP_Error(
                    'bagels_rest_access_denied',
                    esc_html__('Access denied.', 'bagels'),
                    array('status' => 403)
                );
            }

            return $result;
        }

        public function render_login_attempt_notice($message)
        {
            if (!$this->is_standard_login_request()) {
                return $message;
            }

            if ($this->is_current_ip_whitelisted()) {
                return $message;
            }

            $current_action = isset($_REQUEST['action']) ? sanitize_key(wp_unslash($_REQUEST['action'])) : '';
            if (in_array($current_action, array('bagels_otp', 'bagels_otp_verify'), true)) {
                return $message;
            }

            $state = $this->get_failed_login_state();
            $now = time();
            $notice = '';

            if (!empty($state['permanent_block'])) {
                $notice = esc_html__('This IP address is permanently blocked. Please contact the site administrator.', 'bagels');
                return $message . '<div id="login_error">' . esc_html($notice) . '</div>';
            }

            if ((int) $state['lock_until'] > $now) {
                $remaining = (int) $state['lock_until'] - $now;
                $notice = sprintf(
                    /* translators: %s: Human readable duration */
                    esc_html__('Too many failed login attempts. Try again in %s.', 'bagels'),
                    esc_html(human_time_diff($now, $now + max(1, $remaining)))
                );

                return $message . '<div id="login_error">' . esc_html($notice) . '</div>';
            }

            if ((int) $state['total_failures'] > 0) {
                $policies = $this->get_bruteforce_lock_policies();
                $stage_index = (int) $state['stage_index'];
                $active_policy = $policies[min($stage_index, count($policies) - 1)];
                $allowed_attempts = max(1, (int) $active_policy['attempts']);
                $attempts_left = max(0, $allowed_attempts - (int) $state['attempts_in_stage']);

                if ($attempts_left > 0) {
                    $notice = sprintf(
                        /* translators: %d: Remaining attempts */
                        esc_html__('Login attempts left before lock: %d', 'bagels'),
                        (int) $attempts_left
                    );

                    return $message . '<p class="message">' . esc_html($notice) . '</p>';
                }
            }

            return $message;
        }

        public function hide_login_form_during_lockout()
        {
            if (!$this->is_standard_login_request()) {
                return;
            }

            if ($this->is_current_ip_whitelisted()) {
                return;
            }

            $current_action = isset($_REQUEST['action']) ? sanitize_key(wp_unslash($_REQUEST['action'])) : '';
            if (in_array($current_action, array('bagels_otp', 'bagels_otp_verify'), true)) {
                return;
            }

            if (!$this->has_too_many_failed_logins()) {
                return;
            }
            ?>
                                    <style>
                                        #loginform > p,
                                        #loginform .user-pass-wrap,
                                        #loginform .forgetmenot,
                                        #loginform .submit {
                                            display: none !important;
                                        }
                                    </style>
                                    <?php
        }

        private function has_too_many_failed_logins()
        {
            if ($this->is_current_ip_whitelisted()) {
                return false;
            }

            if ($this->is_ip_permanently_blocked()) {
                return true;
            }

            $state = $this->get_failed_login_state();

            return (int) $state['lock_until'] > time();
        }

        private function get_user_ip_hash()
        {
            return md5($this->get_user_ip());
        }

        private function get_default_failed_login_state()
        {
            return array(
                'stage_index' => 0,
                'attempts_in_stage' => 0,
                'lock_until' => 0,
                'total_failures' => 0,
                'permanent_block' => false,
                'permanent_blocked_at' => 0,
            );
        }

        private function get_failed_login_state()
        {
            global $wpdb;

            $defaults = $this->get_default_failed_login_state();
            $row = $wpdb->get_row(
                $wpdb->prepare(
                    "SELECT stage_index, attempts_in_stage, lock_until, total_failures, permanent_block, permanent_blocked_at
                     FROM {$this->login_guard_table}
                     WHERE ip_hash = %s
                     LIMIT 1",
                    $this->get_user_ip_hash()
                ),
                ARRAY_A
            );

            if (!is_array($row)) {
                return $defaults;
            }

            return array(
                'stage_index' => isset($row['stage_index']) ? (int) $row['stage_index'] : $defaults['stage_index'],
                'attempts_in_stage' => isset($row['attempts_in_stage']) ? (int) $row['attempts_in_stage'] : $defaults['attempts_in_stage'],
                'lock_until' => isset($row['lock_until']) ? (int) $row['lock_until'] : $defaults['lock_until'],
                'total_failures' => isset($row['total_failures']) ? (int) $row['total_failures'] : $defaults['total_failures'],
                'permanent_block' => !empty($row['permanent_block']),
                'permanent_blocked_at' => isset($row['permanent_blocked_at']) ? (int) $row['permanent_blocked_at'] : $defaults['permanent_blocked_at'],
            );
        }

        private function set_failed_login_state($state)
        {
            global $wpdb;

            $ip = $this->get_user_ip();
            $ip_hash = $this->get_user_ip_hash();
            $stage_index = isset($state['stage_index']) ? (int) $state['stage_index'] : 0;
            $attempts_in_stage = isset($state['attempts_in_stage']) ? (int) $state['attempts_in_stage'] : 0;
            $lock_until = isset($state['lock_until']) ? (int) $state['lock_until'] : 0;
            $total_failures = isset($state['total_failures']) ? (int) $state['total_failures'] : 0;
            $permanent_block = !empty($state['permanent_block']) ? 1 : 0;
            $permanent_blocked_at = isset($state['permanent_blocked_at']) ? (int) $state['permanent_blocked_at'] : 0;
            $now_mysql = current_time('mysql', 1);

            $wpdb->query(
                $wpdb->prepare(
                    "INSERT INTO {$this->login_guard_table}
                        (ip_hash, ip_address, stage_index, attempts_in_stage, lock_until, total_failures, permanent_block, permanent_blocked_at, created_at, updated_at)
                     VALUES (%s, %s, %d, %d, %d, %d, %d, %d, %s, %s)
                     ON DUPLICATE KEY UPDATE
                        ip_address = VALUES(ip_address),
                        stage_index = VALUES(stage_index),
                        attempts_in_stage = VALUES(attempts_in_stage),
                        lock_until = VALUES(lock_until),
                        total_failures = VALUES(total_failures),
                        permanent_block = VALUES(permanent_block),
                        permanent_blocked_at = VALUES(permanent_blocked_at),
                        updated_at = VALUES(updated_at)",
                    $ip_hash,
                    $ip,
                    $stage_index,
                    $attempts_in_stage,
                    $lock_until,
                    $total_failures,
                    $permanent_block,
                    $permanent_blocked_at,
                    $now_mysql,
                    $now_mysql
                )
            );
        }

        private function get_bruteforce_lock_policies()
        {
            $policies = array(
                array('attempts' => 3, 'lock_seconds' => HOUR_IN_SECONDS),
                array('attempts' => 2, 'lock_seconds' => 2 * HOUR_IN_SECONDS),
                array('attempts' => 1, 'lock_seconds' => 5 * HOUR_IN_SECONDS),
                array('attempts' => 1, 'lock_seconds' => 10 * HOUR_IN_SECONDS),
                array('attempts' => 1, 'lock_seconds' => DAY_IN_SECONDS),
                array('attempts' => 1, 'lock_seconds' => 0, 'permanent_block' => true),
            );

            return apply_filters('bagels_bruteforce_lock_policies', $policies);
        }

        private function is_ip_permanently_blocked()
        {
            $state = $this->get_failed_login_state();

            return !empty($state['permanent_block']);
        }

        private function clear_failed_login_state()
        {
            global $wpdb;

            $wpdb->delete(
                $this->login_guard_table,
                array('ip_hash' => $this->get_user_ip_hash()),
                array('%s')
            );
        }

        private function is_standard_login_request()
        {
            $request_uri = $_SERVER['REQUEST_URI'] ?? '';
            $path = $this->get_request_path($request_uri);

            return (
                $path === 'wp-login.php' ||
                $this->is_do_access_request($request_uri) ||
                (isset($GLOBALS['pagenow']) && $GLOBALS['pagenow'] === 'wp-login.php')
            );
        }

        private function is_protected_rest_request()
        {
            if (!(defined('REST_REQUEST') && REST_REQUEST)) {
                return false;
            }

            $request_uri = $_SERVER['REQUEST_URI'] ?? '';
            $path = $this->get_request_path($request_uri);

            return (
                strpos($path, 'wp-json/jwt-auth') !== false ||
                strpos($path, 'wp-json/wp/v2/users') !== false ||
                strpos($path, 'wp-json/wp/v2/me') !== false
            );
        }

        private function get_user_agent()
        {
            return isset($_SERVER['HTTP_USER_AGENT'])
                ? sanitize_text_field(wp_unslash($_SERVER['HTTP_USER_AGENT']))
                : '';
        }

        private function send_forbidden()
        {
            status_header(403);
            nocache_headers();
            wp_die(
                esc_html__('Access denied.', 'bagels'),
                esc_html__('Access Denied', 'bagels'),
                array('response' => 403, 'back_link' => false)
            );
        }

        private function send_not_found()
        {
            status_header(404);
            nocache_headers();
            // // Load the theme's 404 template
            // $template = get_404_template();
            // if ($template) {
            //     include $template;
            //     exit;
            // }
            // Fallback if no 404 template found
            wp_die(
                esc_html__('Page not found.Contact Your Administration', 'bagels'),
                esc_html__('404', 'bagels'),
                array('response' => 404, 'back_link' => false)
            );
        }
        public function trigger_otp_challenge($user, $username, $password)
        {
            if (is_wp_error($user) || empty($user)) {
                return $user;
            }

            $request_uri = $_SERVER['REQUEST_URI'] ?? '';
            if (strpos($request_uri, 'wp-login.php') === false || empty($_POST['log'])) {
                return $user;
            }

            // WP session reauth — skip OTP, just password is enough
            $is_reauth = isset($_REQUEST['reauth']) && $_REQUEST['reauth'] == '1';
            if ($is_reauth) {
                return $user;
            }

            // OTP only for fresh gate logins.
            $is_fresh_gate_login = isset($_COOKIE['do_access_fresh_login']) && (string) $_COOKIE['do_access_fresh_login'] === '1';
            if (!$is_fresh_gate_login) {
                return $user;
            }

            $otp = wp_rand(100000, 999999);
            set_transient('bagels_otp_' . $user->ID, $otp, 10 * MINUTE_IN_SECONDS);
            delete_transient($this->get_otp_attempts_key($user->ID));

            $to = $user->user_email;
            $subject = get_bloginfo('name') . ' - Login Verification Code';
            $message = "Your verification code is: {$otp}\n\nThis code will expire in 10 minutes.";
            wp_mail($to, $subject, $message);

            $token_data = array(
                'user_id' => $user->ID,
                'remember' => isset($_POST['rememberme']),
                'time' => time(),
            );
            $token_data['hash'] = hash_hmac(
                'sha256',
                $user->ID . $token_data['remember'] . $token_data['time'],
                wp_salt()
            );

            $cookie_value = base64_encode(wp_json_encode($token_data));
            setcookie(
                'bagels_otp_token',
                $cookie_value,
                time() + 10 * MINUTE_IN_SECONDS,
                COOKIEPATH,
                COOKIE_DOMAIN,
                is_ssl(),
                true
            );

            $redirect_to = isset($_REQUEST['redirect_to']) ? $_REQUEST['redirect_to'] : admin_url();
            $otp_url = add_query_arg(
                array(
                    'action' => 'bagels_otp',
                    'redirect_to' => rawurlencode($redirect_to),
                ),
                site_url('wp-login.php', 'login')
            );

            // if (isset($_REQUEST['do_access'])) {
            //     $otp_url = add_query_arg('do_access', rawurlencode(wp_unslash($_REQUEST['do_access'])), $otp_url);
            // } elseif (isset($_COOKIE['do_access_token'])) {
            //     $otp_url = add_query_arg('do_access', rawurlencode(wp_unslash($_COOKIE['do_access_token'])), $otp_url);
            // }

            wp_safe_redirect($otp_url);
            exit;
        }

        private function get_otp_max_attempts()
        {
            return max(1, (int) apply_filters('bagels_otp_max_attempts', 5));
        }

        private function get_otp_attempts_key($user_id)
        {
            return 'bagels_otp_attempts_' . (int) $user_id;
        }

        private function get_otp_context_from_cookie()
        {
            $token = isset($_COOKIE['bagels_otp_token']) ? $_COOKIE['bagels_otp_token'] : '';
            if (empty($token)) {
                return false;
            }

            $decoded = json_decode(base64_decode($token), true);
            if (!$decoded || empty($decoded['user_id']) || empty($decoded['hash'])) {
                return false;
            }

            $expected_hash = hash_hmac('sha256', $decoded['user_id'] . $decoded['remember'] . $decoded['time'], wp_salt());
            if (!hash_equals($expected_hash, $decoded['hash'])) {
                return false;
            }

            return $decoded;
        }

        private function get_otp_login_url()
        {
            return site_url('wp-login.php', 'login');
        }

        public function render_otp_form($error_msg = '', $force_hide_form = false)
        {
            $hide_form = $force_hide_form;
            $otp_context = $this->get_otp_context_from_cookie();
            if (!$hide_form && $otp_context) {
                $attempts_made = (int) get_transient($this->get_otp_attempts_key($otp_context['user_id']));
                if ($attempts_made >= $this->get_otp_max_attempts()) {
                    $hide_form = true;
                }
            }

            $message = empty($error_msg)
                ? '<p class="message">' . __('Please check your email for the 6-digit verification code.', 'bagels') . '</p>'
                : '<div id="login_error">' . esc_html($error_msg) . '</div>';

            login_header(__('Enter OTP', 'bagels'), $message);

            $action_url = site_url('wp-login.php?action=bagels_otp_verify', 'login_post');
            // if (isset($_REQUEST['do_access'])) {
            //     $action_url = add_query_arg('do_access', rawurlencode(wp_unslash($_REQUEST['do_access'])), $action_url);
            // }
            $login_page_url = $this->get_otp_login_url();
            ?>
                                    <?php if ($hide_form): ?>
                                        <p class="submit">
                                            <a class="button button-primary button-large" href="<?php echo esc_url($login_page_url); ?>">
                                                <?php esc_html_e('Back to Login', 'bagels'); ?>
                                            </a>
                                        </p>
                                    <?php else: ?>
                                        <form name="otpform" id="otpform" action="<?php echo esc_url($action_url); ?>" method="post">
                                            <?php wp_nonce_field('bagels_otp_verify', 'bagels_otp_nonce'); ?>
                                            <p>
                                                <label for="otp_code"><?php esc_html_e('Verification Code', 'bagels'); ?></label>
                                                <input type="text" name="otp_code" id="otp_code" class="input" value="" size="20" autocomplete="off" required />
                                            </p>
                                            <?php if (isset($_REQUEST['redirect_to'])): ?>
                                                        <input type="hidden" name="redirect_to" value="<?php echo esc_attr($_REQUEST['redirect_to']); ?>" />
                                            <?php endif; ?>
                                            <p class="submit">
                                                <input type="submit" name="wp-submit" id="wp-submit" class="button button-primary button-large" value="<?php esc_attr_e('Verify Code', 'bagels'); ?>" />
                                            </p>
                                        </form>
                                        <script type="text/javascript">
                                            setTimeout(function() {
                                                try {
                                                    document.getElementById('otp_code').focus();
                                                } catch (e) {}
                                            }, 200);
                                        </script>
                                    <?php endif; ?>
                                    <?php
                                    login_footer();
                                    exit;
        }

        public function process_otp()
        {
            // if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            //     $redirect_url = wp_login_url();
            //     // if (isset($_REQUEST['do_access'])) {
            //     //     $redirect_url = add_query_arg('do_access', rawurlencode(wp_unslash($_REQUEST['do_access'])), $redirect_url);
            //     // }
            //     wp_safe_redirect($redirect_url);
            //     exit;
            // }

            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            wp_safe_redirect(site_url('wp-login.php', 'login'));
            exit;
        }

            if (!isset($_POST['bagels_otp_nonce']) || !wp_verify_nonce($_POST['bagels_otp_nonce'], 'bagels_otp_verify')) {
                $this->render_otp_form(__('Security check failed. Please try again.', 'bagels'));
            }

            $decoded = $this->get_otp_context_from_cookie();
            if (!$decoded) {
                $this->render_otp_form(__('Session expired. Please log in again.', 'bagels'));
            }

            if (time() - $decoded['time'] > 10 * MINUTE_IN_SECONDS) {
                $this->render_otp_form(__('Verification code expired.', 'bagels'));
            }

            $user_id = $decoded['user_id'];
            $attempts_key = $this->get_otp_attempts_key($user_id);
            $max_attempts = $this->get_otp_max_attempts();
            $attempts_made = (int) get_transient($attempts_key);

            if ($attempts_made >= $max_attempts) {
                delete_transient('bagels_otp_' . $user_id);
                setcookie('bagels_otp_token', '', time() - 3600, COOKIEPATH, COOKIE_DOMAIN, is_ssl(), true);
                $this->render_otp_form(__('Maximum verification attempts reached. Please log in again.', 'bagels'), true);
            }

            $entered_otp = sanitize_text_field($_POST['otp_code']);
            $stored_otp = get_transient('bagels_otp_' . $user_id);

            if ($stored_otp !== false && hash_equals((string) $stored_otp, $entered_otp)) {
                delete_transient('bagels_otp_' . $user_id);
                delete_transient($attempts_key);
                setcookie('bagels_otp_token', '', time() - 3600, COOKIEPATH, COOKIE_DOMAIN, is_ssl(), true);
                wp_set_current_user($user_id);
                $remember = !empty($decoded['remember']);

                $default_expiry_ts = time() + ($remember ? (14 * DAY_IN_SECONDS) : (2 * DAY_IN_SECONDS));
                $gate_expiry_ts = 0;
                $gate_token = isset($_COOKIE['do_access_token']) ? sanitize_text_field(wp_unslash($_COOKIE['do_access_token'])) : '';

                if ($gate_token !== '') {
                    $gate_record = get_transient('do_access_' . md5($gate_token));
                    if (is_array($gate_record) && !empty($gate_record['expires'])) {
                        $gate_expiry_ts = (int) $gate_record['expires'];
                    }
                }

                if ($gate_expiry_ts <= 0) {
                    $gate_expiry_ts = time() + DAY_IN_SECONDS;
                }

                $min_expiry_ts = $gate_expiry_ts + (6 * HOUR_IN_SECONDS);
                $wp_expiry_ts = max($default_expiry_ts, $min_expiry_ts);
                $cookie_expiry_filter = function($length, $uid, $rem) use ($wp_expiry_ts) {
                    return max(1, $wp_expiry_ts - time());
                };

                add_filter('auth_cookie_expiration', $cookie_expiry_filter, 99, 3);
                wp_set_auth_cookie($user_id, $remember, is_ssl());
                remove_filter('auth_cookie_expiration', $cookie_expiry_filter, 99);
                $this->set_fresh_gate_login_cookie(false);

                $user = get_userdata($user_id);
                do_action('wp_login', $user->user_login, $user);

                $redirect_to = !empty($_REQUEST['redirect_to']) ? $_REQUEST['redirect_to'] : admin_url();
                wp_safe_redirect($redirect_to);
                exit;
            }

            $attempts_made++;
            set_transient($attempts_key, $attempts_made, 10 * MINUTE_IN_SECONDS);

            if ($attempts_made >= $max_attempts) {
                delete_transient('bagels_otp_' . $user_id);
                setcookie('bagels_otp_token', '', time() - 3600, COOKIEPATH, COOKIE_DOMAIN, is_ssl(), true);
                $this->render_otp_form(__('Maximum verification attempts reached. Please log in again.', 'bagels'), true);
            }

            $remaining_attempts = max(0, $max_attempts - $attempts_made);
            $this->render_otp_form(
                sprintf(
                    /* translators: %d: remaining verification attempts */
                    __('Invalid verification code. Attempts remaining: %d', 'bagels'),
                    (int) $remaining_attempts
                )
            );
        }
        private function revoke_do_access_token()
        {
            $token = $_COOKIE['do_access_token'] ?? '';
            if (!empty($token)) {
                delete_transient('do_access_' . md5($token));
            }
        }

    }
    add_filter('rest_pre_dispatch', function ($result, $server, $request) {

        if (
            strpos($request->get_route(), '/wp/v2/users') !== false &&
            !current_user_can('list_users')
        ) {
            return new WP_Error(
                'rest_forbidden',
                'Sorry, you are not allowed.',
                ['status' => 403]
            );
        }

        return $result;

    }, 10, 3);
    
    add_filter('wp_is_application_passwords_available_for_user', function ($available, $user) {
    // Only allow for administrators
    if (!user_can($user, 'manage_options')) {
        return false;
    }
    return $available;
}, 10, 2);

    new WP_Security_Hardening();
}
