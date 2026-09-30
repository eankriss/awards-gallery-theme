/**
 * Awards Gallery Theme — front-end interactions.
 */
( function () {
	'use strict';

	document.documentElement.classList.remove( 'no-js' );

	// Sticky header: compact + translucent once the page is scrolled.
	var header = document.querySelector( '[data-header]' );
	if ( header ) {
		var onScroll = function () {
			header.classList.toggle( 'is-scrolled', window.scrollY > 10 );
		};
		window.addEventListener( 'scroll', onScroll, { passive: true } );
		onScroll();
	}

	// Mobile drawer
	var drawer = document.querySelector( '[data-drawer]' );
	var openBtn = document.querySelector( '[data-menu-open]' );
	if ( drawer && openBtn ) {
		var panel = drawer.querySelector( '[data-drawer-panel]' );
		var scrim = drawer.querySelector( '[data-drawer-scrim]' );
		var setOpen = function ( open ) {
			drawer.classList.toggle( 'pointer-events-none', ! open );
			panel.classList.toggle( 'translate-x-full', ! open );
			scrim.classList.toggle( 'opacity-0', ! open );
			drawer.setAttribute( 'aria-hidden', String( ! open ) );
			openBtn.setAttribute( 'aria-expanded', String( open ) );
			document.body.style.overflow = open ? 'hidden' : '';
		};
		openBtn.addEventListener( 'click', function () { setOpen( true ); } );
		drawer.querySelector( '[data-menu-close]' ).addEventListener( 'click', function () { setOpen( false ); } );
		scrim.addEventListener( 'click', function () { setOpen( false ); } );
		// Sub-menu accordions inside the drawer.
		drawer.querySelectorAll( '[data-submenu-toggle]' ).forEach( function ( btn ) {
			btn.addEventListener( 'click', function () {
				var list = document.getElementById( btn.getAttribute( 'aria-controls' ) );
				var open = btn.getAttribute( 'aria-expanded' ) !== 'true';
				btn.setAttribute( 'aria-expanded', String( open ) );
				btn.querySelector( 'svg' ).classList.toggle( 'rotate-180', open );
				if ( list ) {
					list.classList.toggle( 'hidden', ! open );
				}
			} );
		} );
		drawer.querySelectorAll( 'a' ).forEach( function ( a ) {
			a.addEventListener( 'click', function () { setOpen( false ); } );
		} );
		document.addEventListener( 'keydown', function ( e ) {
			if ( e.key === 'Escape' ) {
				setOpen( false );
			}
		} );
		// Close the drawer (and release the scroll lock) when resizing up to desktop.
		var desktop = window.matchMedia( '(min-width: 1024px)' );
		var onDesktop = function ( e ) {
			if ( e.matches ) {
				setOpen( false );
			}
		};
		if ( desktop.addEventListener ) {
			desktop.addEventListener( 'change', onDesktop );
		} else {
			desktop.addListener( onDesktop );
		}
	}

	// Reveal on scroll + stat count-up
	var reduce = window.matchMedia( '(prefers-reduced-motion: reduce)' ).matches;

	function countUp( el ) {
		var end = +el.dataset.count;
		var suffix = el.dataset.suffix || '';
		var start = null;
		var duration = 1400;
		function step( t ) {
			if ( ! start ) {
				start = t;
			}
			var p = Math.min( 1, ( t - start ) / duration );
			var eased = 1 - Math.pow( 1 - p, 3 );
			el.textContent = Math.round( end * eased ).toLocaleString( 'en-US' ) + suffix;
			if ( p < 1 ) {
				requestAnimationFrame( step );
			}
		}
		requestAnimationFrame( step );
	}

	// Respect reduced motion: stop autoplaying videos and give the visitor controls.
	if ( reduce ) {
		document.querySelectorAll( 'video[data-autoplay]' ).forEach( function ( video ) {
			video.pause();
			video.removeAttribute( 'autoplay' );
			video.controls = true;
		} );
	}

	var items = document.querySelectorAll( '.reveal' );
	if ( reduce || ! ( 'IntersectionObserver' in window ) ) {
		items.forEach( function ( el ) { el.classList.add( 'is-in' ); } );
		return;
	}

	var io = new IntersectionObserver( function ( entries ) {
		entries.forEach( function ( entry ) {
			if ( ! entry.isIntersecting ) {
				return;
			}
			entry.target.classList.add( 'is-in' );
			var counter = entry.target.querySelector( '[data-count]' );
			if ( counter ) {
				countUp( counter );
			}
			io.unobserve( entry.target );
		} );
	}, { threshold: 0.15, rootMargin: '0px 0px -40px 0px' } );

	items.forEach( function ( el ) { io.observe( el ); } );
} )();
