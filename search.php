<?php
/**
 * The template for displaying search results pages
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#search-result
 */

get_header(); 

//Page Header Banner
$pageTitle = 'Search results for: "'.get_search_query().'"';
echo page_header_banner( $pageTitle, bagels_get_default_image( 'header-banner' ) );

?>

<div class="container blog-wrapper page-container">
    <div class="row">
        <div class="col-lg-9 col-md-9 col-sm-12 col-xs-12">
            <?php if (have_posts()) : while (have_posts()) : the_post(); ?>

                <?php 
                    // Include Blog Posts List
                    get_template_part('framework/template-parts/post/blog', 'post-list'); 
                ?>

            <?php endwhile; ?>

            <div class="pagination-wrapper">
                <?php pagination(); ?>
            </div>
                
            <?php else: ?>
                <h3>No results found for: '<?php echo get_search_query(); ?>'</h3>    
            <?php endif; ?>
        </div>

        <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12">
            <?php 
                // Include Blog Sidebar
                get_template_part('framework/template-parts/post/blog', 'sidebar'); 
            ?>
        </div>
    </div>
</div>


<?php get_footer();