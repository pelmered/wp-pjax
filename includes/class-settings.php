<?php

class SL_Delivery_Segments_Settings {


    /**
     * Plugin version, used for autoatic updates and for cache-busting of style and script file references.
     *
     * @since    0.1.0
     * @var     string
     */
    const VERSION = '1.0.0';
    /**
     * Unique identifier for your plugin.
     *
     * Use this value (not the variable name) as the text domain when internationalizing strings of text. It should
     * match the Text Domain file header in the main plugin file.
     *
     * @since    0.1.0
     * @var      string
     */
    //public $plugin_slug = SVARTALADAN_DELIVERY_SEGMENTS_PLUGIN_SLUG;
    /**
     * Instance of this class.
     *
     * @since    0.1.0
     * @var      object
     */
    protected static $instance = null;
    /**
     * Plugin options
     *
     * @since    0.1.0
     */
    protected $options = array();

    /**
     * Variable to store cached data in singleton for reuse
     *
     * @var type
     * @since    1.0
     */
    protected $cache_data = array();
    /**
     * Initialize the plugin by setting localization, filters, and administration functions.
     *
     * @since    0.1.0
     */

    function __construct() {

        add_action('init', array($this, 'init'), 20);
        // Load plugin text domain
        //add_action('init', array($this, 'load_plugin_textdomain'));


    }


