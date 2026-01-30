# Himuon Image Metadata AI

Generate SEO-friendly image metadata in WordPress using OpenAI and apply it directly to media attachments.

## Overview
This plugin adds a settings page to store your OpenAI API key and a Media Library button that generates image metadata (alt text, title, description, caption) for the selected attachment.

## Features
- Settings page under WordPress Settings for storing an API key
- "Generate Metadata" button in the Media Library attachment details
- Updates attachment title, caption, description, and alt text in one action

## Requirements
- WordPress 6.9+
- PHP 7.4+
- OpenAI API key
- Composer (required to install the production-ready autoloader)

## Installation
### Development install
1. Copy the plugin folder into `wp-content/plugins/`.
2. From the plugin directory, install Composer dependencies:

```bash
composer install
```

### Production install (recommended)
Use Composer in production mode to generate an optimized autoloader:

```bash
composer install --no-dev --optimize-autoloader --classmap-authoritative
```

3. Activate the plugin in the WordPress admin.

## Configuration
1. Go to Settings -> Image Metadata AI.
2. Enter your OpenAI API key and save.

## Usage
1. Open Media -> Library.
2. Click an image attachment.
3. Click the "Generate Metadata" button.
4. The plugin will populate the following fields:
   - Alt text
   - Title
   - Caption
   - Description

## How it works
- The settings page validates the API key by requesting the OpenAI models endpoint.
- The Media Library button triggers an AJAX request that calls the OpenAI Responses API.
- The response is parsed as JSON and written to the attachment fields and alt text.

## File structure
- `himuon-image-metadata-ai.php` plugin bootstrap and autoload
- `src/Init.php` registers admin components
- `src/Admin/SettingsPage.php` settings UI and API key storage
- `src/Admin/Media.php` Media Library integration and metadata generation
- `src/Api/ApiClientTrait.php` OpenAI request helper
- `assets/js/` admin UI scripts
- `assets/css/` admin styles
- `templates/` settings page templates

## Known limitations
- The request payload currently uses a hardcoded image URL instead of the selected attachment URL.

## Troubleshooting
- If you see "API key is required" or "API key is valid" messages not appearing, check browser console and confirm `admin-ajax.php` is reachable.
- If metadata does not update, verify the API key and confirm the selected item is a valid attachment.

## License
GPL-2.0+
