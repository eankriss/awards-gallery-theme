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
        </PanelBody>
        <PanelBody title={__('Cards', 'awards-gallery-theme')} initialOpen={false}>
          <Repeater
            items={attributes.items}
            onChange={set('items')}
            addLabel={__('Add card', 'awards-gallery-theme')}
            newItem={{ kicker: '', title: '', url: '', image: { id: 0, url: '' }, imageUrl: '' }}
            fields={[
              { name: 'image', label: __('Image', 'awards-gallery-theme'), type: 'media' },
              { name: 'kicker', label: __('Kicker', 'awards-gallery-theme'), type: 'text' },
              { name: 'title', label: __('Title', 'awards-gallery-theme'), type: 'text' },
              { name: 'url', label: __('Link URL', 'awards-gallery-theme'), type: 'text' },
            ]}
          />
        </PanelBody>
      </InspectorControls>

      <div {...useBlockProps()}>
        <ServerSideRender block="awards-gallery/craft" attributes={attributes} />
      </div>
    </>
  );
}
