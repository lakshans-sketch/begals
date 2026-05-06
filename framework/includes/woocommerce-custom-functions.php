<?php
/**
 * Custom Functions of the woocommerce plugin.
 * Included in functions.php
 */

// Add woocommerce support to theme
add_action('after_setup_theme', 'woocommerce_support');
function woocommerce_support()
{
    add_theme_support('woocommerce');
}

//Remove the Breadcrumb on the Shop Page
add_filter('woocommerce_before_main_content', 'remove_breadcrumbs_on_shop');
function remove_breadcrumbs_on_shop()
{
    if (is_shop()) {
        remove_action('woocommerce_before_main_content', 'woocommerce_breadcrumb', 20, 0);
    }
}

// Change the breadcrumb delimeter from '/' to '>'
add_filter('woocommerce_breadcrumb_defaults', 'jk_change_breadcrumb_delimiter');
function jk_change_breadcrumb_delimiter($defaults)
{
    $defaults['delimiter'] = ' <span class="breadcrumb-separator">></span> ';
    return $defaults;
}

// Change number or products per row
add_filter('loop_shop_columns', 'loop_columns');
if (!function_exists('loop_columns')) {
    function loop_columns()
    {
        return 4;
    }
}

// Remove the product rating display on product loops
remove_action('woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_rating', 5);

// Remove Upsells products in single product page ('You may also like...' section)
remove_action('woocommerce_after_single_product_summary', 'woocommerce_upsell_display', 15);

// Hide loop read more buttons for out of stock items 
if (!function_exists('woocommerce_template_loop_add_to_cart')) {
    function woocommerce_template_loop_add_to_cart()
    {
        global $product;
        if (!$product->is_in_stock() || !$product->is_purchasable())
            return;
    }
}

// Enabling gallery features
add_action('after_setup_theme', 'add_zoom_lightbox_theme_support', 99);
function add_zoom_lightbox_theme_support()
{
    add_theme_support('wc-product-gallery-zoom');
    add_theme_support('wc-product-gallery-lightbox');
    add_theme_support('wc-product-gallery-slider');
}

/** 
 * Filter WooCommerce Flexslider options - Add Navigation Arrows and remove gallery thumbnails
 */
add_filter('woocommerce_single_product_carousel_options', 'bagels_update_woo_flexslider_options');
function bagels_update_woo_flexslider_options($options)
{
    if (wp_is_mobile()) {
        $options['directionNav'] = true;
        $options['controlNav'] = false;
    }

    return $options;
}

//Shop page
// Filter products by attributes like size and color in shop pages
// add_filter( 'woocommerce_product_query_tax_query', 'filter_products_by_attributes', 10, 2 );
function filter_products_by_attributes($tax_query, $instance)
{
    $query_vars = array(
        'pa_color' => filter_input(INPUT_GET, 'color'),
        'pa_size' => filter_input(INPUT_GET, 'size')
    );

    foreach ($query_vars as $key => $value):
        if (!empty($value)) {
            $tax_query[] = array(
                'taxonomy' => $key,
                'field' => 'term_id',
                'terms' => $value,
                'operator' => 'IN'
            );
        }
    endforeach;

    return $tax_query;
}

// Filter products by meta fields like price in shop pages
// add_filter( 'woocommerce_product_query_meta_query', 'filter_products_by_meta_fields', 10, 2 );
function filter_products_by_meta_fields($meta_query, $instance)
{
    $query_vars = array(
        '_price' => filter_input(INPUT_GET, 'price')
    );

    if (!empty($query_vars['_price'])) {
        $price_arr = explode('-', $query_vars['_price']);

        if ($price_arr[1] === '*') {
            $price_val = $price_arr[0];
            $compare = '>=';
        } else {
            $price_val = $price_arr;
            $compare = 'BETWEEN';
        }

        $meta_query[] = array(
            'key' => '_price',
            'value' => $price_val,
            'compare' => $compare,
            'type' => 'DECIMAL'
        );
    }

    return $meta_query;
}

