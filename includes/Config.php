<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Config
 *
 * @author Peter Elmered
 */
class WP_PJAX_Config
{
    private static $_config = NULL;
    private static $config_option_key = 'wp_pjax_options';

    /**
     * Instance of this class.
     *
     * @since    0.1.0
     * @var      object
     */
    protected static $instance = null;


    private function __construct() {
        //add_action('init', array($this, 'init'), 20);
        self::$_config = $this->get();
    }
    /**
     * Return an instance of this class.
     *
     * @since     0.1.0
     *
     * @return    object    A single instance of this class.
     */
    public static function get_instance() {
        // If the single instance hasn't been set, set it now.
        if (null == self::$instance) {
            self::$instance = new self;
        }
        return self::$instance;
    }

    /*
    public function __construct()
    {
        $this->_config = $this->get();
    }
    */

    static function load()
    {
        update_option( self::$config_option_key, self::get_defaults(), '', 'yes' );

        //Load plugins options
        self::$_config = get_option(self::$config_option_key, FALSE);

        //self::_config['enable'] = 1;

        //If not set, add the option
        if(!self::$_config)
        {
            add_option( self::$config_option_key, self::get_defaults(), '', 'yes' );
        }
    }
    
    static function get( $key = null )
    {
        if( empty(self::$_config) )
        {
            self::load();
        }

        if( $key && key_exists( $key, self::$_config ) )
        {
            return self::$_config[$key];
        }
        
        return self::$_config;
    }

    static function get_available_options()
    {
        return array(
            //Clear cache
            'clear_cache',
            //Enable plugin
            'enable',
            //Basic settings / Selectors
            'menu_selector',
            'content_selector',
            'menu_active_class',
            'show_toggle',
            //Advanced settings
            //General
            'load_timeout',
            //Notices
            'show_notice',
            'show_extended_notice',
            'notice_timeout',
            'notice_sticky',
            //Page Cache
            'page_cache',
            'browser_page_cache',
            'page_cache_lifetime',
            'page_cache_exceptions',
            //Strip cookies
            'strip_cookies',
            'strip_cookies_list',
            //Page Cache Prefetch
            'page_cache_prefetch',
            'page_cache_prefetch_interval',
            'page_cache_prefetch_pages_per_interval',
            //Prefetch
            'page_cache_prefetch_sitemap_url',
            'page_cache_prefetch_sitemap_refresh_interval',
            'page_cache_prefetch_sitemap_refresh_on_publish',
            //Fading
            'content_fade',
            'content_fade_timeout_in',
            'content_fade_timeout_out',
        );
    }

    static function get_defaults()
    {
        $available = self::get_available_options();

        $a = array_combine($available, array_fill(0, count($available), ''));
        //print_r($a);

        return array_merge(
            array_combine($available,
                array_fill(0, count($available), '')
            ), array(
            'enable' => 1,
            'menu_selector' => 'body a',
            'content_selector' => '#main',
            'menu_active_class' => 'current_page_item current_menu_item',
            'show_toggle' => 1,
            'load_timeout' => 4000,
            'show_notice' => 1,
            //'show_extended_notice' => 0,
            'show_extended_notice' => 1,
            'notice_sticky' => 1,
            'page_cache' => 1,
            'browser_page_cache' => 0,
            'page_cache_lifetime' => 600,
            'page_cache_exceptions' => 'wp-admin',
            'strip_cookies' => 0,
            'page_cache_prefetch' => 0,
            'page_cache_prefetch_interval' => 300,
            'page_cache_prefetch_pages_per_interval' => 20,
            //'page_cache_prefetch_sitemap_url' => '',
            'page_cache_prefetch_sitemap_refresh_interval' => 6000
        ) );
    }
    
    
    /**
 * Add admin settingspage
 */
    function admin_pages() {
        add_options_page(  'WP PJAX', 'WP-PJAX', 'manage_options','pe-wp-pjax', array($this, 'configuration_page') ); 
    }
    
/**
 * Prints the content of the configuration page
 */

    function configuration_page() 
    {
        
        if ( !current_user_can( 'manage_options' ) )  {
            wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
        }
        
        $available_options =
        
        
        $wp_pjax_options = $this->get();
        
        
        
        if( isset($_POST['clear_cache']))
        {
            $page_cache = wp_pjax_get_instance('PageCache');
            $page_cache->init( $this->_config );
            $page_cache->clearCache();
            
            echo '<div id="setting-error-settings_updated" class="updated settings-error"><p><strong>Cache cleared!</strong></p></div>';
        }
        else
        {
            $plugin_option_array = array();

            foreach ( $available_options AS $o )
            {
                if( !empty($_POST[$o]) || is_numeric($_POST[$o]) )
                {
                    $plugin_option_array[$o] = $_POST[$o];
                }
            }

            if( !empty($plugin_option_array) )
            {
                update_option( WP_PJAX_OPTIONS_KEY, $plugin_option_array ); 

                echo '<div id="setting-error-settings_updated" class="updated settings-error"><p><strong>Settings saved!</strong></p></div>';
                
                $wp_pjax_options = array_merge($wp_pjax_options, $plugin_option_array);
            }
            
        }
        
        //Load default settings
        if( isset($_POST['load-default-settings']))
        {
            $wp_pjax_options = array_merge($wp_pjax_options, $this->get_defaults());
            
            update_option( WP_PJAX_OPTIONS_KEY, $wp_pjax_options ); 
            
            echo '<div id="setting-error-settings_updated" class="updated settings-error"><p><strong>Default settings loaded!</strong></p></div>';
        }
        
        
        //print_r($wp_pjax_options);
        
        include WP_PJAX_PLUGIN_PATH.'views/configuration.php';
    }
    
}

function wp_pjax_config()
{
    return WP_PJAX_Config::get_instance();
}