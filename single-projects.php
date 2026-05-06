<?php
/**
 * The template for displaying all single projects
 * 
 */

if ( !is_plugin_active('advanced-custom-fields-pro/acf.php') ):
    header('Location: /');
    exit();
endif;

get_header(); 

//Page Header Banner
$featuredImage = get_field("featured_image");
echo page_header_banner('', $featuredImage['sizes']['large']);

?>

<!-- Page Container -->
<div class="container page-container single-project-wrapper">
    <div class="row"> 

        <!-- Featured Image -->
        <?php
        if(get_field("featured_image" )): ?>
        <div class="col-lg-3 col-md-3 col-sm-4 col-xs-12"> 
            <div class="project-box">
                <a href="<?php echo $featuredImage; ?>" data-fancybox="Projects" aria-label="<?php _e( "View project", 'bagels' ); ?>">
                    <img src="<?php echo $featuredImage['sizes']['large']; ?>" alt="">
                    <div class="overlay">
                        <span class="icon-view"><i class="fal fa-image"></i></span>
                    </div>
                </a>
            </div>
        </div>
        <?php endif; ?>
         
        <!-- Other Images -->
        <?php
        $images = get_field('project_images');
        if( $images ): ?>
            <?php foreach( $images as $image ): ?>
                <div class="col-lg-3 col-md-3 col-sm-4 col-xs-12"> 
                    <div class="project-box">
                        <a href="<?php echo $image['sizes']['large']; ?>" data-fancybox="Projects" aria-label="<?php _e( "View project", 'bagels' ); ?>">
                            <img src="<?php echo $image['sizes']['large']; ?>" alt="<?php echo $image['alt']; ?>">
                            <div class="overlay">
                                <span class="icon-view"><i class="fal fa-image"></i></span>
                            </div>
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
    
    <div class="row">
        <div class="project-services">
            <?php
            $cats = get_field('related_service');
            if($cats){
                $catStr = '<ul>';
                foreach($cats as $cat){
                    $catStr .='<li><a href="'.get_permalink($cat->ID).'" aria-label="' . __( $cat->post_title, 'bagels' ) . '">'.$cat->post_title.'</a></li>';
                }
                $catStr .='</ul>';
            }
            ?>
            <div><strong>Related Services:</strong> <?php echo $catStr; ?></div>
        </div>
    </div>
</div>
<!-- End Page Container -->

<?php get_footer();