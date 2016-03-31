<?php
/**
 *
 * @author Nerijus Eimanavičius <nerijus@eimanavicius.lt>
 */

namespace WpPjax;

use WP_UnitTestCase;

class MakeSureWeWillBeLoadedFirstTest extends WP_UnitTestCase {

	/**
	 * @var PluginsLifecycleHooks
	 */
	protected $plugin;

	function setUp() {
		parent::setUp();
		$this->plugin = PluginsLifecycleHooks::init( realpath( ABSPATH . 'wp-content/plugins/wp-pjax/wp-pjax.php' ) );
	}

	function test_do_nothing_when_no_plugin_will_beactive() {
		update_option( 'active_plugins', array() );

		$this->plugin->makeSureWeWillBeLoadedFirst();

		$this->assertEquals( array(), get_option( 'active_plugins' ) );
	}

	function test_do_nothing_when_we_not_going_to_be_loaded() {
		$activePlugins = array( 'seo/seo.php', 'wordfence/wordfence.php', 'wp-pjax.php' );
		update_option( 'active_plugins', $activePlugins );

		$this->plugin->makeSureWeWillBeLoadedFirst();

		$this->assertEquals( $activePlugins, get_option( 'active_plugins' ) );
	}

	function test_do_nothing_when_we_are_the_only_one() {
		$active_plugins = array( 'wp-pjax/wp-pjax.php' );
		update_option( 'active_plugins', $active_plugins );

		$this->plugin->makeSureWeWillBeLoadedFirst();

		$this->assertEquals( $active_plugins, get_option( 'active_plugins' ) );
	}

	function test_do_nothing_when_we_going_to_be_loaded_first() {
		$active_plugins = array( 'wp-pjax/wp-pjax.php', 'hello.php', 'zzz.php' );
		update_option( 'active_plugins', $active_plugins );

		$this->plugin->makeSureWeWillBeLoadedFirst();

		$this->assertEquals( $active_plugins, get_option( 'active_plugins' ) );
	}

	function test_move_us_to_first_position_from_end() {
		$expected = array( 'seo/seo.php', 'wordfence/wordfence.php', 'wp-pjax/wp-pjax.php' );
		update_option( 'active_plugins', $expected );

		$this->plugin->makeSureWeWillBeLoadedFirst();

		$expected = array( 'wp-pjax/wp-pjax.php', 'seo/seo.php', 'wordfence/wordfence.php' );
		$actual   = get_option( 'active_plugins' );
		$this->assertEquals( $expected, $actual );
	}
}
