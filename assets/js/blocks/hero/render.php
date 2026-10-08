<?php
/**
 * Awards Gallery Hero — render.
 *
 * @var array $attributes
 */

if (!defined('ABSPATH')) {
    exit;
}

$bg      = awards_gallery_media_url($attributes);
$bg_type = $attributes['bgType'] ?? 'image';
// overflow-clip (not hidden) so the sticky sound button can track the viewport.
$wrapper = ['class' => 'relative isolate flex min-h-[640px] items-center overflow-clip pb-16 pt-32 lg:min-h-[900px]'];

// Video background: same sources as the Video block (uploaded MP4, or a YouTube / Vimeo / .mp4 link), always muted, looping autoplay.
$video_src = $video_embed = $poster = '';
$sound     = false;
if ($bg_type === 'video') {
    $file        = $attributes['video']['url'] ?? '';
    $link        = trim($attributes['videoUrl'] ?? '');
    $sound       = !empty($attributes['soundToggle']);
    $video_embed = !$file && $link ? awards_gallery_video_embed_url($link, true, false, $sound) : '';
    $video_src   = $file ?: ($video_embed ? '' : awards_gallery_video_file_url($link));
    $poster      = $attributes['poster']['url'] ?? '';
    $sound       = $sound && ($video_embed || $video_src);
    if ($sound) {
        $wrapper['data-video-sound'] = $video_embed ? (str_contains($video_embed, 'vimeo') ? 'vimeo' : 'youtube') : 'file';
    }
    // Smaller desktops: a fixed 900px height crops a 16:9 video hard at the sides, so the
    // height follows the video's ratio (56.25vw) up to 1440px, where the design's 900px returns.
    if ($video_embed || $video_src) {
        $wrapper['class'] = str_replace('lg:min-h-[900px]', 'lg:min-h-[56.25vw] min-[1440px]:min-h-[900px]', $wrapper['class']);
    }
}

// Slideshow background: needs two or more images to cycle; one image behaves like a single image.
$slides = $bg_type === 'slideshow'
    ? array_values(array_filter($attributes['slides'] ?? [], fn($s) => !empty($s['url'])))
    : [];
$interval = max(3, min(15, (float) ($attributes['slideInterval'] ?? 6)));
$effect   = in_array($attributes['slideEffect'] ?? '', ['fade', 'slide', 'slide-up', 'zoom', 'blur', 'cut'], true) ? $attributes['slideEffect'] : 'fade';
$speed    = $effect === 'cut' ? 0 : max(0.3, min(2.5, (float) ($attributes['slideSpeed'] ?? 1.5)));

