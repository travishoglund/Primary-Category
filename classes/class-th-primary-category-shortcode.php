<?php

class TH_Primary_Category_Shortcode {

	static function init() {
		add_action( 'init', __CLASS__ . '::ready' );
	}

	/**
	 * Ready to kick off main actions
	 */
	static function ready() {
		add_shortcode( 'primary-posts', __CLASS__ . '::primary_posts_shortcode' );
		add_action( 'wp_enqueue_scripts', __CLASS__ . '::enqueue_shortcode_scripts_and_styles' );
	}

	/**
	 * Shortcode to allow easy Primary Category feeds throughout the WP site
	 *
	 * @param $atts
	 * @param null $content
	 *
	 * @return mixed|string|void
	 */
	static function primary_posts_shortcode( $atts, $content = null ) {

		$category_id = array_key_exists( 'category_id', $atts ) ? intval( $atts['category_id'] ) : 0;
		$post_type   = array_key_exists( 'post_type', $atts ) ? sanitize_text_field( $atts['post_type'] ) : 'post';
		$limit       = array_key_exists( 'limit', $atts ) ? intval( $atts['limit'] ) : 3;

		if ( ! $category_id || ! term_exists( $category_id, 'category' ) ) {
			return '<script>console.log( "Warning: Category ID (' . ( array_key_exists( 'category_id', $atts ) ? sanitize_text_field( $atts['category_id'] ) : $category_id ) . ') does not exist for calling shortcode [primary-posts]" );</script>';
		}

		if ( ! post_type_exists( $post_type ) ) {
			return '<script>console.log( "Warning: Post Type (' . $post_type . ') does not exist for calling shortcode [primary-posts]" );</script>';
		}

		$primary_posts = self::get_primary_posts( $category_id, $post_type, $limit );
		$output        = self::primary_posts_output( $primary_posts->posts );

		return apply_filters( 'primary_shortcode_template', $output, $primary_posts->posts );

	}

	/**
	 * Separated to be easily extendable for frontend dev needs
	 * Ex: TH_Primary_Category_Shortcode::get_primary_posts(...)
	 *
	 * @param $category_id
	 * @param string $post_type
	 * @param int $limit
	 *
	 * @return WP_Query
	 */
	static function get_primary_posts( $category_id, $post_type = 'post', $limit = 3 ) {
		return new WP_Query( array(
			'posts_per_page' => $limit,
			'post_type'      => $post_type,
			'meta_key'       => TH_PRIMARY_CATEGORY_META_FIELD,
			'meta_value'     => $category_id,
		) );
	}

	/**
	 * Default Article Feed Output for Shortcode
	 *
	 * @param $posts
	 *
	 * @return string
	 */
	static function primary_posts_output( $posts ) {

		global $post;
		$output = '';

		if ( ! empty( $posts ) ) {
			$output .= '<div class="primary-category-feed">';
			foreach ( $posts as $post ) {
				setup_postdata( $post );
				$output .= sprintf(
					'<div class="primary-category-feed__article">
                            <div class="primary-category-feed__article-title">
                                <a href="%s">%s</a>
                            </div>
                            <div class="primary-category-feed__article-date">
                                %s
                            </div>
                            <div class="primary-category-feed__article-excerpt">
                                %s
                            </div>
                        </div>',
					esc_url( get_the_permalink() ),
					esc_html( get_the_title() ),
					esc_html( get_the_time( 'F d, Y' ) ),
					esc_html( get_the_excerpt() )
				);
				wp_reset_postdata();
			}
			$output .= '</div>';
		}

		return $output;

	}

	/**
	 * Enqueue scripts and styles for shortcode
	 */
	static function enqueue_shortcode_scripts_and_styles() {
		$fingerprint_version = TH_PRIMARY_CATEGORY_DEBUG ? round( microtime( true ) * 1000 ) : TH_PRIMARY_CATEGORY_VERSION;
		wp_enqueue_style( 'th-primary-category-classic-css',
			TH_PRIMARY_CATEGORY_RELATIVE_WP_PATH . 'frontend/styles/shortcode.css',
			false,
			$fingerprint_version );
	}

}
