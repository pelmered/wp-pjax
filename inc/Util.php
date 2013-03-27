<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Utility functions
 *
 * @author Peter Elmered
 */



class WP_PAJX_Util
{
    
    function __construct()
    {
        
    }
    
    
    /**
     * Parses sitemap from URL and returns all URLs in an array.
     * 
     * @param type $sitemap_url Link to sitemap
     * @return array $urls Array of urls; 
     */
    static function parse_sitemap2 ($sitemap_url)
    {
		//$sitemap_url = 'http://kbc.debug.nu/post-sitemap.xml';
        
        $urls = array();  

        $RootDomDocument = new DOMDocument();
        
        //$RootDomDocument->preserveWhiteSpace = false;
        $RootDomDocument->load($sitemap_url);
        $RootDomNodeList = $RootDomDocument->getElementsByTagName('loc');

        var_dump($RootDomDocument);
        var_dump($RootDomNodeList);
        //die('asdasd');
        
        
        foreach($RootDomNodeList as $root) 
		{
            $root_url = $root->nodeValue;
            //$urls[] = $root->nodeValue;
			
			
			
			$DomDocument = new DOMDocument();
			$DomDocument->preserveWhiteSpace = false;
			$DomDocument->load($root_url);
			$DomNodeList = $DomDocument->getElementsByTagName('loc');
			
			foreach($RootDomNodeList as $url)
			{
				$urls[] = $root->nodeValue;
			}
			
			
			
        }

        //display it
        echo "<pre>";
        print_r($urls);
        echo "</pre>";
        
        return $urls;
    }
    

    

    /**
     * Parses sitemap
     *
     * @param string $url
     * @return array
     */
    function parse_sitemap($url) {
        //w3_require_once(W3TC_INC_DIR . '/functions/http.php');

        $urls = array();
        $response = wp_remote_request($url);

        if (!is_wp_error($response) && $response['response']['code'] == 200) {
            $url_matches = null;
            $sitemap_matches = null;

            if (preg_match_all('~<sitemap>(.*?)</sitemap>~is', $response['body'], $sitemap_matches)) {
                $loc_matches = null;
                
                foreach ($sitemap_matches[1] as $sitemap_match) {
                    if (preg_match('~<loc>(.*?)</loc>~is', $sitemap_match, $loc_matches)) {
                        $loc = trim($loc_matches[1]);

                        if ($loc) {
                            $urls = array_merge($urls, WP_PAJX_Util::parse_sitemap($loc));
                        }
                    }
                }
            } elseif (preg_match_all('~<url>(.*?)</url>~is', $response['body'], $url_matches)) {
                $locs = array();
                $loc_matches = null;
                $priority_matches = null;
                
                foreach ($url_matches[1] as $url_match) {
                    $loc = '';
                    $priority = 0;

                    if (preg_match('~<loc>(.*?)</loc>~is', $url_match, $loc_matches)) {
                        $loc = trim($loc_matches[1]);
                    }

                    if (preg_match('~<priority>(.*?)</priority>~is', $url_match, $priority_matches)) {
                        $priority = (double) trim($priority_matches[1]);
                    }

                    if ($loc && $priority) {
                        $locs[$loc] = $priority;
                    }
                }

                arsort($locs);

                $urls = array_keys($locs);
            }
        }

        return $urls;
    }
    
}
