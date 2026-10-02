import { __ } from '@wordpress/i18n';
import { useBlockProps, InspectorControls } from '@wordpress/block-editor';
import { PanelBody, TextControl, TextareaControl } from '@wordpress/components';
import ServerSideRender from '@wordpress/server-side-render';

export default function Edit({ attributes, setAttributes }) {
  const set = (key) => (value) => setAttributes({ [key]: value });

  return (
    <>
      <InspectorControls>
        <PanelBody title={__('Content', 'awards-gallery-theme')} initialOpen>
          <TextControl label={__('Eyebrow', 'awards-gallery-theme')} value={attributes.eyebrow} onChange={set('eyebrow')} />
          <TextControl label={__('Heading', 'awards-gallery-theme')} value={attributes.heading} onChange={set('heading')} />
          <TextControl label={__('Heading (gold part)', 'awards-gallery-theme')} value={attributes.headingGold} onChange={set('headingGold')} />
          <TextareaControl label={__('Text', 'awards-gallery-theme')} value={attributes.text} onChange={set('text')} />
        </PanelBody>
        <PanelBody title={__('Form', 'awards-gallery-theme')} initialOpen>
          <TextControl
            label={__('Form shortcode', 'awards-gallery-theme')}
            help={__('Paste the "Subscribe Form" shortcode from Contact → Contact Forms.', 'awards-gallery-theme')}
            value={attributes.shortcode}
            onChange={set('shortcode')}
          />
        </PanelBody>
      </InspectorControls>

      <div {...useBlockProps()}>
        {/* Preview only: the form can't be submitted inside the editor. */}
        <div style={{ pointerEvents: 'none' }}>
          <ServerSideRender block="awards-gallery/subscribe" attributes={attributes} />
        </div>
      </div>
    </>
  );
}
