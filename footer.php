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
                            <?php echo $social['icon']; // phpcs:ignore -- escaped in awards_gallery_uploaded_icon() ?>
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
                        <?php echo awards_gallery_footer_contact_icon('address'); // phpcs:ignore -- escaped in the helper ?>
                        <span><?php echo nl2br(esc_html($address)); ?></span>
                    </li>
                <?php endif; ?>
                <?php if ($phones) : ?>
                    <li class="flex gap-4 max-sm:flex-col max-sm:items-center max-sm:gap-2">
                        <?php echo awards_gallery_footer_contact_icon('phone'); // phpcs:ignore -- escaped in the helper ?>
                        <span>
                            <?php foreach (array_values($phones) as $i => $phone) : ?>
                                <?php echo $i ? '<br>' : ''; ?><a href="<?php echo esc_attr(awards_gallery_tel_href($phone)); ?>"><?php echo esc_html($phone); ?></a>
                            <?php endforeach; ?>
                        </span>
                    </li>
                <?php endif; ?>
                <?php if ($email) : ?>
                    <li class="flex gap-4 max-sm:flex-col max-sm:items-center max-sm:gap-2">
                        <?php echo awards_gallery_footer_contact_icon('email'); // phpcs:ignore -- escaped in the helper ?>
                        <a href="mailto:<?php echo esc_attr($email); ?>" class="break-all"><?php echo esc_html($email); ?></a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>

    <div class="mx-auto mt-6 max-w-shell px-5 pb-5 text-center text-xs text-cream/30 sm:mt-8 sm:px-8 sm:py-6 sm:text-sm lg:mt-16 lg:px-20 lg:py-8 lg:text-left">
        <?php echo esc_html(awards_gallery_contact('ag_copyright')); ?>
    </div>
</footer>

