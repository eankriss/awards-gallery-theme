<?php
/**
 * Awards Gallery Also Available — render.
 *
 * @var array $attributes
 */

if (!defined('ABSPATH')) {
    exit;
}

$items = $attributes['items'] ?? [];
?>
<section <?php echo get_block_wrapper_attributes(['class' => 'bg-ink-900 py-20 lg:py-24']); ?>>
  <div class="mx-auto max-w-shell px-5 sm:px-8 lg:px-20">
    <div class="reveal flex flex-col items-center text-center">
      <?php echo awards_gallery_eyebrow($attributes['eyebrow'] ?? ''); // phpcs:ignore ?>
      <h2 class="mt-4 text-4xl font-normal leading-tight text-cream sm:text-5xl lg:text-[52px]">
        <?php echo esc_html($attributes['heading'] ?? ''); ?>
        <?php if (!empty($attributes['headingScript'])) : ?>
          <span class="text-gold"><?php echo esc_html($attributes['headingScript']); ?></span>
        <?php endif; ?>
      </h2>
      <?php if (!empty($attributes['text'])) : ?>
        <p class="mt-5 max-w-md text-[15px] leading-relaxed text-cream/90"><?php echo esc_html($attributes['text']); ?></p>
      <?php endif; ?>
    </div>

    <?php if ($items) : ?>
      <ul class="mt-12 grid grid-cols-1 gap-x-10 gap-y-12 border-t border-gold/30 pt-12 sm:grid-cols-2 lg:mt-14 lg:grid-cols-3 lg:gap-y-16 lg:px-4 lg:pt-14">
        <?php foreach ($items as $i => $item) : ?>
          <li class="reveal" style="--d:<?php echo esc_attr(($i % 3) * 100); ?>ms">
            <span class="block text-gold"><?php echo awards_gallery_also_available_icon($item['icon'] ?? ''); // phpcs:ignore — static SVG markup. ?></span>
            <h3 class="mt-4 text-[13px] font-medium uppercase tracking-[0.12em] text-cream sm:text-sm"><?php echo esc_html($item['title'] ?? ''); ?></h3>
            <?php if (!empty($item['text'])) : ?>
              <p class="mt-2 max-w-xs text-[15px] leading-relaxed text-cream/80"><?php echo esc_html($item['text']); ?></p>
            <?php endif; ?>
          </li>
        <?php endforeach; ?>
      </ul>
    <?php endif; ?>
  </div>
</section>
