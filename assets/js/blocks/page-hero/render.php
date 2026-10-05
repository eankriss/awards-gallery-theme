<?php
/**
 * Awards Gallery Page Hero — render.
 *
 * Shorter hero for subpages (About, Contact…). The homepage uses the
 * full-height Awards Gallery Hero block instead.
 *
 * @var array $attributes
 */

if (!defined('ABSPATH')) {
    exit;
}

$bg      = awards_gallery_media_url($attributes);
$bg_type = $attributes['bgType'] ?? 'image';
$wrapper = ['class' => 'relative isolate flex min-h-[420px] items-center overflow-hidden pb-16 pt-32 lg:min-h-[555px] lg:pb-20 lg:pt-36'];

// Video background: same sources as the Video block (uploaded MP4, or a YouTube / Vimeo / .mp4 link), always muted, looping autoplay.
$video_src = $video_embed = $poster = '';
$sound     = false;
if ($bg_type === 'video') {
    $file        = $attributes['video']['url'] ?? '';
    $link        = trim($attributes['videoUrl'] ?? '');
    $sound       = !empty($attributes['soundToggle']);
    $video_embed = !$file && $link ? awards_gallery_video_embed_url($link, true, false, $sound) : '';
    $video_src   = $file ?: ($video_embed ? '' : $link);
    $poster      = $attributes['poster']['url'] ?? '';
    $sound       = $sound && ($video_embed || $video_src);
    if ($sound) {
        $wrapper['data-video-sound'] = $video_embed ? (str_contains($video_embed, 'vimeo') ? 'vimeo' : 'youtube') : 'file';
    }
}

// Slideshow background: needs two or more images to cycle; one image behaves like a single image.
$slides = $bg_type === 'slideshow'
    ? array_values(array_filter($attributes['slides'] ?? [], fn($s) => !empty($s['url'])))
    : [];
