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

	// Timeline: the gold line fills as the page scrolls; a milestone lights up once the
	// fill passes its dot. The fill tip tracks a line 60% down the viewport.
	// Reduced motion: the line is drawn full and every milestone stays lit.
	document.querySelectorAll( '[data-timeline]' ).forEach( function ( list ) {
		var rail = list.querySelector( '[data-timeline-rail]' );
		var rows = list.querySelectorAll( '[data-timeline-item]' );
		if ( ! rail || rows.length < 2 ) {
			return;
		}
		var dots = [];
		var railTop = 0;
		var railHeight = 0;
		var ticking = false;

		// Stretch the rail from the first dot's centre to the last's.
		var measure = function () {
			var box = list.getBoundingClientRect();
			dots = Array.prototype.map.call( rows, function ( row ) {
				var r = row.querySelector( '[data-timeline-dot]' ).getBoundingClientRect();
				return { x: r.left + r.width / 2 - box.left, y: r.top + r.height / 2 - box.top };
			} );
			railTop = dots[ 0 ].y;
			railHeight = dots[ dots.length - 1 ].y - railTop;
			rail.style.left = dots[ 0 ].x + 'px';
			rail.style.top = railTop + 'px';
			rail.style.height = railHeight + 'px';
			rail.classList.remove( 'hidden' );
		};

		var update = function () {
			ticking = false;
			var tip = reduce ? Infinity : window.innerHeight * 0.6 - list.getBoundingClientRect().top - railTop;
			var progress = Math.max( 0, Math.min( 1, tip / railHeight ) );
			list.style.setProperty( '--timeline-progress', progress );
			rows.forEach( function ( row, i ) {
				// Small tolerance so a dot lights as the tip reaches it, not just after.
				row.classList.toggle( 'is-off', dots[ i ].y - railTop > tip + 2 );
			} );
		};

		var onScroll = function () {
			if ( ! ticking ) {
				ticking = true;
				requestAnimationFrame( update );
			}
		};

		measure();
		update();
		window.addEventListener( 'scroll', onScroll, { passive: true } );
		window.addEventListener( 'resize', function () {
			measure();
			onScroll();
		} );
		// Web fonts can change line heights after load; re-measure once they settle.
		if ( document.fonts && document.fonts.ready ) {
			document.fonts.ready.then( function () {
				measure();
				onScroll();
			} );
		}
	} );

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

	// Click-to-play video: swap the poster + play button for the real player.
	// The click counts as a user gesture, so it may start with sound.
	document.querySelectorAll( '[data-video-play]' ).forEach( function ( btn ) {
		btn.addEventListener( 'click', function () {
			var section = btn.parentNode;
			var tpl = section.querySelector( 'template[data-video-player]' );
			if ( ! tpl ) {
				return;
			}
			section.insertBefore( tpl.content.cloneNode( true ), tpl );
			tpl.remove();
			btn.remove();

			var video = section.querySelector( 'video' );
			var player = section.querySelector( '[data-player]' );
			if ( video && player ) {
				initPlayer( section, video, player );
			}
			if ( video ) {
				video.play();
			}
			( player ? player.querySelector( '[data-player-toggle]' ) : video || section.querySelector( 'iframe' ) ).focus();
		} );
	} );

	// Themed controls for click-to-play MP4s.
	function initPlayer( section, video, player ) {
		var q = function ( sel ) { return player.querySelector( sel ); };
		var bar = q( '[data-player-bar]' );
		var toggle = q( '[data-player-toggle]' );
		var seek = q( '[data-player-seek]' );
		var vol = q( '[data-player-volume]' );
		var muteBtn = q( '[data-player-mute]' );
		var fsBtn = q( '[data-player-fs]' );
		var timeEl = q( '[data-player-time]' );
		var durEl = q( '[data-player-duration]' );
		var seeking = false;
		var idleTimer;

		var fmt = function ( s ) {
			s = Math.max( 0, Math.floor( s || 0 ) );
			var h = Math.floor( s / 3600 );
			var m = Math.floor( ( s % 3600 ) / 60 );
			var sec = ( '0' + ( s % 60 ) ).slice( -2 );
			return h ? h + ':' + ( '0' + m ).slice( -2 ) + ':' + sec : m + ':' + sec;
		};
		var fill = function ( input, ratio ) {
			input.style.setProperty( '--fill', ( ratio * 100 ) + '%' );
		};
		var togglePlay = function () {
			if ( video.paused || video.ended ) {
				video.play();
			} else {
				video.pause();
			}
		};

		var render = function () {
			if ( ! seeking && video.duration ) {
				var r = video.currentTime / video.duration;
				seek.value = Math.round( r * 1000 );
				fill( seek, r );
				timeEl.textContent = fmt( video.currentTime );
			}
		};
		// Progress follows the frame while playing; timeupdate alone looks choppy.
		var loop = function () {
			render();
			if ( ! video.paused ) {
				requestAnimationFrame( loop );
			}
		};

		// Hide the bar while playing until the visitor moves, taps or tabs.
		var wake = function () {
			player.classList.remove( 'is-idle' );
			clearTimeout( idleTimer );
			if ( ! video.paused ) {
				idleTimer = setTimeout( function () {
					if ( ! player.contains( document.activeElement ) || document.activeElement === toggle ) {
						player.classList.add( 'is-idle' );
					}
				}, 2500 );
			}
		};

		video.addEventListener( 'loadedmetadata', function () { durEl.textContent = fmt( video.duration ); } );
		if ( video.readyState >= 1 ) {
			durEl.textContent = fmt( video.duration );
		}
		video.addEventListener( 'play', function () {
			player.setAttribute( 'data-state', 'playing' );
			toggle.setAttribute( 'aria-label', toggle.getAttribute( 'data-label-pause' ) );
			requestAnimationFrame( loop );
			wake();
		} );
		video.addEventListener( 'pause', function () {
			player.setAttribute( 'data-state', 'paused' );
			toggle.setAttribute( 'aria-label', toggle.getAttribute( 'data-label-play' ) );
			wake();
		} );
		video.addEventListener( 'timeupdate', render );
		video.addEventListener( 'volumechange', function () {
			var silent = video.muted || video.volume === 0;
			player.toggleAttribute( 'data-muted', silent );
			muteBtn.setAttribute( 'aria-label', muteBtn.getAttribute( silent ? 'data-label-unmute' : 'data-label-mute' ) );
			vol.value = silent ? 0 : video.volume;
			fill( vol, silent ? 0 : video.volume );
		} );
		fill( vol, 1 );

		// On touch, the first tap on hidden controls only brings them back.
		var revealOnly = false;
		player.addEventListener( 'pointerdown', function ( e ) {
			revealOnly = player.classList.contains( 'is-idle' ) && e.pointerType !== 'mouse';
		}, true );

		// Clicking the picture (not the bar) plays / pauses, like any video player.
		player.addEventListener( 'click', function ( e ) {
			if ( revealOnly ) {
				revealOnly = false;
				return;
			}
			if ( e.target === player || e.target.closest( '[data-player-big]' ) ) {
				togglePlay();
			}
		} );
		toggle.addEventListener( 'click', togglePlay );

		seek.addEventListener( 'input', function () {
			seeking = true;
			var r = seek.value / 1000;
			fill( seek, r );
			timeEl.textContent = fmt( r * video.duration );
		} );
		seek.addEventListener( 'change', function () {
			if ( video.duration ) {
				video.currentTime = ( seek.value / 1000 ) * video.duration;
			}
			seeking = false;
		} );

		vol.addEventListener( 'input', function () {
			video.volume = Number( vol.value );
			video.muted = video.volume === 0;
		} );
		muteBtn.addEventListener( 'click', function () {
			if ( video.muted || video.volume === 0 ) {
				video.muted = false;
				if ( video.volume === 0 ) {
					video.volume = 1;
				}
			} else {
				video.muted = true;
			}
		} );

		// iPhone has no element fullscreen, so fall back to its native video fullscreen.
		fsBtn.addEventListener( 'click', function () {
			if ( document.fullscreenElement ) {
				document.exitFullscreen();
			} else if ( section.requestFullscreen ) {
				section.requestFullscreen();
			} else if ( video.webkitEnterFullscreen ) {
				video.webkitEnterFullscreen();
			}
		} );
		document.addEventListener( 'fullscreenchange', function () {
			var on = document.fullscreenElement === section;
			player.toggleAttribute( 'data-fullscreen', on );
			fsBtn.setAttribute( 'aria-label', fsBtn.getAttribute( on ? 'data-label-exit' : 'data-label-enter' ) );
		} );

		[ 'pointermove', 'pointerdown', 'focusin', 'keydown' ].forEach( function ( type ) {
			player.addEventListener( type, wake );
		} );
		player.addEventListener( 'pointerleave', function () {
			if ( ! video.paused ) {
				player.classList.add( 'is-idle' );
			}
		} );
	}

	// Video sound toggle: autoplay has to start muted, so let the visitor turn sound on.
	// Embeds are driven over postMessage, so no YouTube / Vimeo SDK is loaded.
	document.querySelectorAll( '[data-video-sound]' ).forEach( function ( section ) {
		var btn = section.querySelector( '[data-sound-toggle]' );
		var type = section.getAttribute( 'data-video-sound' );
		var video = section.querySelector( 'video' );
		var frame = section.querySelector( 'iframe' );
		if ( ! btn ) {
			return;
		}

		var setPressed = function ( on ) {
			btn.setAttribute( 'aria-pressed', String( on ) );
			btn.setAttribute( 'aria-label', btn.getAttribute( on ? 'data-label-on' : 'data-label-off' ) );
		};

		// MP4 set to "try sound": it starts unmuted when the browser allows it,
		// otherwise fall back to muted autoplay and leave the button to unmute.
		if ( video && video.hasAttribute( 'data-try-sound' ) && ! reduce ) {
			var attempt = video.play();
			if ( attempt ) {
				attempt.then( function () {
					setPressed( ! video.muted );
				} ).catch( function () {
					video.muted = true;
					video.play();
				} );
			}
		}

		// Keep the button in step when sound is changed from the MP4's own controls.
		if ( video ) {
			video.addEventListener( 'volumechange', function () {
				setPressed( ! video.muted && video.volume > 0 );
			} );
		}

		btn.addEventListener( 'click', function () {
			var on = btn.getAttribute( 'aria-pressed' ) !== 'true';

			if ( type === 'file' && video ) {
				video.muted = ! on;
				if ( on && video.paused ) {
					video.play();
				}
			} else if ( frame && frame.contentWindow ) {
				var msgs = type === 'vimeo'
					? [ { method: 'setMuted', value: ! on }, { method: 'setVolume', value: on ? 1 : 0 } ]
					: [ { event: 'command', func: on ? 'unMute' : 'mute', args: [] } ];
				msgs.forEach( function ( msg ) {
					frame.contentWindow.postMessage( JSON.stringify( msg ), '*' );
				} );
			}

			setPressed( on );
		} );
	} );

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