// add_filter( 'woocommerce_variable_sale_price_html', 'bagels_edit_variation_price_display', 10, 2 );
function bagels_edit_variation_price_display($price, $product)
{
    if ((is_product_category() || is_shop()) && $product->is_type('variable')) {
        // Main Price
        $prices = array($product->get_variation_price('min', true), $product->get_variation_price('max', true));
        $price = $prices[0] !== $prices[1] ? sprintf(__('%1$s', 'woocommerce'), wc_price($prices[0])) : wc_price($prices[0]);
        // Sale Price
        $prices = array($product->get_variation_regular_price('min', true), $product->get_variation_regular_price('max', true));
        sort($prices);
        $saleprice = $prices[0] !== $prices[1] ? sprintf(__('%1$s', 'woocommerce'), wc_price($prices[0])) : wc_price($prices[0]);

        if ($price !== $saleprice) {
            $price = '<del>' . $saleprice . '</del> <ins>' . $price . '</ins>';
        }
        return $price;
    }
}
//Shop page

/*
 * Products single page
 */
//Adds content before add to cart button
add_action('woocommerce_before_add_to_cart_button', 'bagels_add_content_before_add_to_cart_button');
function bagels_add_content_before_add_to_cart_button()
{
    echo '<h4 class="quantity-label">' . __("Quantity") . '</h4>';
}

// Remove the Product SKU from Product Single Page
add_filter('wc_product_sku_enabled', 'bagels_remove_product_sku');
function bagels_remove_product_sku($sku)
{
    if (!is_admin() && is_product()) {
        return false;
    }
    return $sku;
}

add_action('woocommerce_single_product_summary', 'bagels_single_product_summary_hook_actions', 2);
function bagels_single_product_summary_hook_actions()
{
    // Remove the Product meta from Product Single Page
    remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_meta', 40);

    // Remove product description from single product page
    remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_excerpt', 20);
}


//Plus quantity button
add_action('woocommerce_before_add_to_cart_quantity', 'bagels_display_quantity_minus');
function bagels_display_quantity_minus()
{
    echo '<div class="quantity-field-wrapper">';
    echo '<button type="button" class="minus quantity-buttons" ><i class="fa-regular fa-minus"></i></button>';
}

//Minus quantity button
add_action('woocommerce_after_add_to_cart_quantity', 'bagels_display_quantity_plus');
function bagels_display_quantity_plus()
{
    echo '<button type="button" class="plus quantity-buttons" ><i class="fa-regular fa-plus"></i></button>';
    echo '</div>';
}

//Change dimensions of product page gallery images
add_filter('woocommerce_get_image_size_gallery_thumbnail', function ($size) {
    return array(
        'width' => '100',
        'height' => '150',
        'crop' => 0
    );
});

// Add size guide html to footer
add_action('wp_footer', 'bagels_add_size_guide_to_footer');
function bagels_add_size_guide_to_footer()
{
    if (is_product()) {
        // Size guide popup
        global $product;
        $taxonomy = 'product_cat';
        $taxonomies = wp_get_post_terms($product->get_id(), 'product_cat', array('fields' => 'all'));
        $parent_tax_arrays_array = array(); //Array of ancestor taxonomy arrays of product taxonomies

        //Checks for image in current category
        foreach ($taxonomies as $value) {
            $parent_taxes = get_ancestors($value->term_id, $taxonomy); //Gets Ancestor taxonomies of current taxonomy
            if (!empty($parent_taxes)) {
                array_push($parent_tax_arrays_array, $parent_taxes);
            }

            $size_chart = get_field('size_chart_image', $taxonomy . '_' . $value->term_id);

            if (!empty($size_chart)) {
                bagels_generate_size_guide_content($size_chart);
                return;
            }
        }

        // Checks for image in parent categories of detected categories if detected categories have no image
        if (!empty($parent_tax_arrays_array)) {
            foreach ($parent_tax_arrays_array as $key => $parent_tax_array) {
                if (!empty($parent_tax_array)) {
                    foreach ($parent_tax_array as $key => $parent_tax) {
                        $size_chart = get_field('size_chart_image', $taxonomy . '_' . $parent_tax);

                        if ($size_chart) {
                            bagels_generate_size_guide_content($size_chart);
                            return;
                        }
                    }
                }
            }
        }
    }
}

