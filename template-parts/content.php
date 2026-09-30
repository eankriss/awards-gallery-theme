<?php
if (!defined('ABSPATH')) {
    exit;
}
?>
<article id="post-<?php the_ID(); ?>" <?php post_class('overflow-hidden rounded-xl ring-1 ring-black/10'); ?>>
    <?php if (has_post_thumbnail()) : ?>
        <a href="<?php the_permalink(); ?>"><?php the_post_thumbnail('large', ['class' => 'aspect-video w-full object-cover']); ?></a>
    <?php endif; ?>
    <div class="p-5">
        <h2 class="font-display text-lg font-bold"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
        <div class="mt-2 text-sm text-muted"><?php the_excerpt(); ?></div>
    </div>
</article>
