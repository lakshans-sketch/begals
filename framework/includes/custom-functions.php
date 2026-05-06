<?php
/**
 * Custom Functions of the theme.
 * Included in functions.php
 */

/*-------------------------------*/
/*  Default Pagination
/*-------------------------------*/
function pagination($pages = '', $range = 4, $paged = null) {
    $showitems = 0;

    if ($paged == null) {
        global $paged;
    }
    if (empty($paged)) {
        $paged = 1;
    }
    if ($pages == '') {
        global $wp_query;
        $pages = $wp_query->max_num_pages;
        if (!$pages) {
            $pages = 1;
        }
    }
    if (1 != $pages) {
        echo "<ul class='pagination'><li><a href='' aria-label='" . __( "Page", 'bagels' ) . "'>Page " . $paged . " of " . $pages . "</a></li>";
            echo '<li>
                <a href="' . get_previous_posts_page_link() . '" aria-label="' . __( "Previous page", 'bagels' ) . '"><i class="fa-regular fa-chevron-left"></i></a>
            </li>';
            for ($i = 1; $i <= $pages; $i++) {
                if (1 != $pages && (!($i >= $paged + $range + 1 || $i <= $paged - $range - 1) || $pages <= $showitems )) {
                    echo ($paged == $i) ? "<li class=\"active\">
                        <a href='#' aria-label='" . __( "Page " . $i, 'bagels' ) . "'>" . $i . "</a>
                        </li>" : "<li>
                        <a href='" . get_pagenum_link($i) . "' class=\"inactive\" aria-label='" . __( "Page " . $i, 'bagels' ) . "'>" . $i . "</a>
                    </li>";
                }
            }
            echo '<li>
                <a href="' . get_next_posts_page_link($pages) . '" aria-label="' . __( "Next page", 'bagels' ) . '"><i class="fa-regular fa-chevron-right"></i></a>
            </li>
        </ul>';
        
    }
}


/*------------------------------------------------------*/
/*  Custome fields values into wordpress search query
/*------------------------------------------------------*/

/**
 * Extend WordPress search to include custom fields
 *
 * http://adambalee.com
 */

/**
 * Join posts and postmeta tables
 *
 * http://codex.wordpress.org/Plugin_API/Filter_Reference/posts_join
 */
function cf_search_join( $join ) {
    global $wpdb;

    if ( is_search() ) {    
        $join .=' LEFT JOIN '.$wpdb->postmeta. ' ON '. $wpdb->posts . '.ID = ' . $wpdb->postmeta . '.post_id ';
    }
    
    return $join;
}
//add_filter('posts_join', 'cf_search_join' );

/**
 * Modify the search query with posts_where
 *
 * http://codex.wordpress.org/Plugin_API/Filter_Reference/posts_where
 */
function cf_search_where( $where ) {
    global $wpdb;
   
    if ( is_search() ) {
        $where = preg_replace(
            "/\(\s*".$wpdb->posts.".post_title\s+LIKE\s*(\'[^\']+\')\s*\)/",
            "(".$wpdb->posts.".post_title LIKE $1) OR (".$wpdb->postmeta.".meta_value LIKE $1)", $where );
    }

    return $where;
}
//add_filter( 'posts_where', 'cf_search_where' );

/**
 * Prevent duplicates
 *
 * http://codex.wordpress.org/Plugin_API/Filter_Reference/posts_distinct
 */
function cf_search_distinct( $where ) {
    global $wpdb;

    if ( is_search() ) {
        return "DISTINCT";
    }

    return $where;
}
//add_filter( 'posts_distinct', 'cf_search_distinct' );


/*-------------------------------*/
/*  Minify CSS
/*-------------------------------*/
function minifyCss($buffer){
    $buffer = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $buffer);
    $buffer = str_replace(': ', ':', $buffer);
    $buffer = str_replace(array("\r\n", "\r", "\n", "\t", '  ', '    ', '    '), '', $buffer);
    return $buffer;
}

/*-------------------------------*/
/*  Page Title Bar
/*-------------------------------*/
get_template_part( 'framework/template-parts/page/page', 'header-banner' );


