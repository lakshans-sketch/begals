<?php
/**
 * Template Name: Room Template
 * Template Post Type: page
 */


get_header();

// Optional header banner
if ( function_exists( 'page_header_banner' ) ) {
    echo page_header_banner();
}

// Get current page slug
$page_slug = get_post_field( 'post_name', get_post() );

// Sanitize slug (safe for DB query)
$category_slug = sanitize_title( $page_slug );

// Try to get the WooCommerce category term
$category = get_term_by( 'slug', $category_slug, 'product_cat' );

?>
<section class="room-products container">
    <?php
    if ( $category ) :
        // Query products assigned to this category
        $args = array(
            'post_type'      => 'product',
            'posts_per_page' => -1,
            'tax_query'      => array(
                array(
                    'taxonomy' => 'product_cat',
                    'field'    => 'slug',
                    'terms'    => $category_slug,
                ),
            ),  
        );

        $loop = new WP_Query( $args );

        if ( $loop->have_posts() ) :
            echo '<div class="products-grid">';
            while ( $loop->have_posts() ) : $loop->the_post();
                global $product; ?>
                <div class="product-item woocommerce-LoopProduct-link woocommerce-loop-product__link">
                    <a href="<?php the_permalink(); ?> ">
                        <div class="product-thumb">
                            <?php echo $product->get_image( 'woocommerce_thumbnail' ); ?>
                        </div>
                        <h3 class="product-title"><?php the_title(); ?></h3>
                        <span class="product-price"><?php echo $product->get_price_html(); ?></span>
                    </a>
                </div>
            <?php endwhile;
            echo '</div>';
        else :
            echo '<p>No products found for category: <strong>' . esc_html( $category_slug ) . '</strong>.</p>';
        endif;

        wp_reset_postdata();




    else :
        echo '<p><strong>No WooCommerce category found</strong> matching slug: <code>' . esc_html( $category_slug ) . '</code></p>';
    endif;
    ?>
</section>

<?php get_footer(); ?>
