<?php

namespace WpPjax;

/**
 * Class PluginsLifecycleHooks
 *
 * @package WpPjax
 */
class PluginsLifecycleHooks {

	/**
	 * @var string
	 */
	private $pluginFile;

	/**
	 * @param string $pluginFile
	 */
	private function __construct( $pluginFile ) {
		$this->pluginFile = $pluginFile;
	}

	/**
	 * @param string $pluginFile
	 *
	 * @return PluginsLifecycleHooks
	 */
	public static function init( $pluginFile ) {
		$plugin = new self( $pluginFile );
		add_action( 'activated_plugin', array( $plugin, 'make_sure_we_will_be_loaded_first' ) );

		return $plugin;
	}

	public function make_sure_we_will_be_loaded_first() {
		$basename = plugin_basename( $this->pluginFile );
		$plugins  = get_option( 'active_plugins' );
		$position = array_search( $basename, $plugins );
		if ( $position > 0 ) {
			array_splice( $plugins, $position, 1 );
			array_unshift( $plugins, $basename );
			update_option( 'active_plugins', $plugins );
		}
	}
}