// "Get a Quote" emails the address from Customizer → Contact & Footer unless a URL is set.
$secondary_url = $attributes['secondaryUrl'] ?? '';
if (!$secondary_url && awards_gallery_contact('ag_email')) {
    $secondary_url = 'mailto:' . awards_gallery_contact('ag_email');
}
?>
<section <?php echo get_block_wrapper_attributes($wrapper); ?>>
  <?php if ($video_embed || $video_src) : ?>
    <div class="ag-hero-media absolute inset-0 -z-20 opacity-60" aria-hidden="true">
      <?php if ($poster) : ?>
        <img src="<?php echo esc_url($poster); ?>" alt="" class="absolute inset-0 h-full w-full object-cover" fetchpriority="high">
      <?php endif; ?>
      <?php if ($video_embed) : ?>
        <iframe src="<?php echo esc_url($video_embed); ?>" class="ag-hero-embed pointer-events-none border-0" title="<?php esc_attr_e('Background video', 'awards-gallery-theme'); ?>" allow="autoplay; fullscreen; picture-in-picture; encrypted-media" tabindex="-1"></iframe>
      <?php else : ?>
        <video class="absolute inset-0 h-full w-full object-cover" src="<?php echo esc_url($video_src); ?>" <?php echo $poster ? 'poster="' . esc_url($poster) . '"' : ''; ?> autoplay muted loop playsinline preload="auto" data-background></video>
      <?php endif; ?>
    </div>
  <?php elseif (count($slides) > 1) : ?>
    <div class="ag-hero-slides absolute inset-0 -z-20 overflow-hidden opacity-60" data-slideshow="<?php echo esc_attr($interval); ?>" data-effect="<?php echo esc_attr($effect); ?>" data-speed="<?php echo esc_attr($speed); ?>"<?php echo !empty($attributes['slideZoom']) ? ' data-zoom' : ''; ?> style="--ag-speed:<?php echo esc_attr($speed); ?>s;--ag-slide-zoom:<?php echo esc_attr($interval + $speed); ?>s" aria-hidden="true">
      <?php foreach ($slides as $i => $slide) :
          $attrs = ['class' => 'h-full w-full object-cover', 'alt' => ''];
          $attrs += $i === 0 ? ['fetchpriority' => 'high', 'loading' => 'eager'] : ['loading' => 'lazy', 'decoding' => 'async'];
          ?>
        <div class="ag-hero-slide<?php echo $i === 0 ? ' is-active' : ''; ?>" data-slide>
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
    <img src="<?php echo esc_url($bg); ?>" alt="" class="absolute inset-0 -z-20 h-full w-full object-cover opacity-60" fetchpriority="high">
  <?php endif;
  endif; ?>
  <div class="absolute inset-0 -z-10 bg-gradient-to-b from-ink/60 via-ink/40 to-ink/70"></div>

  <div class="mx-auto flex w-full max-w-shell flex-col items-center px-5 text-center sm:px-8 lg:px-14">
    <?php if (!empty($attributes['eyebrow'])) : ?>
      <div class="animate-rise" style="animation-delay:.1s">
        <?php echo awards_gallery_eyebrow($attributes['eyebrow']); // phpcs:ignore ?>
      </div>
    <?php endif; ?>

    <?php // Phones: the size follows the screen width (capped at the old 60px / 72px) so longer words still fit on one line. ?>
    <h1 class="mt-10 flex flex-col items-center font-display font-normal leading-none">
      <?php if (!empty($attributes['heading'])) : ?>
        <span class="animate-rise text-[clamp(2.25rem,12vw,3.75rem)] text-cream sm:text-7xl lg:text-[96px]" style="animation-delay:.25s"><?php echo esc_html($attributes['heading']); ?></span>
      <?php endif; ?>
      <?php if (!empty($attributes['headingScript'])) : ?>
        <span class="animate-rise -mt-2 pb-4 text-[clamp(2.5rem,14vw,4.5rem)] text-gold sm:text-8xl lg:-mt-6 lg:text-[150px]" style="animation-delay:.4s"><?php echo esc_html($attributes['headingScript']); ?></span>
      <?php endif; ?>
    </h1>

    <?php if (!empty($attributes['text'])) : ?>
      <p class="animate-rise mt-6 max-w-2xl font-outfit text-lg leading-relaxed text-cream lg:text-xl" style="animation-delay:.55s"><?php echo esc_html($attributes['text']); ?></p>
    <?php endif; ?>
    <?php if (!empty($attributes['tagline'])) : ?>
      <p class="animate-rise mt-8 text-xs uppercase tracking-[0.3em] text-gold sm:text-[15px]" style="animation-delay:.65s"><?php echo esc_html($attributes['tagline']); ?></p>
    <?php endif; ?>

    <?php if (!empty($attributes['primaryText']) || !empty($attributes['secondaryText'])) : ?>
      <?php // Phones: both buttons share one row at a smaller size; from sm up they use the full button size. ?>
      <div class="animate-rise mt-10 flex w-full gap-3 sm:mt-12 sm:w-auto sm:gap-4" style="animation-delay:.8s">
        <?php if (!empty($attributes['primaryText'])) : ?>
          <a href="<?php echo esc_url($attributes['primaryUrl'] ?: '#'); ?>" class="btn-gold whitespace-nowrap max-sm:h-12 max-sm:min-w-0 max-sm:flex-1 max-sm:px-2 max-sm:text-[11px] max-sm:tracking-[0.15em] max-[359px]:text-[10px] max-[359px]:tracking-[0.1em]"><?php echo esc_html($attributes['primaryText']); ?></a>
        <?php endif; ?>
        <?php if (!empty($attributes['secondaryText'])) : ?>
          <a href="<?php echo esc_url($secondary_url ?: '#'); ?>" class="btn-outline whitespace-nowrap max-sm:h-12 max-sm:min-w-0 max-sm:flex-1 max-sm:px-2 max-sm:text-[11px] max-sm:tracking-[0.15em] max-[359px]:text-[10px] max-[359px]:tracking-[0.1em]"><?php echo esc_html($attributes['secondaryText']); ?></a>
        <?php endif; ?>
      </div>
    <?php endif; ?>
  </div>

  <?php if ($sound) : ?>
    <?php // Sticky like the Video block's button: stays in the bottom corner of the screen while the hero is in view. Phones and tablets show the speaker icon only. ?>
    <div class="pointer-events-none absolute inset-0 z-10 flex flex-col justify-end">
      <div class="sticky bottom-0 flex justify-end px-4 pb-3 sm:p-8">
        <button
          type="button"
          class="group pointer-events-auto relative flex h-10 min-w-10 items-center lg:h-11 lg:min-w-11 justify-center gap-2 rounded-full border border-cream/30 bg-ink/70 px-0 text-cream backdrop-blur transition hover:border-gold hover:text-gold focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-gold lg:px-4 lg:aria-pressed:px-0"
          aria-pressed="false"
          aria-label="<?php esc_attr_e('Turn sound on', 'awards-gallery-theme'); ?>"
          data-label-on="<?php esc_attr_e('Turn sound off', 'awards-gallery-theme'); ?>"
          data-label-off="<?php esc_attr_e('Turn sound on', 'awards-gallery-theme'); ?>"
          data-sound-toggle>
          <span class="absolute inset-0 rounded-full border-2 border-gold opacity-0 motion-safe:animate-sound-hint group-aria-pressed:hidden" aria-hidden="true"></span>
          <svg class="h-[18px] w-[18px] shrink-0 lg:h-5 lg:w-5 group-aria-pressed:hidden" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M11 5 6 9H3v6h3l5 4V5z"/><path d="m22 9-6 6M16 9l6 6"/></svg>
          <svg class="hidden h-[18px] w-[18px] shrink-0 lg:h-5 lg:w-5 group-aria-pressed:block" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M11 5 6 9H3v6h3l5 4V5z"/><path d="M15.5 8.5a5 5 0 0 1 0 7M19 5a10 10 0 0 1 0 14"/></svg>
          <span class="hidden whitespace-nowrap text-xs font-medium uppercase tracking-[0.2em] lg:inline group-aria-pressed:hidden" aria-hidden="true"><?php esc_html_e('Turn sound on', 'awards-gallery-theme'); ?></span>
        </button>
      </div>
    </div>
  <?php endif; ?>
</section>
