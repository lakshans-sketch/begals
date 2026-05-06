<?php
/**
 * Creates shortcode for newsletter widget
 */

class NEWSLETTER_WIDGET{
    public function __construct(){
        add_action( 'wp_enqueue_scripts', array( $this, 'init_plugin' ) );
        add_action( 'wp_ajax_nopriv_send_newsletter_request', array( $this, 'send_newsletter_request' ) );
        add_action( 'wp_ajax_send_newsletter_request', array( $this, 'send_newsletter_request' ) );

        add_shortcode( 'newsletter_widget', array( $this, 'create_newsletter_widget' ) );
    }

    public function init_plugin(){
        wp_enqueue_style('nw-style', get_theme_file_uri( '/framework/includes/plugins/newsletter-widget/assets/style.css' ), array(), VERSION_NUMBER );

        wp_enqueue_script( 
            'ajax_script', 
            get_theme_file_uri( '/framework/includes/plugins/newsletter-widget/assets/script.js' ), 
            array('jquery'),
            VERSION_NUMBER,
            TRUE 
        );
        
        wp_localize_script( 
            'ajax_script', 
            'myAjax', 
            array(
                'url'   => admin_url( 'admin-ajax.php' ),
                'nonce' => wp_create_nonce( "send_newsletter_request_nonce" ),
            )
        );
    }

    // Load markup
    public function create_newsletter_widget() {
        if ( is_plugin_active( 'advanced-custom-fields-pro/acf.php' ) ):
            $integrations_group = BTS::$options[ 'integrations' ];

            if( !empty( $integrations_group[ 'cloudmail' ][ 'api_token' ] ) && !empty( $integrations_group[ 'cloudmail' ][ 'list_id' ] ) ): ?>
                <?php $footer = BTS::$options[ 'footer' ]; ?>

                <div class="bagels-news-letter-widget">
                    <form method="post" class="nlw-form" action="">
                        <div class="nlw-f-1">
                            <div class="nlw-f-input">
                                <input type="email" name="EMAIL" class="" placeholder="<?php _e( !empty( $footer[ 'email_placeholder' ] ) ? !empty( $footer[ 'email_placeholder' ] ) : "Enter your email address", 'bagels' ) ?>" id="nlw-f-i-email" required>
                            </div>

                            <div class="nlw-f-button">
                                <button type="submit" id="nlw-f-b-submit" aria-label="<?php _e( "Subscribe to newsletter" ); ?>">
                                    <span class="nlw-f-b-s-1"><i class="fas fa-paper-plane"></i></span>
                                    <div class="spinner"></div>
                                </button>
                            </div>
                        </div>

                        <div class="nlw-f-msg nlw-f-response"></div>
                    </form>
                </div>
            <?php endif;
        endif;
    }

    /*
    * Send Newsletter (Cloudmail) request
    * Called via AJAX
    */
    public function send_newsletter_request(){
        $integrations_group = BTS::$options[ 'integrations' ];

        if( empty( $integrations_group[ 'cloudmail' ][ 'api_token' ] ) || empty( $integrations_group[ 'cloudmail' ][ 'list_id' ] ) ){
            wp_send_json_error( __( 'Cloudmail is not setup yet.' ) );
            wp_die();
        }

        if( !isset( $_POST[ 'email' ] ) || empty( $_POST[ 'email' ] ) ){
            wp_send_json_error( __( 'Please enter your subscription email.' ) );
            wp_die();
        }

        $email = $_POST[ 'email' ];
        $api_url = 'https://cloudmail.docloud.lk/api/v1/subscribers?list_uid=' . $integrations_group[ 'cloudmail' ][ 'list_id' ];
        $headers = [ 'accept: application/json' ];
        $body = [
            'api_token' => $integrations_group[ 'cloudmail' ][ 'api_token' ],
            'EMAIL' => $email,
        ];

        $ch = curl_init( $api_url );
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        curl_exec($ch);
        curl_close($ch);
        wp_send_json_success();
        wp_die();
    }
}

new NEWSLETTER_WIDGET();