//Generates popup link for the size guide image
function bagels_generate_size_guide_content($image_array)
{
    if (!empty($image_array['sizes']['large'])) {
        printf(
            '<div class="ps-pop-size-guide" id="ps-pop-size-guide"><img src="%1$s" alt="%2$s" class="ps-psg-image" /></div>',
            esc_url($image_array['sizes']['large']),
            esc_attr($image_array['alt'])
        );

        echo '<script>(function( $ ) { $(".ps-size-guide").removeClass("hide"); })( jQuery ); </script>';
    }
}

//Change Add to cart button text
add_filter('woocommerce_product_single_add_to_cart_text', 'bagels_change_add_to_cart_text');
function bagels_change_add_to_cart_text()
{
    $button_text = "Add To Cart";

    if (!empty(BTS::$options['products_group']['single_page']['add_to_cart_button_text'])) {
        $button_text = BTS::$options['products_group']['single_page']['add_to_cart_button_text'];
    }

    _e($button_text);
}

//Add Sharing options
add_action('woocommerce_share', function () {
    echo '<div class="ps-social-sharing clearfix"><div class="ps-ss-label"><i class="fa-regular fa-share-nodes"></i><span class="pss-heading">Share:</span></div>';
    echo do_shortcode('[addtoany]');
    echo '</div>';
}, 10, 2);

/**
 * Change number of product gallery thumbnails per row
 */
add_filter('woocommerce_single_product_image_gallery_classes', 'bagels_change_product_gallery_img_count');
function bagels_change_product_gallery_img_count($wrapper_classes)
{
    $wrapper_classes[2] = 'woocommerce-product-gallery--columns-' . absint(6);
    return $wrapper_classes;
}

if (!function_exists('yith_wcwl_custom_remove_from_wishlist_label')) {
    add_filter('yith_wcwl_remove_from_wishlist_label', 'yith_wcwl_custom_remove_from_wishlist_label');
    function yith_wcwl_custom_remove_from_wishlist_label($label)
    {
        return 'REMOVE FROM WISH LIST';
    }
}

//Remove product tab headings
add_filter('woocommerce_product_description_heading', '__return_null');
add_filter('woocommerce_product_reviews_heading', '__return_null');

//Remove product tabs
add_filter('woocommerce_product_tabs', 'woo_remove_product_tabs', 98);
function woo_remove_product_tabs($tabs)
{
    unset($tabs['additional_information']);   // Remove the additional information tab
    return $tabs;
}

//Add Shipping and Returns product data tab
add_filter('woocommerce_product_tabs', 'bagels_add_product_tab');
function bagels_add_product_tab($tabs)
{
    $tabs['shipping_policy'] = array(
        'title' => __('Shipping & Returns', 'woocommerce'),
        'priority' => 50,
        'callback' => 'bagels_shipping_tab_content'
    );

    return $tabs;
}

// Shipping and Returns tab content
function bagels_shipping_tab_content()
{
    global $product;
    $shipping_info = get_field('shipping_details', $product->get_id());

    /**
     * Checks for and prints content from single product page Shipping Details
     */
    if (!empty($shipping_info)) {
        echo $shipping_info;
    }

    /**
     * Checks for and prints content from site settings Shipping Details
     */ elseif (!empty(BTS::$options['products_group']['single_page']['shipping_n_return_policy'])) {
        echo nl2br(BTS::$options['products_group']['single_page']['shipping_n_return_policy']);
    }
}

