<?php

/**
* http://phpconsole.com
*
* A detached logging facility for PHP, JS and other environments, with analytical twist, to aid your daily development routine.
*
* Watch quick tutorial at: https://vimeo.com/58393977
*
* @link https://github.com/phpconsole
* @copyright Copyright (c) 2012 - 2013 phpconsole.com
* @license See LICENSE file
* @version 1.1.3
*/


class Phpconsole {

    private $version;
    private $type;
    private $api_address;
    private $domain;
    private $users;
    private $user_api_keys;
    private $projects;
    private $initialized;
    private $snippets;
    private $counters;
    private $curl_error_reporting_enabled;
    private $backtrace_depth;

    /*
    ================
    PUBLIC FUNCTIONS
    ================
    */

    /**
     * Constructor - sets preferences
     */
    public function __construct() {

        $this->version = '1.1.3';
        $this->type = 'php';
        $this->api_address = 'https://app.phpconsole.com/api/0.1/';
        $this->domain = false;
        $this->users = array();
        $this->user_api_keys = array();
        $this->projects = array();
        $this->initialized = false;
        $this->snippets = array();
        $this->counters = array();
        $this->curl_error_reporting_enabled = true;
        $this->backtrace_depth = 0;
    }

    /**
     * Set domain
     *
     * @access  public
     * @param   string
     * @return  void
     */
    public function set_domain($domain) {

        $this->domain = $domain;
    }

    /**
     * Add user (developer)
     *
     * @access  public
     * @param   string
     * @param   string
     * @param   string
     * @return  void
     */
    public function add_user($nickname, $user_api_key, $project_api_key) {

        if($this->domain === false) {
            throw new Exception('Domain variable not set.');
        }

        $user_hash = md5($user_api_key.$this->domain);

        $this->users[$nickname] = $user_hash;
        $this->user_api_keys[$user_hash] = $user_api_key;
        $this->projects[$user_hash] = $project_api_key;
    }

    /**
     * User defined php shutdown function
     *
     * @access  public
     * @param   object
     * @return  void
     */
    public static function shutdown($object) {

        $any_snippets = is_array($object->snippets) && count($object->snippets) > 0;
        $any_counters = is_array($object->counters) && count($object->counters) > 0;

        if($any_snippets || $any_counters) {
            $object->_curl($object->api_address, array(
                'client_code_version' => $object->version,
                'client_code_type' => $object->type,
                'snippets' => $object->snippets,
                'counters' => $object->counters
            ));
        }
    }

    /**
     * Add data to phpconsole's local queue
     *
     * @access  public
     * @param   mixed
     * @param   string
     * @return  mixed
     */
    public function send($data_sent, $user = false) {

        $this->_register_shutdown();

        $bt = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);

        $user_hashed_api_key = false;
        $user_api_key = false;
        $project_api_key = false;
        $continue = false;

        if($user === false) {
            if($this->_is_set_cookie('phpconsole_user')) {
                $user_hashed_api_key = $this->_read_cookie('phpconsole_user');
            }
        }
        else {
            if(isset($this->users[$user])) {
                $user_hashed_api_key = $this->users[$user];
            }
        }

        if($user_hashed_api_key !== false) {
            if(isset($this->projects[$user_hashed_api_key])) {
                $project_api_key = $this->projects[$user_hashed_api_key];
                $user_api_key = $this->user_api_keys[$user_hashed_api_key];
                $continue = true;
            }
        }

        if($continue) {
            $this->snippets[] =  array(
                'data_sent' => base64_encode(serialize($data_sent)),
                'file_name' => $bt[$this->backtrace_depth]['file'],
                'line_number' => $bt[$this->backtrace_depth]['line'],
                'address' => $this->_current_page_address(),
                'user_api_key' => $user_api_key,
                'project_api_key' => $project_api_key
            );
        }

