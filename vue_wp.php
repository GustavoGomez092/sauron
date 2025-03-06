<?php
/*
Plugin Name: Struck Logger
Description: Plugin Designed to track maintenance actions on a website and generate reports
Version: 0.0.10
Author: Gustavo Gomez
Author URI: https://github.com/GustavoGomez092
*/


class struck
{

    protected $plugin_options_page = '';

    /**
     * Class constructor
     */
    public function __construct()
    {
        require('plugin_activation.php');
        require('plugin_options.php');
        require('includes/table_creation.php');
        require('includes/observers.php');
        require('includes/endpoints.php');
        add_action('admin_enqueue_scripts', [$this, 'REST_API_DATA_LOCALIZER']);
    }

    /**
     * Get latest Wordfence scan
     */
    public function getLatestWordfenceScan()
    {
        // if class does not exist, return
        if (!class_exists('wordfence')) {
            return array();
        }
        $_POST['lastissuetime'] = 0;
        $_POST['lastctime'] = 0;

        wordfence::ajax_loadIssues_callback();
        return wordfence::ajax_activityLogUpdate_callback();
    }

    /**
     * Script tag modifier
     */

    public function add_type_attribute_front($tag, $handle, $src)
    {
        // change the script tag by adding type="module" and return it.
        if ($handle === 'struck-plugin-dev' || $handle === 'struck-plugin-prod') {
            $tag = '<script type="module" src="' . esc_url($src) . '"></script>';
            return $tag;
        }
        // if not your script, do nothing and return original $tag
        return $tag;
    }

    /**
     * Get Page Speed Data
     */
    public function get_pagespeed_data()
    {
        // API Key
        $api_key = 'AIzaSyCMEdb-e-dCAFpdsOzbZQ7xnFd5Yf4xuXY';
        $url = '';
        // Site URL
        if (
            substr($_SERVER['REMOTE_ADDR'], 0, 4) == '127.'
            || $_SERVER['REMOTE_ADDR'] == '::1'
        ) {
            $url = 'https://www.struck.com';
        } else {
            $url = site_url();
        }

        // API endpoint
        $endpoint = 'https://www.googleapis.com/pagespeedonline/v5/runPagespeed';

        // Build the query arguments
        $args = array(
            'url' => $url,
            'key' => $api_key,
            'strategy' => 'desktop',
        );

        // Add query arguments to the endpoint
        $api_url = add_query_arg($args, $endpoint);

        // Make the GET request using wp_remote_get()
        $response = wp_remote_get($api_url, array('timeout' => 180));

        // Check for errors
        if (is_wp_error($response)) {
            return 'Error: ' . $response->get_error_message();
        }

        // Parse the response
        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);

