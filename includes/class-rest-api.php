<?php

namespace Widget_Banner\Includes;

if (!defined('ABSPATH')) {
    exit;
}

class Rest_Api
{
    /**
     * Registra gli hook REST API.
     */
    public function register(): void
    {
        add_action('rest_api_init', [$this, 'registerRoutes']);
    }

    /**
     * Registra gli endpoint del plugin.
     */
    public function registerRoutes(): void
    {
        register_rest_route('widget-loco/v1', '/test', [
            'methods'             => 'POST',
            'callback'            => [$this, 'handleTest'],
            'permission_callback' => '__return_true',
        ]);
    }

    /**
     * Gestisce la richiesta POST /wp-json/widget-loco/v1/test.
     */
    public function handleTest(\WP_REST_Request $request): \WP_REST_Response
    {
        $message = sanitize_text_field($request->get_param('message'));

        return new \WP_REST_Response([
            'success' => true,
            'message' => 'Risposta dal backend WordPress',
            'received' => $message,
        ], 200);
    }
}