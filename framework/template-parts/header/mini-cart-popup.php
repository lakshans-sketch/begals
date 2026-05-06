<?php
/**
 * Displays header Mini Cart popup
 * Included in nav.php
 * structure changes will be affect in framework/includes/woocommerce-custom-functions.php
 * update_cart_details_fragments() function
 */
?>

<div class="nav-cart hidden-xs hidden-sm">
    <a href="#" class="nav-cart-toggler" aria-label="<?php _e( "Go to cart page", 'bagels' ); ?>">
        <i class="far fa-shopping-basket"></i>
        <div class="cart-totals">
            <!-- <span class="amount"><?php /* echo WC()->cart->get_cart_subtotal(); */ ?></span> /  -->
            <span class="count"><?php echo WC()->cart->get_cart_contents_count(); ?></span>
        </div>
    </a>
    
    <div class="mini-cart-box">
        <div class="mini-cart-wrapper">
            <?php woocommerce_mini_cart(); ?>
        </div>
    </div>
</div>

<ul class="nav navbar-nav navbar-right navbar-country-links hidden-xs hidden-sm" style="display:inline-flex;align-items:center;margin:0;">
    <li>
        <a href="https://dev.jaquestech.usa.lakshan.intern.domedia.lk/?geo=US" 
           class="navbar-above-section-icon sg-country-link">USA</a>
    </li>
    <!-- Custom Link 2 - Direct to target with preference param -->
    <li>
        <a href="https://jacques.vs.domedia.uk/?geo=AU" 
           class="navbar-above-section-icon sg-country-link">UK</a>
    </li>
</ul>