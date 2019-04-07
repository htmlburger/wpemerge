<?php
/**
 * @package   WPEmerge
 * @author    Atanas Angelov <atanas.angelov.dev@gmail.com>
 * @copyright 2018 Atanas Angelov
 * @license   https://www.gnu.org/licenses/gpl-2.0.html GPL-2.0
 * @link      https://wpemerge.com/
 */

namespace WPEmerge\Middleware;

use Closure;
use WPEmerge\Exceptions\ConfigurationException;
use WPEmerge\Helpers\MixedType;

/**
 * Allow objects to have middleware.
 */
trait HasMiddlewareTrait {
	/**
	 * Array of all registered middleware.
	 *
	 * @var array
	 */
	protected $middleware = [];

	/**
	 * Check if the passed entity is a valid middleware.
	 *
	 * @param  mixed   $middleware
	 * @return boolean
	 */
	protected function isMiddleware( $middleware ) {
		return (
			$middleware instanceof Closure
			||
			is_a( $middleware, MiddlewareInterface::class, true )
		);
	}

	/**
	 * Get registered middleware.
	 *
	 * @return array
	 */
	public function getMiddleware() {
		return $this->middleware;
	}

	/**
	 * Set registered middleware.
	 * Accepts: a class name, an instance of a class, a Closure or an array of any of the previous.
	 *
	 * @throws ConfigurationException
	 * @param  string|\Closure|\WPEmerge\Middleware\MiddlewareInterface|array $middleware
	 * @return void
	 */
	public function setMiddleware( $middleware ) {
		$middleware = MixedType::toArray( $middleware );

		foreach ( $middleware as $item ) {
			if ( ! $this->isMiddleware( $item ) ) {
				throw new ConfigurationException(
					'Passed middleware must be a closure or the name or instance of a class which ' .
					'implements the ' . MiddlewareInterface::class . ' interface.'
				);
			}
		}

		$this->middleware = $middleware;
	}

	/**
	 * Add middleware.
	 * Accepts: a class name, an instance of a class, a Closure or an array of any of the previous.
	 *
	 * @param  string|\Closure|\WPEmerge\Middleware\MiddlewareInterface|array $middleware
	 * @return void
	 */
	public function addMiddleware( $middleware ) {
		$middleware = MixedType::toArray( $middleware );

		$this->setMiddleware( array_merge( $this->getMiddleware(), $middleware ) );
	}

	/**
	 * Fluent alias for addMiddleware().
	 *
	 * @codeCoverageIgnore
	 * @param  string|\Closure|\WPEmerge\Middleware\MiddlewareInterface|array $middleware
	 * @return static                                                         $this
	 */
	public function middleware( $middleware ) {
		call_user_func_array( [$this, 'addMiddleware'], func_get_args() );

		return $this;
	}
}
