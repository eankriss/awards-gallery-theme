<?php
/**
 * Contact icons shared by the Contact Info and Visit Us blocks.
 *
 * Solid 24×24 glyphs (fill = currentColor), matching the footer's contact icons.
 * Keys match the icon options in contact-info/edit.js and visit-us/edit.js.
 */

if (!defined('ABSPATH')) {
    exit;
}

function awards_gallery_contact_icon($name, $class = 'h-4 w-4')
{
    $icons = [
        'pin'     => '<path d="M12 22s7-6.5 7-12.5a7 7 0 1 0-14 0C5 15.5 12 22 12 22zm0-10a2.5 2.5 0 1 1 0-5 2.5 2.5 0 0 1 0 5z"/>',
        'phone'   => '<path d="M6.6 10.8a15 15 0 0 0 6.6 6.6l2.2-2.2c.3-.3.7-.4 1-.2 1.1.4 2.3.6 3.6.6.6 0 1 .4 1 1V20c0 .6-.4 1-1 1A17 17 0 0 1 3 4c0-.6.4-1 1-1h3.5c.6 0 1 .4 1 1 0 1.3.2 2.5.6 3.6.1.3 0 .7-.2 1z"/>',
        'mail'    => '<path d="M3 5h18a1 1 0 0 1 1 1v12a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1V6a1 1 0 0 1 1-1zm9 7.2L4.2 7H4v.9l8 5.3 8-5.3V7h-.2L12 12.2z"/>',
        'message' => '<path d="M4 3h16a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H8l-5 4V5a2 2 0 0 1 2-2zm3 6v2h2V9H7zm4 0v2h2V9h-2zm4 0v2h2V9h-2z"/>',
        'clock'   => '<path d="M12 2a10 10 0 1 1 0 20 10 10 0 0 1 0-20zm1 5h-2v6l5 3 1-1.7-4-2.4V7z"/>',
    ];

    if (empty($icons[$name])) {
        $name = 'pin';
    }

    return '<svg class="' . esc_attr($class) . '" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">' . $icons[$name] . '</svg>';
}