//Woocommerce customizer
add_action('customize_register', 'bagels_customizer_panel');
function bagels_customizer_panel($wp_customize)
{
    $wp_customize->add_section('woo_general', array(
        'title' => __('General Settings', 'woocommerce'),
        'panel' => 'woocommerce',
        'description' => __('General Settings', 'woocommerce'),
    ));

    // Shipping policy summary
    $wp_customize->add_setting('shipping_policy', array(
        'transport' => 'postMessage'
    ));
    $wp_customize->add_control('shipping_policy', array(
        'label' => __('Shipping & Return policy summary', 'woocommerce'),
        'type' => 'textarea',
        'section' => 'woo_general',
    ));

    // Sorting options
    $wp_customize->add_setting('woo_sorting_prices', array(
        'transport' => 'postMessage'
    ));
    $wp_customize->add_control('woo_sorting_prices', array(
        'label' => __('Price sorting options', 'woocommerce'),
        'type' => 'textarea',
        'section' => 'woocommerce_product_catalog',
        'description' => __('Insert a JSON of price sorting key values. If you don\'t know how to edit this field please ask the help of a developer', 'woocommerce'),
    ));
}


// Add "Go to Checkout" button below Add to Cart
add_action('woocommerce_single_product_summary', 'bagels_checkout_button_after_add_to_cart', 40);
function bagels_checkout_button_after_add_to_cart()
{
    global $product;

    // Build a direct add-to-cart link that redirects to checkout
    $checkout_url = wc_get_checkout_url();
    $buy_now_url = esc_url(add_query_arg(
        array(
            'add-to-cart' => $product->get_id(),
            'quantity' => 1,
            'redirect' => 'checkout'
        ),
        wc_get_checkout_url()
    ));

    echo '<a href="' . $buy_now_url . '" 
             class="single_add_to_cart_button button alt" 
             style="margin-top:10px; display:inline-block;">
             Go to Checkout
          </a>';
}

// Add payment methods image below the checkout button
add_action('woocommerce_single_product_summary', 'bagels_payment_methods_image', 45);
function bagels_payment_methods_image()
{
    echo '<div class="payment-methods" style="margin:10px 0; ">
            <img src="https://lavivente.lk/wp-content/uploads/2022/07/payment-methods-v4-300x42.png.webp" 
                 alt="Accepted Payment Methods" 
                 style="max-width:230px; height:auto;">
          </div>';
}



/**
 * Change the add to cart message text
 */
add_filter('wc_add_to_cart_message_html', 'bagels_edit_wc_add_to_cart_message', 10, 2);
function bagels_edit_wc_add_to_cart_message($message, $product_id)
{
    $message = "<span class='bagels-prod-added-msg'>" . $message . "</span>";
    return $message;
}

/**
 * Change the location of the wishlist icon on single product page
 */
// add_action( 'woocommerce_after_add_to_cart_form', 'bagels_change_wishlist_icon_position_woo_single');
function bagels_change_wishlist_icon_position_woo_single()
{
    echo do_shortcode('[yith_wcwl_add_to_wishlist]');
}

/**
 * Show product short description on single product page
 */

// Commented because of the dont want short desc to single product page 
// add_action('woocommerce_after_add_to_cart_form', 'bagels_display_short_description'); 
function bagels_display_short_description()
{
    global $post;
    $short_description = apply_filters('woocommerce_short_description', $post->post_excerpt);
    if (!$short_description) {
        return;
    }

    echo '<div class="woocommerce-product-details__short-description">' . $short_description . '</div>';
}
//Products single page

//Products search page
remove_action('woocommerce_no_products_found', 'wc_no_products_found', 10);
add_action('woocommerce_no_products_found', 'bagels_edit_no_products_found_message', 10, 1);
function bagels_edit_no_products_found_message($wc_no_products_found)
{
    echo '<div class="no-prods-message h-p-ul-m-0">
        <h3 class="npm-heading">' . __("Couldn't find any products.", 'bagels') . '</h3>
        <div class="bagels-theme-button npm-button">
            <a href="' . get_permalink(woocommerce_get_page_id('shop')) . '" class="" aria-label="' . __("Browse for more products", 'bagels') . '">
                ' . __("Browse For More Products", 'bagels') . '
            </a>
        </div>
    </div>';
}
;
//Products search page

