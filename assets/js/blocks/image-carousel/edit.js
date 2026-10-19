import { __ } from '@wordpress/i18n';
import { useBlockProps, InspectorControls } from '@wordpress/block-editor';
import { PanelBody, TextControl, TextareaControl, RangeControl, SelectControl, ToggleControl } from '@wordpress/components';
import ServerSideRender from '@wordpress/server-side-render';
import { Repeater } from '../_shared/controls';

export default function Edit({ attributes, setAttributes }) {
  const set = (key) => (value) => setAttributes({ [key]: value });
  return (
    <>
      <InspectorControls>
        <PanelBody title={__('Content', 'awards-gallery-theme')} initialOpen>
          <TextControl label={__('Eyebrow', 'awards-gallery-theme')} value={attributes.eyebrow} onChange={set('eyebrow')} />
          <TextControl label={__('Title', 'awards-gallery-theme')} value={attributes.title} onChange={set('title')} />
          <TextControl label={__('Title (gold part)', 'awards-gallery-theme')} value={attributes.titleAccent} onChange={set('titleAccent')} />
          <TextareaControl label={__('Subheading', 'awards-gallery-theme')} value={attributes.subheading} onChange={set('subheading')} />
          <TextareaControl label={__('Note', 'awards-gallery-theme')} help={__('Displayed below the image carousel.', 'awards-gallery-theme')} value={attributes.note} onChange={set('note')} />
        </PanelBody>
        <PanelBody title={__('Carousel settings', 'awards-gallery-theme')} initialOpen>
          <SelectControl label={__('Image width', 'awards-gallery-theme')} value={attributes.widthMode} options={[{ label: __('Max width (site container)', 'awards-gallery-theme'), value: 'max' }, { label: __('Full width', 'awards-gallery-theme'), value: 'full' }]} onChange={set('widthMode')} />
          <ToggleControl label={__('Continuous autoplay', 'awards-gallery-theme')} help={__('Turn off to show the images without automatic movement.', 'awards-gallery-theme')} checked={attributes.autoplay} onChange={set('autoplay')} />
          <RangeControl label={__('Speed (seconds per loop)', 'awards-gallery-theme')} value={attributes.speed} onChange={set('speed')} min={8} max={120} />
          <SelectControl label={__('Direction', 'awards-gallery-theme')} value={attributes.direction} options={[{ label: __('Left', 'awards-gallery-theme'), value: 'left' }, { label: __('Right', 'awards-gallery-theme'), value: 'right' }]} onChange={set('direction')} />
          <ToggleControl label={__('Pause on hover', 'awards-gallery-theme')} checked={attributes.pauseOnHover} onChange={set('pauseOnHover')} />
          <RangeControl label={__('Card width (px)', 'awards-gallery-theme')} value={attributes.cardWidth} onChange={set('cardWidth')} min={100} max={420} />
          <RangeControl label={__('Card height (px)', 'awards-gallery-theme')} value={attributes.cardHeight} onChange={set('cardHeight')} min={120} max={520} />
          <RangeControl label={__('Gap (px)', 'awards-gallery-theme')} value={attributes.gap} onChange={set('gap')} min={8} max={48} />
        </PanelBody>
        <PanelBody title={__('Images', 'awards-gallery-theme')} initialOpen={false}>
          <Repeater items={attributes.items} onChange={set('items')} addLabel={__('Add image', 'awards-gallery-theme')} newItem={{ image: { id: 0, url: '' } }} fields={[{ name: 'image', label: __('Image', 'awards-gallery-theme'), type: 'media' }]} />
        </PanelBody>
      </InspectorControls>
      <div {...useBlockProps()}><ServerSideRender block="awards-gallery/image-carousel" attributes={attributes} /></div>
    </>
  );
}
