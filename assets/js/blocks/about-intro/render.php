<?php
/**
 * Awards Gallery Who We Are — render.
 *
 * @var array $attributes
 */

if (!defined('ABSPATH')) {
    exit;
}

$img      = awards_gallery_media_url($attributes);
$features = $attributes['features'] ?? [];
$badge_v  = $attributes['badgeValue'] ?? '';
$badge_l  = $attributes['badgeLabel'] ?? '';
?>
<section <?php echo get_block_wrapper_attributes(['class' => 'bg-noise overflow-hidden bg-ink py-20 lg:py-28']); ?>>
  <div class="mx-auto grid max-w-shell grid-cols-1 items-center gap-14 px-5 sm:px-8 lg:grid-cols-2 lg:gap-16 lg:px-20">
    <?php // Image column: soft glow behind the photo, gold badge overlapping the bottom-right corner. ?>
    <div class="reveal relative mx-auto w-full max-w-[600px] pb-8 lg:pb-10">
      <div class="pointer-events-none absolute left-1/2 top-1/2 aspect-square w-[90%] -translate-x-1/2 -translate-y-1/2 rounded-full bg-[radial-gradient(circle,rgba(255,255,255,0.07)_0%,rgba(255,255,255,0.02)_45%,transparent_70%)]" aria-hidden="true"></div>
      <?php if ($img) : ?>
        <img src="<?php echo esc_url($img); ?>" alt="<?php echo esc_attr($attributes['headingScript'] ?? ''); ?>" class="relative w-full object-contain" loading="lazy">
      <?php endif; ?>
      <?php if ($badge_v || $badge_l) : ?>
        <div class="absolute bottom-0 right-0 min-w-[150px] bg-gold-gradient px-6 py-4 text-ink sm:min-w-[230px] sm:px-6 sm:py-5">
          <?php if ($badge_v) : ?>
            <p class="text-2xl leading-none sm:text-[28px]"><?php echo esc_html($badge_v); ?></p>
          <?php endif; ?>
          <?php if ($badge_l) : ?>
            <p class="mt-1.5 text-[11px] uppercase tracking-[0.3em] text-ink/80 sm:text-[13px]"><?php echo esc_html($badge_l); ?></p>
          <?php endif; ?>
        </div>
      <?php endif; ?>
    </div>

    <div>
      <div class="reveal">
        <?php if (!empty($attributes['eyebrow'])) : ?>
          <p class="flex items-center gap-5 text-xs uppercase tracking-eyebrow text-gold sm:text-[15px]">
            <span class="h-px w-12 bg-gradient-to-r from-transparent via-gold/70 to-gold/70"></span>
            <?php echo esc_html($attributes['eyebrow']); ?>
          </p>
        <?php endif; ?>
        <h2 class="mt-5 text-4xl font-normal leading-tight text-cream sm:text-5xl">
          <?php echo esc_html($attributes['heading'] ?? ''); ?>
          <?php if (!empty($attributes['headingScript'])) : ?>
            <span class="mt-1 block font-script text-5xl leading-tight text-gold sm:text-6xl"><?php echo esc_html($attributes['headingScript']); ?></span>
          <?php endif; ?>
        </h2>
        <?php if (!empty($attributes['text'])) : ?>
          <p class="mt-6 max-w-xl text-[15px] leading-relaxed text-cream/90 sm:text-base"><?php echo esc_html($attributes['text']); ?></p>
        <?php endif; ?>
      </div>

      <?php if ($features) : ?>
        <ul class="mt-10 grid grid-cols-1 gap-2 sm:grid-cols-2">
          <?php foreach ($features as $i => $feature) : ?>
            <li class="reveal border-t border-gold bg-ink-800 px-6 py-7" style="--d:<?php echo esc_attr(($i % 2) * 100); ?>ms">
              <?php if (!empty($feature['title'])) : ?>
                <h3 class="text-xs uppercase tracking-[0.3em] text-gold sm:text-[13px]"><?php echo esc_html($feature['title']); ?></h3>
              <?php endif; ?>
              <?php if (!empty($feature['text'])) : ?>
                <p class="mt-4 text-[15px] leading-relaxed text-cream/90"><?php echo esc_html($feature['text']); ?></p>
              <?php endif; ?>
            </li>
          <?php endforeach; ?>
        </ul>
      <?php endif; ?>
    </div>
  </div>
</section>
