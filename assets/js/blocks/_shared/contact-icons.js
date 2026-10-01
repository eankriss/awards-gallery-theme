/**
 * Icon choices for the Contact Info and Visit Us blocks.
 * Keys must match awards_gallery_contact_icon() in contact-info/functions-helpers.php.
 */
import { __ } from '@wordpress/i18n';

export const CONTACT_ICONS = [
  { label: __('Map pin', 'awards-gallery-theme'), value: 'pin' },
  { label: __('Phone', 'awards-gallery-theme'), value: 'phone' },
  { label: __('Email', 'awards-gallery-theme'), value: 'mail' },
  { label: __('Message', 'awards-gallery-theme'), value: 'message' },
  { label: __('Clock', 'awards-gallery-theme'), value: 'clock' },
];
