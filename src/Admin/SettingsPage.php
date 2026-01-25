<?php

namespace Himuon\Image\MetaData\Admin;

use Himuon\Image\MetaData\Api\ApiClientTrait;

// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

final class SettingsPage
{
    use ApiClientTrait;
    public const PAGE_SLUG = 'himuon-image-metadata-ai';
    public const OPTION_NAME = 'himuon_image_meta_data';

    protected $hookSuffix = '';

    protected $apiKeyValue = '';

    public function register()
    {
        add_action('admin_menu', [$this, 'addPage']);
        add_action('admin_enqueue_scripts', [$this, 'enqueueAdminScripts']);

        add_action('wp_ajax_himuon_image_metadata__save_settings', [$this, 'saveSettings']);
    }

    public function saveSettings()
    {
        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => 'Permission denied.'], 403);
        }

        check_ajax_referer('himuon_image_metadata__save_settings');

        $apiKey = isset($_POST['apiKey']) ? trim(wp_unslash($_POST['apiKey'])) : '';

        $apiKey = sanitize_text_field($apiKey);

        if ($apiKey === '') {
            wp_send_json_error([
                'class' => 'himuon-image-metadata--message-error',
                'message' => __('API key is required.')
            ]);
        }
        $this->setApiKey($apiKey);

        $validate = $this->validateApiKey();
        if (is_wp_error($validate)) {
            wp_send_json_error([
                'class' => 'himuon-image-metadata--message-error',
                'message' => $validate->get_error_message()
            ]);
        }
        update_option(self::OPTION_NAME, $apiKey);

        return wp_send_json_success([
            'class' => 'himuon-image-metadata--message-success',
            'message' => __('API key is valid.', 'himuon-image-metadata-ai')
        ]);


    }

    public function addPage()
    {
        $this->hookSuffix = add_submenu_page(
            'options-general.php',
            __('Image Metadata AI', 'himuon-image-metadata-ai'),
            __('Image Metadata AI', 'himuon-image-metadata-ai'),
            'manage_options',
            self::PAGE_SLUG,
            [$this, 'renderPage']
        );
    }

    public function enqueueAdminScripts(string $hookSuffix)
    {
        if ($hookSuffix !== $this->hookSuffix) {
            return;
        }

        wp_enqueue_style(
            'himuon-image-metadata-ai',
            HIMUON_IMAGE_METADATA_AI_URL . 'assets/css/himuon-image-metadata-admin.css',
            [],
            HIMUON_IMAGE_METADATA_AI_VERSION
        );

        wp_enqueue_script(
            'himuon-image-metadata-ai',
            HIMUON_IMAGE_METADATA_AI_URL . 'assets/js/himuon-image-metadata-ai.js',
            [],
            HIMUON_IMAGE_METADATA_AI_VERSION,
            true
        );
        wp_localize_script(
            'himuon-image-metadata-ai',
            'HimuonImageMetadata',
            [
                'url' => admin_url('admin-ajax.php'),
            ]
        );
    }

    public function renderPage()
    {
        $apiKey = get_option(self::OPTION_NAME, '');
        require_once HIMUON_IMAGE_METADATA_AI_PATH . 'templates/settings-page.php';
    }

    public function setApiKey(string $apiKey)
    {
        $this->apiKeyValue = $apiKey;
    }

    protected function apiKey(): string
    {
        if ($this->apiKeyValue !== '') {
            return (string) $this->apiKeyValue;
        }

        return (string) get_option(self::OPTION_NAME, '');
    }

    protected function validateApiKey()
    {
        $result = $this->apiRequest('models', 'GET', []);
        return $result;
    }
}
