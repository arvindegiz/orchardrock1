<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( !class_exists( 'WPO_Menu_Cart_Pro_Main' ) ) :

class WPO_Menu_Cart_Pro_Main {

	function __construct()	{
		// add filters to selected menus to add cart item <li>
		// add_action( 'init', array( $this, 'filter_nav_menus' ) );
		$this->filter_nav_menus();
		add_shortcode( 'wpmenucart', array( $this, 'shortcode' ) );

		// Enable shortcodes in text widgets
		if (!has_filter('widget_text','do_shortcode')) {
			add_filter('widget_text','do_shortcode');
		}

		// AJAX
		add_action( 'wp_ajax_wpmenucart_ajax', array( $this, 'built_in_ajax' ) );
		add_action( 'wp_ajax_nopriv_wpmenucart_ajax', array( $this, 'built_in_ajax' ) );

		$selected_shop_plugin = ! empty( WPO_Menu_Cart_Pro()->main_settings['shop_plugin'] ) ? WPO_Menu_Cart_Pro()->main_settings['shop_plugin'] : '';
		if ( apply_filters( 'wpo_wpmenucart_wc_fragments_enabled', ! isset( WPO_Menu_Cart_Pro()->main_settings['builtin_ajax'] ) && $selected_shop_plugin == 'WooCommerce' ) ) {
			add_filter( 'woocommerce_add_to_cart_fragments', array( $this, 'woocommerce_ajax_fragments' ) );
		}
		// AJAX remove flyout cart item
		if( ( function_exists('WC') || function_exists('EDD') ) && isset(WPO_Menu_Cart_Pro()->main_settings['flyout_remove_items']) && WPO_Menu_Cart_Pro()->main_settings['flyout_remove_items'] != '' ) {
			add_action( 'wp_ajax_wpmenucart_ajax_remove_flyout_cart_item', array( $this, 'remove_flyout_cart_item' ) );
			add_action( 'wp_ajax_nopriv_wpmenucart_ajax_remove_flyout_cart_item', array( $this, 'remove_flyout_cart_item' ) );
		}

		// Twenty Twenty compatibility
		if ( function_exists('twentytwenty_add_sub_toggles_to_main_menu') ) {
			add_filter( 'wpmenucart_main_li_class', array( $this, 'twentytwenty_li_class' ), 10, 3 );
			add_filter( 'wpmenucart_main_a_start', array( $this, 'twentytwenty_item_wrap_before' ), 10, 2 );
			add_filter( 'wpmenucart_main_a_end', array( $this, 'twentytwenty_item_wrap_after' ), 10, 2 );
		}

		// Floating cart icon
		add_action( 'wp_footer', array( $this, 'display_floating_cart_icon' ) );

		// gutenberg blocks
		$this->register_blocks(); // this class instantiates on 'init' hook
	}

	/**
	 * Add filters to selected menus to add cart item <li>
	 */
	public function filter_nav_menus() {
		// exit if no menus set
		if ( !isset( WPO_Menu_Cart_Pro()->main_settings['menu_slugs'] ) || empty( WPO_Menu_Cart_Pro()->main_settings['menu_slugs'] ) ) {
			return;
		}

		//grab menu slugs
		$menu_slugs = apply_filters( 'wpmenucart_menu_slugs', WPO_Menu_Cart_Pro()->main_settings['menu_slugs'] );

		// Loop through $menu_slugs array and add cart <li> item to each menu
		foreach ($menu_slugs as $menu_slug) {
			if ( $menu_slug != '0' ) {
				add_filter( 'wp_nav_menu_' . $menu_slug . '_items', array( $this, 'add_nav_menu_item' ) , 10, 2 );
			}
		}
	}

	/**
	 * Add Menu Cart to menu
	 * 
	 * @return menu items + Menu Cart item
	 */
	public function add_nav_menu_item( $nav_menu_items, $args ) {
		// check if we should add
		if ( $this->should_render_menucart() === false ) {
			return $nav_menu_items;
		}

		$menu_slug      = ( isset( $args->menu ) && isset( $args->menu->slug ) ) ? $args->menu->slug : '';
		$menu_item_html = $this->get_menucart_item( $nav_menu_items, array( 'menu_slug' => $menu_slug, 'menu_args' => $args ) );
		
		if ( apply_filters( 'wpmenucart_prepend_menu_item', false ) ) {
			$nav_menu_items  = apply_filters( 'wpmenucart_menu_item_wrapper', $menu_item_html ) . $nav_menu_items;
		} else {
			$nav_menu_items .= apply_filters( 'wpmenucart_menu_item_wrapper', $menu_item_html );
		}

		return $nav_menu_items;
	}
	