<?php $global_music_url = awards_gallery_global_background_music_url(); ?>
<?php if ($global_music_url) : ?>
    <style>
        @keyframes ag-global-music-pulse {
            0%, 100% { border-color: rgba(218,147,40,.35); box-shadow: 0 8px 24px rgba(0,0,0,.3), 0 0 0 0 rgba(218,147,40,0); }
            50% { border-color: rgba(218,147,40,.95); box-shadow: 0 8px 24px rgba(0,0,0,.3), 0 0 0 7px rgba(218,147,40,.16); }
        }
    </style>
    <audio id="ag-global-background-music" src="<?php echo esc_url($global_music_url); ?>" autoplay loop preload="auto"></audio>
    <button type="button" id="ag-global-background-music-toggle" aria-label="Turn background music on" aria-pressed="false" title="Play music" style="position:fixed;top:50%;right:1.25rem;z-index:60;display:flex;height:3.25rem;width:3.25rem;transform:translateY(-50%);align-items:center;justify-content:center;border:1px solid rgba(255,255,255,.2);border-radius:999px;background:rgba(32,32,32,.88);color:#f5f0e8;box-shadow:0 8px 24px rgba(0,0,0,.3);font:inherit;cursor:pointer;transition:background-color .2s,transform .2s,box-shadow .2s;" onmouseover="this.style.backgroundColor='rgba(65,65,65,.95)';this.style.boxShadow='0 10px 28px rgba(0,0,0,.45)'" onmouseout="this.style.backgroundColor='rgba(32,32,32,.88)';this.style.boxShadow='0 8px 24px rgba(0,0,0,.3)'">
        <span aria-hidden="true" data-global-music-icon style="display:inline-flex;align-items:center;color:rgba(245,240,232,.55);transition:color .2s,text-shadow .2s;">
            <svg data-global-music-off width="30" height="30" viewBox="0 0 32 32" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
                <path d="M11 21V10l9-2v10" stroke-width="2.2"/><circle cx="8.5" cy="21" r="2.1" fill="currentColor" stroke="none"/><circle cx="17.5" cy="18.8" r="2.1" fill="currentColor" stroke="none"/>
                <path d="m21.5 20.5 5 3-5 3z" fill="currentColor" stroke="none"/>
            </svg>
            <svg data-global-music-on width="30" height="30" viewBox="0 0 32 32" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" style="display:none;">
                <path d="M11 21V10l9-2v10" stroke-width="2.2"/><circle cx="8.5" cy="21" r="2.1" fill="currentColor" stroke="none"/><circle cx="17.5" cy="18.8" r="2.1" fill="currentColor" stroke="none"/>
                <path d="M22 21.5v5M25 21.5v5" stroke-width="1.8"/>
            </svg>
        </span>
        <span class="screen-reader-text" data-global-music-label>Play music</span>
    </button>
    <script>
        (function () {
            var audio = document.getElementById('ag-global-background-music');
            var toggle = document.getElementById('ag-global-background-music-toggle');
            if (!audio || !toggle) return;

            var activeSections = new Set();
            var resumeAfterSection = false;

            document.addEventListener('awards-gallery-section-sound', function (event) {
                var detail = event.detail || {};
                if (!detail.section) return;

                if (detail.active) {
                    activeSections.add(detail.section);
                    setState(false);
                    if (!audio.paused) {
                        resumeAfterSection = true;
                        audio.pause();
                    }
                } else {
                    activeSections.delete(detail.section);
                    if (!activeSections.size && resumeAfterSection) {
                        resumeAfterSection = false;
                        audio.muted = false;
                        audio.play().then(function () {
                            setState(true);
                        }).catch(function () {
                            audio.muted = true;
                            audio.play().catch(function () {});
                            setState(false);
                        });
                    }
                }
            });

            var label = toggle.querySelector('span:last-child');
            var icon = toggle.querySelector('[data-global-music-icon]');
            var iconOff = icon.querySelector('[data-global-music-off]');
            var iconOn = icon.querySelector('[data-global-music-on]');
            var setState = function (on) {
                toggle.setAttribute('aria-pressed', on ? 'true' : 'false');
                toggle.setAttribute('aria-label', on ? 'Turn background music off' : 'Turn background music on');
                toggle.setAttribute('title', on ? 'Pause music' : 'Play music');
                iconOff.style.display = on ? 'none' : 'block';
                iconOn.style.display = on ? 'block' : 'none';
                icon.style.color = on ? '#f5f0e8' : 'rgba(245,240,232,.55)';
                icon.style.textShadow = on ? '0 0 10px rgba(218,147,40,.8)' : 'none';
                toggle.style.animation = on ? 'none' : 'ag-global-music-pulse 2s ease-in-out 3';
                label.textContent = on ? 'Pause music' : 'Play music';
            };

            toggle.addEventListener('click', function () {
                if (audio.paused || audio.muted) {
                    audio.muted = false;
                    audio.play().then(function () { setState(true); }).catch(function () { setState(false); });
                } else {
                    audio.pause();
                    setState(false);
                }
            });

            var startGlobalMusic = function () {
                if (activeSections.size || !audio.paused) return;
                audio.muted = false;
                audio.play().then(function () {
                    setState(true);
                }).catch(function () {
                    // A browser may require a gesture before audible playback.
                    // Keep the track running muted, then unmute on the first
                    // gesture so the global player does not remain stopped.
                    audio.muted = true;
                    audio.play().then(function () {
                        setState(true);
                    }).catch(function () {});
                });
            };

            ['pointerdown', 'keydown', 'touchstart'].forEach(function (eventName) {
                document.addEventListener(eventName, function () {
                    if (audio.muted && !activeSections.size) {
                        audio.muted = false;
                    }
                    startGlobalMusic();
                }, { once: true, passive: true });
            });

            // Try audible autoplay first. Browsers may reject this until the
            // visitor interacts, so fall back to muted playback in that case.
            startGlobalMusic();
        }());
    </script>
<?php endif; ?>

<?php wp_footer(); ?>
</body>
</html>
