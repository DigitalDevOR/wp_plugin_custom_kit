<?php

namespace Widget_Banner\Public_Frontend;

if (!defined('ABSPATH')) {
    exit;
}

class Frontend_Renderer
{
    /**
     * Renderizza il frontend del plugin.
     *
     * @param array $atts Attributi ricevuti dallo shortcode.
     *
     * @return string
     */
    public function render(array $atts, array $page): string
    {
        $options = $page;

        $hex_color = ltrim($options['primary_color'], '#');
        if (strlen($hex_color) === 3) {
            $hex_color = $hex_color[0] . $hex_color[0]
                . $hex_color[1] . $hex_color[1]
                . $hex_color[2] . $hex_color[2];
        }

        $analytics_error = null;
        $monthly_total_users = $options['property_id'] !== ''
            ? widget_loco_get_media_mensile_utenti_totale($options['property_id'], $analytics_error)
            : null;

        $data = [
            'title' => $atts['title'] ?? 'Widget Banner',
            'options' => $options,
            'media_mensile_utenti_totale' => $monthly_total_users,
            'google_analytics_error' => current_user_can('manage_options') ? $analytics_error : null,
            'logo_url' => $options['logo_id']
                ? (wp_get_attachment_image_url($options['logo_id'], 'full') ?: WIDGET_LOCO_URL . 'public/logo-napoli.png')
                : WIDGET_LOCO_URL . 'public/logo-napoli.png',
            'primary_color_rgb' => implode(', ', array_map('hexdec', str_split($hex_color, 2))),
            'formats_json' => wp_json_encode(array_values($options['formats']), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP),
        ];

        return widget_loco_view(
            'public/views/shortcode-template.php',
            $data
        );
    }
}