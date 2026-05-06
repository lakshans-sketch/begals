<?php
    /**
     * Includes widgets
     */

    $plugin_list = array(
        'social-links/social-links.php',
        'newsletter-widget/newsletter-form.php',
        'accessibility-widget/accessibility-widget.php',
    );

    foreach ( $plugin_list as $key => $plugin ) { include_once( $plugin ); }
?>