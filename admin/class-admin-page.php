<?php

namespace Widget_Banner\Admin;

if (!defined('ABSPATH')) {
    exit;
}

class Admin_Page
{
    /**
     * Registra gli hook della parte amministrativa.
     */
    public function register(): void
    {
        add_action('admin_menu', [$this, 'addMenuPage']);
        add_action('admin_post_widget_loco_save_settings', [$this, 'saveSettings']);
    }

    /**
     * Salva le configurazioni inviate dal pannello.
     */
    public function saveSettings(): void
    {
        if (!current_user_can('manage_options')) {
            wp_die(esc_html__('Non hai i permessi per modificare queste impostazioni.', 'widget-loco'));
        }

        check_admin_referer('widget_loco_save_settings');

        $input = isset($_POST['widget_loco_options']) && is_array($_POST['widget_loco_options'])
            ? wp_unslash($_POST['widget_loco_options'])
            : [];
        $settings = $this->sanitizeSettings($input);
        update_option('widget_loco_options', $settings);

        $saved = get_option('widget_loco_options');
        $saved_pages = is_array($saved) && is_array($saved['pages'] ?? null)
            ? $saved['pages']
            : [];
        $submitted_pages = is_array($input['pages'] ?? null) ? $input['pages'] : [];
        $status = $saved_pages === $settings['pages']
            ? 'saved'
            : 'error';
        if ($status === 'saved' && count($saved_pages) !== count($submitted_pages)) {
            $status = 'partial';
        }

        wp_safe_redirect(add_query_arg(
            'widget_banner_status',
            $status,
            menu_page_url('widget-loco', false)
        ));
        exit;
    }

    /**
     * Valida e sanifica i valori inviati dalla dashboard.
     *
     * @param mixed $input Valori inviati dal modulo.
     *
     * @return array
     */
    public function sanitizeSettings($input): array
    {
        $current = widget_loco_get_options();
        if (!is_array($input)) {
            return $current;
        }

        $pages = $current['pages'];
        if (!empty($input['pages_submitted'])) {
            $pages = [];
            $submitted_pages = is_array($input['pages'] ?? null) ? $input['pages'] : [];
            $default_formats = widget_banner_get_formats();

            foreach ($submitted_pages as $submitted_key => $submitted_page) {
                if (!is_array($submitted_page)) {
                    continue;
                }

                $name = isset($submitted_page['name']) && is_string($submitted_page['name'])
                    ? sanitize_text_field($submitted_page['name'])
                    : '';
                $slug = isset($submitted_page['slug']) && is_string($submitted_page['slug'])
                    ? sanitize_key($submitted_page['slug'])
                    : '';
                if ($slug === '') {
                    $slug = sanitize_key(sanitize_title($name));
                }

                if ($name === '' || $slug === '') {
                    add_settings_error(
                        'widget_loco_options',
                        'invalid_page_shortcode',
                        'Ogni configurazione deve avere un nome e un identificativo shortcode valido.'
                    );
                    continue;
                }

                if (isset($pages[$slug])) {
                    add_settings_error(
                        'widget_loco_options',
                        'duplicate_page_shortcode',
                        'Gli identificativi shortcode delle configurazioni devono essere univoci.'
                    );
                    continue;
                }

                $previous_id = is_string($submitted_key) && strpos($submitted_key, 'saved-') === 0
                    ? substr($submitted_key, 6)
                    : $slug;
                $previous_page = $current['pages'][$previous_id] ?? null;
                $page_color = isset($submitted_page['primary_color']) && is_string($submitted_page['primary_color'])
                    ? sanitize_hex_color($submitted_page['primary_color'])
                    : null;
                $logo_id = isset($submitted_page['logo_id']) && is_scalar($submitted_page['logo_id'])
                    ? absint($submitted_page['logo_id'])
                    : (int) ($previous_page['logo_id'] ?? 0);
                if ($logo_id && !wp_attachment_is_image($logo_id)) {
                    $logo_id = (int) ($previous_page['logo_id'] ?? 0);
                }
                $property_id = isset($submitted_page['property_id']) && is_string($submitted_page['property_id'])
                    ? trim(sanitize_text_field($submitted_page['property_id']))
                    : ($previous_page['property_id'] ?? '');

                $pages[$slug] = [
                    'name' => $name,
                    'primary_color' => $page_color ?: ($previous_page['primary_color'] ?? '#2e6cab'),
                    'logo_id' => $logo_id,
                    'property_id' => $property_id,
                    'hero_subtitle' => isset($submitted_page['hero_subtitle']) && is_string($submitted_page['hero_subtitle'])
                        ? sanitize_textarea_field($submitted_page['hero_subtitle'])
                        : ($previous_page['hero_subtitle'] ?? ''),
                    'final_message' => isset($submitted_page['final_message']) && is_string($submitted_page['final_message'])
                        ? sanitize_textarea_field($submitted_page['final_message'])
                        : ($previous_page['final_message'] ?? ''),
                    'formats' => $this->sanitizeFormats(
                        $submitted_page['formats'] ?? [],
                        $previous_page['formats'] ?? $default_formats
                    ),
                ];
            }
        }

        return ['pages' => $pages];
    }

