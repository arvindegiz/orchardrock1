<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'WPO_Menu_Cart_Pro_Assets' ) ) :

class WPO_Menu_Cart_Pro_Assets {

	public $asset_suffix;
	
	function __construct()	{
		$this->asset_suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

		add_action( 'wp_enqueue_scripts', array( $this, 'frontend_scripts_styles' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'backend_scripts_styles' ) );

		// gutenberg blocks
		add_action( 'wp_default_styles', array( $this, 'load_block_editor_styles' ), 99 ); // load block editor styles
		$this->register_blocks_scripts(); // this class instantiates on 'init' hook
	}

	/**
	 * To avoid issues with relative font paths, we parse the CSS file and print it inline
	 */
	function get_parsed_font_css() {
		ob_start();
		if ( file_exists( WPO_Menu_Cart_Pro()->plugin_path() . '/assets/css/wpmenucart-font.css' ) ) {
			include( WPO_Menu_Cart_Pro()->plugin_path() . '/assets/css/wpmenucart-font.css' ) ;
		}
		$font_css = str_replace( '../font', WPO_Menu_Cart_Pro()->plugin_url() . '/assets/font', ob_get_clean() );
		return $font_css;
	}

	/*
	 * Allow wpmenucart-main.css to be overriden via the theme
	 */
	public function get_main_css_url() {
		return file_exists( get_stylesheet_directory() . '/wpmenucart-main.css' ) ? get_stylesheet_directory_uri() . '/wpmenucart-main.css' : WPO_Menu_Cart_Pro()->plugin_url() . '/assets/css/wpmenucart-main'.$this->asset_suffix.'.css';
	}

