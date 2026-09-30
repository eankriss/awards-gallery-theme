import { __ } from '@wordpress/i18n';
import { useBlockProps, InspectorControls } from '@wordpress/block-editor';
import { PanelBody } from '@wordpress/components';
import ServerSideRender from '@wordpress/server-side-render';
import { Repeater } from '../_shared/controls';

export default function Edit({ attributes, setAttributes }) {
  return (
    <>
      <InspectorControls>
        <PanelBody title={__('Stats', 'awards-gallery-theme')} initialOpen>
          <Repeater
            items={attributes.items}
            onChange={(v) => setAttributes({ items: v })}
            addLabel={__('Add stat', 'awards-gallery-theme')}
            newItem={{ value: '', suffix: '', label: '' }}
            fields={[
              { name: 'value', label: __('Number', 'awards-gallery-theme'), type: 'text' },
              { name: 'suffix', label: __('Suffix (e.g. + or %)', 'awards-gallery-theme'), type: 'text' },
              { name: 'label', label: __('Label', 'awards-gallery-theme'), type: 'text' },
            ]}
          />
        </PanelBody>
      </InspectorControls>

      <div {...useBlockProps()}>
        <ServerSideRender block="awards-gallery/stats" attributes={attributes} />
      </div>
    </>
  );
}
