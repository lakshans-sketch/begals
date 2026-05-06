<?php
/**
 * Displays blog sidebar
 * Included in index.php / search.php
 */
?>

<div class="blog-sidebar">
    <div class="sidebar-widget">
        <div class="search-wrapper">
            <form action="<?php echo get_site_url() ?>" method="GET">
                <input type="search" name="s" placeholder="Click to Search" class="fld-search" required="required">
                <input type="hidden" name="post_type" value="post">
                <button class="btn-search"><i class="far fa-search" aria-hidden="true"></i></button>
            </form>
        </div>
    </div>

    <div class="sidebar-widget">
        <h3 class="sidebar-widget-title line-bottom"><?php _e( 'Latest News', 'bagels' ); ?></h3>
        <ul class="list-border">
            <?php
                $recent_posts = wp_get_recent_posts( array(
                    'numberposts' => 10,
                    'post_status' => 'publish'
                ) );
                
                foreach ( $recent_posts as $recent ) {
                    echo '<li><a href="' . get_permalink($recent["ID"]) . '" aria-label="' . __( "Read: ", $recent["post_title"], 'bagels' ) . '">'.$recent["post_title"].'</a></li>';
                }
            ?>
        </ul>
    </div>
</div>