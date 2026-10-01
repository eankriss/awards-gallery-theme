import { __ } from '@wordpress/i18n';
import { useBlockProps, InspectorControls, InnerBlocks } from '@wordpress/block-editor';
import { PanelBody, TextControl, TextareaControl } from '@wordpress/components';
import ServerSideRender from '@wordpress/server-side-render';
import { Repeater } from '../_shared/controls';
import { CONTACT_ICONS } from '../_shared/contact-icons';

export default function Edit({ attributes, setAttributes }) {
  const set = (key) => (value) => setAttributes({ [key]: value });

  return (
    <>
      <InspectorControls>
        <PanelBody title={__('Contact cards', 'awards-gallery-theme')} initialOpen>
          <Repeater
            items={attributes.cards}
            onChange={set('cards')}
            addLabel={__('Add card', 'awards-gallery-theme')}
            newItem={{ icon: 'pin', title: '', text: '', url: '' }}
            fields={[
              { name: 'icon', label: __('Icon', 'awards-gallery-theme'), type: 'select', options: CONTACT_ICONS },
              { name: 'title', label: __('Title', 'awards-gallery-theme'), type: 'text' },
              { name: 'text', label: __('Text (one item per line)', 'awards-gallery-theme'), type: 'textarea' },
              { name: 'url', label: __('Link URL (optional)', 'awards-gallery-theme'), type: 'text' },
            ]}
          />
        </PanelBody>
        <PanelBody title={__('Business hours', 'awards-gallery-theme')} initialOpen={false}>
          <TextControl label={__('Title', 'awards-gallery-theme')} value={attributes.hoursTitle} onChange={set('hoursTitle')} />
          <Repeater
            items={attributes.hours}
            onChange={set('hours')}
            addLabel={__('Add row', 'awards-gallery-theme')}
            newItem={{ day: '', time: '' }}
            fields={[
              { name: 'day', label: __('Day(s)', 'awards-gallery-theme'), type: 'text' },
              { name: 'time', label: __('Hours (write "Closed" for closed days)', 'awards-gallery-theme'), type: 'text' },
            ]}
          />
        </PanelBody>
        <PanelBody title={__('Quick tip', 'awards-gallery-theme')} initialOpen={false}>
          <TextControl label={__('Title', 'awards-gallery-theme')} value={attributes.tipTitle} onChange={set('tipTitle')} />
          <TextareaControl label={__('Text', 'awards-gallery-theme')} value={attributes.tipText} onChange={set('tipText')} />
        </PanelBody>
        <PanelBody title={__('Form area', 'awards-gallery-theme')} initialOpen={false}>
          <TextControl label={__('Title', 'awards-gallery-theme')} value={attributes.formTitle} onChange={set('formTitle')} />
          <TextControl label={__('Gold label', 'awards-gallery-theme')} value={attributes.formLabel} onChange={set('formLabel')} />
          <TextControl label={__('Note under the form', 'awards-gallery-theme')} value={attributes.formNote} onChange={set('formNote')} />
        </PanelBody>
      </InspectorControls>

      <div {...useBlockProps()}>
        <ServerSideRender block="awards-gallery/contact-info" attributes={attributes} />
        <div style={{ border: '1px dashed #DA9328', padding: 16, marginTop: 8, background: '#0A0908', color: '#F5F0E8' }}>
          <p style={{ margin: '0 0 8px', fontSize: 12, textTransform: 'uppercase', letterSpacing: '0.2em', color: '#DA9328' }}>
            {__('Form area — add a Shortcode block for your form here', 'awards-gallery-theme')}
          </p>
          <InnerBlocks allowedBlocks={['core/shortcode', 'core/html', 'core/paragraph']} templateLock={false} />
        </div>
      </div>
    </>
  );
}
