<?php
/*
Plugin Name: Primary Category
Plugin URI: https://www.travishoglund.com
Description: Allows setting of Primary Category on Post and Custom Post Types
Version: 1.0
Author: Travis Hoglund
Author URI: https://www.travishoglund.com
License: GPLv2 or later
Text Domain: primary-category
Domain Path: /languages
*/

if ( ! defined( 'ABSPATH' ) ) {
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	exit();
}

define( 'TH_PRIMARY_CATEGORY_DEBUG', false );
define( 'TH_PRIMARY_CATEGORY_VERSION', '1.0' );
define( 'TH_PRIMARY_CATEGORY_DOMAIN', 'primary-category' );
define( 'TH_PRIMARY_CATEGORY_PATH', plugin_dir_path( __FILE__ ) );
define( 'TH_PRIMARY_CATEGORY_RELATIVE_WP_PATH', str_replace( ABSPATH, '/', TH_PRIMARY_CATEGORY_PATH ) );
define( 'TH_PRIMARY_CATEGORY_BASENAME', plugin_basename( __FILE__ ) );
define( 'TH_PRIMARY_CATEGORY_META_FIELD', '_th_primary_category_id' );

require_once( dirname( __FILE__ ) . '/classes/class-th-primary-category.php' );
