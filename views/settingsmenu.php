<?php

class WPPjaxSettingsPage {

	/**
	 * Holds the values to be used in the fields callbacks
	 */
	private $options;
	private $wp_pjax_options;

	public function __construct() {
		add_action( 'admin_menu', array( $this, 'add_plugin_page' ) );
		add_action( 'admin_init', array( $this, 'page_init' ) );
	}

	/**
	 * Add options page
	 */
	public function add_plugin_page() {
		// This page will be under "Settings"
		add_options_page(
			'WP-PJAX Settings',
			'WP-PJAX',
			'manage_options',
			'wp-pjax-settings',
			array( $this, 'create_admin_page' )
		);
	}

	/**
	 * Options page callback
	 */
	public function create_admin_page() {
		$this->options         = get_option( 'my_option_name' );
		$this->wp_pjax_options = get_option( WP_PJAX_OPTIONS_KEY );
		?>
		<div class="wrap">
			<h2>WP-PJAX Settings</h2>

			<form method="post" action="options.php">
				<?php
				// This prints out all hidden setting fields
				settings_fields( 'wp_pjax_option_group' );
				do_settings_sections( 'installation-instructions' );
				do_settings_sections( 'wp-pjax-selectors' );
				do_settings_sections( 'wp-pjax-handlers' );
				submit_button();
				?>
			</form>
		</div>
		<?php
	}

	/**
	 * Register and add settings
	 */
	public function page_init() {
		register_setting(
			'wp_pjax_option_group',
			WP_PJAX_OPTIONS_KEY,
			array( $this, 'sanitize' )
		);

		add_settings_section(
			'installation_instructions',
			'Installation',
			function () {
				echo 'The plugin needs to control whether the header and footer should fire or not for every request. Therefore you need to make some small changes in your theme for this plugin to work properly. This is what you need to do: <p> 1. Header. Put this line of code in the top of every header file (any header*.php file) in your theme (before any code or output) <br><code>&#60;&#63;php if&#40;function&#95;exists&#40; &#39;get&#95;pjax&#95;header&#39; &#41;&#41; if&#40;get&#95;pjax&#95;header&#40;&#41;&#41; return FALSE&#59; &#63;&#62;</code> <p>2. Footer. Put this line of code in the top of every footer file (any footer*.php file) in your theme (before any code or output) <br><code>&#60;&#63;php if&#40;function&#95;exists&#40; &#39;get&#95;pjax&#95;footer&#39; &#41;&#41; if&#40;get&#95;pjax&#95;footer&#40;&#41;&#41; return FALSE&#59; &#63;&#62;</code> <p> 3. Sidebar. Put this line of code in the top of every sidebar file (any sidebar*.php file) in your theme (before any code or output). This is currently not used, but it will probably be used in later versions. So for safe upgrades in the future, I recommend that you do this.) <br><code>&#60;&#63;php if&#40;function&#95;exists&#40; &#39;get&#95;pjax&#95;sidebar&#39; &#41;&#41; if&#40;get&#95;pjax&#95;sidebar&#40;&#41;&#41; return FALSE&#59; &#63;&#62;</code>';
			},
			'installation-instructions'
		);

		add_settings_section(
			'wp_pjax_selectors',
			'Selectors',
			function () {
				echo 'Enter the selectors for jquery-pjax to work on your own theme.';
			},
			'wp-pjax-selectors'
		);

		$this->add_settings_input_field(
			WP_PJAX_CONFIG_PREFIX . 'enable',
			'Enable',
			'wp-pjax-selectors',
			'wp_pjax_selectors',
			array(
				'type'         => 'checkbox',
				'option_name'  => WP_PJAX_OPTIONS_KEY,
				'option_array' => &$this->wp_pjax_options,
			)
		);

		$this->add_settings_input_field(
			WP_PJAX_CONFIG_PREFIX . 'menu-selector',
			'Menu selector',
			'wp-pjax-selectors',
			'wp_pjax_selectors',
			array( 'type' => 'text', 'option_name' => WP_PJAX_OPTIONS_KEY, 'option_array' => &$this->wp_pjax_options )
		);

		$this->add_settings_input_field(
			WP_PJAX_CONFIG_PREFIX . 'content-selector',
			'Content selector',
			'wp-pjax-selectors',
			'wp_pjax_selectors',
			array( 'type' => 'text', 'option_name' => WP_PJAX_OPTIONS_KEY, 'option_array' => &$this->wp_pjax_options )
		);

		$this->add_settings_input_field(
			WP_PJAX_CONFIG_PREFIX . 'menu-active-class',
			'Menu active class',
			'wp-pjax-selectors',
			'wp_pjax_selectors',
			array( 'type' => 'text', 'option_name' => WP_PJAX_OPTIONS_KEY, 'option_array' => &$this->wp_pjax_options )
		);

		add_settings_section(
			'wp_pjax_handlers',
			'JavaScript Handlers',
			function () {
				print 'Enter JavaScript handlers if you have to manage your contents before or after page load.';
			},
			'wp-pjax-handlers'
		);

		$this->add_settings_input_field(
			WP_PJAX_CONFIG_PREFIX . 'pre-handler',
			'Handler working before page load',
			'wp-pjax-handlers',
			'wp_pjax_handlers',
			array( 'type' => 'text', 'option_name' => WP_PJAX_OPTIONS_KEY, 'option_array' => &$this->wp_pjax_options )
		);

		$this->add_settings_input_field(
			WP_PJAX_CONFIG_PREFIX . 'post-handler',
			'Handler working after page load',
			'wp-pjax-handlers',
			'wp_pjax_handlers',
			array( 'type' => 'text', 'option_name' => WP_PJAX_OPTIONS_KEY, 'option_array' => &$this->wp_pjax_options )
		);
	}

