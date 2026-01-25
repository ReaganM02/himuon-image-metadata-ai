<?php

namespace Himuon\Image\MetaData;

use Himuon\Image\MetaData\Admin\Media;
use Himuon\Image\MetaData\Admin\SettingsPage;

// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

class Init
{
    public static function load()
    {
        $settingsPage = new SettingsPage();
        $settingsPage->register();

        $media = new Media();
        $media->register();
    }
}