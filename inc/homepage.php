<?php
/**
 * Homepage composition.
 *
 * The homepage is a stack of Awards Gallery blocks. The same block markup is
 * used three ways: (1) registered as an editor pattern, (2) seeded into a
 * "Home" page set as the static front page, and (3) as the front-page.php
 * fallback when the site shows latest posts instead of a static front page.
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * The ordered homepage block markup (block-comment serialised).
 */
function awards_gallery_homepage_blocks()
{
    return implode("\n", [
        '<!-- wp:awards-gallery/hero /-->',
        '<!-- wp:awards-gallery/marquee /-->',
        '<!-- wp:awards-gallery/collection /-->',
        '<!-- wp:awards-gallery/stats /-->',
        '<!-- wp:awards-gallery/video /-->',
        '<!-- wp:awards-gallery/process /-->',
        '<!-- wp:awards-gallery/craft /-->',
        '<!-- wp:awards-gallery/testimonials /-->',
        '<!-- wp:awards-gallery/cta /-->',
    ]);
}

/**
 * Register the homepage pattern so editors can insert/edit the full layout.
 */
add_action('init', function () {
    if (!function_exists('register_block_pattern')) {
        return;
    }
    register_block_pattern('awards-gallery/homepage', [
        'title'      => __('Awards Gallery Homepage', 'awards-gallery-theme'),
        'categories' => ['featured'],
        'content'    => awards_gallery_homepage_blocks(),
    ]);
});

/**
 * On first run, make sure a "Home" page holds the homepage blocks and is set
 * as the static front page. An existing but empty "Home" page is filled in;
 * one that already has content is left untouched. Guarded by an option so it
 * only happens once.
 */
function awards_gallery_create_home_page()
{
    if (get_option('awards_gallery_home_seeded')) {
        return;
    }

    $existing = get_page_by_path('home');
    if (!$existing) {
        $home_id = wp_insert_post([
            'post_type'    => 'page',
            'post_status'  => 'publish',
            'post_title'   => 'Home',
            'post_name'    => 'home',
            'post_content' => awards_gallery_homepage_blocks(),
        ]);
    } else {
        $home_id = $existing->ID;
        if (!trim($existing->post_content)) {
            wp_update_post([
                'ID'           => $home_id,
                'post_content' => awards_gallery_homepage_blocks(),
            ]);
        }
    }

    if ($home_id && !is_wp_error($home_id)) {
        update_option('show_on_front', 'page');
        update_option('page_on_front', $home_id);
    }

    update_option('awards_gallery_home_seeded', AWARDS_GALLERY_VERSION);
}
add_action('admin_init', 'awards_gallery_create_home_page');

/**
 * One-time migration: the Video block was added to the design after the Home
 * page was seeded. Insert it right after the Stats block on the front page,
 * unless the page already has one or no longer has a Stats block.
 */
function awards_gallery_add_video_block()
{
    if (get_option('awards_gallery_video_added')) {
        return;
    }
    update_option('awards_gallery_video_added', AWARDS_GALLERY_VERSION);

    $home = get_post((int) get_option('page_on_front'));
    if (!$home || strpos($home->post_content, 'wp:awards-gallery/video') !== false) {
        return;
    }

    $content = preg_replace(
        '~(<!-- wp:awards-gallery/stats(?: \{.*?\})? /-->)~s',
        "$1\n<!-- wp:awards-gallery/video /-->",
        $home->post_content,
        1,
        $count
    );

    if ($count) {
        wp_update_post(['ID' => $home->ID, 'post_content' => $content]);
    }
}
add_action('admin_init', 'awards_gallery_add_video_block', 20);
