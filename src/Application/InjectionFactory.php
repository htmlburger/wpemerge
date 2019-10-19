<?php
/**
 * @package   WPEmerge
 * @author    Atanas Angelov <atanas.angelov.dev@gmail.com>
 * @copyright 2018 Atanas Angelov
 * @license   https://www.gnu.org/licenses/gpl-2.0.html GPL-2.0
 * @link      https://wpemerge.com/
 */

namespace WPEmerge\Application;

use WPEmerge\Exceptions\ClassNotFoundException;

/**
 * Injection factory.
 */
class InjectionFactory {
	/**
	 * Application.
	 *
	 * @var Application
	 */
	protected $app = null;

	/**
	 * Constructor.
	 *
	 * @codeCoverageIgnore
	 * @param Application $app
	 */
	public function __construct( Application $app ) {
		$this->app = $app;
	}

	/**
	 * Make a Handler.
	 *
	 * @throws ClassNotFoundException
	 * @param  string $class
	 * @return object
	 */
	public function make( $class ) {
		$instance = $this->app->resolve( $class );

		if ( $instance === null ) {
			if ( ! class_exists( $class ) ) {
				throw new ClassNotFoundException( 'Class not found: ' . $class );
			}

			$instance = new $class();
		}

		return $instance;
	}
}