$interval = max(3, min(15, (float) ($attributes['slideInterval'] ?? 6)));
$effect   = in_array($attributes['slideEffect'] ?? '', ['fade', 'slide', 'slide-up', 'zoom', 'blur', 'cut'], true) ? $attributes['slideEffect'] : 'fade';
$speed    = $effect === 'cut' ? 0 : max(0.3, min(2.5, (float) ($attributes['slideSpeed'] ?? 1.5)));
?>
<section <?php echo get_block_wrapper_attributes($wrapper); ?>>
  <?php if ($video_embed || $video_src) : ?>
    <div class="ag-page-hero-media absolute inset-0 -z-20 opacity-50" aria-hidden="true">
      <?php if ($poster) : ?>
        <img src="<?php echo esc_url($poster); ?>" alt="" class="absolute inset-0 h-full w-full object-cover" fetchpriority="high">
      <?php endif; ?>
      <?php if ($video_embed) : ?>
        <iframe src="<?php echo esc_url($video_embed); ?>" class="ag-page-hero-embed pointer-events-none border-0" title="<?php esc_attr_e('Background video', 'awards-gallery-theme'); ?>" allow="autoplay; fullscreen; picture-in-picture; encrypted-media" tabindex="-1"></iframe>
      <?php else : ?>
        <video class="absolute inset-0 h-full w-full object-cover" src="<?php echo esc_url($video_src); ?>" <?php echo $poster ? 'poster="' . esc_url($poster) . '"' : ''; ?> autoplay muted loop playsinline preload="auto" data-background></video>
      <?php endif; ?>
    </div>
  <?php elseif (count($slides) > 1) : ?>
    <div class="ag-page-hero-slides absolute inset-0 -z-20 overflow-hidden opacity-50" data-slideshow="<?php echo esc_attr($interval); ?>" data-effect="<?php echo esc_attr($effect); ?>" data-speed="<?php echo esc_attr($speed); ?>"<?php echo !empty($attributes['slideZoom']) ? ' data-zoom' : ''; ?> style="--ag-speed:<?php echo esc_attr($speed); ?>s;--ag-slide-zoom:<?php echo esc_attr($interval + $speed); ?>s" aria-hidden="true">
      <?php foreach ($slides as $i => $slide) :
          $attrs = ['class' => 'h-full w-full object-cover', 'alt' => ''];
          $attrs += $i === 0 ? ['fetchpriority' => 'high', 'loading' => 'eager'] : ['loading' => 'lazy', 'decoding' => 'async'];
          ?>
        <div class="ag-page-hero-slide<?php echo $i === 0 ? ' is-active' : ''; ?>" data-slide>
          <?php
          if (!empty($slide['id']) && ($img = wp_get_attachment_image((int) $slide['id'], 'full', false, $attrs))) {
              echo $img; // phpcs:ignore
          } else {
              echo '<img src="' . esc_url($slide['url']) . '"';
              foreach ($attrs as $k => $v) {
                  echo ' ' . esc_attr($k) . '="' . esc_attr($v) . '"';
              }
              echo '>';
          }
          ?>
        </div>
      <?php endforeach; ?>
    </div>
  <?php else :
      // Single image — also the fallback when a video or slideshow has nothing to show.
      $bg = $slides[0]['url'] ?? ($bg_type === 'video' && $poster ? $poster : $bg);
      if ($bg) : ?>
    <img src="<?php echo esc_url($bg); ?>" alt="" class="absolute inset-0 -z-20 h-full w-full scale-105 object-cover opacity-50 blur-[2px]" fetchpriority="high">
  <?php endif;
  endif; ?>
  <div class="absolute inset-0 -z-10 bg-gradient-to-b from-ink/70 via-ink/55 to-ink/80"></div>

  <div class="mx-auto flex w-full max-w-shell flex-col items-center px-5 text-center sm:px-8 lg:px-14">
    <?php if (!empty($attributes['eyebrow'])) : ?>
      <div class="animate-rise" style="animation-delay:.1s">
        <?php echo awards_gallery_eyebrow($attributes['eyebrow']); // phpcs:ignore ?>
      </div>
    <?php endif; ?>

    <?php if (!empty($attributes['heading']) || !empty($attributes['headingScript'])) : ?>
      <h1 class="mt-6 flex flex-col items-center font-display font-normal leading-none">
        <?php if (!empty($attributes['heading'])) : ?>
          <span class="animate-rise text-4xl text-cream sm:text-5xl lg:text-[56px]" style="animation-delay:.25s"><?php echo esc_html($attributes['heading']); ?></span>
        <?php endif; ?>
        <?php if (!empty($attributes['headingScript'])) : ?>
          <span class="animate-rise pb-2 pt-1 text-5xl text-gold sm:text-6xl lg:text-[80px]" style="animation-delay:.4s"><?php echo esc_html($attributes['headingScript']); ?></span>
        <?php endif; ?>
      </h1>
    <?php endif; ?>

    <?php if (!empty($attributes['text'])) : ?>
      <p class="animate-rise mt-5 max-w-xl font-outfit text-base leading-relaxed text-cream lg:text-lg" style="animation-delay:.55s"><?php echo esc_html($attributes['text']); ?></p>
    <?php endif; ?>
  </div>

  <?php if ($sound) : ?>
    <div class="absolute bottom-5 right-5 z-10 sm:bottom-8 sm:right-8">
      <button
        type="button"
        class="group relative flex h-11 min-w-11 items-center justify-center gap-2 rounded-full border border-cream/30 bg-ink/70 px-4 text-cream backdrop-blur transition hover:border-gold hover:text-gold focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-gold aria-pressed:px-0"
        aria-pressed="false"
        aria-label="<?php esc_attr_e('Turn sound on', 'awards-gallery-theme'); ?>"
        data-label-on="<?php esc_attr_e('Turn sound off', 'awards-gallery-theme'); ?>"
        data-label-off="<?php esc_attr_e('Turn sound on', 'awards-gallery-theme'); ?>"
        data-sound-toggle>
        <span class="absolute inset-0 rounded-full border-2 border-gold opacity-0 motion-safe:animate-sound-hint group-aria-pressed:hidden" aria-hidden="true"></span>
        <svg class="h-5 w-5 shrink-0 group-aria-pressed:hidden" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M11 5 6 9H3v6h3l5 4V5z"/><path d="m22 9-6 6M16 9l6 6"/></svg>
        <svg class="hidden h-5 w-5 shrink-0 group-aria-pressed:block" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M11 5 6 9H3v6h3l5 4V5z"/><path d="M15.5 8.5a5 5 0 0 1 0 7M19 5a10 10 0 0 1 0 14"/></svg>
        <span class="whitespace-nowrap text-xs font-medium uppercase tracking-[0.2em] group-aria-pressed:hidden"><?php esc_html_e('Turn sound on', 'awards-gallery-theme'); ?></span>
      </button>
    </div>
  <?php endif; ?>
</section>