// Change number of related products output
// add_filter( 'woocommerce_output_related_products_args', 'jk_related_products_args' );
function jk_related_products_args($args)
{
    $args['posts_per_page'] = 6; // # of related products
    $args['columns'] = 6; // # of columns per row
    return $args;
}

add_filter('woocommerce_output_related_products_args', 'bagels_related_products_args');
function bagels_related_products_args($args)
{
    if (!empty(BTS::$options['products_group']['single_page']['related_products']['display_style']) && BTS::$options['products_group']['single_page']['related_products']['display_style'] === 'gallery') {
        $args['posts_per_page'] = bagels_get_related_list_post_count();
        $args['columns'] = 0;
        $args['orderby'] = 'date';

        // Disable posts shuffle on product single related products section
        add_filter('woocommerce_product_related_posts_shuffle', '__return_false');
    }

    return $args;
}

/**
 * Gets the total number of posts shown in the related section of products single page
 * @return int $post_count: number of posts
 */
function bagels_get_related_list_post_count()
{
    return 4;
}

/**
 * Change number of products that are displayed per page (shop page)
 */
add_filter('loop_shop_per_page', 'bagels_loop_shop_per_page', 20);
function bagels_loop_shop_per_page($cols)
{
    // $cols contains the current number of products per page based on the value stored on Options –> Reading
    // Returns the number of products shown per page.
    $cols = 40;
    return $cols;
}

/**
 * Gets the total number of posts belonging to a given post type
 * @param array $info: information for querying
 * @return int $post_count: number of posts
 */
function bagels_get_total_post_count($info = array())
{
    $count = 0;

    if (!empty($info['post_type']) && gettype($info['post_type']) === 'string') {
        $args = array(
            'post_type' => $info['post_type'],
            'post_status' => 'publish',
            'posts_per_page' => -1,
            'fields' => 'ids',
        );

        // if ( !empty( $info[ 'product_cat' ] ) ) { $args[ 'product_cat' ] = $info[ 'product_cat' ]; }

        if (!empty($info['meta_query'])) {
            $args['meta_query'] = $info['meta_query'];
        }

        if (!empty($info['tax_query'])) {
            $args['tax_query'] = $info['tax_query'];
        }

        if (!empty($info['post__not_in'])) {
            $args['post__not_in'] = $info['post__not_in'];
        }

        $total_posts = new WP_Query($args);
        if ($total_posts->have_posts()) {
            $count = $total_posts->post_count;
        }
    }

    return $count;
}

/**
 * Returns posts boxes markup of a given post type through ajax.
 */
add_action('wp_ajax_nopriv_bingo_get_post_boxes_via_ajax', 'bingo_get_post_boxes_via_ajax');
add_action('wp_ajax_bingo_get_post_boxes_via_ajax', 'bingo_get_post_boxes_via_ajax');
function bingo_get_post_boxes_via_ajax()
{
    $res_data = array(
        'statusMsg' => 'Internal error.',
        'data' => array(
            'posts' => '',
        ),
    );

    if (!empty($_POST['post_type']) && !empty($_POST['cat_name'])) {
        $count = !empty($_POST['count']) && is_numeric($_POST['count']) ? (int) $_POST['count'] : 4;
        $request_count = !empty($_POST['request_count']) && is_numeric($_POST['request_count']) ? (int) $_POST['request_count'] : 0;
        $offset = $request_count * $count;

        $args = array(
            'post_type' => $_POST['post_type'],
            'post_status' => 'publish',
            'no_found_rows' => true,
            'orderby' => 'date',
        );

        if (!empty($_POST['cat_name']) && !empty(gettype($_POST['cat_name'] === 'string')) && !empty($_POST['tax_name'])) {
            $cat_names_arr = $_POST['cat_name'];

            if (gettype($cat_names_arr) === 'array') {
                $args['tax_query'] = array(
                    array(
                        'taxonomy' => $_POST['tax_name'],
                        'field' => 'slug',
                        'terms' => $cat_names_arr,
                        'operator' => 'IN',
                    ),
                );
            }
        }

        if (!empty($_POST['excluded_posts']) && gettype($_POST['excluded_posts']) === 'array') {
            $args['post__not_in'] = $_POST['excluded_posts'];
        }

        $total_posts_arguments = $args;
        $args['posts_per_page'] = $count;
        $args['offset'] = $offset;

        $total_posts_count = bagels_get_total_post_count($total_posts_arguments);
        $posts = new WP_Query($args);

        if ($posts->have_posts()) {
            $res_data['statusMsg'] = 'Posts found.';
            $res_data['data']['noMorePosts'] = $total_posts_count <= ($offset + $count);

            while ($posts->have_posts()):
                $posts->the_post();
                global $product;
                ob_start();
                wc_get_template_part('content', 'product', 90);
                $res_data['data']['posts'] .= ob_get_clean();
            endwhile;
        } else {
            $res_data['statusMsg'] = 'No posts found.';
        }

        wp_send_json_success($res_data, 200);
        wp_die();
    } else {
        $res_data['statusMsg'] = 'Post type undefined.';
    }

    wp_send_json_error($res_data, 202);
    wp_die();
}

