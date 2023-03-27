jQuery( function( $ ) {
	// reload when item is added or removed
	$( document.body ).on( 'edd_cart_item_removed edd_cart_item_added', function( event, response ) {
		var data = {
			security:	wpmenucart_ajax.nonce,
			action:		"wpmenucart_ajax",
		};

		xhr = $.ajax({
			type:		'POST',
			url:		wpmenucart_ajax.ajaxurl,
			data:		data,
			success:	function( response ) {
				if( response.data.menu_cart != 'undefined' && response.data.menu_cart !== null ) {
					$('.wpmenucartli').html( response.data.menu_cart );
					$('div.wpmenucart-shortcode span.reload_shortcode').html( response.data.menu_cart );
				}

				if( $('.wpmenucart-floating-cart').length && response.data.floating_cart != 'undefined' && response.data.floating_cart !== null ) {
					$('.wpmenucart-floating-cart').html( response.data.floating_cart );
				}
			}
		});

		// update empty class for menu item
		if ( 'cart_quantity' in response && parseInt( response.cart_quantity ) > 0 ) {
			$('.empty-wpmenucart').removeClass('empty-wpmenucart');
			$('.wpmenucart-floating-cart').removeClass('empty');
			$('.wpmenucart-floating-contents').removeClass('empty-wpmenucart-floating-visible');
		} else {
			if ( !(wpmenucart_ajax.always_display) ) {
				$('.wpmenucartli').addClass('empty-wpmenucart');
				$('.wpmenucart-shortcode').addClass('empty-wpmenucart');
				$('.wpmenucart-floating-cart').addClass('empty');
			} else {
				$('.wpmenucart-floating-contents').addClass('empty-wpmenucart-floating-visible');
			}
		}
	});
});