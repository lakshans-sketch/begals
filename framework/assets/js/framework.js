/**
 * Theme functions file.
 * Contains main scripts regards to this theme.
 */

"use strict";

(function( $ ) {
    
    // Page loader
    $(window).load(function() {
        $(".page-loader").fadeOut("medium");
    });

/* ---------------------------------
    Scroll To Top
   --------------------------------- */
    function scrollToTop() {
        $(window).scroll(function () {
            if ($(this).scrollTop() > 500) {
                $('#scroll-top').fadeIn();
            } else {
                $('#scroll-top').fadeOut();
            }
        });

        $('#scroll-top').click(function () {
            $('html, body').animate({scrollTop: 0}, 800);
            return false;
        });
    }
    
/* ---------------------------------
    Header menu drop down trigger
    --------------------------------- */
    function triggerHeaderDropdownsOnHover(){
        let dropdownMenuLI = $('#main-navbar .navbar-nav .menu-item.menu-item-has-children');

        if (dropdownMenuLI.length) {
            let dropdownMenuLink = dropdownMenuLI.find('.dropdown-toggle');
    
            if (dropdownMenuLink.length) {
                dropdownMenuLink.on('mouseover', function(){
                    let parentLi= $(this).parent('.menu-item-has-children');
                    if (parentLi.length) { parentLi.addClass('open'); }
                });
        
                dropdownMenuLink.on('mouseleave', function(){
                    let thisObj = $(this);
                    let parentLI = thisObj.parent('.menu-item-has-children');
                    
                    if (parentLI.length) {
                        let parentSiblingLinks = thisObj.parent('.menu-item-has-children').siblings('.menu-item-has-children').find('.dropdown-toggle');
                        let siblingMenu = thisObj.siblings('.dropdown-menu');
        
                        if (parentSiblingLinks.length) {
                            // Hides dropdown immediately after hovering over a different main menu item
                            parentSiblingLinks.on('mouseover', function(){
                                parentLI.removeClass('open');
                            });
                        }
            
                        if (siblingMenu.length) {
                            // Adds class 'mouse-on-sibling' to parent list item
                            siblingMenu.on('mouseover', function(){
                                parentLI.addClass('mouse-on-sibling');
                            });
                
                            // Removes class 'mouse-on-sibling' from parent list item
                            siblingMenu.on('mouseleave', function(){
                                parentLI.removeClass('mouse-on-sibling');
                
                                // Hides dropdown after a delay if mouse leaves the dropdown list
                                setTimeout(function(){
                                    parentLI.removeClass('open');
                                }, 250);
                            });
                        }
            
                        // Hides dropdown after a delay when the mouse leaves the main menu item if the main menu list item does not have the class 'mouse-on-sibling'
                        setTimeout(function(){
                            if( !parentLI.hasClass('mouse-on-sibling') ){ parentLI.removeClass('open'); }
                        }, 250);
                    }
                });
            }
        }
    }
    
/* ---------------------------------
    Sticky Header
   --------------------------------- */
    function stickyHeader() {
        var stickyOffset = 0;
        //var stickyOffsetMobile = $('#site-header').offset().top;

        $(window).scroll(function () {
            var sticky = $('#site-header'),
                scroll = $(window).scrollTop();

            if (scroll > stickyOffset) {
                sticky.addClass('sticky');
            } else {
                sticky.removeClass('sticky');
            }
        });
    }

/* ---------------------------------
    Blank option styling of select tag 
    --------------------------------- */
    function replacePlaceholders(){
    	let selectField = $('select');

        if (selectField) {
            let options = selectField.find('option');
            let firstOption = selectField.find('option:nth-child(1)');
            
            if (firstOption) {
                if( !firstOption.val() ){
                    selectField.css( 'color', '#b8b8b8' );
                    options.css( 'color', "#262626" );
                    firstOption.css( 'color', '#b8b8b8' );
                    
                    selectField.on( 'change', function() {
                        let color = !this.value ? '#b8b8b8' : '#262626';
                        $(this).css( 'color', color );
                        selectField.find('option:not(:first-child)').css( 'color', "#262626" );
                    });
                }
            }
        }
    }

/* ---------------------------------
    Initializes swiper carousel
    --------------------------------- */
    function initSwiper(){
        const swiper = new Swiper('.swiper.s1', {
            // Optional parameters
            loop: true,
            grabCursor: true,
            slidesPerView: 2,
            spaceBetween: 20,
            autoplay: {
                delay: 500,
            },
          
            // If we need pagination
            pagination: {
              el: '.swiper-pagination',
              type: 'bullets',
              clickable: true,
            },
          
            // Navigation arrows
            navigation: {
              nextEl: '.swiper-button-next1',
              prevEl: '.swiper-button-prev1',
            },
        });
        
        const swiper2 = new Swiper('.swiper.s2', {
            // Optional parameters
            loop: false,
            grabCursor: true,
            slidesPerView: 1,
            spaceBetween: 20,
          
            // If we need pagination
            pagination: {
              el: '.swiper-pagination',
              type: 'bullets',
              clickable: true
            },
          
            // Navigation arrows
            navigation: {
              nextEl: '.swiper-button-next2',
              prevEl: '.swiper-button-prev2',
            },
        });
    }
    
/* ---------------------------------
    Fancybox Initilization
   --------------------------------- */
    function fancyBox() {
        $("[data-fancybox]").fancybox({
            thumbs: false,
        });
    }
 
/* ---------------------------------
    Isotope Initialization
   --------------------------------- */
    function isotope(){
        var $container = $('.gallery-container');
        $container.isotope({
                filter: '*',
                animationOptions: {
                    duration: 750,
                    easing: 'linear',
                    queue: false
                }
        });

        $('.gallery-filter li').click(function(){
            $('.gallery-filter .current').removeClass('current');
            $(this).addClass('current');

            var selector = $(this).attr('data-filter');
            $container.isotope({
                filter: selector,
                animationOptions: {
                    duration: 750,
                    easing: 'linear',
                    queue: false
                }
             });
             return false;
        }); 

    }
    
/* ---------------------------------
    Show / Hide Search form
   --------------------------------- */
    function toggleSearch() {
        let navSearchField = $('.nav-search-field');
        
        if (navSearchField.length) {
            $('#toggle-search').click(function (e) {
                e.preventDefault();
                e.stopPropagation();
                navSearchField.addClass('visible');
                // $('.nav-search-field').slideToggle('fast');
                navSearchField.find('input[type=search]').focus();
            });
            $("body").click(function(){
                navSearchField.slideUp('fast');
            });
            navSearchField.click(function (e) {
                e.stopPropagation();
            });
    
            $('.nav-search-close').on('click', function(){
                navSearchField.removeClass('visible');
            });
        }
    }   
    
/* ---------------------------------
    Mobile Sidebar
   --------------------------------- */
    function toggleSidebar() {
        $('.sidebar-toggler').click(function (e) {
            var attr = $(this).attr('data-target');
            var target_sidebar = $(this).attr('data-sidebar');
            
            $(this).toggleClass('open');
            if(!(typeof attr !== typeof undefined && attr !== false)){
                e.preventDefault();
                $('#'+target_sidebar).toggleClass('open');
                $('.sidebar-overlay').fadeIn();
            }
        });
        
        $('.sidebar-overlay').click(function () {
            $('.sidebar-toggler').removeClass('open');
            $('.sidebar-nav').removeClass('open');
            $(this).fadeOut();
        });
    }

/* ---------------------------------
    AOS Animation
   --------------------------------- */ 
    function AOSInit(){
        AOS.init();
    }
    

    // Initialize the functions
    scrollToTop();
    stickyHeader();
    fancyBox();
    toggleSearch();
    toggleSidebar();
    AOSInit();
    triggerHeaderDropdownsOnHover();
    new replacePlaceholders();
    new initSwiper();

})( jQuery );