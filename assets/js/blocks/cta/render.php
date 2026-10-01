<?php
/**
 * Awards Gallery Call to Action — render.
 *
 * @var array $attributes
 */

if (!defined('ABSPATH')) {
    exit;
}

$url = $attributes['buttonUrl'] ?? '';
if (!$url && awards_gallery_contact('ag_email')) {
    $url = 'mailto:' . awards_gallery_contact('ag_email');
}

$secondary_text = $attributes['secondaryButtonText'] ?? '';
$secondary_url  = $attributes['secondaryButtonUrl'] ?? '';
$show_primary   = !empty($attributes['buttonText']);
$show_secondary = $secondary_text && $secondary_url;

$wrapper = ['class' => 'relative bg-gold-sheen py-20 text-ink lg:py-20'];
?>
<section <?php echo get_block_wrapper_attributes($wrapper); ?>>
  <div class="reveal mx-auto flex max-w-4xl flex-col items-center px-5 text-center">
    <?php if (!empty($attributes['eyebrow'])) : ?>
      <p class="text-xs uppercase tracking-eyebrow text-ink/60 sm:text-[15px]"><?php echo esc_html($attributes['eyebrow']); ?></p>
    <?php endif; ?>
    <h2 class="mt-4 font-script text-5xl font-normal leading-tight sm:text-7xl lg:text-[96px]">
      <?php echo esc_html(trim(($attributes['heading'] ?? '') . ' ' . ($attributes['headingScript'] ?? ''))); ?>
    </h2>
    <?php if (!empty($attributes['text'])) : ?>
      <p class="mt-4 text-base leading-relaxed text-ink/75"><?php echo esc_html($attributes['text']); ?></p>
    <?php endif; ?>
    <?php if ($show_primary || $show_secondary) : ?>
      <div class="mt-8 flex flex-wrap items-center justify-center gap-4">
        <?php if ($show_primary) : ?>
          <a href="<?php echo esc_url($url ?: '#'); ?>" class="btn-dark"><?php echo esc_html($attributes['buttonText']); ?></a>
        <?php endif; ?>
        <?php if ($show_secondary) : ?>
          <a href="<?php echo esc_url($secondary_url); ?>" class="btn-dark-outline"><?php echo esc_html($secondary_text); ?></a>
        <?php endif; ?>
      </div>
    <?php endif; ?>
  </div>
</section>
