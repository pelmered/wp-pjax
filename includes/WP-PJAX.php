<?php
/**
 * Description of wp-pjax
 *
 * @author Peter Elmered
 */
 
//var_dump(get_transient('pjax_page_cache_page-na_pagename-fasiliteter'));


require_once 'Util.php';

//define('WP_DEBUG', true);
//error_reporting(E_ALL ^ E_NOTICE ^ E_DEPRECATED );
//ini_set("display_errors", 1);


class WP_PJAX_WP_PJAX
{

    var $is_pjax; //Is current request PJAX?
    var $_config = array();
    var $plugin_url;
    var $plugin_path;
    var $page_cache = null;

    /**
     * Instance of this class.
     *
     * @since    0.1.0
     * @var      object
     */
    protected static $instance = null;

    private function __construct() {

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
    
    function run()
    {
        //Set plugin url
        $this->plugin_url = plugins_url().'/wp-pjax';
        //Set plugin path
        $this->plugin_path = plugin_dir_path(dirname(__FILE__));
        
        $config = wp_pjax_get_instance('Config');
        $this->_config = $config->get();
        
        //$this->_config['debug_mode'] = TRUE;
        
        if( is_admin() )
        {
            //Add admin settings menu item
            // add_action('admin_menu', array(&$config, 'admin_pages') );
            
            //Configuration page styles

            add_action( 'wp_enqueue_scripts', function(){
                wp_enqueue_style( 'wp-pjax-admin', $this->plugin_url . '/css/wp-pjax-admin.css' ) ;  
            });


        }

        //var_dump(wp_pjax_config()->get( 'enable' ));
        if( wp_pjax_config()->get( 'enable' ) != 1 )
        {
            return;
        }

        //var_dump(wp_pjax_config()->get( 'enable' ));
        
        $this->is_pjax = is_pjax_request();
        
        global $wp_pjax_options;
        $wp_pjax_options = $this->_config;



        
        // add_action('plugins_loaded', array($this, 'test'), 1);    // lower priority - allow packages to load

        /*
        $this->_config['wp-pjax-page_cache'] = 1;
        $this->_config['wp-pjax-page_cache_prefetch'] = 0;
        $this->_config['wp-pjax-show_notice'] = 1;
        $this->_config['wp-pjax-show_extended_notice'] = 1;
        $this->_config['wp-pjax-notice_sticky'] = 1;
        $this->_config['wp-pjax-notice_timeout'] = 500;
        $this->_config['wp-pjax-content_fade'] = 1;
        $this->_config['wp-pjax-content_fade_timeout_out'] = 100;
        $this->_config['wp-pjax-content_fade_timeout_in'] = 100;
        $this->_config['debug_mode'] = 1;
        $this->_config['wp-pjax-show_toggle'] = 1;
        $this->_config['wp-pjax-load_timeout'] = 10;
        $this->_config['wp-pjax-pre-handler'] = '';
        $this->_config['wp-pjax-post-handler'] = '';
        */


        //get_defaults

        if( $this->is_pjax )
        {
            add_action('send_headers', array($this, 'send_headers'), 2, 999 );

            if( wp_pjax_config()->get( 'page_cache' ) == 1 )
            {
                $this->page_cache = wp_pjax_get_instance('PageCache');
                $this->page_cache->init( );

                
                
                //add_action('send_headers', array(&$this, 'send_headers'), 1, 999 );  
            }
            else if(wp_pjax_config()->get( 'show_extended_notice' ) == 1)
            {
                header( 'PJAX-Page-Cache: DISABLED');
            }
            
            //$this->send_headers($wp);
            //$this->pjax_render($wp);
            
            //Include and render page template
            add_action('wp', array(&$this, 'pjax_render') );
            //add_action('template_redirect', array(&$this, 'pjax_render') );
            
            //we only want partial content -> Stop execution
            //die();
        }
        else
        {
            add_action('wp_enqueue_scripts', array(&$this, 'pjax_load') );
            //add_action( 'wp_enqueue_scripts', function() { wp_enqueue_script('jquery-pjax', $this->plugin_url . '/js/jquery-pjax/jquery.pjax.js', array('jquery')); } );


        }
        /*
        global $wp_filter;
        print_r($wp_filter['get_header']);
        */
        //add_action('get_header', array(&$this, 'pjax_render') );
       
        //Add style and scripts to header
        //add_action('wp_head', array(&$this, 'enqueue_assets') );
        
        if( wp_pjax_config()->get( 'page_cache_prefetch' ) == 1)// && $_SERVER['REQUEST_URI'] == '/' )
        {
            $this->page_cache = wp_pjax_get_instance('PageCachePrefetch'); //&
            $this->page_cache->init( $this->_config );

            //add_action('send_headers', array(&$this, 'send_headers'), 1, 999 );  
        }


        
    // Register hooks that are fired when the plugin is activated, deactivated, and uninstalled, respectively.
       // register_activation_hook( __FILE__, array(&$this, 'activate_plugin') );
       // register_deactivation_hook( __FILE__, array(&$this, 'deactivate_plugin') );
       // register_uninstall_hook( __FILE__, array(&$this, 'uninstall_plugin' ) );
        
    }
    
    
    function activate_plugin()
    {
        
    }
    function deactivate_plugin()
    {
        
    }
    function uninstall_plugin()
    {
        
    }
    

    

    
    //function send_headers( $wp, $pg )
    function send_headers( $wp )
    {

        if( wp_pjax_config()->get( 'page_cache' ) == 1 )
        {
            if( empty( $this->page_cache ) )
            {
                $this->page_cache = &wp_pjax_get_instance('PageCache');
                $this->page_cache->init( $this->_config );
            }

            if( $this->page_cache->status !== 'SKIP')
            {
                //Attemt to cache the HTML in the browser cache
                //But do not serve cached pages to prefetch
                if( !wp_pjax_check_request('HTTP_X_WP_PJAX_PREFETCH')  && wp_pjax_config()->get( 'browser_page_cache' ) == 1 )
                {
                    $seconds_to_cache = 650;
                    $ts = gmdate("D, d M Y H:i:s", time() + $seconds_to_cache) . " GMT";
                    header("Expires: $ts");
                    header("Pragma: cache");
                    header("Cache-Control: max-age=$seconds_to_cache");
                }

                //echo $_SERVER['HTTP_COOKIE'];
                // Unset cookies
                if ( wp_pjax_config()->get( 'strip_cookies' ) == 1 && isset($_SERVER['HTTP_COOKIE'])) {
                    $cookies = explode(';', $_SERVER['HTTP_COOKIE']);
                    foreach($cookies as $cookie) {
                        $parts = explode('=', $cookie);
                        $name = trim($parts[0]);
                        setcookie($name, '', time()-1000);
                        setcookie($name, '', time()-1000, '/');
                    }
                    //header_remove('Cookie');
                }
            }


            if(  wp_pjax_config()->get( 'show_extended_notice' ) == 1 && current_user_can('edit_plugins') ) //|| $this->_config['debug_mode'] )
            {
                header('PJAX-loaded-resource: '.$this->page_cache->key );

                if(!isset($this->page_cache->status ))
                {
                    $this->page_cache->status == 'MISS';
                }
                header( 'PJAX-Page-Cache: '.$this->page_cache->status );

                if( $this->page_cache->status == 'HIT' )
                {
                    header('PJAX-Page-Cache: HIT');
                }
                else
                {
                    header('PJAX-Page-Cache: MISS');
                }

            }
        }


        
    }
    

    //PJAX
    function pjax_load()
    {
        $config = wp_pjax_config()->get();

        wp_enqueue_script('jquery-pjax', $this->plugin_url . '/js/jquery-pjax/jquery.pjax.js', array('jquery'));
        wp_enqueue_script('wp-pjax', $this->plugin_url . '/js/wp-pjax.js', array('jquery', 'jquery-pjax'));

        //add_action( 'wp_enqueue_scripts', function() { wp_enqueue_script('wp-pjax', $this->plugin_url . '/wp-pjax.js.php', array('jquery', 'pjax')); }    );


        wp_localize_script( 'wp-pjax', 'wp_pjax_config', $config );
        
        //add_action( 'wp_head', array(&$this, 'generate_js') );
        
        if( wp_pjax_config()->get( 'show_notice' ) == 1  && current_user_can('edit_plugins')|| wp_pjax_config()->get( 'debug_mode' ) )
        {
            wp_enqueue_script('jquery-notice', $this->plugin_url . '/js/jquery.notice.js', array('jquery'));
            wp_enqueue_style( 'wp-pjax', $this->plugin_url . '/css/wp-pjax.css' );
        }
        
        if( wp_pjax_config()->get( 'show_toggle' ) == 1 && current_user_can('edit_plugins') || wp_pjax_config()->get( 'debug_mode' ) )
        {
            wp_enqueue_style( 'wp-pjax', $this->plugin_url . '/css/wp-pjax.css' );
            add_action('wp_footer', array(&$this, 'add_toggle_html') );
        }
    }
    
    
    function add_toggle_html()
    {
        include $this->plugin_path.'views/toggle.php';
    }
    
    function generate_js()
    {
        $wp_pjax_options = $this->_config;
        include $this->plugin_path.'includes/WP-PJAX.js.php';
    }

    function pjax_render( $wp )
    {
        $this->page_cache = new stdClass();
        $this->page_cache = wp_pjax_get_instance('PageCache');
        
        if( wp_pjax_config()->get( 'page_cache' ) == 1 )
        {
            ob_start();  
        }

        //Include the original WP template loader. This will output the right page template/content
        include ABSPATH . WPINC.DIRECTORY_SEPARATOR.'template-loader.php';        

        if( wp_pjax_config()->get( 'page_cache' ) == 1 )
        {
            $page_content = ob_get_clean();
            //echo $page_content;
            //wp_cache_set( 'pjax_post_'.$post_id, $page_content,  'pjax_page_cache', 300 );
            
            $this->page_cache->set($page_content);
            
            echo $page_content;
            die();
        }
        
        return '';
    }
        


}

