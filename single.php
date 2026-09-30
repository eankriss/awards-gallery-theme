<?php
if (!defined('ABSPATH')) {
    exit;
}

get_header();

while (have_posts()) :
    the_post();
    ?>
    <article id="post-<?php the_ID(); ?>" <?php post_class('container-shell py-10'); ?>>
        <h1 class="font-display text-4xl font-bold"><?php the_title(); ?></h1>
        <p class="mt-2 text-sm text-muted"><?php echo esc_html(get_the_date()); ?></p>

        <?php if (has_post_thumbnail()) : ?>
            <div class="mt-6"><?php the_post_thumbnail('large', ['class' => 'w-full rounded-xl']); ?></div>
        <?php endif; ?>

        <div class="entry-content mt-8">
            <?php the_content(); ?>
        </div>
    </article>
    <?php
endwhile;

get_footer();
