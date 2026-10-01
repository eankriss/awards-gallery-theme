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
          <TextControl label={__('Heading (second part)', 'awards-gallery-theme')} value={attributes.headingScript} onChange={set('headingScript')} />
          <TextareaControl label={__('Text', 'awards-gallery-theme')} value={attributes.text} onChange={set('text')} />
        </PanelBody>
        <PanelBody title={__('Button', 'awards-gallery-theme')} initialOpen={false}>
          <TextControl label={__('Button text', 'awards-gallery-theme')} value={attributes.buttonText} onChange={set('buttonText')} />
          <TextControl
            label={__('Button URL', 'awards-gallery-theme')}
            help={__('Leave empty to email the address set in Customizer → Contact & Footer.', 'awards-gallery-theme')}
            value={attributes.buttonUrl}
            onChange={set('buttonUrl')}
          />
        </PanelBody>
        <PanelBody title={__('Second button', 'awards-gallery-theme')} initialOpen={false}>
          <TextControl label={__('Button text', 'awards-gallery-theme')} value={attributes.secondaryButtonText} onChange={set('secondaryButtonText')} />
          <TextControl
            label={__('Button URL', 'awards-gallery-theme')}
            help={__('Leave text or URL empty to hide this button.', 'awards-gallery-theme')}
            value={attributes.secondaryButtonUrl}
            onChange={set('secondaryButtonUrl')}
          />
        </PanelBody>
      </InspectorControls>

      <div {...useBlockProps()}>
        <ServerSideRender block="awards-gallery/cta" attributes={attributes} />
      </div>
    </>
  );
}
