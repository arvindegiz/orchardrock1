// used when the plugin setting is enabled
jQuery( function( $ ) {
	$( document.body ).on( 'click', '.wpmenucart-product-remove', function() {
		var data = {
			security:	wpmenucart_ajax.nonce,
			action:		"wpmenucart_ajax_remove_flyout_cart_item",
			key:		$(this).data('key'),
		};

		xhr = $.ajax({
			type:		'POST',
			url:		wpmenucart_ajax.ajaxurl,
			data:		data,
			success:	function( response ) {
				if( response ) {
					if( response.data.menu_cart != 'undefined' && response.data.menu_cart !== null ) {
						$('.wpmenucartli').html( response.data.menu_cart );
						$('div.wpmenucart-shortcode span.reload_shortcode').html( response.data.menu_cart );
					}
	
					if( $('.wpmenucart-floating-cart').length && response.data.floating_cart != 'undefined' && response.data.floating_cart !== null ) {
						$('.wpmenucart-floating-cart').html( response.data.floating_cart );
					}
				} else {
					return;
				}
			},
		});
	});
});