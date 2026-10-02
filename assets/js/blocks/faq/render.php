<?php
/**
 * Awards Gallery FAQ — render.
 *
 * Native <details>/<summary> accordion: works without JS and is keyboard and
 * screen-reader friendly. A shared name makes it exclusive (one open at a time)
 * in browsers that support it; others simply allow several open.
 *
 * @var array $attributes
 */

if (!defined('ABSPATH')) {
    exit;
}

// Questions without an answer are hidden on the site so visitors never open an empty
// panel. The editor preview (a REST request) still lists them, with a reminder.
$in_editor = defined('REST_REQUEST') && REST_REQUEST;
$items     = array_filter($attributes['items'] ?? [], fn($item) => trim($item['question'] ?? '') !== '' && ($in_editor || trim($item['answer'] ?? '') !== ''));
$group = !empty($attributes['singleOpen']) ? wp_unique_id('ag-faq-') : '';
?>
<section <?php echo get_block_wrapper_attributes(['class' => 'bg-ink-900 py-20 lg:py-24']); ?>>
  <div class="mx-auto max-w-[900px] px-5 sm:px-8">
    <div class="reveal text-center">
      <?php if (!empty($attributes['heading'])) : ?>
        <h2 class="text-3xl font-bold leading-tight text-white sm:text-4xl lg:text-[44px]"><?php echo esc_html($attributes['heading']); ?></h2>
      <?php endif; ?>
      <?php if (!empty($attributes['text'])) : ?>
        <p class="mx-auto mt-4 max-w-xl text-[15px] leading-relaxed text-cream/80"><?php echo esc_html($attributes['text']); ?></p>
      <?php endif; ?>
    </div>

    <?php if ($items) : ?>
      <div class="mt-10 flex flex-col gap-3 lg:mt-12">
        <?php foreach ($items as $i => $item) : ?>
          <details class="reveal group rounded-lg border border-gold/70 bg-[#141311] transition-colors duration-300 open:border-gold hover:border-gold" data-accordion style="--d:<?php echo esc_attr(min($i, 5) * 60); ?>ms"<?php echo $group ? ' name="' . esc_attr($group) . '"' : ''; ?>>
            <summary class="flex cursor-pointer list-none items-center justify-between gap-4 px-5 py-4 text-sm font-semibold text-white sm:px-6 sm:text-[15px] [&::-webkit-details-marker]:hidden">
              <?php echo esc_html($item['question']); ?>
              <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-ink text-gold transition-transform duration-300 group-open:rotate-45 group-data-[state=closing]:rotate-0" aria-hidden="true">
                <svg class="h-3 w-3" viewBox="0 0 12 12" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"><path d="M6 1.5v9M1.5 6h9"/></svg>
              </span>
            </summary>
            <?php // Outer div is what animates (height); padding sits on the inner one so it can reach 0. ?>
            <div class="overflow-hidden" data-accordion-panel>
            <div class="px-5 pb-5 text-[15px] leading-relaxed text-cream/85 sm:px-6 [&_p+p]:mt-3">
              <?php if (trim($item['answer'] ?? '') === '') : ?>
                <p class="italic text-gold"><?php esc_html_e('No answer yet — this question is hidden on the site until you add one in the block settings.', 'awards-gallery-theme'); ?></p>
              <?php else : ?>
                <?php echo wpautop(esc_html($item['answer'])); // phpcs:ignore — escaped, then paragraphs added. ?>
              <?php endif; ?>
            </div>
            </div>
          </details>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </div>
</section>
