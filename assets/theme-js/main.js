/**
 * Custom specific Theme functions file.
 */

"use strict";

(function( $ ) {

    // Initialize Owl Carousel for sales products
    $(document).ready(function() {
        if ($('.offer-products-wrapper.owl-carousel').length) {
            $('.offer-products-wrapper.owl-carousel').owlCarousel({
                loop: true,
                margin: 20,
                nav: true,
                dots: false,
                responsive: {
                    0: { items: 1 },
                    600: { items: 2 },
                    1000: { items: 3 }
                },
                navText: [
                    '<span class="owl-prev">&lt;</span>',
                    '<span class="owl-next">&gt;</span>'
                ]
            });
        }
    });

})( jQuery );
