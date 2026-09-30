<?php
if (!defined('ABSPATH')) {
    exit;
}

get_header();
?>

<section class="relative isolate flex min-h-[calc(100vh-5rem)] items-center overflow-hidden py-20 lg:py-28">
    <img src="<?php echo esc_url(get_theme_file_uri('/assets/img/hero-bg.jpg')); ?>" alt="" class="absolute inset-0 -z-30 h-full w-full object-cover opacity-25">
    <div class="absolute inset-0 -z-20 bg-gradient-to-b from-ink/70 via-ink/60 to-ink"></div>
    <div class="absolute left-1/2 top-1/2 -z-10 aspect-square w-[620px] max-w-[140vw] -translate-x-1/2 -translate-y-1/2 animate-glow rounded-full bg-[radial-gradient(circle,rgba(218,147,40,0.22),transparent_65%)] lg:w-[900px]"></div>

    <?php // Oversized watermark numeral behind the heading. ?>
    <p class="pointer-events-none absolute left-1/2 top-1/2 -z-10 -translate-x-1/2 -translate-y-[62%] select-none text-[42vw] font-light leading-none text-transparent [-webkit-text-stroke:1px_rgba(218,147,40,0.18)] lg:text-[420px]" aria-hidden="true">404</p>

    <div class="mx-auto flex w-full max-w-3xl flex-col items-center px-5 text-center">
        <div class="animate-rise sm:hidden" style="animation-delay:.1s">
            <?php echo awards_gallery_eyebrow(__('Error 404', 'awards-gallery-theme')); // phpcs:ignore ?>
        </div>
        <div class="animate-rise hidden sm:block" style="animation-delay:.1s">
            <?php echo awards_gallery_eyebrow(__('Error 404 · Page Not Found', 'awards-gallery-theme')); // phpcs:ignore ?>
        </div>

        <h1 class="mt-8 flex flex-col items-center leading-none">
            <span class="animate-rise text-4xl font-normal sm:text-5xl lg:text-[64px]" style="animation-delay:.25s"><?php esc_html_e('This Page Has', 'awards-gallery-theme'); ?></span>
            <span class="animate-rise mt-1 pb-2 font-script text-6xl text-gold sm:text-7xl lg:text-[104px]" style="animation-delay:.4s"><?php esc_html_e('Gone Missing', 'awards-gallery-theme'); ?></span>
        </h1>

        <p class="animate-rise mt-6 max-w-xl font-outfit text-lg leading-relaxed text-cream/90" style="animation-delay:.55s">
            <?php esc_html_e('The page you’re looking for may have been moved, renamed, or no longer exists. Let’s get you back to the winners’ circle.', 'awards-gallery-theme'); ?>
        </p>

        <div class="animate-rise mt-10 flex w-full justify-center" style="animation-delay:.7s">
            <a href="<?php echo esc_url(home_url('/')); ?>" class="btn-gold w-full sm:w-auto"><?php esc_html_e('Back to Home', 'awards-gallery-theme'); ?></a>
        </div>
    </div>
</section>

<?php
get_footer();
