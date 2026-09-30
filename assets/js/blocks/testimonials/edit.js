import { __ } from '@wordpress/i18n';
import { useBlockProps, InspectorControls } from '@wordpress/block-editor';
import { PanelBody, TextControl } from '@wordpress/components';
import ServerSideRender from '@wordpress/server-side-render';
import { Repeater } from '../_shared/controls';

export default function Edit({ attributes, setAttributes }) {
  const set = (key) => (value) => setAttributes({ [key]: value });

  return (
    <>
      <InspectorControls>
        <PanelBody title={__('Intro', 'awards-gallery-theme')} initialOpen>
          <TextControl label={__('Eyebrow', 'awards-gallery-theme')} value={attributes.eyebrow} onChange={set('eyebrow')} />
          <TextControl label={__('Heading', 'awards-gallery-theme')} value={attributes.heading} onChange={set('heading')} />
          <TextControl label={__('Heading (gold part)', 'awards-gallery-theme')} value={attributes.headingScript} onChange={set('headingScript')} />
        </PanelBody>
        <PanelBody title={__('Testimonials', 'awards-gallery-theme')} initialOpen={false}>
          <Repeater
            items={attributes.items}
            onChange={set('items')}
            addLabel={__('Add testimonial', 'awards-gallery-theme')}
            newItem={{ quote: '', name: '', role: '' }}
            fields={[
              { name: 'quote', label: __('Quote', 'awards-gallery-theme'), type: 'textarea' },
              { name: 'name', label: __('Name', 'awards-gallery-theme'), type: 'text' },
              { name: 'role', label: __('Role', 'awards-gallery-theme'), type: 'text' },
            ]}
          />
        </PanelBody>
      </InspectorControls>

      <div {...useBlockProps()}>
        <ServerSideRender block="awards-gallery/testimonials" attributes={attributes} />
      </div>
    </>
  );
}
