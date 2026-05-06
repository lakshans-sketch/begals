<?php
/**
 * Displays mobile woocommerce search for mobile
 * Included in header.php
 */
?>

<div class="mobile-woo-search">
    <button type="submit" class="woo-search-closer"><i class="fal fa-times"></i></button>
    
    <div class="woo-search-box">
        <form action="<?php echo get_site_url() ?>" method="GET">
            <div class="search-group">
                <input type="search" name="s" placeholder="<?php _e( 'Search for products..', 'bagels' ); ?>" autofocus required class="fld-search">
                <input type="hidden" name="post_type" value="product">
                <button type="submit" class="btn-search"><i class="far fa-search"></i></button>
            </div>
        </form>
    </div>
</div>