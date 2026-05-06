<?php
/**
 * Creates accessibility menu widget
 */

class ACCESSIBILITY_WIDGET{
    public function __construct(){
        add_action( 'wp_enqueue_scripts', array( $this, 'init_plugin' ) );
        add_shortcode( 'accessibility_menu_widget', array( $this, 'create_accessibility_menu_widget' ) );
    }

    // Load assets
    public function init_plugin(){
        wp_enqueue_style('aw-style', get_theme_file_uri( '/framework/includes/plugins/accessibility-widget/assets/style.css' ), array(), VERSION_NUMBER );
        wp_enqueue_script('aw-script', get_theme_file_uri( '/framework/includes/plugins/accessibility-widget/assets/script.js' ), array(), VERSION_NUMBER, true);
    }

    // Get accessibility menu items
    public function get_menu_items(){
        return array(
            array(
                'title' => 'Increase font size',
                'icon' => 'fa-regular fa-magnifying-glass-plus',
                'action' => 'bagels-abtw-increase-font-size',
            ),
            array(
                'title' => 'Decrease font size',
                'icon' => 'fa-regular fa-magnifying-glass-minus',
                'action' => 'bagels-abtw-decrease-font-size',
            ),
            array(
                'title' => 'Greyscale',
                'icon' => 'fa-solid fa-circle-half-stroke',
                'action' => 'bagels-abtw-greyscale',
            ),
            array(
                'title' => 'Light background',
                'icon' => 'fa-sharp fa-regular fa-lightbulb',
                'action' => 'bagels-abtw-light-background',
            ),
            array(
                'title' => 'Links underlined',
                'icon' => 'fa-regular fa-link',
                'action' => 'bagels-abtw-links-underlined',
            ),
            array(
                'title' => 'Readable font',
                'icon' => 'fa-regular fa-font',
                'action' => 'bagels-abtw-readable-font',
            ),
            array(
                'title' => 'Reset',
                'icon' => 'fa-sharp fa-regular fa-rotate-left',
                'action' => 'bagels-abtw-reset',
            ),
        );
    }

    // Load markup
    public function create_accessibility_menu_widget( $atts ) {
        $menu_items = $this->get_menu_items();
        $position = 'left';
        
        if ( !empty( $atts ) && !empty( $atts[ 'position' ] ) ) {
            $position = $atts[ 'position' ];
        }

        if( !empty( $menu_items ) ): ?>
            <nav class="bagels-accessibility-widget <?php echo $position; ?>">
                <a class="abtw-tt-link" href="#" title="<?php _e( "Accessibility Tools" ); ?>" aria-label="<?php _e( "View Accessibility Tools", 'bagels' ); ?>">
                    <i class="fa-regular fa-wheelchair"></i>
                </a>

                <div class="abtw-toolbar">
                    <h3 class="abtw-t-title"><?php _e( "Accessibility Tools" ) ?></h3>
                    
                    <ul class="abtw-t-items">
                        <?php foreach ( $menu_items as $key => $menu_item ): ?>
                            <?php if ( !empty( $menu_item[ 'action' ] ) ): ?>
                                <li class="abtw-t-i-single">
                                    <a href="#" class="abtw-t-i-s-link <?php echo $menu_item[ 'action' ] ;?>" data-bagels-abtw-action="<?php echo $menu_item[ 'action' ]; ?>" aria-label="<?php _e( !empty( $menu_item[ 'title' ] ) ? $menu_item[ 'title' ] : "", 'bagels' ); ?>">
                                        <?php if ( !empty( $menu_item[ 'icon' ] ) ): ?>
                                            <div class="abtw-t-i-s-l-icon">
                                            <i class="abtw-t-i-s-l-i-init <?php echo $menu_item[ 'icon' ]; ?>"></i>
                                                <i class="fa-sharp fa-solid fa-square-check abtw-t-i-s-l-i-check"></i>
                                            </div>
                                        <?php endif; ?>

                                        <?php if ( !empty( $menu_item[ 'title' ] ) ): ?>
                                            <div class="abtw-t-i-s-l-title"><?php echo $menu_item[ 'title' ]; ?></div>
                                        <?php endif; ?>
                                    </a>
                                </li>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </nav>
        <?php endif;
    }
}

new ACCESSIBILITY_WIDGET();