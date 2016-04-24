<?php
/**
 * Plugin Name: WP-PJAX
 * Plugin URI:  http://wordpress.org/extend/plugins/wp-pjax/
 * Description: Makes Wordpress use the PJAX (PushState + AJAX) technique for loading content
 * Version:     0.0.4.1.1
 * Author:      Peter Elmered
 * Author URI:  http://elmered.com
 * License:     GPL2+
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: pe_wp_pjax
 *
 * Copyright (C) 2013  Peter Elmered
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 *
 * @package wp-pjax
 */

if ( ! class_exists( '\Composer\Autoload\ClassLoader' ) ) {
	require_once __DIR__ . '/vendor/autoload.php';
}

define( 'WP_PJAX_PLUGIN_PATH', dirname( __FILE__ ) . DIRECTORY_SEPARATOR );

if ( is_admin() ) {
	$wp_pjax_settings_page = new WPPjaxSettingsPage();
}

\WpPjax\PluginsLifecycleHooks::init( __FILE__ );

/**
 * Returns instance of singleton class
 *
 * @param string $class Short class name.
 *
 * @return object
 */
function wp_pjax_get_instance( $class ) {
	static $instances = array();

	if ( ! isset( $instances[ $class ] ) ) {
		$classname = 'WP_PJAX_' . $class;

		$instances[ $class ] = new $classname();
	}

	return $instances[ $class ]; // Don't return reference!
}

global $wp_pjax_options;

if ( ! function_exists( 'is_pjax_request' ) ) {
	/**
	 * Check is request from pjax
	 *
	 * @return bool
	 */
	function is_pjax_request() {
		if ( defined( 'IS_PJAX' ) && IS_PJAX ) {
			return true;
		} elseif ( defined( 'IS_PJAX' ) && ! IS_PJAX ) {
			return false;
		} elseif (
			( isset( $_SERVER['HTTP_X_PJAX'] ) && (bool) $_SERVER['HTTP_X_PJAX'] ) // Input var okay.
			|| ( isset( $_SERVER['X_PJAX'] ) && (bool) $_SERVER['X_PJAX'] ) // Input var okay.
		) {
			define( 'IS_PJAX', true );

			return true;
		} else {
			define( 'IS_PJAX', false );

			return false;
		}
	}
}

if ( ! function_exists( 'wp_pjax_check_request' ) ) {
	/**
	 * Checks header as flag value
	 *
	 * @param string $check Header name.
	 *
	 * @return bool
	 */
	function wp_pjax_check_request( $check ) {
		$define_key = 'IS_' . $check;

		if ( defined( $define_key ) && constant( $define_key ) ) {
			return true;
		}
		if ( defined( $define_key ) && ! constant( $define_key ) ) {
			return false;
		} elseif (
			( isset( $_SERVER[ 'HTTP_' . $check ] ) && (bool) $_SERVER[ 'HTTP_' . $check ] ) // Input var okay.
			|| ( isset( $_SERVER[ $check ] ) && (bool) $_SERVER[ $check ] ) // Input var okay.
		) {
			define( $define_key, true );

			return true;
		} else {
			define( $define_key, false );

			return false;
		}
	}
}

if ( ! function_exists( 'get_pjax_header' ) ) {
	/**
	 * Should wp_header execution be skipped
	 *
	 * @return bool
	 */
	function get_pjax_header() {
		if ( is_pjax_request() ) {
			// Return TRUE to skip execution of wp_header.
			return true;
		} else {
			// Return FALSE to execute wp_header.
			return false;
		}
	}
}

if ( ! function_exists( 'get_pjax_footer' ) ) {
	/**
	 * Should wp_footer execution be skipped
	 *
	 * @return bool
	 */
	function get_pjax_footer() {
		if ( is_pjax_request() ) {
			do_action( 'get_pjax_footer' );

			// Return TRUE to skip execution of wp_footer.
			return true;
		} else {
			// Return FALSE to execute wp_footer.
			return false;
		}
	}
}

if ( ! function_exists( 'get_pjax_sidebar' ) ) {
	/**
	 * Should sidebar execution be skipped
	 *
	 * @return bool
	 */
	function get_pjax_sidebar() {
		if ( is_pjax_request() ) {
			do_action( 'wp_pjax_sidebar' );

			// Return TRUE to skip execution of sidebar.
			return true;
		} else {
			// Return FALSE to execute sidebar.
			return false;
		}
	}
}

if ( ! function_exists( 'wp_pjax_header' ) ) {
	add_action( 'wp_pjax_header', 'wp_pjax_header', 10, 3 );

	/**
	 * Prints the <title> tag based on what is being viewed
	 *
	 * @param WP                $wp         Current WordPress environment instance.
	 * @param WP_PJAX_PageCache $page_cache Instance of PageCache.
	 * @param string            $status     Status of cache MISS or HIT.
	 */
	function wp_pjax_header( WP $wp, WP_PJAX_PageCache $page_cache, $status ) {
		if ( $page_cache->config[ WP_PJAX_CONFIG_PREFIX . 'show-extended-notice' ]
		     && current_user_can( 'edit_plugins' )
		     || $page_cache->config['debug_mode']
		) {
			header( 'PJAX-loaded-resource: ' . $page_cache->key );
		}

		echo '<title>', esc_html( apply_filters( 'wp_pjax_title', '' ) ), '</title>';
	}
}

add_filter( 'wp_pjax_title', 'wp_pjax_title' );
/**
 * Adds blog name and description to wp_title
 *
 * @return string
 */
function wp_pjax_title() {
	$title = wp_title( '|', false, 'right' );

	// Add the blog name.
	$title .= get_bloginfo( 'name' );

	// Add the blog description for the home/front page.
	$site_description = get_bloginfo( 'description', 'display' );
	if ( $site_description && ( is_home() || is_front_page() ) ) {
		$title .= " | $site_description";
	}

	return $title;
}

$wp_pjax = wp_pjax_get_instance( 'WP_PJAX' );
$wp_pjax->run();
