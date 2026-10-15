<?php
/**
 * Product category archive (taxonomy "product-category"): sort options, main query setup and assets.
 * Used by taxonomy-product-category.php.
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Sort choices for the category page dropdown (query var "sort").
 */
function awards_gallery_award_sort_options()
{
    return [
        'recommended' => __('Recommended', 'awards-gallery-theme'),
        'newest'      => __('Newest', 'awards-gallery-theme'),
        'name-asc'    => __('Name: A–Z', 'awards-gallery-theme'),
        'name-desc'   => __('Name: Z–A', 'awards-gallery-theme'),
    ];
}

/**
 * The sort key from ?sort=, falling back to "recommended".
 */
function awards_gallery_current_sort()
{
    if (isset($_POST['sort'])) {
        $sort = sanitize_key(wp_unslash($_POST['sort']));
        return array_key_exists($sort, awards_gallery_award_sort_options()) ? $sort : 'recommended';
    }
    $sort = isset($_GET['sort']) ? sanitize_key(wp_unslash($_GET['sort'])) : '';
    return array_key_exists($sort, awards_gallery_award_sort_options()) ? $sort : 'recommended';
}

function awards_gallery_current_subcategory()
{
    if (isset($_POST['subcategory'])) {
        return sanitize_text_field(wp_unslash($_POST['subcategory']));
    }
    return isset($_GET['subcategory']) ? sanitize_text_field(wp_unslash($_GET['subcategory'])) : '';
}

/**
 * Category page query: products only, 18 per page, ordered by the chosen sort.
 * "Recommended" follows the editor's manual order (Order field), then newest first.
 */
add_action('pre_get_posts', function ($query) {
    if (is_admin() || !$query->is_main_query() || !$query->is_tax('product-category')) {
        return;
    }

    $query->set('post_type', 'product-page');
    // Eighteen is the standard archive batch. The front end temporarily limits
    // the first row set to 15 or 16 only at the narrower desktop breakpoints.
    $query->set('posts_per_page', 18);

    if ($subcategory = awards_gallery_current_subcategory()) {
        $query->set('meta_query', [[
            'key' => 'awards_product_subcategory', 'value' => $subcategory, 'compare' => '=',
        ]]);
    }

    switch (awards_gallery_current_sort()) {
        case 'newest':
            $query->set('orderby', 'date');
            $query->set('order', 'DESC');
            break;
        case 'name-asc':
            $query->set('orderby', 'title');
            $query->set('order', 'ASC');
            break;
        case 'name-desc':
            $query->set('orderby', 'title');
            $query->set('order', 'DESC');
            break;
        default:
            $query->set('orderby', ['menu_order' => 'ASC', 'date' => 'DESC']);
    }
});

/**
 * Sort auto-submit and "Load More".
 */
add_action('wp_enqueue_scripts', function () {
    $file = get_theme_file_path('/assets/js/category-load-more.js');
    if (is_tax('product-category') && file_exists($file)) {
        wp_enqueue_script('awards-gallery-category', get_theme_file_uri('/assets/js/category-load-more.js'), [], filemtime($file), true);
    }
});
