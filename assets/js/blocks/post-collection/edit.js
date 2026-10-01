import { __ } from '@wordpress/i18n';
import { useBlockProps, InspectorControls } from '@wordpress/block-editor';
import { PanelBody, TextControl, SelectControl, RangeControl, ToggleControl } from '@wordpress/components';
import { useSelect } from '@wordpress/data';
import { store as coreStore } from '@wordpress/core-data';
import ServerSideRender from '@wordpress/server-side-render';

// Post types with no front-end listing of their own (pages, media, editor internals).
const HIDDEN_TYPES = ['page', 'attachment'];

export default function Edit({ attributes, setAttributes }) {
  const set = (key) => (value) => setAttributes({ [key]: value });

  const postTypes = useSelect((select) => select(coreStore).getPostTypes({ per_page: -1 }), []);
  const typeOptions = (postTypes || [])
    .filter((type) => type.viewable && !HIDDEN_TYPES.includes(type.slug))
    .map((type) => ({ label: type.labels?.name || type.name, value: type.slug }));

  return (
    <>
      <InspectorControls>
        <PanelBody title={__('Query', 'awards-gallery-theme')} initialOpen>
          <SelectControl
            label={__('Post type', 'awards-gallery-theme')}
            value={attributes.postType}
            options={typeOptions.length ? typeOptions : [{ label: __('Posts', 'awards-gallery-theme'), value: 'post' }]}
            onChange={set('postType')}
            __nextHasNoMarginBottom
          />
          <RangeControl
            label={__('Posts per page (grid)', 'awards-gallery-theme')}
            value={attributes.postsPerPage}
            onChange={set('postsPerPage')}
            min={1}
            max={24}
          />
          <ToggleControl
            label={__('Show pagination', 'awards-gallery-theme')}
            checked={attributes.showPagination}
            onChange={set('showPagination')}
            __nextHasNoMarginBottom
          />
        </PanelBody>
        <PanelBody title={__('Featured post', 'awards-gallery-theme')} initialOpen={false}>
          <ToggleControl
            label={__('Show featured post', 'awards-gallery-theme')}
            checked={attributes.showFeatured}
            onChange={set('showFeatured')}
            __nextHasNoMarginBottom
          />
          <TextControl
            label={__('Featured category slug', 'awards-gallery-theme')}
            help={__('The newest post in this category is shown large on top. Only applies to post types that use categories.', 'awards-gallery-theme')}
            value={attributes.featuredCategory}
            onChange={set('featuredCategory')}
          />
          <TextControl label={__('Featured badge', 'awards-gallery-theme')} value={attributes.featuredLabel} onChange={set('featuredLabel')} />
          <TextControl label={__('Featured link text', 'awards-gallery-theme')} value={attributes.featuredLinkText} onChange={set('featuredLinkText')} />
        </PanelBody>
        <PanelBody title={__('Text', 'awards-gallery-theme')} initialOpen={false}>
          <TextControl label={__('Card link text', 'awards-gallery-theme')} value={attributes.linkText} onChange={set('linkText')} />
          <TextControl label={__('No posts message', 'awards-gallery-theme')} value={attributes.emptyText} onChange={set('emptyText')} />
        </PanelBody>
      </InspectorControls>

      <div {...useBlockProps()}>
        <ServerSideRender block="awards-gallery/post-collection" attributes={attributes} />
      </div>
    </>
  );
}
