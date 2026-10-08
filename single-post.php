<?php
/**
 * Single blog post (default "post" type): article hero, body, quote CTA and
 * the three latest other posts. Other post types fall back to single.php.
 */

if (!defined('ABSPATH')) {
    exit;
}

get_header();

while (have_posts()) :
    the_post();

    $post_id    = get_the_ID();
    $category   = awards_gallery_primary_category($post_id);
    $blog_url   = awards_gallery_blog_url();
    $quote_url  = home_url('/contact-us/');
    // Hero crop: the focal point set in the editor's "Hero Banner" panel, else the older
    // Top/Center/Bottom setting some posts still carry, else the centre.
    $focal      = get_post_meta($post_id, 'awards_post_hero_focal', true);
    $legacy     = ['top' => '50% 0%', 'center' => '50% 50%', 'bottom' => '50% 100%'];
    $hero_pos   = is_array($focal) && isset($focal['x'], $focal['y'])
        ? sprintf('%s%% %s%%', round($focal['x'] * 100, 1), round($focal['y'] * 100, 1))
        : ($legacy[get_post_meta($post_id, 'awards_post_hero_position', true)] ?? '50% 50%');
    $crumbs     = array_filter([
        [__('Home', 'awards-gallery-theme'), home_url('/')],
        [__('Blog', 'awards-gallery-theme'), $blog_url],
        [get_the_title(), get_permalink()],
    ]);

    $more_posts = new WP_Query([
        'post_type'           => 'post',
        'posts_per_page'      => 3,
        'post__not_in'        => [$post_id],
        'ignore_sticky_posts' => true,
        'no_found_rows'       => true,
    ]);
    ?>
    <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

        <?php // Hero: pads its own top to clear the fixed header (see awards_gallery_starts_with_hero()). ?>
        <header class="relative isolate overflow-hidden bg-black pb-14 pt-28 lg:pb-16 lg:pt-32">
            <?php // Background: the post's featured image, else the default article banner. ?>
            <?php if (has_post_thumbnail()) : ?>
                <?php the_post_thumbnail('full', ['class' => 'absolute inset-0 -z-20 h-full w-full object-cover opacity-80', 'style' => 'object-position:' . $hero_pos, 'alt' => '', 'sizes' => '100vw', 'loading' => 'eager', 'fetchpriority' => 'high']); ?>
            <?php else : ?>
                <img src="<?php echo esc_url(get_theme_file_uri('/assets/img/article-hero-bg.jpg')); ?>" alt="" class="absolute inset-0 -z-20 h-full w-full object-cover opacity-80" style="object-position:<?php echo esc_attr($hero_pos); ?>" fetchpriority="high">
            <?php endif; ?>
            <?php // Overlay (Figma "Overlay+Blur"): image at 80% on black, a dark shade behind the text side, and a fade into the body. ?>
            <div class="absolute inset-0 -z-10 bg-gradient-to-r from-black/75 via-black/45 to-black/10"></div>
            <div class="absolute inset-0 -z-10 bg-gradient-to-b from-black/30 via-transparent to-ink/80"></div>

            <div class="mx-auto max-w-shell px-5 sm:px-8 lg:px-20">
                <nav aria-label="<?php esc_attr_e('Breadcrumb', 'awards-gallery-theme'); ?>">
                    <ol class="flex flex-wrap items-center gap-x-3 gap-y-1 text-sm text-cream">
                        <?php foreach (array_values($crumbs) as $i => [$label, $url]) : ?>
                            <?php if ($i) : ?><li aria-hidden="true" class="text-cream/70">›</li><?php endif; ?>
                            <li>
                                <?php if ($i === count($crumbs) - 1) : // The current post: plain text, kept to one line. ?>
                                    <span class="block max-w-[16rem] truncate text-gold sm:max-w-md lg:max-w-xl" aria-current="page" title="<?php echo esc_attr($label); ?>"><?php echo esc_html($label); ?></span>
                                <?php else : ?>
                                    <a href="<?php echo esc_url($url); ?>" class="transition hover:text-gold"><?php echo esc_html($label); ?></a>
                                <?php endif; ?>
                            </li>
                        <?php endforeach; ?>
                    </ol>
                </nav>

                <?php if ($category) : ?>
                    <a href="<?php echo esc_url(get_category_link($category)); ?>" class="mt-12 inline-block bg-gold-line px-4 py-1.5 text-[11px] uppercase tracking-[0.3em] text-ink hover:text-ink sm:text-xs lg:mt-14"><?php echo esc_html($category->name); ?></a>
                <?php endif; ?>

                <h1 class="<?php echo $category ? 'mt-5' : 'mt-12 lg:mt-14'; ?> max-w-3xl text-[28px] font-normal uppercase leading-tight text-cream sm:text-[32px] lg:text-[36px]"><?php the_title(); ?></h1>

            </div>
        </header>

        <div class="bg-noise bg-ink bg-ink-sheen py-14 lg:py-20">
            <div class="mx-auto max-w-[940px] px-5 sm:px-8">
                <?php if (has_excerpt()) : ?>
                    <p class="border-l-2 border-gold pl-6 text-base italic leading-relaxed text-cream sm:text-lg"><?php echo esc_html(get_the_excerpt()); ?></p>
                <?php endif; ?>

                <div class="article-content <?php echo has_excerpt() ? 'mt-10 lg:mt-12' : ''; ?>">
                    <?php the_content(); ?>
                </div>

                <?php wp_link_pages(['before' => '<nav class="mt-10 flex gap-3 text-sm text-gold">', 'after' => '</nav>']); ?>

                <div class="mt-12 flex justify-end lg:mt-14">
                    <a href="<?php echo esc_url($quote_url); ?>" class="btn-gold"><?php esc_html_e('Get a Free Quote', 'awards-gallery-theme'); ?></a>
                </div>
            </div>
        </div>
    </article>

    <?php if ($more_posts->have_posts()) : ?>
        <section class="bg-[#0B0A08] py-14 lg:py-20" aria-labelledby="more-articles">
            <div class="mx-auto max-w-shell px-5 sm:px-8 lg:px-20">
                <div class="flex items-center justify-between gap-6">
                    <h2 id="more-articles" class="flex items-center gap-4 text-xs uppercase tracking-eyebrow text-gold sm:text-[13px]">
                        <span class="h-px w-12 bg-gradient-to-r from-transparent via-gold/70 to-gold" aria-hidden="true"></span>
                        <?php esc_html_e('More Articles', 'awards-gallery-theme'); ?>
                    </h2>
                    <a href="<?php echo esc_url($blog_url); ?>" class="group/link whitespace-nowrap text-xs uppercase tracking-[0.3em] text-gold sm:text-[13px]">
                        <?php esc_html_e('View All', 'awards-gallery-theme'); ?> <span class="inline-block transition-transform duration-300 group-hover/link:translate-x-1" aria-hidden="true">→</span>
                    </a>
                </div>

                <div class="mt-8 grid grid-cols-1 gap-6 sm:grid-cols-2 lg:mt-10 lg:grid-cols-3">
                    <?php $i = 0; while ($more_posts->have_posts()) : $more_posts->the_post();
                        $card_cat = awards_gallery_primary_category(get_the_ID());
                    ?>
                        <article class="reveal flex flex-col border-gold-line hover:[--line-alpha:0.7]" style="--d:<?php echo esc_attr(($i++ % 3) * 100); ?>ms">
                            <a href="<?php the_permalink(); ?>" class="group/img relative block overflow-hidden" tabindex="-1" aria-hidden="true">
                                <?php if (has_post_thumbnail()) : ?>
                                    <?php the_post_thumbnail('medium_large', ['class' => 'aspect-[418/220] w-full object-cover transition duration-700 group-hover/img:scale-105', 'alt' => '', 'sizes' => '(min-width: 1024px) 33vw, (min-width: 640px) 50vw, 100vw']); ?>
                                <?php else : ?>
                                    <div class="aspect-[418/220] w-full bg-ink-800"></div>
                                <?php endif; ?>
                                <?php if ($card_cat) : ?>
                                    <span class="absolute left-3 top-3 bg-gold-line px-3 py-1 text-[11px] uppercase tracking-[0.25em] text-ink"><?php echo esc_html($card_cat->name); ?></span>
                                <?php endif; ?>
                            </a>
                            <div class="px-6 pb-7 pt-6">
                                <p class="text-[13px] text-cream/90">
                                    <time datetime="<?php echo esc_attr(get_the_date('c')); ?>"><?php echo esc_html(get_the_date()); ?></time>
                                    <span class="mx-1 text-cream/50" aria-hidden="true">•</span>
                                    <?php echo esc_html(sprintf(__('%d min read', 'awards-gallery-theme'), awards_gallery_read_time(get_post()))); ?>
                                </p>
                                <h3 class="mt-4 text-[15px] font-normal uppercase leading-snug tracking-[0.02em]">
                                    <a href="<?php the_permalink(); ?>" class="text-white transition hover:text-gold"><?php the_title(); ?></a>
                                </h3>
                            </div>
                        </article>
                    <?php endwhile; wp_reset_postdata(); ?>
                </div>
            </div>
        </section>
    <?php endif; ?>

    <?php // Breadcrumb structured data, so search results can show "Home › Blog › Post". ?>
    <script type="application/ld+json"><?php echo wp_json_encode([
        '@context'        => 'https://schema.org',
        '@type'           => 'BreadcrumbList',
        'itemListElement' => array_map(fn($crumb, $i) => [
            '@type'    => 'ListItem',
            'position' => $i + 1,
            'name'     => $crumb[0],
            'item'     => $crumb[1],
        ], array_values($crumbs), array_keys(array_values($crumbs))),
    ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE); ?></script>
    <?php
endwhile;

get_footer();
