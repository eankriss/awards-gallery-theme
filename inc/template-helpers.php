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

/**
 * Image URL for a block attribute pair: an image picked in the editor
 * (stored as { id, url } under $media_key) wins over the bundled default
 * filename stored under $fallback_key.
 */
function awards_gallery_media_url($data, $media_key = 'image', $fallback_key = 'imageUrl')
{
    if (!empty($data[$media_key]['url'])) {
        return $data[$media_key]['url'];
    }
    return awards_gallery_img_src($data[$fallback_key] ?? '');
}

/**
 * Logo URL: the WordPress Custom Logo when set, otherwise the bundled logo.
 */
function awards_gallery_logo_url()
{
    $logo_id = get_theme_mod('custom_logo');
    if ($logo_id) {
        $url = wp_get_attachment_image_url($logo_id, 'full');
        if ($url) {
            return $url;
        }
    }
    return get_theme_file_uri('/assets/img/logo.png');
}

/**
 * Centered section eyebrow with thin gold rules either side.
 */
function awards_gallery_eyebrow($text, $tone = 'text-gold', $rule = 'via-gold/70')
{
    if (!$text) {
        return '';
    }
    return sprintf(
        '<p class="flex items-center justify-center gap-5 text-xs uppercase tracking-eyebrow sm:text-[15px] %1$s"><span class="h-px w-12 bg-gradient-to-r from-transparent %2$s to-transparent"></span>%3$s<span class="h-px w-12 bg-gradient-to-r from-transparent %2$s to-transparent"></span></p>',
        esc_attr($tone),
        esc_attr($rule),
        esc_html($text)
    );
}

/**
 * Embed URL for a YouTube or Vimeo link, or '' for anything else.
 * Autoplaying embeds are muted and looped so browsers allow them to start.
 */
function awards_gallery_video_embed_url($url, $autoplay = true, $controls = false)
{
    if (preg_match('~(?:youtube\.com/(?:watch\?v=|embed/|shorts/)|youtu\.be/)([\w-]{11})~', $url, $m)) {
        return add_query_arg([
            'autoplay'       => $autoplay ? 1 : 0,
            'mute'           => $autoplay ? 1 : 0,
            'loop'           => $autoplay ? 1 : 0,
            'playlist'       => $m[1],
            'controls'       => $controls ? 1 : 0,
            'rel'            => 0,
            'modestbranding' => 1,
            'playsinline'    => 1,
        ], 'https://www.youtube-nocookie.com/embed/' . $m[1]);
    }

    if (preg_match('~vimeo\.com/(?:video/)?(\d+)~', $url, $m)) {
        return add_query_arg([
            'autoplay'   => $autoplay ? 1 : 0,
            'muted'      => $autoplay ? 1 : 0,
            'loop'       => $autoplay ? 1 : 0,
            'background' => ($autoplay && !$controls) ? 1 : 0,
        ], 'https://player.vimeo.com/video/' . $m[1]);
    }

    return '';
}
