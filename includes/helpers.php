<?php

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Renderizza un template PHP e restituisce l'HTML generato.
 *
 * @param string $path Percorso relativo del template.
 * @param array  $data Variabili da rendere disponibili nel template.
 *
 * @return string
 */
function widget_loco_view(string $path, array $data = []): string
{
    $fullPath = WIDGET_LOCO_PATH . $path;

    if (!file_exists($fullPath)) {
        return '';
    }

    // Estrae le variabili dall'array $data, evitando conflitti con variabili già esistenti
    extract($data, EXTR_SKIP);

    // Avvia l'output buffering per catturare l'output del template
    ob_start();

    // Include il template PHP dove è possibile accedere alle variabili estratte
    include $fullPath;

    // Restituisce l'output catturato e pulisce il buffer
    return ob_get_clean();
}

/**
 * Restituisce le impostazioni della pagina shortcode con valori sicuri.
 *
 * @return array
 */
function widget_loco_get_options(): array
{
    $saved_options = get_option('widget_loco_options', []);
    $saved_pages = is_array($saved_options) ? ($saved_options['pages'] ?? null) : null;

    return [
        'pages' => widget_banner_get_pages($saved_pages, widget_banner_get_formats()),
    ];
}

/**
 * Restituisce le configurazioni aggiuntive delle pagine.
 *
 * @param mixed $saved_pages Configurazioni salvate nell'opzione WordPress.
 *
 * @return array
 */
function widget_banner_get_pages($saved_pages, array $default_formats = []): array
{
    if (!is_array($saved_pages)) {
        return [];
    }

    $pages = [];
    foreach ($saved_pages as $saved_id => $page) {
        if (!is_array($page)) {
            continue;
        }

        $id = sanitize_key((string) $saved_id);
        if ($id === '' || isset($pages[$id])) {
            continue;
        }

        $color = isset($page['primary_color']) && is_string($page['primary_color'])
            ? sanitize_hex_color($page['primary_color'])
            : null;
        $logo_id = isset($page['logo_id']) ? absint($page['logo_id']) : 0;
        if ($logo_id && !wp_attachment_is_image($logo_id)) {
            $logo_id = 0;
        }

        $pages[$id] = [
            'name' => isset($page['name']) && is_string($page['name'])
                ? sanitize_text_field($page['name'])
                : $id,
            'primary_color' => $color ?: '#2e6cab',
            'logo_id' => $logo_id,
            'property_id' => isset($page['property_id']) && is_string($page['property_id'])
                ? sanitize_text_field($page['property_id'])
                : '',
            'hero_subtitle' => isset($page['hero_subtitle']) && is_string($page['hero_subtitle'])
                ? sanitize_textarea_field($page['hero_subtitle'])
                : '',
            'final_message' => isset($page['final_message']) && is_string($page['final_message'])
                ? sanitize_textarea_field($page['final_message'])
                : '',
            'formats' => widget_banner_get_formats($page['formats'] ?? $default_formats),
        ];
    }

    return $pages;
}

/**
 * Restituisce i posizionamenti pubblicitari configurati.
 *
 * @param mixed $saved_formats Posizionamenti salvati nell'opzione WordPress.
 *
 * @return array
 */
