<?php

namespace WpPjax;

use WP_UnitTestCase;

/**
 * Class PluginActivationTest
 *
 * @package WpPjax
 */
class PluginActivationTest extends WP_UnitTestCase {

	function test_plugin_is_loaded_first() {
		$plugin = 'wp-pjax/wp-pjax.php';

		activate_plugin( 'hello.php' );
		activate_plugin( $plugin );

		$activePlugins = get_option( 'active_plugins' );
		$this->assertEquals( $plugin, $activePlugins[0] );
	}

	function test_plugin_is_loaded_first_after_other_plugin_activation() {
		$plugin = 'wp-pjax/wp-pjax.php';

		activate_plugin( $plugin );
		activate_plugin( 'hello.php' );

		$activePlugins = get_option( 'active_plugins' );
		$this->assertEquals( $plugin, $activePlugins[0] );
	}
}
