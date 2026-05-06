<?php
/**
 * The header for our theme
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 */
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>

<head>
    <meta charset="<?php bloginfo('charset'); ?>" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <link rel="pingback" href="<?php bloginfo('pingback_url'); ?>" />


    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Heebo:wght@100..900&family=League+Spartan:wght@100..900&display=swap"
        rel="stylesheet">
    <!-- START Display Upgrade Message for IE 10 or Less -->
    <script>
        if (navigator.userAgent.indexOf("MSIE") >= 0) {
            document.write('<div style="background: #fff; height: 100%; text-align: center; padding: 10% 20px 5%; position: fixed; top: 0px; left: 0; bottom: 0; right: 0; line-height: 1.6; font-size: 16px; color: #f00; z-index: 9999999;">This website may not be compatible with your outdated Internet Explorer version.<br>Please upgrade to Internet Explorer 11 or use a different web browser.</div>');
        }
    </script>
    <!-- END Display Upgrade Message for IE 10 or Less -->

    <?php wp_head(); ?>

</head>

<body <?php body_class(); ?>>
    <?php echo do_shortcode('[accessibility_menu_widget position="right"]'); ?>

    <div class="body-wrap"><!-- This div will end in footer -->
        <?php wp_body_open(); ?>

        <?php
        if (!empty(BTS::$options['general']['page_loader'])): ?>
            <div class="page-loader">
                <div class="pgl-spinner"></div>
            </div>
        <?php endif; ?>

        <?php
        /* 
         * Includes Main Navigation 
         */
        get_template_part('framework/template-parts/navigation/nav');


        /*
         * Left Sidebar for mobile
         */
        get_template_part('framework/template-parts/header/sidebar-left');

        /*
         * A Woocommerce Section
         * Includes Sidebar cart for mobile
         * Includes Mobile Woo Search
         */
        if (bagels_is_woocommerce_enabled() && wp_is_mobile()) {
            // Sidebar Cart
            get_template_part('framework/template-parts/header/sidebar-right');

            // Mobile Woo Search
            get_template_part('framework/template-parts/header/mobile-woo-search');
        }