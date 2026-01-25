<?php
// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}
?>
<div class="himuon-image-metadata-wrapper">
    <h1 class="himuon-image-metadata-title">
        <?php echo esc_html__('Image Metadata AI Settings', 'himuon-image-metadata-ai'); ?>
    </h1>
    <form class="himuon-image-metadata--form">
        <div>
            <label for="api-key"
                   class="himuon-image-metadata-label"><?php echo esc_html__('API Key', 'himuon-image-metadata-ai') ?></label>
            <input class="himuon-image-metadata-input-ui"
                   type="password"
                   id="api-key"
                   name="apiKey"
                   value="<?php echo esc_attr($apiKey); ?>" />
        </div>
        <div class="himuon-image-metadata--message">
        </div>
        <div class="himuon-image-metadata--action">
            <button type="submit"
                    class="button button-primary"
                    data-label=" <?php echo esc_attr__('Save Settings', 'himuon-image-metadata-ai') ?>"
                    data-label-loading=" <?php echo esc_attr__('Loading...', 'himuon-image-metadata-ai') ?>">
                <?php echo esc_html__('Save Settings', 'himuon-image-metadata-ai') ?>
            </button>
            <input type="hidden"
                   name="action"
                   value="himuon_image_metadata__save_settings" />
            <?php echo wp_nonce_field('himuon_image_metadata__save_settings') ?>
        </div>
    </form>
</div>