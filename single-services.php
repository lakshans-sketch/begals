<?php
/**
 * The template for displaying all single services
 * 
 */

get_header(); 

//Page Header Banner
echo page_header_banner();
?>

<!-- Page Container -->
<div class="container page-container">
    
    <?php if ( have_posts() ) :
        while ( have_posts() ) : the_post();

            echo the_content();

        endwhile;
    endif; ?>

</div>
<!-- End Page Container -->

<?php get_footer();