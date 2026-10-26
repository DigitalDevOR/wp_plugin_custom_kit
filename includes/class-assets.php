<?php

namespace Widget_Banner\Includes;

if (!defined('ABSPATH')) {
    exit;
}

class Assets
{
    public function register(): void
    {
        add_action('wp_enqueue_scripts', [$this, 'registerFrontendAssets']);

        add_action('admin_enqueue_scripts', [$this, 'enqueueAdminAssets']);
    }

    public function registerFrontendAssets(): void
    {
        wp_register_style(
            'widget-loco-frontend',
            WIDGET_LOCO_URL . 'assets/frontend/css/frontend.css',
            [],
            WIDGET_LOCO_VERSION
        );

        wp_register_script(
            'widget-loco-frontend',
            WIDGET_LOCO_URL . 'assets/frontend/js/frontend.js',
            [],
            WIDGET_LOCO_VERSION,
            true
        );

        wp_localize_script(
            'widget-loco-frontend',
            'WidgetLoco',
            [
                'ajaxUrl' => admin_url('admin-ajax.php'),
                'nonce'   => wp_create_nonce('widget_loco_nonce'),
            ]
        );
    }

    public function enqueueAdminAssets(string $hook): void
    {
        if ($hook !== 'toplevel_page_widget-loco') {
            return;
        }

        wp_enqueue_media();

        wp_enqueue_style(
            'widget-loco-admin',
            WIDGET_LOCO_URL . 'assets/admin/css/admin.css',
            [],
            WIDGET_LOCO_VERSION
        );

        wp_enqueue_script(
            'widget-loco-admin',
            WIDGET_LOCO_URL . 'assets/admin/js/admin.js',
            [],
            WIDGET_LOCO_VERSION,
            true
        );
    }
}