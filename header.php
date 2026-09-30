<?php
if (!defined('ABSPATH')) {
    exit;
}
?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<header class="site-header border-b border-black/10">
    <div class="container-shell flex items-center justify-between py-5">
        <div class="site-branding">
            <?php if (has_custom_logo()) : ?>
                <?php the_custom_logo(); ?>
            <?php else : ?>
                <a class="font-display text-xl font-bold" href="<?php echo esc_url(home_url('/')); ?>"><?php bloginfo('name'); ?></a>
            <?php endif; ?>
        </div>

        <nav class="primary-nav" aria-label="<?php esc_attr_e('Primary', 'awards-gallery-theme'); ?>">
            <?php
            wp_nav_menu([
                'theme_location' => 'primary',
                'container'      => false,
                'menu_class'     => 'flex gap-6',
                'fallback_cb'    => false,
            ]);
            ?>
        </nav>
    </div>
</header>

<main id="main" class="site-main">
