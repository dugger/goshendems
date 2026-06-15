/**
 * Client-side category filtering and hash scrolling for the Resources archive.
 */
( function() {
	const archive = document.querySelector( '.resources-list' );
	if ( ! archive ) {
		return;
	}

	const cards = archive.querySelectorAll( '.resource-card' );
	const filterButtons = document.querySelectorAll( '[data-resource-filter]' );

	function setActiveFilter( slug ) {
		filterButtons.forEach( function( button ) {
			const isActive = button.getAttribute( 'data-resource-filter' ) === slug;
			button.classList.toggle( 'is-active', isActive );
			button.setAttribute( 'aria-pressed', isActive ? 'true' : 'false' );
		} );

		cards.forEach( function( card ) {
			if ( 'all' === slug ) {
				card.hidden = false;
				return;
			}

			const categories = card.getAttribute( 'data-resource-categories' ) || '';
			card.hidden = ! categories.split( ' ' ).includes( slug );
		} );
	}

	filterButtons.forEach( function( button ) {
		button.addEventListener( 'click', function() {
			setActiveFilter( button.getAttribute( 'data-resource-filter' ) );
		} );
	} );

	function scrollToHash() {
		if ( ! window.location.hash ) {
			return;
		}

		const target = document.querySelector( window.location.hash );
		if ( target ) {
			target.scrollIntoView( { behavior: 'smooth', block: 'start' } );
			target.classList.add( 'is-highlighted' );
			window.setTimeout( function() {
				target.classList.remove( 'is-highlighted' );
			}, 2000 );
		}
	}

	if ( window.location.hash ) {
		window.requestAnimationFrame( scrollToHash );
	}

	window.addEventListener( 'hashchange', scrollToHash );
}() );
