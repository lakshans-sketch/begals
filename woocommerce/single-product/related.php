<?php
/**
 * Related Products
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/related.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see         https://docs.woocommerce.com/document/template-structure/
 * @package     WooCommerce\Templates
 * @version     3.9.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( $related_products ) : ?>
    <section class="related products">
        <?php $heading = apply_filters( 'woocommerce_product_related_products_heading', __( 'Related products', 'woocommerce' ) ); ?>
        <?php if ( $heading ): ?><h2><?php esc_html_e( $heading, 'woocommerce' ); ?></h2><?php endif; ?>

        <div class="related-products-slider">
            <?php woocommerce_product_loop_start(); ?>
                <div class="bagles-swiper-wrapper">
                    <div class="swiper">
                        <div class="swiper-wrapper">
                            <?php foreach ( $related_products as $related_product ) : ?>
                                <?php
                                    $post_object = get_post( $related_product->get_id() );
                                    setup_postdata( $GLOBALS['post'] =& $post_object );
                                    wc_get_template_part( 'content', 'product' );
                                ?>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <a href="#" class="swiper-button-prev rps-prev" aria-label="<?php _e( "Go to previous slide" . get_the_title(), 'bagels' ); ?>"></a>
                    <a href="#" class="swiper-button-next rps-next" aria-label="<?php _e( "Go to next slide" . get_the_title(), 'bagels' ); ?>"></a>
                </div>
            <?php woocommerce_product_loop_end(); ?>
        </div>
    </section>
<?php endif;

wp_reset_postdata();