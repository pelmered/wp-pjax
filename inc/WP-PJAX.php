<?php

require_once 'Util.php';

/**
 * Class WP_PJAX_WP_PJAX
 *
 * @author Peter Elmered
 */
class WP_PJAX_WP_PJAX
{

    /**
     * Is current request PJAX?
     *
     * @var boolean
     */
    public $is_pjax;

    private $config = array();

    public $plugin_url;

    public $plugin_path;

    /**
     * @var stdClass
     */
    public $page_cache;

    public function __construct()
    {
        $this->page_cache = new stdClass();
    }

    public function run()
    {
        $this->plugin_url = plugins_url() . '/wp-pjax';
        $this->plugin_path = plugin_dir_path(dirname(__FILE__));

        $config = wp_pjax_get_instance('Config');
        $this->config = $config->get();

        if (is_admin()) {
            add_action(
                'wp_enqueue_scripts',
                function () {
                    wp_enqueue_style('wp-pjax-admin', $this->plugin_url . '/css/wp-pjax-admin.css');
                }
            );
        }

        if (!isset($this->config[WP_PJAX_CONFIG_PREFIX . 'enable'])
            || $this->config[WP_PJAX_CONFIG_PREFIX . 'enable'] !== 'checked'
        ) {
            return;
        }

        $this->is_pjax = is_pjax_request();

        global $wp_pjax_options;
        $wp_pjax_options = $this->config;

        if ($this->is_pjax) {
            if ($this->config[WP_PJAX_CONFIG_PREFIX . 'page-cache'] == 1) {
                add_action('send_headers', array(&$this, 'send_headers'), 2, 999);

                $this->page_cache = wp_pjax_get_instance('PageCache');
                $this->page_cache->init($this->config);
            } elseif ($this->config[WP_PJAX_CONFIG_PREFIX . 'show-extended-notice'] == 1) {
                header('PJAX-Page-Cache: DISABLED');
            }

            //Include and render page template
            add_action('wp', array(&$this, 'pjax_render'));
        } else {
            add_action('get_header', array(&$this, 'pjax_load'));
        }

        if ($this->config[WP_PJAX_CONFIG_PREFIX . 'page-cache-prefetch'] == 1) {
            $this->page_cache = wp_pjax_get_instance('PageCachePrefetch');
            $this->page_cache->init($this->config);
        }
    }

    /**
     * @param $wp
     * @param stdClass $pg
     */
    public function send_headers($wp, $pg)
    {
        if (!$pg) {
            $pg = $this->page_cache;
        }

        if ($pg->status !== 'SKIP') {
            //Attemt to cache the HTML in the browser cache
            //But do not serve cached pages to prefetch
            if (!wp_pjax_check_request('HTTP_X_WP_PJAX_PREFETCH')
                && $this->config[WP_PJAX_CONFIG_PREFIX . 'browser-page-cache'] == 1
            ) {
                $seconds_to_cache = 650;
                $ts = gmdate("D, d M Y H:i:s", time() + $seconds_to_cache) . " GMT";
                header("Expires: $ts");
                header("Pragma: cache");
                header("Cache-Control: max-age=$seconds_to_cache");
            }

            // Unset cookies
            if ($this->config[WP_PJAX_CONFIG_PREFIX . 'strip-cookies'] == 1 && isset($_SERVER['HTTP_COOKIE'])) {
                $cookies = explode(';', $_SERVER['HTTP_COOKIE']);
                foreach ($cookies as $cookie) {
                    $parts = explode('=', $cookie);
                    $name = trim($parts[0]);
                    setcookie($name, '', time() - 1000);
                    setcookie($name, '', time() - 1000, '/');
                }
            }
        }

        if ($this->config[WP_PJAX_CONFIG_PREFIX . 'show-extended-notice'] == 1
            && current_user_can('edit_plugins')
        ) {
            header('PJAX-loaded-resource: ' . $pg->key);

            if (!isset($pg->status)) {
                $pg->status == 'MISS';
            }
            header('PJAX-Page-Cache: ' . $pg->status);
        }
    }

    public function pjax_load()
    {
        add_action(
            'wp_enqueue_scripts',
            function () {
                wp_enqueue_script('wp-pjax', $this->plugin_url . '/js/jquery-pjax/jquery.pjax.js', array('jquery'));
            }
        );
        add_action(
            'wp_enqueue_scripts',
            function () {
                wp_enqueue_script('theme-pjax', $this->plugin_url . '/js/wp-pjax.js', array('jquery', 'pjax'));
            }
        );
        add_action(
            'wp_enqueue_scripts',
            function () {
                wp_enqueue_script('theme-pjax', $this->plugin_url . '/wp-pjax.js.php', array('jquery', 'pjax'));
            }
        );

        add_action('wp_head', array(&$this, 'generate_js'));

        if ($this->config[WP_PJAX_CONFIG_PREFIX . 'show-notice'] == 1
            && current_user_can('edit_plugins')
            || $this->config['debug_mode']
        ) {
            add_action(
                'wp_enqueue_scripts',
                function () {
                    wp_enqueue_script('jquery-notice', $this->plugin_url . '/js/jquery.notice.js', array('jquery'));
                }
            );
            add_action(
                'wp_enqueue_scripts',
                function () {
                    wp_enqueue_style('wp-pjax', $this->plugin_url . '/css/wp-pjax.css');
                }
            );
        }

        if ($this->config[WP_PJAX_CONFIG_PREFIX . 'show-toggle'] == 1
            && current_user_can('edit_plugins')
            || $this->config['debug_mode']
        ) {
            add_action(
                'wp_enqueue_scripts',
                function () {
                    wp_enqueue_style('wp-pjax', $this->plugin_url . '/css/wp-pjax.css');
                }
            );

            add_action('wp_footer', array(&$this, 'add_toggle_html'));
        }
    }

    public function add_toggle_html()
    {
        include $this->plugin_path . 'views/toggle.php';
    }

    public function generate_js()
    {
        include $this->plugin_path . 'inc/WP-PJAX.js.php';
    }

    public function pjax_render($wp)
    {
        $this->page_cache = new stdClass();
        $this->page_cache = wp_pjax_get_instance('PageCache');

        if ($this->config[WP_PJAX_CONFIG_PREFIX . 'page-cache'] == 1) {
            ob_start();
        }

        //Include the original WP template loader. This will output the right page template/content
        include ABSPATH . WPINC . DIRECTORY_SEPARATOR . 'template-loader.php';

        if ($this->config[WP_PJAX_CONFIG_PREFIX . 'page-cache'] == 1) {
            $page_content = ob_get_clean();

            $this->page_cache->set($page_content);

            echo $page_content;
            die();
        }

        return '';
    }
}
