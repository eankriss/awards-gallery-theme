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
 * Card excerpt: the manual excerpt, or the content trimmed to ~28 words.
 */
function awards_gallery_post_excerpt($post, $words = 28)
{
    $text = has_excerpt($post) ? get_the_excerpt($post) : get_post_field('post_content', $post);
    return wp_trim_words(wp_strip_all_tags(strip_shortcodes($text)), $words);
}
