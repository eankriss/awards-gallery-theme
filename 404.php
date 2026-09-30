<?php
if (!defined('ABSPATH')) {
    exit;
}

get_header();
?>

<div class="container-shell py-20 text-center">
    <h1 class="font-display text-5xl font-bold">404</h1>
    <p class="mt-4 text-muted"><?php esc_html_e('The page you are looking for could not be found.', 'awards-gallery-theme'); ?></p>
    <a class="mt-8 inline-block rounded-lg bg-ink px-6 py-3 font-semibold text-paper" href="<?php echo esc_url(home_url('/')); ?>">
        <?php esc_html_e('Back to Home', 'awards-gallery-theme'); ?>
    </a>
</div>

<?php
get_footer();
