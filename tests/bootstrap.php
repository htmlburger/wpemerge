<?php

class WPEmerge_Tests_Bootstrap {

	/**
	 * The bootstrap instance.
	 *
	 * @var WPEmerge_Tests_Bootstrap
	 */
	protected static $instance = null;

	/**
	 * Directory where wordpress-tests-lib is installed
	 *
	 * @var string
	 */
	public $wp_tests_dir;

	/**
	 * Testing directory.
	 *
	 * @var string
	 */
	public $tests_dir;

	/**
	 * Library directory.
	 *
	 * @var string
	 */
	public $library_directory;

	/**
	 * Setup the unit testing environment
	 */
	private function __construct() {
		ini_set( 'display_errors','on' );
		error_reporting( E_ALL );

		$this->tests_dir = __DIR__;
		$this->library_directory = dirname( $this->tests_dir );
		$this->wp_tests_dir = getenv( 'WP_TESTS_DIR' ) ? getenv( 'WP_TESTS_DIR' ) : $this->library_directory . '/tmp/wordpress-tests-lib';

		define( 'WPEMERGE_TEST_DIR', $this->tests_dir );

		// load test function so tests_add_filter() is available
		require_once( $this->wp_tests_dir . '/includes/functions.php' );

		// load plugin
		tests_add_filter( 'muplugins_loaded', array( $this, 'load_plugin' ) );

		// load the WP testing environment
		require_once( $this->wp_tests_dir . '/includes/bootstrap.php' );

		// make sure query vars are prepared
		global $wp;
		if ( ! is_array( $wp->query_vars ) ) {
			$wp->query_vars = [];
		}

		WPEmerge\Facades\Framework::boot();
	}

	/**
	 * Load the plugin
	 */
	public function load_plugin() {
		require_once( $this->library_directory . '/vendor/autoload.php' );
	}

	/**
	 * Get the single tests boostrap instance
	 *
	 * @return WPEmerge_Tests_Bootstrap
	 */
	public static function instance() {
		if ( is_null( static::$instance ) ) {
			static::$instance = new self();
		}

		return static::$instance;
	}

}

WPEmerge_Tests_Bootstrap::instance();
