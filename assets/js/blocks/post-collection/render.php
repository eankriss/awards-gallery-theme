<?php
/**
 * Awards Gallery Post Collection — render.
 *
 * Top: the newest post in the featured category, shown large (page 1 only).
 * Below: a grid of the latest posts of the chosen post type, without that post.
 *
 * @var array $attributes
 */

if (!defined('ABSPATH')) {
    exit;
}

$post_type    = post_type_exists($attributes['postType'] ?? '') ? $attributes['postType'] : 'post';
$per_page     = max(1, (int) ($attributes['postsPerPage'] ?? 6));
$featured_cat = sanitize_title($attributes['featuredCategory'] ?? '');
$link_text    = $attributes['linkText'] ?? '';
// Static pages paginate with "page", archives with "paged".
$paged        = max(1, (int) get_query_var('paged'), (int) get_query_var('page'));

// Newest post in the featured category. It is left out of the grid on every page (so
// pagination stays consistent) but only shown large on page 1.
$featured_id = 0;
if (!empty($attributes['showFeatured']) && $featured_cat && is_object_in_taxonomy($post_type, 'category')) {
    $found       = get_posts([
        'post_type'      => $post_type,
        'posts_per_page' => 1,
        'category_name'  => $featured_cat,
        'fields'         => 'ids',
        'no_found_rows'  => true,
    ]);
    $featured_id = (int) ($found[0] ?? 0);
}
$featured = ($featured_id && $paged === 1) ? get_post($featured_id) : null;

