<?php
/**
 * Awards Gallery Post Collection — render.
 *
 * Top: the newest 1–3 posts with the featured tag (page 1 only), each as a wide card.
 * Below: a grid of the latest posts, without those.
 *
 * @var array $attributes
 */

if (!defined('ABSPATH')) {
    exit;
}

$post_type      = post_type_exists($attributes['postType'] ?? '') ? $attributes['postType'] : 'post';
$per_page       = max(1, (int) ($attributes['postsPerPage'] ?? 6));
$featured_tag   = sanitize_title($attributes['featuredTag'] ?? 'featured');
$featured_count = max(1, min(3, (int) ($attributes['featuredCount'] ?? 3)));
$link_text      = $attributes['linkText'] ?? '';
// Badge on the featured image: custom text, else the featured tag's own name.
$tag_term       = $featured_tag ? get_term_by('slug', $featured_tag, 'post_tag') : null;
$badge          = ($attributes['featuredLabel'] ?? '') ?: ($tag_term ? $tag_term->name : '');
$f_link_text    = $attributes['featuredLinkText'] ?? '';
// Static pages paginate with "page", archives with "paged".
$paged          = max(1, (int) get_query_var('paged'), (int) get_query_var('page'));

// Newest posts with the featured tag. They are left out of the grid on every page
// (so pagination stays consistent) but only shown on page 1.
$featured_ids = [];
if (!empty($attributes['showFeatured']) && $featured_tag && is_object_in_taxonomy($post_type, 'post_tag')) {
    $featured_ids = array_map('intval', get_posts([
        'post_type'      => $post_type,
        'posts_per_page' => $featured_count,
        'tag'            => $featured_tag,
        'fields'         => 'ids',
        'no_found_rows'  => true,
    ]));
}
$featured = $paged === 1 ? array_map('get_post', $featured_ids) : [];

$grid = new WP_Query([
    'post_type'           => $post_type,
    'post_status'         => 'publish',
    'posts_per_page'      => $per_page,
    'paged'               => $paged,
    'post__not_in'        => $featured_ids,
    'ignore_sticky_posts' => true,
]);

// The post's categories (e.g. Buyer's Guide) followed by the read time.
$meta = function ($post) {
    $tags = awards_gallery_post_categories($post);
    ?>
    <p class="flex flex-wrap items-center gap-x-3 gap-y-1 text-xs sm:text-[13px]">
      <?php foreach ($tags as $tag) : ?>
        <span class="uppercase tracking-[0.3em] bg-gold-line bg-clip-text text-transparent"><?php echo esc_html($tag); ?></span>
        <span class="text-cream/50" aria-hidden="true">•</span>
      <?php endforeach; ?>
      <span class="text-cream/80"><?php echo esc_html(sprintf(__('%d min read', 'awards-gallery-theme'), awards_gallery_read_time($post))); ?></span>
    </p>
    <?php
};

// Responsive thumbnail (srcset) or a dark placeholder.
$thumb = function ($post, $size, $class, $sizes, $extra = []) {
    $img = get_the_post_thumbnail($post, $size, array_merge(['class' => $class, 'alt' => '', 'sizes' => $sizes], $extra));
    echo $img ?: '<div class="' . esc_attr($class) . ' bg-ink-800"></div>'; // phpcs:ignore
};

