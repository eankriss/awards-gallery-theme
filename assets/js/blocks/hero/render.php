<?php
/**
 * Awards Gallery Hero — render.
 *
 * @var array $attributes
 */

if (!defined('ABSPATH')) {
    exit;
}

$bg = awards_gallery_media_url($attributes);
?>
<section <?php echo get_block_wrapper_attributes(['class' => 'relative isolate flex min-h-[640px] items-center overflow-hidden pb-16 pt-32 lg:min-h-[900px]']); ?>>
  <?php if ($bg) : ?>
    <img src="<?php echo esc_url($bg); ?>" alt="" class="absolute inset-0 -z-20 h-full w-full object-cover opacity-60" fetchpriority="high">
  <?php endif; ?>
  <div class="absolute inset-0 -z-10 bg-gradient-to-b from-ink/60 via-ink/40 to-ink/70"></div>

  <div class="mx-auto flex w-full max-w-shell flex-col items-center px-5 text-center sm:px-8 lg:px-14">
    <?php if (!empty($attributes['eyebrow'])) : ?>
      <div class="animate-rise" style="animation-delay:.1s">
        <?php echo awards_gallery_eyebrow($attributes['eyebrow']); // phpcs:ignore ?>
      </div>
    <?php endif; ?>

    <h1 class="mt-10 flex flex-col items-center font-display font-normal leading-none">
      <?php if (!empty($attributes['heading'])) : ?>
        <span class="animate-rise text-6xl text-cream sm:text-7xl lg:text-[96px]" style="animation-delay:.25s"><?php echo esc_html($attributes['heading']); ?></span>
      <?php endif; ?>
      <?php if (!empty($attributes['headingScript'])) : ?>
        <span class="animate-rise -mt-2 pb-4 text-7xl text-gold sm:text-8xl lg:-mt-6 lg:text-[150px]" style="animation-delay:.4s"><?php echo esc_html($attributes['headingScript']); ?></span>
      <?php endif; ?>
    </h1>

    <?php if (!empty($attributes['text'])) : ?>
      <p class="animate-rise mt-6 max-w-2xl font-outfit text-lg leading-relaxed text-cream lg:text-xl" style="animation-delay:.55s"><?php echo esc_html($attributes['text']); ?></p>
    <?php endif; ?>
    <?php if (!empty($attributes['tagline'])) : ?>
      <p class="animate-rise mt-8 text-xs uppercase tracking-[0.3em] text-gold sm:text-[15px]" style="animation-delay:.65s"><?php echo esc_html($attributes['tagline']); ?></p>
    <?php endif; ?>

    <?php if (!empty($attributes['primaryText']) || !empty($attributes['secondaryText'])) : ?>
      <div class="animate-rise mt-12 flex w-full flex-col gap-4 sm:w-auto sm:flex-row" style="animation-delay:.8s">
        <?php if (!empty($attributes['primaryText'])) : ?>
          <a href="<?php echo esc_url($attributes['primaryUrl'] ?: '#'); ?>" class="btn-gold"><?php echo esc_html($attributes['primaryText']); ?></a>
        <?php endif; ?>
        <?php if (!empty($attributes['secondaryText'])) : ?>
          <a href="<?php echo esc_url($attributes['secondaryUrl'] ?: '#'); ?>" class="btn-outline"><?php echo esc_html($attributes['secondaryText']); ?></a>
        <?php endif; ?>
      </div>
    <?php endif; ?>
  </div>
</section>
