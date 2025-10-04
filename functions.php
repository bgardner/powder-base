<?php
/**
 * Functions for the Powder WordPress theme.
 *
 * @package	Powder
 * @author	Brian Gardner
 * @license	GNU General Public License v3
 * @link	https://powder.design/
 */
 
/**
 * Setup theme defaults and supports.
 */
function powder_setup() {
	add_editor_style( get_template_directory_uri() . '/style.css' );
	remove_theme_support( 'core-block-patterns' );
}
add_action( 'after_setup_theme', 'powder_setup' );

/**
 * Enqueue frontend styles.
 */
function powder_enqueue_style_sheet() {
	wp_enqueue_style( 'powder', get_template_directory_uri() . '/style.css', [], wp_get_theme( 'powder' )->get( 'Version' ) );
}
add_action( 'wp_enqueue_scripts', 'powder_enqueue_style_sheet' );

/**
 * Register block styles.
 */
function powder_register_block_styles() {
	$block_styles = [
		'core/social-links' => array(
			'outline' => __( 'Outline', 'powder' ),
		),
	];

	foreach ( $block_styles as $block => $styles ) {
		foreach ( $styles as $style_name => $style_label ) {
			register_block_style(
				$block,
				[
					'name'  => $style_name,
					'label' => $style_label,
				]
			);
		}
	}
}
add_action( 'init', 'powder_register_block_styles' );

/**
 * Check for theme updates.
 */
function powder_theme_updates( $transient ) {
	$update_url = 'https://powder.design/theme-updates.json';

	$response = wp_remote_get( $update_url );
	if ( is_wp_error( $response ) ) {
		return $transient;
	}

	$data = json_decode( wp_remote_retrieve_body( $response ) );
	if ( ! $data ) {
		return $transient;
	}

	$theme           = wp_get_theme( 'powder' );
	$current_version = $theme->get( 'Version' );

	if ( version_compare( $current_version, $data->version, '<' ) ) {
		$transient->response['powder'] = [
			'theme'       => 'powder',
			'new_version' => $data->version,
			'url'         => 'https://powder.design/changelog/',
			'package'     => $data->download_url,
		];
	}

	return $transient;
}
add_filter( 'pre_set_site_transient_update_themes', 'powder_theme_updates' );
