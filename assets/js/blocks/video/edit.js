import { __ } from '@wordpress/i18n';
import { useBlockProps, InspectorControls } from '@wordpress/block-editor';
import { PanelBody, TextControl, ToggleControl } from '@wordpress/components';
import ServerSideRender from '@wordpress/server-side-render';
import { MediaField } from '../_shared/controls';

export default function Edit({ attributes, setAttributes }) {
  const set = (key) => (value) => setAttributes({ [key]: value });

  return (
    <>
      <InspectorControls>
        <PanelBody title={__('Video', 'awards-gallery-theme')} initialOpen>
          <MediaField
            label={__('Upload video (MP4)', 'awards-gallery-theme')}
            allowedTypes={['video']}
            value={attributes.video}
            onChange={set('video')}
          />
          <TextControl
            label={__('…or video URL', 'awards-gallery-theme')}
            help={__('YouTube, Vimeo or a direct .mp4 link. Used when no video is uploaded.', 'awards-gallery-theme')}
            value={attributes.videoUrl}
            onChange={set('videoUrl')}
          />
          <MediaField
            label={__('Poster image (shown before the video plays)', 'awards-gallery-theme')}
            value={attributes.poster}
            onChange={set('poster')}
          />
        </PanelBody>
        <PanelBody title={__('Playback', 'awards-gallery-theme')} initialOpen={false}>
          <ToggleControl
            label={__('Autoplay (muted, looping)', 'awards-gallery-theme')}
            checked={attributes.autoplay}
            onChange={set('autoplay')}
            __nextHasNoMarginBottom
          />
          <ToggleControl
            label={__('Show player controls', 'awards-gallery-theme')}
            checked={attributes.controls}
            onChange={set('controls')}
            __nextHasNoMarginBottom
          />
        </PanelBody>
        <PanelBody title={__('Overlay', 'awards-gallery-theme')} initialOpen={false}>
          <ToggleControl
            label={__('Show logo over the video', 'awards-gallery-theme')}
            help={__('Only needed when the logo is not already part of the video.', 'awards-gallery-theme')}
            checked={attributes.showMark}
            onChange={set('showMark')}
            __nextHasNoMarginBottom
          />
          {attributes.showMark ? (
            <MediaField label={__('Logo image', 'awards-gallery-theme')} value={attributes.mark} onChange={set('mark')} />
          ) : null}
          <TextControl label={__('Section ID (anchor)', 'awards-gallery-theme')} value={attributes.sectionId} onChange={set('sectionId')} />
        </PanelBody>
      </InspectorControls>

      <div {...useBlockProps()}>
        <ServerSideRender block="awards-gallery/video" attributes={attributes} />
      </div>
    </>
  );
}
