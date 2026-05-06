<?php
/**
 * The template for displaying the footer
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 */

?>

<?php     
    /* Includes Footer CTA Banner Area */
    get_template_part( 'framework/template-parts/footer/footer', 'cta-banner' ); 
    
    /* Includes Footer Widget Area */
    get_template_part( 'framework/template-parts/footer/footer', 'widgets' ); 
    
    /* Includes Footer Bottom */
    get_template_part( 'framework/template-parts/footer/footer', 'bottom' ); 
    
?>

<!------------------ DON'T TOUCH AREA ------------------>

<?php 
/*
 * A Woocommerce Section
 * Mobile cart icon
 */
if ( bagels_is_woocommerce_enabled() && wp_is_mobile() ): ?>
    <div class="mobile-cart-icon visible-xs visible-sm">
        <a href="#" data-sidebar="right-sidebar" class="sidebar-toggler" aria-label="<?php _e( "Show shopping cart", 'bagels' ); ?>">
            <i class="far fa-shopping-basket"></i>
            <span class="cart-count-bubble"><?php echo WC()->cart->get_cart_contents_count(); ?></span> <!-- Changes will affect in woocommerce-custom-functions.php update_cart_details_fragments() function -->
        </a>
    </div>
<?php 
endif;
// End Woocommerce Section
?>

<?php if( BTS::$options['general']['back_to_top'] ): ?>   
    <div id="scroll-top"><img src="<?php echo THEME_DIRECTORY_URI; ?>/framework/assets/images/scroll-up.png" alt="Scroll To Top"></div>
<?php endif; ?>
    
<?php
// Add dynamic css configured by theme options
get_template_part('framework/assets/css/colors'); 
?>

<!--------------- END : DON'T TOUCH AREA --------------->

<?php wp_footer(); ?> 
    </div><!-- /.body-wrap -->
</body>
</html>
