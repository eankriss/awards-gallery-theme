<?php
/**
 * Awards Gallery Page Hero — render.
 *
 * Shorter hero for subpages (About, Contact…). The homepage uses the
 * full-height Awards Gallery Hero block instead.
 *
 * @var array $attributes
 */

if (!defined('ABSPATH')) {
    exit;
}

$bg = awards_gallery_media_url($attributes);
?>
<section <?php echo get_block_wrapper_attributes(['class' => 'relative isolate flex min-h-[420px] items-center overflow-hidden border-b border-gold/30 pb-16 pt-32 lg:min-h-[555px] lg:pb-20 lg:pt-36']); ?>>
  <?php if ($bg) : ?>
    <img src="<?php echo esc_url($bg); ?>" alt="" class="absolute inset-0 -z-20 h-full w-full scale-105 object-cover opacity-50 blur-[2px]" fetchpriority="high">
  <?php endif; ?>
  <div class="absolute inset-0 -z-10 bg-gradient-to-b from-ink/70 via-ink/55 to-ink/80"></div>

  <div class="mx-auto flex w-full max-w-shell flex-col items-center px-5 text-center sm:px-8 lg:px-14">
    <?php if (!empty($attributes['eyebrow'])) : ?>
      <div class="animate-rise" style="animation-delay:.1s">
        <?php echo awards_gallery_eyebrow($attributes['eyebrow']); // phpcs:ignore ?>
      </div>
    <?php endif; ?>

    <?php if (!empty($attributes['heading']) || !empty($attributes['headingScript'])) : ?>
      <h1 class="mt-6 flex flex-col items-center font-display font-normal leading-none">
        <?php if (!empty($attributes['heading'])) : ?>
          <span class="animate-rise text-4xl text-cream sm:text-5xl lg:text-[56px]" style="animation-delay:.25s"><?php echo esc_html($attributes['heading']); ?></span>
        <?php endif; ?>
        <?php if (!empty($attributes['headingScript'])) : ?>
          <span class="animate-rise pb-2 pt-1 text-5xl text-gold sm:text-6xl lg:text-[80px]" style="animation-delay:.4s"><?php echo esc_html($attributes['headingScript']); ?></span>
        <?php endif; ?>
      </h1>
    <?php endif; ?>

    <?php if (!empty($attributes['text'])) : ?>
      <p class="animate-rise mt-5 max-w-xl font-outfit text-base leading-relaxed text-cream lg:text-lg" style="animation-delay:.55s"><?php echo esc_html($attributes['text']); ?></p>
    <?php endif; ?>
  </div>
</section>
