<?php
require_once( TH_PRIMARY_CATEGORY_PATH . 'classes/class-th-primary-category-classic.php' );
require_once( TH_PRIMARY_CATEGORY_PATH . 'classes/class-th-primary-category-gutenberg.php' );
require_once( TH_PRIMARY_CATEGORY_PATH . 'classes/class-th-primary-category-shortcode.php' );

class TH_Primary_Category {

	function __construct() {

		// load translations
		add_action( 'plugins_loaded', __CLASS__ . '::load_translations' );

		// Support for Classic Editor and Gutenberg
		TH_Primary_Category_Classic::init();
		TH_Primary_Category_Gutenberg::init();

		// Register Shortcode for frontend functionality
		TH_Primary_Category_Shortcode::init();
	}

	/**
	 * Loads the translations for the plugin
	 */
	static public function load_translations() {
		$path    = str_replace( '\\', '/', TH_PRIMARY_CATEGORY_PATH );
		$mu_path = str_replace( '\\', '/', WPMU_PLUGIN_DIR );

		if ( false !== stripos( $path, $mu_path ) ) {
			load_muplugin_textdomain( TH_PRIMARY_CATEGORY_DOMAIN,
				dirname( TH_PRIMARY_CATEGORY_BASENAME ) . '/languages/' );
		} else {
			load_plugin_textdomain( TH_PRIMARY_CATEGORY_DOMAIN,
				false,
				dirname( TH_PRIMARY_CATEGORY_BASENAME ) . '/languages/' );
		}
	}

}

new TH_Primary_Category();
