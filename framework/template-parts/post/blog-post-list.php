<?php
/**
 * Displays blog posts list
 * Included in index.php / search.php
 */

$title = get_the_title();
?>

<div class="blog-posts">
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 pre-post-wrapper">
        <article class="post-wrapper clearfix">
            <div class="col-md-4 col-sm-4 col-xs-12 p-0">
                <div class="post-header">
                    <div class="post-thumb">
                        <?php $thumb = null; ?>
                        <?php if (has_post_thumbnail()): ?>
                            <?php $thumb = wp_get_attachment_url(get_post_thumbnail_id($post->ID)); ?>
                            
                            <a href="<?php echo get_permalink(); ?>" aria-label="<?php _e( "Read :" . $title, 'bagels' ); ?>">
                                <img src="<?php echo $thumb; ?>" alt="" class="image-responsive">
                            </a>
                        <?php else: ?>
                            <img src="<?php echo get_template_directory_uri() ?>/framework/assets/images/default.jpg" alt="" class="image-responsive">
                        <?php endif; ?>
                    </div>
                    <div class="post-date">
                        <?php the_time('j'); ?> <span><?php the_time('M'); ?></span>
                    </div>
                </div>
            </div>

            <div class="col-md-8 col-sm-8 col-xs-12 p-0">
                <div class="post-content">
                    <h5 class="post-title">
                        <a href="<?php echo get_permalink() ?>" aria-label="<?php _e( "Read: " . $title, 'bagels' ); ?>">
                            <?php echo wp_trim_words( $title, 14 ); ?>
                        </a>
                    </h5>
                    
                    <p><?php echo wp_trim_words(apply_filters('the_content', get_the_content()), 45); ?></p>
                    
                    <a href="<?php echo get_permalink() ?>" class="post-link" aria-label="<?php _e( "Read: " . $title, 'bagels' ); ?>">
                        <?php _e( "Read more", 'bagels' ); ?> <i class="far fa-angle-double-right"></i>
                    </a>
                    
                    <div class="clearfix"></div>
                </div>
            </div>
        </article>
    </div>
</div>