    /**
     * Valida i posizionamenti per una singola configurazione.
     *
     * @param mixed $submitted_formats Posizionamenti inviati dal modulo.
     * @param array $current_formats Posizionamenti attuali da usare come fallback.
     *
     * @return array
     */
    private function sanitizeFormats($submitted_formats, array $current_formats): array
    {
        $submitted_formats = is_array($submitted_formats) ? $submitted_formats : [];

        foreach ($current_formats as $format_id => $current_format) {
            $submitted_format = isset($submitted_formats[$format_id]) && is_array($submitted_formats[$format_id])
                ? array_replace($current_format, $submitted_formats[$format_id])
                : $current_format;

            if (!in_array($submitted_format['dev'], ['desktop', 'mobile'], true)) {
                $submitted_format['dev'] = $current_format['dev'];
            }
            if (!in_array($submitted_format['cat'], ['standard', 'impact', 'video', 'native', 'sponsor'], true)) {
                $submitted_format['cat'] = $current_format['cat'];
            }

            foreach (['name', 'size', 'place', 'fx', 'dims', 'model', 'file_formats', 'max_weight', 'third_party_tracking', 'creative_delivery'] as $field) {
                if (!is_string($submitted_format[$field])) {
                    $submitted_format[$field] = $current_format[$field];
                }
            }

            $price = $submitted_format['price'];
            if ($price !== null && $price !== '' && (!is_scalar($price) || !is_numeric($price) || (float) $price < 0)) {
                $submitted_format['price'] = $current_format['price'];
            }

            $submitted_formats[$format_id] = $submitted_format;
        }

        return widget_banner_get_formats($submitted_formats);
    }

    /**
     * Aggiunge la voce di menu nella dashboard.
     */
    public function addMenuPage(): void
    {
        add_menu_page(
            'Widget Banner',               // Titolo della pagina
            'Widget Banner',               // Nome nel menu
            'manage_options',              // Permesso richiesto
            'widget-loco',                 // Slug della pagina
            [$this, 'renderPage'],         // Callback
            'dashicons-admin-generic',     // Icona
            80                             // Posizione nel menu
        );
    }

    /**
     * Renderizza la pagina amministrativa.
     */
    public function renderPage(): void
    {
        echo widget_loco_view(
            'admin/views/settings-page.php',
            [
                'options' => widget_loco_get_options(),
                'status' => isset($_GET['widget_banner_status']) && is_string($_GET['widget_banner_status'])
                    ? sanitize_key(wp_unslash($_GET['widget_banner_status']))
                    : '',
                'google_credentials_status' => is_readable(widget_loco_google_credentials_file())
                    ? 'readable'
                    : (file_exists(widget_loco_google_credentials_file()) ? 'unreadable' : 'missing'),
            ]
        );
    }
}