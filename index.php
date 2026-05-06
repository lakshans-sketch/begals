<?php
/**
 * The main template file
 *
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query.
 * E.g., it puts together the home page when no home.php file exists.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 */

get_header(); 

//Page Header Banner
echo page_header_banner( 'Blog', bagels_get_default_image( 'header-banner' ) );
?>
	
<!-- Page Container -->
<div class="container blog-wrapper page-container">
    <div class="row">
        <div class="col-md-8 col-sm-12 col-xs-12 col-md-push-2">
            <?php if (have_posts()) : while (have_posts()) : the_post(); ?>

                <?php 
                    // Include Blog Posts List
                    get_template_part( 'framework/template-parts/post/blog', 'post-list' ); 
                ?>
                
            <?php endwhile; ?>

            <div class="pagination-wrapper">
                <?php pagination(); ?>
            </div>
            
            <?php else: ?>
                <h5><?php _e( 'No Posts found.', 'bagels' ); ?></h5>
            <?php endif; ?>
        </div>
    </div>
</div>


<?php get_footer();