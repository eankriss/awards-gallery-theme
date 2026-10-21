<?php
/**
 * Product category archive (taxonomy "product-category", e.g. /product-category/crystal-awards/).
 *
 * Breadcrumb hero, "Browse by" category sidebar, result count, sort dropdown,
 * product card grid with "Load More", and the quote CTA. Query setup (18 per page,
 * sorting) lives in inc/awards-archive.php; behaviour in assets/js/category-load-more.js.
 */

if (!defined('ABSPATH')) {
    exit;
}

get_header();

global $wp_query;

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

$term    = get_queried_object();
$all_url = awards_gallery_products_url();
$total   = (int) $wp_query->found_posts;
$sort    = awards_gallery_current_sort();

// Category chain, root first: used for breadcrumbs and category navigation.
$ancestors = array_reverse(get_ancestors($term->term_id, 'product-category', 'taxonomy'));

$crumbs = [
    [__('Home', 'awards-gallery-theme'), home_url('/')],
    [__('Products', 'awards-gallery-theme'), $all_url],
];
foreach ($ancestors as $ancestor_id) {
    $ancestor = get_term($ancestor_id, 'product-category');
    $link     = $ancestor instanceof WP_Term ? get_term_link($ancestor) : null;
    if ($link && !is_wp_error($link)) {
        $crumbs[] = [$ancestor->name, $link];
    }
}
$crumbs[] = [$term->name, get_term_link($term)];

// Browse the sibling categories under the same top-level collection.
$root_id = $ancestors ? (int) $ancestors[0] : (int) $term->term_id;
$browse  = get_terms(['taxonomy' => 'product-category', 'parent' => $root_id, 'hide_empty' => true]);
$browse  = is_wp_error($browse) ? [] : $browse;

if (!$browse) {
    $browse = get_terms(['taxonomy' => 'product-category', 'parent' => 0, 'hide_empty' => true]);
    $browse = is_wp_error($browse) ? [] : $browse;
}

$subcategory_values = [];
$subcategory_products = get_posts([
    'post_type' => 'product-page', 'post_status' => 'publish', 'posts_per_page' => -1, 'fields' => 'ids',
    'tax_query' => [['taxonomy' => 'product-category', 'terms' => $term->term_id]],
    'meta_query' => [['key' => 'awards_product_subcategory', 'compare' => 'EXISTS']],
]);
foreach ($subcategory_products as $subcategory_product_id) {
    $subcategory_value = trim((string) get_post_meta($subcategory_product_id, 'awards_product_subcategory', true));
    if ($subcategory_value !== '') $subcategory_values[$subcategory_value] = $subcategory_value;
}
natcasesort($subcategory_values);
$current_subcategory = awards_gallery_current_subcategory();

