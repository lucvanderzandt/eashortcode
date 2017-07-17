<?php
/**
 * Plugin Name: Eashortcode
 * Plugin URI: https://github.com/lucvanderzandt/eashortcode
 * Version: 1.1
 * Author: Luc van der Zandt <lucvanderzandt@kpnplanet.nl>
 * Author URI: https://github.com/lucvanderzandt
 * Description: A plugin to easily add a WooCommerce 'Add to chart' button in Visual Editor
 * License: Apache License 2.0
 */
include_once('class-eashortcode-plugin-settings.php');
 
class Eashortcode_Plugin {

    /**
     * @constructor
     */
    function __construct() {
        add_action( 'admin_init', array( $this, 'setup_eashortcode' ) ); 
    }
    
    /**
     * Register plugin with WordPress
     */
    function setup_eashortcode() {
        if( current_user_can( 'edit_posts' ) && current_user_can( 'edit_pages' ) ) {
            add_filter( 'mce_external_plugins', array( &$this, 'add_tinymce_plugin' ) );
            add_filter( 'mce_buttons', array( &$this, 'add_tinymce_toolbar_button' ) );
        }
    }
    
    /**
     * Hook the plugin to TinyMCE
     * 
     * @return Updated plugin array
     */
    function add_tinymce_plugin( $plugin_array ) {
        $plugin_array['eashortcode_class'] = plugin_dir_url ( __FILE__ ) .  'js/script.js';
        return $plugin_array;
    }
    
    /**
     * Add a new button to TinyMCE
     *
     * @return Updated button array
     */
    function add_tinymce_toolbar_button( $buttons ) {
        $newBtns = array (
           'add_product'
        );
        $buttons = array_merge( $buttons, $newBtns );
        return $buttons;
    }
}

$eashortcode_plugin = new Eashortcode_Plugin;
$eashortcode_settings = new Eashortcode_Settings;