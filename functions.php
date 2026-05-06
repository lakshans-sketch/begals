<?php
/**
 * Bagels functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 */

include_once( ABSPATH . 'wp-admin/includes/plugin.php' );

define('THEME_DIRECTORY_URI', get_template_directory_uri());
define('THEME_DIRECTORY', get_template_directory());
define('STYLE_SHEET_URI', get_stylesheet_uri());
define('VERSION_NUMBER', 2.3);

/**
 * Sets up theme defaults and registers support for various WordPress features.
 *
 * Note that this function is hooked into the after_setup_theme hook, which
 * runs before the init hook. The init hook is too late for some features, such
 * as indicating support for post thumbnails.
 */
function bagels_setup() {
    /*
     * Let WordPress manage the document title.
     * By adding theme support, we declare that this theme does not use a hard-coded <title> tag in the document head, 
     * and expect WordPress to provide it for us.
     */
    add_theme_support( 'title-tag' );

    /*
     * Make theme available for translation.
     */
    load_theme_textdomain( 'bagels' );

    /*
     * Enable support for Post Thumbnails on posts and pages.
     *
     * @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
     */
    add_theme_support( 'post-thumbnails' );

    // Bootstrap Nav Walker
    require_once( THEME_DIRECTORY. '/framework/includes/class-wp-bootstrap-navwalker.php' );
	// Custom Login URL Handler (Place this AFTER Bootstrap Nav Walker)
	if ( file_exists( get_template_directory() . '/framework/includes/custom-login-url.php' ) ) {
	    require_once get_template_directory() . '/framework/includes/custom-login-url.php';
	} else {
	    // Log error if file is missing
	    if ( defined('WP_DEBUG') && WP_DEBUG ) {
	        error_log( 'Custom Login URL file not found: ' . get_template_directory() . '/framework/includes/custom-login-url.php' );
	    }
	}
    if ( ! file_exists( THEME_DIRECTORY. '/framework/includes/class-wp-bootstrap-navwalker.php' ) ) {
        // File does not exist... return an error.
        return new WP_Error( 'class-wp-bootstrap-navwalker-missing', __( 'It appears the class-wp-bootstrap-navwalker.php file may be missing.', 'wp-bootstrap-navwalker' ) );
    } else {
        // File exists... require it.
        require_once THEME_DIRECTORY. '/framework/includes/class-wp-bootstrap-navwalker.php';
    }
    
    /*
     * Add menu support
     */
    register_nav_menus( array(
        'primary-menu'  => __( 'Primary Menu' ),
        'mobile-slide-menu'  => __( 'Mobile Slide Menu' ),
    ) );
    
    /*
     * Include plugins built in to the theme
     */
    include_once( THEME_DIRECTORY. '/framework/includes/plugins/plugins.php' );
}
add_action( 'after_setup_theme', 'bagels_setup' );

// Custom Functions
require_once( THEME_DIRECTORY.'/framework/includes/custom-functions.php' );

// Custom Post Types 
require_once( THEME_DIRECTORY.'/framework/includes/custom-post-types.php' );

// Woocommerce Custom Functions 
if ( bagels_is_woocommerce_enabled() ) {
    require_once( THEME_DIRECTORY.'/framework/includes/woocommerce-custom-functions.php' );
}

// Theme Settings
if ( is_plugin_active( 'advanced-custom-fields-pro/acf.php' ) ){
    require_once( THEME_DIRECTORY. '/framework/includes/theme-settings.php' );
}

/*
 * Import Styles and Scripts
 */
