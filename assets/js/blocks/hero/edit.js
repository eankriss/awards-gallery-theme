import { __ } from '@wordpress/i18n';
import { useBlockProps, InspectorControls, MediaUpload, MediaUploadCheck } from '@wordpress/block-editor';
import { BaseControl, Button, PanelBody, RangeControl, SelectControl, TextControl, TextareaControl, ToggleControl } from '@wordpress/components';
import ServerSideRender from '@wordpress/server-side-render';
import { MediaField } from '../_shared/controls';

/**
 * Multi-image picker for the slideshow; stores [{ id, url }, …].
 * Opens the media library's gallery view, so images can be added, removed and reordered.
 */
function GalleryField({ label, help, value = [], onChange }) {
  return (
    <BaseControl label={label} help={help} __nextHasNoMarginBottom>
      <MediaUploadCheck>
        <MediaUpload
          multiple
          gallery
          addToGallery={value.length > 0}
          allowedTypes={['image']}
          value={value.map((img) => img.id)}
          onSelect={(media) => onChange(media.map((m) => ({ id: m.id, url: m.url })))}
          render={({ open }) => (
            <div style={{ display: 'flex', gap: 8, flexWrap: 'wrap', alignItems: 'center' }}>
              {value.map((img, i) => (
                <img
                  key={img.id || i}
                  src={img.url}
                  alt=""
                  style={{ width: 56, height: 56, objectFit: 'cover', borderRadius: 6 }}
                />
              ))}
              <div style={{ display: 'flex', gap: 8, flexBasis: '100%' }}>
                <Button variant="secondary" onClick={open}>
                  {value.length ? __('Edit images', 'awards-gallery-theme') : __('Select images', 'awards-gallery-theme')}
                </Button>
                {value.length ? (
                  <Button variant="tertiary" isDestructive onClick={() => onChange([])}>
                    {__('Clear', 'awards-gallery-theme')}
                  </Button>
                ) : null}
              </div>
            </div>
          )}
        />
      </MediaUploadCheck>
    </BaseControl>
  );
}

export default function Edit({ attributes, setAttributes }) {
  const set = (key) => (value) => setAttributes({ [key]: value });
  const bgType = attributes.bgType || 'image';

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
          <SelectControl
            label={__('Background type', 'awards-gallery-theme')}
            value={bgType}
            options={[
              { label: __('Single image', 'awards-gallery-theme'), value: 'image' },
              { label: __('Video', 'awards-gallery-theme'), value: 'video' },
              { label: __('Image slideshow', 'awards-gallery-theme'), value: 'slideshow' },
            ]}
            onChange={set('bgType')}
            __nextHasNoMarginBottom
          />

          {bgType === 'image' ? (
            <MediaField label={__('Background image', 'awards-gallery-theme')} value={attributes.image} onChange={set('image')} />
          ) : null}

          {bgType === 'video' ? (
            <>
              <MediaField
                label={__('Upload video (MP4)', 'awards-gallery-theme')}
                allowedTypes={['video']}
                value={attributes.video}
                onChange={set('video')}
              />
              <TextControl
                label={__('…or video URL', 'awards-gallery-theme')}
                help={__('YouTube, Vimeo or a direct .mp4 link. Used when no video is uploaded. Plays muted and looping behind the content.', 'awards-gallery-theme')}
                value={attributes.videoUrl}
                onChange={set('videoUrl')}
              />
              <MediaField
                label={__('Poster image (shown while the video loads)', 'awards-gallery-theme')}
                value={attributes.poster}
                onChange={set('poster')}
              />
              <ToggleControl
                label={__('Show sound on/off button', 'awards-gallery-theme')}
                help={__('Browsers only allow muted autoplay. This button lets visitors turn the sound on.', 'awards-gallery-theme')}
                checked={attributes.soundToggle}
                onChange={set('soundToggle')}
                __nextHasNoMarginBottom
              />
            </>
          ) : null}

          {bgType === 'slideshow' ? (
            <>
              <GalleryField
                label={__('Slideshow images', 'awards-gallery-theme')}
                help={__('Drag to reorder in the media library.', 'awards-gallery-theme')}
                value={attributes.slides}
                onChange={set('slides')}
              />
              <RangeControl
                label={__('Seconds per image', 'awards-gallery-theme')}
                min={3}
                max={15}
                value={attributes.slideInterval}
                onChange={set('slideInterval')}
                __nextHasNoMarginBottom
              />
              <SelectControl
                label={__('Transition effect', 'awards-gallery-theme')}
                help={__('How the next image comes in. Plays on the live site; the editor preview shows the first image only.', 'awards-gallery-theme')}
                value={attributes.slideEffect}
                options={[
                  { label: __('Fade', 'awards-gallery-theme'), value: 'fade' },
                  { label: __('Slide from right', 'awards-gallery-theme'), value: 'slide' },
                  { label: __('Slide from bottom', 'awards-gallery-theme'), value: 'slide-up' },
                  { label: __('Zoom fade', 'awards-gallery-theme'), value: 'zoom' },
                  { label: __('Blur fade', 'awards-gallery-theme'), value: 'blur' },
                  { label: __('Cut (no animation)', 'awards-gallery-theme'), value: 'cut' },
                ]}
                onChange={set('slideEffect')}
                __nextHasNoMarginBottom
              />
              {attributes.slideEffect !== 'cut' ? (
                <RangeControl
                  label={__('Transition speed (seconds)', 'awards-gallery-theme')}
                  min={0.3}
                  max={2.5}
                  step={0.1}
                  value={attributes.slideSpeed}
                  onChange={set('slideSpeed')}
                  __nextHasNoMarginBottom
                />
              ) : null}
              <ToggleControl
                label={__('Slow zoom while each image shows', 'awards-gallery-theme')}
                checked={attributes.slideZoom}
                onChange={set('slideZoom')}
                __nextHasNoMarginBottom
              />
            </>
          ) : null}
        </PanelBody>
      </InspectorControls>

      <div {...useBlockProps()}>
        <ServerSideRender block="awards-gallery/hero" attributes={attributes} />
      </div>
    </>
  );
}
