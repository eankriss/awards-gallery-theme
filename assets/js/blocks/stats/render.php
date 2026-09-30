<?php
/**
 * Awards Gallery Stats — render.
 *
 * @var array $attributes
 */

if (!defined('ABSPATH')) {
    exit;
}

$items = $attributes['items'] ?? [];
$last  = count($items) - 1;
?>
<section <?php echo get_block_wrapper_attributes(['class' => 'bg-gold text-ink']); ?>>
  <dl class="mx-auto grid max-w-shell grid-cols-2 lg:grid-cols-4">
    <?php foreach ($items as $i => $item) :
        $number = (int) preg_replace('/\D+/', '', $item['value'] ?? '');
        $suffix = $item['suffix'] ?? '';
    ?>
      <div class="reveal flex flex-col items-center px-4 py-12 text-center lg:py-14<?php echo $i < $last ? ' lg:border-r lg:border-ink/15' : ''; ?><?php echo $i % 2 === 0 ? ' border-r border-ink/15' : ''; ?>" style="--d:<?php echo esc_attr(($i % 4) * 100); ?>ms">
        <dt class="order-2 mt-2 max-w-[14rem] text-[11px] uppercase tracking-[0.3em] text-ink/75 sm:text-[15px]"><?php echo esc_html($item['label'] ?? ''); ?></dt>
        <dd class="order-1 text-[clamp(2rem,1.25rem+2.5vw,3rem)] font-normal leading-tight" data-count="<?php echo esc_attr($number); ?>" data-suffix="<?php echo esc_attr($suffix); ?>"><?php echo esc_html(number_format_i18n($number) . $suffix); ?></dd>
      </div>
    <?php endforeach; ?>
  </dl>
</section>
