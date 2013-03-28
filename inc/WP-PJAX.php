<?php
/**
 * Description of wp-pjax
 *
 * @author Peter Elmered
 */
 
//var_dump(get_transient('pjax_page_cache_page-na_pagename-fasiliteter'));


require_once 'Util.php';

define('WP_DEBUG', true);
error_reporting(E_ALL ^ E_NOTICE ^ E_DEPRECATED );
ini_set("display_errors", 1);


class WP_PJAX_WP_PJAX
{

    var $is_pjax; //Is current request PJAX?
    var $_config = array();
    var $plugin_url;
    var $plugin_path;
    var $page_cache = '';
    
    
    function run()
    {
        //Set plugin url
        $this->plugin_url = plugins_url().'/wp-pjax';
        //Set plugin path
        $this->plugin_path = plugin_dir_path(dirname(__FILE__));
        
        $config = wp_pjax_get_instance('Config');
        $this->_config = $config->get();
        
        register_activation_hook( __FILE__, array(&$this, 'activate_plugin') );
        register_deactivation_hook( __FILE__, array(&$this, 'deactivate_plugin') );
        
        $this->_config['debug_mode'] = TRUE;
        
        if( is_admin() )
        {
            //Add admin settings menu item
            add_action('admin_menu', array(&$config, 'admin_pages') );
            
            //Configuration page styles
            wp_enqueue_style( 'wp-pjax-admin', $this->plugin_url . '/css/wp-pjax-admin.css' );
        }
        
        if( $this->_config['pe-wp-pjax-enable'] != 1 )
        {
            return;
        }
        
        $this->is_pjax = is_pjax_request();
        
        global $wp_pjax_options;
        $wp_pjax_options = $this->_config;

        
    //    add_action('plugins_loaded', array($this, 'test'), 1);    // lower priority - allow packages to load

        
        if( $this->is_pjax )
        {
            
            if( $this->_config['pe-wp-pjax-page-cache'] == 1 )
            {
                add_action('send_headers', array(&$this, 'send_headers'), 2, 999 );  
            
                $this->page_cache = &wp_pjax_get_instance('PageCache');
                $this->page_cache->init( $this->_config );
                
                
                
                //add_action('send_headers', array(&$this, 'send_headers'), 1, 999 );  
            }
            
        //phpconsole(array('action'=> 'Initialize', 'start' => $start + $i ), 'peter');
            //$this->send_headers($wp);
            //$this->pjax_render($wp);
            
//            die('asdasd');
            //Include and render page template
            add_action('wp', array(&$this, 'pjax_render') );
            //add_action('template_redirect', array(&$this, 'pjax_render') );
            
            //we only want partial content -> Stop execution
            //die();
        }
        else
        {
            add_action('get_header', array(&$this, 'pjax_load') );
         }
        /*
        global $wp_filter;
        print_r($wp_filter['get_header']);
        */
        //add_action('get_header', array(&$this, 'pjax_render') );
       
        //Add style and scripts to header
        //add_action('wp_head', array(&$this, 'enqueue_assets') );
        
        if( $this->_config['pe-wp-pjax-page-cache-prefetch'] == 1)// && $_SERVER['REQUEST_URI'] == '/' )
        {
            $this->page_cache = &wp_pjax_get_instance('PageCachePrefetch');
            $this->page_cache->init( $this->_config );

            //add_action('send_headers', array(&$this, 'send_headers'), 1, 999 );  
        }


        
    }
    
    
    function activate_plugin()
    {
        
    }
    function deactivate_plugin()
    {
        
    }
    

    

    
    function send_headers( $wp, $pg )
    {
        phpconsole(array('config' => $this->_config), 'peter');
        
        if( $pg->status !== 'SKIP')
        {
            //Attemt to cache the HTML in the browser cache
            //Do not serve cached pages to prefetch
            if( !wp_pjax_check_request('HTTP_X_WP_PJAX_PREFETCH')  && $this->_config['pe-wp-pjax-browser-page-cache'] == 1 )
            {
                $seconds_to_cache = 650;
                $ts = gmdate("D, d M Y H:i:s", time() + $seconds_to_cache) . " GMT";
                header("Expires: $ts");
                header("Pragma: cache");
                header("Cache-Control: max-age=$seconds_to_cache");
          
            }
            //echo $_SERVER['HTTP_COOKIE'];
            // Unset cookies
            
            if ( $this->_config['pe-wp-pjax-strip-cookies'] == 1 && isset($_SERVER['HTTP_COOKIE'])) {
                $cookies = explode(';', $_SERVER['HTTP_COOKIE']);
                foreach($cookies as $cookie) {
                    $parts = explode('=', $cookie);
                    $name = trim($parts[0]);
                    setcookie($name, '', time()-1000);
                    setcookie($name, '', time()-1000, '/');
                }
            }
        }
        

        header_remove('Cookie');

        if(  $this->_config['pe-wp-pjax-show-extended-notice'] == 1 && current_user_can('edit_plugins') || $this->_config['debug_mode'] )
        {
            //var_dump($this->page_cache['hit']);
            //die('fakk2');
            
            //$pg = wp_pjax_get_instance('PageCache');
            header('PJAX-loaded-resource: '.$pg->key );    
            
            if(!isset($pg->status ))
            {
                $pg->status == 'MISS';
            }
            
            header( 'PJAX-Page-Cache: '.$pg->status );
            /*
            if( $pg->status == 'HIT' )
            {
                header('PJAX-Page-Cache: HIT');
            }
            else
            {
                header('PJAX-Page-Cache: MISS');
            }
            */
        }
        
    }
    