	/**
	 * Get the settings option array and print one of its values
	 */
	public function add_settings_input_field( $id, $title, $page, $section_id, $args ) {
		add_settings_field(
			$id,
			$title,
			array( $this, 'add_input_callback' ),
			$page,
			$section_id,
			array_merge( array( 'id' => $id ), $args )
		);
	}

	/**
	 * Sanitize each setting field as needed
	 *
	 * @param array $input Contains all settings fields as array keys
	 *
	 * @return array
	 */
	public function sanitize( $input ) {
		$output = array();

		$this->sanitizeCheckbox( $output, $input, WP_PJAX_CONFIG_PREFIX . 'enable' );
		$this->sanitizeText( $output, $input, WP_PJAX_CONFIG_PREFIX . 'menu-selector' );
		$this->sanitizeText( $output, $input, WP_PJAX_CONFIG_PREFIX . 'content-selector' );
		$this->sanitizeText( $output, $input, WP_PJAX_CONFIG_PREFIX . 'menu-active-class' );
		$this->sanitizeText( $output, $input, WP_PJAX_CONFIG_PREFIX . 'pre-handler' );
		$this->sanitizeText( $output, $input, WP_PJAX_CONFIG_PREFIX . 'post-handler' );

		return $output;
	}

	private function sanitizeCheckbox( &$output, $input, $id ) {
		if ( isset( $input[ $id ] ) && 'checked' === $input[ $id ] ) {
			$output[ $id ] = 'checked';
		}
	}

	private function sanitizeText( &$output, $input, $id ) {
		if ( isset( $input[ $id ] ) && ! empty( $input[ $id ] ) ) {
			$output[ $id ] = sanitize_text_field( $input[ $id ] );
		}
	}

	/**
	 * Print the Section text
	 */
	public function print_section_info() {
		echo 'Enter your settings below:';
	}

	/**
	 * Get the settings option array and print one of its values
	 */
	public function add_input_callback( $args ) {
		$id            = $args['id'];
		$input_options = $args['option_array'];
		if ( isset( $args['option_name'] ) ) {
			$name = sprintf( '%1$s' . '[%2$s]', $args['option_name'], $id );
		} else {
			$name = $id;
		}
		$this->print_input_element(
			$args['type'],
			$id,
			$name,
			isset( $input_options[ $id ] ) ? esc_attr( $input_options[ $id ] ) : ''
		);
	}

	public function print_input_element( $type, $id, $name, $value ) {
		switch ( $type ) {
			case 'checkbox':
			case 'radio':
				printf(
					'<input type="%1$s" id="%2$s" name="%3$s" value="checked" %4$s/>',
					$type,
					$id,
					$name,
					$value === 'checked' ? 'checked="checked" ' : ''
				);
				break;
			default:
				printf( '<input type="%1$s" id="%2$s" name="%3$s" value="%4$s" />', $type, $id, $name, $value );
				break;
		}
	}
}
