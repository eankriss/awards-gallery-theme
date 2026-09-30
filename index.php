<?php
if (!defined('ABSPATH')) {
    exit;
}

get_header();
?>

<div class="container-shell py-10">
    <?php if (have_posts()) : ?>
        <div class="grid gap-8 sm:grid-cols-2 lg:grid-cols-3">
            <?php
            while (have_posts()) :
                the_post();
                get_template_part('template-parts/content');
            endwhile;
            ?>
        </div>
        <div class="mt-10"><?php the_posts_pagination(); ?></div>
    <?php else : ?>
        <p><?php esc_html_e('Nothing found.', 'awards-gallery-theme'); ?></p>
    <?php endif; ?>
</div>

<?php
get_footer();
