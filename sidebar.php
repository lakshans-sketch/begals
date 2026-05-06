<?php
/**
 * The sidebar of the template
 *
 * @link https://developer.wordpress.org/themes/functionality/sidebars/
 */

?>

<div class="sidebar">
    <div class="sidebar-widget">
        <div class="search-wrapper">
            <form action="<?php echo get_site_url() ?>" method="GET">
                <input type="search" name="s" placeholder="Search Products..." class="fld-search" required="required">
                <input type="hidden" name="post_type" value="product">
            </form>
        </div>
    </div>

    <div class="sidebar-widget">
        <h3 class="sidebar-widget-title line-bottom">Product Categories</h3>

        <?php
            $taxonomy     = 'product_cat';
            $orderby      = 'name';  
            $show_count   = 0;      // 1 for yes, 0 for no
            $pad_counts   = 0;      // 1 for yes, 0 for no
            $hierarchical = 1;      // 1 for yes, 0 for no  
            $title        = '';  
            $empty        = 0;

            $args = array(
                'taxonomy'     => $taxonomy,
                'orderby'      => $orderby,
                'show_count'   => $show_count,
                'pad_counts'   => $pad_counts,
                'hierarchical' => $hierarchical,
                'title_li'     => $title,
                'hide_empty'   => $empty
            );
            $all_categories = get_categories( $args );

            if($all_categories):
                echo '<ul class="sidebar-list">';
                    foreach ($all_categories as $cat):
                        if($cat->category_parent == 0):
                            $category_id = $cat->term_id;       
                            echo '<li>
                                <span>
                                    <a href="'.get_term_link($cat->slug, 'product_cat').'" aria-label="' .  __( "Category name", 'bagels' ) . '">
                                        '.$cat->name.'<span class="category-count">('.$cat->count. ')</span>
                                    </a>
                                </span>';

                                // Sub categories
                                $args2 = array(
                                    'taxonomy'     => $taxonomy,
                                    'child_of'     => 0,
                                    'parent'       => $category_id,
                                    'orderby'      => $orderby,
                                    'show_count'   => $show_count,
                                    'pad_counts'   => $pad_counts,
                                    'hierarchical' => $hierarchical,
                                    'title_li'     => $title,
                                    'hide_empty'   => $empty
                                );
                                $sub_cats = get_categories( $args2 );
                                if($sub_cats):
                                    echo '<ul>';
                                        foreach($sub_cats as $sub_category) {
                                            echo '<li>
                                                <span>
                                                    <a href="'.get_term_link($sub_category->slug, 'product_cat').'" aria-label="' .  __( "Sub-category name", 'bagels' ) . '">
                                                        '.$sub_category->name.' <span class="category-count">('.$sub_category->count. ')</span>
                                                    </a>
                                                </span>
                                            </li>';
                                        } 
                                    echo '</ul><i class="fal fa-angle-down arrow-dropdown"></i>';
                                endif;
                                // End sub categories

                            echo '</li>';
                        endif;       
                    endforeach;
                echo '</ul>';
            endif;
          ?>   
    </div>

</div>