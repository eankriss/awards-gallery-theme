<?php
/**
 * Navigation: menu data (Appearance → Menus) and the header nav renderers.
 *
 * Menus are turned into a plain tree of links so the desktop header, the
 * mobile drawer and the footer can each render it their own way:
 *
 *   [ 'title', 'url', 'target', 'active', 'ancestor', 'children' => [ … ] ]
 *
 * `active`   — this item is the current page.
 * `ancestor` — one of its sub-items is the current page.
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Links for a menu location as a tree. Falls back to $fallback when no menu
 * is assigned (or it is empty).
 *
 * Filter `awards_gallery_menu_links` to change the links for a location.
 */
function awards_gallery_menu_links($location, $fallback = [])
{
    $links     = $fallback;
    $locations = get_nav_menu_locations();
    $items     = !empty($locations[$location]) ? wp_get_nav_menu_items($locations[$location]) : [];

    if ($items) {
        _wp_menu_item_classes_by_context($items);

        $nodes = [];
        foreach ($items as $item) {
            $classes = (array) $item->classes;
            $nodes[$item->ID] = [
                'title'    => $item->title,
                'url'      => $item->url,
                'target'   => $item->target,
                // On a blog post / Single Product page, the menu item pointing at the blog / products listing is the current section.
                'active'   => in_array('current-menu-item', $classes, true)
                    || (is_singular('post') && untrailingslashit($item->url) === untrailingslashit(awards_gallery_blog_url()))
                    || (is_page_template('page-templates/single-product.php') && untrailingslashit($item->url) === untrailingslashit(awards_gallery_products_url())),
                'ancestor' => (bool) array_intersect(['current-menu-ancestor', 'current-menu-parent'], $classes),
                'children' => [],
                'parent'   => (int) $item->menu_item_parent,
            ];
        }

        // Attach children to parents by reference so nesting of any depth works.
        $links = [];
        foreach ($nodes as $id => &$node) {
            if ($node['parent'] && isset($nodes[$node['parent']])) {
                $nodes[$node['parent']]['children'][] = &$node;
            } else {
                $links[] = &$node;
            }
        }
        unset($node);
    }

    return apply_filters('awards_gallery_menu_links', $links, $location);
}

/**
 * Default header/footer links used until a menu is assigned.
 */
function awards_gallery_default_links()
{
    $link = fn($title, $url, $active = false) => [
        'title'    => $title,
        'url'      => $url,
        'target'   => '',
        'active'   => $active,
        'ancestor' => false,
        'children' => [],
    ];

    return [
        $link(__('Home', 'awards-gallery-theme'), home_url('/'), is_front_page()),
        $link(__('Products', 'awards-gallery-theme'), '#', is_page_template('page-templates/single-product.php')),
        $link(__('Contact us', 'awards-gallery-theme'), '#'),
        $link(__('About us', 'awards-gallery-theme'), '#'),
        $link(__('Blog', 'awards-gallery-theme'), '#'),
    ];
}

/**
 * `href`, `target`, `rel` and `aria-current` attributes for a link.
 */
function awards_gallery_link_attrs($link)
{
    $attrs = ' href="' . esc_url($link['url']) . '"';
    if (!empty($link['target'])) {
        $attrs .= ' target="' . esc_attr($link['target']) . '"';
        if ($link['target'] === '_blank') {
            $attrs .= ' rel="noopener"';
        }
    }
    if (!empty($link['active'])) {
        $attrs .= ' aria-current="page"';
    }
    return $attrs;
}

function awards_gallery_chevron($class)
{
    return '<svg class="' . esc_attr($class) . '" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true"><path d="m6 9 6 6 6-6"/></svg>';
}

/**
 * Desktop header nav. Items with sub-items open a dropdown on hover and on
 * keyboard focus; a third level is listed indented inside the dropdown.
 */