	/**
	 * Load styles & scripts
	 */
	public function frontend_scripts_styles ( $hook ) {
		if ( isset( WPO_Menu_Cart_Pro()->main_settings['icon_display'] ) ) {
			wp_enqueue_style(
				'wpmenucart-icons',
				WPO_Menu_Cart_Pro()->plugin_url() . '/assets/css/wpmenucart-icons-pro'.$this->asset_suffix.'.css',
				array(),
				WPO_MENU_CART_PRO_VERSION
			);
			
			wp_add_inline_style( 'wpmenucart-icons', $this->get_parsed_font_css() );
		}

		wp_enqueue_style(
			'wpmenucart',
			$this->get_main_css_url(),
			array(),
			WPO_MENU_CART_PRO_VERSION
		);

		// cart icon color
		if ( isset( WPO_Menu_Cart_Pro()->main_settings['cart_icon_color_enabled'] ) ) {
			$cart_color = WPO_Menu_Cart_Pro()->main_settings['cart_icon_color'];
			wp_add_inline_style( 'wpmenucart', '.wpmenucart-contents i { color: ' . $cart_color . ' !important; }' ); 
		}

		// add custom styles when entered in geek settings
		if ( ! empty( WPO_Menu_Cart_Pro()->geek_settings['custom_styles'] ) ) {
			wp_add_inline_style( 'wpmenucart', WPO_Menu_Cart_Pro()->geek_settings['custom_styles'] );
		}

		// hide built-in theme carts
		if ( isset( WPO_Menu_Cart_Pro()->main_settings['hide_theme_cart'] ) ) {
			wp_add_inline_style( 'wpmenucart', '.et-cart-info { display:none !important; } .site-header-cart { display:none !important; }' );
		}

		// load Stylesheet if twentytwelve is active
		if ( wp_get_theme() == 'Twenty Twelve' ) {
			wp_enqueue_style(
				'wpmenucart-twentytwelve',
				WPO_Menu_Cart_Pro()->plugin_url() . '/assets/css/wpmenucart-twentytwelve'.$this->asset_suffix.'.css',
				array(),
				WPO_MENU_CART_PRO_VERSION
			);
		}

		// load Stylesheet if twentyfourteen is active
		if ( wp_get_theme() == 'Twenty Fourteen' ) {
			wp_enqueue_style(
				'wpmenucart-twentyfourteen',
				WPO_Menu_Cart_Pro()->plugin_url() . '/assets/css/wpmenucart-twentyfourteen'.$this->asset_suffix.'.css',
				array(),
				WPO_MENU_CART_PRO_VERSION
			);
		}

		// load builtin ajax if enabled or required
		if ( isset( WPO_Menu_Cart_Pro()->main_settings['builtin_ajax'] ) ) {
			wp_enqueue_script(
				'wpmenucart',
				WPO_Menu_Cart_Pro()->plugin_url() . '/assets/js/wpmenucart'.$this->asset_suffix.'.js',
				array( 'jquery' ),
				WPO_MENU_CART_PRO_VERSION
			);

			// get URL to WordPress ajax handling page  
			if ( WPO_Menu_Cart_Pro()->main_settings['shop_plugin'] == 'Easy Digital Downloads' && function_exists( 'edd_get_ajax_url' ) ) {
				// use EDD function to prevent SSL issues http://git.io/V7w76A
				$ajax_url = edd_get_ajax_url();
			} else {
				$ajax_url = admin_url( 'admin-ajax.php' );
			}

			wp_localize_script(
				'wpmenucart',
				'wpmenucart_ajax',
				array(  
					'ajaxurl' => $ajax_url,
					'nonce'   => wp_create_nonce( 'wpmenucart' ),
				)
			);
		}

		if ( ! isset( WPO_Menu_Cart_Pro()->main_settings['builtin_ajax'] ) && WPO_Menu_Cart_Pro()->main_settings['shop_plugin'] == 'Easy Digital Downloads' ) {
			wp_enqueue_script(
				'wpmenucart-edd-ajax',
				WPO_Menu_Cart_Pro()->plugin_url() . '/assets/js/wpmenucart-edd-ajax'.$this->asset_suffix.'.js',
				array( 'jquery' ),
				WPO_MENU_CART_PRO_VERSION
			);

			wp_localize_script(
				'wpmenucart-edd-ajax',
				'wpmenucart_ajax',
				array(  
					'ajaxurl'        => function_exists( 'edd_get_ajax_url' ) ? edd_get_ajax_url() : admin_url( 'admin-ajax.php' ),
					'nonce'          => wp_create_nonce( 'wpmenucart' ),
					'always_display' => isset( WPO_Menu_Cart_Pro()->main_settings['always_display'] ) ? WPO_Menu_Cart_Pro()->main_settings['always_display'] : '',
				)
			);
		}

		if ( ( function_exists('WC') || function_exists('EDD') ) && isset( WPO_Menu_Cart_Pro()->main_settings['flyout_remove_items'] ) && WPO_Menu_Cart_Pro()->main_settings['flyout_remove_items'] != '' ) {
			wp_enqueue_script(
				'wpmenucart-remove',
				WPO_Menu_Cart_Pro()->plugin_url() . '/assets/js/wpmenucart-remove'.$this->asset_suffix.'.js',
				array( 'jquery' ),
				WPO_MENU_CART_PRO_VERSION
			);

			wp_localize_script(
				'wpmenucart-remove',
				'wpmenucart_ajax',
				array(  
					'ajaxurl' => admin_url( 'admin-ajax.php' ),
					'nonce'   => wp_create_nonce( 'wpmenucart' ),
				)
			);
		}

		// extra script that improves AJAX behavior when 'Always display cart' is disabled
		if ( ! isset( WPO_Menu_Cart_Pro()->main_settings['always_display'] ) ) {
			wp_enqueue_script(
				'wpmenucart-ajax-assist',
				WPO_Menu_Cart_Pro()->plugin_url() . '/assets/js/wpmenucart-ajax-assist'.$this->asset_suffix.'.js',
				array( 'jquery' ),
				WPO_MENU_CART_PRO_VERSION
			);

			wp_localize_script(
				'wpmenucart-ajax-assist',
				'wpmenucart_ajax_assist',
				array(  
					'shop_plugin'    => WPO_Menu_Cart_Pro()->main_settings['shop_plugin'],
					'always_display' => isset( WPO_Menu_Cart_Pro()->main_settings['always_display'] ) ? WPO_Menu_Cart_Pro()->main_settings['always_display'] : '',
				)
			);
		}
	}

