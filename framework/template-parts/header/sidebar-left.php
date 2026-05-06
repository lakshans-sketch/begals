<?php
/**
 * Displays Left Sidebar
 * Included in header.php
 */
?>

<?php
    $side_nav = array(
        'menu'              => 'mobile-slide-menu',
        'theme_location'    => 'mobile-slide-menu',
        'depth'             => 3,
        'container'         => '',
        'container_class'   => 'collapse navbar-collapse',
        'menu_class'        => 'nav navbar-nav navbar-right',
        'menu_id'           => '',
        'echo'              => true,
        'fallback_cb'       => 'wp_bootstrap_navwalker::fallback',
        'walker'            => new wp_bootstrap_navwalker()
    );
?>

<div class="sidebar-nav visible-xs visible-sm" id="left-sidebar">    
    <?php wp_nav_menu( $side_nav ); ?>
</div>

<div class="sidebar-overlay"></div>