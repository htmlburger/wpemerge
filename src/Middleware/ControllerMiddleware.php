<?php
/**
 * @package   WPEmerge
 * @author    Atanas Angelov <atanas.angelov.dev@gmail.com>
 * @copyright 2018 Atanas Angelov
 * @license   https://www.gnu.org/licenses/gpl-2.0.html GPL-2.0
 * @link      https://wpemerge.com/
 */

namespace WPEmerge\Middleware;

use WPEmerge;

/**
 * Redirect users who do not have a capability to a specific URL.
 */
class ControllerMiddleware {
	/**
	 * Middleware.
	 *
	 * @var array<string>
	 */
	protected $middleware = [];

	/**
	 * Methods the middleware applies to.
	 *
	 * @var array<string>
	 */
	protected $whitelist = [];

	/**
	 * Methods the middleware does not apply to.
	 *
	 * @var array<string>
	 */
	protected $blacklist = [];

	/**
	 * Constructor.
	 *
	 * @param  string|array<string> $middleware
	 */
	public function __construct( $middleware ) {
		$this->middleware = (array) $middleware;
	}

	/**
	 * Get middleware.
	 *
	 * @codeCoverageIgnore
	 * @return array<string>
	 */
	public function get() {
		return $this->middleware;
	}

	/**
	 * Set methods the middleware should apply to.
	 *
	 * @codeCoverageIgnore
	 * @param  string|array<string> $methods
	 * @return static
	 */
	public function only( $methods ) {
		$this->whitelist = (array) $methods;

		return $this;
	}

	/**
	 * Set methods the middleware should not apply to.
	 *
	 * @codeCoverageIgnore
	 * @param  string|array<string> $methods
	 * @return static
	 */
	public function except( $methods ) {
		$this->blacklist = (array) $methods;

		return $this;
	}

	/**
	 * Get whether the middleware applies to the specified method.
	 *
	 * @param  string $method
	 * @return boolean
	 */
	public function appliesTo( $method ) {
		if ( in_array( $method, $this->blacklist, true ) ) {
			return false;
		}

		if ( empty( $this->whitelist ) ) {
			return true;
		}

		return in_array( $method, $this->whitelist, true );
	}
}
