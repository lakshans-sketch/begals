<?php
/**
 * The template for displaying comments
 *
 * This is the template that displays the area of the page that contains both the current comments and the comment form.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 */
/*
 * If the current post is protected by a password and the visitor has not yet entered the password we will 
 * return early without loading the comments.
 */
if ( post_password_required() ) {
    return;
}

if( BTS::$options['page_settings']['post_comments'] ):
?>
    <div class="post-comments">
        <?php
        if ( have_comments() ) : ?>

            <h3 class="comments-title">
                <?php 
                    $comments_number = get_comments_number();
                    $post_title = get_the_title();

                    if( $comments_number > 1 ){
                        printf( __( '%1$s Replies to %2$s', 'bagels' ), $comments_number, $post_title );
                    }else{
                        printf( __( '%1$s Reply to %2$s', 'bagels' ), $comments_number, $post_title );
                    }
                ?>
            </h3>

            <ul class="comment-list">
                <?php
                wp_list_comments(array(
                    'avatar_size' => 50,
                    'style' => 'ul',
                    'short_ping' => true,
                    'reply_text' => 'Reply',
                ));
                ?>
            </ul>

        <?php
        endif; // Check for have_comments().

        comment_form();
        ?>
    </div>
<?php 
endif;