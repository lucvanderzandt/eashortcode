<?php
/**
 * Plugin Name: Eashortcode
 * Plugin URI: https://github.com/lucvanderzandt/eashortcode
 * Version: 1.0
 * Author: Luc van der Zandt <lucvanderzandt@kpnplanet.nl>
 * Author URI: https://github.com/lucvanderzandt
 * Description: A plugin to easily add an 'Add to chart' button in Visual Editor
 * License: Apache License 2.0
 */
 
 class Eashortcode_Class {

     function __construct() {
         add_action( 'admin_init', array( $this, 'setup_eashortcode' ) );
     }
     
     function setup_eashortcode() {
         if( current_user_can( 'edit_posts' ) && current_user_can( 'edit_pages' ) ) {
            add_filter( 'mce_external_plugins', array( &$this, 'add_tinymce_plugin' ) );
            add_filter( 'mce_buttons', array( &$this, 'add_tinymce_toolbar_button' ) );
         }        
     }
     
     function add_tinymce_plugin( $plugin_array ) {
         $plugin_array['eashortcode_class'] = plugin_dir_url ( __FILE__ ) .  'eashortcode.js';
         return $plugin_array;
     }
     
     function add_tinymce_toolbar_button( $buttons ) {
         $newBtns = array (
            'add_product'
         );
         $buttons = array_merge( $buttons, $newBtns );
         return $buttons;
     }
 }
 
 $eashortcode_class = new Eashortcode_Class; 
 
 class Eashortcode_Settings {
     
     function __construct() {
         add_action( 'admin_menu', array( $this, 'eashortcode_add_admin_menu' ) );
         add_action( 'admin_init', array( $this, 'eashortcode_settings_init' ) );
     }

     function eashortcode_add_admin_menu(  ) { 
        add_options_page( 'Eashortcode', 'Eashortcode', 'manage_options', 'eashortcode', 'eashortcode_options_page' );
     }

     function eashortcode_settings_init(  ) { 
        register_setting( 'pluginPage', 'eashortcode_settings' );
        add_settings_section(
            'eashortcode_pluginPage_section', 
            __( 'Settings', 'eashortcode' ), 
            'eashortcode_settings_section_callback', 
            'pluginPage'
        );

        add_settings_field( 
            'consumer_key', 
            __( 'Consumer key', 'eashortcode' ), 
            'eashortcode_text_field_0_render', 
            'pluginPage', 
            'eashortcode_pluginPage_section' 
        );

        add_settings_field( 
            'consumer_secret', 
            __( 'Consumer secret', 'eashortcode' ), 
            'eashortcode_text_field_1_render', 
            'pluginPage', 
            'eashortcode_pluginPage_section' 
        );
        
        $options = get_option('eashortcode_settings');
        $script_params = array(
            'consumer_key' => $options['consumer_key'],
            'consumer_secret' => $options['consumer_secret']
        );
        wp_enqueue_script( 'eashortcode', plugin_dir_url ( __FILE__ ) .  'eashortcode.js', false );
        wp_localize_script( 'eashortcode', 'scriptParams', $script_params );
        
        $plugin = plugin_basename( __FILE__ );
        add_filter( 'plugin_action_links_$plugin', 'plugin_add_settings_link' );
     }
 
     function eashortcode_text_field_0_render(  ) { 
        $options = get_option( 'eashortcode_settings' );
        ?>
        <input type='text' name='eashortcode_settings[consumer_key]' value='<?php echo $options['consumer_key']; ?>'>
        <?php
     }

     function eashortcode_text_field_1_render(  ) { 
        $options = get_option( 'eashortcode_settings' );
        ?>
        <input type='text' name='eashortcode_settings[consumer_secret]' value='<?php echo $options['consumer_secret']; ?>'>
        <?php
     }

     function eashortcode_settings_section_callback(  ) { 
        echo __( '<div>This software needs your API keys to retrieve the products you\'re offering</div>
                  <div>Retrieve your consumer key and consumer secret on the <a href=\'/wp-admin/admin.php?page=wc-settings&tab=api\'>WooCommerce settings page</a></div>', 'eashortcode' );
     }

     function eashortcode_options_page(  ) { 
        ?>
        <form action='options.php' method='post'>
            <h1>Eashortcode</h1>
            <?php
            settings_fields( 'pluginPage' );
            do_settings_sections( 'pluginPage' );
            submit_button();
            ?>
        </form>
        <?php
     }
 
     function plugin_add_settings_link( $links ) {
        $settings_link = '<a href=\'options-general.php?page=eashortcode\'>' . __( 'Settings' ) . '</a>';
        array_unshift( $links, $settings_link );
        return $links;
     }
 }
 
 $eashortcode_settings = new Eashortcode_Settings; 