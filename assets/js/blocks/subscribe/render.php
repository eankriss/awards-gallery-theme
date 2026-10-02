<?php
/**
 * Awards Gallery Subscribe — render.
 *
 * Centred "stay updated" band with an inline email form. The form itself is a
 * Contact Form 7 shortcode (the "Subscribe Form"); CFDB7 keeps the signups.
 *
 * @var array $attributes
 */

if (!defined('ABSPATH')) {
    exit;
}

$shortcode = trim($attributes['shortcode'] ?? '');
// Only run shortcodes here, so the field can't be used to inject other markup.
$form      = $shortcode && has_shortcode($shortcode, 'contact-form-7') ? do_shortcode($shortcode) : '';
$in_editor = defined('REST_REQUEST') && REST_REQUEST;
?>
<section <?php echo get_block_wrapper_attributes(['class' => 'bg-noise bg-ink-900 py-16 lg:py-20']); ?>>
  <div class="reveal mx-auto flex max-w-3xl flex-col items-center px-5 text-center sm:px-8">
    <?php echo awards_gallery_eyebrow($attributes['eyebrow'] ?? ''); // phpcs:ignore ?>

    <?php if (!empty($attributes['heading']) || !empty($attributes['headingGold'])) : ?>
      <h2 class="mt-5 text-[28px] font-normal leading-tight text-cream sm:text-[32px] lg:text-4xl">
        <?php echo esc_html($attributes['heading'] ?? ''); ?>
        <?php if (!empty($attributes['headingGold'])) : ?>
          <span class="text-gold"><?php echo esc_html($attributes['headingGold']); ?></span>
        <?php endif; ?>
      </h2>
    <?php endif; ?>

    <?php if (!empty($attributes['text'])) : ?>
      <p class="mt-4 max-w-xl text-sm leading-relaxed text-cream/80 sm:text-[15px]"><?php echo esc_html($attributes['text']); ?></p>
    <?php endif; ?>

    <?php if ($form) : ?>
      <div class="mt-8 w-full max-w-[560px] text-left"><?php echo $form; // phpcs:ignore — Contact Form 7 output. ?></div>
    <?php elseif ($in_editor) : ?>
      <p class="mt-8 text-sm italic text-gold"><?php esc_html_e('Add the Subscribe Form shortcode in the block settings to show the email field.', 'awards-gallery-theme'); ?></p>
    <?php endif; ?>
  </div>
</section>
