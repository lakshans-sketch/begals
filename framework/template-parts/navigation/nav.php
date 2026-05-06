<?php
/**
 * Displays header main navigation
 * Included in header.php
 */

$site_title = get_bloginfo( 'name' );
?>

<nav class="navbar navbar-fixed-top navbar-default" id="site-header">
    <div class="container">
        <div class="flex-parent">
            <div class="navbar-header">
                <a class="navbar-brand header-logo" href="<?php echo get_site_url(); ?>" aria-label="<?php _e( "Go to home page", 'bagels' ); ?>">
                    <?php if( !empty( BTS::$options['header']['logo'] ) ): ?>
                        <img src="<?php echo esc_url( BTS::$options['header']['logo']['sizes']['medium'] ); ?>" alt="<?php echo $site_title; ?>">
                    <?php else: 
                        _e( $site_title, 'bagels' );
                    endif; ?>
                </a>

                <div class="header-buttons-wrapper">
                    <!------------------ DON'T TOUCH AREA ------------------>
                        <?php 
                            /*
                            * Mobile header actions
                            */
                            get_template_part( 'framework/template-parts/header/mobile-header-actions' ); 
                        ?>
                    <!--------------- END : DON'T TOUCH AREA --------------->
                </div>
            </div>

            <div class="collapse navbar-collapse" id="main-navbar">
                
                <?php
                    $main_nav = array(
                        'menu'              => 'primary-menu',
                        'theme_location'    => 'primary-menu',
                        'depth'             => 3,
                        'container'         => '',
                        'container_class'   => 'collapse navbar-collapse',
                        'menu_class'        => 'nav navbar-nav navbar-right',
                        'menu_id'           => '',
                        'echo'              => true,
                        'fallback_cb'       => 'WP_Bootstrap_Navwalker::fallback',
                        'walker'            => new WP_Bootstrap_Navwalker()
                    );
                    wp_nav_menu( $main_nav );
                ?>
 
 
            <?php 
            /*
             * Header Search for desktop
             */
            if( BTS::$options['header']['header_search'] /* && !wp_is_mobile() */ ){
                get_template_part( 'framework/template-parts/header/header-search' );
            }
            ?>
                                                                             
            <?php 
            /*
             * A Woocommerce Section
             * Includes Mini Cart popup for desktop
             */
            if ( bagels_is_woocommerce_enabled() /* && !wp_is_mobile() */ ) {
                get_template_part( 'framework/template-parts/header/mini-cart-popup' ); 
            }
            ?>

        </div>
    </div>
</nav><!-- /#site-header -->