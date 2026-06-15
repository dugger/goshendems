/**
 * Hide the home hero description when the CTA block overflows the hero image.
 */
( function() {
	const heroes = document.querySelectorAll( '.hero:not(.hero--short)' );
	if ( ! heroes.length ) {
		return;
	}

	function ctaOverflowsHero( hero, cta ) {
		const heroTop = hero.getBoundingClientRect().top;
		const ctaTop = cta.getBoundingClientRect().top;

		return ctaTop < heroTop + 2;
	}

	function adjustHeroCta( hero ) {
		const cta = hero.querySelector( '.cta' );
		const description = cta && cta.querySelector( '.hero__description' );

		if ( ! cta || ! description ) {
			return;
		}

		description.hidden = false;

		if ( ctaOverflowsHero( hero, cta ) ) {
			description.hidden = true;
		}
	}

	function adjustAllHeroCtas() {
		heroes.forEach( adjustHeroCta );
	}

	adjustAllHeroCtas();

	if ( document.fonts && document.fonts.ready ) {
		document.fonts.ready.then( adjustAllHeroCtas );
	}

	let resizeTimer;
	window.addEventListener( 'resize', function() {
		window.clearTimeout( resizeTimer );
		resizeTimer = window.setTimeout( adjustAllHeroCtas, 100 );
	} );

	if ( typeof ResizeObserver !== 'undefined' ) {
		const observer = new ResizeObserver( adjustAllHeroCtas );
		heroes.forEach( function( hero ) {
			observer.observe( hero );
		} );
	}
}() );
