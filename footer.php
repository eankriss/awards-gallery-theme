<?php
if (!defined('ABSPATH')) {
    exit;
}

$footer_links = awards_gallery_menu_links('footer', awards_gallery_menu_links('primary', awards_gallery_default_links()));
$address      = awards_gallery_contact('ag_address');
$phones       = array_filter([awards_gallery_contact('ag_phone_1'), awards_gallery_contact('ag_phone_2')]);
$email        = awards_gallery_contact('ag_email');

$socials = array_filter([
    'Facebook'  => [awards_gallery_contact('ag_facebook'), '<path d="M14 8h2V5h-2a3 3 0 0 0-3 3v2H9v3h2v6h3v-6h2l.5-3H14V8.5a.5.5 0 0 1 .5-.5z"/>'],
    'Instagram' => [awards_gallery_contact('ag_instagram'), '<rect x="5" y="5" width="14" height="14" rx="4"/><circle cx="12" cy="12" r="3.2"/><circle cx="16.3" cy="7.7" r=".6" fill="currentColor"/>'],
    'X'         => [awards_gallery_contact('ag_twitter'), '<path d="M20 7.5c-.6.3-1.2.4-1.8.5.7-.4 1.1-1 1.4-1.7-.6.4-1.3.6-2 .8a3.1 3.1 0 0 0-5.3 2.8A8.8 8.8 0 0 1 5.8 6.6a3.1 3.1 0 0 0 1 4.1c-.5 0-1-.2-1.4-.4 0 1.5 1.1 2.8 2.5 3a3 3 0 0 1-1.4.1 3.1 3.1 0 0 0 2.9 2.1A6.2 6.2 0 0 1 5 16.8 8.8 8.8 0 0 0 18.6 9.4V9c.6-.4 1-.9 1.4-1.5z"/>'],
], fn($social) => $social[0] !== '');
?>
</main>

<footer class="bg-ink-900 pt-16 lg:pt-20">
    <div class="mx-auto grid max-w-[1000px] grid-cols-1 gap-12 px-5 sm:grid-cols-2 sm:px-8 lg:grid-cols-[1.15fr_1fr_1fr] lg:px-0">
        <div class="sm:col-span-2 lg:col-span-1">
            <img src="<?php echo esc_url(awards_gallery_logo_url()); ?>" alt="<?php echo esc_attr(get_bloginfo('name')); ?>" class="h-16 w-auto lg:h-[86px]" loading="lazy">
            <p class="mt-8 max-w-[16rem] text-base leading-relaxed text-cream lg:pl-4"><?php echo esc_html(awards_gallery_contact('ag_footer_text')); ?></p>
            <?php if ($socials) : ?>
                <div class="mt-6 flex gap-3 lg:pl-4">
                    <?php foreach ($socials as $label => [$url, $icon]) : ?>
                        <a href="<?php echo esc_url($url); ?>" class="flex h-[34px] w-[34px] items-center justify-center rounded-full border border-cream/80 text-cream transition-colors hover:border-gold hover:text-gold" aria-label="<?php echo esc_attr($label); ?>" target="_blank" rel="noopener">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.4" aria-hidden="true"><?php echo $icon; // phpcs:ignore -- static markup ?></svg>
                        </a>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <div>
            <h4 class="text-[15px] uppercase tracking-[0.3em] text-gold"><?php esc_html_e('Navigation', 'awards-gallery-theme'); ?></h4>
            <ul class="mt-6 flex flex-col gap-3 pl-5 text-base text-cream">
                <?php foreach ($footer_links as $link) : ?>
                    <li><a href="<?php echo esc_url($link['url']); ?>" class="transition-colors hover:text-gold"><?php echo esc_html($link['title']); ?></a></li>
                <?php endforeach; ?>
            </ul>
        </div>

        <div>
            <h4 class="text-[15px] uppercase tracking-[0.3em] text-gold"><?php esc_html_e('Contact Us', 'awards-gallery-theme'); ?></h4>
            <ul class="mt-6 flex flex-col gap-4 text-base leading-relaxed text-cream">
                <?php if ($address) : ?>
                    <li class="flex gap-4">
                        <svg class="mt-1.5 h-3.5 w-3.5 shrink-0 text-gold" viewBox="0 0 24 24" fill="currentColor"><path d="M12 22s7-6.5 7-12.5a7 7 0 1 0-14 0C5 15.5 12 22 12 22zm0-10a2.5 2.5 0 1 1 0-5 2.5 2.5 0 0 1 0 5z"/></svg>
                        <span><?php echo nl2br(esc_html($address)); ?></span>
                    </li>
                <?php endif; ?>
                <?php if ($phones) : ?>
                    <li class="flex gap-4">
                        <svg class="mt-1.5 h-3.5 w-3.5 shrink-0 text-gold" viewBox="0 0 24 24" fill="currentColor"><path d="M6.6 10.8a15 15 0 0 0 6.6 6.6l2.2-2.2c.3-.3.7-.4 1-.2 1.1.4 2.3.6 3.6.6.6 0 1 .4 1 1V20c0 .6-.4 1-1 1A17 17 0 0 1 3 4c0-.6.4-1 1-1h3.5c.6 0 1 .4 1 1 0 1.3.2 2.5.6 3.6.1.3 0 .7-.2 1z"/></svg>
                        <span>
                            <?php foreach (array_values($phones) as $i => $phone) : ?>
                                <?php echo $i ? '<br>' : ''; ?><a href="<?php echo esc_attr(awards_gallery_tel_href($phone)); ?>"><?php echo esc_html($phone); ?></a>
                            <?php endforeach; ?>
                        </span>
                    </li>
                <?php endif; ?>
                <?php if ($email) : ?>
                    <li class="flex gap-4">
                        <svg class="mt-1.5 h-3.5 w-3.5 shrink-0 text-gold" viewBox="0 0 24 24" fill="currentColor"><path d="M3 5h18a1 1 0 0 1 1 1v12a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1V6a1 1 0 0 1 1-1zm9 7.2L4.2 7H4v.9l8 5.3 8-5.3V7h-.2L12 12.2z"/></svg>
                        <a href="mailto:<?php echo esc_attr($email); ?>" class="break-all"><?php echo esc_html($email); ?></a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>

    <div class="mx-auto mt-16 max-w-shell px-5 py-8 text-sm text-cream/30 sm:px-8 lg:px-20">
        &copy; <?php echo esc_html(gmdate('Y')); ?> <?php echo esc_html(awards_gallery_contact('ag_copyright')); ?>
    </div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
