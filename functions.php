<?php

if (!defined('ABSPATH')) {
    exit;
}

define('AWARDS_GALLERY_VERSION', '1.0.0');

/* ============================================================
   Advanced Custom Fields (bundled)
   Loads ACF from /acf inside the theme unless the ACF plugin is
   already active. Skipped until the /acf folder is added.
   ============================================================ */
add_action('after_setup_theme', function () {
    $acf = get_stylesheet_directory() . '/acf/acf.php';
    if (!class_exists('ACF') && file_exists($acf)) {
        include_once $acf;
    }
});

add_filter('acf/settings/path', function ($path) {
    return get_stylesheet_directory() . '/acf/';
});

add_filter('acf/settings/dir', function ($dir) {
    return get_stylesheet_directory_uri() . '/acf/';
});

/* ============================================================
   Theme support
   ============================================================ */
function awards_gallery_setup()
{
    load_theme_textdomain('awards-gallery-theme', get_template_directory() . '/languages');

    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('editor-styles');
    add_theme_support('wp-block-styles');
    add_theme_support('align-wide');
    add_theme_support('custom-logo');
    add_theme_support('responsive-embeds');
    add_theme_support('html5', ['gallery', 'caption', 'style', 'script']);

    register_nav_menus([
        'primary' => __('Primary Menu', 'awards-gallery-theme'),
        'footer'  => __('Footer Menu', 'awards-gallery-theme'),
    ]);
}
add_action('after_setup_theme', 'awards_gallery_setup');

/* ============================================================
   Front-end assets
   ============================================================ */
function awards_gallery_fonts_url()
{
    return 'https://fonts.googleapis.com/css2?family=Great+Vibes&family=Outfit:wght@400&family=Pinyon+Script&family=Rubik:ital,wght@0,300;0,400;0,500;0,700;1,400&display=swap';
}

function awards_gallery_enqueue_assets()
{
    $critical_css = get_theme_file_path('/assets/css/critical.min.css');
    $main_css     = get_theme_file_path('/assets/css/main.min.css');
    $main_js      = get_theme_file_path('/assets/js/awards-gallery.js');

    wp_enqueue_style('awards-gallery-fonts', awards_gallery_fonts_url(), [], null);

    if (file_exists($critical_css)) {
        wp_enqueue_style(
            'awards-gallery-critical',
            get_theme_file_uri('/assets/css/critical.min.css'),
            [],
            filemtime($critical_css)
        );
    }

    if (file_exists($main_css)) {
        wp_enqueue_style(
            'awards-gallery-main',
            get_theme_file_uri('/assets/css/main.min.css'),
            ['awards-gallery-fonts'],
            filemtime($main_css)
        );
    }

    wp_enqueue_style(
        'awards-gallery-style',
        get_stylesheet_uri(),
        [],
        filemtime(get_theme_file_path('/style.css'))
    );

    if (file_exists($main_js)) {
        wp_enqueue_script(
            'awards-gallery-main',
            get_theme_file_uri('/assets/js/awards-gallery.js'),
            [],
            filemtime($main_js),
            true
        );
    }
}
add_action('wp_enqueue_scripts', 'awards_gallery_enqueue_assets');

// Single Product page template: thumbnail → main image switching.
function awards_gallery_enqueue_product_gallery()
{
    $file = get_theme_file_path('/assets/js/product-gallery.js');
    if (is_page_template('page-templates/single-product.php') && file_exists($file)) {
        wp_enqueue_script('awards-gallery-product', get_theme_file_uri('/assets/js/product-gallery.js'), [], filemtime($file), true);
    }
}
add_action('wp_enqueue_scripts', 'awards_gallery_enqueue_product_gallery');

function awards_gallery_preconnect_fonts()
{
    echo '<link rel="preconnect" href="https://fonts.googleapis.com">' . "\n";
    echo '<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>' . "\n";
}
add_action('wp_head', 'awards_gallery_preconnect_fonts', 1);

/* ============================================================
   Editor assets — preview blocks with real fonts + utilities
   ============================================================ */
function awards_gallery_editor_assets()
{
    if (file_exists(get_theme_file_path('/assets/css/main.min.css'))) {
        add_editor_style('assets/css/main.min.css');
    }
    add_editor_style('assets/css/editor.css');
}
add_action('after_setup_theme', 'awards_gallery_editor_assets');

function awards_gallery_editor_fonts()
{
    wp_enqueue_style('awards-gallery-fonts', awards_gallery_fonts_url(), [], null);
}
add_action('enqueue_block_editor_assets', 'awards_gallery_editor_fonts');

/* ============================================================
   Auto-register dynamic blocks in /assets/js/blocks
   ============================================================ */
function awards_gallery_register_blocks()
{
    $blocks_dir = get_theme_file_path('/assets/js/blocks');

    if (!is_dir($blocks_dir)) {
        return;
    }

    foreach (scandir($blocks_dir) as $folder) {
        if ($folder === '.' || $folder === '..') {
            continue;
        }

        $block_path = $blocks_dir . '/' . $folder;

        if (is_dir($block_path) && file_exists($block_path . '/block.json')) {
            // A block may ship its own PHP (shared templates, AJAX handlers).
            if (file_exists($block_path . '/functions-helpers.php')) {
                require_once $block_path . '/functions-helpers.php';
            }

            register_block_type($block_path);
        }
    }
}
add_action('init', 'awards_gallery_register_blocks');

/* ============================================================
   Site search is not used — front-end ?s= requests get the 404 page.
   (WP Admin search is unaffected.)
   ============================================================ */
function awards_gallery_disable_search($query)
{
    if (!is_admin() && $query->is_main_query() && $query->is_search()) {
        $query->is_search = false;
        $query->query_vars['s'] = false;
        $query->query['s'] = false;
        $query->set_404();
        status_header(404);
        nocache_headers();
    }
}
add_action('parse_query', 'awards_gallery_disable_search');
add_filter('get_search_form', '__return_empty_string');

/* ============================================================
   Allow SVG uploads (Media Library)
   ============================================================ */
function awards_gallery_mime_types($mimes)
{
    // SVGs can carry scripts, so only Administrators may upload them.
    if (current_user_can('manage_options')) {
        $mimes['svg'] = 'image/svg+xml';
    }
    return $mimes;
}
add_filter('upload_mimes', 'awards_gallery_mime_types');

// SVGs have no intrinsic size, so give their Media Library thumbnails a width.
function awards_gallery_fix_svg_admin()
{
    echo '<style>
        .attachment-266x266, .thumbnail img {
            width: 100% !important;
            height: auto !important;
        }
    </style>';
}
add_action('admin_head', 'awards_gallery_fix_svg_admin');

/* ============================================================
   Theme modules
   ============================================================ */
require_once get_theme_file_path('/inc/template-helpers.php');
require_once get_theme_file_path('/inc/navigation.php');
require_once get_theme_file_path('/inc/customizer.php');
require_once get_theme_file_path('/inc/homepage.php');

// Contact Form 7: the form templates use their own <div> layout (styled in main.css),
// so stop CF7 from wrapping lines in <p>/<br>.
add_filter('wpcf7_autop_or_not', '__return_false');
