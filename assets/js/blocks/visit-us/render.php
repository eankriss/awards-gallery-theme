<?php
/**
 * Awards Gallery Visit Us — render.
 *
 * @var array $attributes
 */

if (!defined('ABSPATH')) {
    exit;
}

// The editor field takes a pasted <iframe> embed code or a bare URL. Only the https
// src is kept and the iframe is rebuilt here, so pasted markup never reaches the page.
$embed   = trim($attributes['mapEmbed'] ?? '');
$map_src = '';
if (preg_match('/\ssrc=["\']([^"\']+)["\']/i', $embed, $m)) {
    $map_src = html_entity_decode($m[1]);
} elseif (preg_match('#^https://\S+$#', $embed)) {
    $map_src = $embed;
}
if (strpos($map_src, 'https://') !== 0) {
    $map_src = '';
}
$details = $attributes['details'] ?? [];
?>
<section <?php echo get_block_wrapper_attributes(['class' => 'bg-noise bg-gradient-to-br from-[#1A1712] via-ink-800 to-ink py-16 lg:py-20']); ?>>
  <div class="mx-auto grid max-w-shell grid-cols-1 items-center gap-10 px-5 sm:px-8 lg:grid-cols-2 lg:gap-16 lg:px-20">
    <div class="reveal">
      <?php if (!empty($attributes['eyebrow'])) : ?>
        <p class="flex items-center gap-5 text-xs uppercase tracking-eyebrow text-gold sm:text-[13px]">
          <span class="h-px w-12 bg-gradient-to-r from-transparent via-gold/70 to-gold/70"></span>
          <?php echo esc_html($attributes['eyebrow']); ?>
        </p>
      <?php endif; ?>
      <h2 class="mt-4 text-3xl font-normal leading-tight text-cream sm:text-4xl">
        <?php echo esc_html($attributes['heading'] ?? ''); ?>
        <?php if (!empty($attributes['headingScript'])) : ?>
          <span class="text-gold"><?php echo esc_html($attributes['headingScript']); ?></span>
        <?php endif; ?>
      </h2>
      <?php if (!empty($attributes['text'])) : ?>
        <p class="mt-5 max-w-md text-[15px] leading-relaxed text-cream/90"><?php echo esc_html($attributes['text']); ?></p>
      <?php endif; ?>
      <?php if ($details) : ?>
        <ul class="mt-6 flex flex-col gap-3 text-sm text-cream/90">
          <?php foreach ($details as $detail) : ?>
            <li class="flex items-start gap-3">
              <span class="mt-0.5 shrink-0 text-gold"><?php echo awards_gallery_contact_icon($detail['icon'] ?? '', 'h-3.5 w-3.5'); // phpcs:ignore — static SVG. ?></span>
              <span><?php echo esc_html($detail['text'] ?? ''); ?></span>
            </li>
          <?php endforeach; ?>
        </ul>
      <?php endif; ?>
    </div>

    <?php if ($map_src) : ?>
      <div class="reveal overflow-hidden border border-gold/40" style="--d:100ms">
        <iframe src="<?php echo esc_url($map_src); ?>" title="<?php echo esc_attr($attributes['mapTitle'] ?? ''); ?>" class="block aspect-[16/9] w-full border-0 lg:aspect-[528/280]" loading="lazy" referrerpolicy="no-referrer-when-downgrade" allowfullscreen></iframe>
      </div>
    <?php endif; ?>
  </div>
</section>
