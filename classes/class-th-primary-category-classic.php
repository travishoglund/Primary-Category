<?php

class TH_Primary_Category_Classic {

	static function init() {
		add_action( 'current_screen', __CLASS__ . '::ready' );
	}

	/**
	 * Fires after current_screen hook to check for Gutenberg page builder before defaulting to classic
	 */
	static function ready() {
		if ( is_admin() && self::is_editing_classic_admin() && self::is_applicable_post_type() ) {
			add_action( 'admin_enqueue_scripts', __CLASS__ . '::enqueue_classic_scripts_and_styles' );
			add_action( 'add_meta_boxes', __CLASS__ . '::register_meta_boxes' );
			add_action( 'save_post', __CLASS__ . '::save_post', 10, 2 );
		}
	}

	/**
	 * Ensures Gutenberg is NOT active on editing screen
	 * @return bool
	 */
	private static function is_editing_classic_admin() {

		$current_screen = get_current_screen();

		// Make sure we are on an edit screen
		if ( property_exists( $current_screen, 'base' ) && $current_screen->base === 'post' ) {

			if ( function_exists( 'is_gutenberg_page' ) && is_gutenberg_page() ) {
				// Gutenberg is on
				return false;
			}

			if ( method_exists( $current_screen, 'is_block_editor' ) && $current_screen->is_block_editor() ) {
				// Gutenberg page on 5+.
				return false;
			}

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
	 * Enqueue Classic scripts and styles for our custom plugin
	 */
	static function enqueue_classic_scripts_and_styles() {
		$fingerprint_version = TH_PRIMARY_CATEGORY_DEBUG ? round( microtime( true ) * 1000 ) : TH_PRIMARY_CATEGORY_VERSION;
		wp_enqueue_style( 'th-primary-category-classic-css',
			TH_PRIMARY_CATEGORY_RELATIVE_WP_PATH . 'admin/styles/classic.css',
			false,
			$fingerprint_version );
	}

	/**
	 * Register our custom meta boxes
	 */
	static function register_meta_boxes() {
		add_meta_box( 'th_primary_category',
			__( 'Primary Category', TH_PRIMARY_CATEGORY_DOMAIN ),
			__CLASS__ . '::primary_category_meta_box',
			get_current_screen(),
			'side' );
	}

	/**
	 * Output for Primary Category Meta Box
	 */
	static function primary_category_meta_box() {

		$uncategorized_id            = get_cat_ID( 'Uncategorized' );
		$current_primary_category_id = get_post_meta( get_the_ID(), TH_PRIMARY_CATEGORY_META_FIELD, true ) ?: 0;

		printf(
			'<div class="th-primary-category-sidebar">%s%s</div>',
			wp_dropdown_categories(
				array(
					'class'             => 'th-primary-category-sidebar__select',
					'echo'              => 0,
					'exclude'           => $uncategorized_id,
					'hide_empty'        => 0,
					'hierarchical'      => true,
					'name'              => TH_PRIMARY_CATEGORY_META_FIELD,
					'option_none_value' => 0,
					'selected'          => $current_primary_category_id,
					'show_option_none'  => 'None',
				)
			),
			wp_nonce_field( 'th_primary_category_save', 'th_primary_category_save_nonce', true, false )
		);
	}

	/**
	 * Saves Primary Category ID after verifying validity and user permissions
	 *
	 * @param $post_id
	 * @param $post
	 *
	 * @return mixed
	 */
	static function save_post( $post_id, $post ) {

		// Prevent Autosaves from triggering save
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return $post_id;
		}

		// Verify nonce referrer
		if ( ! wp_verify_nonce( $_POST['th_primary_category_save_nonce'], 'th_primary_category_save' ) ) {
			return $post_id;
		}

		// Verify user permission
		if ( ! current_user_can( 'edit_post', $post_id ) || ! current_user_can( 'manage_categories', $post_id ) ) {
			return $post_id;
		}

		$primary_category_id_submitted = intval( $_POST[ TH_PRIMARY_CATEGORY_META_FIELD ] );
		$valid_category_ids            = get_terms( array(
			'fields'     => 'ids',
			'hide_empty' => false,
			'taxonomy'   => 'category',
		) );

		// Save if selection is valid, delete if set to None (0)
		if ( in_array( $primary_category_id_submitted, $valid_category_ids ) ) {
			update_post_meta( $post_id, TH_PRIMARY_CATEGORY_META_FIELD, $primary_category_id_submitted );
		} elseif ( $primary_category_id_submitted === 0 ) {
			delete_post_meta( $post_id, TH_PRIMARY_CATEGORY_META_FIELD );
		}

		return $post_id;
	}

}
