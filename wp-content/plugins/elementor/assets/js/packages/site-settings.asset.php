<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * This file is generated by Webpack, do not edit it directly.
 */

return [
	'handle' => 'elementor-packages-site-settings',
	'src' => plugins_url( '/', __FILE__ ) . 'site-settings{{MIN_SUFFIX}}.js',
	'i18n' => [
		'domain' => 'elementor',
		'replace_requested_file' => true,
	],
	'type' => 'extension',
	'deps' => [
		'elementor-packages-documents',
		'elementor-packages-editor',
		'elementor-packages-icons',
		'elementor-packages-top-bar',
		'elementor-packages-ui',
		'elementor-packages-v1-adapters',
		'wp-i18n',
	],
];
