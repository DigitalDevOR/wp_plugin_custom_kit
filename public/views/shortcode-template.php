<?php

if (!defined('ABSPATH')) {
    exit;
}

$instance_id = wp_unique_id('widget-banner-');
$format_categories = [
    'standard' => 'Standard',
    'impact' => 'Impact',
    'video' => 'Video e audio',
    'native' => 'Native',
    'sponsor' => 'Sponsorship',
];

?>

<div
    id="<?php echo esc_attr($instance_id); ?>"
    class="widget-banner-page"
    data-widget-banner
    data-radio-name="<?php echo esc_attr($options['name']); ?>"
    data-formats="<?php echo esc_attr($formats_json); ?>"
    style="--blue: <?php echo esc_attr($options['primary_color']); ?>; --blue-rgb: <?php echo esc_attr($primary_color_rgb); ?>;"
>

    <header class="hero">
        <div class="wrap">
            <div class="topbar">
                <a class="mark" href="#<?php echo esc_attr($instance_id); ?>-formati" aria-label="<?php echo esc_attr($options['name']); ?>">
                    <img src="<?php echo esc_url($logo_url); ?>" alt="<?php echo esc_attr($options['name']); ?>">
                </a>
                <nav aria-label="Sezioni">
                    <a href="#<?php echo esc_attr($instance_id); ?>-formati">Formati</a>
                    <a href="#<?php echo esc_attr($instance_id); ?>-listino">Listino</a>
                    <a href="#<?php echo esc_attr($instance_id); ?>-contatti">Contatti</a>
                </nav>
                <?php if ($media_mensile_utenti_totale !== null) : ?>
                    <div class="stats-badge">
                        <span class="num"><?php echo esc_html(number_format_i18n($media_mensile_utenti_totale, 0)); ?></span>
                        <span class="lbl">Utenti medi mensili</span>
                    </div>
                <?php elseif (current_user_can('manage_options')) : ?>
                    <p class="widget-banner-ga-warning">
                        <?php if ($options['property_id'] === '') : ?>
                            Contatore Analytics non configurato: inserisci il Property ID nelle impostazioni della configurazione.
                        <?php else : ?>
                            Contatore Analytics non disponibile.
                            <?php if ($google_analytics_error !== null) : ?>
                                <?php echo esc_html($google_analytics_error); ?>
                            <?php else : ?>
                                Verifica i log PHP/WordPress per i dettagli.
                            <?php endif; ?>
                        <?php endif; ?>
                    </p>
                <?php endif; ?>
            </div>

            <div class="freq">Media kit digitale · sito · mobile · app</div>
            <h1>Scegli dove<br>si <span>vede</span> il tuo brand</h1>
            <p><?php echo nl2br(esc_html($options['hero_subtitle'])); ?></p>
        </div>
    </header>

    <main class="wrap" id="<?php echo esc_attr($instance_id); ?>-formati">
        <div class="controls">
            <div class="devices" role="tablist" aria-label="Dispositivo"></div>
            <div class="filters"></div>
        </div>

        <div class="sec-head">
            <h2 class="section-title"></h2>
            <p class="section-subtitle">Sito <?php echo esc_html($options['name']); ?> da computer</p>
        </div>
        <div class="grid" aria-live="polite">
            <?php foreach ($options['formats'] as $format) : ?>
                <?php if ($format['dev'] !== 'desktop') { continue; } ?>
                <article class="card">
                    <div class="pv">
                        <div class="server-preview" aria-hidden="true">ADV</div>
                    </div>
                    <div class="card-top">
                        <h3><?php echo esc_html($format['name']); ?></h3>
                        <span class="tag"><?php echo esc_html($format_categories[$format['cat']] ?? $format['cat']); ?></span>
                    </div>
                    <dl class="spec">
                        <div><dt>Posizionamento</dt><dd><?php echo esc_html($format['place']); ?></dd></div>
                        <div><dt>Comportamento</dt><dd><?php echo esc_html($format['fx']); ?></dd></div>
                        <div><dt>Dimensioni</dt><dd class="mono"><?php echo esc_html($format['dims']); ?></dd></div>
                    </dl>
                    <div class="price">
                        <div>
                            <div class="model"><?php echo esc_html($format['model']); ?></div>
                            <?php if ($format['price'] === null) : ?>
                                <span class="val empty">€ —</span>
                            <?php else : ?>
                                <span class="val"><?php echo esc_html(number_format_i18n((float) $format['price'], 2)); ?>&nbsp;€</span>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="actions">
                        <button class="btn" type="button" data-demo="<?php echo esc_attr($format['id']); ?>">Vedi demo</button>
                        <button class="link" type="button" data-sheet="<?php echo esc_attr($format['id']); ?>">Scheda tecnica</button>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>

        <section id="<?php echo esc_attr($instance_id); ?>-listino">
            <div class="sec-head">
                <h2>Listino <em>per posizionamento</em></h2>
                <p>Prezzi da compilare. I formati impact e le sponsorship hanno di norma un costo diverso dallo standard: qui li vedi tutti affiancati.</p>
            </div>
            <div class="tbl-wrap">
                <table class="price-list">
                    <thead>
                        <tr><th>Formato</th><th>Categoria</th><th>Dimensioni</th><th>Modello</th><th>Prezzo</th></tr>
                    </thead>
                    <tbody>
                        <?php foreach (['desktop' => 'Desktop', 'mobile' => 'Mobile'] as $device => $device_label) : ?>
                            <tr><td colspan="5" class="dev-label"><?php echo esc_html($device_label); ?></td></tr>
                            <?php foreach ($options['formats'] as $format) : ?>
                                <?php if ($format['dev'] !== $device) { continue; } ?>
                                <tr>
                                    <td><strong><?php echo esc_html($format['name']); ?></strong></td>
                                    <td><?php echo esc_html($format_categories[$format['cat']] ?? $format['cat']); ?></td>
                                    <td class="num"><?php echo esc_html($format['size']); ?></td>
                                    <td><?php echo esc_html($format['model']); ?></td>
                                    <td class="num">
                                        <?php if ($format['price'] === null) : ?>
                                            <span class="pill">Da definire</span>
                                        <?php else : ?>
                                            <?php echo esc_html(number_format_i18n((float) $format['price'], 2)); ?>&nbsp;€
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </section>

        <section class="contact" id="<?php echo esc_attr($instance_id); ?>-contatti">
            <div>
                <h2><?php echo nl2br(esc_html($options['final_message'])); ?></h2>
                <p>Raccontaci obiettivo, periodo e budget: ti proponiamo il mix di formati su sito e app.</p>
                <div class="addr">Contatto commerciale: da inserire</div>
            </div>
            <div class="steps">
                <div class="step"><i>1</i>Scegli i formati</div>
                <div class="step"><i>2</i>Richiedi il preventivo</div>
                <div class="step"><i>3</i>Invia le creatività</div>
            </div>
        </section>

        <footer>Dimensioni in pixel secondo i formati standard IAB. Formati, comportamenti e prezzi sono una proposta di lavoro e non un listino ufficiale.</footer>
    </main>

    <dialog class="format-dialog" aria-label="Dettagli formato">
        <div class="dlg-head">
            <div><small class="dialog-device"></small><h3 class="dialog-title"></h3></div>
            <button class="x" type="button" aria-label="Chiudi">×</button>
        </div>
        <div class="dlg-tabs" role="tablist">
            <button class="tab-demo" type="button" role="tab" aria-selected="true">Demo</button>
            <button class="tab-sheet" type="button" role="tab" aria-selected="false">Scheda tecnica</button>
        </div>
        <div class="dlg-body">
            <div class="panel-demo"><div class="demo-stage"></div><p class="demo-note"></p></div>
            <div class="panel-sheet" hidden><div class="tbl-wrap"><table class="sheet"></table></div></div>
        </div>
    </dialog>
</div>
