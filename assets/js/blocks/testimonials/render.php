<?php
/**
 * Awards Gallery Testimonials — render.
 *
 * @var array $attributes
 */

if (!defined('ABSPATH')) {
    exit;
}
?>
<section <?php echo get_block_wrapper_attributes(['class' => 'bg-[#0B0A08] py-20 lg:py-28']); ?>>
  <div class="mx-auto max-w-shell px-5 sm:px-8 lg:px-20">
    <div class="reveal flex flex-col items-center text-center">
      <?php echo awards_gallery_eyebrow($attributes['eyebrow'] ?? ''); // phpcs:ignore ?>
      <h2 class="mt-8 text-4xl font-normal leading-tight sm:text-5xl lg:text-[56px]">
        <?php echo esc_html($attributes['heading'] ?? ''); ?>
        <?php if (!empty($attributes['headingScript'])) : ?>
          <span class="text-gold"><?php echo esc_html($attributes['headingScript']); ?></span>
        <?php endif; ?>
      </h2>
    </div>

    <div class="mt-14 grid grid-cols-1 divide-y divide-cream/10 border-t border-gold/70 bg-[#0D0C0A] md:grid-cols-3 md:divide-x md:divide-y-0 lg:mt-16">
      <?php foreach (($attributes['items'] ?? []) as $i => $item) : ?>
        <figure class="reveal flex flex-col p-8 lg:px-9 lg:pb-10 lg:pt-10" style="--d:<?php echo esc_attr(($i % 3) * 100); ?>ms">
          <span class="font-serif text-3xl leading-none text-gold/50" aria-hidden="true">&quot;</span>
          <blockquote class="mt-8 text-base italic leading-relaxed text-cream"><?php echo esc_html($item['quote'] ?? ''); ?></blockquote>
          <figcaption class="mt-8 border-t border-gold/70 pt-6">
            <p class="text-base text-gold"><?php echo esc_html($item['name'] ?? ''); ?></p>
            <?php if (!empty($item['role'])) : ?>
              <p class="mt-2 text-[13px] uppercase tracking-[0.3em] text-cream sm:text-[15px]"><?php echo esc_html($item['role']); ?></p>
            <?php endif; ?>
          </figcaption>
        </figure>
      <?php endforeach; ?>
    </div>
  </div>
</section>
