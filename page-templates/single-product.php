<?php
/**
 * Template Name: Single Product
 * Template Post Type: page
 *
 * Product page (Acrylic Award, Crystal Award…): breadcrumb hero, image gallery,
 * description, key features, "ideal for", related products and a quote CTA.
 * Content comes from the page itself (title, editor text) and the ACF
 * "Products Data" field group.
 */

if (!defined('ABSPATH')) {
    exit;
}

get_header();

/**
 * ACF value, or $default when ACF is inactive or the field is empty.
 */
$field = function ($name, $post_id = false, $default = '') {
    if (!function_exists('get_field')) {
        return $default;
    }
    $value = get_field($name, $post_id);
    return $value ?: $default;
};

/**
 * Text values from a one-column repeater, skipping empty rows.
 */
$rows = function ($repeater, $sub_field) use ($field) {
    return array_values(array_filter(array_map(
        fn($row) => trim((string) ($row[$sub_field] ?? '')),
        (array) $field($repeater, false, [])
    )));
};

while (have_posts()) :
    the_post();

    $post_id     = get_the_ID();
    $theme       = $field('awards_products_theme');
    $short_desc  = $field('awards_products_short_description');
    $features    = $rows('awards_products_key_features_list', 'awards_products_key_features_item');
    $ideal_for   = $rows('awards_products_ideal_for_list', 'awards_products_ideal_for_item');
    $has_content = trim(get_the_content()) !== '';

    // Gallery (ACF image arrays); falls back to the featured image.
    $images = array_values(array_filter(array_map(
        fn($img) => is_array($img) ? (int) ($img['ID'] ?? $img['id'] ?? 0) : (int) $img,
        (array) $field('awards_products_image_gallery', false, [])
    )));
    if (!$images && has_post_thumbnail()) {
        $images = [get_post_thumbnail_id()];
    }

    // Related products (ACF post IDs) — published ones only, in the chosen order.
    $related = array_values(array_filter(
        array_map('get_post', (array) $field('awards_related_products', false, [])),
        fn($p) => $p instanceof WP_Post && $p->ID !== $post_id && get_post_status($p) === 'publish'
    ));
    // Breadcrumb "Products" and "View All" both go to the products listing.
    $all_url = awards_gallery_products_url();

    $crumbs = array_filter([
        [__('Home', 'awards-gallery-theme'), home_url('/')],
        [__('Products', 'awards-gallery-theme'), $all_url],
        [get_the_title(), get_permalink()],
    ]);
    ?>
    <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

        <?php
        // Hero: pads its own top to clear the fixed header (see awards_gallery_starts_with_hero()),
        // then a 293px banner (Figma) with the image at 80% opacity on black.
        ?>
        <header class="bg-black pt-[81px]">
            <div class="relative isolate overflow-hidden">
                <img src="<?php echo esc_url(get_theme_file_uri('/assets/img/award template banner.webp')); ?>" alt="" class="absolute inset-0 -z-10 h-full w-full object-cover opacity-80" fetchpriority="high">

                <div class="mx-auto flex max-w-shell flex-col justify-center px-5 pb-10 pt-12 sm:px-8 lg:min-h-[293px] lg:px-20 lg:pb-[46px] lg:pt-[82px]">
                    <nav aria-label="<?php esc_attr_e('Breadcrumb', 'awards-gallery-theme'); ?>">
                        <ol class="flex flex-wrap items-center gap-x-2.5 gap-y-1 text-base leading-6 text-cream lg:text-lg">
                            <?php foreach (array_values($crumbs) as $i => [$label, $url]) : ?>
                                <?php if ($i) : ?><li aria-hidden="true">›</li><?php endif; ?>
                                <li>
                                    <?php if ($i === count($crumbs) - 1) : ?>
                                        <span class="block max-w-[16rem] truncate text-gold sm:max-w-md lg:max-w-xl" aria-current="page" title="<?php echo esc_attr($label); ?>"><?php echo esc_html($label); ?></span>
                                    <?php else : ?>
                                        <a href="<?php echo esc_url($url); ?>" class="transition hover:text-gold"><?php echo esc_html($label); ?></a>
                                    <?php endif; ?>
                                </li>
                            <?php endforeach; ?>
                        </ol>
                    </nav>

                    <?php if ($theme) : ?>
                        <p class="animate-rise mt-10 text-xs uppercase leading-6 tracking-[0.4em] text-gold sm:text-base lg:mt-[49px]" style="animation-delay:.1s"><?php echo esc_html($theme); ?></p>
                    <?php endif; ?>
                    <h1 class="animate-rise <?php echo $theme ? 'mt-2' : 'mt-10 lg:mt-[49px]'; ?> text-4xl font-normal uppercase leading-none text-cream sm:text-5xl lg:text-[60px] lg:tracking-[-1.5px]" style="animation-delay:.2s"><?php the_title(); ?></h1>
                </div>
            </div>
        </header>

        <div class="bg-[#0A0908] pb-16 pt-6 lg:pb-24 lg:pt-12">
            <div class="mx-auto grid max-w-shell gap-12 px-5 sm:px-8 lg:grid-cols-[minmax(0,520fr)_minmax(0,760fr)] lg:gap-14 lg:px-20">

                <?php if ($images) : ?>
                    <div class="reveal lg:sticky lg:top-28 lg:self-start" data-product-gallery>
                        <?php foreach ($images as $i => $image_id) : ?>
                            <div class="overflow-hidden rounded-lg border border-cream/25 bg-ink-800<?php echo $i ? ' hidden' : ''; ?>" data-gallery-slide>
                                <?php echo wp_get_attachment_image($image_id, 'large', false, [
                                    'class'         => 'aspect-[520/467] w-full object-cover',
                                    'sizes'         => '(min-width: 1024px) 520px, 100vw',
                                    'loading'       => $i ? 'lazy' : 'eager',
                                    'fetchpriority' => $i ? 'auto' : 'high',
                                    'alt'           => get_post_meta($image_id, '_wp_attachment_image_alt', true) ?: get_the_title(),
                                ]); ?>
                            </div>
                        <?php endforeach; ?>

                        <?php if (count($images) > 1) : ?>
                            <?php // Thumbnail slider: three visible at a time; arrows appear when there are more. ?>
                            <div class="relative mt-4" data-gallery-track-wrap>
                                <ul class="flex snap-x snap-mandatory gap-3 overflow-x-auto scroll-smooth [scrollbar-width:none] sm:gap-4 [&::-webkit-scrollbar]:hidden" data-gallery-track>
                                    <?php foreach ($images as $i => $image_id) : ?>
                                        <li class="w-[calc((100%-1.5rem)/3)] shrink-0 snap-start sm:w-[calc((100%-2rem)/3)]">
                                            <button type="button" class="block w-full overflow-hidden rounded-md border border-cream/15 opacity-60 transition hover:opacity-100 focus-visible:outline focus-visible:outline-2 focus-visible:-outline-offset-2 focus-visible:outline-gold aria-pressed:border-gold aria-pressed:opacity-100" aria-pressed="<?php echo $i ? 'false' : 'true'; ?>" aria-label="<?php echo esc_attr(sprintf(__('Show image %1$d of %2$d', 'awards-gallery-theme'), $i + 1, count($images))); ?>" data-gallery-thumb>
                                                <?php echo wp_get_attachment_image($image_id, 'medium', false, [
                                                    'class'   => 'aspect-[3/2] w-full object-cover',
                                                    'sizes'   => '(min-width: 1024px) 165px, 33vw',
                                                    'loading' => 'lazy',
                                                    'alt'     => '',
                                                ]); ?>
                                            </button>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                                <?php if (count($images) > 3) : ?>
                                    <button type="button" class="absolute -left-3 top-1/2 z-10 flex h-9 w-9 -translate-y-1/2 items-center justify-center rounded-full border border-gold/60 bg-ink/85 text-gold shadow-[0_6px_20px_rgba(0,0,0,0.6)] backdrop-blur transition hover:border-gold hover:bg-ink focus-visible:outline focus-visible:outline-2 focus-visible:outline-gold disabled:pointer-events-none disabled:opacity-0" aria-label="<?php esc_attr_e('Previous images', 'awards-gallery-theme'); ?>" data-gallery-prev disabled>
                                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m15 18-6-6 6-6"/></svg>
                                    </button>
                                    <button type="button" class="absolute -right-3 top-1/2 z-10 flex h-9 w-9 -translate-y-1/2 items-center justify-center rounded-full border border-gold/60 bg-ink/85 text-gold shadow-[0_6px_20px_rgba(0,0,0,0.6)] backdrop-blur transition hover:border-gold hover:bg-ink focus-visible:outline focus-visible:outline-2 focus-visible:outline-gold disabled:pointer-events-none disabled:opacity-0" aria-label="<?php esc_attr_e('Next images', 'awards-gallery-theme'); ?>" data-gallery-next>
                                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m9 18 6-6-6-6"/></svg>
                                    </button>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

                <div class="reveal <?php echo $images ? '' : 'lg:col-span-2'; ?>" style="--d:100ms">
                    <?php if ($short_desc || $has_content) : ?>
                        <h2 class="flex items-center gap-4 text-xs uppercase tracking-eyebrow text-gold sm:text-[13px]">
                            <span class="h-px w-12 bg-gradient-to-r from-transparent via-gold/70 to-gold" aria-hidden="true"></span>
                            <?php esc_html_e('About This Award', 'awards-gallery-theme'); ?>
                        </h2>
                        <?php if ($short_desc) : ?>
                            <p class="mt-6 text-[15px] leading-relaxed text-cream/60"><?php echo esc_html($short_desc); ?></p>
                        <?php endif; ?>
                        <?php if ($has_content) : ?>
                            <div class="mt-4 text-[15px] leading-relaxed text-cream [&_a]:text-gold [&_a]:underline [&_a]:underline-offset-4 [&>*+*]:mt-4">
                                <?php the_content(); ?>
                            </div>
                        <?php endif; ?>
                    <?php endif; ?>

                    <?php if ($features) : ?>
                        <h3 class="mt-12 text-xs font-normal uppercase tracking-[0.35em] text-cream sm:text-[13px]"><?php esc_html_e('Key Features', 'awards-gallery-theme'); ?></h3>
                        <ul class="mt-5 grid gap-2.5 sm:grid-cols-2">
                            <?php foreach ($features as $feature) : ?>
                                <li class="relative flex items-center gap-4 bg-ink-700 px-5 py-5 text-[15px] text-cream">
                                    <span class="absolute inset-x-0 top-0 h-0.5 bg-gold-line" aria-hidden="true"></span>
                                    <span class="h-1.5 w-1.5 shrink-0 rotate-45 bg-gold" aria-hidden="true"></span>
                                    <?php echo esc_html($feature); ?>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>

                    <?php if ($ideal_for) : ?>
                        <h3 class="mt-12 text-xs font-normal uppercase tracking-[0.35em] text-cream sm:text-[13px]"><?php esc_html_e('Ideal For', 'awards-gallery-theme'); ?></h3>
                        <ul class="mt-6 flex flex-wrap gap-x-8 gap-y-5">
                            <?php foreach ($ideal_for as $item) : ?>
                                <li class="flex items-center gap-2.5 text-xs uppercase tracking-[0.25em] text-cream sm:text-[13px]">
                                    <span class="h-1.5 w-1.5 shrink-0 rotate-45 bg-gold" aria-hidden="true"></span>
                                    <?php echo esc_html($item); ?>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </article>

    <?php if ($related) : ?>
        <section class="bg-gradient-to-b from-[#060504] to-[#0D0C0A] py-14 lg:py-20" aria-labelledby="also-available">
            <div class="mx-auto max-w-shell px-5 sm:px-8 lg:px-20">
                <div class="flex items-center justify-between gap-6">
                    <h2 id="also-available" class="flex items-center gap-4 text-xs uppercase tracking-eyebrow text-gold sm:text-[13px]">
                        <span class="h-px w-12 bg-gradient-to-r from-transparent via-gold/70 to-gold" aria-hidden="true"></span>
                        <?php esc_html_e('Also Available', 'awards-gallery-theme'); ?>
                    </h2>
                    <a href="<?php echo esc_url($all_url); ?>" class="group/link whitespace-nowrap text-xs uppercase tracking-[0.3em] text-gold sm:text-[13px]">
                        <?php esc_html_e('View All', 'awards-gallery-theme'); ?> <span class="inline-block transition-transform duration-300 group-hover/link:translate-x-1" aria-hidden="true">→</span>
                    </a>
                </div>

                <div class="mt-8 grid grid-cols-1 gap-6 sm:grid-cols-2 lg:mt-10 lg:grid-cols-3">
                    <?php foreach ($related as $i => $product) :
                        // Card image: the product's first gallery image, else its featured image.
                        $gallery  = (array) $field('awards_products_image_gallery', $product->ID, []);
                        $first    = $gallery[0] ?? null;
                        $image_id = is_array($first) ? (int) ($first['ID'] ?? 0) : (int) $first;
                        $image_id = $image_id ?: (int) get_post_thumbnail_id($product);
                        $kicker   = $field('awards_products_theme', $product->ID);
                    ?>
                        <a href="<?php echo esc_url(get_permalink($product)); ?>" class="reveal group flex flex-col border border-gold/60 bg-ink-900 transition-colors duration-300 hover:border-gold hover:text-cream" style="--d:<?php echo esc_attr(($i % 3) * 100); ?>ms">
                            <div class="overflow-hidden">
                                <?php if ($image_id) : ?>
                                    <?php echo wp_get_attachment_image($image_id, 'medium_large', false, [
                                        'class'   => 'aspect-[2/1] w-full object-cover transition duration-700 group-hover:scale-105',
                                        'sizes'   => '(min-width: 1024px) 33vw, (min-width: 640px) 50vw, 100vw',
                                        'loading' => 'lazy',
                                        'alt'     => '',
                                    ]); ?>
                                <?php else : ?>
                                    <div class="aspect-[2/1] w-full bg-ink-800"></div>
                                <?php endif; ?>
                            </div>
                            <div class="px-6 pb-7 pt-6">
                                <?php if ($kicker) : ?>
                                    <p class="text-xs uppercase tracking-[0.3em] text-gold sm:text-[13px]"><?php echo esc_html($kicker); ?></p>
                                <?php endif; ?>
                                <h3 class="<?php echo $kicker ? 'mt-2' : ''; ?> text-[15px] font-normal text-cream"><?php echo esc_html(get_the_title($product)); ?></h3>
                            </div>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>
    <?php endif; ?>

    <?php
    // Same "Ready to Order?" band as the Call to Action block; the button goes to the Contact Us page.
    echo render_block([
        'blockName'    => 'awards-gallery/cta',
        'attrs'        => [
            'eyebrow'             => '',
            'heading'             => __('Ready to Order?', 'awards-gallery-theme'),
            'headingScript'       => '',
            'text'                => __("Send us your requirements and we'll have a custom quote in 24–48 hours.", 'awards-gallery-theme'),
            'buttonText'          => __('Get a Quote', 'awards-gallery-theme'),
            'buttonUrl'           => home_url('/contact-us/'),
            'secondaryButtonText' => '',
        ],
        'innerBlocks'  => [],
        'innerHTML'    => '',
        'innerContent' => [],
    ]); // phpcs:ignore
    ?>

    <?php // Breadcrumb structured data, so search results can show "Home › Products › Product". ?>
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
