<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */


/**
 * Description of PageCachePrefetch
 *
 * @author Peter Elmered
 */
class WP_PJAX_PageCachePrefetch
{
    
    var $_config;
    
    function init($config)
    {
        //return '';

        
        $this->_config = $config;
        
        //$this->prefetch();
        
        
        
        //wp_clear_scheduled_hook('wp-pjax-pg-prefetch');
        
        //Add cron schedule for prefetch
        add_filter('cron_schedules', array( $this, 'addPrefetchCronSchedules' ) );
        
        add_action('wp-pjax-pg-prefetch', array(&$this, 'prefetch'));
        
        
        if( !wp_next_scheduled( 'wp-pjax-pg-prefetch' ) )
        {
            $r = wp_schedule_event( current_time( 'timestamp' ), 'wp_pjax_pg_prefetch', 'wp-pjax-pg-prefetch' );
            var_dump($r);
            
        }
        
    }
    
    function addPrefetchCronSchedules( $schedules ) 
    {
	// add prefetch interval schedule
	$schedules['wp_pjax_pg_prefetch'] = array(
		'interval' => 300,
		'display' => __('WP-PJAX Prefetch Interval')
	);
	// add url cache refresh interval schedule
	$schedules['wp_pjax_pg_prefetch_urls'] = array(
		'interval' => 300,
		'display' => __('WP-PJAX URL cache refresh interval')
	);
	return $schedules;
    }
    
    
    function prefetch( $start = 0 )
    {

        $log = wp_pjax_get_instance('Log');
        $log->setFile('prefetch');
        
        define('WP_DEBUG', true);
        error_reporting(E_ALL ^ E_NOTICE ^ E_DEPRECATED );
        ini_set("display_errors", 1);
   
        
        
        $start_time = microtime(TRUE);
   
        
        //phpconsole(array('action'=> 'Initialize', 'start' => $start + $i ), 'peter');
                
        
        set_time_limit(0);
        ini_set('max_execution_time', 0);
        
        $max_exec_time = ini_get('max_execution_time'); 
        
        echo 'Max exec time: '.$max_exec_time;
        
        
        
	
       // $urls = get_transient('WP_PJAX_PREFETCH_URLS_TANSIENT');

        if( !$urls)
        {
            $sitemap_url = $this->_config[WP_PJAX_CONFIG_PREFIX.'page-cache-prefetch-sitemap-url'];

            $urls = WP_PAJX_Util::parse_sitemap($sitemap_url);
            
            set_transient('WP_PJAX_PREFETCH_URLS_TANSIENT', $urls, 86400 + 3600);
            
            $msg ='Page index refreshed. '.count($urls).' URLs added.';
            
            $log->write($msg);
            echo $msg;

        }

        //print_r($this->_config);
        //die($sitemap_url);
        
        $url_count = count($urls);
        
		
        
        print_r($urls);
        
     
		
		
        $last_prefetch = get_transient('WP_PJAX_LAST_PREFETCH');
        
        if( $last_prefetch === FALSE)
        {
            
            
            
        }
        
        
        $queue = array_slice($urls, $start);

        echo 'Queue: ';
        print_r($queue);
                
        $timeout = 20;
        
        $args = array(
            'headers' => array(
                'X_PJAX' => 'true',
                'X_PJAX_Container' => '#container',
                'X_Requested_With' => 'XMLHttpRequest',
                'HTTP_X_WP_PJAX_PREFETCH' => 'true'
            ),
//            'blocking' => FALSE,
            'timeout' => $timeout
        );
        
        $i = 0;
        $msg = '';
        $pages_fetched = 0;
        
        foreach($queue AS $url)
        {
            ++$i;
            
            $time_elapsed = microtime(TRUE) - $start_time;
            
            if( $time_elapsed > ($max_exec_time - ($timeout+1) ) && $max_exec_time != 0 )
            {
                wp_schedule_single_event(current_time('timestamp'), 'wp-pjax-pg-prefetch', array($start + $i ));

                //phpconsole(array('action'=> 'new request','queue' => $queue, 'urls' => $urls, 'start' => $start, 'i' => $i ), 'peter');
                
                $msg .= 'execution timeout';
                
                $log->write($msg);
                echo $msg;
                
                continue;
            }
            
            //echo "\nRequest ".$i.": ".$time_elapsed."\n";
            
            //echo $url."\n";
            
            $r = wp_remote_request($url, $args);

            
            if(is_array($r))
            {
                
                $pages_fetched++;
                
                /*
                echo 'body length: '.strlen($r['body'])."\n";

                unset($r['body']);
                
                print_r($r['headers']);
                
                echo "\n\n\n\n\n";
                */
            }
            else
            {
                $pages_fetched++;
                //print_r($r);
            }
            
 

        }
        
        $msg .= 'Pages prefetched: '.$pages_fetched. ' Running time: '.(microtime(TRUE) - $start_time);


        $log->write($msg);

        echo $msg;
        
        die('die');
        
    }
    
}
