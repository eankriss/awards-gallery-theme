/**
 * Awards Gallery Hero — front end. Loaded only on pages that use the block.
 */
( function () {
	var reduce = window.matchMedia( '(prefers-reduced-motion: reduce)' ).matches;

	// Background video: with reduced motion, hold on the first frame / poster.
	if ( reduce ) {
		document.querySelectorAll( '.wp-block-awards-gallery-hero video[data-background]' ).forEach( function ( video ) {
			video.pause();
			video.removeAttribute( 'autoplay' );
		} );
	}

	// Slideshow background: move to the next image every few seconds, using the effect set in the editor (style.css).
	// Reduced motion keeps the first image; the timer pauses while the tab is hidden.
	document.querySelectorAll( '.wp-block-awards-gallery-hero [data-slideshow]' ).forEach( function ( root ) {
		var slides = root.querySelectorAll( '[data-slide]' );
		if ( reduce || slides.length < 2 ) {
			return;
		}
		var delay = Math.max( 3, parseFloat( root.getAttribute( 'data-slideshow' ) ) || 6 ) * 1000;
		var speed = ( parseFloat( root.getAttribute( 'data-speed' ) ) || 0 ) * 1000;
		var zoom = root.hasAttribute( 'data-zoom' );
		var index = 0;
		var timer;

		var show = function ( slide ) {
			slide.classList.add( 'is-active' );
			if ( zoom ) {
				// Next frame, so the zoom transition starts from scale(1).
				requestAnimationFrame( function () {
					requestAnimationFrame( function () { slide.classList.add( 'is-zoom' ); } );
				} );
			}
		};
		var next = function () {
			var prev = slides[ index ];
			prev.classList.remove( 'is-active' );
			prev.classList.add( 'is-leaving' );
			setTimeout( function () { prev.classList.remove( 'is-leaving', 'is-zoom' ); }, speed + 50 );
			index = ( index + 1 ) % slides.length;
			show( slides[ index ] );
		};
		var start = function () {
			clearInterval( timer );
			timer = setInterval( next, delay );
		};

		// Lazy images parked off-screen (slide effects) would never load, so fetch the rest once the page has loaded.
		var preload = function () {
			root.querySelectorAll( 'img[loading="lazy"]' ).forEach( function ( img ) { img.loading = 'eager'; } );
		};
		if ( document.readyState === 'complete' ) {
			preload();
		} else {
			window.addEventListener( 'load', preload );
		}

		show( slides[ 0 ] );
		start();
		document.addEventListener( 'visibilitychange', function () {
			if ( document.hidden ) {
				clearInterval( timer );
			} else {
				start();
			}
		} );
	} );
} )();
