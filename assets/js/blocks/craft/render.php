<?php
/**
 * Awards Gallery Behind the Craft — render.
 *
 * @var array $attributes
 */

if (!defined('ABSPATH')) {
    exit;
}

$bg      = awards_gallery_media_url($attributes, 'background', 'backgroundUrl');
$wrapper = ['class' => 'bg-ink-800 bg-cover bg-center py-20 lg:py-24'];
if (!empty($attributes['sectionId'])) {
    $wrapper['id'] = sanitize_title($attributes['sectionId']);
}
if ($bg) {
    $wrapper['style'] = 'background-image:url(' . esc_url($bg) . ')';
}
?>
<section <?php echo get_block_wrapper_attributes($wrapper); ?>>
  <div class="mx-auto max-w-shell px-5 sm:px-8 lg:px-20">
    <div class="reveal">
      <?php echo awards_gallery_eyebrow($attributes['eyebrow'] ?? ''); // phpcs:ignore ?>
    </div>
    <div class="mt-12 grid grid-cols-1 gap-3 md:grid-cols-3 lg:mt-20">
      <?php foreach (($attributes['items'] ?? []) as $i => $item) :
          $img   = awards_gallery_media_url($item);
          $title = $item['title'] ?? '';
          $url   = trim($item['url'] ?? '');
          // No link → a plain, non-clickable card. Without the `group` class the
          // hover zoom/lift stays off too, so it doesn't look clickable.
          $tag   = $url ? 'a' : 'div';
      ?>
        <<?php echo $tag; ?><?php echo $url ? ' href="' . esc_url($url) . '"' : ''; ?> class="reveal relative block overflow-hidden bg-ink<?php echo $url ? ' group' : ''; ?>" style="--d:<?php echo esc_attr(($i % 3) * 100); ?>ms">
          <?php if ($img) : ?>
            <img src="<?php echo esc_url($img); ?>" alt="<?php echo esc_attr($title); ?>" class="aspect-[418/540] w-full object-cover transition duration-700 group-hover:scale-105" loading="lazy">
          <?php else : ?>
            <div class="aspect-[418/540] w-full bg-ink"></div>
          <?php endif; ?>
          <div class="absolute inset-0 bg-gradient-to-t from-ink/90 via-ink/5 to-transparent"></div>
          <div class="absolute inset-x-0 bottom-0 px-6 pb-8 transition-transform duration-500 group-hover:-translate-y-1.5">
            <?php if (!empty($item['kicker'])) : ?>
              <p class="text-xs uppercase tracking-[0.3em] text-gold sm:text-base"><?php echo esc_html($item['kicker']); ?></p>
            <?php endif; ?>
            <h3 class="mt-1 text-2xl font-bold text-cream lg:text-[28px]"><?php echo esc_html($title); ?></h3>
          </div>
        </<?php echo $tag; ?>>
      <?php endforeach; ?>
    </div>
  </div>
</section>
