<?php
/**
 * Awards Gallery Contact Info — render.
 *
 * Left: contact cards, business hours and a tip. Right: the form area — whatever
 * inner blocks were added (e.g. a form plugin's shortcode). With no form yet,
 * the right column is left out and the cards spread across the full width.
 *
 * @var array  $attributes
 * @var string $content    Rendered inner blocks.
 */

if (!defined('ABSPATH')) {
    exit;
}

$has_form = trim(wp_strip_all_tags($content, true)) !== '' || preg_match('/<(form|iframe|input)\b/i', $content);
$card     = 'reveal border border-gold/80 bg-ink-900 px-5 py-5 sm:px-6';
$label    = 'text-xs uppercase tracking-[0.3em] text-gold sm:text-[13px]';
?>
<section <?php echo get_block_wrapper_attributes(['class' => 'bg-noise bg-ink py-16 lg:py-20']); ?>>
  <div class="mx-auto grid max-w-shell grid-cols-1 items-start gap-5 px-5 sm:px-8 lg:px-20 <?php echo $has_form ? 'lg:grid-cols-[1fr_1.55fr] lg:gap-6' : ''; ?>">

    <div class="grid grid-cols-1 gap-3 <?php echo $has_form ? 'sm:grid-cols-2 lg:grid-cols-1' : 'sm:grid-cols-2 lg:grid-cols-3'; ?>">
      <?php foreach (($attributes['cards'] ?? []) as $item) :
          $lines = array_filter(array_map('trim', explode("\n", $item['text'] ?? '')));
          $url   = trim($item['url'] ?? '');
      ?>
        <div class="<?php echo esc_attr($card); ?> flex gap-4">
          <span class="mt-0.5 shrink-0 text-gold"><?php echo awards_gallery_contact_icon($item['icon'] ?? ''); // phpcs:ignore — static SVG. ?></span>
          <div class="min-w-0">
            <?php if (!empty($item['title'])) : ?>
              <h3 class="<?php echo esc_attr($label); ?>"><?php echo esc_html($item['title']); ?></h3>
            <?php endif; ?>
            <?php if ($lines) : ?>
              <p class="mt-2 break-words text-sm leading-relaxed text-cream">
                <?php if ($url) : ?><a href="<?php echo esc_url($url); ?>" class="hover:text-gold"<?php echo preg_match('#^https?://#', $url) ? ' target="_blank" rel="noopener"' : ''; ?>><?php endif; ?>
                <?php echo implode('<br>', array_map('esc_html', $lines)); ?>
                <?php if ($url) : ?></a><?php endif; ?>
              </p>
            <?php endif; ?>
          </div>
        </div>
      <?php endforeach; ?>

      <?php if (!empty($attributes['hours'])) : ?>
        <div class="<?php echo esc_attr($card); ?>">
          <?php if (!empty($attributes['hoursTitle'])) : ?>
            <h3 class="<?php echo esc_attr($label); ?>"><?php echo esc_html($attributes['hoursTitle']); ?></h3>
          <?php endif; ?>
          <dl class="mt-4 flex flex-col gap-3 text-sm">
            <?php foreach ($attributes['hours'] as $row) :
                $time = $row['time'] ?? '';
                // Open hours are gold; a "Closed" day stays plain cream, as in the design.
                $closed = stripos($time, 'closed') !== false;
            ?>
              <div class="flex justify-between gap-4">
                <dt class="text-cream"><?php echo esc_html($row['day'] ?? ''); ?></dt>
                <dd class="text-right <?php echo $closed ? 'text-cream' : 'text-gold'; ?>"><?php echo esc_html($time); ?></dd>
              </div>
            <?php endforeach; ?>
          </dl>
        </div>
      <?php endif; ?>

      <?php if (!empty($attributes['tipTitle']) || !empty($attributes['tipText'])) : ?>
        <div class="<?php echo esc_attr($card); ?>">
          <?php if (!empty($attributes['tipTitle'])) : ?>
            <h3 class="<?php echo esc_attr($label); ?>"><?php echo esc_html($attributes['tipTitle']); ?></h3>
          <?php endif; ?>
          <?php if (!empty($attributes['tipText'])) : ?>
            <p class="mt-3 text-sm leading-relaxed text-cream"><?php echo esc_html($attributes['tipText']); ?></p>
          <?php endif; ?>
        </div>
      <?php endif; ?>
    </div>

    <?php if ($has_form) : ?>
      <div class="reveal border border-gold/80 bg-ink-900 px-5 py-6 sm:px-8 sm:py-7">
        <?php if (!empty($attributes['formTitle']) || !empty($attributes['formLabel'])) : ?>
          <p class="flex flex-wrap items-center gap-x-3 gap-y-1 border-b border-cream/15 pb-4 text-sm">
            <?php if (!empty($attributes['formTitle'])) : ?>
              <span class="text-cream"><?php echo esc_html($attributes['formTitle']); ?></span>
            <?php endif; ?>
            <?php if (!empty($attributes['formTitle']) && !empty($attributes['formLabel'])) : ?>
              <span class="text-gold" aria-hidden="true">•</span>
            <?php endif; ?>
            <?php if (!empty($attributes['formLabel'])) : ?>
              <span class="text-xs uppercase tracking-[0.3em] text-gold"><?php echo esc_html($attributes['formLabel']); ?></span>
            <?php endif; ?>
          </p>
        <?php endif; ?>
        <div class="mt-6"><?php echo $content; // phpcs:ignore — rendered inner blocks. ?></div>
        <?php if (!empty($attributes['formNote'])) : ?>
          <p class="mt-4 text-center text-xs text-cream/80"><?php echo esc_html($attributes['formNote']); ?></p>
        <?php endif; ?>
      </div>
    <?php endif; ?>
  </div>
</section>
