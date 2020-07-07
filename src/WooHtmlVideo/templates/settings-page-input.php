<?php
/**
 * Template to create the HTML code for the inputs in the settings page of the plugin.
 */
?>
<input
    name="<?php printf('%s[%s]', $settings_field, $field['name']) ?>"
    type="<?= $field['type'] ?>"
    <?php foreach ($field['attrs'] as $k => $v) printf('%s="%s" ', $k, $v); ?>
>
<?php
// add the description if present
if (!empty($field['description'])): ?>
<p class="description"><?= $field['description'] ?></p>
<?php endif; ?>