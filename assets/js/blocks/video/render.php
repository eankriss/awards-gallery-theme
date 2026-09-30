<?php
/**
 * Awards Gallery Video — render.
 *
 * @var array $attributes
 */

if (!defined('ABSPATH')) {
    exit;
}

$autoplay = !empty($attributes['autoplay']);
$controls = !empty($attributes['controls']) || !$autoplay;
$file     = $attributes['video']['url'] ?? '';
$link     = trim($attributes['videoUrl'] ?? '');
$poster   = $attributes['poster']['url'] ?? '';
$mark     = !empty($attributes['showMark']) ? ($attributes['mark']['url'] ?? '') : '';
$embed    = !$file && $link ? awards_gallery_video_embed_url($link, $autoplay, $controls) : '';
$src      = $file ?: ($embed ? '' : $link);

$wrapper = ['class' => 'relative aspect-video overflow-hidden bg-black lg:aspect-[1440/990]'];
?>
<section <?php echo get_block_wrapper_attributes($wrapper); ?>>
  <?php if ($embed) : ?>
    <iframe
      src="<?php echo esc_url($embed); ?>"
      class="pointer-events-auto absolute left-1/2 top-1/2 h-[56.25vw] min-h-full w-[177.78vh] min-w-full -translate-x-1/2 -translate-y-1/2 border-0"
      title="<?php esc_attr_e('Video introduction', 'awards-gallery-theme'); ?>"
      allow="autoplay; fullscreen; picture-in-picture; encrypted-media"
      allowfullscreen
      loading="lazy"></iframe>
  <?php elseif ($src) : ?>
    <video
      class="absolute inset-0 h-full w-full object-cover"
      src="<?php echo esc_url($src); ?>"
      <?php echo $poster ? 'poster="' . esc_url($poster) . '"' : ''; ?>
      <?php echo $autoplay ? 'autoplay muted loop data-autoplay' : ''; ?>
      <?php echo $controls ? 'controls' : ''; ?>
      playsinline
      preload="metadata"></video>
  <?php elseif ($poster) : ?>
    <img src="<?php echo esc_url($poster); ?>" alt="" class="absolute inset-0 h-full w-full object-cover" loading="lazy">
  <?php elseif (defined('REST_REQUEST') && REST_REQUEST) : ?>
    <div class="absolute inset-0 flex items-center justify-center text-center text-sm uppercase tracking-[0.3em] text-cream/60">
      <?php esc_html_e('Select a video in the block settings →', 'awards-gallery-theme'); ?>
    </div>
  <?php endif; ?>

  <?php if ($mark) : ?>
    <img src="<?php echo esc_url($mark); ?>" alt="" aria-hidden="true" class="pointer-events-none absolute right-5 top-6 w-10 sm:right-8 sm:top-10 lg:w-[56px]" loading="lazy">
  <?php endif; ?>
</section>