function awards_gallery_desktop_nav($links)
{
    foreach ($links as $link) {
        $current = $link['active'] || $link['ancestor'];
        $classes = 'group/link text-[13px] uppercase tracking-[0.1em] transition-colors hover:text-gold xl:text-[15px] xl:tracking-[0.15em] '
            . ($current ? 'text-gold' : 'text-cream');
        // Underline drawn as a background on an inline <span> (so it skips the chevron and
        // its padding doesn't shift layout): full width when current, grows from the centre on hover/focus.
        $label = '<span class="bg-[linear-gradient(currentColor,currentColor)] bg-[position:50%_100%] bg-no-repeat pb-2 transition-[background-size] duration-300 ease-out group-hover/link:bg-[length:100%_1px] group-focus-visible/link:bg-[length:100%_1px] '
            . ($current ? 'bg-[length:100%_1px]' : 'bg-[length:0%_1px]') . '">'
            . esc_html($link['title']) . '</span>';

        if (!$link['children']) {
            printf('<a%s class="%s">%s</a>', awards_gallery_link_attrs($link), esc_attr($classes), $label); // phpcs:ignore -- $label escaped above
            continue;
        }
        ?>
        <div class="group relative flex h-20 items-center transition-[height] duration-300 group-[.is-scrolled]/header:h-16">
            <a<?php echo awards_gallery_link_attrs($link); // phpcs:ignore ?> class="<?php echo esc_attr($classes); ?> inline-flex items-center gap-1.5" aria-haspopup="true">
                <?php echo $label; // phpcs:ignore -- escaped above ?>
                <?php echo awards_gallery_chevron('h-3.5 w-3.5 transition-transform duration-300 group-hover:rotate-180 group-focus-within:rotate-180'); // phpcs:ignore ?>
            </a>
            <div class="invisible absolute left-1/2 top-full z-50 -translate-x-1/2 translate-y-2 opacity-0 transition duration-200 group-focus-within:visible group-focus-within:translate-y-0 group-focus-within:opacity-100 group-hover:visible group-hover:translate-y-0 group-hover:opacity-100">
                <ul class="min-w-[240px] border border-gold/40 border-t-gold bg-ink-800 py-3 shadow-[0_24px_40px_-20px_rgba(0,0,0,0.9)]">
                    <?php awards_gallery_dropdown_items($link['children']); ?>
                </ul>
            </div>
        </div>
        <?php
    }
}

/**
 * Dropdown rows for the desktop nav (recursive, indenting deeper levels).
 */
function awards_gallery_dropdown_items($links, $depth = 0)
{
    foreach ($links as $link) {
        $current = $link['active'] || $link['ancestor'];
        $classes = $depth
            ? 'block py-2 pl-10 pr-6 text-sm text-cream/75 transition-colors hover:bg-gold/10 hover:text-gold'
            : 'block px-6 py-2.5 text-[13px] uppercase tracking-[0.15em] transition-colors hover:bg-gold/10 hover:text-gold';
        $classes .= $current ? ' text-gold' : ($depth ? '' : ' text-cream');
        ?>
        <li>
            <a<?php echo awards_gallery_link_attrs($link); // phpcs:ignore ?> class="<?php echo esc_attr($classes); ?>"><?php echo esc_html($link['title']); ?></a>
            <?php if ($link['children']) : ?>
                <ul><?php awards_gallery_dropdown_items($link['children'], $depth + 1); ?></ul>
            <?php endif; ?>
        </li>
        <?php
    }
}

/**
 * Mobile / tablet drawer nav. Items with sub-items get a toggle button that
 * expands them in place (accordion); the branch holding the current page
 * starts open.
 */
function awards_gallery_drawer_nav($links, $depth = 0)
{
    static $uid = 0;

    foreach ($links as $link) {
        $current = $link['active'] || $link['ancestor'];
        $sizes   = ['text-lg uppercase tracking-[0.2em]', 'text-[15px] uppercase tracking-[0.15em]', 'text-[15px] tracking-[0.1em]'];
        $colors  = ['text-cream', 'text-cream/80', 'text-cream/60'];
        $level   = min($depth, 2);
        $classes = $sizes[$level] . ' ' . ($current ? 'text-gold' : $colors[$level]) . ' transition-colors hover:text-gold';
        if ($link['active'] && !$depth) {
            $classes .= ' underline decoration-1 underline-offset-[10px]';
        }
        ?>
        <li class="flex flex-col items-center">
            <?php if ($link['children']) : $id = 'ag-submenu-' . (++$uid); ?>
                <div class="flex items-center gap-1 pl-10">
                    <a<?php echo awards_gallery_link_attrs($link); // phpcs:ignore ?> class="<?php echo esc_attr($classes); ?>"><?php echo esc_html($link['title']); ?></a>
                    <button type="button" class="flex h-10 w-10 items-center justify-center text-cream/80 transition-colors hover:text-gold" aria-expanded="<?php echo $link['ancestor'] ? 'true' : 'false'; ?>" aria-controls="<?php echo esc_attr($id); ?>" data-submenu-toggle>
                        <span class="sr-only"><?php echo esc_html(sprintf(__('Show %s sub-menu', 'awards-gallery-theme'), $link['title'])); ?></span>
                        <?php echo awards_gallery_chevron('h-4 w-4 transition-transform duration-300' . ($link['ancestor'] ? ' rotate-180' : '')); // phpcs:ignore ?>
                    </button>
                </div>
                <ul id="<?php echo esc_attr($id); ?>" class="mt-4 flex flex-col items-center gap-4<?php echo $link['ancestor'] ? '' : ' hidden'; ?>">
                    <?php awards_gallery_drawer_nav($link['children'], $depth + 1); ?>
                </ul>
            <?php else : ?>
                <a<?php echo awards_gallery_link_attrs($link); // phpcs:ignore ?> class="<?php echo esc_attr($classes); ?>"><?php echo esc_html($link['title']); ?></a>
            <?php endif; ?>
        </li>
        <?php
    }
}
