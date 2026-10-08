<?php
/**
 * Awards Gallery Product Grid — render.
 *
 * @var array $attributes
 */

if (!defined('ABSPATH')) {
    exit;
}

$link_text = $attributes['linkText'] ?? '';
?>
<section <?php echo get_block_wrapper_attributes(['class' => 'bg-noise bg-ink bg-ink-sheen py-16 lg:py-20']); ?>>
  <div class="mx-auto max-w-shell px-5 sm:px-8 lg:px-20">
    <?php
    // Cells overlap by 1px (-ml-px/-mt-px) so neighbouring borders read as single hairlines.
    // Later cells paint over earlier ones, so the hovered/focused card is lifted (z-10)
    // to keep all four sides of its gold outline visible.
    ?>
    <div class="grid grid-cols-1 pl-px pt-px sm:grid-cols-2 lg:grid-cols-3">
      <?php foreach (($attributes['items'] ?? []) as $i => $item) :
          $img   = $item['image']['url'] ?? '';
          $title = $item['title'] ?? '';
          $url   = trim($item['url'] ?? '');
          // No link → a non-clickable card (a <div>); the image zoom still plays.
          $tag   = $url ? 'a' : 'div';
      ?>
        <<?php echo $tag; ?><?php echo $url ? ' href="' . esc_url($url) . '"' : ''; ?> class="reveal group relative -ml-px -mt-px flex flex-col hover:z-10 focus-visible:z-10 border border-gold/50 bg-ink-900 transition-colors duration-300 hover:border-gold hover:text-cream" style="--d:<?php echo esc_attr(($i % 3) * 100); ?>ms">
          <div class="overflow-hidden">
            <?php if ($img) : ?>
              <img src="<?php echo esc_url($img); ?>" alt="<?php echo esc_attr($title); ?>" class="aspect-[418/380] w-full object-cover transition duration-700 group-hover:scale-105" loading="lazy">
            <?php else : ?>
              <?php echo awards_gallery_card_placeholder('aspect-[418/380]'); // phpcs:ignore -- escaped in helper ?>
            <?php endif; ?>
          </div>
          <div class="flex flex-1 flex-col px-6 pb-8 pt-7 lg:px-7">
            <?php if (!empty($item['kicker'])) : ?>
              <p class="text-xs uppercase tracking-[0.3em] text-gold sm:text-[13px]"><?php echo esc_html($item['kicker']); ?></p>
            <?php endif; ?>
            <h3 class="mt-2 text-xl font-normal text-cream lg:text-[22px]"><?php echo esc_html($title); ?></h3>
            <?php if (!empty($item['text'])) : ?>
              <p class="mt-3 text-[15px] leading-relaxed text-cream/85"><?php echo esc_html($item['text']); ?></p>
            <?php endif; ?>
            <?php if ($url && $link_text) : ?>
              <span class="mt-auto inline-flex items-center gap-3 pt-6 text-xs uppercase tracking-[0.3em] text-gold"><?php echo esc_html($link_text); ?> <span class="transition-transform duration-300 group-hover:translate-x-1">→</span></span>
            <?php endif; ?>
          </div>
        </<?php echo $tag; ?>>
      <?php endforeach; ?>
    </div>
  </div>
</section>
