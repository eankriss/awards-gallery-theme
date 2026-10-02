<?php
/**
 * Awards Gallery Testimonials — render.
 *
 * @var array $attributes
 */

if (!defined('ABSPATH')) {
    exit;
}

$items    = $attributes['items'] ?? [];
$carousel = $attributes['carousel'] ?? true;
$autoplay = $carousel && !empty($attributes['autoplay']);
$seconds  = max(3, min(15, (int) ($attributes['autoplaySeconds'] ?? 5)));

$card = function ($item, $class, $style = '') {
    ?>
    <figure class="<?php echo esc_attr($class); ?>"<?php echo $style ? ' style="' . esc_attr($style) . '"' : ''; ?>>
      <span class="font-serif text-3xl leading-none text-gold/50" aria-hidden="true">&quot;</span>
      <blockquote class="mt-8 text-base italic leading-relaxed text-cream"><?php echo esc_html($item['quote'] ?? ''); ?></blockquote>
      <figcaption class="mt-8 border-t border-gold/70 pt-6">
        <p class="text-base text-gold"><?php echo esc_html($item['name'] ?? ''); ?></p>
        <?php if (!empty($item['role'])) : ?>
          <p class="mt-2 text-[13px] uppercase tracking-[0.3em] text-cream sm:text-[15px]"><?php echo esc_html($item['role']); ?></p>
        <?php endif; ?>
      </figcaption>
    </figure>
    <?php
};

$arrow = 'flex h-12 w-12 items-center justify-center rounded-full border border-gold/60 text-gold transition hover:bg-gold hover:text-ink focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-gold disabled:pointer-events-none disabled:opacity-30';
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

    <?php if ($carousel) : ?>
      <div class="reveal mt-14 lg:mt-16" data-carousel<?php echo $autoplay ? ' data-autoplay="' . esc_attr($seconds * 1000) . '"' : ''; ?>>
        <div
          class="-mx-5 flex snap-x snap-mandatory scroll-px-5 gap-5 overflow-x-auto px-5 [scrollbar-width:none] sm:mx-0 sm:scroll-px-0 sm:px-0 [&::-webkit-scrollbar]:hidden"
          role="region"
          aria-label="<?php esc_attr_e('Testimonials', 'awards-gallery-theme'); ?>"
          tabindex="0"
          data-carousel-track>
          <?php foreach ($items as $item) {
              $card($item, 'flex w-[85%] shrink-0 snap-start flex-col border-t border-gold/70 bg-[#0D0C0A] p-8 sm:w-[calc((100%-1.25rem)/2)] lg:w-[calc((100%-2.5rem)/3)] lg:px-9 lg:py-10');
          } ?>
        </div>

        <div class="mt-10 hidden items-center justify-between gap-6" data-carousel-nav>
          <div class="flex flex-wrap gap-2" data-carousel-dots data-label="<?php esc_attr_e('Go to testimonial %d', 'awards-gallery-theme'); ?>"></div>
          <div class="flex gap-3">
            <?php if ($autoplay) : ?>
              <button
                type="button"
                class="group mr-1 flex h-12 w-12 items-center justify-center rounded-full text-cream/60 transition hover:text-gold focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-gold"
                aria-pressed="false"
                aria-label="<?php esc_attr_e('Pause slideshow', 'awards-gallery-theme'); ?>"
                data-label-pause="<?php esc_attr_e('Pause slideshow', 'awards-gallery-theme'); ?>"
                data-label-play="<?php esc_attr_e('Play slideshow', 'awards-gallery-theme'); ?>"
                data-carousel-pause>
                <svg class="h-4 w-4 group-aria-pressed:hidden" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><rect x="6" y="4.5" width="4" height="15" rx="1"/><rect x="14" y="4.5" width="4" height="15" rx="1"/></svg>
                <svg class="hidden h-4 w-4 group-aria-pressed:block" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M7 4.5v15a1 1 0 0 0 1.5.86l12.5-7.5a1 1 0 0 0 0-1.72L8.5 3.64A1 1 0 0 0 7 4.5z"/></svg>
              </button>
            <?php endif; ?>
            <button type="button" class="<?php echo esc_attr($arrow); ?>" aria-label="<?php esc_attr_e('Previous testimonial', 'awards-gallery-theme'); ?>" data-carousel-prev>
              <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M15 18l-6-6 6-6"/></svg>
            </button>
            <button type="button" class="<?php echo esc_attr($arrow); ?>" aria-label="<?php esc_attr_e('Next testimonial', 'awards-gallery-theme'); ?>" data-carousel-next>
              <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M9 6l6 6-6 6"/></svg>
            </button>
          </div>
        </div>
      </div>
    <?php else : ?>
      <div class="mt-14 grid grid-cols-1 divide-y divide-cream/10 border-t border-gold/70 bg-[#0D0C0A] md:grid-cols-3 md:divide-x md:divide-y-0 lg:mt-16">
        <?php foreach ($items as $i => $item) {
            $card($item, 'reveal flex flex-col p-8 lg:px-9 lg:pb-10 lg:pt-10', '--d:' . (($i % 3) * 100) . 'ms');
        } ?>
      </div>
    <?php endif; ?>
  </div>
</section>
