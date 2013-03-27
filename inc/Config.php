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
            'pe-wp-pjax-clear-cache',
            'pe-wp-pjax-enable',
            'pe-wp-pjax-menu-selector', 'pe-wp-pjax-content-selector', 'pe-wp-pjax-menu-active-class',
            'pe-wp-pjax-content-fade','pe-wp-pjax-content-fade-timeout-in','pe-wp-pjax-content-fade-timeout-out',
            'pe-wp-pjax-show-toggle', 
            'pe-wp-pjax-load-timeout',
            //Notices
            'pe-wp-pjax-show-notice', 'pe-wp-pjax-show-extended-notice','pe-wp-pjax-notice-timeout','pe-wp-pjax-notice-sticky',
            //Page Cache
            'pe-wp-pjax-page-cache','pe-wp-pjax-browser-page-cache','pe-wp-pjax-page-cache-lifetime','pe-wp-pjax-page-cache-exceptions',
            //Strip cookies
            'pe-wp-pjax-strip-cookies','pe-wp-pjax-strip-cookies-list',
            //Page Cache Prefetch
            'pe-wp-pjax-page-cache-prefetch','pe-wp-pjax-page-cache-prefetch-interval','pe-wp-pjax-page-cache-prefetch-pages-per-interval',
            'pe-wp-pjax-page-cache-prefetch-sitemap-url','pe-wp-pjax-page-cache-prefetch-sitemap-refresh-interval', 'pe-wp-pjax-page-cache-prefetch-sitemap-refresh-on-publish'
        );
        
        if( isset($_POST['pe-wp-pjax-clear-cache']))
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
            }
            
        }
        $wp_pjax_options = $this->_config;
        //print_r($wp_pjax_options);
        
        include WP_PJAX_PLUGIN_PATH.'views/configuration.php';
    }
    
}
