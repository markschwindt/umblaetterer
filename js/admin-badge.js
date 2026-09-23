/**
 * The media picker for the badge widget.
 *
 * Events are delegated from the document because widgets appear, disappear and
 * are re-rendered as they are dragged, saved and previewed — binding directly
 * to the buttons would only ever catch the ones that happened to exist when
 * the page loaded.
 */

( function( $ ) {
	'use strict';

	if ( ! $ || ! window.wp || ! window.wp.media ) {
		return;
	}

	let frame;

	$( document ).on( 'click', '.umbl-badge-select', function( event ) {
		event.preventDefault();

		const $wrap = $( this ).closest( '.umbl-badge-image' );

		frame = window.wp.media( {
			title: $( this ).text(),
			button: { text: $( this ).text() },
			library: { type: 'image' },
			multiple: false,
		} );

		frame.on( 'select', function() {
			const image = frame.state().get( 'selection' ).first().toJSON();
			const thumb = image.sizes && image.sizes.thumbnail ? image.sizes.thumbnail.url : image.url;

			$wrap.find( '.umbl-badge-id' ).val( image.id ).trigger( 'change' );
			$wrap.find( '.umbl-badge-preview' ).html(
				$( '<img>' ).attr( 'src', thumb ).css( { maxWidth: '80px', height: 'auto' } )
			);
			$wrap.find( '.umbl-badge-remove' ).show();
		} );

		frame.open();
	} );

	$( document ).on( 'click', '.umbl-badge-remove', function( event ) {
		event.preventDefault();

		const $wrap = $( this ).closest( '.umbl-badge-image' );

		$wrap.find( '.umbl-badge-id' ).val( '' ).trigger( 'change' );
		$wrap.find( '.umbl-badge-preview' ).empty();
		$( this ).hide();
	} );

	// Only the "image" mark needs a picture; hide the field for the others.
	$( document ).on( 'change', '.umbl-badge-mark', function() {
		$( this )
			.closest( '.widget-content, .widget-inside' )
			.find( '.umbl-badge-image' )
			.toggle( 'image' === $( this ).val() );
	} );
}( window.jQuery ) );
