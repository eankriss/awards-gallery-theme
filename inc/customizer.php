<?php
/**
 * Customizer settings for site-wide contact details (footer, CTA).
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Contact details with their defaults.
 */
function awards_gallery_contact_defaults()
{
    return [
        'ag_footer_text' => 'Three generations of Filipino craftsmanship. Premium awards custom-made for every milestone.',
        'ag_address'     => "A-2 A. Sandoval Avenue\nPalatiw, Pasig 1600\nMetro Manila",
        'ag_phone_1'     => '(02) 7587-2248',
        'ag_phone_2'     => '(0917) 637-5394',
        'ag_email'       => 'awardsgalleryph@gmail.com',
        'ag_copyright'   => 'Awards Gallery Philippines. All rights reserved.',
        'ag_facebook'    => '#',
        'ag_instagram'   => '#',
        'ag_twitter'     => '#',
        'ag_youtube'     => '#',
    ];
}

function awards_gallery_contact($key)
{
    $defaults = awards_gallery_contact_defaults();
    return get_theme_mod($key, $defaults[$key] ?? '');
}

function awards_gallery_customize_register($wp_customize)
{
    $wp_customize->add_section('awards_gallery_contact', [
        'title'    => __('Contact & Footer', 'awards-gallery-theme'),
        'priority' => 30,
    ]);

    $fields = [
        'ag_footer_text' => [__('Footer text', 'awards-gallery-theme'), 'textarea', 'sanitize_textarea_field'],
        'ag_address'     => [__('Address', 'awards-gallery-theme'), 'textarea', 'sanitize_textarea_field'],
        'ag_phone_1'     => [__('Phone 1', 'awards-gallery-theme'), 'text', 'sanitize_text_field'],
        'ag_phone_2'     => [__('Phone 2', 'awards-gallery-theme'), 'text', 'sanitize_text_field'],
        'ag_email'       => [__('Email', 'awards-gallery-theme'), 'email', 'sanitize_email'],
        'ag_copyright'   => [__('Copyright text', 'awards-gallery-theme'), 'text', 'sanitize_text_field'],
    ];

    $defaults = awards_gallery_contact_defaults();

    foreach ($fields as $id => [$label, $type, $sanitize]) {
        $wp_customize->add_setting($id, [
            'default'           => $defaults[$id],
            'sanitize_callback' => $sanitize,
        ]);
        $wp_customize->add_control($id, [
            'label'   => $label,
            'section' => 'awards_gallery_contact',
            'type'    => $type,
        ]);
    }

    // Social links repeater: each row is an uploaded icon, a label and a URL.
    require_once get_theme_file_path('/inc/class-social-links-control.php');

    $wp_customize->add_setting('ag_socials', [
        'default'           => wp_json_encode(awards_gallery_legacy_socials()),
        'sanitize_callback' => 'awards_gallery_sanitize_socials',
    ]);
    $wp_customize->add_control(new Awards_Gallery_Social_Links_Control($wp_customize, 'ag_socials', [
        'label'       => __('Social links', 'awards-gallery-theme'),
        'description' => __('Upload an icon for each link. Facebook, Instagram, X and YouTube use the built-in icon when none is uploaded. Rows with an empty URL are hidden.', 'awards-gallery-theme'),
        'section'     => 'awards_gallery_contact',
    ]));
}
add_action('customize_register', 'awards_gallery_customize_register');

add_action('customize_controls_enqueue_scripts', function () {
    $file = get_theme_file_path('/assets/js/customizer/social-links.js');
    wp_enqueue_media();
    wp_enqueue_script(
        'awards-gallery-social-links',
        get_theme_file_uri('/assets/js/customizer/social-links.js'),
        ['customize-controls', 'jquery'],
        filemtime($file),
        true
    );
});

/**
 * Built-in icons for the original networks, used when a row has no uploaded icon.
 */
function awards_gallery_social_svgs()
{
    return [
        'facebook'  => '<path d="M14 8h2V5h-2a3 3 0 0 0-3 3v2H9v3h2v6h3v-6h2l.5-3H14V8.5a.5.5 0 0 1 .5-.5z"/>',
        'instagram' => '<rect x="5" y="5" width="14" height="14" rx="4"/><circle cx="12" cy="12" r="3.2"/><circle cx="16.3" cy="7.7" r=".6" fill="currentColor"/>',
        'x'         => '<path d="M5.5 5.5h3.6l9.4 13h-3.6z" stroke-linejoin="round"/><path d="M18.3 5.5l-5.5 6.1M11.2 13.4l-5.5 5.1" stroke-linecap="round"/>',
        'youtube'   => '<rect x="3.5" y="6.5" width="17" height="11" rx="3.5"/><path d="M10.5 9.8v4.4l3.8-2.2z" fill="currentColor"/>',
    ];
}

/**
 * The three fixed Facebook / Instagram / X URL fields the repeater replaced, as repeater rows.
 */
function awards_gallery_legacy_socials()
{
    return [
        ['icon' => 0, 'label' => 'Facebook', 'url' => awards_gallery_contact('ag_facebook')],
        ['icon' => 0, 'label' => 'Instagram', 'url' => awards_gallery_contact('ag_instagram')],
        ['icon' => 0, 'label' => 'X', 'url' => awards_gallery_contact('ag_twitter')],
        ['icon' => 0, 'label' => 'YouTube', 'url' => awards_gallery_contact('ag_youtube')],
    ];
}

function awards_gallery_sanitize_socials($value)
{
    $rows = json_decode((string) $value, true);
    if (!is_array($rows)) {
        return '[]';
    }
    $clean = [];
    foreach ($rows as $row) {
        if (!is_array($row)) {
            continue;
        }
        $clean[] = [
            'icon'  => absint($row['icon'] ?? 0),
            'label' => sanitize_text_field($row['label'] ?? ''),
            'url'   => esc_url_raw($row['url'] ?? ''),
        ];
    }
    return wp_json_encode($clean);
}

/**
 * Footer social links: rows with a URL, each with an uploaded icon URL or a built-in SVG.
 */
function awards_gallery_social_links()
{
    $rows = json_decode((string) get_theme_mod('ag_socials', ''), true);
    if (!is_array($rows)) {
        $rows = awards_gallery_legacy_socials();
    }

    $svgs  = awards_gallery_social_svgs();
    $links = [];
    foreach ($rows as $row) {
        if (empty($row['url'])) {
            continue;
        }
        $label = $row['label'] ?? '';
        $key   = strtolower($label) === 'twitter' ? 'x' : strtolower($label);
        $links[] = [
            'label' => $label,
            'url'   => $row['url'],
            'image' => !empty($row['icon']) ? wp_get_attachment_image_url($row['icon'], 'thumbnail') : '',
            'svg'   => $svgs[$key] ?? '',
        ];
    }
    return $links;
}

/**
 * "tel:" href from a display phone number, e.g. (02) 7587-2248 → tel:+63275872248.
 */
function awards_gallery_tel_href($phone)
{
    $digits = preg_replace('/\D+/', '', $phone);
    if (strpos($digits, '0') === 0) {
        $digits = '63' . substr($digits, 1);
    }
    return 'tel:+' . $digits;
}
