<?php

namespace Himuon\Image\MetaData\Api;

use WP_Error;

// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

trait ApiClientTrait
{
    protected function apiRequest(string $endpoint, $method = 'GET' | 'POST', array $payload = [], array $headers = [])
    {
        $baseURL = "https://api.openai.com/v1/$endpoint";
        $args = [
            'timeout' => 20,
            'method' => $method,
            'headers' => array_merge([
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $this->apiKey(),
            ], $headers),
        ];

        if ($method !== 'GET' && !empty($payload)) {
            $args['body'] = wp_json_encode($payload);
        }

        $response = wp_remote_request($baseURL, $args);

        if (is_wp_error($response)) {
            return new WP_Error(
                'api_error',
                'OpenAI error' . $response->get_error_message(),
                ['status' => $response->get_error_code()]
            );
        }

        $status = wp_remote_retrieve_response_code($response);
        $body = json_decode(wp_remote_retrieve_body($response), true);

        if ($status < 200 || $status >= 300) {
            return new WP_Error(
                'api_response_error',
                $body['error']['message'] ?? __('API request failed.', 'himuon-image-metadata-ai')
            );
        }
        return (array) $body;
    }

    abstract protected function apiKey(): string;
}