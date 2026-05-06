<?php
/**
 * Creates social media links widget
 */

class SOCIAL_MEDIA_WIDGET{
    public function __construct(){
        add_action( 'wp_enqueue_scripts', array( $this, 'init_plugin' ) );
        add_shortcode( 'social_icons_widget', array( $this, 'create_social_icons_widget' ) );
    }

    // Load assets
    public function init_plugin(){
        wp_enqueue_style('siw-style', get_theme_file_uri( '/framework/includes/plugins/social-links/assets/style.css' ), array(), VERSION_NUMBER );
    }

    // Load markup
    public function create_social_icons_widget() {
        if( is_plugin_active( 'advanced-custom-fields-pro/acf.php' ) && !empty( BTS::$options[ 'social_icons' ] ) ): ?>
            <div class="bagels-social-icons-widget">
                <ul class="siw-list">
                    <?php foreach ( BTS::$options[ 'social_icons' ] as $key => $value ):
                        if( !empty( $value[ 'url' ] ) && !empty( $value[ 'social_icons' ] ) ){
                            printf( 
                                '<li><a href="%1$s" target="_blank" title="%2$s" rel="noopener" aria-label="' . __( $value[ 'social_icons' ][ 'label' ], 'bagels' ) . '"><i class="fab fa-%3$s"></i></a></li>', 
                                esc_url( $value[ 'url' ] ), 
                                __( $value[ 'social_icons' ][ 'label' ], 'bagels' ), 
                                $value[ 'social_icons' ][ 'value' ]
                            );
                        }
                    endforeach; ?>
                </ul>
            </div>
        <?php endif;
    }
}

new SOCIAL_MEDIA_WIDGET();