function widget_banner_get_formats($saved_formats = null): array
{
    $defaults = [
        'd-masthead' => ['dev' => 'desktop', 'cat' => 'impact', 'name' => 'Masthead', 'size' => '970×250', 'place' => 'Testata, sotto la navigazione, above the fold', 'fx' => 'Statico o HTML5; video muto in autoplay opzionale', 'dims' => 'Slot 970×250 px', 'model' => 'CPM', 'price' => 7, 'file_formats' => 'Da definire', 'max_weight' => 'Da definire', 'third_party_tracking' => 'Da definire', 'creative_delivery' => 'Da definire'],
        'd-mpu' => ['dev' => 'desktop', 'cat' => 'standard', 'name' => 'MPU / Box', 'size' => '300×250', 'place' => 'Colonna destra, primo box', 'fx' => 'Statico o HTML5', 'dims' => 'Slot 300×250 px', 'model' => 'CPM', 'price' => 3, 'file_formats' => 'Da definire', 'max_weight' => 'Da definire', 'third_party_tracking' => 'Da definire', 'creative_delivery' => 'Da definire'],
        'd-halfpage' => ['dev' => 'desktop', 'cat' => 'impact', 'name' => 'Half Page', 'size' => '300×600', 'place' => 'Colonna destra, sticky durante lo scroll', 'fx' => "Resta visibile mentre l'utente scorre", 'dims' => 'Slot 300×600 px', 'model' => 'CPM', 'price' => 8, 'file_formats' => 'Da definire', 'max_weight' => 'Da definire', 'third_party_tracking' => 'Da definire', 'creative_delivery' => 'Da definire'],
        'd-sticky' => ['dev' => 'desktop', 'cat' => 'impact', 'name' => 'Sticky Footer', 'size' => '970×90', 'place' => 'Fondo pagina, ancorato al browser', 'fx' => "Compare allo scroll, chiudibile dall'utente", 'dims' => 'Slot 970×90 px, larghezza piena del browser', 'model' => 'CPM', 'price' => 5, 'file_formats' => 'Da definire', 'max_weight' => 'Da definire', 'third_party_tracking' => 'Da definire', 'creative_delivery' => 'Da definire'],
        'd-inter' => ['dev' => 'desktop', 'cat' => 'impact', 'name' => 'Interstitial', 'size' => '800×600', 'place' => 'Centro pagina, sopra il contenuto', 'fx' => 'All’apertura o al cambio pagina, frequency cap 1/utente/giorno (da definire)', 'dims' => 'Slot 800×600 px', 'model' => 'CPM', 'price' => 10, 'file_formats' => 'Da definire', 'max_weight' => 'Da definire', 'third_party_tracking' => 'Da definire', 'creative_delivery' => 'Da definire'],
        'd-video' => ['dev' => 'desktop', 'cat' => 'video', 'name' => 'Video In-Article', 'size' => '16:9', 'place' => 'Dentro l’articolo, tra i paragrafi', 'fx' => 'Parte muto quando entra in vista, si ferma quando esce', 'dims' => 'Player 640×360 px', 'model' => 'CPV / CPM', 'price' => 10, 'file_formats' => 'Da definire', 'max_weight' => 'Da definire', 'third_party_tracking' => 'Da definire', 'creative_delivery' => 'Da definire'],
        'd-native' => ['dev' => 'desktop', 'cat' => 'native', 'name' => 'Branded Content', 'size' => 'Articolo', 'place' => 'Feed magazine e homepage', 'fx' => "Articolo firmato con label 'Sponsorizzato'; prezzo variabile in base alla frequenza di pubblicazione", 'dims' => 'Card nel feed + pagina articolo dedicata', 'model' => 'CPM (variabile per frequenza)', 'price' => 20, 'file_formats' => 'Da definire', 'max_weight' => 'Da definire', 'third_party_tracking' => 'Da definire', 'creative_delivery' => 'Da definire'],
        'd-takeover' => ['dev' => 'desktop', 'cat' => 'sponsor', 'name' => 'Homepage Takeover', 'size' => 'Pacchetto', 'place' => 'Homepage: masthead + MPU + half page', 'fx' => 'Tutti i posizionamenti impact in esclusiva, fino al raggiungimento del target di impression concordato', 'dims' => '970×250 + 300×250 + 300×600', 'model' => 'CPM', 'price' => 16, 'file_formats' => 'Da definire', 'max_weight' => 'Da definire', 'third_party_tracking' => 'Da definire', 'creative_delivery' => 'Da definire'],
        'm-top' => ['dev' => 'mobile', 'cat' => 'standard', 'name' => 'Top Banner', 'size' => '320×100', 'place' => 'Sotto l’header, above the fold', 'fx' => 'Statico o HTML5', 'dims' => 'Slot 320×100 px (accetta anche 320×50)', 'model' => 'CPM', 'price' => 3, 'file_formats' => 'Da definire', 'max_weight' => 'Da definire', 'third_party_tracking' => 'Da definire', 'creative_delivery' => 'Da definire'],
        'm-mpu' => ['dev' => 'mobile', 'cat' => 'standard', 'name' => 'MPU In-Feed', 'size' => '300×250', 'place' => 'Tra i contenuti, durante lo scroll', 'fx' => 'Statico o HTML5', 'dims' => 'Slot 300×250 px', 'model' => 'CPM', 'price' => 3, 'file_formats' => 'Da definire', 'max_weight' => 'Da definire', 'third_party_tracking' => 'Da definire', 'creative_delivery' => 'Da definire'],
        'm-halfpage' => ['dev' => 'mobile', 'cat' => 'impact', 'name' => 'Half Page Mobile', 'size' => '300×600', 'place' => 'Tra i contenuti, a metà pagina', 'fx' => 'Formato verticale ad alto impatto', 'dims' => 'Slot 300×600 px', 'model' => 'CPM', 'price' => 8, 'file_formats' => 'Da definire', 'max_weight' => 'Da definire', 'third_party_tracking' => 'Da definire', 'creative_delivery' => 'Da definire'],
        'm-sticky' => ['dev' => 'mobile', 'cat' => 'impact', 'name' => 'Sticky Footer', 'size' => '320×50', 'place' => 'Fondo dello schermo, ancorato', 'fx' => 'Sempre visibile durante lo scroll, chiudibile', 'dims' => 'Slot 320×50 px', 'model' => 'CPM', 'price' => 5, 'file_formats' => 'Da definire', 'max_weight' => 'Da definire', 'third_party_tracking' => 'Da definire', 'creative_delivery' => 'Da definire'],
        'm-inter' => ['dev' => 'mobile', 'cat' => 'impact', 'name' => 'Interstitial', 'size' => '320×480', 'place' => 'A tutto schermo, sopra il contenuto', 'fx' => 'Al cambio pagina, chiudibile, frequency cap da definire', 'dims' => 'Slot 320×480 px', 'model' => 'CPM', 'price' => 10, 'file_formats' => 'Da definire', 'max_weight' => 'Da definire', 'third_party_tracking' => 'Da definire', 'creative_delivery' => 'Da definire'],
        'm-video' => ['dev' => 'mobile', 'cat' => 'video', 'name' => 'Video In-Read', 'size' => '16:9', 'place' => 'Dentro l’articolo, tra i paragrafi', 'fx' => 'Parte muto quando entra in vista', 'dims' => 'Player a larghezza schermo, 16:9', 'model' => 'CPV / CPM', 'price' => 10, 'file_formats' => 'Da definire', 'max_weight' => 'Da definire', 'third_party_tracking' => 'Da definire', 'creative_delivery' => 'Da definire'],
        'm-native' => ['dev' => 'mobile', 'cat' => 'native', 'name' => 'Native In-Feed', 'size' => 'Card', 'place' => 'Nel feed delle notizie', 'fx' => "Card con label 'Sponsorizzato'; prezzo variabile in base alla frequenza di pubblicazione", 'dims' => 'Immagine 1200×628 + titolo + testo', 'model' => 'CPM (variabile per frequenza)', 'price' => 20, 'file_formats' => 'Da definire', 'max_weight' => 'Da definire', 'third_party_tracking' => 'Da definire', 'creative_delivery' => 'Da definire'],
        'm-takeover' => ['dev' => 'mobile', 'cat' => 'sponsor', 'name' => 'Takeover Mobile', 'size' => 'Pacchetto', 'place' => 'Homepage mobile: top banner + MPU + sticky', 'fx' => 'Tutti gli spazi della homepage in esclusiva, fino al raggiungimento del target di impression concordato', 'dims' => '320×100 + 300×250 + 320×50', 'model' => 'CPM', 'price' => 16, 'file_formats' => 'Da definire', 'max_weight' => 'Da definire', 'third_party_tracking' => 'Da definire', 'creative_delivery' => 'Da definire'],
    ];

    $formats = [];
    foreach ($defaults as $id => $default) {
        $saved = is_array($saved_formats) && isset($saved_formats[$id]) && is_array($saved_formats[$id])
            ? $saved_formats[$id]
            : [];

        $format = $default;
        $format['id'] = $id;
        $format['dev'] = isset($saved['dev']) && in_array($saved['dev'], ['desktop', 'mobile'], true)
            ? $saved['dev']
            : $default['dev'];
        $format['cat'] = isset($saved['cat']) && in_array($saved['cat'], ['standard', 'impact', 'video', 'native', 'sponsor'], true)
            ? $saved['cat']
            : $default['cat'];

        foreach (['name', 'size', 'place', 'fx', 'dims', 'model', 'file_formats', 'max_weight', 'third_party_tracking', 'creative_delivery'] as $field) {
            if (isset($saved[$field]) && is_string($saved[$field])) {
                $format[$field] = sanitize_textarea_field($saved[$field]);
            }
        }

        if (array_key_exists('price', $saved)) {
            if ($saved['price'] === null || $saved['price'] === '') {
                $format['price'] = null;
            } elseif (is_scalar($saved['price']) && is_numeric($saved['price']) && (float) $saved['price'] >= 0) {
                $format['price'] = (float) $saved['price'];
            }
        }

        $formats[$id] = $format;
    }

    return $formats;
}