	/**
	 * Determine whether menu cart should be added/rendered
	 * Used to prevent fatal errors in certain editor contexts
	 * 
	 * @return bool whether to render or not
	 */
	public function should_render_menucart() {
		// wp_doing_ajax() requires WP4.7+
		$is_ajax = function_exists( 'wp_doing_ajax' ) ? wp_doing_ajax() : defined( 'DOING_AJAX' ) && DOING_AJAX;
		$render = true; // it's all good man!

		if ( false === WPO_Menu_Cart_Pro()->is_shop_active( array(), WPO_Menu_Cart_Pro()->main_settings['shop_plugin'] ) ) {
			$render = false;
		}
		// WooCommerce
		elseif ( WPO_Menu_Cart_Pro()->main_settings['shop_plugin'] == 'WooCommerce' ) {
			// Elementor compatibility
			if ( is_admin() && (isset($_GET['action']) && $_GET['action'] == 'elementor') ) {
				$render = false;
			}
			// disable on cart & checkout pages by default
			elseif ( ! $is_ajax && function_exists('WC') && ( is_checkout() || is_cart() ) && empty( WPO_Menu_Cart_Pro()->main_settings['show_on_cart_checkout_page'] ) ) {
				$render = false;
			}
		}

		return apply_filters( 'wpmenucart_should_render', $render );
	}

	/**
	 * Determine whether floating cart icon should be added/rendered
	 * Used to prevent fatal errors in certain editor contexts
	 * 
	 * @return bool whether to render or not
	 */
	public function should_render_floating_cart() {
		$render = $this->should_render_menucart();

		// floating cart
		if ( ! isset( WPO_Menu_Cart_Pro()->main_settings['floating_cart'] ) || WPO_Menu_Cart_Pro()->main_settings['floating_cart'] == 'no' ) {
			$render = false;
		}

		return apply_filters( 'wpmenucart_floating_cart_should_render', $render );
	}

	/**
	 * Create HTML for shortcode
	 * @param  array $atts shortcode attributes
	 * @return string      'menucart' html
	 */
	public function shortcode($atts) {
		if( $this->should_render_menucart() === true ) {
			extract(shortcode_atts( array('style' => '', 'flyout' => 'hover', 'before' => '', 'after' => '') , $atts));

			$item_data = WPO_Menu_Cart_Pro()->shop->menu_item();
		
			$classes = $flyout;

			// Hide when empty
			if ( $item_data['cart_contents_count'] == 0 && ! isset( WPO_Menu_Cart_Pro()->main_settings['always_display'] ) && ! WPO_Menu_Cart_Pro()->main->is_block_editor() ) {
				$classes .= ' empty-wpmenucart';
			}

			$menu_item_html = $this->get_menucart_item( '', array( 'part' => 'main_li_content') );

			$menu = $before . '<span class="reload_shortcode">'.$menu_item_html. '</span>' . $after;
			$html = '<div class="wpmenucart-shortcode '.$classes.'" style="'.$style.'">'.$menu.'</div>';
			return $html;
		}
	}

	/**
	 * Get menu cart item
	 * 
	 * @param  string $nav_menu_items
	 * @param  array  $args
	 * @return string                  'menucart_item' html
	 */
	public function get_menucart_item( $nav_menu_items = '', $args = array() ) {
		$menucart_item = new WPO_Menu_Cart_Pro_Template( 'menucart-item', $nav_menu_items, $args );
		$menucart_item = $menucart_item->get_output();

		return $menucart_item;
	}

	/**
	 * Get floating cart icon
	 * 
	 * @param  string $nav_menu_items
	 * @param  array  $args
	 * @return string                  'floating_cart' html
	 */
	public function get_floating_cart_icon( $nav_menu_items = '', $args = array() ) {
		$floating_cart = new WPO_Menu_Cart_Pro_Template( 'floating-cart', $nav_menu_items, $args );
		$floating_cart = $floating_cart->get_output();

		return $floating_cart;
	}

	public function display_floating_cart_icon() {
		if( $this->should_render_floating_cart() === true ) {
			echo $this->get_floating_cart_icon();
		}
	}

	/**
	 * WooCommerce Ajax
	 * 
	 * @return ajax fragments
	 */
	public function woocommerce_ajax_fragments( $fragments ) {
		if ( ! defined('WOOCOMMERCE_CART') ) {
			define( 'WOOCOMMERCE_CART', true );
		}
		
		$fragments['a.wpmenucart-contents'] = $this->get_menucart_item( '', array( 'part' => 'main_a', 'wc_fragments' => true ) );
		$fragments['.sub-menu.wpmenucart'] = $this->get_menucart_item( '', array( 'part' => 'submenu', 'wc_fragments' => true ) );
		if( $this->should_render_floating_cart() === true ) {
			$fragments['.wpmenucart-floating-cart'] = $this->get_floating_cart_icon();
		}

		return $fragments;
	}

	public function built_in_ajax() {
		check_ajax_referer( 'wpmenucart', 'security' );
		
		if ( ! defined('WOOCOMMERCE_CART') ) {
			define( 'WOOCOMMERCE_CART', true );
		}
		
		$response['menu_cart'] = $this->get_menucart_item( '', array( 'part' => 'main_li_content' ) );
		if( $this->should_render_floating_cart() === true ) {
			$response['floating_cart'] = $this->get_floating_cart_icon( '', array( 'part' => 'floating_div_content' ) );
		}

		wp_send_json_success( $response );
		die();
	}

	public function twentytwenty_li_class( $classes, $item_data, $settings ) {
		return "{$classes} menu-item-menucart"; // toggle targets menu-item-{$ID}, as passed in the functions below
	}

