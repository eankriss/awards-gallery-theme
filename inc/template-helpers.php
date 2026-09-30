<?php
/**
 * Shared rendering helpers used by block render.php files and templates.
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Resolve an image value to a usable URL.
 * Full URLs pass through; bare filenames resolve against /assets/img.
 */
function awards_gallery_img_src($value)
{
    if (!$value) {
        return '';
    }
    if (preg_match('#^https?://#', $value) || strpos($value, '/') === 0) {
        return $value;
    }
    return get_theme_file_uri('/assets/img/' . ltrim($value, '/'));
}
