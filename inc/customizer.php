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
        'ag_facebook'    => [__('Facebook URL (empty hides it)', 'awards-gallery-theme'), 'url', 'esc_url_raw'],
        'ag_instagram'   => [__('Instagram URL (empty hides it)', 'awards-gallery-theme'), 'url', 'esc_url_raw'],
        'ag_twitter'     => [__('X / Twitter URL (empty hides it)', 'awards-gallery-theme'), 'url', 'esc_url_raw'],
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
}
add_action('customize_register', 'awards_gallery_customize_register');

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
