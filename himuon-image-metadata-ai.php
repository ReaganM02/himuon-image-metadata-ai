<?php

use Himuon\Image\MetaData\Init;
/**
 * Plugin Name:       Himuon Image Metadata AI
 * Description:       A robust add-on that seamlessly connects Gravity Forms with Klaviyo, allowing form submissions to be converted into Klaviyo profiles and automatically subscribed to specific Klaviyo lists.
 * Version:           1.0.0
 * Author:            Reagan Mahinay
 * Author URI:        https://reagandev.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       himuon-image-metadata-ai
 * Requires at least: 6.9
 * Requires PHP: 7.4
 * Domain Path:       /languages
 * Tested up to: 6.9
 * @package himuon-image-metadata-ai
 */

define('HIMUON_IMAGE_METADATA_AI_VERSION', '1.0.0');
define('HIMUON_IMAGE_METADATA_AI_PATH', plugin_dir_path(__FILE__));
define('HIMUON_IMAGE_METADATA_AI_URL', plugin_dir_url(__FILE__));


$autoload = HIMUON_IMAGE_METADATA_AI_PATH . 'vendor/autoload.php';

require_once $autoload;

add_action('plugins_loaded', [Init::class, 'load']);