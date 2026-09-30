import { __ } from '@wordpress/i18n';
import { useBlockProps, InspectorControls } from '@wordpress/block-editor';
import { PanelBody, TextControl, TextareaControl } from '@wordpress/components';
import ServerSideRender from '@wordpress/server-side-render';
import { MediaField } from '../_shared/controls';

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
          <TextControl label={__('Tagline', 'awards-gallery-theme')} value={attributes.tagline} onChange={set('tagline')} />
        </PanelBody>
        <PanelBody title={__('Buttons', 'awards-gallery-theme')} initialOpen={false}>
          <TextControl label={__('Primary text', 'awards-gallery-theme')} value={attributes.primaryText} onChange={set('primaryText')} />
          <TextControl label={__('Primary URL', 'awards-gallery-theme')} value={attributes.primaryUrl} onChange={set('primaryUrl')} />
          <TextControl label={__('Secondary text', 'awards-gallery-theme')} value={attributes.secondaryText} onChange={set('secondaryText')} />
          <TextControl
            label={__('Secondary URL', 'awards-gallery-theme')}
            help={__('Leave empty to email the address set in Customizer → Contact & Footer.', 'awards-gallery-theme')}
            value={attributes.secondaryUrl}
            onChange={set('secondaryUrl')}
          />
        </PanelBody>
        <PanelBody title={__('Background', 'awards-gallery-theme')} initialOpen={false}>
          <MediaField label={__('Background image', 'awards-gallery-theme')} value={attributes.image} onChange={set('image')} />
        </PanelBody>
      </InspectorControls>

      <div {...useBlockProps()}>
        <ServerSideRender block="awards-gallery/hero" attributes={attributes} />
      </div>
    </>
  );
}
