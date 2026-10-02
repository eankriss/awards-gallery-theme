<?php
/**
 * Awards Gallery Post Collection — helpers.
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Estimated reading time in minutes (≈200 words a minute, at least 1).
 */
function awards_gallery_read_time($post)
{
    $words = str_word_count(wp_strip_all_tags(strip_shortcodes(get_post_field('post_content', $post))));
    return max(1, (int) ceil($words / 200));
}

/**
 * Label shown on a card: the post's first term in its first public hierarchical
 * taxonomy (Category for posts), skipping the featured term and "Uncategorized".
 */
function awards_gallery_post_label($post, $skip_slug = '')
{
    foreach (get_object_taxonomies(get_post_type($post), 'objects') as $taxonomy) {
        if (!$taxonomy->public || !$taxonomy->hierarchical) {
            continue;
        }
        $terms = get_the_terms($post, $taxonomy->name);
        if (!$terms || is_wp_error($terms)) {
            continue;
        }
        foreach ($terms as $term) {
            if ($term->slug !== $skip_slug && $term->slug !== 'uncategorized') {
                return $term->name;
            }
        }
    }
    return '';
}

/**
 * Category names for a post: terms from its public hierarchical taxonomies
 * (Categories for posts), skipping "Uncategorized", up to $limit.
 */
function awards_gallery_post_categories($post, $limit = 3)
{
    $names = [];
    foreach (get_object_taxonomies(get_post_type($post), 'objects') as $taxonomy) {
        if (!$taxonomy->public || !$taxonomy->hierarchical) {
            continue;
        }
        foreach ((array) get_the_terms($post, $taxonomy->name) as $term) {
            if ($term instanceof WP_Term && $term->slug !== 'uncategorized') {
                $names[] = $term->name;
            }
        }
    }
    return array_slice($names, 0, $limit);
}

/**
 * Card excerpt: the manual excerpt, or the content trimmed to ~28 words.
 */
function awards_gallery_post_excerpt($post, $words = 28)
{
    $text = has_excerpt($post) ? get_the_excerpt($post) : get_post_field('post_content', $post);
    return wp_trim_words(wp_strip_all_tags(strip_shortcodes($text)), $words);
}

/**
 * The "Featured" tag only drives the block layout; its archive page is thin,
 * duplicate content, so keep it out of search results and the XML sitemap.
 * Filter `awards_gallery_featured_tag` if the block uses a different slug.
 */
function awards_gallery_featured_slug()
{
    return apply_filters('awards_gallery_featured_tag', 'featured');
}

add_filter('wp_robots', function ($robots) {
    if (is_tag(awards_gallery_featured_slug())) {
        $robots['noindex'] = true;
        $robots['follow']  = true;
    }
    return $robots;
});

add_filter('wp_sitemaps_taxonomies_query_args', function ($args, $taxonomy) {
    $term = $taxonomy === 'post_tag' ? get_term_by('slug', awards_gallery_featured_slug(), 'post_tag') : null;
    if ($term) {
        $args['exclude'] = array_merge((array) ($args['exclude'] ?? []), [$term->term_id]);
    }
    return $args;
}, 10, 2);
