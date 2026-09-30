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
        <PanelBody title={__('Intro', 'awards-gallery-theme')} initialOpen>
          <TextControl label={__('Eyebrow', 'awards-gallery-theme')} value={attributes.eyebrow} onChange={set('eyebrow')} />
          <TextControl label={__('Heading', 'awards-gallery-theme')} value={attributes.heading} onChange={set('heading')} />
          <TextControl label={__('Heading (script)', 'awards-gallery-theme')} value={attributes.headingScript} onChange={set('headingScript')} />
          <TextareaControl label={__('Text', 'awards-gallery-theme')} value={attributes.text} onChange={set('text')} />
        </PanelBody>
        <PanelBody title={__('Products', 'awards-gallery-theme')} initialOpen={false}>
          <TextControl
            label={__('Card link text', 'awards-gallery-theme')}
            help={__('Optional, e.g. "View Details". Leave empty to hide.', 'awards-gallery-theme')}
            value={attributes.linkText}
            onChange={set('linkText')}
          />
          <Repeater
            items={attributes.items}
            onChange={set('items')}
            addLabel={__('Add product', 'awards-gallery-theme')}
            newItem={{ kicker: '', title: '', url: '', image: { id: 0, url: '' }, imageUrl: '' }}
            fields={[
              { name: 'image', label: __('Image', 'awards-gallery-theme'), type: 'media' },
              { name: 'kicker', label: __('Kicker', 'awards-gallery-theme'), type: 'text' },
              { name: 'title', label: __('Title', 'awards-gallery-theme'), type: 'text' },
              { name: 'url', label: __('Link URL', 'awards-gallery-theme'), type: 'text' },
            ]}
          />
        </PanelBody>
        <PanelBody title={__('Button & background', 'awards-gallery-theme')} initialOpen={false}>
          <TextControl label={__('Button text', 'awards-gallery-theme')} value={attributes.buttonText} onChange={set('buttonText')} />
          <TextControl label={__('Button URL', 'awards-gallery-theme')} value={attributes.buttonUrl} onChange={set('buttonUrl')} />
          <MediaField label={__('Background image', 'awards-gallery-theme')} value={attributes.background} onChange={set('background')} />
          <TextControl label={__('Section ID (anchor)', 'awards-gallery-theme')} value={attributes.sectionId} onChange={set('sectionId')} />
        </PanelBody>
      </InspectorControls>

      <div {...useBlockProps()}>
        <ServerSideRender block="awards-gallery/collection" attributes={attributes} />
      </div>
    </>
  );
}
