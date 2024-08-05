<?php
/**
 * Plugin Name: Gravity Forms Custom Repeater Add-On
 * Description: A custom add-on to add repeater functionality to Gravity Forms.
 * Version: 1.0
 * Author: Your Name
 */

// Prevent direct access to the file
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Register the custom repeater field
add_action( 'gform_loaded', 'register_custom_repeater_field', 10, 2 );
function register_custom_repeater_field() {
    require_once plugin_dir_path( __FILE__ ) . 'gravity-forms-repeater/includes/class-gf-custom-repeater.php';
    GF_Fields::register( new GF_Custom_Repeater_Field() );
}

// Enqueue scripts and styles
add_action( 'wp_enqueue_scripts', 'enqueue_custom_repeater_scripts' );
function enqueue_custom_repeater_scripts() {
    wp_enqueue_script( 'gf-custom-repeater', plugin_dir_url( __FILE__ ) . 'gravity-forms-repeater/assets/repeater.js', array( 'jquery' ), '1.0', true );
    wp_enqueue_style( 'gf-custom-repeater', plugin_dir_url( __FILE__ ) . 'gravity-forms-repeater/assets/repeater.css', array(), '1.0' );
}
?>
