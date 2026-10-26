<?php

if (!defined('ABSPATH')) {
    exit;
}

$default_formats = widget_banner_get_formats();

?>

<div class="wrap widget-loco-admin">

    <h1>Widget Banner</h1>

    <p>
        Non esiste una configurazione predefinita: crea una o più configurazioni indipendenti. Ognuna avrà il proprio shortcode, colore, contenuti, posizionamenti e prezzi.
    </p>

    <div class="widget-loco-admin-card">
        <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
            <input type="hidden" name="action" value="widget_loco_save_settings">
            <?php wp_nonce_field('widget_loco_save_settings'); ?>
            <input type="hidden" name="widget_loco_options[pages_submitted]" value="1">

            <?php if ($status === 'saved') : ?>
                <div class="notice notice-success inline"><p>Configurazioni salvate correttamente.</p></div>
            <?php elseif ($status === 'partial') : ?>
                <div class="notice notice-warning inline"><p>Salvataggio parziale: controlla che ogni configurazione abbia nome e shortcode univoco. Le configurazioni valide sono state salvate.</p></div>
            <?php elseif ($status === 'error') : ?>
                <div class="notice notice-error inline"><p>Non è stato possibile verificare il salvataggio. Riprova e controlla i dati inseriti.</p></div>
            <?php endif; ?>
            <?php if ($google_credentials_status === 'readable') : ?>
                <div class="notice notice-success inline"><p>Credenziali Google trovate e leggibili dal server.</p></div>
            <?php elseif ($google_credentials_status === 'unreadable') : ?>
                <div class="notice notice-error inline"><p>Il file credentials.json è presente ma il server non può leggerlo. Verifica i permessi.</p></div>
            <?php else : ?>
                <div class="notice notice-warning inline">
                    <p>Il file credentials.json non è presente sul server in <code>includes/private-google-analytics/credentials.json</code>. Copialo manualmente nella cartella del plugin installato.</p>
                </div>
            <?php endif; ?>

            <h2 class="widget-banner-formats-heading">Le tue configurazioni</h2>
            <p>Ogni configurazione è indipendente. I posizionamenti e i prezzi proposti sono valori iniziali, modificabili separatamente in ciascuna configurazione.</p>
            <div class="widget-banner-pages">
                <?php foreach ($options['pages'] as $page_id => $page) : ?>
                    <?php $page_index = 'saved-' . sanitize_key($page_id); ?>
                    <article class="widget-loco-admin-card widget-banner-page-card" data-profile-card>
                        <div class="widget-banner-page-heading">
                            <h3>Configurazione: <?php echo esc_html($page['name']); ?></h3>
                            <button type="button" class="button-link-delete widget-banner-remove-page">Rimuovi</button>
                        </div>
                        <div class="widget-loco-admin-field">
                            <label for="widget-banner-page-<?php echo esc_attr($page_index); ?>-name">Nome della configurazione</label>
                            <input id="widget-banner-page-<?php echo esc_attr($page_index); ?>-name" class="widget-banner-page-name" type="text" name="widget_loco_options[pages][<?php echo esc_attr($page_index); ?>][name]" value="<?php echo esc_attr($page['name']); ?>" required>
                        </div>
                        <div class="widget-loco-admin-field">
                            <label for="widget-banner-page-<?php echo esc_attr($page_index); ?>-slug">Identificativo shortcode</label>
                            <input id="widget-banner-page-<?php echo esc_attr($page_index); ?>-slug" class="widget-banner-page-slug" type="text" name="widget_loco_options[pages][<?php echo esc_attr($page_index); ?>][slug]" value="<?php echo esc_attr($page_id); ?>" pattern="[A-Za-z0-9_-]+" data-manual="true">
                            <p>Shortcode: <code class="widget-banner-page-shortcode">[widget_banner_<?php echo esc_html($page_id); ?>]</code></p>
                        </div>
                        <div class="widget-loco-admin-field">
                            <label for="widget-banner-page-<?php echo esc_attr($page_index); ?>-color">Colore principale</label>
                            <input id="widget-banner-page-<?php echo esc_attr($page_index); ?>-color" name="widget_loco_options[pages][<?php echo esc_attr($page_index); ?>][primary_color]" type="color" value="<?php echo esc_attr($page['primary_color']); ?>">
                        </div>
                        <div class="widget-loco-admin-field">
                            <label for="widget-banner-page-<?php echo esc_attr($page_index); ?>-property-id">Google Analytics 4 Property ID</label>
                            <input id="widget-banner-page-<?php echo esc_attr($page_index); ?>-property-id" name="widget_loco_options[pages][<?php echo esc_attr($page_index); ?>][property_id]" type="text" inputmode="numeric" pattern="[0-9]*" value="<?php echo esc_attr($page['property_id']); ?>" placeholder="Es. 381552632">
                            <p>Se non impostato, il contatore degli utenti medi mensili non sarà mostrato.</p>
                        </div>
                        <?php
                        $default_logo_url = WIDGET_LOCO_URL . 'public/logo-napoli.png';
                        $page_logo_url = $page['logo_id']
                            ? wp_get_attachment_image_url($page['logo_id'], 'medium')
                            : false;
                        ?>
                        <div class="widget-loco-admin-field widget-banner-logo-field">
                            <span class="widget-banner-logo-label">Logo di questa configurazione</span>
                            <img class="widget-banner-logo-preview" src="<?php echo esc_url($page_logo_url ?: $default_logo_url); ?>" data-default-logo="<?php echo esc_url($default_logo_url); ?>" alt="Anteprima logo">
                            <input type="hidden" class="widget-banner-logo-id" name="widget_loco_options[pages][<?php echo esc_attr($page_index); ?>][logo_id]" value="<?php echo esc_attr((string) $page['logo_id']); ?>">
                            <button type="button" class="button widget-banner-select-logo">Scegli logo</button>
                            <button type="button" class="button-link-delete widget-banner-remove-logo" <?php disabled(!$page['logo_id']); ?>>Ripristina logo predefinito</button>
                        </div>
                        <div class="widget-loco-admin-field">
                            <label for="widget-banner-page-<?php echo esc_attr($page_index); ?>-subtitle">Paragrafo introduttivo</label>
                            <textarea id="widget-banner-page-<?php echo esc_attr($page_index); ?>-subtitle" name="widget_loco_options[pages][<?php echo esc_attr($page_index); ?>][hero_subtitle]" rows="3"><?php echo esc_textarea($page['hero_subtitle']); ?></textarea>
                        </div>
                        <div class="widget-loco-admin-field">
                            <label for="widget-banner-page-<?php echo esc_attr($page_index); ?>-message">Testo della sezione finale</label>
                            <textarea id="widget-banner-page-<?php echo esc_attr($page_index); ?>-message" name="widget_loco_options[pages][<?php echo esc_attr($page_index); ?>][final_message]" rows="2"><?php echo esc_textarea($page['final_message']); ?></textarea>
                        </div>
                        <h4>Posizionamenti e prezzi di questa configurazione</h4>
                        <?php
                        echo widget_loco_view('admin/views/formats-table.php', [
                            'formats' => $page['formats'],
                            'format_input_prefix' => 'widget_loco_options[pages][' . $page_index . '][formats]',
                            'table_id' => 'widget-banner-' . $page_index . '-formats',
                        ]);
                        ?>
                    </article>
                <?php endforeach; ?>
            </div>
            <p><button type="button" class="button" id="widget-banner-add-page">Aggiungi configurazione</button></p>
            <template id="widget-banner-page-template">
                <article class="widget-loco-admin-card widget-banner-page-card" data-profile-card>
                    <div class="widget-banner-page-heading">
                        <h3>Nuova configurazione</h3>
                        <button type="button" class="button-link-delete widget-banner-remove-page">Rimuovi</button>
                    </div>
                    <div class="widget-loco-admin-field">
                        <label for="widget-banner-page-__INDEX__-name">Nome della configurazione</label>
                        <input id="widget-banner-page-__INDEX__-name" class="widget-banner-page-name" type="text" name="widget_loco_options[pages][__INDEX__][name]" value="" required>
                    </div>
                    <div class="widget-loco-admin-field">
                        <label for="widget-banner-page-__INDEX__-slug">Identificativo shortcode</label>
                        <input id="widget-banner-page-__INDEX__-slug" class="widget-banner-page-slug" type="text" name="widget_loco_options[pages][__INDEX__][slug]" value="" pattern="[A-Za-z0-9_-]+">
                        <p>Shortcode: <code class="widget-banner-page-shortcode">[widget_banner_identificativo]</code></p>
                    </div>
                    <div class="widget-loco-admin-field">
                        <label for="widget-banner-page-__INDEX__-color">Colore principale</label>
                        <input id="widget-banner-page-__INDEX__-color" name="widget_loco_options[pages][__INDEX__][primary_color]" type="color" value="#2e6cab">
                    </div>
                    <div class="widget-loco-admin-field">
                        <label for="widget-banner-page-__INDEX__-property-id">Google Analytics 4 Property ID</label>
                        <input id="widget-banner-page-__INDEX__-property-id" name="widget_loco_options[pages][__INDEX__][property_id]" type="text" inputmode="numeric" pattern="[0-9]*" value="" placeholder="Es. 381552632">
                        <p>Se non impostato, il contatore degli utenti medi mensili non sarà mostrato.</p>
                    </div>
                    <div class="widget-loco-admin-field widget-banner-logo-field">
                        <span class="widget-banner-logo-label">Logo di questa configurazione</span>
                        <img class="widget-banner-logo-preview" src="<?php echo esc_url(WIDGET_LOCO_URL . 'public/logo-napoli.png'); ?>" data-default-logo="<?php echo esc_url(WIDGET_LOCO_URL . 'public/logo-napoli.png'); ?>" alt="Anteprima logo">
                        <input type="hidden" class="widget-banner-logo-id" name="widget_loco_options[pages][__INDEX__][logo_id]" value="0">
                        <button type="button" class="button widget-banner-select-logo">Scegli logo</button>
                        <button type="button" class="button-link-delete widget-banner-remove-logo" disabled>Ripristina logo predefinito</button>
                    </div>
                    <div class="widget-loco-admin-field">
                        <label for="widget-banner-page-__INDEX__-subtitle">Paragrafo introduttivo</label>
                        <textarea id="widget-banner-page-__INDEX__-subtitle" name="widget_loco_options[pages][__INDEX__][hero_subtitle]" rows="3"></textarea>
                    </div>
                    <div class="widget-loco-admin-field">
                        <label for="widget-banner-page-__INDEX__-message">Testo della sezione finale</label>
                        <textarea id="widget-banner-page-__INDEX__-message" name="widget_loco_options[pages][__INDEX__][final_message]" rows="2"></textarea>
                    </div>
                    <h4>Posizionamenti e prezzi di questa configurazione</h4>
                    <?php
                    echo widget_loco_view('admin/views/formats-table.php', [
                        'formats' => $default_formats,
                        'format_input_prefix' => 'widget_loco_options[pages][__INDEX__][formats]',
                        'table_id' => 'widget-banner-__INDEX__-formats',
                    ]);
                    ?>
                </article>
            </template>

            <?php submit_button('Salva modifiche'); ?>
        </form>
    </div>

    <div class="widget-loco-admin-card">
        <h2>Shortcode</h2>
        <p>Dopo aver salvato una configurazione, inserisci il suo shortcode nella pagina o nell’articolo WordPress in cui vuoi mostrarla.</p>
        <?php if (empty($options['pages'])) : ?>
            <p>Non hai ancora creato configurazioni.</p>
        <?php else : ?>
            <?php foreach ($options['pages'] as $page_id => $page) : ?>
                <p><?php echo esc_html($page['name']); ?>:
                    <code>[widget_banner_<?php echo esc_html($page_id); ?>]</code>
                    <code>[widget_banner id="<?php echo esc_attr($page_id); ?>"]</code>
                </p>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

</div>