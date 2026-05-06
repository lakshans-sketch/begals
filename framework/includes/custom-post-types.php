<?php
/**
 * Custom Post Types of the theme.
 * Included in functions.php
 */

if ( ! class_exists( 'Bagels_custom_post_types' ) ) :
    /*
     * Custom post types class
     */
    class Bagels_custom_post_types{

        public function __construct(){
            add_action( 'init', array( $this, 'initialize' ) );
        }

        // Init
        public function initialize(){
            $this->posttype_testimonials();
            //$this->posttype_services();
            //$this->posttype_projects();
            //$this->posttype_gallery();
        }

        // Testimonials
        public function posttype_testimonials() {
            register_post_type('Testimonials', array(
                'labels' => array(
                    'name' => 'Testimonials',
                    'singular_name' => 'testimonial',
                    'add_new_item' => 'Add New Testimonial',
                    'edit_item' => 'Edit Testimonial',
                ),
                'description' => 'Testimonials',
                'public' => true,
                'menu_position' => 20,
                'supports' => array('title')
            ));
        }

        // Services
        public function posttype_services() {
            register_post_type('Services', array(
                'labels' => array(
                    'name' => 'Services',
                    'singular_name' => 'service',
                    'add_new_item' => 'Add New Service',
                    'edit_item' => 'Edit Service',
                ),
                'description' => 'Services',
                'public' => true,
                'menu_position' => 20,
                'show_in_menu' => true,
                'show_in_nav_menus' => true,
                'supports' => array('title','editor','thumbnail')
            ));
        }

        // Projects
        public function posttype_projects() {
            register_post_type('Projects', array(
                'labels' => array(
                    'name' => 'Projects',
                    'singular_name' => 'project',
                    'add_new_item' => 'Add New Project',
                    'edit_item' => 'Edit Project',
                ),
                'description' => 'Projects',
                'public' => true,
                'menu_position' => 20,
                'supports' => array('title')
            ));
        }

        // Gallery
        public function posttype_gallery() {
            register_post_type('Gallery', array(
                'labels' => array(
                    'name' => 'Gallery',
                    'singular_name' => 'Gallery',
                    'add_new_item' => 'Add New Item',
                    'edit_item' => 'Edit Gallery Item',
                ),
                'description' => 'Gallery',
                'public' => true,
                'menu_position' => 20,
                'supports' => array('title')
            ));
        }
    }

    new Bagels_custom_post_types();

endif;