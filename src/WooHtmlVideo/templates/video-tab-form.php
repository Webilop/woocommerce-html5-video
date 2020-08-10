<?php
/**
 * Template used to create the form to handle the video tab data.
 */
?>
<div class="wh5v-video-tab-form">
    <div class="row">
        <?php
        \woocommerce_wp_text_input([
            'id' => 'wh5v_name_field',
            'name' => sprintf('%s[%s]', $args['group'], 'name'),
            'value' => $args['data']['name'],
            'label' => __('Tab Name', 'wh5v'),
            'description' => __('Label displayed in the video tab in the product page.', 'wh5v')
        ]);
        ?>
    </div>
    <div class="row">
        <?php
        \woocommerce_wp_text_input([
            'id' => 'wh5v_position_field',
            'type' => 'number',
            'custom_attributes' => ['min' => 1],
            'name' => sprintf('%s[%s]', $args['group'], 'position'),
            'value' => $args['data']['position'],
            'label' => __('Tab Position', 'wh5v'),
            'description' => __('Order position of the video tab in the product page.', 'wh5v')
        ]);
        ?>
    </div>
    <?php if (!empty($args['data']['post_id'])): ?>
    <div class="row">
        <p class="form-field">
            <label><?= __('Videos', 'wh5v') ?></label>
            <!-- <a class="button" target="_blank" href="<?= get_edit_post_link($args['data']['post_id']) ?>"><?= __('Edit', 'wh5v') ?></a> -->
            <button class="button" id="wh5vOpenVideosBtn" data-product-url="<?= get_edit_post_link($args['data']['post_id']) ?>"><?= __('Edit', 'wh5v') ?></button>
            <span id="amountVideosTag"><?php printf(__('%d videos blocks'), $args['data']['videos']) ?></span>
            <div id="wh5vVideosDialog" data-postid="<?= $args['data']['post_id'] ?>">
                <iframe style="width:100%;height:100%;" src="<?= get_edit_post_link($args['data']['post_id']) ?>"></iframe>
            </div>
        </p>
    </div>
    <?php endif; ?>
</div>