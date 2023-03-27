<?php

if ( ! class_exists( 'WPMenuCart_WooCommerce_Pro' ) ) {
	
	class WPMenuCart_WooCommerce_Pro extends WPMenuCart_WooCommerce {     
	
		/**
		 * Construct.
		 */
		public function __construct() {}
	
		/**
		 * Add Menu Cart to menu
		 * 
		 * @return menu items including cart
		 */
		
		public function submenu_items() {
			global $woocommerce;
			// make sure cart and session loaded! https://wordpress.org/support/topic/activation-breaks-customise?replies=10#post-7908988
			if ( empty( $woocommerce->session ) ) {
				$session_class = apply_filters( 'woocommerce_session_handler', 'WC_Session_Handler' );
				$woocommerce->session = new $session_class();
			}
			if (empty($woocommerce->cart)) {
				$woocommerce->cart = new WC_Cart();
			}

			$cart = $woocommerce->cart->get_cart();
			// die('<pre>'.print_r( $cart, true ).'</pre>');
			$submenu_items = array();

			if (count($cart) > 0) {
				foreach ( $cart as $cart_item_key => $cart_item ) {
					$_product = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
					if (isset($cart_item['product_id'])) {
						$product_id = apply_filters( 'woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key );
					} else {
						$product_id = method_exists($_product, 'get_id') ? $_product->get_id() : $_product->id;
					}

					// item visibility filter for third party plugins compatibility
					if ( apply_filters( 'wpo_wpmenucart_cart_item_visible', true, $cart_item, $cart_item_key ) === false) {
						continue;
					}
					
					if ( $_product->exists() && $cart_item['quantity'] > 0 ) {
						$item_quantity  = apply_filters( 'wpo_wpmenucart_cart_item_quantity', esc_attr( $cart_item['quantity'] ), $cart_item, $cart_item_key );
						$item_price     = apply_filters( 'woocommerce_cart_item_price', $woocommerce->cart->get_product_price( $_product ), $cart_item, $cart_item_key );
						$product_name   = method_exists($_product, 'get_name') ? $_product->get_name() : $_product->get_title();
						$product_name   = apply_filters( 'wpo_wpmenucart_cart_item_name', $product_name, $cart_item, $cart_item_key );
						$item_name      = apply_filters( 'woocommerce_cart_item_name', $product_name, $cart_item, $cart_item_key );
						$item_thumbnail = apply_filters( 'woocommerce_cart_item_thumbnail', $_product->get_image(), $cart_item, $cart_item_key );
						$item_permalink = apply_filters( 'woocommerce_cart_item_permalink', $_product->is_visible() ? $_product->get_permalink( $cart_item ) : '', $cart_item, $cart_item_key );

						$submenu_items[] = array(
							'item_thumbnail'	=> $item_thumbnail,
							'item_name'			=> $item_name,
							'item_quantity'		=> $item_quantity,
							'item_price'		=> $item_price,
							'item_permalink'	=> $item_permalink,
							'cart_item'			=> $cart_item,
						);
					
					}
				}
			} else {
				$submenu_items = array();
			}
	
			return apply_filters( 'wpmenucart_submenu_items_data', $submenu_items );
		}

	}

}