import { registerBlockType } from '@wordpress/blocks';
import { InnerBlocks } from '@wordpress/block-editor';
import metadata from './block.json';
import Edit from './edit';
import './style.css';

registerBlockType(metadata.name, {
  ...metadata,
  edit: Edit,
  // The form column is saved as inner blocks (e.g. a Shortcode block); everything else is rendered in PHP.
  save: () => <InnerBlocks.Content />,
});