        // Return the response data
        return $data;
    }


    /**
     * Initialize RADL
     */
    public function REST_API_DATA_LOCALIZER()
    {
        // check if we are on the correct page
        if (get_current_Screen()->id !== 'toplevel_page_struck-logs')
            return;


        // define the name of the file to be inserted
        $name = 'struckData';
        $site_url = site_url();
        $plugin_data = $this->get_installed_plugins_data();
        $wordfence_logs = $this->getLatestWordfenceScan();


        // add the data you want to pass from PHP to JS
        // Data will be inserted in the window object with the name defined above

        // Get ACF options
        $plugin_options = array();

        // Get custom data example
        $custom_data = array(
            'plugin_options' => get_fields('options'),
            'installed_plugins' => $plugin_data,
            'site_url' => $site_url,
            'wordfence_logs' => $wordfence_logs
        );

        $normalized_array = array_merge($plugin_options, $custom_data);

        wp_register_script($name, '');
        wp_enqueue_script($name);
        wp_add_inline_script($name, 'window.' . $name . ' = ' . wp_json_encode($normalized_array), 'after');
    }

    /**
     * Get Installed plugin data
     */
    public function get_installed_plugins_data()
    {
        // Fetch all installed plugins
        $installed_plugins = get_plugins(); // WordPress function to get all installed plugins
        $plugin_data_array = array();

        foreach ($installed_plugins as $plugin_file => $plugin_info) {
            // Extract the plugin slug
            $plugin_slug = dirname($plugin_file);

            // Fetch plugin info from WordPress Repository API
            $api_url = "https://api.wordpress.org/plugins/info/1.0/" . $plugin_slug . ".json";
            $response = wp_remote_get($api_url);

            if (is_wp_error($response)) {
                continue; // Skip this plugin if API request failed
            }

            $plugin_info_data = json_decode(wp_remote_retrieve_body($response), true);

            if (empty($plugin_info_data)) {
                continue; // Skip if no valid data returned
            }

            // Populate the plugin data array
            $plugin_data_array[] = array(
                "plugin_name" => $plugin_info_data['name'] ?? 'Unknown',
                "WP_required_version" => $plugin_info_data['requires'] ?? 'Unknown',
                "tested_up_to_WP_version" => $plugin_info_data['tested'] ?? 'Unknown',
                "requires_php" => $plugin_info_data['requires_php'] ?? 'Unknown',
                "rating" => $plugin_info_data['rating'] ?? 0,
                "last_repo_update" => $plugin_info_data['last_updated'] ?? 'Unknown',
            );

            foreach ($plugin_data_array as $key => $plugin_data) {
                if ($plugin_data["plugin_name"] === 'Unknown') {
                    unset($plugin_data_array[$key]);
                }
            }

            $plugin_data_array = array_values($plugin_data_array);
        }

        return $plugin_data_array;
    }

    /**
     * Plugin shortcode for front-end
     */
    function plugin_shortcode($atts)
    {
        $handle = 'struck-plugin-';

        add_filter('script_loader_tag', [$this, 'add_type_attribute_front'], 10, 3);

        // enqueue development or production Vue code
        if (file_exists(dirname(__FILE__) . "/dist/vue-wp.js")) {
            $handle .= 'prod';
            wp_enqueue_script($handle, plugins_url("/dist/vue-wp.js", __FILE__), ['wp-element'], '0.1', true);
            wp_enqueue_style($handle, plugins_url("/dist/vue-wp.css", __FILE__), false, '0.1', 'all');
        } else {
            $handle .= 'dev';
            wp_enqueue_script($handle, 'http://localhost:5173/src/main.js', ['wp-element'], '0.1', true);
        }
        return "<div id='struck' class='struck'></div>";
    }

    /**
     * Inserting nonce to window object for admin
     */
    public function add_nonce()
    {
        wp_register_script(
            'custom-js-file',
            plugins_url("custom.js", __FILE__),
            [],
            null,
            false
        );

        wp_enqueue_script('custom-js-file');
    }

    /**
     * Custom API endpoints for admin
     */

    public function get_user(WP_REST_Request $request)
    {
        $user = wp_get_current_user();
        return $user;
    }

    // Create a REST endpoint to consume get_pagespeed_data
    public function api_init()
    {
        register_rest_route('struck/v1', '/pagespeed/', [
            'methods' => 'GET',
            'callback' => [$this, 'get_pagespeed_data'],
            'permission_callback' => function () {
                return current_user_can('manage_options');
            }
        ]);
    }

    /**
     * Initialize hooks.
     */
    public function init()
    {
        add_shortcode('struck', [$this, 'plugin_shortcode']);
        add_action('rest_api_init', [$this, 'api_init']);

        // adding nonce to the window object if logged in
        add_action('admin_enqueue_scripts', function () {
            if (is_user_logged_in()) {
                $this->add_nonce();
                wp_localize_script('custom-js-file', 'wpApiSettings', array(
                    'root' => esc_url_raw(rest_url()),
                    'nonce' => wp_create_nonce('wp_rest')
                ));
            }
        });
    }
}

$wp_vue = new struck();
$wp_vue->init();
