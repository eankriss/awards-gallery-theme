<?php
/**
 * Awards Gallery Process — render.
 *
 * @var array $attributes
 */

if (!defined('ABSPATH')) {
    exit;
}

$mark = awards_gallery_media_url($attributes);
// Rows of three, each with a gold rule on top (the design's row dividers).
$rows = array_chunk($attributes['steps'] ?? [], 3);
?>
<section <?php echo get_block_wrapper_attributes(['class' => 'relative overflow-hidden bg-[#0B0A08] py-20 lg:py-28']); ?>>
  <?php if ($mark) : ?>
    <img src="<?php echo esc_url($mark); ?>" alt="" class="pointer-events-none absolute -right-24 top-1/2 hidden w-[520px] -translate-y-1/2 opacity-[0.08] lg:block xl:right-0 xl:w-[634px]" loading="lazy">
  <?php endif; ?>
  <div class="relative mx-auto max-w-shell px-5 sm:px-8 lg:px-20">
    <div class="reveal flex flex-col items-center text-center">
      <?php echo awards_gallery_eyebrow($attributes['eyebrow'] ?? ''); // phpcs:ignore ?>
      <h2 class="mt-3 text-4xl font-normal leading-tight sm:text-5xl lg:text-[56px]">
        <?php echo esc_html($attributes['heading'] ?? ''); ?>
        <?php if (!empty($attributes['headingScript'])) : ?>
          <span class="text-gold"><?php echo esc_html($attributes['headingScript']); ?></span>
        <?php endif; ?>
      </h2>
    </div>

    <ol class="mt-12 lg:mt-12">
      <?php $n = 0; foreach ($rows as $row) : ?>
        <li class="grid grid-cols-1 border-t border-gold/80 bg-[#0D0C0A] md:grid-cols-3">
          <?php foreach ($row as $step) : $n++; ?>
            <div class="reveal px-8 pb-10 pt-10 lg:px-8" style="--d:<?php echo esc_attr((($n - 1) % 3) * 100); ?>ms">
              <span class="block text-5xl font-normal text-gold lg:text-[56px]"><?php echo esc_html(str_pad((string) $n, 2, '0', STR_PAD_LEFT)); ?></span>
              <h3 class="mt-6 text-[15px] font-normal uppercase tracking-[0.06em] text-cream sm:text-base"><?php echo esc_html($step['title'] ?? ''); ?></h3>
              <p class="mt-3 max-w-sm text-[15px] leading-relaxed text-cream/90 sm:text-base"><?php echo esc_html($step['text'] ?? ''); ?></p>
            </div>
          <?php endforeach; ?>
        </li>
      <?php endforeach; ?>
    </ol>
  </div>
</section>
