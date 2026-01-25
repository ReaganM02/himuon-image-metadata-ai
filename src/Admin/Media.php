<?php

namespace Himuon\Image\MetaData\Admin;

use Himuon\Image\MetaData\Api\ApiClientTrait;
use Himuon\Image\MetaData\Admin\SettingsPage;

// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

final class Media
{
    use ApiClientTrait;
    private const ACTION = 'himuon_image_metadata__generate';

    private const NONCE = 'himuon_image_meta_data_generate';
    public function register()
    {
        add_action('admin_enqueue_scripts', [$this, 'adminEnqueueScripts']);
        add_action('wp_ajax_' . self::ACTION, [$this, 'generateMetaData']);
    }

    protected function apiKey(): string
    {
        error_log(print_r(SettingsPage::OPTION_NAME, true));
        return (string) get_option(SettingsPage::OPTION_NAME, '');
    }

    public function generateMetaData()
    {
        check_ajax_referer(self::NONCE);

        $imageID = isset($_POST['id']) ? absint($_POST['id']) : 0;

        if (!$imageID || get_post_type($imageID) !== 'attachment') {
            return wp_send_json_error(__('Invalid Image ID', 'himuon-image-metadata-ai'));
        }

        $imageURL = wp_get_attachment_image_url($imageID, 'medium');

        $payload = [
            'model' => 'gpt-5-nano-2025-08-07',
            'max_output_tokens' => 1000,
            'reasoning' => [
                'effort' => 'low'
            ],
            'input' => [
                [
                    'role' => 'user',
                    'content' => [
                        [
                            'type' => 'input_text',
                            'text' => 'Generate SEO-friendly WordPress metadata. Return JSON with: alt_text (50-125 chars, descriptive for accessibility), title (40-60 chars), description (150-300 chars), caption (100-150 chars).',
                        ],
                        [
                            'type' => 'input_image',
                            'image_url' => 'http://reagandev.com/wp-content/uploads/2025/11/wedding.png',
                        ],
                    ],
                ],
            ],
            'text' => [
                'format' => [
                    'type' => 'json_object'
                ]
            ]
        ];


        $response = $this->apiRequest('responses', 'POST', $payload);


        if (is_wp_error($response)) {
            error_log(print_r($response, true));
            wp_send_json_error(__('Failed to fetch metadata', 'himuon-image-metadata-ai'));
        }

        $output = json_decode($response['output'][1]['content'][0]['text'], true);

        wp_update_post([
            'ID' => $imageID,
            'post_title' => sanitize_text_field($output['title'] ?? ''),
            'post_excerpt' => sanitize_text_field($output['caption'] ?? ''),
            'post_content' => wp_kses_post($output['description'] ?? ''),
        ]);

        if (!empty($output['alt_text'])) {
            update_post_meta(
                $imageID,
                '_wp_attachment_image_alt',
                sanitize_text_field($output['alt_text'])
            );
        }

        wp_send_json_success($output);
    }
    public function adminEnqueueScripts(string $hook)
    {
        error_log($hook);
        if ($hook !== 'upload.php') {
            return;
        }
        wp_enqueue_script(
            'media-after-settings',
            HIMUON_IMAGE_METADATA_AI_URL . 'assets/js/himuon-image-metadata-media.js',
            ['media-views'],
            HIMUON_IMAGE_METADATA_AI_VERSION,
            true
        );
        wp_localize_script(
            'media-after-settings',
            'HimuonGenerateMetaData',
            [
                'url' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce(self::NONCE),
                'action' => self::ACTION
            ]
        );
        wp_enqueue_style(
            'himuon-image-metadata-media',
            HIMUON_IMAGE_METADATA_AI_URL . 'assets/css/himuon-image-metadata-media.css',
            [],
            HIMUON_IMAGE_METADATA_AI_VERSION
        );
    }
}