// Remove product in the cart using ajax
function warp_ajax_product_remove()
{

    // Get mini cart
    ob_start();

    foreach (WC()->cart->get_cart() as $cart_item_key => $cart_item) {
        if ($cart_item['product_id'] == $_POST['product_id'] && $cart_item_key == $_POST['cart_item_key']) {
            WC()->cart->remove_cart_item($cart_item_key);
        }
    }

    WC()->cart->calculate_totals();
    WC()->cart->maybe_set_cart_cookies();

    woocommerce_mini_cart();

    $mini_cart = ob_get_clean();

    // Fragments and mini cart are returned
    $data = array(
        'fragments' => apply_filters(
            'woocommerce_add_to_cart_fragments',
            array(
                'div.widget_shopping_cart_content' => $mini_cart,
            )
        ),
        'cart_total' => WC()->cart->get_cart_subtotal(),
        'cart_item_count' => WC()->cart->get_cart_contents_count(),
        'cart_hash' => apply_filters('woocommerce_add_to_cart_hash', WC()->cart->get_cart_for_session() ? md5(json_encode(WC()->cart->get_cart_for_session())) : '', WC()->cart->get_cart_for_session())
    );

    wp_send_json($data);

    wp_die();
}

add_action('wp_ajax_product_remove', 'warp_ajax_product_remove');
add_action('wp_ajax_nopriv_product_remove', 'warp_ajax_product_remove');


// update mini cart when products added via AJAX
add_filter('woocommerce_add_to_cart_fragments', 'update_cart_details_fragments');
function update_cart_details_fragments($fragments)
{

    ob_start();
    woocommerce_mini_cart();
    $mini_cart = ob_get_clean();

    $cartTotal = WC()->cart->get_cart_subtotal();
    $cartContentsCounts = WC()->cart->get_cart_contents_count();

    // Original Code : framework/template-parts/header/mini-cart-popup.php
    $fragments['div.cart-totals'] = '
        <div class="cart-totals">
            <!-- <span class="amount">' . $cartTotal . '</span> -->
            <span class="count">' . $cartContentsCounts . '</span>
        </div>
    ';

    // Original Code : framework/template-parts/header/mini-cart-popup.php, sidebar-cart.php
    $fragments['div.mini-cart-wrapper'] = '<div class="mini-cart-wrapper">' . $mini_cart . '</div>';

    // Original Code : framework/template-parts/navigation/nav.php
    $fragments['span.cart-count-bubble'] = '<span class="cart-count-bubble">' . $cartContentsCounts . '</span>';

    return $fragments;
}

// Woocommerce scripts enques
add_action('wp_enqueue_scripts', 'ajax_woocommerce_enqueues');
function ajax_woocommerce_enqueues()
{
    wp_enqueue_script('ajax-script-woo', get_stylesheet_directory_uri() . '/framework/assets/js/woocommerce-ajax.js', array('jquery'), '1.0.0', true);
}

