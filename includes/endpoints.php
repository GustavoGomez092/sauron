<?php

// Endpoints

class Struck_endpoints
{
    private $api;

    function __construct()
    {
        require_once('api.php');
        $this->api = new Struck_API();
        add_action('rest_api_init', [$this, 'api_init']);
    }

    // Validate date function
    private function validateDate($date, $format = 'Y-m-d')
    {
        $d = DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) == $date;
    }

    // Create a REST endpoint to consume get_pagespeed_data
    public function api_init()
    {
        register_rest_route('struck/v1', '/logs/', [
            'methods' => 'GET',
            'args' => array(
                'offset' => array(
                    'validate_callback' => function ($param) {
                        return is_numeric($param);
                    },
                    'required' => false,
                ),
                'limit' => array(
                    'validate_callback' => function ($param) {
                        return is_numeric($param);
                    },
                    'required' => false,
                ),
                'user_ids' => array(
                    'validate_callback' => function ($param) {
                        return is_array($param);
                    },
                    'required' => false,
                ),
                'start_date' => array(
                    'validate_callback' => function ($param) {
                        return $this->validateDate($param);
                    },
                    'required' => false,
                ),
                'end_date' => array(
                    'validate_callback' => function ($param) {
                        return $this->validateDate($param);
                    },
                    'required' => false,
                )
            ),
            'callback' => function ($args) {

                $params = $args->get_params();

                $offset = array_key_exists('offset', $params) ? $params['offset'] : 0;
                $limit = array_key_exists('limit', $params) ? $params['limit'] : 10;
                $user_ids = array_key_exists('user_ids', $params) ? $params['user_ids'] : null;
                $start_date = array_key_exists('start_date', $params) ? $params['start_date'] : null;
                $end_date = array_key_exists('end_date', $params) ? $params['end_date'] : null;

                if ($start_date && $end_date) {
                    return $this->api->get_user_logs_by_date_range($user_ids, $start_date, $end_date);
                } else {
                    return $this->api->get_logs($offset, $limit);
                }
            },
            // 'permission_callback' => '__return_true',
            'permission_callback' => function () {
                return current_user_can('manage_options');
            }
        ]);

        register_rest_route('struck/v1', 'users', [
            'methods' => 'GET',
            'args' => array(
                'offset' => array(
                    'validate_callback' => function ($param) {
                        return is_numeric($param);
                    },
                    'required' => false,
                ),
                'limit' => array(
                    'validate_callback' => function ($param) {
                        return is_numeric($param);
                    },
                    'required' => false,
                ),
                'user_id' => array(
                    'validate_callback' => function ($param) {
                        return is_numeric($param);
                    },
                    'required' => false,
                ),
                'start_date' => array(
                    'validate_callback' => function ($param) {
                        return $this->validateDate($param);
                    },
                    'required' => false,
                ),
                'end_date' => array(
                    'validate_callback' => function ($param) {
                        return $this->validateDate($param);
                    },
                    'required' => false,
                )
            ),
            'callback' => function () {
                $all_users = get_users();
                $final_users = [];

                foreach ($all_users as $value) {
                    $final_users[] = array(
                        'id' => $value->data->ID,
                        'name' => $value->data->display_name,
                        'email' => $value->data->user_email,
                    );
                }

                return $final_users;
            },
            // 'permission_callback' => '__return_true',
            'permission_callback' => function () {
                return current_user_can('manage_options');
            }
        ]);

        register_rest_route('struck/v1', 'total', [
            'methods' => 'GET',

            'callback' => function () {
                return $this->api->get_total_entries();
            },
            // 'permission_callback' => '__return_true',
            'permission_callback' => function () {
                return current_user_can('manage_options');
            }
        ]);


        register_rest_route('struck/v1', 'manual-entry', [
            'methods' => 'POST',
            'args' => array(
                'action_type' => array(
                    'validate_callback' => function ($param) {
                        return is_string($param) && $this->api->validate_action($param);
                    },
                    'required' => true,
                ),
                'action_taken' => array(
                    'validate_callback' => function ($param) {
                        return is_string($param);
                    },
                    'required' => true,
                ),
            ),
            'callback' => function (WP_REST_Request $request) {
                $params = $request->get_params();

                return $this->api->insert_manual_entry($params['action_type'], $params['action_taken']);
            },
            // 'permission_callback' => '__return_true',
            'permission_callback' => function () {
                return current_user_can('manage_options');
            }
        ]);
    }
}

new Struck_endpoints();