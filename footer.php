<?php
if (!defined('ABSPATH')) {
    exit;
}
?>
</main>

<footer class="site-footer border-t border-black/10">
    <div class="container-shell flex flex-col items-center justify-between gap-4 py-8 text-sm text-muted sm:flex-row">
        <?php
        wp_nav_menu([
            'theme_location' => 'footer',
            'container'      => false,
            'menu_class'     => 'flex gap-5',
            'fallback_cb'    => false,
            'depth'          => 1,
        ]);
        ?>
        <p>&copy; <?php echo esc_html(gmdate('Y')); ?> <?php bloginfo('name'); ?></p>
    </div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
