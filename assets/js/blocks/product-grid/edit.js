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
        <PanelBody title={__('Settings', 'awards-gallery-theme')} initialOpen>
          <TextControl
            label={__('Link text', 'awards-gallery-theme')}
            help={__('Shown on cards that have a link URL.', 'awards-gallery-theme')}
            value={attributes.linkText}
            onChange={set('linkText')}
          />
        </PanelBody>
        <PanelBody title={__('Products', 'awards-gallery-theme')} initialOpen={false}>
          <Repeater
            items={attributes.items}
            onChange={set('items')}
            addLabel={__('Add product', 'awards-gallery-theme')}
            newItem={{ kicker: '', title: '', text: '', url: '', image: { id: 0, url: '' } }}
            fields={[
              { name: 'image', label: __('Image', 'awards-gallery-theme'), type: 'media' },
              { name: 'kicker', label: __('Kicker', 'awards-gallery-theme'), type: 'text' },
              { name: 'title', label: __('Title', 'awards-gallery-theme'), type: 'text' },
              { name: 'text', label: __('Text', 'awards-gallery-theme'), type: 'textarea' },
              { name: 'url', label: __('Link URL', 'awards-gallery-theme'), type: 'text' },
            ]}
          />
        </PanelBody>
      </InspectorControls>

      <div {...useBlockProps()}>
        <ServerSideRender block="awards-gallery/product-grid" attributes={attributes} />
      </div>
    </>
  );
}
