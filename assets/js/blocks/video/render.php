<?php
/**
 * Awards Gallery Video — render.
 *
 * @var array $attributes
 */

if (!defined('ABSPATH')) {
    exit;
}

$overlay  = !empty($attributes['playOverlay']);
$autoplay = !$overlay && !empty($attributes['autoplay']);
$controls = !empty($attributes['controls']) || !$autoplay;
$sound    = $autoplay && ($attributes['soundToggle'] ?? true);
$file     = $attributes['video']['url'] ?? '';
$link     = trim($attributes['videoUrl'] ?? '');
$poster   = $attributes['poster']['url'] ?? '';
$poster_id = (int) ($attributes['poster']['id'] ?? 0);
$mark     = !empty($attributes['showMark']) ? ($attributes['mark']['url'] ?? '') : '';
$embed    = !$file && $link ? awards_gallery_video_embed_url($link, $autoplay, $controls, $sound, $overlay) : '';
$src      = $file ?: ($embed ? '' : $link);
$overlay  = $overlay && ($embed || $src);
$try_sound = $sound && $src && !empty($attributes['trySound']);
// Click-to-play MP4s get the themed player instead of the browser's native controls.
$custom   = $overlay && $src;

// overflow-clip (not hidden) so the sticky sound button can track the viewport.
$wrapper = ['class' => 'relative aspect-video overflow-clip bg-black'];
if ($sound) {
    $wrapper['data-video-sound'] = $embed ? (str_contains($embed, 'vimeo') ? 'vimeo' : 'youtube') : 'file';
}

// Click to play: the player sits in a <template> and is only injected (and downloaded) on click.
$cover = '';
if ($overlay) {
    $cover_class = 'absolute inset-0 h-full w-full object-cover';
    if ($poster_id) {
        $cover = wp_get_attachment_image($poster_id, 'full', false, ['class' => $cover_class, 'alt' => '']);
    } else {
        $cover_url = $poster ?: (($yt = awards_gallery_youtube_id($link)) && !$file ? 'https://i.ytimg.com/vi/' . $yt . '/hqdefault.jpg' : '');
        $cover     = $cover_url ? '<img src="' . esc_url($cover_url) . '" alt="" class="' . $cover_class . '" decoding="async">' : '';
    }
}

ob_start();
if ($embed) : ?>
    <iframe
      src="<?php echo esc_url($embed); ?>"
      class="absolute inset-0 h-full w-full border-0<?php echo ($autoplay && !$controls) ? ' pointer-events-none' : ''; ?>"
      title="<?php esc_attr_e('Video introduction', 'awards-gallery-theme'); ?>"
      allow="autoplay; fullscreen; picture-in-picture; encrypted-media"
      allowfullscreen
      <?php echo $overlay ? '' : 'loading="lazy"'; ?>></iframe>
