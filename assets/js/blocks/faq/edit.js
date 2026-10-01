import { __ } from '@wordpress/i18n';
import { useBlockProps, InspectorControls } from '@wordpress/block-editor';
import { PanelBody, TextControl, TextareaControl, ToggleControl } from '@wordpress/components';
import ServerSideRender from '@wordpress/server-side-render';
import { Repeater } from '../_shared/controls';

export default function Edit({ attributes, setAttributes }) {
  const set = (key) => (value) => setAttributes({ [key]: value });

  return (
    <>
      <InspectorControls>
        <PanelBody title={__('Intro', 'awards-gallery-theme')} initialOpen>
          <TextControl label={__('Heading', 'awards-gallery-theme')} value={attributes.heading} onChange={set('heading')} />
          <TextareaControl label={__('Text', 'awards-gallery-theme')} value={attributes.text} onChange={set('text')} />
          <ToggleControl
            label={__('Only one answer open at a time', 'awards-gallery-theme')}
            checked={attributes.singleOpen}
            onChange={set('singleOpen')}
            __nextHasNoMarginBottom
          />
        </PanelBody>
        <PanelBody title={__('Questions', 'awards-gallery-theme')} initialOpen={false}>
          <Repeater
            items={attributes.items}
            onChange={set('items')}
            addLabel={__('Add question', 'awards-gallery-theme')}
            newItem={{ question: '', answer: '' }}
            fields={[
              { name: 'question', label: __('Question', 'awards-gallery-theme'), type: 'text' },
              { name: 'answer', label: __('Answer (blank line = new paragraph)', 'awards-gallery-theme'), type: 'textarea' },
            ]}
          />
        </PanelBody>
      </InspectorControls>

      <div {...useBlockProps()}>
        <ServerSideRender block="awards-gallery/faq" attributes={attributes} />
      </div>
    </>
  );
}
