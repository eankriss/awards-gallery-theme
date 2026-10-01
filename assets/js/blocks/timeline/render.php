<?php
/**
 * Awards Gallery Timeline — render.
 *
 * @var array $attributes
 */

if (!defined('ABSPATH')) {
    exit;
}

$items = $attributes['items'] ?? [];
$last  = count($items) - 1;
?>
<section <?php echo get_block_wrapper_attributes(['class' => 'bg-noise bg-ink bg-ink-sheen py-20 lg:py-28']); ?>>
  <div class="mx-auto max-w-shell px-5 sm:px-8 lg:px-20">
    <div class="reveal flex flex-col items-center text-center">
      <?php echo awards_gallery_eyebrow($attributes['eyebrow'] ?? ''); // phpcs:ignore ?>
      <h2 class="mt-4 text-3xl font-medium leading-tight text-cream sm:text-4xl lg:text-[44px]">
        <?php echo esc_html($attributes['heading'] ?? ''); ?>
        <?php if (!empty($attributes['headingScript'])) : ?>
          <span class="text-gold"><?php echo esc_html($attributes['headingScript']); ?></span>
        <?php endif; ?>
      </h2>
    </div>

    <?php if ($items) : ?>
      <?php
      // Scroll-driven timeline (assets/js/awards-gallery.js → [data-timeline]): the gold
      // fill grows down the faint track as the visitor scrolls, and each milestone lights
      // up when the fill reaches its dot. JS positions the rail between the first and
      // last dots and adds .is-off to milestones not reached yet — without JS the rail
      // stays hidden and every milestone shows fully lit.
      ?>
      <ol class="relative mx-auto mt-14 max-w-[900px] lg:mt-16" data-timeline>
        <?php if ($last > 0) : ?>
          <span class="pointer-events-none absolute left-[78px] hidden w-px -translate-x-1/2 bg-gold/20 sm:left-[112px]" data-timeline-rail aria-hidden="true">
            <span class="absolute inset-0 origin-top bg-gold shadow-[0_0_10px_rgba(218,147,40,0.6)]" style="transform:scaleY(var(--timeline-progress,1))"></span>
          </span>
        <?php endif; ?>
        <?php foreach ($items as $i => $item) : ?>
          <li class="group/item grid grid-cols-[52px_28px_1fr] gap-x-3 sm:grid-cols-[80px_32px_1fr] sm:gap-x-4" data-timeline-item>
            <span class="pt-0.5 text-right text-sm text-gold transition-colors duration-500 group-[.is-off]/item:text-gold/40 sm:text-lg"><?php echo esc_html($item['year'] ?? ''); ?></span>
            <span class="relative flex justify-center" aria-hidden="true">
              <span class="relative mt-1 flex h-5 w-5 items-center justify-center rounded-full border-2 border-gold-light bg-ink shadow-[0_0_0_4px_rgba(218,147,40,0.15)] transition-[border-color,box-shadow] duration-500 group-[.is-off]/item:border-gold/30 group-[.is-off]/item:shadow-none" data-timeline-dot>
                <span class="h-2.5 w-2.5 rounded-full bg-gold-light transition-transform duration-500 group-[.is-off]/item:scale-0"></span>
              </span>
            </span>
            <div class="transition-[opacity,transform] duration-700 ease-out group-[.is-off]/item:translate-x-3 group-[.is-off]/item:opacity-30<?php echo $i === $last ? '' : ' pb-12 sm:pb-14'; ?>">
              <h3 class="text-sm font-semibold uppercase tracking-[0.08em] text-cream sm:text-base"><?php echo esc_html($item['title'] ?? ''); ?></h3>
              <span class="mt-2 block h-0.5 w-10 origin-left bg-gold transition-transform delay-150 duration-500 group-[.is-off]/item:scale-x-0" aria-hidden="true"></span>
              <?php if (!empty($item['text'])) : ?>
                <p class="mt-5 max-w-2xl text-[15px] leading-relaxed text-cream/90"><?php echo esc_html($item['text']); ?></p>
              <?php endif; ?>
            </div>
          </li>
        <?php endforeach; ?>
      </ol>
    <?php endif; ?>
  </div>
</section>
