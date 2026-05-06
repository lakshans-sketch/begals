<?php
/**
 * The template for displaying all pages
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages
 * and that other 'pages' on your WordPress site may use a different template.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 */

get_header(); 

//Page Header Banner
echo page_header_banner();

if( BTS::$options['page_settings']['breadcumbs'] ): ?>
    <div class="breadcumb-container">
        <div class="container">
            <?php theme_breadcrumbs(); ?>
        </div>
    </div>
<?php endif; ?>

<!-- Page Container -->
<div class="container page-container">

    <?php 
        if ( have_posts() ):
            while ( have_posts() ) : the_post();
                the_content();
            endwhile;
        else: 
    ?>
        <p><?php _e( 'Sorry, no posts matched your criteria.', 'bagels' ); ?></p>

    <?php endif; ?>
</div>
<!-- End Page Container -->


<?php get_footer();