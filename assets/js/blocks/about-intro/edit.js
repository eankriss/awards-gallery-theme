import { __ } from '@wordpress/i18n';
import { useBlockProps, InspectorControls } from '@wordpress/block-editor';
import { PanelBody, TextControl, TextareaControl } from '@wordpress/components';
import ServerSideRender from '@wordpress/server-side-render';
import { MediaField, Repeater } from '../_shared/controls';

export default function Edit({ attributes, setAttributes }) {
  const set = (key) => (value) => setAttributes({ [key]: value });

  return (
    <>
      <InspectorControls>
        <PanelBody title={__('Content', 'awards-gallery-theme')} initialOpen>
          <TextControl label={__('Eyebrow', 'awards-gallery-theme')} value={attributes.eyebrow} onChange={set('eyebrow')} />
          <TextControl label={__('Heading', 'awards-gallery-theme')} value={attributes.heading} onChange={set('heading')} />
          <TextControl label={__('Heading (script)', 'awards-gallery-theme')} value={attributes.headingScript} onChange={set('headingScript')} />
          <TextareaControl label={__('Text', 'awards-gallery-theme')} value={attributes.text} onChange={set('text')} />
        </PanelBody>
        <PanelBody title={__('Image', 'awards-gallery-theme')} initialOpen={false}>
          <MediaField label={__('Image', 'awards-gallery-theme')} value={attributes.image} onChange={set('image')} />
          <TextControl
            label={__('Badge value', 'awards-gallery-theme')}
            help={__('Leave value and label empty to hide the badge.', 'awards-gallery-theme')}
            value={attributes.badgeValue}
            onChange={set('badgeValue')}
          />
          <TextControl label={__('Badge label', 'awards-gallery-theme')} value={attributes.badgeLabel} onChange={set('badgeLabel')} />
        </PanelBody>
        <PanelBody title={__('Feature cards', 'awards-gallery-theme')} initialOpen={false}>
          <Repeater
            items={attributes.features}
            onChange={set('features')}
            addLabel={__('Add card', 'awards-gallery-theme')}
            newItem={{ title: '', text: '' }}
            fields={[
              { name: 'title', label: __('Title', 'awards-gallery-theme'), type: 'text' },
              { name: 'text', label: __('Text', 'awards-gallery-theme'), type: 'textarea' },
            ]}
          />
        </PanelBody>
      </InspectorControls>

      <div {...useBlockProps()}>
        <ServerSideRender block="awards-gallery/about-intro" attributes={attributes} />
      </div>
    </>
  );
}
