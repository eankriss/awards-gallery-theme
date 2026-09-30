<?php
/**
 * Awards Gallery Collection — render.
 *
 * @var array $attributes
 */

if (!defined('ABSPATH')) {
    exit;
}

$bg      = awards_gallery_media_url($attributes, 'background', 'backgroundUrl');
$wrapper = ['class' => 'relative bg-ink-700 bg-cover bg-top py-20 lg:py-28'];
if (!empty($attributes['sectionId'])) {
    $wrapper['id'] = sanitize_title($attributes['sectionId']);
}
if ($bg) {
    $wrapper['style'] = 'background-image:url(' . esc_url($bg) . ')';
}
?>
<section <?php echo get_block_wrapper_attributes($wrapper); ?>>
  <div class="mx-auto max-w-shell px-5 sm:px-8 lg:px-20">
    <div class="reveal flex flex-col items-center text-center">
      <?php echo awards_gallery_eyebrow($attributes['eyebrow'] ?? ''); // phpcs:ignore ?>
      <h2 class="mt-8 text-4xl font-normal leading-tight sm:text-5xl lg:text-[56px]">
        <?php echo esc_html($attributes['heading'] ?? ''); ?>
        <?php if (!empty($attributes['headingScript'])) : ?>
          <span class="font-script text-5xl text-gold sm:text-6xl lg:text-[72px]"><?php echo esc_html($attributes['headingScript']); ?></span>
        <?php endif; ?>
      </h2>
      <?php if (!empty($attributes['text'])) : ?>
        <p class="mt-8 max-w-sm text-[15px] leading-relaxed text-cream"><?php echo esc_html($attributes['text']); ?></p>
      <?php endif; ?>
    </div>

    <div class="mt-14 grid grid-cols-1 gap-3 sm:grid-cols-2 lg:mt-20 lg:grid-cols-3">
      <?php foreach (($attributes['items'] ?? []) as $i => $item) :
          $img   = awards_gallery_media_url($item);
          $title = $item['title'] ?? '';
      ?>
        <a href="<?php echo esc_url(($item['url'] ?? '') ?: '#'); ?>" class="reveal group relative block overflow-hidden bg-ink" style="--d:<?php echo esc_attr(($i % 3) * 100); ?>ms">
          <?php if ($img) : ?>
            <img src="<?php echo esc_url($img); ?>" alt="<?php echo esc_attr($title); ?>" class="aspect-[418/540] w-full object-cover transition duration-700 group-hover:scale-105" loading="lazy">
          <?php else : ?>
            <div class="aspect-[418/540] w-full bg-ink-800"></div>
          <?php endif; ?>
          <div class="absolute inset-0 bg-gradient-to-t from-ink via-ink/10 to-transparent"></div>
          <div class="absolute inset-x-0 bottom-0 px-6 pb-8 transition-transform duration-500 group-hover:-translate-y-1.5">
            <?php if (!empty($item['kicker'])) : ?>
              <p class="text-xs uppercase tracking-[0.3em] text-gold sm:text-base"><?php echo esc_html($item['kicker']); ?></p>
            <?php endif; ?>
            <h3 class="mt-1 text-2xl font-bold text-cream lg:text-[28px]"><?php echo esc_html($title); ?></h3>
            <?php if (!empty($attributes['linkText'])) : ?>
              <span class="mt-3 inline-flex items-center gap-3 text-[11px] uppercase tracking-[0.24em] text-cream/80 transition-colors group-hover:text-gold"><?php echo esc_html($attributes['linkText']); ?> <span class="transition-transform duration-300 group-hover:translate-x-1">→</span></span>
            <?php endif; ?>
          </div>
        </a>
      <?php endforeach; ?>
    </div>

    <?php if (!empty($attributes['buttonText'])) : ?>
      <div class="reveal mt-16 flex justify-center">
        <a href="<?php echo esc_url($attributes['buttonUrl'] ?: '#'); ?>" class="btn-gold"><?php echo esc_html($attributes['buttonText']); ?></a>
      </div>
    <?php endif; ?>
  </div>
</section>
