<?php
if (!defined('ABSPATH')) {
    exit;
}

get_header();

while (have_posts()) :
    the_post();
    ?>
    <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
        <div class="entry-content">
            <?php the_content(); ?>
        </div>
    </article>
    <?php
endwhile;

get_footer();