function bagels_scripts() {

    wp_enqueue_style('fw-fontawesome-css', get_theme_file_uri( '/framework/libs/font-awesome-6/css/all.min.css' ), array(), null );
    wp_enqueue_style('fw-bootstrap-css', get_theme_file_uri( '/framework/libs/bootstrap/bootstrap.min.css' ), array(), null );
    wp_enqueue_style('fw-swiper-css', get_theme_file_uri( '/framework/libs/swiper/swiper-bundle.min.css' ), array(), null );
    wp_enqueue_style('fw-fancybox-css', get_theme_file_uri( '/framework/libs/fancybox/jquery.fancybox.min.css' ), array(), null );
    wp_enqueue_style('fw-aos-css', get_theme_file_uri( '/framework/libs/aos/aos.css' ), array(), null );
    
    wp_enqueue_style('wp-style', get_stylesheet_uri(), array(), VERSION_NUMBER);
    wp_enqueue_style('blog-css', get_theme_file_uri( '/framework/assets/css/blog.css' ), array(), VERSION_NUMBER);
    wp_enqueue_style('theme-css', get_theme_file_uri( '/assets/theme-css/main-style.css' ), array(), VERSION_NUMBER);

    wp_enqueue_script('fw-bootstrap-js', get_theme_file_uri( '/framework/libs/bootstrap/bootstrap.min.js' ), array(), null, true);
    wp_enqueue_script('fw-swiper-js', get_theme_file_uri( '/framework/libs/swiper/swiper-bundle.min.js' ), array(), null, true);
    wp_enqueue_script('fw-fancybox-js', get_theme_file_uri( '/framework/libs/fancybox/jquery.fancybox.min.js' ), array(), null, true);
    wp_enqueue_script('fw-aos-js', get_theme_file_uri( '/framework/libs/aos/aos.js' ), array(), null, true);
    wp_enqueue_script('fw-js', get_theme_file_uri( '/framework/assets/js/framework.js' ), array(), VERSION_NUMBER, true);
    wp_enqueue_script('theme-js', get_theme_file_uri( '/assets/theme-js/main.js' ), array(), VERSION_NUMBER, true);
    
     // Owl Carousel CSS
    wp_enqueue_style('owl-carousel-css', 'https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.min.css', [], '2.3.4');
    wp_enqueue_style('owl-carousel-theme-css', 'https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.theme.default.min.css', [], '2.3.4');
    wp_enqueue_script('owl-carousel-js', 'https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js', ['jquery'], '2.3.4', true);


    // wp_enqueue_script('spamProtection-js', get_theme_file_uri( '/framework/assets/js/SpamFormProtection.js' ), array(), VERSION_NUMBER, true);
    
    wp_localize_script( 'fw-js', 'bagels_ajax_object', array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ) );
    
    if ( bagels_is_woocommerce_enabled() ) {
        wp_enqueue_style('fw-woocommerce-css', get_theme_file_uri( '/framework/assets/css/woocommerce.css' ), array(), VERSION_NUMBER);
        wp_enqueue_script('fw-woocommerce-js', get_theme_file_uri( '/framework/assets/js/woocommerce.js' ), array(), VERSION_NUMBER, true);
    }
}
add_action( 'wp_enqueue_scripts', 'bagels_scripts' );

class ThemeOptions{
   static $getData;
}

add_action( 'init', 'init_actions' );
function init_actions(){
    // Initialize theme options
    ThemeOptions::$getData = get_option('do_all_theme_options');
}

/**
 * Hides menu items from the dashboard
 */
add_action( 'admin_menu', 'bagels_hide_admmin_menu_items' );
function bagels_hide_admmin_menu_items() {
    if ( wp_get_current_user()->user_email !== get_option( 'admin_email' ) ){ remove_menu_page( 'edit.php?post_type=acf-field-group' ); }
}

// Hide Advanced Custom fields from the menu (except for developers: comment this code if you want to edit ACF)
//add_filter( 'acf/settings/show_admin', '__return_false' );
  
/**
 * Adds classes to the body tag
 */
add_filter( 'body_class','bagels_body_classes' );
function bagels_body_classes( $classes ) {
    if ( wp_is_mobile() ) { $classes[] = 'wp-mobile'; }

    if ( is_plugin_active( 'advanced-custom-fields-pro/acf.php' ) && !empty( BTS::$options[ 'contact_details' ][ 'location' ] ) ) {
        $classes[] = 'bagels-has-business-location';
    }

    return $classes;
}


