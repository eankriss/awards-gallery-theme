<?php
if (!defined('ABSPATH')) {
    exit;
}

$nav_links = awards_gallery_menu_links('primary', awards_gallery_default_links());
$logo_url  = awards_gallery_logo_url();
$site_name = get_bloginfo('name');
?>
<!doctype html>
<html <?php language_attributes(); ?> class="no-js">
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php wp_head(); ?>
</head>
<body <?php body_class('bg-ink font-sans text-cream'); ?>>
<?php wp_body_open(); ?>

<?php // Accessibility: first Tab stop, hidden until focused; jumps past the header to <main id="main">. ?>
<a href="#main" class="sr-only focus:not-sr-only focus:fixed focus:left-4 focus:top-4 focus:z-[100] focus:bg-gold focus:px-5 focus:py-3 focus:text-xs focus:uppercase focus:tracking-[0.2em] focus:text-ink focus:outline-none focus:ring-2 focus:ring-cream"><?php esc_html_e('Skip to content', 'awards-gallery-theme'); ?></a>

<?php // Sticky header: JS adds .is-scrolled after the first scroll → compact, translucent, shadowed. ?>
<header class="group/header fixed inset-x-0 top-0 z-50 border-b border-gold/40 bg-ink transition-[background-color,box-shadow] duration-300 [&.is-scrolled]:bg-ink/85 [&.is-scrolled]:shadow-[0_12px_30px_-12px_rgba(0,0,0,0.8)] [&.is-scrolled]:backdrop-blur-md" data-header>
    <div class="mx-auto flex h-20 max-w-shell items-center justify-between gap-8 px-5 transition-[height] duration-300 group-[.is-scrolled]/header:h-16 sm:px-8 lg:px-14">
        <a href="<?php echo esc_url(home_url('/')); ?>" class="shrink-0" aria-label="<?php echo esc_attr(sprintf(__('%s home', 'awards-gallery-theme'), $site_name)); ?>">
            <img src="<?php echo esc_url($logo_url); ?>" alt="<?php echo esc_attr($site_name); ?>" class="h-11 w-auto transition-[height] duration-300 group-[.is-scrolled]/header:h-9 lg:h-[50px] lg:group-[.is-scrolled]/header:h-10">
        </a>

        <?php // Link spacing/size step down on small desktops (1024–1279px) so longer menus still fit. ?>
        <nav class="hidden items-center gap-5 whitespace-nowrap lg:flex xl:gap-10" aria-label="<?php esc_attr_e('Primary', 'awards-gallery-theme'); ?>">
            <?php awards_gallery_desktop_nav($nav_links); ?>
        </nav>

        <button type="button" class="-mr-2 flex h-11 w-11 items-center justify-center text-cream lg:hidden" aria-label="<?php esc_attr_e('Open menu', 'awards-gallery-theme'); ?>" aria-expanded="false" data-menu-open>
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M3 7h18M3 12h18M3 17h18"/></svg>
        </button>
    </div>
</header>

<?php // Mobile / tablet drawer: full screen on phones, a side panel on tablets, hidden from lg up. ?>
<div class="pointer-events-none fixed inset-0 z-[60] lg:hidden" data-drawer aria-hidden="true">
    <div class="absolute inset-0 bg-ink/70 opacity-0 transition-opacity duration-300" data-drawer-scrim></div>
    <aside class="absolute right-0 top-0 flex h-full w-full translate-x-full flex-col bg-ink-800 transition-transform duration-300 ease-out sm:w-[420px]" data-drawer-panel role="dialog" aria-modal="true" aria-label="<?php esc_attr_e('Menu', 'awards-gallery-theme'); ?>">
        <?php // Logo centred in the bar; the close button sits absolutely on the right so it doesn't push it off-centre. ?>
        <div class="relative flex h-20 shrink-0 items-center justify-center border-b border-gold/40 px-16">
            <a href="<?php echo esc_url(home_url('/')); ?>" class="shrink-0" aria-label="<?php echo esc_attr(sprintf(__('%s home', 'awards-gallery-theme'), $site_name)); ?>">
                <img src="<?php echo esc_url($logo_url); ?>" alt="<?php echo esc_attr($site_name); ?>" class="h-11 w-auto">
            </a>
            <button type="button" class="absolute right-3 top-1/2 flex h-11 w-11 -translate-y-1/2 items-center justify-center text-cream transition-colors hover:text-gold sm:right-6" aria-label="<?php esc_attr_e('Close menu', 'awards-gallery-theme'); ?>" data-menu-close>
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M5 5l14 14M19 5L5 19"/></svg>
            </button>
        </div>

        <nav class="flex flex-1 flex-col overflow-y-auto px-8 pb-10 pt-12 text-center" aria-label="<?php esc_attr_e('Mobile', 'awards-gallery-theme'); ?>">
            <ul class="flex flex-col items-center gap-8">
                <?php awards_gallery_drawer_nav($nav_links); ?>
            </ul>
        </nav>

        <div class="shrink-0 px-5 pb-10 sm:px-8">
            <a href="<?php echo esc_url(awards_gallery_contact('ag_email') ? 'mailto:' . awards_gallery_contact('ag_email') : '#'); ?>" class="btn-gold w-full"><?php esc_html_e('Get a Quote', 'awards-gallery-theme'); ?></a>
        </div>
    </aside>
</div>

<main id="main" tabindex="-1" class="site-main focus:outline-none<?php echo awards_gallery_starts_with_hero() ? '' : ' pt-20'; ?>">
