<?php

namespace Widget_Banner\Includes;

use Widget_Banner\Public_Frontend\Frontend_Renderer;

if (!defined('ABSPATH')) {
    exit;
}

class Shortcode
{
    private Frontend_Renderer $renderer;

    public function __construct(Frontend_Renderer $renderer)
    {
        $this->renderer = $renderer;
    }

    public function register(): void
    {
        add_action('init', [$this, 'registerShortcodes']);
    }

    public function registerShortcodes(): void
    {
        add_shortcode('widget_banner', [$this, 'render']);
        add_shortcode('widget_loco', [$this, 'render']);

        $pages = widget_loco_get_options()['pages'];
        foreach ($pages as $page_id => $page) {
            $tag = 'widget_banner_' . $page_id;
            add_shortcode($tag, [$this, 'render']);
        }
    }

    public function render($atts = [], $content = null, $shortcode_tag = ''): string
    {
        $atts = is_array($atts) ? $atts : [];
        $profile_id = '';
        if (is_string($shortcode_tag) && strpos($shortcode_tag, 'widget_banner_') === 0) {
            $profile_id = sanitize_key(substr($shortcode_tag, strlen('widget_banner_')));
        }
        if ($profile_id === '' && isset($atts['id']) && is_string($atts['id'])) {
            $profile_id = sanitize_key($atts['id']);
        }

        return $this->renderPage($profile_id, $atts);
    }

    private function renderPage(string $profile_id, $atts = []): string
    {
        $pages = widget_loco_get_options()['pages'];
        $page = $profile_id !== '' ? ($pages[$profile_id] ?? null) : null;

        if ($page === null) {
            return current_user_can('manage_options')
                ? '<p class="widget-banner-error">Widget Banner: configurazione non trovata. Crea e salva una configurazione nel pannello, poi usa il relativo shortcode.</p>'
                : '';
        }

        wp_enqueue_style(
            'widget-loco-frontend',
            WIDGET_LOCO_URL . 'assets/frontend/css/frontend.css',
            [],
            WIDGET_LOCO_VERSION
        );
        wp_enqueue_script(
            'widget-loco-frontend',
            WIDGET_LOCO_URL . 'assets/frontend/js/frontend.js',
            [],
            WIDGET_LOCO_VERSION,
            true
        );

        $atts = shortcode_atts([
            'title' => 'Widget Banner',
        ], $atts, 'widget_banner');

        return $this->renderer->render($atts, $page);
    }
}