<?php
/**
 * Color theme stylesheet according to the user defines
 * Included in footer.php
 */
?>

<?php
    ob_start();
    
    $links_color = BTS::$options['colors']['links_color'];
    $links_hover_color = BTS::$options['colors']['links_hover_color'];
    $bg_color = BTS::$options['colors']['bg_color'];
    $body_font_color = BTS::$options['colors']['body_font_color'];
    $button_primary_color = BTS::$options['colors']['button_primary_color'];
    $button_hover_color = BTS::$options['colors']['button_hover_color'];
    $button_text_color = BTS::$options['colors']['button_text_color'];
    $button_hover_text_color = BTS::$options['colors']['button_hover_text_color'];
?>

<style>
    /* Override framework.css */
    <?php if($body_font_color): ?>
        body,
        .woo-single-wrapper div.product form.cart .variations label,
        input[type="text"],
        input[type="email"],
        input[type="date"],
        input[type="tel"],
        input[type="url"],
        input[type="number"],
        input[type="file"],
        select,
        textarea{
            color: <?php echo $body_font_color; ?>;
        }
    <?php endif; ?>
    
    <?php if($links_color): ?>    
        a,
        .swiper-button-next,
        .swiper-button-prev{
            color: <?php echo $links_color; ?>;
        }
        
        .nav-search-field .input-group .input-group-btn .btn-default:hover{
            background-color: <?php echo $links_color; ?>;
            border-color: <?php echo $links_color; ?>;
        }
        
        .nav-search-field .input-group .input-group-btn .btn-default:hover{
            background-color: <?php echo $links_color; ?>;
            border-color: <?php echo $links_color; ?>;
        }
        
        .wpcf7 input[type="text"]:focus,
        .wpcf7 input[type="email"]:focus,
        .wpcf7 input[type="date"]:focus,
        .wpcf7 input[type="tel"]:focus,
        .wpcf7 input[type="url"]:focus,
        .wpcf7 input[type="number"]:focus,
        .wpcf7 input[type="file"]:focus,
        .wpcf7 textarea:focus,
        .wpcf7 select:focus{
            border-color: <?php echo $links_color; ?>;
        }
        
        .footer-cta-strip,
        .pagination-wrapper a.page-numbers:hover,
        .pagination-wrapper .current{
            background-color: <?php echo $links_color; ?>;
        }
        .footer-widget-box .widget-para-link,
        .footer-widget-box .widget-nav-list li.active a,
        .pagination-wrapper a.page-numbers,
        .pagination-wrapper .current,
        .pg-temp-contact .contact-group .cg-heading{
            color: <?php echo $links_color; ?>;
        }
        .footer-widget-box .widget-social-list li a:hover,
        .owl-dots .owl-dot span,
        .owl-dots .owl-dot.active span, .owl-dots .owl-dot:hover span{
            border-color: <?php echo $links_color; ?>;
        }
        .owl-dots .owl-dot.active span, .owl-dots .owl-dot:hover span,
        .header-ham-icon span{
            background: <?php echo $links_color; ?>;
        }
        
        .sidebar-search .search-group input[type="search"]:focus{
            outline-color: <?php echo $links_color; ?>;
        }
        
    <?php endif; ?>
        
    <?php if( $links_hover_color ): ?>     
        a:hover, 
        /* a:focus, */
        .contact-group .cg-text a:hover,
        .nav-cart.open .nav-cart-toggler,
        body.bagels-abtw-font-resized .bagels-accessibility-widget .abtw-t-i-s-link.bagels-abtw-increase-font-size,
        .bagels-accessibility-widget .abtw-t-i-s-link.selected,
        #site-header .nav-search-field .input-group .input-group-btn .btn-default:hover,
        #site-header .nav-search-field .input-group .input-group-btn .btn-default:focus,
        #site-header .nav-search-field .input-group .input-group-btn .btn-default:active,
        #site-header .nav-search-close:hover, 
        .mobile-woo-search .woo-search-closer:hover,
        li.product .yith-wcwl-add-to-wishlist.exists a i,
        body.single-product .summary .yith-wcwl-add-to-wishlist.exists .yith-wcwl-add-button a{ 
            color: <?php echo $links_hover_color; ?>;
        }

        @media (min-width: 992px){
            
            #site-header .navbar-nav>li>a:hover,
            #site-header .navbar-nav>li>a:focus,
            #site-header .navbar-nav>.active>a,
            #site-header .navbar-nav>.active>a:focus,
            #site-header .navbar-nav>.active>a:hover,
            #site-header .navbar-nav>.open>a,
            #site-header .navbar-nav>.open>a:focus,
            #site-header .navbar-nav>.open>a:hover,
            .nav-cart .nav-cart-toggler:hover,
            .swiper-button-next:hover,
            .swiper-button-prev:hover,
            .woocommerce ul.products li.product a:hover h2,
            .woocommerce ul.products li.product a:hover .price{
                color: <?php echo $links_hover_color; ?>;
            }
            
            .dropdown-menu>li>a:focus,
            .dropdown-menu>li>a:hover,
            .dropdown-menu>.active>a,
            .dropdown-menu>.active>a:focus,
            .dropdown-menu>.active>a:hover,
            .dropdown-menu > li.open > a{
                background-color: <?php echo $links_hover_color; ?>;
            }
        }

        @media (max-width: 991px){
            .navbar-inverse .navbar-nav>.active>a,
            .navbar-inverse .navbar-nav>.active>a:focus,
            .navbar-inverse .navbar-nav>.active>a:hover,
            .navbar-inverse .navbar-nav>.open>a:focus,
            .navbar-inverse .navbar-nav>.open>a:hover,
            .navbar-inverse .navbar-nav .open .dropdown-menu>.active>a,
            .navbar-inverse .navbar-nav .open .dropdown-menu>.active>a:focus,
            .navbar-inverse .navbar-nav .open .dropdown-menu>.active>a:hover,
            .navbar-inverse .current-menu-parent.dropdown > a,
            .navbar-default .navbar-nav>.active>a,
            .navbar-default .navbar-nav>.active>a:focus,
            .navbar-default .navbar-nav>.active>a:hover,
            .navbar-default .navbar-nav>.open>a:focus,
            .navbar-default .navbar-nav>.open>a:hover,
            .navbar-default .navbar-nav .open .dropdown-menu>.active>a,
            .navbar-default .navbar-nav .open .dropdown-menu>.active>a:focus,
            .navbar-default .navbar-nav .open .dropdown-menu>.active>a:hover,
            .navbar-default .current-menu-parent.dropdown > a,
            .sidebar-nav .navbar-nav>.active>a,
            .sidebar-nav .navbar-nav>.active>a:focus,
            .sidebar-nav .navbar-nav>.active>a:hover,
            .sidebar-nav .navbar-nav>.open>a:focus,
            .sidebar-nav .navbar-nav>.open>a:hover,
            .sidebar-nav .navbar-nav .open .dropdown-menu>.active>a,
            .sidebar-nav .navbar-nav .open .dropdown-menu>.active>a:focus,
            .sidebar-nav .navbar-nav .open .dropdown-menu>.active>a:hover,
            .sidebar-nav .current-menu-parent.dropdown > a,
            .nav-cart .nav-cart-toggler:active,
            .swiper-button-next:active,
            .swiper-button-prev:active,
            .woocommerce ul.products li.product a:active h2,
            .woocommerce ul.products li.product a:active .price{
                color: <?php echo $links_hover_color; ?>;
            }  
        }
    <?php endif; ?>
    
    <?php if($bg_color): ?>
        .woocommerce-message,
        .woocommerce-info,
        #yith-wcwl-popup-message{
            border-color: <?php echo $bg_color; ?>;
        }  
    <?php endif; ?>
    
    <?php if($button_primary_color): ?>
        .wpcf7 input[type="submit"],
        .post-comments .form-submit input[type="submit"],
        .blog-sidebar .btn-search,
        .bagels-news-letter-widget #nlw-f-b-submit,
        .bagels-accessibility-widget .abtw-tt-link,
        .woocommerce-MyAccount-content .woocommerce-Button,
        .woocommerce-page .woocommerce .button:not(.show-title-form),
        .woocommerce #respond input#submit.alt, .woocommerce a.button.alt,
        .woocommerce button.button.alt,
        .woocommerce input.button.alt,
        .mini-cart-wrapper .buttons a ,
        .hero-btn{
            background-color: <?php echo $button_primary_color; ?>;
        }

        .woocommerce:where(body:not(.woocommerce-block-theme-has-button-styles)) #respond input#submit.alt.disabled,
        .woocommerce:where(body:not(.woocommerce-block-theme-has-button-styles)) #respond input#submit.alt.disabled:hover,
        .woocommerce:where(body:not(.woocommerce-block-theme-has-button-styles)) #respond input#submit.alt:disabled,
        .woocommerce:where(body:not(.woocommerce-block-theme-has-button-styles)) #respond input#submit.alt:disabled:hover,
        .woocommerce:where(body:not(.woocommerce-block-theme-has-button-styles)) #respond input#submit.alt:disabled[disabled],
        .woocommerce:where(body:not(.woocommerce-block-theme-has-button-styles)) #respond input#submit.alt:disabled[disabled]:hover,
        .woocommerce:where(body:not(.woocommerce-block-theme-has-button-styles)) a.button.alt.disabled,
        .woocommerce:where(body:not(.woocommerce-block-theme-has-button-styles)) a.button.alt.disabled:hover,
        .woocommerce:where(body:not(.woocommerce-block-theme-has-button-styles)) a.button.alt:disabled,
        .woocommerce:where(body:not(.woocommerce-block-theme-has-button-styles)) a.button.alt:disabled:hover,
        .woocommerce:where(body:not(.woocommerce-block-theme-has-button-styles)) a.button.alt:disabled[disabled],
        .woocommerce:where(body:not(.woocommerce-block-theme-has-button-styles)) a.button.alt:disabled[disabled]:hover,
        .woocommerce:where(body:not(.woocommerce-block-theme-has-button-styles)) button.button.alt.disabled,
        .woocommerce:where(body:not(.woocommerce-block-theme-has-button-styles)) button.button.alt.disabled:hover,
        .woocommerce:where(body:not(.woocommerce-block-theme-has-button-styles)) button.button.alt:disabled,
        .woocommerce:where(body:not(.woocommerce-block-theme-has-button-styles)) button.button.alt:disabled:hover,
        .woocommerce:where(body:not(.woocommerce-block-theme-has-button-styles)) button.button.alt:disabled[disabled],
        .woocommerce:where(body:not(.woocommerce-block-theme-has-button-styles)) button.button.alt:disabled[disabled]:hover,
        .woocommerce:where(body:not(.woocommerce-block-theme-has-button-styles)) input.button.alt.disabled,
        .woocommerce:where(body:not(.woocommerce-block-theme-has-button-styles)) input.button.alt.disabled:hover,
        .woocommerce:where(body:not(.woocommerce-block-theme-has-button-styles)) input.button.alt:disabled,
        .woocommerce:where(body:not(.woocommerce-block-theme-has-button-styles)) input.button.alt:disabled:hover,
        .woocommerce:where(body:not(.woocommerce-block-theme-has-button-styles)) input.button.alt:disabled[disabled],
        .woocommerce:where(body:not(.woocommerce-block-theme-has-button-styles)) input.button.alt:disabled[disabled]:hover,
        :where(body:not(.woocommerce-block-theme-has-button-styles)) .woocommerce #respond input#submit.alt.disabled,
        :where(body:not(.woocommerce-block-theme-has-button-styles)) .woocommerce #respond input#submit.alt.disabled:hover,
        :where(body:not(.woocommerce-block-theme-has-button-styles)) .woocommerce #respond input#submit.alt:disabled,
        :where(body:not(.woocommerce-block-theme-has-button-styles)) .woocommerce #respond input#submit.alt:disabled:hover,
        :where(body:not(.woocommerce-block-theme-has-button-styles)) .woocommerce #respond input#submit.alt:disabled[disabled],
        :where(body:not(.woocommerce-block-theme-has-button-styles)) .woocommerce #respond input#submit.alt:disabled[disabled]:hover,
        :where(body:not(.woocommerce-block-theme-has-button-styles)) .woocommerce a.button.alt.disabled,
        :where(body:not(.woocommerce-block-theme-has-button-styles)) .woocommerce a.button.alt.disabled:hover,
        :where(body:not(.woocommerce-block-theme-has-button-styles)) .woocommerce a.button.alt:disabled,
        :where(body:not(.woocommerce-block-theme-has-button-styles)) .woocommerce a.button.alt:disabled:hover,
        :where(body:not(.woocommerce-block-theme-has-button-styles)) .woocommerce a.button.alt:disabled[disabled],
        :where(body:not(.woocommerce-block-theme-has-button-styles)) .woocommerce a.button.alt:disabled[disabled]:hover,
        :where(body:not(.woocommerce-block-theme-has-button-styles)) .woocommerce button.button.alt.disabled,
        :where(body:not(.woocommerce-block-theme-has-button-styles)) .woocommerce button.button.alt.disabled:hover,
        :where(body:not(.woocommerce-block-theme-has-button-styles)) .woocommerce button.button.alt:disabled,
        :where(body:not(.woocommerce-block-theme-has-button-styles)) .woocommerce button.button.alt:disabled:hover,
        :where(body:not(.woocommerce-block-theme-has-button-styles)) .woocommerce button.button.alt:disabled[disabled],
        :where(body:not(.woocommerce-block-theme-has-button-styles)) .woocommerce button.button.alt:disabled[disabled]:hover,
        :where(body:not(.woocommerce-block-theme-has-button-styles)) .woocommerce input.button.alt.disabled,
        :where(body:not(.woocommerce-block-theme-has-button-styles)) .woocommerce input.button.alt.disabled:hover,
        :where(body:not(.woocommerce-block-theme-has-button-styles)) .woocommerce input.button.alt:disabled,
        :where(body:not(.woocommerce-block-theme-has-button-styles)) .woocommerce input.button.alt:disabled:hover,
        :where(body:not(.woocommerce-block-theme-has-button-styles)) .woocommerce input.button.alt:disabled[disabled],
        :where(body:not(.woocommerce-block-theme-has-button-styles)) .woocommerce input.button.alt:disabled[disabled]:hover{
            background-color: <?php echo $button_primary_color; ?>;
        }
        
        .bagels-news-letter-widget #nlw-f-b-submit{
            border-color: <?php echo $button_primary_color; ?>;
        }
        
        .product .blockUI:after{
            border-color: <?php echo $button_primary_color; ?> <?php echo $button_primary_color; ?> <?php echo $button_primary_color; ?> transparent;
        }
    <?php endif; ?> 
        
    <?php if($button_hover_color): ?>     
        .wpcf7 input[type="submit"]:hover,
        .footer-cta-strip .bagels-theme-button a:hover,
        .post-comments .form-submit input[type="submit"]:hover{
            background-color: <?php echo $button_hover_color; ?>;
        }

        @media (min-width: 992px) {
            .blog-sidebar .btn-search:hover,
            .bagels-news-letter-widget #nlw-f-b-submit:hover,
            .bagels-accessibility-widget .abtw-tt-link:hover,
            .woocommerce-MyAccount-content .woocommerce-Button:hover,
            .woocommerce-page .woocommerce .button:not(.show-title-form):hover,
            .woocommerce #respond input#submit.alt, .woocommerce a.button.alt:hover,
            .woocommerce button.button.alt:hover,
            .woocommerce input.button.alt:hover,
            .mini-cart-wrapper .buttons a:hover,
            .hero-btn:hover{
                background: <?php echo $button_hover_color; ?>;
                border-color: <?php echo $button_hover_color; ?>;
            }
        
            .woocommerce-cart .actions > button:hover{
                background-color: <?php echo $button_hover_color; ?> !important;
            }
        }

        @media (max-width: 991px) {
            .blog-sidebar .btn-search:active,
            .bagels-news-letter-widget #nlw-f-b-submit:active,
            .bagels-accessibility-widget .abtw-tt-link:active,
            .woocommerce-MyAccount-content .woocommerce-Button:active,
            .woocommerce-page .woocommerce .button:not(.show-title-form):active,
            .woocommerce #respond input#submit.alt, .woocommerce a.button.alt:active,
            .woocommerce button.button.alt:active,
            .woocommerce input.button.alt:active,
            .mini-cart-wrapper .buttons a:active{
                background: <?php echo $button_hover_color; ?>;
                border-color: <?php echo $button_hover_color; ?>;
            }
        
            .woocommerce-cart .actions > button:active{
                background-color: <?php echo $button_hover_color; ?> !important;
            }
        }
    <?php endif; ?>
    
    <?php if($button_primary_color): ?>
        @media (min-width: 992px) {
            .woocommerce-cart .actions > button:disabled[disabled]:hover,
            .woocommerce-cart .actions > button:disabled:hover{
                background-color: <?php echo $button_primary_color; ?> !important;
            }
        }

        @media (max-width: 991px) {
            .woocommerce-cart .actions > button:disabled[disabled]:active,
            .woocommerce-cart .actions > button:disabled:active{
                background-color: <?php echo $button_primary_color; ?> !important;
            }
        }
    <?php endif; ?>

    /*  Override elements.css */
    <?php if($links_color): ?>  
        .vc-section-heading h1:after{
            border-color: <?php echo $links_color; ?>
        }
        .news-slider .news-box .news-image .date-box,
        .service-box .service-title,
        .vc-icon-box .icon-sec{
            background-color: <?php echo $links_color; ?>;
        }
        .news-slider .news-box .news-content .news-title:hover,
        .gallery-filter ul li:hover,
        .gallery-filter ul li.current,
        .vc-info-box .info-heading a:hover,
        .vc-social-list.actual ul li a:hover{
            color: <?php echo $links_color; ?>;
        }
        
        @media(max-width: 767px){
            .gallery-filter.dark ul li.current,
            .gallery-filter.light ul li.current{
                background-color: <?php echo $links_color; ?>;
            }
            .gallery-filter.dark ul li,
            .gallery-filter.light ul li{
                color: <?php echo $links_color; ?>;
            }
            .gallery-filter.dark ul li,
            .gallery-filter.light ul li.current{
                border-color: <?php echo $links_color; ?>;
            }
        }
    <?php endif; ?>
    
    <?php if($button_primary_color): ?>  
        .bagels-theme-button a,
        .wp-block-button__link,
        .woo-single-wrapper .cart .quantity-buttons{
            background-color: <?php echo $button_primary_color; ?>;
        }

        .woo-single-wrapper .cart .quantity-buttons,
        .woo-single-wrapper .cart .quantity input.qty{
            border-color: <?php echo $button_primary_color; ?>;
        }
    <?php endif; ?>
    
    <?php if($button_hover_color): ?>     
        /* .bagels-theme-button a:focus,
        .wp-block-button__link:focus,
        .woo-single-wrapper .cart .quantity-buttons:focus,
        .woo-single-wrapper .cart .single_add_to_cart_button:not(.disabled):focus{
            background-color: <?php echo $button_hover_color; ?>;
        } */

        .woocommerce #respond input#submit.alt:hover,
        .woocommerce a.button.alt:hover,
        .woocommerce button.button.alt:hover,
        .woocommerce input.button.alt:hover{
            background-color: <?php echo $button_hover_color; ?>;
        }
        
        @media (min-width: 992px) {
            .woo-single-wrapper .cart .quantity-buttons:hover,
            .woo-single-wrapper .cart .single_add_to_cart_button:not(.disabled):hover,
            .wp-block-button__link:hover,
            .bagels-theme-button a:hover{
                background-color: <?php echo $button_hover_color; ?>;
            }
            
            .woo-single-wrapper .cart .quantity-buttons:hover{
                border-color: <?php echo $button_hover_color; ?>;
            }
        }
        
        @media (max-width: 991px) {
            .woo-single-wrapper .cart .quantity-buttons:active,
            .woo-single-wrapper .cart .single_add_to_cart_button:not(.disabled):active,
            .wp-block-button__link:active,
            .bagels-theme-button a:active{
                background-color: <?php echo $button_hover_color; ?>;
            }

            .woo-single-wrapper .cart .quantity-buttons:active{
                border-color: <?php echo $button_hover_color; ?>;
            }
        }

    <?php endif; ?> 
        
        <?php if($button_text_color): ?>
            .bagels-theme-button a,
            .wp-block-button__link,
            .blog-sidebar .btn-search,
            .bagels-news-letter-widget #nlw-f-b-submit,
            .bagels-accessibility-widget .abtw-tt-link,
            .woocommerce-MyAccount-content .woocommerce-Button,
            .woocommerce-page .woocommerce .button:not(.show-title-form),
            .woocommerce #respond input#submit.alt, .woocommerce a.button.alt,
            .woocommerce button.button.alt,
            .woocommerce input.button.alt,
            .mini-cart-wrapper .buttons a,
            .woo-single-wrapper .cart .quantity-buttons{
                color: <?php echo $button_text_color; ?>;
            }

            .woocommerce-cart .actions > button:disabled[disabled],
            .woocommerce-cart .actions > button:disabled ,
            .hero-btn a{
                color: <?php echo $button_text_color; ?> !important;
            }
        <?php endif; ?>
        
        <?php if($button_hover_text_color): ?>
            @media (min-width: 992px) {
                .bagels-theme-button a:hover,
                .wp-block-button__link:hover,
                .blog-sidebar .btn-search:hover,
                .bagels-news-letter-widget #nlw-f-b-submit:hover,
                .bagels-accessibility-widget .abtw-tt-link:hover,
                .woocommerce-MyAccount-content .woocommerce-Button:hover,
                .woocommerce-page .woocommerce .button:not(.show-title-form):hover,
                .woocommerce #respond input#submit.alt, .woocommerce a.button.alt:hover,
                .woocommerce button.button.alt:hover,
                .woocommerce input.button.alt:hover,
                .mini-cart-wrapper .buttons a:hover,
                .woo-single-wrapper .cart .quantity-buttons:hover{
                    color: <?php echo $button_hover_text_color; ?>;
                }
            }
    
            @media (max-width: 991px) {
                .bagels-theme-button a:active,
                .wp-block-button__link:active,
                .blog-sidebar .btn-search:active,
                .bagels-news-letter-widget #nlw-f-b-submit:active,
                .bagels-accessibility-widget .abtw-tt-link:active,
                .woocommerce-MyAccount-content .woocommerce-Button:active,
                .woocommerce-page .woocommerce .button:not(.show-title-form):active,
                .woocommerce #respond input#submit.alt, .woocommerce a.button.alt:active,
                .woocommerce button.button.alt:active,
                .woocommerce input.button.alt:active,
                .mini-cart-wrapper .buttons a:active,
                .woo-single-wrapper .cart .quantity-buttons:active{
                    color: <?php echo $button_hover_text_color; ?>;
                }
            }
        <?php endif; ?>
        
    <?php if($body_font_color): ?>
        .testimonial-slider.dark .testi-box,
        .testimonial-slider.dark .owl-nav > div{ 
            color: <?php echo $body_font_color; ?>; 
        }
    <?php endif; ?>    
    
    
    /* Override blog.css */
    <?php if($links_color): ?>    
        .post-wrapper .post-content .post-link,
        .single-post .post-meta span,
        .single-post .post-meta span.post-author a,
        .single-post .tags a:hover,
        ul.list-border li a:hover,
        .comment-reply-link:hover,
        .comment-edit-link:hover{
            color: <?php echo $links_color; ?>;
        }
        .post-wrapper .post-header .post-date{
            background-color: <?php echo $links_color; ?>;
        }
        ul.pagination li.active a {
            background-color: <?php echo $links_color; ?>;
            border-color: <?php echo $links_color; ?>;
        }
        .line-bottom:after{
            border-color: <?php echo $links_color; ?>;
        }
    <?php endif; ?> 

    <?php if($links_hover_color): ?>     
        .post-wrapper .post-content .post-title a:hover,
        .post-wrapper .post-content .post-link:hover{ 
            color: <?php echo $links_hover_color; ?>;
        }
    <?php endif; ?>
        
    /* Override woocommerce.css */
    <?php if($body_font_color): ?>
        .woocommerce ul.products li.product h2{ 
            color: <?php echo $body_font_color; ?>; 
        }
    <?php endif; ?>
        
    <?php if($links_color): ?>   
        .woocommerce ul.products li.product h2:hover,
        .woocommerce ul.products li.product .price,
        ul.sidebar-list li span a:hover,
        ul.sidebar-list ul li span a:hover,
        .woocommerce-mini-cart li .cart-item-count .amount,
        .woocommerce-mini-cart li .cart-item-title:hover,
        .mini-cart-wrapper .total .amount{
            color: <?php echo $links_color; ?>;
        }
        .woocommerce span.onsale,
        .woocommerce ul.products li.product .button:hover,
        .woocommerce .woocommerce-MyAccount-navigation ul li.is-active,
        .mobile-cart-icon{
            background-color: <?php echo $links_color; ?>;
        }
    <?php endif; ?>  
        
    <?php if($button_primary_color): ?>  
        .woocommerce #respond input#submit.alt,
        .woocommerce a.button.alt,
        .woocommerce button.button.alt,
        .woocommerce input.button.alt,
        .mini-cart-wrapper .buttons a{ 
            background-color: <?php echo $button_primary_color; ?>;
        }
        
        @media (max-width: 991px) {
            .woocommerce button.button.alt:hover{
                background-color: <?php echo $button_primary_color; ?>;
            }
        }
    <?php endif; ?> 
        
    <?php if($button_hover_color): ?>     
        @media (min-width: 992px) {
            .woocommerce #respond input#submit.alt:hover,
            .woocommerce a.button.alt:hover,
            .woocommerce button.button.alt:hover,
            .woocommerce input.button.alt:hover,
            .mini-cart-wrapper .buttons a:hover{
                background-color: <?php echo $button_hover_color; ?>;
            }
        }

        @media (max-width: 991px) {
            .woocommerce #respond input#submit.alt:active,
            .woocommerce a.button.alt:active,
            .woocommerce button.button.alt:active,
            .woocommerce input.button.alt:active,
            .mini-cart-wrapper .buttons a:active{
                background-color: <?php echo $button_hover_color; ?>;
            }
        }
    <?php endif; ?>     
</style>

<?php
    $outputCss = ob_get_contents();
    ob_end_clean();
    echo minifyCss($outputCss);