	public function twentytwenty_item_wrap_before( $before, $parser ) {
		if ( empty( $parser->menu_args ) ) {
			return $before;
		}

		$args = twentytwenty_add_sub_toggles_to_main_menu(
			$parser->menu_args,
			(object) array(
				'classes' => array('menu-item-has-children'),
				'ID'      => 'menucart',
			),
			1
		);

		if (!empty($args->before)) {
			$before = $args->before . $before;
		}
		return $before;
	}

	public function twentytwenty_item_wrap_after( $after, $parser ) {
		if ( empty( $parser->menu_args ) ) {
			return $after;
		}

		$args = twentytwenty_add_sub_toggles_to_main_menu(
			$parser->menu_args,
			(object) array(
				'classes' => array('menu-item-has-children'),
				'ID'      => 'menucart',
			),
			1
		);

		if (!empty($args->after)) {
			$after = $args->after . $after;
		}
		return $after;
	}

	public function remove_flyout_cart_item() {
		check_ajax_referer( 'wpmenucart', 'security' );

		if ( ! defined('WOOCOMMERCE_CART') ) {
			define( 'WOOCOMMERCE_CART', true );
		}

		if( $_REQUEST['action'] == 'wpmenucart_ajax_remove_flyout_cart_item' && ! empty($_REQUEST['key']) ) {

			$cart_item_key = sanitize_text_field( $_REQUEST['key'] );
			
			if( function_exists( 'WC' ) ) {
				$output = WC()->cart->remove_cart_item( $cart_item_key );
			} elseif( function_exists( 'EDD' ) && isset( $_SESSION['edd']['edd_cart'] ) ) {
				$cart_contents = json_decode( $_SESSION['edd']['edd_cart'] );
				$output        = false;
				
				if( ! empty( $cart_contents ) ) {
					foreach( $cart_contents as $key => $cart_item ) {
						if( $cart_item->id == $cart_item_key ) {
							edd_remove_from_cart( $key );
							$output = true;
							break;
						}
					}
				}
			} else {
				$output = false;
			}
			
			if( $output ) {
				$response['menu_cart'] = $this->get_menucart_item( '', array( 'part' => 'main_li_content' ) );
				if( $this->should_render_floating_cart() === true ) {
					$response['floating_cart'] = $this->get_floating_cart_icon( '', array( 'part' => 'floating_div_content' ) );
				}
				wp_send_json_success( $response );
			} else {
				wp_send_json_error( $output );
			}
		}

		die();
	}

	public function register_blocks() {
		if ( ! function_exists( 'register_block_type' ) ) {
			return;
		}
		
		// navigation block
		register_block_type( 'wpo/wpmenucart-navigation', array(
			'editor_script'   => 'wpmenucart-blocks',
			'attributes'      => array(
				'context' => array(
					'type'    => 'string',
					'default' => 'navigation',
				),
			),
			'render_callback' => array( $this, 'block_output' ),
		) );
		// regular block
		register_block_type( 'wpo/wpmenucart', array(
			'editor_script'   => 'wpmenucart-blocks',
			'attributes'      => array(
				'context' => array(
					'type'    => 'string',
					'default' => 'regular',
				),
			),
			'render_callback' => array( $this, 'block_output' ),
		) );
	}

	public function block_output( $atts ) {
		if ( $this->should_render_menucart() === false ) {
			return '';
		}

		$menu = sprintf( '<ul class="wp-block-navigation__container">%s</ul>', $this->get_menucart_item( '', array() ) );

		if ( ! empty( $atts['context'] ) && $atts['context'] == 'regular' ) {
			$menu = '<div class="wp-block-navigation wpmenucart-block-wrapper">'.$menu.'</div>';
		}

		if ( $this->is_block_editor() ) {
			// deactivate links when using the full site or block editor to prevent navigating away from the editor
			$menu = preg_replace( '/(<[^>]+) href=".*?"/i', '$1', $menu );
		}

		return $menu;
	}

	public function is_rest_request() {
		return defined( 'REST_REQUEST' ) && REST_REQUEST;
	}

	public function is_block_editor() {
		if ( $this->is_rest_request() ) {
			$route = untrailingslashit( $GLOBALS['wp']->query_vars['rest_route'] );
			if ( strpos( $route, 'wpo/wpmenucart-navigation' ) !== false || strpos( $route, '/navigation' ) !== false ) {
				return true;
			} elseif ( strpos( $route, 'wpo/wpmenucart' ) !== false || strpos( $route, '/block-rendered' ) !== false ) {
				return true;
			}
		}
		return false;
	}

	public function is_block_theme() {
		$theme = wp_get_theme();
		if ( ! empty( $theme ) && is_callable( array( $theme, 'is_block_theme' ) ) ) {
			return $theme->is_block_theme();
		}
		return false;
	}

	public function get_current_theme_name() {
		$theme = wp_get_theme();
		if ( ! empty( $theme ) && is_callable( array( $theme, 'display' ) ) ) {
			return $theme->display( 'Name' );
		}
		return false;
	}

}


endif; // class_exists