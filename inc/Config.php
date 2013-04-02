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
    private $_config = NULL;
    
    public function __construct()
    {
        $this->_config = $this->get();
    }
    
    function load()
    {
        //Load plugins options
        $this->_config = get_option(WP_PJAX_OPTIONS_KEY, FALSE);
        
        //If not set, add the option
        if(!$this->_config)
        {
            add_option( WP_PJAX_OPTIONS_KEY, array(), '', 'yes' );
        }
    }
    
    function get()
    {
        if( empty($this->_config))
        {
            $this->load();
        }
        
        return $this->_config;
    }
    
    function get_defaults()
    {
        return array(
            WP_PJAX_CONFIG_PREFIX.'menu-selector' => 'body a',
            WP_PJAX_CONFIG_PREFIX.'content-selector' => '#main',
            WP_PJAX_CONFIG_PREFIX.'menu-active-class' => 'current_page_item current_menu_item',
            WP_PJAX_CONFIG_PREFIX.'show-toggle' => 1,
            WP_PJAX_CONFIG_PREFIX.'load-timeout' => 4000, 
            WP_PJAX_CONFIG_PREFIX.'show-notice' => 1, 
            WP_PJAX_CONFIG_PREFIX.'show-extended-notice' => 0,
            WP_PJAX_CONFIG_PREFIX.'notice-sticky' => 1,
            WP_PJAX_CONFIG_PREFIX.'page-cache' => 0, 
            WP_PJAX_CONFIG_PREFIX.'browser-page-cache' => 0, 
            WP_PJAX_CONFIG_PREFIX.'page-cache-lifetime' => 600,
            WP_PJAX_CONFIG_PREFIX.'page-cache-exceptions' => 'wp-admin',
            WP_PJAX_CONFIG_PREFIX.'strip-cookies' => 0,
            WP_PJAX_CONFIG_PREFIX.'page-cache-prefetch' => 0,
            WP_PJAX_CONFIG_PREFIX.'page-cache-prefetch-interval' => 300,
            WP_PJAX_CONFIG_PREFIX.'page-cache-prefetch-pages-per-interval' => 20,
            //WP_PJAX_CONFIG_PREFIX.'page-cache-prefetch-sitemap-url' => '',
            WP_PJAX_CONFIG_PREFIX.'page-cache-prefetch-sitemap-refresh-interval' => 6000
        );
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
        
        $available_options = array(
            //Clear cache
            WP_PJAX_CONFIG_PREFIX.'clear-cache',
            //Enable plugin
            WP_PJAX_CONFIG_PREFIX.'enable',
            //Basic settings / Selectors
            WP_PJAX_CONFIG_PREFIX.'menu-selector', 
            WP_PJAX_CONFIG_PREFIX.'content-selector',
            WP_PJAX_CONFIG_PREFIX.'menu-active-class',
            WP_PJAX_CONFIG_PREFIX.'show-toggle', 
            //Advanced settings
            //General
            WP_PJAX_CONFIG_PREFIX.'load-timeout',
            //Notices
            WP_PJAX_CONFIG_PREFIX.'show-notice', 
            WP_PJAX_CONFIG_PREFIX.'show-extended-notice',
            WP_PJAX_CONFIG_PREFIX.'notice-timeout',
            WP_PJAX_CONFIG_PREFIX.'notice-sticky',
            //Page Cache
            WP_PJAX_CONFIG_PREFIX.'page-cache',
            WP_PJAX_CONFIG_PREFIX.'browser-page-cache',
            WP_PJAX_CONFIG_PREFIX.'page-cache-lifetime',
            WP_PJAX_CONFIG_PREFIX.'page-cache-exceptions',
            //Strip cookies
            WP_PJAX_CONFIG_PREFIX.'strip-cookies',
            WP_PJAX_CONFIG_PREFIX.'strip-cookies-list',
            //Page Cache Prefetch
            WP_PJAX_CONFIG_PREFIX.'page-cache-prefetch',
            WP_PJAX_CONFIG_PREFIX.'page-cache-prefetch-interval',
            WP_PJAX_CONFIG_PREFIX.'page-cache-prefetch-pages-per-interval',
            //Prefetch
            WP_PJAX_CONFIG_PREFIX.'page-cache-prefetch-sitemap-url',
            WP_PJAX_CONFIG_PREFIX.'page-cache-prefetch-sitemap-refresh-interval', 
            WP_PJAX_CONFIG_PREFIX.'page-cache-prefetch-sitemap-refresh-on-publish',
            //Fading
            WP_PJAX_CONFIG_PREFIX.'content-fade',
            WP_PJAX_CONFIG_PREFIX.'content-fade-timeout-in',
            WP_PJAX_CONFIG_PREFIX.'content-fade-timeout-out',
        );
        
        
        $wp_pjax_options = $this->get();
        
        
        
        if( isset($_POST[WP_PJAX_CONFIG_PREFIX.'clear-cache']))
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
        if( isset($_POST[WP_PJAX_CONFIG_PREFIX.'load-default-settings']))
        {
            $wp_pjax_options = array_merge($wp_pjax_options, $this->get_defaults());
            
            update_option( WP_PJAX_OPTIONS_KEY, $wp_pjax_options ); 
            
            echo '<div id="setting-error-settings_updated" class="updated settings-error"><p><strong>Deafault settings loaded!</strong></p></div>';
        }
        
        
        //print_r($wp_pjax_options);
        
        include WP_PJAX_PLUGIN_PATH.'views/configuration.php';
    }
    
}
