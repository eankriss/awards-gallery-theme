<?php
if (!defined('ABSPATH')) {
    exit;
}

get_header();

if (is_page()) {
    // Static front page: render exactly what is in the page editor.
    while (have_posts()) {
        the_post();
        echo '<div class="entry-content">';
        the_content();
        echo '</div>';
    }
} else {
    // "Your latest posts" is selected — show the default homepage layout.
    echo do_blocks(awards_gallery_homepage_blocks()); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
}

get_footer();
