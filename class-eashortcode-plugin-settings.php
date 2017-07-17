<?php
class Eashortcode_Settings {
    
    /**
     * @constructor
     */
    function __construct() {
        if( is_admin() ) {
            add_action( 'admin_menu', array( $this, 'eashortcode_add_admin_menu' ) );
            add_action( 'admin_init', array( $this, 'eashortcode_settings_init' ) );
        }
    }
    
    function eashortcode_add_admin_menu(  ) { 
        add_options_page( 'Eashortcode', 'Eashortcode', 'manage_options', 'eashortcode',
            array( $this, 'eashortcode_options_page' ) );
     }
 
    /**
     * Setup settings page
     */
    function eashortcode_settings_init(  ) { 
    
        register_setting( 'pluginPage', 'eashortcode_settings' );
        
        add_settings_section(
            'eashortcode_pluginPage_section', 
            __( 'Settings', 'eashortcode' ), 
            array( $this, 'eashortcode_settings_section_callback' ), 
            'pluginPage'
        );

        add_settings_field( 
            'consumer_key', 
            __( 'Consumer key', 'eashortcode' ), 
            array( $this, 'consumer_key_render' ), 
            'pluginPage', 
            'eashortcode_pluginPage_section' 
        );

        add_settings_field( 
            'consumer_secret', 
            __( 'Consumer secret', 'eashortcode' ), 
            array( $this, 'consumer_secret_render' ), 
            'pluginPage', 
            'eashortcode_pluginPage_section' 
        );
        
        $options = get_option('eashortcode_settings');
        $script_params = array(
            'consumer_key' => $options['consumer_key'],
            'consumer_secret' => $options['consumer_secret']
        );
        wp_enqueue_script( 'eashortcode', plugin_dir_url ( __FILE__ ) .  'js/script.js', false );
        wp_localize_script( 'eashortcode', 'scriptParams', $script_params );
        
        add_filter( 'plugin_action_links_eashortcode/class-eashortcode-plugin.php', 
            array( &$this, 'plugin_add_settings_link' ) );
    }
 
    /**
     * Render consumer key settings field
     */
    function consumer_key_render(  ) { 
        $options = get_option( 'eashortcode_settings' );
        ?>
        <input type='text' name='eashortcode_settings[consumer_key]' 
            value='<?php echo $options['consumer_key']; ?>'>
        <?php
    }

    /**
     * Render consumer secret settings field
     */
    function consumer_secret_render(  ) { 
        $options = get_option( 'eashortcode_settings' );
        ?>
        <input type='text' name='eashortcode_settings[consumer_secret]' 
            value='<?php echo $options['consumer_secret']; ?>'>
        <?php
    }

    /**
     * Render settings text
     */
    function eashortcode_settings_section_callback(  ) { 
        ?>
        <div>This software needs your API keys to retrieve the products you're offering</div>
        <div>Retrieve your consumer key and consumer secret on the 
        <a href='/wp-admin/admin.php?page=wc-settings&tab=api'>WooCommerce settings page</a></div>
        <?php
    }

    /**
     * Render settings page
     */
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
    
    /**
     * Add settings link to plugin overview page
     *
     * @return Updated link array
     */
    function plugin_add_settings_link( $links ) {
        $settings_link = '<a href=\'options-general.php?page=eashortcode\'>' . 
            __( 'Settings' ) . '</a>';
        array_unshift( $links, $settings_link );
        return $links;
    }
}