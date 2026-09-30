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

$wrapper = ['class' => 'relative bg-gold-sheen py-20 text-ink lg:py-20'];
if (!empty($attributes['sectionId'])) {
    $wrapper['id'] = sanitize_title($attributes['sectionId']);
}
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
    <?php if (!empty($attributes['buttonText'])) : ?>
      <a href="<?php echo esc_url($url ?: '#'); ?>" class="btn-dark mt-8"><?php echo esc_html($attributes['buttonText']); ?></a>
    <?php endif; ?>
  </div>
</section>