/*-------------------------------*/
/*  Breadcumbs
/*-------------------------------*/
function theme_breadcrumbs() {
    /* === OPTIONS === */
    $text['home'] = 'Home'; // text for the 'Home' link
    $text['category'] = 'Archive by Category "%s"'; // text for a category page
    $text['tax'] = 'Archive for "%s"'; // text for a taxonomy page
    $text['search'] = 'Search Results for "%s" Query'; // text for a search results page
    $text['tag'] = 'Posts Tagged "%s"'; // text for a tag page
    $text['author'] = 'Articles Posted by %s'; // text for an author page
    $text['404'] = 'Error 404'; // text for the 404 page
    $showCurrent = 1; // 1 - show current post/page title in breadcrumbs, 0 - don't show
    $showOnHome = 0; // 1 - show breadcrumbs on the homepage, 0 - don't show
    $delimiter = '<span class="delimiter"> &raquo; </span>'; // delimiter between crumbs
    $before = '<span class="current">'; // tag before the current crumb
    $after = '</span>'; // tag after the current crumb
    /* === END OF OPTIONS === */
    global $post;
    $homeLink = get_bloginfo('url') . '/';
    $linkBefore = '<span typeof="v:Breadcrumb">';
    $linkAfter = '</span>';
    $linkAttr = ' rel="v:url" property="v:title"';
    $link = $linkBefore . '<a' . $linkAttr . ' href="%1$s" aria-label="' . __( "", 'bagels' ) . '">%2$s</a>' . $linkAfter;
    if (is_home() || is_front_page()) {
        if ($showOnHome == 1)
            echo '<div id="crumbs"><a href="' . $homeLink . '" aria-label="' . __( "", 'bagels' ) . '">' . $text['home'] . '</a></div>';
    } else {
        echo '<div id="crumbs" xmlns:v="http://rdf.data-vocabulary.org/#">' . sprintf($link, $homeLink, $text['home']) . $delimiter;

        if (is_category()) {
            $thisCat = get_category(get_query_var('cat'), false);
            if ($thisCat->parent != 0) {
                $cats = get_category_parents($thisCat->parent, TRUE, $delimiter);
                $cats = str_replace('<a', $linkBefore . '<a' . $linkAttr, $cats);
                $cats = str_replace('</a>', '</a>' . $linkAfter, $cats);
                echo $cats;
            }
            echo $before . sprintf($text['category'], single_cat_title('', false)) . $after;
        } elseif (is_tax()) {
            $thisCat = get_category(get_query_var('cat'), false);
            if ($thisCat->parent != 0) {
                $cats = get_category_parents($thisCat->parent, TRUE, $delimiter);
                $cats = str_replace('<a', $linkBefore . '<a' . $linkAttr, $cats);
                $cats = str_replace('</a>', '</a>' . $linkAfter, $cats);
                echo $cats;
            }
            echo $before . sprintf($text['tax'], single_cat_title('', false)) . $after;
        } elseif (is_search()) {
            echo $before . sprintf($text['search'], get_search_query()) . $after;
        } elseif (is_day()) {
            echo sprintf($link, get_year_link(get_the_time('Y')), get_the_time('Y')) . $delimiter;
            echo sprintf($link, get_month_link(get_the_time('Y'), get_the_time('m')), get_the_time('F')) . $delimiter;
            echo $before . get_the_time('d') . $after;
        } elseif (is_month()) {
            echo sprintf($link, get_year_link(get_the_time('Y')), get_the_time('Y')) . $delimiter;
            echo $before . get_the_time('F') . $after;
        } elseif (is_year()) {
            echo $before . get_the_time('Y') . $after;
        } elseif (is_single() && !is_attachment()) {
            if (get_post_type() != 'post') {
                $post_type = get_post_type_object(get_post_type());
                $slug = $post_type->rewrite;
                printf($link, $homeLink . '/' . $slug['slug'] . '/', $post_type->labels->singular_name);
                if ($showCurrent == 1)
                    echo $delimiter . $before . get_the_title() . $after;
            } else {
                $cat = get_the_category();
                $cat = $cat[0];
                $cats = get_category_parents($cat, TRUE, $delimiter);
                if ($showCurrent == 0)
                    $cats = preg_replace("#^(.+)$delimiter$#", "$1", $cats);
                $cats = str_replace('<a', $linkBefore . '<a' . $linkAttr, $cats);
                $cats = str_replace('</a>', '</a>' . $linkAfter, $cats);
                echo $cats;
                if ($showCurrent == 1)
                    echo $before . get_the_title() . $after;
            }
        } elseif (!is_single() && !is_page() && get_post_type() != 'post' && !is_404()) {
            $post_type = get_post_type_object(get_post_type());
            echo $before . $post_type->labels->singular_name . $after;
        } elseif (is_attachment()) {
            $parent = get_post($post->post_parent);
            $cat = get_the_category($parent->ID);
            $cat = $cat[0];
            $cats = get_category_parents($cat, TRUE, $delimiter);
            $cats = str_replace('<a', $linkBefore . '<a' . $linkAttr, $cats);
            $cats = str_replace('</a>', '</a>' . $linkAfter, $cats);
            echo $cats;
            printf($link, get_permalink($parent), $parent->post_title);
            if ($showCurrent == 1)
                echo $delimiter . $before . get_the_title() . $after;
        } elseif (is_page() && !$post->post_parent) {
            if ($showCurrent == 1)
                echo $before . get_the_title() . $after;
        } elseif (is_page() && $post->post_parent) {
            $parent_id = $post->post_parent;
            $breadcrumbs = array();
            while ($parent_id) {
                $page = get_page($parent_id);
                $breadcrumbs[] = sprintf($link, get_permalink($page->ID), get_the_title($page->ID));
                $parent_id = $page->post_parent;
            }
            $breadcrumbs = array_reverse($breadcrumbs);
            for ($i = 0; $i < count($breadcrumbs); $i++) {
                echo $breadcrumbs[$i];
                if ($i != count($breadcrumbs) - 1)
                    echo $delimiter;
            }
            if ($showCurrent == 1)
                echo $delimiter . $before . get_the_title() . $after;
        } elseif (is_tag()) {
            echo $before . sprintf($text['tag'], single_tag_title('', false)) . $after;
        } elseif (is_author()) {
            global $author;
            $userdata = get_userdata($author);
            echo $before . sprintf($text['author'], $userdata->display_name) . $after;
        } elseif (is_404()) {
            echo $before . $text['404'] . $after;
        }
        if (get_query_var('paged')) {
            if (is_category() || is_day() || is_month() || is_year() || is_search() || is_tag() || is_author())
                echo ' (';
            echo __('Page') . ' ' . get_query_var('paged');
            if (is_category() || is_day() || is_month() || is_year() || is_search() || is_tag() || is_author())
                echo ')';
        }
        echo '</div>';
    }
}

