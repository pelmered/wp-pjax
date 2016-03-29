<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of cache
 *
 * @author Peter Elmered
 */




class WP_PJAX_PageCache
{
    public $key;
    public $status;
    var $_config;
    private $transient_cache_prefix = 'wp_pjax_pc_';

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
    
    function init()
    {
        //$this->_config = $config;
        
        //apply_filters('wp_pjax_use_pg', $wp_pjax_options))
        

        add_filter( 'wp_pjax_use_pg', array( &$this, 'use_pg' ), 1, 1 ); 
        add_action( 'parse_request', array( &$this, 'page_cache' ), 1, 1 );
    }
    
    function use_pg($wp)
    {
        //print_r($wp);
        //Do not serve cached pages to prefetch
        if( wp_pjax_check_request('HTTP_X_WP_PJAX_PREFETCH')  )
        {
            return FALSE;
        }

        //$exceptions = explode("\n", wp_pjax_config()->get( 'page_cache_exceptions']);
        $exceptions = explode("\n", wp_pjax_config()->get('page_cache_exceptions'));

        //print_r($exceptions);
        
        foreach( $wp->query_vars AS $qv )
        {
            if( empty($qv) )
            {
                continue;
            }
            
            foreach( $exceptions AS $e )
            {
                //echo $qv . '___'.$e."\n\n";
                
                if(strpos($e, $qv) !== false) 
                {
                    return FALSE;
                }
            }
            
            
        }
        
        
//        wp_pjax_config()->get( 'page_cache_exceptions']
        
        
        
        return TRUE;
    }
        
    function page_cache( $wp )
    {
        if( !apply_filters('wp_pjax_use_pg', $wp) )
        {
            $this->status = 'SKIP';
            return NULL;
        }
        
        $page_content = get_transient( $this->get_key() );
        
        //var_dump($page_content);
        //$page_content = false;


        if ( $page_content !== FALSE )  
        {  
            $this->status = 'HIT';
            
            do_action('send_headers', $wp, $this);
            
            do_action('wp_pjax_header', $wp, $this, $this->status );
            
            echo $page_content;
            die();
        }
        else
        {
            $this->status = 'MISS';
            //do_action('send_headers', $wp, $this);
            //do_action('wp_pjax_header', $wp, $this, $this->status );
            return FALSE;
        }
    }
    
    function get_key(  )
    {
        if( empty($this->key ))
        {
            $key = $this->generate_page_cache_key( );
        }
        else
        {
            $key = $this->key;
        }
        
        return $key;
    }
    
    function generate_page_cache_key( )
    {
        global $wp;
        
        $key = $this->transient_cache_prefix;
        
        if(empty($wp->query_vars))
        {
            $key .= '_index';
        }
        else 
        {
            foreach( $wp->query_vars AS $k => $v )
            {
                if(empty($v))
                {
                    $v = 'na';
                }
                $key .= '_'.$k.'-'.$v;
            }
        }
        
        $this->key = $key;
        
        return $key;
    }
    
    function set($page_content)
    {
        return set_transient( $this->get_key(), $page_content, wp_pjax_config()->get('page_cache_lifetime') );
    }
    
    function clearCache()
    {
        global $wpdb;
        
        $wpdb->query( "DELETE FROM ". $wpdb->prefix ."options WHERE option_name LIKE ('_transient_".$this->transient_cache_prefix."%')" );
    }
    
    
}
