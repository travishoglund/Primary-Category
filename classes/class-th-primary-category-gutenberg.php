<?php

class TH_Primary_Category_Gutenberg {

	static function init() {
		add_action( 'init', __CLASS__ . '::register_meta_field' );
		add_action( 'current_screen', __CLASS__ . '::ready' );
	}

	/**
	 * Fires after current_screen hook to check for Gutenberg page builder
	 */
	static function ready() {
		if ( is_admin() && self::is_editing_gutenberg_admin() && self::is_applicable_post_type() ) {
			add_action( 'enqueue_block_editor_assets', __CLASS__ . '::enqueue_gutenberg_scripts_and_styles' );
		}
	}

	/**
	 * Ensures that we are on a Gutenberg Admin Editing Screen
	 * @return bool
	 */
	private static function is_editing_gutenberg_admin() {
		if ( function_exists( 'is_gutenberg_page' ) && is_gutenberg_page() ) {
			// Gutenberg is on
			return true;
		}

		$current_screen = get_current_screen();
		if ( method_exists( $current_screen, 'is_block_editor' ) && $current_screen->is_block_editor() ) {
			// Gutenberg page on 5+.
			return true;
		}

		return false;
	}

	/**
	 * Check that this Post Type is linked to Category Taxonomy
	 * @return bool
	 */
	private static function is_applicable_post_type() {
		$current_screen = get_current_screen();
		$post_type      = $current_screen->post_type;
		$taxonomies     = get_object_taxonomies( $post_type );

		return in_array( 'category', $taxonomies );
	}

	/**
	 * Enqueue Gutenberg scripts and styles for our custom plugin
	 */
	static function enqueue_gutenberg_scripts_and_styles() {

		$fingerprint_version = TH_PRIMARY_CATEGORY_DEBUG ? round( microtime( true ) * 1000 ) : TH_PRIMARY_CATEGORY_VERSION;

		wp_enqueue_script( 'th-primary-category-gutenberg-js',
			TH_PRIMARY_CATEGORY_RELATIVE_WP_PATH . 'admin/scripts/gutenberg/build/index.js',
			array(
				'lodash',
				'wp-components',
				'wp-compose',
				'wp-editor',
				'wp-edit-post',
				'wp-element',
				'wp-hooks',
				'wp-i18n',
				'wp-plugins',
			),
			$fingerprint_version );

		wp_enqueue_style( 'th-primary-category-gutenberg-css',
			TH_PRIMARY_CATEGORY_RELATIVE_WP_PATH . 'admin/styles/gutenberg.css',
			false,
			$fingerprint_version );
	}

	/**
	 * Register Meta Field for use with Gutenberg
	 */
	static function register_meta_field() {
		register_post_meta( '',
			TH_PRIMARY_CATEGORY_META_FIELD,
			array(
				'auth_callback' => function () {
					return current_user_can( 'edit_posts' ) && current_user_can( 'manage_categories' );
				},
				'show_in_rest'  => true,
				'single'        => true,
				'type'          => 'string',
			) );
	}

}
