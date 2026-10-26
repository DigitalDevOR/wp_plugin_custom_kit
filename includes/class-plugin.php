<?php

namespace Widget_Banner\Includes;

use Widget_Banner\Admin\Admin_Page;
use Widget_Banner\Public_Frontend\Frontend_Renderer;
use Widget_Banner\Includes\Assets;

if (!defined('ABSPATH')) {
    exit;
}

class Plugin
{
    public function run(): void
    {
        // Gestione CSS e JavaScript
        $assets = new Assets();
        $assets->register();
        
        // REST API
        $rest_api = new Rest_Api();
        $rest_api->register();

        // Renderer frontend
        $renderer = new Frontend_Renderer();

        // Registrazione shortcode
        $shortcode = new Shortcode($renderer);
        $shortcode->register();

        // Dashboard amministrazione
        if (is_admin()) {
            $admin = new Admin_Page();
            $admin->register();
        }
    }
}