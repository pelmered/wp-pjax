<?php
//http://codex.wordpress.org/Creating_Options_Pages example #2

if (is_admin()) {
	define('WP_DEBUG', true);
	error_reporting(E_ALL ^ E_NOTICE ^ E_DEPRECATED );
	ini_set("display_errors", 1);
}

class WPPjaxSettingsPage
{
    /*
     * Holds the values to be used in the fields callbacks
     */
    private $options;
	private $wp_pjax_options;

    /*
     * Start up
     */
    public function __construct()
    {
        add_action('admin_menu', [$this, 'add_plugin_page']);
        add_action('admin_init', [$this, 'page_init']);
    }

    /*
     * Add options page
     */
    public function add_plugin_page()
    {
        // This page will be under "Settings"
        add_options_page(
            'WP-PJAX Settings', 
            'WP-PJAX (test-GL)', 
            'manage_options', 
            'wp-pjax-settings', 
            [$this, 'create_admin_page'] //function() { $this->create_admin_page(); }
        );
    }

    /*
     * Options page callback
     */
    public function create_admin_page()
    {
        // Set class property
        $this->options = get_option('my_option_name');
		$this->wp_pjax_options = get_option(WP_PJAX_OPTIONS_KEY);
		
		var_dump($this->options);
		var_dump($this->wp_pjax_options);
        ?>
        <div class="wrap">
            <h2>WP-PJAX Settings (being tested in GadgetLife)</h2>           
            <form method="post" action="options.php">
            <?php
                // This prints out all hidden setting fields
				settings_fields('wp_pjax_option_group');
				do_settings_sections('wp-pjax-setting');
                do_settings_sections('my-setting-admin');
                submit_button(); 
            ?>
            </form>
        </div>
        <?php
    }

    /*
     * Register and add settings
     */
    public function page_init()
    {
		register_setting(
			'wp_pjax_option_group', // Option group
			WP_PJAX_OPTIONS_KEY // Option name
		);
		
		add_settings_section(
            'wp_pjax_setting', // ID
            'WP-PJAX Settings', // Title
            [$this, 'print_section_info'], // Callback
            'wp-pjax-setting' // Page
        );
		
		$this->add_settings_input_field(
            WP_PJAX_CONFIG_PREFIX.'enable', // ID
            'Enable', // Title
            'wp-pjax-setting', // Page
            'wp_pjax_setting', // Section
			[ 'type' => 'checkbox', 'option_name' => WP_PJAX_OPTIONS_KEY, 'option_array' => &$this->wp_pjax_options ]
        );
		
		$this->add_settings_input_field(
            WP_PJAX_CONFIG_PREFIX.'menu-selector', // ID
            'Menu selector', // Title
            'wp-pjax-setting', // Page
            'wp_pjax_setting', // Section
			[ 'type' => 'text', 'option_name' => WP_PJAX_OPTIONS_KEY, 'option_array' => &$this->wp_pjax_options ]
        ); 
		
		$option_name = 'my_option_name';
        register_setting(
            'wp_pjax_option_group', // Option group
            $option_name, // Option name
            [$this, 'sanitize'] // Sanitize
        );

        add_settings_section(
            'setting_section_id', // ID
            'My Custom Settings', // Title
            [$this, 'print_section_info'], // Callback
            'my-setting-admin' // Page
        );

        $this->add_settings_input_field(
            'id_number', // ID
            'ID Number', // Title
            'my-setting-admin', // Page
            'setting_section_id', // Section
			[ 'type' => 'number', 'option_name' => $option_name, 'option_array' => &$this->options ]
        );

		$this->add_settings_input_field(
            'title', // ID
            'Title', // Title
            'my-setting-admin', // Page
            'setting_section_id', // Section
			[ 'type' => 'text', 'option_name' => $option_name, 'option_array' => &$this->options ]
        ); 
    }

    /*
     * Sanitize each setting field as needed
     *
     * @param array $input Contains all settings fields as array keys
     */
    public function sanitize($input)
    {
        $new_input = array();
        if (isset($input['id_number']))
            $new_input['id_number'] = absint($input['id_number']);

        if (isset($input['title']))
            $new_input['title'] = sanitize_text_field($input['title']);

        return $new_input;
    }
	
	/*
     * Print the Section text
     */
    public function print_section_info()
    {
        print 'Enter your settings below:';
    }
	
	/*
	 * Get the settings option array and print one of its values
	 */
	public function add_settings_input_field($id, $title, $page, $section_id, $args) {
		add_settings_field(
			$id, // ID
			$title, // Title 
			[$this, 'add_input_callback'], // Callback
			$page, // Page
			$section_id, // Section           
			array_merge([ 'id' => $id ], $args)
		);
	}
	
	/*
	 * Get the settings option array and print one of its values
	 */
	public function add_input_callback($args) {
		$id = $args["id"];
		$input_options = $args["option_array"];
		//var_dump($args);
		if (isset($args["option_name"]))
			$name = sprintf('%1$s'.'[%2$s]', $args["option_name"], $id);
		else
			$name = $id;
		$this->print_input_element(
			$args["type"],
			$id,
			$name,
			isset($input_options[$id]) ? esc_attr($input_options[$id]) : ''
		);
	}
	
	public function print_input_element($type, $id, $name, $value) {
		switch ($type) {
			case 'checkbox':
			case 'radio':
				printf('<input type="%1$s" id="%2$s" name="%3$s" value="checked" %4$s/>', $type, $id, $name, $value === 'checked' ? 'checked="checked" ' : '');
				break;
			default:
				printf('<input type="%1$s" id="%2$s" name="%3$s" value="%4$s" />', $type, $id, $name, $value);
				break;
		}
	}
}

if (is_admin())
    $wp_pjax_settings_page = new WPPjaxSettingsPage();