	/**
	 * Load styles & scripts
	 */
	public function backend_scripts_styles ( $hook ) {
		// only load on our own settings page
		// maybe find a way to refer directly to WPO_Menu_Cart_Pro_Settings::$options_page_hook ?
		if ( $hook == 'woocommerce_page_wpo_wpmenucart_options_page' || $hook == 'settings_page_wpo_wpmenucart_options_page' ) {
			wp_enqueue_style(
				'wpmenucart-icons',
				WPO_Menu_Cart_Pro()->plugin_url() . '/assets/css/wpmenucart-icons-pro'.$this->asset_suffix.'.css',
				array(),
				WPO_MENU_CART_PRO_VERSION
			);
			
			wp_add_inline_style( 'wpmenucart-icons', $this->get_parsed_font_css() );

			wp_enqueue_style(
				'wpmenucart-admin',
				WPO_Menu_Cart_Pro()->plugin_url() . '/assets/css/wpmenucart-admin'.$this->asset_suffix.'.css',
				array( 'jquery-ui-style' ),
				WPO_MENU_CART_PRO_VERSION
			);
			
			wp_enqueue_script(
				'wpmenucart-admin',
				WPO_Menu_Cart_Pro()->plugin_url() . '/assets/js/wpmenucart-admin'.$this->asset_suffix.'.js',
				array( 'common', 'jquery', 'jquery-ui-tabs' ),
				WPO_MENU_CART_PRO_VERSION
			);

			wp_enqueue_script(
				'wpmenucart-upload-js',
				WPO_Menu_Cart_Pro()->plugin_url() . '/assets/js/media-upload'.$this->asset_suffix.'.js',
				array( 'jquery' ),
				WPO_MENU_CART_PRO_VERSION
			);

			wp_enqueue_media();
		}
	}

	/**
	 * Load Block Editor CSS
	 */
	public function load_block_editor_styles( $wp_styles ) {
		$wp_edit_blocks = $wp_styles->query( 'wp-edit-blocks', 'registered' );
		$handles        = array(
			'wpmenucart-icons',
			'wpmenucart',
		);

		if ( ! $wp_edit_blocks ) {
			return;
		}

		// add handle css as 'wp-edit-blocks' dependency
		foreach ( $handles as $handle ) {
			$style = $wp_styles->query( $handle, 'registered' );
			if ( ! $style ) {
				$wp_styles->add( 'wpmenucart-icons', WPO_Menu_Cart_Pro()->plugin_url() . '/assets/css/wpmenucart-icons-pro'.$this->asset_suffix.'.css', array(), WPO_MENU_CART_PRO_VERSION, 'all' );
				$wp_styles->add( 'wpmenucart', $this->get_main_css_url(), array(), WPO_MENU_CART_PRO_VERSION, 'all' );
			}
			if ( $wp_styles->query( $handle, 'registered' ) && ! in_array( $handle, $wp_edit_blocks->deps, true ) ) {
				$wp_edit_blocks->deps[] = $handle;
			}
		}

		// add inline font css
		$wp_styles->add_inline_style( 'wp-edit-blocks', $this->get_parsed_font_css() );

		// add custom styles when entered in geek settings
		if ( ! empty( WPO_Menu_Cart_Pro()->geek_settings['custom_styles'] ) ) {
			$wp_styles->add_inline_style( 'wpmenucart', WPO_Menu_Cart_Pro()->geek_settings['custom_styles'] );
		}

		// hide built-in theme carts
		if ( isset( WPO_Menu_Cart_Pro()->main_settings['hide_theme_cart'] ) ) {
			$wp_styles->add_inline_style( 'wpmenucart', '.et-cart-info { display:none !important; } .site-header-cart { display:none !important; }' );
		}
	}

	/**
	 * Register blocks scripts
	 */
	public function register_blocks_scripts() {
		wp_register_script(
			'wpmenucart-blocks',
			WPO_Menu_Cart_Pro()->plugin_url() . '/assets/js/wpmenucart-blocks'.$this->asset_suffix.'.js',
			array( 'wp-blocks', 'wp-element', 'wp-i18n', 'wp-server-side-render' ),
			WPO_MENU_CART_PRO_VERSION
		);
	}

}

endif; // class_exists

return new WPO_Menu_Cart_Pro_Assets();