// end dimox_breadcrumbs()


/*-------------------------------------------*/
/*  Contact form 7 custom header and footer
/*-------------------------------------------*/
function wpcf7_mail_html_header( $body_html, $instance ) { 
    ob_start();
    include( THEME_DIRECTORY.'/framework/includes/email/mail-header.php' );
    $body_html .= ob_get_clean();
    return $body_html;
}; 

function wpcf7_mail_html_footer( $body_html, $instance ) { 
    ob_start();
    include( THEME_DIRECTORY.'/framework/includes/email/mail-footer.php' );
    $body_html .= ob_get_clean();
    return $body_html;
}; 

add_filter( 'wpcf7_mail_html_header', 'wpcf7_mail_html_header', 10, 2 ); 
add_filter( 'wpcf7_mail_html_footer', 'wpcf7_mail_html_footer', 10, 2 );

/*
 * Check if Woocommerce installed and activated
 */
function bagels_is_woocommerce_enabled(){
    return class_exists( 'WooCommerce' ) ? true : false;
}

/*
 * Get theme default images
 * Supports header-banner, user
 */
function bagels_get_default_image( $type ){
    $image_path = THEME_DIRECTORY_URI . '/framework/assets/images/';
    
    if( $type == 'header-banner' ){
        $image_path = $image_path . 'header-banner-default.jpg';
    }elseif( $type == 'user' ){
        $image_path = $image_path . 'user.jpg';
    }else{
        $image_path = $image_path . 'default-pic.jpg';
    }

    return $image_path;
}

// Change wordpress admin login interface logo
add_action( 'login_head',  'bagels_custom_admin_login_logo' );
function bagels_custom_admin_login_logo(){
    if( !empty( BTS::$options['header'] ) && !empty( BTS::$options['header']['logo'] ) ):
        ob_start(); ?>

        <style type="text/css">
            .login h1 a{
                width: 150px;
                height: 80px;
                background-image: url( <?php echo esc_url( BTS::$options['header']['logo']['sizes']['medium'] ); ?> ) !important;
                background-size: contain;
                background-position: center;
                margin-bottom: 0;
            }
        </style>
    <?php
        $output = ob_get_clean();
        echo $output;
    endif;
}

/**
 * Get chat link for whatsapp
 * @return string $whatsapp_link: html for the Whatsapp chat link
 * @param string $info[ 'number' ]: number to be inserted to the chat link
 * @param string $info[ 'template_msg' ]: default text to appear in the outgoing message
 */
function bingo_get_whatsapp_chat_link( $info = array() ){
    if ( !empty( $info[ 'number' ] ) ) {
        $whatsapp_link = "https://wa.me/" . preg_replace( '/[^0-9]/', '', $info[ 'number' ] );
        if ( !empty( $info[ 'template_msg' ] ) ) { $whatsapp_link .= "?text=" . $info[ 'template_msg' ]; }
        
        return $whatsapp_link;
    }

    return null;
}