// The first featured image is the biggest thing on the page, so it loads first instead of lazily.
$lead_img_attrs = ['loading' => false, 'fetchpriority' => 'high', 'decoding' => 'async'];
?>
<section <?php echo get_block_wrapper_attributes(['class' => 'bg-noise bg-ink bg-ink-sheen py-16 lg:py-20']); ?>>
  <div class="mx-auto max-w-shell px-5 sm:px-8 lg:px-20">

    <?php if ($featured) : ?>
      <div class="grid grid-cols-1 gap-6">
        <?php foreach ($featured as $n => $post_item) : $f_url = get_permalink($post_item); ?>
          <article class="reveal grid grid-cols-1 overflow-hidden border-gold-line hover:[--line-alpha:0.7] md:grid-cols-2">
            <a href="<?php echo esc_url($f_url); ?>" class="group/img relative block overflow-hidden" tabindex="-1" aria-hidden="true">
              <?php $thumb($post_item, 'large', 'aspect-[16/11] h-full w-full object-cover transition duration-700 group-hover/img:scale-105', '(min-width: 768px) 50vw, 100vw', $n === 0 ? $lead_img_attrs : []); ?>
              <?php if ($badge) : ?>
                <span class="absolute left-4 top-4 bg-gold-line px-3 py-1 text-[11px] uppercase tracking-[0.3em] text-ink"><?php echo esc_html($badge); ?></span>
              <?php endif; ?>
            </a>
            <div class="flex flex-col justify-center px-6 py-8 sm:px-10 lg:px-12">
              <?php $meta($post_item); ?>
              <h2 class="mt-5 text-2xl font-normal uppercase leading-snug text-cream lg:text-[28px]">
                <a href="<?php echo esc_url($f_url); ?>" class="text-white transition hover:text-gold"><?php echo esc_html(get_the_title($post_item)); ?></a>
              </h2>
              <p class="mt-5 text-[15px] leading-relaxed text-cream/85"><?php echo esc_html(awards_gallery_post_excerpt($post_item, 34)); ?></p>
              <?php // Tablet: the half-width column is too narrow for one row, so date and link stack. ?>
              <div class="mt-8 flex items-center justify-between gap-4 text-xs sm:text-[13px] md:flex-col md:items-start md:gap-3 lg:flex-row lg:items-center lg:justify-between lg:gap-4">
                <time datetime="<?php echo esc_attr(get_the_date('c', $post_item)); ?>" class="whitespace-nowrap text-cream/80"><?php echo esc_html(get_the_date('', $post_item)); ?></time>
                <?php if ($f_link_text) : ?>
                  <a href="<?php echo esc_url($f_url); ?>" class="group/link inline-flex items-center gap-3 whitespace-nowrap uppercase tracking-[0.3em]"><span class="bg-gold-line bg-clip-text text-transparent"><?php echo esc_html($f_link_text); ?></span><span class="sr-only">: <?php echo esc_html(get_the_title($post_item)); ?></span> <span class="bg-gold-line bg-clip-text text-transparent transition-transform duration-300 group-hover/link:translate-x-1" aria-hidden="true">→</span></a>
                <?php endif; ?>
              </div>
            </div>
          </article>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>

    <?php if ($grid->have_posts()) : ?>
      <div class="<?php echo $featured ? 'mt-6 ' : ''; ?>grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
        <?php $i = 0; while ($grid->have_posts()) : $grid->the_post();
            $label = awards_gallery_post_label(get_post());
        ?>
          <article class="reveal group relative flex flex-col border-gold-line hover:[--line-alpha:0.7]" style="--d:<?php echo esc_attr(($i++ % 3) * 100); ?>ms">
            <?php // Only the image, title and "Read More" are links; the rest of the card is plain text. ?>
            <a href="<?php the_permalink(); ?>" class="group/img relative block overflow-hidden" tabindex="-1" aria-hidden="true">
              <?php $thumb(get_post(), 'medium_large', 'aspect-[418/220] w-full object-cover transition duration-700 group-hover/img:scale-105', '(min-width: 1024px) 33vw, (min-width: 640px) 50vw, 100vw'); ?>
              <?php if ($label) : ?>
                <span class="absolute left-3 top-3 bg-gold-line px-3 py-1 text-[11px] uppercase tracking-[0.25em] text-ink"><?php echo esc_html($label); ?></span>
              <?php endif; ?>
            </a>
            <div class="flex flex-1 flex-col px-6 pb-7 pt-6">
              <p class="flex items-center gap-2 text-[13px]">
                <time datetime="<?php echo esc_attr(get_the_date('c')); ?>" class="text-cream/90"><?php echo esc_html(get_the_date()); ?></time>
                <span class="text-cream/50" aria-hidden="true">•</span>
                <span class="text-gold/70"><?php echo esc_html(sprintf(__('%d min read', 'awards-gallery-theme'), awards_gallery_read_time(get_post()))); ?></span>
              </p>
              <h3 class="mt-4 text-[15px] font-normal uppercase leading-snug tracking-[0.02em] text-cream">
                <a href="<?php the_permalink(); ?>" class="text-white transition hover:text-gold"><?php the_title(); ?></a>
              </h3>
              <p class="mt-3 text-sm leading-relaxed text-cream/85"><?php echo esc_html(awards_gallery_post_excerpt(get_post())); ?></p>
              <?php if ($link_text) : ?>
                <div class="mt-auto pt-6">
                  <span class="block h-px bg-gold-line" aria-hidden="true"></span>
                  <a href="<?php the_permalink(); ?>" class="group/link mt-5 inline-block text-xs uppercase tracking-[0.3em]">
                    <span class="bg-gold-line bg-clip-text text-transparent"><?php echo esc_html($link_text); ?></span><span class="sr-only">: <?php the_title(); ?></span> <span class="inline-block bg-gold-line bg-clip-text text-transparent transition-transform duration-300 group-hover/link:translate-x-1" aria-hidden="true">→</span>
                  </a>
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