$grid = new WP_Query([
    'post_type'           => $post_type,
    'post_status'         => 'publish',
    'posts_per_page'      => $per_page,
    'paged'               => $paged,
    'post__not_in'        => $featured_id ? [$featured_id] : [],
    'ignore_sticky_posts' => true,
]);
?>
<section <?php echo get_block_wrapper_attributes(['class' => 'bg-noise bg-ink bg-ink-sheen py-16 lg:py-20']); ?>>
  <div class="mx-auto max-w-shell px-5 sm:px-8 lg:px-20">

    <?php if ($featured) :
        $f_img   = get_the_post_thumbnail_url($featured, 'large');
        $f_label = awards_gallery_post_label($featured, $featured_cat);
        $f_url   = get_permalink($featured);
    ?>
      <article class="reveal group grid grid-cols-1 overflow-hidden border-gold-line hover:[--line-alpha:0.7] md:grid-cols-2">
        <a href="<?php echo esc_url($f_url); ?>" class="relative block overflow-hidden" tabindex="-1" aria-hidden="true">
          <?php if ($f_img) : ?>
            <img src="<?php echo esc_url($f_img); ?>" alt="" class="aspect-[16/11] h-full w-full object-cover transition duration-700 group-hover:scale-105" loading="lazy">
          <?php else : ?>
            <div class="aspect-[16/11] h-full w-full bg-ink-800"></div>
          <?php endif; ?>
          <?php if (!empty($attributes['featuredLabel'])) : ?>
            <span class="absolute left-4 top-4 bg-gold-line px-3 py-1 text-[11px] uppercase tracking-[0.3em] text-ink"><?php echo esc_html($attributes['featuredLabel']); ?></span>
          <?php endif; ?>
        </a>
        <div class="flex flex-col justify-center px-6 py-8 sm:px-10 lg:px-12">
          <p class="flex flex-wrap items-center gap-x-3 gap-y-1 text-xs sm:text-[13px]">
            <?php if ($f_label) : ?>
              <span class="uppercase tracking-[0.3em] bg-gold-line bg-clip-text text-transparent"><?php echo esc_html($f_label); ?></span>
              <span class="text-cream/50" aria-hidden="true">•</span>
            <?php endif; ?>
            <span class="text-cream/80"><?php echo esc_html(sprintf(__('%d min read', 'awards-gallery-theme'), awards_gallery_read_time($featured))); ?></span>
          </p>
          <h2 class="mt-5 text-2xl font-normal uppercase leading-snug text-cream lg:text-[28px]">
            <a href="<?php echo esc_url($f_url); ?>" class="text-white hover:text-white"><?php echo esc_html(get_the_title($featured)); ?></a>
          </h2>
          <p class="mt-5 text-[15px] leading-relaxed text-cream/85"><?php echo esc_html(awards_gallery_post_excerpt($featured, 34)); ?></p>
          <div class="mt-8 flex items-center justify-between gap-4 text-xs sm:text-[13px]">
            <time datetime="<?php echo esc_attr(get_the_date('c', $featured)); ?>" class="text-cream/80"><?php echo esc_html(get_the_date('', $featured)); ?></time>
            <?php if (!empty($attributes['featuredLinkText'])) : ?>
              <a href="<?php echo esc_url($f_url); ?>" class="inline-flex items-center gap-3 uppercase tracking-[0.3em] hover:text-transparent"><span class="bg-gold-line bg-clip-text text-transparent"><?php echo esc_html($attributes['featuredLinkText']); ?></span> <span class="bg-gold-line bg-clip-text text-transparent transition-transform duration-300 group-hover:translate-x-1">→</span></a>
            <?php endif; ?>
          </div>
        </div>
      </article>
    <?php endif; ?>

    <?php if ($grid->have_posts()) : ?>
      <div class="<?php echo $featured ? 'mt-6 ' : ''; ?>grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
        <?php $i = 0; while ($grid->have_posts()) : $grid->the_post();
            $img   = get_the_post_thumbnail_url(null, 'medium_large');
            $label = awards_gallery_post_label(get_post(), $featured_cat);
        ?>
          <article class="reveal group relative flex flex-col border-gold-line hover:[--line-alpha:0.7]" style="--d:<?php echo esc_attr(($i++ % 3) * 100); ?>ms">
            <div class="relative overflow-hidden">
              <?php if ($img) : ?>
                <img src="<?php echo esc_url($img); ?>" alt="" class="aspect-[418/220] w-full object-cover transition duration-700 group-hover:scale-105" loading="lazy">
              <?php else : ?>
                <div class="aspect-[418/220] w-full bg-ink-800"></div>
              <?php endif; ?>
              <?php if ($label) : ?>
                <span class="absolute left-3 top-3 bg-gold-line px-3 py-1 text-[11px] uppercase tracking-[0.25em] text-ink"><?php echo esc_html($label); ?></span>
              <?php endif; ?>
            </div>
            <div class="flex flex-1 flex-col px-6 pb-7 pt-6">
              <p class="flex items-center gap-2 text-[13px]">
                <time datetime="<?php echo esc_attr(get_the_date('c')); ?>" class="text-cream/90"><?php echo esc_html(get_the_date()); ?></time>
                <span class="text-cream/50" aria-hidden="true">•</span>
                <span class="text-gold/70"><?php echo esc_html(sprintf(__('%d min read', 'awards-gallery-theme'), awards_gallery_read_time(get_post()))); ?></span>
              </p>
              <?php // The title link stretches over the whole card (after:inset-0), so the card is clickable. ?>
              <h3 class="mt-4 text-[15px] font-normal uppercase leading-snug tracking-[0.02em] text-cream">
                <a href="<?php the_permalink(); ?>" class="text-white after:absolute after:inset-0 hover:text-white"><?php the_title(); ?></a>
              </h3>
              <p class="mt-3 text-sm leading-relaxed text-cream/85"><?php echo esc_html(awards_gallery_post_excerpt(get_post())); ?></p>
              <?php if ($link_text) : ?>
                <div class="mt-auto pt-6">
                  <span class="block h-px bg-gold-line" aria-hidden="true"></span>
                  <span class="mt-5 block text-xs uppercase tracking-[0.3em]">
                    <span class="bg-gold-line bg-clip-text text-transparent"><?php echo esc_html($link_text); ?></span> <span class="inline-block bg-gold-line bg-clip-text text-transparent transition-transform duration-300 group-hover:translate-x-1">→</span>
                  </span>
                </div>
              <?php endif; ?>
            </div>
          </article>
        <?php endwhile; ?>
      </div>

      <?php if (!empty($attributes['showPagination']) && $grid->max_num_pages > 1) :
          $links = paginate_links([
              'total'     => $grid->max_num_pages,
              'current'   => $paged,
              'type'      => 'array',
              'prev_text' => '←',
              'next_text' => '→',
          ]);
      ?>
        <nav class="mt-12 flex flex-wrap justify-center gap-2 text-sm [&_.current]:border-gold [&_.current]:bg-gold [&_.current]:text-ink [&_a:hover]:border-gold [&_a:hover]:text-gold [&>*]:flex [&>*]:h-10 [&>*]:min-w-10 [&>*]:items-center [&>*]:justify-center [&>*]:border [&>*]:border-gold/40 [&>*]:px-3" aria-label="<?php esc_attr_e('Posts pagination', 'awards-gallery-theme'); ?>">
          <?php echo implode('', $links); // phpcs:ignore — core-generated markup. ?>
        </nav>
      <?php endif; ?>
    <?php elseif (!$featured && !empty($attributes['emptyText'])) : ?>
      <p class="py-10 text-center text-cream/70"><?php echo esc_html($attributes['emptyText']); ?></p>
    <?php endif; ?>
    <?php wp_reset_postdata(); ?>
  </div>
</section>
