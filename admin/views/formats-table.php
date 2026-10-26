<?php

if (!defined('ABSPATH')) {
    exit;
}

$format_fields = [
    'name' => 'Nome formato',
    'size' => 'Formato',
    'place' => 'Posizionamento',
    'fx' => 'Comportamento',
    'dims' => 'Dimensioni tecniche',
    'model' => 'Modello prezzo',
    'price' => 'Prezzo (€)',
    'file_formats' => 'Formati file accettati',
    'max_weight' => 'Peso massimo',
    'third_party_tracking' => 'Tracciamento terze parti',
    'creative_delivery' => 'Consegna creatività',
];
$device_labels = [
    'desktop' => 'Desktop',
    'mobile' => 'Mobile',
];
$category_labels = [
    'standard' => 'Standard',
    'impact' => 'Impact',
    'video' => 'Video e audio',
    'native' => 'Native',
    'sponsor' => 'Sponsorship',
];
?>
<div class="widget-banner-formats-table-wrap">
    <table id="<?php echo esc_attr($table_id); ?>" class="widefat striped widget-banner-formats-table">
        <thead>
            <tr>
                <th scope="col">Riga</th>
                <th scope="col">Dispositivo</th>
                <th scope="col">Categoria</th>
                <?php foreach ($format_fields as $label) : ?>
                    <th scope="col"><?php echo esc_html($label); ?></th>
                <?php endforeach; ?>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($formats as $format_id => $format) : ?>
                <tr>
                    <th scope="row">
                        <strong><?php echo esc_html($format['name']); ?></strong><br>
                        <code><?php echo esc_html($format_id); ?></code>
                    </th>
                    <td>
                        <select name="<?php echo esc_attr($format_input_prefix . '[' . $format_id . '][dev]'); ?>">
                            <?php foreach ($device_labels as $value => $label) : ?>
                                <option value="<?php echo esc_attr($value); ?>" <?php selected($format['dev'], $value); ?>>
                                    <?php echo esc_html($label); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                    <td>
                        <select name="<?php echo esc_attr($format_input_prefix . '[' . $format_id . '][cat]'); ?>">
                            <?php foreach ($category_labels as $value => $label) : ?>
                                <option value="<?php echo esc_attr($value); ?>" <?php selected($format['cat'], $value); ?>>
                                    <?php echo esc_html($label); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                    <?php foreach ($format_fields as $field => $label) : ?>
                        <?php $field_id = $table_id . '-' . $format_id . '-' . $field; ?>
                        <td>
                            <label class="screen-reader-text" for="<?php echo esc_attr($field_id); ?>">
                                <?php echo esc_html($format['name'] . ' — ' . $label); ?>
                            </label>
                            <?php if ($field === 'price') : ?>
                                <input
                                    id="<?php echo esc_attr($field_id); ?>"
                                    type="number"
                                    min="0"
                                    step="0.01"
                                    name="<?php echo esc_attr($format_input_prefix . '[' . $format_id . '][price]'); ?>"
                                    value="<?php echo $format['price'] === null ? '' : esc_attr((string) $format['price']); ?>"
                                    placeholder="Da definire"
                                />
                            <?php else : ?>
                                <textarea
                                    id="<?php echo esc_attr($field_id); ?>"
                                    name="<?php echo esc_attr($format_input_prefix . '[' . $format_id . '][' . $field . ']'); ?>"
                                    rows="2"
                                ><?php echo esc_textarea($format[$field]); ?></textarea>
                            <?php endif; ?>
                        </td>
                    <?php endforeach; ?>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
