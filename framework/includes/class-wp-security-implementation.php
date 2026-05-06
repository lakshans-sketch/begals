<?php

/**
 * Class WP_Security_Hardening
 *
 * Handles all WordPress security hardening measures
 *
 * Features:
 *
 * @package    Bagels_Estore
 * @subpackage Security
 * @author     DoMedia
 * @version    1.0.0
 */

if ( ! class_exists( 'WP_Security_Hardening' ) ) {

    class WP_Security_Hardening {

        public function __construct() {
            $this->init();
        }

        private function init() {
            
        }

    }

    new WP_Security_Hardening();

}