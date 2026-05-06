<?php
/**
 * The template for displaying all single posts
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 */

get_header(); 

//Page Header Banner
echo page_header_banner();
?>

<?php if ( have_posts() ) : ?>
    <?php while ( have_posts() ) : the_post();?>

        <div class="container blog-wrapper page-container">
            <div class="row">
                <div class="col-lg-9 col-md-9 col-sm-12 col-xs-12">
                    <div class="single-post">
                        <article class="clearfix">

                            <!-- <div class="post-title">
                                <h3><?php /* the_title(); */ ?></h3>
                            </div> -->
                            <div class="post-meta">
                                <ul class="list-inline">
                                    <li><?php _e( "Posted:", 'bagels' ); ?> <span><?php the_time('F jS, Y'); ?></span></li>
                                    <!-- <li><?php /* _e( "By:", 'bagels' ); */ ?> <span class="post-author"><?php /* the_author_posts_link(); */ ?></span></li> -->
                                </ul>
                            </div>
                            <div class="post-content">
                                <?php echo the_content(); ?>
                            </div>

                            <div class="clearfix"></div>
                            <?php
                            $posttags = get_the_tags();
                            if ($posttags) {
                                echo '<div class="tags"><i class="fas fa-tags" aria-hidden="true"></i> Tags: ';
                                foreach ($posttags as $tag) {
                                    echo ' <a href="' . get_tag_link($tag->term_id) . '" aria-label="' . __( $tag->name, 'bagels' ) . '">' . $tag->name . '</a>';
                                }
                                echo '</div>';
                            }
                            ?>
                        </article>
                    </div>
                    
                    <?php
                    // If comments are open or we have at least one comment, load up the comment template.
                    if ( comments_open() || get_comments_number() ) :
                        comments_template();
                    endif;
                    ?>
                    
                </div>

                <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12">
                    <?php 
                        // Include Blog Sidebar
                        get_template_part('framework/template-parts/post/blog', 'sidebar'); 
                    ?>
                </div>
            </div>
        </div>

        <?php
    endwhile;
endif; ?>


<?php get_footer();