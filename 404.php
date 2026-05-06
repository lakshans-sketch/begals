<?php
/**
 * The template for displaying 404 pages (not found)
 *
 * @link https://codex.wordpress.org/Creating_an_Error_404_Page
 */

get_header(); 

//Page Header Banner
echo page_header_banner('Page not found !!');

?>

<!-- Page Container -->
<div class="container page-container page-404">
    <h1 class="error-404"><?php _e( '404', 'bagels' ); ?></h1>
    <h3><?php _e( 'We can\'t seem to find the page you\'re looking for.', 'bagels' ); ?></h3><br>
    
    <div class="bagels-theme-button txt-center">
        <a href="<?php echo get_site_url(); ?>" aria-label="<?php _e( 'Return to home page', 'bagels' ); ?>"><?php _e( 'Go back Home', 'bagels' ); ?></a>
    </div>
</div>

<?php get_footer();