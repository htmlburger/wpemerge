<?php
/**
 * @package   WPEmerge
 * @author    Atanas Angelov <atanas.angelov.dev@gmail.com>
 * @copyright 2018 Atanas Angelov
 * @license   https://www.gnu.org/licenses/gpl-2.0.html GPL-2.0
 * @link      https://wpemerge.com/
 */

namespace WPEmerge\Routing;

use WPEmerge\Middleware\ExecutesMiddlewareTrait;
use WPEmerge\Requests\RequestInterface;

/**
 * Represent middleware that envelops a handler.
 */
class Pipeline {
	use ExecutesMiddlewareTrait;

	/**
	 * Pipeline handler.
	 *
	 * @var PipelineHandler
	 */
	protected $handler = null;

	/**
	 * Middleware.
	 *
	 * @var array<array>
	 */
	protected $middleware = [];

	/**
	 * Get handler.
	 *
	 * @codeCoverageIgnore
	 * @return PipelineHandler
	 */
	public function getHandler() {
		return $this->handler;
	}

	/**
	 * Set handler.
	 *
	 * @codeCoverageIgnore
	 * @param  string|\Closure $handler
	 * @return void
	 */
	public function setHandler( $handler ) {
		$this->handler = new PipelineHandler( $handler );
	}

	/**
	 * Fluent alias for setHandler().
	 *
	 * @codeCoverageIgnore
	 * @param  string|\Closure $handler
	 * @return static          $this
	 */
	public function to( $handler ) {
		call_user_func_array( [$this, 'setHandler'], func_get_args() );

		return $this;
	}

	/**
	 * Add middleware to the pipeline.
	 *
	 * @codeCoverageIgnore
	 * @param  array<array> $middleware
	 * @return static       $this
	 */
	public function pipe( $middleware ) {
		$this->middleware = array_merge( $this->middleware, $middleware );

		return $this;
	}

	/**
	 * Get a response for the given request.
	 *
	 * @param  RequestInterface                    $request
	 * @param  array                               $arguments
	 * @return \Psr\Http\Message\ResponseInterface
	 */
	public function run( RequestInterface $request, $arguments ) {
		return $this->executeMiddleware( $this->middleware, $request, function () use ( $arguments ) {
			return call_user_func_array( [$this->getHandler(), 'execute'], $arguments );
		} );
	}
}
