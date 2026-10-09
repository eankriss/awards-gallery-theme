<?php
if (!defined('ABSPATH')) {
    exit;
}

$footer_links = awards_gallery_menu_links('footer', awards_gallery_menu_links('primary', awards_gallery_default_links()));
$address      = awards_gallery_contact('ag_address');
$phones       = array_filter([awards_gallery_contact('ag_phone_1'), awards_gallery_contact('ag_phone_2')]);
$email        = awards_gallery_contact('ag_email');

$socials      = awards_gallery_social_links();
?>
</main>

<?php // Phones (below sm): every column stacks and centres, with the nav links split into two columns. Tablets (sm–lg): the brand block centres above a divider and Navigation/Contact sit below as a centred pair of left-aligned columns. Desktop (lg): three left-aligned columns. ?>
<footer class="border-t border-gold/15 bg-ink-900 pt-10 sm:pt-12 lg:pt-20">
    <div class="mx-auto grid max-w-[1000px] grid-cols-1 gap-8 px-5 max-sm:text-center sm:grid-cols-[auto_auto] sm:justify-center sm:gap-10 sm:gap-x-16 sm:px-8 lg:grid-cols-[1.15fr_1fr_1fr] lg:gap-12 lg:px-0">
        <div class="max-lg:flex max-lg:flex-col max-lg:items-center max-lg:text-center sm:col-span-2 sm:max-lg:border-b sm:max-lg:border-gold/15 sm:max-lg:pb-10 lg:col-span-1">
            <img src="<?php echo esc_url(awards_gallery_logo_url()); ?>" alt="<?php echo esc_attr(get_bloginfo('name')); ?>" class="h-16 w-auto lg:h-[86px]" loading="lazy">
            <p class="mt-8 max-w-[16rem] text-base leading-relaxed text-cream sm:max-w-[26rem] lg:max-w-[17rem] lg:pl-4"><?php echo esc_html(awards_gallery_contact('ag_footer_text')); ?></p>
            <?php if ($socials) : ?>
                <div class="mt-6 flex gap-3 max-lg:justify-center lg:pl-4">
                    <?php foreach ($socials as $social) : ?>
                        <a href="<?php echo esc_url($social['url']); ?>" class="flex h-[34px] w-[34px] items-center justify-center rounded-full border border-cream/80 text-cream transition-colors hover:border-gold hover:text-gold" aria-label="<?php echo esc_attr($social['label']); ?>" target="_blank" rel="noopener">
                            <?php if ($social['image']) : ?>
                                <img src="<?php echo esc_url($social['image']); ?>" alt="" class="h-4 w-4 object-contain" loading="lazy">
                            <?php elseif ($social['svg']) : ?>
                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.4" aria-hidden="true"><?php echo $social['svg']; // phpcs:ignore -- static markup ?></svg>
                            <?php else : ?>
                                <span class="text-xs uppercase" aria-hidden="true"><?php echo esc_html(mb_substr($social['label'], 0, 1)); ?></span>
                            <?php endif; ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <div>
            <h4 class="text-[15px] uppercase tracking-[0.3em] text-gold"><?php esc_html_e('Navigation', 'awards-gallery-theme'); ?></h4>
            <?php
            // Fill the two phone columns top-to-bottom: Home, Products | About Us, Blog. With an odd
            // count the last item of the first column (Contact Us) spans both columns on the last row, centred.
            $nav_rows = (int) ceil(count($footer_links) / 2);
            $nav_odd  = count($footer_links) % 2 ? $nav_rows - 1 : -1;
            ?>
            <ul class="mt-6 flex flex-col gap-3 text-base text-cream max-sm:mx-auto max-sm:grid max-sm:w-fit max-sm:grid-flow-col max-sm:grid-cols-2 max-sm:grid-rows-[repeat(var(--nav-rows),auto)] max-sm:gap-x-14 lg:pl-5" style="--nav-rows: <?php echo $nav_rows; ?>">
                <?php foreach (array_values($footer_links) as $i => $link) : ?>
                    <li<?php echo $i === $nav_odd ? ' class="max-sm:col-span-2 max-sm:[grid-row:var(--nav-rows)]"' : ''; ?>><a href="<?php echo esc_url($link['url']); ?>" class="transition-colors hover:text-gold"><?php echo esc_html($link['title']); ?></a></li>
                <?php endforeach; ?>
            </ul>
        </div>

        <div>
            <h4 class="text-[15px] uppercase tracking-[0.3em] text-gold"><?php esc_html_e('Contact Us', 'awards-gallery-theme'); ?></h4>
            <ul class="mt-6 flex flex-col gap-4 text-base leading-relaxed text-cream">
                <?php if ($address) : ?>
                    <li class="flex gap-4 max-sm:flex-col max-sm:items-center max-sm:gap-2">
                        <svg class="h-3.5 w-3.5 shrink-0 text-gold sm:mt-1.5" viewBox="0 0 24 24" fill="currentColor"><path d="M12 22s7-6.5 7-12.5a7 7 0 1 0-14 0C5 15.5 12 22 12 22zm0-10a2.5 2.5 0 1 1 0-5 2.5 2.5 0 0 1 0 5z"/></svg>
                        <span><?php echo nl2br(esc_html($address)); ?></span>
                    </li>
                <?php endif; ?>
                <?php if ($phones) : ?>
                    <li class="flex gap-4 max-sm:flex-col max-sm:items-center max-sm:gap-2">
                        <svg class="h-3.5 w-3.5 shrink-0 text-gold sm:mt-1.5" viewBox="0 0 24 24" fill="currentColor"><path d="M6.6 10.8a15 15 0 0 0 6.6 6.6l2.2-2.2c.3-.3.7-.4 1-.2 1.1.4 2.3.6 3.6.6.6 0 1 .4 1 1V20c0 .6-.4 1-1 1A17 17 0 0 1 3 4c0-.6.4-1 1-1h3.5c.6 0 1 .4 1 1 0 1.3.2 2.5.6 3.6.1.3 0 .7-.2 1z"/></svg>
                        <span>
                            <?php foreach (array_values($phones) as $i => $phone) : ?>
                                <?php echo $i ? '<br>' : ''; ?><a href="<?php echo esc_attr(awards_gallery_tel_href($phone)); ?>"><?php echo esc_html($phone); ?></a>
                            <?php endforeach; ?>
                        </span>
                    </li>
                <?php endif; ?>
                <?php if ($email) : ?>
                    <li class="flex gap-4 max-sm:flex-col max-sm:items-center max-sm:gap-2">
                        <svg class="h-3.5 w-3.5 shrink-0 text-gold sm:mt-1.5" viewBox="0 0 24 24" fill="currentColor"><path d="M3 5h18a1 1 0 0 1 1 1v12a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1V6a1 1 0 0 1 1-1zm9 7.2L4.2 7H4v.9l8 5.3 8-5.3V7h-.2L12 12.2z"/></svg>
                        <a href="mailto:<?php echo esc_attr($email); ?>" class="break-all"><?php echo esc_html($email); ?></a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>

    <div class="mx-auto mt-6 max-w-shell px-5 pb-5 text-center text-xs text-cream/30 sm:mt-8 sm:px-8 sm:py-6 sm:text-sm lg:mt-16 lg:px-20 lg:py-8 lg:text-left">
        &copy; <?php echo esc_html(gmdate('Y')); ?> <?php echo esc_html(awards_gallery_contact('ag_copyright')); ?>
    </div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
