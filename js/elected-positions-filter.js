/**
 * File elected-positions-filter.js.
 *
 * Handles filtering elected positions by level on the archive page.
 * Supports URL query parameters for linking to pre-filtered lists.
 */
( function() {
	const filterButtons = document.querySelectorAll( '.elected-positions-filter-btn' );
	const positionGroups = document.querySelectorAll( '.elected-positions-group' );

	// Return early if filter buttons or position groups don't exist.
	if ( ! filterButtons.length || ! positionGroups.length ) {
		return;
	}

	/**
	 * Get the level filter from URL query parameter
	 */
	function getLevelFromURL() {
		const urlParams = new URLSearchParams( window.location.search );
		return urlParams.get( 'level' ) || 'all';
	}

	/**
	 * Update URL with level filter query parameter
	 */
	function updateURL( level ) {
		const url = new URL( window.location.href );
		
		if ( level === 'all' ) {
			url.searchParams.delete( 'level' );
		} else {
			url.searchParams.set( 'level', level );
		}
		
		// Update URL without page reload
		window.history.pushState( { level: level }, '', url.toString() );
	}

	/**
	 * Apply filter based on selected level
	 */
	function applyFilter( selectedLevel, scrollToFirst ) {
		// Update active state on buttons
		filterButtons.forEach( function( btn ) {
			const btnLevel = btn.getAttribute( 'data-level' );
			if ( btnLevel === selectedLevel ) {
				btn.classList.add( 'active' );
			} else {
				btn.classList.remove( 'active' );
			}
		} );

		// Show/hide position groups based on selected level
		let firstVisibleGroup = null;
		positionGroups.forEach( function( group ) {
			const groupLevel = group.getAttribute( 'data-level' );

			if ( selectedLevel === 'all' || groupLevel === selectedLevel ) {
				group.style.display = '';
				// Track first visible group for scrolling
				if ( selectedLevel !== 'all' && ! firstVisibleGroup ) {
					firstVisibleGroup = group;
				}
			} else {
				group.style.display = 'none';
			}
		} );

		// Smooth scroll to first visible group if filtering (not "all")
		if ( scrollToFirst && selectedLevel !== 'all' && firstVisibleGroup ) {
			setTimeout( function() {
				firstVisibleGroup.scrollIntoView( { behavior: 'smooth', block: 'start' } );
			}, 100 );
		}
	}

	// Apply filter on page load based on URL parameter
	const initialLevel = getLevelFromURL();
	applyFilter( initialLevel, true );

	// Add click event listeners to filter buttons
	filterButtons.forEach( function( button ) {
		button.addEventListener( 'click', function() {
			const selectedLevel = this.getAttribute( 'data-level' );
			
			// Update URL
			updateURL( selectedLevel );
			
			// Apply filter
			applyFilter( selectedLevel, true );
		} );
	} );

	// Handle browser back/forward buttons
	window.addEventListener( 'popstate', function( event ) {
		const level = getLevelFromURL();
		applyFilter( level, false );
	} );
}() );

