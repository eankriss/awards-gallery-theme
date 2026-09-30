import { __ } from '@wordpress/i18n';
import { useBlockProps, InspectorControls } from '@wordpress/block-editor';
import { PanelBody } from '@wordpress/components';
import ServerSideRender from '@wordpress/server-side-render';
import { Repeater } from '../_shared/controls';

export default function Edit({ attributes, setAttributes }) {
  return (
    <>
      <InspectorControls>
        <PanelBody title={__('Highlights', 'awards-gallery-theme')} initialOpen>
          <Repeater
            items={attributes.items}
            onChange={(v) => setAttributes({ items: v })}
            addLabel={__('Add highlight', 'awards-gallery-theme')}
            newItem={{ text: '' }}
            fields={[{ name: 'text', label: __('Text', 'awards-gallery-theme'), type: 'text' }]}
          />
        </PanelBody>
      </InspectorControls>

      <div {...useBlockProps()}>
        <ServerSideRender block="awards-gallery/marquee" attributes={attributes} />
      </div>
    </>
  );
}
