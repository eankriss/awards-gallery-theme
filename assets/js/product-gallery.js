/**
 * Single Product template: clicking a thumbnail shows that image in the main frame.
 * The thumbnails sit in a horizontal slider (three visible); the arrow buttons
 * page through it and hide at either end. Swipe / trackpad scrolling also works.
 * Loaded only on pages using page-templates/single-product.php.
 */
( function () {
	document.querySelectorAll( '[data-product-gallery]' ).forEach( function ( gallery ) {
		var slides = gallery.querySelectorAll( '[data-gallery-slide]' );
		var thumbs = gallery.querySelectorAll( '[data-gallery-thumb]' );
		var track = gallery.querySelector( '[data-gallery-track]' );
		var prev = gallery.querySelector( '[data-gallery-prev]' );
		var next = gallery.querySelector( '[data-gallery-next]' );

		// Width of one thumbnail plus the gap, i.e. one scroll step.
		var step = function () {
			var items = track.children;
			return items.length > 1 ? items[ 1 ].offsetLeft - items[ 0 ].offsetLeft : track.clientWidth;
		};

		var updateArrows = function () {
			if ( ! prev || ! next ) {
				return;
			}
			var max = track.scrollWidth - track.clientWidth;
			prev.disabled = track.scrollLeft <= 1;
			next.disabled = track.scrollLeft >= max - 1;
		};

		// Scroll just enough to bring the chosen thumbnail fully into view.
		var reveal = function ( thumb ) {
			var item = thumb.parentNode;
			var left = item.offsetLeft - track.offsetLeft;
			if ( left < track.scrollLeft ) {
				track.scrollLeft = left;
			} else if ( left + item.offsetWidth > track.scrollLeft + track.clientWidth ) {
				track.scrollLeft = left + item.offsetWidth - track.clientWidth;
			}
		};

		thumbs.forEach( function ( thumb, index ) {
			thumb.addEventListener( 'click', function () {
				slides.forEach( function ( slide, i ) {
					slide.classList.toggle( 'hidden', i !== index );
				} );
				thumbs.forEach( function ( other, i ) {
					other.setAttribute( 'aria-pressed', String( i === index ) );
				} );
				reveal( thumb );
			} );
		} );

		if ( track && prev && next ) {
			prev.addEventListener( 'click', function () { track.scrollBy( { left: -step() } ); } );
			next.addEventListener( 'click', function () { track.scrollBy( { left: step() } ); } );
			track.addEventListener( 'scroll', updateArrows, { passive: true } );
			window.addEventListener( 'resize', updateArrows );
			updateArrows();
		}
	} );
} )();