/**
 * Adds custom class to related products in the single product page to facilitate Swiper carousel
 */
add_action('woocommerce_after_single_product_summary', 'bagels_add_classes_to_posts', 10);
function bagels_add_classes_to_posts()
{
    add_filter('post_class', function ($classes, $class, $product_id) {
        $classes = array_merge(['swiper-slide'], $classes);
        return $classes;
    }, 10, 3);
}

/**
 * Removes the sorting options from shop page
 */
remove_action('woocommerce_before_shop_loop', 'woocommerce_catalog_ordering', 30);

/**
 * Removes the results count from shop page
 */
remove_action('woocommerce_before_shop_loop', 'woocommerce_result_count', 20);

/**
 * Removes breadcrumbs from single product page
 */
remove_action('woocommerce_before_main_content', 'woocommerce_breadcrumb', 20);

/**
 * Change currency symbol
 */
add_filter('woocommerce_currency_symbol', 'bagels_change_currency_symbol', 10, 2);
function bagels_change_currency_symbol($currency_symbol, $currency)
{
    if ($currency === 'LKR') {
        $currency_symbol = 'LKR ';
    }
    return $currency_symbol;
}

/*
 * Wishlist events
 */
//Commented because before wishlist remova;
// add_action('woocommerce_before_add_to_cart_form', 'bagels_display_add_to_wishlist_option');
// function bagels_display_add_to_wishlist_option(){
//     echo do_shortcode('[yith_wcwl_add_to_wishlist]');
// }

/*
 *Defines shortcode to dispay wihlist count on page load
 */
if (defined('YITH_WCWL') && !function_exists('yith_wcwl_get_items_count')) {
    function yith_wcwl_get_items_count()
    {
        $wish_count = yith_wcwl_count_all_products();

        ob_start(); ?>
                        <span class="yith-wcwl-items-count">
                            <i class="yith-wcwl-icon"><?php echo esc_html($wish_count); ?></i>
                        </span>
                        <?php
                        return ob_get_clean();
    }

    add_shortcode('yith_wcwl_items_count', 'yith_wcwl_get_items_count');
}

/*
 *Dynamically updates the wishlist count
 */
if (defined('YITH_WCWL') && !function_exists('yith_wcwl_ajax_update_count')) {
    function yith_wcwl_ajax_update_count()
    {
        wp_send_json(array(
            'count' => yith_wcwl_count_all_products()
        ));
    }

    add_action('wp_ajax_yith_wcwl_update_wishlist_count', 'yith_wcwl_ajax_update_count');
    add_action('wp_ajax_nopriv_yith_wcwl_update_wishlist_count', 'yith_wcwl_ajax_update_count');
}

if (defined('YITH_WCWL') && !function_exists('yith_wcwl_enqueue_custom_script')) {
    function yith_wcwl_enqueue_custom_script()
    {
        wp_add_inline_script(
            'jquery-yith-wcwl',
            "jQuery( function( $ ) {
                $( document ).on( 'added_to_wishlist removed_from_wishlist', function() {
                    $.get( yith_wcwl_l10n.ajax_url, {
                        action: 'yith_wcwl_update_wishlist_count'
                    }, function( data ) {
                        $('.yith-wcwl-items-count').children('i').html( data.count );

                        if(data.count > 0){
                            $('.wish-item-count').css({'display': 'flex'});
                        }else{
                            $('.wish-item-count').hide();
                        }
                    });
                } );
            } );"
        );
    }

    add_action('wp_enqueue_scripts', 'yith_wcwl_enqueue_custom_script', 20);
}

/**
 * Removes the wishlist page title
 */
if (!function_exists('bagels_disable_wishlist_page_title')) {
    add_filter('yith_wcwl_wishlist_params', 'bagels_disable_wishlist_page_title');
    function bagels_disable_wishlist_page_title($params)
    {
        $params['page_title'] = '';
        return $params;
    }
}

