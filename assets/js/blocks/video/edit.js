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
            label={__('Click to play (poster + play button)', 'awards-gallery-theme')}
            help={__('Shows the poster image with a play button. The video only loads when clicked, then plays with sound. Without a poster, YouTube links use their own thumbnail.', 'awards-gallery-theme')}
            checked={attributes.playOverlay}
            onChange={set('playOverlay')}
            __nextHasNoMarginBottom
          />
          {!attributes.playOverlay ? (
            <ToggleControl
              label={__('Autoplay (muted, looping)', 'awards-gallery-theme')}
              checked={attributes.autoplay}
              onChange={set('autoplay')}
              __nextHasNoMarginBottom
            />
          ) : null}
          {!attributes.playOverlay && attributes.autoplay ? (
            <ToggleControl
              label={__('Show sound on/off button', 'awards-gallery-theme')}
              help={__('Browsers only allow muted autoplay. This button lets visitors turn the sound on.', 'awards-gallery-theme')}
              checked={attributes.soundToggle}
              onChange={set('soundToggle')}
              __nextHasNoMarginBottom
            />
          ) : null}
          {!attributes.playOverlay && attributes.autoplay && attributes.soundToggle ? (
            <ToggleControl
              label={__('Try to start with sound (MP4 only)', 'awards-gallery-theme')}
              help={__('Plays with sound when the browser allows it (mostly returning Chrome visitors). Everyone else gets muted autoplay with the sound button. Has no effect on YouTube / Vimeo links.', 'awards-gallery-theme')}
              checked={attributes.trySound}
              onChange={set('trySound')}
              __nextHasNoMarginBottom
            />
          ) : null}
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
        </PanelBody>
      </InspectorControls>

      <div {...useBlockProps()}>
        <ServerSideRender block="awards-gallery/video" attributes={attributes} />
      </div>
    </>
  );
}
