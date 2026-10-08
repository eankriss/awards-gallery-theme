/**
 * "Hero Banner" panel in the post sidebar: a focal point picker on the featured
 * image. The point is saved to the awards_post_hero_focal meta and becomes the
 * hero image's object-position, so that spot stays in view when the banner crops.
 * Plain wp.* globals (no build step); enqueued for posts only in functions.php.
 */
( function ( wp ) {
	'use strict';

	var el = wp.element.createElement;
	var __ = wp.i18n.__;
	var useSelect = wp.data.useSelect;
	var useDispatch = wp.data.useDispatch;
	var FocalPointPicker = wp.components.FocalPointPicker;
	var Button = wp.components.Button;
	// WordPress 6.6 moved the panel from wp.editPost to wp.editor.
	var Panel = ( wp.editor && wp.editor.PluginDocumentSettingPanel ) || wp.editPost.PluginDocumentSettingPanel;

	var KEY = 'awards_post_hero_focal';
	var CENTER = { x: 0.5, y: 0.5 };

	function HeroFocalPanel() {
		var data = useSelect( function ( select ) {
			var editor = select( 'core/editor' );
			var mediaId = editor.getEditedPostAttribute( 'featured_media' );
			var media = mediaId ? select( 'core' ).getMedia( mediaId, { context: 'view' } ) : null;
			return {
				meta: editor.getEditedPostAttribute( 'meta' ) || {},
				hasImage: !! mediaId,
				url: media ? media.source_url : '',
			};
		}, [] );
		var editPost = useDispatch( 'core/editor' ).editPost;

		// An unset meta comes back from the REST API as [] (or null).
		var saved = data.meta[ KEY ];
		var hasSaved = !! saved && typeof saved.x === 'number' && typeof saved.y === 'number';
		var value = hasSaved ? saved : CENTER;
		var setFocal = function ( point ) {
			var meta = {};
			meta[ KEY ] = point ? { x: Number( point.x ), y: Number( point.y ) } : null;
			editPost( { meta: meta } );
		};

		return el(
			Panel,
			{ name: 'awards-gallery-hero-focal', title: __( 'Hero Banner', 'awards-gallery-theme' ) },
			! data.hasImage
				? el( 'p', null, __( 'Set a featured image to choose which part of it the hero banner keeps in view.', 'awards-gallery-theme' ) )
				: ! data.url
					? el( 'p', null, __( 'Loading image…', 'awards-gallery-theme' ) )
					: [
						el( FocalPointPicker, {
							key: 'picker',
							label: __( 'Focal point', 'awards-gallery-theme' ),
							help: __( 'Drag the dot onto the part that must stay visible (a face, the award). The banner is wide, so the rest of the image may be cropped.', 'awards-gallery-theme' ),
							url: data.url,
							value: value,
							onChange: setFocal,
							__nextHasNoMarginBottom: true,
						} ),
						hasSaved
							? el( Button, { key: 'reset', variant: 'secondary', size: 'small', style: { marginTop: 12 }, onClick: function () { setFocal( null ); } }, __( 'Reset to centre', 'awards-gallery-theme' ) )
							: null,
					]
		);
	}

	wp.plugins.registerPlugin( 'awards-gallery-hero-focal', { render: HeroFocalPanel } );
} )( window.wp );
