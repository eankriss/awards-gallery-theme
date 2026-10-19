<?php
if (!defined('ABSPATH')) { exit; }

$items = array_values(array_filter($attributes['items'] ?? [], static fn($item) => !empty($item['image']['url'])));
if (!$items) { return; }
$speed = max(8, min(120, (int) ($attributes['speed'] ?? 32)));
$width = max(100, min(420, (int) ($attributes['cardWidth'] ?? 180)));
$height = max(120, min(520, (int) ($attributes['cardHeight'] ?? 250)));
$gap = max(8, min(48, (int) ($attributes['gap'] ?? 18)));
$pause = !empty($attributes['pauseOnHover']);
$direction = ($attributes['direction'] ?? 'left') === 'right' ? 'right' : 'left';
$width_mode = ($attributes['widthMode'] ?? 'max') === 'full' ? 'full' : 'max';
$autoplay = !isset($attributes['autoplay']) || !empty($attributes['autoplay']);
$loop_items = $items;
while ($autoplay && count($loop_items) < 10) {
    $loop_items = array_merge($loop_items, $items);
}
$track = function ($hidden = false) use ($items, $loop_items, $autoplay) {
    $track_items = $autoplay ? $loop_items : $items;
    echo '<div class="awards-image-carousel__group"' . ($hidden ? ' aria-hidden="true"' : '') . '>';
    foreach ($track_items as $item) {
        $image = $item['image']['url'] ?? '';
        $alt = !empty($item['image']['id']) ? get_post_meta((int) $item['image']['id'], '_wp_attachment_image_alt', true) : '';
        printf('<div class="awards-image-carousel__card"><img src="%s" alt="%s" loading="lazy"></div>', esc_url($image), esc_attr($alt));
    }
    echo '</div>';
};
?>
<section <?php echo get_block_wrapper_attributes(['class' => 'awards-image-carousel awards-image-carousel--' . $width_mode . ($autoplay ? '' : ' awards-image-carousel--static')]); ?> style="--carousel-speed:<?php echo esc_attr($speed); ?>s;--carousel-width:<?php echo esc_attr($width); ?>px;--carousel-height:<?php echo esc_attr($height); ?>px;--carousel-gap:<?php echo esc_attr($gap); ?>px;--carousel-direction:<?php echo esc_attr($direction); ?>;">
  <div class="awards-image-carousel__inner">
    <?php if (!empty($attributes['eyebrow']) || !empty($attributes['title']) || !empty($attributes['subheading'])) : ?>
      <div class="awards-image-carousel__intro">
        <?php echo awards_gallery_eyebrow($attributes['eyebrow'] ?? ''); // phpcs:ignore ?>
        <?php if (!empty($attributes['title'])) : ?><h2><?php echo esc_html($attributes['title']); ?><?php if (!empty($attributes['titleAccent'])) : ?> <span class="text-gold"><?php echo esc_html($attributes['titleAccent']); ?></span><?php endif; ?></h2><?php endif; ?>
        <?php if (!empty($attributes['subheading'])) : ?><p class="awards-image-carousel__subheading"><?php echo esc_html($attributes['subheading']); ?></p><?php endif; ?>
      </div>
    <?php endif; ?>
    <div class="awards-image-carousel__viewport<?php echo $pause && $autoplay ? ' is-pausable' : ''; ?>" aria-label="<?php esc_attr_e('Image carousel', 'awards-gallery-theme'); ?>">
      <div class="awards-image-carousel__track"><?php $track(); $track(true); ?></div>
    </div>
    <?php if (!empty($attributes['note'])) : ?>
      <p class="awards-image-carousel__note"><?php echo esc_html($attributes['note']); ?></p>
    <?php endif; ?>
  </div>
</section>
