<?php

/**
 * Class WP_Security_Hardening
 *
 * Handles all WordPress security hardening measures.
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

        public function __construct()
        {
            $this->geoip_db_path = BAGELS_GEOLITE2_DIRECTORY . '/GeoLite2-Country.mmdb';
            $this->init();
        }

        private function init()
        {
            $this->load_geoip();
            $this->restrict_admin_access();
        }

        /*===============================================================================================================
        =            GeoIP Admin Access Restriction (Country-Based Security Layer)
        =
        =            This module restricts WordPress admin/login access based on the user's IP geolocation. It ensures
        =            that only users from allowed countries (and whitelisted IPs) can access sensitive admin areas.
        =            It uses the GeoLite2 database for IP-to-country lookup
        =            Main Functions:
        =                load_geoip()
        =                restrict_admin_access()
        =                get_user_ip()
        =                get_country_from_ip($ip)
        =                is_whitelisted_ip($ip)
        =                is_allowed_country($country)
        =
        =            Configuration:
        =                GeoLite2 library path is defined in theme configuration:
        =                define('BAGELS_GEOLITE2_DIRECTORY', THEME_DIRECTORY . '/framework/libs/GeoLite2');
        =
        =                Autoloader is included via:
        =                BAGELS_GEOLITE2_DIRECTORY . '/vendor/autoload.php';
        =
        ==================================================================================================================*/

        // Allowed countries (ISO codes)
        private $allowed_countries = array(
            'LK',  
        );

        // Whitelisted IPs (always allowed regardless of country)
        private $whitelisted_ips = array();
        private function load_geoip()
        {
            $autoloader = BAGELS_GEOLITE2_DIRECTORY . '/vendor/autoload.php';

            if (!file_exists($autoloader)) {
                error_log('GeoLite2 autoloader not found: ' . $autoloader);
                return;
            }

            require_once $autoloader;
            $this->geoip_loaded = class_exists('\GeoIp2\Database\Reader');

            if (!$this->geoip_loaded) {
                error_log('GeoLite2 reader class is not available.');
            }
        }

        private function restrict_admin_access()
        {
            add_action('init', function () {
                if (defined('DOING_CRON') || defined('WP_CLI')) {
                    return;
                }

                if (defined('DOING_AJAX') && DOING_AJAX) {
                    return;
                }

                if (defined('REST_REQUEST') && REST_REQUEST) {
                    return;
                }

                $is_admin = is_admin();
                $request_uri = $_SERVER['REQUEST_URI'] ?? '';
                $is_login = false;

                if (isset($GLOBALS['pagenow'])) {
                    $is_login = in_array($GLOBALS['pagenow'], array(
                        'wp-login.php',
                        'wp-register.php',
                        'wp-signup.php',
                    ), true);
                }

                if (!$is_login) {
                    $is_login = (
                        strpos($request_uri, 'wp-login.php') !== false ||
                        strpos($request_uri, 'wp-register.php') !== false ||
                        strpos($request_uri, 'wp-signup.php') !== false
                    );
                }

                if (!$is_admin && !$is_login) {
                    return;
                }

                $ip = $this->get_user_ip();
                if ($this->is_whitelisted_ip($ip)) {
                    return; // always allow
                }
                $country = $this->get_country_from_ip($ip);

                if (empty($country)) {
                    return; // GeoIP failed, skip blocking to avoid lockout
                }

                // Check allowed countries
                if (!$this->is_allowed_country($country)) {
                    status_header(403);
                    wp_die(
                        '<h1>Access Restricted</h1><p>The admin area is only accessible from allowed regions.</p>',
                        'Access Denied',
                        array(
                            'response' => 403,
                            'back_link' => false,
                        )
                    );
                }
            });
        }

        private function get_user_ip()
        {
            $headers = array(
                'HTTP_CF_CONNECTING_IP',
                'HTTP_X_REAL_IP',
                'HTTP_X_FORWARDED_FOR',
                'HTTP_CLIENT_IP',
                'REMOTE_ADDR',
            );

            foreach ($headers as $header) {
                if (empty($_SERVER[$header])) {
                    continue;
                }

                $ip = trim(explode(',', $_SERVER[$header])[0]);

                if (filter_var($ip, FILTER_VALIDATE_IP)) {
                    return sanitize_text_field($ip);
                }
            }

            return '0.0.0.0';
        }

        private function get_country_from_ip($ip)
        {
            if (in_array($ip, array('127.0.0.1', '::1', '0.0.0.0'), true)) {
                return null;
            }

            if (!filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
                return null;
            }

            if (!$this->geoip_loaded) {
                return null;
            }

            if (!file_exists($this->geoip_db_path)) {
                error_log('GeoLite2 database not found: ' . $this->geoip_db_path);
                return null;
            }

            try {
                $reader = new \GeoIp2\Database\Reader($this->geoip_db_path);
                $record = $reader->country($ip);
                $country = !empty($record->country->isoCode) ? $record->country->isoCode : null;
                $reader->close();

                return $country;
            } catch (\Exception $e) {
                error_log('GeoLite2 lookup failed: ' . $e->getMessage());
                return null;
            }
        }
        // Check if IP is whitelisted
        private function is_whitelisted_ip($ip)
        {
            return in_array($ip, $this->whitelisted_ips, true);
        }

        // Check if country is allowed
        private function is_allowed_country($country)
        {
            return in_array($country, $this->allowed_countries, true);
        }
/*===============================================================================================================
=                                         GeoIP Admin Access Restriction block End
===============================================================================================================*/





    }

    new WP_Security_Hardening();
}