        return $data_sent;
    }

    /**
     * Increment selected counter
     *
     * @access  public
     * @param   int
     * @param   string
     * @return  void
     */
    public function count($number = 1, $user = false) {

        $this->_register_shutdown();

        $user_api_key = false;

        if($user === false) {
            if($this->_is_set_cookie('phpconsole_user')) {
                $user_hash = $this->_read_cookie('phpconsole_user');
                $user_api_key = $this->user_api_keys[$user_hash];
            }
        }
        else {
            if(isset($this->users[$user])) {
                $user_hash = $this->users[$user];
                $user_api_key = $this->user_api_keys[$user_hash];
            }
        }

        if($user_api_key !== false) {
            if(!isset($this->counters[$user_api_key][$number])) {
                $this->counters[$user_api_key][$number] = 0;
            }

            $this->counters[$user_api_key][$number]++;
        }
    }

    /**
     * Save cookie (that allows for identification) in user's browser
     *
     * @access  public
     * @param   string
     * @return  void
     */
    public function set_user_cookie($name) {

        $this->_register_shutdown();

        if(isset($this->users[$name])) {
            $user_hash = $this->users[$name];

            $this->_set_cookie('phpconsole_user', $user_hash, time()+60*60*24*365);

            $this->send('Cookie for user "'.$name.'" and domain "'.$this->domain.'" has been set.', $name);
        }
    }

    /**
     * Destroy cookie (that allows for identification) in user's browser
     *
     * @access  public
     * @param   string
     * @return  void
     */
    public function destroy_user_cookie($name) {

        $this->_register_shutdown();

        if(isset($this->users[$name])) {
            $this->_set_cookie('phpconsole_user', '', 0);

            $this->send('Cookie for user "'.$name.'" and domain "'.$this->domain.'" has been destroyed.', $name);
        }
    }

    /**
     * Check if phpconsole is initialized
     *
     * @access  public
     * @return  bool
     */
    public function is_initialized() {
        return $this->initialized;
    }

    /**
     * Disable displaying errors if response from cURL != 200
     *
     * @access  public
     * @return  bool
     */
    public function disable_curl_error_reporting() {
        $this->curl_error_reporting_enabled = false;
    }

    /**
     * Set backtrace depth to determine correct file and line number that called send()
     *
     * @access  public
     * @param   int
     * @return  void
     */
    public function set_backtrace_depth($depth) {

        $this->backtrace_depth = $depth;
    }

    /*
    =================
    PRIVATE FUNCTIONS
    =================
    */

    /**
     * cURL to selected address with provided parameters
     *
     * @access  private
     * @param   string
     * @param   array
     * @return  void
     */
    private function _curl($url, $params) {

        $post_string = http_build_query($params);
        $headers = array('Content-Type: application/x-www-form-urlencoded');

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_string);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        curl_exec($ch);
        $curl_error = curl_error($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if($http_code !== 200 && $this->curl_error_reporting_enabled) {
            trigger_error(htmlentities('cURL error code '.$http_code.': '.$curl_error));
        }
    }

    /**
     * Register shutdown function and mark as initialized
     *
     * @access  private
     * @return  void
     */
    private function _register_shutdown() {

        if(!$this->is_initialized()) {

            register_shutdown_function('phpconsole::shutdown', $this);

            $this->initialized = true;
        }
    }

    /**
     * Check if cookie exists
     *
     * @access  private
     * @param   string
     * @return  bool
     */
    private function _is_set_cookie($name) {
        return isset($_COOKIE[$name]);
    }

    /**
     * Read cookie
     *
     * @access  private
     * @param   string
     * @return  string
     */
    private function _read_cookie($name) {
        return $_COOKIE[$name];
    }

    /**
     * Set cookie
     *
     * @access  private
     * @param   string
     * @param   string
     * @param   int
     * @return  void
     */
    private function _set_cookie($name, $value, $time) {
        setcookie($name, $value, $time, '/', $this->domain);
    }

    /**
     * Get full address for current page
     *
     * @access  private
     * @return  string
     */
    private function _current_page_address() {

        if(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') {
            $address = 'https://';
        }
        else {
            $address = 'http://';
        }

        if(isset($_SERVER['HTTP_HOST'])) {
            $address .= $_SERVER['HTTP_HOST'];
        }

        if(isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] != '80') {
            $address .= ':'.$_SERVER['SERVER_PORT'];
        }

        if(isset($_SERVER['REQUEST_URI'])) {
            $address .= $_SERVER['REQUEST_URI'];
        }

        return $address;
    }

}