$eyebrow_term = $ancestors ? get_term($ancestors[count($ancestors) - 1], 'product-category') : null;
$eyebrow      = $eyebrow_term instanceof WP_Term ? $eyebrow_term->name : __('Our Collection', 'awards-gallery-theme');
$next_url = get_next_posts_page_link();
?>
<main id="main">

    <?php // Hero: shares the product-page banner and top spacing. ?>
    <header class="bg-black">
        <div class="relative isolate overflow-hidden">
            <img src="<?php echo esc_url(get_theme_file_uri('/assets/img/award template banner.webp')); ?>" alt="" class="absolute inset-0 -z-10 h-full w-full object-cover opacity-80" fetchpriority="high">

            <div class="mx-auto flex max-w-shell flex-col justify-center px-5 pb-10 pt-12 sm:px-8 lg:min-h-[293px] lg:px-20 lg:pb-[46px] lg:pt-[82px]">
                <nav aria-label="<?php esc_attr_e('Breadcrumb', 'awards-gallery-theme'); ?>">
                    <ol class="flex flex-wrap items-center gap-x-2.5 gap-y-1 text-base leading-6 text-cream lg:text-lg">
                        <?php foreach ($crumbs as $i => [$label, $url]) : ?>
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

                <p class="animate-rise mt-10 text-xs uppercase leading-6 tracking-[0.4em] text-gold sm:text-base lg:mt-[49px]" style="animation-delay:.1s"><?php echo esc_html($eyebrow); ?></p>
                <h1 class="animate-rise mt-2 text-4xl font-normal uppercase leading-none text-cream sm:text-5xl lg:text-[60px] lg:tracking-[-1.5px]" style="animation-delay:.2s"><?php single_term_title(); ?></h1>
                <?php if ($term->description) : ?>
                    <p class="animate-rise mt-5 max-w-2xl text-[15px] leading-relaxed text-cream/70" style="animation-delay:.3s"><?php echo esc_html($term->description); ?></p>
                <?php endif; ?>
            </div>
        </div>
    </header>

    <div class="bg-[#0A0908] pb-16 pt-10 lg:pb-24 lg:pt-14">
        <div class="mx-auto max-w-shell px-5 sm:px-8 lg:px-20">

            <?php // Filter panel: category browsing stays compact and consistent on every breakpoint. ?>
            <aside class="reveal rounded-sm border border-cream/15 bg-ink-900/60 px-5 py-5 sm:px-7 sm:py-6" aria-labelledby="browse-by">
                <div class="flex flex-col gap-5 sm:flex-row sm:items-end sm:justify-between">
                    <div>
                        <p class="text-[11px] uppercase tracking-[0.32em] text-gold sm:text-xs"><?php esc_html_e('Explore the collection', 'awards-gallery-theme'); ?></p>
                        <h2 id="browse-by" class="mt-2 text-xl font-normal text-cream sm:text-2xl"><?php esc_html_e('Browse By', 'awards-gallery-theme'); ?></h2>
                    </div>
                </div>

                <div class="ag-filter-row">
                <div class="ag-filter-field relative mt-5 max-w-xl">
                    <label for="award-browse" class="sr-only"><?php esc_html_e('Choose an award category', 'awards-gallery-theme'); ?></label>
                    <select id="award-browse" class="ag-native-select sr-only" data-browse-select tabindex="-1" aria-hidden="true">
                        <?php foreach ($browse as $browse_term) :
                            $browse_link = get_term_link($browse_term);
                            if (is_wp_error($browse_link)) {
                                continue;
                            }
                            ?>
                            <option value="<?php echo esc_url($browse_link); ?>"<?php selected((int) $browse_term->term_id, (int) $term->term_id); ?>><?php echo esc_html($browse_term->name); ?></option>
                        <?php endforeach; ?>
                    </select>
                    <button type="button" class="ag-dropdown-toggle" aria-expanded="false" aria-controls="award-browse-menu" data-dropdown-toggle>
                        <span data-dropdown-value><?php echo esc_html($term->name); ?></span>
                        <svg class="ag-filter-chevron" viewBox="0 0 12 8" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true"><path d="m1 1.5 5 5 5-5"/></svg>
                    </button>
                    <ul id="award-browse-menu" class="ag-dropdown-menu" role="listbox" hidden>
                        <?php foreach ($browse as $browse_term) : $browse_link = get_term_link($browse_term); if (is_wp_error($browse_link)) continue; ?>
                            <li><button type="button" role="option" class="ag-dropdown-option<?php echo ((int) $browse_term->term_id === (int) $term->term_id) ? ' is-selected' : ''; ?>" data-dropdown-option data-value="<?php echo esc_url($browse_link); ?>" data-label="<?php echo esc_attr($browse_term->name); ?>"><?php echo esc_html($browse_term->name); ?></button></li>
                        <?php endforeach; ?>
                    </ul>
                </div>

                <?php if ($subcategory_values) : ?>
                    <form method="post" action="<?php echo esc_url(get_term_link($term)); ?>" class="ag-theme-filter-form">
                    <input type="hidden" name="sort" value="<?php echo esc_attr($sort); ?>">
                    <div class="ag-filter-field relative mt-4 max-w-xl md:mt-5 md:inline-block md:w-[17rem] md:max-w-none lg:w-[20rem]">
                        <label for="award-subcategory" class="sr-only"><?php esc_html_e('Filter by subcategory', 'awards-gallery-theme'); ?></label>
                        <select id="award-subcategory" name="subcategory" class="ag-native-select sr-only" data-theme-select tabindex="-1" aria-hidden="true">
                            <option value=""><?php esc_html_e('Subcategory', 'awards-gallery-theme'); ?></option>
                            <?php foreach ($subcategory_values as $subcategory_value) : ?>
                                <option value="<?php echo esc_attr($subcategory_value); ?>"<?php selected($current_subcategory, $subcategory_value); ?>><?php echo esc_html($subcategory_value); ?></option>
                            <?php endforeach; ?>
                        </select>
                        <button type="button" class="ag-dropdown-toggle" aria-expanded="false" aria-controls="award-subcategory-menu" data-dropdown-toggle><span data-dropdown-value><?php echo esc_html($current_subcategory ?: __('Subcategory', 'awards-gallery-theme')); ?></span><svg class="ag-filter-chevron" viewBox="0 0 12 8" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true"><path d="m1 1.5 5 5 5-5"/></svg></button>
                        <ul id="award-subcategory-menu" class="ag-dropdown-menu" role="listbox" hidden>
                            <li><button type="button" role="option" class="ag-dropdown-option<?php echo !$current_subcategory ? ' is-selected' : ''; ?>" data-dropdown-option data-value="" data-label="<?php esc_attr_e('Subcategory', 'awards-gallery-theme'); ?>"><?php esc_html_e('Subcategory', 'awards-gallery-theme'); ?></button></li>
                            <?php foreach ($subcategory_values as $subcategory_value) : ?><li><button type="button" role="option" class="ag-dropdown-option<?php echo $current_subcategory === $subcategory_value ? ' is-selected' : ''; ?>" data-dropdown-option data-value="<?php echo esc_attr($subcategory_value); ?>" data-label="<?php echo esc_attr($subcategory_value); ?>"><?php echo esc_html($subcategory_value); ?></button></li><?php endforeach; ?>
                        </ul>
                    </div>
                    </form>
                <?php endif; ?>
                </div>
            </aside>

            <section class="mt-10 min-w-0" aria-label="<?php esc_attr_e('Products', 'awards-gallery-theme'); ?>">

                <?php // Results toolbar: count and sorting have separate visual anchors. ?>
                <div class="flex flex-col gap-5 border-b border-cream/15 pb-5 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <p class="text-[11px] uppercase tracking-[0.32em] text-gold sm:text-xs"><?php esc_html_e('Showing', 'awards-gallery-theme'); ?></p>
                        <p class="mt-1 text-sm text-cream/65">
                        <?php echo esc_html(sprintf(_n('%s product', '%s products', $total, 'awards-gallery-theme'), number_format_i18n($total))); ?>
                        </p>
                    </div>

                    <form method="post" action="<?php echo esc_url(get_term_link($term)); ?>" class="ag-sort-control flex items-center justify-between gap-4 sm:justify-start">
                        <input type="hidden" name="subcategory" value="<?php echo esc_attr($current_subcategory); ?>">
                        <label for="award-sort" class="ag-sort-label text-[11px] uppercase tracking-[0.25em] text-cream/55 sm:text-xs"><?php esc_html_e('Sort By', 'awards-gallery-theme'); ?></label>
                        <div class="ag-filter-field relative">
                            <select id="award-sort" name="sort" class="ag-native-select sr-only" data-sort-select tabindex="-1" aria-hidden="true">
                                <?php foreach (awards_gallery_award_sort_options() as $value => $label) : ?>
                                    <option value="<?php echo esc_attr($value); ?>"<?php selected($sort, $value); ?>><?php echo esc_html($label); ?></option>
                                <?php endforeach; ?>
                            </select>
                            <button type="button" class="ag-dropdown-toggle ag-sort-toggle" aria-expanded="false" aria-controls="award-sort-menu" data-dropdown-toggle>
                                <span data-dropdown-value><?php echo esc_html(awards_gallery_award_sort_options()[$sort]); ?></span>
                                <svg class="ag-filter-chevron" viewBox="0 0 12 8" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true"><path d="m1 1.5 5 5 5-5"/></svg>
                            </button>
                            <ul id="award-sort-menu" class="ag-dropdown-menu ag-sort-menu" role="listbox" hidden>
                                <?php foreach (awards_gallery_award_sort_options() as $value => $label) : ?>
                                    <li><button type="button" role="option" class="ag-dropdown-option<?php echo $sort === $value ? ' is-selected' : ''; ?>" data-dropdown-option data-value="<?php echo esc_attr($value); ?>" data-label="<?php echo esc_attr($label); ?>"><?php echo esc_html($label); ?></button></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                        <noscript><button type="submit" class="btn-outline !h-10 !px-5"><?php esc_html_e('Apply', 'awards-gallery-theme'); ?></button></noscript>
                    </form>
                </div>

                <?php if (have_posts()) : ?>
                    <div class="mt-8 grid grid-cols-2 gap-4 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 xl:gap-6" data-award-grid data-total-products="<?php echo esc_attr($total); ?>">
                        <?php
                        $i = 0;
                        while (have_posts()) :
                            the_post();

                            // Card image: always use the product's featured image.
                            $image_id = (int) get_post_thumbnail_id();
                            $kicker   = $field('awards_product_subcategory', get_the_ID());
                            $sku      = trim((string) $field('awards_products_sku', get_the_ID()));
                            if ($sku === '') {
                                $title_parts = preg_split('/\s+/', trim(get_the_title()));
                                $sku = strtoupper((string) end($title_parts));
                            }
                            ?>
                            <a href="<?php the_permalink(); ?>" class="reveal group flex min-w-0 flex-col border border-gold/60 bg-ink-900 transition-colors duration-300 hover:border-gold hover:text-cream" data-product-index="<?php echo esc_attr($i); ?>" style="--d:<?php echo esc_attr(($i % 6) * 70); ?>ms">
                                <div class="relative overflow-hidden bg-black">
                                    <?php if ($image_id) : ?>
                                        <?php echo wp_get_attachment_image($image_id, 'medium_large', false, [
                                            'class'   => 'aspect-[4/3] w-full object-cover object-top transition duration-700 group-hover:scale-105',
                                            'sizes'   => '(min-width: 1280px) 28vw, (min-width: 640px) 45vw, 100vw',
                                            'loading' => $i < 3 ? 'eager' : 'lazy',
                                            'alt'     => '',
                                        ]); ?>
                                    <?php else : ?>
                                        <?php echo awards_gallery_card_placeholder('aspect-[4/3]'); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                                    <?php endif; ?>
                                    <div class="pointer-events-none absolute inset-x-0 bottom-0 h-1/4 bg-gradient-to-t from-ink-900 to-transparent" aria-hidden="true"></div>
                                </div>
                                <div class="flex flex-1 flex-col px-3 pb-4 pt-4 sm:px-3.5 sm:pb-5 sm:pt-5 xl:px-3 xl:pb-4 xl:pt-4">
                                    <?php if ($kicker) : ?>
                                        <p class="text-[9px] uppercase leading-tight tracking-[0.18em] text-gold sm:text-[10px]"><?php echo esc_html($kicker); ?></p>
                                    <?php endif; ?>
                                    <h3 class="<?php echo $kicker ? 'mt-1.5' : ''; ?> break-words text-[11px] font-normal leading-snug text-cream sm:text-xs"><?php echo esc_html($sku); ?></h3>
                                    <span class="mt-auto inline-flex items-center gap-1.5 pt-4 text-[9px] uppercase tracking-[0.18em] text-gold sm:text-[10px] sm:tracking-[0.2em]">
                                        <?php esc_html_e('View Details', 'awards-gallery-theme'); ?>
                                        <span class="inline-block transition-transform duration-300 group-hover:translate-x-1" aria-hidden="true">→</span>
                                    </span>
                                </div>
                            </a>
                            <?php
                            $i++;
                        endwhile;
                        ?>
                    </div>

                    <?php // Keep a button available when 16–18 products need to reveal at a narrower breakpoint. ?>
                    <?php if ($next_url || $total > 15) : ?>
                        <?php // Works as a normal "next page" link without JS; the script swaps it for in-place loading. ?>
                        <div class="mt-12 flex justify-center" data-load-more-wrap>
                            <a href="#" data-load-url="<?php echo esc_url($next_url); ?>" class="btn-outline min-w-48" data-load-more data-loading="<?php esc_attr_e('Loading…', 'awards-gallery-theme'); ?>" aria-label="<?php echo esc_attr(sprintf(__('Load more products. Showing %1$s of %2$s.', 'awards-gallery-theme'), number_format_i18n($wp_query->post_count), number_format_i18n($total))); ?>"><?php esc_html_e('Load More Products', 'awards-gallery-theme'); ?></a>
                        </div>
                    <?php endif; ?>
                <?php else : ?>
                    <div class="mt-10 border border-gold/30 bg-ink-900 px-6 py-14 text-center">
                        <p class="text-[15px] text-cream/70"><?php esc_html_e('No products in this category yet.', 'awards-gallery-theme'); ?></p>
                        <a href="<?php echo esc_url($all_url); ?>" class="btn-outline mt-6"><?php esc_html_e('View All Products', 'awards-gallery-theme'); ?></a>
                    </div>
                <?php endif; ?>
            </section>
        </div>
    </div>

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

    <?php // Breadcrumb structured data, so search results can show "Home › Products › Category". ?>
    <script type="application/ld+json"><?php echo wp_json_encode([
        '@context'        => 'https://schema.org',
        '@type'           => 'BreadcrumbList',
        'itemListElement' => array_map(fn($crumb, $i) => [
            '@type'    => 'ListItem',
            'position' => $i + 1,
            'name'     => $crumb[0],
            'item'     => $crumb[1],
        ], $crumbs, array_keys($crumbs)),
    ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE); ?></script>
</main>
<?php
get_footer();
