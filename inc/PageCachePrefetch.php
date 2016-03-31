<?php

/**
 * Class WP_PJAX_PageCachePrefetch
 *
 * @author Peter Elmered
 */
class WP_PJAX_PageCachePrefetch {

	private $config;

	public function init( $config ) {
		$this->config = $config;

		//Add cron schedule for prefetch
		add_filter( 'cron_schedules', array( $this, 'addPrefetchCronSchedules' ) );

		add_action( 'wp-pjax-pg-prefetch', array( &$this, 'prefetch' ) );

		if ( ! wp_next_scheduled( 'wp-pjax-pg-prefetch' ) ) {
			wp_schedule_event( current_time( 'timestamp' ), 'wp_pjax_pg_prefetch', 'wp-pjax-pg-prefetch' );
		}
	}

	public function addPrefetchCronSchedules( $schedules ) {
		// add prefetch interval schedule
		$schedules['wp_pjax_pg_prefetch'] = array(
			'interval' => 300,
			'display'  => __( 'WP-PJAX Prefetch Interval' ),
		);
		// add url cache refresh interval schedule
		$schedules['wp_pjax_pg_prefetch_urls'] = array(
			'interval' => 300,
			'display'  => __( 'WP-PJAX URL cache refresh interval' ),
		);

		return $schedules;
	}

	public function prefetch( $start = 0 ) {
		/** @var WP_PJAX_Log $log */
		$log = wp_pjax_get_instance( 'Log' );
		$log->set_file( 'prefetch' );

		$start_time = microtime( true );

		set_time_limit( 0 );
		ini_set( 'max_execution_time', 0 );

		$max_exec_time = ini_get( 'max_execution_time' );

		echo 'Max exec time: ' . $max_exec_time;

		$sitemap_url = $this->config[ WP_PJAX_CONFIG_PREFIX . 'page-cache-prefetch-sitemap-url' ];

		$urls = WP_PAJX_Util::parse_sitemap( $sitemap_url );

		set_transient( 'WP_PJAX_PREFETCH_URLS_TANSIENT', $urls, 86400 + 3600 );

		$msg = 'Page index refreshed. ' . count( $urls ) . ' URLs added.';

		$log->write( $msg );
		echo $msg;

		print_r( $urls );

		get_transient( 'WP_PJAX_LAST_PREFETCH' );

		$queue = array_slice( $urls, $start );

		echo 'Queue: ';
		print_r( $queue );

		$timeout = 20;

		$args = array(
			'headers' => array(
				'X_PJAX'                  => 'true',
				'X_PJAX_Container'        => '#container',
				'X_Requested_With'        => 'XMLHttpRequest',
				'HTTP_X_WP_PJAX_PREFETCH' => 'true',
			),
			'timeout' => $timeout,
		);

		$i             = 0;
		$msg           = '';
		$pages_fetched = 0;

		foreach ( $queue as $url ) {
			++ $i;

			$time_elapsed = microtime( true ) - $start_time;

			if ( $time_elapsed > ( $max_exec_time - ( $timeout + 1 ) ) && 0 != $max_exec_time ) {
				wp_schedule_single_event( current_time( 'timestamp' ), 'wp-pjax-pg-prefetch', array( $start + $i ) );

				$msg .= 'execution timeout';

				$log->write( $msg );
				echo $msg;

				continue;
			}

			$r = wp_remote_request( $url, $args );

			if ( is_array( $r ) ) {
				$pages_fetched ++;
			} else {
				$pages_fetched ++;
			}
		}

		$msg .= 'Pages prefetched: ' . $pages_fetched . ' Running time: ' . ( microtime( true ) - $start_time );

		$log->write( $msg );

		echo $msg;

		die( 'die' );
	}
}
