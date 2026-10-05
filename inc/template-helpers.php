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
 * With $sound the player accepts postMessage commands so a button can unmute it.
 * With $on_click the player is injected after a click, so it starts at once with sound.
 */
function awards_gallery_video_embed_url($url, $autoplay = true, $controls = false, $sound = false, $on_click = false)
{
    $start = $autoplay || $on_click;

    if ($id = awards_gallery_youtube_id($url)) {
        return add_query_arg([
            'autoplay'       => $start ? 1 : 0,
            'mute'           => $autoplay ? 1 : 0,
            'loop'           => $autoplay ? 1 : 0,
            'playlist'       => $id,
            'controls'       => $controls ? 1 : 0,
            'rel'            => 0,
            'modestbranding' => 1,
            'playsinline'    => 1,
            'enablejsapi'    => $sound ? 1 : 0,
        ], 'https://www.youtube-nocookie.com/embed/' . $id);
    }

    if (preg_match('~vimeo\.com/(?:video/)?(\d+)~', $url, $m)) {
        // Vimeo's background mode locks the audio off, so hide the UI piece by piece instead.
        $chromeless = $autoplay && !$controls;
        return add_query_arg([
            'autoplay'   => $start ? 1 : 0,
            'muted'      => $autoplay ? 1 : 0,
            'loop'       => $autoplay ? 1 : 0,
            'background' => ($chromeless && !$sound) ? 1 : 0,
            'controls'   => ($chromeless && $sound) ? 0 : 1,
        ], 'https://player.vimeo.com/video/' . $m[1]);
    }

    return '';
}

/**
 * YouTube video ID from a watch / share / embed / shorts link, or ''.
 */
function awards_gallery_youtube_id($url)
{
    return preg_match('~(?:youtube\.com/(?:watch\?v=|embed/|shorts/)|youtu\.be/)([\w-]{11})~', $url, $m) ? $m[1] : '';
}

/**
 * Whether the current page should start flush under the fixed header: the
 * homepage, or any single page/post whose first block is a hero. Those heroes
 * pad their own top to clear the header, so <main> skips its pt-20 offset and
 * the hero image fills the space the header uncovers as it shrinks and grows.
 */
function awards_gallery_starts_with_hero()
{
    if (is_front_page()) {
        return true;
    }
    if (!is_singular()) {
        return false;
    }
    // Blog posts (single-post.php) and Single Product pages always open with their own hero.
    if (is_singular('post') || is_page_template('page-templates/single-product.php')) {
        return true;
    }

    // parse_blocks() returns whitespace between blocks as nameless entries; skip them.
    foreach (parse_blocks(get_post_field('post_content', get_queried_object_id())) as $block) {
        if (!empty($block['blockName'])) {
            return in_array($block['blockName'], ['awards-gallery/hero', 'awards-gallery/page-hero'], true);
        }
    }
    return false;
}

/**
 * The category to show for a post (badge, breadcrumb): its first category,
 * skipping "Uncategorized" and the Featured category, which only drives layout.
 */
function awards_gallery_primary_category($post_id)
{
    $skip = ['uncategorized', function_exists('awards_gallery_featured_slug') ? awards_gallery_featured_slug() : 'featured'];
    foreach (get_the_category($post_id) as $term) {
        if (!in_array($term->slug, $skip, true)) {
            return $term;
        }
    }
    return null;
}

/**
 * Products listing URL: the page with the "products" slug, else /products/.
 */
function awards_gallery_products_url()
{
    $page = get_page_by_path('products');
    return $page ? get_permalink($page) : home_url('/products/');
}

/**
 * Blog listing URL: the Settings → Reading "Posts page" when set, else /blog/.
 */
function awards_gallery_blog_url()
{
    $page = (int) get_option('page_for_posts');
    return $page ? get_permalink($page) : home_url('/blog/');
}
