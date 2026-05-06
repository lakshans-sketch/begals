<?php
/**
 * Displays mobile header action icons
 * Included in nav.php
 */

/*
 * A Woocommerce Section
 * Mobile Woo Search
 */
if ( bagels_is_woocommerce_enabled() && wp_is_mobile()): ?>
    <a href="#" class="woo-search-icon visible-xs visible-sm" aria-label="<?php _e( "Show search bar", 'bagels' ); ?>"><i class="far fa-search"></i></a>
<?php endif; ?>

<a href="#" data-sidebar="left-sidebar" class="sidebar-toggler header-ham-icon visible-xs visible-sm" aria-label="<?php _e( "Show side menu", 'bagels' ); ?>">
    <span></span><span></span><span></span><span></span>
</a>