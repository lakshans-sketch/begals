<?php
get_header();

// Get ACF group field
$hero_background = get_field('hero_section');
$video = $hero_background['landing_video'] ?? null;
?>


<section class="hero">
    <?php if ($video && isset($video['url'])): ?>
        <video class="hero-video" autoplay muted loop playsinline>
            <source src="<?php echo esc_url($video['url']); ?>" type="video/mp4">
            Your browser does not support the video tag.
        </video>
    <?php endif; ?>

    <div class="hero-overlay"></div>
    <div class="container page-container pg-home">
        <div class="hero-content">
            <h1>Sri Lankan Domain <img src="https://upload.wikimedia.org/wikipedia/commons/1/11/Flag_of_Sri_Lanka.svg" alt="Sri Lanka Flag" class="sl-flag" style="width:32px;height:auto;margin-left:8px;vertical-align:middle;"></h1>
            <h4>Discover Sri Lankan products, culture, and services.</h4>
            <p>Explore authentic Sri Lankan offerings — from local artisans and traditional crafts to modern
                services that celebrate our island's heritage. Learn about regional specialties, cultural highlights,
                and ways to support local businesses.</p>

            <button class="hero-btn">
                <a href="" class="hero-btn-link">Learn More</a>
            </button>
        </div>
    </div>
</section>

<section class="offer container">
    <h2 class="offer-heading">Featured Sri Lankan Products</h2>

    <?php
    $products_on_sale = wc_get_products([
        'limit'  => -1,
        'status' => 'publish',
        'sale'   => true,
    ]);

    if (!empty($products_on_sale)) : ?>
        <div class="offer-products-wrapper owl-carousel">
            <?php foreach ($products_on_sale as $product) :

                $regular_price = floatval($product->get_regular_price());
                $sale_price    = floatval($product->get_sale_price());

                if ($sale_price > 0 && $sale_price < $regular_price) : ?>
                    <div class="offer-product-card">
                        <!-- Product Image -->
                        <div class="product-image">
                            <a href="<?php echo esc_url($product->get_permalink()); ?>">
                                <?php echo $product->get_image('medium'); ?>
                            </a>
                        </div>

                        <!-- Product Info -->
                        <div class="product-info">
                            <h3 class="product-title">
                                <a href="<?php echo esc_url($product->get_permalink()); ?>">
                                    <?php echo esc_html($product->get_name()); ?>
                                </a>
                            </h3>

                            <!-- Prices -->
                            <div class="product-prices">
                                <span class="regular-price"><?php echo wc_price($regular_price); ?></span>
                                <span class="sale-price"><?php echo wc_price($sale_price); ?></span>
                            </div>

                            <!-- Stock Status -->
                            <!-- <div class="stock-status">
                                <?php echo wc_get_stock_html($product); ?>
                            </div> -->

                            <!-- Ratings -->
                            <!-- <?php if ($product->get_rating_count() > 0) : ?>
                                <div class="product-rating">
                                    <?php
                                    echo wc_get_rating_html($product->get_average_rating());
                                    echo ' (' . $product->get_rating_count() . ' review' . ($product->get_rating_count() > 1 ? 's' : '') . ')';
                                    ?>
                                </div>
                            <?php endif; ?> -->
                        </div>
                    </div>
            <?php
                endif;
            endforeach; ?>
        </div>
    <?php else : ?>
        <p class="no-products-message">No discounted products right now.</p>
    <?php endif; ?>
</section>







<?php get_footer(); ?>