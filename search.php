<?php
if (!defined('ABSPATH')) {
    exit;
}

get_header();
?>

<div class="container-shell py-10">
    <h1 class="font-display text-3xl font-bold">
        <?php
        /* translators: %s: search query. */
        printf(esc_html__('Search results for: %s', 'awards-gallery-theme'), esc_html(get_search_query()));
        ?>
    </h1>

    <?php if (have_posts()) : ?>
        <div class="mt-8 grid gap-8 sm:grid-cols-2 lg:grid-cols-3">
            <?php
            while (have_posts()) :
                the_post();
                get_template_part('template-parts/content');
            endwhile;
            ?>
        </div>
        <div class="mt-10"><?php the_posts_pagination(); ?></div>
    <?php else : ?>
        <p class="mt-6"><?php esc_html_e('No results found.', 'awards-gallery-theme'); ?></p>
        <div class="mt-4"><?php get_search_form(); ?></div>
    <?php endif; ?>
</div>

<?php
get_footer();