    //PJAX
    function pjax_load()
    {
        wp_enqueue_script('wp-pjax', $this->plugin_url . '/js/jquery-pjax/jquery.pjax.js', array('jquery'));
        //wp_enqueue_script('theme-pjax', $this->plugin_url . '/js/wp-pjax.js', array('jquery', 'pjax'));
        //wp_enqueue_script('theme-pjax', $this->plugin_url . '/wp-pjax.js.php', array('jquery', 'pjax'));
        
        add_action( 'wp_head', array(&$this, 'generate_js') );
        
        if( $this->_config['pe-wp-pjax-show-notice'] == 1  && current_user_can('edit_plugins')|| $this->_config['debug_mode'] )
        {
            wp_enqueue_script('jquery-notice', $this->plugin_url . '/js/jquery.notice.js', array('jquery'));
            
            wp_enqueue_style( 'wp-pjax', $this->plugin_url . '/css/wp-pjax.css' );
        }
        
        if( $this->_config['pe-wp-pjax-show-toggle'] == 1 && current_user_can('edit_plugins') || $this->_config['debug_mode'] )
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
        include $this->plugin_path.'inc/WP-PJAX.js.php';
    }

    function pjax_render( $wp )
    {
        $this->page_cache = &wp_pjax_get_instance('PageCache');
        
        if( $this->_config['pe-wp-pjax-page-cache'] == 1 )
        {
            ob_start();  
        }

        //Include the original WP tamplate loader. This will output the right page template/content
        include ABSPATH . WPINC.DIRECTORY_SEPARATOR.'template-loader.php';        

        if( $this->_config['pe-wp-pjax-page-cache'] == 1 )
        {
            $page_content = ob_get_clean();
            //echo $page_content;
            //wp_cache_set( 'pjax_post_'.$post_id, $page_content,  'pjax_page_cache', 300 );
            
            $this->page_cache->set($page_content);
            
            echo $page_content;
            die('asdasdasdsadsad');
            //echo 'set transient: ';
            //echo $this->page_cache['key'];
            //var_dump($s);
        }
        
        return '';
        
        if (array_key_exists('HTTP_X_PJAX', $_SERVER) && $_SERVER['HTTP_X_PJAX'])
        {
            //$start_time = microtime(true);
            $_pjax = TRUE;
            
            global $wp_query;
            
            $post = $wp_query->post;
            $post_id = $wp_query->post->ID;
            
            do_action('wp_pjax_before_render', $post);

            //Get page content from WP Object cache
            
            //$page_content = wp_cache_get( 'pjax_post_'.$post_id, 'pjax_page_cache' );
            //$page_content = get_transient( 'pjax_post_'.$post_id );
            $page_content = FALSE;
            
            do_action('wp_pjax_header', $wp_query->post, $this->_config);
            
            if ( $page_content === FALSE )  {
                if(  $this->_config['pe-wp-pjax-show-extended-notice'] && current_user_can('edit_plugins') || $this->_config['debug_mode'] )
                {
                    header('PJAX-Cache: MISS!');
                }
                
                $theme_path = get_template_directory();

                ob_start();
            
                // Load PJAX template conditionally based on post's template (as defined via Wordpress Administration)
                $template_name = get_post_meta($post_id, '_wp_page_template', true);
                //var_dump( $template_name );
                
                if( file_exists( $theme_path . DIRECTORY_SEPARATOR . $template_name ) )
                {
                    include($theme_path . DIRECTORY_SEPARATOR . $template_name);
                }
                else
                {   //If template file not found, revert to the default /page.php template 
                    include($theme_path . DIRECTORY_SEPARATOR . 'page.php');
                }

                $page_content = ob_get_clean();

                
                //wp_cache_set( 'pjax_post_'.$post_id, $page_content,  'pjax_page_cache', 300 );
                set_transient( 'pjax_post_'.$post_id, $page_content, 300 );
                
            } else {

                if(  $this->_config['pe-wp-pjax-show-extended-notice']  && current_user_can('edit_plugins') || $this->_config['debug_mode'] )
                {
                    header('PJAX-Cache: HIT!');
                }
                
            }
            
            //Echp page content
            echo $page_content;
            
            //Stop execution. We do not want to execure more code
            //die();
        }
        else
            $_pjax = FALSE;
    }
        


}