    function init() {

        // Load admin style sheet and JavaScript.
        add_action('admin_enqueue_scripts', array($this, 'enqueue_settings_styles'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_settings_scripts'));

        add_action('admin_menu', array( $this, 'register_settings_page' ) );

    }


    function register_settings_page() {
        $settings_page = add_submenu_page( 'options-general.php', 'Leveranssegmentering', 'Leveranssegmentering', 'manage_options', 'sl-delivery-segments', array( $this, 'settings_page_callback' ) );
        add_action( "load-{$settings_page}", array( $this, 'load_settings_page' ) );

        //die($settings_page);

        $this->load_settings_page();
    }

    function settings_page_callback() {
        $settings = $this;
        $form_post_url = admin_url( 'options-general.php?page=sl-delivery-segments' );
        require sl_delivery_segments::get_config('plugin_path') . 'templates/base.php';
    }

    function load_settings_page() {
        if ( $_POST["sl-delivery-segments-settings-submit"] == '1' ) {

            $url_parameters = '';

            if( isset($_GET['tab']) )
            {
                $url_parameters .= '&tab='.$_GET['tab'];
            }

            if( $this->save_plugin_settings() )
            {
                $url_parameters .= '&updated=true';
            }

            wp_redirect( admin_url( 'options-general.php?page=sl-delivery-segments' ) );

            exit;
        }
    }

    public function get()
    {
        return  get_option( "sl_delivery_segments_settings" );
    }

    function save_plugin_settings() {
        global $pagenow;

        if ( $pagenow == 'options-general.php' && $_GET['page'] == 'sl-delivery-segments' ){

            check_admin_referer( "sl-delivery-segments-settings" );

            $settings = get_option( "sl_delivery_segments_settings" );

            $settings = array();

            $fields = $this->get_settings_fields();

            foreach( $fields AS $key => $field )
            {

                if( $field['type'] == 'repeater' )
                {
                    $post_data = $_POST[$key];
                    //$settings[ $key ][] = $this->get_save_fields($settings, $key_trace, $field['sub_fields']);
                    array_walk_recursive( $post_data, function( $item, $key ) {
                        return sanitize_text_field($item);
                    });

                    $settings[ sanitize_key( $key ) ] = $post_data;

                }
                else if( isset( $_POST[$key] ) )
                {
                    $settings[ sanitize_key( $key ) ] = sanitize_text_field( $_POST[$key] );
                }
            }

            return update_option( "sl_delivery_segments_settings", $settings );
        }

        return false;
    }

    function get_save_fields( $settings, $key_trace, $fields )
    {
        var_dump($_POST);
        var_dump($fields);
        $iterator = new RecursiveIteratorIterator(new RecursiveArrayIterator($fields));
        $keys = array();
        foreach ($iterator as $key => $value) {

            $j = 0;


            /*
            var_dump($iterator->current());

            var_dump($depth);
            */

            if( $key != 'type' || $value == 'repeater' )
            {
                continue;
            }
            $j = 0;
            var_dump($key);
            var_dump($value);


            $depth = $iterator->getDepth();

            $xkey = '';

            // Build long key name based on parent keys
            for ($i = 0; $i < $depth; $i++) {

                $mkey = $iterator->getSubIterator($i)->key();

                echo $i;

                if( $mkey == 'sub_fields')
                {
                    $mkey = $j;
                }

                if( $i > 0 )
                {
                    $mkey = '['.$mkey.']';
                }


                $xkey .= $mkey;

                //$key = $iterator->getSubIterator($i)->key() . '_' . $key;


            }
            var_dump($xkey);

            $keys[] = $xkey;
        }
        //var_export($keys);

        var_dump($keys);

        return '';



        //post_code_repeater[0][delivery_day]

        $i = 0;

        foreach( $fields AS $key => $field )
        {

            //$key_trace .= $key;

            var_dump($key);
            var_dump($field);
            var_dump($key_trace);
            if( $field['type'] == 'repeater' )
            {
                $key_trace .= '['.$key.']['.$i++.']';

                $settings[ $key ][] = $this->get_save_fields($settings, $key_trace, $field['sub_fields']);


                /*
                foreach( $_POST['post_code_repeater'] AS $index => $data )
                {
                    //var_dump($field['sub_fields']);
                    foreach( $field['sub_fields'] AS $repeater_key => $sub_field )
                    {
                        $rkey = $key.'_'.$repeater_key.'_'.$index;


                        $rkey[$index] = $key.'_'.$repeater_key.'_';

                        //$settings[ sanitize_key( $rkey ) ] = sanitize_text_field( $_POST[$rkey] );

                        var_dump($sub_field);


                        if( $sub_field['type'] == 'repeater' )
                        {
                            die();
                            $settings[ $key ][$index][$repeater_key] = $this->get_save_fields($sub_field);

                        }
                        else
                        {
                            $settings[ $key ][$index][$repeater_key] = sanitize_text_field( $data[$repeater_key] );
                        }
                    }
                }
                */

            }
            else //if( isset( $_POST[$key] ) )
            {
                $settings[ sanitize_key( $key ) ][] = sanitize_text_field( $_POST[$key] );
                var_dump('post');
                var_dump($settings);
            }
        }
        return $settings;
    }

    function get_settings_fields( $with_sections = false ) {

        $fields = include sl_delivery_segments::get_config('plugin_path') . 'includes/settings-fields.php';

        if( !$with_sections )
        {
            $f = array();

            foreach( $fields AS $section_fields )
            {
                $f = $f + $section_fields;
            }

            $fields = $f;
        }

        return $fields;
    }

    function extract_repeater_value($key, $settings)
    {
        $kk = array_map( function( $k ) {
            return trim( $k, '[]' );
        }, explode('[',$key));

        $value = $settings;

        foreach( $kk AS $x )
        {
            $value = $value[$x];
        }

        return $value;
    }

    function extract_repeater_sub_fields($key, $settings)
    {
        $kk = array_map( function( $k ) {
            return trim( $k, '[]' );
        }, explode('[',$key));

        $value = $settings;
        //var_dump($value);
        foreach( $kk AS $x )
        {
            $value = $value[$x];
        }

        return $value;
    }

    function generate_field( $key, $field, $settings )
    {
        ?>
        <?php if( $field['type'] === 'repeater'): ?>
        <div class="repeater-block">
        <table>
        <?php else: ?>
        <tr valign="top">
            <th scope="row" class="titledesc">
                <label for="<?php echo $key; ?>"><?php echo $field['label']; ?></label>
            </th>
            <td class="forminp forminp-text">
            <?php endif; ?>
                <?php
                switch( $field['type'] ) {

                    case 'repeater' :

                        ?>
                            <table>
                        <?php

                        if( strpos( $key, '][' ) !== false )
                        {
                            $value = $this->extract_repeater_sub_fields($key, $settings);
                        }
                        else if( isset( $settings[$key] ))
                        {
                            $value = $settings[$key];
                        }
                        else
                        {
                            $value = array(0 => '');

                        }
                        foreach ($value AS $index => $data)
                        {
                            foreach ($field['sub_fields'] AS $repeater_key => $sub_field)
                            {
                                $base_key = $key . '[' . $index . ']' . '[' . $repeater_key . ']';

                                $this->generate_field($base_key, $sub_field, $settings);
                            }
                        }

                        /*
                        $i = 1;

                        $count = count($settings[$key]) + 2;

                        for ( $i = 0; $i <= $count ; $i++ )
                        {
                            foreach ($field['sub_fields'] AS $repeater_key => $sub_field) {
                                $this->generate_field($key . '[' . $i . ']' . '[' . $repeater_key . ']', $sub_field, $settings);
                            }
                        }
                        */
                            ?>
                            </table></div>
                        <?php

                        break;
                    case 'text' :

                        if( strpos( $key, '][' ) !== false )
                        {
                            $value = $this->extract_repeater_value($key, $settings);
                        }
                        else
                        {
                            $value = $settings[$key];
                        }

                        ?>
                        <input name="<?php echo $key; ?>" id="<?php echo $key; ?>" type="text" style=""
                               value="<?php echo $value; ?>" class="" placeholder="">
                        <?php
                        break;
                    case 'select' :
                        ?>
                        <select name="<?php echo $key; ?>">
                            <?php foreach( $field['options'] AS $value => $label ) :
                                $value = ( is_integer( $value ) ? sanitize_title( $label ) : $value );

                                var_dump( $settings[$key] );
                                var_dump( $value );
                                ?>
                                <option value="<?php echo $value; ?>" <?php selected( $settings[$key], $value ); ?>><?php echo $label; ?></option>
                            <?php endforeach; ?>
                        </select>
                        <?php
                        break;
                    case 'checkbox' :
                        $value = ( isset( $field['value'] ) ? $field['value'] : '1' );
                        ?>
                        <input name="<?php echo $key; ?>" id="<?php echo $key; ?>" type="checkbox" style=""
                               value="<?php echo $value; ?>" class="" placeholder=""
                            <?php checked( $settings[$key], $value ); ?>
                        />
                        <?php
                        break;
                }
                ?>
                <span class="description"><?php echo $field['desc']; ?></span>

        <?php if( $field['type'] == 'repeater'): ?>
        </table></div>
        <?php else : ?>
            </td>
        </tr>
        <?php endif; ?>
        <?php
    }

    /**
     * Register and enqueue admin-specific style sheet.
     *
     * @since     0.1.0
     *
     * @return    null    Return early if no settings page is registered.
     */
    public function enqueue_settings_styles() {
        global $wp_scripts;

        wp_enqueue_style($this->plugin_slug . '-admin-options-styles', sl_delivery_segments::get_config('plugin_url') . 'assets/css/admin-options.css', array(), self::VERSION);


        /*
        wp_enqueue_style('woocommerce_frontend_styles', WC()->plugin_url() . '/assets/css/woocommerce.css');
        $jquery_version = isset($wp_scripts->registered['jquery-ui-core']->ver) ? $wp_scripts->registered['jquery-ui-core']->ver : '1.9.2';
        wp_enqueue_style('jquery-ui-style', '//ajax.googleapis.com/ajax/libs/jqueryui/' . $jquery_version . '/themes/smoothness/jquery-ui.css');
        wp_enqueue_style('woocommerce_admin_styles', WC()->plugin_url() . '/assets/css/admin.css');
        wp_enqueue_style($this->plugin_slug . '-admin-styles', LAVENDLA_PARTNERS_PLUGIN_URL . 'assets/css/admin.css', array(), self::VERSION);
        */
    }

    /**
     * Register and enqueue admin-specific JavaScript.
     *
     * @since     0.1.0
     *
     * @return    null    Return early if no settings page is registered.
     */
    public function enqueue_settings_scripts() {
        /*
        //global $current_screen, $typenow, $woocommerce;
        wp_enqueue_script($this->plugin_slug . '-admin-script', LAVENDLA_PARTNERS_PLUGIN_URL . 'assets/js/admin-product-options.js', array('jquery', 'select2'), self::VERSION);

        //Inject variables into our scripts
        wp_localize_script($this->plugin_slug . '-admin-script', 'lavendla_partners', array(
        'woocommerce_url'           => WC()->plugin_url(),
        'site_url'                  => get_bloginfo('url'),
        'ajax_url'                  => admin_url('/admin-ajax.php'),
        'search_products_nonce' 	=> wp_create_nonce("search-products"),

        'search_str'                => __( 'Search for a partner...', $this->plugin_slug )
        ));
        */
    }


}