<?php elseif ($src) : ?>
    <video
      class="absolute inset-0 h-full w-full object-cover"
      src="<?php echo esc_url($src); ?>"
      <?php echo $poster ? 'poster="' . esc_url($poster) . '"' : ''; ?>
      <?php echo $autoplay ? ($try_sound ? 'autoplay loop data-autoplay data-try-sound' : 'autoplay muted loop data-autoplay') : ''; ?>
      <?php echo ($controls && !$custom) ? 'controls' : ''; ?>
      playsinline
      preload="<?php echo $overlay ? 'auto' : 'metadata'; ?>"></video>
  <?php if ($custom) : ?>
    <div class="ag-player absolute inset-0 z-10 flex flex-col justify-end" data-player data-state="paused">
      <button type="button" class="ag-player__big absolute left-1/2 top-1/2 flex h-16 w-16 -translate-x-1/2 -translate-y-1/2 items-center justify-center rounded-full bg-gold text-ink shadow-[0_10px_40px_rgba(0,0,0,0.45)] transition duration-300 hover:scale-110 hover:bg-gold-light focus-visible:outline focus-visible:outline-4 focus-visible:outline-cream/70 lg:h-24 lg:w-24" aria-label="<?php esc_attr_e('Play', 'awards-gallery-theme'); ?>" data-player-big>
        <svg class="ml-1 h-6 w-6 lg:h-9 lg:w-9" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M7 4.5v15a1 1 0 0 0 1.5.86l12.5-7.5a1 1 0 0 0 0-1.72L8.5 3.64A1 1 0 0 0 7 4.5z"/></svg>
      </button>
      <div class="ag-player__bar bg-gradient-to-t from-ink/90 via-ink/50 to-transparent px-3 pb-2 pt-12 transition-opacity duration-300 sm:px-8 sm:pb-5" data-player-bar>
        <input type="range" class="ag-range ag-range--seek w-full" min="0" max="1000" step="1" value="0" aria-label="<?php esc_attr_e('Seek', 'awards-gallery-theme'); ?>" data-player-seek>
        <div class="mt-1 flex items-center gap-1 sm:mt-2 sm:gap-2">
          <button type="button" class="flex h-10 w-10 items-center justify-center rounded-full text-cream transition hover:bg-cream/10 hover:text-gold focus-visible:outline focus-visible:outline-2 focus-visible:outline-gold" aria-label="<?php esc_attr_e('Play', 'awards-gallery-theme'); ?>" data-label-play="<?php esc_attr_e('Play', 'awards-gallery-theme'); ?>" data-label-pause="<?php esc_attr_e('Pause', 'awards-gallery-theme'); ?>" data-player-toggle>
            <svg class="h-5 w-5" data-icon="play" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M7 4.5v15a1 1 0 0 0 1.5.86l12.5-7.5a1 1 0 0 0 0-1.72L8.5 3.64A1 1 0 0 0 7 4.5z"/></svg>
            <svg class="h-5 w-5" data-icon="pause" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><rect x="6" y="4.5" width="4" height="15" rx="1"/><rect x="14" y="4.5" width="4" height="15" rx="1"/></svg>
          </button>
          <span class="font-outfit text-xs tabular-nums tracking-wider text-cream/80 sm:text-sm"><span data-player-time>0:00</span> <span class="text-cream/40">/</span> <span data-player-duration>0:00</span></span>
          <div class="ml-auto flex items-center gap-1 sm:gap-2">
            <button type="button" class="flex h-10 w-10 items-center justify-center rounded-full text-cream transition hover:bg-cream/10 hover:text-gold focus-visible:outline focus-visible:outline-2 focus-visible:outline-gold" aria-label="<?php esc_attr_e('Mute', 'awards-gallery-theme'); ?>" data-label-mute="<?php esc_attr_e('Mute', 'awards-gallery-theme'); ?>" data-label-unmute="<?php esc_attr_e('Unmute', 'awards-gallery-theme'); ?>" data-player-mute>
              <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" data-icon="vol"><path d="M11 5 6 9H3v6h3l5 4V5z"/><path d="M15.5 8.5a5 5 0 0 1 0 7M19 5a10 10 0 0 1 0 14"/></svg>
              <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" data-icon="muted"><path d="M11 5 6 9H3v6h3l5 4V5z"/><path d="m22 9-6 6M16 9l6 6"/></svg>
            </button>
            <input type="range" class="ag-range hidden w-20 sm:block lg:w-24" min="0" max="1" step="0.05" value="1" aria-label="<?php esc_attr_e('Volume', 'awards-gallery-theme'); ?>" data-player-volume>
            <button type="button" class="flex h-10 w-10 items-center justify-center rounded-full text-cream transition hover:bg-cream/10 hover:text-gold focus-visible:outline focus-visible:outline-2 focus-visible:outline-gold" aria-label="<?php esc_attr_e('Full screen', 'awards-gallery-theme'); ?>" data-label-enter="<?php esc_attr_e('Full screen', 'awards-gallery-theme'); ?>" data-label-exit="<?php esc_attr_e('Exit full screen', 'awards-gallery-theme'); ?>" data-player-fs>
              <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" data-icon="fs-enter"><path d="M4 9V4h5M20 9V4h-5M4 15v5h5M20 15v5h-5"/></svg>
              <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" data-icon="fs-exit"><path d="M9 4v5H4M15 4v5h5M9 20v-5H4M15 20v-5h5"/></svg>
            </button>
          </div>
        </div>
      </div>
    </div>
  <?php endif; ?>
<?php endif;
$player = ob_get_clean();
?>
<section <?php echo get_block_wrapper_attributes($wrapper); ?>>
  <?php if ($overlay) : ?>
    <template data-video-player><?php echo $player; ?></template>
    <button
      type="button"
      class="group absolute inset-0 z-10 flex h-full w-full items-center justify-center focus-visible:outline-none"
      aria-label="<?php esc_attr_e('Play video', 'awards-gallery-theme'); ?>"
      data-video-play>
      <?php echo $cover; ?>
      <span class="absolute inset-0 bg-ink/30 transition group-hover:bg-ink/15" aria-hidden="true"></span>
      <span class="relative flex h-16 w-16 items-center justify-center rounded-full bg-gold text-ink shadow-[0_10px_40px_rgba(0,0,0,0.45)] transition duration-300 group-hover:scale-110 group-hover:bg-gold-light group-focus-visible:ring-4 group-focus-visible:ring-cream/70 lg:h-24 lg:w-24" aria-hidden="true">
        <svg class="ml-1 h-6 w-6 lg:h-9 lg:w-9" viewBox="0 0 24 24" fill="currentColor"><path d="M7 4.5v15a1 1 0 0 0 1.5.86l12.5-7.5a1 1 0 0 0 0-1.72L8.5 3.64A1 1 0 0 0 7 4.5z"/></svg>
      </span>
    </button>
  <?php elseif ($player) : ?>
    <?php echo $player; ?>
  <?php elseif ($poster) : ?>
    <img src="<?php echo esc_url($poster); ?>" alt="" class="absolute inset-0 h-full w-full object-cover" loading="lazy">
  <?php elseif (defined('REST_REQUEST') && REST_REQUEST) : ?>
    <div class="absolute inset-0 flex items-center justify-center text-center text-sm uppercase tracking-[0.3em] text-cream/60">
      <?php esc_html_e('Select a video in the block settings →', 'awards-gallery-theme'); ?>
    </div>
  <?php endif; ?>

  <?php if ($sound && $player) : ?>
    <?php // Phones and tablets show the speaker icon only, like the Hero block. ?>
    <div class="pointer-events-none absolute inset-0 z-10 flex flex-col justify-end">
      <div class="sticky bottom-0 flex justify-end p-5 sm:p-8">
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

  <?php if ($mark) : ?>
    <img src="<?php echo esc_url($mark); ?>" alt="" aria-hidden="true" class="pointer-events-none absolute right-5 top-6 z-20 w-10 sm:right-8 sm:top-10 lg:w-[56px]" loading="lazy">
  <?php endif; ?>
</section>
