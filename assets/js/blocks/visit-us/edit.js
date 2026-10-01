import { __ } from '@wordpress/i18n';
import { useBlockProps, InspectorControls } from '@wordpress/block-editor';
import { PanelBody, TextControl, TextareaControl } from '@wordpress/components';
import ServerSideRender from '@wordpress/server-side-render';
import { Repeater } from '../_shared/controls';
import { CONTACT_ICONS } from '../_shared/contact-icons';

export default function Edit({ attributes, setAttributes }) {
  const set = (key) => (value) => setAttributes({ [key]: value });

  return (
    <>
      <InspectorControls>
        <PanelBody title={__('Map', 'awards-gallery-theme')} initialOpen>
          <TextareaControl
            label={__('Map embed', 'awards-gallery-theme')}
            help={__('In Google Maps: Share → Embed a map → Copy HTML, then paste the whole <iframe> code (or just its src link) here.', 'awards-gallery-theme')}
            value={attributes.mapEmbed}
            onChange={set('mapEmbed')}
            rows={4}
          />
          <TextControl
            label={__('Map title (for screen readers)', 'awards-gallery-theme')}
            value={attributes.mapTitle}
            onChange={set('mapTitle')}
          />
        </PanelBody>
        <PanelBody title={__('Content', 'awards-gallery-theme')} initialOpen={false}>
          <TextControl label={__('Eyebrow', 'awards-gallery-theme')} value={attributes.eyebrow} onChange={set('eyebrow')} />
          <TextControl label={__('Heading', 'awards-gallery-theme')} value={attributes.heading} onChange={set('heading')} />
          <TextControl label={__('Heading (gold part)', 'awards-gallery-theme')} value={attributes.headingScript} onChange={set('headingScript')} />
          <TextareaControl label={__('Text', 'awards-gallery-theme')} value={attributes.text} onChange={set('text')} />
        </PanelBody>
        <PanelBody title={__('Details', 'awards-gallery-theme')} initialOpen={false}>
          <Repeater
            items={attributes.details}
            onChange={set('details')}
            addLabel={__('Add detail', 'awards-gallery-theme')}
            newItem={{ icon: 'pin', text: '' }}
            fields={[
              { name: 'icon', label: __('Icon', 'awards-gallery-theme'), type: 'select', options: CONTACT_ICONS },
              { name: 'text', label: __('Text', 'awards-gallery-theme'), type: 'text' },
            ]}
          />
        </PanelBody>
      </InspectorControls>

      <div {...useBlockProps()}>
        <ServerSideRender block="awards-gallery/visit-us" attributes={attributes} />
      </div>
    </>
  );
}
