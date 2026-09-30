import { __ } from '@wordpress/i18n';
import { useBlockProps, InspectorControls } from '@wordpress/block-editor';
import { PanelBody, TextControl } from '@wordpress/components';
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
          <TextControl label={__('Heading (gold part)', 'awards-gallery-theme')} value={attributes.headingScript} onChange={set('headingScript')} />
          <MediaField label={__('Decorative image', 'awards-gallery-theme')} value={attributes.image} onChange={set('image')} />
        </PanelBody>
        <PanelBody title={__('Steps', 'awards-gallery-theme')} initialOpen={false}>
          <Repeater
            items={attributes.steps}
            onChange={set('steps')}
            addLabel={__('Add step', 'awards-gallery-theme')}
            newItem={{ title: '', text: '' }}
            fields={[
              { name: 'title', label: __('Title', 'awards-gallery-theme'), type: 'text' },
              { name: 'text', label: __('Text', 'awards-gallery-theme'), type: 'textarea' },
            ]}
          />
        </PanelBody>
      </InspectorControls>

      <div {...useBlockProps()}>
        <ServerSideRender block="awards-gallery/process" attributes={attributes} />
      </div>
    </>
  );
}
