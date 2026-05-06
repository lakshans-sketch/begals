/**
 * Ajax functions woocommerce plugin.
 */

"use strict";

// Ajax delete product in the cart
(function( $ ) {
    
    $(document.body).on('click', '.mini_cart_item a.remove', function (e) {    
        e.preventDefault();

        var product_id = $(this).attr("data-product_id"),
            cart_item_key = $(this).attr("data-cart_item_key"),
            product_container = $(this).parents('.mini_cart_item'),
            cart_total = $('.cart-totals .amount'),
            cart_count = $('.cart-totals .count'),
            container = $('.mini-cart-wrapper');

        // Add loader
        product_container.block({
            message: '<div class="cart-loader"></div>'
        });

        $.ajax({
            type: 'POST',
            dataType: 'json',
            url: wc_add_to_cart_params.ajax_url,
            data: {
                action: "product_remove",
                product_id: product_id,
                cart_item_key: cart_item_key
            },
            success: function(response) {

                if ( ! response || response.error )
                    return;
                var fragments = response.fragments;
                // Replace fragments
                if ( fragments ) {
                    $.each( fragments, function( key, value ) {
                        $( key ).replaceWith( value );
                    });
                    container.html(fragments['div.widget_shopping_cart_content']);
                }
                cart_total.html(response.cart_total);
                cart_count.html(response.cart_item_count);
            }
        });
    });
    
})( jQuery );