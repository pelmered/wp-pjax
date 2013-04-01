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
    
    function init($config)
    {
        $this->_config = $config;
        
        //apply_filters('wp_pjax_use_pg', $wp_pjax_options))
        
        
        //die('asdasd');
        add_filter( 'wp_pjax_use_pg', array( &$this, 'use_pg' ), 1, 1 ); 
        add_action('parse_request', array(&$this, 'page_cache'), 1, 1 );   
    }
    
    function use_pg($wp)
    {
        //print_r($wp);
        //Do not serve cached pages to prefetch
        if( wp_pjax_check_request('HTTP_X_WP_PJAX_PREFETCH')  )
        {
            return FALSE;
        }
        
        $exceptions = explode("\n", $this->_config[WP_PJAX_CONFIG_PREFIX.'page-cache-exceptions']);
        
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
        
        
//        $this->_config[WP_PJAX_CONFIG_PREFIX.'page-cache-exceptions']
        
        
        
        return TRUE;
    }
        
    function page_cache( $wp )
    {
        
        if( !apply_filters('wp_pjax_use_pg', $wp) )
        {
            $this->status = 'SKIP';  
            //phpconsole(array('STATUS' => $this->status,'QUERY VARS' => $wp->query_vars,'SERVER'=> $_SERVER), 'peter');
            return NULL;
        }
        
        
        //phpconsole(array('get key' => $this->get_key()  ), 'peter');
        
        $page_content = get_transient( $this->get_key() );
        
        
        //phpconsole(array('page content' => $page_content ), 'peter');
        
        //var_dump($page_content); 
        
        if ( $page_content !== FALSE )  
        {  
            $this->status = 'HIT';
            //phpconsole(array('STATUS' => $this->status,'QUERY VARS' => $wp->query_vars,'SERVER'=> $_SERVER), 'peter');
            
            do_action('send_headers', $wp, $this);
            
            do_action('wp_pjax_header', $wp, $this, $this->status );
            
            echo $page_content;
            die();
        }
        else
        {
            $this->status = 'MISS';  
            //phpconsole(array('STATUS' => $this->status,'QUERY VARS' => $wp->query_vars,'SERVER'=> $_SERVER), 'peter');
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
        
        $key = WP_PJAX_TRANIENT_PREFIX;
        
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
        //phpconsole(array('SET_KEY' => $this->get_key(), 'lifetime' => $this->_config[WP_PJAX_CONFIG_PREFIX.'page-cache-lifetime'] ), 'peter');
        return set_transient( $this->get_key(), $page_content, $this->_config[WP_PJAX_CONFIG_PREFIX.'page-cache-lifetime'] );
    }
    
    function clearCache()
    {
        global $wpdb;
        
        $wpdb->query( "DELETE FROM ". $wpdb->prefix ."options WHERE option_name LIKE ('_transient_".WP_PJAX_TRANIENT_PREFIX."%')" );
    }
    
    
}
