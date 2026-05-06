<?php
/**
 * Bagels Theme settings class (Shortened to BTS).
 * Included in functions.php
 */

class BTS{

	public static $options = array();

    public function __construct(){
    	$this->add_settings_page();
        add_filter( 'acf/load_field/name=social_icon', array( $this, 'add_social_icons' ) );
        add_action( 'init', array( $this, 'initialize' ) );
    }

    // Init
    public function initialize(){
        
    }

    // Add theme settings page
    public function add_settings_page() {
    	if( function_exists( 'acf_add_options_page' ) ) { 
	        acf_add_options_page( array(
		        'page_title'    => 'Site Settings',
		        'menu_title'    => 'Site Settings',
		        'menu_slug'     => 'site-settings',
		        'capability'    => 'edit_posts',
		        'redirect'      => false
		    ) ); 
	    }
    }

    // Add Social icons list
	public function add_social_icons( $field ) {
	    $field['choices'] = array(
	        'facebook-f' => 'Facebook',
	        'youtube' => 'YouTube',
	        'instagram' => 'Instagram',
	        'twitter' => 'Twitter',
	        'linkedin-in' => 'LinkedIn',
	        'pinterest-p' => 'Pinterest',
	        'vimeo-v' => 'Vimeo',
	        'google-plus-g' => 'Google Plus',
	        'yelp' => 'Yelp',
	    );
	    return $field;  
	}

	public function get_options(){
		$options = array(
			'general' => get_field( 'general', 'options' ),
			'colors' => get_field( 'colors', 'options' ),
			'header' => get_field( 'header', 'options' ),
			'footer' => get_field( 'footer', 'options' ),
			'contact_details' => get_field( 'contact_details', 'options' ),
			'social_icons' => get_field( 'social_icons', 'options' ),
			'page_settings' => get_field( 'page_settings', 'options' ),
			'products_group' => get_field( 'ss_products_group', 'options' ),
			'integrations' => get_field( 'integrations', 'options' )
		);

		return $options;
	}
    
}

add_action( 'acf/init', 'bts_acf_init' );
function bts_acf_init() {
	$bts = new BTS();
	BTS::$options = $bts->get_options();
}