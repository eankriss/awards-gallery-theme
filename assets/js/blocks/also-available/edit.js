import { __ } from '@wordpress/i18n';
import { useBlockProps, InspectorControls } from '@wordpress/block-editor';
import { PanelBody, TextControl, TextareaControl } from '@wordpress/components';
import ServerSideRender from '@wordpress/server-side-render';
import { Repeater } from '../_shared/controls';

// Keys must match awards_gallery_also_available_icon() in functions-helpers.php.
const ICONS = [
  { label: __('Medal', 'awards-gallery-theme'), value: 'medal' },
  { label: __('Building', 'awards-gallery-theme'), value: 'landmark' },
  { label: __('Plate / sign', 'awards-gallery-theme'), value: 'plate' },
  { label: __('Sword', 'awards-gallery-theme'), value: 'sword' },
  { label: __('Gift', 'awards-gallery-theme'), value: 'gift' },
  { label: __('Message', 'awards-gallery-theme'), value: 'message' },
  { label: __('Trophy', 'awards-gallery-theme'), value: 'trophy' },
  { label: __('Star', 'awards-gallery-theme'), value: 'star' },
];

export default function Edit({ attributes, setAttributes }) {
  const set = (key) => (value) => setAttributes({ [key]: value });

  return (
    <>
      <InspectorControls>
        <PanelBody title={__('Intro', 'awards-gallery-theme')} initialOpen>
          <TextControl label={__('Eyebrow', 'awards-gallery-theme')} value={attributes.eyebrow} onChange={set('eyebrow')} />
          <TextControl label={__('Heading', 'awards-gallery-theme')} value={attributes.heading} onChange={set('heading')} />
          <TextControl label={__('Heading (gold part)', 'awards-gallery-theme')} value={attributes.headingScript} onChange={set('headingScript')} />
          <TextareaControl label={__('Text', 'awards-gallery-theme')} value={attributes.text} onChange={set('text')} />
        </PanelBody>
        <PanelBody title={__('Items', 'awards-gallery-theme')} initialOpen={false}>
          <Repeater
            items={attributes.items}
            onChange={set('items')}
            addLabel={__('Add item', 'awards-gallery-theme')}
            newItem={{ icon: 'star', title: '', text: '' }}
            fields={[
              { name: 'icon', label: __('Icon', 'awards-gallery-theme'), type: 'select', options: ICONS },
              { name: 'title', label: __('Title', 'awards-gallery-theme'), type: 'text' },
              { name: 'text', label: __('Text', 'awards-gallery-theme'), type: 'textarea' },
            ]}
          />
        </PanelBody>
      </InspectorControls>

      <div {...useBlockProps()}>
        <ServerSideRender block="awards-gallery/also-available" attributes={attributes} />
      </div>
    </>
  );
}