/* WooCommerce Show Product Image on Checkout Page */
add_filter('woocommerce_cart_item_name', 'do_lv_add_product_image_on_checkout_page', 10, 3);
function do_lv_add_product_image_on_checkout_page($name, $cart_item, $cart_item_key)
{
    if (!is_checkout()) {
        return $name;
    }

    $product = apply_filters('woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key);

    $thumbnail = $product->get_image(array(30, 45));

    // Get product attributes
    $attributes = $product->get_attributes();
    $attr_text = [];
    foreach ($attributes as $attribute) {
        $label = wc_attribute_label($attribute->get_name()); // Attribute name
        $values = implode(', ', $attribute->get_options()); // Attribute values
        $attr_text[] = "$values";
    }

    $output = '<div class="td-product-content">' .
        $thumbnail .
        '<div class="description">
            <div class="name">' . $product->get_title() . ' x ' . $cart_item['quantity'] . '</div>';

    if (!empty($attr_text)) {
        $output .= '<div class="attributes">Variation: ' . implode(', ', $attr_text) . '</div>';
    }

    $output .= '</div></div>';

    return $output;
}


// Disable checkout fields
add_filter('woocommerce_checkout_fields', 'bagels_remove_checkout_fields');
function bagels_remove_checkout_fields($fields)
{
    unset($fields['billing']['billing_company']);
    unset($fields['shipping']['shipping_company']);

    if (!empty(BTS::$options['products_group']['checkout_page']) && isset(BTS::$options['products_group']['checkout_page']['postcode_visibility']) && !BTS::$options['products_group']['checkout_page']['postcode_visibility']) {
        unset($fields['billing']['billing_postcode']);
        unset($fields['shipping']['shipping_postcode']);
    }

    $fields['billing']['billing_birthday'] = array(
        'type' => 'date',
        'label' => 'Date of Birth',
        'required' => true,
        'class' => array('form-row-wide'), // aligns nicely beside another field {form-row-firt for the wide input feild}
        'priority' => 26,
    );

    // Add custom "Age" field under Billing section
    // $fields['billing']['billing_age'] = array(
    //     'type'        => 'number',
    //     'label'       => 'Age',
    //     'required'    => true,
    //     'class'       => array( 'form-row-wide' ), 
    //     'clear'       => false,
    //     'priority'    => 26, 
    //     'custom_attributes' => array(
    //         'min' => '1',
    //         'max' => '120',
    //     ),
    // );



    return $fields;
}



add_action( 'woocommerce_checkout_update_order_meta', 'bagels_save_birthday_field' );
function bagels_save_birthday_field( $order_id ) {
    if ( ! empty( $_POST['billing_birthday'] ) ) {
        update_post_meta( $order_id, '_billing_birthday', sanitize_text_field( $_POST['billing_birthday'] ) );
    }
}

add_action( 'woocommerce_admin_order_data_after_billing_address', 'bagels_display_birthday_in_admin', 10, 1 );
function bagels_display_birthday_in_admin( $order ){
    $birthday = get_post_meta( $order->get_id(), '_billing_birthday', true );
    if ( $birthday ) {
        echo '<p><strong>Date of Birth:</strong> ' . esc_html( $birthday ) . '</p>';
    }
}

/**
 * Replaces single product page's product price range with that of the selected variation
 * This includes js that updates the variation price as well
 * Note: The js works only for the default html of the related elements
 */
add_action('woocommerce_variable_add_to_cart', 'bagels_replace_price_range_with_variation_price');
function bagels_replace_price_range_with_variation_price()
{
    global $product;
    $price = $product->get_price_html();

    wc_enqueue_js("     
        $(document).on('found_variation', 'form.cart', function( event, variation ) {   
            if(variation.price_html) $('.summary > p.price').html(variation.price_html);
            $('.woocommerce-variation-price').hide();
        });

        $(document).on('hide_variation', 'form.cart', function( event, variation ) {   
            $('.summary > p.price').html('" . $price . "');
        });
    ");
}