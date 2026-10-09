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
        'ag_copyright'   => '© 2026 Awards Gallery Philippines. All rights reserved.',
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

        // Contact icons: an upload control right under its field. Empty uses the built-in icon.
        [$icon, $icon_label] = awards_gallery_contact_icon_fields()[$id] ?? ['', ''];
        if ($icon) {
            $wp_customize->add_setting($icon, [
                'default'           => 0,
                'sanitize_callback' => 'absint',
            ]);
            $wp_customize->add_control(new WP_Customize_Media_Control($wp_customize, $icon, [
                'label'       => $icon_label,
                'description' => __('Optional. No icon is shown when empty.', 'awards-gallery-theme'),
                'section'     => 'awards_gallery_contact',
                'mime_type'   => 'image',
            ]));
        }
    }

    // Social links repeater: each row is an uploaded icon, a label and a URL.
    require_once get_theme_file_path('/inc/class-social-links-control.php');

    $wp_customize->add_setting('ag_socials', [
        'default'           => '[]',
        'sanitize_callback' => 'awards_gallery_sanitize_socials',
    ]);
    $wp_customize->add_control(new Awards_Gallery_Social_Links_Control($wp_customize, 'ag_socials', [
        'label'       => __('Social links', 'awards-gallery-theme'),
        'description' => __('Upload an icon for each link. Rows without an icon or URL are hidden.', 'awards-gallery-theme'),
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
 * Contact field → its icon setting. The phone icon sits under Phone 2 since one icon covers both numbers.
 */
function awards_gallery_contact_icon_fields()
{
    return [
        'ag_address' => ['ag_icon_address', __('Address icon', 'awards-gallery-theme')],
        'ag_phone_2' => ['ag_icon_phone', __('Phone icon', 'awards-gallery-theme')],
        'ag_email'   => ['ag_icon_email', __('Email icon', 'awards-gallery-theme')],
    ];
}

/**
 * An uploaded icon. SVGs are drawn as a CSS mask filled with currentColor, so they take the
 * text colour (and its hover colour) like the old inline icons, whatever colour the file uses.
 * Other images (PNG, JPG…) keep their own colours.
 */
function awards_gallery_uploaded_icon($id, $class)
{
    $id  = absint($id);
    $url = $id ? (wp_get_attachment_image_url($id, 'thumbnail') ?: wp_get_attachment_url($id)) : '';
    if (!$url) {
        return '';
    }
    if (get_post_mime_type($id) === 'image/svg+xml') {
        $mask = 'url(' . esc_url($url) . ') center / contain no-repeat';
        return '<span class="' . esc_attr($class) . '" style="background-color: currentColor; -webkit-mask: ' . $mask . '; mask: ' . $mask . ';" aria-hidden="true"></span>';
    }
    return '<img src="' . esc_url($url) . '" alt="" class="' . esc_attr($class) . ' object-contain" loading="lazy">';
}

/**
 * Footer contact icon: the uploaded icon in gold, or nothing when none is set.
 */
function awards_gallery_footer_contact_icon($key)
{
    return awards_gallery_uploaded_icon(get_theme_mod('ag_icon_' . $key, 0), 'h-3.5 w-3.5 shrink-0 text-gold sm:mt-1.5');
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
 * Footer social links: rows with both a URL and an uploaded icon (markup ready to print).
 */
function awards_gallery_social_links()
{
    $rows  = json_decode((string) get_theme_mod('ag_socials', '[]'), true);
    $links = [];
    foreach (is_array($rows) ? $rows : [] as $row) {
        $icon = awards_gallery_uploaded_icon($row['icon'] ?? 0, 'h-4 w-4');
        if (empty($row['url']) || !$icon) {
            continue;
        }
        $links[] = [
            'label' => $row['label'] ?? '',
            'url'   => $row['url'],
            'icon'  => $icon,
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
