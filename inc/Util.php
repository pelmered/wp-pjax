<?php

/**
 * Class WP_PAJX_Util with utility functions
 *
 * @author Peter Elmered
 */
class WP_PAJX_Util {

	/**
	 * Parses sitemap
	 *
	 * @param string $url
	 *
	 * @return array
	 */
	public static function parse_sitemap( $url ) {
		$urls     = array();
		$response = wp_remote_request( $url );

		if ( ! is_wp_error( $response ) && $response['response']['code'] == 200 ) {
			$url_matches     = null;
			$sitemap_matches = null;

			if ( preg_match_all( '~<sitemap>(.*?)</sitemap>~is', $response['body'], $sitemap_matches ) ) {
				$loc_matches = null;

				foreach ( $sitemap_matches[1] as $sitemap_match ) {
					if ( preg_match( '~<loc>(.*?)</loc>~is', $sitemap_match, $loc_matches ) ) {
						$loc = trim( $loc_matches[1] );

						if ( $loc ) {
							$urls = array_merge( $urls, WP_PAJX_Util::parse_sitemap( $loc ) );
						}
					}
				}
			} elseif ( preg_match_all( '~<url>(.*?)</url>~is', $response['body'], $url_matches ) ) {
				$locs             = array();
				$loc_matches      = null;
				$priority_matches = null;

				foreach ( $url_matches[1] as $url_match ) {
					$loc      = '';
					$priority = 0;

					if ( preg_match( '~<loc>(.*?)</loc>~is', $url_match, $loc_matches ) ) {
						$loc = trim( $loc_matches[1] );
					}

					if ( preg_match( '~<priority>(.*?)</priority>~is', $url_match, $priority_matches ) ) {
						$priority = (double) trim( $priority_matches[1] );
					}

					if ( $loc && $priority ) {
						$locs[ $loc ] = $priority;
					}
				}

				arsort( $locs );

				$urls = array_keys( $locs );
			}
		}

		return $urls;
	}
}
