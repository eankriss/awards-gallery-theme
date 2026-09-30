<?php
/**
 * Awards Gallery Marquee — render.
 *
 * @var array $attributes
 */

if (!defined('ABSPATH')) {
    exit;
}

$items = array_filter(array_map(fn($item) => trim($item['text'] ?? ''), $attributes['items'] ?? []));
if (!$items) {
    return;
}

// The track holds two identical groups and scrolls by -50%, so the loop is seamless.
$group = function ($hidden) use ($items) {
    ?>
    <div class="flex items-center gap-14"<?php echo $hidden ? ' aria-hidden="true"' : ''; ?>>
      <?php foreach ($items as $text) : ?>
        <span><?php echo esc_html($text); ?></span><span class="text-[10px] text-ink/40">◆</span>
      <?php endforeach; ?>
    </div>
    <?php
};
?>
<div <?php echo get_block_wrapper_attributes(['class' => 'overflow-hidden bg-gold py-4']); ?> aria-label="<?php esc_attr_e('Highlights', 'awards-gallery-theme'); ?>">
  <div class="flex w-max animate-marquee gap-14 whitespace-nowrap text-xs font-medium uppercase tracking-[0.2em] text-ink sm:text-[15px]">
    <?php $group(false); ?>
    <?php $group(true); ?>
  </div>
</div>
