<?php
/**
 * Customizer repeater for the footer social links. The rows are stored as JSON in the
 * setting and edited by assets/js/customizer/social-links.js.
 */

if (!defined('ABSPATH')) {
    exit;
}

class Awards_Gallery_Social_Links_Control extends WP_Customize_Control
{
    public $type = 'awards_social_links';

    /**
     * Icon thumbnails for the saved rows, so the script can show them without fetching.
     */
    public function to_json()
    {
        parent::to_json();
        $icons = [];
        $rows  = json_decode((string) $this->value(), true);
        foreach (is_array($rows) ? $rows : [] as $row) {
            if (!empty($row['icon'])) {
                $icons[$row['icon']] = wp_get_attachment_image_url($row['icon'], 'thumbnail');
            }
        }
        $this->json['icons'] = $icons;
    }

    public function render_content()
    {
        ?>
        <span class="customize-control-title"><?php echo esc_html($this->label); ?></span>
        <?php if ($this->description) : ?>
            <span class="description customize-control-description"><?php echo esc_html($this->description); ?></span>
        <?php endif; ?>
        <div class="ag-social-rows"></div>
        <button type="button" class="button ag-social-add"><?php esc_html_e('Add social link', 'awards-gallery-theme'); ?></button>
        <?php
    }
}
